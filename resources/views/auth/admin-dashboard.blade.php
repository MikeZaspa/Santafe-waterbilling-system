<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Santa Fe Water Billing System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome for additional icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Leaflet CSS for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="icon" type="image/png" href="image/santalogo.png">
    <!-- Custom CSS for minor adjustments -->
    <style>
        :root {
            --primary-color: #d32f2f;
            --primary-light: #ff6659;
            --primary-dark: #9a0007;
            --sidebar-bg: #f8f9fa;
            --sidebar-text: rgba(0,0,0,0.8);
            --sidebar-hover: rgba(0,0,255,0.1);
            --overlay-color: rgba(7, 7, 7, 0.1);
            --header-height: 70px;
            --white: #ffffff;
            --border: #e0e0e0;
            --text-light: #6c757d;
            --warning: #ffc107;
            --error: #dc3545;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        .sidebar {
            width: 280px;
            background: white;
            position: fixed;
            height: 100vh;
            top: 0;
            left: 0;
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
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
       .sidebar-header .logo {
            width: 60px;
            height: 60px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto;
        }
        
        .sidebar-menu .nav-link {
            color: gray;
            padding: 0.75rem 1.5rem;
            margin: 0 0.5rem;
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .sidebar-menu .nav-link:hover {
            color: white;
            background: blue;
            transform: translateX(5px);
        }
        
        .sidebar-menu .nav-link.active {
            color: white;
            background: blue;
            font-weight: 500;
            position: relative;
        }
        
        .sidebar-menu .nav-link.active::after {
            content: '';
            position: absolute;
            right: -10px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: white;
            border-radius: 2px;
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
        
        .header-left {
            display: flex;
            align-items: center;
        }
        
        .header-right {
            display: flex;
            align-items: center;
        }
        
        .header-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: blue;
           
        }
        
        .header-subtitle {
            margin: 0;
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .content-wrapper {
            padding: 20px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: rgba(211, 47, 47, 0.05);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .login-logo {
            width: 100px;       
            height: 100px;      
            border-radius: 50%; 
            object-fit: cover;  
        }
        
        /* Mobile overlay styles */
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

        /* Mobile menu toggle button */
        .mobile-menu-toggle {
            font-size: 1.5rem;
            padding: 0.25rem 0.5rem;
            border: none;
            background: transparent;
            color: var(--primary-color);
        }
        
        /* Modal styles */
        .modal-dialog {
            max-width: 95%;
        }
        
        .modal-content {
            height: 90vh;
        }
        
        .modal-body {
            height: calc(90vh - 140px);
            overflow-y: auto;
        }
        
        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }
        
        .badge-active {
            background-color: #28a745;
        }
        
        .badge-inactive {
            background-color: #6c757d;
        }
        
        .location-pin {
            cursor: pointer;
            color: #1a73e8;
        }
        
        .location-pin:hover {
            color: #0d5bba;
        }
        
        /* Header icon styles */
        .header-icon {
            font-size: 1.25rem;
            color: #6c757d;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        
        .header-icon:hover {
            color: var(--primary-color);
        }
        
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            width: 10px;
            height: 10px;
            background-color: #dc3545;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        /* Map modal styles */
        #mapModal .modal-dialog {
            max-width: 90%;
        }
        
        #mapModal .modal-content {
            height: 80vh;
        }
        
        #mapModal .modal-body {
            height: calc(80vh - 140px);
            padding: 0;
        }
        
        #locationMap {
            height: 100%;
            width: 100%;
        }
        
        .map-info {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
            max-width: 250px;
        }
        
        /* Loading spinner for map */
        .map-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            text-align: center;
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
        
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            /* Don't move the main content when sidebar is active on mobile */
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
        
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            min-height: 56px;
            padding: 0.9rem 1.5rem;
        }

        .card-header > * {
            margin-bottom: 0;
        }

        .card h3 {
            font-weight: 700;
            color: #2c3e50;
        }

        .card h6 {
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .complaints-summary-card .summary-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: rgba(220, 53, 69, 0.12);
            color: #dc3545;
            font-size: 1.25rem;
        }

        .floating-complaints-btn {
            position: fixed;
            right: 1.25rem;
            bottom: 1.25rem;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 28px rgba(13, 110, 253, 0.35);
            z-index: 1060;
            padding: 0;
        }

        .floating-complaints-count {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 22px;
            height: 22px;
            border-radius: 999px;
            background: #dc3545;
            color: #ffffff;
            font-size: 0.72rem;
            line-height: 22px;
            font-weight: 700;
            text-align: center;
            border: 2px solid #ffffff;
            padding: 0 5px;
        }

        #complaintsModal .modal-dialog {
            max-width: 360px;
            width: calc(100% - 1.5rem);
            margin: 0.75rem 1rem 0.75rem auto;
            display: flex;
            align-items: center;
            min-height: calc(100% - 1.5rem);
        }

        #complaintsModal .modal-content {
            height: min(78vh, 660px);
            max-height: none;
            border-radius: 26px;
            overflow: hidden;
            border: 0;
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.28);
            display: flex;
            flex-direction: column;
        }

        #complaintsModal .modal-body {
            flex: 1;
            min-height: 0;
            overflow: hidden;
            background: #f3f4f6;
            padding: 0.9rem;
            display: flex;
            flex-direction: column;
        }

        .complaints-conversation-list {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding-right: 4px;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .complaint-conversation-card {
            background: #ffffff;
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
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #4b5563;
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
            height: min(76vh, 640px);
            max-height: none;
            border: 0;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.28);
            display: flex;
            flex-direction: column;
        }

        .complaint-chat-modal .modal-body {
            flex: 1;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .admin-complaint-thread {
            flex: 1;
            min-height: 0;
            max-height: none;
            overflow-y: auto;
            padding: 1rem;
            background: radial-gradient(circle at top left, #f5f8ff, #edf2fb 55%, #e7edf9);
        }

        .admin-chat-row {
            display: flex;
            margin-bottom: 0.85rem;
        }

        .admin-chat-row.is-admin {
            justify-content: flex-end;
        }

        .admin-chat-row.is-consumer {
            justify-content: flex-start;
        }

        .admin-chat-bubble {
            width: auto;
            max-width: 88%;
            background: #ffffff;
            padding: 0.85rem 0.95rem;
            border: 1px solid rgba(13, 110, 253, 0.14);
            box-shadow: 0 10px 24px rgba(13, 48, 108, 0.08);
        }

        .admin-chat-row.is-admin .admin-chat-bubble {
            border-radius: 16px 16px 6px 16px;
            border-color: rgba(13, 110, 253, 0.2);
        }

        .admin-chat-row.is-consumer .admin-chat-bubble {
            border-radius: 16px 16px 16px 6px;
            border-color: rgba(108, 117, 125, 0.2);
            box-shadow: 0 8px 20px rgba(71, 85, 105, 0.08);
        }

        .admin-chat-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.65rem;
            color: #64748b;
            font-size: 0.82rem;
            line-height: 1.2;
        }

        .admin-chat-message {
            margin: 0.55rem 0 0.65rem;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .complaint-reply-form {
            width: 100%;
        }

        .complaint-reply-form .form-label {
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

        .reply-composer-row {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: end;
            gap: 0.55rem;
        }

        .reply-textarea {
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

        .reply-textarea:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.18);
        }

        .reply-send-btn {
            min-width: 122px;
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

        .reply-send-btn:hover,
        .reply-send-btn:focus {
            background: #c92c3a;
            color: #ffffff;
        }

        .complaint-composer {
            border-top: 1px solid rgba(13, 110, 253, 0.14);
            background: #ffffff;
            padding: 0.75rem 0.9rem;
        }

        #complaintAttachmentModal .modal-dialog {
            max-width: min(760px, 82vw);
        }

        #complaintAttachmentModal .modal-content {
            height: 72vh;
        }

        #complaintAttachmentModal .modal-body {
            height: calc(72vh - 86px);
            overflow: hidden;
        }

        .attachment-preview-frame {
            width: 100%;
            height: 100%;
            min-height: 0;
            border: 0;
            background-color: #f8f9fa;
        }
        
        @media (max-width: 576px) {
            .header {
                margin: 8px 12px;
                padding: 0 12px;
            }

            .header-title {
                font-size: 1.1rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .header-subtitle {
                display: none;
            }

            .header-left {
                flex: 1;
                min-width: 0;
            }

            .header-left > div {
                min-width: 0;
            }

            .header-right {
                margin-left: 8px;
                flex-shrink: 0;
            }

            .mobile-menu-toggle {
                margin-right: 8px !important;
                width: 32px;
                height: 32px;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .dropdown-toggle span {
                display: none;
            }

            #dropdownUser {
                padding: 0;
                display: inline-flex;
                align-items: center;
            }

            #dropdownUser::after {
                margin-left: 0;
                vertical-align: middle;
            }

            .content-wrapper {
                padding: 12px;
            }
            
            .modal-dialog {
                max-width: 100%;
                margin: 0;
            }
            
            .modal-content {
                height: 100vh;
                border-radius: 0;
            }
            
            .modal-body {
                height: calc(100vh - 140px);
            }

            .attachment-preview-frame {
                min-height: 60vh;
            }

            #complaintAttachmentModal .modal-dialog {
                max-width: 94%;
                margin: 1rem auto;
            }

            #complaintAttachmentModal .modal-content {
                height: 70vh;
                border-radius: 18px;
            }

            #complaintsModal .modal-dialog {
                max-width: 360px;
                width: calc(100% - 1rem);
                margin: 0.5rem 0.5rem 0.5rem auto;
                min-height: calc(100% - 1rem);
            }

            #complaintsModal .modal-content {
                height: min(74vh, 620px);
                max-height: none;
                border-radius: 24px;
            }

            #complaintsModal .modal-body {
                flex: 1;
                min-height: 0;
                overflow: hidden;
            }

            .complaint-chat-modal .modal-dialog {
                max-width: 360px;
                width: calc(100% - 1rem);
                margin: 0.5rem 0.5rem 0.5rem auto;
                min-height: calc(100% - 1rem);
            }

            .complaint-chat-modal .modal-content {
                height: min(74vh, 620px);
                max-height: none;
                border-radius: 22px;
            }

            .complaint-chat-modal .modal-body {
                flex: 1;
                min-height: 0;
                overflow: hidden;
            }

            .reply-composer-row {
                grid-template-columns: 1fr 112px;
                gap: 0.5rem;
            }

            .reply-send-btn {
                width: 100%;
                min-width: 0;
                height: 76px;
            }

            #complaintAttachmentModal .modal-body {
                height: calc(70vh - 86px);
            }

            .floating-complaints-btn {
                right: 0.9rem;
                bottom: 0.9rem;
                width: 52px;
                height: 52px;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Mobile Overlay -->
<div id="mobileOverlay" class="mobile-overlay"></div>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <div id="sidebarHeader" class="sidebar-header text-center">
        <img src="{{ asset('image/santafe.png') }}" class="login-logo img-fluid mb-3">
        <h1 id="sidebarTitle" class="h5">Santa Fe Water Billing</h1>
    </div>
    <nav id="sidebarMenu" class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a id="dashboardLink" class="nav-link active" href="admin-dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a id="consumersLink" class="nav-link" href="admin-consumer">
                    <i class="bi bi-people"></i> Manage Consumers
                </a>
            </li>
            <li class="nav-item">
                <a id="accountsLink" class="nav-link" href="admin-consumer-form">
                    <i class="bi bi-person-badge"></i> Manage Accounts
                </a>
            </li>
            <li class="nav-item">
                <a id="plumberLink" class="nav-link" href="admin-plumber">
                    <i class="bi bi-wrench"></i> Manage Plumber
                </a>
            </li>
            <li class="nav-item">
                <a id="accountantLink" class="nav-link" href="admin-accountant">
                    <i class="bi bi-cash-stack"></i> Manage Accountant
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="water-rates">
                    <i class="bi bi-cash-coin"></i> Water Rates
                </a>
            </li>

            <!-- Notices -->
            <li class="nav-item">
                <a class="nav-link" href="admin-accountant-notice">
                    <i class="bi bi-bell"></i> Notices
                </a>
            </li>
            <li class="nav-item">
                <a id="announcementLink" class="nav-link" href="admin-announcement">
                    <i class="bi bi-megaphone"></i> Announcements
                </a>
            </li>

        </ul>
    </nav>
</div>

<!-- Main Content -->
<div id="mainContent" class="main-content">
    <!-- Header -->
    <header id="header" class="header">
        <div id="headerLeft" class="header-left">
            <button id="sidebarToggle" class="btn d-lg-none me-3 mobile-menu-toggle">
                <i class="bi bi-list"></i>
            </button>
              <div>
                <h2 class="header-title"Admin Dashboard</h2>
                <p class="header-subtitle">Santa Fe Water Billing System</p>
            </div>
        </div>
        
        <div id="headerRight" class="header-right">
            <div class="position-relative me-3 d-none d-sm-block">
                <i class="bi bi-cloud-download header-icon" id="backupDatabaseIcon" title="Backup Database"></i>
            </div>
            <div class="position-relative me-3 d-none d-sm-block">
                <i class="bi bi-clock-history header-icon" id="adminLogsIcon" data-bs-toggle="modal" data-bs-target="#adminLogsModal" title="Admin Logs"></i>
            </div>
            <div class="position-relative me-3 d-none d-sm-block">
                <i class="bi bi-bell header-icon" title="Complaint Notifications"></i>
            </div>
            <div id="userDropdown" class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-none d-md-inline">Admin</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                    <li><a id="adminLogsBtn" class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#adminLogsModal">Admin Logs</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <!-- In the dropdown menu -->
                    <li>
                        <a id="logoutLink" class="dropdown-item text-danger" href="#">
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
    
    <div id="contentWrapper" class="content-wrapper">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any() && old('consumer_id'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div id="statsCards" class="row g-4">
            <!-- Total Consumers Card -->
            <div id="totalConsumersCard" class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Consumers</h6>
                                <h3 class="mb-0" id="totalConsumersCount">{{ $totalConsumers }}</h3>
                                <small class="text-success">
                                    <i class="bi bi-arrow-up"></i> All registered
                                </small>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-people-fill text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Consumers Card -->
            <div id="activeConsumersCard" class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Active Consumers</h6>
                                <h3 class="mb-0 text-success" id="activeConsumersCount">{{ $activeConsumers }}</h3>
                                <small class="text-success">
                                    <i class="bi bi-check-circle"></i> Currently active
                                </small>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Plumbers Card -->
            <div id="totalPlumbersCard" class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Plumbers</h6>
                                <h3 class="mb-0 text-info" id="totalPlumbersCount">{{ $totalPlumbers }}</h3>
                                <small class="text-muted">
                                    <i class="bi bi-wrench-adjustable-circle"></i> Registered staff
                                </small>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="bi bi-wrench-adjustable text-info fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Accountants Card -->
            <div id="totalAccountantsCard" class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Accountants</h6>
                                <h3 class="mb-0 text-warning" id="totalAccountantsCount">{{ $totalAccountants }}</h3>
                                <small class="text-muted">
                                    <i class="bi bi-cash-stack"></i> Registered staff
                                </small>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="bi bi-cash-stack text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div id="chartsSection" class="row g-4 mt-2">
            <!-- Consumer Status Pie Chart -->
            <div id="consumerStatusChartContainer" class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">Active Consumers</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="consumerStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total Consumers Line Chart -->
            <div id="totalConsumersChartContainer" class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">Total Consumers</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="totalConsumersChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

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

<!-- Consumer Complaints Modal -->
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
                                <span class="badge text-bg-primary-subtle text-primary js-conversation-count">{{ $conversation['messages']->count() }} messages</span>
                            </div>
                            <p class="complaint-conversation-preview js-conversation-preview">{{ \Illuminate\Support\Str::limit($conversation['last_message'], 160) }}</p>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <small class="text-muted js-conversation-last-activity" data-last-iso="{{ optional($conversation['last_message_at'])->toIso8601String() }}">
                                    Last activity:
                                    {{ optional($conversation['last_message_at'])->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'No messages yet' }}
                                </small>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary open-complaint-chat-btn"
                                    data-chat-target="complaintChatModal{{ $conversation['consumer_id'] }}">
                                    <i class="bi bi-chat-dots me-1"></i> Open Chat
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
                            <button type="submit" class="btn btn-sm btn-outline-light" title="Delete Conversation" aria-label="Delete Conversation">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0">
                    <div class="admin-complaint-thread js-admin-complaint-thread" data-consumer-id="{{ $conversation['consumer_id'] }}">
                        @foreach ($conversation['messages'] as $message)
                            @php
                                $isAdminMessage = $message->isAdminReply();
                            @endphp
                            <div class="admin-chat-row js-admin-chat-message {{ $isAdminMessage ? 'is-admin' : 'is-consumer' }}" data-message-id="{{ $message->id }}">
                                <div class="admin-chat-bubble">
                                    <div class="admin-chat-meta">
                                        <span>{{ $isAdminMessage ? 'Admin' : 'Consumer' }}</span>
                                        <span>{{ $message->created_at->timezone('Asia/Manila')->format('M d, Y h:i A') }}</span>
                                    </div>
                                    <p class="admin-chat-message">{{ $message->plainMessage() }}</p>
                                    @if (!empty($message->attachment_path))
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary view-complaint-attachment-btn"
                                            data-attachment-url="{{ route('admin.complaints.attachment', $message->id) }}">
                                            <i class="bi bi-paperclip me-1"></i> View Attachment
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer complaint-composer">
                    <form action="{{ route('admin.complaints.reply') }}" method="POST" class="w-100 complaint-reply-form js-admin-reply-form">
                        @csrf
                        <input type="hidden" name="consumer_id" value="{{ $conversation['consumer_id'] }}">
                        <p class="typing-indicator js-consumer-typing-indicator d-none" data-consumer-id="{{ $conversation['consumer_id'] }}">Consumer is typing...</p>
                        <label class="form-label mb-1">Reply as Admin</label>
                        <div class="reply-composer-row">
                            <textarea
                                name="message"
                                class="form-control reply-textarea js-admin-typing-input"
                                data-consumer-id="{{ $conversation['consumer_id'] }}"
                                rows="3"
                                placeholder="Type your reply here..."
                                required>{{ (int) old('consumer_id') === (int) $conversation['consumer_id'] ? old('message') : '' }}</textarea>
                            <button type="submit" class="btn reply-send-btn">
                                <i class="bi bi-send me-1"></i> Send Reply
                            </button>
                        </div>
                        @if ($errors->has('message') && (int) old('consumer_id') === (int) $conversation['consumer_id'])
                            <small class="text-danger">{{ $errors->first('message') }}</small>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Complaint Attachment Viewer Modal -->
<div id="complaintAttachmentModal" class="modal fade" tabindex="-1" aria-labelledby="complaintAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 id="complaintAttachmentModalLabel" class="modal-title">
                    <i class="bi bi-paperclip me-2"></i>Complaint Attachment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2 p-md-3">
                <iframe id="complaintAttachmentFrame" class="attachment-preview-frame" title="Complaint Attachment Preview"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Admin Logs Modal -->
<div id="adminLogsModal" class="modal fade" tabindex="-1" aria-labelledby="adminLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div id="adminLogsModalHeader" class="modal-header bg-primary text-white">
                <h5 id="adminLogsModalLabel" class="modal-title">
                    <i class="fas fa-history me-2"></i>Admin Login Logs
                </h5>
                <button id="adminLogsModalClose" type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="adminLogsModalBody" class="modal-body p-0">
                <div id="adminLogsModalContainer" class="container-fluid p-3">
                    <!-- Filters -->
                    <div id="logsFilters" class="row mb-3">
                        <div id="filtersContainer" class="col-12">
                            <form id="logsFilterForm" class="row g-2">
                                
                                <div id="activityFilterContainer" class="col-md-3">
                                    <label id="activityFilterLabel" class="form-label">Activity</label>
                                    <input type="text" name="activity" id="activityFilter" class="form-control form-control-sm" placeholder="Search activity...">
                                </div>
                                <div id="dateFromFilterContainer" class="col-md-2">
                                    <label id="dateFromFilterLabel" class="form-label">Date From</label>
                                    <input type="date" name="date_from" id="dateFromFilter" class="form-control form-control-sm">
                                </div>
                                <div id="dateToFilterContainer" class="col-md-2">
                                    <label id="dateToFilterLabel" class="form-label">Date To</label>
                                    <input type="date" name="date_to" id="dateToFilter" class="form-control form-control-sm">
                                </div>
                                <div id="filterButtonsContainer" class="col-md-2 d-flex align-items-end">
                                    <button type="button" id="filterLogsBtn" class="btn btn-primary btn-sm me-1">Filter</button>
                                    <button type="button" id="resetLogsBtn" class="btn btn-secondary btn-sm">Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Logs Table -->
                    <div id="logsTableContainer" class="table-responsive">
                        <table id="logsTable" class="table table-striped table-hover">
                            <thead id="logsTableHead" class="table-light">
                                <tr id="logsTableHeaderRow">
                                    <th id="idColumnHeader">ID</th>
                                        
                                    <th id="emailColumnHeader">Email</th>
                                    <th id="ipColumnHeader">IP Address</th>
                                    <th id="locationColumnHeader">Location</th>
                                    <th id="deviceColumnHeader">Device</th>
                                    <th id="activityColumnHeader">Activity</th>
                                    <th id="loginTimeColumnHeader">Login Time</th>
                                    <th id="logoutTimeColumnHeader">Logout Time</th>
                                    <th id="durationColumnHeader">Duration</th>
                                    <th id="statusColumnHeader">Status</th>
                                    <th id="actionsColumnHeader">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="logsTableBody">
                                <tr id="loadingRow">
                                    <td id="loadingCell" colspan="11" class="text-center py-4">
                                        <div id="loadingSpinner" class="spinner-border text-primary" role="status">
                                            <span id="loadingSpinnerText" class="visually-hidden">Loading...</span>
                                        </div>
                                        <p id="loadingText" class="mt-2">Loading logs...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
                        <div id="paginationInfo" class="text-muted me-3">
                            Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalRecords">0</span> entries
                        </div>
                        <div class="d-flex align-items-center">
                            <button id="startFirstBtn" type="button" class="btn btn-sm btn-outline-secondary me-1" title="Start">
                                <i class="bi bi-chevron-double-left"></i>
                            </button>
                            <nav id="logsPagination">
                                <!-- Pagination will be loaded here -->
                            </nav>
                            <button id="endLastBtn" type="button" class="btn btn-sm btn-outline-secondary ms-1" title="End">
                                <i class="bi bi-chevron-double-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div id="statisticsContainer" class="row mt-3">
                        <div id="statisticsCardContainer" class="col-12">
                            <div id="statisticsCard" class="card">
                                <div id="statisticsCardBody" class="card-body">
                                    <h6 id="statisticsTitle" class="card-title">Quick Statistics</h6>
                                    <div id="statisticsRow" class="row text-center">
                                        <div id="totalLogsContainer" class="col-md-3">
                                            <div id="totalLogsCard" class="border rounded p-2">
                                                <h4 id="totalLogsCount" class="text-primary mb-0">0</h4>
                                                <small id="totalLogsLabel" class="text-muted">Total Logs</small>
                                            </div>
                                        </div>
                                        <div id="successfulLoginsContainer" class="col-md-3">
                                            <div id="successfulLoginsCard" class="border rounded p-2">
                                                <h4 id="successfulLoginsCount" class="text-success mb-0">0</h4>
                                                <small id="successfulLoginsLabel" class="text-muted">Successful Logins</small>
                                            </div>
                                        </div>
                                        <div id="failedAttemptsContainer" class="col-md-3">
                                            <div id="failedAttemptsCard" class="border rounded p-2">
                                                <h4 id="failedAttemptsCount" class="text-danger mb-0">0</h4>
                                                <small id="failedAttemptsLabel" class="text-muted">Failed Attempts</small>
                                            </div>
                                        </div>
                                        <div id="activeSessionsContainer" class="col-md-3">
                                            <div id="activeSessionsCard" class="border rounded p-2">
                                                <h4 id="activeSessionsCount" class="text-warning mb-0">0</h4>
                                                <small id="activeSessionsLabel" class="text-muted">Active Sessions</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Map Modal -->
<div id="mapModal" class="modal fade" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div id="mapModalHeader" class="modal-header bg-primary text-white">
                <h5 id="mapModalLabel" class="modal-title">
                    <i class="fas fa-map-marked-alt me-2"></i>Location Details
                </h5>
                <button id="mapModalClose" type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="mapModalBody" class="modal-body p-0">
                <div id="locationMap"></div>
                <div id="mapLoading" class="map-loading d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading location...</span>
                    </div>
                    <p class="mt-2">Getting location details...</p>
                </div>
                <div id="mapInfo" class="map-info">
                    <h6 id="locationInfoTitle" class="mb-2">Login Location</h6>
                    <div id="locationDetails" class="small">
                        <p id="ipAddressInfo" class="mb-1"><strong>IP Address:</strong> <span id="ipAddressValue">-</span></p>
                        <p id="locationInfo" class="mb-1"><strong>Location:</strong> <span id="locationValue">-</span></p>
                        <p id="coordinatesInfo" class="mb-1"><strong>Coordinates:</strong> <span id="coordinatesValue">-</span></p>
                        <p id="loginTimeInfo" class="mb-1"><strong>Login Time:</strong> <span id="loginTimeValue">-</span></p>
                        <p id="deviceInfo" class="mb-0"><strong>Device:</strong> <span id="deviceValue">-</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- SweetAlert2 for notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Leaflet JS for maps -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
 $(document).ready(function() {
    let map; // Will store the map instance
    let currentMarker; // Will store the current marker
    const complaintsModalEl = document.getElementById('complaintsModal');
    const complaintAttachmentModalEl = document.getElementById('complaintAttachmentModal');
    const complaintAttachmentFrame = document.getElementById('complaintAttachmentFrame');
    const complaintConversationsListEl = document.getElementById('complaintConversationsList');
    const totalComplaintsSummaryEl = document.getElementById('totalComplaintsSummary');
    const totalComplaintsModalEl = document.getElementById('totalComplaintsModal');
    const floatingComplaintsCountEl = document.getElementById('floatingComplaintsCount');
    const adminComplaintReplyUrl = @json(route('admin.complaints.reply'));
    const adminComplaintDeleteBaseUrl = @json(url('/admin/complaints/conversation'));
    const adminComplaintAttachmentBaseUrl = @json(url('/admin/complaints'));
    const adminComplaintTypingUrl = @json(route('admin.complaints.typing'));
    const adminComplaintTypingStatusBaseUrl = @json(url('/admin/complaints/conversation'));
    const adminComplaintOnlineStatusesUrl = @json(route('admin.complaints.online-statuses'));
    const complaintTimeZone = 'Asia/Manila';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const openChatConsumerId = @json(session('open_chat_consumer_id'));
    const oldConsumerId = @json(old('consumer_id'));
    let activeComplaintModalEl = null;
    let totalComplaintMessages = Number(@json($totalComplaints)) || 0;
    const pageLoadedAtMs = Date.now();
    const seenComplaintMessageIds = new Set(
        Array.from(document.querySelectorAll('.js-admin-chat-message'))
            .map((node) => Number(node.getAttribute('data-message-id')))
            .filter((value) => Number.isFinite(value))
    );
    const adminTypingIdleTimers = {};
    const adminTypingHeartbeatTimers = {};
    let isOnlineStatusesSyncBusy = false;
    
    // Mobile sidebar toggle functionality
    const sidebar = $('.sidebar');
    const mainContent = $('.main-content');
    const header = $('.header');
    const sidebarToggle = $('#sidebarToggle');
    const mobileOverlay = $('.mobile-overlay');
    
    sidebarToggle.on('click', function() {
        sidebar.toggleClass('active');
        mobileOverlay.toggleClass('active');
        
        // Add overlay to header when sidebar is active
        if (sidebar.hasClass('active')) {
            header.css('background-color', 'var(--overlay-color)');
            $('body').css('overflow', 'hidden');
        } else {
            header.css('background-color', 'white');
            $('body').css('overflow', '');
        }
    });

    // Close sidebar when clicking on overlay
    mobileOverlay.on('click', function() {
        sidebar.removeClass('active');
        mobileOverlay.removeClass('active');
        header.css('background-color', 'white');
        $('body').css('overflow', '');
    });
    
    // Close sidebar when clicking on a nav link (for mobile)
    $('.sidebar-menu .nav-link').on('click', function() {
        if ($(window).width() < 992) {
            sidebar.removeClass('active');
            mobileOverlay.removeClass('active');
            header.css('background-color', 'white');
            $('body').css('overflow', '');
        }
    });
    
    // Handle window resize
    $(window).on('resize', function() {
        // Close sidebar if window is resized to desktop size
        if ($(window).width() >= 992) {
            sidebar.removeClass('active');
            mobileOverlay.removeClass('active');
            header.css('background-color', 'white');
            $('body').css('overflow', '');
        }
    });

    // Consumer Status Pie Chart - Now showing only active consumers
    const consumerStatusCtx = document.getElementById('consumerStatusChart').getContext('2d');
    const consumerStatusChart = new Chart(consumerStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active Consumers'],
            datasets: [{
                data: [{{ $activeConsumers }}],
                backgroundColor: [
                    '#28a745', // Green for active
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            return `${label}: ${value}`;
                        }
                    }
                }
            }
        }
    });

    // Total Consumers Line Chart (monthlyGrowth from controller)
    const totalConsumersCtx = document.getElementById('totalConsumersChart').getContext('2d');
    const monthlyGrowth = @json($monthlyGrowth);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // Fill missing months with 0 if necessary
    let growthData = Array(12).fill(0);
    monthlyGrowth.forEach((count, idx) => {
        growthData[idx] = count;
    });

    new Chart(totalConsumersCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'New Consumers',
                data: growthData,
                backgroundColor: 'rgba(211,47,47,0.15)',
                borderColor: '#d32f2f',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#d32f2f'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Consumers'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `New Consumers: ${context.raw}`;
                        }
                    }
                }
            }
        }
    }); 

    // Sign out confirmation dialog
    $('#logoutLink').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Sign Out?',
            text: 'Are you sure you want to sign out?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Sign Out',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Signing Out...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                setTimeout(() => {
                    document.getElementById('logout-form').submit();
                }, 1000);
            }
        });
    });
    
    // Database Backup functionality
    $('#backupDatabaseIcon').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Backup Database',
            text: 'Are you sure you want to backup database? This may take a few moments.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d32f2f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Backup Now',
            cancelButtonText: 'Cancel',
            reverseButtons: false,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Creating Backup...',
                    text: 'Please wait while we create a backup of your database.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Make AJAX request to backup endpoint
                $.ajax({
                    url: '/admin/backup-database',
                    type: 'GET',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data, status, xhr) {
                        // Get filename from Content-Disposition header if available
                        const filename = xhr.getResponseHeader('Content-Disposition') 
                            ? xhr.getResponseHeader('Content-Disposition').split('filename=')[1].replace(/"/g, '')
                            : 'database_backup_' + new Date().toISOString().slice(0, 10) + '.sql';
                        
                        // Create download link
                        const url = window.URL.createObjectURL(data);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                        
                        // Show success message
                        Swal.fire({
                            title: 'Backup Successful',
                            text: 'Database backup has been downloaded successfully.',
                            icon: 'success',
                            confirmButtonColor: '#d32f2f',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr, status, error) {
                        // Show error message
                        Swal.fire({
                            title: 'Backup Failed',
                            text: 'There was an error creating the database backup. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#d32f2f'
                        });
                    }
                });
            }
        });
    });
    
    // Admin Logs Modal functionality
    let currentPage = 1;

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

    async function postAdminTypingState(consumerId, isTyping) {
        if (!consumerId || !adminComplaintTypingUrl) {
            return;
        }

        try {
            await fetch(adminComplaintTypingUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    consumer_id: Number(consumerId),
                    is_typing: Boolean(isTyping)
                })
            });
        } catch (error) {
            // Silent fail to avoid interrupting chat UI.
        }
    }

    function clearAdminTypingHeartbeat(consumerId) {
        if (adminTypingHeartbeatTimers[consumerId]) {
            clearInterval(adminTypingHeartbeatTimers[consumerId]);
            delete adminTypingHeartbeatTimers[consumerId];
        }
    }

    function clearAdminTypingIdleTimer(consumerId) {
        if (adminTypingIdleTimers[consumerId]) {
            clearTimeout(adminTypingIdleTimers[consumerId]);
            delete adminTypingIdleTimers[consumerId];
        }
    }

    function stopAdminTypingFlow(consumerId) {
        if (!consumerId) {
            return;
        }

        clearAdminTypingIdleTimer(consumerId);
        clearAdminTypingHeartbeat(consumerId);
        postAdminTypingState(consumerId, false);
    }

    function startAdminTypingFlow(consumerId) {
        if (!consumerId) {
            return;
        }

        postAdminTypingState(consumerId, true);

        if (!adminTypingHeartbeatTimers[consumerId]) {
            adminTypingHeartbeatTimers[consumerId] = setInterval(function() {
                postAdminTypingState(consumerId, true);
            }, 4000);
        }

        clearAdminTypingIdleTimer(consumerId);
        adminTypingIdleTimers[consumerId] = setTimeout(function() {
            stopAdminTypingFlow(consumerId);
        }, 2500);
    }

    function updateConsumerTypingIndicator(consumerId, isTyping) {
        const indicatorEl = document.querySelector(`.js-consumer-typing-indicator[data-consumer-id="${consumerId}"]`);
        if (!indicatorEl) {
            return;
        }

        if (isTyping) {
            indicatorEl.classList.remove('d-none');
        } else {
            indicatorEl.classList.add('d-none');
        }
    }

    async function pollOpenConversationTypingStates() {
        const openModals = Array.from(document.querySelectorAll('.complaint-chat-modal.show[data-consumer-id]'));
        if (!openModals.length) {
            return;
        }

        await Promise.all(openModals.map(async function(modalEl) {
            const consumerId = Number(modalEl.getAttribute('data-consumer-id'));
            if (!Number.isFinite(consumerId) || consumerId <= 0) {
                return;
            }

            try {
                const response = await fetch(`${adminComplaintTypingStatusBaseUrl}/${consumerId}/typing-status`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                updateConsumerTypingIndicator(consumerId, Boolean(payload?.is_typing));
            } catch (error) {
                // Silent fail to keep modal stable.
            }
        }));
    }

    function setComplaintTotals(value) {
        totalComplaintMessages = Math.max(0, Number(value) || 0);

        if (totalComplaintsSummaryEl) {
            totalComplaintsSummaryEl.textContent = `Total: ${totalComplaintMessages}`;
        }

        if (totalComplaintsModalEl) {
            totalComplaintsModalEl.textContent = `Total messages: ${totalComplaintMessages}`;
        }

        if (floatingComplaintsCountEl) {
            if (totalComplaintMessages > 0) {
                floatingComplaintsCountEl.textContent = totalComplaintMessages > 99 ? '99+' : String(totalComplaintMessages);
                floatingComplaintsCountEl.classList.remove('d-none');
            } else {
                floatingComplaintsCountEl.textContent = '0';
                floatingComplaintsCountEl.classList.add('d-none');
            }
        }
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
        let cardEl = complaintConversationsListEl.querySelector(`.complaint-conversation-card[data-consumer-id="${consumerId}"]`);
        const activityLabel = `Last activity: ${formatDateTime(item.created_at)}`;

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
                    <span class="badge text-bg-primary-subtle text-primary js-conversation-count">1 messages</span>
                </div>
                <p class="complaint-conversation-preview js-conversation-preview">${escapeHtml(shortenText(item.message || '', 160))}</p>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted js-conversation-last-activity" data-last-iso="${escapeHtml(item.created_at || '')}">${escapeHtml(activityLabel)}</small>
                    <button
                        type="button"
                        class="btn btn-sm btn-primary open-complaint-chat-btn"
                        data-chat-target="complaintChatModal${consumerId}">
                        <i class="bi bi-chat-dots me-1"></i> Open Chat
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
            countEl.textContent = `${nextCount} messages`;
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
                                <h5 class="modal-title mb-0"><i class="bi bi-chat-left-text me-2"></i>${escapeHtml(item.consumer_name || 'Unknown Consumer')}</h5>
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
                                    <button type="submit" class="btn btn-sm btn-outline-light" title="Delete Conversation" aria-label="Delete Conversation">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body p-0">
                            <div class="admin-complaint-thread js-admin-complaint-thread" data-consumer-id="${consumerId}"></div>
                        </div>
                        <div class="modal-footer complaint-composer">
                            <form action="${adminComplaintReplyUrl}" method="POST" class="w-100 complaint-reply-form js-admin-reply-form">
                                <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                                <input type="hidden" name="consumer_id" value="${consumerId}">
                                <p class="typing-indicator js-consumer-typing-indicator d-none" data-consumer-id="${consumerId}">Consumer is typing...</p>
                                <label class="form-label mb-1">Reply as Admin</label>
                                <div class="reply-composer-row">
                                    <textarea name="message" class="form-control reply-textarea js-admin-typing-input" data-consumer-id="${consumerId}" rows="3" placeholder="Type your reply here..." required></textarea>
                                    <button type="submit" class="btn reply-send-btn"><i class="bi bi-send me-1"></i> Send Reply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        modalEl = document.getElementById(modalId);
        return modalEl;
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
            ? `<button type="button" class="btn btn-sm btn-outline-secondary view-complaint-attachment-btn" data-attachment-url="${adminComplaintAttachmentBaseUrl}/${item.id}/attachment"><i class="bi bi-paperclip me-1"></i> View Attachment</button>`
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

    setComplaintTotals(totalComplaintMessages);
    syncConversationOnlineStatuses();

    setInterval(pollOpenConversationTypingStates, 2500);
    setInterval(syncConversationOnlineStatuses, 10000);

    $(document).on('submit', '.js-delete-conversation-form', function(event) {
        event.preventDefault();

        const formEl = this;
        const confirmMessage = (formEl.getAttribute('data-confirm-message') || 'Delete this conversation? This cannot be undone.').trim();
        const submitForm = function() {
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
            }).then((result) => {
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

    $(document).on('input', '.js-admin-typing-input', function() {
        const consumerId = Number($(this).data('consumer-id'));
        const messageValue = ($(this).val() || '').toString().trim();

        if (!Number.isFinite(consumerId) || consumerId <= 0) {
            return;
        }

        if (messageValue.length > 0) {
            startAdminTypingFlow(consumerId);
        } else {
            stopAdminTypingFlow(consumerId);
        }
    });

    $(document).on('blur', '.js-admin-typing-input', function() {
        const consumerId = Number($(this).data('consumer-id'));
        if (Number.isFinite(consumerId) && consumerId > 0) {
            stopAdminTypingFlow(consumerId);
        }
    });

    $(document).on('submit', '.js-admin-reply-form', async function(event) {
        event.preventDefault();

        const formEl = this;
        const consumerId = Number($(formEl).find('input[name="consumer_id"]').val());
        const messageInputEl = formEl.querySelector('textarea[name="message"]');
        const submitButtonEl = formEl.querySelector('button[type="submit"]');
        const messageValue = (messageInputEl?.value || '').toString().trim();

        if (!Number.isFinite(consumerId) || consumerId <= 0 || messageValue.length === 0) {
            return;
        }

        stopAdminTypingFlow(consumerId);

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

            const payload = await response.json().catch(() => ({}));
            if (!response.ok || payload?.success !== true) {
                const firstError = payload?.errors
                    ? Object.values(payload.errors).flat()[0]
                    : 'Unable to send reply right now.';
                throw new Error(firstError || 'Unable to send reply right now.');
            }

            const complaint = payload?.complaint;
            if (complaint && Number.isFinite(Number(complaint.id))) {
                seenComplaintMessageIds.add(Number(complaint.id));
                appendAdminReplyMessage(complaint);
                setComplaintTotals(totalComplaintMessages + 1);
            }

            if (messageInputEl) {
                messageInputEl.value = '';
                messageInputEl.focus();
            }
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Unable to send reply right now.';
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
            if (submitButtonEl) {
                submitButtonEl.disabled = false;
            }
        }
    });

    $(document).on('click', '.open-complaint-chat-btn', function() {
        const chatTarget = ($(this).data('chat-target') || '').toString();
        const chatModalEl = chatTarget ? document.getElementById(chatTarget) : null;

        if (!chatModalEl) {
            return;
        }

        const complaintsModal = complaintsModalEl ? bootstrap.Modal.getOrCreateInstance(complaintsModalEl) : null;
        if (complaintsModal) {
            complaintsModal.hide();
            activeComplaintModalEl = complaintsModalEl;
        }

        bootstrap.Modal.getOrCreateInstance(chatModalEl).show();
    });

    $(document).on('shown.bs.modal', '.complaint-chat-modal', function() {
        const threadEl = this.querySelector('.js-admin-complaint-thread');
        if (threadEl) {
            threadEl.scrollTop = threadEl.scrollHeight;
        }
        pollOpenConversationTypingStates();
    });

    $(document).on('hidden.bs.modal', '.complaint-chat-modal', function() {
        const inputEl = this.querySelector('.js-admin-typing-input');
        const consumerId = Number(inputEl?.getAttribute('data-consumer-id') || this.getAttribute('data-consumer-id'));
        if (Number.isFinite(consumerId) && consumerId > 0) {
            stopAdminTypingFlow(consumerId);
            updateConsumerTypingIndicator(consumerId, false);
        }
    });

    const autoOpenConsumerId = oldConsumerId || openChatConsumerId;
    if (autoOpenConsumerId) {
        const autoOpenModalEl = document.getElementById(`complaintChatModal${autoOpenConsumerId}`);

        if (autoOpenModalEl) {
            bootstrap.Modal.getOrCreateInstance(autoOpenModalEl).show();
        }
    }

    $(document).on('click', '.view-complaint-attachment-btn', function() {
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

    $('#complaintAttachmentModal').on('hidden.bs.modal', function() {
        if (complaintAttachmentFrame) {
            complaintAttachmentFrame.src = 'about:blank';
        }

        if (activeComplaintModalEl) {
            bootstrap.Modal.getOrCreateInstance(activeComplaintModalEl).show();
            activeComplaintModalEl = null;
        }
    });

    window.addEventListener('complaint-notifications:update', function(event) {
        const detail = event?.detail || {};
        if (detail.role !== 'admin') {
            return;
        }

        const notifications = Array.isArray(detail.notifications) ? detail.notifications : [];
        notifications.forEach(function(item) {
            const messageId = Number(item.id);
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
    });
    
    // Initialize when modal is shown
    $('#adminLogsModal').on('shown.bs.modal', function() {
        loadAdminLogs();
        loadAdmins();
    });
    
    // Initialize map when modal is shown
    $('#mapModal').on('shown.bs.modal', function() {
        // Initialize map if it doesn't exist
        if (!map) {
            map = L.map('locationMap').setView([0, 0], 2);
            
            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
        } else {
            // Refresh the map to fix display issues
            setTimeout(function() {
                map.invalidateSize();
            }, 100);
        }
    });
    
    // Load admin logs
function loadAdminLogs(page = 1) {
    currentPage = page;
    
    // Show loading state
    $('#logsTableBody').html(`
        <tr id="loadingRow">
            <td id="loadingCell" colspan="11" class="text-center py-4">
                <div id="loadingSpinner" class="spinner-border text-primary" role="status">
                    <span id="loadingSpinnerText" class="visually-hidden">Loading...</span>
                </div>
                <p id="loadingText" class="mt-2">Loading logs...</p>
            </td>
        </tr>
    `);
    
    // Get filter values
    const activity = $('#activityFilter').val();
    const dateFrom = $('#dateFromFilter').val();
    const dateTo = $('#dateToFilter').val();
    
    // Build query string
    let queryString = `?page=${page}`;
    if (activity) queryString += `&activity=${activity}`;
    if (dateFrom) queryString += `&date_from=${dateFrom}`;
    if (dateTo) queryString += `&date_to=${dateTo}`;
    
    // Fetch logs via AJAX
    $.get(`/admin/logs/api${queryString}`, function(data) {
        renderLogsTable(data.logs.data);
        renderPagination(data.logs);
        updateStatistics(data.statistics);
    }).fail(function() {
        $('#logsTableBody').html(`
            <tr id="errorRow">
                <td id="errorCell" colspan="11" class="text-center py-4">
                    <i id="errorIcon" class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                    <p id="errorMessage" class="text-muted">Error loading logs. Please try again.</p>
                </td>
            </tr>
        `);
    });
}
    
 // Render logs table
function renderLogsTable(logs) {
    if (logs.length === 0) {
        $('#logsTableBody').html(`
            <tr id="noDataRow">
                <td id="noDataCell" colspan="11" class="text-center py-4">
                    <i id="noDataIcon" class="fas fa-search fa-2x text-muted mb-3"></i>
                    <p id="noDataMessage" class="text-muted">No logs found</p>
                </td>
            </tr>
        `);
        return;
    }
    
    let html = '';
    logs.forEach(function(log, index) {
        const logId = `log-${log.id}`;
        const rowId = `log-row-${index}`;
        const displayId = index + 1;
        const loginTime = new Date(log.login_at).toLocaleString();
        const logoutTime = log.logout_at ? new Date(log.logout_at).toLocaleString() : '';
        const duration = log.session_duration ? formatDuration(log.session_duration) : '-';
        const status = log.logout_at ? 
            '<span class="badge bg-secondary">Completed</span>' : 
            '<span class="badge bg-success">Active</span>';
        
        // Improved activity badge with better error detection
        let activityBadge = 'bg-primary';
        if (log.activity.includes('successful') || log.activity.includes('Successfully')) {
            activityBadge = 'bg-success';
        } else if (log.activity.includes('failed') || log.activity.includes('Failed') || 
                   log.activity.includes('error') || log.activity.includes('Error') ||
                   log.activity.includes('wrong_password') || log.activity.includes('inactive') ||
                   log.activity.includes('not_found') || log.activity.includes('not_verified')) {
            activityBadge = 'bg-danger';
        } else if (log.activity.includes('warning') || log.activity.includes('Warning')) {
            activityBadge = 'bg-warning';
        }
        
        // Improved location display with better fallbacks
        let locationDisplay = '<span class="text-muted">Unknown</span>';
        if (log.city && log.country && log.city !== 'Unknown' && log.country !== 'Unknown') {
            locationDisplay = `
                <div class="d-flex align-items-center">
                    <span>${log.city}, ${log.country}</span>
                </div>
            `;
        } else if (log.country && log.country !== 'Unknown') {
            locationDisplay = `
                <div class="d-flex align-items-center">
                    <span>${log.country}</span>
                </div>
            `;
        } else if (log.ip_address === '127.0.0.1' || log.ip_address.startsWith('192.168.') || log.ip_address.startsWith('10.')) {
            locationDisplay = '<span class="text-info">Local Network</span>';
        } else if (log.ip_address) {
            locationDisplay = `<span class="text-warning">IP: ${log.ip_address}</span>`;
        }
        
        html += `
            <tr id="${rowId}" data-log-id="${log.id}" 
                data-ip="${log.ip_address}"
                data-country="${log.country || ''}"
                data-city="${log.city || ''}"
                data-region="${log.region || ''}"
                data-email="${log.email}"
                data-activity="${log.activity}"
                data-login-time="${loginTime}"
                data-browser="${log.browser}"
                data-platform="${log.platform}"
                data-latitude="${log.latitude || ''}"
                data-longitude="${log.longitude || ''}"
                data-status="${log.status}">
                <td>
                    <span class="badge bg-secondary">${displayId}</span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-2">
                            ${log.email.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <div class="fw-bold">${log.email}</div>
                            ${log.status === 'failed' ? '<small class="text-danger">Failed Attempt</small>' : ''}
                        </div>
                    </div>
                </td>
                <td>
                    <code>${log.ip_address}</code>
                </td>
                <td>${locationDisplay}</td>
                <td>
                    <small>
                        <i class="fas fa-desktop me-1 text-muted"></i> ${log.browser || 'Unknown'}<br>
                        <i class="fas fa-laptop me-1 text-muted"></i> ${log.platform || 'Unknown'}
                    </small>
                </td>
                <td>
                    <span class="badge ${activityBadge}">${log.activity}</span>
                </td>
                <td>
                    <small>
                        <span>${loginTime.split(',')[0]}</span><br>
                        <span>${loginTime.split(',')[1]}</span>
                    </small>
                </td>
                <td>
                    ${logoutTime ? 
                        `<small>
                            <span>${logoutTime.split(',')[0]}</span><br>
                            <span>${logoutTime.split(',')[1]}</span>
                        </small>` : 
                        `<span class="badge bg-warning">Active</span>`
                    }
                </td>
                <td>
                    ${duration !== '-' ? 
                        `<span class="badge bg-info">${duration}</span>` : 
                        `<span class="text-muted">-</span>`
                    }
                </td>
                <td>${status}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary view-map-btn" data-log-id="${log.id}" title="View on Map" 
                            ${(!log.latitude || !log.longitude) && log.ip_address === '127.0.0.1' ? 'disabled' : ''}>
                            <i class="fas fa-map-marked-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    $('#logsTableBody').html(html);
    
    // Add click event for view map buttons
    $('.view-map-btn:not(:disabled)').on('click', function() {
        const logId = $(this).data('log-id');
        const row = $(`tr[data-log-id="${logId}"]`);
        
        const ip = row.data('ip');
        const country = row.data('country');
        const city = row.data('city');
        const region = row.data('region');
        const email = row.data('email');
        const activity = row.data('activity');
        const loginTime = row.data('login-time');
        const browser = row.data('browser');
        const platform = row.data('platform');
        const latitude = row.data('latitude');
        const longitude = row.data('longitude');
        const status = row.data('status');
        
        // Update map info
        $('#ipAddressValue').text(ip);
        $('#locationValue').text(city && country ? `${city}, ${region ? region + ', ' : ''}${country}` : 'Unknown Location');
        $('#loginTimeValue').text(loginTime);
        $('#deviceValue').text(`${browser} on ${platform}`);
        
        // Show map modal
        $('#mapModal').modal('show');
        $('#mapLoading').removeClass('d-none');
        
        // Check if we have coordinates
        if (latitude && longitude) {
            const lat = parseFloat(latitude);
            const lon = parseFloat(longitude);
            
            $('#coordinatesValue').text(`${lat.toFixed(6)}, ${lon.toFixed(6)}`);
            map.setView([lat, lon], 10);
            
            if (currentMarker) {
                map.removeLayer(currentMarker);
            }
            
            currentMarker = L.marker([lat, lon]).addTo(map);
            currentMarker.bindPopup(`
                <div>
                    <strong>IP Address:</strong> ${ip}<br>
                    <strong>Location:</strong> ${city}, ${region ? region + ', ' : ''}${country}<br>
                    <strong>Coordinates:</strong> ${lat.toFixed(6)}, ${lon.toFixed(6)}<br>
                    <strong>Login Time:</strong> ${loginTime}<br>
                    <strong>Device:</strong> ${browser} on ${platform}<br>
                    <strong>Status:</strong> ${status}
                </div>
            `).openPopup();
            
            $('#mapLoading').addClass('d-none');
        } else if (ip && ip !== '127.0.0.1') {
            getLocationFromIP(ip, city, country, region, loginTime, browser, platform, status);
        } else {
            $('#mapLoading').addClass('d-none');
            $('#coordinatesValue').text('Not available');
            Swal.fire({
                title: 'Location Not Available',
                text: 'Location data is not available for this log entry. This could be due to local network access or geolocation service limitations.',
                icon: 'info',
                confirmButtonColor: '#d32f2f'
            });
        }
    });

    // Show tooltip for disabled buttons
    $('.view-map-btn:disabled').on('click', function() {
        Swal.fire({
            title: 'Local Network',
            text: 'Location mapping is not available for local network IP addresses.',
            icon: 'info',
            confirmButtonColor: '#d32f2f'
        });
    });

}
    
    // Function to get location from IP using multiple services
    function getLocationFromIP(ip, city, country, region, loginTime, browser, platform) {
        // Try ipapi.co first
        $.ajax({
            url: `https://ipapi.co/${ip}/json/`,
            method: 'GET',
            dataType: 'json',
            timeout: 5000,
            success: function(data) {
                if (data && data.latitude && data.longitude) {
                    const lat = data.latitude;
                    const lon = data.longitude;
                    
                    // Update coordinates display
                    $('#coordinatesValue').text(`${lat.toFixed(6)}, ${lon.toFixed(6)}`);
                    
                    // Set map view to the location
                    map.setView([lat, lon], 10);
                    
                    // Remove existing marker if it exists
                    if (currentMarker) {
                        map.removeLayer(currentMarker);
                    }
                    
                    // Add new marker
                    currentMarker = L.marker([lat, lon]).addTo(map);
                    
                    // Add popup with location info
                    currentMarker.bindPopup(`
                        <div>
                            <strong>IP Address:</strong> ${ip}<br>
                            <strong>Location:</strong> ${data.city || city}, ${data.region || region}, ${data.country_name || country}<br>
                            <strong>Coordinates:</strong> ${lat.toFixed(6)}, ${lon.toFixed(6)}<br>
                            <strong>ISP:</strong> ${data.org || 'Unknown'}<br>
                            <strong>Login Time:</strong> ${loginTime}<br>
                            <strong>Device:</strong> ${browser} on ${platform}
                        </div>
                    `).openPopup();
                    
                    // Update the location display with more accurate data
                    $('#locationValue').text(`${data.city || city}, ${data.region || region}, ${data.country_name || country}`);
                    
                    // Hide loading indicator
                    $('#mapLoading').addClass('d-none');
                } else {
                    // If ipapi.co doesn't work, try ip-api.com
                    tryAlternativeIPService(ip, city, country, region, loginTime, browser, platform);
                }
            },
            error: function() {
                // If ipapi.co fails, try ip-api.com
                tryAlternativeIPService(ip, city, country, region, loginTime, browser, platform);
            }
        });
    }
    
    // Try alternative IP geolocation service
    function tryAlternativeIPService(ip, city, country, region, loginTime, browser, platform) {
        $.ajax({
            url: `http://ip-api.com/json/${ip}`,
            method: 'GET',
            dataType: 'json',
            timeout: 5000,
            success: function(data) {
                if (data && data.status === 'success' && data.lat && data.lon) {
                    const lat = data.lat;
                    const lon = data.lon;
                    
                    // Update coordinates display
                    $('#coordinatesValue').text(`${lat.toFixed(6)}, ${lon.toFixed(6)}`);
                    
                    // Set map view to the location
                    map.setView([lat, lon], 10);
                    
                    // Remove existing marker if it exists
                    if (currentMarker) {
                        map.removeLayer(currentMarker);
                    }
                    
                    // Add new marker
                    currentMarker = L.marker([lat, lon]).addTo(map);
                    
                    // Add popup with location info
                    currentMarker.bindPopup(`
                        <div>
                            <strong>IP Address:</strong> ${ip}<br>
                            <strong>Location:</strong> ${data.city || city}, ${data.regionName || region}, ${data.country || country}<br>
                            <strong>Coordinates:</strong> ${lat.toFixed(6)}, ${lon.toFixed(6)}<br>
                            <strong>ISP:</strong> ${data.isp || 'Unknown'}<br>
                            <strong>Login Time:</strong> ${loginTime}<br>
                            <strong>Device:</strong> ${browser} on ${platform}
                        </div>
                    `).openPopup();
                    
                    // Update the location display with more accurate data
                    $('#locationValue').text(`${data.city || city}, ${data.regionName || region}, ${data.country || country}`);
                    
                    // Hide loading indicator
                    $('#mapLoading').addClass('d-none');
                } else {
                    // If both IP services fail, try geocoding with city/country
                    geocodeWithNominatim(city, country, region, ip, loginTime, browser, platform);
                }
            },
            error: function() {
                // If both IP services fail, try geocoding with city/country
                geocodeWithNominatim(city, country, region, ip, loginTime, browser, platform);
            }
        });
    }
    
    // Function to geocode using Nominatim as fallback
    function geocodeWithNominatim(city, country, region, ip, loginTime, browser, platform) {
        if (!city && !country) {
            // If we don't have location data, show an error
            $('#mapLoading').addClass('d-none');
            Swal.fire({
                title: 'Location Not Found',
                text: 'Unable to determine location for this log entry.',
                icon: 'warning',
                confirmButtonColor: '#d32f2f',
                timer: 3000
            });
            return;
        }
        
        // Use Nominatim API to geocode the location
        const query = `${city}, ${region ? region + ', ' : ''}${country}`;
        
        $.ajax({
            url: `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`,
            method: 'GET',
            timeout: 5000,
            success: function(data) {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    
                    // Update coordinates display
                    $('#coordinatesValue').text(`${lat.toFixed(6)}, ${lon.toFixed(6)}`);
                    
                    // Set map view to the location
                    map.setView([lat, lon], 10);
                    
                    // Remove existing marker if it exists
                    if (currentMarker) {
                        map.removeLayer(currentMarker);
                    }
                    
                    // Add new marker
                    currentMarker = L.marker([lat, lon]).addTo(map);
                    
                    // Add popup with location info
                    currentMarker.bindPopup(`
                        <div>
                            <strong>IP Address:</strong> ${ip}<br>
                            <strong>Location:</strong> ${city}, ${region ? region + ', ' : ''}${country}<br>
                            <strong>Coordinates:</strong> ${lat.toFixed(6)}, ${lon.toFixed(6)}<br>
                            <strong>Login Time:</strong> ${loginTime}<br>
                            <strong>Device:</strong> ${browser} on ${platform}
                        </div>
                    `).openPopup();
                } else {
                    // If geocoding fails, show a message
                    Swal.fire({
                        title: 'Location Not Found',
                        text: 'Unable to find coordinates for this location. Showing default view.',
                        icon: 'warning',
                        confirmButtonColor: '#d32f2f',
                        timer: 3000
                    });
                }
                
                // Hide loading indicator
                $('#mapLoading').addClass('d-none');
            },
            error: function() {
                // If API call fails, show a message
                Swal.fire({
                    title: 'Geocoding Error',
                    text: 'Unable to get coordinates for this location. Showing default view.',
                    icon: 'error',
                    confirmButtonColor: '#d32f2f',
                    timer: 3000
                });
                
                // Hide loading indicator
                $('#mapLoading').addClass('d-none');
            }
        });
    }
    
    // Render pagination
    function renderPagination(logs) {
        // Update pagination info
        $('#showingFrom').text(logs.from);
        $('#showingTo').text(logs.to);
        $('#totalRecords').text(logs.total);
        
        if (logs.last_page <= 1) {
            $('#logsPagination').html('');
            $('#startFirstBtn').prop('disabled', true);
            $('#endLastBtn').prop('disabled', true);
            return;
        }
        
        let html = '<ul class="pagination">';
        
        // Previous button
        html += `<li class="page-item ${logs.current_page === 1 ? 'disabled' : ''}">
            <a id="prevPageBtn" class="page-link" href="#" onclick="loadAdminLogs(${logs.current_page - 1}); return false;">Previous</a>
        </li>`;
        
        // Page numbers
        for (let i = 1; i <= logs.last_page; i++) {
            if (i === 1 || i === logs.last_page || (i >= logs.current_page - 2 && i <= logs.current_page + 2)) {
                html += `<li class="page-item ${i === logs.current_page ? 'active' : ''}">
                    <a id="page-${i}" class="page-link" href="#" onclick="loadAdminLogs(${i}); return false;">${i}</a>
                </li>`;
            } else if (i === logs.current_page - 3 || i === logs.current_page + 3) {
                html += `<li class="page-item disabled"><a id="ellipsis-${i}" class="page-link" href="#">...</a></li>`;
            }
        }
        
        // Next button
        html += `<li class="page-item ${logs.current_page === logs.last_page ? 'disabled' : ''}">
            <a id="nextPageBtn" class="page-link" href="#" onclick="loadAdminLogs(${logs.current_page + 1}); return false;">Next</a>
        </li>`;
        
        html += '</ul>';
        $('#logsPagination').html(html);
        
        // Enable/disable navigation buttons based on current page
        $('#startFirstBtn').prop('disabled', logs.current_page === 1);
        $('#endLastBtn').prop('disabled', logs.current_page === logs.last_page);
    }
    
    // Update statistics
    function updateStatistics(stats) {
        $('#totalLogsCount').text(stats.total);
        $('#successfulLoginsCount').text(stats.successful);
        $('#failedAttemptsCount').text(stats.failed);
        $('#activeSessionsCount').text(stats.active);
    }
    
    // Format duration from seconds to HH:MM:SS
    function formatDuration(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }
    
    // Filter logs
    $('#filterLogsBtn').on('click', function() {
        loadAdminLogs(1);
    });
    
    // Reset filters
    $('#resetLogsBtn').on('click', function() {
        $('#adminFilter').val('');
        $('#activityFilter').val('');
        $('#dateFromFilter').val('');
        $('#dateToFilter').val('');
        loadAdminLogs(1);
    });
    
    // Start from first button
    $('#startFirstBtn').on('click', function() {
        loadAdminLogs(1);
    });
    
    // Go to last button
    $('#endLastBtn').on('click', function() {
        loadAdminLogs($('#totalRecords').text());
    });
    
    // Notification icon click handler
    $('#notificationIcon').on('click', function() {
        Swal.fire({
            icon: 'info',
            title: 'Notifications',
            text: 'You have no new notifications at this time.',
            confirmButtonColor: '#d32f2f'
        });
    });
    
    // Add ID to admin logs modal display
    $('#adminLogsModal').on('shown.bs.modal', function() {
        // Store the modal display state
        localStorage.setItem('adminLogsModalVisible', 'true');
    });
    
    // Hide admin logs modal display
    $('#adminLogsModal').on('hidden.bs.modal', function() {
        // Store the modal display state
        localStorage.setItem('adminLogsModalVisible', 'false');
    });
    
    // Check if modal should be shown on page load (based on stored state)
    if (localStorage.getItem('adminLogsModalVisible') === 'true') {
        // Show the modal
        $('#adminLogsModal').modal('show');
    }
});


</script>
<script src="{{ asset('js/complaint-notifications.js') }}?v={{ filemtime(public_path('js/complaint-notifications.js')) }}"></script>
<script>
$(function () {
    initComplaintNotifications({
        role: 'admin',
        pollingInterval: 5000
    });
});
</script>

</body>
</html>
