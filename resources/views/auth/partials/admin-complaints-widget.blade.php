@php
    $complaintConversations = $complaintConversations ?? collect();
    $totalComplaints = (int) ($totalComplaints ?? 0);
@endphp

<style>
    :root {
        --chat-primary: #1f6ff3;
        --chat-thread-bg: #dfe4ee;
        --chat-bubble-bg: #f8f9fc;
        --chat-bubble-border: #d0d9ea;
        --chat-meta: #6a7a97;
        --chat-send: #e53950;
        --chat-send-hover: #d02d44;
    }

    .floating-complaints-btn {
        position: fixed;
        right: 1.25rem;
        bottom: 1.25rem;
        width: 56px;
        height: 56px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        z-index: 1060;
        box-shadow: 0 10px 24px rgba(13, 110, 253, 0.35);
    }

    .floating-complaints-count {
        position: absolute;
        top: -6px;
        right: -8px;
        min-width: 22px;
        height: 22px;
        border-radius: 999px;
        padding: 0 6px;
        background: #dc3545;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        line-height: 22px;
        text-align: center;
        border: 2px solid #fff;
    }

    #complaintsModal .modal-dialog {
        position: fixed;
        right: 1rem;
        bottom: 5.2rem;
        margin: 0;
        width: min(360px, calc(100vw - 1rem));
        max-width: min(360px, calc(100vw - 1rem));
        height: min(78vh, 660px);
    }

    #complaintsModal .modal-content {
        height: 100%;
        border-radius: 24px;
        border: 1px solid #d3dced;
        overflow: hidden;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.28);
    }

    #complaintsModal .modal-body {
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .complaints-conversation-list {
        max-height: none;
        min-height: 0;
        flex: 1 1 auto;
        overflow-y: auto;
        padding-right: 4px;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        scrollbar-width: thin;
        scrollbar-color: #9ca3af #edf1f6;
    }

    .complaints-conversation-list::-webkit-scrollbar,
    .admin-complaint-thread::-webkit-scrollbar {
        width: 9px;
    }

    .complaints-conversation-list::-webkit-scrollbar-track,
    .admin-complaint-thread::-webkit-scrollbar-track {
        background: #edf1f6;
        border-radius: 999px;
    }

    .complaints-conversation-list::-webkit-scrollbar-thumb,
    .admin-complaint-thread::-webkit-scrollbar-thumb {
        background: #9ca3af;
        border-radius: 999px;
        border: 2px solid #edf1f6;
    }

    .complaints-conversation-list::-webkit-scrollbar-thumb:hover,
    .admin-complaint-thread::-webkit-scrollbar-thumb:hover {
        background: #7b8794;
    }

    .complaint-conversation-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
    }

    .consumer-online-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.76rem;
        font-weight: 600;
        color: #9ca3af;
    }

    .consumer-online-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #adb5bd;
    }

    .consumer-online-indicator.is-online {
        color: #198754;
    }

    .consumer-online-indicator.is-online .consumer-online-dot {
        background: #20c997;
        box-shadow: 0 0 0 3px rgba(32, 201, 151, 0.2);
    }

    .complaint-conversation-preview {
        margin: 0.6rem 0;
        color: #4b5563;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .complaint-conversation-last-activity {
        color: #6b7280 !important;
        font-size: 0.8rem;
    }

    .open-complaint-chat-btn {
        border: 0;
        border-radius: 6px;
        padding: 0.34rem 0.72rem;
        font-weight: 700;
        background: #dc3545;
        color: #fff;
    }

    .open-complaint-chat-btn:hover,
    .open-complaint-chat-btn:focus {
        background: #bf2f3e;
        color: #fff;
    }

    .complaint-chat-modal .modal-dialog {
        position: fixed;
        right: 1rem;
        bottom: 5.2rem;
        margin: 0;
        width: min(420px, calc(100vw - 1rem));
        max-width: min(420px, calc(100vw - 1rem));
        height: min(76vh, 640px);
    }

    .complaint-chat-modal .modal-content {
        height: 100%;
        border-radius: 16px;
        border: 1px solid #d6deee;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(18, 35, 73, 0.24);
    }

    .complaint-chat-modal .modal-header {
        background: var(--chat-primary);
        color: #fff;
        border-bottom: 0;
        padding: 0.9rem 1rem;
    }

    .complaint-chat-modal .modal-header .modal-title {
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .complaint-chat-modal .modal-header p {
        color: rgba(255, 255, 255, 0.78) !important;
        font-size: 0.84rem;
    }

    .complaint-chat-modal .modal-header .btn-close {
        opacity: 0.95;
    }

    .complaint-chat-modal .delete-conversation-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.65);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .complaint-chat-modal .delete-conversation-btn:hover {
        background: rgba(255, 255, 255, 0.12);
    }

    .admin-complaint-thread {
        max-height: none;
        min-height: 220px;
        height: 100%;
        overflow-y: auto;
        background: var(--chat-thread-bg);
        padding: 0.95rem;
        scrollbar-width: thin;
        scrollbar-color: #9ca3af #edf1f6;
    }

    .admin-chat-row {
        display: flex;
        margin-bottom: 0.8rem;
    }

    .admin-chat-row.is-admin {
        justify-content: flex-end;
    }

    .admin-chat-row.is-consumer {
        justify-content: flex-start;
    }

    .admin-chat-bubble {
        max-width: 92%;
        background: var(--chat-bubble-bg);
        border: 1px solid var(--chat-bubble-border);
        border-radius: 14px;
        padding: 0.68rem 0.84rem;
        box-shadow: 0 2px 7px rgba(15, 23, 42, 0.05);
    }

    .admin-chat-row.is-admin .admin-chat-bubble {
        background: #f7f9fd;
        border-color: #cdd8eb;
    }

    .admin-chat-meta {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
        color: var(--chat-meta);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .admin-chat-message {
        margin: 0;
        white-space: pre-wrap;
        word-break: break-word;
        color: #2a3447;
        font-size: 1.14rem;
        line-height: 1.35;
    }

    .complaint-chat-modal .modal-footer {
        display: block;
        background: #fff;
        border-top: 1px solid #e5eaf4;
        padding: 0.95rem 1rem 1rem;
    }

    .complaint-chat-modal .js-admin-reply-form {
        display: block !important;
        gap: 0 !important;
    }

    .complaint-chat-modal .js-admin-reply-form label {
        color: #3a4963;
        font-size: 1.08rem;
        font-weight: 700;
        margin-bottom: 0.55rem !important;
    }

    .admin-reply-controls {
        display: flex;
        gap: 0.55rem;
        align-items: stretch;
    }

    .admin-reply-controls textarea {
        border-radius: 12px;
        border: 2px solid #2b73ef;
        resize: none;
        min-height: 76px;
        padding: 0.65rem 0.75rem;
        font-size: 1rem;
        line-height: 1.28;
    }

    .admin-reply-controls textarea:focus {
        border-color: #2b73ef;
        box-shadow: 0 0 0 0.2rem rgba(43, 115, 239, 0.2);
    }

    .admin-reply-send-btn {
        min-width: 136px;
        border: 0;
        border-radius: 10px;
        background: var(--chat-send);
        color: #fff;
        font-size: 1rem;
        font-weight: 700;
        padding: 0.7rem 1rem;
    }

    .admin-reply-send-btn:hover,
    .admin-reply-send-btn:focus {
        background: var(--chat-send-hover);
        color: #fff;
    }

    @media (max-width: 480px) {
        #complaintsModal .modal-dialog {
            right: 0.45rem;
            bottom: 5rem;
            width: calc(100vw - 0.9rem);
            max-width: calc(100vw - 0.9rem);
            height: min(74vh, 620px);
        }

        .complaint-chat-modal .modal-dialog {
            right: 0.45rem;
            bottom: 5rem;
            width: calc(100vw - 0.9rem);
            max-width: calc(100vw - 0.9rem);
            height: min(74vh, 620px);
        }

        .admin-reply-controls {
            flex-direction: column;
        }

        .admin-reply-send-btn {
            width: 100%;
        }
    }

    #complaintAttachmentFrame {
        width: 100%;
        min-height: min(75vh, 720px);
        border: 0;
        background: #fff;
    }
