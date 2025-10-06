<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Added for logging
use Illuminate\Support\Facades\Storage; // Added for file manipulation
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon; // For date formatting

class DocProjetsLoisFinancesController extends Controller
{
    // Removed DocumentSearchService injection as it wasn't used in your provided code.
    // If you plan to use a service for search logic, re-inject it and use it.

    public function index(): View
    {
        $title = 'Documents Projets de Lois de Finances';
        $categoryName = 'Projets de lois de finances'; // Specific category for this controller

        // Define the target categories for this controller
        $categoryNames = [
            $categoryName,
        ];

        // Retrieve the IDs of the target categories
        $targetCategoryIds = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();

        // Retrieve all documents linked to the 'Projets de lois de finances' category
        // Apply pagination directly here for initial display
        $documents = Document::with('categories')
            ->whereHas('categories', function ($query) use ($targetCategoryIds) {
                $query->whereIn('categories.id', $targetCategoryIds);
            })
            ->orderBy('date_publication', 'desc')
            ->paginate(44);

        // Retrieve all categories that have documents, ordered by name, for the filter dropdown
        $allCategories = Category::whereHas('documents')->orderBy('name')->get();

        // Auto-selected categories ('Projets de lois de finances' in this case) for the view
        $autoSelectedCategories = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();
        $autoSelectedCount = count($autoSelectedCategories);
        
        // Prepare routes for Blade and JavaScript
        $bladeRouteNames = [
            'show' => 'documents.projets_lois_finances.show',
            'download' => 'documents.projets_lois_finances.download',
        ];
        
        return view('frontend.pages.page_documents', [
            'title' => $title,
            'documents' => $documents,
            'routes' => $bladeRouteNames, // For the Blade partial view if included
            'jsRoutes' => [ // For JavaScript
                'search' => route('documents.projets_lois_finances.search'), // Corrected route name
                'show' => route('documents.projets_lois_finances.show', ['slug' => 'PLACEHOLDER_SLUG']),
                'download' => route('documents.projets_lois_finances.download', ['id' => 'PLACEHOLDER_ID'])
            ],
            'autoSelectedCategories' => $autoSelectedCategories,
            'autoSelectedCount' => $autoSelectedCount,
            'allCategories' => $allCategories, // For the filter list
        ]);
    }

