<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ChatbotController extends Controller
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
        $this->baseUrl = 'https://api.openai.com/v1';
    }

    public function processMessage(Request $request): JsonResponse
    {
        // try {
            $request->validate([
                'message' => 'required|string|max:500',
                'conversation_id' => 'nullable|string',
                'category' => 'nullable|string|in:sales,purchases,inventory,accounts'
            ]);

            $message = $request->input('message');
            $category = $request->input('category', 'sales');
            $conversationId = $request->input('conversation_id', uniqid());
            $user = Auth::user();
            $this->logConversation($user, $message, $conversationId, 'user', $category);

            $response = $this->generateAIResponse($message, $user, $category, $conversationId);

            $this->logConversation($user, $response, $conversationId, 'bot', $category);

            return response()->json([
                'success' => true,
                'response' => $response,
                'conversation_id' => $conversationId,
                'category' => $category,
                'timestamp' => now()->format('H:i')
            ]);

        // } catch (\Exception $e) {
        //     Log::error('Chatbot error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());

        //     return response()->json([
        //         'success' => false,
        //         'response' => 'Sorry, I encountered an error while processing your request. Please try again.',
        //         'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
        //     ], 500);
        // }
    }

    private function generateAIResponse(string $message, $user, string $category = 'sales', string $conversationId = null): string
    {
        // try {
            $assistantId = $this->getOrCreateAssistant($category);

            $threadId = $this->getOrCreateThread($conversationId, $user->id);

            $this->addMessageToThread($threadId, $message);

            $runId = $this->runAssistant($threadId, $assistantId);

            $aiResponse = $this->waitForRunCompletion($threadId, $runId);

            if (strpos($aiResponse, 'SIMPLE_QUERY:') !== false) {
                return $this->handleSimpleQuery($aiResponse);
            }

            if (strpos($aiResponse, 'GENERATE_REPORT:') !== false) {
                return $this->handleExcelReportGeneration($message, $aiResponse, $user, $category, $conversationId);
            }

            return $this->cleanResponse($aiResponse);

        // } catch (\Exception $e) {
        //     Log::error('OpenAI Assistants API error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
        //     return 'Sorry, I encountered an error. Please try again.';
        // }
    }

    private function getOrCreateAssistant(string $category): string
    {
        $cacheKey = "openai_assistant_id_{$category}";

        $assistantId = Cache::get($cacheKey);

        if ($assistantId) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'OpenAI-Beta' => 'assistants=v2'
                ])->get($this->baseUrl . "/assistants/{$assistantId}");

                if ($response->successful()) {
                    Log::info("Using existing assistant: {$assistantId} for category: {$category}");
                    return $assistantId;
                }
            } catch (\Exception $e) {
                Log::warning("Cached assistant not found, creating new one: " . $e->getMessage());
            }
        }

        return $this->createAssistant($category);
    }

    private function createAssistant(string $category): string
    {
        $systemPrompt = $this->getSystemPromptForCategory($category);
        $categoryInfo = $this->getCategorySchemas()[$category] ?? ['name' => 'Sales'];

        Log::info("Creating new assistant for category: {$category}");

        $response = Http::timeout(30)->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
        ])->post($this->baseUrl . '/assistants', [
            'name' => $categoryInfo['name'] . ' Data Assistant',
            'instructions' => $systemPrompt,
            'model' => 'gpt-4o-mini',
            'temperature' => 0.3,
            'tools' => []
        ]);

        if (!$response->successful()) {
            $statusCode = $response->status();
            $errorBody = $response->body();
            $errorMessage = 'Failed to create assistant';

            try {
                $errorData = $response->json();
                if (isset($errorData['error']['message'])) {
                    $errorMessage = $errorData['error']['message'];
                }
            } catch (\Exception $e) {
            }

            Log::error("Failed to create assistant for category {$category}. Status: {$statusCode}, Error: {$errorBody}");

            if ($statusCode === 401) {
                throw new \Exception('Invalid OpenAI API key. Please check your configuration.');
            } elseif ($statusCode === 429) {
                throw new \Exception('OpenAI API rate limit exceeded. Please try again later.');
            } elseif ($statusCode >= 500) {
                throw new \Exception('OpenAI service is currently unavailable. Please try again later.');
            } else {
                throw new \Exception('Failed to create assistant: ' . $errorMessage);
            }
        }

        $data = $response->json();
        $assistantId = $data['id'];

        Cache::put("openai_assistant_id_{$category}", $assistantId, now()->addDays(30));

        Log::info("Created new assistant: {$assistantId} for category: {$category}");

        return $assistantId;
    }

    private function getOrCreateThread(string $conversationId, int $userId): string
    {
        $cacheKey = "openai_thread_{$conversationId}_{$userId}";

        $threadId = Cache::get($cacheKey);

        if ($threadId) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'OpenAI-Beta' => 'assistants=v2'
                ])->get($this->baseUrl . "/threads/{$threadId}");

                if ($response->successful()) {
                    Log::info("Using existing thread: {$threadId} for conversation: {$conversationId}");
                    return $threadId;
                }
            } catch (\Exception $e) {
                Log::warning("Cached thread not found, creating new one: " . $e->getMessage());
            }
        }

        return $this->createThread($conversationId, $userId);
    }

    private function createThread(string $conversationId, int $userId): string
    {
        Log::info("Creating new thread for conversation: {$conversationId}");

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
        ])->post($this->baseUrl . '/threads', [
            'metadata' => [
                'conversation_id' => (string) $conversationId,
                'user_id' => (string) $userId
            ]
        ]);

        if (!$response->successful()) {
            Log::error('Failed to create thread: ' . $response->body());
            throw new \Exception('Failed to create thread');
        }

        $data = $response->json();
        $threadId = $data['id'];

        Cache::put("openai_thread_{$conversationId}_{$userId}", $threadId, now()->addHours(24));

        Log::info("Created new thread: {$threadId} for conversation: {$conversationId}");

        return $threadId;
    }

    private function addMessageToThread(string $threadId, string $message): void
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
        ])->post($this->baseUrl . "/threads/{$threadId}/messages", [
            'role' => 'user',
            'content' => $message
        ]);

        if (!$response->successful()) {
            Log::error('Failed to add message to thread: ' . $response->body());
            throw new \Exception('Failed to add message to thread');
        }

        Log::info("Added message to thread: {$threadId}");
    }

    private function runAssistant(string $threadId, string $assistantId): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
        ])->post($this->baseUrl . "/threads/{$threadId}/runs", [
            'assistant_id' => $assistantId
        ]);

        if (!$response->successful()) {
            Log::error('Failed to run assistant: ' . $response->body());
            throw new \Exception('Failed to run assistant');
        }

        $data = $response->json();
        $runId = $data['id'];

        Log::info("Started assistant run: {$runId} on thread: {$threadId}");

        return $runId;
    }

    private function waitForRunCompletion(string $threadId, string $runId): string
    {
        $maxAttempts = 60;
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'OpenAI-Beta' => 'assistants=v2'
            ])->get($this->baseUrl . "/threads/{$threadId}/runs/{$runId}");

            if (!$response->successful()) {
                Log::error('Failed to check run status: ' . $response->body());
                throw new \Exception('Failed to check run status');
            }

            $run = $response->json();
            $status = $run['status'];

            Log::info("Run {$runId} status: {$status} (attempt {$attempts})");

            if ($status === 'completed') {
                return $this->getLatestAssistantMessage($threadId);
            }

            if (in_array($status, ['failed', 'cancelled', 'expired'])) {
                $errorMessage = $run['last_error']['message'] ?? 'Unknown error';
                Log::error("Assistant run failed with status {$status}: {$errorMessage}");
                throw new \Exception("Assistant run {$status}: {$errorMessage}");
            }

            sleep(1);
            $attempts++;
        }

        Log::error("Assistant run timeout after {$maxAttempts} seconds");
        throw new \Exception('Assistant run timeout - please try again');
    }

    private function getLatestAssistantMessage(string $threadId): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'OpenAI-Beta' => 'assistants=v2'
        ])->get($this->baseUrl . "/threads/{$threadId}/messages", [
            'limit' => 1,
            'order' => 'desc'
        ]);

        if (!$response->successful()) {
            Log::error('Failed to retrieve messages: ' . $response->body());
            throw new \Exception('Failed to retrieve assistant response');
        }

        $data = $response->json();

        if (empty($data['data'])) {
            throw new \Exception('No messages found in thread');
        }

        $message = $data['data'][0];

        if (isset($message['content'][0]['text']['value'])) {
            return $message['content'][0]['text']['value'];
        }

        throw new \Exception('Invalid message format');
    }

    private function handleSimpleQuery(string $aiResponse): string
    {
        try {
            $parts = explode('SIMPLE_QUERY:', $aiResponse);
            if (!isset($parts[1])) {
                return $this->cleanResponse($aiResponse);
            }

            $query = $this->cleanSQLQuery(trim($parts[1]));
            $naturalText = trim($parts[0]);

            $this->validateQuery($query);
            $result = DB::select($query);

            if (empty($result)) {
                return "No data found.";
            }

            $firstRow = (array) $result[0];
            $value = reset($firstRow);

            if ($naturalText) {
                return $naturalText . ": " . $value;
            }

            return "Result: " . $value;

        } catch (\Exception $e) {
            Log::error('Simple query error: ' . $e->getMessage());
            return "I couldn't fetch that information. Please try again.";
        }
    }

    private function cleanResponse(string $response): string
    {
        $response = preg_replace('/GENERATE_REPORT:.*$/s', '', $response);
        $response = preg_replace('/SIMPLE_QUERY:.*$/s', '', $response);
        $response = preg_replace('/```sql.*?```/s', '', $response);
        $response = preg_replace('/SELECT.*?FROM.*?;/si', '', $response);
        return trim($response);
    }

    private function getCategorySchemas(): array
    {
        return [
            'sales' => [
                'name' => 'Sales',
                'description' => 'Sales orders report with comprehensive order information',
                'prompt' => $this->getSalesPrompt(),
            ],
            'purchases' => [
                'name' => 'Purchases',
                'description' => 'Purchase orders report with comprehensive purchase information',
                'prompt' => $this->getPurchasesPrompt(),
            ],
            'inventory' => [
                'name' => 'Inventory',
                'description' => 'Stock inventory report with product quantities',
                'prompt' => $this->getInventoryPrompt(),
            ],
            'accounts' => [
                'name' => 'Accounts',
                'description' => 'Accounting reports with financial information',
                'prompt' => $this->getAccountsPrompt(),
            ]
        ];
    }

    private function getSalesPrompt(): string
    {
        return "You are an expert Oracle SQL generator for a restaurant ERP system.

Your job:
- Convert the user's natural language sales question into ONE Oracle SELECT query.
- Use only the views: VW_REST_SUMMARY_ORDER_WISE and VW_REST_ORDER_DTL.
- Always return ONLY the SQL query (no explanation, no markdown, no comments).
- BUT if the user's question is unclear or ambiguous, ask a clarification question instead of creating a wrong query.

-----------------------------------------------------
DATABASE SCHEMA
-----------------------------------------------------

1) VW_REST_SUMMARY_ORDER_WISE  (order-level summary)
Columns:
ORDER_DATE (DATE)
BRANCH_ID (NUMBER)
BRANCH_NAME (VARCHAR2)
ORDER_ID (NUMBER or VARCHAR2)
ORDER_SERIAL (VARCHAR2)
ITEMS_AMOUNT (NUMBER)
DISCOUNT_ON_ITEMS (NUMBER)
TOTAL_ADD_ON_PRICE (NUMBER)
GROSS_SALES (NUMBER)
DISCOUNT_BY_RESTAURANT (NUMBER)
COUPON_DISCOUNT (NUMBER)
TOTAL_DISCOUNTS (NUMBER)
DELIVERY_CHARGES (NUMBER)
VAT (NUMBER)
NET_SALES (NUMBER)
PAYMENT_METHOD (VARCHAR2)
BANK_ID (NUMBER)
PAYMENT_STATUS (VARCHAR2)
ORDER_STATUS (VARCHAR2)
SALES_TYPE (VARCHAR2)
CASH_SALES (NUMBER)
CARD_SALES (NUMBER)
CREDIT_SALES (NUMBER)
DELIVERY_PARTNER_SALES (NUMBER)
DELIVERY_SALES (NUMBER)
DINE_IN_SALES (NUMBER)
TAKEAWAY_SALES (NUMBER)
PARTNER_NAME (VARCHAR2)
CUSTOMER_NAME (VARCHAR2)
CUSTOMER_ID (NUMBER)

