<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Acteur;
use App\Models\Institution;
use App\Models\Localite;
use App\Models\LocaliteProgramme;
use App\Models\LocaliteProjet;
use App\Models\Programme;
use App\Models\Projet;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = 'home';

    public function __construct()
    {
        //$this->middleware('check.utilisateur');
    }

    public function showLoginForm()
    {
        return view('frontend.pages.page_connexion');
    }




    public function login(Request $request)
    {
        
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $utilisateur = Auth::guard('utilisateur')->user();
                
                // if (!$utilisateur->is_active()) {
                //     Auth::guard('utilisateur')->logout();
                //     return redirect()->back()->with('error', 'Votre compte est en attente d\'approbation par un administrateur.');
                // }
            return $this->sendLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

public function logout(Request $request)
{
    Auth::logout(); // Déconnexion de l'utilisateur
    $request->session()->invalidate(); // Invalider la session actuelle
    $request->session()->regenerateToken(); // Régénérer le token CSRF pour éviter la réutilisation

    return redirect('connexion')->with('status', 'Vous avez été déconnecté avec succès.');
}

public function home()
{
    // Récupérer l'utilisateur connecté
    $utilisateur = Auth::guard('utilisateur')->user();

    $loaclite = Localite::all();
    $loaclite_programme = LocaliteProgramme::all();


    // Récupérer l'institution liée à l'utilisateur
    $institution = Acteur::where('id', $utilisateur->nom)->first();
   

    if ($institution) {
        // Récupérer les projets et programmes associés à cette institution via acteur_id
        $projets = Projet::where('acteur_id', $institution->id)->with(['projetlocalite','institution'])->get();
        $programmes = Programme::where('acteur_id', $institution->id)->with(['programmelocalite','institution'])->get();

    
        

        // Récupérer les années de début et de fin des projets et programmes
        // $years = collect();

        // // Ajouter les années des projets
        // foreach ($projets as $projet) {
        //     $years->push(date('Y', strtotime($projet->date_debut)));
        //     $years->push(date('Y', strtotime($projet->date_fin)));
        // }

        // Ajouter les années des programmes
        // foreach ($programmes as $programme) {
        //     $years->push(date('Y', strtotime($programme->date_debut)));
        //     $years->push(date('Y', strtotime($programme->date_fin)));
        // }

        // Trouver l'année minimale et maximale
        // $plageAnnees = [
        //     'min' => $years->min(),
        //     'max' => $years->max(),
        // ];
     } //else {
    //     $projets = [];
    //     $programmes = [];
    //     $plageAnnees = [
    //         'min' => date('Y'),
    //         'max' => date('Y'),
    //     ];
    // }

    // Passer les données à la vue
    
    return view('frontend.pages.page_profil', compact('utilisateur', 'projets', 'programmes', 'institution'));
}





    protected function attemptLogin(Request $request)
    {
        return Auth::guard('utilisateur')->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    protected function credentials(Request $request){
        $credentials = $request->only($this->username(), 'password');
        $credentials['is_active'] = 1;
        return $credentials;
    }




    protected function guard()
    {
        return Auth::guard('utilisateur');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        //dd("test");
        $user = \App\Models\Utilisateur::where($this->username(), $request->{$this->username()})->first();
        if ($user && $user->is_active == 0) {
            return redirect()->back()->withErrors([
                $this->username() => trans('auth.inactive'),
            ]);
        }
    
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => [trans('auth.failed')],
            ]);
    }

    public function redirectPath()
    {
        return $this->redirectTo;
    }



    public function connexion()
    {
        // Renvoie la vue de connexion avec les institutions disponibles
        //$institutions = \App\Models\Institution::all();
        return view('frontend.pages.page_connexion');
    }
}
