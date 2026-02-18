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
    <!-- Chart.js for graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        /* Status colors */
        .text-paid { color: #28a745; }
        .text-unpaid { color: #ffc107; }
        .text-overdue { color: #dc3545; }
        .text-income { color: #17a2b8; }
        
        .bg-paid { background-color: rgba(40, 167, 69, 0.1); }
        .bg-unpaid { background-color: rgba(255, 193, 7, 0.1); }
        .bg-overdue { background-color: rgba(220, 53, 69, 0.1); }
        .bg-income { background-color: rgba(23, 162, 184, 0.1); }
        
        .graph-card {
            margin-top: 20px;
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

<div class="sidebar">
    <div class="sidebar-header text-center">
        <img src="{{ asset('image/santafe.png') }}" class="login-logo img-fluid mb-3">
        <h1 class="h5">Santa Fe Water Billing</h1>
    </div>
    
    <nav class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="admin-accountant-dashboard">
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
                <a class="nav-link" href="admin-accountant-reports">
                    <i class="bi bi-file-earmark-bar-graph"></i> Reports
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link" href="paymentVerificationSection">
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
                <h2 class="header-title">Billing Dashboard</h2>
                <p class="header-subtitle">Santa Fe Water Billing System</p>
            </div>
        </div>
        
        <div class="header-right">
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
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-none d-md-inline">Accountant</span>
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
        <div class="row g-4">
            <!-- Paid Bills Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Paid Bills</h6>
                                <h3>{{ $paidCount ?? 0 }}</h3>
                                <small class="text-success">
                                    <i class="bi bi-arrow-up"></i> Successfully paid
                                </small>
                            </div>
                            <div class="bg-paid p-3 rounded">
                                <i class="bi bi-check-circle-fill text-paid fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unpaid Bills Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Unpaid Bills</h6>
                                <h3>{{ $unpaidCount ?? 0 }}</h3>
                                <small class="text-warning">
                                    <i class="bi bi-exclamation-circle"></i> Pending payment
                                </small>
                            </div>
                            <div class="bg-unpaid p-3 rounded">
                                <i class="bi bi-exclamation-circle-fill text-unpaid fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overdue Bills Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Overdue Bills</h6>
                                <h3>{{ $overdueCount ?? 0 }}</h3>
                                <small class="text-danger">
                                    <i class="bi bi-clock"></i> Past due date
                                </small>
                            </div>
                            <div class="bg-overdue p-3 rounded">
                                <i class="bi bi-clock-history text-overdue fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Income Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Monthly Income</h6>
                                <h3>₱{{ number_format($totalIncome ?? 0, 2) }}</h3>
                                <small class="text-success">
                                    <i class="bi bi-cash-stack"></i> Total revenue
                                </small>
                            </div>
                            <div class="bg-income p-3 rounded">
                                <i class="bi bi-cash-stack text-income fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Graphs Section -->
        <div class="row mt-4">
            <!-- Monthly Revenue Trend Chart -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm graph-card">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Revenue Trend</h5>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Status Distribution Chart -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm graph-card">
                    <div class="card-body">
                        <h5 class="card-title">Payment Status Distribution</h5>
                        <div class="chart-container">
                            <canvas id="paymentStatusChart"></canvas>
                        </div>
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
<!-- SweetAlert2 for notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            url: '/admin/payments/pending-notifications',
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
            window.location.href = `/paymentVerificationSection?payment_id=${paymentId}`;
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

    // Initialize charts when the page loads
    // Revenue Trend Chart (Line Chart)
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyLabels) !!},
            datasets: [{
                label: 'Monthly Revenue (₱)',
                data: {!! json_encode($monthlyRevenue) !!},
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                borderColor: 'rgba(23, 162, 184, 1)',
                borderWidth: 2,
                tension: 0.3,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(23, 162, 184, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Payment Status Distribution Chart (Doughnut Chart)
    const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
    const paymentStatusChart = new Chart(paymentStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Unpaid', 'Overdue'],
            datasets: [{
                data: [{{ $paidCount ?? 0 }}, {{ $unpaidCount ?? 0 }}, {{ $overdueCount ?? 0 }}],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '70%'
        }
    });
    // SweetAlert2 Logout Confirmation - Direct redirect to consumer portal
$('#logout-btn').on('click', function(e) {
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
        reverseButtons: false,
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Direct redirect to consumer portal page
            window.location.href = '/accountant-login';
        }
    });
});
});
</script>
<script>
    window.sessionTimeoutConfig = {
        durationMinutes: 240,
        warningSeconds: 30,
        logoutRedirectUrl: '/accountant-login'
    };
</script>
@include('auth.partials.session-timeout')

</body>
</html>
