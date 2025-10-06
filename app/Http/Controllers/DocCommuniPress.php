<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category; // Assurez-vous que le modèle Category est inclus
use Illuminate\Http\Request;
use App\Services\DocumentSearchService;

class DocCommuniPress extends Controller
{
    protected $searchService;

    // Injecter le service dans le contrôleur
    public function __construct(DocumentSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index()
    {
        $title = 'Documents Communiqués de presse';

        // Récupérer tous les documents liés à la catégorie 'Actifs financiers de l\'État'
        $documents = Document::with('categories')
            ->whereHas('categories', function ($query) {
                $query->where('name', 'Communiqués de presse');
            })
            ->orderBy('date_publication', 'desc')
            ->paginate(44);

        $hasCategory = $documents->isNotEmpty();

        // Récupérer les IDs des catégories 'Actifs financiers de l\'État'
        $autoSelectedCategories = Category::where('name', 'Communiqués de presse')->pluck('id')->toArray();
        $searchRoute = route('documents.search.presse');

        return view('frontend.pages.page_documents', compact('documents','searchRoute', 'title', 'hasCategory', 'autoSelectedCategories'));
    }

    public function search(Request $request)
    {
        $categoryName = "Communiqués de presse";
        $query = $request->input('query', '');
        $filters = $request->input('filters', []);
    
        $documentsQuery = Document::with('categories')
            ->whereHas('categories', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
    
        if (isset($filters['months']) && count($filters['months']) > 0) {
            $months = implode(',', array_map('intval', $filters['months']));
            $documentsQuery->whereRaw("MONTH(date_publication) IN ($months)");
        }
    
        if (isset($filters['years']) && count($filters['years']) > 0) {
            $years = implode(',', array_map('intval', $filters['years']));
            $documentsQuery->whereRaw("YEAR(date_publication) IN ($years)");
        }
    
        if (isset($filters['categories']) && count($filters['categories']) > 0) {
            $categories = array_map('intval', $filters['categories']);
            $documentsQuery->whereHas('categories', function ($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }
    
        if (!empty($query)) {
            $documentsQuery->where('title', 'like', '%' . $query . '%');
        }
    
        $documentsPaginated = $documentsQuery->orderBy('date_publication', 'desc')->paginate(44);
    
        // Génération du HTML côté serveur (en PHP)
        $html = '';
        foreach ($documentsPaginated as $document) {
            // Récupérer mois et année formatés
            $month = strtolower(\Carbon\Carbon::parse($document->date_publication)->translatedFormat('F'));
            $year = \Carbon\Carbon::parse($document->date_publication)->format('Y');
            $categoriesIds = implode(',', $document->categories->pluck('id')->toArray());
            $categoriesNames = $document->categories->pluck('name')->implode(', ');
    
            $html .= '<div class="col-12 col-md-4 col-lg-3 mb-4 document-card" ' .
                     'data-title="' . strtolower($document->title) . '" ' .
                     'data-categories="' . $categoriesIds . '" ' .
                     'data-month="' . $month . '" ' .
                     'data-year="' . $year . '">' .
                     '<div class="card h-100 shadow-sm border-0">' .
                     '<div class="card-body d-flex flex-column">' .
                     '<a href="' . $document->download_link . '" class="text-decoration-none text-dark d-flex align-items-center gap-2 mb-3">' .
                     '<i class="fas fa-file-pdf fa-2x text-danger"></i>' .
                     '<h3 class="card-title mb-0" style="font-size: 0.9rem">' . ucfirst(strtolower($document->title)) . '</h3>' .
                     '</a>' .
                     '<p class="download-count text-muted mb-2">' .
                     '<i class="fas fa-file-download"></i> ' . $document->download_count . ' téléchargements' .
                     '</p>' .
                     '<p class="card-category text-secondary mb-1">' .
                     'Catégories : <span class="text-danger">' . $categoriesNames . '</span>' .
                     '</p>' .
                     '<p class="card-date text-muted mb-4">' .
                     '<strong>Publié le ' . \Carbon\Carbon::parse($document->date_publication)->translatedFormat('d F Y') . '</strong>' .
                     '</p>' .
                     '<a href="' . $document->download_link . '" download class="btn btn-danger mt-auto">' .
                     '<i class="fas fa-download"></i> Télécharger' .
                     '</a>' .
                     '</div></div></div>';
        }
    
        // Transformer la collection pour ne garder que les champs utiles en JSON
        $documentsTransformed = $documentsPaginated->getCollection()->transform(function ($document) {
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
        $documentsPaginated->setCollection($documentsTransformed);
    
        // Vérifier si un seul document a une seule catégorie
        $isSingleCategory = false;
        if ($documentsPaginated->count() === 1) {
            $doc = $documentsPaginated->first();
            $isSingleCategory = $doc['category'] !== 'Aucune catégorie trouvée' && count(explode(',', $doc['category'])) === 1;
        }
    
        return response()->json([
            'status' => 'success',
            'documents' => $documentsPaginated,
            'count' => $documentsPaginated->count(),
            'isSingleCategory' => $isSingleCategory,
            'message' => $documentsPaginated->count() > 0 ? 'Documents trouvés' : 'Aucun document trouvé',
            'html' => $html,  // Ajout du HTML généré au JSON
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
