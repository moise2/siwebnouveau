<?php

// namespace App\Http\Controllers;

// use App\Models\Api;
// use App\Models\Twitter;
// use App\Services\TwitterService;
// use Illuminate\Http\Request;

// class TwitterController extends Controller
// {
//     protected $twitterService;

//     public function __construct(TwitterService $twitterService)
//     {
//         $this->twitterService = $twitterService;
//     }

//     public function showLastTweet()
//     {
//         // Appel du service pour obtenir le dernier tweet
//         //$lastTweet = $this->twitterService->getLastTweet();

//         $tweets = Api::getTweets();
//         print_r($tweets);

//         // Retourne la vue avec le tweet
//         return view('tweets.show', compact('lastTweet'));
//     }



//     public function gettwitt()
//     {
//         //$tweets = Api::getTweets();
//        // print_r($tweets);

//         $tweet='{
//         "data": [
//             {
//                 "edit_history_tweet_ids": [
//                     "1809512423742714199"
//                 ],
//                 "text": "RT @CommercegouvTg: Au Togo, les Très petites, petites et moyennes entreprises (TPME) peuvent désormais accéder facilement à leur charte…",
//                 "id": "1809512423742714199"
//             },
//             {
//                 "edit_history_tweet_ids": [
//                     "1808510560322933152"
//                 ],
//                 "text": "Avis spécifique de passation de marchés: 𝐬𝐞́𝐥𝐞𝐜𝐭𝐢𝐨𝐧 𝐝’𝐮𝐧 𝐚𝐫𝐜𝐡𝐢𝐭𝐞𝐜𝐭𝐞 𝐩𝐨𝐮𝐫 𝐥’𝐢𝐝𝐞𝐧𝐭𝐢𝐟𝐢𝐜𝐚𝐭𝐢𝐨𝐧 𝐞𝐭 𝐥’𝐚𝐦𝐞́𝐧𝐚𝐠𝐞𝐦𝐞𝐧𝐭 𝐝𝐞𝐬 𝐛𝐮𝐫𝐞𝐚𝐮𝐱 𝐝𝐮 𝐟𝐮𝐭𝐮𝐫 𝐌𝐂𝐀-𝐓𝐨𝐠𝐨.\nhttps://t.co/sFj4ujhNvh https://t.co/JiPSAfJfrf",
//                 "id": "1808510560322933152"
//             },
//             {
//                 "edit_history_tweet_ids": [
//                     "1801262203447775575"
//                 ],
//                 "text": "Le Togo prévoit une croissance de 6,6% en 2024, avec une inflation en baisse à 2,7%. Les réformes économiques et les investissements, comme la Plateforme Industrielle d’Adétikopé, stimulent cette dynamique. \n#Togo \n#Économie \n#Croissance\n\nhttps://t.co/Hir00zMzEt https://t.co/0RaU4FunI2",
//                 "id": "1801262203447775575"
//             },
//             {
//                 "edit_history_tweet_ids": [
//                     "1800936070210875886"
//                 ],
//                 "text": "La croissance économique en 2023 a atteint 5,6 % et l\'inflation est tombée à 2,6 %. Le Togo continue de renforcer la viabilité de la dette et d\'améliorer l\'inclusion sociale. #FMI #Togo #Economie #DéveloppementDurable\nhttps://t.co/FIlUuObZ8K\nhttps://t.co/WAaAcICIzt",
//                 "id": "1800936070210875886"
//             },
//             {
//                 "edit_history_tweet_ids": [
//                     "1800936067459117250"
//                 ],
//                 "text": "La mission du FMI au Togo note des performances économiques robustes et des progrès vers les ODD. La consultation de 2024 s\'est concentrée sur la stabilité macroéconomique et la viabilité de la dette. https://t.co/MQLYSMnpKP",
//                 "id": "1800936067459117250"
//             },
//             {
//                 "edit_history_tweet_ids": [
//                     "1800578059579310198"
//                 ],
//                 "text": "RT @CommercegouvTg: &lt;En 2023, le Togo🇹🇬 a consolidé sa performance avec un taux satisfaisant de mise en œuvre des réformes de 76%. Cette év…",
//                 "id": "1800578059579310198"
//             },
//             {
//                 "edit_history_tweet_ids": [
//                     "1784154873564446731"
//                 ],
//                 "text": "https://t.co/kGlw9PVH1o",
//                 "id": "1784154873564446731"
//             },
//             {
//                 "edit_history_tweet_ids": [
//                     "1778058859057197565"
//                 ],
//                 "text": "Que ce mois béni de Ramadan soit pour vous une période de réflexion, de prière et de paix. Puissent vos jours être illuminés par la miséricorde et la grâce divine, et que vos prières soient exaucées. Que cette fête de Ramadan vous apporte joie, bonheur et spiritualité. https://t.co/9prAQp4MdP",
//                 "id": "1778058859057197565"
//             },
//             {
//                 "edit_history_tweet_ids": [
//                     "1776257291160797403"
//                 ],
//                 "text": "Restez informés sur nos appels d\'offres à venir en vous inscrivant sur notre liste de diffusion via zineb.benbrahim@crownagents.co.uk. Ensemble, bâtissons un avenir prospère pour tous! 🚀 #Togo #DéveloppementÉconomique #PassationDesMarchés",
//                 "id": "1776257291160797403"
//             },
//             {
//                 "edit_history_tweet_ids": [
//                     "1776257288338104511"
//                 ],
//                 "text": "Le Togo, éligible au financement de la MCC, concentre ses efforts sur l\'accord prévu d\'ici fin 2024. Priorisant l\'énergie et le numérique, le gouvernement prévoit des investissements significatifs. https://t.co/KVxbc2mLXP",
//                 "id": "1776257288338104511"
//             }
//             ],
//             "meta": {
//                 "result_count": 10,
//                 "newest_id": "1809512423742714199",
//                 "oldest_id": "1776257288338104511",
//                 "next_token": "7140dibdnow9c7btw4831rkzqehpxjqt6ra0863q6gylt"
//             }
//         }';


