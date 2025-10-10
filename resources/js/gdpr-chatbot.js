/**
 * GDPR Chatbot Client
 * Handles communication with the Python LLM chatbot backend
 */

class GDPRChatbot {
    constructor() {
        this.sessionId = this.generateSessionId();
        this.organizationId = this.getOrganizationId();
        this.isInitialized = false;
        this.apiBaseUrl = '/api/chatbot'; // Laravel proxy endpoint

        this.initElements();
        this.bindEvents();
    }

    initElements() {
        this.toggleBtn = document.getElementById('chatbotToggle');
        this.closeBtn = document.getElementById('chatbotClose');
        this.sidebar = document.getElementById('chatbotSidebar');
        this.messagesContainer = document.getElementById('chatbotMessages');
        this.form = document.getElementById('chatbotForm');
        this.input = document.getElementById('chatbotInput');
        this.loader = document.getElementById('chatbotLoader');
    }

    bindEvents() {
        this.toggleBtn?.addEventListener('click', () => this.toggleSidebar());
        this.closeBtn?.addEventListener('click', () => this.closeSidebar());
        this.form?.addEventListener('submit', (e) => this.handleSubmit(e));

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.sidebar?.classList.contains('active')) {
                this.closeSidebar();
            }
        });
    }

    generateSessionId() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    getOrganizationId() {
        // Get organization ID from meta tag or data attribute
        const meta = document.querySelector('meta[name="organization-id"]');
        if (meta) return parseInt(meta.content);

        // Fallback: try to get from window object or default to 1
        return window.organizationId || 1;
    }

    async toggleSidebar() {
        if (!this.sidebar) return;

        if (this.sidebar.classList.contains('active')) {
            this.closeSidebar();
        } else {
            this.sidebar.classList.add('active');
            this.input?.focus();

            // Initialize session on first open
            if (!this.isInitialized) {
                await this.initializeSession();
            }
        }
    }

    closeSidebar() {
        this.sidebar?.classList.remove('active');
    }

    async initializeSession() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/init`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    session_id: this.sessionId,
                    organization_id: this.organizationId
                })
            });

            if (response.ok) {
                this.isInitialized = true;
                console.log('✅ Chatbot session initialized');
            } else {
                throw new Error('Failed to initialize chatbot session');
            }
        } catch (error) {
            console.error('❌ Chatbot initialization error:', error);
            this.addBotMessage('Sorry, I am currently unavailable. Please try again later.');
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        const message = this.input?.value.trim();
        if (!message) return;

        // Add user message to UI
        this.addUserMessage(message);
        this.input.value = '';

        // Show loader
        this.showLoader();

        try {
            // Send message to backend
            const response = await fetch(`${this.apiBaseUrl}/message`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    session_id: this.sessionId,
                    message: message
                })
            });

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            // Add bot response
            this.addBotMessage(data.answer, data.sources);

        } catch (error) {
            console.error('❌ Chat error:', error);
            this.addBotMessage('Sorry, I encountered an error processing your request. Please try again.');
        } finally {
            this.hideLoader();
        }
    }

    addUserMessage(text) {
        const messageEl = document.createElement('div');
        messageEl.className = 'chat-message user-message';
        messageEl.innerHTML = `
            <div class="message-avatar">
                <i data-feather="user"></i>
            </div>
            <div class="message-content">
                <p>${this.escapeHtml(text)}</p>
            </div>
        `;

        this.messagesContainer?.appendChild(messageEl);
        this.scrollToBottom();
        this.initFeatherIcons();
    }

    addBotMessage(text, sources = []) {
        const messageEl = document.createElement('div');
        messageEl.className = 'chat-message bot-message';

        let sourcesHtml = '';
        if (sources && sources.length > 0) {
            sourcesHtml = '<div class="message-sources mt-2 small text-muted">';
            sourcesHtml += '<strong>Sources:</strong><br>';
            sources.forEach((source, index) => {
                sourcesHtml += `<div class="source-item">${index + 1}. ${this.escapeHtml(source.substring(0, 100))}...</div>`;
            });
            sourcesHtml += '</div>';
        }

        messageEl.innerHTML = `
            <div class="message-avatar">
                <i data-feather="cpu"></i>
            </div>
            <div class="message-content">
                <p>${this.escapeHtml(text)}</p>
                ${sourcesHtml}
            </div>
        `;

        this.messagesContainer?.appendChild(messageEl);
        this.scrollToBottom();
        this.initFeatherIcons();
    }

    showLoader() {
        this.loader?.style.setProperty('display', 'flex');
    }

    hideLoader() {
        this.loader?.style.setProperty('display', 'none');
    }

    scrollToBottom() {
        if (this.messagesContainer) {
            this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    initFeatherIcons() {
        // Reinitialize Feather icons for new elements
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.gdprChatbot = new GDPRChatbot();
});