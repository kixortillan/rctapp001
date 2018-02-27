<?php

namespace App\BL\Auth;

use GuzzleHttp;

class Authenticate
{
    public static function login(string $username, string $password)
    {
        $http = new GuzzleHttp\Client([
            'base_uri' => env('APP_URL'),
        ]);

        try {

            $response = $http->post('/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env('OAUTH2_CLIENT_ID'),
                    'client_secret' => env('OAUTH2_CLIENT_SECRET'),
                    'username' => $username,
                    'password' => $password,
                    'scope' => '',
                ],
            ]);

        } catch (GuzzleHttp\Exception\ClientException $ex) {

            throw $ex;

        }

        $body = json_decode((string) $response->getBody(), true);

        try {

            $response = $http->get('/api/user', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $body['access_token'],
                ],
            ]);

        } catch (GuzzleHttp\Exception\ClientException $ex) {

            throw $ex;

        }

        $userInfo = json_decode((string) $response->getBody(), true);

        $user = $userInfo['data']['user']['data'];
        $roles = $userInfo['data']['roles']['data'];

        $response = array_merge($body, [
            'user' => $user,
            'roles' => $roles,
        ]);

        return $response;

    }
}
