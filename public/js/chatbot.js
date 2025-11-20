class Chatbot {
    constructor() {
        this.isOpen = false;
        this.isTyping = false;
        this.conversationHistory = [];
        this.conversationId = null;
        this.currentCategory = 'sales';
        this.categories = [];
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadCategories();
        this.loadConversationHistory();
    }

    bindEvents() {
        document.getElementById('chatbotToggle').addEventListener('click', () => {
            this.toggleChatbot();
        });

        document.getElementById('chatbotClose').addEventListener('click', () => {
            this.closeChatbot();
        });

        document.getElementById('chatbotClear').addEventListener('click', () => {
            this.clearConversation();
        });

        document.getElementById('chatbotSend').addEventListener('click', () => {
            this.sendMessage();
        });

        document.getElementById('chatbotInput').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.sendMessage();
            }
        });

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

        this.addMessage(message, 'user');
        input.value = '';
        this.showTypingIndicator();
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

        this.conversationHistory.push({
            content: content,
            sender: sender,
            timestamp: time
        });

        this.saveConversationHistory();
    }

    formatMessage(content) {
        const excelRegex = /\[DOWNLOAD_EXCEL:([^\]]+)\]/g;
        content = content.replace(excelRegex, (match, downloadUrl) => {
            return `<a href="${downloadUrl}" target="_blank" class="excel-download-button" download>
                        <i class="fas fa-file-excel"></i> Download Excel Report
                    </a>`;
        });

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
            this.showTypingIndicator();

            const response = await fetch('/chatbot/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: message,
                    conversation_id: this.conversationId || this.generateConversationId(),
                    category: this.currentCategory
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

    async loadCategories() {
        try {
            const response = await fetch('/chatbot/categories', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                this.categories = data.categories;
                this.renderCategoryTabs();
            }

        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    renderCategoryTabs() {
        const tabsContainer = document.getElementById('chatbotCategoryTabs');
        if (!tabsContainer) return;

        let tabsHtml = '';
        this.categories.forEach(category => {
            const isActive = category.id === this.currentCategory ? 'active' : '';
            tabsHtml += `
                <button class="category-tab ${isActive}" data-category="${category.id}" title="${category.description}">
                    <span class="tab-icon">${category.icon}</span>
                    <span class="tab-label">${category.name}</span>
                </button>
            `;
        });

        tabsContainer.innerHTML = tabsHtml;

        // Bind tab click events
        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.addEventListener('click', (e) => {
                this.switchCategory(tab.dataset.category);
            });
        });
    }

    switchCategory(category) {
        this.currentCategory = category;

        // Update active tab
        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.classList.toggle('active', tab.dataset.category === category);
        });

        // Clear input and show category message
        const categoryInfo = this.categories.find(c => c.id === category);
        if (categoryInfo) {
            this.addSystemMessage(`Switched to ${categoryInfo.name} category. ${categoryInfo.description}`);
        }
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
                const recentMessages = this.conversationHistory.slice(-10);
                this.conversationHistory = recentMessages;

                recentMessages.forEach(msg => {
                    this.addMessage(msg.content, msg.sender, msg.timestamp);
                });
            }
        } catch (error) {
            console.warn('Could not load conversation history:', error);
            this.conversationHistory = [];
        }
    }

    addSystemMessage(content) {
        this.addMessage(content, 'bot');
    }

    clearConversation() {
        if (confirm('Are you sure you want to clear the conversation history and delete all generated reports?')) {
            fetch('/chatbot/clear-conversation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    conversation_id: this.conversationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.conversationHistory = [];
                    this.conversationId = null;
                    const messagesContainer = document.getElementById('chatbotMessages');
                    messagesContainer.innerHTML = `
                        <div class="welcome-message">
                            <h4>Welcome to Report Assistant!</h4>
                            <p>I'm here to help you with reporting and data analysis. How can I assist you today?</p>
                        </div>
                    `;
                    this.saveConversationHistory();

                    if (data.deleted_reports > 0) {
                        this.addMessage(`✅ Cleared conversation and deleted ${data.deleted_reports} report(s).`, 'bot');
                    }
                } else {
                    console.error('Failed to clear conversation:', data.message);
                    alert('Failed to clear conversation. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error clearing conversation:', error);
                alert('An error occurred while clearing the conversation.');
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    window.chatbot = new Chatbot();
});

window.Chatbot = Chatbot;
