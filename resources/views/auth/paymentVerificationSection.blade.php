<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Payment Verification</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <!-- SweetAlert2 for notifications -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
      --primary: #4361ee;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --danger: #f72585;
      --warning: #f8961e;
      --light-bg: #f5f7fb;
      --gray: #6c757d;
    }

    body {
      background-color: var(--light-bg);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
      color: #333;
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
      border-bottom: 1px solid rgba(0,0,0,0.1);
      text-align: center;
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
    
    .login-logo {
      width: 100px;       
      height: 100px;      
      border-radius: 50%; 
      object-fit: cover;  
      margin-bottom: 15px;
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
      margin: 20px;
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
      .main-content {
        margin-left: 0;
      }
    }


    /* Payment Verification Styles */
    .table-container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      padding: 25px;
      margin: 30px auto;
      max-width: 1400px;
    }

    .table-title {
      padding-bottom: 20px;
      margin-bottom: 25px;
      border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    }

    .table-title h3 {
      color: var(--primary);
      font-weight: 700;
      font-size: 1.5rem;
    }

    .form-control, .form-select {
      border-radius: 8px;
      padding: 10px 14px;
      border: 1px solid #ddd;
      box-shadow: none;
      font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0.25rem rgba(67, 97, 238, 0.15);
    }

    table thead th {
      background-color: var(--primary);
      color: white;
      font-weight: 600;
      padding: 14px 10px;
      border: none;
    }

    table tbody tr {
      transition: all 0.2s ease;
    }

    table tbody tr:hover {
      background-color: rgba(67, 97, 238, 0.05);
    }

    table tbody td {
      padding: 14px 10px;
      vertical-align: middle;
    }

    .status-badge {
      padding: 6px 12px;
      border-radius: 50px;
      font-size: 0.82rem;
      font-weight: 500;
    }

    .status-pending {
      background-color: rgba(248, 150, 30, 0.15);
      color: var(--warning);
    }

    .status-verified {
      background-color: rgba(76, 201, 240, 0.15);
      color: var(--success);
    }

    .status-rejected {
      background-color: rgba(247, 37, 133, 0.15);
      color: var(--danger);
    }

    .notification-badge {
      position: absolute;
      top: -6px;
      right: -8px;
      min-width: 18px;
      height: 18px;
      padding: 0 5px;
      border-radius: 999px;
      background: #dc3545;
      color: #fff;
      font-size: 0.7rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      line-height: 1;
    }

    .notification-dropdown {
      width: 360px;
      max-width: 92vw;
      padding: 0;
    }

    .notification-actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 15px;
      border-bottom: 1px solid #eef0f3;
    }

    .notification-list {
      max-height: 360px;
      overflow-y: auto;
    }

    .notification-item {
      display: block;
      padding: 12px 15px;
      border-bottom: 1px solid #f1f3f5;
      text-decoration: none;
      color: inherit;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .notification-item:hover {
      background-color: rgba(67, 97, 238, 0.06);
    }

    .notification-item:last-child {
      border-bottom: none;
    }

    .notification-title {
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 2px;
    }

    .notification-message {
      font-size: 0.84rem;
      color: #5e6670;
      margin-bottom: 2px;
      line-height: 1.3;
    }

    .notification-time {
      font-size: 0.75rem;
      color: #8c939b;
    }

    .notification-empty {
      padding: 18px 15px;
      text-align: center;
      color: #6c757d;
      font-size: 0.9rem;
    }

    .modal-content {
      border-radius: 12px;
      border: none;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
      padding: 15px 25px;
      background-color: var(--primary);
      color: white;
    }

    .modal-body {
      padding: 25px;
    }

    .modal-footer {
      border-bottom-left-radius: 12px;
      border-bottom-right-radius: 12px;
      padding: 15px 25px;
    }

    .payment-details strong {
      min-width: 120px;
      color: var(--gray);
      display: inline-block;
    }

    .proof-image-frame {
      width: 100%;
      height: 320px;
      border-radius: 8px;
      border: 1px solid #eee;
      background-color: #f8f9fa;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    #verifyProofImage {
      width: 100%;
      height: 100%;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      object-fit: contain;
      background-color: #f8f9fa;
    }

    .image-placeholder {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f8f9fa;
      border-radius: 8px;
      border: 2px dashed #dee2e6;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
      border-radius: 8px !important;
      padding: 6px 12px;
      margin: 0 3px;
      border: 1px solid #ddd;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
      background: var(--primary);
      color: white !important;
      border: 1px solid var(--primary);
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

    @media (max-width: 768px) {
      .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start;
      }

      .d-flex.justify-content-between .d-flex {
        margin-top: 15px;
        width: 100%;
      }

      #paymentSearch {
        margin-bottom: 10px;
      }
      
      .modal-dialog {
        margin: 1rem;
      }
    }
  </style>
