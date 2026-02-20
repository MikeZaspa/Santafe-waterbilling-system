<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Santa Fe Water Billing System - Consumer Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
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
            margin: 16px 20px 0;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            border-radius: 8px;
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
            color: #333;
        }
        
        .header-subtitle {
            margin: 0;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .content-wrapper {
            padding: 20px;
        }
        
        /* Dashboard Cards */
        .stats-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            padding: 25px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .stats-card .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .stats-card .card-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: #333;
        }

        .stats-card .card-label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 500;
        }

        .stats-card.paid .card-icon {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .stats-card.unpaid .card-icon {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .stats-card.overdue .card-icon {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .stats-card.total .card-icon {
            background-color: rgba(0, 123, 255, 0.1);
            color: #007bff;
        }

        /* Chart Cards */
        .chart-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            padding: 25px;;
            margin-top: 25px
            border: 1px solid rgba(0, 0, 0, 0.04);
            height: 100%;
        }

        .chart-card .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Billing Table */
        .billing-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            padding: 25px;
            margin-top: 25px;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }

        .badge-paid {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .badge-unpaid {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .badge-overdue {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 25px;
        }

        .action-btn {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .action-btn:hover {
            border-color: var(--primary-color);
            transform: translateY(-3px);
            color: var(--primary-color);
            text-decoration: none;
        }

        .action-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .action-btn span {
            font-weight: 600;
            font-size: 0.9rem;
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
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
        
        .login-logo {
            width: 100px;       
            height: 100px;      
            border-radius: 50%; 
            object-fit: cover;  
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
                <a class="nav-link " href="{{ url('/consumer-profile') }}">
                    <i class="bi bi-person"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ url('/dashboard-consumer') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="consumer/dashboard">
                    <i class="bi bi-receipt"></i> Billing
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('consumer.complaints.index') }}">
                    <i class="bi bi-chat-left-text"></i> Complain
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
        </div>
       
        <div class="header-right">
            @php
                $notifications = $notifications ?? collect();
                $unreadNotificationsCount = $notifications->where('is_read', false)->count();
            @endphp
            <div class="dropdown position-relative me-3">
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
                            <button class="btn btn-sm btn-outline-primary mark-all-read-btn">Mark all as read</button>
                        @endif
                    </div>

                    <div class="notification-list">
                        @if($notifications->count() > 0)
                            @foreach($notifications as $notification)
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
                            @endforeach
                        @else
                            <div class="notification-empty">
                                <i class="bi bi-bell-slash"></i>
                                <p>No notifications</p>
                            </div>
                        @endif
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
                    <li>
                        <a class="dropdown-item text-danger" href="#" id="logout-btn">
                            <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                        </a>
                        <form id="logout-form" action="{{ route('consumer.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Dashboard Stats Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card paid">
                    <div class="card-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="card-value">{{ $paidCount }}</div>
                    <div class="card-label">Paid Bills</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card unpaid">
                    <div class="card-icon">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <div class="card-value">{{ $unpaidCount }}</div>
                    <div class="card-label">Unpaid Bills</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card overdue">
                    <div class="card-icon">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="card-value">{{ $overdueCount }}</div>
                    <div class="card-label">Overdue Bills</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card total">
                    <div class="card-icon">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div class="card-value">{{ $totalCount }}</div>
                    <div class="card-label">Total Bills</div>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="chart-card">
                    <h5 class="card-title">Billing Status Distribution</h5>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="chart-card">
                    <h5 class="card-title">Monthly Consumption Trend</h5>
                    <div class="chart-container">
                        <canvas id="consumptionChart"></canvas>
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
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
 $(document).ready(function() {
    const notificationList = $('.notification-list').first();
    const notificationActions = $('.notification-actions').first();
    let knownNotificationIds = new Set(
        $('.notification-item[data-id]').map(function () {
            return Number($(this).data('id'));
        }).get()
    );

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, function (char) {
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
        const existingButton = notificationActions.find('.mark-all-read-btn');

        if (unreadCount > 0) {
            if (!existingButton.length) {
                notificationActions.append('<button class="btn btn-sm btn-outline-primary mark-all-read-btn">Mark all as read</button>');
            }
        } else if (existingButton.length) {
            existingButton.remove();
        }
    }

    function updateNotificationBadge(unreadCount) {
        const $badge = $('#notificationBell').find('.notification-badge');

        if (unreadCount > 0) {
            if ($badge.length) {
                $badge.text(unreadCount);
            } else {
                $('#notificationBell').append('<span class="notification-badge">' + unreadCount + '</span>');
            }
        } else {
            $badge.remove();
        }
    }

    function renderNotificationList(notifications) {
        if (!notifications.length) {
            notificationList.html(`
                <div class="notification-empty">
                    <i class="bi bi-bell-slash"></i>
                    <p>No notifications</p>
                </div>
            `);
            return;
        }

        const html = notifications.map(function (notification) {
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

        notificationList.html(html);
    }

    function isPaymentVerificationUpdate(notification) {
        const text = `${notification.title || ''} ${notification.message || ''}`.toLowerCase();
        return text.includes('payment approved') || text.includes('payment rejected');
    }

    function fetchNotifications(showToastForNew) {
        $.ajax({
            url: "{{ route('consumer.notifications.index') }}",
            type: 'GET',
            data: { limit: 20 },
            success: function (response) {
                if (!response || !response.success) {
                    return;
                }

                const notifications = Array.isArray(response.notifications) ? response.notifications : [];
                const unreadCount = Number(response.unread_count ?? notifications.filter(n => !n.is_read).length);
                const currentIds = new Set(notifications.map(n => Number(n.id)));
                const newNotifications = showToastForNew
                    ? notifications.filter(n => !knownNotificationIds.has(Number(n.id)))
                    : [];

                renderNotificationList(notifications);
                updateNotificationBadge(unreadCount);
                renderMarkAllButton(unreadCount);
                knownNotificationIds = currentIds;

                const paymentUpdates = newNotifications.filter(isPaymentVerificationUpdate);
                if (paymentUpdates.length > 0) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: paymentUpdates.length === 1 ? paymentUpdates[0].title : `${paymentUpdates.length} payment updates`,
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });
                }
            }
        });
    }

    $(document).on('click', '.notification-item', function () {
        const notificationId = Number($(this).data('id'));
        if (!notificationId || !$(this).hasClass('unread')) {
            return;
        }

        $.ajax({
            url: `{{ url('/consumer/notifications') }}/${notificationId}/read`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response && response.success) {
                    fetchNotifications(false);
                }
            }
        });
    });

    $(document).on('click', '.mark-all-read-btn', function (e) {
        e.stopPropagation();

        $.ajax({
            url: "{{ route('consumer.notifications.read-all') }}",
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response && response.success) {
                    fetchNotifications(false);
                }
            }
        });
    });

    fetchNotifications(false);
    setInterval(function () {
        fetchNotifications(true);
    }, 15000);

    // Mobile sidebar toggle functionality
    const sidebar = $('.sidebar');
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
        if ($(window).width() >= 992) {
            sidebar.removeClass('active');
            mobileOverlay.removeClass('active');
            header.css('background-color', 'white');
            $('body').css('overflow', '');
        }
    });
    
    // SweetAlert2 Logout Confirmation
    $('#logout-btn').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Logout Confirmation',
            text: "Are you sure you want to logout?",
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
                Swal.fire({
                    title: 'Logging out...',
                    text: 'Please wait while we securely log you out.',
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
    
    // Initialize Charts
    // Billing Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Unpaid', 'Overdue'],
            datasets: [{
                data: [{{ $paidCount }}, {{ $unpaidCount }}, {{ $overdueCount }}],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(255, 193, 7, 1)'
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
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    // Monthly Consumption Trend Chart
    const consumptionCtx = document.getElementById('consumptionChart').getContext('2d');
    const consumptionChart = new Chart(consumptionCtx, {
        type: 'line',
        data: {
            labels: @json($monthlyLabels),
            datasets: [{
                label: 'Water Consumption (m³)',
                data: @json($monthlyConsumption),
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
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
                        text: 'Consumption (m³)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Consumption: ${context.raw} m³`;
                        }
                    }
                }
            }
        }
    });
});
</script>
</body>
</html>
