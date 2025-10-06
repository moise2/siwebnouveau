<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category; // Assurez-vous que le modèle Category est inclus
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DocumentSearchService;

class DocCommuniquePresseController extends Controller
{
    protected $searchService;

    // Injecter le service dans le contrôleur
    public function __construct(DocumentSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index()
    {
        $title = 'Documents  Communiqués de presse';

        // Récupérer tous les documents liés à la catégorie 'Budget Citoyen'
        $documents = Document::with('categories')
            ->whereHas('categories', function ($query) {
                $query->where('name', 'Communiqués de presse');
            })
            ->orderBy('date_publication', 'desc')
            ->paginate(44);

        $hasCategory = $documents->isNotEmpty();

        // Récupérer les IDs des catégories 'Budget citoyen'
        $autoSelectedCategories = Category::where('name', 'Communiqués de presse')->pluck('id')->toArray();

        // Compter le nombre de catégories auto-sélectionnées
        $autoSelectedCount = count($autoSelectedCategories);

        return view('frontend.pages.page_documents', compact('documents', 'title', 'hasCategory', 'autoSelectedCategories', 'autoSelectedCount'));
    }



    public function search(Request $request)
    {
        $category = 'Communiqués de presse';

        $query = $request->input('query', '');
        $filters = $request->input('filters', []);

        // Utiliser le service de recherche pour filtrer les documents
        $documents = $this->searchService->searchByCategory($category, $query, $filters);
        $isSingleCategory = count($documents) === 1 && isset($documents[0]->categories) && count($documents[0]->categories) === 1; // Vérifier si une seule catégorie a été trouvée

        return response()->json([
            'status' => 'success',
            'documents' => $documents,
            'isSingleCategory' => $isSingleCategory, // Ajouter cette ligne
            'message' => count($documents) > 0 ? 'Documents trouvés' : 'Aucun document trouvé'
        ]);
    }


    public function show($slug)
    {
        // Rechercher le document par son slug
        $document = Document::where('slug', $slug)->firstOrFail();

        // Chemin vers le fichier dans le stockage (assume que le fichier est stocké dans le dossier "public" de storage)
        $filePath = storage_path('app/public/' . $document->file_path);

        // Vérifier si le fichier existe
        if (!file_exists($filePath)) {
            abort(404, 'Le fichier est introuvable.');
        }

        // Détecter le type MIME du fichier (utile pour l'affichage dans le navigateur)
        $mimeType = mime_content_type($filePath);

        // Si le fichier est un PDF ou un fichier que le navigateur peut afficher, on l'ouvre
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }



    public function download($id)
    {
        // Récupérer le document par son ID
        $document = Document::findOrFail($id);

        // Utiliser la session pour vérifier si l'utilisateur a déjà téléchargé ce document
        $downloadedDocuments = session()->get('downloaded_documents', []);

        // Vérifier si le document a déjà été téléchargé dans la session courante
        if (!in_array($id, $downloadedDocuments)) {
            // Incrémenter le compteur de téléchargements seulement si ce n'est pas déjà fait
            $document->increment('download_count');

            // Ajouter l'ID du document dans la session pour éviter l'incrémentation multiple
            session()->push('downloaded_documents', $id);
        }

        // Chemin du fichier à partir du champ file_path
        $filePath = storage_path('app/public/' . $document->file_path);

        // Vérifier si le fichier existe
        if (!file_exists($filePath)) {
            return abort(404, 'Fichier non trouvé.');
        }

        // Téléchargement du fichier
        return response()->download($filePath, $document->title . '.' . $document->file_type);
    }
}
