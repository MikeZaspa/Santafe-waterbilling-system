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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1040;
            background: white;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
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
            border-radius: 16px 16px 6px 16px;
            padding: 0.85rem 0.95rem;
            border: 1px solid rgba(13, 110, 253, 0.12);
            box-shadow: 0 10px 24px rgba(13, 48, 108, 0.08);
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
            <h2 class="h5 mb-0">Complaint Management</h2>
        </div>
        <div>
            <span class="me-3">{{ $consumer->first_name }} {{ $consumer->last_name }}</span>
            <form action="{{ route('consumer.logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
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
                        <h4 class="mb-1">Complaint Chatbox</h4>
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

<button class="btn btn-primary floating-complaint-btn d-lg-none" type="button" data-bs-toggle="modal" data-bs-target="#complaintChatModal">
    <i class="bi bi-chat-left-dots me-1"></i> Complain
</button>

<div class="modal fade complaint-chat-modal" id="complaintChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">Complaint Chatbox</h5>
                    <p class="mb-0 text-muted small">Keep all your complaints in one place.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="complaint-thread" id="complaintThread">
                    @forelse ($complaints as $complaint)
                        <div class="complaint-row">
                            <div class="complaint-bubble">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <span class="badge text-bg-primary-subtle text-primary">Complaint #{{ $loop->iteration }}</span>
                                    <span class="complaint-meta">{{ $complaint->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <p class="complaint-message">{{ $complaint->message }}</p>
                                @if ($complaint->attachment_path)
                                    <a class="btn btn-sm btn-outline-secondary mb-2" href="{{ route('consumer.complaints.attachment', $complaint->id) }}" target="_blank">
                                        <i class="bi bi-paperclip me-1"></i> View Attachment
                                    </a>
                                @endif
                                <div class="chat-actions">
                                    <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#editComplaintModal{{ $complaint->id }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('consumer.complaints.destroy', $complaint->id) }}" method="POST" onsubmit="return confirm('Delete this complaint?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="editComplaintModal{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="{{ route('consumer.complaints.update', $complaint->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Complaint #{{ $loop->iteration }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Update Message</label>
                                                <textarea name="message" class="form-control" rows="4" required>{{ $complaint->message }}</textarea>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label">Replace Attachment (optional)</label>
                                                <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
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
                <form action="{{ route('consumer.complaints.store') }}" method="POST" enctype="multipart/form-data" class="w-100">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-12">
                            <label class="form-label mb-1">Message</label>
                            <textarea name="message" class="form-control" rows="3" placeholder="Type your complaint message here..." required>{{ old('message') }}</textarea>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label mb-1">Attachment (optional)</label>
                            <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <small class="text-muted">Allowed: JPG, PNG, PDF, DOC, DOCX (max 5MB)</small>
                        </div>
                        <div class="col-md-4 d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Send Complaint
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    const complaintThread = document.getElementById('complaintThread');
    const complaintChatModalEl = document.getElementById('complaintChatModal');
    const hasComplaintErrors = @json($errors->any());

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
</script>
</body>
</html>
