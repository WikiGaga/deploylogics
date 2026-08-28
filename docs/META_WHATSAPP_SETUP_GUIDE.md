# Meta WhatsApp Business API — Setup & Implementation Guide

**Project:** GBC (Laravel)  
**Purpose:** Document Meta WhatsApp Business API setup and how this application implements automated order notifications, so the same architecture can be replicated in other apps.

**Last updated:** August 2026

---

## Table of Contents

1. [Overview](#1-overview)
2. [Meta / Facebook Setup (One-Time)](#2-meta--facebook-setup-one-time)
3. [Environment Variables](#3-environment-variables)
4. [Application Configuration](#4-application-configuration)
5. [Database Requirements](#5-database-requirements)
6. [Architecture](#6-architecture)
7. [Core Sending Logic](#7-core-sending-logic)
8. [PDF Flow (Order Confirmation)](#8-pdf-flow-order-confirmation)
9. [When Messages Are Triggered](#9-when-messages-are-triggered)
10. [Queue Jobs — Retry & Logging](#10-queue-jobs--retry--logging)
11. [Queue Worker Setup](#11-queue-worker-setup)
12. [Implementation Checklist (Other Apps)](#12-implementation-checklist-other-apps)
13. [Testing Guide](#13-testing-guide)
14. [Known Gaps / Not Implemented](#14-known-gaps--not-implemented)
15. [Minimal Reference Implementation](#15-minimal-reference-implementation)
16. [File Map in This Repository](#16-file-map-in-this-repository)

---

## 1. Overview

This app uses the **Meta WhatsApp Business API** (Facebook Graph API) to send **automated template messages** in two cases:

| Event | Trigger | Job | Template Name |
|---|---|---|---|
| Order received / modified | POS order saved & printed | `POSOrderReceived` | `v3_malek_al_pizza_order_confirmation_notification` |
| Order ready | Kitchen marks order as `ready` | `POSOrderReady` | `v3_order_ready_malek_al_pizza` |

**Important notes:**

- Automated Meta messages run **only when** `WHATSAPP_MODE=LIVE`.
- The manual **"Send WhatsApp"** button on the vendor order page uses a **different provider** (WhatsApp Intelligent API via `whatsintelligent.com`) and is **not** part of this Meta flow.
- There is **no dedicated WhatsApp Composer package** — sending uses Laravel `Http` facade and Guzzle.

---

## 2. Meta / Facebook Setup (One-Time)

### Step 1 — Create Meta Business Assets

1. Go to [Meta Business Suite](https://business.facebook.com/) and create or verify your business.
2. Go to [Meta for Developers](https://developers.facebook.com/) → **My Apps** → **Create App**.
3. Choose app type: **Business**.
4. Add product: **WhatsApp**.

### Step 2 — Add a WhatsApp Business Account (WABA)

1. In the app dashboard: **WhatsApp → Getting Started**.
2. Connect or create a **WhatsApp Business Account**.
3. Add and verify a **phone number** (this becomes your sending number).
4. Note the **Phone Number ID** (this is **not** the phone number itself — it is a numeric ID shown in API Setup).

### Step 3 — Get a Permanent Access Token

1. In **WhatsApp → API Setup**, create a **System User** token (recommended for production).
2. Required permissions:
   - `whatsapp_business_messaging`
   - `whatsapp_business_management`
3. Save the token:
   - Live: `WHATSAPP_TOKEN`
   - Sandbox: `WHATSAPP_TOKEN_SANDBOX`

### Step 4 — Create and Approve Message Templates

Templates must be created in **WhatsApp Manager → Message Templates** and **approved by Meta** before they can be sent.

#### Template A — Order Confirmation

| Property | Value |
|---|---|
| **Name** | `v3_malek_al_pizza_order_confirmation_notification` |
| **Category** | Utility or Marketing (as applicable) |
| **Language** | English (`en`) |
| **Header** | Document (PDF invoice attachment) |
| **Body** | Named parameters (see table below) |

**Body parameters (order in code must match Meta template exactly):**

| # | Parameter Name | Example (EN) | Example (AR) |
|---|---|---|---|
| 1 | `order_status_ar` | — | `استلام` or `تعديل` |
| 2 | `branch_name_ar` | — | Branch name in Arabic |
| 3 | `order_status_en` | `received` or `modified` | — |
| 4 | `branch_name_en` | Branch name | — |
| 5 | `order_id_ar` | — | Order serial (`.` instead of `-`) |
| 6 | `order_id_en` | Order serial | — |
| 7 | `total_amnt_ar` | — | `12.500 ر.ع.` |
| 8 | `total_amnt_en` | `12.500 OMR` | — |
| 9 | `branch_no_ar` | — | Restaurant phone |
| 10 | `branch_no_en` | Restaurant phone | — |

**Header:** PDF document with dynamic filename (order serial).

#### Template B — Order Ready

| Property | Value |
|---|---|
| **Name** | `v3_order_ready_malek_al_pizza` |
| **Language** | Arabic (`ar`) |
| **Header** | None |
| **Body** | Named parameters (see table below) |

**Body parameters:**

| # | Parameter Name | Source in App |
|---|---|---|
| 1 | `order_number_ar` | Order serial (`.` instead of `-`) |
| 2 | `order_number_en` | Same |
| 3 | `branch_name_ar` | `config('constants.invoice_branch_name')` |
| 4 | `branch_name_en` | `$order->restaurant->name` |
| 5 | `branch_phone_ar` | `$order->restaurant->phone` |
| 6 | `branch_phone_en` | Same |

> **For other apps:** Replace template names and parameter labels to match your brand, but keep the **same order and naming** in both Meta Manager and application code.

### Step 5 — Sandbox vs Live

| Mode | Use Case | Environment Variables |
|---|---|---|
| `SANDBOX` | Meta test numbers only | `PHONE_NUMBER_ID_SANDBOX`, `WHATSAPP_TOKEN_SANDBOX` |
| `LIVE` | Production customer messaging | `WHATSAPP_PHONE_NUMBER_ID`, `WHATSAPP_TOKEN` |

This app gates all automated sends on:

```php
config('whatsapp.whatsapp_mode') == 'LIVE'
```

---

## 3. Environment Variables

Add these to `.env`:

```env
# Meta WhatsApp
WHATSAPP_MODE=LIVE
WHATSAPP_API_VERSION=v20.0
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_TOKEN=your_permanent_access_token
VERIFY_TOKEN=your_webhook_verify_token

# Sandbox (optional — for Meta test environment)
# WHATSAPP_MODE=SANDBOX
# PHONE_NUMBER_ID_SANDBOX=...
# WHATSAPP_TOKEN_SANDBOX=...

# Branch / invoice labels used in template parameters
BRANCH_ID=1
INVOICE_BRANCH_NAME=المصنعة

# PDF upload to public server (required for order confirmation with attachment)
LIVE_SERVER_API_URL=https://your-live-server.com/api/v1
SYNC_API_TOKEN=your_shared_secret_token
LIVE_SERVER_API_TIMEOUT=60

# Queue (required for async sending)
QUEUE_CONNECTION=database
```

---

## 4. Application Configuration

### 4.1 `config/whatsapp.php`

```php
<?php

$config = [];

if (env('WHATSAPP_MODE', NULL) == 'SANDBOX') {
    $config = [
        'whatsapp_mode' => env('WHATSAPP_MODE', NULL),
        'verify_token' => env('VERIFY_TOKEN', NULL),
        'whatsapp_phone_number_id' => env('PHONE_NUMBER_ID_SANDBOX', NULL),
        'whatsapp_token' => env('WHATSAPP_TOKEN_SANDBOX', NULL),
        'whatsapp_api_version' => env('WHATSAPP_API_VERSION', 'v20.0'),
    ];
} elseif (env('WHATSAPP_MODE', NULL) == 'LIVE') {
    $config = [
        'whatsapp_mode' => env('WHATSAPP_MODE', NULL),
        'verify_token' => env('VERIFY_TOKEN', NULL),
        'whatsapp_phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', NULL),
        'whatsapp_token' => env('WHATSAPP_TOKEN', NULL),
        'whatsapp_api_version' => env('WHATSAPP_API_VERSION', 'v20.0'),
    ];
}

return $config;
```

### 4.2 `config/services.php` (PDF hosting)

Order confirmation attaches a PDF. Meta requires a **public HTTPS URL** it can fetch:

```php
'live_server' => [
    'url' => env('LIVE_SERVER_API_URL', 'https://malikalpizza.royalerp.net/api/v1'),
    'token' => env('SYNC_API_TOKEN'),
    'timeout' => env('LIVE_SERVER_API_TIMEOUT', 60),
    'restaurant_id' => env('RESTAURANT_ID'),
],

'sync_api' => [
    'token' => env('SYNC_API_TOKEN'),
],
```

### 4.3 `config/constants.php` (template labels)

```php
'branch_id' => env('BRANCH_ID'),
'invoice_branch_name' => env('INVOICE_BRANCH_NAME', 'المصنعة'),
```

---

## 5. Database Requirements

### 5.1 `orders` table

Add column if not present:

```sql
ALTER TABLE orders ADD COLUMN whatsapp_confirmation_sent_at TIMESTAMP NULL;
```

**Purpose:** Prevents duplicate "new order" confirmation messages.

### 5.2 `order_whatsapp_msg_log` table

Audit log for all WhatsApp send attempts:

```sql
CREATE TABLE order_whatsapp_msg_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    message_status VARCHAR(20) NOT NULL,
    message_type VARCHAR(50) NOT NULL,
    order_amount DECIMAL(10,3) NULL,
    branch_id INT NULL,
    phone VARCHAR(30) NULL,
    message_exception TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**`message_status` values:** `success`, `failed`

**`message_type` values:**

| Value | Meaning |
|---|---|
| `order_creation` | New POS order confirmation |
| `order_modification` | Updated POS order confirmation |
| `order_ready` | Kitchen ready notification |

### 5.3 Laravel queue tables

```bash
php artisan queue:table
php artisan migrate
```

---

## 6. Architecture

```
┌─────────────────┐     WHATSAPP_MODE=LIVE      ┌──────────────────┐
│  POSController  │ ──────────────────────────► │ POSOrderReceived │
│  (order save)   │   dispatch(phone, orderId)  │  queue: whatsapp │
└─────────────────┘                             └────────┬─────────┘
                                                         │
┌─────────────────┐     status = ready          ┌────────▼─────────┐
│ KitchenController│ ─────────────────────────► │  POSOrderReady   │
└─────────────────┘                             └────────┬─────────┘
                                                         │
                                                ┌────────▼─────────┐
                                                │  WhatsappService │
                                                └────────┬─────────┘
                                                         │
                    ┌────────────────────────────────────┼────────────────────────┐
                    │                                    │                        │
            Generate PDF                         Meta Graph API              OrderWhatsappMsgLog
            Upload to live server          POST /{phone_id}/messages         (success/failure)
            Return public URL              Template + parameters
```

### Key Files

| File | Role |
|---|---|
| `config/whatsapp.php` | Credentials and mode switching |
| `app/Services/WhatsappService.php` | Meta API calls, PDF generation, phone formatting |
| `app/Jobs/POSOrderReceived.php` | Async order confirmation job |
| `app/Jobs/POSOrderReady.php` | Async order ready notification job |
| `app/Models/OrderWhatsappMsgLog.php` | Message audit log model |
| `app/Services/UploadPdfService.php` | Live server PDF receiver |
| `routes/api_sync.php` | `POST /upload-order-pdf` endpoint |
| `app/Http/Controllers/Vendor/POSController.php` | Dispatches order confirmation |
| `app/Http/Controllers/Vendor/KitchenController.php` | Dispatches order ready |

---

## 7. Core Sending Logic

### 7.1 Meta API Endpoint

```http
POST https://graph.facebook.com/{API_VERSION}/{PHONE_NUMBER_ID}/messages
Authorization: Bearer {WHATSAPP_TOKEN}
Content-Type: application/json
```

Implemented in `WhatsappService` using:

```php
$url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNoId}/messages";

$response = Http::withToken($this->token)
    ->acceptJson()
    ->post($url, $payload);
```

### 7.2 Order Confirmation Payload (Simplified)

```json
{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "+96891234567",
  "type": "template",
  "template": {
    "name": "v3_malek_al_pizza_order_confirmation_notification",
    "language": { "code": "en" },
    "components": [
      {
        "type": "header",
        "parameters": [{
          "type": "document",
          "document": {
            "link": "https://your-server.com/uploads/attachments/123456_99.pdf",
            "filename": "ORD.12345.pdf"
          }
        }]
      },
      {
        "type": "body",
        "parameters": [
          { "type": "text", "parameter_name": "order_status_ar", "text": "استلام" },
          { "type": "text", "parameter_name": "branch_name_ar", "text": "المصنعة" },
          { "type": "text", "parameter_name": "order_status_en", "text": "received" },
          { "type": "text", "parameter_name": "branch_name_en", "text": "Branch Name" },
          { "type": "text", "parameter_name": "order_id_ar", "text": "ORD.12345" },
          { "type": "text", "parameter_name": "order_id_en", "text": "ORD.12345" },
          { "type": "text", "parameter_name": "total_amnt_ar", "text": "12.500 ر.ع." },
          { "type": "text", "parameter_name": "total_amnt_en", "text": "12.500 OMR" },
          { "type": "text", "parameter_name": "branch_no_ar", "text": "91234567" },
          { "type": "text", "parameter_name": "branch_no_en", "text": "91234567" }
        ]
      }
    ]
  }
}
```

**On success**, the app sets:

```php
$order->whatsapp_confirmation_sent_at = now();
$order->save();
```

### 7.3 Order Ready Payload

Same structure as above, using:

- Template: `v3_order_ready_malek_al_pizza`
- Language: `ar`
- No document header
- 6 body parameters only

### 7.4 Phone Number Formatting

`WhatsappService::formatPhoneNumber()` rules:

| Input | Output |
|---|---|
| `91234567` (Oman mobile, 8 digits starting with 7 or 9) | `+96891234567` |
| `96891234567` | `+96891234567` |
| `+96891234567` | unchanged |
| `0096891234567` | `+96891234567` |
| Invalid format | throws `InvalidArgumentException` |

### 7.5 Template Parameter Sanitization

WhatsApp rejects parameters with newlines or excessive spaces:

```php
private function cleanParam($text)
{
    $text = (string) $text;

    // No newlines or tabs
    $text = str_replace(["\r\n", "\r", "\n", "\t"], ' | ', $text);

    // Max 4 consecutive spaces
    $text = preg_replace('/ {5,}/', '    ', $text);

    return trim($text);
}
```

---

## 8. PDF Flow (Order Confirmation)

Meta document headers require a **publicly accessible HTTPS URL**. Branch/POS servers generate PDFs locally, then upload to the live server.

### Flow

```
POS / Branch Server                          Live Server
─────────────────                            ───────────
1. Render `bill-pdf` Blade view (Dompdf)
2. Save locally to public/uploads/attachments/
3. POST /upload-order-pdf  ────────────────► UploadPdfService
   - pdf file (multipart)                     - validates secret_key
   - secret_key = SYNC_API_TOKEN              - saves to public/uploads/attachments/
                                               - returns public URL
4. Use returned URL in Meta template header
```

### Live Server Endpoint

**URL:** `POST {LIVE_SERVER_API_URL}/upload-order-pdf`

**Route file:** `routes/api_sync.php` (protected by `validate.api.token` middleware)

**Request fields:**

| Field | Type | Required |
|---|---|---|
| `pdf` | file (PDF, max 10MB) | Yes |
| `secret_key` | string | Yes (must match `SYNC_API_TOKEN`) |

**Success response:**

```json
{
  "success": true,
  "file_name": "1712345678_abc123.pdf",
  "url": "https://your-domain.com/uploads/attachments/1712345678_abc123.pdf"
}
```

> **For other apps:** If PDFs are already hosted on S3/CDN with public HTTPS URLs, skip the upload step and pass that URL directly to Meta.

---

## 9. When Messages Are Triggered

### 9.1 Order Confirmation — `POSController`

**File:** `app/Http/Controllers/Vendor/POSController.php`

After POS order commit and kitchen print, if `WHATSAPP_MODE=LIVE` and phone is present:

```php
POSOrderReceived::dispatch($request->phone, $order->id, $state)
    ->onConnection('database')
    ->onQueue('whatsapp');
```

**`$state` values:**

| State | When |
|---|---|
| `'new'` | First-time order creation |
| `'update'` | Order being edited |

**Dispatch conditions:**

| Payment Status | Printed State | Sends When |
|---|---|---|
| Unpaid | First print (`printed == 0`) | Always (if phone provided) |
| Unpaid | Re-edit (`printed == 1`) | Items changed, new items, or deleted items |
| Paid | Any | Items need reprint (new/changed/deleted) |

**Phone source:** `$request->phone` from the POS form input.

### 9.2 Order Ready — `KitchenController`

**File:** `app/Http/Controllers/Vendor/KitchenController.php`

When kitchen status changes to `ready`:

```php
$phone = $order->customer ? $order->customer->customer_mobile_no : null;

if ($phone && config('whatsapp.whatsapp_mode') == 'LIVE') {
    POSOrderReady::dispatch($phone, $order->id, 'ready')
        ->onConnection('database')
        ->onQueue('whatsapp');
}
```

**Phone source:** `tbl_sale_customer.customer_mobile_no` via `$order->customer` relationship.

### 9.3 Duplicate Prevention — `POSOrderReceived`

```php
if ($this->state == 'new' && !empty($order->whatsapp_confirmation_sent_at)) {
    return; // skip — confirmation already sent for this order
}
```

---

## 10. Queue Jobs — Retry & Logging

Both `POSOrderReceived` and `POSOrderReady` share these settings:

| Setting | Value |
|---|---|
| Queue name | `whatsapp` |
| Connection | `database` |
| Max tries | 3 |
| Backoff | 30s, 120s, 300s |
| Timeout | 60 seconds |
| Uniqueness | 10 minutes (`ShouldBeUnique`) |
| Unique ID pattern | `pos-order-whatsapp-{orderId}-{phone}-{state}` |

### Error Handling

**Temporary errors** (timeout, connection refused, HTTP 5xx) → job re-throws exception and retries.

**Permanent errors** (invalid phone, template mismatch, auth failure) → `$this->fail($e)` — no more retries.

### Logging

Every attempt writes to `order_whatsapp_msg_log`:

```php
OrderWhatsappMsgLog::create([
    'order_id' => $order->id,
    'message_status' => 'success', // or 'failed'
    'message_type' => 'order_creation', // or 'order_modification', 'order_ready'
    'order_amount' => $order->order_amount,
    'branch_id' => config('constants.branch_id'),
    'phone' => $this->phone,
    'message_exception' => $e->getMessage(), // on failure only
]);
```

Laravel log entries:

- Success: `Whatsapp Message Sent` / `Whatsapp Order Ready Message Sent`
- Failure: `Whatsapp Message Send Error` / `Whatsapp Order Ready Message Send Error`
- Permanent failure: `Whatsapp Message Job Permanently Failed`

---

## 11. Queue Worker Setup

Automated messages are **queued**, not sent synchronously during the HTTP request.

### Development

```bash
php artisan queue:work database --queue=whatsapp --tries=3 --timeout=60
```

### Production (Supervisor example)

```ini
[program:laravel-whatsapp-worker]
command=php /path/to/artisan queue:work database --queue=whatsapp --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/laravel-whatsapp-worker.log
```

**Note:** If `QUEUE_CONNECTION=sync`, jobs run inline during the request. This works for local debugging but is not recommended for production.

---

## 12. Implementation Checklist (Other Apps)

### Meta / Infrastructure

- [ ] Meta Business app created with WhatsApp product enabled
- [ ] WhatsApp Business Account (WABA) connected
- [ ] Phone number verified
- [ ] Permanent System User access token with messaging permissions
- [ ] Message templates created and **approved** in WhatsApp Manager
- [ ] Template parameter names match application code exactly
- [ ] Public HTTPS URL available for PDF documents

### Application Code

- [ ] `config/whatsapp.php` with SANDBOX/LIVE mode support
- [ ] `WhatsappService` class with:
  - [ ] `sendOrderConfirmationMessage()`
  - [ ] `sendOrderReadyMessage()`
  - [ ] `formatPhoneNumber()`
  - [ ] `cleanParam()`
  - [ ] PDF generation and public URL resolution
- [ ] Queue jobs: `POSOrderReceived`, `POSOrderReady`
- [ ] `OrderWhatsappMsgLog` model and database table
- [ ] `orders.whatsapp_confirmation_sent_at` column
- [ ] Trigger points in POS and Kitchen controllers
- [ ] Gate all sends on `WHATSAPP_MODE == 'LIVE'`

### Operations

- [ ] `.env` configured on all POS/branch servers
- [ ] Queue worker running on each server that dispatches jobs
- [ ] `jobs` and `failed_jobs` tables migrated
- [ ] Log monitoring for WhatsApp errors
- [ ] Monitor `order_whatsapp_msg_log` for failed sends

---

## 13. Testing Guide

### Sandbox Testing

1. Set `WHATSAPP_MODE=SANDBOX` (note: this app's code only auto-sends on `LIVE` — temporarily change the gate or test via tinker for sandbox).
2. Add test recipient numbers in Meta **WhatsApp → API Setup → To**.
3. Place a test POS order with a phone number.
4. Verify job appears in `jobs` table with queue `whatsapp`.
5. Run queue worker and confirm message delivery in WhatsApp.

### Live Testing

1. Set `WHATSAPP_MODE=LIVE`.
2. Use a real customer number (must comply with Meta opt-in policy).
3. Test both flows:
   - New order → confirmation message with PDF attachment
   - Kitchen status → ready → ready notification

### Common Failures

| Error | Likely Cause | Fix |
|---|---|---|
| `(#132001) Template name does not exist` | Template not approved or name mismatch | Verify exact template name in Meta Manager |
| `(#131008) Required parameter is missing` | Parameter count/names don't match template | Align code parameters with approved template |
| `(#131026) Message undeliverable` | Invalid number or no WhatsApp account | Verify phone formatting |
| PDF header fails | URL not public HTTPS or unreachable | Test URL in browser; check live server |
| Nothing sent | Mode not LIVE or queue not running | Check `WHATSAPP_MODE` and queue worker |
| Duplicate confirmations | Timestamp not saved | Verify `whatsapp_confirmation_sent_at` is set on success |

### Manual Test via Tinker

```php
php artisan tinker

$order = App\Models\Order::with('restaurant.branch', 'details')->find(ORDER_ID);
$service = new App\Services\WhatsappService();
$service->sendOrderConfirmationMessage('+96891234567', $order, 'new');
```

---

## 14. Known Gaps / Not Implemented

| Feature | Status in This App |
|---|---|
| WhatsApp webhook (delivery/read receipts) | Not implemented — `VERIFY_TOKEN` is configured but unused |
| Incoming customer replies | Not implemented |
| Opt-in / consent tracking | Not in code — must be handled per Meta policy |
| SANDBOX auto-send | Code only dispatches when mode is `LIVE` |
| Unified phone field | POS uses `$request->phone`; kitchen uses `customer_mobile_no` |

---

## 15. Minimal Reference Implementation

### Service Skeleton (for new Laravel apps)

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    protected $phoneNoId;
    protected $token;
    protected $apiVersion;

    public function __construct()
    {
        $this->phoneNoId = config('whatsapp.whatsapp_phone_number_id');
        $this->token = config('whatsapp.whatsapp_token');
        $this->apiVersion = config('whatsapp.whatsapp_api_version', 'v20.0');
    }

    public function sendTemplate(string $to, string $templateName, string $lang, array $components): array
    {
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

        if ($response->failed()) {
            throw new \Exception('WhatsApp send failed: ' . json_encode($response->json()));
        }

        return $response->json();
    }

    private function formatPhoneNumber(string $phone): string
    {
        $phone = trim($phone);
        $phone = preg_replace('/(?!^\+)[^\d]/', '', $phone);

        if (str_starts_with($phone, '00')) {
            $phone = '+' . substr($phone, 2);
        }

        if (preg_match('/^[79]\d{7}$/', $phone)) {
            return '+968' . $phone;
        }

        if (preg_match('/^968[79]\d{7}$/', $phone)) {
            return '+' . $phone;
        }

        if (preg_match('/^\+[1-9]\d{7,14}$/', $phone)) {
            return $phone;
        }

        throw new \InvalidArgumentException('Invalid phone number format.');
    }
}
```

### Job Dispatch Pattern

```php
if ($phone && config('whatsapp.whatsapp_mode') === 'LIVE') {
    POSOrderReceived::dispatch($phone, $order->id, 'new')
        ->onConnection('database')
        ->onQueue('whatsapp');
}
```

### Job Skeleton

```php
<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderWhatsappMsgLog;
use App\Services\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class POSOrderReceived implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $phone;
    public int $orderId;
    public string $state;

    public int $tries = 3;
    public int $timeout = 60;
    public int $uniqueFor = 600;

    public function __construct(string $phone, int $orderId, string $state = 'new')
    {
        $this->phone = $phone;
        $this->orderId = $orderId;
        $this->state = $state;
    }

    public function uniqueId(): string
    {
        return 'pos-order-whatsapp-' . $this->orderId . '-' . $this->phone . '-' . $this->state;
    }

    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function handle(): void
    {
        $order = Order::with(['restaurant', 'details'])->findOrFail($this->orderId);

        if ($this->state === 'new' && !empty($order->whatsapp_confirmation_sent_at)) {
            return;
        }

        $service = new WhatsappService();
        $service->sendOrderConfirmationMessage($this->phone, $order, $this->state);

        OrderWhatsappMsgLog::create([
            'order_id' => $order->id,
            'message_status' => 'success',
            'message_type' => $this->state === 'new' ? 'order_creation' : 'order_modification',
            'order_amount' => $order->order_amount,
            'branch_id' => config('constants.branch_id'),
            'phone' => $this->phone,
        ]);
    }

    public function failed(Throwable $e): void
    {
        $order = Order::find($this->orderId);

        OrderWhatsappMsgLog::create([
            'order_id' => $order->id,
            'message_status' => 'failed',
            'message_type' => $this->state === 'new' ? 'order_creation' : 'order_modification',
            'order_amount' => $order->order_amount,
            'branch_id' => config('constants.branch_id'),
            'message_exception' => $e->getMessage(),
            'phone' => $this->phone,
        ]);
    }
}
```

---

## 16. File Map in This Repository

```
config/
├── whatsapp.php                  Meta API credentials (SANDBOX / LIVE)
├── services.php                  Live server PDF upload config
└── constants.php                 Branch name for template parameters

app/Services/
├── WhatsappService.php           Meta API calls, PDF generation, phone formatting
└── UploadPdfService.php          Live server endpoint — receives and stores PDFs

app/Jobs/
├── POSOrderReceived.php          Queued order confirmation sender
└── POSOrderReady.php             Queued order ready sender

app/Models/
└── OrderWhatsappMsgLog.php       Audit log model (table: order_whatsapp_msg_log)

app/Http/Controllers/Vendor/
├── POSController.php             Dispatches POSOrderReceived after order save
└── KitchenController.php         Dispatches POSOrderReady when status = ready

routes/
└── api_sync.php                  POST /upload-order-pdf (PDF hosting endpoint)
```

---

## Summary

This application implements Meta WhatsApp Business API messaging using a **queue-based architecture**:

1. **POS or Kitchen event** triggers a queued job (only when `WHATSAPP_MODE=LIVE`).
2. **Job** loads order data and calls `WhatsappService`.
3. **WhatsappService** generates a PDF (for confirmations), uploads it to a public server, builds a Meta template payload, and POSTs to the Graph API.
4. **Result** is logged to `order_whatsapp_msg_log` and Laravel logs.
5. **Duplicate prevention** uses `whatsapp_confirmation_sent_at` on the order record.

To replicate in another app, follow the checklist in Section 12 and adapt template names, phone sources, and PDF hosting to your environment while keeping the same overall flow.

---

*Document generated from GBC codebase analysis. For questions about this implementation, refer to the source files listed in Section 16.*
