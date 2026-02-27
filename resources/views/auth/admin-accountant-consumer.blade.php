<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Santa Fe Water Billing System - Billing Management</title>
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
            --primary-color: #d32f2f;
            --primary-light: #ff6659;
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
        
        /* Sidebar Styles */
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

        /* Main Content */
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
           margin: 20px;
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
        
        /* Table Styles */
        .table-container {
            width: 100%;
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
            width: 100%;
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
            margin: 0;
            color: blue;
            font-size: 24px;
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

       /* Enhanced Badge Styles */
.badge {
    font-weight: 600;
    padding: 6px 12px;
    font-size: 0.75rem;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.badge-paid {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(40, 167, 69, 0.1) 100%);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.2);
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.1);
}

.badge-unpaid {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.1) 100%);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.2);
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.1);
}

.badge-overdue {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 193, 7, 0.1) 100%);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.2);
    box-shadow: 0 2px 4px rgba(255, 193, 7, 0.1);
}

/* Hover effects for better interactivity */
.badge-paid:hover {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.2) 0%, rgba(40, 167, 69, 0.15) 100%);
    transform: translateY(-1px);
}

.badge-unpaid:hover {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.2) 0%, rgba(220, 53, 69, 0.15) 100%);
    transform: translateY(-1px);
}

.badge-overdue:hover {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 193, 7, 0.15) 100%);
    transform: translateY(-1px);
}

/* Archive Badge Styles */
.badge-archived {
    background: linear-gradient(135deg, rgba(108, 117, 125, 0.15) 0%, rgba(108, 117, 125, 0.1) 100%);
    color: #6c757d;
    border: 1px solid rgba(108, 117, 125, 0.2);
    box-shadow: 0 2px 4px rgba(108, 117, 125, 0.1);
}

.badge-archived:hover {
    background: linear-gradient(135deg, rgba(108, 117, 125, 0.2) 0%, rgba(108, 117, 125, 0.15) 100%);
    transform: translateY(-1px);
}

/* Archive Tab Styles */
.nav-tabs .nav-link.active {
    color: var(--primary-color);
    border-color: var(--primary-color);
    font-weight: 600;
}

.nav-tabs .nav-link {
    color: #6c757d;
}

.nav-tabs .nav-link:hover {
    color: var(--primary-color);
    border-color: transparent;
}

/* Archive Table Styles */
.archived-row {
    background-color: rgba(108, 117, 125, 0.05);
}

.archived-row:hover {
    background-color: rgba(108, 117, 125, 0.08);
}

        /* Button Styles */
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

        /* Modal Styles */
        .modal-header {
            background-color: white;
            color: black;
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

        /* Receipt Modal Styles */
        .receipt-container {
            font-family: 'Courier New', monospace;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: white;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px dashed #ddd;
            padding-bottom: 15px;
        }
        .receipt-details {
            margin-bottom: 15px;
        }
        .receipt-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 20px;
            border-top: 2px dashed #ddd;
            padding-top: 15px;
            font-size: 0.9em;
            color: #666;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadein {
            animation: fadeIn 0.6s ease-out forwards;
        }
        
        .login-logo {
            width: 100px;       
            height: 100px;      
            border-radius: 50%; 
            object-fit: cover;  
        }

        /* Print styles for receipt */
@media print {
    body * {
        visibility: hidden;
    }
    #receiptContent, #receiptContent * {
        visibility: visible;
    }
    #receiptContent {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}

/* Penalty indicator */
.penalty-indicator {
    color: #dc3545;
    font-size: 0.8em;
    font-weight: bold;
}
/* Notification Styles */
.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: var(--primary-color);
    color: white;
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
    padding: 0;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.14);
    background: #fff;
}

.notification-list {
    max-height: 320px;
    overflow-y: auto;
    overflow-x: hidden;
}

.notification-item {
    display: block;
    text-decoration: none;
    color: #212529;
    padding: 14px 16px;
    border-bottom: 1px solid #f1f1f1;
    cursor: pointer;
    transition: background-color 0.2s;
    background: #f8f9fa;
}

.notification-item:hover {
    background-color: #f1f3f5;
}

