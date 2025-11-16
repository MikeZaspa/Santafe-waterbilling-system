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
    <!-- Font Awesome for additional icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Leaflet CSS for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
            --white: #ffffff;
            --border: #e0e0e0;
            --text-light: #6c757d;
            --warning: #ffc107;
            --error: #dc3545;
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
        
        /* Map modal styles */
        #mapModal .modal-dialog {
            max-width: 90%;
        }
        
        #mapModal .modal-content {
            height: 80vh;
        }
        
        #mapModal .modal-body {
            height: calc(80vh - 140px);
            padding: 0;
        }
        
        #locationMap {
            height: 100%;
            width: 100%;
        }
        
        .map-info {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
            max-width: 250px;
        }
        
        /* Loading spinner for map */
        .map-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            text-align: center;
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
<div id="mobileOverlay" class="mobile-overlay"></div>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <div id="sidebarHeader" class="sidebar-header text-center">
        <img src="{{ asset('image/santafe.png') }}" class="login-logo img-fluid mb-3">
        <h1 id="sidebarTitle" class="h5">Santa Fe Water Billing</h1>
    </div>
    <nav id="sidebarMenu" class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a id="dashboardLink" class="nav-link active" href="admin-dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a id="consumersLink" class="nav-link" href="admin-consumer">
                    <i class="bi bi-people"></i> Manage Consumers
                </a>
            </li>
            <li class="nav-item">
                <a id="plumberLink" class="nav-link" href="admin-plumber">
                    <i class="bi bi-wrench"></i> Manage Plumber
                </a>
            </li>
            <li class="nav-item">
                <a id="accountantLink" class="nav-link" href="admin-accountant">
                    <i class="bi bi-cash-stack"></i> Manage Accountant
                </a>
            </li>
        </ul>
    </nav>
</div>

