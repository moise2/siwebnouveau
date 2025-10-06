<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Added for logging
use Illuminate\Support\Facades\Storage; // Added for file manipulation
use App\Services\DocumentSearchService; // Ensure this service is correctly implemented
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon; // For date formatting

class DocDaoController extends Controller
{
    protected DocumentSearchService $searchService;

    public function __construct(DocumentSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(): View
    {
        $title = 'Documents DAO (Dossier d\'Appel d\'Offre)';
        $categoryName = 'DAO'; // Specific category for this controller

        // Define the target categories for this controller
        $categoryNames = [
            $categoryName,
        ];

        // Retrieve the IDs of the target categories
        $targetCategoryIds = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();

        // Retrieve all documents linked to the 'DAO' category
        // Apply pagination directly here for initial display
        $documents = Document::with('categories')
            ->whereHas('categories', function ($query) use ($targetCategoryIds) {
                $query->whereIn('categories.id', $targetCategoryIds);
            })
            ->orderBy('date_publication', 'desc')
            ->paginate(44);

        // Retrieve all categories that have documents, ordered by name, for the filter dropdown
        $allCategories = Category::whereHas('documents')->orderBy('name')->get();

        // Auto-selected categories ('DAO' in this case) for the view
        $autoSelectedCategories = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();
        $autoSelectedCount = count($autoSelectedCategories);
        
        // Prepare routes for Blade and JavaScript
        $bladeRouteNames = [
            'show' => 'documents.dao.show',
            'download' => 'documents.dao.download',
        ];
        
        return view('frontend.pages.page_documents', [
            'title' => $title,
            'documents' => $documents,
            'routes' => $bladeRouteNames, // For the Blade partial view if included
            'jsRoutes' => [ // For JavaScript
                'search' => route('documents.dao.search'),
                'show' => route('documents.dao.show', ['slug' => 'PLACEHOLDER_SLUG']),
                'download' => route('documents.dao.download', ['id' => 'PLACEHOLDER_ID'])
            ],
            'autoSelectedCategories' => $autoSelectedCategories,
            'autoSelectedCount' => $autoSelectedCount,
            'allCategories' => $allCategories, // For the filter list
        ]);
    }

    /**
     * This method appears in your routes as 'all-documents-dao' but was not
     * present in your controller code. It's commented out here but you might
     * want to implement it if needed, likely for showing all documents related
     * to "DAO" without the initial category filter constraints applied in `index()`.
     * Or, it could be redundant if `index()` covers the "all" use case.
     * I'm providing a basic structure if you decide to use it.
     */
    // public function all(): View
    // {
    //     $title = 'Tous les Documents DAO';
    //     $categoryName = 'DAO';

    //     $documents = Document::with('categories')
    //         ->whereHas('categories', function ($query) use ($categoryName) {
    //             $query->where('name', $categoryName);
    //         })
    //         ->orderBy('date_publication', 'desc')
    //         ->paginate(44);

    //     $allCategories = Category::whereHas('documents')->orderBy('name')->get();

    //     // For the 'all' page, auto-selected categories might not be relevant,
    //     // or you might want to auto-select the main category. Adjust as needed.
    //     $autoSelectedCategories = Category::where('name', $categoryName)->pluck('id')->toArray();
    //     $autoSelectedCount = count($autoSelectedCategories);

    //     $bladeRouteNames = [
    //         'show' => 'documents.dao.show',
    //         'download' => 'documents.dao.download',
    //     ];

    //     return view('frontend.pages.page_documents', [
    //         'title' => $title,
    //         'documents' => $documents,
    //         'routes' => $bladeRouteNames,
    //         'jsRoutes' => [
    //             'search' => route('documents.dao.search'),
    //             'show' => route('documents.dao.show', ['slug' => 'PLACEHOLDER_SLUG']),
    //             'download' => route('documents.dao.download', ['id' => 'PLACEHOLDER_ID'])
    //         ],
    //         'autoSelectedCategories' => $autoSelectedCategories,
    //         'autoSelectedCount' => $autoSelectedCount,
    //         'allCategories' => $allCategories,
    //     ]);
    // }

    public function search(Request $request): JsonResponse
    {
        Log::debug('Search request received for "DAO" documents', [
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

        $categoryName = "DAO"; // Specific category for search
        $targetCategoryIds = Category::where('name', $categoryName)->pluck('id')->toArray();

        // Build the base query, filtered by the 'DAO' category
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
        // Note: These will be added to the 'DAO' filter already in place
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
                    'show' => 'documents.dao.show', 
                    'download' => 'documents.dao.download' 
                ]
            ])->render(),
            'pagination' => $documents->links('pagination::bootstrap-4')->toHtml()
        ]);
    }

    /**
     * Displays a document inline in the browser.
     * Updated with Voyager's JSON path handling logic.
     */
    public function show(string $slug): Response
    {
        $document = Document::where('slug', $slug)->firstOrFail();

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
            Log::error("File path in database is not a string for document (show method - DAO)", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'The requested file path is misconfigured.');
        }

        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("File not found on disk for display (show method - DAO)", [
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
            Log::error("File path in database is not a string for document (download method - DAO)", [
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
            Log::error("File not found on disk for download (download method - DAO)", [
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