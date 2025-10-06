<?php

namespace App\Services;

use GuzzleHttp\Client;

class LibreTranslateService
{
    protected $client;
    protected $apiUrl = 'https://libretranslate.com/translate';

    public function __construct()
    {
        $this->client = new Client();
    }

    public function translate($text, $sourceLang = 'fr', $targetLang = 'en')
    {
        $response = $this->client->post($this->apiUrl, [
            'form_params' => [
                'q' => $text,
                'source' => $sourceLang,
                'target' => $targetLang,
                'format' => 'text'
            ]
        ]);

        $responseData = json_decode($response->getBody(), true);
        return $responseData['translatedText'] ?? $text;
    }
}
