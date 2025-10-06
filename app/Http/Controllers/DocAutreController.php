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
use Illuminate\Pagination\LengthAwarePaginator; // Assurez-vous que c'est bien nécessaire si vous ne l'utilisez pas directement
use Illuminate\Support\Collection; // Assurez-vous que c'est bien nécessaire
use Carbon\Carbon;

class DocAutreController extends Controller
{
    protected DocumentSearchService $searchService;

    public function __construct(DocumentSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(): View
    {
        $title = 'Documents Autres';
        $categoryName = 'Autres'; // Catégorie spécifique pour ce contrôleur

        // Définir les catégories cibles pour ce contrôleur
        $categoryNames = [
            $categoryName,
        ];

        // Récupérer les IDs des catégories cibles
        $targetCategoryIds = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();

        // Récupérer tous les documents appartenant à la catégorie 'Autres'
        // Applique la pagination directement ici pour l'affichage initial
        $documents = Document::with('categories')
            ->whereHas('categories', function ($query) use ($targetCategoryIds) {
                $query->whereIn('categories.id', $targetCategoryIds);
            })
            ->orderBy('date_publication', 'desc')
            ->paginate(44);
        
        // Toutes les catégories avec des documents pour le filtre dropdown
        $allCategories = Category::whereHas('documents')->orderBy('name')->get();

        // Les catégories auto-sélectionnées (Autres dans ce cas) pour la vue
        $autoSelectedCategories = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();
        $autoSelectedCount = count($autoSelectedCategories);
        
        // Préparation des routes pour Blade et JavaScript
        $bladeRouteNames = [
            'show' => 'documents.autres.show',
            'download' => 'documents.autres.download',
        ];
        
        return view('frontend.pages.page_documents', [
            'title' => $title,
            'documents' => $documents,
            'routes' => $bladeRouteNames, // Pour la vue partielle Blade si elle est incluse
            'jsRoutes' => [ // Pour le JavaScript
                'search' => route('documents.autres.search'),
                'show' => route('documents.autres.show', ['slug' => 'PLACEHOLDER_SLUG']),
                'download' => route('documents.autres.download', ['id' => 'PLACEHOLDER_ID'])
            ],
            'autoSelectedCategories' => $autoSelectedCategories,
            'autoSelectedCount' => $autoSelectedCount,
            'allCategories' => $allCategories, // Pour la liste des filtres
        ]);
    }

    // La méthode 'all' n'était pas présente dans les contrôleurs précédents,
    // si elle est nécessaire, veuillez fournir sa logique.
    // Pour l'instant, elle sera vide ou retirée si non utilisée.
    public function all(): View
    {
        // Cette méthode semble être une route supplémentaire "/all" qui n'était pas détaillée.
        // Si elle doit afficher tous les documents "Autres" sans filtre initial, elle pourrait ressembler à ceci:
        return $this->index(); // Ou une logique spécifique si différente
    }

    public function search(Request $request): JsonResponse
    {
        Log::debug('Search request received for "Autres" documents', [
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

        $categoryName = "Autres"; // Catégorie spécifique pour la recherche
        $targetCategoryIds = Category::where('name', $categoryName)->pluck('id')->toArray();

        // Construction de la requête de base, filtrée par la catégorie 'Autres'
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
        }

        // Appliquer les filtres supplémentaires (années, mois, autres catégories)
        if (isset($validated['filters']['years']) && count($validated['filters']['years']) > 0) {
            $documentsQuery->whereIn(\DB::raw('YEAR(date_publication)'), $validated['filters']['years']);
        }
        if (isset($validated['filters']['months']) && count($validated['filters']['months']) > 0) {
            $documentsQuery->whereIn(\DB::raw('MONTH(date_publication)'), $validated['filters']['months']);
        }
        
        // Si l'utilisateur a sélectionné des catégories supplémentaires via le dropdown
        // Note: Celles-ci s'ajouteront au filtre 'Autres' déjà en place
        if (isset($validated['filters']['categories']) && count($validated['filters']['categories']) > 0) {
            $documentsQuery->whereHas('categories', function ($q) use ($validated) {
                $q->whereIn('categories.id', $validated['filters']['categories']);
            });
        }

        // Exécuter la requête avec pagination
        $documents = $documentsQuery->orderBy('date_publication', 'desc')->paginate(44);

        // Retourner les résultats en JSON
        return response()->json([
            'html' => view('frontend.partials.documents_list', [
                'documents' => $documents,
                'routes' => [
                    'show' => 'documents.autres.show', 
                    'download' => 'documents.autres.download' 
                ]
            ])->render(),
            'pagination' => $documents->links('pagination::bootstrap-4')->toHtml()
        ]);
    }

    /**
     * Affiche un document dans le navigateur (inline).
     * Mise à jour avec la logique de gestion des chemins JSON de Voyager.
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
            Log::error("Le chemin du fichier dans la base de données n'est pas une chaîne pour le document (méthode show - Autres)", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'Le chemin du fichier demandé est mal configuré.');
        }

        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("Fichier non trouvé sur le disque pour affichage (méthode show - Autres)", [
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

    /**
     * Gère le téléchargement d'un document.
     * Mise à jour avec la logique de gestion des chemins JSON de Voyager.
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
            Log::error("Le chemin du fichier dans la base de données n'est pas une chaîne pour le document (méthode download - Autres)", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'Le chemin du fichier est mal configuré.');
        }

        // Logique d'incrémentation du compteur de téléchargements
        $downloadedDocuments = session()->get('downloaded_documents', []);
        if (!in_array($document->id, $downloadedDocuments)) {
            $document->increment('download_count');
            session()->push('downloaded_documents', $document->id);
        }

        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("Fichier introuvable sur le disque pour téléchargement (méthode download - Autres)", [
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