<!-- Main Content -->
<div id="mainContent" class="main-content">
    <!-- Header -->
    <header id="header" class="header">
        <div id="headerLeft" class="header-left">
            <button id="sidebarToggle" class="btn d-lg-none me-3 mobile-menu-toggle">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <h2 id="headerTitle" class="header-title">Dashboard Overview</h2>
                <p id="headerSubtitle" class="header-subtitle">Santa Fe Water Billing System</p>
            </div>
        </div>
        
        <div id="headerRight" class="header-right">
            <div class="position-relative me-3 d-none d-sm-block">
                <i class="bi bi-cloud-download header-icon" id="backupDatabaseIcon" title="Backup Database"></i>
            </div>
            <div class="position-relative me-3 d-none d-sm-block">
                <i class="bi bi-clock-history header-icon" id="adminLogsIcon" data-bs-toggle="modal" data-bs-target="#adminLogsModal" title="Admin Logs"></i>
            </div>
            <div id="userDropdown" class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-none d-md-inline">Admin</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                    <li><a id="profileLink" class="dropdown-item" href="#">Profile</a></li>
                    <li><a id="settingsLink" class="dropdown-item" href="#">Settings</a></li>
                    <li><a id="adminLogsBtn" class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#adminLogsModal">Admin Logs</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <!-- In the dropdown menu -->
                    <li>
                        <a id="logoutLink" class="dropdown-item text-danger" href="#">
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
    
    <div id="contentWrapper" class="content-wrapper">
        <div id="statsCards" class="row g-4">
            <!-- Total Consumers Card -->
            <div id="totalConsumersCard" class="col-md-6 col-lg-6">
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
            <div id="activeConsumersCard" class="col-md-6 col-lg-6">
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
        <div id="chartsSection" class="row g-4 mt-2">
            <!-- Consumer Status Pie Chart -->
            <div id="consumerStatusChartContainer" class="col-lg-6">
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
            <div id="totalConsumersChartContainer" class="col-lg-6">
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
<div id="adminLogsModal" class="modal fade" tabindex="-1" aria-labelledby="adminLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div id="adminLogsModalHeader" class="modal-header bg-primary text-white">
                <h5 id="adminLogsModalLabel" class="modal-title">
                    <i class="fas fa-history me-2"></i>Admin Login Logs
                </h5>
                <button id="adminLogsModalClose" type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="adminLogsModalBody" class="modal-body p-0">
                <div id="adminLogsModalContainer" class="container-fluid p-3">
                    <!-- Filters -->
                    <div id="logsFilters" class="row mb-3">
                        <div id="filtersContainer" class="col-12">
                            <form id="logsFilterForm" class="row g-2">
                                
                                <div id="activityFilterContainer" class="col-md-3">
                                    <label id="activityFilterLabel" class="form-label">Activity</label>
                                    <input type="text" name="activity" id="activityFilter" class="form-control form-control-sm" placeholder="Search activity...">
                                </div>
                                <div id="dateFromFilterContainer" class="col-md-2">
                                    <label id="dateFromFilterLabel" class="form-label">Date From</label>
                                    <input type="date" name="date_from" id="dateFromFilter" class="form-control form-control-sm">
                                </div>
                                <div id="dateToFilterContainer" class="col-md-2">
                                    <label id="dateToFilterLabel" class="form-label">Date To</label>
                                    <input type="date" name="date_to" id="dateToFilter" class="form-control form-control-sm">
                                </div>
                                <div id="filterButtonsContainer" class="col-md-2 d-flex align-items-end">
                                    <button type="button" id="filterLogsBtn" class="btn btn-primary btn-sm me-1">Filter</button>
                                    <button type="button" id="resetLogsBtn" class="btn btn-secondary btn-sm">Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Logs Table -->
                    <div id="logsTableContainer" class="table-responsive">
                        <table id="logsTable" class="table table-striped table-hover">
                            <thead id="logsTableHead" class="table-light">
                                <tr id="logsTableHeaderRow">
                                    <th id="idColumnHeader">ID</th>
                                    
                                    <th id="emailColumnHeader">Email</th>
                                    <th id="ipColumnHeader">IP Address</th>
                                    <th id="locationColumnHeader">Location</th>
                                    <th id="deviceColumnHeader">Device</th>
                                    <th id="activityColumnHeader">Activity</th>
                                    <th id="loginTimeColumnHeader">Login Time</th>
                                    <th id="logoutTimeColumnHeader">Logout Time</th>
                                    <th id="durationColumnHeader">Duration</th>
                                    <th id="statusColumnHeader">Status</th>
                                    <th id="actionsColumnHeader">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="logsTableBody">
                                <tr id="loadingRow">
                                    <td id="loadingCell" colspan="11" class="text-center py-4">
                                        <div id="loadingSpinner" class="spinner-border text-primary" role="status">
                                            <span id="loadingSpinnerText" class="visually-hidden">Loading...</span>
                                        </div>
                                        <p id="loadingText" class="mt-2">Loading logs...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
                        <div id="paginationInfo" class="text-muted me-3">
                            Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalRecords">0</span> entries
                        </div>
                        <div class="d-flex align-items-center">
                            <button id="startFirstBtn" type="button" class="btn btn-sm btn-outline-secondary me-1" title="Start">
                                <i class="bi bi-chevron-double-left"></i>
                            </button>
                            <nav id="logsPagination">
                                <!-- Pagination will be loaded here -->
                            </nav>
                            <button id="endLastBtn" type="button" class="btn btn-sm btn-outline-secondary ms-1" title="End">
                                <i class="bi bi-chevron-double-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div id="statisticsContainer" class="row mt-3">
                        <div id="statisticsCardContainer" class="col-12">
                            <div id="statisticsCard" class="card">
                                <div id="statisticsCardBody" class="card-body">
                                    <h6 id="statisticsTitle" class="card-title">Quick Statistics</h6>
                                    <div id="statisticsRow" class="row text-center">
                                        <div id="totalLogsContainer" class="col-md-3">
                                            <div id="totalLogsCard" class="border rounded p-2">
                                                <h4 id="totalLogsCount" class="text-primary mb-0">0</h4>
                                                <small id="totalLogsLabel" class="text-muted">Total Logs</small>
                                            </div>
                                        </div>
                                        <div id="successfulLoginsContainer" class="col-md-3">
                                            <div id="successfulLoginsCard" class="border rounded p-2">
                                                <h4 id="successfulLoginsCount" class="text-success mb-0">0</h4>
                                                <small id="successfulLoginsLabel" class="text-muted">Successful Logins</small>
                                            </div>
                                        </div>
                                        <div id="failedAttemptsContainer" class="col-md-3">
                                            <div id="failedAttemptsCard" class="border rounded p-2">
                                                <h4 id="failedAttemptsCount" class="text-danger mb-0">0</h4>
                                                <small id="failedAttemptsLabel" class="text-muted">Failed Attempts</small>
                                            </div>
                                        </div>
                                        <div id="activeSessionsContainer" class="col-md-3">
                                            <div id="activeSessionsCard" class="border rounded p-2">
                                                <h4 id="activeSessionsCount" class="text-warning mb-0">0</h4>
                                                <small id="activeSessionsLabel" class="text-muted">Active Sessions</small>
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

