<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsServices
{
    public static function send($numbers, $message)
    {
        $response = Http::asForm()->post(config('services.sms.url'), [
            'username'    => config('services.sms.username'),
            'apikey'      => config('services.sms.apikey'),
            'apirequest'  => 'Text',
            'sender'      => config('services.sms.sender'),
            'route'       => config('services.sms.route'),
            'format'      => 'JSON',
            'message'     => $message,
            'mobile'      => $numbers, // comma separated
            'TemplateID'  => config('services.sms.template_id'),
        ]);

        return $response->json();
    }
}
