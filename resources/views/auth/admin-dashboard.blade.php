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
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            
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

        .complaints-table td {
            vertical-align: middle;
        }

        .complaint-message-preview {
            max-width: 340px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .complaint-message-cell {
            min-width: 300px;
        }

        .complaint-message-content {
            white-space: pre-wrap;
            word-break: break-word;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 1rem;
            min-height: 140px;
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

        #complaintsModal .modal-dialog {
            max-width: 92%;
        }

        #complaintsModal .modal-content {
            height: 85vh;
        }

        #complaintsModal .modal-body {
            height: calc(85vh - 140px);
        }

        #complaintAttachmentModal .modal-dialog {
            max-width: 88%;
        }

        #complaintAttachmentModal .modal-content {
            height: 88vh;
        }

        #complaintAttachmentModal .modal-body {
            height: calc(88vh - 86px);
            overflow: hidden;
        }

        #complaintMessageModal .modal-dialog {
            max-width: 720px;
        }

        #complaintMessageModal .modal-content,
        #complaintMessageModal .modal-body {
            height: auto;
        }

        .attachment-preview-frame {
            width: 100%;
            height: 100%;
            min-height: 0;
            border: 0;
            background-color: #f8f9fa;
        }
        
        @media (max-width: 576px) {
            .header-title {
                font-size: 1rem;
            }
            
            .header-subtitle {
                display: none;
            }
            
            .dropdown-toggle span {
                display: none;
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

            #complaintsModal .modal-dialog,
            #complaintAttachmentModal .modal-dialog {
                max-width: 100%;
                margin: 0;
            }

            #complaintsModal .modal-content,
            #complaintAttachmentModal .modal-content {
                height: 100vh;
                border-radius: 0;
            }

            #complaintsModal .modal-body {
                height: calc(100vh - 140px);
            }

            #complaintAttachmentModal .modal-body {
                height: calc(100vh - 86px);
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
                <h2 class="header-title">Billing Dashboard</h2>
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
        <div id="statsCards" class="row g-4">
            <!-- Total Consumers Card -->
            <div id="totalConsumersCard" class="col-md-6 col-lg-6">
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
            <div id="activeConsumersCard" class="col-md-6 col-lg-6">
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

        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="card border-0 shadow-sm complaints-summary-card">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="summary-icon">
                                <i class="bi bi-chat-left-text"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Consumer Complaints</h5>
                                <p class="text-muted mb-0">Review all submitted complaint messages in one modal.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger-subtle text-danger fs-6">Total: {{ $totalComplaints }}</span>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#complaintsModal">
                                <i class="bi bi-eye me-1"></i> View Complaints
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                    <p class="text-muted mb-0">Latest complaint records from consumers.</p>
                    <span class="badge bg-danger-subtle text-danger">Total: {{ $totalComplaints }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover complaints-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Consumer</th>
                                <th>Meter No.</th>
                                <th>Message</th>
                                <th>Last Message</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentComplaints as $complaint)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ optional($complaint->consumer)->first_name }} {{ optional($complaint->consumer)->last_name }}</td>
                                    <td>{{ optional($complaint->consumer)->meter_no ?? 'N/A' }}</td>
                                    <td class="complaint-message-cell">
                                        <span class="complaint-message-preview" title="{{ $complaint->message }}">
                                            {{ $complaint->message ?? 'No message provided.' }}
                                        </span>
                                    </td>
                                    <td >
                                        <small>{{ $complaint->last_message_at?->format('M d, Y h:i A') ?? $complaint->created_at->format('M d, Y h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-primary view-complaint-message-btn"
                                                data-complaint-message="{{ $complaint->message }}"
                                                data-consumer-name="{{ trim(optional($complaint->consumer)->first_name . ' ' . optional($complaint->consumer)->last_name) }}">
                                                <i class="bi bi-chat-left-text"></i> View Message
                                            </button>
                                            @if (!empty($complaint->attachment_path))
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-secondary view-complaint-attachment-btn"
                                                    data-attachment-url="{{ route('admin.complaints.attachment', $complaint->id) }}">
                                                    <i class="bi bi-paperclip"></i> View File
                                                </button>
                                            @else
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    disabled>
                                                    <i class="bi bi-paperclip"></i> No File
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No consumer complaints found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Complaint Message Viewer Modal -->
<div id="complaintMessageModal" class="modal fade" tabindex="-1" aria-labelledby="complaintMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 id="complaintMessageModalLabel" class="modal-title">
                    <i class="bi bi-chat-left-text me-2"></i>Complaint Message
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <p id="complaintMessageConsumer" class="text-muted mb-2 small"></p>
                <div id="complaintMessageContent" class="complaint-message-content"></div>
            </div>
        </div>
    </div>
</div>

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
    const complaintMessageModalEl = document.getElementById('complaintMessageModal');
    const complaintMessageContentEl = document.getElementById('complaintMessageContent');
    const complaintMessageConsumerEl = document.getElementById('complaintMessageConsumer');
    const complaintAttachmentModalEl = document.getElementById('complaintAttachmentModal');
    const complaintAttachmentFrame = document.getElementById('complaintAttachmentFrame');
    let reopenComplaintsAfterMessage = false;
    let reopenComplaintsAfterAttachment = false;
    
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

    // SweetAlert2 Logout Confirmation - With reversed buttons
    $('#logoutLink').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Logout Confirmation',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d32f2f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Logout!',
            cancelButtonText: 'Cancel',
            reverseButtons: false, // This reverses the button order
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Logging out...',
                    text: 'Please wait while we securely log you out.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit the logout form
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

    $('.view-complaint-message-btn').on('click', function() {
        if (!complaintMessageModalEl || !complaintMessageContentEl) {
            return;
        }

        const complaintMessage = $(this).data('complaint-message');
        const consumerName = ($(this).data('consumer-name') || '').toString().trim();

        complaintMessageContentEl.textContent = complaintMessage || 'No message provided.';
        if (complaintMessageConsumerEl) {
            complaintMessageConsumerEl.textContent = consumerName ? `From: ${consumerName}` : 'From: Unknown Consumer';
        }

        reopenComplaintsAfterMessage = true;

        const complaintsModal = bootstrap.Modal.getInstance(complaintsModalEl);
        if (complaintsModal) {
            complaintsModal.hide();
        }
        bootstrap.Modal.getOrCreateInstance(complaintMessageModalEl).show();
    });

    $('#complaintMessageModal').on('hidden.bs.modal', function() {
        if (complaintMessageContentEl) {
            complaintMessageContentEl.textContent = '';
        }

        if (complaintMessageConsumerEl) {
            complaintMessageConsumerEl.textContent = '';
        }

        if (reopenComplaintsAfterMessage && complaintsModalEl) {
            bootstrap.Modal.getOrCreateInstance(complaintsModalEl).show();
        }
        reopenComplaintsAfterMessage = false;
    });

    $('.view-complaint-attachment-btn').on('click', function() {
        const attachmentUrl = $(this).data('attachment-url');
        if (!attachmentUrl || !complaintAttachmentModalEl || !complaintAttachmentFrame) {
            return;
        }

        complaintAttachmentFrame.src = attachmentUrl;
        reopenComplaintsAfterAttachment = true;

        const complaintsModal = bootstrap.Modal.getInstance(complaintsModalEl);
        if (complaintsModal) {
            complaintsModal.hide();
        }
        bootstrap.Modal.getOrCreateInstance(complaintAttachmentModalEl).show();
    });

    $('#complaintAttachmentModal').on('hidden.bs.modal', function() {
        if (complaintAttachmentFrame) {
            complaintAttachmentFrame.src = 'about:blank';
        }

        if (reopenComplaintsAfterAttachment && complaintsModalEl) {
            bootstrap.Modal.getOrCreateInstance(complaintsModalEl).show();
        }
        reopenComplaintsAfterAttachment = false;
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
<script src="{{ asset('js/complaint-notifications.js') }}"></script>
<script>
$(function () {
    initComplaintNotifications({ role: 'admin' });
});
</script>

</body>
</html>
