<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Santa Fe Water Billing System - Reports</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            border-bottom: 1px solid rgba(0,0,0,0.1);
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
            padding: 20px;
        }
        
        /* Table Styles */
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
            color: #4361ee;
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

        /* Badge Styles */
        .badge {
            font-weight: 500;
            padding: 6px 10px;
            font-size: 0.75rem;
            border-radius: 4px;
            text-transform: capitalize;
            display: inline-flex;
            align-items: center;
            gap:4px;
            white-space: nowrap;
        }

        .badge-paid {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .badge-unpaid {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .badge-overdue {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
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

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--primary-color);
            color: #fff;
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
        }

        .notification-item {
            display: block;
            padding: 12px 15px;
            border-bottom: 1px solid #f1f1f1;
            text-decoration: none;
            color: inherit;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-title {
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }

        .notification-message {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 4px;
            line-height: 1.4;
        }

        .notification-time {
            font-size: 0.75rem;
            color: #adb5bd;
        }

        .notification-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .notification-empty {
            padding: 24px 16px;
            text-align: center;
            color: #6c757d;
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
        
        /* Filter Controls */
        .filter-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        /* Responsive adjustments */
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
            
            .filter-controls {
                flex-direction: column;
                align-items: stretch;
                width: 100%;
            }
            
            .filter-controls > div {
                width: 100%;
            }
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
                <a class="nav-link" href="admin-accountant-dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin-accountant-consumer">
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
                <a class="nav-link active" href="admin-accountant-reports">
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
                <h2 class="header-title">Reports</h2>
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
                    <span class="d-none d-md-inline">Accountant</span>
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
    
    <!-- Dashboard Content -->
    <div class="content-wrapper">
        <div class="table-container animate-fadein">
            <div class="table-title">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h3 class="mb-0">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>
                        Billing Status Report
                    </h3>
                    <div class="filter-controls">
                        <div class="input-group" style="width: 180px;">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>
                        <div class="input-group" style="width: 200px;">
                            <input type="month" class="form-control" id="monthFilter">
                            <button class="btn btn-outline-secondary" id="applyFilter">
                                <i class="bi bi-funnel"></i>
                            </button>
                        </div>
                        <div class="input-group" style="width: 200px;">
                            <input type="text" class="form-control" id="nameSearch" placeholder="Search by name...">
                            <button class="btn btn-outline-secondary" id="applyNameSearch">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        <button class="btn btn-primary" id="exportBtn">
                            <i class="bi bi-printer me-2"></i> Print Report
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="reportsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Consumer</th>
                            <th>Meter No.</th>
                            <th>Due Date</th>
                            <th>Consumption (m³)</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Print Preview Modal -->
<div class="modal fade" id="printReportModal" tabindex="-1" aria-labelledby="printReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printReportModalLabel">Print Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="printPreviewContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printPreviewBtn">
                    <i class="bi bi-printer me-2"></i>Print Now
                </button>
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

    const printHeaderLogo = "{{ asset('image/santafe.png') }}";

    const printPreviewContent = $('#printPreviewContent');
    const printReportModal = new bootstrap.Modal(document.getElementById('printReportModal'));
    let printPreviewHtml = '';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatDueDateForPrint(value) {
        return value ? moment(value).format('MMM D, YYYY') : '';
    }

    function formatConsumptionForPrint(value) {
        if (value === null || value === undefined || value === '') return '0.00 m³';
        return `${parseFloat(value).toFixed(2)} m³`;
    }

    function formatAmountForPrint(value) {
        if (value === null || value === undefined || value === '') return '₱0.00';
        return `₱${parseFloat(value).toFixed(2)}`;
    }

    function buildPrintRows() {
        const rows = reportsTable.rows({ page: 'current', search: 'applied' }).data().toArray();

        if (!rows.length) {
            return '<tr><td colspan="7" style="text-align:center; padding:12px;">No records found for current filters.</td></tr>';
        }

        return rows.map(function(row, index) {
            return `
                <tr>
                    <td>${escapeHtml(row.DT_RowIndex || (index + 1))}</td>
                    <td>${escapeHtml(row.consumer_name || '')}</td>
                    <td>${escapeHtml(row.meter_no || '')}</td>
                    <td>${escapeHtml(formatDueDateForPrint(row.due_date))}</td>
                    <td>${escapeHtml(formatConsumptionForPrint(row.consumption))}</td>
                    <td>${escapeHtml(formatAmountForPrint(row.total_amount))}</td>
                    <td>${escapeHtml((row.status || '').toUpperCase())}</td>
                </tr>
            `;
        }).join('');
    }

    function buildPrintPreviewHtml() {
        const now = moment().format('MMM D, YYYY h:mm A');
        const monthFilter = $('#monthFilter').val() ? moment($('#monthFilter').val(), 'YYYY-MM').format('MMMM YYYY') : 'All Months';
        const nameFilter = $('#nameSearch').val() ? $('#nameSearch').val() : 'All Consumers';
        const statusFilter = $('#statusFilter').val() ? $('#statusFilter').val().toUpperCase() : 'ALL';

        return `
            <div style="font-family:Arial, sans-serif; color:#212529;">
                <div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-bottom:12px;">
                    <img src="${printHeaderLogo}" alt="Santa Fe Logo" style="width:58px; height:58px; object-fit:cover;">
                    <div style="text-align:center; line-height:1.25;">
                        <div style="font-weight:700;">Santa Fe Water System and</div>
                        <div style="font-weight:700;">Management Board</div>
                        <div>Santa Fe New Municipal Hall</div>
                        <div>PooC, Santa Fe, Cebu 6047</div>
                        <div><strong>CONTACT NO.</strong> 09469615234/09305694771</div>
                    </div>
                </div>

                <h4 style="text-align:center; margin:12px 0 8px;">Billing Status Report</h4>
                <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:13px;">
                    <div><strong>Month:</strong> ${escapeHtml(monthFilter)}</div>
                    <div><strong>Name:</strong> ${escapeHtml(nameFilter)}</div>
                    <div><strong>Status:</strong> ${escapeHtml(statusFilter)}</div>
                    <div><strong>Generated:</strong> ${escapeHtml(now)}</div>
                </div>

                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr>
                            <th style="border:1px solid #000; padding:6px; text-align:left;">ID</th>
                            <th style="border:1px solid #000; padding:6px; text-align:left;">Consumer</th>
                            <th style="border:1px solid #000; padding:6px; text-align:left;">Meter No.</th>
                            <th style="border:1px solid #000; padding:6px; text-align:left;">Due Date</th>
                            <th style="border:1px solid #000; padding:6px; text-align:left;">Consumption (m³)</th>
                            <th style="border:1px solid #000; padding:6px; text-align:left;">Total Amount</th>
                            <th style="border:1px solid #000; padding:6px; text-align:left;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${buildPrintRows()}
                    </tbody>
                </table>
            </div>
        `;
    }

    function printFromModalContent(htmlContent) {
        const printFrame = document.createElement('iframe');
        printFrame.style.position = 'fixed';
        printFrame.style.right = '0';
        printFrame.style.bottom = '0';
        printFrame.style.width = '0';
        printFrame.style.height = '0';
        printFrame.style.border = '0';
        document.body.appendChild(printFrame);

        const frameDoc = printFrame.contentWindow.document;
        frameDoc.open();
        frameDoc.write(`
            <!DOCTYPE html>
            <html>
                <head>
                    <title>Billing Status Report</title>
                    <style>
                        body { margin: 16px; font-family: Arial, sans-serif; }
                    </style>
                </head>
                <body>${htmlContent}</body>
            </html>
        `);
        frameDoc.close();

        printFrame.onload = function() {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();
            setTimeout(function() {
                document.body.removeChild(printFrame);
            }, 500);
        };
    }

    const reportsTable = $('#reportsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('accountant.reports.data') }}",
            type: 'GET',
            data: function(d) {
                d.month = $('#monthFilter').val();
                d.name = $('#nameSearch').val();
                d.status = $('#statusFilter').val();
            },
            error: function(xhr) {
                let errorMsg = "Failed to load data";
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                console.error("AJAX Error:", xhr.status, errorMsg);
                Swal.fire('Error', errorMsg, 'error');
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'consumer_name', name: 'consumer_name' },
            { data: 'meter_no', name: 'meter_no' },
            { 
                data: 'due_date', 
                name: 'due_date',
                render: function(data) {
                    return data ? moment(data).format('MMM D, YYYY') : '';
                }
            },
            { 
                data: 'consumption', 
                name: 'consumption',
                render: function(data) {
                    return data ? parseFloat(data).toFixed(2) + ' m³' : '0.00 m³';
                }
            },
            { 
                data: 'total_amount', 
                name: 'total_amount',
                render: function(data) {
                    return data ? '₱' + parseFloat(data).toFixed(2) : '₱0.00';
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    const status = String(data || '').toLowerCase();
                    let badgeClass = 'bg-secondary';
                    let label = status ? status.toUpperCase() : 'UNKNOWN';

                    if (status === 'paid') {
                        badgeClass = 'badge-paid';
                    } else if (status === 'unpaid') {
                        badgeClass = 'badge-unpaid';
                    } else if (status === 'overdue') {
                        badgeClass = 'badge-overdue';
                    }

                    return `<span class="badge ${badgeClass}">${label}</span>`;
                }
            }
        ],
    });

    $('#exportBtn').on('click', function() {
        printPreviewHtml = buildPrintPreviewHtml();
        printPreviewContent.html(printPreviewHtml);
        printReportModal.show();
    });

    $('#printPreviewBtn').on('click', function() {
        if (!printPreviewHtml) {
            printPreviewHtml = buildPrintPreviewHtml();
        }
        printFromModalContent(printPreviewHtml);
    });

    $('#applyFilter').click(function() {
        reportsTable.ajax.reload();
    });

    $('#statusFilter').change(function() {
        reportsTable.ajax.reload();
    });
    
    // Add search functionality for name
    $('#applyNameSearch').click(function() {
        reportsTable.ajax.reload();
    });
    
    // Allow search on Enter key
    $('#nameSearch').keypress(function(e) {
        if (e.which == 13) { // Enter key
            reportsTable.ajax.reload();
        }
    });

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
@include('auth.partials.session-timeout')
</body>
</html>