2) VW_REST_ORDER_DTL (item-level)
Columns:
ID (NUMBER)
ORDER_ID (NUMBER or VARCHAR2)
ORDER_SERIAL (VARCHAR2)
ORDER_DATE (DATE)
ORDER_STATUS (VARCHAR2)
PAYMENT_STATUS (VARCHAR2)
ORDER_TYPE (VARCHAR2)
BRANCH_ID (NUMBER)
BRANCH_NAME (VARCHAR2)
FOOD_ID (NUMBER)
FOOD_NAME (VARCHAR2)
PRICE (NUMBER)
QUANTITY (NUMBER)
ITEM_AMOUNT (NUMBER)
ITEM_DISCOUNT (NUMBER)
ITEM_AMOUNT_AFTER_DISCOUNT (NUMBER)
TOTAL_ADD_ON_PRICE (NUMBER)
ITEM_NET_AMOUNT (NUMBER)
IS_DELETED (VARCHAR2)

-----------------------------------------------------
BUSINESS RULES
-----------------------------------------------------
- \"Sales\", \"sale\", \"revenue\" → SUM(NET_SALES) from VW_REST_SUMMARY_ORDER_WISE.
- ALWAYS apply:
  PAYMENT_STATUS = 'paid'
  ORDER_STATUS <> 'canceled'
