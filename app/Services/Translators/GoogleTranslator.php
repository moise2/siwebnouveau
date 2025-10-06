<?php

namespace App\Services\Translators;

use Google\Cloud\Translate\V2\TranslateClient;
use Exception;
use Illuminate\Support\Facades\Log;

class GoogleTranslator implements TranslatorInterface
{
    protected $client;

    public function __construct()
    {
        $apiKey = env('GOOGLE_TRANSLATE_API_KEY');
        if (!$apiKey) {
            Log::error("Google Translate API key (GOOGLE_TRANSLATE_API_KEY) not found in .env.");
            throw new Exception("Google Translate API key is not configured. Please check your .env file.");
        }
        $this->client = new TranslateClient([
            'key' => $apiKey
        ]);
    }

    public function translate(string $text, string $targetLang, string $sourceLang = 'auto'): ?string
    {
        try {
            $source = ($sourceLang === 'auto') ? null : $sourceLang;

            $result = $this->client->translate($text, [
                'target' => $targetLang,
                'source' => $source,
            ]);

            return $result['text'] ?? null;
        } catch (Exception $e) {
            Log::error("Google Translate API Error for text '{$text}' to '{$targetLang}': " . $e->getMessage());
            return null;
        }
    }
}