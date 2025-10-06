<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\HomeController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ArticleReformeController;
use App\Http\Controllers\ArticleactualiteController;
use App\Http\Controllers\ArticleconseilministreController;
use App\Http\Controllers\ArticlefinanceController;
use App\Http\Controllers\ArticlesocialController;

use App\Http\Controllers\CartographieController;
use App\Http\Controllers\DocActifsFinanciersController;
use App\Http\Controllers\DocBudgetCitoyenController;
use App\Http\Controllers\DocAllDetteController;
use App\Http\Controllers\DocBudgetDepensesController;
use App\Http\Controllers\DocTofeController;
use App\Http\Controllers\DocBulletinsStatistiquesController;
use App\Http\Controllers\DocBudgetEtatController;
use App\Http\Controllers\DocAutreController;
use App\Http\Controllers\DocBudgetProgrammeController;
use App\Http\Controllers\DocBudgetRecetteController;
use App\Http\Controllers\DocBudgetSensibleGenreController;
use App\Http\Controllers\DocBudgetVertController;
use App\Http\Controllers\DocCommuniquePresseController;
use App\Http\Controllers\DocLoiFinancesController;
use App\Http\Controllers\DocPrevisionRecettesController;
use App\Http\Controllers\DocProgrammationBudgetaireController;
use App\Http\Controllers\DocRapportCourDesComptesController;
use App\Http\Controllers\DocRapportsReformesController;
use App\Http\Controllers\DocStrategieEndettementController;
use App\Http\Controllers\DocRapportExecutionBudgetController;
use App\Http\Controllers\DocDaoController;
use App\Http\Controllers\DocAmiController;
use App\Http\Controllers\DocDettePubliqueController;
use App\Http\Controllers\DocLoisController;
use App\Http\Controllers\LangueController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContactController;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\TwitterController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ApiProxyController;
use App\Http\Controllers\DocProjetsLoisFinancesController;
use App\Http\Controllers\DocRapportdetteController;
use App\Http\Controllers\DocLoisDecretsController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\OrganigrammeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\TestOsr2Controller;



Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home/download/{id}', [HomeController::class, 'download'])->name('documents.download.home');
Route::get('/tweets',);


Route::get('/api/get/token',)->name('api.get.token');
Route::get('/api/get/programmes', [HomeController::class, 'getProgrammes'])->name('api.get.programmes');
Route::get('/api/get/chiffrescles', [HomeController::class, 'getChiffresCles'])->name('api.get.chiffrescles');
Route::get('/api/get/localites', [HomeController::class, 'getLocalites'])->name('api.get.localites');
Route::post('/api/import/all', [ImportController::class, 'importAll'])->name('api.import.all');


// routes/web.php (ou api.php)
Route::get('/api/test/login', [TestOsr2Controller::class,'loginAndShow']);
Route::get('/api/test/protected', [TestOsr2Controller::class,'callProtected']);





//... autres routes

Route::get('/categorie/{slug}', [FrontendController::class, 'articlesParCategorie'])->name('page_categorie_article');


// Group for Articles
Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/', [ArticlesController::class, 'index'])->name('index');
    Route::post('/search-articles', [ArticlesController::class, 'search'])->name('search'); // Renamed from articles.search.search
    Route::get('/all-articles',)->name('all'); // Renamed from articles.all
    Route::get('/{slug}', [ArticlesController::class, 'show'])->name('show');
});


// Group for general Documents
Route::prefix('documents')->name('documents.')->group(function () {
    Route::get('/', [DocumentController::class, 'index'])->name('general.index'); // Accueil des documents
    Route::get('/all', [DocumentController::class, 'all'])->name('general.all'); // Tous les documents
    Route::get('/{slug}', [DocumentController::class, 'show'])->name('general.show'); // Détail d’un document
    Route::post('/search', [DocumentController::class, 'search'])->name('general.search'); // Recherche
    Route::get('/download/{id}', [DocumentController::class, 'download'])->name('general.download'); // Téléchargement
});



