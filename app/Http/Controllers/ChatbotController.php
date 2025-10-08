<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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
                'conversation_id' => 'nullable|string'
            ]);

            $message = $request->input('message');
            $conversationId = $request->input('conversation_id', uniqid());
            $user = Auth::user();

            $this->logConversation($user, $message, $conversationId);

            $response = $this->generateAIResponse($message, $user);

            $this->logConversation($user, $response, $conversationId, 'bot');

            return response()->json([
                'success' => true,
                'response' => $response,
                'conversation_id' => $conversationId,
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

    private function generateAIResponse(string $message, $user): string
    {
        try {
            $systemPrompt = $this->getSystemPrompt();

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
                'temperature' => 0.7,
            ])->json();

            dd($response);
            $aiResponse = $response['choices'][0]['message']['content'] ?? 'Sorry, I could not process your request.';

            if ($this->shouldGenerateReport($message, $aiResponse)) {
                return $this->handleReportGeneration($message, $aiResponse, $user);
            }

            return $aiResponse;

        } catch (\Exception $e) {
            Log::error('OpenAI API error: ' . $e->getMessage());
            return $this->generateResponse($message, $user);
        }
    }

    private function generateResponse(string $message, $user): string
    {
        $lowerMessage = strtolower($message);

        if (strpos($lowerMessage, 'sales') !== false || strpos($lowerMessage, 'revenue') !== false) {
            return $this->getSalesReportResponse();
        }

        if (strpos($lowerMessage, 'inventory') !== false || strpos($lowerMessage, 'stock') !== false) {
            return $this->getInventoryReportResponse();
        }

        if (strpos($lowerMessage, 'financial') !== false ||
            strpos($lowerMessage, 'profit') !== false ||
            strpos($lowerMessage, 'loss') !== false) {
            return $this->getFinancialReportResponse();
        }

        if (strpos($lowerMessage, 'custom') !== false || strpos($lowerMessage, 'specific') !== false) {
            return $this->getCustomReportResponse();
        }

        if (strpos($lowerMessage, 'help') !== false || strpos($lowerMessage, 'assist') !== false) {
            return $this->getHelpResponse();
        }

        return $this->getDefaultResponse();
    }

    private function getSalesReportResponse(): string
    {
        return "I can help you generate sales reports! Here are the available options:\n\n📊 **Sales Report Types:**\n• Daily Sales Summary\n• Monthly Sales Analysis\n• Product-wise Sales Performance\n• Customer Sales History\n• Sales Trend Analysis\n\nWould you like me to generate a specific sales report? Please specify the date range and any filters you need.";
    }

    private function getInventoryReportResponse(): string
    {
        return "I can assist with inventory reports! Here's what I can help you with:\n\n📦 **Inventory Report Options:**\n• Current Stock Levels\n• Low Stock Alerts\n• Inventory Valuation\n• Stock Movement History\n• Supplier Performance Analysis\n\nPlease let me know which inventory report you need and any specific criteria.";
    }

    private function getFinancialReportResponse(): string
    {
        return "I can help you with financial reporting! Available options include:\n\n💰 **Financial Report Types:**\n• Profit & Loss Statement\n• Balance Sheet Summary\n• Cash Flow Analysis\n• Budget vs Actual Comparison\n• Financial Performance Metrics\n\nWhat specific financial information do you need? Please specify the period and any particular metrics.";
    }

    private function getCustomReportResponse(): string
    {
        return "I can help you create custom reports! To assist you better, please provide:\n\n🔧 **Custom Report Requirements:**\n• What data do you need?\n• What time period?\n• Any specific filters or criteria?\n• Preferred format (PDF, Excel, etc.)\n\nThe more details you provide, the better I can help you generate the exact report you need.";
    }

    private function getHelpResponse(): string
    {
        return "I'm your Report Assistant! Here's how I can help you:\n\n🤖 **What I can do:**\n• Generate various types of reports\n• Analyze data and provide insights\n• Help with report customization\n• Answer questions about your data\n\n💡 **Quick Tips:**\n• Use the quick action buttons for common reports\n• Be specific about date ranges and filters\n• Ask for help if you're unsure about anything\n\nWhat would you like to work on today?";
    }

    private function getDefaultResponse(): string
    {
        $responses = [
            "I understand you're looking for help with reporting. Could you be more specific about what type of report or data analysis you need?",
            "I'm here to help with your reporting needs! What specific information are you looking for?",
            "Let me help you with that. What kind of report would you like to generate?",
            "I can assist you with various reporting tasks. Could you tell me more about what you need?"
        ];

        return $responses[array_rand($responses)];
    }

    private function getSystemPrompt(): string
    {
        return "You are a professional Oracle database expert and report generation assistant. Your role is to:

1. Understand user requirements for reports and data analysis
2. Generate appropriate Oracle SQL queries based on user needs
3. Provide clear explanations of what data will be retrieved
4. Suggest report formats and visualizations when appropriate

When a user asks for a report, you should:
- Ask clarifying questions about date ranges, filters, and specific data needed
- Generate Oracle SQL queries that can be executed
- Explain what the query will return
- Suggest how the data should be presented

Always be helpful, professional, and focus on Oracle database queries and report generation. If you need to generate a report, respond with 'GENERATE_REPORT:' followed by the Oracle query and report details.";
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

    private function handleReportGeneration(string $message, string $aiResponse, $user): string
    {
        try {
            $reportData = $this->extractReportData($message, $aiResponse);
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

    private function extractReportData(string $message, string $aiResponse): array
    {
        $lowerMessage = strtolower($message);

        $reportData = [
            'title' => 'Generated Report',
            'description' => $message,
            'query' => '',
            'type' => 'custom'
        ];

        if (strpos($lowerMessage, 'sales') !== false) {
            $reportData['type'] = 'sales';
            $reportData['title'] = 'Sales Report';
            $reportData['query'] = $this->generateSalesQuery($message);
        } elseif (strpos($lowerMessage, 'inventory') !== false || strpos($lowerMessage, 'stock') !== false) {
            $reportData['type'] = 'inventory';
            $reportData['title'] = 'Inventory Report';
            $reportData['query'] = $this->generateInventoryQuery($message);
        } elseif (strpos($lowerMessage, 'financial') !== false) {
            $reportData['type'] = 'financial';
            $reportData['title'] = 'Financial Report';
            $reportData['query'] = $this->generateFinancialQuery($message);
        } else {
            $reportData['query'] = $this->generateCustomQuery($message);
        }

        return $reportData;
    }

    private function generateSalesQuery(string $message): string
    {
        return "SELECT
                    DATE_TRUNC('month', sale_date) as month,
                    COUNT(*) as total_sales,
                    SUM(amount) as total_revenue,
                    AVG(amount) as average_sale
                FROM sales
                WHERE sale_date >= TRUNC(SYSDATE, 'MM') - INTERVAL '12' MONTH
                GROUP BY DATE_TRUNC('month', sale_date)
                ORDER BY month DESC";
    }

    private function generateInventoryQuery(string $message): string
    {
        return "SELECT
                    p.product_name,
                    p.product_code,
                    i.current_stock,
                    i.min_stock_level,
                    i.max_stock_level,
                    CASE
                        WHEN i.current_stock <= i.min_stock_level THEN 'Low Stock'
                        WHEN i.current_stock >= i.max_stock_level THEN 'Overstocked'
                        ELSE 'Normal'
                    END as stock_status
                FROM products p
                JOIN inventory i ON p.id = i.product_id
                WHERE i.current_stock IS NOT NULL
                ORDER BY i.current_stock ASC";
    }

    private function generateFinancialQuery(string $message): string
    {
        return "SELECT
                    EXTRACT(YEAR FROM transaction_date) as year,
                    EXTRACT(MONTH FROM transaction_date) as month,
                    SUM(CASE WHEN transaction_type = 'income' THEN amount ELSE 0 END) as total_income,
                    SUM(CASE WHEN transaction_type = 'expense' THEN amount ELSE 0 END) as total_expenses,
                    SUM(CASE WHEN transaction_type = 'income' THEN amount ELSE -amount END) as net_profit
                FROM financial_transactions
                WHERE transaction_date >= TRUNC(SYSDATE, 'YYYY')
                GROUP BY EXTRACT(YEAR FROM transaction_date), EXTRACT(MONTH FROM transaction_date)
                ORDER BY year DESC, month DESC";
    }

    private function generateCustomQuery(string $message): string
    {
        return "SELECT
                    'Custom Report' as report_type,
                    COUNT(*) as record_count,
                    SYSDATE as generated_at
                FROM dual";
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

            $data = $this->executeQuery($report->query);

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

    private function logConversation($user, string $message, string $conversationId, string $sender = 'user'): void
    {
        try {
            DB::table('chatbot_conversations')->insert([
                'user_id' => $user->id,
                'conversation_id' => $conversationId,
                'message' => $message,
                'sender' => $sender,
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
}
