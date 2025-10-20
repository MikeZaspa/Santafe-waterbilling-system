<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Santa Fe Water Billing System - Notice Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="icon" type="image/png" href="image/santafe.png">
    <style>
        :root {
            --primary-color: #d32f2f;
            --primary-light: #ff6659;
            --primary-dark: #9a0007;
            --sidebar-bg: linear-gradient(180deg, #d32f2f 0%, #9a0007 100%);
            --sidebar-text: rgba(255,255,255,0.9);
            --sidebar-hover: rgba(255,255,255,0.1);
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: #f8f9fa;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
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
        }
        
        .sidebar-menu .nav-link i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: all 0.3s;
            padding: 20px;
        }
        
        .header {
            height: 70px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            background: white;
            padding: 0 20px;
            border-radius: 8px;
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

        /* Notice text styles */
        .notice-content {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .notice-content.expanded {
            white-space: normal;
            text-overflow: unset;
            max-width: none;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .main-content.active {
                margin-left: 280px;
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

        /* Mobile overlay styles */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .mobile-overlay.active {
            opacity: 1;
            visibility: visible;
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
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="admin-accountant-dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <!-- Billing -->
            <li class="nav-item">
                <a class="nav-link" href="admin-accountant-consumer">
                    <i class="bi bi-people"></i> Billing
                </a>
            </li>
            
            <!-- Manage Accounts -->
            <li class="nav-item">
                <a class="nav-link" href="admin-consumer-form">
                    <i class="bi bi-person-gear"></i> Manage Accounts
                </a>
            </li>

            <!-- Water Rates -->
            <li class="nav-item">
                <a class="nav-link" href="water-rates">
                    <i class="bi bi-cash-coin"></i> Water Rates
                </a>
            </li>

            <!-- Notices -->
            <li class="nav-item">
                <a class="nav-link active" href="admin-accountant-notice">
                    <i class="bi bi-bell"></i> Notices
                </a>
            </li>

            <!-- Reports -->
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
    <header class="header d-flex align-items-center">
        <button id="sidebarToggle" class="btn d-lg-none me-3">
            <i class="bi bi-list"></i>
        </button>
       
        <div class="ms-auto d-flex align-items-center">
            <!-- Notification Bell for Admin -->
            <div class="position-relative me-3">
                <a href="#" class="text-decoration-none text-dark position-relative" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-5"></i>
                </a>
            </div>
            <!-- User Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <span>Admin</span>
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

    <!-- Notice Management Content -->
    <div class="table-container animate-fadein">
        <div class="table-title">
            <div class="d-flex justify-content-between align-items-center w-100">
                <h3 class="mb-0">Notice Management</h3>
                <button class="btn btn-primary" id="addNoticeBtn" data-bs-toggle="modal" data-bs-target="#noticeModal">
                    <i class="bi bi-plus-circle-fill me-2"></i>
                    Add New Notice
                </button>
            </div>
        </div>
        
        <!-- Filters and Search -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search notices...">
                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <input type="date" class="form-control" id="dateFilter">
            </div>
        </div>
        
        <!-- Notices Table -->
        <div class="table-responsive">
            <table class="table table-hover" id="noticesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Consumer Name</th>
                        <th>Notice Content</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Notices data will be loaded here via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Notice Modal -->
<div class="modal fade" id="noticeModal" tabindex="-1" aria-labelledby="noticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="noticeModalLabel">Add New Notice</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="noticeForm">
                    <input type="hidden" id="noticeId">
                    
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
                                    <input type="text" class="form-control" id="consumerSearch" placeholder="Search consumers...">
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

                    <!-- Notice Content -->
                    <div class="mb-3">
                        <label for="noticeContent" class="form-label fw-bold">Notice Content</label>
                        <textarea class="form-control" id="noticeContent" rows="3" required placeholder="Enter notice content..."></textarea>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="noticeDescription" class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="noticeDescription" rows="3" placeholder="Enter description (optional)..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveNotice">
                    <span class="spinner-border spinner-border-sm d-none" id="saveSpinner"></span>
                    Save Notice
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this notice? This action cannot be undone.</p>
                <input type="hidden" id="deleteNoticeId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
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
    // Initialize DataTable
    const table = $('#noticesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('notices.index') }}",
            type: 'GET',
            data: function(d) {
                d.date = $('#dateFilter').val();
                d.search = $('#searchInput').val();
            }
        },
        columns: [
            { 
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    // Use DataTables' built-in row index for numbering
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { 
                data: 'consumer', 
                name: 'consumer.first_name',
                render: function(data, type, row) {
                    if (!data) return 'N/A';
                    let fullName = data.last_name || '';
                    if (fullName && data.first_name) fullName += ', ';
                    fullName += data.first_name || '';
                    if (data.middle_name) fullName += ' ' + data.middle_name.charAt(0) + '.';
                    if (data.suffix) fullName += ' ' + data.suffix;
                    return fullName.trim();
                }
            },
            { 
                data: 'notice', 
                name: 'notice',
                render: function(data, type, row) {
                    const shortText = data.length > 50 ? data.substring(0, 50) + '...' : data;
                    return `
                        <div class="notice-content" title="${data}">
                            ${shortText}
                        </div>
                    `;
                }
            },
            { 
                data: 'description', 
                name: 'description',
                render: function(data, type, row) {
                    if (!data) return 'No description';
                    const shortText = data.length > 50 ? data.substring(0, 50) + '...' : data;
                    return `
                        <div class="notice-content" title="${data}">
                            ${shortText}
                        </div>
                    `;
                }
            },
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary edit-btn" data-id="${data}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                    `;
                }
            }
        ],
        // Add error handling for DataTables
        error: function(xhr, error, thrown) {
            console.error('DataTables error:', error, thrown);
            // You can show a user-friendly error message here if needed
        }
    });

    // Apply filters
    $('#dateFilter').change(function() {
        table.ajax.reload();
    });

    // Search button
    $('#searchBtn').click(function() {
        table.search($('#searchInput').val()).draw();
    });

    // Search on enter key
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            table.search($(this).val()).draw();
        }
    });

    // Initialize modal when opened
    $('#noticeModal').on('show.bs.modal', function() {
        resetForm();
        fetchConsumers();
    });

    // Reset all form fields
    function resetForm() {
        $('#noticeForm')[0].reset();
        $('#noticeId').val('');
        $('#selectedConsumerText').text('Select Consumer');
        $('#consumer_id').val('');
        $('#noticeModalLabel').text('Add New Notice');
        $('#saveNotice').html('<i class="bi bi-save me-2"></i> Save Notice');
    }

    // Function to fetch consumers and populate dropdown
    function fetchConsumers() {
        $.ajax({
            url: '/notices/consumers',
            type: 'GET',
            dataType: 'json',
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
                            <span class="ms-2">No consumers found</span>
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
            let fullName = consumer.last_name || '';
            if (fullName && consumer.first_name) fullName += ', ';
            fullName += consumer.first_name || '';
            if (consumer.middle_name) fullName += ' ' + consumer.middle_name.charAt(0) + '.';
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
        let fullName = consumer.last_name || '';
        if (fullName && consumer.first_name) fullName += ', ';
        fullName += consumer.first_name || '';
        if (consumer.middle_name) fullName += ' ' + consumer.middle_name.charAt(0) + '.';
        if (consumer.suffix) fullName += ' ' + consumer.suffix;
        fullName = fullName.trim();
        
        // Update consumer display
        $('#selectedConsumerText').text(fullName);
        $('#consumer_id').val(consumer.id);
        
        // Close dropdown
        $('.dropdown-menu').removeClass('show');
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

    // Save notice (create or update)
    $('#saveNotice').click(function() {
        const formData = {
            consumer_id: $('#consumer_id').val(),
            notice: $('#noticeContent').val(),
            description: $('#noticeDescription').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Validation
        if (!formData.consumer_id) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select a consumer'
            });
            return;
        }

        if (!formData.notice.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter notice content'
            });
            return;
        }

        const noticeId = $('#noticeId').val();
        const url = noticeId ? `/notices/${noticeId}` : '/notices';
        const method = noticeId ? 'PUT' : 'POST';

        // Show loading state
        const $saveBtn = $(this);
        const $spinner = $('#saveSpinner');
        $saveBtn.prop('disabled', true);
        $spinner.removeClass('d-none');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#noticeModal').modal('hide');
                    table.ajax.reload();
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
                $saveBtn.prop('disabled', false);
                $spinner.addClass('d-none');
            }
        });
    });

    // Edit notice
    $(document).on('click', '.edit-btn', function() {
        const noticeId = $(this).data('id');
        
        // Show loading state
        $('#noticeModal').modal('show');
        $('#selectedConsumerText').html(`
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        
        $.ajax({
            url: `/notices/${noticeId}/edit`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const notice = response.data;
                    const consumer = notice.consumer;
                    
                    // Fill the form with notice data
                    $('#noticeId').val(notice.id);
                    
                    // Build the full name with proper handling of optional fields
                    let fullName = consumer.last_name || '';
                    if (fullName && consumer.first_name) fullName += ', ';
                    fullName += consumer.first_name || '';
                    if (consumer.middle_name) fullName += ' ' + consumer.middle_name.charAt(0) + '.';
                    if (consumer.suffix) fullName += ' ' + consumer.suffix;
                    fullName = fullName.trim();
                    
                    $('#selectedConsumerText').text(fullName);
                    $('#consumer_id').val(consumer.id);
                    $('#noticeContent').val(notice.notice);
                    $('#noticeDescription').val(notice.description || '');
                    
                    // Update modal title and button text
                    $('#noticeModalLabel').text('Edit Notice');
                    $('#saveNotice').html('<i class="bi bi-save me-2"></i> Update Notice');
                }
            },
            error: function(xhr) {
                // Show error message in consumer display
                $('#selectedConsumerText').text('Error loading consumer');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to load notice data'
                });
            }
        });
    });

    // Delete notice
    $(document).on('click', '.delete-btn', function() {
        const noticeId = $(this).data('id');
        $('#deleteNoticeId').val(noticeId);
        $('#deleteModal').modal('show');
    });

    // Confirm delete
    $('#confirmDelete').click(function() {
        const noticeId = $('#deleteNoticeId').val();
        
        $.ajax({
            url: `/notices/${noticeId}`,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    table.ajax.reload();
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
                    text: xhr.responseJSON?.message || 'Failed to delete notice'
                });
            }
        });
    });

    // Mobile sidebar toggle
    $('#sidebarToggle').click(function() {
        $('.sidebar').toggleClass('active');
        $('.mobile-overlay').toggleClass('active');
        $('.main-content').toggleClass('active');
    });

    $('.mobile-overlay').click(function() {
        $('.sidebar').removeClass('active');
        $('.mobile-overlay').removeClass('active');
        $('.main-content').removeClass('active');
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
                performLogout();
            }
        });
    });

    function performLogout() {
        Swal.fire({
            title: 'Signing Out...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '/logout',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                window.location.href = '/admin-login';
            },
            error: function(xhr) {
                window.location.href = '/admin-login';
            }
        });
    }
});
</script>
</body>
</html>