// Group for DocAllDetteController
Route::prefix('alldettedocuments')->name('documents.all_dette.')->group(function () {
    Route::get('/', [DocAllDetteController::class, 'index'])->name('index');
    Route::get('/all-documents-dette', [DocAllDetteController::class, 'all'])->name('all');
    Route::get('/{slug}', [DocAllDetteController::class, 'show'])->name('show');
    Route::post('/search', [DocAllDetteController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocAllDetteController::class, 'download'])->name('download');
});


// Group for DocDettePubliqueController
Route::prefix('documents-dette')->name('documents.dette_publique.')->group(function () {
    Route::get('/', [DocDettePubliqueController::class, 'index'])->name('index'); 
    Route::get('/apropos', [DocDettePubliqueController::class, 'apropos'])->name('apropos'); // Nouvelle route ajoutée
    Route::get('/all-documents-dette', [DocDettePubliqueController::class, 'all'])->name('all'); // This route exists in your original route block
    Route::get('/{slug}', [DocDettePubliqueController::class, 'show'])->name('show'); 
    Route::post('/search', [DocDettePubliqueController::class, 'search'])->name('search'); 
    Route::get('/download/{id}', [DocDettePubliqueController::class, 'download'])->name('download'); 
});


// Group for DocDaoController
Route::prefix('dao-documents')->name('documents.dao.')->group(function () {
    Route::get('/', [DocDaoController::class, 'index'])->name('index');
    Route::get('/all-documents-dao', [DocDaoController::class, 'all'])->name('all'); // This route exists in your original route block
    Route::get('/{slug}', [DocDaoController::class, 'show'])->name('show');
    Route::post('/search', [DocDaoController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocDaoController::class, 'download'])->name('download');
});


// Group for DocAmiController
Route::prefix('ami-documents')->name('documents.ami.')->group(function () {
    Route::get('/', [DocAmiController::class, 'index'])->name('index'); 
    Route::get('/all-documents-ami', [DocAmiController::class, 'all'])->name('all'); 
    Route::get('/{slug}', [DocAmiController::class, 'show'])->name('show'); 
    Route::post('/search', [DocAmiController::class, 'search'])->name('search'); 
    Route::get('/download/{id}', [DocAmiController::class, 'download'])->name('download'); // à activer si nécessaire
});


// Routes pour les rapports d'exécution du budget (already grouped, just ensure names are unique)
Route::prefix('rapport-execution-du-budget')->group(function () {
    Route::get('/',)->name('docrapportexecutionbudget.index');
    Route::post('/search',)->name('docrapportexecutionbudget.search');
    Route::get('/download/{id}',)->name('docrapportexecutionbudget.download');
    Route::get('/{slug}',)->name('docrapportexecutionbudget.show');
});


Route::prefix('rapport-dette')->name('documents.rapport_dette.')->group(function () {
    Route::get('/', [DocRapportdetteController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocRapportdetteController::class, 'show'])->name('show');
    Route::get('/all', [DocRapportdetteController::class, 'all'])->name('all'); // Added an 'all' route for consistency
    Route::post('/search', [DocRapportdetteController::class, 'search'])->name('search'); // Corrected name
    Route::get('/download/{id}', [DocRapportdetteController::class, 'download'])->name('download');
});


// Route pour afficher la page de l'agenda avec la liste des événements
Route::get('/agenda', [EventsController::class, 'index'])->name('agenda');

// Route pour afficher les détails d'un événement spécifique par son slug
Route::get('/events/{slug}', [EventsController::class, 'show'])->name('events.show');

// Route pour effectuer la recherche d'événements (utilisée par le formulaire AJAX)
Route::post('/search-evenements', [EventsController::class, 'search'])->name('events.search');


// Route pour afficher la page de recherche
Route::get('/recherches',[SearchController::class, 'index' ])->name('search.index');

// Route pour la recherche via AJAX
Route::post('/search', [SearchController::class, 'search'])->name('search.results');


