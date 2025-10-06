<?php
namespace App\Http\Controllers;

use App\Models\Utilisateur;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use TCG\Voyager\Models\Role;

class UtilisateurController extends Controller
{
    public function index()
    {
        $institutions = Institution::all();
        return view('frontend.pages.page_j', compact('institutions'));
    }

    public function create()
    {
        $institutions = Institution::all();
        return view('utilisateurs.create', compact('institutions'));
    }

   
    public function store(Request $request)
    {
        $institutions = Institution::all(); // Récupérer toutes les institutions
        
        // Validation des données avec messages d'erreur personnalisés
        // $request->validate([
        //     'nom' => 'required|string|max:255',
        //     'prenoms' => 'required|string|max:255',
        //     'sexe' => 'required|in:masculin,féminin',
        //     'contact' => 'required|string|max:255',
        //     'email' => 'required|email|unique:utilisateurs,email',
        //     'role' => 'required|in:PF,PTF',
        //     'password' => 'required|min:8|confirmed',
        //     'type_utilisateur' => 'required|string', // Assure-toi que ce champ est bien dans le formulaire
        //     'institution_id' => 'required|exists:institutions,id',
        //     'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // ], [
        //     'nom.required' => 'Le champ nom est obligatoire.',
        //     'prenoms.required' => 'Le champ prénoms est obligatoire.',
        //     'sexe.required' => 'Le champ sexe est obligatoire.',
        //     'sexe.in' => 'Le sexe doit être masculin ou féminin.',
        //     'contact.required' => 'Le champ contact est obligatoire.',
        //     'email.required' => 'Le champ email est obligatoire.',
        //     'email.email' => 'Veuillez entrer une adresse email valide.',
        //     'email.unique' => 'Cet email est déjà utilisé.',
        //     'role.required' => 'Le champ rôle est obligatoire.',
        //     'role.in' => 'Le rôle doit être PF ou PTF.',
        //     'password.required' => 'Le champ mot de passe est obligatoire.',
        //     'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        //     'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        //     'type_utilisateur.required' => 'Le champ type d’utilisateur est obligatoire.',
        //     'institution_id.required' => 'Le champ institution est obligatoire.',
        //     'institution_id.exists' => 'L’institution sélectionnée est invalide.',
        //     'image.required' => 'L’image de profil est obligatoire.',
        //     'image.image' => 'Veuillez télécharger un fichier image valide.',
        //     'image.mimes' => 'Le format de l’image doit être jpeg, png, jpg, gif, ou svg.',
        //     'image.max' => 'L’image ne doit pas dépasser 2 Mo.',
        // ]);
    



        // Vérifier si le type utilisateur existe dans la table roles
        $request->validate([
            'role' => 'required|in:CT,PTF,PF',  // Validation pour les rôles
            // Ajoute ici les autres validations pour les autres champs
        ]);


        if (Utilisateur::where('email', $request->email)->exists()) {
            return back()->withErrors(['email' => 'Cet email est déjà utilisé.']);
        }
    
        // Vérifier si le nom et le prénom existent déjà ensemble
        if (Utilisateur::where('nom', $request->nom)->where('prenoms', $request->prenom)->exists()) {
            return back()->withErrors(['nom_prenom' => 'Cet utilisateur (nom et prénoms) existe déjà.']);
        }
    
        // Vérifier si l'institution est déjà occupée par un autre utilisateur
        if (Utilisateur::where('institution', $request->institution)->exists()) {
            return back()->withErrors(['institution' => 'Cette institution est déjà occupée par un autre utilisateur.']);
        }
        
        // Récupération du rôle sélectionné dans la requête
        $role = Role::where('display_name', $request->role)->first();
        
        // Vérification si le rôle existe
        if (!$role) {
            dd('1');
            // Si le rôle n'existe pas, on redirige en arrière avec un message d'erreur
            return redirect()->back()->withErrors(['role' => 'Le rôle sélectionné est invalide.']);
            dd('2');

        }
        //dd($request->role);

   
        // Gestion de l'image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/images');
        }
    
        // Création de l'utilisateur
        Utilisateur::create([
            'nom' => $request->nom,
            'prenoms' => $request->prenoms,
            'sexe' => $request->sexe,
            'contact' => $request->contact,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id, // Enregistrer l'ID du rôle ou type d'utilisateur
            'institution' => $request->institution,
            'image' => $imagePath, // Stockage du chemin de l'image
        ]);
    
        // Redirection avec message de succès
        return redirect()->route('connexion')->with('success', 'Utilisateur créé avec succès.');
    }
    
}
