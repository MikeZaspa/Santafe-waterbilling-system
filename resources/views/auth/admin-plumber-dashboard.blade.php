<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Santa Fe Water Billing - Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        .metric-card {
            border-radius: 10px;
            transition: all 0.3s;
            border-left: 4px solid;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .metric-card.completed {
            border-left-color: #28A745;
        }
        
        .metric-card.pending {
            border-left-color: #FFC107;
        }
        
        .metric-card.overdue {
            border-left-color: #DC3545;
        }
        
        .metric-card.disconnected {
            border-left-color: #DC3545;
        }
        
        .metric-card.total {
            border-left-color: #17A2B8;
        }
        
        .metric-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .metric-icon.completed {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28A745;
        }
        
        .metric-icon.pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: #FFC107;
        }
        
        .metric-icon.overdue {
            background-color: rgba(220, 53, 69, 0.1);
            color: #DC3545;
        }
        
        .metric-icon.disconnected {
            background-color: rgba(220, 53, 69, 0.1);
            color: #DC3545;
        }
        
        .metric-icon.total {
            background-color: rgba(23, 162, 184, 0.1);
            color: #17A2B8;
        }
        
        .login-logo {
            width: 100px;       
            height: 100px;      
            border-radius: 50%; 
            object-fit: cover;  
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
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
        
        /* Status colors */
        .text-completed { color: #28a745; }
        .text-pending { color: #ffc107; }
        .text-overdue { color: #dc3545; }
        .text-disconnected { color: #dc3545; }
        .text-total { color: #17a2b8; }
        
        .bg-completed { background-color: rgba(40, 167, 69, 0.1); }
        .bg-pending { background-color: rgba(255, 193, 7, 0.1); }
        .bg-overdue { background-color: rgba(220, 53, 69, 0.1); }
        .bg-disconnected { background-color: rgba(220, 53, 69, 0.1); }
        .bg-total { background-color: rgba(23, 162, 184, 0.1); }
        
        /* Disconnection list styles */
        .disconnection-item {
            border-left: 3px solid #dc3545;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        
        .disconnection-item:last-child {
            margin-bottom: 0;
        }
        
        .consumer-name {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .disconnection-date {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .no-disconnections {
            text-align: center;
            padding: 2rem;
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
        
        /* Session Timer Styles */
        .session-timer {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            padding: 10px 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1050;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .session-timer.warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
        }
        
        .session-timer.danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        
        .session-timer-icon {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .session-timer-text {
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .session-timer-countdown {
            font-weight: 700;
            margin-left: 5px;
        }
        
        /* Session Modal Styles */
        .session-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1100;
            justify-content: center;
            align-items: center;
        }
        
        .session-modal-content {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }
        
        .session-modal-icon {
            font-size: 3rem;
            color: #ffc107;
            margin-bottom: 15px;
        }
        
        .session-modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .session-modal-message {
            margin-bottom: 20px;
            color: #6c757d;
        }
        
        .session-modal-countdown {
            font-size: 2rem;
            font-weight: 700;
            color: #dc3545;
            margin-bottom: 20px;
        }
        
        .session-modal-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        /* Auth Guard Loading Screen */
        .auth-guard-loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .auth-guard-loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* 404 Error Page Styles */
        .error-404-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #f8f9fa;
            z-index: 9999;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
        }
        
        .error-404-container.active {
            display: flex;
        }
        
        .error-404-content {
            max-width: 600px;
        }
        
        .error-404-code {
            font-size: 8rem;
            font-weight: 700;
            color: #dc3545;
            line-height: 1;
            margin-bottom: 20px;
        }
        
        .error-404-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        
        .error-404-message {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .error-404-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
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
            /* Make metric cards stack vertically */
            .row.g-4.mb-4 {
                flex-direction: column;
            }
            
            .row.g-4.mb-4 > div {
                width: 100%;
                margin-bottom: 15px;
            }
            
            /* Make charts stack vertically */
            .row.g-4 {
                flex-direction: column;
            }
            
            .row.g-4 > div {
                width: 100%;
                margin-bottom: 15px;
            }
            
            /* Adjust chart container height */
            .chart-container {
                height: 250px;
            }
            
            /* Header title adjustments */
            .header-title {
                font-size: 1rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 150px;
            }
            
            /* Adjust dropdown menu */
            .dropdown-menu {
                position: absolute;
                right: 0;
                left: auto;
            }
            
            /* Adjust chart tooltips for mobile */
            .chartjs-tooltip {
                transform: scale(0.8);
                transform-origin: center center;
            }
            
            /* Session timer adjustments for mobile */
            .session-timer {
                bottom: 10px;
                right: 10px;
                padding: 8px 12px;
            }
            
            .session-timer-text {
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 576px) {
            .mobile-header-title {
                font-size: 0.9rem;
                max-width: 120px;
            }
            
            .position-relative.me-3 {
                display: none !important;
            }
            
            .header {
                padding: 0 15px;
            }
            
            .header-title {
                font-size: 1rem;
            }
            
            .header-subtitle {
                display: none;
            }
            
            .dropdown-toggle span {
                display: none;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            /* Session timer adjustments for small mobile */
            .session-timer {
                bottom: 5px;
                right: 5px;
                padding: 6px 10px;
            }
            
            .session-timer-icon {
                font-size: 1rem;
            }
            
            .session-timer-text {
                font-size: 0.75rem;
            }
            
            /* 404 Error Page Mobile Adjustments */
            .error-404-code {
                font-size: 5rem;
            }
            
            .error-404-title {
                font-size: 1.5rem;
            }
            
            .error-404-actions {
                flex-direction: column;
                width: 100%;
            }
            
            .error-404-actions .btn {
                width: 100%;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Auth Guard Loading Screen -->
<div class="auth-guard-loading" id="authGuardLoading">
    <div class="auth-guard-loading-spinner"></div>
    <p class="mt-3">Verifying authentication...</p>
</div>

<!-- 404 Error Page -->
<div class="error-404-container" id="error404Container">
    <div class="error-404-content">
        <div class="error-404-code">404</div>
        <h1 class="error-404-title">Access Denied</h1>
        <p class="error-404-message">You don't have permission to access this page. Please log in with the appropriate credentials.</p>
        <div class="error-404-actions">
            <a href="/admin-portal" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right me-2"></i>Go to Login
            </a>
            <button class="btn btn-outline-secondary" id="retryAccessBtn">
                <i class="bi bi-arrow-clockwise me-2"></i>Retry Access
            </button>
        </div>
    </div>
</div>

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
                <a class="nav-link active" href="admin-plumber-dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin-plumber-consumer">
                    <i class="bi bi-people"></i> Reading
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
                <h2 class="header-title">Reading Dashboard Overview</h2>
                <p class="header-subtitle">Santa Fe Water Billing System</p>
            </div>
        </div>
        
        <div class="header-right">
            <div class="position-relative me-3 d-none d-sm-block">
                <i class="bi bi-bell fs-5"></i>
            </div>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-none d-md-inline">Plumber</span>
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
    
    <!-- Dashboard Content -->
    <div class="content-wrapper">
        <!-- Metrics Cards -->
        <div class="row g-4 mb-4">
            <!-- Completed Readings Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Completed Readings</h6>
                                <h3>{{ $completedCount }}</h3>
                                <small class="text-success">
                                    <i class="bi bi-check-circle"></i> Readings with both current and previous values
                                </small>
                            </div>
                            <div class="bg-completed p-3 rounded">
                                <i class="bi bi-check-circle-fill text-completed fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Add this card to your dashboard HTML -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Reconnection Fees</h6>
                                <h3>₱{{ number_format($monthlyReconnectionFees) }}</h3>
                                <small class="text-info">
                                    <i class="bi bi-currency-dollar"></i> Collected this month
                                </small>
                            </div>
                            <div class="bg-info p-3 rounded" style="background-color: #cfe2ff !important;">
                                <i class="bi bi-cash-coin text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disconnected Consumers Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Disconnected</h6>
                                <h3>{{ $disconnectedCount }}</h3>
                                <small class="text-danger">
                                    <i class="bi bi-x-circle"></i> Consumers with disconnected service
                                </small>
                            </div>
                            <div class="bg-disconnected p-3 rounded">
                                <i class="bi bi-x-circle-fill text-disconnected fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Reconnections</h6>
                                <h3>{{ $reconnectionCount }}</h3>
                                <small class="text-success">
                                    <i class="bi bi-plug"></i> Recently reconnected consumers
                                </small>
                            </div>
                            <div class="bg-success p-3 rounded" style="background-color: #d1e7dd !important;">
                                <i class="bi bi-plug-fill text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Recent Disconnections Row -->
        <div class="row g-4">
            <!-- Charts Column -->
            <div class="col-lg-8">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">Completed Readings by Month</h5>
                                <div class="chart-container">
                                    <canvas id="completedReadingsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">Monthly Consumption Trend (m³)</h5>
                                <div class="chart-container">
                                    <canvas id="consumptionTrendChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Disconnections Column -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between align-items-center">
                            <span>Recent Disconnections</span>
                            <span class="badge bg-danger">{{ $disconnectedCount }}</span>
                        </h5>
                        
                        @if($recentDisconnections->count() > 0)
                            <div class="disconnections-list">
                                @foreach($recentDisconnections as $disconnection)
                                    <div class="disconnection-item">
                                        <div class="consumer-name">
                                            {{ $disconnection->consumer->first_name }} 
                                            {{ $disconnection->consumer->last_name }}
                                        </div>
                                        <div class="disconnection-date">
                                            <small>
                                                <i class="bi bi-calendar-event me-1"></i>
                                                {{ \Carbon\Carbon::parse($disconnection->updated_at)->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div class="meter-info">
                                            <small class="text-muted">
                                                Meter: {{ $disconnection->meter_no }}
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="no-disconnections">
                                <i class="bi bi-check-circle display-4 text-success mb-3"></i>
                                <p class="mb-0">No disconnected consumers</p>
                                <small>All consumers are currently connected</small>
                            </div>
                        @endif
                        
                        @if($disconnectedCount > 5)
                            <div class="text-center mt-3">
                                <a href="admin-plumber-disconnection" class="btn btn-sm btn-outline-danger">
                                    View All Disconnections
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recently Reconnected -->
            <div class="col-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title d-flex justify-content-between align-items-center mb-3">
                            <span>Recently Reconnected Consumers</span>
                            <span class="badge bg-success" id="reconnectedCount">{{ $reconnectionCount }}</span>
                        </h5>
                        
                        <!-- Search Box -->
                        <div class="mb-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchReconnected" placeholder="Search by name or meter number...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Reconnected Consumers List -->
                        <div id="reconnectedConsumersList" class="flex-grow-1" style="max-height: 200px; overflow-y: auto;">
                            <!-- Loading state -->
                            <div class="text-center text-muted py-4" id="loadingReconnected">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                Loading reconnected consumers...
                            </div>
                            
                            <!-- Empty state -->
                            <div class="text-center text-muted py-4 d-none" id="emptyReconnected">
                                <i class="bi bi-arrow-repeat display-4 d-block mb-2"></i>
                                <p class="mb-0">No recent reconnections</p>
                                <small>Reconnected consumers will appear here</small>
                            </div>
                            
                            <!-- Consumers will be populated here -->
                        </div>
                        
                        <!-- Refresh Button -->
                        <div class="mt-3 pt-3 border-top">
                            <button class="btn btn-sm btn-outline-success w-100" id="refreshReconnectedBtn">
                                <i class="bi bi-arrow-clockwise me-1"></i> Refresh List
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Session Timer -->
<div class="session-timer" id="sessionTimer">
    <i class="bi bi-clock session-timer-icon"></i>
    <div class="session-timer-text">
        Session: <span class="session-timer-countdown" id="sessionCountdown">3:00</span>
    </div>
</div>

<!-- Session Warning Modal -->
<div class="session-modal" id="sessionWarningModal">
    <div class="session-modal-content">
        <i class="bi bi-exclamation-triangle session-modal-icon"></i>
        <h3 class="session-modal-title">Session Expiring</h3>
        <p class="session-modal-message">Your session will expire in:</p>
        <div class="session-modal-countdown" id="modalCountdown">30</div>
        <div class="session-modal-buttons">
            <button class="btn btn-primary" id="extendSessionBtn">Extend Session</button>
            <button class="btn btn-outline-secondary" id="signOutNowBtn">Sign Out Now</button>
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
    // AUTHENTICATION GUARD
    // Check if user is authenticated before loading the dashboard
    function checkAuthentication() {
        // Check if user came from login page or has valid session
        const referrer = document.referrer;
        const currentPath = window.location.pathname;
        
        // Check for session token in localStorage
        const sessionToken = localStorage.getItem('plumberSessionToken');
        const sessionExpiry = localStorage.getItem('plumberSessionExpiry');
        
        // Check if session is valid
        const isSessionValid = sessionToken && sessionExpiry && new Date().getTime() < parseInt(sessionExpiry);
        
        // Check if user is coming from login page or has valid session
        if (!isSessionValid && !referrer.includes('admin-portal') && !referrer.includes('admin-login')) {
            // User is trying to directly access the dashboard without authentication
            show404Error();
            return false;
        }
        
        // If session is expired, redirect to login
        if (sessionExpiry && new Date().getTime() > parseInt(sessionExpiry)) {
            redirectToLogin();
            return false;
        }
        
        // Verify authentication with server
        $.ajax({
            url: '/api/check-auth',
            type: 'GET',
            success: function(response) {
                // User is authenticated, hide loading screen and continue
                $('#authGuardLoading').fadeOut(300, function() {
                    $(this).remove();
                    initializeDashboard();
                });
            },
            error: function(xhr) {
                // User is not authenticated, show 404 error
                if (xhr.status === 401) {
                    show404Error();
                } else {
                    // Handle other errors
                    $('#authGuardLoading').fadeOut(300, function() {
                        $(this).remove();
                        show404Error();
                    });
                }
            }
        });
        
        return true;
    }
    
    // Show 404 error page
    function show404Error() {
        $('#authGuardLoading').fadeOut(300, function() {
            $(this).remove();
            $('#error404Container').addClass('active');
            
            // Log the unauthorized access attempt
            console.warn('Unauthorized access attempt to admin-plumber-dashboard', {
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent,
                referrer: document.referrer,
                ip: null // IP would need to be collected server-side
            });
        });
    }
    
    // Redirect to login page
    function redirectToLogin() {
        $('#authGuardLoading').fadeOut(300, function() {
            $(this).remove();
            window.location.href = '/admin-portal';
        });
    }
    
    // Retry access button handler
    $('#retryAccessBtn').click(function() {
        // Show loading screen again
        $('#error404Container').removeClass('active');
        $('body').append('<div class="auth-guard-loading" id="authGuardLoading"><div class="auth-guard-loading-spinner"></div><p class="mt-3">Retrying authentication...</p></div>');
        
        // Retry authentication check
        setTimeout(function() {
            checkAuthentication();
        }, 1000);
    });
    
    // Prevent direct access by checking browser history
    function preventDirectAccess() {
        // Check if browser back/forward buttons were used
        let navigationEntries = performance.getEntriesByType('navigation');
        
        if (navigationEntries.length > 0 && navigationEntries[0].type === 'back_forward') {
            // User navigated using browser back/forward buttons
            // Check if they have a valid session
            const sessionToken = localStorage.getItem('plumberSessionToken');
            const sessionExpiry = localStorage.getItem('plumberSessionExpiry');
            
            if (!sessionToken || !sessionExpiry || new Date().getTime() > parseInt(sessionExpiry)) {
                // No valid session, redirect to login
                redirectToLogin();
                return false;
            }
        }
        
        return true;
    }
    
    // Prevent right-click and view source for additional security
    function preventViewSource() {
        // Disable right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
        document.addEventListener('keydown', function(e) {
            if (e.keyCode === 123 || // F12
                (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) || // Ctrl+Shift+I/J
                (e.ctrlKey && e.keyCode === 85)) { // Ctrl+U
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Initialize the dashboard after authentication check
    function initializeDashboard() {
        // Session Management Variables
        const sessionTimeout = 180000; // 3 minutes in milliseconds
        const warningTimeout = 30000; // 30 seconds before expiry
        let sessionTimer;
        let warningTimer;
        let timeRemaining = sessionTimeout;
        let isModalOpen = false;
        
        // Store session token and expiry in localStorage
        const sessionToken = 'plumber_' + Math.random().toString(36).substr(2, 9);
        const sessionExpiry = new Date().getTime() + sessionTimeout;
        
        localStorage.setItem('plumberSessionToken', sessionToken);
        localStorage.setItem('plumberSessionExpiry', sessionExpiry.toString());
        
        // Start the session timer
        function startSessionTimer() {
            resetSessionTimer();
            
            // Set up activity event listeners
            $(document).on('mousemove keydown click scroll', function() {
                if (!isModalOpen) {
                    resetSessionTimer();
                }
            });
        }
        
        // Reset the session timer
        function resetSessionTimer() {
            clearTimeout(sessionTimer);
            clearTimeout(warningTimer);
            
            timeRemaining = sessionTimeout;
            updateSessionTimerDisplay();
            
            // Update session expiry in localStorage
            const newExpiry = new Date().getTime() + sessionTimeout;
            localStorage.setItem('plumberSessionExpiry', newExpiry.toString());
            
            // Set timer to show warning modal
            warningTimer = setTimeout(showWarningModal, sessionTimeout - warningTimeout);
            
            // Set timer to logout automatically
            sessionTimer = setTimeout(logoutUser, sessionTimeout);
        }
        
        // Update the session timer display
        function updateSessionTimerDisplay() {
            const minutes = Math.floor(timeRemaining / 60000);
            const seconds = Math.floor((timeRemaining % 60000) / 1000);
            const display = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            
            $('#sessionCountdown').text(display);
            
            // Change timer color based on remaining time
            const $sessionTimer = $('#sessionTimer');
            $sessionTimer.removeClass('warning danger');
            
            if (timeRemaining <= 30000) {
                $sessionTimer.addClass('danger');
            } else if (timeRemaining <= 60000) {
                $sessionTimer.addClass('warning');
            }
        }
        
        // Show warning modal
        function showWarningModal() {
            isModalOpen = true;
            let modalCountdown = Math.floor(warningTimeout / 1000);
            
            $('#modalCountdown').text(modalCountdown);
            $('#sessionWarningModal').css('display', 'flex');
            
            // Update modal countdown
            const modalInterval = setInterval(function() {
                modalCountdown--;
                $('#modalCountdown').text(modalCountdown);
                
                if (modalCountdown <= 0) {
                    clearInterval(modalInterval);
                    logoutUser();
                }
            }, 1000);
            
            // Store interval ID to clear it when modal is closed
            $('#sessionWarningModal').data('interval', modalInterval);
        }
        
        // Hide warning modal
        function hideWarningModal() {
            isModalOpen = false;
            $('#sessionWarningModal').css('display', 'none');
            
            // Clear the modal countdown interval
            const modalInterval = $('#sessionWarningModal').data('interval');
            if (modalInterval) {
                clearInterval(modalInterval);
            }
        }
        
        // Extend session
        function extendSession() {
            hideWarningModal();
            resetSessionTimer();
            
            // Show success message
            showSuccessToast('Session extended for another 3 minutes');
        }
        
        // Logout user
        function logoutUser() {
            hideWarningModal();
            
            // Clear session from localStorage
            localStorage.removeItem('plumberSessionToken');
            localStorage.removeItem('plumberSessionExpiry');
            
            Swal.fire({
                title: 'Session Expired',
                text: 'Your session has expired. You will be redirected to the login page.',
                icon: 'info',
                confirmButtonText: 'OK',
                allowOutsideClick: false
            }).then(function() {
                performLogout();
            });
        }
        
        // Countdown timer update
        const countdownInterval = setInterval(function() {
            if (timeRemaining > 0) {
                timeRemaining -= 1000;
                updateSessionTimerDisplay();
            }
        }, 1000);
        
        // Mobile sidebar toggle functionality
        const sidebar = $('.sidebar');
        const mainContent = $('.main-content');
        const header = $('.header');
        const sidebarToggle = $('#sidebarToggle');
        const mobileOverlay = $('.mobile-overlay');
        
        sidebarToggle.on('click', function() {
            sidebar.toggleClass('active');
            mainContent.toggleClass('active');
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

        let allReconnectedConsumers = [];

        function loadReconnectedConsumers() {
            // Show loading state
            $('#loadingReconnected').removeClass('d-none');
            $('#emptyReconnected').addClass('d-none');
            
            $.ajax({
                url: '/reconnected-consumers',
                type: 'GET',
                success: function(response) {
                    // Hide loading state
                    $('#loadingReconnected').addClass('d-none');
                    
                    if (response.success) {
                        allReconnectedConsumers = response.reconnected_consumers;
                        filterAndDisplayConsumers($('#searchReconnected').val());
                    } else {
                        showNoReconnectedConsumers();
                    }
                },
                error: function(xhr) {
                    $('#loadingReconnected').addClass('d-none');
                    console.error('Failed to load reconnected consumers');
                    showErrorState();
                }
            });
        }

        function filterAndDisplayConsumers(searchTerm = '') {
            let filteredConsumers = allReconnectedConsumers;
            
            if (searchTerm) {
                const searchLower = searchTerm.toLowerCase();
                filteredConsumers = allReconnectedConsumers.filter(consumer => 
                    consumer.consumer_name.toLowerCase().includes(searchLower) ||
                    consumer.meter_no.toLowerCase().includes(searchLower)
                );
            }
            
            if (filteredConsumers.length > 0) {
                updateReconnectedList(filteredConsumers);
                $('#emptyReconnected').addClass('d-none');
            } else {
                showNoReconnectedConsumers(searchTerm);
            }
        }

        // Real-time search with client-side filtering
        $('#searchReconnected').on('input', function() {
            const searchTerm = $(this).val().trim();
            const $clearBtn = $('#clearSearch');
            
            if (searchTerm.length > 0) {
                $clearBtn.show();
            } else {
                $clearBtn.hide();
            }
            
            filterAndDisplayConsumers(searchTerm);
        });

        function updateReconnectedList(consumers) {
            const container = $('#reconnectedConsumersList');
            container.empty();
            
            consumers.forEach(consumer => {
                const item = `
                    <div class="reconnection-item mb-3 p-3 border-start border-success border-3">
                        <div class="consumer-name fw-bold text-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            ${consumer.consumer_name}
                        </div>
                        <div class="reconnection-date small text-muted">
                            <i class="bi bi-calendar-event me-1"></i>
                            ${new Date(consumer.reconnection_date).toLocaleDateString('en-US', {
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}
                        </div>
                        <div class="meter-info small text-muted">
                            <i class="bi bi-speedometer2 me-1"></i>
                            Meter: ${consumer.meter_no}
                        </div>
                    </div>
                `;
                container.append(item);
            });
            
            // Update the count badge
            $('#reconnectedCount').text(consumers.length);
        }

        function showNoReconnectedConsumers() {
            const container = $('#reconnectedConsumersList');
            container.html(`
                <div class="text-center text-muted py-5" id="emptyReconnected">
                    <i class="bi bi-arrow-repeat display-4 d-block mb-3 text-muted"></i>
                    <h6 class="text-muted mb-2">No Reconnected Consumers</h6>
                </div>
            `);
            
            // Update the count badge to 0
            $('#reconnectedCount').text('0');
        }

        function showErrorState() {
            const container = $('#reconnectedConsumersList');
            container.html(`
                <div class="text-center text-danger py-5">
                    <i class="bi bi-exclamation-triangle display-4 d-block mb-3"></i>
                    <h6 class="text-danger mb-2">Failed to Load Data</h6>
                    <p class="small text-muted mb-3">Unable to load reconnected consumers at this time.</p>
                    <button class="btn btn-sm btn-outline-danger" onclick="loadReconnectedConsumers()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Try Again
                    </button>
                </div>
            `);
        }

        // Refresh button click handler
        $('#refreshReconnectedBtn').click(function() {
            const $btn = $(this);
            const originalText = $btn.html();
            
            $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Refreshing...');
            $btn.prop('disabled', true);
            
            loadReconnectedConsumers();
            
            // Re-enable button after 2 seconds
            setTimeout(() => {
                $btn.html(originalText);
                $btn.prop('disabled', false);
            }, 2000);
        });

        // Clear search button
        $('#clearSearch').click(function() {
            $('#searchReconnected').val('');
            loadReconnectedConsumers();
        });

        // Session management event handlers
        $('#extendSessionBtn').click(function() {
            extendSession();
        });
        
        $('#signOutNowBtn').click(function() {
            hideWarningModal();
            performLogout();
        });
        
        // Load reconnected consumers on page load
        loadReconnectedConsumers();
        
        // Start the session timer
        startSessionTimer();
        
        // Auto-refresh reconnected consumers every 30 seconds
        setInterval(loadReconnectedConsumers, 30000);
        
        // Close sidebar when clicking on overlay
        mobileOverlay.on('click', function() {
            sidebar.removeClass('active');
            mainContent.removeClass('active');
            mobileOverlay.removeClass('active');
            header.css('background-color', 'white');
            $('body').css('overflow', '');
        });
        
        // Close sidebar when clicking on a nav link (for mobile)
        $('.sidebar-menu .nav-link').on('click', function() {
            if ($(window).width() < 992) {
                sidebar.removeClass('active');
                mainContent.removeClass('active');
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
                mainContent.removeClass('active');
                mobileOverlay.removeClass('active');
                header.css('background-color', 'white');
                $('body').css('overflow', '');
            }
        });
        
        // Initialize charts
        const consumptionCtx = document.getElementById('consumptionTrendChart').getContext('2d');
        const consumptionChart = new Chart(consumptionCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Water Consumption (m³)',
                    data: @json($consumptionData),
                    backgroundColor: 'rgba(23, 162, 184, 0.2)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw} m³`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cubic Meters (m³)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                }
            }
        });

        const completedCtx = document.getElementById('completedReadingsChart').getContext('2d');
        const completedChart = new Chart(completedCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Completed Readings',
                    data: @json($completedData),
                    backgroundColor: 'rgba(40, 167, 69, 0.5)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Readings'
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                }
            }
        });
        
        // Handle window resize for charts
        $(window).on('resize', function() {
            consumptionChart.resize();
            completedChart.resize();
        });

        // DASHBOARD UPDATE FUNCTIONS - INSERTED HERE

        // Function to update dashboard charts and data after reconnection
        function updateDashboardAfterReconnection() {
            // Show loading state
            const loadingToast = Swal.fire({
                title: 'Updating Dashboard...',
                text: 'Refreshing consumption data',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Reload the page to get updated data (simple approach)
            setTimeout(() => {
                window.location.reload();
            }, 1500);

            // Alternative: AJAX approach to update charts without page reload
            // updateDashboardCharts();
        }

        // AJAX function to update charts without page reload
        function updateDashboardCharts() {
            $.ajax({
                url: '/dashboard-data',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        // Update consumption chart
                        updateConsumptionChart(response.data.current_month_consumption);
                        
                        // Update completed readings count
                        updateCompletedReadings(response.data.current_month_completed);
                        
                        // Update reconnection fees
                        updateReconnectionFees(response.data.current_month_reconnection_fees);
                        
                        Swal.close();
                        showSuccessToast('Dashboard updated successfully!');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    console.error('Failed to update dashboard data');
                    // Fallback to page reload
                    window.location.reload();
                }
            });
        }

        // Function to update consumption chart
        function updateConsumptionChart(newConsumption) {
            const consumptionChart = Chart.getChart('consumptionTrendChart');
            if (consumptionChart) {
                const currentMonth = new Date().getMonth();
                const data = consumptionChart.data.datasets[0].data;
                
                // Update current month's consumption
                data[currentMonth] = newConsumption;
                
                consumptionChart.update('active');
            }
        }

        // Function to update completed readings
        function updateCompletedReadings(newCount) {
            const completedChart = Chart.getChart('completedReadingsChart');
            if (completedChart) {
                const currentMonth = new Date().getMonth();
                const data = completedChart.data.datasets[0].data;
                
                // Update current month's completed readings
                data[currentMonth] = newCount;
                
                completedChart.update('active');
            }
        }

        // Function to update reconnection fees display
        function updateReconnectionFees(newFees) {
            // Update the reconnection fees card
            $('.card:has(.fa-cash-coin) h3').text('₱' + newFees.toLocaleString());
        }

        // Modify the reconnect function to call dashboard update
        function restoreDisconnectedConsumer(consumerId, consumerName, isFromMainView = false) {
            Swal.fire({
                title: 'Reconnect Consumer?',
                html: `
                    <p>Are you sure you want to reconnect <strong>${consumerName}</strong>?</p>
                    <p class="text-info"><i class="bi bi-info-circle me-2"></i>A reconnection fee of ₱500 will be applied.</p>
                    <div class="form-group mt-3">
                        <label for="reconnectionNotes" class="form-label">Notes (Optional)</label>
                        <textarea id="reconnectionNotes" class="form-control" rows="3" placeholder="Add any notes about this reconnection..."></textarea>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Reconnect Consumer',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    const notes = document.getElementById('reconnectionNotes').value;
                    
                    // Show loading state
                    Swal.fire({
                        title: 'Reconnecting Consumer...',
                        text: 'Please wait while we reconnect the consumer',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `/disconnections/${consumerId}/restore`,
                        type: 'POST',
                        data: {
                            notes: notes,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.close();
                            
                            if (response.success) {
                                showSuccessAlert('Success!', response.message);
                                
                                // Update dashboard data after successful reconnection
                                updateDashboardAfterReconnection();
                                
                                // Reload the main billing table to show the reconnected consumer
                                if (typeof table !== 'undefined') {
                                    table.ajax.reload(null, false);
                                }
                                
                                // If we're in the disconnected consumers modal, close it
                                if ($('#disconnectedConsumersListModal').is(':visible')) {
                                    $('#disconnectedConsumersListModal').modal('hide');
                                }
                                
                                // Show success message
                                showSuccessToast(`${consumerName} has been successfully reconnected!`);
                            } else {
                                showErrorAlert('Reconnection Failed!', response.message);
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            const errorMessage = xhr.responseJSON?.message || 'Failed to reconnect consumer';
                            showErrorAlert('Reconnection Failed!', errorMessage);
                        }
                    });
                }
            });
        }

        // Auto-refresh dashboard data every 30 seconds
        setInterval(function() {
            if (!$('.modal').is(':visible')) { // Only refresh if no modal is open
                updateDashboardCharts();
            }
        }, 30000);

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

        function showSuccessToast(message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'success',
                title: message
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
            // Clear session from localStorage
            localStorage.removeItem('plumberSessionToken');
            localStorage.removeItem('plumberSessionExpiry');
            
            // Show loading state
            Swal.fire({
                title: 'Signing Out...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send logout request to server
            $.ajax({
                url: '/logout',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    window.location.href = '/admin-portal';
                },
                error: function(xhr) {
                    window.location.href = '/admin-portal';
                }
            });
        }
    }
    
    // Start authentication check
    if (checkAuthentication()) {
        // Additional security measures
        preventDirectAccess();
        preventViewSource();
    }
});
</script>

</body>
</html>
