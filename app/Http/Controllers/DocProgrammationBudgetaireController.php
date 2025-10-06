<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\DocumentSearchService; // Ensure this service is correctly implemented
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class DocProgrammationBudgetaireController extends Controller
{
    protected DocumentSearchService $searchService;

    public function __construct(DocumentSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Displays the initial page for "Programmation budgétaire" documents.
     */
    public function index(): View
    {
        $title = 'Documents Programmation budgétaire';
        $categoryName = 'Programmation budgétaire'; // Specific category for this controller

        // Delegate document retrieval to the DocumentSearchService
        // The service should handle the category filtering and initial pagination (e.g., 44 items)
        $documents = $this->searchService->search($categoryName);

        // Retrieve all categories that have documents, ordered by name, for the filter dropdown
        $allCategories = Category::whereHas('documents')->orderBy('name')->get();

        // Auto-selected categories ('Programmation budgétaire' in this case) for the view
        $autoSelectedCategories = Category::where('name', $categoryName)->pluck('id')->toArray();
        $autoSelectedCount = count($autoSelectedCategories);
        
        // Prepare routes for Blade and JavaScript
        $bladeRouteNames = [
            'show' => 'documents.programmation_budgetaire.show',
            'download' => 'documents.programmation_budgetaire.download',
        ];
        
        return view('frontend.pages.page_documents', [
            'title' => $title,
            'documents' => $documents,
            'routes' => $bladeRouteNames, // For the Blade partial view if included
            'jsRoutes' => [ // For JavaScript
                'search' => route('documents.programmation_budgetaire.search'),
                'show' => route('documents.programmation_budgetaire.show', 'PLACEHOLDER_SLUG'), // Placeholder for dynamic routing
                'download' => route('documents.programmation_budgetaire.download', 'PLACEHOLDER_ID') // Placeholder for dynamic routing
            ],
            'autoSelectedCategories' => $autoSelectedCategories,
            'autoSelectedCount' => $autoSelectedCount,
            'allCategories' => $allCategories, // For the filter list
        ]);
    }

    /**
     * This method fetches ALL documents for the 'Programmation budgétaire' category without pagination.
     */
    public function all(): JsonResponse
    {
        $categoryName = "Programmation budgétaire";
        $documents = Document::with('categories')
            ->whereHas('categories', function ($query) use ($categoryName) {
                $query->where('name', $categoryName);
            })
            ->orderBy('date_publication', 'desc')
            ->get(); // Get all, no pagination for this endpoint

        // Transform the collection for API-friendly JSON output
        $documentsTransformed = $documents->transform(function ($document) {
            return [
                'title' => $document->title,
                'id' => $document->id,
                'category' => $document->categories->pluck('name')->implode(', ') ?: 'Aucune catégorie trouvée',
                'slug' => $document->slug,
                'created_at' => $document->created_at->toDateTimeString(),
                'file_url' => asset('storage/' . $document->file_path), // Ensure this path is correct for direct access
                'date_publication' => $document->date_publication,
                'download_count' => $document->download_count,
            ];
        });

        return response()->json($documentsTransformed);
    }

    /**
     * Handles AJAX search requests for "Programmation budgétaire" documents.
     * Delegates the heavy lifting to the DocumentSearchService.
     */
    public function search(Request $request): JsonResponse
    {
        Log::debug('Search request received for "Programmation budgétaire" documents (delegated to service)', [
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

        $categoryName = "Programmation budgétaire"; // Specific category for search

        // Delegate the actual search logic to the DocumentSearchService
        $documents = $this->searchService->search(
            $categoryName,
            $validated['query'] ?? '',
            $validated['filters'] ?? []
        );

        // Return results as JSON for AJAX requests
        return response()->json([
            'html' => view('frontend.partials.documents_list', [
                'documents' => $documents,
                'routes' => [ // Pass routes for correct links in the partial
                    'show' => 'documents.programmation_budgetaire.show', 
                    'download' => 'documents.programmation_budgetaire.download' 
                ]
            ])->render(),
            'pagination' => $documents->links('pagination::bootstrap-4')->toHtml()
        ]);
    }

    /**
     * Displays a document inline in the browser.
     * Includes robust handling for Voyager's JSON file paths and correct category validation.
     */
    public function show(string $slug): Response
    {
        $categoryName = 'Programmation budgétaire';

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
            Log::error("File path in database is not a string for document (show method - Programmation budgétaire)", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'The requested file path is misconfigured.');
        }

        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("File not found on disk for display (show method - Programmation budgétaire)", [
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
     * Includes robust handling for Voyager's JSON file paths and correct category validation.
     */
    public function download(int $id): BinaryFileResponse
    {
        $document = Document::findOrFail($id);

        // Ensure the document belongs to this controller's specific category
        $categoryName = 'Programmation budgétaire';
        if (!$document->categories->contains('name', $categoryName)) {
            Log::warning("Attempt to download document from wrong category (download method - Programmation budgétaire)", [
                'document_id' => $id,
                'actual_categories' => $document->categories->pluck('name')->toArray(),
                'expected_category' => $categoryName
            ]);
            abort(404, 'Document not found in the specified category.');
        }

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
            Log::error("File path in database is not a string for document (download method - Programmation budgétaire)", [
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
            Log::error("File not found on disk for download (download method - Programmation budgétaire)", [
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