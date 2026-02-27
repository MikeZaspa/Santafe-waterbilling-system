<!-- Admin Complaint Conversation Modal -->
<div class="modal fade" id="adminComplaintConversationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title mb-0" id="adminComplaintTitle">Complaint</h5>
                    <small id="adminComplaintConsumerInfo" class="text-light"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="complaint-thread" id="adminComplaintMessagesThread" style="max-height: 60vh; overflow-y: auto; padding: 1rem;">
                    <!-- Messages will be loaded here -->
                </div>
            </div>
            <div class="modal-footer complaint-composer border-top">
                <form id="adminComplaintReplyForm" enctype="multipart/form-data" class="w-100">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="row g-2 align-items-end">
                        <div class="col-12">
                            <label class="form-label mb-1">Your Reply</label>
                            <textarea name="message" class="form-control" rows="2" placeholder="Type your reply..." required></textarea>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label mb-1">Attachment (optional)</label>
                            <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <small class="text-muted d-block">Max 5MB - JPG, PNG, PDF, DOC, DOCX</small>
                        </div>
                        <div class="col-md-5 d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-send me-1"></i> Send Reply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let currentAdminComplaintId = null;

    // Function to load complaint conversation for admin
    window.loadAdminComplaintConversation = async function(complaintId) {
        try {
            const response = await fetch(`/admin/complaints/${complaintId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) throw new Error('Failed to load complaint');
            
            const data = await response.json();
            if (!data.success) throw new Error(data.message || 'Failed to load complaint');

            currentAdminComplaintId = complaintId;
            
            // Update modal title and consumer info
            document.getElementById('adminComplaintTitle').textContent = data.complaint.subject || 'Complaint #' + complaintId;
            const consumerInfo = `From: ${data.complaint.consumer.name} (${data.complaint.consumer.email})`;
            document.getElementById('adminComplaintConsumerInfo').textContent = consumerInfo;

            // Render messages
            renderAdminComplaintMessages(data.messages);

            // Update reply form action
            document.getElementById('adminComplaintReplyForm').action = `/admin/complaints/${complaintId}/reply`;

            // Set status value
            document.querySelector('#adminComplaintReplyForm select[name="status"]').value = data.complaint.status;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('adminComplaintConversationModal'));
            modal.show();

            // Scroll to bottom
            setTimeout(() => {
                const thread = document.getElementById('adminComplaintMessagesThread');
                thread.scrollTop = thread.scrollHeight;
            }, 100);
        } catch (error) {
            console.error('Error loading complaint:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to load complaint'
                });
            }
        }
    };

    // Render complaint messages for admin view
    function renderAdminComplaintMessages(messages) {
        const thread = document.getElementById('adminComplaintMessagesThread');
        if (!thread) return;

        thread.innerHTML = '';

        if (!Array.isArray(messages) || messages.length === 0) {
            thread.innerHTML = '<div class="text-center text-muted py-5">No messages yet</div>';
            return;
        }

        messages.forEach(msg => {
            const isAdmin = msg.sender_type === 'admin';
            const alignClass = isAdmin ? 'flex-end' : 'flex-start';
            const bubbleColor = isAdmin ? 'bg-info text-white' : 'bg-light text-dark';
            const borderRadius = isAdmin ? 'border-radius: 16px 16px 6px 16px;' : 'border-radius: 16px 16px 16px 6px;';

            const html = `
                <div class="complaint-row d-flex justify-content-${alignClass} mb-3">
                    <div class="complaint-bubble ${bubbleColor}" style="${borderRadius} padding: 0.85rem 0.95rem; max-width: 70%; box-shadow: 0 10px 24px rgba(0,0,0,0.1);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <strong style="font-size: 0.9rem;">${escapeHtml(msg.sender_name)}</strong>
                            <small class="ms-2" style="font-size: 0.8rem;">${msg.created_at}</small>
                        </div>
                        <p class="mb-2" style="white-space: pre-wrap; word-break: break-word;">${escapeHtml(msg.message)}</p>
                        ${msg.has_attachment ? `
                            <a href="/admin/complaints/${currentAdminComplaintId}/message/${msg.id}/attachment" 
                               class="btn btn-sm btn-outline-secondary" target="_blank" style="margin-top: 0.5rem;">
                                <i class="bi bi-download"></i> Download
                            </a>
                        ` : ''}
                    </div>
                </div>
            `;

            thread.insertAdjacentHTML('beforeend', html);
        });
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        };
        return String(text || '').replace(/[&<>"']/g, m => map[m]);
    }

    // Handle admin reply form submission
    document.addEventListener('DOMContentLoaded', function() {
        const replyForm = document.getElementById('adminComplaintReplyForm');
        if (replyForm) {
            replyForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    if (!response.ok) throw new Error('Failed to send reply');
                    
                    const data = await response.json();
                    if (!data.success) throw new Error(data.message || 'Failed to send reply');

                    // Clear form
                    this.reset();

                    // Reload conversation
                    await loadAdminComplaintConversation(currentAdminComplaintId);

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Reply sent successfully!',
                            timer: 2000
                        });
                    }
                } catch (error) {
                    console.error('Error sending reply:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to send reply'
                        });
                    }
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }
    });
</script>
