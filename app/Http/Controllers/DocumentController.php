<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\DocumentSearchService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Pagination\LengthAwarePaginator; // Import the Paginator


class DocumentController extends Controller
{
    protected $searchService;

    // Injecter le service dans le contrôleur
    public function __construct(DocumentSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(): View
    {
        $title = 'Tous les Documents'; // Le titre reflète désormais "Tous les Documents"

        // Appelle le service de recherche sans spécifier de nom de catégorie
        // Le premier argument est la catégorie, que nous voulons ici être inexistante
        // pour récupérer tous les documents.
        $documents = $this->searchService->search(
            null, // categoryName est null pour tous les documents, y compris ceux sans catégorie
            '', // Requête de recherche vide par défaut
            [] // Filtres vides par défaut
        );

        // Puisqu'il n'y a pas de catégorie auto-sélectionnée spécifique au départ,
        // les catégories auto-sélectionnées sont vides.
        $autoSelectedCategories = [];
        // Récupérer toutes les catégories, même celles sans documents associés,
        // si vous voulez qu'elles apparaissent dans le filtre du frontend,
        // sinon `whereHas('documents')` est correct si vous ne voulez que les catégories qui ont des documents.
        // Pour "tous les documents même sans catégorie", la liste des catégories à filtrer doit inclure toutes les catégories existantes.
        // $allCategories = Category::orderBy('name')->get();
        $allCategories = Category::whereHas('documents')->orderBy('name')->get();


        // Préparation claire des routes pour Blade et pour JavaScript,
        // en utilisant les routes générales.
        $bladeRouteNames = [
            'show' => 'documents.general.show',     // Route générale pour l'affichage
            'download' => 'documents.general.download', // Route générale pour le téléchargement
        ];

        
        // `jsRoutes` pour le script (utilise des URLs complètes ou des templates)
        return view('frontend.pages.page_documents', [
            'title' => $title,
            'documents' => $documents,
            'routes' => $bladeRouteNames,           // Pour la vue partielle Blade
            'jsRoutes' => [                         // Pour le JavaScript
                'search' => route('documents.general.search'), // Route générale pour la recherche
                'show' => route('documents.general.show', 'PLACEHOLDER_SLUG'),
                'download' => route('documents.general.download', 'PLACEHOLDER_ID')
            ],
            'autoSelectedCategories' => $autoSelectedCategories,
            'autoSelectedCount' => count($autoSelectedCategories),
            'allCategories' => $allCategories,
        ]);
    }

    public function show(string $slug): BinaryFileResponse
    {
        $document = Document::where('slug', $slug)->firstOrFail();

        // --- Début de la logique de récupération du chemin du fichier ---
        $filePathInDb = $document->file_path; // Get the raw value from the DB

        $realFilePath = null;

        if (is_string($filePathInDb)) {
            $decodedPath = json_decode($filePathInDb, true);

            // Check if it's an array (from JSON decode) and if it has Voyager's format
            if (is_array($decodedPath) && !empty($decodedPath) && isset($decodedPath[0]['download_link'])) {
                // It's the new Voyager format (JSON)
                // The path is in 'download_link'. Replace backslashes with forward slashes.
                $realFilePath = str_replace('\\', '/', $decodedPath[0]['download_link']);
            } else {
                // It's the old format (simple string) or another non-JSON known format
                $realFilePath = $filePathInDb;
            }
        } else {
            // If $filePathInDb is not a valid string
            Log::error("File path in database is not a string for document (show method)", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'The requested file path is misconfigured.');
        }
        // --- End of file path retrieval logic ---

        // Verify that the real file path was extracted and the file exists
        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("File not found on disk for display (show method)", [
                'document_id' => $document->id,
                'extracted_path_used' => $realFilePath, // The path we attempted to use
                'original_db_value' => $filePathInDb,  // The raw DB value
                'storage_exists_check' => Storage::disk('public')->exists($realFilePath) ? 'true' : 'false'
            ]);
            abort(404, 'The requested file is not found or has been removed from the server.');
        }

        // Get the absolute path for the response
        $filePath = Storage::disk('public')->path($realFilePath);

        // MIME Type detection is more reliable with Laravel's method
        $mimeType = Storage::disk('public')->mimeType($realFilePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            // 'inline' will try to display the file in the browser, 'attachment' will force download
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }


    public function search(Request $request)
    {
        \Log::debug('Search request received', [
            'query' => $request->input('query'),
            'filters' => $request->input('filters'),
            'page' => $request->input('page'),
            'wantsJson' => $request->wantsJson(),
            'isAjax' => $request->ajax()
        ]);

        $validated = $request->validate([
            'query' => 'nullable|string',
            'filters' => 'nullable|array',
            'page' => 'nullable|integer' // Ajoutez la pagination
        ]);

        // La recherche s'applique maintenant à tous les documents, y compris ceux sans catégorie.
        $documents = $this->searchService->search(
            null, // Le nom de la catégorie est null pour rechercher sur tous les documents
            $validated['query'] ?? '',
            $validated['filters'] ?? []
        );

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('frontend.partials.documents_list', [
                    'documents' => $documents,
                    'routes' => [
                        'show' => 'documents.general.show',     // Utilisation des routes générales
                        'download' => 'documents.general.download' // Utilisation des routes générales
                    ]
                ])->render(),
                'pagination' => $documents->links('pagination::bootstrap-4')->toHtml()
            ]);
        }

        return back();
    }


    public function download(int $id): BinaryFileResponse
    {
        $document = Document::findOrFail($id);

        // --- Début de la logique de récupération du chemin du fichier ---
        $filePathInDb = $document->file_path; // Récupère la valeur brute de la BDD

        $realFilePath = null;
        $downloadFilename = $document->title; // Fallback pour le nom du fichier téléchargé

        if (is_string($filePathInDb)) {
            // Tente de décoder le JSON
            $decodedPath = json_decode($filePathInDb, true);

            // Vérifie si le décodage a réussi et si c'est le format de tableau d'objets de Voyager
            if (is_array($decodedPath) && !empty($decodedPath) && isset($decodedPath[0]['download_link'])) {
                // C'est le nouveau format Voyager (JSON)
                // Le chemin est dans 'download_link'. Remplace les backslashes par des forward slashes.
                $realFilePath = str_replace('\\', '/', $decodedPath[0]['download_link']);
                // Définit le nom de fichier de téléchargement à partir du nom original de Voyager si disponible
                $downloadFilename = $decodedPath[0]['original_name'] ?? $document->title;

            } else {
                // C'est l'ancien format (simple chaîne de caractères)
                $realFilePath = $filePathInDb;
                // Le nom de fichier est par défaut le titre du document, ou sera dérivé plus bas.
            }
        } else {
            // Si $filePathInDb n'est pas une chaîne valide
            Log::error("Chemin de fichier dans la base de données n'est pas une chaîne pour le document", [
                'document_id' => $document->id,
                'path_in_db' => $filePathInDb
            ]);
            abort(404, 'Le chemin du fichier est mal configuré.');
        }
        // --- Fin de la logique de récupération du chemin du fichier ---

        // Logique d'incrémentation du compteur de téléchargements
        $downloadedDocuments = session()->get('downloaded_documents', []);
        if (!in_array($document->id, $downloadedDocuments)) {
            $document->increment('download_count');
            session()->push('downloaded_documents', $document->id);
        }

        // Vérifie si le chemin réel a été extrait et si le fichier existe
        if (!$realFilePath || !Storage::disk('public')->exists($realFilePath)) {
            Log::error("Fichier introuvable sur le disque pour téléchargement", [
                'document_id' => $document->id,
                'extracted_path_used' => $realFilePath, // Le chemin que nous avons tenté d'utiliser
                'original_db_value' => $filePathInDb,  // La valeur brute de la BDD
                'storage_exists_check' => Storage::disk('public')->exists($realFilePath) ? 'true' : 'false'
            ]);
            abort(404, 'Le fichier demandé est introuvable ou a été supprimé du serveur.');
        }

        // Récupère le chemin absolu du fichier pour le téléchargement
        $absolutePath = Storage::disk('public')->path($realFilePath);

        // Prépare un nom de fichier propre pour le téléchargement
        // Utilise le nom de fichier défini plus tôt, sinon le titre du document
        // Assure une extension correcte
        $finalDownloadFilename = $downloadFilename;
        $extension = pathinfo($realFilePath, PATHINFO_EXTENSION); // Assurez-vous d'avoir une extension

        // Nettoie le nom du fichier pour le téléchargement
        if ($finalDownloadFilename) {
            $cleanTitle = preg_replace('/[^A-Za-z0-9.\-_]/', '_', pathinfo($finalDownloadFilename, PATHINFO_FILENAME));
            $finalDownloadFilename = $cleanTitle . '.' . $extension;
        } else {
            // Si le nom original n'a pas pu être déterminé, utilise un nom générique avec l'extension
            $finalDownloadFilename = 'document_telecharge.' . $extension;
        }

        // Retourne la réponse de téléchargement
        return response()->download($absolutePath, $finalDownloadFilename);
    }
}