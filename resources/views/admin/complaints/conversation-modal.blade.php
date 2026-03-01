<!-- Complaint Conversation Modal -->
<div id="conversationModal" class="modal fade" tabindex="-1" aria-labelledby="conversationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="conversationModalLabel">Complaint Conversation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="conversationContainer" style="max-height: 400px; overflow-y: auto; margin-bottom: 15px;">
                    <!-- Messages will be loaded here -->
                    <div id="conversationLoader" class="text-center">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <div class="w-100">
                    <div class="input-group">
                        <textarea 
                            id="conversationReply" 
                            class="form-control" 
                            placeholder="Type your reply..." 
                            rows="3"
                            style="resize: vertical;">
                        </textarea>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="sendReplyBtn" class="btn btn-primary">
                            <i class="bi bi-send"></i> Send Reply
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Conversation modal functionality
let currentConversationComplaintId = null;
let currentConversationUserRole = 'admin'; // Default role

$('#conversationModal').on('show.bs.modal', function() {
    if (currentConversationComplaintId) {
        loadConversation(currentConversationComplaintId);
    }
});

function openConversationModal(complaintId, userRole = 'admin') {
    currentConversationComplaintId = complaintId;
    currentConversationUserRole = userRole || 'admin';
    $('#conversationReply').val('');
    bootstrap.Modal.getOrCreateInstance(document.getElementById('conversationModal')).show();
}

function loadConversation(complaintId) {
    const conversationContainer = $('#conversationContainer');
    conversationContainer.html(`
        <div class="text-center">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);

    $.ajax({
        url: `/admin/complaints/${complaintId}/conversation`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            let html = '';
            
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(function(message) {
                    const prefix = '[ADMIN_REPLY] ';
                    const rawMessage = String(message.message || '');
                    const isAdmin = rawMessage.startsWith(prefix);
                    const displayMessage = isAdmin ? rawMessage.slice(prefix.length).trim() : rawMessage;
                    const senderName = message.sender_name || (isAdmin ? 'Admin' : 'Consumer');
                    const timestamp = new Date(message.created_at).toLocaleString();
                    
                    html += `
                        <div class="mb-3 ${isAdmin ? 'text-end' : ''}">
                            <div class="d-inline-block ${isAdmin ? 'bg-primary text-white' : 'bg-light'} rounded-3 p-3" style="max-width: 70%;">
                                <small class="d-block fw-bold mb-1">${senderName}</small>
                                <p class="mb-1">${escapeHtml(displayMessage)}</p>
                                <small class="d-block text-muted" style="font-size: 0.75rem;">
                                    ${isAdmin ? '<i class="bi bi-check-all"></i>' : ''} ${timestamp}
                                </small>
                            </div>
                        </div>
                    `;
                });
            } else {
                html = '<p class="text-center text-muted">No messages yet.</p>';
            }
            
            conversationContainer.html(html);
            // Auto-scroll to bottom
            conversationContainer.scrollTop(conversationContainer[0].scrollHeight);
        },
        error: function() {
            conversationContainer.html(`
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-circle"></i> Failed to load conversation.
                </div>
            `);
        }
    });
}

$('#sendReplyBtn').on('click', function() {
    const message = $('#conversationReply').val().trim();
    
    if (!message) {
        Swal.fire({
            icon: 'warning',
            title: 'Empty Message',
            text: 'Please enter a message before sending.'
        });
        return;
    }
    
    if (!currentConversationComplaintId) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Complaint ID not found.'
        });
        return;
    }
    
    const btn = $(this);
    const originalText = btn.html();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Sending...');
    
    $.ajax({
        url: `/admin/complaints/${currentConversationComplaintId}/reply`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            message: message
        }),
        success: function(response) {
            $('#conversationReply').val('');
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Reply sent successfully!',
                timer: 1500
            });
            loadConversation(currentConversationComplaintId);
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to send reply. Please try again.'
            });
        },
        complete: function() {
            btn.prop('disabled', false).html(originalText);
        }
    });
});

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
