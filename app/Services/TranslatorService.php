<?php

namespace App\Services;

use App\Services\Translators\GoogleTranslator; 
use App\Services\Translators\TranslatorInterface;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class TranslatorService
{
    protected TranslatorInterface $translatorProvider;

    public function __construct()
    {
        // Initialise le fournisseur de traduction GoogleTranslator
        try {
            $this->translatorProvider = new GoogleTranslator();
        } catch (Exception $e) {
            Log::critical("Google TranslatorService initialization failed: " . $e->getMessage());
            throw new Exception("Translation service is not available due to configuration error.");
        }
    }

    /**
     * Traduit un texte en utilisant Google Cloud Translation avec mise en cache.
     *
     * @param string $text Le texte à traduire.
     * @param string|null $targetLang La langue cible. Si null, utilise la locale courante de l'application.
     * @param string $sourceLang La langue source (défaut 'auto').
     * @param int $cacheDurationMinutes Durée de mise en cache en minutes (par défaut 1 semaine).
     * @return string Le texte traduit ou le texte original en cas d'échec de traduction.
     */
    public function translate(
        string $text,
        ?string $targetLang = null,
        string $sourceLang = 'auto',
        int $cacheDurationMinutes = 60 * 24 * 7
    ): string {
        // Si targetLang n'est pas fourni, utilise la locale actuelle de Laravel
        $targetLang = $targetLang ?? App::getLocale();

        // Clé de cache unique pour ce texte, langue cible et source
        $cacheKey = 'google_translation_' . md5($text . $targetLang . $sourceLang);

        return Cache::remember($cacheKey, $cacheDurationMinutes, function () use ($text, $targetLang, $sourceLang) {
            $translatedText = $this->translatorProvider->translate($text, $targetLang, $sourceLang);

            if (is_null($translatedText)) {
                Log::warning("Google Translate API translation failed for text: '{$text}' to '{$targetLang}'. Returning original text.");
                return $text;
            }
            return $translatedText;
        });
    }

    /**
     * Traduit plusieurs textes. Chaque texte est traité individuellement avec cache.
     * @param array $texts Tableau de textes à traduire.
     * @param string|null $targetLang La langue cible. Si null, utilise la locale courante.
     * @param string $sourceLang La langue source.
     * @return array Tableau de textes traduits (ou originaux si échec).
     */
    public function translateMany(
        array $texts,
        ?string $targetLang = null,
        string $sourceLang = 'auto'
    ): array {
        $translatedResults = [];
        foreach ($texts as $key => $text) {
            $translatedResults[$key] = $this->translate($text, $targetLang, $sourceLang);
        }
        return $translatedResults;
    }
}