<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class WhatsappService
{
    protected $phoneNoId;
    protected $token;
    protected $apiVersion;

    public const SALARY_PARAM_ORDER = [
        'employee_name',
        'salary_date',
        'basic_salary',
        'working_hours',
        'leaves',
        'working_days',
        'actual_basic',
        'over_time',
        'bonus',
        'deduction',
        'govt_fines',
        'admin_fines',
        'loan_amount',
        'net_payment',
    ];

    public function __construct()
    {
        $this->phoneNoId = config('whatsapp.whatsapp_phone_number_id');
        $this->token = config('whatsapp.whatsapp_token');
        $this->apiVersion = config('whatsapp.whatsapp_api_version', 'v20.0');
    }

    public function isLiveMode()
    {
        return config('whatsapp.whatsapp_mode') === 'LIVE';
    }

    public function sendTemplate($to, $templateName, $lang, array $components)
    {
        if (empty($this->phoneNoId) || empty($this->token)) {
            throw new \RuntimeException('WhatsApp Meta API credentials are not configured.');
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNoId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $lang],
                'components' => $components,
            ],
        ];

        $response = Http::withToken($this->token)
            ->acceptJson()
            ->post($url, $payload);

        $body = $response->json();

        if ($response->failed()) {
            $message = isset($body['error']['message'])
                ? $body['error']['message']
                : $response->body();
            throw new \RuntimeException('WhatsApp send failed: ' . $message);
        }

        return $body;
    }

    public function sendSalaryNotification($phone, array $namedParams)
    {
        $parameters = [];
        foreach (self::SALARY_PARAM_ORDER as $name) {
            $value = isset($namedParams[$name]) ? $namedParams[$name] : '0';
            $parameters[] = [
                'type' => 'text',
                'parameter_name' => $name,
                'text' => $this->cleanParam($value),
            ];
        }

        $components = [
            [
                'type' => 'body',
                'parameters' => $parameters,
            ],
        ];

        return $this->sendTemplate(
            $phone,
            config('whatsapp.salary_notification.template_name'),
            config('whatsapp.salary_notification.template_lang', 'en'),
            $components
        );
    }

    public function formatPhoneNumber($phone)
    {
        $phone = trim((string) $phone);
        $phone = preg_replace('/(?!^\+)[^\d]/', '', $phone);

        if (strpos($phone, '00') === 0) {
            $phone = '+' . substr($phone, 2);
        }

        // Already E.164 with +
        if (preg_match('/^\+[1-9]\d{7,14}$/', $phone)) {
            return $phone;
        }

        // Oman local mobile: 91234567
        if (preg_match('/^[79]\d{7}$/', $phone)) {
            return '+968' . $phone;
        }

        // Oman with country code: 96891234567
        if (preg_match('/^968[79]\d{7}$/', $phone)) {
            return '+' . $phone;
        }

        // Pakistan with country code: 923097274927
        if (preg_match('/^92(3\d{9})$/', $phone)) {
            return '+' . $phone;
        }

        // Pakistan local with leading zero: 03097274927
        if (preg_match('/^0(3\d{9})$/', $phone)) {
            return '+92' . substr($phone, 1);
        }

        // Pakistan local mobile without zero: 3097274927
        if (preg_match('/^(3\d{9})$/', $phone)) {
            return '+92' . $phone;
        }

        throw new InvalidArgumentException('Invalid phone number format: ' . $phone);
    }

    public function cleanParam($text)
    {
        $text = (string) $text;
        $text = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $text);
        $text = preg_replace('/ {5,}/', '    ', $text);

        return trim($text);
    }

    public function buildPreviewText(array $params)
    {
        return "Dear {$params['employee_name']},\n\n"
            . "Here's the information regarding your salary payment for {$params['salary_date']}:\n\n"
            . "BASIC SALARY: {$params['basic_salary']}\n"
            . "OT/HRS: {$params['working_hours']}\n"
            . "LEAVE: {$params['leaves']}\n"
            . "ACTUAL WORKING DAYS: {$params['working_days']}\n"
            . "ACTUAL BASIC: {$params['actual_basic']}\n"
            . "OVERTIME: {$params['over_time']}\n"
            . "BONUS: {$params['bonus']}\n"
            . "DEDUCTIONS: {$params['deduction']}\n"
            . "GOVERNMENT FINES: {$params['govt_fines']}\n"
            . "ADMIN FINES: {$params['admin_fines']}\n"
            . "LOAN AMOUNT: {$params['loan_amount']}\n\n"
            . "NET PAYMENT: {$params['net_payment']}\n\n"
            . "Thank you.";
    }
}
