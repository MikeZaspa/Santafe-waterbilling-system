<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Santa Fe Water Billing System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
        
        .map-popup {
            font-family: Arial, sans-serif;
        }
        
        .map-popup h6 {
            margin-bottom: 5px;
            color: #1a73e8;
        }
        
        .map-popup p {
            margin: 2px 0;
            font-size: 12px;
        }
        
        #map {
            height: 300px;
            border-radius: 8px;
            margin-top: 10px;
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
                <a class="nav-link active" href="admin-dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin-consumer">
                    <i class="bi bi-people"></i> Manage Consumers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin-plumber">
                    <i class="bi bi-wrench"></i> Manage Plumber
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin-accountant">
                    <i class="bi bi-cash-stack"></i> Manage Accountant
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="backupDatabaseBtn">
                    <i class="bi bi-cloud-download"></i> Backup Database
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
                <h2 class="header-title">Dashboard Overview</h2>
                <p class="header-subtitle">Santa Fe Water Billing System</p>
            </div>
        </div>
        
        <div class="header-right">
            <div class="position-relative me-3 d-none d-sm-block">
                <i class="bi bi-cloud-download header-icon" id="backupDatabaseIcon" title="Backup Database"></i>
            </div>
            <div class="position-relative me-3 d-none d-sm-block">
                <i class="bi bi-clock-history header-icon" id="adminLogsIcon" data-bs-toggle="modal" data-bs-target="#adminLogsModal" title="Admin Logs"></i>
            </div>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-none d-md-inline">Admin</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><a class="dropdown-item" href="#" id="adminLogsBtn" data-bs-toggle="modal" data-bs-target="#adminLogsModal">Admin Logs</a></li>
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
            <!-- Total Consumers Card -->
            <div class="col-md-6 col-lg-6">
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
            <div class="col-md-6 col-lg-6">
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
        <div class="row g-4 mt-2">
            <!-- Consumer Status Pie Chart -->
            <div class="col-lg-6">
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
            <div class="col-lg-6">
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
    </div>
</div>

<!-- Admin Logs Modal -->
<div class="modal fade" id="adminLogsModal" tabindex="-1" aria-labelledby="adminLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="adminLogsModalLabel">
                    <i class="fas fa-history me-2"></i>Admin Login Logs
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="container-fluid p-3">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form id="logsFilterForm" class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label">Admin</label>
                                    <select name="admin_id" id="adminFilter" class="form-select form-select-sm">
                                        <option value="">All Admins</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Activity</label>
                                    <input type="text" name="activity" id="activityFilter" class="form-control form-control-sm" placeholder="Search activity...">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="date_from" id="dateFromFilter" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date To</label>
                                    <input type="date" name="date_to" id="dateToFilter" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" id="filterLogsBtn" class="btn btn-primary btn-sm me-1">Filter</button>
                                    <button type="button" id="resetLogsBtn" class="btn btn-secondary btn-sm">Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Map Toggle -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <button class="btn btn-outline-primary btn-sm" id="toggleMapBtn">
                                <i class="fas fa-map me-1"></i> Toggle Map
                            </button>
                        </div>
                    </div>

                    <!-- Geolocation Map -->
                    <div id="mapContainer" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">
                                <i class="fas fa-map-marker-alt me-2"></i>Login Locations Map
                            </h6>
                            <small class="text-muted">Click on location pins to see details</small>
                        </div>
                        <div id="map"></div>
                    </div>

                    <!-- Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Admin</th>
                                    <th>IP Address</th>
                                    <th>Location</th>
                                    <th>Device</th>
                                    <th>Activity</th>
                                    <th>Login Time</th>
                                    <th>Logout Time</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="logsTableBody">
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Loading logs...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        <nav id="logsPagination">
                            <!-- Pagination will be loaded here -->
                        </nav>
                    </div>

                    <!-- Statistics -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Quick Statistics</h6>
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="border rounded p-2">
                                                <h4 class="text-primary mb-0" id="totalLogsCount">0</h4>
                                                <small class="text-muted">Total Logs</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-2">
                                                <h4 class="text-success mb-0" id="successfulLoginsCount">0</h4>
                                                <small class="text-muted">Successful Logins</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-2">
                                                <h4 class="text-danger mb-0" id="failedAttemptsCount">0</h4>
                                                <small class="text-muted">Failed Attempts</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-2">
                                                <h4 class="text-warning mb-0" id="activeSessionsCount">0</h4>
                                                <small class="text-muted">Active Sessions</small>
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

