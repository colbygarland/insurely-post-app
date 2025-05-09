<?php

return [
    'twilio' => [
        'default' => 'twilio',
        'connections' => [
            'twilio' => [
                /*
                |--------------------------------------------------------------------------
                | SID
                |--------------------------------------------------------------------------
                |
                | Your Twilio Account SID #
                |
                */
                'sid' => env('TWILIO_ACCOUNT_SID', ''),

                /*
                |--------------------------------------------------------------------------
                | Access Token
                |--------------------------------------------------------------------------
                |
                | Access token that can be found in your Twilio dashboard
                |
                */
                'token' => env('TWILIO_AUTH_TOKEN', ''),

                /*
                |--------------------------------------------------------------------------
                | From Number
                |--------------------------------------------------------------------------
                |
                | The Phone number registered with Twilio that your SMS & Calls will come from
                |
                */
                'from' => env('TWILIO_PHONE_NUMBER', ''),
            ],
        ],
    ],
];
