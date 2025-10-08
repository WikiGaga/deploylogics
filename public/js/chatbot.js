class Chatbot {
    constructor() {
        this.isOpen = false;
        this.isTyping = false;
        this.conversationHistory = [];
        this.conversationId = null;
        this.currentCategory = 'general';
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
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        content = content.replace(urlRegex, '<a href="$1" target="_blank" style="color: #667eea;">$1</a>');

        const reportButtonRegex = /\[REPORT_BUTTON:([^\]]+)\]/g;
        content = content.replace(reportButtonRegex, (match, reportId) => {
            return `<button class="report-button" data-report-id="${reportId}" onclick="window.chatbot.openReport('${reportId}')">
                        <i class="fas fa-chart-bar"></i> View Report
                    </button>`;
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
        if (confirm('Are you sure you want to clear the conversation history?')) {
            this.conversationHistory = [];
            const messagesContainer = document.getElementById('chatbotMessages');
            messagesContainer.innerHTML = `
                <div class="welcome-message">
                    <h4>Welcome to Report Assistant!</h4>
                    <p>I'm here to help you with reporting and data analysis. How can I assist you today?</p>
                </div>
            `;
            this.saveConversationHistory();
        }
    }

    async openReport(reportId) {
        try {
            const response = await fetch(`/chatbot/report/${reportId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                this.generateReportPage(data.report);
            } else {
                alert('Error loading report: ' + data.message);
            }

        } catch (error) {
            console.error('Report loading error:', error);
            alert('Error loading report. Please try again.');
        }
    }

    generateReportPage(report) {
        const reportWindow = window.open('', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');

        const html = `
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>${report.title}</title>
                <style>
                    body { font-family: 'Roboto', sans-serif; margin: 0; padding: 20px; background: #f8f9fa; }
                    .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
                    .header h1 { margin: 0; font-size: 24px; }
                    .header p { margin: 5px 0 0 0; opacity: 0.9; }
                    .report-container { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                    th { background: #f8f9fa; font-weight: 600; }
                    .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
                    .stat-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
                    .stat-value { font-size: 24px; font-weight: bold; color: #667eea; }
                    .stat-label { color: #666; margin-top: 5px; }
                    .no-data { text-align: center; padding: 40px; color: #666; }
                    .loading { text-align: center; padding: 40px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>${report.title}</h1>
                    <p>Generated on ${new Date(report.generated_at).toLocaleString()}</p>
                </div>

                <div class="report-container">
                    <h3>Report Description</h3>
                    <p>${report.description}</p>

                    ${this.generateReportContent(report)}
                </div>
            </body>
            </html>
        `;

        reportWindow.document.write(html);
        reportWindow.document.close();
    }

    generateReportContent(report) {
        if (!report.data || report.data.length === 0) {
            return '<div class="no-data">No data available for this report.</div>';
        }

        if (report.data.length === 1 && report.data[0].error) {
            return `<div class="no-data">Error: ${report.data[0].message}</div>`;
        }

        const headers = Object.keys(report.data[0]);
        const rows = report.data;

        let tableHtml = '<table><thead><tr>';
        headers.forEach(header => {
            tableHtml += `<th>${header.replace(/_/g, ' ').toUpperCase()}</th>`;
        });
        tableHtml += '</tr></thead><tbody>';

        rows.forEach(row => {
            tableHtml += '<tr>';
            headers.forEach(header => {
                const value = row[header];
                tableHtml += `<td>${value !== null ? value : '-'}</td>`;
            });
            tableHtml += '</tr>';
        });
        tableHtml += '</tbody></table>';

        let statsHtml = '';
        if (rows.length > 0) {
            const numericColumns = headers.filter(header => {
                return rows.some(row => !isNaN(parseFloat(row[header])) && isFinite(row[header]));
            });

            if (numericColumns.length > 0) {
                statsHtml = '<div class="stats">';
                numericColumns.forEach(column => {
                    const values = rows.map(row => parseFloat(row[column])).filter(v => !isNaN(v));
                    if (values.length > 0) {
                        const sum = values.reduce((a, b) => a + b, 0);
                        const avg = sum / values.length;
                        const max = Math.max(...values);
                        const min = Math.min(...values);

                        statsHtml += `
                            <div class="stat-card">
                                <div class="stat-value">${sum.toLocaleString()}</div>
                                <div class="stat-label">Total ${column.replace(/_/g, ' ')}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">${avg.toFixed(2)}</div>
                                <div class="stat-label">Average ${column.replace(/_/g, ' ')}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">${max.toLocaleString()}</div>
                                <div class="stat-label">Max ${column.replace(/_/g, ' ')}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">${min.toLocaleString()}</div>
                                <div class="stat-label">Min ${column.replace(/_/g, ' ')}</div>
                            </div>
                        `;
                    }
                });
                statsHtml += '</div>';
            }
        }

        return statsHtml + tableHtml;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    window.chatbot = new Chatbot();
});

window.Chatbot = Chatbot;
