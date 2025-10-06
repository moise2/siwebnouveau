<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Services\BlockedDomainsManager;

class SubscriberController extends Controller
{
    private $blockedDomainsManager;

    public function __construct(BlockedDomainsManager $blockedDomainsManager)
    {
        $this->blockedDomainsManager = $blockedDomainsManager;
    }

    public function subscribe(Request $request)
    {
        $blockedDomains = $this->blockedDomainsManager->getBlockedDomains();
        $verificationToken = Subscriber::generateToken();
        $request->validate([
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) use ($blockedDomains) {
                    $domain = substr(strrchr($value, "@"), 1);

                    if (!$domain || in_array($domain, $blockedDomains)) {
                        $fail("L'adresse email provient d'un domaine non autorisé.");
                    }

                    if (strlen($domain) < 5) {
                        $fail("Le domaine de l'email est trop court.");
                    }
                },
                'unique:subscribers,email',
            ],
        ]);

        // Vérification reCAPTCHA
        $recaptcha = $this->verifyCaptcha($request);

        if (!$recaptcha['success'] || $recaptcha['score'] < 0.5) {
            return redirect()->back()->with('error', 'Échec de la vérification reCAPTCHA.');
        }
       
// dd('1');
        // try {
            $subscriber = Subscriber::create([
                'email' => $request->email,
                'verification_code' => $verificationToken,
                'verified' => false,
                'is_active' => false,
            ]);

            // dd('cc');

            // dd('2');

            Mail::to($request->email)->send(new \App\Mail\EmailVerification($verificationToken, $request->email));
            // Mail::to($request->email)->send(new EmailVerification($verificationToken, $request->email));
            //Mail::to($request->email)->send(new \App\Mail\EmailVerification($verificationCode, $request->email));
// dd('3');
            return redirect()->back()->with('success', 'Veuillez vérifier votre adresse email.');
        // } catch (\Exception $e) {
        //     \Log::error("Erreur lors de l'envoi de l'email : " . $e->getMessage());
        //     return redirect()->back()->with('error', "Une erreur est survenue.");
        // }
    }

    public function verifyEmail($token)
    {
        // Rechercher le subscriber correspondant au code de vérification
        $subscriber = Subscriber::where('verification_code', $token)->first();
    
        // Si aucun subscriber n'est trouvé pour ce code de vérification, renvoyer une erreur
        if (!$subscriber) {
            \Log::info("Aucun subscriber trouvé pour le token : $token");
            return redirect('/')->with('error', 'Lien de vérification invalide ou expiré.');
        }
    
        // Vérification de l'expiration du token
        if ($subscriber->created_at->addHours(24)->isPast()) {
            return redirect('/')->with('error', 'Lien de vérification expiré.');
        }
    
        // Si le code de vérification correspond, mettre à jour l'utilisateur
        $subscriber->update([
            'verified' => true,
            'is_active' => true,
            'verification_code' => null, // Supprimer le code de vérification après utilisation
        ]);
    
        // Retourner un message de succès
        return redirect('/')->with('success', 'Votre adresse email a été vérifiée avec succès.');
    }
    

    public function verifyCaptcha($request)
    {
        $response = $request->input('g-recaptcha-response');
        $secretKey = config('services.recaptcha.secret_key');

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $response,
        ]);

        return $response->json();
    }
}