.notification-item.unread {
    background-color: rgba(211, 47, 47, 0.05);
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-title {
    font-weight: 600;
    margin-bottom: 3px;
    font-size: .73rem;
    color: #1f2937;
    text-decoration: none;
}

.notification-message {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 5px;
    line-height: 1.4;
    word-break: break-word;
    text-decoration: none;
}

.notification-time {
    font-size: 0.75rem;
    color: #adb5bd;
    text-decoration: none;
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

.notification-icon.danger {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.notification-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 14px 16px 10px;
    border-bottom: 1px solid #e9ecef;
    background: #fff;
}

.notification-actions h6 {
    font-size: 1rem;
    font-weight: 500;
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
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Mobile Overlay -->
<div class="mobile-overlay"></div>

<div class="sidebar">
    <div class="sidebar-header text-center">
        <img src="{{ asset('image/santafe.png') }}" class="login-logo img-fluid mb-3">
        <h1 class="h5">Santa Fe Water Billing</h1>
    </div>
    
    <nav class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link " href="admin-accountant-dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="admin-accountant-consumer">
                    <i class="bi bi-people"></i> Billing
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
                <a class="nav-link" href="admin-accountant-reports">
                    <i class="bi bi-file-earmark-bar-graph"></i> Reports
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link" href="{{ route('paymentVerificationSection') }}">
                    <i class="bi bi-credit-card"></i> Payment Verification
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
                <h2 class="header-title">Billing Management</h2>
                <p class="header-subtitle">Santa Fe Water Billing System</p>
            </div>
        </div>
       
        <div class="header-right">
            <!-- Notification Bell for Admin -->
            <div class="position-relative me-3">
                <a href="#" class="text-decoration-none text-dark position-relative" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="notification-badge d-none" id="notificationBadge">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationBell">
                    <div class="notification-actions">
                        <h6 class="mb-0">Pending Payments</h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshNotificationsBtn">Refresh</button>
                    </div>
                    <div class="notification-list" id="notificationList">
                        <div class="notification-empty">No pending payments.</div>
                    </div>
                </div>
            </div>
            <!-- User Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <span>Accountant</span>
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
                    <h3 class="mb-0">Consumers Billing </h3>
                    <div>
                        <button class="btn btn-primary" id="addBillingBtn" data-bs-toggle="modal" data-bs-target="#billingModal">
                            <i class="bi bi-plus-circle-fill me-2"></i>
                            Create New Billing
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-3" id="billingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                        Active Billings
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archived" type="button" role="tab">
                        Archived Billings
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="billingTabsContent">
                <!-- Active Billings Tab -->
                <div class="tab-pane fade show active" id="active" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search consumer...">
                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="month" class="form-control" id="monthFilter">
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="billingTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Consumer</th>
                                    <th>Type</th>
                                    <th>Meter No.</th>
                                    <th>Due Date</th>
                                    <th>Previous Reading</th>
                                    <th>Current Reading</th>
                                    <th>Consumption</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Active billing data will be loaded here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Archived Billings Tab -->
                <div class="tab-pane fade" id="archived" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="archiveSearchInput" placeholder="Search archived consumer...">
                                <button class="btn btn-outline-secondary" type="button" id="archiveSearchBtn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-danger" id="emptyArchiveBtn">
                                <i class="bi bi-trash me-2"></i>
                                Empty Archive
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="archiveTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Consumer</th>
                                    <th>Type</th>
                                    <th>Meter No.</th>
                                    <th>Archived Date</th>
                                    <th>Archived By</th>
                                    <th>Reason</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Archived billing data will be loaded here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Archive Billing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="archiveForm">
                    <input type="hidden" id="archiveBillingId">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Consumer</label>
                        <input type="text" class="form-control" id="archiveConsumerName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason for Archiving</label>
                        <select class="form-select" id="archiveReason" required>
                            <option value="">Select Reason</option>
                            <option value="Meter Disconnected">Meter Disconnected</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3" id="otherReasonContainer" style="display: none;">
                        <label class="form-label fw-bold">Specify Reason</label>
                        <textarea class="form-control" id="otherReason" rows="2" placeholder="Please specify the reason..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes (Optional)</label>
                        <textarea class="form-control" id="archiveNotes" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmArchiveBtn">Archive</button>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Restore Billing</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to restore this billing record? It will be moved back to the active billings.</p>
                <input type="hidden" id="restoreBillingId">
                <div class="mb-3">
                    <label class="form-label fw-bold">Consumer</label>
                    <input type="text" class="form-control" id="restoreConsumerName" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmRestoreBtn">Restore</button>
            </div>
        </div>
    </div>
</div>

<!-- Empty Archive Confirmation Modal -->
<div class="modal fade" id="emptyArchiveModal" tabindex="-1" aria-labelledby="emptyArchiveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Empty Archive</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6 class="alert-heading">Warning!</h6>
                    <p class="mb-0">This action will permanently delete all archived billing records. This cannot be undone.</p>
                </div>
                <p>Are you absolutely sure you want to empty the archive?</p>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confirmEmptyArchive">
                    <label class="form-check-label" for="confirmEmptyArchive">
                        I understand this action is irreversible
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmEmptyArchiveBtn" disabled>Empty Archive</button>
            </div>
        </div>
    </div>
</div>

<!-- Simplified Billing Modal -->
<div class="modal fade" id="billingModal" tabindex="-1" aria-labelledby="billingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Create New Billing</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="billingForm">
                    <input type="hidden" id="billingId">
                    
                    <!-- Consumer Selection -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Consumer</label>
                        <div class="dropdown">
                            <button class="form-select text-start" type="button" id="consumerDropdownButton" data-bs-toggle="dropdown">
                                <span id="selectedConsumerText">Select Consumer</span>
                            </button>
                            <input type="hidden" id="consumer_id" required>
                            <ul class="dropdown-menu w-100 p-0">
                                <li class="p-2">
                                    <input type="text" class="form-control" id="consumerSearch" placeholder="Search...">
                                </li>
                                <li><hr class="my-1"></li>
                                <div id="consumerOptions" style="max-height: 200px; overflow-y: auto;">
                                    <div class="text-center p-3">
                                        <div class="spinner-border spinner-border-sm"></div>
                                        <span>Loading consumers...</span>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- Type and Meter No. -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Type</label>
                            <input type="text" class="form-control" id="type" placeholder="Auto-filled from consumer" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Meter No.</label>
                            <input type="text" class="form-control" id="meterNumber" placeholder="Enter meter number" readonly>
                        </div>
                    </div>

                    <!-- Billing Details -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Due Date</label>
                            <input type="date" class="form-control" id="dueDate" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <!-- Changed from dropdown to read-only input -->
                            <input type="text" class="form-control" id="status" readonly value="unpaid">
                        </div>
                    </div>

                    <!-- Water Readings -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Previous (m³)</label>
                            <input type="number" class="form-control" id="previousReading" step="0.01" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Current (m³)</label>
                            <input type="number" class="form-control" id="currentReading" step="0.01" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Consumption (m³)</label>
                            <input type="number" class="form-control" id="consumption" readonly>
                        </div>
                    </div>

                    <!-- Amounts -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Total Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="totalAmount" readonly>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveBilling">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="paymentForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="paymentModalLabel">Process Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="paymentBillingId" name="billing_id">
          <div class="mb-3">
            <label>Amount Due</label>
            <input type="text" id="paymentAmountDue" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label>Payment Amount</label>
            <input type="number" id="paymentAmount" class="form-control" min="0" step="0.01" required>
          </div>
          <div class="mb-3">
            <label>Change</label>
            <input type="text" id="paymentChange" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label>Payment Date</label>
            <input type="date" id="paymentDate" name="payment_date" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Submit Payment</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="receiptModalLabel">Payment Receipt</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="receipt-container" id="receiptContent">
          <!-- Receipt content will be loaded here -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="printReceiptBtn">Print Receipt</button>
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
<!-- Moment.js for date handling -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>
 $(document).ready(function() {
    const notificationBadge = $('#notificationBadge');
    const notificationList = $('#notificationList');

    function formatNotificationTime(value) {
        if (!value) return 'Just now';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return 'Just now';

        const diffSeconds = Math.floor((Date.now() - date.getTime()) / 1000);
        if (diffSeconds < 60) return `${Math.max(diffSeconds, 1)}s ago`;
        if (diffSeconds < 3600) return `${Math.floor(diffSeconds / 60)}m ago`;
        if (diffSeconds < 86400) return `${Math.floor(diffSeconds / 3600)}h ago`;
        if (diffSeconds < 604800) return `${Math.floor(diffSeconds / 86400)}d ago`;
        return date.toLocaleString();
    }

    function renderNotificationBadge(count) {
        if (count > 0) {
            notificationBadge.text(count > 99 ? '99+' : count).removeClass('d-none');
        } else {
            notificationBadge.addClass('d-none').text('0');
        }
    }

    function renderNotificationList(notifications) {
        if (!Array.isArray(notifications) || notifications.length === 0) {
            notificationList.html('<div class="notification-empty">No pending payments.</div>');
            return;
        }

        const notificationHtml = notifications.map(function(payment) {
            const amount = Number(payment.amount || 0).toFixed(2);
            return `
                <a href="#" class="notification-item payment-notification-item" data-id="${payment.id}">
                    <div class="notification-title">${payment.consumer_name || 'N/A'}</div>
                    <div class="notification-message">Meter: ${payment.meter_no || 'N/A'} | Ref: ${payment.reference_number || 'N/A'} | Amount: P${amount}</div>
                    <div class="notification-time">Submitted ${formatNotificationTime(payment.created_at)}</div>
                </a>
            `;
        }).join('');

        notificationList.html(notificationHtml);
    }

    function fetchPendingPaymentNotifications() {
        $.ajax({
            url: "{{ route('admin.payments.pending-notifications') }}",
            type: 'GET',
            data: { limit: 10 },
            success: function(response) {
                if (!response || !response.success) {
                    renderNotificationBadge(0);
                    renderNotificationList([]);
                    return;
                }

                const notifications = response.notifications || [];
                renderNotificationBadge(response.pending_count ?? notifications.length);
                renderNotificationList(notifications);
            },
            error: function() {
                renderNotificationBadge(0);
            }
        });
    }

    $('#refreshNotificationsBtn').on('click', function(e) {
        e.preventDefault();
        fetchPendingPaymentNotifications();
    });

    $(document).on('click', '.payment-notification-item', function(e) {
        e.preventDefault();
        const paymentId = $(this).data('id');
        if (paymentId) {
            window.location.href = `{{ route('paymentVerificationSection') }}?payment_id=${paymentId}`;
        }
    });

    fetchPendingPaymentNotifications();
    setInterval(fetchPendingPaymentNotifications, 15000);

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

    // Initialize Active Billings DataTable
    const activeTable = $('#billingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('accountant.billings.data') }}",
            type: 'GET',
            data: function(d) {
                d.status = $('#statusFilter').val();
                d.month = $('#monthFilter').val();
                d.payment_method = $('#paymentMethodFilter').val();
                d.archived = 0; // Only active records
            }
        },
        columns: [
            { 
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            { 
                data: 'consumer', 
                name: 'consumer.first_name',
                render: function(data, type, row) {
                    return data ? data.first_name + ' ' + data.last_name : 'N/A';
                }
            },
            { data: 'consumer_type', name: 'consumer_type' },
            { data: 'meter_no', name: 'meter_no' },
            { 
                data: 'due_date', 
                name: 'due_date',
                render: function(data) {
                    return data ? moment(data).format('MMM D, YYYY') : '';
                }
            },
            { data: 'previous_reading', name: 'previous_reading' },
            { data: 'current_reading', name: 'current_reading' },
            { data: 'consumption', name: 'consumption' },
            { 
                data: 'total_amount', 
                name: 'total_amount',
                render: function(data, type, row) {
                    // Handle various data types and edge cases
                    let amount = 0;
                    
                    if (typeof data === 'number') {
                        amount = data;
                    } else if (typeof data === 'string') {
                        // Remove currency symbol and commas if present
                        const cleanString = data.replace(/[₱,]/g, '').trim();
                        amount = parseFloat(cleanString) || 0;
                    } else if (data) {
                        amount = parseFloat(data) || 0;
                    }
                    
                    // Format with Philippine Peso symbol
                    let formattedAmount = '₱' + amount.toFixed(2);
                    
                    // Add penalty indicator if applicable
                    if (row.penalty_amount && row.penalty_amount > 0) {
                        formattedAmount += ' <span class="penalty-indicator">(+₱' + row.penalty_amount.toFixed(2) + ' penalty)</span>';
                    }
                    
                    return formattedAmount;
                }
            },
            {
                data: 'status',
                name: 'status',
                render: function(data, type, row) {
                    let badgeClass = '';
                    let icon = '';
                    
                    if (data === 'paid') {
                        badgeClass = 'badge-paid';
                        icon = '<i class="bi bi-check-circle-fill"></i>';
                    } 
                    else if (data === 'unpaid') {
                        badgeClass = 'badge-unpaid';
                        icon = '<i class="bi bi-exclamation-circle-fill"></i>';
                    }
                    else if (data === 'overdue') {
                        badgeClass = 'badge-overdue';
                        icon = '<i class="bi bi-clock-fill"></i>';
                    }
                    else {
                        badgeClass = 'badge-secondary';
                        icon = '<i class="bi bi-question-circle-fill"></i>';
                    }
                    
                    return `<span class="badge ${badgeClass}">${icon} ${data.toUpperCase()}</span>`;
                }
            },
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let paymentButton = '';
                    let receiptButton = '';
                    let isPaid = (row.status === 'paid');

                    // Only show payment button if status is unpaid or overdue
                    if (!isPaid) {
                        paymentButton = `<button class="btn btn-sm btn-success payment-btn" data-id="${data}">
                            <i class="bi bi-cash-coin"></i> Pay
                        </button>`;
                    }

                    // Only enable receipt button if the bill is paid
                    if (isPaid) {
                        receiptButton = `<button class="btn btn-sm btn-info receipt-btn" data-id="${data}">
                            <i class="bi bi-receipt"></i> Receipt
                        </button>`;
                    } else {
                        receiptButton = `<button class="btn btn-sm btn-secondary receipt-btn" data-id="${data}" disabled title="Bill must be paid first">
                            <i class="bi bi-receipt"></i> Receipt
                        </button>`;
                    }
                    
                    return `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary edit-btn" data-id="${data}" ${isPaid ? 'disabled' : ''}>
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-warning archive-btn" data-id="${data}" data-consumer="${row.consumer ? row.consumer.first_name + ' ' + row.consumer.last_name : 'N/A'}">
                            <i class="bi bi-archive"></i> Archive
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${data}" ${isPaid ? 'disabled' : ''}>
                            <i class="bi bi-trash"></i> Delete
                        </button>
                        ${paymentButton}
                        ${receiptButton}
                    </div>
                    `;
                }
            }
        ]
    });

    // Initialize Archived Billings DataTable
    const archiveTable = $('#archiveTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('accountant.billings.archived.data') }}",
            type: 'GET',
            data: function(d) {
                d.search = $('#archiveSearchInput').val();
            }
        },
        columns: [
            { 
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            { 
                data: 'consumer', 
                name: 'consumer.first_name',
                render: function(data, type, row) {
                    return data ? data.first_name + ' ' + data.last_name : 'N/A';
                }
            },
            { data: 'consumer_type', name: 'consumer_type' },
            { data: 'meter_no', name: 'meter_no' },
            { 
                data: 'archived_at', 
                name: 'archived_at',
                render: function(data) {
                    return data ? moment(data).format('MMM D, YYYY') : '';
                }
            },
            { 
                data: 'archived_by', 
                name: 'archived_by',
                render: function(data) {
                    return data ? data.name : 'System';
                }
            },
            { 
                data: 'archive_reason', 
                name: 'archive_reason',
                render: function(data, type, row) {
                    let reason = data || 'No reason specified';
                    if (data === 'Other' && row.archive_notes) {
                        reason = row.archive_notes;
                    }
                    return `<span title="${reason}">${reason.length > 30 ? reason.substring(0, 30) + '...' : reason}</span>`;
                }
            },
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    const consumerName = row.consumer ? row.consumer.first_name + ' ' + row.consumer.last_name : 'N/A';
                    
                    return `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-success restore-btn" data-id="${data}" data-consumer="${consumerName}">
                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                        </button>
                        <button class="btn btn-sm btn-danger delete-archive-btn" data-id="${data}">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                        <button class="btn btn-sm btn-info view-archive-details-btn" data-id="${data}">
                            <i class="bi bi-eye"></i> Details
                        </button>
                    </div>
                    `;
                }
            }
        ],
        createdRow: function(row, data, dataIndex) {
            $(row).addClass('archived-row');
        }
    });

    // Apply filters for active table
    $('#statusFilter, #monthFilter').change(function() {
        activeTable.ajax.reload();
    });

    // Search button for active table
    $('#searchBtn').click(function() {
        activeTable.search($('#searchInput').val()).draw();
    });

    // Archive button click handler
    $(document).on('click', '.archive-btn', function() {
        const billingId = $(this).data('id');
        const consumerName = $(this).data('consumer');
        
        $('#archiveBillingId').val(billingId);
        $('#archiveConsumerName').val(consumerName);
        $('#archiveModal').modal('show');
    });

    // Archive reason change handler
    $('#archiveReason').change(function() {
        if ($(this).val() === 'Other') {
            $('#otherReasonContainer').show();
            $('#otherReason').prop('required', true);
        } else {
            $('#otherReasonContainer').hide();
            $('#otherReason').prop('required', false);
        }
    });

    // Confirm archive button
    $('#confirmArchiveBtn').click(function() {
        const billingId = $('#archiveBillingId').val();
        const reason = $('#archiveReason').val();
        const notes = reason === 'Other' ? $('#otherReason').val() : $('#archiveNotes').val();
        
        if (!reason) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select a reason for archiving'
            });
            return;
        }
        
        if (reason === 'Other' && !$('#otherReason').val()) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please specify the reason for archiving'
            });
            return;
        }
        
        $.ajax({
            url: `/accountant/billings/${billingId}/archive`,
            type: 'POST',
            data: {
                reason: reason,
                notes: notes,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#archiveModal').modal('hide');
                    activeTable.ajax.reload();
                    archiveTable.ajax.reload();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Archived',
                        text: response.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to archive billing'
                });
            }
        });
    });

    // Restore button click handler
    $(document).on('click', '.restore-btn', function() {
        const billingId = $(this).data('id');
        const consumerName = $(this).data('consumer');
        
        $('#restoreBillingId').val(billingId);
        $('#restoreConsumerName').val(consumerName);
        $('#restoreModal').modal('show');
    });

    // Confirm restore button
    $('#confirmRestoreBtn').click(function() {
        const billingId = $('#restoreBillingId').val();
        
        $.ajax({
            url: `/accountant/billings/${billingId}/restore`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#restoreModal').modal('hide');
                    activeTable.ajax.reload();
                    archiveTable.ajax.reload();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Restored',
                        text: response.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to restore billing'
                });
            }
        });
    });

    // Empty archive button
    $('#emptyArchiveBtn').click(function() {
        $('#emptyArchiveModal').modal('show');
    });

    // Confirm empty archive checkbox
    $('#confirmEmptyArchive').change(function() {
        $('#confirmEmptyArchiveBtn').prop('disabled', !$(this).is(':checked'));
    });

    // Confirm empty archive button
    $('#confirmEmptyArchiveBtn').click(function() {
        $.ajax({
            url: '/accountant/billings/empty-archive',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#emptyArchiveModal').modal('hide');
                    archiveTable.ajax.reload();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Archive Emptied',
                        text: response.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to empty archive'
                });
            }
        });
    });

    // Archive search button
    $('#archiveSearchBtn').click(function() {
        archiveTable.search($('#archiveSearchInput').val()).draw();
    });

    // View archive details button
    $(document).on('click', '.view-archive-details-btn', function() {
        const billingId = $(this).data('id');
        
        // You can implement a modal to show detailed archive information
        // This would show the original billing data plus archive metadata
        $.ajax({
            url: `/accountant/billings/${billingId}/archive-details`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show archive details in a modal
                    showArchiveDetails(response.data);
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load archive details'
                });
            }
        });
    });

    // Delete archive record button
    $(document).on('click', '.delete-archive-btn', function() {
        const billingId = $(this).data('id');
        
        Swal.fire({
            title: 'Delete Archived Record?',
            text: "This will permanently delete this archived billing record. This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete permanently!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/accountant/billings/${billingId}/force-delete`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            archiveTable.ajax.reload();
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to delete archived record'
                        });
                    }
                });
            }
        });
    });

    // Function to show archive details
    function showArchiveDetails(data) {
        const modalContent = `
            <div class="modal fade" id="archiveDetailsModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">Archive Details</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Billing Information</h6>
                                    <p><strong>Consumer:</strong> ${data.consumer.first_name} ${data.consumer.last_name}</p>
                                    <p><strong>Meter No:</strong> ${data.meter_no}</p>
                                    <p><strong>Type:</strong> ${data.consumer_type}</p>
                                    <p><strong>Last Reading:</strong> ${data.current_reading}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Archive Information</h6>
                                    <p><strong>Archived Date:</strong> ${moment(data.archived_at).format('MMM D, YYYY h:mm A')}</p>
                                    <p><strong>Archived By:</strong> ${data.archived_by ? data.archived_by.name : 'System'}</p>
                                    <p><strong>Reason:</strong> ${data.archive_reason}</p>
                                    <p><strong>Notes:</strong> ${data.archive_notes || 'None'}</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        $('#archiveDetailsModal').remove();
        
        // Append and show modal
        $('body').append(modalContent);
        $('#archiveDetailsModal').modal('show');
    }

    // Initialize modal when opened
    $('#billingModal').on('show.bs.modal', function() {
        resetForm();
        fetchConsumers();
    });

    // Reset all form fields
    function resetForm() {
        $('#billingForm')[0].reset();
        $('#billingId').val('');
        $('#billingModal .modal-title').text('Create New Billing');
        $('#saveBilling').html('Save').prop('disabled', true);
        $('#selectedConsumerText').text('Select Consumer');
        $('#consumer_id').val('');
        $('#type').val('');
        $('#meterNumber').val('');
        $('#previousReading').val('');
        $('#currentReading').val('');
        $('#consumption').val('');
        $('#totalAmount').val('');
        
        // Set default due date to today so billing month matches current reading month
        const today = new Date();
        $('#dueDate').val(today.toISOString().split('T')[0]);
    }

    // Function to fetch consumers and populate dropdown
    function fetchConsumers() {
        const selectedMonth = $('#dueDate').val();

        $.ajax({
            url: '/accountant/billings/consumers',
            type: 'GET',
            dataType: 'json',
            data: {
                month: selectedMonth
            },
            beforeSend: function() {
                $('#consumerOptions').html(`
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-hourglass"></i>
                        <span class="ms-2">Loading consumers...</span>
                    </div>
                `);
            },
            success: function(response) {
                if (response && response.length > 0) {
                    populateConsumerOptions(response);
                    setupSearchFunctionality();
                } else {
                    $('#consumerOptions').html(`
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-info-circle"></i>
                            <span class="ms-2">No consumers with monthly reading found</span>
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                console.error('Error loading consumers:', xhr.responseText);
                $('#consumerOptions').html(`
                    <div class="text-center py-3 text-danger">
                        <i class="bi bi-exclamation-circle"></i>
                        <span class="ms-2">Failed to load consumers</span>
                    </div>
                `);
            }
        });
    }
    
    // Function to populate consumer options
    function populateConsumerOptions(consumers) {
        const optionsContainer = $('#consumerOptions');
        optionsContainer.empty();
        
        consumers.forEach((consumer) => {
            let fullName = consumer.first_name || '';
            if (consumer.middle_name) fullName += ' ' + consumer.middle_name;
            fullName += ' ' + (consumer.last_name || '');
            if (consumer.suffix) fullName += ' ' + consumer.suffix;
            
            const optionItem = $(`
                <li>
                    <a class="dropdown-item consumer-option" href="#" data-id="${consumer.id}">
                        ${fullName.trim()} 
                        <small class="text-muted d-block">Meter: ${consumer.meter_no || 'N/A'}</small>
                    </a>
                </li>
            `);
            
            optionItem.on('click', function(e) {
                e.preventDefault();
                selectConsumer(consumer);
            });
            
            optionsContainer.append(optionItem);
        });
    }

    // Function to handle consumer selection
    function selectConsumer(consumer) {
        let fullName = consumer.first_name || '';
        if (consumer.middle_name) fullName += ' ' + consumer.middle_name;
        fullName += ' ' + (consumer.last_name || '');
        if (consumer.suffix) fullName += ' ' + consumer.suffix;
        fullName = fullName.trim();
        
        // Update consumer display
        $('#selectedConsumerText').text(fullName);
        $('#consumer_id').val(consumer.id);
        $('#type').val(consumer.consumer_type || 'N/A');
        $('#meterNumber').val(consumer.meter_no || 'N/A');
        
        // Close dropdown
        $('.dropdown-menu').removeClass('show');
        
        // Fetch the consumer's last reading
        fetchLastReading(consumer.id);
    }

    // Function to fetch last reading data
    function fetchLastReading(consumerId) {
        const selectedMonth = $('#dueDate').val();

        $.ajax({
            url: `/billing/last-reading/${consumerId}`,
            type: 'GET',
            dataType: 'json',
            data: {
                month: selectedMonth
            },
            beforeSend: function() {
                $('#previousReading').val('Loading...');
                $('#currentReading').val('Loading...');
                $('#consumption').val('Loading...');
                $('#saveBilling').prop('disabled', true);
            },
            success: function(response) {
                if (response.last_reading) {
                    const last = response.last_reading;
                    $('#previousReading').val(parseFloat(last.previous_reading).toFixed(2));
                    $('#currentReading').val(parseFloat(last.current_reading).toFixed(2));
                    calculateConsumption();
                    $('#saveBilling').prop('disabled', false);
                } else {
                    $('#previousReading').val('0.00');
                    $('#currentReading').val('0.00');
                    $('#consumption').val('0.00');
                    $('#totalAmount').val('0.00');
                    $('#saveBilling').prop('disabled', true);
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Monthly Reading',
                        text: 'No plumber reading found for the selected billing month.'
                    });
                }
                calculateWaterBill();
            },
            error: function(xhr) {
                console.error('Error fetching last reading:', xhr.responseText);
                $('#previousReading').val('Error');
                $('#currentReading').val('Error');
                $('#consumption').val('Error');
                $('#totalAmount').val('0.00');
                $('#saveBilling').prop('disabled', true);
                alert('Failed to retrieve meter readings.');
            }
        });
    }

    // Function to calculate consumption automatically
    function calculateConsumption() {
        const prevReading = parseFloat($('#previousReading').val()) || 0;
        const currReading = parseFloat($('#currentReading').val()) || 0;
        
        if (isNaN(prevReading)) {
            $('#consumption').val('0.00');
            return;
        }
        
        if (isNaN(currReading)) {
            $('#consumption').val('0.00');
            return;
        }
        
        if (currReading < prevReading) {
            alert('Current reading cannot be less than previous reading');
            $('#currentReading').val(prevReading.toFixed(2));
            $('#consumption').val('0.00');
            return;
        }
        
        const consumption = currReading - prevReading;
        $('#consumption').val(consumption.toFixed(2));
    }

    // Calculate water bill
    function calculateWaterBill() {
        const consumerType = $('#type').val();
        const consumption = parseFloat($('#consumption').val()) || 0;
        
        if (!consumerType || isNaN(consumption)) {
            return;
        }
        
        $.ajax({
            url: '/water-rates/calculate',
            type: 'POST',
            data: {
                type: consumerType,
                consumption: consumption,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#totalAmount').val(response.amount.toFixed(2));
                    calculateChange();
                } else {
                    alert(response.message || 'Error calculating water bill');
                    $('#totalAmount').val('0.00');
                }
            },
            error: function(xhr) {
                console.error('Error calculating water bill:', xhr.responseText);
                $('#totalAmount').val('0.00');
                alert('Failed to calculate water bill. Please try again.');
            }
        });
    }

    // Setup search functionality for consumers
    function setupSearchFunctionality() {
        $('#consumerSearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.consumer-option').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).parent().toggle(text.includes(searchTerm));
            });
        });
    }

 $('#saveBilling').click(function() {
    const formData = {
        consumer_id: $('#consumer_id').val(),
        current_reading: $('#currentReading').val(),
        due_date: $('#dueDate').val(),
        status: $('#status').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    const billingId = $('#billingId').val();
    const url = billingId ? `/accountant/billings/${billingId}` : '/accountant/billings';
    const method = billingId ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        type: method,
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#billingModal').modal('hide');
                activeTable.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                const errorType = xhr.responseJSON.type;
                
                if (errorType === 'paid') {
                    // Show the paid billing details using SweetAlert
                    const billing = xhr.responseJSON.data;
                    showPaidBillingDetails(billing);
                } else if (errorType === 'unpaid') {
                    // Show specific message for unpaid billing with OK button
                    Swal.fire({
                        icon: 'warning',
                        title: 'Unpaid Billing Exists',
                        html: errors.unpaid.join('<br>'),
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Close the billing modal
                            $('#billingModal').modal('hide');
                        }
                    });
                } else {
                    // Handle other validation errors
                    let errorMessages = '';
                    for (const field in errors) {
                        errorMessages += errors[field].join('<br>') + '<br>';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: errorMessages
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'An error occurred'
                });
            }
        }
    });
});


   // Function to show paid billing details
