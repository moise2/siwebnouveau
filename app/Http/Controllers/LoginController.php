<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\Projet;
use App\Models\User;
use App\Models\Utilisateur;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected function authenticated(Request $request, $user)
    {
        if ($user->two_factor_enabled) {
            // Logique pour la double authentification
            return redirect()->route('two-factor.index');
        }


        $user = Auth::user();

        // Compter les projets, programmes et calculer les fonds
        $totalProjects = Projet::count();
        $totalPrograms = Programme::count();
        $totalFunds = Projet::sum('funds');

        // Récupérer les détails des projets
        $projects = Projet::select('id', 'name', 'details')->get();

        // Récupérer les chiffres clés depuis la session
        $chiffresCles = session('chiffresCles');

        // Retourner la vue avec toutes les données

        // $chiffresCles = '{
        //     "summary": {
        //         "total_programmes": 120,
        //         "total_projets": 150,
        //         "total_reformes": 30,
        //         "total_bailleurs": 10,
        //         "total_acteurs": 25,
        //         "total_indicateurs": 250,
        //         "total_suivi_activites": 200,
        //         "total_decaissements": 40000000
        //     },
        //     "programmes": {
        //         "total": 120,
        //         "details": [
        //         {
        //             "id": 1,
        //             "nom": "Programme A",
        //             "date_debut": "2021-01-01",
        //             "date_fin": "2023-12-31",
        //             "taux_execution_physique": 80.5,
        //             "taux_execution_financier": 75.0,
        //             "budget": 5000000,
        //             "decaissements": {
        //             "montant_total": 4000000,
        //             "details_par_bailleur": [
        //                 {
        //                 "bailleur": "Banque Mondiale",
        //                 "montant": 2000000
        //                 },
        //                 {
        //                 "bailleur": "Fonds Africain",
        //                 "montant": 2000000
        //                 }
        //             ]
        //             },
        //             "etat_programme": "En cours",
        //             "priorite": {
        //             "id": 1,
        //             "libelle": "Priorité Haute"
        //             },
        //             "axes_strategiques": {
        //             "id": 1,
        //             "libelle": "Axe stratégique 1",
        //             "strategie": {
        //                 "id": 1,
        //                 "intitule": "Stratégie nationale 2025"
        //             }
        //             },
        //             "localisations": [
        //             {
        //                 "localite_id": 1,
        //                 "libelle": "Région A"
        //             }
        //             ]
        //         }
        //         ]
        //     },
        //     "projets": {
        //         "total": 150,
        //         "details": [
        //         {
        //             "id": 1,
        //             "nom": "Projet X",
        //             "date_debut": "2021-06-01",
        //             "date_fin": "2022-12-31",
        //             "taux_execution_physique": 70.0,
        //             "taux_execution_financier": 60.0,
        //             "budget": 2000000,
        //             "decaissements": {
        //             "montant_total": 1500000,
        //             "details_par_bailleur": [
        //                 {
        //                 "bailleur": "Banque Mondiale",
        //                 "montant": 1000000
        //                 },
        //                 {
        //                 "bailleur": "Fonds Africain",
        //                 "montant": 500000
        //                 }
        //             ]
        //             },
        //             "etat_projet": "En cours",
        //             "priorite": {
        //             "id": 2,
        //             "libelle": "Priorité Moyenne"
        //             },
        //             "axes_strategiques": {
        //             "id": 2,
        //             "libelle": "Axe stratégique B",
        //             "strategie": {
        //                 "id": 1,
        //                 "intitule": "Stratégie nationale 2025"
        //             }
        //             }
        //         }
        //         ]
        //     },
        //     "reformes": {
        //         "total": 30,
        //         "details": [
        //         {
        //             "id": 1,
        //             "nom": "Réforme Fiscale 2022",
        //             "ministere": "Ministère des Finances",
        //             "objectif_reforme": "Modernisation de la fiscalité",
        //             "etat_avancement": "Validé",
        //             "axes_strategiques": {
        //             "id": 3,
        //             "libelle": "Axe Réforme",
        //             "strategie": {
        //                 "id": 1,
        //                 "intitule": "Stratégie nationale 2025"
        //             }
        //             }
        //         }
        //         ]
        //     },
        //     "indicateurs": {
        //         "total": 250,
        //         "details": [
        //         {
        //             "id": 1,
        //             "nom": "Indicateur A",
        //             "valeur_reference": 50.0,
        //             "valeur_cible": 100.0,
        //             "valeur_actuelle": 80.0,
        //             "source": "Rapport annuel 2022",
        //             "type": "indicateur_projet",
        //             "projet_id": 1
        //         }
        //         ]
        //     },
        //     "bailleurs": {
        //         "total": 10,
        //         "details": [
        //         {
        //             "id": 1,
        //             "nom": "Banque Mondiale",
        //             "contribution_totale": 5000000,
        //             "projets_finances": [
        //             {
        //                 "id": 1,
        //                 "nom": "Projet X",
        //                 "montant": 1000000
        //             }
        //             ],
        //             "programmes_finances": [
        //             {
        //                 "id": 1,
        //                 "nom": "Programme A",
        //                 "montant": 2000000
        //             }
        //             ]
        //         }
        //         ]
        //     },
        //     "suivi_activites": {
        //         "total": 200,
        //         "details": [
        //         {
        //             "id": 1,
        //             "activite_id": 1,
        //             "taux_execution_physique_precedent": 60.0,
        //             "taux_execution_physique_actuel": 70.0,
        //             "depenses_precedentes": 1500000,
        //             "depenses_actuelles": 1700000,
        //             "etat": "Vérifié"
        //         }
        //         ]
        //     },
        //     "decaissements": {
        //         "total": 40000000,
        //         "details": [
        //         {
        //             "bailleur": "Banque Mondiale",
        //             "montant": 5000000,
        //             "type_financement": "Prêt",
        //             "date_decaissement": "2022-01-01"
        //         },
        //         {
        //             "bailleur": "Fonds Africain",
        //             "montant": 3000000,
        //             "type_financement": "Don",
        //             "date_decaissement": "2022-06-01"
        //         }
        //         ]
        //     },
        //     "acteurs": {
        //         "total": 25,
        //         "details": [
        //         {
        //             "id": 1,
        //             "nom": "Ministère des Finances",
        //             "type": "Ministère",
        //             "programmes_participes": [
        //             {
        //                 "programme_id": 1,
        //                 "nom": "Programme A"
        //             }
        //             ],
        //             "projets_participes": [
        //             {
        //                 "projet_id": 1,
        //                 "nom": "Projet X"
        //             }
        //             ]
        //         }
        //         ]
        //     },
        //     "execution_physique_financiere": {
        //         "periode": "2023-01-01 à 2023-12-31",
        //         "resume": {
        //         "taux_execution_physique_moyen": 76.0,
        //         "taux_execution_financier_moyen": 72.0
        //         },
        //         "details": [
        //         {
        //             "programme_id": 1,
        //             "nom": "Programme A",
        //             "taux_execution_physique": 80.5,
        //             "taux_execution_financier": 75.0
        //         },
        //         {
        //             "projet_id": 1,
        //             "nom": "Projet X",
        //             "taux_execution_physique": 70.0,
        //             "taux_execution_financier": 60.0
        //         }
        //         ]
        //     }
        // }';

        // Décoder les données JSON en un tableau associatif
        $chiffresClesData = json_decode($chiffresCles, true);
        session(['chiffresCles' => $chiffresClesData]);



        // Redirection en fonction du rôle
        return view('ptf.profils', compact('user', 'totalProjects', 'totalPrograms', 'totalFunds', 'projects', 'chiffresCles'));

        // return redirect()->intended($user->role === 'PTF' ? '/ptf/profils' : '/pf/profils');
    }



    protected function create(array $data)
    {
        return Utilisateur::create([
            'image' => $data['image'],
            'nom' => $data['nom'],
            'prenoms' => $data['prenoms'],
            'sexe' => $data['sexe'],
            'contact' => $data['contact'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'two_factor_enabled' => false,
        ]);
    }
}
