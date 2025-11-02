<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Santa Fe Water Billing System - Consumer Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --primary-color: #d32f2f;
            --primary-light: #ff6659;
            --primary-dark: #9a0007;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            background-image: url('https://images.unsplash.com/photo-1569336415962-a2bddaa96e4d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeIn 0.6s ease-out forwards;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            border: 4px solid rgba(255, 255, 255, 0.2);
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(211, 47, 47, 0.25);
        }
        
        .btn-login {
            background-color: var(--primary-color);
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
        }
        
        .btn-login:hover {
            background-color: var(--primary-dark);
            color: white;
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-right: none;
        }
        
        .input-group .form-control {
            border-left: none;
        }
        
        .login-footer {
            text-align: center;
            padding: 1rem;
            font-size: 0.9rem;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .password-toggle {
            cursor: pointer;
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-left: none;
            display: flex;
            align-items: center;
            padding: 0 15px;
        }
        
        .password-toggle:hover {
            background-color: #e9ecef;
        }
        
        /* Simplified 2FA Modal Styles */
        .modal-content {
            border-radius: 8px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem 1.5rem 0.5rem;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .verification-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .verification-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .verification-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .verification-subtitle {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        
        .code-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 1.5rem;
        }
        
        .code-input {
            width: 45px;
            height: 45px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            border: 1px solid #ced4da;
            border-radius: 6px;
        }
        
        .code-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(211, 47, 47, 0.25);
        }
        
        .btn-verify {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
        }
        
        .btn-verify:hover {
            background-color: var(--primary-dark);
            color: white;
        }
        
        .resend-container {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .resend-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .resend-link:hover {
            text-decoration: underline;
        }
        
        .resend-link.disabled {
            color: #6c757d;
            pointer-events: none;
        }
        
        /* Attempt counter styles */
        .attempt-counter {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.5rem;
            text-align: center;
        }
        
        .attempt-counter.warning {
            color: #ff9800;
        }
        
        .attempt-counter.danger {
            color: #f44336;
            font-weight: 600;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <img src="{{ asset('image/santafe.png') }}" class="login-logo">
                <h2>Santa Fe Water Billing System</h2>
                <p class="mb-0">Consumer Login Portal</p>
            </div>
            
            <div class="login-body">
                <form id="loginForm" action="{{ route('consumer.login') }}" method="POST">
                    @csrf <!-- Add this for Laravel CSRF protection -->
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Account Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                id="username" name="username" 
                                value="{{ old('username') }}" 
                                placeholder="Enter your meter number" required>
                        </div>
                        @error('username')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                id="password" name="password" 
                                placeholder="Enter your password" required>
                            <span class="password-toggle" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="attempt-counter" id="attemptCounter">
                        <span id="attemptText">Login attempts remaining: 3</span>
                    </div>
                    
                    <button type="submit" class="btn btn-login w-100 mb-3" id="loginButton">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Login
                    </button>
                </form>

                <div class="portal-links d-flex flex-column gap-3">
                    <a href="admin-login" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2">
                         <i class="fas fa-tools"></i> Back to Main Login
                    </a>              
               </div>
        </div>
    </div>
    
    <!-- Simplified 2FA Verification Modal -->
    <div class="modal fade" id="twoFactorModal" tabindex="-1" aria-labelledby="twoFactorModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="verification-container">
                        <div class="verification-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h5 class="verification-title">Two-Factor Authentication</h5>
                        <p class="verification-subtitle">We've sent a 6-digit verification code to your email</p>
                    </div>
                    
                    <form id="twoFactorForm" action="{{ route('consumer.verify2fa') }}" method="POST">
                        @csrf
                        <div class="code-inputs">
                            <input type="text" class="form-control code-input" maxlength="1" required>
                            <input type="text" class="form-control code-input" maxlength="1" required>
                            <input type="text" class="form-control code-input" maxlength="1" required>
                            <input type="text" class="form-control code-input" maxlength="1" required>
                            <input type="text" class="form-control code-input" maxlength="1" required>
                            <input type="text" class="form-control code-input" maxlength="1" required>
                            <!-- Hidden field to combine all inputs -->
                            <input type="hidden" id="two_factor_code" name="two_factor_code" required>
                        </div>
                        
                        @error('two_factor_code')
                            <div class="alert alert-danger mb-3">{{ $message }}</div>
                        @endif
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-verify">
                                Verify Code
                            </button>
                        </div>
                    </form>
                    
                    <div class="resend-container text-center">
                        Didn't receive the code? 
                        <a href="#" id="resendCode" class="resend-link">Resend Code</a>
                        <span id="countdown"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $(document).ready(function() {
        // Set current year in footer
        $('#currentYear').text(new Date().getFullYear());
        
        // Toggle password visibility
        $('#togglePassword').click(function() {
            const passwordField = $('#password');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);
            $(this).find('i').toggleClass('bi-eye bi-eye-slash');
        });
        
        // Login attempt tracking
        let loginAttempts = parseInt(localStorage.getItem('loginAttempts') || '0');
        let lockoutEndTime = localStorage.getItem('lockoutEndTime');
        let isLockedOut = false;
        
        // Check if currently locked out
        if (lockoutEndTime) {
            const endTime = new Date(lockoutEndTime);
            const now = new Date();
            
            if (now < endTime) {
                // Still locked out
                isLockedOut = true;
                const remainingSeconds = Math.floor((endTime - now) / 1000);
                showLockoutModal(remainingSeconds);
                disableLoginForm();
            } else {
                // Lockout period has passed
                resetLoginAttempts();
            }
        }
        
        // Update attempt counter display
        updateAttemptCounter();
        
        // Handle form submission
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            
            if (isLockedOut) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Account Locked',
                    text: 'Please wait before trying again.',
                    confirmButtonColor: '#d32f2f',
                });
                return false;
            }
            
            // Get form data
            const formData = {
                username: $('#username').val(),
                password: $('#password').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            // Disable login button during request
            $('#loginButton').prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i> Verifying...');
            
            // Send AJAX request
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Reset attempts on successful login
                        resetLoginAttempts();
                        
                        if (response.requires_2fa) {
                            // Show success message for 2FA
                            Swal.fire({
                                icon: 'success',
                                title: 'Login Successful',
                                text: response.message,
                                confirmButtonColor: '#d32f2f',
                            }).then(function() {
                                // Show the 2FA modal
                                const twoFactorModal = new bootstrap.Modal(document.getElementById('twoFactorModal'));
                                twoFactorModal.show();
                                startCountdown();
                            });
                        } else {
                            // Direct login without 2FA
                            Swal.fire({
                                icon: 'success',
                                title: 'Login Successful',
                                text: 'Redirecting to your dashboard...',
                                timer: 1500,
                                showConfirmButton: false,
                            }).then(function() {
                                window.location.href = response.redirect;
                            });
                        }
                    }
                },
                error: function(xhr) {
                    // Re-enable login button
                    $('#loginButton').prop('disabled', false).html('<i class="bi bi-box-arrow-in-right me-2"></i> Login');
                    
                    if (xhr.status === 429) {
                        // User is locked out
                        const response = xhr.responseJSON;
                        showLockoutModal(response.remaining_time);
                        disableLoginForm();
                    } else {
                        // Login failed
                        const response = xhr.responseJSON;
                        
                        // Increment login attempts
                        loginAttempts++;
                        localStorage.setItem('loginAttempts', loginAttempts.toString());
                        
                        // Update counter display
                        updateAttemptCounter();
                        
                        // Check if max attempts reached
                        if (loginAttempts >= 3) {
                            // Lock out for 30 seconds
                            const lockoutDuration = 30; // seconds
                            const endTime = new Date();
                            endTime.setSeconds(endTime.getSeconds() + lockoutDuration);
                            
                            localStorage.setItem('lockoutEndTime', endTime.toISOString());
                            
                            // Show lockout modal
                            showLockoutModal(lockoutDuration);
                            disableLoginForm();
                        } else {
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Failed',
                                text: response.message || 'Invalid account number or password. Please try again.',
                                confirmButtonColor: '#d32f2f',
                            });
                        }
                    }
                }
            });
            
            return false;
        });
        
        // Handle 2FA form submission
        $('#twoFactorForm').on('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = {
                two_factor_code: $('#two_factor_code').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            // Disable verify button during request
            $('.btn-verify').prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i> Verifying...');
            
            // Send AJAX request
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Authentication Successful',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(function() {
                            // Redirect to dashboard
                            window.location.href = response.redirect;
                        });
                    }
                },
                error: function(xhr) {
                    // Re-enable verify button
                    $('.btn-verify').prop('disabled', false).html('Verify Code');
                    
                    // Show error message
                    const response = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Verification Failed',
                        text: response.message || 'Invalid verification code. Please try again.',
                        confirmButtonColor: '#d32f2f',
                    });
                }
            });
            
            return false;
        });
        
        // Update attempt counter display
        function updateAttemptCounter() {
            const remainingAttempts = 3 - loginAttempts;
            const attemptCounter = $('#attemptCounter');
            const attemptText = $('#attemptText');
            
            if (remainingAttempts <= 1) {
                attemptCounter.removeClass('warning').addClass('danger');
                attemptText.text(`Last attempt before account lockout!`);
            } else if (remainingAttempts === 2) {
                attemptCounter.removeClass('danger').addClass('warning');
                attemptText.text(`Login attempts remaining: ${remainingAttempts}`);
            } else {
                attemptCounter.removeClass('warning danger');
                attemptText.text(`Login attempts remaining: ${remainingAttempts}`);
            }
        }
        
        // Show lockout modal with countdown
        function showLockoutModal(seconds) {
            Swal.fire({
                icon: 'warning',
                title: 'Account Temporarily Locked',
                html: `Too many failed login attempts. Please wait <b>${seconds}</b> seconds before trying again.`,
                timer: seconds * 1000,
                timerProgressBar: true,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                    const timerInterval = setInterval(() => {
                        const remaining = Math.ceil(Swal.getTimerLeft() / 1000);
                        if (remaining > 0) {
                            Swal.getHtmlContainer().querySelector('b').textContent = remaining;
                        } else {
                            clearInterval(timerInterval);
                        }
                    }, 1000);
                }
            }).then(() => {
                // Lockout period is over
                resetLoginAttempts();
                enableLoginForm();
                Swal.fire({
                    icon: 'info',
                    title: 'Account Unlocked',
                    text: 'You can now try logging in again.',
                    confirmButtonColor: '#d32f2f',
                });
            });
        }
        
        // Disable login form
        function disableLoginForm() {
            $('#loginButton').prop('disabled', true);
            $('#username, #password').prop('disabled', true);
            isLockedOut = true;
        }
        
        // Enable login form
        function enableLoginForm() {
            $('#loginButton').prop('disabled', false);
            $('#username, #password').prop('disabled', false);
            isLockedOut = false;
        }
        
        // Reset login attempts
        function resetLoginAttempts() {
            loginAttempts = 0;
            localStorage.removeItem('loginAttempts');
            localStorage.removeItem('lockoutEndTime');
            updateAttemptCounter();
        }
        
        // Show 2FA modal if session variable is set
        @if(session('show2faModal'))
            const twoFactorModal = new bootstrap.Modal(document.getElementById('twoFactorModal'));
            twoFactorModal.show();
            startCountdown();
        @endif
        
        // Code input handling
        const codeInputs = $('.code-input');
        
        codeInputs.on('input', function() {
            const value = $(this).val();
            
            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                $(this).val('');
                return;
            }
            
            // Move to next input if current is filled
            if (value.length === 1) {
                const index = codeInputs.index(this);
                if (index < codeInputs.length - 1) {
                    codeInputs.eq(index + 1).focus();
                }
            }
            
            // Update hidden field
            updateCodeField();
        });
        
        // Handle backspace
        codeInputs.on('keydown', function(e) {
            if (e.key === 'Backspace' && $(this).val() === '') {
                const index = codeInputs.index(this);
                if (index > 0) {
                    codeInputs.eq(index - 1).focus();
                }
            }
        });
        
        // Handle paste
        codeInputs.on('paste', function(e) {
            e.preventDefault();
            const pastedData = e.originalEvent.clipboardData.getData('text');
            const digits = pastedData.replace(/\D/g, '').slice(0, 6);
            
            for (let i = 0; i < digits.length; i++) {
                codeInputs.eq(i).val(digits[i]);
            }
            
            if (digits.length > 0 && digits.length < 6) {
                codeInputs.eq(digits.length).focus();
            }
            
            updateCodeField();
        });
        
        // Update hidden field with combined code
        function updateCodeField() {
            let code = '';
            codeInputs.each(function() {
                code += $(this).val();
            });
            $('#two_factor_code').val(code);
        }
        
        // Resend code functionality
        $('#resendCode').click(function(e) {
            e.preventDefault();
            
            const resendLink = $(this);
            const countdown = $('#countdown');
            
            // Disable the link
            resendLink.addClass('disabled');
            
            // Send AJAX request to resend code
            $.ajax({
                url: '{{ route("consumer.resend2fa") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        const alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            response.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>';
                        $('#twoFactorModal .modal-body').prepend(alertHtml);
                        
                        // Start countdown
                        startCountdown();
                    } else {
                        // Show error message
                        const alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            response.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>';
                        $('#twoFactorModal .modal-body').prepend(alertHtml);
                        
                        // Re-enable the link
                        resendLink.removeClass('disabled');
                    }
                },
                error: function() {
                    // Show error message
                    const alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        'An error occurred. Please try again.' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>';
                    $('#twoFactorModal .modal-body').prepend(alertHtml);
                    
                    // Re-enable the link
                    resendLink.removeClass('disabled');
                }
            });
        });
        
        // Countdown function
        function startCountdown() {
            let timeLeft = 60;
            const resendLink = $('#resendCode');
            const countdown = $('#countdown');
            
            resendLink.addClass('disabled');
            countdown.text(`(${timeLeft}s)`);
            
            const timer = setInterval(function() {
                timeLeft--;
                countdown.text(`(${timeLeft}s)`);
                
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    resendLink.removeClass('disabled');
                    countdown.text('');
                }
            }, 1000);
        }
        
        // Auto-focus on first code input when modal is shown
        $('#twoFactorModal').on('shown.bs.modal', function() {
            $('.code-input').first().focus();
        });
    });
</script>
</body>
</html>