<!-- Map Modal -->
<div id="mapModal" class="modal fade" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div id="mapModalHeader" class="modal-header bg-primary text-white">
                <h5 id="mapModalLabel" class="modal-title">
                    <i class="fas fa-map-marked-alt me-2"></i>Location Details
                </h5>
                <button id="mapModalClose" type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="mapModalBody" class="modal-body p-0">
                <div id="locationMap"></div>
                <div id="mapLoading" class="map-loading d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading location...</span>
                    </div>
                    <p class="mt-2">Getting location details...</p>
                </div>
                <div id="mapInfo" class="map-info">
                    <h6 id="locationInfoTitle" class="mb-2">Login Location</h6>
                    <div id="locationDetails" class="small">
                        <p id="ipAddressInfo" class="mb-1"><strong>IP Address:</strong> <span id="ipAddressValue">-</span></p>
                        <p id="locationInfo" class="mb-1"><strong>Location:</strong> <span id="locationValue">-</span></p>
                        <p id="coordinatesInfo" class="mb-1"><strong>Coordinates:</strong> <span id="coordinatesValue">-</span></p>
                        <p id="loginTimeInfo" class="mb-1"><strong>Login Time:</strong> <span id="loginTimeValue">-</span></p>
                        <p id="deviceInfo" class="mb-0"><strong>Device:</strong> <span id="deviceValue">-</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Session Timer Display -->
