<!-- GDPR Chatbot Sidebar -->
<div class="chatbot-toggle-btn">
    <button class="btn btn-primary btn-icon rounded-circle" id="chatbotToggle">
        <i data-feather="message-circle"></i>
    </button>
</div>

<div class="chatbot-sidebar" id="chatbotSidebar">
    <div class="chatbot-header">
        <div class="d-flex align-items-center">
            <i data-feather="shield" class="mr-1"></i>
            <h4 class="mb-0">GDPR Assistant</h4>
        </div>
        <button class="btn btn-icon btn-sm" id="chatbotClose">
            <i data-feather="x"></i>
        </button>
    </div>

    <div class="chatbot-messages" id="chatbotMessages">
        <div class="chat-message bot-message">
            <div class="message-avatar">
                <i data-feather="cpu"></i>
            </div>
            <div class="message-content">
                <p>Hello! I'm your GDPR assistant. I can help you with questions about data protection regulations and your organization's compliance data. How can I assist you today?</p>
            </div>
        </div>
    </div>

    <div class="chatbot-input">
        <form id="chatbotForm">
            <div class="input-group">
                <input type="text"
                       class="form-control"
                       id="chatbotInput"
                       placeholder="Ask me about GDPR..."
                       autocomplete="off">
                <button class="btn btn-primary" type="submit" id="chatbotSend">
                    <i data-feather="send"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="chatbot-loader" id="chatbotLoader" style="display: none;">
        <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span class="ml-1">Thinking...</span>
    </div>
</div>

<style>
    .chatbot-toggle-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1050;
    }

    .chatbot-toggle-btn .btn {
        width: 60px;
        height: 60px;
        box-shadow: 0 4px 12px rgba(115, 103, 240, 0.4);
    }

    .chatbot-sidebar {
        position: fixed;
        right: -450px;
        top: 0;
        height: 100vh;
        width: 420px;
        background: white;
        box-shadow: -4px 0 12px rgba(0, 0, 0, 0.1);
        z-index: 1049;
        transition: right 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .chatbot-sidebar.active {
        right: 0;
    }

    .chatbot-header {
        padding: 20px;
        border-bottom: 1px solid #ebe9f1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(118deg, #7367f0, rgba(115, 103, 240, 0.7));
        color: white;
    }

    .chatbot-header h4 {
        color: white;
    }

    .chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f8f8f8;
    }

    .chat-message {
        display: flex;
        margin-bottom: 20px;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .message-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .bot-message .message-avatar {
        background: linear-gradient(118deg, #7367f0, rgba(115, 103, 240, 0.7));
        color: white;
    }

    .user-message {
        flex-direction: row-reverse;
    }

    .user-message .message-avatar {
        background: #ea5455;
        color: white;
        margin-right: 0;
        margin-left: 12px;
    }

    .message-content {
        background: white;
        padding: 12px 16px;
        border-radius: 8px;
        max-width: 75%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .user-message .message-content {
        background: #7367f0;
        color: white;
    }

    .message-content p {
        margin: 0;
        line-height: 1.5;
    }

    .chatbot-input {
        padding: 20px;
        border-top: 1px solid #ebe9f1;
        background: white;
    }

    .chatbot-input .form-control {
        border-radius: 24px;
        padding: 12px 20px;
    }

    .chatbot-input .btn {
        border-radius: 50%;
        width: 48px;
        height: 48px;
        margin-left: 8px;
    }

    .chatbot-loader {
        position: absolute;
        bottom: 90px;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        padding: 12px 20px;
        border-radius: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
    }

    /* Dark theme support */
    .dark-layout .chatbot-sidebar {
        background: #283046;
    }

    .dark-layout .chatbot-header {
        border-bottom-color: #3b4253;
    }

    .dark-layout .chatbot-messages {
        background: #161d31;
    }

    .dark-layout .message-content {
        background: #283046;
        color: #b4b7bd;
    }

    .dark-layout .user-message .message-content {
        background: #7367f0;
        color: white;
    }

    .dark-layout .chatbot-input {
        background: #283046;
        border-top-color: #3b4253;
    }

    .dark-layout .chatbot-loader {
        background: #283046;
        color: #b4b7bd;
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        .chatbot-sidebar {
            width: 100%;
            right: -100%;
        }
    }
</style>