<style>
    .session-timer {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 10px 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        font-size: 0.8rem;
        color: #6c757d;
        z-index: 1000;
        display: none;
    }

    .session-timer.warning {
        border-color: #f59f00;
        color: #f59f00;
    }

    .session-timer.danger {
        border-color: #dc3545;
        color: #dc3545;
    }
</style>

<div class="session-timer" id="sessionTimer">
    <i class="bi bi-clock-history me-2"></i>
    <span id="sessionTimerLabel">Session expires in:</span>
    <span id="sessionTimeDisplay">240:00</span>
</div>

<script>
    (function () {
        if (window.__sessionTimeoutInitialized) {
            return;
        }
        window.__sessionTimeoutInitialized = true;

        const config = Object.assign(
            {
                durationMinutes: 240,
                warningSeconds: 30,
                logoutEndpoint: '/logout',
                logoutRedirectUrl: '/admin-login',
                labelText: 'Session expires in:'
            },
            window.sessionTimeoutConfig || {}
        );

        const sessionTimer = document.getElementById('sessionTimer');
        const sessionTimeDisplay = document.getElementById('sessionTimeDisplay');
        const sessionTimerLabel = document.getElementById('sessionTimerLabel');

        if (!sessionTimer || !sessionTimeDisplay || !sessionTimerLabel) {
            return;
        }

        sessionTimerLabel.textContent = config.labelText;

        const sessionDuration = Math.max(1, Number(config.durationMinutes) || 240) * 60 * 1000;
        const warningTime = Math.max(1, Number(config.warningSeconds) || 30) * 1000;

        let sessionTimeout;
        let warningTimeout;
        let sessionInterval;
        let sessionExpiryTime;
        let isSessionActive = false;
        let warningDialogOpen = false;

        function updateSessionDisplay() {
            if (!isSessionActive) return;

            const timeLeft = Math.max(0, sessionExpiryTime - Date.now());
            const minutes = Math.floor(timeLeft / 60000);
            const seconds = Math.floor((timeLeft % 60000) / 1000);

            sessionTimeDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            sessionTimer.classList.remove('warning', 'danger');
            if (timeLeft <= 10000) {
                sessionTimer.classList.add('danger');
            } else if (timeLeft <= warningTime) {
                sessionTimer.classList.add('warning');
            }
        }

        function clearSessionHandles() {
            clearTimeout(sessionTimeout);
            clearTimeout(warningTimeout);
            clearInterval(sessionInterval);
        }

        function scheduleSessionHandles() {
            clearSessionHandles();
            sessionTimeout = setTimeout(endSession, sessionDuration);
            warningTimeout = setTimeout(showSessionWarning, Math.max(1000, sessionDuration - warningTime));
            sessionInterval = setInterval(updateSessionDisplay, 1000);
        }

        function startSession() {
            isSessionActive = true;
            sessionExpiryTime = Date.now() + sessionDuration;
            sessionTimer.style.display = 'block';
            updateSessionDisplay();
            scheduleSessionHandles();
        }

        function resetSessionTimer(force) {
            if (!isSessionActive) return;
            if (warningDialogOpen && !force) return;

            sessionExpiryTime = Date.now() + sessionDuration;
            sessionTimer.classList.remove('warning', 'danger');
            updateSessionDisplay();
            scheduleSessionHandles();
        }

        async function performAutoLogout() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (config.logoutEndpoint) {
                try {
                    const headers = { 'X-Requested-With': 'XMLHttpRequest' };
                    if (csrfToken) {
                        headers['X-CSRF-TOKEN'] = csrfToken;
                    }

                    await fetch(config.logoutEndpoint, {
                        method: 'POST',
                        headers,
                        credentials: 'same-origin'
                    });
                } catch (e) {
                    // Continue with redirect even if logout request fails.
                }
            }

            window.location.href = config.logoutRedirectUrl;
        }

        function endSession() {
            isSessionActive = false;
            clearSessionHandles();
            sessionTimer.style.display = 'none';

            if (window.Swal) {
                Swal.fire({
                    title: 'Session Expired',
                    text: 'Your session has expired due to inactivity. Please log in again.',
                    icon: 'info',
                    confirmButtonColor: '#0d6efd',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => performAutoLogout());
                return;
            }

            alert('Your session has expired due to inactivity. Please log in again.');
            performAutoLogout();
        }

        function showSessionWarning() {
            if (!isSessionActive) return;
            warningDialogOpen = true;

            const warningMessage = `Your session will expire in ${Math.ceil(warningTime / 1000)} seconds due to inactivity.`;

            if (window.Swal) {
                Swal.fire({
                    title: 'Session Expiring Soon',
                    html: `${warningMessage}<br><br>Would you like to extend your session?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Extend Session',
                    cancelButtonText: 'Log Out',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    warningDialogOpen = false;
                    if (result.isConfirmed) {
                        resetSessionTimer(true);
                        Swal.fire({
                            title: 'Session Extended',
                            text: `Your session has been extended for another ${config.durationMinutes} minutes.`,
                            icon: 'success',
                            timer: 2500,
                            showConfirmButton: false
                        });
                    } else {
                        endSession();
                    }
                });
                return;
            }

            const shouldExtend = confirm(`${warningMessage}\n\nPress OK to extend your session, or Cancel to log out.`);
            warningDialogOpen = false;
            if (shouldExtend) {
                resetSessionTimer(true);
            } else {
                endSession();
            }
        }

        const activityEvents = ['mousemove', 'mousedown', 'keypress', 'scroll', 'touchstart', 'click'];
        activityEvents.forEach((eventName) => {
            document.addEventListener(eventName, () => resetSessionTimer(false), { passive: true });
        });

        startSession();
    })();
</script>
