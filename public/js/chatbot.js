/**
 * Chatbot JavaScript Functionality
 * Professional chatbot with reporting capabilities
 */

class Chatbot {
    constructor() {
        this.isOpen = false;
        this.isTyping = false;
        this.conversationHistory = [];
        this.conversationId = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadConversationHistory();
    }

    bindEvents() {
        // Toggle chatbot window
        document.getElementById('chatbotToggle').addEventListener('click', () => {
            this.toggleChatbot();
        });

        // Close chatbot window
        document.getElementById('chatbotClose').addEventListener('click', () => {
            this.closeChatbot();
        });

        // Send message on button click
        document.getElementById('chatbotSend').addEventListener('click', () => {
            this.sendMessage();
        });

        // Send message on Enter key
        document.getElementById('chatbotInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Quick action buttons
        document.querySelectorAll('.quick-action-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const action = e.target.getAttribute('data-action');
                this.handleQuickAction(action);
            });
        });

        // Close chatbot when clicking outside
        document.addEventListener('click', (e) => {
            const chatbotContainer = document.querySelector('.chatbot-container');
            const chatbotWindow = document.getElementById('chatbotWindow');

            if (this.isOpen &&
                !chatbotContainer.contains(e.target) &&
                chatbotWindow.classList.contains('active')) {
                this.closeChatbot();
            }
        });
    }

    toggleChatbot() {
        const chatbotWindow = document.getElementById('chatbotWindow');

        if (this.isOpen) {
            this.closeChatbot();
        } else {
            this.openChatbot();
        }
    }

    openChatbot() {
        const chatbotWindow = document.getElementById('chatbotWindow');
        const chatbotToggle = document.getElementById('chatbotToggle');

        chatbotWindow.classList.add('active');
        chatbotToggle.style.transform = 'scale(1.1)';
        this.isOpen = true;

        // Focus on input
        setTimeout(() => {
            document.getElementById('chatbotInput').focus();
        }, 300);
    }

    closeChatbot() {
        const chatbotWindow = document.getElementById('chatbotWindow');
        const chatbotToggle = document.getElementById('chatbotToggle');

        chatbotWindow.classList.remove('active');
        chatbotToggle.style.transform = 'scale(1)';
        this.isOpen = false;
    }

    sendMessage() {
        const input = document.getElementById('chatbotInput');
        const message = input.value.trim();

        if (!message) return;

        // Add user message to chat
        this.addMessage(message, 'user');

        // Clear input
        input.value = '';

        // Show typing indicator
        this.showTypingIndicator();

        // Process message
        this.processMessage(message);
    }

    addMessage(content, sender, timestamp = null) {
        const messagesContainer = document.getElementById('chatbotMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message ${sender}`;

        const time = timestamp || new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

        messageDiv.innerHTML = `
            <div class="message-bubble ${sender}">
                ${this.formatMessage(content)}
                <div class="message-time">${time}</div>
            </div>
        `;

        messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();

        // Store in conversation history
        this.conversationHistory.push({
            content: content,
            sender: sender,
            timestamp: time
        });

        this.saveConversationHistory();
    }

    formatMessage(content) {
        // Convert URLs to clickable links
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        content = content.replace(urlRegex, '<a href="$1" target="_blank" style="color: #667eea;">$1</a>');

        // Convert line breaks to HTML
        content = content.replace(/\n/g, '<br>');

        return content;
    }

    showTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        typingIndicator.classList.add('active');
        this.isTyping = true;
    }

    hideTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        typingIndicator.classList.remove('active');
        this.isTyping = false;
    }

    async processMessage(message) {
        try {
            // Show typing indicator
            this.showTypingIndicator();

            // Send message to backend
            const response = await fetch('/chatbot/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: message,
                    conversation_id: this.conversationId || this.generateConversationId()
                })
            });

            const data = await response.json();

            this.hideTypingIndicator();

            if (data.success) {
                this.addMessage(data.response, 'bot', data.timestamp);
                this.conversationId = data.conversation_id;
            } else {
                this.addMessage('Sorry, I encountered an error while processing your request. Please try again.', 'bot');
            }

        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('Sorry, I encountered an error while processing your request. Please try again.', 'bot');
            console.error('Chatbot error:', error);
        }
    }

    async generateResponse(message) {
        const lowerMessage = message.toLowerCase();

        // Report-related responses
        if (lowerMessage.includes('sales') || lowerMessage.includes('revenue')) {
            return this.getSalesReportResponse();
        }

        if (lowerMessage.includes('inventory') || lowerMessage.includes('stock')) {
            return this.getInventoryReportResponse();
        }

        if (lowerMessage.includes('financial') || lowerMessage.includes('profit') || lowerMessage.includes('loss')) {
            return this.getFinancialReportResponse();
        }

        if (lowerMessage.includes('custom') || lowerMessage.includes('specific')) {
            return this.getCustomReportResponse();
        }

        if (lowerMessage.includes('help') || lowerMessage.includes('assist')) {
            return this.getHelpResponse();
        }

        // Default response
        return this.getDefaultResponse();
    }

    getSalesReportResponse() {
        return `I can help you generate sales reports! Here are the available options:

📊 **Sales Report Types:**
• Daily Sales Summary
• Monthly Sales Analysis
• Product-wise Sales Performance
• Customer Sales History
• Sales Trend Analysis

Would you like me to generate a specific sales report? Please specify the date range and any filters you need.`;
    }

    getInventoryReportResponse() {
        return `I can assist with inventory reports! Here's what I can help you with:

📦 **Inventory Report Options:**
• Current Stock Levels
• Low Stock Alerts
• Inventory Valuation
• Stock Movement History
• Supplier Performance Analysis

Please let me know which inventory report you need and any specific criteria.`;
    }

    getFinancialReportResponse() {
        return `I can help you with financial reporting! Available options include:

💰 **Financial Report Types:**
• Profit & Loss Statement
• Balance Sheet Summary
• Cash Flow Analysis
• Budget vs Actual Comparison
• Financial Performance Metrics

What specific financial information do you need? Please specify the period and any particular metrics.`;
    }

    getCustomReportResponse() {
        return `I can help you create custom reports! To assist you better, please provide:

🔧 **Custom Report Requirements:**
• What data do you need?
• What time period?
• Any specific filters or criteria?
• Preferred format (PDF, Excel, etc.)

The more details you provide, the better I can help you generate the exact report you need.`;
    }

    getHelpResponse() {
        return `I'm your Report Assistant! Here's how I can help you:

🤖 **What I can do:**
• Generate various types of reports
• Analyze data and provide insights
• Help with report customization
• Answer questions about your data

💡 **Quick Tips:**
• Use the quick action buttons for common reports
• Be specific about date ranges and filters
• Ask for help if you're unsure about anything

What would you like to work on today?`;
    }

    getDefaultResponse() {
        const responses = [
            "I understand you're looking for help with reporting. Could you be more specific about what type of report or data analysis you need?",
            "I'm here to help with your reporting needs! What specific information are you looking for?",
            "Let me help you with that. What kind of report would you like to generate?",
            "I can assist you with various reporting tasks. Could you tell me more about what you need?"
        ];

        return responses[Math.floor(Math.random() * responses.length)];
    }

    handleQuickAction(action) {
        const actionMessages = {
            'sales-report': 'I can help you generate a sales report. What specific sales data do you need?',
            'inventory-report': 'I can assist with inventory reporting. What inventory information are you looking for?',
            'financial-report': 'I can help with financial reports. What financial data do you need?',
            'custom-report': 'I can help you create a custom report. What specific data and criteria do you need?'
        };

        const message = actionMessages[action] || 'How can I help you with that?';
        this.addMessage(message, 'bot');
    }

    scrollToBottom() {
        const messagesContainer = document.getElementById('chatbotMessages');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    generateConversationId() {
        return 'conv_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    saveConversationHistory() {
        try {
            localStorage.setItem('chatbot_conversation', JSON.stringify(this.conversationHistory));
        } catch (error) {
            console.warn('Could not save conversation history:', error);
        }
    }

    loadConversationHistory() {
        try {
            const saved = localStorage.getItem('chatbot_conversation');
            if (saved) {
                this.conversationHistory = JSON.parse(saved);
                // Only load last 10 messages to avoid clutter
                const recentMessages = this.conversationHistory.slice(-10);
                this.conversationHistory = recentMessages;

                // Display recent messages
                recentMessages.forEach(msg => {
                    this.addMessage(msg.content, msg.sender, msg.timestamp);
                });
            }
        } catch (error) {
            console.warn('Could not load conversation history:', error);
            this.conversationHistory = [];
        }
    }

    // Public method to add system messages
    addSystemMessage(content) {
        this.addMessage(content, 'bot');
    }

    // Public method to clear conversation
    clearConversation() {
        this.conversationHistory = [];
        const messagesContainer = document.getElementById('chatbotMessages');
        messagesContainer.innerHTML = `
            <div class="welcome-message">
                <h4>Welcome to Report Assistant!</h4>
                <p>I'm here to help you with reporting and data analysis. How can I assist you today?</p>

                <div class="quick-actions">
                    <button class="quick-action-btn" data-action="sales-report">Sales Report</button>
                    <button class="quick-action-btn" data-action="inventory-report">Inventory Report</button>
                    <button class="quick-action-btn" data-action="financial-report">Financial Report</button>
                    <button class="quick-action-btn" data-action="custom-report">Custom Report</button>
                </div>
            </div>
        `;

        // Re-bind quick action events
        document.querySelectorAll('.quick-action-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const action = e.target.getAttribute('data-action');
                this.handleQuickAction(action);
            });
        });

        this.saveConversationHistory();
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.chatbot = new Chatbot();
});

// Export for global access
window.Chatbot = Chatbot;
