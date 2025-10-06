<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Added for logging
use Illuminate\Support\Facades\Storage; // Added for file manipulation
use App\Services\DocumentSearchService; // Ensure this service is correctly implemented
use App\Services\TranslatorService; // <-- NOUVEAU : Importez votre TranslatorService
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon; // For date formatting

class DocDettePubliqueController extends Controller
{
    protected DocumentSearchService $searchService;
    protected TranslatorService $translatorService; // <-- NOUVEAU : Déclarez le TranslatorService

    public function __construct(DocumentSearchService $searchService, TranslatorService $translatorService) // <-- NOUVEAU : Injectez-le
    {
        $this->searchService = $searchService;
        $this->translatorService = $translatorService; // <-- NOUVEAU : Initialisez-le
    }

    public function apropos()
    {
        // Pour la page 'apropos', si elle contient du contenu dynamique provenant de la base de données
        // ou des textes stockés ailleurs et que vous voulez les traduire, vous feriez :
        /*
        $originalTitle = "À Propos de la Direction de la Dette Publique";
        $originalContent = "Le contenu détaillé de la page à propos...";

        $translatedTitle = $this->translatorService->translate($originalTitle ?? ''); // Added null coalescing
        $translatedContent = $this->translatorService->translate($originalContent ?? ''); // Added null coalescing

        return view('frontend.pages.page_a_propos_dette', [
            'pageTitle' => $translatedTitle,
            'pageContent' => $translatedContent,
        ]);
        */
        // Si le contenu est principalement statique et géré par des fichiers de langue Laravel,
        // cette méthode n'a pas besoin de modifications.
        return view('frontend.pages.page_a_propos_dette');
    }

    public function index(): View
    {
        $title = 'Documents Dette Publique'; // Ce titre peut être traduit via les fichiers de langue Laravel
        $categoryName = 'Dette publique'; // Specific category for this controller

        // Define the target categories for this controller
        $categoryNames = [
            $categoryName,
        ];

        // Retrieve the IDs of the target categories
        $targetCategoryIds = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();

        // Retrieve all documents linked to the 'Dette publique' category
        // Apply pagination directly here for initial display
        $documents = Document::with('categories')
            ->whereHas('categories', function ($query) use ($targetCategoryIds) {
                $query->whereIn('categories.id', $targetCategoryIds);
            })
            ->orderBy('date_publication', 'desc')
            ->paginate(44);

        // --- NOUVEAU : Traduire les titres et descriptions des documents pour l'affichage initial ---
        $documents->getCollection()->transform(function ($document) {
            $document->title = $this->translatorService->translate($document->title ?? ''); // Corrected line
            $document->description = $this->translatorService->translate($document->description ?? ''); // Corrected line
            return $document;
        });
        // -----------------------------------------------------------------------------------------

        // Retrieve all categories that have documents, ordered by name, for the filter dropdown
        $allCategories = Category::whereHas('documents')->orderBy('name')->get();

        // --- NOUVEAU : Traduire les noms des catégories pour le filtre ---
        $allCategories->transform(function ($category) {
            $category->name = $this->translatorService->translate($category->name ?? ''); // Corrected line
            return $category;
        });
        // -----------------------------------------------------------------

        // Auto-selected categories ('Dette publique' in this case) for the view
        $autoSelectedCategories = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();
        $autoSelectedCount = count($autoSelectedCategories);

        // Prepare routes for Blade and JavaScript
        $bladeRouteNames = [
            'show' => 'documents.dette_publique.show',
            'download' => 'documents.dette_publique.download',
        ];

        return view('frontend.pages.page_documents', [
            'title' => $title, // Ce titre est une chaîne statique, pourrait être __('your.lang.key')
            'documents' => $documents,
            'routes' => $bladeRouteNames, // For the Blade partial view if included
            'jsRoutes' => [ // For JavaScript
                'search' => route('documents.dette_publique.search'),
                'show' => route('documents.dette_publique.show', ['slug' => 'PLACEHOLDER_SLUG']),
                'download' => route('documents.dette_publique.download', ['id' => 'PLACEHOLDER_ID'])
            ],
            'autoSelectedCategories' => $autoSelectedCategories,
            'autoSelectedCount' => $autoSelectedCount,
            'allCategories' => $allCategories, // For the filter list
        ]);
    }

