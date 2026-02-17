<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Santa Fe Water Billing System - Accountants</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="image/santalogo.png">
    <!-- Custom CSS -->
    <style>
        :root {
           --primary-color: #0d6efd;
            --primary-light: #6a59ffff;
            --primary-dark: #9a0007;
            --sidebar-bg: #f8f9fa;
            --sidebar-text: rgba(0,0,0,0.8);
            --sidebar-hover: rgba(0,0,255,0.1);
            --overlay-color: rgba(7, 7, 7, 0.1);
            --header-height: 70px;
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
            font-size: 1.25rem;
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

        /* Add Accountant Button Styles */
        #addAccountantBtn {
            background-color: var(--primary-color);
            border: none;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 5px rgba(211, 47, 47, 0.2);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        #addAccountantBtn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(211, 47, 47, 0.3);
        }

        #addAccountantBtn:active {
            transform: translateY(0);
            box-shadow: 0 2px 5px rgba(211, 47, 47, 0.2);
        }

        #addAccountantBtn i {
            font-size: 1.1rem;
            margin-right: 8px;
        }
                
        /* Enhanced Table Styles */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            padding: 25px;
            margin-top: 25px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            width: 100%;
            overflow: hidden;
        }

        .table-title {
            color: var(--primary-dark);
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-title h3 {
            font-weight: 600;
            color: blue;
            margin: 0;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            --bs-table-striped-bg: rgba(211, 47, 47, 0.02);
            --bs-table-hover-bg: rgba(211, 47, 47, 0.05);
            margin-bottom: 0;
            width: 100%;
            table-layout: auto;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom-width: 2px;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            border-color: rgba(0, 0, 0, 0.03);
            white-space: nowrap;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Enhanced Badges */
        .badge {
            font-weight: 500;
            padding: 6px 10px;
            font-size: 0.75rem;
            border-radius: 4px;
            text-transform: capitalize;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        .badge i {
            font-size: 0.65rem;
        }

        .badge-status-active {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .badge-status-inactive {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .badge-status-busy {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        /* Action Buttons */
        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-action i {
            font-size: 0.9rem;
        }

        .btn-action:hover {
            transform: scale(1.1);
        }

        .btn-action + .btn-action {
            margin-left: 8px;
        }

        /* Search Box */
        .search-box {
            min-width: 250px;
        }

        .search-box .form-control {
            border-right: 0;
        }

        .search-box .btn {
            border-left: 0;
            background-color: white;
        }

        .search-box .btn:hover {
            background-color: #f8f9fa;
        }

        /* Pagination */
        .dataTables_paginate .paginate_button {
            padding: 6px 12px;
            border-radius: 6px;
            margin: 0 2px;
            border: 1px solid transparent;
        }

        .dataTables_paginate .paginate_button.current {
            background: var(--primary-color);
            color: white !important;
            border-color: var(--primary-color);
        }

        .dataTables_paginate .paginate_button:hover {
            background: rgba(211, 47, 47, 0.1);
            color: var(--primary-color) !important;
            border-color: rgba(211, 47, 47, 0.2);
        }

        /* Info Text */
        .dataTables_info {
            padding-top: 12px !important;
            color: #6c757d !important;
            font-size: 0.875rem;
        }
        
        /* Modal Styles */
        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }

        .modal-footer .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .modal-footer .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .form-label.required:after {
            content: " *";
            color: var(--primary-color);
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
        
        /* Session management styles */
        .session-timer {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            font-size: 0.8rem;
            color: var(--text-light);
            z-index: 1000;
            display: none;
        }

        .session-timer.warning {
            border-color: var(--warning);
            color: var(--warning);
        }

        .session-timer.danger {
            border-color: var(--error);
            color: var(--error);
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
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadein {
            animation: fadeIn 0.6s ease-out forwards;
        }
        
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }

        .login-logo {
            width: 100px;       
            height: 100px;      
            border-radius: 50%; 
            object-fit: cover;  
        }

        /* Better table alignment */
        .table td, .table th {
            vertical-align: middle;
        }

        /* Ensure table cells don't wrap unnecessarily */
        .table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }
        
        /* Password toggle */
        .password-toggle {
            cursor: pointer;
        }
        
        /* Password confirmation styling */
        .password-confirm-group {
            margin-top: 10px;
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
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Mobile Overlay -->
<div class="mobile-overlay"></div>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <div id="sidebarHeader" class="sidebar-header text-center">
        <img src="{{ asset('image/santafe.png') }}" class="login-logo img-fluid mb-3">
        <h1 id="sidebarTitle" class="h5">Santa Fe Water Billing</h1>
    </div>
    <nav id="sidebarMenu" class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a id="dashboardLink" class="nav-link" href="admin-dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a id="consumersLink" class="nav-link" href="admin-consumer">
                    <i class="bi bi-people"></i> Manage Consumers
                </a>
            </li>
            <li class="nav-item">
                <a id="accountsLink" class="nav-link " href="admin-consumer-form">
                    <i class="bi bi-person-badge"></i> Manage Accounts
                </a>
            </li>
            <li class="nav-item">
                <a id="plumberLink" class="nav-link" href="admin-plumber">
                    <i class="bi bi-wrench"></i> Manage Plumber
                </a>
            </li>
            <li class="nav-item">
                <a id="accountantLink" class="nav-link active" href="admin-accountant">
                    <i class="bi bi-cash-stack"></i> Manage Accountant
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="admin-announcement">
                    <i class="bi bi-megaphone"></i> Announcements
                </a>
            </li>
        </ul>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <button id="sidebarToggle" class="btn d-lg-none me-3 mobile-menu-toggle">
                <i class="bi bi-list"></i>
            </button>
             <div>
                <h2 class="header-title">Accountant Management</h2>
                <p class="header-subtitle">Santa Fe Water Billing System</p>
            </div>
        </div>
        
        <div class="header-right">
            <div class="position-relative me-3 d-none d-sm-block">
                <i class="bi bi-bell fs-5"></i>
            </div>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-none d-md-inline">Admin</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
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
   
    <div class="content-wrapper">
        <div class="table-container animate-fadein">
            <div class="table-title">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h3 class="mb-0">Accountant Management</h3>
                    <button class="btn btn-primary" id="addAccountantBtn" data-bs-toggle="modal" data-bs-target="#accountantModal">
                        <i class="bi bi-plus-circle-fill me-2"></i>
                        Add New Accountant
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="accountantsTable">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Middle Name</th>
                            <th>Suffix</th>
                            <th>Contact Number</th>
                            <th>Address</th>
                            <th width="120">Status</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accountants as $accountant)
                        <tr id="accountantRow_{{ $accountant->id }}">
                            <td class="fw-semibold">{{ $accountant->id }}</td>
                            <td>{{ $accountant->username }}</td>
                            <td>{{ $accountant->email }}</td>
                            <td>{{ $accountant->first_name }}</td>
                            <td>{{ $accountant->last_name }}</td>
                            <td>{{ $accountant->middle_name }}</td>
                            <td>{{ $accountant->suffix }}</td>
                            <td>{{ $accountant->contact_number }}</td>
                            <td>{{ $accountant->address }}</td>  
                            <td>
                                <span class="badge 
                                    @if($accountant->status == 'active') badge-status-active
                                    @elseif($accountant->status == 'inactive') badge-status-inactive
                                    @else badge-status-busy @endif">
                                    <i class="bi 
                                        @if($accountant->status == 'active') bi-check-circle
                                        @elseif($accountant->status == 'inactive') bi-pause-circle
                                        @else bi-hourglass @endif"></i>
                                    {{ ucfirst($accountant->status) }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <button class="btn btn-action btn-warning edit-accountant" data-id="{{ $accountant->id }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-action btn-danger delete-accountant" data-id="{{ $accountant->id }}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Accountant Modal (Add/Edit) -->
<div class="modal fade" id="accountantModal" tabindex="-1" aria-labelledby="accountantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Accountant</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="accountantForm">
                    <input type="hidden" id="accountantId">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label required">Username</label>
                                <input type="text" class="form-control" id="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label required">Email</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="password" class="form-label" id="passwordLabel">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password">
                                    <button class="btn btn-outline-secondary password-toggle" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text" id="passwordHelp">Leave blank to keep current password</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password Confirmation Field (only shown when adding new accountant or changing password) -->
                    <div class="row password-confirm-group" id="passwordConfirmGroup">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                    <button class="btn btn-outline-secondary password-toggle-confirm" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="firstName" class="form-label required">First Name</label>
                        <input type="text" class="form-control" id="firstName" required>
                    </div>
                    <div class="mb-3">
                        <label for="middleName" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="middleName">
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label required">Last Name</label>
                        <input type="text" class="form-control" id="lastName" required>
                    </div>
                    <div class="mb-3">
                        <label for="suffix" class="form-label">Suffix</label>
                        <input type="text" class="form-control" id="suffix" placeholder="e.g., Jr., Sr., III">
                    </div>

                    <div class="mb-3">
                        <label for="contactNumber" class="form-label required">Contact Number</label>
                        <input type="tel" class="form-control" id="contactNumber" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label required">Address</label>
                        <input type="text" class="form-control" id="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label required">Status</label>
                        <select class="form-select" id="status" required>
                            <option value="" selected disabled>Select status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveAccountant">Save Accountant</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this accountant? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Session Timer Display -->
<div class="session-timer" id="sessionTimer">
    <i class="fas fa-clock me-2"></i>
    Session expires in: <span id="sessionTimeDisplay">240:00</span>
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
    // Session management variables
    const sessionTimer = document.getElementById('sessionTimer');
    const sessionTimeDisplay = document.getElementById('sessionTimeDisplay');
    let sessionTimeout; // Will store the timeout ID
    let warningTimeout; // Will store the warning timeout ID
    let sessionInterval; // Will store the interval ID for updating the display
    const sessionDuration = 4 * 60 * 60 * 1000; // 4 hours in milliseconds
    const warningTime = 30 * 1000; // 30 seconds before expiry to show warning
    let sessionStartTime;
    let sessionExpiryTime;
    let isSessionActive = false;
    
    // Initialize session management
    function initSessionManagement() {
        // Set up event listeners to track user activity
        document.addEventListener('mousemove', resetSessionTimer);
        document.addEventListener('mousedown', resetSessionTimer);
        document.addEventListener('keypress', resetSessionTimer);
        document.addEventListener('scroll', resetSessionTimer);
        document.addEventListener('touchstart', resetSessionTimer);
        document.addEventListener('click', resetSessionTimer);
        
        // Start session immediately on page load
        startSession();
    }
    
    // Start a new session after page load
    function startSession() {
        isSessionActive = true;
        sessionStartTime = new Date();
        sessionExpiryTime = new Date(sessionStartTime.getTime() + sessionDuration);
        
        // Show the session timer
        sessionTimer.style.display = 'block';
        updateSessionDisplay();
        
        // Set up the session expiry timer
        clearTimeout(sessionTimeout);
        sessionTimeout = setTimeout(() => {
            endSession();
        }, sessionDuration);
        
        // Set up the warning timer
        clearTimeout(warningTimeout);
        warningTimeout = setTimeout(() => {
            showSessionWarning();
        }, sessionDuration - warningTime);
        
        // Set up the interval to update the display
        clearInterval(sessionInterval);
        sessionInterval = setInterval(() => {
            updateSessionDisplay();
            
            // Check if session is about to expire
            const now = new Date();
            const timeLeft = sessionExpiryTime - now;
            
            if (timeLeft <= warningTime && timeLeft > 0) {
                sessionTimer.classList.add('warning');
            } else if (timeLeft <= 30000) { // Last 30 seconds
                sessionTimer.classList.remove('warning');
                sessionTimer.classList.add('danger');
            }
        }, 1000); // Update every second
    }
    
    // Reset the session timer on user activity
    function resetSessionTimer() {
        if (!isSessionActive) return;
        
        // Clear existing timers
        clearTimeout(sessionTimeout);
        clearTimeout(warningTimeout);
        clearInterval(sessionInterval);
        
        // Reset the session
        sessionStartTime = new Date();
        sessionExpiryTime = new Date(sessionStartTime.getTime() + sessionDuration);
        
        // Reset the timer display
        sessionTimer.classList.remove('warning', 'danger');
        updateSessionDisplay();
        
        // Set up new timers
        sessionTimeout = setTimeout(() => {
            endSession();
        }, sessionDuration);
        
        warningTimeout = setTimeout(() => {
            showSessionWarning();
        }, sessionDuration - warningTime);
        
        sessionInterval = setInterval(() => {
            updateSessionDisplay();
            
            // Check if session is about to expire
            const now = new Date();
            const timeLeft = sessionExpiryTime - now;
            
            if (timeLeft <= warningTime && timeLeft > 0) {
                sessionTimer.classList.add('warning');
            } else if (timeLeft <= 30000) { // Last 30 seconds
                sessionTimer.classList.remove('warning');
                sessionTimer.classList.add('danger');
            }
        }, 1000);
    }
    
    // Update the session time display
    function updateSessionDisplay() {
        if (!isSessionActive) return;
        
        const now = new Date();
        const timeLeft = Math.max(0, sessionExpiryTime - now);
        
        // Convert to minutes and seconds
        const minutes = Math.floor(timeLeft / 60000);
        const seconds = Math.floor((timeLeft % 60000) / 1000);
        
        // Format as MM:SS
        sessionTimeDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    
    // Show session warning
    function showSessionWarning() {
        Swal.fire({
            title: 'Session Expiring Soon',
            html: 'Your session will expire in <strong>30 seconds</strong> due to inactivity.<br><br>Would you like to extend your session?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Extend Session',
            cancelButtonText: 'Log Out',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Extend the session
                resetSessionTimer();
                
                Swal.fire({
                    title: 'Session Extended',
                    text: 'Your session has been extended for another 4 hours.',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                // Log out
                endSession();
            }
        });
    }
    
    // End the session
    function endSession() {
        isSessionActive = false;
        
        // Clear all timers
        clearTimeout(sessionTimeout);
        clearTimeout(warningTimeout);
        clearInterval(sessionInterval);
        
        // Hide the session timer
        sessionTimer.style.display = 'none';
        
        // Show session expired message
        Swal.fire({
            title: 'Session Expired',
            text: 'Your session has expired due to inactivity. Please log in again.',
            icon: 'info',
            confirmButtonColor: '#0d6efd',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(() => {
            // Redirect to logout endpoint
            performLogout();
        });
    }
    
    // Initialize session management on page load
    initSessionManagement();
    
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
    
    // Password toggle functionality
    $('.password-toggle').click(function() {
        const passwordInput = $('#password');
        const icon = $(this).find('i');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
    
    // Password confirmation toggle functionality
    $('.password-toggle-confirm').click(function() {
        const passwordInput = $('#password_confirmation');
        const icon = $(this).find('i');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    // Handle password input to show/hide confirmation field dynamically
    $('#password').on('input', function() {
        const accountantId = $('#accountantId').val();
        const passwordValue = $(this).val();
        
        // If in edit mode and password is not empty, show confirmation
        if (accountantId && passwordValue.length > 0) {
            $('#passwordConfirmGroup').show();
            $('#password_confirmation').prop('required', true);
        } 
        // If in edit mode and password is empty, hide confirmation
        else if (accountantId && passwordValue.length === 0) {
            $('#passwordConfirmGroup').hide();
            $('#password_confirmation').prop('required', false).val('');
        }
        // In add mode, always show confirmation
        else if (!accountantId) {
            $('#passwordConfirmGroup').show();
            $('#password_confirmation').prop('required', true);
        }
    });

    // Prevent numbers in name fields
    $('#firstName, #middleName, #lastName, #suffix').on('input', function() {
        // Remove any numbers from input
        this.value = this.value.replace(/[0-9]/g, '');
    });

    // Enhanced contact number validation
    $('#contactNumber').on('input', function() {
        // Remove any non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Ensure it starts with 09 and limit to 11 digits
        if (this.value.length > 0 && !this.value.startsWith('09')) {
            // If it doesn't start with 09, prepend 09
            this.value = '09' + this.value.substring(2);
        }
        
        // Limit to 11 digits
        if (this.value.length > 11) {
            this.value = this.value.substring(0, 11);
        }
        
        // Visual feedback for validation
        if (this.value.length > 0 && this.value.length < 11) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Phone number must be 11 digits starting with 09</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    // Username validation - minimum 6 characters, letters and underscores only
    $('#username').on('input', function() {
        // Only allow letters and underscore (no numbers)
        this.value = this.value.replace(/[^a-zA-Z_]/g, '');
        
        // Check minimum length
        if (this.value.length > 0 && this.value.length < 6) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Username must be at least 6 characters long and contain only letters and underscores</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    // Initialize DataTable with automatic row numbering
    $('#accountantsTable').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
            search: "",
            searchPlaceholder: "Search accountants...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            emptyTable: "<div class='text-center'> No data available in table</div>",     
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        initComplete: function() {
            $('.dataTables_filter input').addClass('form-control');
            $('.dataTables_length select').addClass('form-select');
        },
        // Add automatic row numbering
        columnDefs: [{
            targets: 0,
            render: function(data, type, row, meta) {
                // Show sequential row numbers that update automatically
                if (type === 'display') {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
                return data;
            }
        }]
    });

    // Reset form when modal is closed
    $('#accountantModal').on('hidden.bs.modal', function() {
        $('#accountantForm')[0].reset();
        $('#accountantId').val('');
        $('#modalTitle').text('Add New Accountant');
        $('#passwordLabel').text('Password');
        $('#passwordHelp').text('');
        $('#password').attr('placeholder', '');
        $('#passwordConfirmGroup').show();
        $('#email').removeClass('is-invalid');
        $('#username').removeClass('is-invalid');
        $('#username').next('.invalid-feedback').remove();
        $('#contactNumber').removeClass('is-invalid');
        $('#contactNumber').next('.invalid-feedback').remove();
    });

    // Add Accountant button click
    $('#addAccountantBtn').click(function() {
        $('#modalTitle').text('Add New Accountant');
        $('#accountantId').val('');
        $('#passwordLabel').text('Password');
        $('#passwordHelp').text('');
        $('#password').attr('placeholder', 'Enter password').prop('required', true);
        $('#passwordConfirmGroup').show();
        $('#password_confirmation').prop('required', true);
    });

    // Email validation
    $('#email').on('input', function() {
        const email = this.value;
        if (email && !validateEmail(email)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    // Save Accountant (Add/Edit)
    $('#saveAccountant').click(function() {
        // Get all form values
        const formData = {
            username: $('#username').val().trim(),
            email: $('#email').val().trim(),
            password: $('#password').val(),
            first_name: $('#firstName').val().trim(),
            middle_name: $('#middleName').val().trim(),
            last_name: $('#lastName').val().trim(),
            suffix: $('#suffix').val().trim(),
            contact_number: $('#contactNumber').val().trim(),
            address: $('#address').val().trim(),
            status: $('#status').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        const accountantId = $('#accountantId').val();
        const password = $('#password').val();
        const isEditMode = accountantId !== '';

        // Basic validation
        if (!formData.username || !formData.email || !formData.first_name || !formData.last_name || 
            !formData.contact_number || !formData.address || !formData.status) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill all required fields'
            });
            return;
        }

        // Username minimum length validation
        if (formData.username.length < 6) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Username must be at least 6 characters long'
            });
            return;
        }

        // For new accountant, password is required
        if (!isEditMode && !password) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Password is required for new accountants'
            });
            return;
        }
        
        // Password confirmation validation
        const passwordConfirmation = $('#password_confirmation').val();
        
        // For new accountant, password confirmation is required
        if (!isEditMode && password !== passwordConfirmation) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Password confirmation does not match'
            });
            return;
        }
        
        // For editing, if password is provided, confirmation is required
        if (isEditMode && password && password !== passwordConfirmation) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Password confirmation does not match'
            });
            return;
        }

        // If password is provided in edit mode, include confirmation
        if (password) {
            formData.password_confirmation = passwordConfirmation;
        }

        // Email validation
        if (formData.email && !validateEmail(formData.email)) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please enter a valid email address'
            });
            return;
        }

        // If editing and password is empty, remove it from form data
        if (isEditMode && !password) {
            delete formData.password;
        }

        // Username validation (letters and underscores only)
        const usernameRegex = /^[a-zA-Z_]+$/;
        if (!usernameRegex.test(formData.username)) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Username can only contain letters and underscores'
            });
            return;
        }

        // Enhanced phone number validation - exactly 11 digits starting with 09
        if (formData.contact_number.length !== 11 || !formData.contact_number.startsWith('09')) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Phone number must be exactly 11 digits starting with 09'
            });
            return;
        }
        
        const url = accountantId ? `/admin-accountant/${accountantId}` : '/admin-accountant';
        const method = accountantId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#accountantModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON?.message || 'Something went wrong!';
                if (xhr.responseJSON?.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });

    // Edit Accountant
    $(document).on('click', '.edit-accountant', function() {
        const accountantId = $(this).data('id');
        
        $.ajax({
            url: `/admin-accountant/${accountantId}/edit`,
            type: 'GET',
            success: function(response) {
                $('#modalTitle').text('Edit Accountant');
                $('#accountantId').val(response.id);
                $('#username').val(response.username);
                $('#email').val(response.email);
                $('#firstName').val(response.first_name);
                $('#middleName').val(response.middle_name);
                $('#lastName').val(response.last_name);
                $('#suffix').val(response.suffix);
                $('#contactNumber').val(response.contact_number);
                $('#address').val(response.address);
                $('#status').val(response.status);
                
                // Set up password fields for editing
                $('#passwordLabel').text('Password');
                $('#passwordHelp').text('Leave blank to keep current password');
                $('#password').attr('placeholder', '').prop('required', false);
                
                // Hide confirmation field initially
                $('#passwordConfirmGroup').hide();
                $('#password_confirmation').prop('required', false);
                
                // Clear password fields when editing
                $('#password').val('');
                $('#password_confirmation').val('');
                
                $('#accountantModal').modal('show');
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to fetch accountant data'
                });
            }
        });
    });

    // Delete Accountant
    let deleteAccountantId = null;

    $(document).on('click', '.delete-accountant', function() {
        deleteAccountantId = $(this).data('id');
        $('#deleteModal').modal('show');
    });

    $('#confirmDelete').click(function() {
        if (!deleteAccountantId) return;
        
        $.ajax({
            url: `/admin-accountant/${deleteAccountantId}`,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                
                // Remove the row
                $('#accountantRow_' + deleteAccountantId).remove();
                
                // Re-number all remaining rows sequentially starting from 1
                renumberTableRows();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                deleteAccountantId = null;
            },
            error: function(xhr) {
                $('#deleteModal').modal('hide');
                let errorMessage = xhr.responseJSON?.message || 'Failed to delete accountant';
                
                if (xhr.responseJSON?.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });

    // Function to renumber table rows sequentially
    function renumberTableRows() {
        $('#accountantsTable tbody tr').each(function(index) {
            // Set the first cell to sequential number starting from 1
            $(this).find('td:first').text(index + 1);
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

        $.ajax({
            url: '/logout',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                window.location.href = '/admin-login';
            },
            error: function(xhr) {
                window.location.href = '/admin-login';
            }
        });
    }
});
</script>

</body>
</html>
