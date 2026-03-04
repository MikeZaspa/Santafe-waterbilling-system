<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Santa Fe Water Billing System - Water Rates</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="image/santalogo.png">
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
            --border-color: #dee2e6;
            --light-border: #e9ecef;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: white;
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
            border: 2px solid var(--primary-color);
        }
        
        .sidebar-menu .nav-link {
            color: gray;
            padding: 0.75rem 1.5rem;
            margin: 0 0.5rem;
            border-radius: 6px;
            transition: all 0.3s;
            border: 1px solid transparent;
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
            padding-right: 20px;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            padding-left: 20px;
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
            border-radius: 8px;
            margin: 20px;
            background-color: white;
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
            
            border-radius: 4px;
            background: transparent;
            color: var(--primary-color);
        }
        
        /* Water Rates specific styles */
        .form-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }
        
        .page-title {
            color: blue;
            font-size: 23px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            font-weight: bold;
        }
        
        .rate-section {
            margin-bottom: 30px;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }
        
        .section-header {
            background-color: #6c757d;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            text-align: center;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .table {
            border: none;
        }
        
        /* Custom table header styles */
        .table-custom-header {
            background-color: #6c757d;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .table-custom-header th {
            padding: 12px 15px;
            border: none;
            position: relative;
        }
        
        .table-custom-header th:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 25%;
            height: 50%;
            width: 1px;
            background-color: rgba(255, 255, 255, 0.3);
        }
        
        .table-custom-header th:first-child {
            border-top-left-radius: 5px;
        }
        
        .table-custom-header th:last-child {
            border-top-right-radius: 5px;
        }
        
        .table td {
            border-color: var(--light-border);
            padding: 12px 15px;
            vertical-align: middle;
        }
        
        .no-rates {
            padding: 15px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
            border-radius: 4px;
            margin: 10px;
        }
        
        .sequence-number {
            width: 50px;
            text-align: center;
        }
        
        .rate-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .rate-tab {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid var(--border-color);
            border-radius: 5px 5px 0 0;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .rate-tab.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--border-color);
        }
        
        .back-button-container {
            margin-bottom: 20px;
        }
        
        .range-input-error {
            border-color: #dc3545;
            border-width: 2px;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            padding: 5px;
            border-radius: 4px;
            background-color: rgba(220, 53, 69, 0.1);
        }
        
        /* Modal styles without borders */
        .modal-content {
            border: none;
            border-radius: 8px;
        }
        
        .modal-header {
            border-bottom: none;
        }
        
        .modal-footer {
            border-top: none;
        }
        
        /* Button styles */
        .btn {
            border-width: 2px;
        }
        
        .btn-warning {
            border-color: #ffc107;
        }
        
        .btn-danger {
            border-color: #dc3545;
        }
        
        .btn-secondary {
            border-color: #6c757d;
        }
        
        /* Form controls */
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 4px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(211, 47, 47, 0.25);
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
            
            /* Don't move main content when sidebar is active on mobile */
            .main-content {
                margin-left: 0;
                width: 100%;
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
                <a id="accountantLink" class="nav-link" href="admin-accountant">
                    <i class="bi bi-cash-stack"></i> Manage Accountant
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="water-rates">
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
                <h2 class="header-title">Water Rates Management</h2>
                <p class="header-subtitle">Santa Fe Water Billing System</p>
            </div>
        </div>
       
        <div class="header-right">
            <div class="position-relative me-3 d-none d-sm-block">
                <i class="bi bi-bell fs-5"></i>
            </div>
            <!-- User Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <span>Admin</span>
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
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="page-title mb-0"><i class="bi bi-rulers me-2"></i>Cubic Range</h1>
                <button type="button" class="btn btn-primary" id="addRateBtn" data-bs-toggle="modal" data-bs-target="#waterRateModal">
                    <i class="bi bi-plus-circle me-2"></i>Add New Water Rate
                </button>
            </div>
            
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <!-- Rate Tabs Navigation -->
            <div class="rate-tabs">
                <div class="rate-tab active" data-tab="residential">Residential</div>
                <div class="rate-tab" data-tab="commercial">Commercial</div>
                <div class="rate-tab" data-tab="institutional">Institutional</div>
            </div>
            
            <!-- Rates Tables Grouped by Type -->
            <div class="rate-section" id="residential-rates">
                <div class="section-header">Residential Rates</div>
                @if($rates->where('type', 'residential')->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-custom-header">
                            <tr>
                                <th class="sequence-number">#</th>
                                <th>Range (m³)</th>
                                <th>Amount (₱)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $residentialCount = 1; @endphp
                            @foreach($rates->where('type', 'residential')->sortBy('range') as $rate)
                            <tr>
                                <td class="sequence-number">{{ $residentialCount++ }}</td>
                                <td>{{ $rate->range }}</td>
                                <td>₱{{ number_format($rate->amount, 2) }}</td>
                                <td>
                                    <a href="{{ route('water-rates.edit', $rate->id) }}"
                                       class="btn btn-sm btn-warning edit-rate-btn"
                                       data-id="{{ $rate->id }}"
                                       data-type="{{ $rate->type }}"
                                       data-range="{{ $rate->range }}"
                                       data-amount="{{ $rate->amount }}">Edit</a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                            data-bs-target="#confirm-modal" data-id="{{ $rate->id }}">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="no-rates">No residential rates found</div>
                @endif
            </div>

            <div class="rate-section" id="commercial-rates" style="display: none;">
                <div class="section-header">Commercial Rates</div>
                @if($rates->where('type', 'commercial')->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-custom-header">
                            <tr>
                                <th class="sequence-number">#</th>
                                <th>Range (m³)</th>
                                <th>Amount (₱)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $commercialCount = 1; @endphp
                            @foreach($rates->where('type', 'commercial')->sortBy('range') as $rate)
                            <tr>
                                <td class="sequence-number">{{ $commercialCount++ }}</td>
                                <td>{{ $rate->range }}</td>
                                <td>₱{{ number_format($rate->amount, 2) }}</td>
                                <td>
                                    <a href="{{ route('water-rates.edit', $rate->id) }}"
                                       class="btn btn-sm btn-warning edit-rate-btn"
                                       data-id="{{ $rate->id }}"
                                       data-type="{{ $rate->type }}"
                                       data-range="{{ $rate->range }}"
                                       data-amount="{{ $rate->amount }}">Edit</a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                            data-bs-target="#confirm-modal" data-id="{{ $rate->id }}">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="no-rates">No commercial rates found</div>
                @endif
            </div>

            <div class="rate-section" id="institutional-rates" style="display: none;">
                <div class="section-header">Institutional Rates</div>
                @if($rates->where('type', 'institutional')->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-custom-header">
                            <tr>
                                <th class="sequence-number">#</th>
                                <th>Range (m³)</th>
                                <th>Amount (₱)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $institutionalCount = 1; @endphp
                            @foreach($rates->where('type', 'institutional')->sortBy('range') as $rate)
                            <tr>
                                <td class="sequence-number">{{ $institutionalCount++ }}</td>
                                <td>{{ $rate->range }}</td>
                                <td>
                                    @if($rate->range === '0-5')
                                        Free
                                    @else
                                        ₱{{ number_format($rate->amount, 2) }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('water-rates.edit', $rate->id) }}"
                                       class="btn btn-sm btn-warning edit-rate-btn"
                                       data-id="{{ $rate->id }}"
                                       data-type="{{ $rate->type }}"
                                       data-range="{{ $rate->range }}"
                                       data-amount="{{ $rate->amount }}">Edit</a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                            data-bs-target="#confirm-modal" data-id="{{ $rate->id }}">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="no-rates">No institutional rates found</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Water Rate Modal -->
<div class="modal fade" id="waterRateModal" tabindex="-1" aria-labelledby="waterRateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="waterRateModalLabel">Add New Water Rate</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="waterRateForm">
                    <input type="hidden" id="rateId">
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="modalType" class="form-label fw-bold">Type</label>
                            <select class="form-select" id="modalType" name="type" required>
                                <option value="">Select Type</option>
                                <option value="residential">Residential</option>
                                <option value="commercial">Commercial</option>
                                <option value="institutional">Institutional</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="modalRange" class="form-label fw-bold">Range </label>
                            <input type="text" class="form-control" id="modalRange" name="range" 
                                   placeholder="e.g. 0-10, 11-20, etc." required>
                            <div id="modalRangeError" class="error-message">Please enter a valid range (e.g., 0-10)</div>
                        </div>
                        <div class="col-md-4">
                            <label for="modalAmount" class="form-label fw-bold">Amount (₱)</label>
                            <input type="number" step="0.01" class="form-control" id="modalAmount" name="amount" 
                                   placeholder="0.00" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveRate">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirm-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this water rate?
            </div>
            <div class="modal-footer">
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
    $(document).ready(function() {
        const waterRateModalEl = document.getElementById('waterRateModal');
        const waterRateModal = waterRateModalEl ? bootstrap.Modal.getOrCreateInstance(waterRateModalEl) : null;

        // Hide error messages initially
        $('#modalRangeError').hide();
        
        // Validate range input to only allow numbers and hyphens
        $('#modalRange').on('input', function() {
            // Remove any character that's not a digit or hyphen
            let value = $(this).val().replace(/[^0-9-]/g, '');
            
            // Update input value
            $(this).val(value);
            
            // Validate format (should be like "0-10")
            validateRangeFormat();
        });
        
        // Function to validate range format
        function validateRangeFormat() {
            const rangeValue = $('#modalRange').val();
            const rangePattern = /^\d+-\d+$/; // Pattern for "number-number"
            
            if (rangeValue && !rangePattern.test(rangeValue)) {
                $('#modalRange').addClass('range-input-error');
                $('#modalRangeError').show();
                return false;
            } else {
                $('#modalRange').removeClass('range-input-error');
                $('#modalRangeError').hide();
                return true;
            }
        }
        
        // Handle tab switching
        $('.rate-tab').click(function() {
            const tabId = $(this).data('tab');
            
            // Update active tab
            $('.rate-tab').removeClass('active');
            $(this).addClass('active');
            
            // Show corresponding section
            $('.rate-section').hide();
            $(`#${tabId}-rates`).show();
        });
        
        // Handle delete confirmation
        $('#confirm-modal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const rateId = button.data('id');
            const form = $('#delete-form');
            form.attr('action', '/water-rates/' + rateId);
        });

        // Reset all form fields
        function resetForm() {
            $('#waterRateForm')[0].reset();
            $('#rateId').val('');
            $('#waterRateModalLabel').text('Add New Water Rate');
            $('#saveRate').html('<i class="bi bi-save me-2"></i> Save');
            $('#modalRangeError').hide();
            $('#modalRange').removeClass('range-input-error');
        }

        // Save water rate (create or update)
        $('#saveRate').click(function() {
            const formData = {
                type: $('#modalType').val(),
                range: $('#modalRange').val(),
                amount: $('#modalAmount').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Validation
            if (!formData.type) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select a type'
                });
                return;
            }

            if (!formData.range.trim()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please enter a range'
                });
                return;
            }

            if (!validateRangeFormat()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please enter a valid range (e.g., 0-10)'
                });
                return;
            }

            if (!formData.amount || parseFloat(formData.amount) <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please enter a valid amount'
                });
                return;
            }

            const rateId = $('#rateId').val();
            const url = rateId ? `/water-rates/${rateId}` : '/water-rates';
            const method = rateId ? 'PUT' : 'POST';

            // Show loading state
            const $saveBtn = $(this);
            $saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

            $.ajax({
                url: url,
                type: method,
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (waterRateModal) {
                            waterRateModal.hide();
                        }
                        location.reload(); // Simple reload to show updated data
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
                        // Validation errors
                        const errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        for (const field in errors) {
                            errorMessages += errors[field].join('<br>') + '<br>';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMessages
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'An error occurred'
                        });
                    }
                },
                complete: function() {
                    $saveBtn.prop('disabled', false).html('<i class="bi bi-save me-2"></i> Save');
                }
            });
        });

        // Edit rate button click handler
        $(document).on('click', '.edit-rate-btn', function(e) {
            e.preventDefault();

            resetForm();
            $('#rateId').val($(this).data('id'));
            $('#modalType').val($(this).data('type'));
            $('#modalRange').val($(this).data('range'));
            $('#modalAmount').val($(this).data('amount'));
            $('#waterRateModalLabel').text('Edit Water Rate');
            $('#saveRate').html('<i class="bi bi-save me-2"></i> Update');

            if (waterRateModal) {
                waterRateModal.show();
            }
        });

        // Add rate button click handler
        $('#addRateBtn').click(function() {
            resetForm();
            if (waterRateModal) {
                waterRateModal.show();
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
@include('auth.partials.admin-complaints-widget')
<script src="{{ asset('js/complaint-notifications.js') }}?v={{ filemtime(public_path('js/complaint-notifications.js')) }}"></script>
<script>
    $(function () {
        initComplaintNotifications({
            role: 'admin',
            pollingInterval: 5000
        });
    });
</script>
</body>
</html>
