<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\Acteur;
use App\Models\Category; // Assurez-vous d'utiliser le modèle Category si vous traduisez ses noms
use App\Models\Document;
use App\Models\Post;
use App\Models\Event;
use Carbon\Carbon;
use App\Models\Api;
use App\Models\AxeStrategique;
use App\Models\Token;
use App\Models\ChiffreCle;
use App\Models\Etat;
use App\Models\ExecutionPhysiqueFinanciere;
use App\Models\Synthese;
use App\Models\Localite;
use App\Models\Twitter;
use App\Models\LocaliteProjet;
use App\Models\LocaliteProgramme;
use App\Models\Priorite;
use App\Models\Projet;
use App\Models\Programme;
use App\Models\Reforme;
use App\Models\WebNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\TwitterService;
use App\Services\TranslatorService; // <-- NOUVELLE LIGNE : Importez votre TranslatorService
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log; // Pour les logs de débogage
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HomeController extends Controller
{
    protected $twitterService;
    protected $translatorService; // <-- NOUVELLE LIGNE : Propriété pour le TranslatorService

    // <-- NOUVEAU BLOC : Constructeur pour injecter TranslatorService
    public function __construct(TranslatorService $translatorService)
    {
        $this->translatorService = $translatorService;
    }
    // FIN NOUVEAU BLOC

    public function getChartData($axeId, $year) {
        // Filtrer les réformes selon l'axe et l'année
        $reformes = Reforme::where('axe_strategique_id', $axeId)
            ->where('annee', $year) // Utilisation de la colonne 'annee'
            ->selectRaw("count(id) as nb, etat_avancement")
            ->groupBy('etat_avancement')
            ->get();

        // Traduire les états d'avancement si nécessaire
        $targetLang = App::getLocale(); // Récupère la locale actuelle
        $translatedReformes = $reformes->map(function ($reforme) use ($targetLang) {
            $reforme->etat_avancement = $this->translatorService->translate($reforme->etat_avancement, $targetLang);
            return $reforme;
        });

        $labels = [];
        $data = [];
        foreach ($translatedReformes as $reforme) { // Utilisez la collection traduite
            array_push($labels, $reforme->etat_avancement);
            array_push($data, $reforme->nb);
        }

        // Calculer les counts pour le graphique camembert
        $statusCounts = [
            $this->translatorService->translate('Terminé', $targetLang) => 0,
            $this->translatorService->translate('En cours', $targetLang) => 0,
            $this->translatorService->translate('Non démarré', $targetLang) => 0
        ];
        foreach ($translatedReformes as $reforme) { // Utilisez la collection traduite
            if ($reforme->etat_avancement == $this->translatorService->translate('Terminé', $targetLang)) {
                $statusCounts[$this->translatorService->translate('Terminé', $targetLang)]++;
            } elseif ($reforme->etat_avancement == $this->translatorService->translate('En cours', $targetLang)) {
                $statusCounts[$this->translatorService->translate('En cours', $targetLang)]++;
            } else {
                $statusCounts[$this->translatorService->translate('Non démarré', $targetLang)]++;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'statusCounts' => $statusCounts, // Compter les statuts pour le graphique camembert
        ];
    }

    public function getData(Request $request) {
        $axe = $request->input('axe'); // Récupérer l'ID de l'axe
        $year = $request->input('year'); // Récupérer l'année sélectionnée

        // Récupérer les réformes selon l'axe et l'année
        $reformes = Reforme::with('institution', 'axeStrategique')
            ->where('axe_strategique_id', $axe)
            ->where('annee', $year) // Utilisation de la colonne 'annee'
            ->get();

        // Traduire les attributs des réformes
        $targetLang = App::getLocale();
        $reformes->each(function ($reforme) use ($targetLang) {
            if (isset($reforme->name)) { // Supposons que les réformes ont un champ 'name' ou 'title'
                $reforme->name = $this->translatorService->translate($reforme->name, $targetLang);
            }
            if (isset($reforme->description)) {
                $reforme->description = $this->translatorService->translate($reforme->description, $targetLang);
            }
            if (isset($reforme->etat_avancement)) {
                $reforme->etat_avancement = $this->translatorService->translate($reforme->etat_avancement, $targetLang);
            }
            // Traduire le nom de l'axe stratégique si présent
            if ($reforme->axeStrategique && isset($reforme->axeStrategique->name)) {
                $reforme->axeStrategique->name = $this->translatorService->translate($reforme->axeStrategique->name, $targetLang);
            }
            // Traduire le nom de l'institution si présent
            if ($reforme->institution && isset($reforme->institution->name)) {
                $reforme->institution->name = $this->translatorService->translate($reforme->institution->name, $targetLang);
            }
        });

        // Récupérer les données de graphique
        $chartData = $this->getChartData($axe, $year);

        return response()->json([
            'axe' => AxeStrategique::find($axe), // Le nom de l'axe devra être traduit dans la vue si utilisé
            'reformes' => $reformes,
            'chartData' => $chartData
        ]);
    }

    public function index(Request $request)
    {
        $lastTweet = null;
        $tweets = [];

        try {
            // Récupérer les tweets en utilisant le package thujohn/twitter
            $tweets = Twitter::getUserTimeline([
                'screen_name' => 'togoreforme', // Nom d'utilisateur Twitter
                'count' => 2,                    // Nombre de tweets à récupérer
            ]);

            if (!empty($tweets)) {
                $lastTweet = $tweets[0]; // Récupère le premier tweet
            } else {
                $lastTweet = "Aucun tweet trouvé pour cet utilisateur.";
            }
        } catch (\Exception $e) {
            $lastTweet = "Erreur de connexion avec l'API Twitter : " . $e->getMessage();
        }

        $currentYear = date('Y');
        $years = range($currentYear - 10, $currentYear);

        // Si vous utilisez TwitterService pour le dernier tweet, assurez-vous qu'il est injecté
        // ou instanciez-le si nécessaire. Pour l'instant, je laisse votre appel existant.
        $twitterService = new TwitterService();
        $lastTweet = Twitter::getwitt(); // Note : cette ligne semble redondante avec le bloc try/catch ci-dessus

        // Récupérer les catégories
        // Si ces "catégories" sont en fait des Posts filtrés par catégorie,
        // nous traduirons les attributs du Post (titre, contenu, etc.)
        // $categoriesPosts = Post::whereHas('categories', function ($query) {
        //     $query->where('slug', 'reformes');
        // })
        // ->orderBy('published_at', 'desc')
        // ->take(4)
        // ->get();

        // // Traduire les posts de la collection $categoriesPosts
        // $categoriesPosts->each(function ($post) {
        //     $post->title = $this->translatorService->translate($post->title);
        //     // Ajoutez d'autres champs de Post à traduire si besoin, ex: $post->body
        //     if (isset($post->body)) {
        //         $post->body = $this->translatorService->translate($post->body);
        //     }
        //     // Si vous affichez les noms des catégories réelles associées au post
        //     $post->categories->each(function ($category) {
        //         if (isset($category->name)) {
        //             $category->name = $this->translatorService->translate($category->name);
        //         }
        //     });
        // });

        // Récupérer les articles
        $articles = Post::with('categories')->whereHas('categories', function ($query) {
            $query->where(DB::raw('LOWER(name)'), 'like', '%réformes%');
        })->latest('published_at')->take(4)->get();

        // Traduire les articles
        $articles->each(function ($article) {
            $article->title = $this->translatorService->translate($article->title);
            if (isset($article->body)) { // Supposons que Post a un champ 'body' pour le contenu
                $article->body = $this->translatorService->translate($article->body);
            }
             // Traduire les catégories associées aux articles (si utilisées)
            $article->categories->each(function ($category) {
                if (isset($category->name)) {
                    $category->name = $this->translatorService->translate($category->name);
                }
            });
        });

        // Récupérer les publications (Documents)
        $publications = Document::latest()->take(3)->get();

        // Traduire les publications
        $publications->each(function ($document) {
            $document->title = $this->translatorService->translate($document->title);
            if (isset($document->description)) { // Si Document a un champ 'description'
                $document->description = $this->translatorService->translate($document->description);
            }
        });

        // Récupérer les fichiers pour le slider
        // Note: slide_files() retourne une collection mixte d'articles, documents et événements.
        // La traduction devra être appliquée DANS slide_files() ou APRÈS son appel pour chaque type.
        $slides = $this->slide_files();

        // Traduire les articles, documents et événements récupérés par slide_files()
        if (isset($slides['articles'])) {
            $slides['articles']->each(function ($article) {
                if (isset($article->title)) $article->title = $this->translatorService->translate($article->title);
                if (isset($article->body)) $article->body = $this->translatorService->translate($article->body);
                // Traduire les catégories des articles dans les slides si elles sont affichées
                $article->categories->each(function ($category) {
                    if (isset($category->name)) {
                        $category->name = $this->translatorService->translate($category->name);
                    }
                });
            });
        }
        if (isset($slides['documents'])) {
            $slides['documents']->each(function ($document) {
                if (isset($document->title)) $document->title = $this->translatorService->translate($document->title);
                if (isset($document->description)) $document->description = $this->translatorService->translate($document->description);
            });
        }
        if (isset($slides['events'])) {
            $slides['events']->each(function ($event) {
                if (isset($event->title)) $event->title = $this->translatorService->translate($event->title);
                if (isset($event->description)) $event->description = $this->translatorService->translate($event->description);
            });
        }


        // Récupérer et traduire les événements principaux
        $events = Event::latest()->take(3)->get();

        $events->each(function ($event) {
            $event->title = $this->translatorService->translate($event->title);
            if (isset($event->description)) {
                $event->description = $this->translatorService->translate($event->description);
            }
        });

        $currentDate = Carbon::now();

        // Récupérer et traduire les notifications web
        $notifications = WebNotification::where('is_active', true)
            ->where(function ($query) use ($currentDate) {
                $query->where('start_time', '<=', $currentDate)
                    ->orWhereNull('start_time');
            })
            ->where(function ($query) use ($currentDate) {
                $query->where('end_time', '>=', $currentDate)
                    ->orWhereNull('end_time');
            })
            ->get();

        $notifications->each(function ($notification) {
            if (isset($notification->message)) {
                $notification->message = $this->translatorService->translate($notification->message);
            }
            if (isset($notification->title)) {
                $notification->title = $this->translatorService->translate($notification->title);
            }
        });

       
        // Passer toutes les données à la vue

        
        return view('frontend.pages.home', [
            'slides' => $slides,
            'articles' => $articles,
            'publications' => $publications,
            // 'categories' => $categoriesPosts, // Utilisez $categoriesPosts si c'est ce que vous passez
            'events' => $events,
            'tweets' => $tweets,
            'lastTweet' => $lastTweet,
            'notifications'=> $notifications,
        ]);
    }

    public function changeLang($lang)
    {
        // 1. (Optionnel mais recommandé) Vérifiez si la langue est supportée
        $supportedLocales = array_keys(config('app.locales'));
        if (!in_array($lang, $supportedLocales)) {
            Log::warning("Tentative de changement vers une langue non supportée: " . $lang);
            abort(400, 'Langue non supportée.');
        }

        // 2. Définir la locale dans la session
        Session::put('locale', $lang);

        // 3. Définir la locale pour la requête actuelle de Laravel
        App::setLocale($lang);

        // 4. Rediriger l'utilisateur vers la page précédente
        return Redirect::back();
    }


    public function slide_files()
    {
        // Récupérer les 3 derniers articles publiés
        $articles = Post::with('categories')
            ->latest()
            ->take(3)
            ->get();

        // Récupérer les 2 derniers documents publiés dans les 7 derniers jours
        $documents = Document::where('created_at', '>=', Carbon::now()->subDays(7))
            ->latest()
            ->take(2)
            ->get()
            ->map(function ($document) {
                $filePath = $document->file_path;

                // Vérifier si file_path est un JSON et extraire le bon chemin
                $decoded = json_decode($filePath, true);
                if (is_array($decoded) && isset($decoded[0]['download_link'])) {
                    $filePath = $decoded[0]['download_link'];
                }

                // Normaliser les slashes (`\` → `/`)
                $filePath = str_replace('\\', '/', $filePath);

                // Si ce n'est pas une URL externe, corriger le chemin
                if (!Str::startsWith($filePath, ['http://', 'https://'])) {
                    $filePath = preg_replace('#^storage/#', '', ltrim($filePath, '/')); // Supprime un éventuel "storage/" en trop
                    $filePath = asset("storage/" . $filePath); // Générer l'URL correcte
                }

                $document->download_link = $filePath;
                return $document;
            });

        // Récupérer les 2 derniers événements publiés dans les 7 derniers jours
        $events = Event::where('created_at', '>=', Carbon::now()->subDays(7))
            ->latest()
            ->take(2)
            ->get();

        // NOTE: La traduction de ces objets est maintenant gérée dans la méthode 'index()'
        // après l'appel à 'slide_files()'. Ne traduisez pas ici pour éviter la double traduction.
        return [
            'articles' => $articles,
            'documents' => $documents,
            'events' => $events,
        ];
    }

    public function showTweets()
    {
        // Récupérer les derniers tweets de l'utilisateur 'togoreforme'
        $tweets = Twitter::userTimeline([
            'screen_name' => 'togoreforme', // Remplacez par le nom d'utilisateur
            'count' => 1, // Nombre de tweets à afficher
            'format' => 'array'
        ]);

        return view('tweets.index', compact('tweets'));
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
                'extracted_path_used' => $realFilePath,
                'original_db_value' => $filePathInDb,
                'storage_exists_check' => Storage::disk('public')->exists($realFilePath) ? 'true' : 'false'
            ]);
            abort(404, 'Le fichier demandé est introuvable ou a été supprimé du serveur.');
        }

        // Récupère le chemin absolu du fichier pour le téléchargement
        $absolutePath = Storage::disk('public')->path($realFilePath);

        // Prépare un nom de fichier propre pour le téléchargement
        $finalDownloadFilename = $downloadFilename;
        $extension = pathinfo($realFilePath, PATHINFO_EXTENSION);

        if ($finalDownloadFilename) {
            $cleanTitle = preg_replace('/[^A-Za-z0-9.\-_]/', '_', pathinfo($finalDownloadFilename, PATHINFO_FILENAME));
            $finalDownloadFilename = $cleanTitle . '.' . $extension;
        } else {
            $finalDownloadFilename = 'document_telecharge.' . $extension;
        }

        // Retourne la réponse de téléchargement
        return response()->download($absolutePath, $finalDownloadFilename);
    }
}