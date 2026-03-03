<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Santa Fe Water Billing System - Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="image/santalogo.png">
    <style>
        :root {
            --primary-color: #0d6efd;
            --primary-dark: #9a0007;
            --sidebar-bg: #f8f9fa;
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
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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

        .header-left,
        .header-right {
            display: flex;
            align-items: center;
        }

        .header-title {
            margin: 0;
            font-size: 1.5rem;
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
            color: #6c757d;
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .table-title h3 {
            font-weight: 600;
            color: blue;
            margin: 0;
        }

        #addAnnouncementBtn {
            background-color: var(--primary-color);
            border: none;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        #addAnnouncementBtn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
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
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

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

        .btn-action:hover {
            transform: scale(1.08);
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }

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

        .mobile-menu-toggle {
            font-size: 1.5rem;
            padding: 0.25rem 0.5rem;
            border: none;
            background: transparent;
            color: var(--primary-color);
        }

        .login-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
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
                width: 100%;
            }
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
    <div class="mobile-overlay"></div>

    <div id="sidebar" class="sidebar">
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
                <a class="nav-link active " href="admin-announcement">
                    <i class="bi bi-megaphone"></i> Announcements
                </a>
            </li>
        </ul>
        </nav>
    </div>

    <div class="main-content">
        <header class="header">
            <div class="header-left">
                <button id="sidebarToggle" class="btn d-lg-none me-3 mobile-menu-toggle">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <h2 class="header-title">Announcement Management</h2>
                    <p class="header-subtitle">Santa Fe Water Billing System</p>
                </div>
            </div>
            <div class="header-right">
                <div class="position-relative me-3 d-none d-sm-block">
                    <i class="bi bi-bell fs-5"></i>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-none d-md-inline">Admin</span>
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

        <div class="content-wrapper">
            <div class="table-container">
                <div class="table-title">
                    <div>
                        <h3 class="mb-0"><i class="bi bi-megaphone-fill me-2"></i>Announcements</h3>
                       
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#announcementModal" id="addAnnouncementBtn">
                        <i class="bi bi-plus-circle-fill me-2"></i>
                        Add Announcement
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="announcementsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="announcementModalLabel">Add Announcement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="announcementForm">
                        <input type="hidden" id="announcementId">
                        <div class="mb-3">
                            <label for="announcementTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="announcementTitle" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="announcementMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="announcementMessage" rows="5" required maxlength="5000"></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="announcementActive" checked>
                            <label class="form-check-label" for="announcementActive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="saveAnnouncementBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/complaint-notifications.js') }}?v={{ filemtime(public_path('js/complaint-notifications.js')) }}"></script>
    <script>
        $(function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const modal = new bootstrap.Modal(document.getElementById('announcementModal'));
            let announcements = [];

            function escapeHtml(value) {
                if (value === null || value === undefined) return '';
                return String(value)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function showAlert(message, type = 'success') {
                const alert = $(
                    '<div class="alert alert-' + type + ' alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 2000;">' +
                    escapeHtml(message) +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>'
                );
                $('body').append(alert);
                setTimeout(() => alert.alert('close'), 3000);
            }

            function renderTable() {
                const rows = announcements.map((a, index) => {
                    const statusBadge = a.is_active
                        ? '<span class="badge text-bg-success">Active</span>'
                        : '<span class="badge text-bg-secondary">Inactive</span>';
                    const publishedAt = a.published_at ? new Date(a.published_at).toLocaleString() : '-';
                    return `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${escapeHtml(a.title)}</td>
                            <td style="max-width:420px; white-space:normal;">${escapeHtml(a.message)}</td>
                            <td>${statusBadge}</td>
                            <td>${escapeHtml(publishedAt)}</td>
                            <td class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-primary btn-action edit-btn" data-id="${a.id}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning btn-action toggle-btn" data-id="${a.id}" title="Toggle Status">
                                    <i class="bi bi-toggle-${a.is_active ? 'on' : 'off'}"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-action delete-btn" data-id="${a.id}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');

                $('#announcementsTable tbody').html(
                    rows || '<tr><td colspan="6" class="text-center text-muted py-4">No announcements found</td></tr>'
                );
            }

            function loadAnnouncements() {
                $.get('/announcements')
                    .done((response) => {
                        announcements = response.data || [];
                        renderTable();
                    })
                    .fail(() => showAlert('Failed to load announcements', 'danger'));
            }

            function resetForm() {
                $('#announcementId').val('');
                $('#announcementTitle').val('');
                $('#announcementMessage').val('');
                $('#announcementActive').prop('checked', true);
                $('#announcementModalLabel').text('Add Announcement');
            }

            $('#addAnnouncementBtn').on('click', resetForm);

            $('#saveAnnouncementBtn').on('click', function () {
                const id = $('#announcementId').val();
                const payload = {
                    title: $('#announcementTitle').val().trim(),
                    message: $('#announcementMessage').val().trim(),
                    is_active: $('#announcementActive').is(':checked') ? 1 : 0
                };

                if (!payload.title || !payload.message) {
                    showAlert('Title and message are required.', 'warning');
                    return;
                }

                const url = id ? `/announcements/${id}` : '/announcements';
                const method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: payload,
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                }).done((response) => {
                    modal.hide();
                    loadAnnouncements();
                    showAlert(response.message || 'Saved successfully');
                }).fail((xhr) => {
                    const message = xhr.responseJSON?.message || 'Save failed';
                    showAlert(message, 'danger');
                });
            });

            $(document).on('click', '.edit-btn', function () {
                const id = Number($(this).data('id'));
                const item = announcements.find((a) => a.id === id);
                if (!item) return;

                $('#announcementId').val(item.id);
                $('#announcementTitle').val(item.title);
                $('#announcementMessage').val(item.message);
                $('#announcementActive').prop('checked', !!item.is_active);
                $('#announcementModalLabel').text('Edit Announcement');
                modal.show();
            });

            $(document).on('click', '.toggle-btn', function () {
                const id = $(this).data('id');
                $.ajax({
                    url: `/announcements/${id}/toggle-status`,
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                }).done((response) => {
                    loadAnnouncements();
                    showAlert(response.message || 'Status updated');
                }).fail(() => showAlert('Failed to update status', 'danger'));
            });

            $(document).on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                if (!confirm('Delete this announcement?')) return;

                $.ajax({
                    url: `/announcements/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                }).done((response) => {
                    loadAnnouncements();
                    showAlert(response.message || 'Deleted successfully');
                }).fail(() => showAlert('Failed to delete announcement', 'danger'));
            });

            const sidebar = $('#sidebar');
            const header = $('.header');
            const sidebarToggle = $('#sidebarToggle');
            const mobileOverlay = $('.mobile-overlay');

            sidebarToggle.on('click', function () {
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

            mobileOverlay.on('click', function () {
                sidebar.removeClass('active');
                mobileOverlay.removeClass('active');
                header.css('background-color', 'white');
                $('body').css('overflow', '');
            });

            $('.sidebar-menu .nav-link').on('click', function () {
                if ($(window).width() < 992) {
                    sidebar.removeClass('active');
                    mobileOverlay.removeClass('active');
                    header.css('background-color', 'white');
                    $('body').css('overflow', '');
                }
            });

            $(window).on('resize', function () {
                if ($(window).width() >= 992) {
                    sidebar.removeClass('active');
                    mobileOverlay.removeClass('active');
                    header.css('background-color', 'white');
                    $('body').css('overflow', '');
                }
            });

            $('#logoutBtn').on('click', function (e) {
                e.preventDefault();
                if (!confirm('Are you sure you want to sign out?')) return;

                $.ajax({
                    url: '/logout',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                }).always(() => {
                    window.location.href = '/admin-login';
                });
            });

            loadAnnouncements();
        });
    </script>
    @include('auth.partials.admin-complaints-widget')
    <script>
        $(function () {
            initComplaintNotifications({ role: 'admin' });
        });
    </script>
</body>
</html>
