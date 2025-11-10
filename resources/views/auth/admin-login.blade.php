<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Santa Fe Water Billing System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo env('NOCAPTCHA_SITEKEY'); ?>"></script>
    <style>
        :root {
            --primary: #1a73e8;
            --primary-dark: #0d5bba;
            --text: #202124;
            --text-light: #5f6368;
            --light: #f8f9fa;
            --white: #ffffff;
            --border: #dadce0;
            --error: #d93025;
            --success: #06d6a0;
            --warning: #ffa726;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: var(--white);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 8px;
            border: 1px solid var(--border);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        
        .login-logo {
            width: 170px;
            height: 120px;
            margin-bottom: 1.5rem;
        }
        
        .system-title {
            color: var(--text);
            font-size: 1.5rem;
            font-weight: 400;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
            position: relative;
        }
        
        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
        }
        
        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            cursor: pointer;
        }
        
        .btn-login {
            width: 100%;
            padding: 0.8rem;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }
        
        .btn-login:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-login:disabled {
            background-color: var(--text-light);
            cursor: not-allowed;
        }
        
        .forgot-password {
            margin: 1rem 0;
            text-align: right;
        }
        
        .forgot-password a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        
        .forgot-password a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .forgot-password a i {
            margin-left: 5px;
            font-size: 0.8rem;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            color: var(--text-light);
            font-size: 0.8rem;
        }
        
        .divider::before, .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid var(--border);
        }
        
        .divider::before {
            margin-right: 1rem;
        }
        
        .divider::after {
            margin-left: 1rem;
        }
        
        .signup-link {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: var(--text-light);
        }
        
        .signup-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            margin-left: 0.5rem;
        }
        
        .language-selector {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: var(--text-light);
        }
        
        .language-selector a {
            color: var(--text);
            text-decoration: none;
            margin: 0 0.3rem;
        }
        
        .language-selector a.active {
            color: var(--primary);
        }
        
        .error-message {
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 0.4rem;
        }
        
        .attempts-warning {
            color: var(--warning);
            font-size: 0.8rem;
            margin-top: 0.4rem;
            font-weight: 500;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem;
                border: none;
            }
        }
        
        .alert-success {
            background-color: #e6ffed;
            color: var(--success);
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid #a3d9a5;
            border-radius: 4px;
        }
        
        .countdown-timer {
            color: var(--warning);
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        .recaptcha-info {
            font-size: 0.7rem;
            color: var(--text-light);
            margin-top: 0.5rem;
            text-align: center;
        }
        
        .recaptcha-info a {
            color: var(--primary);
            text-decoration: none;
        }

        .portal-links {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .portal-link {
            display: block;
            padding: 0.8rem;
            background-color: var(--light);
            color: var(--primary);
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid var(--border);
        }

        .portal-link:hover {
            background-color: var(--primary);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(26, 115, 232, 0.2);
        }
        
        .divider {
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Two-factor authentication styles */
        .verification-code-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .verification-code-inputs input {
            width: 45px;
            height: 45px;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            border: 1px solid var(--border);
            border-radius: 4px;
        }

        .verification-code-inputs input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
        }

        .resend-code {
            margin-top: 15px;
            font-size: 0.9rem;
        }

        .resend-code button {
            background: none;
            border: none;
            color: var(--primary);
            font-weight: 500;
            cursor: pointer;
            padding: 0;
        }

        .resend-code button:hover {
            text-decoration: underline;
        }

        .resend-code button:disabled {
            color: var(--text-light);
            cursor: not-allowed;
        }

        .countdown {
            color: var(--warning);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="{{ asset('image/santafe.png') }}" class="login-logo" alt="Santa Fe Water">
            <h1 class="system-title">Santa Fe Water Billing System</h1>
        </div> 
        
        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <form id="loginForm" method="POST" action="{{ route('admin-login') }}">
            @csrf
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
            
            <div class="form-group">
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Email address">
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <input type="password" id="password" name="password" required placeholder="Password">
                <i class="fas fa-eye-slash input-icon" id="togglePassword"></i>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="forgot-password">
                <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                    Forgot password? <i class="fas fa-question-circle"></i>
                </a>
            </div>
            
            <button type="submit" class="btn-login" id="loginBtn">
                <span>Log In</span>
            </button>
            <div class="back-link">
                <a href="{{ url('/consumer-portal') }}">
                    <i class="fas fa-arrow-left"></i> Back to Main Login
                </a>
            </div>
            
        </form>

        <!-- Two-Factor Authentication Modal -->
        <div class="modal fade" id="twoFactorModal" tabindex="-1" aria-labelledby="twoFactorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title" id="twoFactorModalLabel">Two-Factor Authentication</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                            <p class="mb-2">We've sent a verification code to your email</p>
                            <p class="text-muted small" id="twoFactorEmail"></p>
                        </div>
                        
                        <form id="twoFactorForm">
                            @csrf
                            <input type="hidden" id="twoFactorEmailInput" name="email">
                            <input type="hidden" id="twoFactorPasswordInput" name="password">
                            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-2fa">
                            
                            <div class="verification-code-inputs">
                                <input type="text" maxlength="1" class="form-control" id="digit1" name="digit1" required>
                                <input type="text" maxlength="1" class="form-control" id="digit2" name="digit2" required>
                                <input type="text" maxlength="1" class="form-control" id="digit3" name="digit3" required>
                                <input type="text" maxlength="1" class="form-control" id="digit4" name="digit4" required>
                                <input type="text" maxlength="1" class="form-control" id="digit5" name="digit5" required>
                                <input type="text" maxlength="1" class="form-control" id="digit6" name="digit6" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="verifyCodeBtn">
                                    <i class="fas fa-check-circle me-2"></i>Verify Code
                                </button>
                            </div>
                            
                            <div class="resend-code text-center">
                                <p>Didn't receive the code? 
                                    <button type="button" id="resendCodeBtn">Resend</button>
                                </p>
                                <p class="countdown" id="countdown" style="display: none;">Resend code in <span id="countdownTime">60</span> seconds</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        

        <!-- Forgot Password Modal -->
        <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title" id="forgotPasswordModalLabel">Reset Your Password</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="forgotPasswordForm">
                            @csrf
                            <div class="mb-3">
                                <label for="resetEmail" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="resetEmail" name="email" required placeholder="Enter your registered email">
                                <div class="form-text">We'll send a password reset link to your email.</div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="sendResetLink">
                                    <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginBtn = document.getElementById('loginBtn');
        const togglePassword = document.getElementById('togglePassword');
        const recaptchaResponse = document.getElementById('g-recaptcha-response');
        const twoFactorModal = new bootstrap.Modal(document.getElementById('twoFactorModal'));
        const twoFactorForm = document.getElementById('twoFactorForm');
        const twoFactorEmailInput = document.getElementById('twoFactorEmailInput');
        const twoFactorPasswordInput = document.getElementById('twoFactorPasswordInput');
        const twoFactorEmailDisplay = document.getElementById('twoFactorEmail');
        const verifyCodeBtn = document.getElementById('verifyCodeBtn');
        const resendCodeBtn = document.getElementById('resendCodeBtn');
        const countdownElement = document.getElementById('countdown');
        const countdownTimeElement = document.getElementById('countdownTime');
        const recaptchaResponse2FA = document.getElementById('g-recaptcha-response-2fa');
        
        // Track login attempts
        let loginAttempts = 0;
        const maxAttempts = 3;
        let lockoutTime = 30; // seconds
        let isLocked = false;
        let countdownInterval;
        let resendCountdownInterval;
        
        // Toggle password visibility
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        // Form validation
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            // Check if account is locked
            if (isLocked) {
                return;
            }
            
            let isValid = true;
            
            // Validate email
            if(!emailInput.value) {
                isValid = false;
                emailInput.style.borderColor = 'var(--error)';
                const errorDiv = emailInput.nextElementSibling;
                if(errorDiv && errorDiv.classList.contains('error-message')) {
                    errorDiv.textContent = 'Email is required';
                    errorDiv.style.display = 'block';
                }
            } else if(!/\S+@\S+\.\S+/.test(emailInput.value)) {
                isValid = false;
                emailInput.style.borderColor = 'var(--error)';
                const errorDiv = emailInput.nextElementSibling;
                if(errorDiv && errorDiv.classList.contains('error-message')) {
                    errorDiv.textContent = 'Please enter a valid email address';
                    errorDiv.style.display = 'block';
                }
            }
            
            // Validate password
            if(!passwordInput.value) {
                isValid = false;
                passwordInput.style.borderColor = 'var(--error)';
                const errorDiv = passwordInput.nextElementSibling.nextElementSibling;
                if(errorDiv && errorDiv.classList.contains('error-message')) {
                    errorDiv.textContent = 'Password is required';
                    errorDiv.style.display = 'block';
                }
            }
            
            if(!isValid) {
                return;
            }
            
            // Execute reCAPTCHA
            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo env('NOCAPTCHA_SITEKEY'); ?>', {action: 'login'}).then(function(token) {
                    // Set the token in the hidden input
                    recaptchaResponse.value = token;
                    
                    // Submit the form via AJAX to check credentials
                    checkCredentials();
                });
            });
        });
        
        function checkCredentials() {
            const formData = new FormData(form);
            
            // Show loading state
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying credentials...';
            
            fetch('/admin-check-credentials', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Credentials are correct, show 2FA modal
                    twoFactorEmailInput.value = emailInput.value;
                    twoFactorPasswordInput.value = passwordInput.value;
                    twoFactorEmailDisplay.textContent = emailInput.value;
                    
                    // Reset button state
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = '<span>Log In</span>';
                    
                    // Show 2FA modal
                    twoFactorModal.show();
                    
                    // Focus on first digit input
                    document.getElementById('digit1').focus();
                    
                    // Start resend countdown
                    startResendCountdown();
                } else {
                    // Login failed
                    loginAttempts++;
                    
                    // Reset button state
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = '<span>Log In</span>';
                    
                    // Show error message
                    if (data.errors && data.errors.password) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: data.errors.password,
                            confirmButtonColor: '#1a73e8'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: 'Invalid credentials. Please try again.',
                            confirmButtonColor: '#1a73e8'
                        });
                    }
                    
                    // Check if max attempts reached
                    if (loginAttempts >= maxAttempts) {
                        lockAccount();
                    } else {
                        // Show remaining attempts
                        const remainingAttempts = maxAttempts - loginAttempts;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Login Failed',
                            html: `Invalid credentials. You have <strong>${remainingAttempts}</strong> attempt${remainingAttempts > 1 ? 's' : ''} remaining.`,
                            confirmButtonColor: '#1a73e8',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<span>Log In</span>';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#1a73e8'
                });
            });
        }
        
        // Two-factor form submission
        twoFactorForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Execute reCAPTCHA
            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo env('NOCAPTCHA_SITEKEY'); ?>', {action: '2fa'}).then(function(token) {
                    // Set the token in the hidden input
                    recaptchaResponse2FA.value = token;
                    
                    // Submit the form via AJAX
                    submitTwoFactorForm();
                });
            });
        });
        
        function submitTwoFactorForm() {
            const formData = new FormData(twoFactorForm);
            
            // Show loading state
            verifyCodeBtn.disabled = true;
            verifyCodeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            
            fetch('/admin-verify-2fa', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 2FA successful, redirect
                    window.location.href = data.redirect || '/admin-dashboard';
                } else {
                    // 2FA failed
                    verifyCodeBtn.disabled = false;
                    verifyCodeBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Verify Code';
                    
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Verification Failed',
                        text: data.message || 'Invalid verification code. Please try again.',
                        confirmButtonColor: '#1a73e8'
                    });
                    
                    // Clear the inputs
                    document.querySelectorAll('.verification-code-inputs input').forEach(input => {
                        input.value = '';
                    });
                    
                    // Focus on first digit input
                    document.getElementById('digit1').focus();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                verifyCodeBtn.disabled = false;
                verifyCodeBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Verify Code';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#1a73e8'
                });
            });
        }
        
        // Resend code functionality
        resendCodeBtn.addEventListener('click', function() {
            const email = twoFactorEmailInput.value;
            
            // Show loading state
            resendCodeBtn.disabled = true;
            resendCodeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            
            fetch('/admin-resend-2fa', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Start countdown
                    startResendCountdown();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Code Sent',
                        text: 'A new verification code has been sent to your email.',
                        confirmButtonColor: '#1a73e8',
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    resendCodeBtn.disabled = false;
                    resendCodeBtn.innerHTML = 'Resend';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to resend code. Please try again.',
                        confirmButtonColor: '#1a73e8'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resendCodeBtn.disabled = false;
                resendCodeBtn.innerHTML = 'Resend';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#1a73e8'
                });
            });
        });
        
        function startResendCountdown() {
            let timeLeft = 60;
            
            // Show countdown, hide resend button
            countdownElement.style.display = 'block';
            resendCodeBtn.style.display = 'none';
            countdownTimeElement.textContent = timeLeft;
            
            // Clear any existing countdown
            if (resendCountdownInterval) {
                clearInterval(resendCountdownInterval);
            }
            
            // Start countdown
            resendCountdownInterval = setInterval(() => {
                timeLeft--;
                countdownTimeElement.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(resendCountdownInterval);
                    countdownElement.style.display = 'none';
                    resendCodeBtn.style.display = 'inline';
                    resendCodeBtn.disabled = false;
                    resendCodeBtn.innerHTML = 'Resend';
                }
            }, 1000);
        }
        
        // Auto-focus next input when a digit is entered
        document.querySelectorAll('.verification-code-inputs input').forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length === 1) {
                    if (index < 5) {
                        document.getElementById(`digit${index + 2}`).focus();
                    }
                }
            });
            
            // Handle backspace
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    document.getElementById(`digit${index}`).focus();
                }
            });
            
            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').trim();
                
                if (pastedData.length === 6 && /^\d{6}$/.test(pastedData)) {
                    // Fill all inputs with pasted data
                    for (let i = 0; i < 6; i++) {
                        document.getElementById(`digit${i + 1}`).value = pastedData[i];
                    }
                    
                    // Focus on last input
                    document.getElementById('digit6').focus();
                }
            });
        });
        
        function lockAccount() {
            isLocked = true;
            let timeLeft = lockoutTime;
            
            // Show lockout message with countdown
            Swal.fire({
                icon: 'warning',
                title: 'Account Temporarily Locked',
                html: `Too many failed login attempts. Please try again in <strong id="countdown">${timeLeft}</strong> seconds.`,
                confirmButtonColor: '#1a73e8',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false
            });
            
            // Start countdown
            countdownInterval = setInterval(() => {
                timeLeft--;
                document.getElementById('countdown').textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    unlockAccount();
                }
            }, 1000);
            
            // Disable login button
            loginBtn.disabled = true;
            loginBtn.innerHTML = `<i class="fas fa-lock"></i> Account Locked (${timeLeft}s)`;
        }
        
        function unlockAccount() {
            isLocked = false;
            loginAttempts = 0;
            
            // Close the SweetAlert
            Swal.close();
            
            // Enable login button
            loginBtn.disabled = false;
            loginBtn.innerHTML = '<span>Log In</span>';
            
            // Show unlock message
            Swal.fire({
                icon: 'success',
                title: 'Account Unlocked',
                text: 'You can now try logging in again.',
                confirmButtonColor: '#1a73e8',
                timer: 3000,
                timerProgressBar: true
            });
        }
        
        // Clear errors when typing
        emailInput.addEventListener('input', function() {
            this.style.borderColor = 'var(--border)';
            const errorDiv = this.nextElementSibling;
            if(errorDiv && errorDiv.classList.contains('error-message')) {
                errorDiv.style.display = 'none';
            }
        });
        
        passwordInput.addEventListener('input', function() {
            this.style.borderColor = 'var(--border)';
            const errorDiv = this.nextElementSibling.nextElementSibling;
            if(errorDiv && errorDiv.classList.contains('error-message')) {
                errorDiv.style.display = 'none';
            }
        });
        
        // Forgot Password Form Handling
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        const sendResetLinkBtn = document.getElementById('sendResetLink');

        if (forgotPasswordForm) {
            forgotPasswordForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const email = document.getElementById('resetEmail').value;
                const originalBtnText = sendResetLinkBtn.innerHTML;
                
                // Show loading state
                sendResetLinkBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
                sendResetLinkBtn.disabled = true;
                
                try {
                    const response = await fetch('/forgot-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email: email })
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            confirmButtonColor: '#1a73e8'
                        });
                        
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
                        modal.hide();
                        
                        // Reset form
                        forgotPasswordForm.reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'An error occurred. Please try again.',
                            confirmButtonColor: '#1a73e8'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                        confirmButtonColor: '#1a73e8'
                    });
                } finally {
                    // Reset button
                    sendResetLinkBtn.innerHTML = originalBtnText;
                    sendResetLinkBtn.disabled = false;
                }
            });
        }
    });
</script>
</body>
</html>