// Group for ArticleReformeController
Route::prefix('articlereformes')->name('articlereformes.')->group(function () {
    Route::get('/', [ArticleReformeController::class, 'index'])->name('index');
    Route::get('/{slug}', [ArticleReformeController::class, 'show'])->name('show');
    Route::get('/all', [ArticleReformeController::class, 'all'])->name('all');
    Route::post('/search', [ArticleReformeController::class, 'search'])->name('search');
});


// Group for ArticleactualiteController
Route::prefix('actualites')->name('actualites.')->group(function () {
    Route::get('/', [ArticleactualiteController::class, 'index'])->name('index');
    Route::get('/{slug}', [ArticleactualiteController::class, 'show'])->name('show');
    Route::get('/search', [ArticleactualiteController::class, 'search'])->name('search');
});


// Group for ArticlefinanceController
Route::prefix('economie-finance')->name('articles.economie_finance.')->group(function () { // Renamed name prefix
    Route::get('/', [ArticlefinanceController::class, 'index'])->name('index');
    Route::get('/{slug}', [ArticlefinanceController::class, 'show'])->name('show');
    Route::get('/search', [ArticlefinanceController::class, 'search'])->name('search');
});


// Group for ArticleconseilministreController
Route::prefix('conseil-des-ministres')->name('conseilministre.')->group(function () {
    Route::get('/', [ArticleconseilministreController::class, 'index'])->name('index');
    Route::get('/{slug}', [ArticleconseilministreController::class, 'show'])->name('show');
});


// Group for ArticlesocialController
Route::prefix('sociale')->name('sociale.')->group(function () {
    Route::get('/', [ArticlesocialController::class, 'index'])->name('index');
    Route::get('/{slug}', [ArticlesocialController::class, 'show'])->name('show');
    Route::get('/search', [ArticlesocialController::class, 'search'])->name('search');
});


// Group for DocBulletinsStatistiquesController
Route::prefix('bulletin-statistique')->name('documents.bulletins_statistiques.')->group(function () {
    Route::get('/', [DocBulletinsStatistiquesController::class, 'index'])->name('index');
    Route::get('/download/{id}', [DocBulletinsStatistiquesController::class, 'download'])->name('download');
    Route::get('/{slug}', [DocBulletinsStatistiquesController::class, 'show'])->name('show');
    Route::post('/search', [DocBulletinsStatistiquesController::class, 'search'])->name('search');
    Route::get('/all', [DocBulletinsStatistiquesController::class, 'all'])->name('all');
});




// Group for DocRapportExecutionBudgetController (second instance, already has a prefix group, just ensure names are unique within it)
// The existing group for 'rapport-execution-du-budget' already handles its routes.
// I will ensure the names are unique by using the prefix.
// The original code had a duplicate `Route::get('documents/{slug}',...)` outside the prefix group.
// I will assume the intention is to have all routes for this controller under its prefix.
Route::prefix('rapport-execution-du-budget')->name('documents.rapports_execution_budget_etat.')->group(function () {
    Route::get('/', [DocRapportExecutionBudgetController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocRapportExecutionBudgetController::class, 'show'])->name('show');
    Route::get('/all', [DocRapportExecutionBudgetController::class, 'all'])->name('all');
    Route::post('/search', [DocRapportExecutionBudgetController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocRapportExecutionBudgetController::class, 'download'])->name('download');
});



// Group for DocRapportsReformesController
Route::prefix('reformes')->name('documents.rapports_reformes.')->group(function () {
    Route::get('/', [DocRapportsReformesController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocRapportsReformesController::class, 'show'])->name('show');
    Route::get('/all', [DocRapportsReformesController::class, 'all'])->name('all');
    Route::post('/search', [DocRapportsReformesController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocRapportsReformesController::class, 'download'])->name('download');
});





