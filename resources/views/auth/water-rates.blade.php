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
        
        /* Water Rates specific styles */
        .form-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }
        
        .page-title {
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .rate-section {
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }
        
        .section-header {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            text-align: center;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .no-rates {
            padding: 15px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }
        
        .sequence-number {
            width: 50px;
            text-align: center;
        }
        
        .rate-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .rate-tab {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid transparent;
            border-bottom: none;
            border-radius: 5px 5px 0 0;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .rate-tab.active {
            background-color: var(--primary-color);
            color: white;
            border-color: #dee2e6;
        }
        
        .back-button-container {
            margin-bottom: 20px;
        }
        
        .range-input-error {
            border-color: #dc3545;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
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
                <h2 class="header-title">Water Rates Management</h2>
                <p class="header-subtitle">Santa Fe Water Billing System</p>
            </div>
        </div>
       
        <div class="header-right">
            <!-- Notification Bell for Admin -->
            <div class="position-relative me-3">
                <a href="#" class="text-decoration-none text-dark position-relative" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-5"></i>
                </a>
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
    
    <!-- Dashboard Content -->
    <div class="content-wrapper">
        <div class="container py-4">
            <h1 class="page-title text-center">Water Rates Management</h1>
            
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <!-- Add/Edit Form -->
            <div class="form-container">
                <h4 id="form-title">{{ isset($waterRate) ? 'Edit Water Rate' : 'Add New Water Rate' }}</h4>
                <form method="POST" action="{{ isset($waterRate) ? route('water-rates.update', $waterRate->id) : route('water-rates.store') }}" id="water-rate-form">
                    @csrf
                    @if(isset($waterRate))
                        @method('PUT')
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="residential" {{ isset($waterRate) && $waterRate->type == 'residential' ? 'selected' : '' }}>Residential</option>
                                <option value="commercial" {{ isset($waterRate) && $waterRate->type == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                <option value="institutional" {{ isset($waterRate) && $waterRate->type == 'institutional' ? 'selected' : '' }}>Institutional</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="range" class="form-label">Range (cubic meters)</label>
                            <input type="text" class="form-control" id="range" name="range" 
                                   value="{{ $waterRate->range ?? old('range') }}" 
                                   placeholder="e.g. 0-10, 11-20, etc." required>
                            <div id="range-error" class="error-message">Please enter a valid range (e.g., 0-10)</div>
                        </div>
                        <div class="col-md-4">
                            <label for="amount" class="form-label">Amount (₱)</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                                   value="{{ $waterRate->amount ?? old('amount') }}" 
                                   placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        @if(isset($waterRate))
                            <a href="{{ route('water-rates.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        @endif
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
            
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
                        <thead class="table-primary">
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
                                    <a href="{{ route('water-rates.edit', $rate->id) }}" class="btn btn-sm btn-warning">Edit</a>
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
                        <thead class="table-primary">
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
                                    <a href="{{ route('water-rates.edit', $rate->id) }}" class="btn btn-sm btn-warning">Edit</a>
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
                        <thead class="table-primary">
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
                                    <a href="{{ route('water-rates.edit', $rate->id) }}" class="btn btn-sm btn-warning">Edit</a>
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

<!-- Confirmation Modal -->
<div class="modal fade" id="confirm-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        // Hide error message initially
        $('#range-error').hide();
        
        // Validate range input to only allow numbers and hyphens
        $('#range').on('input', function() {
            // Remove any character that's not a digit or hyphen
            let value = $(this).val().replace(/[^0-9-]/g, '');
            
            // Update input value
            $(this).val(value);
            
            // Validate format (should be like "0-10")
            validateRangeFormat();
        });
        
        // Function to validate range format
        function validateRangeFormat() {
            const rangeValue = $('#range').val();
            const rangePattern = /^\d+-\d+$/; // Pattern for "number-number"
            
            if (rangeValue && !rangePattern.test(rangeValue)) {
                $('#range').addClass('range-input-error');
                $('#range-error').show();
                return false;
            } else {
                $('#range').removeClass('range-input-error');
                $('#range-error').hide();
                return true;
            }
        }
        
        // Form submission validation
        $('#water-rate-form').on('submit', function(e) {
            if (!validateRangeFormat()) {
                e.preventDefault();
                return false;
            }
        });
        
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
</body>
</html>