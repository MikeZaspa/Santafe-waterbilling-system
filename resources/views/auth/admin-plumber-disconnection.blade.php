<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Santa Fe Water Billing System - Disconnections</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="icon" type="image/png" href="image/santafe.png">
    <style>
        :root {
            --primary-color: #d32f2f;
            --primary-light: #ff6659;
            --primary-dark: #9a0007;
            --sidebar-bg: linear-gradient(180deg, #d32f2f 0%, #9a0007 100%);
            --sidebar-text: rgba(255,255,255,0.9);
            --sidebar-hover: rgba(255,255,255,0.1);
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        .sidebar {
            width: 280px;
            background: #f8f9fa;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
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
            padding: 20px;
            width: 100%;
        }
        
        .main-content.active {
            margin-left: 280px;
            width: calc(100% - 280px);
        }
        
        .header {
            height: 70px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 1040;
            background: white;
            padding: 0 15px;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            padding: 1rem;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
            width: 100%;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }
        
        .btn-success:hover {
            background-color: #157347;
            border-color: #146c43;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(211, 47, 47, 0.25);
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
            background-color: rgba(0, 0, 0, 0.5);
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
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadein {
            animation: fadeIn 0.6s ease-out forwards;
        }
        
        /* Mobile Card View Styles */
        .mobile-card-view {
            display: none;
        }
        
        .disconnection-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 15px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }
        
        .disconnection-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .disconnection-card-header {
            background-color: #f8f9fa;
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .disconnection-card-body {
            padding: 15px;
        }
        
        .disconnection-card-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .disconnection-card-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .disconnection-card-label {
            font-weight: 500;
            color: #6c757d;
            flex: 1;
        }
        
        .disconnection-card-value {
            flex: 2;
            text-align: right;
        }
        
        .disconnection-card-actions {
            padding: 10px 15px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        
        /* No data message styles */
        .no-data-container {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
        
        .no-data-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }
        
        .no-data-text {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .no-data-subtext {
            font-size: 0.9rem;
            color: #adb5bd;
        }
        
        /* Responsive styles */
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
            .desktop-table-view {
                display: none;
            }
            
            .mobile-card-view {
                display: block;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .card-header h5 {
                margin-bottom: 0;
            }
            
            .table th, .table td {
                padding: 0.5rem;
                font-size: 0.85rem;
            }
            
            .modal-dialog {
                margin: 0.5rem auto;
            }
            
            .modal-content {
                border-radius: 0;
            }
            
            .modal-body {
                padding: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .header {
                height: 60px;
                padding: 0 10px;
            }
            
            .sidebar-header {
                padding: 1rem;
            }
            
            .login-logo {
                width: 70px;
                height: 70px;
            }
            
            .sidebar-menu .nav-link {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            
            .mobile-header-title {
                font-size: 0.9rem;
                max-width: 120px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .position-relative.me-3 {
                display: none !important;
            }
            
            .dropdown-toggle span {
                display: none;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .table th, .table td {
                padding: 0.3rem;
                font-size: 0.8rem;
            }
            
            .modal-dialog {
                width: 95%;
                max-width: none;
                margin: 0.5rem auto;
            }
            
            .modal-body {
                padding: 0.75rem;
            }
            
            .form-control, .form-select {
                font-size: 0.9rem;
                padding: 0.375rem 0.75rem;
            }
            
            .disconnection-card-header {
                padding: 10px 12px;
            }
            
            .disconnection-card-body {
                padding: 12px;
            }
            
            .disconnection-card-actions {
                padding: 8px 12px;
            }
            
            .no-data-container {
                padding: 1.5rem;
            }
            
            .no-data-icon {
                font-size: 2.5rem;
            }
            
            .no-data-text {
                font-size: 1rem;
            }
        }
        
        /* DataTables responsive */
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 150px !important;
        }
        
        @media (max-width: 767px) {
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                float: none;
                text-align: left;
            }
            
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                margin-left: 0;
                margin-top: 0.5em;
            }
            
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
            }
        }
        
        /* Disconnection status styling */
        .status-disconnected {
            color: #dc3545;
            font-weight: 600;
        }
        
        .status-reconnected {
            color: #198754;
            font-weight: 600;
        }
        
        /* Action buttons */
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Mobile Overlay -->
<div class="mobile-overlay"></div>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header text-center">
        <img src="{{ asset('image/santafe.png') }}" class="login-logo img-fluid mb-3">
        <h1 class="h5">Santa Fe Water Billing</h1>
    </div>
    
    <nav class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="admin-plumber-dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin-plumber-consumer">
                    <i class="bi bi-people"></i> Reading
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="admin-plumber-disconnection">
                    <i class="bi bi-x-circle"></i> Disconnection
                </a>
            </li>
        </ul>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <header class="header d-flex align-items-center">
        <button id="sidebarToggle" class="btn d-lg-none me-3 mobile-menu-toggle">
            <i class="bi bi-list"></i>
        </button>
        <h2 class="h5 mb-0 mobile-header-title">Disconnection Records</h2>
        
        <div class="ms-auto d-flex align-items-center">
            <div class="position-relative me-3 d-none d-sm-block">
                <i class="bi bi-bell fs-5"></i>
            </div>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <span>Plumber</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="#" id="logoutBtn">
                            <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    
    <div class="container-fluid mt-3 mt-md-4">
        <div class="row">
            <div class="col-12">
                <div class="card animate-fadein">
                    <div class="card-header">
                        <h5 class="mb-0">Disconnection Records</h5>
                    </div>
                    <div class="card-body">
                        <!-- Desktop Table View -->
                        <div class="table-responsive desktop-table-view">
                            <table class="table table-hover" id="disconnectionTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Consumer</th>
                                        <th>Meter No.</th>
                                        <th>Reason</th>
                                        <th>Disconnection Date</th>
                                        <th>Reconnection Date</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($disconnections) > 0)
                                        @foreach($disconnections as $disconnection)
                                        <tr>
                                            <td>{{ $disconnection->id }}</td>
                                            <td>{{ $disconnection->consumer->first_name }} {{ $disconnection->consumer->last_name }}</td>
                                            <td>{{ $disconnection->billing->meter_no }}</td>
                                            <td>{{ $disconnection->reason }}</td>
                                            <td>{{ $disconnection->disconnection_date->format('M d, Y') }}</td>
                                            <td>{{ $disconnection->reconnection_date ? $disconnection->reconnection_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                @if($disconnection->status === 'disconnected')
                                                    <span class="status-disconnected">Disconnected</span>
                                                @else
                                                    <span class="status-reconnected">Reconnected</span>
                                                @endif
                                            </td>
                                            <td>{{ $disconnection->notes ?? 'N/A' }}</td>
                                            <td>
                                                <div class="action-buttons">
                                                    @if($disconnection->status === 'disconnected')
                                                    <button class="btn btn-success btn-sm reconnect-btn" 
                                                            data-id="{{ $disconnection->id }}"
                                                            data-consumer="{{ $disconnection->consumer->first_name }} {{ $disconnection->consumer->last_name }}"
                                                            data-meter="{{ $disconnection->billing->meter_no }}">
                                                        <i class="bi bi-plug"></i> Reconnect
                                                    </button>
                                                    @else
                                                    <span class="text-muted">Reconnected</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="no-data-container">
                                                    <i class="bi bi-inbox no-data-icon"></i>
                                                    <p class="no-data-text">No disconnection records found</p>
                                                    <p class="no-data-subtext">There are no disconnection records to display</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Mobile Card View -->
                        <div class="mobile-card-view" id="mobileDisconnectionCards">
                            @if(count($disconnections) > 0)
                                @foreach($disconnections as $disconnection)
                                <div class="disconnection-card" data-id="{{ $disconnection->id }}">
                                    <div class="disconnection-card-header">
                                        <span>ID: {{ $disconnection->id }}</span>
                                        @if($disconnection->status === 'disconnected')
                                            <span class="status-disconnected">Disconnected</span>
                                        @else
                                            <span class="status-reconnected">Reconnected</span>
                                        @endif
                                    </div>
                                    <div class="disconnection-card-body">
                                        <div class="disconnection-card-row">
                                            <span class="disconnection-card-label">Consumer:</span>
                                            <span class="disconnection-card-value">{{ $disconnection->consumer->first_name }} {{ $disconnection->consumer->last_name }}</span>
                                        </div>
                                        <div class="disconnection-card-row">
                                            <span class="disconnection-card-label">Meter No:</span>
                                            <span class="disconnection-card-value">{{ $disconnection->billing->meter_no }}</span>
                                        </div>
                                        <div class="disconnection-card-row">
                                            <span class="disconnection-card-label">Reason:</span>
                                            <span class="disconnection-card-value">{{ $disconnection->reason }}</span>
                                        </div>
                                        <div class="disconnection-card-row">
                                            <span class="disconnection-card-label">Disconnection Date:</span>
                                            <span class="disconnection-card-value">{{ $disconnection->disconnection_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="disconnection-card-row">
                                            <span class="disconnection-card-label">Reconnection Date:</span>
                                            <span class="disconnection-card-value">{{ $disconnection->reconnection_date ? $disconnection->reconnection_date->format('M d, Y') : 'N/A' }}</span>
                                        </div>
                                        <div class="disconnection-card-row">
                                            <span class="disconnection-card-label">Notes:</span>
                                            <span class="disconnection-card-value">{{ $disconnection->notes ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="disconnection-card-actions">
                                        @if($disconnection->status === 'disconnected')
                                        <button class="btn btn-success btn-sm reconnect-btn" 
                                                data-id="{{ $disconnection->id }}"
                                                data-consumer="{{ $disconnection->consumer->first_name }} {{ $disconnection->consumer->last_name }}"
                                                data-meter="{{ $disconnection->billing->meter_no }}">
                                            <i class="bi bi-plug"></i> Reconnect
                                        </button>
                                        @else
                                        <span class="text-muted">Reconnected</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="no-data-container">
                                    <i class="bi bi-inbox no-data-icon"></i>
                                    <p class="no-data-text">No disconnection records found</p>
                                    <p class="no-data-subtext">There are no disconnection records to display</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reconnection Modal -->
<div class="modal fade" id="reconnectionModal" tabindex="-1" aria-labelledby="reconnectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reconnectionModalLabel">Reconnect Consumer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reconnectionForm">
                    <input type="hidden" id="disconnectionId" name="disconnection_id">
                    <div class="mb-3">
                        <label for="consumerName" class="form-label">Consumer</label>
                        <input type="text" class="form-control" id="consumerName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="meterNumber" class="form-label">Meter Number</label>
                        <input type="text" class="form-control" id="meterNumber" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="reconnectionDate" class="form-label">Reconnection Date</label>
                        <input type="date" class="form-control" id="reconnectionDate" name="reconnection_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="reconnectionNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="reconnectionNotes" name="reconnection_notes" rows="3" placeholder="Add any notes about the reconnection"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmReconnection">Confirm Reconnection</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<!-- SweetAlert2 for notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
 $(document).ready(function() {
    // Mobile sidebar toggle functionality
    const sidebar = $('.sidebar');
    const mainContent = $('.main-content');
    const sidebarToggle = $('#sidebarToggle');
    const mobileOverlay = $('.mobile-overlay');
    
    sidebarToggle.on('click', function() {
        sidebar.toggleClass('active');
        mainContent.toggleClass('active');
        mobileOverlay.toggleClass('active');
        
        // Prevent scrolling when sidebar is open
        if (sidebar.hasClass('active')) {
            $('body').css('overflow', 'hidden');
        } else {
            $('body').css('overflow', '');
        }
    });
    
    // Close sidebar when clicking on overlay
    mobileOverlay.on('click', function() {
        sidebar.removeClass('active');
        mainContent.removeClass('active');
        mobileOverlay.removeClass('active');
        $('body').css('overflow', '');
    });
    
    // Close sidebar when clicking on a nav link (for mobile)
    $('.sidebar-menu .nav-link').on('click', function() {
        if ($(window).width() < 992) {
            sidebar.removeClass('active');
            mainContent.removeClass('active');
            mobileOverlay.removeClass('active');
            $('body').css('overflow', '');
        }
    });
    
    // Handle window resize
    $(window).on('resize', function() {
        // Close sidebar if window is resized to desktop size
        if ($(window).width() >= 992) {
            sidebar.removeClass('active');
            mainContent.removeClass('active');
            mobileOverlay.removeClass('active');
            $('body').css('overflow', '');
        }
    });

    // Initialize DataTable
    $('#disconnectionTable').DataTable({
        order: [[0, 'desc']],
        language: {
            lengthMenu: "Show _MENU_ entries",
            search: "Search:",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            },
            emptyTable: "No disconnection records found"
        }
    });

    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Helper functions for notifications
    function showSuccessAlert(title, message) {
        Swal.fire({
            icon: 'success',
            title: title,
            text: message,
            timer: 3000
        });
    }

    function showErrorAlert(title, message) {
        Swal.fire({
            icon: 'error',
            title: title,
            html: message
        });
    }

    function showErrorToast(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }
    
    // Logout functionality
    $('#logoutBtn').click(function(e) {
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
                // Perform logout - you can customize this based on your authentication system
                performLogout();
            }
        });
    });

    function performLogout() {
        // Show loading state
        Swal.fire({
            title: 'Signing Out...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Example: Send logout request to server
        // Replace this with your actual logout endpoint
        $.ajax({
            url: '/logout', // Your logout route
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Redirect to login page
                window.location.href = '/admin-login';
            },
            error: function(xhr) {
                // If AJAX fails, still redirect to login
                window.location.href = '/admin-login';
            }
        });
    }

    // Reconnection functionality
    const reconnectionModal = new bootstrap.Modal(document.getElementById('reconnectionModal'));
    
    // Handle reconnect button clicks - Updated to work with both table and card views
    $(document).on('click', '.reconnect-btn', function() {
        const disconnectionId = $(this).data('id');
        const consumerName = $(this).data('consumer');
        const meterNumber = $(this).data('meter');
        
        // Set modal values
        $('#disconnectionId').val(disconnectionId);
        $('#consumerName').val(consumerName);
        $('#meterNumber').val(meterNumber);
        
        // Set today's date as default for reconnection
        const today = new Date().toISOString().split('T')[0];
        $('#reconnectionDate').val(today);
        
        // Clear notes
        $('#reconnectionNotes').val('');
        
        // Show modal
        reconnectionModal.show();
    });
    
    // Handle confirm reconnection
    $('#confirmReconnection').click(function() {
        const disconnectionId = $('#disconnectionId').val();
        const reconnectionDate = $('#reconnectionDate').val();
        const notes = $('#reconnectionNotes').val();
        
        // Validate form
        if (!reconnectionDate) {
            showErrorToast('Please select a reconnection date');
            return;
        }
        
        // Show loading state
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Send AJAX request to update disconnection
        $.ajax({
            url: '/admin-plumber-disconnection/' + disconnectionId + '/reconnect',
            type: 'POST',
            data: {
                reconnection_date: reconnectionDate,
                notes: notes,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Close modal
                reconnectionModal.hide();
                
                if (response.success) {
                    // Show success message
                    showSuccessAlert('Success', response.message);
                    
                    // Reload page after a short delay to show updated status
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showErrorAlert('Error', response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while processing the reconnection';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showErrorAlert('Error', errorMessage);
            }
        });
    });
});
</script>
</body>
</html>