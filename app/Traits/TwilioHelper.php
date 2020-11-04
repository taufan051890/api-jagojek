<?php

namespace App\Traits;

use Twilio\Rest\Client;


trait TwilioHelper {

    private function sendMessage($phone,$message){

        $client = new Client(
            config('app.twilio_sid'),
            config('app.twilio_auth_token')
        );

        try{

            $client->messages->create('+'.$phone,[
                'from' => 'JagojekId',
                'body' => $message
            ]);

            return true;

        }catch (\Exception $e){

            return false;

        }
    }

    private function startVerification($phone){
        $phone = '+'.$phone;

        $client = new Client(
            config('app.twilio_sid'),
            config('app.twilio_auth_token')
        );

        try{
            $verification = $client->verify->v2
                ->services(config('app.twilio_verification_sid'))
                ->verifications
                ->create($phone,'sms');

            return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }

    }

    private function verifyAuthentication($phone,$code){
        $phone = '+'.$phone;

        $client = new Client(
            config('app.twilio_sid'),
            config('app.twilio_auth_token')
        );

        try{
            $verification = $client->verify->v2
                ->services(config('app.twilio_verification_sid'))
                ->verificationChecks
                ->create($code,['to'=>$phone]);

            return $verification;

        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
