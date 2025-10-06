<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;  // Le modèle spécifique Utilisateur
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CustomRegisterController extends Controller
{
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest:utilisateur')->except('logout');
        $this->middleware('check.utilisateur')->only('profil');
    }
    

    // Validation des données de l'utilisateur lors de l'inscription
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nom' => ['required', 'string', 'max:255'],
            'prenoms' => ['required', 'string', 'max:255'],
            'sexe' => ['required', 'string', 'in:homme,femme'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:utilisateurs'],  // Utilisateur spécifique
            'contact' => ['required', 'string', 'max:15'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    // Création d'un utilisateur
    protected function create(array $data)
    {
        // Crée un utilisateur spécifique avec les données validées
        return Utilisateur::create([
            'nom' => $data['nom'],
            'prenoms' => $data['prenoms'],
            'sexe' => $data['sexe'],
            'email' => $data['email'],
            'contact' => $data['contact'],
            'password' => Hash::make($data['password']),
            'approved' => false,  // En attente d'approbation par un administrateur
        ]);
    }

    // Fonction d'enregistrement
    public function register(Request $request)
    {
        // Validation des données d'enregistrement
        $this->validator($request->all())->validate();

        // Création de l'utilisateur
        $utilisateur = $this->create($request->all());

        // Retour de la réponse JSON indiquant que l'enregistrement a réussi
        return response()->json([
            "message" => "Votre compte a été créé avec succès et est en attente d'approbation par un administrateur."
        ], 200); // Code 200 pour succès
    }

    // Méthode de connexion


    // Méthode alternative pour l'enregistrement (si besoin)
    public function registerAlternative(Request $request)
    {
        // Validation des données envoyées
        $validator = Validator::make($request->all(), [
            'nom' => ['required', 'string', 'max:255'],
            'prenoms' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:utilisateurs'],  // Utilisateur spécifique
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:15'],
        ]);

        // Gestion des erreurs de validation
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Création de l'utilisateur
        $utilisateur = Utilisateur::create([
            'nom' => $request->input('nom'),
            'prenoms' => $request->input('prenoms'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
            'approved' => false,  // Toujours en attente d'approbation
        ]);

        // Retour de la réponse JSON
        return response()->json([
            "message" => "Votre compte a été créé avec succès avec la méthode alternative et est en attente d'approbation.",
            "utilisateur" => $utilisateur
        ], 200); // Code 200 pour succès
    }
}
