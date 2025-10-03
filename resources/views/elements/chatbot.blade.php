<!-- Chatbot Container -->
<div class="chatbot-container">
    <!-- Chatbot Toggle Button -->
    <button class="chatbot-toggle" id="chatbotToggle">
        <i class="fas fa-comments"></i>
    </button>

    <!-- Chatbot Window -->
    <div class="chatbot-window" id="chatbotWindow">
        <!-- Chatbot Header -->
        <div class="chatbot-header">
            <div>
                <h3>Report Assistant</h3>
                <div class="chatbot-status">
                    <span class="chatbot-status-dot"></span>
                    <span>Online</span>
                </div>
            </div>
            <button class="chatbot-close" id="chatbotClose">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Chatbot Messages Area -->
        <div class="chatbot-messages" id="chatbotMessages">
            <!-- Welcome Message -->
            <div class="welcome-message">
                <h4>Welcome to Report Assistant!</h4>
                <p>I'm here to help you with reporting and data analysis. How can I assist you today?</p>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <button class="quick-action-btn" data-action="sales-report">Sales Report</button>
                    <button class="quick-action-btn" data-action="inventory-report">Inventory Report</button>
                    <button class="quick-action-btn" data-action="financial-report">Financial Report</button>
                    <button class="quick-action-btn" data-action="custom-report">Custom Report</button>
                </div>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div class="typing-indicator" id="typingIndicator">
            <div class="typing-dots">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>

        <!-- Chatbot Input Area -->
        <div class="chatbot-input-area">
            <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Type your message..." maxlength="500">
            <button class="chatbot-send" id="chatbotSend">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>