function showPaidBillingDetails(billing) {
    // Build consumer name
    const consumer = billing.consumer;
    let consumerName = consumer.first_name || '';
    if (consumer.middle_name) consumerName += ' ' + consumer.middle_name;
    consumerName += ' ' + (consumer.last_name || '');
    if (consumer.suffix) consumerName += ' ' + consumer.suffix;
    
    // Check if payment_date exists and format it
    let paymentDate = 'N/A';
    if (billing.payment_date) {
        paymentDate = moment(billing.payment_date).format('MMM D, YYYY');
    } else if (billing.updated_at && billing.status === 'paid') {
        // If payment_date is not available but status is paid, use updated_at as fallback
        paymentDate = moment(billing.updated_at).format('MMM D, YYYY');
    }
    
    // Calculate total amount with penalty
    const totalAmount = parseFloat(billing.total_amount);
    const penaltyAmount = parseFloat(billing.penalty_amount || 0);
    const totalWithPenalty = totalAmount + penaltyAmount;
    
    // Check if payment was late
    const dueDate = moment(billing.due_date);
    const paidDate = moment(billing.payment_date || billing.updated_at);
    const isLatePayment = paidDate.isAfter(dueDate);
    
    // Calculate next month due date
    const nextMonth = new Date(billing.due_date);
    nextMonth.setMonth(nextMonth.getMonth() + 1);
    const formattedNextMonth = moment(nextMonth).format('MMMM YYYY');
    
    // Create HTML for billing details
    const billingDetails = `
        <div class="text-start">
            <div class="alert alert-info">
                <h6><i class="bi bi-info-circle me-2"></i>Billing Information</h6>
                <p>This consumer has already paid for this billing period.</p>
                ${isLatePayment ? '<p class="text-warning mb-0"><i class="bi bi-exclamation-triangle me-2"></i>This payment was made after the due date.</p>' : ''}
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Consumer:</strong> ${consumerName}</p>
                    <p><strong>Meter No:</strong> ${billing.meter_no}</p>
                    <p><strong>Type:</strong> ${billing.consumer_type}</p>
                    <p><strong>Due Date:</strong> ${moment(billing.due_date).format('MMM D, YYYY')}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Previous Reading:</strong> ${billing.previous_reading} m³</p>
                    <p><strong>Current Reading:</strong> ${billing.current_reading} m³</p>
                    <p><strong>Consumption:</strong> ${billing.consumption} m³</p>
                    <p><strong>Base Amount:</strong> ₱${totalAmount.toFixed(2)}</p>
                </div>
            </div>
            
            ${penaltyAmount > 0 ? `
            <div class="row mt-2">
                <div class="col-12">
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-clock me-2"></i>Late Payment Penalty</h6>
                        <p class="mb-1"><strong>Penalty Fee:</strong> ₱${penaltyAmount.toFixed(2)}</p>
                        <p class="mb-0"><strong>Total Amount Paid:</strong> ₱${totalWithPenalty.toFixed(2)}</p>
                    </div>
                </div>
            </div>
            ` : `
            <div class="row mt-2">
                <div class="col-12">
                    <p><strong>Total Amount Paid:</strong> ₱${totalAmount.toFixed(2)}</p>
                </div>
            </div>
            `}
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <p><strong>Payment Date:</strong> ${paymentDate}</p>
                    ${isLatePayment ? `<p class="text-danger"><strong>Days Late:</strong> ${paidDate.diff(dueDate, 'days')} days</p>` : ''}
                </div>
            </div>
            
            <div class="alert alert-warning mt-3">
                <p class="mb-0"><i class="bi bi-calendar me-2"></i>Next billing period: <strong>${formattedNextMonth}</strong></p>
            </div>
        </div>
    `;
    
    // Show SweetAlert with billing details (only OK button)
    Swal.fire({
        title: 'Paid Billing Details',
        html: billingDetails,
        icon: 'info',
        showCloseButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#3085d6',
        width: '700px',
        customClass: {
            closeButton: 'swal2-close-button-custom'
        }
    });
}

    $(document).on('click', '.edit-btn', function() {
        const billingId = $(this).data('id');
        
        // Show loading state
        $('#billingModal').modal('show');
        $('#selectedConsumerText').html(`
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        
        $.ajax({
            url: `/accountant/billings/${billingId}/edit`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const billing = response.data.billing;
                    const consumer = response.data.consumer;
                    
                    // Fill the form with consumer data
                    $('#billingId').val(billing.id);
                    
                    // Build the full name with proper handling of optional fields
                    let fullName = consumer.first_name || '';
                    if (consumer.middle_name) fullName += ' ' + consumer.middle_name;
                    fullName += ' ' + (consumer.last_name || '');
                    if (consumer.suffix) fullName += ' ' + consumer.suffix;
                    fullName = fullName.trim();
                    
                    $('#selectedConsumerText').text(fullName);
                    $('#consumer_id').val(consumer.id);
                    $('#type').val(consumer.consumer_type || 'N/A');
                    $('#meterNumber').val(consumer.meter_no || 'N/A');
                    
                    // Fill billing data
                    $('#dueDate').val(billing.due_date.split('T')[0]);
                    $('#previousReading').val(parseFloat(billing.previous_reading).toFixed(2));
                    $('#currentReading').val(parseFloat(billing.current_reading).toFixed(2));
                    $('#consumption').val(parseFloat(billing.consumption).toFixed(2));
                    $('#totalAmount').val(parseFloat(billing.total_amount).toFixed(2));
                    $('#status').val(billing.status || 'unpaid');
                    
                    // Update modal title and button text
                    $('#billingModal .modal-title').text('Edit Billing');
                    $('#saveBilling').html('<i class="bi bi-save me-2"></i> Update Billing').prop('disabled', false);
                }
            },
            error: function(xhr) {
                // Show error message in consumer display
                $('#selectedConsumerText').text('Error loading consumer');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to load billing data'
                });
            }
        });
    });

    // Delete billing - with validation for paid billings
 $(document).on('click', '.delete-btn', function() {
    const billingId = $(this).data('id');
    const $row = $(this).closest('tr');
    
    // Get billing status from the row data
    const status = $row.find('.badge').text().trim().toLowerCase();
    
    // Check if billing is paid
    if (status === 'paid') {
        Swal.fire({
            icon: 'error',
            title: 'Cannot Delete Paid Billing',
            html: `
                <p>This billing record has been paid and cannot be deleted.</p>
            `,
            confirmButtonText: 'Okay',
            confirmButtonColor: '#3085d6'
        });
        return;
    }

    // For unpaid/overdue billings, proceed with confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/accountant/billings/${billingId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        activeTable.ajax.reload(); // This refreshes the table
                        Swal.fire(
                            'Deleted!',
                            response.message,
                            'success'
                        );
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Validation error (paid billing)
                        Swal.fire({
                            icon: 'error',
                            title: 'Cannot Delete',
                            html: `
                                <p>${xhr.responseJSON.message}</p>
                                <p class="text-info mt-2">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Please use the <strong>Archive</strong> button to maintain financial records.
                                </p>
                            `,
                            confirmButtonText: 'Okay',
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to delete billing'
                        });
                    }
                }
            });
        }
    });
});

    // Handle payment button click
    function calculateLatePenalty(dueDate, paymentDate) {
        if (!dueDate || !paymentDate) return 0;

        const due = moment(dueDate).startOf('day');
        const paid = moment(paymentDate).startOf('day');
        if (!due.isValid() || !paid.isValid()) return 0;

        // Apply fixed ₱10 penalty only after 3 full days from due date.
        return paid.isAfter(due.clone().add(3, 'days')) ? 10 : 0;
    }

    function updatePaymentAmountDue() {
        const baseAmount = parseFloat($('#paymentForm').data('base-amount')) || 0;
        const existingPenalty = parseFloat($('#paymentForm').data('existing-penalty')) || 0;
        const dueDate = $('#paymentForm').data('due-date');
        const paymentDate = $('#paymentDate').val();

        const latePenalty = calculateLatePenalty(dueDate, paymentDate);
        const penaltyAmount = Math.max(existingPenalty, latePenalty);
        const totalDue = baseAmount + penaltyAmount;

        $('#paymentAmountDue').val('₱' + totalDue.toFixed(2));
        $('.penalty-info').remove();

        if (penaltyAmount > 0) {
            $('#paymentAmountDue').after(`
                <div class="alert alert-warning mt-2 mb-2 p-2 penalty-info">
                    <small>
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Includes ₱${penaltyAmount.toFixed(2)} late payment penalty
                    </small>
                </div>
            `);
        }
    }

 $(document).on('click', '.payment-btn', function(e) {
    e.preventDefault();
    const billingId = $(this).data('id');
    
    console.log('Payment button clicked for billing ID:', billingId);

    // Show modal and reset fields
    $('#paymentModal').modal('show');
    $('#paymentAmountDue').val('Loading...');
    $('#paymentAmount').val('');
    $('#paymentChange').val('₱0.00');
    $('#paymentBillingId').val(billingId);
    $('#paymentDate').val(new Date().toISOString().split('T')[0]);

    // Load billing details via AJAX
    $.ajax({
        url: `/accountant/billings/${billingId}/details`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Billing details response:', response);
            if (response.success) {
                const billing = response.data;
                const totalAmount = parseFloat(billing.total_amount);
                $('#paymentBillingId').val(billing.id);
                $('#paymentForm').data('base-amount', totalAmount);
                $('#paymentForm').data('existing-penalty', parseFloat(billing.penalty_amount || 0));
                $('#paymentForm').data('due-date', billing.due_date);
                updatePaymentAmountDue();
            } else {
                console.error('Failed to load billing details');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load billing data.'
                });
                $('#paymentModal').modal('hide');
            }
        },
        error: function(xhr) {
            console.error('AJAX error:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message || 'Failed to load billing data.'
            });
            $('#paymentModal').modal('hide');
        }
    });
});
    // Keep track of paid billing IDs
    let paidBillings = new Set();

    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();

        const billingId = $('#paymentBillingId').val();
        const totalAmount = parseFloat($('#paymentAmountDue').val().replace(/[₱,]/g, '')) || 0;
        const formData = {
            billing_id: billingId,
            payment_amount: parseFloat($('#paymentAmount').val()),
            payment_date: $('#paymentDate').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Prevent duplicate payment
        if (paidBillings.has(billingId)) {
            Swal.fire('Error', 'This bill has already been paid.', 'error');
            return;
        }

        // Validate: must be a number and > 0
        if (isNaN(formData.payment_amount) || formData.payment_amount <= 0) {
            Swal.fire('Error', 'Please enter a valid amount', 'error');
            return;
        }

        // Validate: must not be less than amount due
        if (formData.payment_amount < totalAmount) {
            Swal.fire('Error', 'Payment cannot be less than the total amount due (₱' + totalAmount.toFixed(2) + ')', 'error');
            return;
        }

        // Submit
        const submitBtn = $(this).find('[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

        $.ajax({
            url: '/payments/process',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Mark this billing as paid
                    paidBillings.add(billingId);

                    $('#paymentModal').modal('hide');
                    // Reload the table to update the status and buttons
                    $('#billingTable').DataTable().ajax.reload(null, false);
                    Swal.fire('Success', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Payment failed', 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('Submit Payment');
            }
        });
    });

    // Recalculate due amount/penalty when payment date changes.
    $(document).on('change', '#paymentDate', function() {
        updatePaymentAmountDue();
        $('#paymentAmount').trigger('input');
    });

    // Handle real-time change calculation
    $(document).on('input', '#paymentAmount', function() {
        const totalAmount = parseFloat($('#paymentAmountDue').val().replace(/[₱,]/g, '')) || 0;
        const paymentAmount = parseFloat($(this).val()) || 0;
        const change = paymentAmount - totalAmount;

        $('#paymentChange').val(change >= 0 ? '₱' + change.toFixed(2) : '₱0.00');
    });

    // Optional: Clear change when modal closes
    $('#paymentModal').on('hidden.bs.modal', function () {
        $('#paymentChange').val('₱0.00');
        $('#paymentAmount').val('');
        $('.penalty-info').remove();
        $('#paymentForm').removeData('base-amount existing-penalty due-date');
    });

    // Handle receipt button click
    $(document).on('click', '.receipt-btn', function(e) {
        e.preventDefault();
        const billingId = $(this).data('id');
        
        // First, check if the button is disabled
        if ($(this).prop('disabled')) {
            Swal.fire({
                icon: 'warning',
                title: 'Cannot Generate Receipt',
                text: 'This bill has not been paid yet. Please process the payment first.'
            });
            return;
        }
        
        // Show loading state
        $('#receiptModal').modal('show');
        $('#receiptContent').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Generating receipt...</p>
            </div>
        `);
        
        // Fetch billing details for receipt
        $.ajax({
            url: `/accountant/billings/${billingId}/receipt`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const billing = response.data;
                    generateReceipt(billing);
                } else {
                    // If the server says it's not paid, show an error
                    if (response.error && response.error === 'not_paid') {
                        $('#receiptModal').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Cannot Generate Receipt',
                            text: 'This bill has not been paid yet.'
                        });
                    } else {
                        $('#receiptContent').html(`
                            <div class="text-center py-4 text-danger">
                                <i class="bi bi-exclamation-circle"></i>
                                <p class="mt-2">Failed to generate receipt: ${response.message}</p>
                            </div>
                        `);
                    }
                }
            },
            error: function(xhr) {
                console.error('Error fetching receipt data:', xhr.responseText);
                $('#receiptContent').html(`
                    <div class="text-center py-4 text-danger">
                        <i class="bi bi-exclamation-circle"></i>
                        <p class="mt-2">Failed to load receipt data. Please try again.</p>
                    </div>
                `);
            }
        });
    });

    // Function to generate receipt HTML based on the provided template
    function generateReceipt(billing) {
        const paymentDate = billing.payment_date ? new Date(billing.payment_date) : new Date();
        const readingDate = new Date(billing.reading_date || billing.due_date);
        
        // Format dates
        const formattedPaymentDate = paymentDate.toLocaleDateString('en-PH', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).replace(/\//g, '-');
        
        const formattedReadingDate = readingDate.toLocaleDateString('en-PH', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).replace(/\//g, '-');
        
        const nextMonth = new Date(readingDate);
        nextMonth.setMonth(nextMonth.getMonth() + 1);
        const formattedNextMonth = nextMonth.toLocaleDateString('en-PH', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).replace(/\//g, '-');
        
        // Calculate due date (22nd of current month)
        const dueDate = new Date(readingDate);
        dueDate.setDate(22);
        const formattedDueDate = dueDate.toLocaleDateString('en-PH', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).replace(/\//g, '-');
        
        // Calculate disconnection date (25th of current month)
        const disconnectionDate = new Date(readingDate);
        disconnectionDate.setDate(25);
        const formattedDisconnectionDate = disconnectionDate.toLocaleDateString('en-PH', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).replace(/\//g, '-');
        
        // Build consumer name
        const consumer = billing.consumer;
        let consumerName = consumer.first_name || '';
        if (consumer.middle_name) consumerName += ' ' + consumer.middle_name;
        consumerName += ' ' + (consumer.last_name || '');
        if (consumer.suffix) consumerName += ' ' + consumer.suffix;
        
        // Generate bill number starting from 0001
        // If billing.id is not starting from 1, we'll calculate the sequential number
        const billNumber = String(billing.sequential_number || billing.id).padStart(4, '0');
        
        // Get current month name for bill month
        const monthNames = ["January", "February", "March", "April", "May", "June",
                           "July", "August", "September", "October", "November", "December"];
        const billMonth = `${readingDate.getDate()}-${monthNames[readingDate.getMonth()].substring(0, 3)}`;
        
        // Calculate total amount with penalty
        const totalAmount = parseFloat(billing.total_amount) + (billing.penalty_amount || 0);
        
        const receiptHTML = `
            <div class="receipt-container" style="font-family: Arial, sans-serif; max-width: 400px; margin: 0 auto; padding: 15px; border: 1px solid #000; background-color: white;">
                <div class="receipt-header" style="text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px;">
                    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                        <img src="{{ asset('image/santafe.png') }}" style="width: 60px; height: 60px; margin-right: 15px;" alt="Santa Fe Logo">
                        <div>
                            <h4 style="margin: 5px 0; font-size: 18px;">Santa Fe Water System and Management Board</h4>
                            <p style="margin: 3px 0; font-size: 14px;">Santa Fe New Municipal Hall</p>
                        </div>
                    </div>
                    <p style="margin: 3px 0; font-size: 14px;">PooC, Santa Fe, Cebu 6047</p>
                    <p style="margin: 3px 0; font-size: 14px;">CONTACT NO. 09469615234/09305694771</p>
                </div>
                
                <div style="text-align: center; margin: 10px 0;">
                    <h3 style="margin: 5px 0; text-decoration: underline;">STATEMENT OF ACCOUNT</h3>
                </div>
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 12px;">
                    <tr>
                        <td style="width: 30%; padding: 2px;"><strong>Account Type</strong></td>
                        <td style="width: 35%; padding: 2px;">${consumer.consumer_type || 'RESIDENTIAL'}</td>
                        <td style="width: 15%; padding: 2px;"><strong>Meter No.</strong></td>
                        <td style="width: 20%; padding: 2px;">${consumer.meter_no || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px;"><strong>Bill Num</strong></td>
                        <td style="padding: 2px;">: ${billNumber}</td>
                        <td style="padding: 2px;"><strong>Brand</strong></td>
                        <td style="padding: 2px;">:</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px;"><strong>Name</strong></td>
                        <td style="padding: 2px;">: ${consumerName.trim()}</td>
                        <td style="padding: 2px;"><strong>Bill Month</strong></td>
                        <td style="padding: 2px;">${billMonth}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px;"><strong>Address</strong></td>
                        <td style="padding: 2px;" colspan="3">: ${consumer.address || 'PooC, Santa Fe, Cebu'}</td>
                    </tr>
                </table>
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 12px;">
                    <tr>
                        <td colspan="2" style="padding: 2px;"><strong>Reading</strong></td>
                        <td colspan="2" style="padding: 2px;"><strong>CHARGES</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 15%; padding: 2px;"><strong>From</strong></td>
                        <td style="width: 35%; padding: 2px;">: ${formattedReadingDate}</td>
                        <td style="width: 25%; padding: 2px;"><strong>Current :</strong></td>
                        <td style="width: 25%; padding: 2px; text-align: right;">${parseFloat(billing.total_amount).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px;"><strong>To</strong></td>
                        <td style="padding: 2px;">: ${formattedNextMonth}</td>
                        <td style="padding: 2px;"><strong>Past Due :</strong></td>
                        <td style="padding: 2px; text-align: right;">0.00</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px;"><strong>Previous</strong></td>
                        <td style="padding: 2px;">: ${parseFloat(billing.previous_reading).toFixed(0)}</td>
                        <td style="padding: 2px;"><strong>Penalty :</strong></td>
                        <td style="padding: 2px; text-align: right;">${billing.penalty_amount ? parseFloat(billing.penalty_amount).toFixed(2) : '0.00'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px;"><strong>Present</strong></td>
                        <td style="padding: 2px;">: ${parseFloat(billing.current_reading).toFixed(0)}</td>
                        <td style="padding: 2px;"></td>
                        <td style="padding: 2px;"></td>
                    </tr>
                    <tr>
                        <td style="padding: 2px;"><strong>Usage</strong></td>
                        <td style="padding: 2px;">: ${parseFloat(billing.consumption).toFixed(0)}</td>
                        <td style="padding: 2px;"></td>
                        <td style="padding: 2px;"></td>
                    </tr>
                </table>
                
                <div style="text-align: right; margin: 15px 0; border-top: 1px solid #000; padding-top: 5px;">
                    <p style="margin: 5px 0; font-weight: bold;">TOTAL BEFORE DUE DATE : Php ${parseFloat(billing.total_amount).toFixed(2)}</p>
                    ${billing.penalty_amount && billing.penalty_amount > 0 ? 
                    `<p style="margin: 5px 0; font-weight: bold; color: #dc3545;">PENALTY : Php ${parseFloat(billing.penalty_amount).toFixed(2)}</p>
                    <p style="margin: 5px 0; font-weight: bold;">TOTAL AMOUNT DUE : Php ${totalAmount.toFixed(2)}</p>` : ''}
                </div>
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 12px;">
                    <tr>
                        <td style="padding: 2px;"><strong>DUE DATE</strong></td>
                        <td style="padding: 2px;">: ${formattedDueDate}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px;"><strong>DISCONNECTION DATE</strong></td>
                        <td style="padding: 2px;">: ${formattedDisconnectionDate}</td>
                    </tr>
                </table>
                
                <div style="text-align: center; margin: 15px 0; font-style: italic;">
                    <p style="margin: 5px 0; font-size: 12px;">"AYAW SAYANGI ANG TUBIG KAY ANG TUBIG KINABUHI"</p>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <p style="margin: 3px 0; font-size: 11px; font-weight: bold;">FOR DISCONNECTION</p>
                    <p style="margin: 3px 0; font-size: 11px;">months</p>
                </div>
                
                <div class="receipt-footer" style="text-align: center; margin-top: 20px; border-top: 1px dashed #000; padding-top: 10px; font-size: 10px;">
                    <p style="margin: 3px 0;">Thank you for your payment!</p>
                    <p style="margin: 3px 0;">Santa Fe Water System and Management Board</p>
                </div>
            </div>
        `;
        
        $('#receiptContent').html(receiptHTML);
    }

    // Handle print receipt button
    $('#printReceiptBtn').click(function() {
        const printWindow = window.open('', '_blank');
        const receiptContent = document.getElementById('receiptContent').innerHTML;
        
        // For printing, we need to handle the image path differently
        const printContent = receiptContent.replace(
            'src="{{ asset('image/santafe.png') }}"',
            'src="/image/santafe.png"'
        );
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Print Receipt</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 20px;
                    }
                    @media print {
                        body {
                            padding: 0;
                        }
                        .receipt-container {
                            box-shadow: none;
                            border: 1px solid #000;
                            max-width: 100%;
                        }
                        img {
                            max-width: 60px;
                            height: auto;
                        }
                    }
                </style>
            </head>
            <body>
                ${printContent}
                <script>
                    window.onload = function() {
                        // Add a small delay to ensure images are loaded before printing
                        setTimeout(function() {
                            window.print();
                            setTimeout(function() {
                                window.close();
                            }, 100);
                        }, 500);
                    }
                <\/script>
            </body>
            </html>
        `);
        
        printWindow.document.close();
    });

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
        
        // Alternative: Simple redirect (if no server-side logout needed)
        // window.location.href = '/login';
    }
});
</script>
</body>
</html>
