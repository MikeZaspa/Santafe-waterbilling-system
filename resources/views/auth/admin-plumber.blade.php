<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Santa Fe Water Billing System - Plumbers</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
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
            font-size: .875rem;
            color: #6c757d;
        }

        .content-wrapper {
            margin: 20px;
        }
        
        /* Add Plumber Button Styles */
        #addPlumberBtn {
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

        #addPlumberBtn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(211, 47, 47, 0.3);
        }

        #addPlumberBtn:active {
            transform: translateY(0);
            box-shadow: 0 2px 5px rgba(211, 47, 47, 0.2);
        }

        #addPlumberBtn i {
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
                <a id="plumberLink" class="nav-link active" href="admin-plumber">
                    <i class="bi bi-wrench"></i> Manage Plumber
                </a>
            </li>
            <li class="nav-item">
                <a id="accountantLink" class="nav-link" href="admin-accountant">
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
                <h2 class="header-title">Plumber Management</h2>
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
                    <h3 class="mb-0">Plumber Information</h3>
                    <button class="btn btn-primary" id="addPlumberBtn" data-bs-toggle="modal" data-bs-target="#plumberModal">
                        <i class="bi bi-plus-circle-fill me-2"></i>
                        Add New Plumber
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="plumbersTable">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Username</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Middle Name</th>
                            <th>Suffix</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th width="120">Status</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plumbers as $plumber)
                        <tr id="plumberRow_{{ $plumber->id }}">
                            <td class="fw-semibold">{{ $plumber->id }}</td>
                            <td>{{ $plumber->username }}</td>
                            <td>{{ $plumber->first_name }}</td>
                            <td>{{ $plumber->last_name }}</td>
                            <td>{{ $plumber->middle_name }}</td>
                            <td>{{ $plumber->suffix }}</td>
                            <td>{{ $plumber->contact_number }}</td>
                            <td>{{ $plumber->email ?? 'N/A' }}</td>
                            <td>{{ $plumber->address }}</td>  
                            <td>
                                <span class="badge 
                                    @if($plumber->status == 'active') badge-status-active
                                    @elseif($plumber->status == 'inactive') badge-status-inactive
                                    @else badge-status-busy @endif">
                                    <i class="bi 
                                        @if($plumber->status == 'active') bi-check-circle
                                        @elseif($plumber->status == 'inactive') bi-pause-circle
                                        @else bi-hourglass @endif"></i>
                                    {{ ucfirst($plumber->status) }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <button class="btn btn-action btn-warning edit-plumber" data-id="{{ $plumber->id }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-action btn-danger delete-plumber" data-id="{{ $plumber->id }}" title="Delete">
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

<!-- Plumber Modal (Add/Edit) -->
<div class="modal fade" id="plumberModal" tabindex="-1" aria-labelledby="plumberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Plumber</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="plumberForm">
                    <input type="hidden" id="plumberId">

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
                    
                    <!-- Password Confirmation Field (only shown when adding new plumber or changing password) -->
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
                <button type="button" class="btn btn-primary" id="savePlumber">Save Plumber</button>
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
                Are you sure you want to delete this plumber? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
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
        const header = $('.header');
        const sidebarToggle = $('#sidebarToggle');
        const mobileOverlay = $('.mobile-overlay');

        sidebarToggle.on('click', function() {
            sidebar.toggleClass('active');
            mobileOverlay.toggleClass('active');
            
            if (sidebar.hasClass('active')) {
                header.css('background-color', 'var(--overlay-color)');
                $('body').css('overflow', 'hidden');
            } else {
                header.css('background-color', 'white');
                $('body').css('overflow', '');
            }
        });
        
        mobileOverlay.on('click', function() {
            sidebar.removeClass('active');
            mobileOverlay.removeClass('active');
            header.css('background-color', 'white');
            $('body').css('overflow', '');
        });
        
        $('.sidebar-menu .nav-link').on('click', function() {
            if ($(window).width() < 992) {
                sidebar.removeClass('active');
                mobileOverlay.removeClass('active');
                header.css('background-color', 'white');
                $('body').css('overflow', '');
            }
        });
        
        $(window).on('resize', function() {
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
            const plumberId = $('#plumberId').val();
            const passwordValue = $(this).val();
            
            // If in edit mode and password is not empty, show confirmation
            if (plumberId && passwordValue.length > 0) {
                $('#passwordConfirmGroup').show();
                $('#password_confirmation').prop('required', true);
            } 
            // If in edit mode and password is empty, hide confirmation
            else if (plumberId && passwordValue.length === 0) {
                $('#passwordConfirmGroup').hide();
                $('#password_confirmation').prop('required', false).val('');
            }
            // In add mode, always show confirmation
            else if (!plumberId) {
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
        $('#plumbersTable').DataTable({
            responsive: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                search: "",
                searchPlaceholder: "Search plumbers...",
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
        $('#plumberModal').on('hidden.bs.modal', function() {
            $('#plumberForm')[0].reset();
            $('#plumberId').val('');
            $('#modalTitle').text('Add New Plumber');
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

        // Add Plumber button click
        $('#addPlumberBtn').click(function() {
            $('#modalTitle').text('Add New Plumber');
            $('#plumberId').val('');
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

        // Save Plumber (Add/Edit) - UPDATED
        $('#savePlumber').click(function() {
            // Get all form values
            const formData = {
                username: $('#username').val().trim(),
                password: $('#password').val(),
                first_name: $('#firstName').val().trim(),
                middle_name: $('#middleName').val().trim(),
                last_name: $('#lastName').val().trim(),
                suffix: $('#suffix').val().trim(),
                contact_number: $('#contactNumber').val().trim(),
                email: $('#email').val().trim(),
                address: $('#address').val().trim(),
                status: $('#status').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            const plumberId = $('#plumberId').val();
            const password = $('#password').val();
            const isEditMode = plumberId !== '';

            // Basic validation
            if (!formData.username || !formData.first_name || !formData.last_name || 
                !formData.contact_number || !formData.address || !formData.status || !formData.email) {
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

            // For new plumber, password is required
            if (!isEditMode && !password) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Password is required for new plumbers'
                });
                return;
            }
            
            // Password confirmation validation
            const passwordConfirmation = $('#password_confirmation').val();
            
            // For new plumber, password confirmation is required
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
            
            const url = plumberId ? `/admin-plumber/${plumberId}` : '/admin-plumber';
            const method = plumberId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function(response) {
                    $('#plumberModal').modal('hide');
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

        // Edit Plumber - UPDATED VERSION
        $(document).on('click', '.edit-plumber', function() {
            const plumberId = $(this).data('id');
            
            $.ajax({
                url: `/admin-plumber/${plumberId}/edit`,
                type: 'GET',
                success: function(response) {
                    $('#modalTitle').text('Edit Plumber');
                    $('#plumberId').val(response.id);
                    $('#username').val(response.username);
                    $('#firstName').val(response.first_name);
                    $('#middleName').val(response.middle_name);
                    $('#lastName').val(response.last_name);
                    $('#suffix').val(response.suffix);
                    $('#contactNumber').val(response.contact_number);
                    $('#email').val(response.email || '');
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
                    
                    $('#plumberModal').modal('show');
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Failed to fetch plumber data'
                    });
                }
            });
        });

        // Delete Plumber - WITH AUTO RENUMBERING
        let deletePlumberId = null;

        $(document).on('click', '.delete-plumber', function() {
            deletePlumberId = $(this).data('id');
            $('#deleteModal').modal('show');
        });

        $('#confirmDelete').click(function() {
            if (!deletePlumberId) return;
            
            $.ajax({
                url: `/admin-plumber/${deletePlumberId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    
                    // Remove row
                    $('#plumberRow_' + deletePlumberId).remove();
                    
                    // Re-number all remaining rows sequentially starting from 1
                    renumberPlumberRows();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    deletePlumberId = null;
                },
                error: function(xhr) {
                    $('#deleteModal').modal('hide');
                    let errorMessage = xhr.responseJSON?.message || 'Failed to delete plumber';
                    
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
        function renumberPlumberRows() {
            $('#plumbersTable tbody tr').each(function(index) {
                // Set first cell to sequential number starting from 1
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
@include('auth.partials.admin-complaints-widget')
<script src="{{ asset('js/complaint-notifications.js') }}"></script>
<script>
$(function () {
    initComplaintNotifications({ role: 'admin' });
});
</script>

</body>
</html>
