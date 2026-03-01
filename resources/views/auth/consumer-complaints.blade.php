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
            top: 0;
            left: 0;
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
            margin: 12px;
            margin-left: 20px;
            margin-right: 20px;
            margin-bottom: 0px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            border-radius: 5px;
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

        .complaint-chat-modal .modal-dialog {
            max-width: 380px;
            width: calc(100% - 1.5rem);
            margin: 0.75rem 1rem 0.75rem auto;
            display: flex;
            align-items: center;
            min-height: calc(100% - 1.5rem);
        }

        .complaint-chat-modal .modal-content {
            height: 70vh;
            max-height: 560px;
            border: 0;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.28);
            display: flex;
            flex-direction: column;
        }

        .complaint-chat-modal .modal-header {
            border-bottom: 0;
            background: #1f6feb;
            color: #ffffff;
        }

        .complaint-chat-modal .modal-body {
            flex: 1;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .complaint-thread {
            flex: 1;
            min-height: 0;
            max-height: none;
            overflow-y: auto;
            padding: 0.9rem;
            background: #d5dbe7;
        }

        .complaint-row {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 0.85rem;
        }

        .complaint-row.is-admin {
            justify-content: flex-end;
        }

        .complaint-bubble {
            width: auto;
            max-width: 88%;
            background: #ffffff;
            border-radius: 16px;
            padding: 0.82rem 0.9rem;
            border: 1px solid #c7d2e4;
            box-shadow: 0 6px 16px rgba(43, 62, 94, 0.08);
        }

        .complaint-row.is-admin .complaint-bubble {
            border-radius: 16px 16px 6px 16px;
            border-color: #bcd3f5;
            box-shadow: 0 8px 18px rgba(64, 74, 84, 0.08);
        }

        .complaint-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.82rem;
            line-height: 1.2;
        }

        .complaint-message {
            margin: 0.55rem 0 0.65rem;
            white-space: pre-wrap;
        }

        .complaint-composer {
            border-top: 1px solid rgba(13, 110, 253, 0.14);
            background: #ffffff;
            padding: 0.75rem 0.9rem;
        }

        .consumer-complaint-form {
            width: 100%;
        }

        .consumer-complaint-form .form-label {
            margin-bottom: 0.45rem;
            font-size: 1rem;
            font-weight: 500;
            color: #374151;
        }

        .typing-indicator {
            min-height: 18px;
            margin-bottom: 0.35rem;
            color: #64748b;
            font-size: 0.82rem;
            line-height: 1.2;
        }

        .consumer-composer-row {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: end;
            gap: 0.55rem;
        }

        .consumer-textarea {
            min-height: 76px;
            resize: none;
            border: 2px solid #93c5fd;
            border-radius: 10px;
            background: #f8fbff;
            padding: 0.6rem 0.72rem;
            font-size: 0.98rem;
            line-height: 1.35;
            box-shadow: none;
        }

        .consumer-textarea:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.18);
        }

        .consumer-send-btn {
            min-width: 124px;
            height: 76px;
            border: 0;
            border-radius: 10px;
            background: #dc3545;
            color: #ffffff;
            font-weight: 600;
            font-size: 1rem;
            line-height: 1.2;
            padding: 0.5rem 0.78rem;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .consumer-send-btn:hover,
        .consumer-send-btn:focus {
            background: #c92c3a;
            color: #ffffff;
        }

        .consumer-attachment-row {
            margin-top: 0.55rem;
        }

        .floating-complaint-btn {
            position: fixed;
            right: 1.1rem;
            bottom: 1.1rem;
            z-index: 1060;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 28px rgba(13, 110, 253, 0.35);
            padding: 0;
        }

        .floating-complaint-count {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 20px;
            height: 20px;
            border-radius: 999px;
            background: #dc3545;
            color: #ffffff;
            font-size: 0.7rem;
            line-height: 20px;
            font-weight: 700;
            text-align: center;
            border: 2px solid #ffffff;
            padding: 0 4px;
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
            .complaint-chat-modal .modal-dialog {
                max-width: 360px;
                width: calc(100% - 1rem);
                margin: 0.5rem 0.5rem 0.5rem auto;
                min-height: calc(100% - 1rem);
            }

            .complaint-chat-modal .modal-content {
                height: 78vh;
                max-height: none;
                border-radius: 22px;
            }

            .consumer-composer-row {
                grid-template-columns: 1fr 112px;
                gap: 0.5rem;
            }

            .consumer-send-btn {
                width: 100%;
                min-width: 0;
                height: 76px;
            }

            .floating-complaint-btn {
                right: 0.9rem;
                bottom: 0.9rem;
                width: 50px;
                height: 50px;
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
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#complaintChatModal">
                    <i class="bi bi-chat-left-text me-1"></i> Open Chatbox
                </button>
            </div>
        </div>
    </div>
</div>

<button
    class="btn btn-primary floating-complaint-btn"
    type="button"
    data-bs-toggle="modal"
    data-bs-target="#complaintChatModal"
    title="Open Complaint Chatbox"
    aria-label="Open Complaint Chatbox">
    <i class="bi bi-chat-left-dots fs-5"></i>
    @if ($complaints->count() > 0)
        <span class="floating-complaint-count">{{ $complaints->count() > 99 ? '99+' : $complaints->count() }}</span>
    @endif
</button>

<div class="modal fade complaint-chat-modal" id="complaintChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">
                        <i class="bi bi-chat-left-text me-2"></i>{{ $consumer->first_name ?? 'Consumer' }} {{ $consumer->last_name ?? '' }}
                    </h5>
                    <p class="mb-0 text-white-50 small">Meter No: {{ $consumer->meter_no ?? 'N/A' }}</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form
                        action="{{ route('consumer.complaints.destroy-conversation') }}"
                        method="POST"
                        class="js-delete-conversation-form"
                        data-confirm-message="Delete your whole complaint conversation? This cannot be undone.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-light" title="Delete Conversation" aria-label="Delete Conversation">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="complaint-thread" id="complaintThread">
                    @forelse ($complaints as $complaint)
                        @php
                            $isAdminReply = $complaint->isAdminReply();
                        @endphp
                        <div class="complaint-row js-consumer-chat-message {{ $isAdminReply ? 'is-admin' : 'is-consumer' }}" data-message-id="{{ $complaint->id }}">
                            <div class="complaint-bubble">
                                <div class="complaint-meta-row">
                                    <span>{{ $isAdminReply ? 'Admin' : 'Consumer' }}</span>
                                    <span class="complaint-meta">{{ $complaint->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <p class="complaint-message">{{ $complaint->plainMessage() }}</p>
                                @if ($complaint->attachment_path)
                                    <button
                                        class="btn btn-sm btn-outline-secondary mb-2"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#attachmentViewerModal"
                                        data-attachment-url="{{ route('consumer.complaints.attachment', $complaint->id) }}"
                                    >
                                        <i class="bi bi-paperclip me-1"></i> View Attachment
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-chat-square-text d-block mb-2 fs-3"></i>
                            No complaints yet. Send your first message below.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer complaint-composer">
                <form id="consumerComplaintForm" action="{{ route('consumer.complaints.store') }}" method="POST" enctype="multipart/form-data" class="consumer-complaint-form">
                    @csrf
                    <p id="adminTypingIndicator" class="typing-indicator d-none">Admin is typing...</p>
                    <label class="form-label">Message</label>
                    <div class="consumer-composer-row">
                        <textarea id="consumerTypingInput" name="message" class="form-control consumer-textarea" rows="3" placeholder="Type your reply here..." required>{{ old('message') }}</textarea>
                        <button type="submit" class="btn consumer-send-btn">
                            <i class="bi bi-send me-1"></i> Send Reply
                        </button>
                    </div>
                    <div class="consumer-attachment-row">
                        <label class="form-label mb-1">Attachment (optional)</label>
                        <input type="file" name="attachment" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <small class="text-muted">Allowed: JPG, PNG, PDF, DOC, DOCX (max 5MB)</small>
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
    const complaintThread = document.getElementById('complaintThread');
    const complaintChatModalEl = document.getElementById('complaintChatModal');
    const attachmentViewerModalEl = document.getElementById('attachmentViewerModal');
    const attachmentViewerFrame = document.getElementById('attachmentViewerFrame');
    const hasComplaintErrors = @json($errors->any());
    const notificationBellEl = document.getElementById('notificationBell');
    const notificationListEl = document.querySelector('.notification-list');
    const notificationActionsEl = document.querySelector('.notification-actions');
    const notificationEndpoint = "{{ route('consumer.notifications.index') }}";
    const notificationReadAllEndpoint = "{{ route('consumer.notifications.read-all') }}";
    const notificationReadEndpointBase = "{{ url('/consumer/notifications') }}";
    const complaintLiveEndpoint = "{{ route('consumer.complaints.live') }}";
    const complaintTypingEndpoint = "{{ route('consumer.complaints.typing') }}";
    const complaintHeartbeatEndpoint = "{{ route('consumer.complaints.heartbeat') }}";
    const complaintTypingStatusEndpoint = "{{ route('consumer.complaints.typing-status') }}";
    const csrfToken = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '';
    const logoutButton = document.getElementById('logout-btn');
    const logoutForm = document.getElementById('logout-form');
    const consumerTypingInputEl = document.getElementById('consumerTypingInput');
    const consumerComplaintFormEl = document.getElementById('consumerComplaintForm');
    const adminTypingIndicatorEl = document.getElementById('adminTypingIndicator');
    const knownComplaintMessageIds = new Set(
        Array.from(document.querySelectorAll('.js-consumer-chat-message'))
            .map((node) => Number(node.getAttribute('data-message-id')))
            .filter((value) => Number.isFinite(value))
    );
    let latestComplaintMessageId = Math.max(0, ...Array.from(knownComplaintMessageIds));
    let isComplaintSyncBusy = false;
    let isConsumerSendBusy = false;
    let consumerTypingIdleTimer = null;
    let consumerTypingHeartbeatTimer = null;
    let isTypingStatusBusy = false;

    async function sendConsumerOnlineHeartbeat() {
        if (!complaintHeartbeatEndpoint) {
            return;
        }

        try {
            await fetch(complaintHeartbeatEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            });
        } catch (error) {
            // Silent fail to avoid interrupting complaint page usage.
        }
    }

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

    if (complaintChatModalEl && complaintThread) {
        complaintChatModalEl.addEventListener('shown.bs.modal', function () {
            complaintThread.scrollTop = complaintThread.scrollHeight;
        });
    }

    if (hasComplaintErrors && complaintChatModalEl) {
        const complaintChatModal = new bootstrap.Modal(complaintChatModalEl);
        complaintChatModal.show();
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

    async function postConsumerTypingState(isTyping) {
        if (!complaintTypingEndpoint) {
            return;
        }

        try {
            await fetch(complaintTypingEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    is_typing: Boolean(isTyping)
                })
            });
        } catch (error) {
            // Silent fail to keep chat flow uninterrupted.
        }
    }

    function setAdminTypingIndicator(isTyping) {
        if (!adminTypingIndicatorEl) {
            return;
        }

        if (isTyping && complaintChatModalEl && complaintChatModalEl.classList.contains('show')) {
            adminTypingIndicatorEl.classList.remove('d-none');
        } else {
            adminTypingIndicatorEl.classList.add('d-none');
        }
    }

    function stopConsumerTypingFlow() {
        if (consumerTypingIdleTimer) {
            clearTimeout(consumerTypingIdleTimer);
            consumerTypingIdleTimer = null;
        }

        if (consumerTypingHeartbeatTimer) {
            clearInterval(consumerTypingHeartbeatTimer);
            consumerTypingHeartbeatTimer = null;
        }

        postConsumerTypingState(false);
    }

    function startConsumerTypingFlow() {
        postConsumerTypingState(true);

        if (!consumerTypingHeartbeatTimer) {
            consumerTypingHeartbeatTimer = setInterval(function () {
                postConsumerTypingState(true);
            }, 4000);
        }

        if (consumerTypingIdleTimer) {
            clearTimeout(consumerTypingIdleTimer);
        }

        consumerTypingIdleTimer = setTimeout(function () {
            stopConsumerTypingFlow();
        }, 2500);
    }

    async function syncAdminTypingStatus() {
        if (isTypingStatusBusy || !complaintTypingStatusEndpoint) {
            return;
        }

        if (!complaintChatModalEl || !complaintChatModalEl.classList.contains('show')) {
            setAdminTypingIndicator(false);
            return;
        }

        isTypingStatusBusy = true;
        try {
            const response = await fetch(complaintTypingStatusEndpoint, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            setAdminTypingIndicator(Boolean(payload?.is_typing));
        } catch (error) {
            // Silent fail to avoid noisy UI.
        } finally {
            isTypingStatusBusy = false;
        }
    }

    if (consumerTypingInputEl) {
        consumerTypingInputEl.addEventListener('input', function () {
            const value = (consumerTypingInputEl.value || '').trim();
            if (value.length > 0) {
                startConsumerTypingFlow();
            } else {
                stopConsumerTypingFlow();
            }
        });

        consumerTypingInputEl.addEventListener('blur', function () {
            stopConsumerTypingFlow();
        });
    }

    if (consumerComplaintFormEl) {
        consumerComplaintFormEl.addEventListener('submit', async function (event) {
            event.preventDefault();

            if (isConsumerSendBusy) {
                return;
            }

            const formEl = consumerComplaintFormEl;
            const messageInputEl = formEl.querySelector('textarea[name="message"]');
            const attachmentInputEl = formEl.querySelector('input[name="attachment"]');
            const submitButtonEl = formEl.querySelector('button[type="submit"]');
            const messageValue = (messageInputEl?.value || '').trim();

            if (!messageValue.length) {
                return;
            }

            stopConsumerTypingFlow();

            const formData = new FormData(formEl);
            isConsumerSendBusy = true;

            if (submitButtonEl) {
                submitButtonEl.disabled = true;
            }

            try {
                const response = await fetch(formEl.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const payload = await response.json().catch(() => ({}));
                if (!response.ok || payload?.success !== true) {
                    const firstError = payload?.errors
                        ? Object.values(payload.errors).flat()[0]
                        : 'Unable to send message right now.';
                    throw new Error(firstError || 'Unable to send message right now.');
                }

                const complaint = payload?.complaint;
                if (complaint && Number.isFinite(Number(complaint.id))) {
                    appendLiveComplaintMessage(complaint);
                }

                if (messageInputEl) {
                    messageInputEl.value = '';
                    messageInputEl.focus();
                }

                if (attachmentInputEl) {
                    attachmentInputEl.value = '';
                }
            } catch (error) {
                const errorMessage = error instanceof Error ? error.message : 'Unable to send message right now.';
                if (typeof Swal !== 'undefined' && Swal && typeof Swal.fire === 'function') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Send failed',
                        text: errorMessage,
                        confirmButtonColor: '#d32f2f'
                    });
                } else {
                    alert(errorMessage);
                }
            } finally {
                isConsumerSendBusy = false;
                if (submitButtonEl) {
                    submitButtonEl.disabled = false;
                }
            }
        });
    }

    document.addEventListener('submit', function (event) {
        const formEl = event.target instanceof HTMLFormElement
            ? event.target
            : null;

        if (!formEl || !formEl.classList.contains('js-delete-conversation-form')) {
            return;
        }

        event.preventDefault();

        const confirmMessage = (formEl.getAttribute('data-confirm-message') || 'Delete this conversation? This cannot be undone.').trim();
        const submitForm = function () {
            HTMLFormElement.prototype.submit.call(formEl);
        };

        if (typeof Swal !== 'undefined' && Swal && typeof Swal.fire === 'function') {
            Swal.fire({
                title: 'Delete Conversation?',
                text: confirmMessage,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d32f2f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                reverseButtons: false
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

    if (complaintChatModalEl) {
        complaintChatModalEl.addEventListener('shown.bs.modal', function () {
            syncAdminTypingStatus();
        });

        complaintChatModalEl.addEventListener('hidden.bs.modal', function () {
            stopConsumerTypingFlow();
            setAdminTypingIndicator(false);
        });
    }

    function escapeChatHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatChatDate(isoString) {
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
            hour12: true
        }).replace(',', '');
    }

    function appendLiveComplaintMessage(message) {
        if (!complaintThread || !message || !message.id) {
            return;
        }

        const messageId = Number(message.id);
        if (!Number.isFinite(messageId) || knownComplaintMessageIds.has(messageId)) {
            return;
        }

        knownComplaintMessageIds.add(messageId);
        latestComplaintMessageId = Math.max(latestComplaintMessageId, messageId);

        const isAdmin = Boolean(message.is_admin);
        const rowClass = isAdmin ? 'is-admin' : 'is-consumer';
        const senderLabel = isAdmin ? 'Admin' : 'Consumer';
        const timeLabel = formatChatDate(message.created_at);
        const attachmentButton = message.has_attachment && message.attachment_url
            ? `
                <button
                    class="btn btn-sm btn-outline-secondary mb-2"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#attachmentViewerModal"
                    data-attachment-url="${escapeChatHtml(message.attachment_url)}">
                    <i class="bi bi-paperclip me-1"></i> View Attachment
                </button>
            `
            : '';

        const html = `
            <div class="complaint-row js-consumer-chat-message ${rowClass}" data-message-id="${messageId}">
                <div class="complaint-bubble">
                    <div class="complaint-meta-row">
                        <span>${senderLabel}</span>
                        <span class="complaint-meta">${escapeChatHtml(timeLabel)}</span>
                    </div>
                    <p class="complaint-message">${escapeChatHtml(message.message || '')}</p>
                    ${attachmentButton}
                </div>
            </div>
        `;

        complaintThread.insertAdjacentHTML('beforeend', html);

        if (complaintChatModalEl && complaintChatModalEl.classList.contains('show')) {
            complaintThread.scrollTop = complaintThread.scrollHeight;
        }
    }

    async function syncLiveComplaints() {
        if (isComplaintSyncBusy || !complaintLiveEndpoint) {
            return;
        }

        isComplaintSyncBusy = true;
        try {
            const response = await fetch(`${complaintLiveEndpoint}?since_id=${latestComplaintMessageId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            if (!payload || !Array.isArray(payload.messages)) {
                return;
            }

            payload.messages.forEach(appendLiveComplaintMessage);
            if (Number.isFinite(Number(payload.latest_id))) {
                latestComplaintMessageId = Math.max(latestComplaintMessageId, Number(payload.latest_id));
            }
        } catch (error) {
            // Silent fail to keep chat usable even if live endpoint is temporarily unavailable.
        } finally {
            isComplaintSyncBusy = false;
        }
    }

    setTimeout(syncLiveComplaints, 1500);
    sendConsumerOnlineHeartbeat();
    setInterval(syncLiveComplaints, 3000);
    setInterval(sendConsumerOnlineHeartbeat, 10000);
    setInterval(syncAdminTypingStatus, 2500);

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
