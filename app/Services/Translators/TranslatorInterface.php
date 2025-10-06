<?php

namespace App\Services\Translators;

interface TranslatorInterface
{
    public function translate(string $text, string $targetLang, string $sourceLang = 'auto'): ?string;
}