<div id="sessionTimer" class="session-timer">
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
<!-- Leaflet JS for maps -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  $(document).ready(function() {
    // Session management variables
    const sessionTimer = document.getElementById('sessionTimer');
    const sessionTimeDisplay = document.getElementById('sessionTimeDisplay');
    let sessionTimeout;
    let warningTimeout;
    let sessionInterval;
    const sessionDuration = 3 * 60 * 1000; // 3 minutes
    const warningTime = 30 * 1000; // 30 seconds warning
    let sessionStartTime;
    let sessionExpiryTime;
    let isSessionActive = false;
    let map;
    let currentMarker;
    let locationCache = {}; // Cache for location data
    
    // Initialize session management
    function initSessionManagement() {
        document.addEventListener('mousemove', resetSessionTimer);
        document.addEventListener('mousedown', resetSessionTimer);
        document.addEventListener('keypress', resetSessionTimer);
        document.addEventListener('scroll', resetSessionTimer);
        document.addEventListener('touchstart', resetSessionTimer);
        document.addEventListener('click', resetSessionTimer);
        
        startSession();
    }
    
    function startSession() {
        isSessionActive = true;
        sessionStartTime = new Date();
        sessionExpiryTime = new Date(sessionStartTime.getTime() + sessionDuration);
        
        sessionTimer.style.display = 'block';
        updateSessionDisplay();
        
        clearTimeout(sessionTimeout);
        sessionTimeout = setTimeout(() => endSession(), sessionDuration);
        
        clearTimeout(warningTimeout);
        warningTimeout = setTimeout(() => showSessionWarning(), sessionDuration - warningTime);
        
        clearInterval(sessionInterval);
        sessionInterval = setInterval(() => {
            updateSessionDisplay();
            
            const now = new Date();
            const timeLeft = sessionExpiryTime - now;
            
            if (timeLeft <= warningTime && timeLeft > 0) {
                sessionTimer.classList.add('warning');
            } else if (timeLeft <= 30000) {
                sessionTimer.classList.remove('warning');
                sessionTimer.classList.add('danger');
            }
        }, 1000);
    }
    
    function resetSessionTimer() {
        if (!isSessionActive) return;
        
        clearTimeout(sessionTimeout);
        clearTimeout(warningTimeout);
        clearInterval(sessionInterval);
        
        sessionStartTime = new Date();
        sessionExpiryTime = new Date(sessionStartTime.getTime() + sessionDuration);
        
        sessionTimer.classList.remove('warning', 'danger');
        updateSessionDisplay();
        
        sessionTimeout = setTimeout(() => endSession(), sessionDuration);
        warningTimeout = setTimeout(() => showSessionWarning(), sessionDuration - warningTime);
        
        sessionInterval = setInterval(() => {
            updateSessionDisplay();
            
            const now = new Date();
            const timeLeft = sessionExpiryTime - now;
            
            if (timeLeft <= warningTime && timeLeft > 0) {
                sessionTimer.classList.add('warning');
            } else if (timeLeft <= 30000) {
                sessionTimer.classList.remove('warning');
                sessionTimer.classList.add('danger');
            }
        }, 1000);
    }
    
    function updateSessionDisplay() {
        if (!isSessionActive) return;
        
        const now = new Date();
        const timeLeft = Math.max(0, sessionExpiryTime - now);
        
        const minutes = Math.floor(timeLeft / 60000);
        const seconds = Math.floor((timeLeft % 60000) / 1000);
        
        sessionTimeDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    
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
                resetSessionTimer();
                Swal.fire({
                    title: 'Session Extended',
                    text: 'Your session has been extended for another 3 minutes.',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                endSession();
            }
        });
    }
    
    function endSession() {
        isSessionActive = false;
        
        clearTimeout(sessionTimeout);
        clearTimeout(warningTimeout);
        clearInterval(sessionInterval);
        
        sessionTimer.style.display = 'none';
        
        Swal.fire({
            title: 'Session Expired',
            text: 'Your session has expired due to inactivity. Please log in again.',
            icon: 'info',
            confirmButtonColor: '#d32f2f',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(() => {
            document.getElementById('logout-form').submit();
        });
    }
    
    initSessionManagement();
    
    // Mobile sidebar toggle
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

    // Consumer Status Pie Chart
    const consumerStatusCtx = document.getElementById('consumerStatusChart').getContext('2d');
    const consumerStatusChart = new Chart(consumerStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active Consumers'],
            datasets: [{
                data: [{{ $activeConsumers }}],
                backgroundColor: ['#28a745'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
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

    // Total Consumers Line Chart
    const totalConsumersCtx = document.getElementById('totalConsumersChart').getContext('2d');
    const monthlyGrowth = @json($monthlyGrowth);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

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
                    title: { display: true, text: 'Consumers' }
                }
            },
            plugins: {
                legend: { position: 'top' },
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

    // Logout Confirmation
    $('#logoutLink').on('click', function(e) {
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
                Swal.fire({
                    title: 'Logging out...',
                    text: 'Please wait while we securely log you out.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                
                setTimeout(() => {
                    document.getElementById('logout-form').submit();
                }, 1000);
            }
        });
    });
    
    // Database Backup
    $('#backupDatabaseIcon').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Backup Database',
            text: 'Are you sure you want to backup database? This may take a few moments.',
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
                Swal.fire({
                    title: 'Creating Backup...',
                    text: 'Please wait while we create a backup of your database.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                
                $.ajax({
                    url: '/admin/backup-database',
                    type: 'GET',
                    xhrFields: { responseType: 'blob' },
                    success: function(data, status, xhr) {
                        const filename = xhr.getResponseHeader('Content-Disposition') 
                            ? xhr.getResponseHeader('Content-Disposition').split('filename=')[1].replace(/"/g, '')
                            : 'database_backup_' + new Date().toISOString().slice(0, 10) + '.sql';
                        
                        const url = window.URL.createObjectURL(data);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                        
                        Swal.fire({
                            title: 'Backup Successful',
                            text: 'Database backup has been downloaded successfully.',
                            icon: 'success',
                            confirmButtonColor: '#d32f2f',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
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
    
    // Admin Logs Modal
    let currentPage = 1;
    
    $('#adminLogsModal').on('shown.bs.modal', function() {
        loadAdminLogs();
        loadAdmins();
    });
    
    // Map initialization with improved setup
    $('#mapModal').on('shown.bs.modal', function() {
        if (!map) {
            map = L.map('locationMap').setView([20, 0], 2);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18
            }).addTo(map);
        } else {
            setTimeout(() => map.invalidateSize(), 200);
        }
    });
    
    // Complete location detection process
    $('.view-map-btn').on('click', function() {
        const logId = $(this).data('log-id');
        const row = $(`tr[data-log-id="${logId}"]`);
        
        // Extract all data from the row
        const logData = {
            id: logId,
            ip: row.data('ip'),
            country: row.data('country'),
            city: row.data('city'),
            region: row.data('region'),
            email: row.data('email'),
            activity: row.data('activity'),
            loginTime: row.data('login-time'),
            browser: row.data('browser'),
            platform: row.data('platform'),
            latitude: row.data('latitude'),
            longitude: row.data('longitude')
        };
        
        // Update info panel immediately
        updateLocationInfo(logData);
        
        // Show map modal
        $('#mapModal').modal('show');
        
        // Show loading indicator
        $('#mapLoading').removeClass('d-none');
        
        // Start the location detection process
        detectLocation(logData);
    });
    
    // Update location info panel
    function updateLocationInfo(data) {
        $('#ipAddressValue').text(data.ip || 'Unknown');
        $('#locationValue').text(
            data.city && data.country ? 
                `${data.city}, ${data.region ? data.region + ', ' : ''}${data.country}` : 
                'Unknown'
        );
        $('#loginTimeValue').text(data.loginTime || 'Unknown');
        $('#deviceValue').text(
            data.browser && data.platform ? 
                `${data.browser} on ${data.platform}` : 
                'Unknown'
        );
    }
    
    // Main location detection function with multiple fallback methods
    function detectLocation(data) {
        // Method 1: Use stored coordinates if available
        if (data.latitude && data.longitude) {
            const lat = parseFloat(data.latitude);
            const lon = parseFloat(data.longitude);
            
            updateMapWithLocation(lat, lon, {
                ...data,
                resolvedLocation: `${data.city}, ${data.region ? data.region + ', ' : ''}${data.country}`
            });
            
            return;
        }
        
        // Method 2: Try IP-based geolocation
        if (data.ip) {
            getLocationFromIP(data)
                .then(locationData => {
                    updateMapWithLocation(locationData.lat, locationData.lon, {
                        ...data,
                        ...locationData,
                        resolvedLocation: locationData.text
                    });
                })
                .catch(() => {
                    // Method 3: Try geocoding city/country
                    if (data.city && data.country) {
                        geocodeLocation(data.city, data.country, data.region)
                            .then(locationData => {
                                updateMapWithLocation(locationData.lat, locationData.lon, {
                                    ...data,
                                    ...locationData,
                                    resolvedLocation: locationData.text
                                });
                            })
                            .catch(() => {
                                showLocationError("Unable to determine location for this log entry.");
                            });
                    } else {
                        showLocationError("No location information available for this log entry.");
                    }
                });
            
            return;
        }
        
        // Method 4: Try geocoding without IP
        if (data.city && data.country) {
            geocodeLocation(data.city, data.country, data.region)
                .then(locationData => {
                    updateMapWithLocation(locationData.lat, locationData.lon, {
                        ...data,
                        ...locationData,
                        resolvedLocation: locationData.text
                    });
                })
                .catch(() => {
                    showLocationError("Unable to determine location for this log entry.");
                });
        } else {
            showLocationError("No location information available for this log entry.");
        }
    }
    
    // Get location from IP address using multiple services
    function getLocationFromIP(data) {
        return new Promise((resolve, reject) => {
            // Check cache first
            const cacheKey = `ip_${data.ip}`;
            if (locationCache[cacheKey]) {
                resolve(locationCache[cacheKey]);
                return;
            }
            
            // Try ipapi.co first (more reliable)
            $.ajax({
                url: `https://ipapi.co/${data.ip}/json/`,
                method: 'GET',
                timeout: 5000,
                success: function(response) {
                    if (response && response.latitude && response.longitude) {
                        const locationData = {
                            lat: response.latitude,
                            lon: response.longitude,
                            city: response.city || data.city,
                            region: response.region || data.region,
                            country: response.country_name || data.country,
                            text: `${response.city || data.city}, ${response.region || data.region ? (response.region || data.region) + ', ' : ''}${response.country_name || data.country}`,
                            isp: response.org || 'Unknown'
                        };
                        
                        // Cache the result
                        locationCache[cacheKey] = locationData;
                        resolve(locationData);
                    } else {
                        reject();
                    }
                },
                error: function() {
                    // Try ip-api.com as backup
                    $.ajax({
                        url: `http://ip-api.com/json/${data.ip}`,
                        method: 'GET',
                        timeout: 5000,
                        success: function(response) {
                            if (response && response.status === 'success' && response.lat && response.lon) {
                                const locationData = {
                                    lat: response.lat,
                                    lon: response.lon,
                                    city: response.city || data.city,
                                    region: response.regionName || data.region,
                                    country: response.country || data.country,
                                    text: `${response.city || data.city}, ${response.regionName || data.region ? (response.regionName || data.region) + ', ' : ''}${response.country || data.country}`,
                                    isp: response.isp || 'Unknown'
                                };
                                
                                // Cache the result
                                locationCache[cacheKey] = locationData;
                                resolve(locationData);
                            } else {
                                reject();
                            }
                        },
                        error: function() {
                            reject();
                        }
                    });
                }
            });
        });
    }
    
    // Geocode location using Nominatim
    function geocodeLocation(city, country, region) {
        return new Promise((resolve, reject) => {
            const query = `${city}, ${region ? region + ', ' : ''}${country}`;
            const cacheKey = `geo_${query}`;
            
            // Check cache first
            if (locationCache[cacheKey]) {
                resolve(locationCache[cacheKey]);
                return;
            }
            
            $.ajax({
                url: `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&addressdetails=1`,
                method: 'GET',
                timeout: 5000,
                success: function(response) {
                    if (response && response.length > 0) {
                        const result = response[0];
                        const locationData = {
                            lat: parseFloat(result.lat),
                            lon: parseFloat(result.lon),
                            city: result.address.city || result.address.town || city,
                            region: result.address.state || region,
                            country: result.address.country || country,
                            text: `${result.address.city || result.address.town || city}, ${result.address.state || region ? (result.address.state || region) + ', ' : ''}${result.address.country || country}`
                        };
                        
                        // Cache the result
                        locationCache[cacheKey] = locationData;
                        resolve(locationData);
                    } else {
                        reject();
                    }
                },
                error: function() {
                    reject();
                }
            });
        });
    }
    
    // Update map with location data
    function updateMapWithLocation(lat, lon, data) {
        // Update coordinates display
        $('#coordinatesValue').text(`${lat.toFixed(6)}, ${lon.toFixed(6)}`);
        
        // Update location display if we have resolved location
        if (data.resolvedLocation) {
            $('#locationValue').text(data.resolvedLocation);
        }
        
        // Set map view to the location with appropriate zoom
        map.setView([lat, lon], 12);
        
        // Remove existing marker if it exists
        if (currentMarker) {
            map.removeLayer(currentMarker);
        }
        
        // Create custom icon
        const customIcon = L.icon({
            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        
        // Add new marker
        currentMarker = L.marker([lat, lon], { icon: customIcon }).addTo(map);
        
        // Create popup content
        const popupContent = `
            <div style="min-width: 250px;">
                <h6 class="mb-2">Login Location Details</h6>
                <hr class="my-2">
                <p class="mb-1"><strong>IP Address:</strong> ${data.ip}</p>
                <p class="mb-1"><strong>Location:</strong> ${data.resolvedLocation || 'Unknown'}</p>
                <p class="mb-1"><strong>Coordinates:</strong> ${lat.toFixed(6)}, ${lon.toFixed(6)}</p>
                ${data.isp ? `<p class="mb-1"><strong>ISP:</strong> ${data.isp}</p>` : ''}
                <p class="mb-1"><strong>Login Time:</strong> ${data.loginTime}</p>
                <p class="mb-0"><strong>Device:</strong> ${data.browser} on ${data.platform}</p>
            </div>
        `;
        
        // Add popup to marker and open it
        currentMarker.bindPopup(popupContent).openPopup();
        
        // Hide loading indicator
        $('#mapLoading').addClass('d-none');
    }
    
    // Show location error
    function showLocationError(message) {
        $('#mapLoading').addClass('d-none');
        
        Swal.fire({
            title: 'Location Issue',
            text: message,
            icon: 'warning',
            confirmButtonColor: '#d32f2f',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        });
    }
    
    // Load admin logs
    function loadAdminLogs(page = 1) {
        currentPage = page;
        
        $('#logsTableBody').html(`
            <tr>
                <td colspan="11" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading logs...</p>
                </td>
            </tr>
        `);
        
        const activity = $('#activityFilter').val();
        const dateFrom = $('#dateFromFilter').val();
        const dateTo = $('#dateToFilter').val();
        
        let queryString = `?page=${page}`;
        if (activity) queryString += `&activity=${activity}`;
        if (dateFrom) queryString += `&date_from=${dateFrom}`;
        if (dateTo) queryString += `&date_to=${dateTo}`;
        
        $.get(`/admin/logs/api${queryString}`, function(data) {
            renderLogsTable(data.logs.data);
            renderPagination(data.logs);
            updateStatistics(data.statistics);
        }).fail(function() {
            $('#logsTableBody').html(`
                <tr>
                    <td colspan="11" class="text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                        <p class="text-muted">Error loading logs. Please try again.</p>
                    </td>
                </tr>
            `);
        });
    }
    
    // Render logs table
    function renderLogsTable(logs) {
        if (logs.length === 0) {
            $('#logsTableBody').html(`
                <tr>
                    <td colspan="11" class="text-center py-4">
                        <i class="fas fa-search fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No logs found</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        let html = '';
        logs.forEach(function(log, index) {
            const displayId = index + 1;
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
                </div>` : 
                `<span class="text-muted">Unknown</span>`;
            
            html += `
                <tr data-log-id="${log.id}" 
                    data-ip="${log.ip_address}"
                    data-country="${log.country}"
                    data-city="${log.city}"
                    data-region="${log.region || ''}"
                    data-email="${log.email}"
                    data-activity="${log.activity}"
                    data-login-time="${loginTime}"
                    data-browser="${log.browser}"
                    data-platform="${log.platform}"
                    data-latitude="${log.latitude || ''}"
                    data-longitude="${log.longitude || ''}">
                    <td>
                        <span class="badge bg-secondary">${displayId}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-2">
                                ${log.email.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="fw-bold">${log.email}</div>
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
                            <span>${loginTime.split(',')[0]}</span><br>
                            <span>${loginTime.split(',')[1]}</span>
                        </small>
                    </td>
                    <td>
                        ${logoutTime ? 
                            `<small>
                                <span>${logoutTime.split(',')[0]}</span><br>
                                <span>${logoutTime.split(',')[1]}</span>
                            </small>` : 
                            `<span class="badge bg-warning">Active</span>`
                        }
                    </td>
                    <td>
                        ${duration !== '-' ? 
                            `<span class="badge bg-info">${duration}</span>` : 
                            `<span class="text-muted">-</span>`
                        }
                    </td>
                    <td>${status}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary view-map-btn" data-log-id="${log.id}" title="View on Map">
                                <i class="fas fa-map-marked-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        $('#logsTableBody').html(html);
    }
    
    // Render pagination
    function renderPagination(logs) {
        $('#showingFrom').text(logs.from);
        $('#showingTo').text(logs.to);
        $('#totalRecords').text(logs.total);
        
        if (logs.last_page <= 1) {
            $('#logsPagination').html('');
            $('#startFirstBtn').prop('disabled', true);
            $('#endLastBtn').prop('disabled', true);
            return;
        }
        
        let html = '<ul class="pagination">';
        
        html += `<li class="page-item ${logs.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadAdminLogs(${logs.current_page - 1}); return false;">Previous</a>
        </li>`;
        
        for (let i = 1; i <= logs.last_page; i++) {
            if (i === 1 || i === logs.last_page || (i >= logs.current_page - 2 && i <= logs.current_page + 2)) {
                html += `<li class="page-item ${i === logs.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadAdminLogs(${i}); return false;">${i}</a>
                </li>`;
            } else if (i === logs.current_page - 3 || i === logs.current_page + 3) {
                html += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
            }
        }
        
        html += `<li class="page-item ${logs.current_page === logs.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadAdminLogs(${logs.current_page + 1}); return false;">Next</a>
        </li>`;
        
        html += '</ul>';
        $('#logsPagination').html(html);
        
        $('#startFirstBtn').prop('disabled', logs.current_page === 1);
        $('#endLastBtn').prop('disabled', logs.current_page === logs.last_page);
    }
    
    // Update statistics
    function updateStatistics(stats) {
        $('#totalLogsCount').text(stats.total);
        $('#successfulLoginsCount').text(stats.successful);
        $('#failedAttemptsCount').text(stats.failed);
        $('#activeSessionsCount').text(stats.active);
    }
    
    // Format duration
    function formatDuration(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }
    
    // Filter logs
    $('#filterLogsBtn').on('click', function() {
        loadAdminLogs(1);
    });
    
    // Reset filters
    $('#resetLogsBtn').on('click', function() {
        $('#activityFilter').val('');
        $('#dateFromFilter').val('');
        $('#dateToFilter').val('');
        loadAdminLogs(1);
    });
    
    // Pagination buttons
    $('#startFirstBtn').on('click', function() {
        loadAdminLogs(1);
    });
    
    $('#endLastBtn').on('click', function() {
        loadAdminLogs($('#totalRecords').text());
    });
    
    // Notification icon
    $('#notificationIcon').on('click', function() {
        Swal.fire({
            icon: 'info',
            title: 'Notifications',
            text: 'You have no new notifications at this time.',
            confirmButtonColor: '#d32f2f'
        });
    });
    
    // Modal state management
    $('#adminLogsModal').on('shown.bs.modal', function() {
        localStorage.setItem('adminLogsModalVisible', 'true');
    });
    
    $('#adminLogsModal').on('hidden.bs.modal', function() {
        localStorage.setItem('adminLogsModalVisible', 'false');
    });
    
    if (localStorage.getItem('adminLogsModalVisible') === 'true') {
        $('#adminLogsModal').modal('show');
    }
});


</script>

</body>
</html>