    /**
     * Handles document search for 'Projets de Lois de Finances' category.
     */
    public function search(Request $request): JsonResponse
    {
        Log::debug('Search request received for "Projets de Lois de Finances" documents', [
            'query' => $request->input('query'),
            'filters' => $request->input('filters'),
            'page' => $request->input('page'),
            'wantsJson' => $request->wantsJson(),
            'isAjax' => $request->ajax()
        ]);
        
        $validated = $request->validate([
            'query' => 'nullable|string',
            'filters' => 'nullable|array',
            'page' => 'nullable|integer'
        ]);

        $categoryName = "Projets de lois de finances"; // Specific category for search
        $targetCategoryIds = Category::where('name', $categoryName)->pluck('id')->toArray();

        // Build the base query, filtered by the 'Projets de lois de finances' category
        $documentsQuery = Document::with('categories')
            ->whereHas('categories', function ($query) use ($targetCategoryIds) {
                $query->whereIn('categories.id', $targetCategoryIds);
            });

        // Apply text search if a query is provided
        if (!empty($validated['query'])) {
            $searchTerm = $validated['query'];
            
            // CLEANING AND SMARTER SEARCH:
            // 1. Removes all characters that are not letters, numbers, or spaces.
            // 2. Replaces groups of spaces with a single space.
            // 3. Transforms the string into an array of words.
            $cleanSearchTerm = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $searchTerm); // Keeps letters, numbers, spaces
            $cleanSearchTerm = preg_replace('/\s+/', ' ', $cleanSearchTerm); // Reduces multiple spaces
            $keywords = explode(' ', trim($cleanSearchTerm));

            // Apply a WHERE clause for each keyword
            $documentsQuery->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    if (mb_strlen($keyword) > 2) { // Ignores very short words (e.g., "un", "le")
                        $q->where(function ($subQ) use ($keyword) {
                            $subQ->where('title', 'like', '%' . $keyword . '%')
                                 ->orWhere('description', 'like', '%' . $keyword . '%');
                        });
                    }
                }
            });
        }

        // Apply additional filters (years, months, other categories)
        if (isset($validated['filters']['years']) && count($validated['filters']['years']) > 0) {
            $documentsQuery->whereIn(\DB::raw('YEAR(date_publication)'), $validated['filters']['years']);
        }
        if (isset($validated['filters']['months']) && count($validated['filters']['months']) > 0) {
            $documentsQuery->whereIn(\DB::raw('MONTH(date_publication)'), $validated['filters']['months']);
        }
        
        // If the user has selected additional categories via the dropdown
        // Note: These will be added to the 'Projets de lois de finances' filter already in place
        if (isset($validated['filters']['categories']) && count($validated['filters']['categories']) > 0) {
            $documentsQuery->whereHas('categories', function ($q) use ($validated) {
                $q->whereIn('categories.id', $validated['filters']['categories']);
            });
        }

        // Execute the query with pagination
        $documents = $documentsQuery->orderBy('date_publication', 'desc')->paginate(44);

        // Return results as JSON
        return response()->json([
            'html' => view('frontend.partials.documents_list', [
                'documents' => $documents,
                'routes' => [
                    'show' => 'documents.projets_lois_finances.show', 
                    'download' => 'documents.projets_lois_finances.download' 
                ]
            ])->render(),
            'pagination' => $documents->links('pagination::bootstrap-4')->toHtml()
        ]);
    }

    /**
     * This method was named `allDocuments` in your original code. 
     * If you need a distinct 'all' endpoint for this controller similar to others, 
     * consider naming it `all()` and implementing it consistently.
     * The `index()` method already handles fetching all documents for this category.
     */
    public function all(): JsonResponse
    {
        $categoryName = "Projets de lois de finances";
        $documents = Document::with('categories')
            ->whereHas('categories', function ($query) use ($categoryName) {
                $query->where('name', $categoryName);
            })
            ->orderBy('date_publication', 'desc')
            ->get(); // Get all, no pagination for this endpoint

        // You might want to transform the documents here if the client expects a specific format
        $documentsTransformed = $documents->transform(function ($document) {
            return [
                'title' => $document->title,
                'id' => $document->id,
                'category' => $document->categories->pluck('name')->implode(', ') ?: 'Aucune catégorie trouvée',
                'slug' => $document->slug,
                'created_at' => $document->created_at->toDateTimeString(),
                'file_url' => asset('storage/' . $document->file_path),
                'date_publication' => $document->date_publication,
                'download_count' => $document->download_count,
            ];
        });

        return response()->json($documentsTransformed);
    }

    /**
     * Displays a document inline in the browser.
     * Updated with Voyager's JSON path handling logic.
     */
    public function show(string $slug): Response
    {
        $categoryName = 'Projets de lois de finances';

        // Retrieve the document, ensuring it belongs to the specified category
        $document = Document::where('slug', $slug)
            ->whereHas('categories', function ($query) use ($categoryName) {
                $query->where('name', $categoryName);
            })
            ->firstOrFail();

        $filePathInDb = $document->file_path; 
        $realFilePath = null;

        if (is_string($filePathInDb)) {
            $decodedPath = json_decode($filePathInDb, true);
            if (is_array($decodedPath) && !empty($decodedPath) && isset($decodedPath[0]['download_link'])) {
                $realFilePath = str_replace('\\', '/', $decodedPath[0]['download_link']);
            } else {
                $realFilePath = $filePathInDb;
            }
        } else {
            Log::error("File path in database is not a string for document (show method - Projets de lois de finances)", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'The requested file path is misconfigured.');
        }

        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("File not found on disk for display (show method - Projets de lois de finances)", [
                'document_id' => $document->id,
                'extracted_path_used' => $realFilePath,
                'original_db_value' => $filePathInDb,
                'storage_exists_check' => Storage::disk('public')->exists($realFilePath) ? 'true' : 'false'
            ]);
            abort(404, 'The requested file is not found or has been removed from the server.');
        }

        $filePath = Storage::disk('public')->path($realFilePath);
        $mimeType = Storage::disk('public')->mimeType($realFilePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

    /**
     * Handles document downloads.
     * Updated with Voyager's JSON path handling logic.
     */
    public function download(int $id): BinaryFileResponse
    {
        $document = Document::findOrFail($id);

        $filePathInDb = $document->file_path;
        $realFilePath = null;
        $downloadFilename = $document->title;

        if (is_string($filePathInDb)) {
            $decodedPath = json_decode($filePathInDb, true);
            if (is_array($decodedPath) && !empty($decodedPath) && isset($decodedPath[0]['download_link'])) {
                $realFilePath = str_replace('\\', '/', $decodedPath[0]['download_link']);
                $downloadFilename = $decodedPath[0]['original_name'] ?? $document->title;
            } else {
                $realFilePath = $filePathInDb;
            }
        } else {
            Log::error("File path in database is not a string for document (download method - Projets de lois de finances)", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'The file path is misconfigured.');
        }

        // Logic for incrementing download count
        $downloadedDocuments = session()->get('downloaded_documents', []);
        if (!in_array($document->id, $downloadedDocuments)) {
            $document->increment('download_count');
            session()->push('downloaded_documents', $document->id);
        }

        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("File not found on disk for download (download method - Projets de lois de finances)", [
                'document_id' => $document->id,
                'extracted_path_used' => $realFilePath,
                'original_db_value' => $filePathInDb,
                'storage_exists_check' => Storage::disk('public')->exists($realFilePath) ? 'true' : 'false'
            ]);
            abort(404, 'The requested file is not found or has been removed from the server.');
        }

        $absolutePath = Storage::disk('public')->path($realFilePath);
        $finalDownloadFilename = $downloadFilename;
        $extension = pathinfo($realFilePath, PATHINFO_EXTENSION); 

        if ($finalDownloadFilename) {
            $cleanTitle = preg_replace('/[^A-Za-z0-9.\-_]/', '_', pathinfo($finalDownloadFilename, PATHINFO_FILENAME));
            $finalDownloadFilename = $cleanTitle . '.' . $extension;
        } else {
            $finalDownloadFilename = 'document_telecharge.' . $extension;
        }

        return response()->download($absolutePath, $finalDownloadFilename);
    }
}