<!-- Session Timer Display -->
<div class="session-timer" id="sessionTimer">
    <i class="fas fa-clock me-2"></i>
    Session expires in: <span id="sessionTimeDisplay">03:00</span>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- SweetAlert2 for notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Leaflet for maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
 $(document).ready(function() {
    // Session management variables
    const sessionTimer = document.getElementById('sessionTimer');
    const sessionTimeDisplay = document.getElementById('sessionTimeDisplay');
    let sessionTimeout; // Will store the timeout ID
    let warningTimeout; // Will store the warning timeout ID
    let sessionInterval; // Will store the interval ID for updating the display
    const sessionDuration = 3 * 60 * 1000; // 3 minutes in milliseconds
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
            confirmButtonColor: '#d32f2f',
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
                    text: 'Your session has been extended for another 3 minutes.',
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
            confirmButtonColor: '#d32f2f',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(() => {
            // Redirect to logout endpoint
            document.getElementById('logout-form').submit();
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
    $('#backupDatabaseIcon, #backupDatabaseBtn').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Backup Database',
            text: 'Are you sure you want to backup the database? This may take a few moments.',
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
    let map;
    let markers = [];
    let mapVisible = false;
    let currentPage = 1;
    
    // Initialize map when modal is shown
    $('#adminLogsModal').on('shown.bs.modal', function() {
        if (!map) {
            initMap();
        }
        loadAdminLogs();
        loadAdmins();
    });
    
    // Initialize map
    function initMap() {
        // Default center (Philippines)
        map = L.map('map').setView([12.8797, 121.7740], 5);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
    }
    
    // Load admin logs
    function loadAdminLogs(page = 1) {
        currentPage = page;
        
        // Show loading state
        $('#logsTableBody').html(`
            <tr>
                <td colspan="9" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading logs...</p>
                </td>
            </tr>
        `);
        
        // Get filter values
        const adminId = $('#adminFilter').val();
        const activity = $('#activityFilter').val();
        const dateFrom = $('#dateFromFilter').val();
        const dateTo = $('#dateToFilter').val();
        
        // Build query string
        let queryString = `?page=${page}`;
        if (adminId) queryString += `&admin_id=${adminId}`;
        if (activity) queryString += `&activity=${activity}`;
        if (dateFrom) queryString += `&date_from=${dateFrom}`;
        if (dateTo) queryString += `&date_to=${dateTo}`;
        
        // Fetch logs via AJAX
        $.get(`/admin/logs/api${queryString}`, function(data) {
            renderLogsTable(data.logs.data);
            renderPagination(data.logs);
            updateStatistics(data.statistics);
            
            // Load map data if map is visible
            if (mapVisible) {
                loadAllLocations(data.logs.data);
            }
        }).fail(function() {
            $('#logsTableBody').html(`
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                        <p class="text-muted">Error loading logs. Please try again.</p>
                    </td>
                </tr>
            `);
        });
    }
    
    // Load admins for filter dropdown
    function loadAdmins() {
        $.get('/admin/admins/api', function(data) {
            let options = '<option value="">All Admins</option>';
            data.forEach(function(admin) {
                options += `<option value="${admin.id}">${admin.email}</option>`;
            });
            $('#adminFilter').html(options);
        });
    }
    
    // Render logs table
    function renderLogsTable(logs) {
        if (logs.length === 0) {
            $('#logsTableBody').html(`
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <i class="fas fa-search fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No logs found</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        let html = '';
        logs.forEach(function(log) {
            const loginTime = new Date(log.login_at).toLocaleString();
            const logoutTime = log.logout_at ? new Date(log.logout_at).toLocaleString() : '';
            const duration = log.session_duration ? formatDuration(log.session_duration) : '-';
            const status = log.logout_at ? 
                '<span class="badge bg-secondary">Completed</span>' : 
                '<span class="badge bg-success">Active</span>';
            
            const activityBadge = log.activity.includes('successful') ? 
                'bg-success' : log.activity.includes('failed') ? 'bg-danger' : 'bg-primary';
            
            const location = log.city && log.country ? 
                `<div class="d-flex align-items-center">
                    <span>${log.city}, ${log.country}</span>
                    <i class="fas fa-map-marker-alt location-pin ms-2" 
                       onclick="showLocationOnMap('${log.ip_address}', '${log.city}', '${log.country}', '${log.region || ''}', '${log.email}', '${log.activity}', '${loginTime}')"
                       title="Show on map"></i>
                </div>` : 
                '<span class="text-muted">Unknown</span>';
            
            html += `
                <tr data-log-id="${log.id}" 
                    data-ip="${log.ip_address}"
                    data-country="${log.country}"
                    data-city="${log.city}"
                    data-region="${log.region || ''}"
                    data-email="${log.email}"
                    data-activity="${log.activity}"
                    data-login-time="${loginTime}">
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-2">
                                ${log.email.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="fw-bold">${log.email}</div>
                                <small class="text-muted">
                                    ${log.admin ? `${log.admin.first_name} ${log.admin.last_name}` : 'Admin Deleted'}
                                </small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <code>${log.ip_address}</code>
                    </td>
                    <td>${location}</td>
                    <td>
                        <small>
                            <i class="fas fa-desktop me-1 text-muted"></i> ${log.browser}<br>
                            <i class="fas fa-laptop me-1 text-muted"></i> ${log.platform}
                        </small>
                    </td>
                    <td>
                        <span class="badge ${activityBadge}">${log.activity}</span>
                    </td>
                    <td>
                        <small>
                            ${loginTime.split(',')[0]}<br>
                            ${loginTime.split(',')[1]}
                        </small>
                    </td>
                    <td>
                        ${logoutTime ? 
                            `<small>
                                ${logoutTime.split(',')[0]}<br>
                                ${logoutTime.split(',')[1]}
                            </small>` : 
                            '<span class="badge bg-warning">Active</span>'
                        }
                    </td>
                    <td>
                        ${duration !== '-' ? 
                            `<span class="badge bg-info">${duration}</span>` : 
                            '<span class="text-muted">-</span>'
                        }
                    </td>
                    <td>${status}</td>
                </tr>
            `;
        });
        
        $('#logsTableBody').html(html);
    }
    
    // Render pagination
    function renderPagination(logs) {
        if (logs.last_page <= 1) {
            $('#logsPagination').html('');
            return;
        }
        
        let html = '<ul class="pagination">';
        
        // Previous button
        html += `<li class="page-item ${logs.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadAdminLogs(${logs.current_page - 1}); return false;">Previous</a>
        </li>`;
        
        // Page numbers
        for (let i = 1; i <= logs.last_page; i++) {
            if (i === 1 || i === logs.last_page || (i >= logs.current_page - 2 && i <= logs.current_page + 2)) {
                html += `<li class="page-item ${i === logs.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadAdminLogs(${i}); return false;">${i}</a>
                </li>`;
            } else if (i === logs.current_page - 3 || i === logs.current_page + 3) {
                html += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
            }
        }
        
        // Next button
        html += `<li class="page-item ${logs.current_page === logs.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadAdminLogs(${logs.current_page + 1}); return false;">Next</a>
        </li>`;
        
        html += '</ul>';
        $('#logsPagination').html(html);
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
    
    // Toggle map visibility
    $('#toggleMapBtn').on('click', function() {
        const mapContainer = $('#mapContainer');
        mapVisible = !mapVisible;
        
        if (mapVisible) {
            mapContainer.removeClass('d-none');
            // Refresh map bounds to show all markers
            setTimeout(() => {
                map.invalidateSize();
                if (markers.length > 0) {
                    const group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                } else {
                    // Load all locations if not already loaded
                    loadAllLocations();
                }
            }, 100);
        } else {
            mapContainer.addClass('d-none');
        }
    });
    
    // Load all locations for the map
    function loadAllLocations(logs) {
        // Clear existing markers
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];
        
        // If logs not provided, get them from the table
        if (!logs) {
            logs = [];
            $('#logsTableBody tr[data-country]').each(function() {
                logs.push({
                    country: $(this).data('country'),
                    city: $(this).data('city'),
                    region: $(this).data('region'),
                    ip: $(this).data('ip'),
                    email: $(this).data('email'),
                    activity: $(this).data('activity'),
                    loginTime: $(this).data('login-time')
                });
            });
        }
        
        // Process each log
        logs.forEach(function(log) {
            if (log.country && log.country !== 'Local') {
                geocodeLocation(log.city, log.country, log.region, log.ip, log.email, log.activity, log.loginTime);
            }
        });
    }
    
    // Geocode location and add marker
    function geocodeLocation(city, country, region, ip, email, activity, loginTime) {
        const query = `${city}, ${region}, ${country}`;
        
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    
                    addMarker(lat, lon, city, country, region, ip, email, activity, loginTime);
                }
            })
            .catch(error => {
                console.error('Geocoding error:', error);
            });
    }
    
    // Add marker to map
    function addMarker(lat, lon, city, country, region, ip, email, activity, loginTime) {
        // Determine marker color based on activity
        let markerColor = 'blue';
        if (activity.includes('failed')) {
            markerColor = 'red';
        } else if (activity.includes('successful')) {
            markerColor = 'green';
        }

        // Create custom icon
        const customIcon = L.divIcon({
            html: `<div style="background-color: ${markerColor}; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
            className: 'custom-marker',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        const marker = L.marker([lat, lon], { icon: customIcon }).addTo(map);
        
        // Create popup content
        const popupContent = `
            <div class="map-popup">
                <h6><i class="fas fa-user"></i> ${email}</h6>
                <p><strong>Location:</strong> ${city}, ${country}</p>
                <p><strong>IP Address:</strong> ${ip}</p>
                <p><strong>Activity:</strong> <span class="badge ${activity.includes('successful') ? 'bg-success' : activity.includes('failed') ? 'bg-danger' : 'bg-primary'}">${activity}</span></p>
                <p><strong>Login Time:</strong> ${loginTime}</p>
                <p><strong>Region:</strong> ${region || 'N/A'}</p>
            </div>
        `;
        
        marker.bindPopup(popupContent);
        markers.push(marker);
    }
    
    // Show specific location on map
    window.showLocationOnMap = function(ip, city, country, region, email, activity, loginTime) {
        // Show map if hidden
        if (!mapVisible) {
            $('#toggleMapBtn').click();
        }

        // Clear existing markers and focus on this one
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        if (city && country && country !== 'Local') {
            geocodeLocation(city, country, region, ip, email, activity, loginTime);
            
            // Wait a bit for geocoding to complete, then fit bounds
            setTimeout(() => {
                if (markers.length > 0) {
                    const group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                    
                    // Open the popup
                    markers[0].openPopup();
                }
            }, 500);
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Location Not Available',
                text: 'No geographic location data available for this IP address.',
                confirmButtonColor: '#d32f2f'
            });
        }
    };
    
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
    
    // Notification icon click handler
    $('#notificationIcon').on('click', function() {
        Swal.fire({
            icon: 'info',
            title: 'Notifications',
            text: 'You have no new notifications at this time.',
            confirmButtonColor: '#d32f2f'
        });
    });
});


</script>

</body>
</html>