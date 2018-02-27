<?php

namespace App\BL\Email;

use Exception;
use GuzzleHttp;
use Log;

class GalileoMailer
{

    public static function send($email, $subject, $message, $appId = null)
    {
        $client = new GuzzleHttp\Client([
            'base_uri' => 'http://34.210.2.200:5657',
        ]);

        try {

            $response = $client->post('email/sendmessage/', [
                'auth' => [
                    'phpuser',
                    'str0ngp@55w0rd',
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'em' => $email,
                    'sb' => $subject,
                    'mg' => $message,
                    'ai' => $appId ? $appId : '1',
                ],
            ]);

        } catch (GuzzleHttp\Exception\ClientException $ex) {

            $response = $ex->getResponse();

        }

        $json = json_decode($response->getBody(), true);

        Log::info('Email Sending Status Code: ' . $response->getStatusCode());

        return $json;
    }
}
