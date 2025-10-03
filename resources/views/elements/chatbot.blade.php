<div class="chatbot-container">
    <button class="chatbot-toggle" id="chatbotToggle">
        <i class="fas fa-comments"></i>
    </button>

    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <div>
                <h3>Report Assistant</h3>
                <div class="chatbot-status">
                    <span class="chatbot-status-dot"></span>
                    <span>Online</span>
                </div>
            </div>
            <div class="chatbot-header-actions">
                <button class="chatbot-clear" id="chatbotClear" title="Clear Conversation">
                    <i class="fas fa-trash"></i>
                </button>
                <button class="chatbot-close" id="chatbotClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="chatbot-messages" id="chatbotMessages">
            <div class="welcome-message">
                <h4>Welcome to Report Assistant!</h4>
                <p>I'm here to help you with reporting and data analysis. How can I assist you today?</p>
            </div>
        </div>

        <div class="typing-indicator" id="typingIndicator">
            <div class="typing-dots">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>

        <div class="chatbot-input-area">
            <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Type your message..." maxlength="500">
            <button class="chatbot-send" id="chatbotSend">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>
