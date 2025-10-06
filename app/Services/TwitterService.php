<?php

namespace App\Services;

use Exception;
use Twitter;

class TwitterService
{
    public function getLastTweet()
    {
        try {
            // Utiliser l'API pour récupérer uniquement le dernier tweet
            $tweets = Twitter::getUserTimeline([
                'screen_name' => 'togoreforme',
                'count' => 1,
                'exclude_replies' => true,
                'tweet_mode' => 'extended'
            ]);

            return $tweets[0] ?? null; // Retourne le premier tweet ou null si la liste est vide
        } catch (Exception $e) {
            \Log::error('Erreur lors de la récupération du dernier tweet : ' . $e->getMessage());
            return null;
        }
    }
}
