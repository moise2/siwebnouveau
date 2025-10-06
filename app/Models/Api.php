<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;
use App\Http\Controllers\NotificationController;
use App\Models\Token;

class Api extends Model
{
    public static function getToken()
    {
        $token = Token::select('token')->first();
        if(!empty($token)){
            return $token->token;
        }else{
            return '';
        }
    }

    // TOKEN
    public static function getAPIToken($username, $password)
    {
        $url = env('API_URL');

        $data_array = array(
            "username" => (string)$username,
            "password" => (string)$password,
        );

        $get_data = \App\Models\Api::callAPI('POST', $url . 'auth/login', $data_array);
        $response = json_decode($get_data, true);
        return $response;
    }

    // LOCALITE
    public static function getLocalites()
    {
        $url = env('API_URL');

        $data_array = array();

        $get_data = \App\Models\Api::callAPI('GET', $url . 'localites?limit=169', $data_array, App\Models\Api::getToken());
        $response = json_decode($get_data, true);
        return $response;
    }

    // PROJETS
    public static function getProjets()
    {
        $url = env('API_URL');

        $data_array = array();

        $get_data = \App\Models\Api::callAPI('GET', $url . 'projets', $data_array, App\Models\Api::getToken());
        $response = json_decode($get_data, true);
        return $response;
    }

    // PROGRAMME
    public static function getProgrammes()
    {
        $url = env('API_URL');

        $data_array = array();

        $get_data = \App\Models\Api::callAPI('GET', $url . 'programmes', $data_array, App\Models\Api::getToken());
        $response = json_decode($get_data, true);
        return $response;
    }

    // PROGRAMME
    public static function getTweets()
    {
        $url = "https://api.twitter.com/2/users/1376503751205392388/tweets";

        $data_array = array();

        $get_data = \App\Models\Api::callAPI('GET', $url, $data_array, "AAAAAAAAAAAAAAAAAAAAAGfFwgEAAAAAPzN9ANCa21SkP6SOq6GSDO3XtTY%3Dh10fri8lItkMJHqy5X2zjwsyMnhf4poK7NlcW31y5AnSHYSJEj");
        $response = json_decode($get_data, true);
        return $response;
    }

    /* API function */
    public static function callAPI($method, $url, $data, $token = null)
    {
        $curl = curl_init();
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        if ($token != null) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);
        if (!$result) {
            die("Connection Failure");
        }
        curl_close($curl);
        return $result;
    }
    /* API function */
}
