<?php


namespace App\Http\Controllers;

use App\Services\LibreTranslateService;

class TranslationController extends Controller
{
    protected $translator;

    public function __construct(LibreTranslateService $translator)
    {
        $this->translator = $translator;
    }

    public function translateText($locale)
    {
        // Exemples de données à traduire
        $texteOriginal = "Bonjour, comment allez-vous ?";

        // Traduire avec LibreTranslate
        $texteTraduit = $this->translator->translate($texteOriginal, 'fr', $locale);

        return view('translated-page', ['texte' => $texteTraduit]);
    }
}