- For item-level queries:
  TRIM(IS_DELETED) <> 'Y'

-----------------------------------------------------
DATE RULES
-----------------------------------------------------
- \"today\" → TRUNC(ORDER_DATE) = TRUNC(SYSDATE)
- \"yesterday\" → TRUNC(ORDER_DATE) = TRUNC(SYSDATE-1)
- \"last 7 days\" → ORDER_DATE >= TRUNC(SYSDATE-6)
- Specific dates:
  Accept formats:
    \"7th Nov\", \"7th November\", \"07-11-25\", \"07/11/2025\"
  Convert with TO_DATE using DD-MM-YYYY or YYYY-MM-DD.
- If user gives day + month but no year → assume current year.
- If user gives no date at all → assume TODAY.

-----------------------------------------------------
BRANCH NAME HANDLING
-----------------------------------------------------
- If user mentions a word that looks like a branch (example: \"mussanah\", \"seeb\", \"sohar\")
  → Filter using:
    LOWER(BRANCH_NAME) LIKE '%<term>%'
- The branch name can be any unknown word; no predefined list.

-----------------------------------------------------
FOOD NAME HANDLING
-----------------------------------------------------
- If user mentions a food name (example: \"family meal\", \"chicken burger\", \"tuesday meal\")
  → Filter using:
    LOWER(FOOD_NAME) LIKE '%<term>%'

-----------------------------------------------------
PAYMENT METHOD HANDLING
-----------------------------------------------------
Interpret common synonyms:
- \"cash\" → PAYMENT_METHOD = 'cash'
- \"card\", \"visa\", \"master\", \"credit card\" → PAYMENT_METHOD IN ('card','visa','master')
- \"credit\", \"on account\" → PAYMENT_METHOD = 'credit'
- \"delivery partner\", partner name such as \"Talabat\", \"Akeed\":
    LOWER(PARTNER_NAME) LIKE '%talabat%'

If user says:
\"give sales for cash, card and credit\"
→ Use GROUP BY PAYMENT_METHOD.

-----------------------------------------------------
SALES TYPE HANDLING (service type)
-----------------------------------------------------
User terms → SALES_TYPE mapping:
- dine in, dine-in, eat in → 'dine in'
- take away, takeaway, pickup → 'take away'
- delivery, home delivery → 'delivery'

If user asks:
\"sales for dine in, take away, delivery\"
→ Group by SALES_TYPE.

-----------------------------------------------------
TOP TRENDING FOOD
-----------------------------------------------------
Top food by quantity:
  SELECT FOOD_ID, FOOD_NAME, SUM(QUANTITY) ...
  ORDER BY SUM(QUANTITY) DESC

Top food by amount:
  SELECT FOOD_ID, FOOD_NAME, SUM(ITEM_NET_AMOUNT) ...
  ORDER BY SUM(ITEM_NET_AMOUNT) DESC

-----------------------------------------------------
AMBIGUITY HANDLING (IMPORTANT)
-----------------------------------------------------
IF a term can be BOTH:
- a branch candidate AND
- a food name candidate AND
- not previously known
THEN DO NOT generate SQL.

Ask EXACTLY ONE SHORT QUESTION like:
\"Is 'mussanah' a branch or a food item?\"
\"Is 'tuesday meal' a food item or something else?\"
\"Does 'royal' refer to a branch, partner, or food item?\"

Only after the user clarifies → generate SQL.

-----------------------------------------------------
OUTPUT RULE
-----------------------------------------------------
- If the question is clear → return ONE complete Oracle SQL query.
- If ambiguous → ask a clarification question.
- NEVER output explanation, markdown, lists, steps, analysis, or text outside the SQL.

-----------------------------------------------------
RESPONSE FORMAT
-----------------------------------------------------
1. Simple questions (total, count, sum):
   Format: Natural text SIMPLE_QUERY: SELECT query
   Example: 'Total sales today SIMPLE_QUERY: SELECT NVL(SUM(NET_SALES), 0) FROM VW_REST_SUMMARY_ORDER_WISE WHERE TRUNC(ORDER_DATE) = TRUNC(SYSDATE) AND PAYMENT_STATUS = ''paid'' AND ORDER_STATUS <> ''canceled'''

2. Detailed lists/reports:
   Format: GENERATE_REPORT: SELECT query
   Example: 'GENERATE_REPORT: SELECT * FROM VW_REST_SUMMARY_ORDER_WISE WHERE ORDER_DATE >= SYSDATE - 30 AND PAYMENT_STATUS = ''paid'' AND ORDER_STATUS <> ''canceled'' ORDER BY ORDER_DATE DESC'";
    }

    private function getPurchasesPrompt(): string
    {
        return "You are an expert Oracle SQL generator for a restaurant ERP system.

Your job:
- Convert the user's natural language purchase question into ONE Oracle SELECT query.
- Use the views/tables for purchases (update this with your actual view names).
- Always return ONLY the SQL query (no explanation, no markdown, no comments).
- BUT if the user's question is unclear or ambiguous, ask a clarification question instead of creating a wrong query.

-----------------------------------------------------
DATABASE SCHEMA
-----------------------------------------------------
(Update this section with your purchase tables/views and columns)

-----------------------------------------------------
OUTPUT RULE
-----------------------------------------------------
- If the question is clear → return ONE complete Oracle SQL query.
- If ambiguous → ask a clarification question.
- NEVER output explanation, markdown, lists, steps, analysis, or text outside the SQL.

-----------------------------------------------------
RESPONSE FORMAT
-----------------------------------------------------
1. Simple questions (total, count, sum):
   Format: Natural text SIMPLE_QUERY: SELECT query

2. Detailed lists/reports:
   Format: GENERATE_REPORT: SELECT query";
    }

    private function getInventoryPrompt(): string
    {
        return "You are an expert Oracle SQL generator for a restaurant ERP system.

Your job:
- Convert the user's natural language inventory question into ONE Oracle SELECT query.
- Use the views/tables for inventory (update this with your actual view names).
- Always return ONLY the SQL query (no explanation, no markdown, no comments).
- BUT if the user's question is unclear or ambiguous, ask a clarification question instead of creating a wrong query.

-----------------------------------------------------
DATABASE SCHEMA
-----------------------------------------------------
(Update this section with your inventory tables/views and columns)

-----------------------------------------------------
OUTPUT RULE
-----------------------------------------------------
- If the question is clear → return ONE complete Oracle SQL query.
- If ambiguous → ask a clarification question.
- NEVER output explanation, markdown, lists, steps, analysis, or text outside the SQL.

-----------------------------------------------------
RESPONSE FORMAT
-----------------------------------------------------
1. Simple questions (total, count, sum):
   Format: Natural text SIMPLE_QUERY: SELECT query

2. Detailed lists/reports:
   Format: GENERATE_REPORT: SELECT query";
    }

    private function getAccountsPrompt(): string
    {
        return "You are an expert Oracle SQL generator for a restaurant ERP system.

Your job:
- Convert the user's natural language accounts question into ONE Oracle SELECT query.
- Use the views/tables for accounts (update this with your actual view names).
- Always return ONLY the SQL query (no explanation, no markdown, no comments).
- BUT if the user's question is unclear or ambiguous, ask a clarification question instead of creating a wrong query.

-----------------------------------------------------
DATABASE SCHEMA
-----------------------------------------------------
(Update this section with your accounts tables/views and columns)

-----------------------------------------------------
OUTPUT RULE
-----------------------------------------------------
- If the question is clear → return ONE complete Oracle SQL query.
- If ambiguous → ask a clarification question.
- NEVER output explanation, markdown, lists, steps, analysis, or text outside the SQL.

-----------------------------------------------------
RESPONSE FORMAT
-----------------------------------------------------
1. Simple questions (total, count, sum):
   Format: Natural text SIMPLE_QUERY: SELECT query

2. Detailed lists/reports:
   Format: GENERATE_REPORT: SELECT query";
    }

    private function getSystemPromptForCategory(string $category): string
    {
        $categoryInfo = $this->getCategorySchemas()[$category] ?? null;

        if ($categoryInfo && isset($categoryInfo['prompt'])) {
            return $categoryInfo['prompt'];
        }

        return "You are a business data assistant for " . ($categoryInfo['name'] ?? 'General') . ".

RESPONSE FORMAT:

1. Simple questions (total, count, sum):
   Format: Natural text SIMPLE_QUERY: SELECT query

2. Detailed lists/reports:
   Format: GENERATE_REPORT: SELECT query

RULES:
- Use Oracle syntax (TRUNC, NVL, TO_DATE, SYSDATE)
- Never mention SQL or technical terms
- Query executes automatically
- No explanations needed";
    }

    private function buildSchemaForCategory(string $category): string
    {
        $categories = $this->getCategorySchemas();

        if (!isset($categories[$category])) {
            $category = 'sales';
        }

        $categoryData = $categories[$category];

        if (isset($categoryData['prompt'])) {
            return $categoryData['prompt'];
        }

        return "No schema defined for category: {$category}";
    }

    private function handleExcelReportGeneration(string $message, string $aiResponse, $user, string $category, string $conversationId = null): string
    {
        try {
            $reportData = $this->extractReportData($message, $aiResponse, $category);
            $query = $reportData['query'];

            $this->validateQuery($query);
            $data = DB::select($query);

            if (empty($data)) {
                return "No data found for your request.";
            }

            $filename = 'report_' . time() . '.xlsx';
            $filePath = storage_path('app/public/reports/' . $filename);

            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }


            if ($category === 'purchases') {
                $this->generatePurchaseOrderExcelFile($data, $filePath);
            } elseif ($category === 'inventory') {
                $this->generateInventoryExcelFile($data, $filePath);
            } else {
                $this->generateExcelFile($data, $filePath);
            }

            $this->storeReportMetadata($filename, $conversationId, $user->id);

            $downloadUrl = url('storage/reports/' . $filename);

            return "📊 Your report is ready!\n[DOWNLOAD_EXCEL:" . $downloadUrl . "]";

        } catch (\Exception $e) {
            Log::error('Report generation error: ' . $e->getMessage());
            return "I encountered an error while generating the report. Please try again.";
        }
    }

    private function generateExcelFile($data, $filePath): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $dataArray = array_map(function($row) {
            return (array) $row;
        }, $data);

        if (empty($dataArray)) {
            return;
        }

        $headers = array_keys($dataArray[0]);
        $sheet->fromArray($headers, NULL, 'A1');

        $rowNum = 2;
        foreach ($dataArray as $row) {
            $sheet->fromArray(array_values($row), NULL, 'A' . $rowNum);
            $rowNum++;
        }

        $totalsRow = $this->calculateTotals($dataArray, $headers);
        if (!empty($totalsRow)) {
            $sheet->fromArray($totalsRow, NULL, 'A' . $rowNum);
            $totalStyle = $sheet->getStyle('A' . $rowNum . ':' . $sheet->getHighestColumn() . $rowNum);
            $totalStyle->getFont()->setBold(true);
            $totalStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setRGB('F3F6F9');
        }

        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $headerStyle = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('667EEA');
        $headerStyle->getFont()->getColor()->setRGB('FFFFFF');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    private function generatePurchaseOrderExcelFile($data, $filePath): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $dataArray = array_map(function($row) {
            return (array) $row;
        }, $data);

        if (empty($dataArray)) {
            return;
        }

        $groupedData = [];
        foreach ($dataArray as $row) {
            $date = date('d-m-Y', strtotime($row['PURCHASE_ORDER_ENTRY_DATE'] ?? $row['purchase_order_entry_date']));
            $poCode = $row['PURCHASE_ORDER_CODE'] ?? $row['purchase_order_code'];
            $supplierName = $row['SUPPLIER_NAME'] ?? $row['supplier_name'];

            $groupedData[$date][$poCode . ' ' . $supplierName][] = $row;
        }

        $headers = ['Barcode', 'Product Name', 'UOM', 'Packing', 'Quantity', 'Rate', 'Amount', 'Disc Amount', 'Vat Amount'];
        $sheet->fromArray($headers, NULL, 'A1');

        $headerStyle = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('D3D3D3');
        $headerStyle->getFont()->getColor()->setRGB('000000');

        $currentRow = 2;

        foreach ($groupedData as $date => $purchaseOrders) {

            $sheet->setCellValue('A' . $currentRow, $date);
            $sheet->mergeCells('A' . $currentRow . ':I' . $currentRow);
            $dateStyle = $sheet->getStyle('A' . $currentRow);
            $dateStyle->getFont()->setBold(true);
            $dateStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setRGB('F0F0F0');
            $currentRow++;

            foreach ($purchaseOrders as $poHeader => $items) {

                $sheet->setCellValue('A' . $currentRow, $poHeader);
                $sheet->mergeCells('A' . $currentRow . ':I' . $currentRow);
                $poStyle = $sheet->getStyle('A' . $currentRow);
                $poStyle->getFont()->setBold(true);
                $poStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFFACD'); // Light yellow/gold
                $currentRow++;

                $poTotals = [
                    'quantity' => 0,
                    'amount' => 0,
                    'disc_amount' => 0,
                    'vat_amount' => 0
                ];

                foreach ($items as $item) {
                    $rowData = [
                        $item['PRODUCT_BARCODE_BARCODE'] ?? $item['product_barcode_barcode'] ?? '',
                        $item['PRODUCT_NAME'] ?? $item['product_name'] ?? '',
                        $item['UOM_NAME'] ?? $item['uom_name'] ?? '',
                        $item['PURCHASE_ORDER_DTLPACKING'] ?? $item['purchase_order_dtlpacking'] ?? '',
                        $item['PURCHASE_ORDER_DTLQUANTITY'] ?? $item['purchase_order_dtlquantity'] ?? '',
                        number_format($item['PURCHASE_ORDER_DTLRATE'] ?? $item['purchase_order_dtlrate'] ?? 0, 3),
                        number_format($item['PURCHASE_ORDER_DTLAMOUNT'] ?? $item['purchase_order_dtlamount'] ?? 0, 3),
                        number_format($item['PURCHASE_ORDER_DTLDISC_AMOUNT'] ?? $item['purchase_order_dtldisc_amount'] ?? 0, 3),
                        number_format($item['PURCHASE_ORDER_DTLVAT_AMOUNT'] ?? $item['purchase_order_dtlvat_amount'] ?? 0, 3)
                    ];

                    $sheet->fromArray($rowData, NULL, 'A' . $currentRow);

                    $itemStyle = $sheet->getStyle('A' . $currentRow . ':I' . $currentRow);
                    $itemStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $poTotals['quantity'] += (float)($item['PURCHASE_ORDER_DTLQUANTITY'] ?? $item['purchase_order_dtlquantity'] ?? 0);
                    $poTotals['amount'] += (float)($item['PURCHASE_ORDER_DTLAMOUNT'] ?? $item['purchase_order_dtlamount'] ?? 0);
                    $poTotals['disc_amount'] += (float)($item['PURCHASE_ORDER_DTLDISC_AMOUNT'] ?? $item['purchase_order_dtldisc_amount'] ?? 0);
                    $poTotals['vat_amount'] += (float)($item['PURCHASE_ORDER_DTLVAT_AMOUNT'] ?? $item['purchase_order_dtlvat_amount'] ?? 0);

                    $currentRow++;
                }

                $totalRowData = [
                    'Total:',
                    '',
                    '',
                    '',
                    number_format($poTotals['quantity'], 0),
                    '',
                    number_format($poTotals['amount'], 3),
                    number_format($poTotals['disc_amount'], 3),
                    number_format($poTotals['vat_amount'], 3)
                ];

                $sheet->fromArray($totalRowData, NULL, 'A' . $currentRow);
                $totalStyle = $sheet->getStyle('A' . $currentRow . ':I' . $currentRow);
                $totalStyle->getFont()->setBold(true);
                $totalStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                           ->getStartColor()->setRGB('F3F6F9');
                $totalStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $currentRow++;
            }
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    private function generateInventoryExcelFile($data, $filePath): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $dataArray = array_map(function($row) {
            return (array) $row;
        }, $data);

        if (empty($dataArray)) {
            return;
        }

        $headers = ['Sr. No', 'BarCode', 'Product Name', 'Quantity', 'Rate'];
        $sheet->fromArray($headers, NULL, 'A1');

        $headerStyle = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('D3D3D3');
        $headerStyle->getFont()->getColor()->setRGB('000000');

        $currentRow = 2;
        $totalQuantity = 0;
        $totalRate = 0;
        $serialNumber = 1;

        foreach ($dataArray as $row) {
            $barcode = $row['PRODUCT_BARCODE_BARCODE'] ?? $row['product_barcode_barcode'] ?? '';
            $productName = $row['PRODUCT_NAME'] ?? $row['product_name'] ?? '';
            $quantity = $row['TBL_PURC_GRN_DTL_QUANTITY'] ?? $row['tbl_purc_grn_dtl_quantity'] ?? 0;
            $rate = $row['TBL_PURC_GRN_DTL_RATE'] ?? $row['tbl_purc_grn_dtl_rate'] ?? 0;

            $formattedQuantity = (float)$quantity;
            if ($formattedQuantity == (int)$formattedQuantity) {
                $formattedQuantity = (int)$formattedQuantity;
            } else {
                $formattedQuantity = number_format($formattedQuantity, 3);
            }

            $formattedRate = number_format((float)$rate, 3);

            $rowData = [
                $serialNumber,
                $barcode,
                strtoupper($productName),
                $formattedQuantity,
                $formattedRate
            ];

            $sheet->fromArray($rowData, NULL, 'A' . $currentRow);

            $itemStyle = $sheet->getStyle('A' . $currentRow . ':E' . $currentRow);
            $itemStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $totalQuantity += (float)$quantity;
            $totalRate += (float)$rate;

            $currentRow++;
            $serialNumber++;
        }

        $totalRowData = [
            '',
            '',
            'Total:',
            number_format($totalQuantity, 3),
            number_format($totalRate, 3)
        ];

        $sheet->fromArray($totalRowData, NULL, 'A' . $currentRow);
        $totalStyle = $sheet->getStyle('A' . $currentRow . ':E' . $currentRow);
        $totalStyle->getFont()->setBold(true);
        $totalStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                   ->getStartColor()->setRGB('F3F6F9');
        $totalStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    private function calculateTotals(array $dataArray, array $headers): array
    {
        $numericColumns = [
            'GROSS_AMOUNT', 'RESTAURANT_DISCOUNT_AMOUNT', 'DELIVERY_CHARGE',
            'TOTAL_TAX_AMOUNT', 'ORDER_AMOUNT', 'CASH_PAID', 'CARD_PAID',
            'PURCHASE_ORDER_DTLQUANTITY', 'PURCHASE_ORDER_DTLRATE', 'PURCHASE_ORDER_DTLAMOUNT',
            'PURCHASE_ORDER_DTLDISC_AMOUNT', 'PURCHASE_ORDER_DTLVAT_AMOUNT', 'PURCHASE_ORDER_DTLTOTAL_AMOUNT',
            'STOCK_QTY', 'PRODUCT_QTY', 'TBL_PURC_GRN_DTL_QUANTITY', 'TBL_PURC_GRN_DTL_RATE'
        ];

        $totals = [];
        $hasNumericData = false;

        foreach ($headers as $index => $header) {
            $upperHeader = strtoupper($header);

            if (in_array($upperHeader, $numericColumns)) {
                $sum = 0;
                foreach ($dataArray as $row) {
                    $value = array_values($row)[$index];
                    if (is_numeric($value)) {
                        $sum += $value;
                        $hasNumericData = true;
                    }
                }
                $totals[] = number_format($sum, 3);
            } else {
                $totals[] = ($index === 0) ? 'TOTAL' : '';
            }
        }

        return $hasNumericData ? $totals : [];
    }

    private function extractReportData(string $message, string $aiResponse, string $category): array
    {
        $reportData = [
            'title' => ucfirst($category) . ' Report',
            'description' => $message,
            'query' => '',
            'type' => $category
        ];

        if (strpos($aiResponse, 'GENERATE_REPORT:') !== false) {
            $parts = explode('GENERATE_REPORT:', $aiResponse);
            if (isset($parts[1])) {
                $query = trim($parts[1]);
                $query = $this->cleanSQLQuery($query);
                $reportData['query'] = $query;
            }
        }
        if (empty($reportData['query'])) {
            $reportData['query'] = $this->generateBasicQuery($category);
        }

        return $reportData;
    }

    private function cleanSQLQuery(string $text): string
    {
        $text = preg_replace('/```sql\s*/i', '', $text);
        $text = preg_replace('/```\s*$/i', '', $text);

        if (preg_match('/^(.*?);/s', $text, $matches)) {
            return trim($matches[1]);
        }

        return trim($text);
    }

    private function generateBasicQuery(string $category): string
    {
        switch ($category) {
            case 'sales':
                return "SELECT * FROM ORDER_REPORT_VIEW WHERE CREATED_AT >= SYSDATE - 7 AND ROWNUM <= 500";
            case 'purchases':
                return "SELECT * FROM VW_PURC_PURCHASE_ORDER WHERE PURCHASE_ORDER_ENTRY_DATE >= SYSDATE - 7 AND ROWNUM <= 500";
            case 'inventory':
                return "SELECT PRODUCT_ID, PRODUCT_NAME, PRODUCT_BARCODE_BARCODE, TBL_PURC_GRN_DTL_QUANTITY, TBL_PURC_GRN_DTL_RATE, PO_DATE FROM VW_PURC_GRN WHERE PO_DATE >= SYSDATE - 7 AND ROWNUM <= 500 ORDER BY PRODUCT_NAME";
            default:
                return "SELECT 'Report Generated' as status, SYSDATE as generated_at FROM dual";
        }
    }

    private function createReport(array $reportData, $user): string
    {
        $reportId = 'report_' . time() . '_' . rand(1000, 9999);

        DB::table('generated_reports')->insert([
            'report_id' => $reportId,
            'user_id' => $user->id,
            'title' => $reportData['title'],
            'description' => $reportData['description'],
            'query' => $reportData['query'],
            'type' => $reportData['type'],
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return $reportId;
    }

    public function viewReport(Request $request, $reportId): JsonResponse
    {
        try {
            $user = Auth::user();

            $report = DB::table('generated_reports')
                ->where('report_id', $reportId)
                ->where('user_id', $user->id)
                ->first();

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found'
                ], 404);
            }

            $data = $this->executeQuery((string) $report->query);

            DB::table('generated_reports')
                ->where('report_id', $reportId)
                ->update([
                    'status' => 'completed',
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'report' => [
                    'id' => $report->report_id,
                    'title' => $report->title,
                    'description' => $report->description,
                    'type' => $report->type,
                    'data' => $data,
                    'generated_at' => $report->created_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Report viewing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading report: ' . $e->getMessage()
            ], 500);
        }
    }

    private function executeQuery(string $query): array
    {
        try {
            $this->validateQuery($query);

            $results = DB::select($query);
            return array_map(function($row) {
                return (array) $row;
            }, $results);
        } catch (\Exception $e) {
            Log::error('Query execution error: ' . $e->getMessage());
            return [
                ['error' => 'Query execution failed', 'message' => $e->getMessage()]
            ];
        }
    }

    private function validateQuery(string $query): void
    {
        $query = strtoupper(trim($query));

        $dangerousKeywords = ['DROP', 'DELETE', 'UPDATE', 'INSERT', 'TRUNCATE', 'ALTER', 'CREATE', 'GRANT', 'REVOKE'];

        foreach ($dangerousKeywords as $keyword) {
            if (preg_match('/\b' . $keyword . '\b/', $query)) {
                throw new \Exception('Query contains dangerous operations');
            }
        }

        if (strpos($query, 'SELECT') !== 0) {
            throw new \Exception('Only SELECT queries allowed');
        }
    }

    private function logConversation($user, string $message, string $conversationId, string $sender = 'user', string $category = 'sales'): void
    {
        try {
            DB::table('chatbot_conversations')->insert([
                'user_id' => $user->id,
                'conversation_id' => $conversationId,
                'message' => $message,
                'sender' => $sender,
                'category' => $category,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::warning('Could not log chatbot conversation: ' . $e->getMessage());
        }
    }

    public function getConversationHistory(Request $request): JsonResponse
    {
        try {
            $conversationId = $request->input('conversation_id');
            $user = Auth::user();

            $conversations = DB::table('chatbot_conversations')
                ->where('user_id', $user->id)
                ->when($conversationId, function ($query, $conversationId) {
                    return $query->where('conversation_id', $conversationId);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'conversations' => $conversations
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching conversation history: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not fetch conversation history'
            ], 500);
        }
    }

    public function generateReport(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'report_type' => 'required|string',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'filters' => 'nullable|array'
            ]);

            $reportType = $request->input('report_type');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $filters = $request->input('filters', []);

            $reportData = $this->processReportGeneration($reportType, $dateFrom, $dateTo, $filters);

            return response()->json([
                'success' => true,
                'report_data' => $reportData,
                'message' => 'Report generated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating report: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processReportGeneration(string $reportType, ?string $dateFrom, ?string $dateTo, array $filters): array
    {

        return [
            'report_type' => $reportType,
            'date_range' => [
                'from' => $dateFrom,
                'to' => $dateTo
            ],
            'filters' => $filters,
            'data' => [],
            'summary' => 'Report generation logic will be implemented here',
            'generated_at' => now()->toISOString()
        ];
    }

    public function getAnalytics(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $totalConversations = DB::table('chatbot_conversations')
                ->where('user_id', $user->id)
                ->count();

            $todayConversations = DB::table('chatbot_conversations')
                ->where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count();

            $popularTopics = DB::table('chatbot_conversations')
                ->where('user_id', $user->id)
                ->where('sender', 'user')
                ->selectRaw('message, COUNT(*) as count')
                ->groupBy('message')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'analytics' => [
                    'total_conversations' => $totalConversations,
                    'today_conversations' => $todayConversations,
                    'popular_topics' => $popularTopics
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching chatbot analytics: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not fetch analytics'
            ], 500);
        }
    }

    public function clearSchemaCache(Request $request): JsonResponse
    {
        try {
            $categories = array_keys($this->getCategorySchemas());
            $clearedCategories = [];
            $recreatedAssistants = [];

            foreach ($categories as $category) {
                // Note: Schema cache no longer needed - prompts are now hardcoded

                $assistantKey = "openai_assistant_id_{$category}";
                $oldAssistantId = Cache::get($assistantKey);

                if ($oldAssistantId) {
                    try {
                        Http::withHeaders([
                            'Authorization' => 'Bearer ' . $this->apiKey,
                            'OpenAI-Beta' => 'assistants=v2'
                        ])->delete($this->baseUrl . "/assistants/{$oldAssistantId}");

                        Log::info("Deleted old assistant: {$oldAssistantId}");
                    } catch (\Exception $e) {
                        Log::warning("Could not delete old assistant: " . $e->getMessage());
                    }
                }

                Cache::forget($assistantKey);

                try {
                    $newAssistantId = $this->createAssistant($category);
                    $recreatedAssistants[$category] = $newAssistantId;
                } catch (\Exception $e) {
                    Log::error("Failed to recreate assistant for {$category}: " . $e->getMessage());
                }

                $clearedCategories[] = $category;
            }

            return response()->json([
                'success' => true,
                'message' => 'Schema cache cleared and assistants recreated successfully',
                'cleared_categories' => $clearedCategories,
                'recreated_assistants' => $recreatedAssistants,
                'note' => 'All conversations will use the new schema'
            ]);

        } catch (\Exception $e) {
            Log::error('Error clearing schema cache: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not clear schema cache'
            ], 500);
        }
    }

    public function listAssistants(Request $request): JsonResponse
    {
        try {
            $assistants = [];
            $categories = array_keys($this->getCategorySchemas());

            foreach ($categories as $category) {
                $cacheKey = "openai_assistant_id_{$category}";
                $assistantId = Cache::get($cacheKey);

                if ($assistantId) {
                    try {
                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $this->apiKey,
                            'OpenAI-Beta' => 'assistants=v2'
                        ])->get($this->baseUrl . "/assistants/{$assistantId}");

                        if ($response->successful()) {
                            $data = $response->json();
                            $assistants[] = [
                                'category' => $category,
                                'assistant_id' => $assistantId,
                                'name' => $data['name'] ?? 'Unknown',
                                'created_at' => $data['created_at'] ?? null,
                                'model' => $data['model'] ?? 'Unknown',
                            ];
                        }
                    } catch (\Exception $e) {
                        Log::warning("Failed to fetch assistant for {$category}: " . $e->getMessage());
                    }
                } else {
                    $assistants[] = [
                        'category' => $category,
                        'assistant_id' => null,
                        'status' => 'Not created yet'
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'assistants' => $assistants
            ]);

        } catch (\Exception $e) {
            Log::error('Error listing assistants: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not list assistants'
            ], 500);
        }
    }

    public function deleteAssistant(Request $request, string $category): JsonResponse
    {
        try {
            if (!array_key_exists($category, $this->getCategorySchemas())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category'
                ], 400);
            }

            $cacheKey = "openai_assistant_id_{$category}";
            $assistantId = Cache::get($cacheKey);

            if (!$assistantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No assistant found for this category'
                ], 404);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'OpenAI-Beta' => 'assistants=v2'
            ])->delete($this->baseUrl . "/assistants/{$assistantId}");

            if (!$response->successful()) {
                throw new \Exception('Failed to delete assistant from OpenAI');
            }

            Cache::forget($cacheKey);

            Log::info("Deleted assistant {$assistantId} for category: {$category}");

            return response()->json([
                'success' => true,
                'message' => 'Assistant deleted successfully',
                'category' => $category,
                'assistant_id' => $assistantId
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting assistant: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not delete assistant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCategories(Request $request): JsonResponse
    {
        try {
            $categories = $this->getCategorySchemas();

            $formattedCategories = [];
            foreach ($categories as $key => $data) {
                $formattedCategories[] = [
                    'id' => $key,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'icon' => $this->getCategoryIcon($key)
                ];
            }

            return response()->json([
                'success' => true,
                'categories' => $formattedCategories
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not fetch categories'
            ], 500);
        }
    }

    private function getCategoryIcon(string $category): string
    {
        $icons = [
            'sales' => '💰',
            'purchases' => '🛒',
            'inventory' => '📦',
            'accounts' => '💳'
        ];

        return $icons[$category] ?? '📊';
    }

    private function storeReportMetadata(string $filename, ?string $conversationId, int $userId): void
    {
        try {
            DB::table('CHATBOT_REPORTS')->insert([
                'FILENAME' => $filename,
                'CONVERSATION_ID' => $conversationId,
                'USER_ID' => $userId,
                'CREATED_AT' => now(),
                'UPDATED_AT' => now()
            ]);
            Log::info('Stored report metadata: ' . $filename . ' for conversation: ' . $conversationId);
        } catch (\Exception $e) {
            Log::warning('Could not store report metadata: ' . $e->getMessage());
        }
    }

    public function clearConversationReports(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'conversation_id' => 'nullable|string'
            ]);

            $conversationId = $request->input('conversation_id');
            $user = Auth::user();
            $deletedCount = 0;
            $reports = [];

            try {
                if ($conversationId) {
                    $reports = DB::table('CHATBOT_REPORTS')
                        ->where('CONVERSATION_ID', $conversationId)
                        ->where('USER_ID', $user->id)
                        ->get();
                } else {
                    $reports = DB::table('CHATBOT_REPORTS')
                        ->where('USER_ID', $user->id)
                        ->get();
                }

                Log::info('Found ' . count($reports) . ' report(s) to delete for user ' . $user->id .
                         ($conversationId ? ' and conversation ' . $conversationId : ''));

                foreach ($reports as $report) {
                    $filename = $report->filename ?? $report->FILENAME ?? null;

                    if (!$filename) {
                        Log::warning('No filename found in report object: ' . json_encode($report));
                        continue;
                    }

                    $filePath = storage_path('app/public/reports/' . $filename);
                    Log::info('Attempting to delete report: ' . $filePath . ' (exists: ' . (file_exists($filePath) ? 'yes' : 'no') . ')');

                    if (file_exists($filePath)) {
                        if (unlink($filePath)) {
                            $deletedCount++;
                            Log::info('Successfully deleted: ' . $filePath);
                        } else {
                            Log::warning('Failed to delete (permission denied?): ' . $filePath);
                        }
                    } else {
                        Log::warning('File not found: ' . $filePath);

                        $altPath = public_path('storage/reports/' . $filename);
                        Log::info('Checking alternative path: ' . $altPath);
                        if (file_exists($altPath)) {
                            if (unlink($altPath)) {
                                $deletedCount++;
                                Log::info('Successfully deleted from alt path: ' . $altPath);
                            }
                        }
                    }
                }

                if ($conversationId) {
                    DB::table('CHATBOT_REPORTS')
                        ->where('CONVERSATION_ID', $conversationId)
                        ->where('USER_ID', $user->id)
                        ->delete();
                } else {
                    DB::table('CHATBOT_REPORTS')
                        ->where('USER_ID', $user->id)
                        ->delete();
                }
            } catch (\Exception $e) {
                Log::warning('chatbot_reports table might not exist, scanning directory instead: ' . $e->getMessage());

                $reportsDir = storage_path('app/public/reports');
                if (is_dir($reportsDir)) {
                    $files = glob($reportsDir . '/report_*.xlsx');
                    foreach ($files as $file) {
                        if (file_exists($file) && unlink($file)) {
                            $deletedCount++;
                            Log::info('Deleted report file: ' . $file);
                        }
                    }
                }
            }

            try {
                if ($conversationId) {
                    DB::table('CHATBOT_CONVERSATIONS')
                        ->where('CONVERSATION_ID', $conversationId)
                        ->where('USER_ID', $user->id)
                        ->delete();
                } else {
                    DB::table('CHATBOT_CONVERSATIONS')
                        ->where('USER_ID', $user->id)
                        ->delete();
                }
            } catch (\Exception $e) {
                Log::warning('Could not clear conversation history: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Conversation and reports cleared successfully',
                'deleted_reports' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error clearing conversation reports: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not clear conversation reports: ' . $e->getMessage()
            ], 500);
        }
    }

    public function previewSchema(Request $request, string $category): JsonResponse
    {
        try {
            if (!array_key_exists($category, $this->getCategorySchemas())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category'
                ], 400);
            }

            $prompt = $this->buildSchemaForCategory($category);

            return response()->json([
                'success' => true,
                'category' => $category,
                'prompt' => $prompt
            ]);

        } catch (\Exception $e) {
            Log::error('Error previewing schema: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not preview schema',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