</head>
<body>

<!-- Mobile Overlay -->
<div class="mobile-overlay"></div>

<!-- Sidebar -->
<div class="sidebar">
  <div class="sidebar-header text-center">
    <img src="{{ asset('image/santafe.png') }}" class="login-logo img-fluid">
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
        <a class="nav-link" href="admin-accountant-reports">
          <i class="bi bi-file-earmark-bar-graph"></i> Reports
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="{{ route('paymentVerificationSection') }}">
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
        <h2 class="header-title">Payment Verification</h2>
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
    <!-- Payment Verification Content -->
    <div class="table-container animate-fadein" id="paymentVerificationSection">
      <div class="table-title">
        <div class="d-flex justify-content-between align-items-center w-100">
          <h3 class="mb-0"><i class="bi bi-credit-card"></i> Payment Verification</h3>
          <div class="d-flex">
            <input type="text" class="form-control me-2" id="paymentSearch" placeholder="Search payments...">
            <select class="form-select" id="paymentStatusFilter">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="verified">Verified</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover" id="paymentsTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Consumer</th>
              <th>Meter No.</th>
              <th>Amount</th>
              <th>Method</th>
              <th>Reference No.</th>
              <th>Submitted</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- AJAX Data -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="paymentVerificationModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-search"></i> Verify Payment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <h6 class="text-primary fw-bold">Payment Details</h6>
            <div class="bg-light p-3 rounded">
              <p><strong>Consumer:</strong> <span id="verifyConsumer"></span></p>
              <p><strong>Meter No:</strong> <span id="verifyMeterNo"></span></p>
              <p><strong>Amount:</strong> ₱<span id="verifyAmount"></span></p>
              <p><strong>Method:</strong> <span id="verifyMethod"></span></p>
              <p><strong>Reference No:</strong> <span id="verifyReference"></span></p>
              <p><strong>Submitted:</strong> <span id="verifySubmitted"></span></p>
            </div>
          </div>
          <div class="col-md-6">
            <h6 class="text-primary fw-bold">Proof of Payment</h6>
            <div id="imageContainer" class="proof-image-frame">
              <img id="verifyProofImage" class="img-fluid rounded shadow-sm" alt="Proof of Payment">
            </div>
          </div>
        </div>
        <div class="mt-3">
          <label for="adminNotes" class="form-label fw-bold">Admin Notes</label>
          <textarea class="form-control" id="adminNotes" rows="3" placeholder="Add notes..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" id="rejectPaymentBtn"><i class="bi bi-x-circle"></i> Reject</button>
        <button class="btn btn-success" id="approvePaymentBtn"><i class="bi bi-check-circle"></i> Approve</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
  // Payment verification functionality
 $(document).ready(function() {
    // Initialize payments table
    const paymentsTable = $('#paymentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.payments.datatable') }}",
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.log('DataTables error:', xhr.responseJSON);
                let errorMsg = "Failed to load data";
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                Swal.fire('Error', errorMsg, 'error');
            }
        },
        columns: [
            {
                data: null,
                name: 'id',
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { 
                data: 'admin_consumer', 
                name: 'adminConsumer.first_name',
                render: function(data, type, row) {
                    // Handle both object and string formats
                    if (typeof data === 'object' && data !== null) {
                        return (data.first_name || '') + ' ' + (data.last_name || '');
                    }
                    return 'N/A';
                }
            },
            { 
                data: 'meter_no', 
                name: 'adminConsumer.meter_no',
                render: function(data, type, row) {
                    // Check if we have adminConsumer data in row
                    if (row.admin_consumer && typeof row.admin_consumer === 'object') {
                        return row.admin_consumer.meter_no || 'N/A';
                    }
                    return data || 'N/A';
                }
            },
            { 
                data: 'amount', 
                name: 'amount',
                render: function(data) {
                    return '₱' + parseFloat(data).toFixed(2);
                }
            },
            { data: 'payment_method', name: 'payment_method' },
            { data: 'reference_number', name: 'reference_number' },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data) {
                    return data ? moment(data).format('MMM D, YYYY h:mm A') : '';
                }
            },
            {
                data: 'status',
                name: 'status',
                render: function(data) {
                    let badgeClass = 'status-pending';
                    let statusText = 'Pending';
                    
                    if (data === 'verified') {
                        badgeClass = 'status-verified';
                        statusText = 'Verified';
                    } 
                    else if (data === 'rejected') {
                        badgeClass = 'status-rejected';
                        statusText = 'Rejected';
                    }
                    
                    return `<span class="status-badge ${badgeClass}">${statusText}</span>`;
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
                        <button class="btn btn-sm btn-info view-payment-btn" data-id="${data}">
                            <i class="bi bi-eye"></i> View
                        </button>
                    </div>
                    `;
                }
            }
        ]
    });

    const notificationBadge = $('#notificationBadge');
    const notificationList = $('#notificationList');
    let knownPendingPaymentIds = new Set();

    function renderNotificationBadge(count) {
        if (count > 0) {
            notificationBadge.text(count > 99 ? '99+' : count).removeClass('d-none');
        } else {
            notificationBadge.addClass('d-none').text('0');
        }
    }

    function renderNotificationList(notifications) {
        if (!notifications.length) {
            notificationList.html('<div class="notification-empty">No pending payments.</div>');
            return;
        }

        const notificationHtml = notifications.map(function(payment) {
            const submittedText = payment.created_at ? moment(payment.created_at).fromNow() : 'Just now';
            const amount = parseFloat(payment.amount || 0).toFixed(2);
            return `
                <a href="#" class="notification-item payment-notification-item" data-id="${payment.id}">
                    <div class="notification-title">${payment.consumer_name}</div>
                    <div class="notification-message">
                        Meter: ${payment.meter_no || 'N/A'} | Ref: ${payment.reference_number || 'N/A'} | Amount:${amount}
                    </div>
                    <div class="notification-time">Submitted ${submittedText}</div>
                </a>
            `;
        }).join('');

        notificationList.html(notificationHtml);
    }

    function fetchPendingPaymentNotifications(showToastForNew = false) {
        $.ajax({
            url: "{{ route('admin.payments.pending-notifications') }}",
            type: 'GET',
            data: { limit: 10 },
            success: function(response) {
                if (!response.success) return;

                const notifications = response.notifications || [];
                const currentIds = new Set(notifications.map(item => Number(item.id)));
                const newPayments = showToastForNew
                    ? notifications.filter(item => !knownPendingPaymentIds.has(Number(item.id)))
                    : [];

                renderNotificationBadge(response.pending_count || notifications.length);
                renderNotificationList(notifications);
                knownPendingPaymentIds = currentIds;

                if (newPayments.length > 0) {
                    const title = newPayments.length === 1
                        ? '1 new payment submitted'
                        : `${newPayments.length} new payments submitted`;

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: title,
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true
                    });

                    paymentsTable.ajax.reload(null, false);
                }
            }
        });
    }

    // Apply filters
    $('#paymentSearch').on('keyup', function() {
        paymentsTable.search(this.value).draw();
    });
    
    $('#paymentStatusFilter').change(function() {
        const status = $(this).val();
        paymentsTable.column(7).search(status).draw();
    });

    function openPaymentDetails(paymentId) {
        // Show loading
        $('#paymentVerificationModal').modal('show');
        $('#verifyConsumer').text('Loading...');
        $('#verifyMeterNo').text('Loading...');
        
        // Show loading placeholder for image
        $('#imageContainer').html(`
            <div class="image-placeholder">
                <div class="text-center text-muted">
                    <i class="bi bi-image" style="font-size: 3rem;"></i>
                    <p class="mt-2">Loading image...</p>
                </div>
            </div>
        `);
        
        // Fetch payment details
        $.ajax({
            url: `{{ url('/admin/payments') }}/${paymentId}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const payment = response.data;
                    
                    // Extract consumer information from multiple possible sources
                    let consumerName = 'N/A';
                    let meterNo = 'N/A';
                    
                    // Try to get consumer data from different locations
                    if (payment.admin_consumer) {
                        // If admin_consumer is available
                        consumerName = (payment.admin_consumer.first_name || '') + ' ' + (payment.admin_consumer.last_name || '');
                        meterNo = payment.admin_consumer.meter_no || 'N/A';
                    } else if (payment.bill && payment.bill.consumer) {
                        // If bill.consumer is available
                        consumerName = (payment.bill.consumer.first_name || '') + ' ' + (payment.bill.consumer.last_name || '');
                        meterNo = payment.bill.consumer.meter_no || 'N/A';
                    } else if (payment.consumer) {
                        // If consumer is directly available
                        consumerName = (payment.consumer.first_name || '') + ' ' + (payment.consumer.last_name || '');
                        meterNo = payment.consumer.meter_no || 'N/A';
                    }
                    
                    // Fill payment details
                    $('#verifyConsumer').text(consumerName.trim() || 'N/A');
                    $('#verifyMeterNo').text(meterNo);
                    $('#verifyAmount').text(parseFloat(payment.amount || 0).toFixed(2));
                    $('#verifyMethod').text(payment.payment_method || 'N/A');
                    $('#verifyReference').text(payment.reference_number || 'N/A');
                    $('#verifySubmitted').text(
                        payment.created_at ? 
                        moment(payment.created_at).format('MMM D, YYYY h:mm A') : 
                        'N/A'
                    );
                    
                    // Set proof image
                    if (payment.proof_image) {
                        // Create image element with error handling
                        const img = document.createElement('img');
                        img.id = 'verifyProofImage';
                        img.className = 'img-fluid rounded shadow-sm';
                        img.alt = 'Proof of Payment';
                        
                        img.onload = function() {
                            $('#imageContainer').html(img);
                        };
                        
                        img.onerror = function() {
                            $('#imageContainer').html(`
                                <div class="image-placeholder">
                                    <div class="text-center text-muted">
                                        <i class="bi bi-x-circle" style="font-size: 3rem;"></i>
                                        <p class="mt-2">Failed to load image</p>
                                        <div class="mt-3">
                                            <button class="btn btn-sm btn-outline-primary me-2" onclick="window.open('{{ url('/payment-proof') }}/${paymentId}', '_blank')">
                                                <i class="bi bi-box-arrow-up-right"></i> Open in New Tab
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `);
                        };
                        
                        // Use dedicated endpoint
                        const imagePath = `{{ url('/payment-proof') }}/${paymentId}`;
                        img.src = imagePath;
                    } else {
                        // Show placeholder when no image is available
                        $('#imageContainer').html(`
                            <div class="image-placeholder">
                                <div class="text-center text-muted">
                                    <i class="bi bi-image" style="font-size: 3rem;"></i>
                                    <p class="mt-2">No proof image available</p>
                                </div>
                            </div>
                        `);
                    }
                    
                    // Set admin notes if exists
                    $('#adminNotes').val(payment.admin_notes || '');
                    
                    // Enable/disable buttons based on status
                    if (payment.status !== 'pending') {
                        $('#approvePaymentBtn, #rejectPaymentBtn').prop('disabled', true);
                    } else {
                        $('#approvePaymentBtn, #rejectPaymentBtn').prop('disabled', false);
                    }
                    
                    // Store payment ID for verification
                    $('#approvePaymentBtn, #rejectPaymentBtn').data('id', paymentId);
                }
            },
            error: function(xhr) {
                // Show error message in consumer display
                $('#verifyConsumer').text('Error loading consumer');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to load payment data'
                });
            }
        });
    }

    // View payment details from table
    $(document).on('click', '.view-payment-btn', function() {
        const paymentId = $(this).data('id');
        openPaymentDetails(paymentId);
    });

    // View payment details from notification dropdown
    $(document).on('click', '.payment-notification-item', function(e) {
        e.preventDefault();
        const paymentId = $(this).data('id');
        openPaymentDetails(paymentId);
    });

    $('#refreshNotificationsBtn').on('click', function(e) {
        e.preventDefault();
        fetchPendingPaymentNotifications(false);
    });

    fetchPendingPaymentNotifications(false);
    setInterval(function() {
        fetchPendingPaymentNotifications(true);
    }, 15000);

    // Approve payment
    $('#approvePaymentBtn').click(function() {
        const paymentId = $(this).data('id');
        const notes = $('#adminNotes').val();
        
        verifyPayment(paymentId, 'verified', notes);
    });

    // Reject payment
    $('#rejectPaymentBtn').click(function() {
        const paymentId = $(this).data('id');
        const notes = $('#adminNotes').val();
        
        verifyPayment(paymentId, 'rejected', notes);
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
            url: "{{ route('logout') }}", // Your logout route
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Redirect to login page
                window.location.href = "{{ url('/admin-login') }}";
            },
            error: function(xhr) {
                // If AJAX fails, still redirect to login
                window.location.href = "{{ url('/admin-login') }}";
            }
        });
        
        // Alternative: Simple redirect (if no server-side logout needed)
        // window.location.href = '/login';
    }

    // Verify payment function
    function verifyPayment(paymentId, status, notes) {
        $.ajax({
            url: `{{ url('/admin/payments') }}/${paymentId}/verify`,
            type: 'POST',
            data: {
                status: status,
                admin_notes: notes,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#approvePaymentBtn, #rejectPaymentBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');
            },
            success: function(response) {
                if (response.success) {
                    $('#paymentVerificationModal').modal('hide');
                    paymentsTable.ajax.reload();
                    fetchPendingPaymentNotifications(false);
                    
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
                    $('#approvePaymentBtn, #rejectPaymentBtn').prop('disabled', false).html(status === 'verified' ? '<i class="bi bi-check-circle"></i> Approve' : '<i class="bi bi-x-circle"></i> Reject');
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to process payment.'
                });
                $('#approvePaymentBtn, #rejectPaymentBtn').prop('disabled', false).html(status === 'verified' ? '<i class="bi bi-check-circle"></i> Approve' : '<i class="bi bi-x-circle"></i> Reject');
            }
        });
    }
});
</script>
</body>
</html>
