<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class WordPressDocsController extends Controller
{
    private $wpApiBaseUrl = 'https://togoreforme.gouv.tg/wp-json/';
    private $bearerToken = '66bc213f13357';

    public function importWpDocuments()
    {
        echo "Starting document import...\n";

        // Package IDs to process
        $packageIds = [
            4226, 4227, 4228, 4288, 4314, 4315, 4343, 4344, 4345, 4346, 4359, 4360, 4367, 4378,
            4380, 4402, 4432, 4451, 4452, 4453, 4475, 4476, 4508, 4512, 4521, 4531, 4535, 4536,
            4542, 4562, 4566, 4567, 4571, 4573, 4603, 4620, 4625, 4626, 4627, 4638, 4645, 4646,
            4647, 4649, 4666, 4667, 4668, 4669, 4670, 4672, 4673, 4676, 4677

        ];

        echo "Starting document import...\n";

        // 2. Process each package ID
        $importedDocumentCount = 0;
        $skippedDocumentCount = 0;

        foreach ($packageIds as $wpPackageId) {
            echo "Processing package ID: " . $wpPackageId . "...\n";
            try {
                if ($this->createDocumentFromPackageId($wpPackageId)) {
                    $importedDocumentCount++;
                    echo "Document imported successfully!\n";
                } else {
                    $skippedDocumentCount++;
                    echo "Document already exists, skipping.\n";
                }
            } catch (\Exception $e) {
                echo "Error importing document: " . $e->getMessage() . "\n";
            }
        }

        $message = "Document import completed!";
        $message .= " ({$importedDocumentCount} documents imported";
        if ($skippedDocumentCount > 0) {
            $message .= ", {$skippedDocumentCount} documents skipped)";
        } else {
            $message .= ")";
        }

        echo $message . "\n";
    }

    private function createDocumentFromPackageId($packageId)
    {
        echo " - Fetching document details from WordPress...\n";
        // Fetch document details from the 'wpdm/v1/packages/{id}' endpoint
        try {
            $response = Http::withToken($this->bearerToken)
                ->withOptions(['verify' => false]) // Disable SSL verification
                ->get($this->wpApiBaseUrl . "wpdm/v1/packages/{$packageId}");

            if (!$response->successful()) {
                echo " - Failed to fetch document details. HTTP status: " . $response->status() . "\n";
                return false;
            }

            $wpDocument = $response->json();
        } catch (\Exception $e) {
            echo " - Error fetching document details: " . $e->getMessage() . "\n";
            return false;
        }

        // Call the createDocument method
        return $this->createDocument($wpDocument);
    }

    private function downloadAndStoreFile($url, $filePath)
    {
        try {
            echo " - Downloading and storing file...\n";
            $fileContent = Http::withToken($this->bearerToken)
                ->withOptions(['verify' => false])
                ->get($url)->body();

            Storage::disk('public')->makeDirectory('documents');
            Storage::disk('public')->put($filePath, $fileContent);
        } catch (\Exception $e) {
            throw new \Exception("Error downloading file from '{$url}': " . $e->getMessage());
        }
    }

    private function generateUniqueSlug($slug)
    {
        $originalSlug = $slug;
        $count = 2;
        while (Document::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }
        return $slug;
    }

    private function mapWpStatus($wpStatus)
    {
        $statusMap = [
            'publish' => 'PUBLISHED',
            'draft' => 'DRAFT',
            'pending' => 'PENDING',
        ];
        return $statusMap[$wpStatus] ?? 'PUBLISHED';
    }

    private function createDocument($wpDocument)
    {
        if (Document::where('wp_post_id', $wpDocument['id'])->exists()) {
            return false; // Document already exists, skip
        }

        $slug = Str::slug($wpDocument['title']);
        if (Document::where('slug', $slug)->where('wp_post_id', '!=', $wpDocument['id'])->exists()) {
            $slug = $this->generateUniqueSlug($slug);
        }

        // Check if the 'files' array is not empty
        if (empty($wpDocument['files'])) {
            return false; // Skip document with no files
        }

        // Download and store the file (handle potential errors)
        try {
            echo " - Downloading and storing file...\n";
            $fileName = basename($wpDocument['files'][0]);
            $filePath = 'documents/' . $fileName;
            $this->downloadAndStoreFile($wpDocument['guid'], $filePath);
        } catch (\Exception $e) {
            echo " - Error downloading/storing document file: " . $e->getMessage() . "\n";
            return false; // Skip to the next document
        }

        echo " - File downloaded and stored successfully!\n";

        // 4. Calculate file size:
        $fileSize = filesize(Storage::disk('public')->path($filePath));
        if ($fileSize === false) {
            echo " - Error getting file size.\n";
            return false; // Skip
        }

        // Create document record:
        echo " - Creating document record in database...\n";
        $document = Document::create([
            'wp_post_id' => (int)$wpDocument['id'],
            'title' => $wpDocument['title'],
            'slug' => $slug,
            'description' => $wpDocument['description'],
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'file_type' => pathinfo($fileName, PATHINFO_EXTENSION),
            'year' => (int)substr($wpDocument['date_created'], 0, 4),
            'status' => $this->mapWpStatus($wpDocument['status']),
            'access_type' => 'download',
            'expiration_date' => null,
            'download_count' => (int)$wpDocument['download_count'],
            'view_count' => (int)$wpDocument['view_count'],
            'download_link' => $wpDocument['guid'],
        ]);

        echo " - Document record created.\n";

        // ... (Category syncing - using categories from the document) ...
        echo " - Syncing categories...\n";
        $categoryIds = [];
        foreach ($wpDocument['categories'] as $wpCategory) {
            $category = Category::where('slug', $wpCategory['slug'])->first();
            if ($category) {
                $categoryIds[] = $category->id;
            }
        }

        $document->categories()->sync($categoryIds);
        echo " - Categories synced.\n";

        return true;
    }

}
