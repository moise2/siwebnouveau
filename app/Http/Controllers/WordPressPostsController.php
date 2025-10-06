<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Models\Category;

class WordPressPostsController extends Controller
{
    private $wpApiBaseUrl = 'https://togoreforme.gouv.tg/wp-json/';

    public function importWpPosts()
    {
        // 1. Define the IDs of the posts you want to import
        $postIdsToImport = [
            4294, 4298, 4302, 4307, 4310, 4320, 4324, 4328, 4334, 4337, 4349, 4363, 4372, 4375, 4384, 4387, 4390, 4393, 4396, 4399, 4408, 4411, 4414, 4417, 4420, 4423, 4426, 4430, 4434, 4437, 4441, 4444, 4447, 4463, 4466, 4469, 4472, 4478, 4482, 4492, 4496, 4500, 4504, 4510, 4515, 4518, 4522, 4528, 4537, 4544, 4548, 4553, 4556, 4559, 4564, 4575, 4579, 4582, 4585, 4588, 4592, 4595, 4599, 4600, 4607, 4610, 4613, 4616, 4641, 4642, 4643, 4654, 4658
        ];

        $importedPostCount = 0;
        $skippedPostCount = 0;

        foreach ($postIdsToImport as $postId) {
            echo "Processing post ID: " . $postId . "...\n";

            try {
                if ($this->importSinglePost($postId)) {
                    $importedPostCount++;
                    echo "Post imported successfully!\n";
                } else {
                    $skippedPostCount++;
                    echo "Post already exists, skipping.\n";
                }
            } catch (\Exception $e) {
                echo "Error importing post: " . $e->getMessage() . "\n";
            }
        }

        $message = "Post import completed!";
        $message .= " ({$importedPostCount} posts imported";
        if ($skippedPostCount > 0) {
            $message .= ", {$skippedPostCount} posts skipped)";
        } else {
            $message .= ")";
        }

        echo $message . "\n";
    }

    private function importSinglePost($postId) {
        $response = Http::withOptions([
            'verify' => false, // Disable SSL verification
        ])->get($this->wpApiBaseUrl . "wp/v2/posts/{$postId}");

        if ($response->successful()) {
            $wpPost = $response->json();

            // 2. Check if the post already exists in your database
            if (!Post::where('wp_post_id', $wpPost['id'])->exists()) {
                $this->createPost($wpPost);
                return true; // Successfully imported
            }
        } else {
            echo "Failed to fetch post from WordPress API. HTTP Status: " . $response->status() . "\n";
        }

        return false; // Post already exists or there was an error
    }

    private function createPost($wpPost)
    {
        // 3. Create the post (only if it doesn't exist)
        $slug = Str::slug($wpPost['title']['rendered']);
        if (Post::where('slug', $slug)->exists()) {
            $slug = $this->generateUniqueSlug($slug);
        }

        $postData = [
            'wp_post_id' => $wpPost['id'],
            'author_id' => $this->getAuthorId($wpPost['author'] ?? 1),
            'title' => $wpPost['title']['rendered'],
            'seo_title' => $wpPost['title']['rendered'],
            'excerpt' => $wpPost['excerpt']['rendered'],
            'body' => $wpPost['content']['rendered'],
            'image' => $this->downloadAndStoreImage($wpPost['jetpack_featured_media_url']),
            'slug' => $slug,
            'meta_description' => $wpPost['yoast_head_json']['og_description'] ?? null,
            'meta_keywords' => implode(', ', $wpPost['yoast_head_json']['keywords'] ?? []),
            'status' => $this->mapWpStatus($wpPost['status']),
            'published_at' => $wpPost['date'],
            'updated_at' => $wpPost['modified'],
        ];

        $post = Post::create($postData);

        // 3-a. Handle categories and syncing
        $categoryIds = [];
        foreach ($wpPost['categories'] ?? [] as $wpCategoryId) {
            $categoryIds[] = $this->getOrCreateCategoryId($wpCategoryId);
        }
        $post->categories()->sync($categoryIds);

        // Refresh the relationship data:
        $post->load('categories'); // Eager load the updated categories
    }

    private function getOrCreateCategoryId($wpCategoryId)
    {
        // Get the category slug from the WordPress API
        $response = Http::withOptions([
            'verify' => false, // Disable SSL verification
        ])->get($this->wpApiBaseUrl . "wp/v2/categories/{$wpCategoryId}");

        if ($response->successful()) {
            $wpCategoryData = $response->json();
            $categorySlug = $wpCategoryData['slug'];

            // Find the category in your database by slug
            $category = Category::whereTranslation('slug', $categorySlug)->first();

            // If the category doesn't exist, create it
            if (!$category) {
                $categoryName = $wpCategoryData['name'];
                $category = Category::create([
                    'slug' => $categorySlug,
                    'name' => $categoryName,
                ]);
            }

            return $category->id;
        } else {
            // Handle API error (maybe log it)
            return null; // Or handle the error in a more appropriate way
        }
    }

    private function downloadAndStoreImage($imageUrl)
    {
        if (!$imageUrl) {
            return null;
        }

        try {
            $imageContent = file_get_contents($imageUrl);
            $imageName = Str::random(40) . '.jpg';

            Storage::disk('public')->put('images/' . $imageName, $imageContent);

            return 'images/' . $imageName;
        } catch (\Exception $e) {
            // Log the error:
            \Log::error("Image download failed: " . $e->getMessage());
            return null;
        }
    }

    // Helper methods (you need to implement the logic based on your app):

    private function getAuthorId($wpAuthorId)
    {
        // Logic to map WordPress author ID to your Laravel User ID
        // For example, you could have a mapping table or use a default author.
        // For now, let's return a default author ID of 1:
        return 1;
    }

    private function mapWpStatus($wpStatus)
    {
        $statusMap = [
            'publish' => 'PUBLISHED',
            'draft' => 'DRAFT',
            'pending' => 'PENDING',
        ];
        return $statusMap[$wpStatus] ?? 'DRAFT';
    }

    private function generateUniqueSlug($slug)
    {
        $originalSlug = $slug;
        $count = 2;
        while (Post::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }
        return $slug;
    }
}
