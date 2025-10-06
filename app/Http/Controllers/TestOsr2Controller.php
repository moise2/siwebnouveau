<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TestOsr2Controller extends Controller
{
    protected string $base;
    protected string $user;
    protected string $pass;
    protected string $scheme;

    public function __construct()
    {
        $this->base   = rtrim(env('SP_BACKEND_API_URL','https://osr2.org/api'), '/');
        $this->user   = env('SP_LOGIN_USER','dossul');
        $this->pass   = env('SP_LOGIN_PASS','P123456789++');
        $this->scheme = env('SP_AUTH_SCHEME','Bearer'); // ou 'Token'
    }

    /* =================== 1) LOGIN : affiche un aperçu du token =================== */
    public function loginAndShow()
    {
        $token = $this->requestNewToken();
        if (!$token) {
            return response()->json(['ok'=>false,'msg'=>'Login KO'], 401);
        }

        // cache ~50 minutes
        Cache::put('osr2_token', $token, now()->addMinutes(50));

        // log (token masqué)
        Log::info('OSR2 token obtained', [
            'preview' => $this->maskToken($token),
        ]);

        return response()->json([
            'ok'           => true,
            'token_preview'=> $this->maskToken($token), // on n’expose pas le token complet
        ]);
    }

    /* =================== 2) Appel protégé : montre l’Authorization utilisé =================== */
    public function callProtected()
    {
        $result = $this->withToken(function (string $authHeader) {
            // log l’en-tête Authorization utilisé (masqué)
            Log::info('Calling OSR2 with header', [
                'Authorization' => $this->maskAuthHeader($authHeader),
                'url'           => "{$this->base}/retrieve/bailleur/data",
                'method'        => 'GET',
            ]);

            return Http::withHeaders([
                'Authorization'    => $authHeader,
                'Accept'           => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])->timeout(60)->get("{$this->base}/retrieve/bailleur/data");
        });

        if ($result['ok'] === false) {
            return response()->json($result, $result['status'] ?? 500);
        }
        return response()->json($result);
    }

    /* =================== Helpers =================== */

    // tente avec token; si 401/403, renouvelle puis retry
    private function withToken(\Closure $doRequest): array
    {
        $token = Cache::get('osr2_token') ?: $this->requestNewToken();
        if (!$token) {
            Log::warning('OSR2 token missing and could not be obtained');
            return ['ok'=>false, 'status'=>401, 'msg'=>'Impossible d’obtenir un token'];
        }
        Cache::put('osr2_token', $token, now()->addMinutes(50));

        $auth = $this->formatAuth($token);
        $res  = $doRequest($auth);

        if (in_array($res->status(), [401,403])) {
            Log::warning('OSR2 call unauthorized/forbidden, will refresh token', [
                'status'        => $res->status(),
                'auth_used'     => $this->maskAuthHeader($auth),
                'response_body' => $this->shorten($res->body()),
            ]);

            $token = $this->requestNewToken();
            if (!$token) {
                return [
                    'ok'     => false,
                    'status' => $res->status(),
                    'msg'    => 'Token expiré et impossible d’en obtenir un nouveau',
                    'body'   => $this->shorten($res->body()),
                ];
            }
            Cache::put('osr2_token', $token, now()->addMinutes(50));
            $auth = $this->formatAuth($token);

            Log::info('Retrying OSR2 call with refreshed token', [
                'Authorization' => $this->maskAuthHeader($auth),
            ]);

            $res = $doRequest($auth);
        }

        if (!$res->ok()) {
            Log::error('OSR2 protected call error', [
                'status' => $res->status(),
                'body'   => $this->shorten($res->body()),
            ]);
            return ['ok'=>false, 'status'=>$res->status(), 'body'=>$this->shorten($res->body())];
        }

        return ['ok'=>true, 'status'=>$res->status(), 'data'=>$res->json()];
    }

    private function requestNewToken(): ?string
    {
        $resp = Http::acceptJson()->timeout(30)->post("{$this->base}/auth/login", [
            'username' => $this->user,
            'password' => $this->pass,
        ]);

        Log::info('OSR2 login attempt', ['status'=>$resp->status(), 'url'=>"{$this->base}/auth/login"]);

        if (!$resp->ok()) return null;

        $j = $resp->json() ?: [];
        $token = $j['token'] ?? $j['access_token'] ?? ($j['data']['token'] ?? null);

        if ($token) {
            Log::info('OSR2 login success', ['token_preview'=>$this->maskToken($token)]);
        } else {
            Log::warning('OSR2 login: token not found in response', ['json_keys'=>array_keys($j)]);
        }

        return $token ?: null;
    }

    private function formatAuth(string $token): string
    {
        if (preg_match('/^\s*(Bearer|Token)\s+/i', $token)) {
            return $token;
        }
        return "{$this->scheme} {$token}";
    }

    /* ===== Utilitaires de log sûrs (masquage) ===== */

    private function maskToken(string $token): string
    {
        $len = strlen($token);
        if ($len <= 12) return str_repeat('*', $len);
        return substr($token, 0, 12).'...'.substr($token, -6);
    }

    private function maskAuthHeader(string $auth): string
    {
        // Exemple: "Bearer eyJ0eXAiOiJK...nmykbQ"
        if (!str_contains($auth, ' ')) return '***';
        [$scheme, $tok] = explode(' ', $auth, 2);
        return $scheme.' '.$this->maskToken(trim($tok));
    }

    private function shorten(?string $s, int $max = 500): ?string
    {
        if ($s === null) return null;
        return strlen($s) > $max ? substr($s, 0, $max).'…' : $s;
    }
}