// Group for DocStrategieEndettementController
Route::prefix('strategie-endettement')->name('documents.strategie_endettement.')->group(function () {
    Route::get('/', [DocStrategieEndettementController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocStrategieEndettementController::class, 'show'])->name('show');
    Route::get('/all', [DocStrategieEndettementController::class, 'all'])->name('all');
    Route::post('/search', [DocStrategieEndettementController::class, 'search'])->name('search'); // Corrected name
    Route::get('/download/{id}', [DocStrategieEndettementController::class, 'download'])->name('download');
});


// Group for DocLoisController
Route::prefix('lois-decrets')->name('documents.lois_decrets.')->group(function () {
    Route::get('/', [DocLoisDecretsController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocLoisDecretsController::class, 'show'])->name('show');
    Route::get('/all', [DocLoisDecretsController::class, 'all'])->name('all');
    Route::post('/search', [DocLoisDecretsController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocLoisDecretsController::class, 'download'])->name('download');
});



// Group for DocLoiFinancesController
Route::prefix('loi-de-finances')->name('documents.loi_finances.')->group(function () {
    Route::get('/', [DocLoiFinancesController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocLoiFinancesController::class, 'show'])->name('show');
    Route::get('/all', [DocLoiFinancesController::class, 'all'])->name('all');
    Route::post('/search', [DocLoiFinancesController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocLoiFinancesController::class, 'download'])->name('download');
});



// Group for DocPrevisionRecettesController (commented out)
// Route::prefix('prevision-des-recettes')->name('documents.prevision_recettes.')->group(function () {
//     Route::get('/',)->name('index'); // Renamed from documents.index
//     Route::get('/{slug}',)->name('show'); // Renamed from documents.show
//     Route::get('/all',)->name('all'); // Renamed from prevision-des-recettes.all
// });


// Group for DocBudgetProgrammeController
Route::prefix('budget-programme')->name('documents.budget_programme.')->group(function () {
    Route::get('/', [DocBudgetProgrammeController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocBudgetProgrammeController::class, 'show'])->name('show');
    Route::post('/search', [DocBudgetProgrammeController::class, 'search'])->name('search');
    // Route::get('/all', [DocBudgetProgrammeController::class, 'all'])->name('all'); // Uncomment if you implement the 'all' method
    Route::get('/download/{id}', [DocBudgetProgrammeController::class, 'download'])->name('download');
});



// Group for DocProgrammationBudgetaireController
Route::prefix('programmation-budgetaire')->name('documents.programmation_budgetaire.')->group(function () {
    Route::get('/', [DocProgrammationBudgetaireController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocProgrammationBudgetaireController::class, 'show'])->name('show');
    Route::get('/all', [DocProgrammationBudgetaireController::class, 'all'])->name('all');
    Route::post('/search', [DocProgrammationBudgetaireController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocProgrammationBudgetaireController::class, 'download'])->name('download');
});






// Group for DocBudgetVertController
Route::prefix('budget-vert')->name('documents.budget_vert.')->group(function () {
    Route::get('/', [DocBudgetVertController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocBudgetVertController::class, 'show'])->name('show');
    // Route::get('/all', [DocBudgetVertController::class, 'all'])->name('all'); // Uncomment if you implement the 'all' method
    Route::post('/search', [DocBudgetVertController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocBudgetVertController::class, 'download'])->name('download');
});


// Group for DocCommuniquePresseController
Route::prefix('communique-de-presse')->name('documents.communique_presse.')->group(function () {
    Route::get('/', [DocCommuniquePresseController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocCommuniquePresseController::class, 'show'])->name('show');
    // Route::get('/all', [DocCommuniquePresseController::class, 'all'])->name('all'); // Uncomment if you implement the 'all' method
    Route::post('/search', [DocCommuniquePresseController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocCommuniquePresseController::class, 'download'])->name('download');
});


