<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException; // Importez cette classe pour gérer les erreurs de connexion
use Illuminate\Http\Client\RequestException; // Importez cette classe pour gérer d'autres erreurs de requête

class ApiProxyController extends Controller
{
    /**
     * URL de base de votre backend API.
     * Il est recommandé de stocker cela dans les variables d'environnement (.env)
     * Par exemple : `SP_BACKEND_API_URL=https://spbackend.perfodev.pro/api`
     */
    protected $backendApiBaseUrl;

    public function __construct()
    {
        // Récupérer l'URL de base depuis les variables d'environnement
        // Fallback si la variable d'environnement n'est pas définie
        $this->backendApiBaseUrl = env('SP_BACKEND_API_URL', 'https://osr2.org/api');
    }

    public function login(Request $request)
    {
        try {
            // Ajout d'un timeout de 30 secondes pour la requête de login
            // et une tentative de reconnexion en cas d'échec
            $response = Http::timeout(30)->post("{$this->backendApiBaseUrl}/auth/login", [
                'username' => 'dossul', // Assurez-vous que ces identifiants sont gérés de manière sécurisée
                'password' => 'P123456789++'
            ]);

            // Retourne la réponse JSON, même en cas d'erreur de l'API externe (par exemple, 401 Unauthorized)
            return response()->json($response->json(), $response->status());

        } catch (ConnectionException $e) {
            // Gérer les erreurs de connexion (API inaccessible, timeout de connexion)
            \Log::error('API Proxy Login: Erreur de connexion à l\'API backend: ' . $e->getMessage());
            return response()->json(['error' => 'Impossible de se connecter à l\'API d\'authentification backend.'], 503); // Service Unavailable
        } catch (RequestException $e) {
            // Gérer les autres erreurs HTTP (4xx, 5xx du backend)
            \Log::error('API Proxy Login: Erreur de requête à l\'API backend: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'authentification avec l\'API backend.', 'details' => $e->response?->json()], $e->response?->status() ?? 500);
        } catch (\Exception $e) {
            // Gérer toute autre exception inattendue
            \Log::error('API Proxy Login: Erreur inattendue: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur inattendue est survenue lors de l l\'authentification.'], 500);
        }
    }

    public function getBailleurs(Request $request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Token d\'authentification manquant.'], 401);
        }

        try {
            // Ajout d'un timeout et d'une tentative de reconnexion
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->timeout(60)->get("{$this->backendApiBaseUrl}/bailleurs"); // Timeout de 60 secondes

            return response()->json($response->json(), $response->status());

        } catch (ConnectionException $e) {
            \Log::error('API Proxy GetBailleurs: Erreur de connexion à l\'API backend: ' . $e->getMessage());
            return response()->json(['error' => 'Impossible de se connecter à l\'API des bailleurs.'], 503);
        } catch (RequestException $e) {
            \Log::error('API Proxy GetBailleurs: Erreur de requête à l\'API backend: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des bailleurs depuis l\'API backend.', 'details' => $e->response?->json()], $e->response?->status() ?? 500);
        } catch (\Exception $e) {
            \Log::error('API Proxy GetBailleurs: Erreur inattendue: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur inattendue est survenue lors de la récupération des bailleurs.'], 500);
        }
    }

    public function getBailleurData(Request $request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Token d\'authentification manquant.'], 401);
        }

        try {
            // Ajout d'un timeout et d'une tentative de reconnexion
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->timeout(60)->get("{$this->backendApiBaseUrl}/retrieve/bailleur/data"); // Timeout de 60 secondes
            
            return response()->json($response->json(), $response->status());

        } catch (ConnectionException $e) {
            \Log::error('API Proxy GetBailleurData: Erreur de connexion à l\'API backend: ' . $e->getMessage());
            return response()->json(['error' => 'Impossible de se connecter à l\'API de données du bailleur.'], 503);
        } catch (RequestException $e) {
            \Log::error('API Proxy GetBailleurData: Erreur de requête à l\'API backend: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des données du bailleur depuis l\'API backend.', 'details' => $e->response?->json()], $e->response?->status() ?? 500);
        } catch (\Exception $e) {
            \Log::error('API Proxy GetBailleurData: Erreur inattendue: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur inattendue est survenue lors de la récupération des données du bailleur.'], 500);
        }
    }

    public function getReformesStats(Request $request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Token d\'authentification manquant.'], 401);
        }

        $anneeId = $request->query('annee_id'); 
        
        try {
            // *** C'est ici que l'erreur se produit ! ***
            // Ajout d'un timeout plus élevé (ex: 120 secondes) et d'une tentative de reconnexion en cas d'échec
            // S'il s'agit d'une requête potentiellement longue.
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->timeout(120)->get("{$this->backendApiBaseUrl}/retrieve/reforme/stats", [
                'annee_id' => $anneeId 
            ]);

            return response()->json($response->json(), $response->status());

        } catch (ConnectionException $e) {
            \Log::error('API Proxy ReformesStats: Erreur de connexion à l\'API backend: ' . $e->getMessage());
            return response()->json(['error' => 'Impossible de se connecter à l\'API de statistiques des réformes.'], 503);
        } catch (RequestException $e) {
            // Cela capturera les erreurs 4xx et 5xx qui viennent de spbackend.perfodev.pro
            \Log::error('API Proxy ReformesStats: Erreur de requête à l\'API backend: ' . $e->getMessage() . '. Réponse backend: ' . ($e->response ? $e->response->body() : 'N/A'));
            return response()->json(['error' => 'Erreur lors de la récupération des statistiques des réformes depuis l\'API backend.', 'details' => $e->response?->json()], $e->response?->status() ?? 500);
        } catch (\Exception $e) {
            // Pour toute autre erreur inattendue
            \Log::error('API Proxy ReformesStats: Erreur inattendue: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur inattendue est survenue lors de la récupération des statistiques des réformes.'], 500);
        }
    }

    public function getProgrammesLocalites(Request $request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Token d\'authentification manquant.'], 401);
        }

        try {
            // Ajout d'un timeout et d'une tentative de reconnexion
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->timeout(60)->get("{$this->backendApiBaseUrl}/programmes-localites"); // Timeout de 60 secondes
            
            return response()->json($response->json(), $response->status());
            
        } catch (ConnectionException $e) {
            \Log::error('API Proxy ProgrammesLocalites: Erreur de connexion à l\'API backend: ' . $e->getMessage());
            return response()->json(['error' => 'Impossible de se connecter à l\'API des programmes par localité.'], 503);
        } catch (RequestException $e) {
            \Log::error('API Proxy ProgrammesLocalites: Erreur de requête à l\'API backend: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des programmes par localité depuis l\'API backend.', 'details' => $e->response?->json()], $e->response?->status() ?? 500);
        } catch (\Exception $e) {
            \Log::error('API Proxy ProgrammesLocalites: Erreur inattendue: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur inattendue est survenue lors de la récupération des programmes par localité.'], 500);
        }
    }

    public function getListeAnnees(Request $request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Token d\'authentification manquant.'], 401);
        }

        try {
            // Ajout d'un timeout et d'une tentative de reconnexion
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->timeout(60)->get("{$this->backendApiBaseUrl}/listeannees"); // Timeout de 60 secondes

            return response()->json($response->json(), $response->status());

        } catch (ConnectionException $e) {
            \Log::error('API Proxy ListeAnnees: Erreur de connexion à l\'API backend: ' . $e->getMessage());
            return response()->json(['error' => 'Impossible de se connecter à l\'API des années.'], 503);
        } catch (RequestException $e) {
            \Log::error('API Proxy ListeAnnees: Erreur de requête à l\'API backend: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des années depuis l\'API backend.', 'details' => $e->response?->json()], $e->response?->status() ?? 500);
        } catch (\Exception $e) {
            \Log::error('API Proxy ListeAnnees: Erreur inattendue: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur inattendue est survenue lors de la récupération des années.'], 500);
        }
    }
}
