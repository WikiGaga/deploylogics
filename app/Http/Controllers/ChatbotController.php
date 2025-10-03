<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle chatbot messages and generate responses
     */
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

            // Log the conversation
            $this->logConversation($user, $message, $conversationId);

            // Process the message and generate response
            $response = $this->generateResponse($message, $user);

            // Log the bot response
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

    /**
     * Generate response based on user message
     */
    private function generateResponse(string $message, $user): string
    {
        $lowerMessage = strtolower($message);

        // Sales report responses
        if (strpos($lowerMessage, 'sales') !== false || strpos($lowerMessage, 'revenue') !== false) {
            return $this->getSalesReportResponse();
        }

        // Inventory report responses
        if (strpos($lowerMessage, 'inventory') !== false || strpos($lowerMessage, 'stock') !== false) {
            return $this->getInventoryReportResponse();
        }

        // Financial report responses
        if (strpos($lowerMessage, 'financial') !== false ||
            strpos($lowerMessage, 'profit') !== false ||
            strpos($lowerMessage, 'loss') !== false) {
            return $this->getFinancialReportResponse();
        }

        // Custom report responses
        if (strpos($lowerMessage, 'custom') !== false || strpos($lowerMessage, 'specific') !== false) {
            return $this->getCustomReportResponse();
        }

        // Help responses
        if (strpos($lowerMessage, 'help') !== false || strpos($lowerMessage, 'assist') !== false) {
            return $this->getHelpResponse();
        }

        // Default response
        return $this->getDefaultResponse();
    }

    /**
     * Get sales report response
     */
    private function getSalesReportResponse(): string
    {
        return "I can help you generate sales reports! Here are the available options:\n\n📊 **Sales Report Types:**\n• Daily Sales Summary\n• Monthly Sales Analysis\n• Product-wise Sales Performance\n• Customer Sales History\n• Sales Trend Analysis\n\nWould you like me to generate a specific sales report? Please specify the date range and any filters you need.";
    }

    /**
     * Get inventory report response
     */
    private function getInventoryReportResponse(): string
    {
        return "I can assist with inventory reports! Here's what I can help you with:\n\n📦 **Inventory Report Options:**\n• Current Stock Levels\n• Low Stock Alerts\n• Inventory Valuation\n• Stock Movement History\n• Supplier Performance Analysis\n\nPlease let me know which inventory report you need and any specific criteria.";
    }

    /**
     * Get financial report response
     */
    private function getFinancialReportResponse(): string
    {
        return "I can help you with financial reporting! Available options include:\n\n💰 **Financial Report Types:**\n• Profit & Loss Statement\n• Balance Sheet Summary\n• Cash Flow Analysis\n• Budget vs Actual Comparison\n• Financial Performance Metrics\n\nWhat specific financial information do you need? Please specify the period and any particular metrics.";
    }

    /**
     * Get custom report response
     */
    private function getCustomReportResponse(): string
    {
        return "I can help you create custom reports! To assist you better, please provide:\n\n🔧 **Custom Report Requirements:**\n• What data do you need?\n• What time period?\n• Any specific filters or criteria?\n• Preferred format (PDF, Excel, etc.)\n\nThe more details you provide, the better I can help you generate the exact report you need.";
    }

    /**
     * Get help response
     */
    private function getHelpResponse(): string
    {
        return "I'm your Report Assistant! Here's how I can help you:\n\n🤖 **What I can do:**\n• Generate various types of reports\n• Analyze data and provide insights\n• Help with report customization\n• Answer questions about your data\n\n💡 **Quick Tips:**\n• Use the quick action buttons for common reports\n• Be specific about date ranges and filters\n• Ask for help if you're unsure about anything\n\nWhat would you like to work on today?";
    }

    /**
     * Get default response
     */
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

    /**
     * Log conversation for analytics
     */
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

    /**
     * Get conversation history
     */
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

    /**
     * Generate actual report data (placeholder for future implementation)
     */
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

            // This is a placeholder - implement actual report generation logic here
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

    /**
     * Process report generation (placeholder)
     */
    private function processReportGeneration(string $reportType, ?string $dateFrom, ?string $dateTo, array $filters): array
    {
        // This is where you would implement actual report generation logic
        // For now, return a placeholder response

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

    /**
     * Get chatbot analytics
     */
    public function getAnalytics(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // Get conversation statistics
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