// Group for DocActifsFinanciersController
Route::prefix('actifs-financiers')->name('documents.actifs_financiers.')->group(function () {
    Route::get('/', [DocActifsFinanciersController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocActifsFinanciersController::class, 'show'])->name('show');
    Route::get('/all', [DocActifsFinanciersController::class, 'all'])->name('all');
    Route::post('/search', [DocActifsFinanciersController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocActifsFinanciersController::class, 'download'])->name('download');
});


// Group for DocBudgetRecetteController
Route::prefix('recettes')->name('documents.recettes.')->group(function () {
    Route::get('/', [DocBudgetRecetteController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocBudgetRecetteController::class, 'show'])->name('show');
    // Route::get('/all', [DocBudgetRecetteController::class, 'all'])->name('all'); // Uncomment if you implement the 'all' method
    Route::post('/search', [DocBudgetRecetteController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocBudgetRecetteController::class, 'download'])->name('download');
});



// Group for DocBudgetSensibleGenreController
Route::prefix('budget-genre')->name('documents.budget_genre.')->group(function () {
    Route::get('/', [DocBudgetSensibleGenreController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocBudgetSensibleGenreController::class, 'show'])->name('show');
    // Route::get('/all', [DocBudgetSensibleGenreController::class, 'all'])->name('all'); // Uncomment if you implement the 'all' method
    Route::post('/search', [DocBudgetSensibleGenreController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocBudgetSensibleGenreController::class, 'download'])->name('download');
});


// Group for DocAutreController
Route::prefix('autres')->name('documents.autres.')->group(function () {
    Route::get('/', [DocAutreController::class, 'index'])->name('index');
    Route::get('/all', [DocAutreController::class, 'all'])->name('all');
    Route::get('/{slug}', [DocAutreController::class, 'show'])->name('show');
    Route::post('/search', [DocAutreController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocAutreController::class, 'download'])->name('download');
});


// Group for DocBudgetEtatController (second instance, different URI)
Route::prefix('budget-etat')->name('documents.budget_etat.')->group(function () {
    Route::get('/', [DocBudgetEtatController::class, 'index'])->name('index');
    // Route::get('/all', [DocBudgetEtatController::class, 'all'])->name('all'); // Uncomment if you implement the 'all' method
    Route::get('/{slug}', [DocBudgetEtatController::class, 'show'])->name('show');
    Route::post('/search', [DocBudgetEtatController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocBudgetEtatController::class, 'download'])->name('download');
});


// Group for DocTofeController
Route::prefix('tofe')->name('documents.tofe.')->group(function () {
    Route::get('/', [DocTofeController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocTofeController::class, 'show'])->name('show');
    Route::get('/all', [DocTofeController::class, 'all'])->name('all');
    Route::post('/search', [DocTofeController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocTofeController::class, 'download'])->name('download');
});


// Group for DocBudgetCitoyenController
Route::prefix('budget-citoyen')->name('documents.budget_citoyen.')->group(function () {
    Route::get('/', [DocBudgetCitoyenController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocBudgetCitoyenController::class, 'show'])->name('show');
    Route::get('/all', [DocBudgetCitoyenController::class, 'all'])->name('all');
    Route::post('/search', [DocBudgetCitoyenController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocBudgetCitoyenController::class, 'download'])->name('download');
});



Route::get('/cartographie', [CartographieController::class, 'index'])->name('cartographie.index');

// Routes pour le Budget Citoyen de l'État (DocBudgetDepensesController)
Route::prefix('budget-depense')->name('documents.budget_depense.')->group(function () {
    Route::get('/', [DocBudgetDepensesController::class, 'index'])->name('index');
    Route::post('/search', [DocBudgetDepensesController::class, 'search'])->name('search');
    Route::get('/download/{id}', [DocBudgetDepensesController::class, 'download'])->name('download');
    Route::get('/{slug}', [DocBudgetDepensesController::class, 'show'])->name('show'); // optionnelle
});



