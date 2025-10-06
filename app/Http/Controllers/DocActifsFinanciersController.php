<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\DocumentSearchService;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class DocActifsFinanciersController extends Controller
{
    protected DocumentSearchService $searchService;

    /**
     * Inject the document search service.
     */
    public function __construct(DocumentSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Display the initial page for "Actifs financiers de l'Etat" documents.
     */
    public function index(): View
    {
        $title = 'Documents Actifs financiers de l\'Etat';
        $categoryName = 'Actifs financiers de l\'Etat'; // Specific category for this controller

        // Retrieve documents using the DocumentSearchService for initial display
        // The service should handle the category filtering internally if designed that way
        // Or, you can explicitly pass the category name for the service to use.
        // Assuming searchService->search() can take a category name as its first argument
        $documents = $this->searchService->search($categoryName); 

        // Retrieve auto-selected categories (Actifs financiers de l'Etat) for the view
        $autoSelectedCategories = Category::where('name', $categoryName)->pluck('id')->toArray();
        
        // Retrieve all categories that have documents, ordered by name, for the filter dropdown
        $allCategories = Category::whereHas('documents')->orderBy('name')->get();

        // Prepare routes for Blade and JavaScript
        $bladeRouteNames = [
            'show' => 'documents.actifs_financiers.show',
            'download' => 'documents.actifs_financiers.download',
        ];
        
        return view('frontend.pages.page_documents', [
            'title' => $title,
            'documents' => $documents,
            'routes' => $bladeRouteNames, // For the Blade partial view if included
            'jsRoutes' => [ // For JavaScript
                'search' => route('documents.actifs_financiers.search'),
                'show' => route('documents.actifs_financiers.show', 'PLACEHOLDER_SLUG'), // Placeholder for JS dynamic route
                'download' => route('documents.actifs_financiers.download', 'PLACEHOLDER_ID') // Placeholder for JS dynamic route
            ],
            'autoSelectedCategories' => $autoSelectedCategories,
            'autoSelectedCount' => count($autoSelectedCategories),
            'allCategories' => $allCategories, // For the filter list
        ]);
    }

    /**
     * Handle AJAX search requests for "Actifs financiers de l'Etat" documents.
     */
    public function search(Request $request): JsonResponse
    {
        Log::debug('Search request received for "Actifs financiers de l\'Etat" documents', [
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

        $categoryName = 'Actifs financiers de l\'Etat';

        // Use the search service to get documents based on category, query, and filters
        $documents = $this->searchService->search(
            $categoryName,
            $validated['query'] ?? '',
            $validated['filters'] ?? []
        );

        // Return results as JSON
        return response()->json([
            'html' => view('frontend.partials.documents_list', [
                'documents' => $documents,
                'routes' => [ // Pass routes for correct links in the partial
                    'show' => 'documents.actifs_financiers.show',
                    'download' => 'documents.actifs_financiers.download'
                ]
            ])->render(),
            'pagination' => $documents->links('pagination::bootstrap-4')->toHtml()
        ]);
    }

    /**
     * This method fetches ALL documents for the 'Actifs financiers de l\'Etat' category without pagination.
     * Useful for API consumption or specific data needs.
     */
    public function all(): JsonResponse
    {
        $categoryName = "Actifs financiers de l'Etat";
        $documents = Document::with('categories')
            ->whereHas('categories', function ($query) use ($categoryName) {
                $query->where('name', $categoryName);
            })
            ->orderBy('date_publication', 'desc')
            ->get(); // Get all, no pagination for this endpoint

        // Transform the collection to a simplified format for JSON output
        $documentsTransformed = $documents->transform(function ($document) {
            return [
                'title' => $document->title,
                'id' => $document->id,
                'category' => $document->categories->pluck('name')->implode(', ') ?: 'Aucune catégorie trouvée',
                'slug' => $document->slug,
                'created_at' => $document->created_at->toDateTimeString(),
                'file_url' => asset('storage/' . $document->file_path), // Adjust if file_path isn't directly usable here
                'date_publication' => $document->date_publication,
                'download_count' => $document->download_count,
            ];
        });

        return response()->json($documentsTransformed);
    }

    /**
     * Displays a document inline in the browser.
     * Includes robust handling for Voyager's JSON file paths and category validation.
     */
    public function show(string $slug): Response
    {
        $categoryName = 'Actifs financiers de l\'Etat';

        // Retrieve the document, ensuring it belongs to the specified category
        $document = Document::where('slug', $slug)
            ->whereHas('categories', function ($query) use ($categoryName) {
                $query->where('name', $categoryName);
            })
            ->firstOrFail();

        $filePathInDb = $document->file_path; 
        $realFilePath = null;

        // Logic to extract the real file path from DB (handles Voyager's JSON format)
        if (is_string($filePathInDb)) {
            $decodedPath = json_decode($filePathInDb, true);
            if (is_array($decodedPath) && !empty($decodedPath) && isset($decodedPath[0]['download_link'])) {
                $realFilePath = str_replace('\\', '/', $decodedPath[0]['download_link']);
            } else {
                $realFilePath = $filePathInDb; // Assume simple string path
            }
        } else {
            Log::error("File path in database is not a string for document (show method - Actifs financiers)", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'The requested file path is misconfigured.');
        }

        // Verify that the real file path was extracted and the file exists on disk
        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("File not found on disk for display (show method - Actifs financiers)", [
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
     * Includes robust handling for Voyager's JSON file paths and category validation.
     */
    public function download(int $id): BinaryFileResponse
    {
        $document = Document::findOrFail($id);

        // Ensure the document belongs to this controller's specific category
        $categoryName = 'Actifs financiers de l\'Etat';
        if (!$document->categories->contains('name', $categoryName)) {
            Log::warning("Attempt to download document from wrong category (download method - Actifs financiers)", [
                'document_id' => $id,
                'actual_categories' => $document->categories->pluck('name')->toArray(),
                'expected_category' => $categoryName
            ]);
            abort(404, 'Document not found in the specified category.');
        }

        $filePathInDb = $document->file_path;
        $realFilePath = null;
        $downloadFilename = $document->title; // Fallback for download filename

        // Logic to extract the real file path and original name from DB (handles Voyager's JSON format)
        if (is_string($filePathInDb)) {
            $decodedPath = json_decode($filePathInDb, true);
            if (is_array($decodedPath) && !empty($decodedPath) && isset($decodedPath[0]['download_link'])) {
                $realFilePath = str_replace('\\', '/', $decodedPath[0]['download_link']);
                $downloadFilename = $decodedPath[0]['original_name'] ?? $document->title; // Use original Voyager filename
            } else {
                $realFilePath = $filePathInDb; // Assume simple string path
            }
        } else {
            Log::error("File path in database is not a string for document (download method - Actifs financiers)", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'The file path is misconfigured.');
        }

        // Increment download count (once per session per document)
        $downloadedDocuments = session()->get('downloaded_documents', []);
        if (!in_array($document->id, $downloadedDocuments)) {
            $document->increment('download_count');
            session()->push('downloaded_documents', $document->id);
        }

        // Verify that the real file path was extracted and the file exists on disk
        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("File not found on disk for download (download method - Actifs financiers)", [
                'document_id' => $document->id,
                'extracted_path_used' => $realFilePath,
                'original_db_value' => $filePathInDb,
                'storage_exists_check' => Storage::disk('public')->exists($realFilePath) ? 'true' : 'false'
            ]);
            abort(404, 'The requested file is not found or has been removed from the server.');
        }

        $absolutePath = Storage::disk('public')->path($realFilePath);
        $extension = pathinfo($realFilePath, PATHINFO_EXTENSION); 

        // Clean the filename for download
        if ($downloadFilename) {
            $cleanTitle = preg_replace('/[^A-Za-z0-9.\-_]/', '_', pathinfo($downloadFilename, PATHINFO_FILENAME));
            $finalDownloadFilename = $cleanTitle . '.' . $extension;
        } else {
            $finalDownloadFilename = 'document_telecharge.' . $extension;
        }

        return response()->download($absolutePath, $finalDownloadFilename);
    }
}