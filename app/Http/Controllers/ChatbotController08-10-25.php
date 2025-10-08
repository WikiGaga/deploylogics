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
        try {
            $request->validate([
                'message' => 'required|string|max:500',
                'conversation_id' => 'nullable|string',
                'category' => 'nullable|string|in:sales,general'
            ]);

            $message = $request->input('message');
            $category = $request->input('category', 'general');
            $conversationId = $request->input('conversation_id', uniqid());
            $user = Auth::user();

            $this->logConversation($user, $message, $conversationId, 'user', $category);

            $response = $this->generateAIResponse($message, $user, $category);

            $this->logConversation($user, $response, $conversationId, 'bot', $category);

            return response()->json([
                'success' => true,
                'response' => $response,
                'conversation_id' => $conversationId,
                'category' => $category,
                'timestamp' => now()->format('H:i')
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'response' => 'Sorry, I encountered an error. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    private function generateAIResponse(string $message, $user, string $category = 'general'): string
    {
        try {
            $systemPrompt = $this->getSystemPromptForCategory($category);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $message],
                ],
                'max_tokens' => 1000,
                'temperature' => 0.3,
            ])->json();

            $aiResponse = $response['choices'][0]['message']['content'] ?? 'Sorry, I could not process your request.';

            if ($this->needsExcelReport($message, $aiResponse)) {
                return $this->handleExcelReportGeneration($message, $aiResponse, $user, $category);
            }

            return $this->cleanResponse($aiResponse);

        } catch (\Exception $e) {
            Log::error('OpenAI API error: ' . $e->getMessage());
            return 'Sorry, I encountered an error. Please try again.';
        }
    }

    private function cleanResponse(string $response): string
    {
        $response = preg_replace('/GENERATE_REPORT:.*$/s', '', $response);
        $response = preg_replace('/```sql.*?```/s', '', $response);
        $response = preg_replace('/SELECT.*?FROM.*?;/si', '', $response);
        return trim($response);
    }

    private function needsExcelReport(string $message, string $aiResponse): bool
    {
        $reportKeywords = ['list', 'report', 'show all', 'export', 'download', 'details'];
        $lowerMessage = strtolower($message);

        foreach ($reportKeywords as $keyword) {
            if (strpos($lowerMessage, $keyword) !== false) {
                return true;
            }
        }

        if (strpos($aiResponse, 'GENERATE_REPORT:') !== false) {
            return true;
        }

        return false;
    }

    private function getCategorySchemas(): array
    {
        return [
            'sales' => [
                'name' => 'Sales',
                'description' => 'Sales orders, order details, and POS transactions',
                'tables' => ['ORDERS', 'ORDER_DETAILS', 'POS_ORDER_ADDITIONAL_DTL'],
            ],
            'general' => [
                'name' => 'General',
                'description' => 'All available tables for cross-category queries',
                'tables' => 'ALL',
            ]
        ];
    }

    private function getSystemPromptForCategory(string $category): string
    {
        $cacheKey = "chatbot_schema_{$category}";

        $schema = Cache::remember($cacheKey, now()->addDays(30), function() use ($category) {
            return $this->buildSchemaForCategory($category);
        });

        $categoryInfo = $this->getCategorySchemas()[$category] ?? ['name' => 'General', 'description' => 'General queries'];

        return "You are a helpful business data assistant for {$categoryInfo['name']}.

DATABASE TABLES:
{$schema}

INSTRUCTIONS:
1. For simple questions (total, count, sum), answer directly in chat with the number/value
2. For detailed lists or reports, respond with 'GENERATE_REPORT:' followed by a query
3. Use proper date formats (TO_DATE, TRUNC, NVL)
4. Be friendly and conversational
5. Never mention SQL, database, or technical terms to the user
6. If user asks for 'total sales' just calculate and tell them the number
7. If user asks for 'list of orders' then generate a report

Examples:
- 'What is total sales today?' → Answer: 'Total sales today is $5,240'
- 'Show me all orders from last month' → GENERATE_REPORT: SELECT...

Always be helpful and user-friendly.";
    }

    private function buildSchemaForCategory(string $category): string
    {
        $categories = $this->getCategorySchemas();

        if (!isset($categories[$category])) {
            $category = 'general';
        }

        $categoryData = $categories[$category];

        if ($category === 'general') {
            return $this->buildFullDatabaseSchema();
        }

        return $this->buildCompactSchema($categoryData['tables']);
    }

    private function buildCompactSchema(array $tables): string
    {
        $schema = "";

        try {
            foreach ($tables as $tableName) {
                $columns = DB::select("
                    SELECT column_name, data_type, nullable
                    FROM user_tab_columns
                    WHERE table_name = ?
                    ORDER BY column_id
                ", [strtoupper($tableName)]);

                if (empty($columns)) {
                    continue;
                }

                $schema .= strtoupper($tableName) . "(\n";

                $columnDefs = [];
                foreach ($columns as $col) {
                    $nullable = $col->nullable === 'N' ? ' NOT NULL' : '';
                    $columnDefs[] = "  {$col->column_name} {$col->data_type}{$nullable}";
                }

                $schema .= implode(",\n", $columnDefs);
                $schema .= "\n)\n\n";
            }

            $schema .= "RELATIONSHIPS:\n";
            $tableList = "'" . implode("','", array_map('strtoupper', $tables)) . "'";

            $constraints = DB::select("
                SELECT
                    a.table_name child_table,
                    a.column_name child_column,
                    c_pk.table_name parent_table,
                    b.column_name parent_column
                FROM user_cons_columns a
                JOIN user_constraints c ON a.constraint_name = c.constraint_name
                JOIN user_constraints c_pk ON c.r_constraint_name = c_pk.constraint_name
                JOIN user_cons_columns b ON c_pk.constraint_name = b.constraint_name
                WHERE c.constraint_type = 'R'
                AND a.table_name IN ({$tableList})
            ");

            foreach ($constraints as $fk) {
                $schema .= "- {$fk->child_table}.{$fk->child_column} -> {$fk->parent_table}.{$fk->parent_column}\n";
            }

            if (empty($constraints)) {
                $schema .= "- No foreign key relationships defined\n";
            }

        } catch (\Exception $e) {
            Log::error('Error building schema: ' . $e->getMessage());
            $schema = "Error fetching schema. Using basic structure.\n";
            $schema .= implode(", ", $tables);
        }

        return $schema;
    }

    private function buildFullDatabaseSchema(): string
    {
        $commonTables = ['ORDERS', 'ORDER_DETAILS', 'POS_ORDER_ADDITIONAL_DTL'];
        return $this->buildCompactSchema($commonTables);
    }

    private function handleExcelReportGeneration(string $message, string $aiResponse, $user, string $category): string
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

            $this->generateExcelFile($data, $filePath);

            $downloadUrl = url('storage/reports/' . $filename);

            $cleanResponse = $this->cleanResponse($aiResponse);
            return $cleanResponse . "\n\n📊 Your report is ready!\n[DOWNLOAD_EXCEL:" . $downloadUrl . "]";

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
                return "SELECT COUNT(*) as total_orders FROM ORDERS WHERE ROWNUM <= 10";
            default:
                return "SELECT 'Report Generated' as status, SYSDATE as generated_at FROM dual";
        }
    }

    private function validateQuery(string $query): void
    {
        $query = strtoupper(trim($query));

        $dangerousKeywords = ['DROP', 'DELETE', 'UPDATE', 'INSERT', 'TRUNCATE', 'ALTER', 'CREATE', 'GRANT', 'REVOKE'];
        foreach ($dangerousKeywords as $keyword) {
            if (strpos($query, $keyword) !== false) {
                throw new \Exception('Query contains dangerous operations');
            }
        }

        if (strpos($query, 'SELECT') !== 0) {
            throw new \Exception('Only SELECT queries allowed');
        }
    }

    private function logConversation($user, string $message, string $conversationId, string $sender = 'user', string $category = 'general'): void
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

            foreach ($categories as $category) {
                $cacheKey = "chatbot_schema_{$category}";
                Cache::forget($cacheKey);
                $clearedCategories[] = $category;
            }

            return response()->json([
                'success' => true,
                'message' => 'Schema cache cleared',
                'cleared_categories' => $clearedCategories
            ]);

        } catch (\Exception $e) {
            Log::error('Error clearing schema cache: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Could not clear cache'
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
            'general' => '🔍'
        ];
        return $icons[$category] ?? '📊';
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

            $schema = $this->buildSchemaForCategory($category);

            return response()->json([
                'success' => true,
                'category' => $category,
                'schema' => $schema,
                'cached' => Cache::has("chatbot_schema_{$category}")
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
