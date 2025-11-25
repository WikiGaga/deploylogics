<?php
    $config = [];

    // Meta/Facebook WhatsApp Business API Configuration
    if(env('WHATSAPP_MODE' , NULL) == 'SANDBOX'){
        $config = [
            'verify_token' => env('VERIFY_TOKEN' , NULL),
            'phone_number_id' => env('PHONE_NUMBER_ID_SANDBOX' , NULL),
            'whatsapp_token' => env('WHATSAPP_TOKEN_SANDBOX' , NULL),
        ];
    } elseif(env('WHATSAPP_MODE', NULL) == 'LIVE'){
        $config = [
            'verify_token' => env('VERIFY_TOKEN' , NULL),
            'phone_number_id' => env('PHONE_NUMBER_ID' , NULL),
            'whatsapp_token'   => env('WHATSAPP_TOKEN' , NULL)
        ];
    }

    // WhatsApp Intelligent API Configuration
    $config['intelligent'] = [
        'api_url' => env('WHATSAPP_INTELLIGENT_API_URL', 'http://whatsintelligent.com/api/create-message'),
        'appkey' => env('WHATSAPP_INTELLIGENT_APPKEY', '2fa4c714-9a38-4f81-851b-3470c758c18b'),
        'authkey' => env('WHATSAPP_INTELLIGENT_AUTHKEY', 'yy3fbHr1GdTaP5D8Tte9w4BlvAmOk0yddf7s8tz0F8L4cZc1iA'),
        'sandbox' => env('WHATSAPP_INTELLIGENT_SANDBOX', 'false'),
    ];

    return $config;
