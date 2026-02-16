<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-0">Announcement Management</h3>
                <small class="text-muted">Create, update, and send announcements to all active consumers</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ url('/admin-dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#announcementModal" id="addAnnouncementBtn">
                    <i class="bi bi-plus-circle"></i> Add Announcement
                </button>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle mb-0" id="announcementsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
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

    <div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="announcementModalLabel">Add Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
    <script>
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
            const alert = $('<div class="alert alert-' + type + ' alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 2000;">' +
                escapeHtml(message) +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>');
            $('body').append(alert);
            setTimeout(() => alert.alert('close'), 3000);
        }

        function renderTable() {
            const rows = announcements.map(a => {
                const statusBadge = a.is_active
                    ? '<span class="badge text-bg-success">Active</span>'
                    : '<span class="badge text-bg-secondary">Inactive</span>';
                const publishedAt = a.published_at ? new Date(a.published_at).toLocaleString() : '-';
                return `
                    <tr>
                        <td>${a.id}</td>
                        <td>${escapeHtml(a.title)}</td>
                        <td style="max-width:420px; white-space:normal;">${escapeHtml(a.message)}</td>
                        <td>${statusBadge}</td>
                        <td>${escapeHtml(publishedAt)}</td>
                        <td class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${a.id}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning toggle-btn" data-id="${a.id}">
                                <i class="bi bi-toggle-${a.is_active ? 'on' : 'off'}"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${a.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            $('#announcementsTable tbody').html(rows || '<tr><td colspan="6" class="text-center text-muted py-4">No announcements found</td></tr>');
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
                url,
                method,
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
            const item = announcements.find(a => a.id === id);
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

        loadAnnouncements();
    </script>
</body>
</html>
