<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    public function showForm()
    {
        // Retourne la vue du formulaire de contact
        return view('frontend.pages.page_contact');
    }

    public function submitForm(Request $request)
    {
       
        // Validation des champs du formulaire
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        // Envoyer l'email de contact
        $recaptcha = $this->verifyCaptcha($request);

        if (!$recaptcha['success'] || $recaptcha['score'] < 0.5) {
            return redirect()->back()->with('error', 'Échec de la vérification reCAPTCHA.');
        }
        try {
            // Tentative d'envoi de l'email
            Mail::to('gitanmomo2@gmail.com')->send(new ContactMail($request->all()));
    
            // Message de succès si l'envoi est réussi
            return response()->json(['success' => 'Votre message a été envoyé avec succès !']);
        } catch (Exception $e) {
            // Message d'erreur si l'envoi échoue
            return response()->json(['error' => 'L\'envoi du message a échoué. Veuillez réessayer plus tard.']);
        }
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