    /**
     * This method appears in your routes as 'all-documents-dette' but was not
     * present in your controller code. It's commented out here but you might
     * want to implement it if needed, likely for showing all documents related
     * to "Dette publique" without the initial category filter constraints applied in `index()`.
     * Or, it could be redundant if `index()` covers the "all" use case.
     * I'm providing a basic structure if you decide to use it.
     */
    // public function all(): View
    // {
    //     $title = 'Tous les Documents Dette Publique';
    //     $categoryName = 'Dette publique';

    //     $documents = Document::with('categories')
    //         ->whereHas('categories', function ($query) use ($categoryName) {
    //             $query->where('name', $categoryName);
    //         })
    //         ->orderBy('date_publication', 'desc')
    //         ->paginate(44);

    //     // --- NOUVEAU : Traduire les titres et descriptions des documents ici aussi si cette méthode est active ---
    //     $documents->getCollection()->transform(function ($document) {
    //         $document->title = $this->translatorService->translate($document->title ?? ''); // Added null coalescing
    //         $document->description = $this->translatorService->translate($document->description ?? ''); // Added null coalescing
    //         return $document;
    //     });
    //     // -----------------------------------------------------------------------------------------------------

    //     $allCategories = Category::whereHas('documents')->orderBy('name')->get();

    //     // --- NOUVEAU : Traduire les noms des catégories ici aussi si cette méthode est active ---
    //     $allCategories->transform(function ($category) {
    //         $category->name = $this->translatorService->translate($category->name ?? ''); // Added null coalescing
    //         return $category;
    //     });
    //     // -----------------------------------------------------------------------------------

    //     // For the 'all' page, auto-selected categories might not be relevant,
    //     // or you might want to auto-select the main category. Adjust as needed.
    //     $autoSelectedCategories = Category::where('name', $categoryName)->pluck('id')->toArray();
    //     $autoSelectedCount = count($autoSelectedCategories);

    //     $bladeRouteNames = [
    //         'show' => 'documents.dette_publique.show',
    //         'download' => 'documents.dette_publique.download',
    //     ];

    //     return view('frontend.pages.page_documents', [
    //         'title' => $title,
    //         'documents' => $documents,
    //         'routes' => $bladeRouteNames,
    //         'jsRoutes' => [
    //             'search' => route('documents.dette_publique.search'),
    //             'show' => route('documents.dette_publique.show', ['slug' => 'PLACEHOLDER_SLUG']),
    //             'download' => route('documents.dette_publique.download', ['id' => 'PLACEHOLDER_ID'])
    //         ],
    //         'autoSelectedCategories' => $autoSelectedCategories,
    //         'autoSelectedCount' => $autoSelectedCount,
    //         'allCategories' => $allCategories,
    //     ]);
    // }

    public function search(Request $request): JsonResponse
    {
        Log::debug('Search request received for "Dette publique" documents', [
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

        $categoryName = "Dette publique"; // Specific category for search
        $targetCategoryIds = Category::where('name', $categoryName)->pluck('id')->toArray();

        // Build the base query, filtered by the 'Dette publique' category
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
        // Note: These will be added to the 'Dette publique' filter already in place
        if (isset($validated['filters']['categories']) && count($validated['filters']['categories']) > 0) {
            $documentsQuery->whereHas('categories', function ($q) use ($validated) {
                $q->whereIn('categories.id', $validated['filters']['categories']);
            });
        }

        // Execute the query with pagination
        $documents = $documentsQuery->orderBy('date_publication', 'desc')->paginate(44);

        // --- NOUVEAU : Traduire les titres et descriptions des documents pour la réponse JSON ---
        $documents->getCollection()->transform(function ($document) {
            $document->title = $this->translatorService->translate($document->title ?? ''); // Corrected line
            $document->description = $this->translatorService->translate($document->description ?? ''); // Corrected line
            return $document;
        });
        // --------------------------------------------------------------------------------------

        // Return results as JSON
        return response()->json([
            'html' => view('frontend.partials.documents_list', [
                'documents' => $documents,
                'routes' => [
                    'show' => 'documents.dette_publique.show',
                    'download' => 'documents.dette_publique.download'
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

        // --- NOUVEAU : Traduire le titre et la description du document affiché sur la page de détail ---
        $document->title = $this->translatorService->translate($document->title ?? ''); // Corrected line
        $document->description = $this->translatorService->translate($document->description ?? ''); // Corrected line
        // -------------------------------------------------------------------------------------------------

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
            Log::error("File path in database is not a string for document (show method - Dette publique)", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'The requested file path is misconfigured.');
        }

        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("File not found on disk for display (show method - Dette publique)", [
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
            Log::error("File path in database is not a string for document (download method - Dette publique)", [
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
            Log::error("File not found on disk for download (download method - Dette publique)", [
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