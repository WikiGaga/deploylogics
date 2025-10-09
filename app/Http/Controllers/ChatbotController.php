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
                'response' => 'Sorry, I encountered an error while processing your request. Please try again.',
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

            if ($this->shouldGenerateReport($message, $aiResponse)) {
                return $this->handleReportGeneration($message, $aiResponse, $user, $category);
            }

            return $aiResponse;

        } catch (\Exception $e) {
            Log::error('OpenAI API error: ' . $e->getMessage());
            return 'Sorry, I encountered an error connecting to the AI service. Please try again.';
        }
    }

    /**
     * Get category-specific schema configurations
     */
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

    /**
     * Get system prompt for specific category
     * Uses cached schema for performance
     */
    private function getSystemPromptForCategory(string $category): string
    {
        $cacheKey = "chatbot_schema_{$category}";

        $schema = Cache::remember($cacheKey, now()->addHours(24), function() use ($category) {
            return $this->buildSchemaForCategory($category);
        });

        $categoryInfo = $this->getCategorySchemas()[$category] ?? ['name' => 'General', 'description' => 'General queries'];

        return "You are an Oracle database expert specializing in {$categoryInfo['name']} reporting.

DATABASE SCHEMA ({$categoryInfo['description']}):
{$schema}

YOUR ROLE:
1. Analyze user requests and generate accurate Oracle queries.
2. Dont ask any questions unless very important about the data you need to generate the report.
3. Run the query and return the result in case of simple answers.
4. Generate excel report in case of list or table of data requested."
;
    }

    /**
     * Build database schema for specific category
     * Dynamically fetches table structure from Oracle
     */
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

    /**
     * Build compact schema format for specified tables
     */
    private function buildCompactSchema(array $tables): string
    {
        $schema = "";

        try {
            foreach ($tables as $tableName) {
                // Get columns for each table
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

            // Get foreign key relationships
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

    /**
     * Build full database schema for general category
     */
    private function buildFullDatabaseSchema(): string
    {
        // For general category, include common tables
        $commonTables = ['ORDERS', 'ORDER_DETAILS', 'POS_ORDER_ADDITIONAL_DTL'];
        return $this->buildCompactSchema($commonTables);
    }

    private function shouldGenerateReport(string $message, string $aiResponse): bool
    {
        $lowerMessage = strtolower($message);
        $lowerResponse = strtolower($aiResponse);

        return strpos($lowerMessage, 'report') !== false ||
               strpos($lowerMessage, 'query') !== false ||
               strpos($lowerMessage, 'data') !== false ||
               strpos($lowerResponse, 'generate_report:') !== false;
    }

    private function handleReportGeneration(string $message, string $aiResponse, $user, string $category): string
    {
        try {
            $reportData = $this->extractReportData($message, $aiResponse, $category);
            $reportId = $this->createReport($reportData, $user);

            $response = $aiResponse;
            if (strpos($aiResponse, 'GENERATE_REPORT:') === false) {
                $response .= "\n\nI've prepared a report for you. Click the button below to view it in a new tab.";
            }

            return $response . "\n\n[REPORT_BUTTON:" . $reportId . "]";

        } catch (\Exception $e) {
            Log::error('Report generation error: ' . $e->getMessage());
            return $aiResponse . "\n\nI encountered an error while generating the report. Please try again.";
        }
    }

    private function extractReportData(string $message, string $aiResponse, string $category): array
    {
        $reportData = [
            'title' => ucfirst($category) . ' Report',
            'description' => $message,
            'query' => '',
            'type' => $category
        ];

        // Extract SQL query from AI response if present
        if (strpos($aiResponse, 'GENERATE_REPORT:') !== false) {
            $parts = explode('GENERATE_REPORT:', $aiResponse);
            if (isset($parts[1])) {
                $query = trim($parts[1]);
                // Extract just the SQL query (remove any text after the query)
                $query = $this->cleanSQLQuery($query);
                $reportData['query'] = $query;
            }
        }

        // If no query found, generate a basic one
        if (empty($reportData['query'])) {
            $reportData['query'] = $this->generateBasicQuery($category);
        }

        return $reportData;
    }

    /**
     * Clean and extract SQL query from AI response
     */
    private function cleanSQLQuery(string $text): string
    {
        // Remove markdown code blocks if present
        $text = preg_replace('/```sql\s*/i', '', $text);
        $text = preg_replace('/```\s*$/i', '', $text);

        // Extract query up to semicolon or end of text
        if (preg_match('/^(.*?);/s', $text, $matches)) {
            return trim($matches[1]);
        }

        return trim($text);
    }

    /**
     * Generate basic query if AI doesn't provide one
     */
    private function generateBasicQuery(string $category): string
    {
        switch ($category) {
            case 'sales':
                return "SELECT COUNT(*) as total_orders FROM ORDERS WHERE ROWNUM <= 10";
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
            // Validate query for security
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

    /**
     * Validate SQL query for security
     * Only allows SELECT statements
     */
    private function validateQuery(string $query): void
    {
        $query = strtoupper(trim($query));

        // Security checks - prevent dangerous operations
        $dangerousKeywords = ['DROP', 'DELETE', 'UPDATE', 'INSERT', 'TRUNCATE', 'ALTER', 'CREATE', 'GRANT', 'REVOKE'];
        foreach ($dangerousKeywords as $keyword) {
            if (strpos($query, $keyword) !== false) {
                throw new \Exception('Query contains dangerous operations and cannot be executed');
            }
        }

        // Only allow SELECT queries
        if (strpos($query, 'SELECT') !== 0) {
            throw new \Exception('Only SELECT queries are allowed');
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

    /**
     * Clear cached database schemas
     * Use this after database structure changes
     */
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
                'message' => 'Schema cache cleared successfully',
                'cleared_categories' => $clearedCategories,
                'note' => 'New schema will be fetched on next query'
            ]);

        } catch (\Exception $e) {
            Log::error('Error clearing schema cache: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not clear schema cache'
            ], 500);
        }
    }

    /**
     * Get available categories for chatbot
     */
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

    /**
     * Get icon for category
     */
    private function getCategoryIcon(string $category): string
    {
        $icons = [
            'sales' => '💰',
            'general' => '🔍'
        ];

        return $icons[$category] ?? '📊';
    }

    /**
     * Preview schema for a category (for debugging)
     */
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
