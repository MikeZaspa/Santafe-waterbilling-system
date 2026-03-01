(function (window, $) {
    'use strict';

    if (!$) {
        return;
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function shortenText(value, maxLength) {
        const text = String(value || '');
        if (text.length <= maxLength) {
            return text;
        }
        return text.slice(0, maxLength - 1) + '...';
    }

    function getEndpoints(role) {
        if (role === 'plumber') {
            return {
                list: '/plumber/complaint-notifications',
                markRead: '/plumber/complaint-notifications/read-all',
            };
        }

        return {
            list: '/admin/complaint-notifications',
            markRead: '/admin/complaint-notifications/read-all',
        };
    }

    function ensureStyles() {
        if (document.getElementById('complaintNotificationStyles')) {
            return;
        }

        const style = document.createElement('style');
        style.id = 'complaintNotificationStyles';
        style.textContent = [
            '.complaint-notification-badge {',
            '  position: absolute;',
            '  top: -6px;',
            '  right: -8px;',
            '  min-width: 18px;',
            '  height: 18px;',
            '  border-radius: 999px;',
            '  background-color: #0d6efd;',
            '  color: #fff;',
            '  font-size: 11px;',
            '  font-weight: 700;',
            '  line-height: 18px;',
            '  text-align: center;',
            '  padding: 0 5px;',
            '  border: 2px solid #fff;',
            '  z-index: 4;',
            '}',
            '.complaint-notification-dropdown {',
            '  width: min(390px, 92vw);',
            '  padding: 0;',
            '  border: 1px solid #cfd6df;',
            '  border-radius: 6px;',
            '  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.12);',
            '  overflow: hidden;',
            '  margin-top: 10px;',
            '}',
            '.complaint-notification-header {',
            '  display: flex;',
            '  align-items: center;',
            '  justify-content: space-between;',
            '  padding: 10px 14px;',
            '  border-bottom: 1px solid #dde3eb;',
            '  background: #ffffff;',
            '}',
            '.complaint-notification-title {',
            '  margin: 0;',
            '  font-size: 15px;',
            '  line-height: 1.1;',
            '  font-weight: 600;',
            '  color: #222a33;',
            '}',
            '.complaint-mark-read-btn {',
            '  border: 1px solid #0d6efd;',
            '  background: #ffffff;',
            '  color: #0d6efd;',
            '  font-size: 15px;',
            '  font-weight: 500;',
            '  border-radius: 5px;',
            '  padding: 6px 12px;',
            '}',
            '.complaint-mark-read-btn:hover {',
            '  background: #0d6efd;',
            '  color: #fff;',
            '}',
            '.complaint-mark-read-btn:disabled {',
            '  opacity: 0.6;',
            '  cursor: not-allowed;',
            '}',
            '.complaint-notification-list {',
            '  max-height: 470px;',
            '  overflow-y: auto;',
            '  background: #fff;',
            '}',
            '.complaint-notification-item {',
            '  display: flex;',
            '  gap: 12px;',
            '  padding: 14px 14px 12px;',
            '  border-bottom: 1px solid #e3e4e6;',
            '  background: #f4eef0;',
            '}',
            '.complaint-notification-item:last-child {',
            '  border-bottom: 0;',
            '}',
            '.complaint-notification-icon {',
            '  width: 34px;',
            '  height: 34px;',
            '  border-radius: 50%;',
            '  display: grid;',
            '  place-items: center;',
            '  background: #f7ecd6;',
            '  color: #e5a400;',
            '  flex-shrink: 0;',
            '  margin-top: 3px;',
            '}',
            '.complaint-notification-icon i {',
            '  font-size: 18px;',
            '}',
            '.complaint-notification-content {',
            '  min-width: 0;',
            '}',
            '.complaint-notification-item-title {',
            '  margin: 0;',
            '  font-size: 15px;',
            '  line-height: 1.15;',
            '  font-weight: 700;',
            '  color: #1f2937;',
            '}',
            '.complaint-notification-item-message {',
            '  margin-top: 4px;',
            '  font-size: 15px;',
            '  line-height: 1.25;',
            '  color: #697586;',
            '  font-weight: 500;',
            '}',
            '.complaint-notification-item-meter {',
            '  margin-top: 4px;',
            '  font-size: 13px;',
            '  color: #6e7784;',
            '  line-height: 1.35;',
            '}',
            '.complaint-notification-item-time {',
            '  margin-top: 8px;',
            '  font-size: 14px;',
            '  color: #94a3b8;',
            '}',
            '.complaint-notification-empty {',
            '  padding: 20px 14px;',
            '  color: #6b7280;',
            '  text-align: center;',
            '  font-size: 15px;',
            '}',
            '@media (max-width: 700px) {',
            '  .complaint-notification-title { font-size: 18px; }',
            '  .complaint-mark-read-btn { font-size: 13px; }',
            '  .complaint-notification-item-title { font-size: 16px; }',
            '  .complaint-notification-item-message { font-size: 14px; }',
            '  .complaint-notification-item-time { font-size: 13px; }',
            '  .complaint-notification-item-meter { font-size: 12px; }',
            '}'
        ].join('\n');

        document.head.appendChild(style);
    }

    function renderList($list, notifications) {
        if (!Array.isArray(notifications) || notifications.length === 0) {
            $list.html('<div class="complaint-notification-empty">No new complaints.</div>');
            return;
        }

        const html = notifications.map(function (item) {
            const title = escapeHtml('New complaint: ' + (item.consumer_name || 'Unknown Consumer'));
            const meter = escapeHtml(item.meter_no || 'N/A');
            const message = escapeHtml(shortenText(item.message || '', 140));
            const timeAgo = escapeHtml(item.time_ago || 'Just now');

            return (
                '<div class="complaint-notification-item">' +
                    '<div class="complaint-notification-icon"><i class="bi bi-exclamation-circle"></i></div>' +
                    '<div class="complaint-notification-content">' +
                        '<p class="complaint-notification-item-title">' + title + '</p>' +
                        '<div class="complaint-notification-item-message">' + message + '</div>' +
                        '<div class="complaint-notification-item-meter">Meter: ' + meter + '</div>' +
                        '<div class="complaint-notification-item-time">' + timeAgo + '</div>' +
                    '</div>' +
                '</div>'
            );
        }).join('');

        $list.html(html);
    }

    function updateBadge($badge, count) {
        if (count > 0) {
            $badge.text(count > 99 ? '99+' : String(count)).removeClass('d-none');
        } else {
            $badge.text('0').addClass('d-none');
        }
    }

    window.initComplaintNotifications = function (options) {
        options = options || {};
        const role = options.role === 'plumber' ? 'plumber' : 'admin';
        const pollingInterval = Number(options.pollingInterval || 20000);
        const endpoints = getEndpoints(role);

        const $bell = $('.header-right .bi-bell').first();
        if (!$bell.length || $bell.data('complaint-notification-init')) {
            return;
        }

        ensureStyles();
        $bell.data('complaint-notification-init', true);

        const $container = $bell.closest('.position-relative');
        if (!$container.length) {
            return;
        }

        $container.addClass('dropdown');
        const bellId = 'complaintNotificationBell-' + role + '-' + Date.now();
        $bell.attr({
            id: bellId,
            role: 'button',
            'data-bs-toggle': 'dropdown',
            'aria-expanded': 'false'
        }).css('cursor', 'pointer');

        const $badge = $('<span class="complaint-notification-badge d-none">0</span>');
        $container.append($badge);

        const dropdownHtml = [
            '<div class="dropdown-menu dropdown-menu-end complaint-notification-dropdown" aria-labelledby="' + bellId + '">',
            '  <div class="complaint-notification-header">',
            '    <h6 class="complaint-notification-title">Notifications</h6>',
            '    <button type="button" class="complaint-mark-read-btn">Mark all as read</button>',
            '  </div>',
            '  <div class="complaint-notification-list"><div class="complaint-notification-empty">Loading...</div></div>',
            '</div>'
        ].join('');

        $container.append(dropdownHtml);
        const $dropdown = $container.find('.complaint-notification-dropdown').first();
        const $list = $dropdown.find('.complaint-notification-list').first();
        const $markReadBtn = $dropdown.find('.complaint-mark-read-btn').first();

        let cachedNotifications = [];

        function updateMarkReadState() {
            $markReadBtn.prop('disabled', cachedNotifications.length === 0);
        }

        function fetchNotifications() {
            $.ajax({
                url: endpoints.list,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    cachedNotifications = Array.isArray(response.notifications) ? response.notifications : [];
                    const unreadCount = Number(response.unread_count || cachedNotifications.length || 0);
                    updateBadge($badge, unreadCount);
                    renderList($list, cachedNotifications);
                    updateMarkReadState();

                    try {
                        window.dispatchEvent(new CustomEvent('complaint-notifications:update', {
                            detail: {
                                role: role,
                                notifications: cachedNotifications,
                                unreadCount: unreadCount
                            }
                        }));
                    } catch (error) {
                        // Ignore event dispatch failures to keep polling stable.
                    }
                }
            });
        }

        function markAllRead() {
            return $.ajax({
                url: endpoints.markRead,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }

        $markReadBtn.on('click', function (event) {
            event.preventDefault();
            if (!cachedNotifications.length) {
                return;
            }

            $markReadBtn.prop('disabled', true).text('Updating...');
            markAllRead().always(function () {
                $markReadBtn.text('Mark all as read');
                fetchNotifications();
            });
        });

        fetchNotifications();

        if (pollingInterval >= 1000) {
            setInterval(fetchNotifications, pollingInterval);
        }
    };
})(window, window.jQuery);
