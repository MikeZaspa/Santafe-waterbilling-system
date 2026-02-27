<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Santa Fe Water Billing System - Complaints</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="image/santalogo.png">
    <style>
        :root {
            --primary-color: #0d6efd;
            --sidebar-bg: #f8f9fa;
            --overlay-color: rgba(7, 7, 7, 0.1);
            --header-height: 70px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1050;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 1.5rem;
            color: black;
            text-align: center;
        }

        .sidebar-menu .nav-link {
            color: gray;
            padding: 0.75rem 1.5rem;
            margin: 0 0.5rem;
            border-radius: 6px;
            transition: all 0.3s;
            position: relative;
        }

        .sidebar-menu .nav-link:hover {
            color: white;
            background: blue;
            transform: translateX(5px);
        }

        .sidebar-menu .nav-link.active {
            color: white;
            background: blue;
        }

        .sidebar-menu .nav-link i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .main-content {
            min-height: 100vh;
            transition: all 0.3s ease;
            padding: 0;
            width: 100%;
            margin-left: 0;
        }

         .header {
            height: var(--header-height);
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 1040;
            background: white;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
        }

        .content-wrapper {
            padding: 20px;
        }

        .card-box {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .login-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }

        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--overlay-color);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .mobile-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 280px;
                width: calc(100% - 280px);
            }
        }

        .complaint-launch-card {
            border: 0;
            background: linear-gradient(135deg, #ffffff, #f4f8ff);
        }

        .launch-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 1.3rem;
            color: #0d6efd;
            background: rgba(13, 110, 253, 0.12);
        }

        .complaint-chat-modal .modal-content {
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(19, 41, 82, 0.18);
        }

        .complaint-chat-modal .modal-header {
            border-bottom: 1px solid rgba(13, 110, 253, 0.14);
            background: linear-gradient(90deg, #f8fbff, #eef4ff);
        }

        .complaint-thread {
            max-height: 60vh;
            overflow-y: auto;
            padding: 1rem 1rem 0.6rem;
            background: radial-gradient(circle at top left, #f4f8ff, #edf2fb 55%, #e7edf9);
        }

        .complaint-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 0.85rem;
        }

        .complaint-bubble {
            width: min(100%, 760px);
            background: #ffffff;
            border: 1px solid rgba(13, 110, 253, 0.12);
            border-radius: 16px 16px 6px 16px;
            padding: 0.85rem 0.95rem;
            box-shadow: 0 10px 24px rgba(13, 48, 108, 0.08);
        }

        .complaint-bubble.bg-success {
            background: #28a745;
        }

        .complaint-bubble.bg-light {
            background: #f8f9fa;
        }

        .complaint-meta {
            color: #64748b;
            font-size: 0.82rem;
            line-height: 1.2;
        }

        .complaint-message {
            margin: 0.55rem 0 0.65rem;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .complaint-meta {
            color: #64748b;
            font-size: 0.82rem;
            line-height: 1.2;
        }

        .complaint-message {
            margin: 0.55rem 0 0.65rem;
            white-space: pre-wrap;
        }

        .chat-actions {
            display: flex;
            gap: 0.4rem;
            flex-wrap: wrap;
        }

        .complaint-composer {
            border-top: 1px solid rgba(13, 110, 253, 0.14);
            background: #ffffff;
        }

        .floating-complaint-btn {
            position: fixed;
            right: 1.1rem;
            bottom: 1.1rem;
            z-index: 1060;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(13, 110, 253, 0.35);
        }

        .attachment-viewer-frame {
            width: 100%;
            min-height: 70vh;
            border: 0;
            border-radius: 10px;
            background: #f8f9fa;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--primary-color);
            color: #ffffff;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .notification-dropdown {
            width: 400px;
            max-width: 90vw;
        }

        .notification-list {
            max-height: 320px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f1f1;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item.unread {
            background-color: rgba(211, 47, 47, 0.05);
        }

        .notification-item:last-child {
            border-bottom: 0;
        }

        .notification-title {
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }

        .notification-message {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .notification-time {
            font-size: 0.75rem;
            color: #adb5bd;
        }

        .notification-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .notification-icon.success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .notification-icon.warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .notification-icon.info {
            background-color: rgba(0, 123, 255, 0.1);
            color: #007bff;
        }

        .notification-actions {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-top: 1px solid #e9ecef;
        }

        .notification-empty {
            padding: 30px 20px;
            text-align: center;
            color: #6c757d;
        }

        .notification-empty i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #dee2e6;
        }

        @media (max-width: 575.98px) {
            .complaint-chat-modal .modal-content {
                border-radius: 0;
            }

            .complaint-thread {
                max-height: calc(100vh - 245px);
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="mobile-overlay"></div>

<div class="sidebar">
    <div class="sidebar-header text-center">
        <img src="{{ asset('image/santafe.png') }}" class="login-logo img-fluid mb-3">
        <h1 class="h5">Santa Fe Water Billing</h1>
    </div>

    <nav class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/consumer-profile') }}">
                    <i class="bi bi-person"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/dashboard-consumer') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/consumer/dashboard') }}">
                    <i class="bi bi-receipt"></i> Billing
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('consumer.complaints.index') }}">
                    <i class="bi bi-chat-left-text"></i> Complain
                </a>
            </li>
        </ul>
    </nav>