//         $tweets = json_decode($tweet,true);
//         print_r($tweets);
//         //if($tweets['status'] != 429){
//             foreach ($tweets['data'] as $tweet) {
//                 $checkTweet = Twitter::select('id')->where('id_twitt', (int)$tweet['id'])->first();
//                 if(empty($checkTweet)){
//                     Twitter::create([
//                         'text'=> substr(json_encode($tweet['text']), 1, -1),
//                         'id_twitt'=> (int)$tweet['id'],
//                     ]);
//                 }
//             }
//         //}

//     }
// }




namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\Twitter;
use Illuminate\Http\Request;

class TwitterController extends Controller
{
    public function showLastTweet()
    {
        // Authentification avec Twitter
        $twitteroauth = new TwitterOAuth(
            env('TWITTER_CONSUMER_KEY'),
            env('TWITTER_CONSUMER_SECRET'),
            env('TWITTER_ACCESS_TOKEN'),
            env('TWITTER_ACCESS_TOKEN_SECRET')
        );

        // Obtenir les derniers tweets depuis Twitter
        $tweets = $twitteroauth->get('statuses/user_timeline', [
            'count' => 5 // Nombre de tweets à récupérer
        ]);

        // Vérifier si la requête est réussie
        if ($twitteroauth->getLastHttpCode() == 200) {
            // Retourner la vue avec les tweets
            return view('tweets.show', compact('tweets'));
        } else {
            // En cas d'erreur, afficher un message
            return view('tweets.show')->withErrors('Erreur lors de la récupération des tweets.');
        }
    }

    public function getTweets()
    {
        // Authentification avec Twitter
        $twitteroauth = new TwitterOAuth(
            env('TWITTER_CONSUMER_KEY'),
            env('TWITTER_CONSUMER_SECRET'),
            env('TWITTER_ACCESS_TOKEN'),
            env('TWITTER_ACCESS_TOKEN_SECRET')
        );

        // Obtenir les derniers tweets
        $tweets = $twitteroauth->get('statuses/user_timeline', [
            'count' => 10 // Nombre de tweets à récupérer
        ]);

        // Vérifier si la requête est réussie
        if ($twitteroauth->getLastHttpCode() == 200) {
            // Enregistrer les tweets dans la base de données si nécessaire
            foreach ($tweets as $tweet) {
                $checkTweet = Twitter::select('id')->where('id_twitt', (int)$tweet->id_str)->first();
                if (empty($checkTweet)) {
                    Twitter::create([
                        'text' => $tweet->text,
                        'id_twitt' => (int)$tweet->id_str,
                    ]);
                }
            }
        } else {
            // En cas d'erreur, afficher un message
            return response()->json(['error' => 'Erreur lors de la récupération des tweets.']);
        }
    }
}