</style>

<button
    type="button"
    class="btn btn-primary floating-complaints-btn"
    data-bs-toggle="modal"
    data-bs-target="#complaintsModal"
    title="View Complaints"
    aria-label="View Complaints">
    <i class="bi bi-chat-left-text fs-5"></i>
    <span id="floatingComplaintsCount" class="floating-complaints-count {{ $totalComplaints > 0 ? '' : 'd-none' }}">{{ $totalComplaints > 99 ? '99+' : $totalComplaints }}</span>
</button>

<div id="complaintsModal" class="modal fade" tabindex="-1" aria-labelledby="complaintsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 id="complaintsModalLabel" class="modal-title">
                    <i class="bi bi-chat-left-text me-2"></i>Consumer Complaints
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted mb-0">Open a conversation and reply to the consumer in chat view.</p>
                    <span id="totalComplaintsModal" class="badge bg-danger-subtle text-danger">Total messages: {{ $totalComplaints }}</span>
                </div>
                <div id="complaintConversationsList" class="complaints-conversation-list">
                    @forelse ($complaintConversations as $conversation)
                        <div class="complaint-conversation-card" data-consumer-id="{{ $conversation['consumer_id'] }}">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="mb-1">{{ $conversation['consumer_name'] }}</h6>
                                        <span class="consumer-online-indicator js-consumer-online-indicator" data-consumer-id="{{ $conversation['consumer_id'] }}" aria-label="Consumer is offline">
                                            <span class="consumer-online-dot"></span>
                                            <span class="js-consumer-online-label">Offline</span>
                                        </span>
                                    </div>
                                    <small class="text-muted">Meter No: {{ $conversation['meter_no'] }}</small>
                                </div>
                                <span class="badge text-bg-primary-subtle text-primary js-conversation-count">
                                    {{ $conversation['messages']->count() }} {{ $conversation['messages']->count() === 1 ? 'message' : 'messages' }}
                                </span>
                            </div>
                            <p class="complaint-conversation-preview js-conversation-preview">{{ \Illuminate\Support\Str::limit($conversation['last_message'], 160) }}</p>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <small class="complaint-conversation-last-activity js-conversation-last-activity" data-last-iso="{{ optional($conversation['last_message_at'])->toIso8601String() }}">
                                    Last activity:
                                    {{ optional($conversation['last_message_at'])->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'No messages yet' }}
                                </small>
                                <button
                                    type="button"
                                    class="btn btn-sm open-complaint-chat-btn"
                                    data-chat-target="complaintChatModal{{ $conversation['consumer_id'] }}">
                                    <i class="bi bi-chat-dots me-1"></i>Open Chat
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">No consumer complaints found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@foreach ($complaintConversations as $conversation)
    <div id="complaintChatModal{{ $conversation['consumer_id'] }}" class="modal fade complaint-chat-modal" data-consumer-id="{{ $conversation['consumer_id'] }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <div>
                        <h5 class="modal-title mb-0">
                            <i class="bi bi-chat-left-text me-2"></i>{{ $conversation['consumer_name'] }}
                        </h5>
                        <p class="mb-0 text-white-50 small">Meter No: {{ $conversation['meter_no'] }}</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <form
                            action="{{ route('admin.complaints.destroy-conversation', $conversation['consumer_id']) }}"
                            method="POST"
                            class="js-delete-conversation-form"
                            data-confirm-message="Delete this consumer complaint conversation? This cannot be undone.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm delete-conversation-btn" title="Delete Conversation" aria-label="Delete Conversation">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0">
                    <div class="admin-complaint-thread js-admin-complaint-thread" data-consumer-id="{{ $conversation['consumer_id'] }}">
                        @foreach ($conversation['messages'] as $message)
                            <div class="admin-chat-row js-admin-chat-message {{ $message->isAdminReply() ? 'is-admin' : 'is-consumer' }}" data-message-id="{{ $message->id }}">
                                <div class="admin-chat-bubble">
                                    <div class="admin-chat-meta">
                                        <span>{{ $message->isAdminReply() ? 'Admin' : 'Consumer' }}</span>
                                        <span>{{ optional($message->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A') }}</span>
                                    </div>
                                    <p class="admin-chat-message">{{ $message->plainMessage() }}</p>
                                    @if (!$message->isAdminReply() && $message->attachment_path)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary mt-2 view-complaint-attachment-btn"
                                            data-attachment-url="{{ route('admin.complaints.attachment', $message->id) }}">
                                            <i class="bi bi-paperclip me-1"></i>View Attachment
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('admin.complaints.reply') }}" method="POST" class="w-100 d-flex flex-column gap-2 js-admin-reply-form">
                        @csrf
                        <input type="hidden" name="consumer_id" value="{{ $conversation['consumer_id'] }}">
                        <label class="form-label mb-0">Reply as Admin</label>
                        <div class="admin-reply-controls">
                            <textarea name="message" class="form-control" rows="2" placeholder="Type your reply here..." required>{{ (string) old('consumer_id') === (string) $conversation['consumer_id'] ? old('message') : '' }}</textarea>
                            <button type="submit" class="btn admin-reply-send-btn">
                                <i class="bi bi-send me-1"></i>Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<div id="complaintAttachmentModal" class="modal fade" tabindex="-1" aria-labelledby="complaintAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 id="complaintAttachmentModalLabel" class="modal-title">
                    <i class="bi bi-paperclip me-2"></i>Complaint Attachment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="complaintAttachmentFrame" title="Complaint Attachment Preview"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        if (typeof window === 'undefined' || typeof document === 'undefined' || typeof window.jQuery === 'undefined') {
            return;
        }

        $(function () {
            const complaintsModalEl = document.getElementById('complaintsModal');
            const complaintAttachmentModalEl = document.getElementById('complaintAttachmentModal');
            const complaintAttachmentFrame = document.getElementById('complaintAttachmentFrame');
            const complaintConversationsListEl = document.getElementById('complaintConversationsList');
            const totalComplaintsModalEl = document.getElementById('totalComplaintsModal');
            const floatingComplaintsCountEl = document.getElementById('floatingComplaintsCount');
            const adminComplaintReplyUrl = @json(route('admin.complaints.reply'));
            const adminComplaintDeleteBaseUrl = @json(url('/admin/complaints/conversation'));
            const adminComplaintAttachmentBaseUrl = @json(url('/admin/complaints'));
            const adminComplaintOnlineStatusesUrl = @json(route('admin.complaints.online-statuses'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            let activeComplaintModalEl = null;
            let totalComplaintMessages = Number(@json($totalComplaints)) || 0;
            let isOnlineStatusesSyncBusy = false;
            const complaintTimeZone = 'Asia/Manila';
            const pageLoadedAtMs = Date.now();
            const seenComplaintMessageIds = new Set(
                Array.from(document.querySelectorAll('.js-admin-chat-message[data-message-id]'))
                    .map((node) => Number(node.getAttribute('data-message-id')))
                    .filter((value) => Number.isFinite(value))
            );

            function setComplaintTotals(value) {
                totalComplaintMessages = Math.max(0, Number(value) || 0);
                if (totalComplaintsModalEl) {
                    totalComplaintsModalEl.textContent = 'Total messages: ' + totalComplaintMessages;
                }
                if (!floatingComplaintsCountEl) {
                    return;
                }
                if (totalComplaintMessages > 0) {
                    floatingComplaintsCountEl.textContent = totalComplaintMessages > 99 ? '99+' : String(totalComplaintMessages);
                    floatingComplaintsCountEl.classList.remove('d-none');
                } else {
                    floatingComplaintsCountEl.textContent = '0';
                    floatingComplaintsCountEl.classList.add('d-none');
                }
            }

            setComplaintTotals(totalComplaintMessages);

            function escapeHtml(value) {
                return String(value || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function shortenText(value, maxLength) {
                const text = String(value || '');
                if (text.length <= maxLength) {
                    return text;
                }
                return text.slice(0, Math.max(0, maxLength - 3)) + '...';
            }

            function formatDateTime(isoString) {
                if (!isoString) {
                    return 'Just now';
                }
                const parsed = new Date(isoString);
                if (Number.isNaN(parsed.getTime())) {
                    return 'Just now';
                }
                return parsed.toLocaleString('en-US', {
                    month: 'short',
                    day: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true,
                    timeZone: complaintTimeZone
                }).replace(',', '');
            }

            function setConversationOnlineState(consumerId, isOnline) {
                if (!complaintConversationsListEl) {
                    return;
                }

                const cardEl = complaintConversationsListEl.querySelector(`.complaint-conversation-card[data-consumer-id="${consumerId}"]`);
                if (!cardEl) {
                    return;
                }

                const indicatorEl = cardEl.querySelector('.js-consumer-online-indicator');
                const labelEl = indicatorEl ? indicatorEl.querySelector('.js-consumer-online-label') : null;
                if (!indicatorEl || !labelEl) {
                    return;
                }

                indicatorEl.classList.toggle('is-online', Boolean(isOnline));
                labelEl.textContent = isOnline ? 'Online' : 'Offline';
                indicatorEl.setAttribute('aria-label', isOnline ? 'Consumer is online' : 'Consumer is offline');
            }

            async function syncConversationOnlineStatuses() {
                if (isOnlineStatusesSyncBusy || !adminComplaintOnlineStatusesUrl || !complaintConversationsListEl) {
                    return;
                }

                const consumerIds = Array.from(complaintConversationsListEl.querySelectorAll('.complaint-conversation-card[data-consumer-id]'))
                    .map((node) => Number(node.getAttribute('data-consumer-id')))
                    .filter((value) => Number.isFinite(value) && value > 0);

                if (consumerIds.length === 0) {
                    return;
                }

                isOnlineStatusesSyncBusy = true;

                try {
                    const query = new URLSearchParams();
                    consumerIds.forEach((consumerId) => query.append('ids[]', String(consumerId)));

                    const response = await fetch(`${adminComplaintOnlineStatusesUrl}?${query.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    const statuses = payload && typeof payload.statuses === 'object' && payload.statuses !== null
                        ? payload.statuses
                        : {};

                    consumerIds.forEach((consumerId) => {
                        setConversationOnlineState(consumerId, Boolean(statuses[String(consumerId)]));
                    });
                } catch (error) {
                    // Silent fail to keep complaint UI responsive.
                } finally {
                    isOnlineStatusesSyncBusy = false;
                }
            }

            function upsertConversationCard(item) {
                if (!complaintConversationsListEl || !item || !item.consumer_id) {
                    return null;
                }

                const consumerId = Number(item.consumer_id);
                if (!Number.isFinite(consumerId) || consumerId <= 0) {
                    return null;
                }

                const activityLabel = 'Last activity: ' + formatDateTime(item.created_at);
                let cardEl = complaintConversationsListEl.querySelector(`.complaint-conversation-card[data-consumer-id="${consumerId}"]`);

                if (!cardEl) {
                    const placeholder = complaintConversationsListEl.querySelector('.text-center.text-muted.py-4');
                    if (placeholder) {
                        placeholder.remove();
                    }

                    cardEl = document.createElement('div');
                    cardEl.className = 'complaint-conversation-card';
                    cardEl.setAttribute('data-consumer-id', String(consumerId));
                    cardEl.innerHTML = `
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="mb-1">${escapeHtml(item.consumer_name || 'Unknown Consumer')}</h6>
                                    <span class="consumer-online-indicator js-consumer-online-indicator" data-consumer-id="${consumerId}" aria-label="Consumer is offline">
                                        <span class="consumer-online-dot"></span>
                                        <span class="js-consumer-online-label">Offline</span>
                                    </span>
                                </div>
                                <small class="text-muted">Meter No: ${escapeHtml(item.meter_no || 'N/A')}</small>
                            </div>
                            <span class="badge text-bg-primary-subtle text-primary js-conversation-count">1 message</span>
                        </div>
                        <p class="complaint-conversation-preview js-conversation-preview">${escapeHtml(shortenText(item.message || '', 160))}</p>
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <small class="complaint-conversation-last-activity js-conversation-last-activity" data-last-iso="${escapeHtml(item.created_at || '')}">${escapeHtml(activityLabel)}</small>
                            <button
                                type="button"
                                class="btn btn-sm open-complaint-chat-btn"
                                data-chat-target="complaintChatModal${consumerId}">
                                <i class="bi bi-chat-dots me-1"></i>Open Chat
                            </button>
                        </div>
                    `;
                    complaintConversationsListEl.prepend(cardEl);
                    return cardEl;
                }

                const countEl = cardEl.querySelector('.js-conversation-count');
                if (countEl) {
                    const currentCount = Number.parseInt(String(countEl.textContent).replace(/\D/g, ''), 10) || 0;
                    const nextCount = currentCount + 1;
                    countEl.textContent = `${nextCount} ${nextCount === 1 ? 'message' : 'messages'}`;
                }

                const previewEl = cardEl.querySelector('.js-conversation-preview');
                if (previewEl) {
                    previewEl.textContent = shortenText(item.message || '', 160);
                }

                const lastActivityEl = cardEl.querySelector('.js-conversation-last-activity');
                if (lastActivityEl) {
                    lastActivityEl.textContent = activityLabel;
                    lastActivityEl.setAttribute('data-last-iso', item.created_at || '');
                }

                complaintConversationsListEl.prepend(cardEl);
                return cardEl;
            }

            function ensureConversationModal(item) {
                if (!item || !item.consumer_id) {
                    return null;
                }

                const consumerId = Number(item.consumer_id);
                if (!Number.isFinite(consumerId) || consumerId <= 0) {
                    return null;
                }

                const modalId = `complaintChatModal${consumerId}`;
                let modalEl = document.getElementById(modalId);
                if (modalEl) {
                    return modalEl;
                }

                const modalHtml = `
                    <div id="${modalId}" class="modal fade complaint-chat-modal" data-consumer-id="${consumerId}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <div>
                                        <h5 class="modal-title mb-0">
                                            <i class="bi bi-chat-left-text me-2"></i>${escapeHtml(item.consumer_name || 'Unknown Consumer')}
                                        </h5>
                                        <p class="mb-0 text-white-50 small">Meter No: ${escapeHtml(item.meter_no || 'N/A')}</p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <form
                                            action="${adminComplaintDeleteBaseUrl}/${consumerId}"
                                            method="POST"
                                            class="js-delete-conversation-form"
                                            data-confirm-message="Delete this consumer complaint conversation? This cannot be undone.">
                                            <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm delete-conversation-btn" title="Delete Conversation" aria-label="Delete Conversation">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                </div>
                                <div class="modal-body p-0">
                                    <div class="admin-complaint-thread js-admin-complaint-thread" data-consumer-id="${consumerId}"></div>
                                </div>
                                <div class="modal-footer">
                                    <form action="${adminComplaintReplyUrl}" method="POST" class="w-100 d-flex flex-column gap-2 js-admin-reply-form">
                                        <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                                        <input type="hidden" name="consumer_id" value="${consumerId}">
                                        <label class="form-label mb-0">Reply as Admin</label>
                                        <div class="admin-reply-controls">
                                            <textarea name="message" class="form-control" rows="2" placeholder="Type your reply here..." required></textarea>
                                            <button type="submit" class="btn admin-reply-send-btn">
                                                <i class="bi bi-send me-1"></i>Send Reply
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                document.body.insertAdjacentHTML('beforeend', modalHtml);
                return document.getElementById(modalId);
            }

            function appendIncomingComplaintMessage(item) {
                if (!item || !item.consumer_id || !item.id) {
                    return;
                }

                upsertConversationCard(item);
                const modalEl = ensureConversationModal(item);
                const threadEl = modalEl ? modalEl.querySelector('.js-admin-complaint-thread') : null;
                if (!threadEl) {
                    return;
                }

                if (threadEl.querySelector(`.js-admin-chat-message[data-message-id="${item.id}"]`)) {
                    return;
                }

                const hasAttachment = Boolean(item.has_attachment);
                const attachmentBtn = hasAttachment
                    ? `<button type="button" class="btn btn-sm btn-outline-secondary mt-2 view-complaint-attachment-btn" data-attachment-url="${adminComplaintAttachmentBaseUrl}/${item.id}/attachment"><i class="bi bi-paperclip me-1"></i>View Attachment</button>`
                    : '';

                const rowHtml = `
                    <div class="admin-chat-row js-admin-chat-message is-consumer" data-message-id="${item.id}">
                        <div class="admin-chat-bubble">
                            <div class="admin-chat-meta">
                                <span>Consumer</span>
                                <span>${escapeHtml(formatDateTime(item.created_at))}</span>
                            </div>
                            <p class="admin-chat-message">${escapeHtml(item.message || '')}</p>
                            ${attachmentBtn}
                        </div>
                    </div>
                `;

                threadEl.insertAdjacentHTML('beforeend', rowHtml);
                if (modalEl.classList.contains('show')) {
                    threadEl.scrollTop = threadEl.scrollHeight;
                }
            }

            function appendAdminReplyMessage(item) {
                if (!item || !item.consumer_id || !item.id) {
                    return;
                }

                upsertConversationCard(item);
                const modalEl = ensureConversationModal(item);
                const threadEl = modalEl ? modalEl.querySelector('.js-admin-complaint-thread') : null;
                if (!threadEl) {
                    return;
                }

                if (threadEl.querySelector(`.js-admin-chat-message[data-message-id="${item.id}"]`)) {
                    return;
                }

                const rowHtml = `
                    <div class="admin-chat-row js-admin-chat-message is-admin" data-message-id="${item.id}">
                        <div class="admin-chat-bubble">
                            <div class="admin-chat-meta">
                                <span>Admin</span>
                                <span>${escapeHtml(formatDateTime(item.created_at))}</span>
                            </div>
                            <p class="admin-chat-message">${escapeHtml(item.message || '')}</p>
                        </div>
                    </div>
                `;

                threadEl.insertAdjacentHTML('beforeend', rowHtml);
                if (modalEl.classList.contains('show')) {
                    threadEl.scrollTop = threadEl.scrollHeight;
                }
            }

            syncConversationOnlineStatuses();
            setInterval(syncConversationOnlineStatuses, 15000);

            $(document).on('click', '.open-complaint-chat-btn', function () {
                const chatTarget = ($(this).data('chat-target') || '').toString();
                const chatModalEl = chatTarget ? document.getElementById(chatTarget) : null;
                if (!chatModalEl || !complaintsModalEl) {
                    return;
                }

                bootstrap.Modal.getOrCreateInstance(complaintsModalEl).hide();
                activeComplaintModalEl = complaintsModalEl;
                bootstrap.Modal.getOrCreateInstance(chatModalEl).show();
            });

            $('#complaintsModal').on('shown.bs.modal', function () {
                syncConversationOnlineStatuses();
            });

            $(document).on('shown.bs.modal', '.complaint-chat-modal', function () {
                const threadEl = this.querySelector('.js-admin-complaint-thread');
                if (threadEl) {
                    threadEl.scrollTop = threadEl.scrollHeight;
                }
            });

            $(document).on('submit', '.js-delete-conversation-form', function (event) {
                const confirmMessage = (this.getAttribute('data-confirm-message') || 'Delete this conversation?').trim();
                event.preventDefault();

                const formEl = this;
                const submitForm = function () {
                    HTMLFormElement.prototype.submit.call(formEl);
                };

                if (typeof Swal !== 'undefined' && Swal && typeof Swal.fire === 'function') {
                    Swal.fire({
                        title: 'Delete Conversation?',
                        text: confirmMessage,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete',
                        cancelButtonText: 'Cancel'
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            submitForm();
                        }
                    });
                    return;
                }

                if (window.confirm(confirmMessage)) {
                    submitForm();
                }
            });

            $(document).on('submit', '.js-admin-reply-form', async function (event) {
                event.preventDefault();

                const formEl = this;
                const consumerId = Number($(formEl).find('input[name="consumer_id"]').val());
                const messageInputEl = formEl.querySelector('textarea[name="message"]');
                const submitButtonEl = formEl.querySelector('button[type="submit"]');
                const messageValue = (messageInputEl && messageInputEl.value ? messageInputEl.value : '').toString().trim();

                if (!Number.isFinite(consumerId) || consumerId <= 0 || messageValue.length === 0) {
                    return;
                }

                if (submitButtonEl) {
                    submitButtonEl.disabled = true;
                }

                try {
                    const response = await fetch(adminComplaintReplyUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            consumer_id: consumerId,
                            message: messageValue
                        })
                    });

                    const payload = await response.json().catch(function () { return {}; });
                    if (!response.ok || payload.success !== true) {
                        throw new Error('Unable to send reply right now.');
                    }

                    const complaint = payload.complaint || {};
                    if (complaint && Number.isFinite(Number(complaint.id))) {
                        seenComplaintMessageIds.add(Number(complaint.id));
                        appendAdminReplyMessage(complaint);
                    } else {
                        const threadEl = formEl.closest('.modal-content')?.querySelector('.js-admin-complaint-thread');
                        if (threadEl) {
                            threadEl.insertAdjacentHTML('beforeend', '' +
                                '<div class="admin-chat-row is-admin">' +
                                    '<div class="admin-chat-bubble">' +
                                        '<div class="admin-chat-meta">' +
                                            '<span>Admin</span>' +
                                            '<span>' + escapeHtml(formatDateTime(new Date().toISOString())) + '</span>' +
                                        '</div>' +
                                        '<p class="admin-chat-message">' + escapeHtml(messageValue) + '</p>' +
                                    '</div>' +
                                '</div>');
                            threadEl.scrollTop = threadEl.scrollHeight;
                        }
                    }

                    setComplaintTotals(totalComplaintMessages + 1);
                    if (messageInputEl) {
                        messageInputEl.value = '';
                        messageInputEl.focus();
                    }
                } catch (error) {
                    alert('Unable to send reply right now.');
                } finally {
                    if (submitButtonEl) {
                        submitButtonEl.disabled = false;
                    }
                }
            });

            $(document).on('click', '.view-complaint-attachment-btn', function () {
                const attachmentUrl = $(this).data('attachment-url');
                if (!attachmentUrl || !complaintAttachmentModalEl || !complaintAttachmentFrame) {
                    return;
                }

                const previewUrl = new URL(attachmentUrl, window.location.origin);
                previewUrl.searchParams.set('preview', '1');
                complaintAttachmentFrame.src = previewUrl.toString();
                activeComplaintModalEl = $(this).closest('.modal.show').get(0) || null;

                if (activeComplaintModalEl) {
                    bootstrap.Modal.getOrCreateInstance(activeComplaintModalEl).hide();
                }

                bootstrap.Modal.getOrCreateInstance(complaintAttachmentModalEl).show();
            });

            $('#complaintAttachmentModal').on('hidden.bs.modal', function () {
                if (complaintAttachmentFrame) {
                    complaintAttachmentFrame.src = 'about:blank';
                }
                if (activeComplaintModalEl) {
                    bootstrap.Modal.getOrCreateInstance(activeComplaintModalEl).show();
                    activeComplaintModalEl = null;
                }
            });

            window.addEventListener('complaint-notifications:update', function (event) {
                const detail = event && event.detail ? event.detail : {};
                if (detail.role !== 'admin') {
                    return;
                }

                const notifications = Array.isArray(detail.notifications) ? detail.notifications : [];
                notifications.forEach(function (item) {
                    const messageId = Number(item && item.id);
                    if (!Number.isFinite(messageId) || seenComplaintMessageIds.has(messageId)) {
                        return;
                    }

                    seenComplaintMessageIds.add(messageId);
                    appendIncomingComplaintMessage(item);

                    const createdAtMs = Date.parse(item.created_at || '');
                    if (Number.isFinite(createdAtMs) && createdAtMs > pageLoadedAtMs) {
                        setComplaintTotals(totalComplaintMessages + 1);
                    }
                });

                syncConversationOnlineStatuses();
            });
        });
    })();
</script>
