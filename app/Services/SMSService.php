<?php

namespace App\Services;

use Twilio\Rest\Client;

class SMSService
{
    protected $client;

    public function __construct()
    {
        // return $this->client = new Client(config('services.twilio.sid'), config('services.twilio.token'));
    }

    public function sendVerification($phoneNumber, $code)
    {
        $this->client->messages->create(
            $phoneNumber,
            [
                'from' => config('services.twilio.phone_number'),
                'body' => "Votre code de vérification est: {$code}",
            ]
        );
    }
}