</div>

<div class="main-content">
    <header class="header">
        <div class="d-flex align-items-center">
            <button id="sidebarToggle" class="btn d-lg-none me-3">
                <i class="bi bi-list"></i>
            </button>
            
        </div>
        @php
            $notifications = $notifications ?? collect();
            $unreadNotificationsCount = $notifications->where('is_read', false)->count();
        @endphp
        <div class="d-flex align-items-center gap-3">
            <div class="dropdown position-relative">
                <a href="#" class="text-decoration-none text-dark position-relative" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-5"></i>
                    @if($unreadNotificationsCount > 0)
                        <span class="notification-badge">{{ $unreadNotificationsCount }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationBell">
                    <div class="notification-actions">
                        <h6 class="mb-0">Notifications</h6>
                        @if($unreadNotificationsCount > 0)
                            <button type="button" class="btn btn-sm btn-outline-primary mark-all-read-btn">Mark all as read</button>
                        @endif
                    </div>
                    <div class="notification-list">
                        @forelse($notifications as $notification)
                            <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}" data-id="{{ $notification->id }}">
                                <div class="d-flex">
                                    <div class="notification-icon {{ $notification->type === 'billing' ? 'info' : ($notification->type === 'payment' ? 'success' : 'warning') }}">
                                        <i class="bi {{ $notification->type === 'billing' ? 'bi-receipt' : ($notification->type === 'payment' ? 'bi-check-circle' : 'bi-info-circle') }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="notification-title">{{ $notification->title }}</div>
                                        <div class="notification-message">{{ $notification->message }}</div>
                                        <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="notification-empty">
                                <i class="bi bi-bell-slash"></i>
                                <p>No notifications</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <!-- User Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <span>{{ $consumer->first_name ?? 'Consumer' }} {{ $consumer->last_name ?? '' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                   
                    <li><hr class="dropdown-divider"></li>
                    <!-- In the dropdown menu -->
                    <li>
                        <a class="dropdown-item text-danger" href="#" id="logout-btn">
                            <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                        </a>
                        <form id="logout-form" action="/logout" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="content-wrapper">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card-box complaint-launch-card p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="launch-icon">
                        <i class="bi bi-chat-left-dots"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 text-primary">Complaints </h4>
                        <p class="mb-0 text-muted">Open the modal to send and manage your complaints in chat view.</p>
                    </div>
                </div>
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#newComplaintModal">
                    <i class="bi bi-plus-circle me-1"></i> New Complaint
                </button>
            </div>
        </div>

        <!-- Complaints List -->
        <div class="row g-3 mt-2" id="complaintsListContainer">
            @forelse($complaints as $complaint)
                <div class="col-12">
                    <div class="card-box p-3 complaint-card cursor-pointer" data-complaint-id="{{ $complaint->id }}" style="cursor: pointer; transition: all 0.3s;">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-1">{{ $complaint->subject ?? 'Complaint #' . $complaint->id }}</h6>
                                <p class="mb-2 text-muted small">{{ Str::limit($complaint->messages->last()?->message ?? $complaint->message, 100) }}</p>
                                <div class="d-flex gap-2 flex-wrap">
                                    <span class="badge bg-light text-dark">{{ ucfirst($complaint->status) }}</span>
                                    <span class="text-muted small">{{ $complaint->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <small class="text-muted d-block">Last updated</small>
                                <small class="d-block">{{ $complaint->last_message_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-chat-square-text d-block mb-2 fs-3"></i>
                        <p>No complaints yet. Click "New Complaint" to get started.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<button class="btn btn-primary floating-complaint-btn d-lg-none" type="button" data-bs-toggle="modal" data-bs-target="#newComplaintModal">
    <i class="bi bi-plus-circle me-1"></i> New
</button>

<!-- New Complaint Modal -->
<div class="modal fade complaint-chat-modal" id="newComplaintModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">Create New Complaint</h5>
                    <p class="mb-0 text-muted small">Tell us about your issue</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('consumer.complaints.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" placeholder="Brief subject of your complaint" required value="{{ old('subject') }}">
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="5" placeholder="Describe your complaint in detail..." required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attachment (optional)</label>
                        <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <small class="text-muted">Allowed formats: JPG, PNG, PDF, DOC, DOCX (max 5MB)</small>
                        @error('attachment')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Submit Complaint
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complaint Conversation Modal -->
<div class="modal fade complaint-chat-modal" id="complaintConversationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="complaintSubjectTitle">Complaint</h5>
                    <p class="mb-0 text-muted small" id="complaintStatusBadge"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="complaint-thread" id="complaintMessagesThread">
                    <!-- Messages will be loaded here -->
                </div>
            </div>
            <div class="modal-footer complaint-composer">
                <form id="complaintReplyForm" enctype="multipart/form-data" method="POST" class="w-100">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-12">
                            <label class="form-label mb-1">Your Reply</label>
                            <textarea name="message" class="form-control" rows="2" placeholder="Type your reply..." required></textarea>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label mb-1">Attachment (optional)</label>
                            <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <small class="text-muted">Allowed: JPG, PNG, PDF, DOC, DOCX (max 5MB)</small>
                        </div>
                        <div class="col-md-4 d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Send Reply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attachmentViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mb-0">Attachment Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe id="attachmentViewerFrame" class="attachment-viewer-frame" title="Attachment preview"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    const complaintConversationModalEl = document.getElementById('complaintConversationModal');
    const complaintMessagesThread = document.getElementById('complaintMessagesThread');
    const complaintReplyForm = document.getElementById('complaintReplyForm');
    const attachmentViewerModalEl = document.getElementById('attachmentViewerModal');
    const attachmentViewerFrame = document.getElementById('attachmentViewerFrame');
    const hasComplaintErrors = @json($errors->any());
    const notificationBellEl = document.getElementById('notificationBell');
    const notificationListEl = document.querySelector('.notification-list');
    const notificationActionsEl = document.querySelector('.notification-actions');
    const notificationEndpoint = "{{ route('consumer.notifications.index') }}";
    const notificationReadAllEndpoint = "{{ route('consumer.notifications.read-all') }}";
    const notificationReadEndpointBase = "{{ url('/consumer/notifications') }}";
    const csrfToken = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '';
    const logoutButton = document.getElementById('logout-btn');
    const logoutForm = document.getElementById('logout-form');
    let currentComplaintId = null;

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            mobileOverlay.classList.toggle('active');
        });
    }

    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            mobileOverlay.classList.remove('active');
        });
    }

    // Complaint list item click handler - Load complaint conversation
    document.querySelectorAll('.complaint-card').forEach(card => {
        card.addEventListener('click', function() {
            const complaintId = this.getAttribute('data-complaint-id');
            loadComplaintConversation(complaintId);
        });
    });

    // Load complaint conversation
    async function loadComplaintConversation(complaintId) {
        try {
            const response = await fetch(`{{ route('consumer.complaints.index') }}/${complaintId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) throw new Error('Failed to load complaint');
            
            const data = await response.json();
            if (!data.success) throw new Error(data.message || 'Failed to load complaint');

            currentComplaintId = complaintId;
            
            // Update modal title and status
            document.getElementById('complaintSubjectTitle').textContent = data.complaint.subject || 'Complaint';
            const statusBadge = document.getElementById('complaintStatusBadge');
            statusBadge.innerHTML = `<span class="badge bg-primary">${data.complaint.status.toUpperCase()}</span>`;

            // Render messages
            renderComplaintMessages(data.messages);

            // Update reply form action
            complaintReplyForm.action = `{{ route('consumer.complaints.index') }}/${complaintId}/reply`;

            // Show modal
            const modal = new bootstrap.Modal(complaintConversationModalEl);
            modal.show();

            // Scroll to bottom
            setTimeout(() => {
                complaintMessagesThread.scrollTop = complaintMessagesThread.scrollHeight;
            }, 100);
        } catch (error) {
            console.error('Error loading complaint:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Failed to load complaint'
            });
        }
    }

    // Render complaint messages in the conversation
    function renderComplaintMessages(messages) {
        if (!complaintMessagesThread) return;

        complaintMessagesThread.innerHTML = '';

        if (!Array.isArray(messages) || messages.length === 0) {
            complaintMessagesThread.innerHTML = '<div class="text-center text-muted py-5">No messages yet</div>';
            return;
        }

        messages.forEach(msg => {
            const isConsumer = msg.sender_type === 'consumer';
            const alignClass = isConsumer ? 'flex-end' : 'flex-start';
            const bubbleClass = isConsumer ? 'bg-success text-white' : 'bg-light';
            const bubbleStyle = isConsumer ? 'border-radius: 16px 16px 6px 16px;' : 'border-radius: 16px 16px 16px 6px;';

            const html = `
                <div class="complaint-row d-flex justify-content-${alignClass} mb-3">
                    <div class="complaint-bubble ${bubbleClass}" style="${bubbleStyle}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="fw-bold">${escapeHtml(msg.sender_name)}</small>
                            <small class="ms-2">${msg.created_at}</small>
                        </div>
                        <p class="complaint-message mb-2">${escapeHtml(msg.message)}</p>
                        ${msg.has_attachment ? `
                            <a href="{{ route('consumer.complaints.index') }}/${currentComplaintId}/message/${msg.id}/attachment" 
                               class="btn btn-sm btn-outline-secondary" target="_blank">
                                <i class="bi bi-download"></i> Download
                            </a>
                        ` : ''}
                    </div>
                </div>
            `;

            complaintMessagesThread.insertAdjacentHTML('beforeend', html);
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

    // Handle complaint reply form submission
    if (complaintReplyForm) {
        complaintReplyForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
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
                await loadComplaintConversation(currentComplaintId);

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Reply sent successfully!'
                });
            } catch (error) {
                console.error('Error sending reply:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to send reply'
                });
            } finally {
                submitBtn.disabled = false;
            }
        });
    }

    if (attachmentViewerModalEl && attachmentViewerFrame) {
        attachmentViewerModalEl.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const attachmentUrl = trigger ? trigger.getAttribute('data-attachment-url') : '';

            if (attachmentUrl) {
                attachmentViewerFrame.src = attachmentUrl;
            }
        });

        attachmentViewerModalEl.addEventListener('hidden.bs.modal', function () {
            attachmentViewerFrame.src = '';
        });
    }

    if (notificationBellEl && notificationListEl && notificationActionsEl) {
        function escapeHtml(value) {
            return String(value || '').replace(/[&<>\"']/g, function (char) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                })[char];
            });
        }

        function getNotificationVisual(notification) {
            const title = String(notification.title || '').toLowerCase();

            if (title.includes('payment rejected')) {
                return { wrapperClass: 'warning', iconClass: 'bi-x-circle' };
            }

            if (title.includes('payment approved') || notification.type === 'payment') {
                return { wrapperClass: 'success', iconClass: 'bi-check-circle' };
            }

            if (notification.type === 'billing') {
                return { wrapperClass: 'info', iconClass: 'bi-receipt' };
            }

            return { wrapperClass: 'warning', iconClass: 'bi-info-circle' };
        }

        function renderMarkAllButton(unreadCount) {
            const existingButton = notificationActionsEl.querySelector('.mark-all-read-btn');

            if (unreadCount > 0 && !existingButton) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'btn btn-sm btn-outline-primary mark-all-read-btn';
                button.textContent = 'Mark all as read';
                notificationActionsEl.appendChild(button);
            }

            if (unreadCount === 0 && existingButton) {
                existingButton.remove();
            }
        }

        function updateNotificationBadge(unreadCount) {
            let badge = notificationBellEl.querySelector('.notification-badge');

            if (unreadCount > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'notification-badge';
                    notificationBellEl.appendChild(badge);
                }

                badge.textContent = String(unreadCount);
            } else if (badge) {
                badge.remove();
            }
        }

        function renderNotificationList(notifications) {
            if (!Array.isArray(notifications) || notifications.length === 0) {
                notificationListEl.innerHTML = `
                    <div class="notification-empty">
                        <i class="bi bi-bell-slash"></i>
                        <p>No notifications</p>
                    </div>
                `;

                return;
            }

            notificationListEl.innerHTML = notifications.map(function (notification) {
                const visual = getNotificationVisual(notification);
                const unreadClass = notification.is_read ? '' : 'unread';

                return `
                    <div class="notification-item ${unreadClass}" data-id="${Number(notification.id)}">
                        <div class="d-flex">
                            <div class="notification-icon ${visual.wrapperClass}">
                                <i class="bi ${visual.iconClass}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="notification-title">${escapeHtml(notification.title || 'Notification')}</div>
                                <div class="notification-message">${escapeHtml(notification.message || '')}</div>
                                <div class="notification-time">${escapeHtml(notification.time_ago || 'Just now')}</div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function fetchNotifications() {
            fetch(`${notificationEndpoint}?all=1`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (response) {
                    return response.ok ? response.json() : null;
                })
                .then(function (payload) {
                    if (!payload || payload.success !== true) {
                        return;
                    }

                    const notifications = Array.isArray(payload.notifications) ? payload.notifications : [];
                    const unreadCount = Number(payload.unread_count ?? notifications.filter(function (item) {
                        return !item.is_read;
                    }).length);

                    renderNotificationList(notifications);
                    renderMarkAllButton(unreadCount);
                    updateNotificationBadge(unreadCount);
                })
                .catch(function () {
                    // Silent fail to avoid blocking complaint flow if notifications endpoint is unavailable.
                });
        }

        notificationListEl.addEventListener('click', function (event) {
            const item = event.target.closest('.notification-item');
            if (!item || !item.classList.contains('unread')) {
                return;
            }

            const notificationId = Number(item.getAttribute('data-id'));
            if (!notificationId) {
                return;
            }

            fetch(`${notificationReadEndpointBase}/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            })
                .then(function () {
                    fetchNotifications();
                })
                .catch(function () {
                    // Ignore transient notification read errors.
                });
        });

        notificationActionsEl.addEventListener('click', function (event) {
            const markAllButton = event.target.closest('.mark-all-read-btn');
            if (!markAllButton) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            fetch(notificationReadAllEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            })
                .then(function () {
                    fetchNotifications();
                })
                .catch(function () {
                    // Ignore transient notification update errors.
                });
        });

        fetchNotifications();
        setInterval(fetchNotifications, 15000);
    }

    if (logoutButton && logoutForm && typeof Swal !== 'undefined') {
        logoutButton.addEventListener('click', function (event) {
            event.preventDefault();

            Swal.fire({
                title: 'Logout Confirmation',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d32f2f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Logout!',
                cancelButtonText: 'Cancel',
                reverseButtons: false,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                Swal.fire({
                    title: 'Logging out...',
                    text: 'Please wait while we securely log you out.',
                    allowOutsideClick: false,
                    didOpen: function () {
                        Swal.showLoading();
                    }
                });

                setTimeout(function () {
                    logoutForm.submit();
                }, 1000);
            });
        });
    }
</script>
</body>
</html>
