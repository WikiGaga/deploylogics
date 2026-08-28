<?php

$config = [];

if (env('WHATSAPP_MODE', null) == 'SANDBOX') {
    $config = [
        'whatsapp_mode' => env('WHATSAPP_MODE', null),
        'verify_token' => env('VERIFY_TOKEN', null),
        'whatsapp_phone_number_id' => env('PHONE_NUMBER_ID_SANDBOX', null),
        'whatsapp_token' => env('WHATSAPP_TOKEN_SANDBOX', null),
        'whatsapp_api_version' => env('WHATSAPP_API_VERSION', 'v20.0'),
        // Legacy aliases
        'phone_number_id' => env('PHONE_NUMBER_ID_SANDBOX', null),
    ];
} elseif (env('WHATSAPP_MODE', null) == 'LIVE') {
    $config = [
        'whatsapp_mode' => env('WHATSAPP_MODE', null),
        'verify_token' => env('VERIFY_TOKEN', null),
        'whatsapp_phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', env('PHONE_NUMBER_ID', null)),
        'whatsapp_token' => env('WHATSAPP_TOKEN', null),
        'whatsapp_api_version' => env('WHATSAPP_API_VERSION', 'v20.0'),
        // Legacy aliases for WhatsAppApiController
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', env('PHONE_NUMBER_ID', null)),
    ];
}

$config['salary_notification'] = [
    'template_name' => env('WA_SALARY_TEMPLATE', 'v3_employee_salary_notification'),
    'template_lang' => env('WA_SALARY_TEMPLATE_LANG', 'en'),
];

// WhatsApp Intelligent API (manual PO/Customer sends — separate from Meta)
$config['intelligent'] = [
    'api_url' => env('WHATSAPP_INTELLIGENT_API_URL', 'http://whatsintelligent.com/api/create-message'),
    'appkey' => env('WHATSAPP_INTELLIGENT_APPKEY', '2fa4c714-9a38-4f81-851b-3470c758c18b'),
    'authkey' => env('WHATSAPP_INTELLIGENT_AUTHKEY', 'yy3fbHr1GdTaP5D8Tte9w4BlvAmOk0yddf7s8tz0F8L4cZc1iA'),
    'sandbox' => env('WHATSAPP_INTELLIGENT_SANDBOX', 'false'),
];

return $config;
