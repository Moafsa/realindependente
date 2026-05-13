/**
 * Portal Communication Page JavaScript
 */

(function() {
    'use strict';

    let currentRecipientId = null;
    let currentRecipientName = null;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeMessageForm();
        loadAnnouncements();
        loadNotifications();
    });

    /**
     * Show specific tab
     */
    window.showTab = function(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });

        // Show selected tab content
        const content = document.getElementById(`content-${tabName}`);
        if (content) {
            content.classList.remove('hidden');
        }

        // Activate selected tab button
        const button = document.getElementById(`tab-${tabName}`);
        if (button) {
            button.classList.add('active', 'border-blue-500', 'text-blue-600');
            button.classList.remove('border-transparent', 'text-gray-500');
        }
    };

    /**
     * Select conversation
     */
    window.selectConversation = function(recipientId, recipientName) {
        currentRecipientId = recipientId;
        currentRecipientName = recipientName;

        // Update chat title
        const chatTitle = document.getElementById('chat-title');
        if (chatTitle) {
            chatTitle.textContent = recipientName;
        }

        // Show chat input
        const chatInput = document.getElementById('chat-input-container');
        if (chatInput) {
            chatInput.classList.remove('hidden');
        }

        // Set recipient ID
        const recipientInput = document.getElementById('recipient-id');
        if (recipientInput) {
            recipientInput.value = recipientId;
        }

        // Load messages
        loadMessages(recipientId);
    };

    /**
     * Initialize message form
     */
    function initializeMessageForm() {
        const form = document.getElementById('message-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                sendMessage();
            });
        }
    }

    /**
     * Send message
     */
    function sendMessage() {
        const form = document.getElementById('message-form');
        const formData = new FormData(form);
        const messageInput = document.getElementById('message-input');
        const message = messageInput.value.trim();

        if (!message || !currentRecipientId) return;

        fetch('{{ route("portal.communication.store") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add message to chat
                addMessageToChat(message, true);
                messageInput.value = '';
                
                // Scroll to bottom
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            } else {
                alert('Erro ao enviar mensagem: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Erro ao enviar mensagem. Tente novamente.');
        });
    }

    /**
     * Load messages for a conversation
     */
    function loadMessages(recipientId) {
        const chatMessages = document.getElementById('chat-messages');
        if (!chatMessages) return;

        // TODO: Load messages from API when Message model is created
        // Use a safe way to set static HTML
        chatMessages.innerHTML = '';
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'text-center text-gray-500 py-12';
        const loadingP = document.createElement('p');
        loadingP.textContent = 'Carregando mensagens...';
        loadingDiv.appendChild(loadingP);
        chatMessages.appendChild(loadingDiv);

        // For now, show empty state
        setTimeout(() => {
            chatMessages.innerHTML = '';
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'text-center text-gray-500 py-12';
            const emptyP = document.createElement('p');
            emptyP.textContent = 'Nenhuma mensagem ainda. Comece a conversar!';
            emptyDiv.appendChild(emptyP);
            chatMessages.appendChild(emptyDiv);
        }, 500);
    }

    /**
     * Add message to chat
     */
    function addMessageToChat(message, isOwn) {
        const chatMessages = document.getElementById('chat-messages');
        if (!chatMessages) return;

        // Clear empty state
        if (chatMessages.querySelector('.text-center')) {
            chatMessages.innerHTML = '';
        }

        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isOwn ? 'justify-end' : 'justify-start'} mb-4`;
        const innerWrapper = document.createElement('div');
        innerWrapper.className = `max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${isOwn ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-900'}`;
        
        const messageP = document.createElement('p');
        messageP.className = 'text-sm';
        messageP.textContent = message;
        
        const timeP = document.createElement('p');
        timeP.className = `text-xs ${isOwn ? 'text-blue-100' : 'text-gray-500'} mt-1`;
        timeP.textContent = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        
        innerWrapper.appendChild(messageP);
        innerWrapper.appendChild(timeP);
        messageDiv.appendChild(innerWrapper);

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    /**
     * Load announcements
     */
    function loadAnnouncements() {
        // TODO: Load from API when Announcement model is created
        // For now, announcements are static in the view
    }

    /**
     * Load notifications
     */
    function loadNotifications() {
        // TODO: Load from API when Notification model is created
        // For now, notifications are static in the view
    }

    /**
     * Mark notification as read
     */
    window.markAsRead = function(notificationId) {
        fetch(`{{ route('portal.communication.read', ':id') }}`.replace(':id', notificationId), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove unread indicator
                const notification = event.target.closest('.p-6');
                if (notification) {
                    const indicator = notification.querySelector('.bg-blue-600');
                    if (indicator) {
                        indicator.remove();
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    };

    /**
     * Mark all notifications as read
     */
    window.markAllAsRead = function() {
        // TODO: Implement when Notification model is created
        alert('Funcionalidade será implementada em breve.');
    };
})();

