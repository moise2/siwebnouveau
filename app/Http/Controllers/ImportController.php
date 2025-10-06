<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class ImportController extends Controller
{
    // Base locale (ton app) pour passer par le proxy interne
    // Ex: http://127.0.0.1:8000 en local, ou https://togoreformes.osr2.org en prod
    private string $proxyBase;

    public function __construct()
    {
        $this->proxyBase = rtrim(env('SP_PROXY_BASE', config('app.url', 'http://127.0.0.1:8000')), '/');
    }

    /* ========================= Helpers ========================= */

    /**
     * Supprime un éventuel schéma "Bearer " / "Token " au début d'une chaîne.
     * Renvoie toujours un token "pur" (sans schéma).
     */
    private function stripAuthScheme(string $token): string
    {
        return preg_replace('/^(Bearer|Token)\s+/i', '', trim($token));
    }

    /* ========================= HTTP helpers ========================= */

    private function proxyLogin(): ?string
    {
        $url = "{$this->proxyBase}/proxy/login";

        try {
            // Appelle TON endpoint /proxy/login qui, lui, va se loguer sur le backend
            $resp = Http::acceptJson()
                ->withOptions(['connect_timeout' => 15, 'http_errors' => false])
                ->timeout(30)
                ->retry(2, 500)
                ->post($url, [
                    'username' => 'dossul',
                    'password' => 'P123456789++'
                ]);

            if ($resp->failed()) {
                Log::error('Proxy login failed', [
                    'url'    => $url,
                    'status' => $resp->status(),
                    'body'   => $resp->body(),
                ]);
                return null;
            }

            $j = $resp->json() ?: [];

            // couvre plusieurs formats possibles
            $tok = $j['token']
                ?? $j['access_token']
                ?? ($j['data']['token'] ?? null)
                ?? ($j['data']['access_token'] ?? null);

            if (!$tok) {
                Log::error('Proxy login: token introuvable dans la réponse', ['url' => $url, 'json' => $j]);
                return null;
            }

            // Toujours retourner un token "pur" (sans schéma) pour éviter "Bearer Bearer ..."
            return $this->stripAuthScheme((string)$tok);

        } catch (ConnectionException $e) {
            Log::error('Proxy Login: Erreur de connexion', ['ex' => $e->getMessage()]);
            return null;
        } catch (RequestException $e) {
            Log::error('Proxy Login: Erreur de requête', [
                'ex'   => $e->getMessage(),
                'resp' => $e->response ? $e->response->body() : 'N/A'
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Proxy Login: Erreur inattendue', ['ex' => $e->getMessage()]);
            return null;
        }
    }

    private function proxyGet(string $path, string $token, array $q = [])
    {
        $url  = "{$this->proxyBase}/" . ltrim($path, '/');
        $pure = $this->stripAuthScheme($token); // sécurité anti "Bearer Bearer"

        $resp = Http::withHeaders([
                'Authorization' => "Bearer {$pure}",
                'Accept'        => 'application/json',
            ])
            ->withOptions(['connect_timeout' => 20, 'http_errors' => false])
            ->timeout(180)
            ->retry(3, 1000)
            ->get($url, $q);

        if ($resp->failed()) {
            Log::error('proxyGet failed', [
                'url'    => $url,
                'query'  => $q,
                'status' => $resp->status(),
                'body'   => $resp->body(), // body brut pour diagnostiquer (scope, IP, etc.)
                'headers'=> [
                    'content-type'     => $resp->header('Content-Type'),
                    'www-authenticate' => $resp->header('WWW-Authenticate'),
                ],
            ]);
            $resp->throw(); // interrompt le flux si critique
        }

        // Retourne JSON si possible, sinon raw_body
        try {
            return $resp->json();
        } catch (\Throwable $e) {
            return ['raw_body' => $resp->body()];
        }
    }

    /* ========================= Endpoint principal ========================= */

    /**
     * POST /api/import/all
     * Import complet : bailleurs/programmes + localités (via tes endpoints /proxy/*)
     */
    public function importAll(Request $req)
    {
        @set_time_limit(900);
        @ini_set('max_execution_time', '900');
        DB::disableQueryLog();

        $t0     = microtime(true);
        $errors = [];

        // 1) login via le proxy
        $token = $this->proxyLogin();
        if (!$token) {
            return response()->json(['ok' => false, 'msg' => 'Login proxy KO'], 401);
        }

        // 2) bailleur + programmes (l’arbre)
        try {
            // Ton contrôleur proxy expose /proxy/bailleur/data
            $data = $this->proxyGet('proxy/bailleur/data', $token);
            $this->upsertBailleursEtProgrammes($data);
        } catch (\Throwable $e) {
            $errors[] = 'bailleurs_programmes';
            Log::error('Erreur bailleurs/programmes', [
                'ex'    => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // 3) localités + pivot
        try {
            // Ton contrôleur proxy expose /proxy/programmes-localites
            $loc = $this->proxyGet('proxy/programmes-localites', $token);
            $payload = $loc['data'] ?? $loc['records'] ?? $loc;
            $this->upsertLocalitesPivot(is_array($payload) ? $payload : []);
        } catch (\Throwable $e) {
            $errors[] = 'localites';
            Log::error('Erreur localites', [
                'ex'    => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        $duration = round(microtime(true) - $t0, 2);

        return response()->json([
            'ok'       => empty($errors),
            'msg'      => empty($errors) ? 'Import terminé ✅' : 'Import terminé avec erreurs partielles',
            'errors'   => $errors,
            'duration' => $duration . 's',
        ]);
    }

    /* ========================= UPSERTS ========================= */

    private function upsertBailleursEtProgrammes(array $data): void
    {
        DB::transaction(function () use ($data) {
            $b = $data['bailleur'] ?? null;
            if ($b && isset($b['id'])) {
                DB::table('bailleurs')->upsert([[
                    'id'         => $b['id'],
                    'nom'        => $b['nom'] ?? ($b['name'] ?? 'N/D'),
                    'image'      => $b['image'] ?? 'default.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]], ['id'], ['nom', 'image', 'updated_at']);
            }

            $now = now();
            $programmesRows = [];
            $execRows       = [];

            foreach (($data['programmes'] ?? []) as $p) {
                if (!isset($p['id'])) continue;

                $nom    = $p['intitule'] ?? ($p['libelle'] ?? 'N/D');
                $budget = 0.0;
                $phys   = [];
                $fin    = [];

                foreach ($p['actions'] ?? [] as $a) {
                    foreach ($a['activites'] ?? [] as $av) {
                        $f = $av['financement'] ?? [];
                        $budget += (float)($f['total'] ?? 0);

                        $sg = $av['suivi_execution']['global'] ?? [];
                        if (isset($sg['taux_execution_physique'])) {
                            $phys[] = (float)$sg['taux_execution_physique'];
                        }
                        if (isset($sg['taux_execution_financier'])) {
                            $fin[] = (float)$sg['taux_execution_financier'];
                        }
                    }
                }

                $txPhys = $phys ? round(array_sum($phys) / max(count($phys), 1), 2) : 0.0;
                $txFin  = $fin  ? round(array_sum($fin)  / max(count($fin), 1),  2) : null;
                $etat   = $txPhys == 0 ? 'Non démarré' : ($txPhys >= 100 ? 'Terminé' : 'En cours');

                $programmesRows[] = [
                    'id'                       => $p['id'],
                    'acteur_id'                => null,
                    'nom'                      => $nom,
                    'date_debut'               => null,
                    'date_fin'                 => null,
                    'taux_execution_physique'  => $txPhys,
                    'taux_execution_financier' => $txFin,
                    'budget'                   => $budget ?: null,
                    'priorite_id'              => null,
                    'axe_strategique_id'       => null,
                    'etat_programme'           => $etat,
                    'created_at'               => $now,
                    'updated_at'               => $now,
                ];

                $execRows[] = [
                    'programme_id'             => $p['id'],
                    'projet_id'                => null,
                    'chiffre_cle_id'           => null,
                    'taux_execution_physique'  => $txPhys,
                    'taux_execution_financier' => $txFin ?? 0,
                    'created_at'               => $now,
                    'updated_at'               => $now,
                ];
            }

            foreach (array_chunk($programmesRows, 500) as $chunk) {
                DB::table('programmes')->upsert(
                    $chunk,
                    ['id'],
                    ['nom','taux_execution_physique','taux_execution_financier','budget','etat_programme','updated_at']
                );
            }

            foreach (array_chunk($execRows, 500) as $chunk) {
                foreach ($chunk as $row) {
                    DB::table('execution_physique_financieres')->updateOrInsert(
                        ['programme_id' => $row['programme_id']],
                        $row
                    );
                }
            }
        });
    }

    private function upsertLocalitesPivot(array $items): void
    {
        DB::transaction(function () use ($items) {
            $now      = now();
            $locRows  = [];
            $pivots   = [];

            foreach ($items as $it) {
                $code = $it['code'] ?? ($it['id'] ?? null);
                if (!$code) continue;

                $locRows[$code] = [
                    'code'       => $code,
                    'nom'        => $it['nom'] ?? ($it['name'] ?? 'N/D'),
                    'region'     => $it['region'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                foreach ($it['programmes'] ?? [] as $p) {
                    if (!isset($p['id'])) continue;
                    $pivots[] = ['programme_id' => $p['id'], 'localite_code' => $code];
                }
            }

            if ($locRows) {
                foreach (array_chunk(array_values($locRows), 500) as $chunk) {
                    DB::table('localites')->upsert($chunk, ['code'], ['nom','region','updated_at']);
                }
            }

            if (!$pivots) return;

            $codes = array_unique(array_column($pivots, 'localite_code'));
            $map   = DB::table('localites')->whereIn('code', $codes)->pluck('id', 'code');

            $rows = [];
            foreach ($pivots as $pv) {
                $code = $pv['localite_code'];
                if (isset($map[$code])) {
                    $rows[] = [
                        'programme_id' => $pv['programme_id'],
                        'localite_id'  => $map[$code],
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ];
                }
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('localite_programmes')->insertOrIgnore($chunk);
            }
        });
    }
}
