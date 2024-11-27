<?php
    if(env('WHATSAPP_MODE' , NULL) == 'SANDBOX'){
        return [
            'verify_token' => env('VERIFY_TOKEN' , NULL),
            'phone_number_id' => env('PHONE_NUMBER_ID_SANDBOX' , NULL),
            'whatsapp_token' => env('WHATSAPP_TOKEN_SANDBOX' , NULL),
        ];
    }
    if(env('WHATSAPP_MODE', NULL) == 'LIVE'){
        return [
            'verify_token' => env('VERIFY_TOKEN' , NULL),
            'phone_number_id' => env('PHONE_NUMBER_ID' , NULL),
            'whatsapp_token'   => env('WHATSAPP_TOKEN' , NULL)
        ];
    }
