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

class DocRapportExecutionBudgetController extends Controller
{
    // Removed DocumentSearchService injection as it wasn't used in your provided code's search logic.
    // If you plan to use a service for search logic, re-inject it and use it.
    // For now, the direct query building remains within the search method.

    public function index(): View
    {
        $title = 'Documents Rapport d\'exécution du budget de l\'Etat';
        $categoryName = 'Rapport d\'exécution du budget de l\'Etat'; // Corrected category for this controller

        // Define the target categories for this controller
        $categoryNames = [
            $categoryName,
        ];

        // Retrieve the IDs of the target categories
        $targetCategoryIds = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();

        // Retrieve all documents linked to the 'Rapport d\'exécution du budget de l\'Etat' category
        // Apply pagination directly here for initial display
        $documents = Document::with('categories')
            ->whereHas('categories', function ($query) use ($targetCategoryIds) {
                $query->whereIn('categories.id', $targetCategoryIds);
            })
            ->orderBy('date_publication', 'desc') // Sort by date in descending order
            ->paginate(44);

        // Retrieve all categories that have documents, ordered by name, for the filter dropdown
        $allCategories = Category::whereHas('documents')->orderBy('name')->get();

        // Auto-selected categories ('Rapport d\'exécution du budget de l\'Etat' in this case) for the view
        $autoSelectedCategories = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();
        $autoSelectedCount = count($autoSelectedCategories);
        
        // Prepare routes for Blade and JavaScript
        $bladeRouteNames = [
            'show' => 'documents.rapports_execution_budget_etat.show',
            'download' => 'documents.rapports_execution_budget_etat.download',
        ];
        
        return view('frontend.pages.page_documents', [
            'title' => $title,
            'documents' => $documents,
            'routes' => $bladeRouteNames, // For the Blade partial view if included
            'jsRoutes' => [ // For JavaScript
                'search' => route('documents.rapports_execution_budget_etat.search'), // Corrected route name
                'show' => route('documents.rapports_execution_budget_etat.show', ['slug' => 'PLACEHOLDER_SLUG']),
                'download' => route('documents.rapports_execution_budget_etat.download', ['id' => 'PLACEHOLDER_ID'])
            ],
            'autoSelectedCategories' => $autoSelectedCategories,
            'autoSelectedCount' => $autoSelectedCount,
            'allCategories' => $allCategories, // For the filter list
        ]);
    }

    /**
     * Handles document search for 'Rapport d\'exécution du budget de l\'Etat' category.
     */
    public function search(Request $request): JsonResponse
    {
        Log::debug('Search request received for "Rapport d\'exécution du budget de l\'Etat" documents', [
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

        $categoryName = "Rapport d'exécution du budget de l'Etat"; // Corrected category for search
        $targetCategoryIds = Category::where('name', $categoryName)->pluck('id')->toArray();

        // Build the base query, filtered by the correct category
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
        // Note: These will be added to the 'Rapport d\'exécution du budget de l\'Etat' filter already in place
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
                    'show' => 'documents.rapports_execution_budget_etat.show', 
                    'download' => 'documents.rapports_execution_budget_etat.download' 
                ]
            ])->render(),
            'pagination' => $documents->links('pagination::bootstrap-4')->toHtml()
        ]);
    }

    /**
     * Returns all documents for the 'Rapport d\'exécution du budget de l\'Etat' category as JSON.
     */
    public function all(): JsonResponse
    {
        $categoryName = "Rapport d'exécution du budget de l'Etat"; // Corrected category
        $documents = Document::with('categories')
            ->whereHas('categories', function ($query) use ($categoryName) {
                $query->where('name', $categoryName);
            })
            ->orderBy('date_publication', 'desc')
            ->get(); // Get all, no pagination for this endpoint

        // Transform the collection to keep only useful fields for JSON output
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
     * Updated with Voyager's JSON path handling logic and correct category filtering.
     */
    public function show(string $slug): Response
    {
        $categoryName = 'Rapport d\'exécution du budget de l\'Etat'; // Corrected category

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
            Log::error("File path in database is not a string for document (show method - Rapport d'exécution du budget de l'Etat)", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'The requested file path is misconfigured.');
        }

        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("File not found on disk for display (show method - Rapport d'exécution du budget de l'Etat)", [
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
     * Updated with Voyager's JSON path handling logic and correct category filtering.
     */
    public function download(int $id): BinaryFileResponse
    {
        $document = Document::findOrFail($id);

        // Ensure the document belongs to this controller's specific category
        $categoryName = 'Rapport d\'exécution du budget de l\'Etat';
        if (!$document->categories->contains('name', $categoryName)) {
            Log::warning("Attempt to download document from wrong category (download method - Rapport d'exécution du budget de l'Etat)", [
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
            Log::error("File path in database is not a string for document (download method - Rapport d'exécution du budget de l'Etat)", [
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
            Log::error("File not found on disk for download (download method - Rapport d'exécution du budget de l'Etat)", [
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