Route::prefix('projets-lois-finances')->name('documents.projets_lois_finances.')->group(function () {
    Route::get('/', [DocProjetsLoisFinancesController::class, 'index'])->name('index');
    Route::get('/{slug}', [DocProjetsLoisFinancesController::class, 'show'])->name('show');
    Route::get('/all', [DocProjetsLoisFinancesController::class, 'all'])->name('all'); // Changed from allDocuments
    Route::post('/search', [DocProjetsLoisFinancesController::class, 'search'])->name('search'); // Corrected name
    Route::get('/download/{id}', [DocProjetsLoisFinancesController::class, 'download'])->name('download');
});


// Route pour afficher le tableau de bord
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Route pour déconnexion
Route::post('/dashboard/deconnexion', [DashboardController::class, 'logout'])->name('logout.dashboard');

// Route pour obtenir les détails du projet (exemple d'implémentation)
Route::get('/projects/{id}',)->name('projects.show');


 Route::get('/lang/{lang}', [LangueController::class, 'changeLanguage'])->name('lang.change');


Route::get('/qui_sommes_nous', function () {
    return view('frontend.pages.page_a_propos');
})->name('qui_sommes_nous');


Route::get('/attributions', function () {
    return view('frontend.pages.page_attributions');
})->name('attributions');

Route::get('/organigramme', [OrganigrammeController::class, 'index'])->name('organigramme');


Route::get('/categorie_article', function () {
    return view('frontend.pages.page_categorie_article');
});


Route::get('/contact', [ContactController::class, 'showForm'])->name('contact');
Route::post('/contact', [ContactController::class, 'submitForm'])->name('contact.submit');


Route::get('/sp', function () {
    return view('frontend.pages.page_sp');
})->name('sp');


Route::get('/partenaire', function () {
    return view('frontend.pages.page_partenaire');
})->name('partenaire');


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    // Add your route for importing WordPress posts:
    Route::get('/importwpdocs',);
    Route::get('/import-wp-posts', [App\Http\Controllers\WordPressPostsController::class, 'importWpPosts']);
});


Route::get('/api/update/documents/date/publication',)->name('api.update.documents.date.publication');
Route::get('/dernier-tweet',)->name('tweet.show');
Route::get('/gettwitt',)->name('gettwitt');
Route::get('/change/lang/{lang}', [HomeController::class, 'changeLang'])->name('change.lang');


// Route pour récupérer les réformes selon l'axe et l'année
Route::get('/get-data',);
Route::get('/reformes/get-chart-data',);


Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');

Route::post('/registreptf',[RegisterController::class, 'register'])->name('register.ptf');


// Routes d'authentification
Route::get('/connexion', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/connexion', [LoginController::class, 'login'])->name('login.post');
Route::post('/deconnexion', [LoginController::class, 'logout'])->name('logout');

Route::post('/proxy/login', [ApiProxyController::class, 'login'])->name('proxy.login');

Route::get('/proxy/bailleurs', [ApiProxyController::class, 'getBailleurs'])->name('proxy.bailleurs');

Route::get('/proxy/bailleur/data', [ApiProxyController::class, 'getBailleurData'])->name('proxy.bailleur.data');




// Route pour récupérer les statistiques des réformes via le proxy
Route::get('/proxy/reformes-stats', [ApiProxyController::class, 'getReformesStats'])->name('proxy.reformes.stats');


Route::get('/proxy/programmes-localites', [ApiProxyController::class, 'getProgrammesLocalites'])->name('proxy.programmes.localites');

Route::get('/proxy/annees', [ApiProxyController::class, 'getListeAnnees'])->name('proxy.listeannes');


Route::get('/subscribe',)->name('subscriber.form');
Route::post('/subscribe', [SubscriberController::class, 'subscribe'])->name('subscriber.subscribe');
Route::get('/verifySubscriber/{verification_code}/{email}', [SubscriberController::class, 'verifySubscriber'])->name('subscriber.verifySubscriber');

// Afficher la liste des abonnés (par exemple pour un administrateur)
Route::get('/subscribers',)->name('subscriber.list');

