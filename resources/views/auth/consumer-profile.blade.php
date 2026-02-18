<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Santa Fe Water Billing System - Consumer Profile</title>
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
        
        /* Profile Card Styles */
        .profile-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            padding: 25px;
            margin-top: 25px;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 600;
            margin-right: 20px;
        }
        
        .profile-info h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .profile-info p {
            margin: 5px 0 0;
            color: #6c757d;
        }
        
        .profile-section {
            margin-bottom: 30px;
        }
        
        .profile-section h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .profile-item {
            display: flex;
            margin-bottom: 15px;
        }
        
        .profile-item-label {
            font-weight: 500;
            width: 180px;
            color: #495057;
        }
        
        .profile-item-value {
            color: #212529;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px 20px;
            border-radius: 8px;
        }
        
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
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
                <a class="nav-link active" href="{{ url('/consumer-profile') }}">
                    <i class="bi bi-person"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/dashboard-consumer') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="consumer/dashboard">
                    <i class="bi bi-receipt"></i> Billing
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
                <h2 class="header-title">Consumer Profile</h2>
            </div>
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

            <!-- User Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <span>{{ $consumer->first_name }} {{ $consumer->last_name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
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
        <!-- Profile Information -->
        <div class="profile-card animate-fadein">
            <div class="profile-header">
                <div class="profile-avatar">
                    {{ strtoupper(substr($consumer->first_name, 0, 1)) }}{{ strtoupper(substr($consumer->last_name, 0, 1)) }}
                </div>
                <div class="profile-info">
                    <h2>{{ $consumer->first_name }} {{ $consumer->last_name }}</h2>
                    <p>Account ID: {{ $consumer->account_id }}</p>
                </div>
            </div>
            
            <!-- Personal Information -->
            <div class="profile-section">
                <h3>Personal Information</h3>
                <div class="profile-item">
                    <div class="profile-item-label">Full Name:</div>
                    <div class="profile-item-value">{{ $consumer->first_name }} {{ $consumer->last_name }}</div>
                </div>
               
                <div class="profile-item">
                    <div class="profile-item-label">Contact Number:</div>
                    <div class="profile-item-value">{{ $consumer->contact_number }}</div>
                </div>
                <div class="profile-item">
                    <div class="profile-item-label">Address:</div>
                    <div class="profile-item-value">{{ $consumer->address }}</div>
                </div>
            </div>
            
            <!-- Account Information -->
            <div class="profile-section">
                <h3>Account Information</h3>
                <div class="profile-item">
                    <div class="profile-item-label">Username:</div>
                    <div class="profile-item-value">{{ $account->username }}</div>
                </div>
                <div class="profile-item">
                    <div class="profile-item-label">Consumer Type:</div>
                    <div class="profile-item-value">{{ $consumer->consumer_type }}</div>
                </div>
                <div class="profile-item">
                    <div class="profile-item-label">Connection Date:</div>
                    <div class="profile-item-value">{{ \Carbon\Carbon::parse($consumer->connection_date)->format('F d, Y') }}</div>
                </div>
                <div class="profile-item">
                    <div class="profile-item-label">Account Status:</div>
                    <div class="profile-item-value">
                        <span class="badge bg-success">Active</span>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<script>
 $(document).ready(function() {
    // Mark notification as read when clicked
    $('.notification-item').click(function() {
        const notificationId = $(this).data('id');
        const $item = $(this);

        if ($item.hasClass('unread')) {
            $.ajax({
                url: `/consumer/notifications/${notificationId}/read`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $item.removeClass('unread');
                        updateNotificationBadge();
                    }
                }
            });
        }
    });

    // Mark all notifications as read
    $('.mark-all-read-btn').click(function(e) {
        e.stopPropagation();

        $.ajax({
            url: '/consumer/notifications/read-all',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('.notification-item').removeClass('unread');
                    updateNotificationBadge();
                }
            }
        });
    });

    // Function to update notification badge
    function updateNotificationBadge() {
        const unreadCount = $('.notification-item.unread').length;
        const $badge = $('.notification-badge');

        if (unreadCount > 0) {
            if ($badge.length === 0) {
                $('#notificationBell').append('<span class="notification-badge">' + unreadCount + '</span>');
            } else {
                $badge.text(unreadCount);
            }
        } else {
            $badge.remove();
        }
    }

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
            buttonsStyling: false,
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
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
                
                // Submit the logout form after a brief delay for better UX
                setTimeout(() => {
                    document.getElementById('logout-form').submit();
                }, 1000);
            }
        });
    });
});
</script>
</body>
</html>
