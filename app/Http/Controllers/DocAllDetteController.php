<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\DocumentSearchService; // Assurez-vous que ce service est bien utilisé si besoin
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class DocAllDetteController extends Controller
{
    protected DocumentSearchService $searchService;

    public function __construct(DocumentSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(): View
    {
        $title = 'Documents : Bulletins statistiques, Stratégies d\'endettement et Dette publique';
        
        $categoryNames = [
            'Bulletin statistique', 
            'Stratégie d\'endettement', // Vérifiez l'orthographe exacte dans la DB
            'Dette publique'
        ];

        $targetCategoryIds = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();

        // Récupérer toutes les catégories ayant des documents, ordonnées par nom
        // C'est cette variable qui alimente le dropdown, assurez-vous que 'Stratégie d\'endettement' y est
        $allCategories = Category::whereHas('documents')->orderBy('name')->get();

        $documents = Document::with('categories')
            ->whereHas('categories', function ($query) use ($targetCategoryIds) {
                $query->whereIn('categories.id', $targetCategoryIds); 
            })
            ->orderBy('date_publication', 'desc')
            ->paginate(44);

        // Cette variable détermine quelles catégories sont pré-sélectionnées et désactivées
        $autoSelectedCategories = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();
        
        $bladeRouteNames = [
            'show' => 'documents.all_dette.show', 
            'download' => 'documents.all_dette.download',
        ];
        
        return view('frontend.pages.page_documents', [
            'title' => $title,
            'documents' => $documents,
            'routes' => $bladeRouteNames,
            'jsRoutes' => [
                'search' => route('documents.all_dette.search'),
                'show' => route('documents.all_dette.show', ['slug' => 'PLACEHOLDER_SLUG']),
                'download' => route('documents.all_dette.download', ['id' => 'PLACEHOLDER_ID'])
            ],
            'autoSelectedCategories' => $autoSelectedCategories,
            'autoSelectedCount' => count($autoSelectedCategories),
            'allCategories' => $allCategories, // Assurez-vous que cette variable contient bien "Stratégie d'endettement"
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        Log::debug('Search request received', [
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

        $categoryNames = [
            'Bulletin statistique', 
            'Stratégie d\'endettement', 
            'Dette publique'
        ];

        $targetCategoryIds = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();

        $documentsQuery = Document::with('categories')
            ->whereHas('categories', function ($query) use ($targetCategoryIds) {
                $query->whereIn('categories.id', $targetCategoryIds);
            });

        // Appliquer la recherche textuelle si une requête est fournie
        if (!empty($validated['query'])) {
            $searchTerm = $validated['query'];
            // NETTOYAGE ET RECHERCHE PLUS INTELLIGENTE :
            // 1. Supprime tous les caractères qui ne sont pas des lettres, chiffres ou espaces.
            // 2. Remplace les groupes d'espaces par un seul espace.
            // 3. Transforme la chaîne en tableau de mots.
            $cleanSearchTerm = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $searchTerm); // Garde lettres, chiffres, espaces
            $cleanSearchTerm = preg_replace('/\s+/', ' ', $cleanSearchTerm); // Réduit les multiples espaces
            $keywords = explode(' ', trim($cleanSearchTerm));

            // Applique une clause WHERE pour chaque mot-clé
            $documentsQuery->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    if (mb_strlen($keyword) > 2) { // Ignore les mots très courts (ex: "un", "le")
                        $q->where(function ($subQ) use ($keyword) {
                            $subQ->where('title', 'like', '%' . $keyword . '%')
                                 ->orWhere('description', 'like', '%' . $keyword . '%');
                        });
                    }
                }
            });
            // Si vous utilisez la recherche Full-Text de MySQL, cela ressemblerait à ceci :
            /*
            if (count($keywords) > 0) {
                $fullTextQuery = implode('* ', $keywords) . '*'; // "mot1* mot2*"
                $documentsQuery->whereRaw("MATCH(title, description) AGAINST(? IN BOOLEAN MODE)", [$fullTextQuery]);
            }
            */
        }

        if (isset($validated['filters']['years']) && count($validated['filters']['years']) > 0) {
            $documentsQuery->whereIn(\DB::raw('YEAR(date_publication)'), $validated['filters']['years']);
        }
        if (isset($validated['filters']['months']) && count($validated['filters']['months']) > 0) {
            $documentsQuery->whereIn(\DB::raw('MONTH(date_publication)'), $validated['filters']['months']);
        }
        if (isset($validated['filters']['categories']) && count($validated['filters']['categories']) > 0) {
            $documentsQuery->whereHas('categories', function ($q) use ($validated) {
                $q->whereIn('categories.id', $validated['filters']['categories']);
            });
        }

        $documents = $documentsQuery->orderBy('date_publication', 'desc')->paginate(44);

        return response()->json([
            'html' => view('frontend.partials.documents_list', [
                'documents' => $documents,
                'routes' => [
                    'show' => 'documents.all_dette.show', 
                    'download' => 'documents.all_dette.download' 
                ]
            ])->render(),
            'pagination' => $documents->links('pagination::bootstrap-4')->toHtml()
        ]);
    }

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
            Log::error("Le chemin du fichier dans la base de données n'est pas une chaîne pour le document", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'Le chemin du fichier demandé est mal configuré.');
        }

        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("Fichier non trouvé sur le disque pour affichage", [
                'document_id' => $document->id,
                'extracted_path_used' => $realFilePath,
                'original_db_value' => $filePathInDb,
                'storage_exists_check' => Storage::disk('public')->exists($realFilePath) ? 'true' : 'false'
            ]);
            abort(404, 'Le fichier demandé est introuvable ou a été supprimé du serveur.');
        }

        $filePath = Storage::disk('public')->path($realFilePath);
        $mimeType = Storage::disk('public')->mimeType($realFilePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

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
            Log::error("Le chemin du fichier dans la base de données n'est pas une chaîne pour le document", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'Le chemin du fichier est mal configuré.');
        }

        $downloadedDocuments = session()->get('downloaded_documents', []);
        if (!in_array($document->id, $downloadedDocuments)) {
            $document->increment('download_count');
            session()->push('downloaded_documents', $document->id);
        }

        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("Fichier introuvable sur le disque pour téléchargement", [
                'document_id' => $document->id,
                'extracted_path_used' => $realFilePath,
                'original_db_value' => $filePathInDb,
                'storage_exists_check' => Storage::disk('public')->exists($realFilePath) ? 'true' : 'false'
            ]);
            abort(404, 'Le fichier demandé est introuvable ou a été supprimé du serveur.');
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