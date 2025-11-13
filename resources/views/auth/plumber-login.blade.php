<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Plumber Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo env('NOCAPTCHA_SITEKEY'); ?>"></script>
    <style>
        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
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
            margin-bottom: 0.5rem;
        }
        
        .portal-title {
            color: var(--primary);
            font-size: 1.2rem;
            font-weight: 500;
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
            box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.2);
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
            background-color: #a0a0a0;
            cursor: not-allowed;
        }
        
        .back-link {
            margin: 1rem 0;
            text-align: center;
        }
        
        .back-link a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        
        .back-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .error-message {
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 0.4rem;
        }
        
        .alert-success {
            background-color: #e6ffed;
            color: var(--success);
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid #a3d9a5;
            border-radius: 4px;
        }
        
        /* 2FA Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: var(--white);
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            position: relative;
        }
        
        .modal-title {
            color: var(--text);
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .modal-description {
            color: var(--text-light);
            margin-bottom: 1.5rem;
        }
        
        .code-input {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .code-input input {
            width: 45px;
            height: 45px;
            text-align: center;
            font-size: 1.2rem;
            border: 1px solid var(--border);
            border-radius: 4px;
        }
        
        .code-input input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.2);
        }
        
        .btn-verify {
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
            margin-bottom: 1rem;
        }
        
        .btn-verify:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-verify:disabled {
            background-color: #a0a0a0;
            cursor: not-allowed;
        }
        
        .resend-container {
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        
        .resend-link {
            color: var(--primary);
            text-decoration: none;
            cursor: pointer;
        }
        
        .resend-link:hover {
            text-decoration: underline;
        }
        
        .resend-link.disabled {
            color: #a0a0a0;
            cursor: not-allowed;
            text-decoration: none;
        }
        
        .countdown {
            color: var(--warning);
            font-weight: 500;
        }
        
        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            color: var(--text-light);
            cursor: pointer;
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
        
        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="{{ asset('image/santafe.png') }}" class="login-logo" alt="Santa Fe Water">
            <h1 class="system-title">Santa Fe Water Billing System</h1>
            <h2 class="portal-title">Plumber Portal</h2>
        </div> 
        
        <!-- ALERT MESSAGES SECTION -->
        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 0.75rem 1.25rem; margin-bottom: 1rem; border: 1px solid #f5c6cb; border-radius: 4px;">
                {{ session('error') }}
            </div>
        @endif
        
        <form id="loginForm" method="POST" action="{{ route('plumber.login.submit') }}">
            @csrf
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
            
            <div class="form-group">
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus placeholder="Username">
                @error('username')
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
            
            <button type="submit" class="btn-login" id="loginBtn">
                <span>Log In as Plumber</span>
            </button>
            
            <div class="recaptcha-info">
                This site is protected by reCAPTCHA and the Google
                <a href="https://policies.google.com/privacy">Privacy Policy</a> and
                <a href="https://policies.google.com/terms">Terms of Service</a> apply.
            </div>
            
            <div class="back-link">
                <a href="{{ url('/consumer-portal') }}">
                    <i class="fas fa-arrow-left"></i> Back to Main Login
                </a>
            </div>
        </form>
    </div>

    <!-- 2FA Verification Modal -->
    <div id="twoFactorModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="closeModal">&times;</span>
            <h2 class="modal-title">Two-Factor Authentication</h2>
            <p class="modal-description">We've sent a verification code to your email. Please enter it below.</p>
            
            <form id="twoFactorForm">
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-2fa">
                
                <div class="code-input">
                    <input type="text" maxlength="1" class="code-digit" required>
                    <input type="text" maxlength="1" class="code-digit" required>
                    <input type="text" maxlength="1" class="code-digit" required>
                    <input type="text" maxlength="1" class="code-digit" required>
                    <input type="text" maxlength="1" class="code-digit" required>
                    <input type="text" maxlength="1" class="code-digit" required>
                </div>
                
                <button type="submit" class="btn-verify" id="verifyBtn">Verify Code</button>
                
                <div class="resend-container">
                    <span id="countdownText" class="countdown"></span>
                    <a href="#" id="resendCode" class="resend-link disabled">Resend Code</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const loginForm = document.getElementById('loginForm');
            const twoFactorModal = document.getElementById('twoFactorModal');
            const closeModal = document.getElementById('closeModal');
            const twoFactorForm = document.getElementById('twoFactorForm');
            const codeDigits = document.querySelectorAll('.code-digit');
            const resendCode = document.getElementById('resendCode');
            const countdownText = document.getElementById('countdownText');
            const recaptchaResponse = document.getElementById('g-recaptcha-response');
            const recaptchaResponse2FA = document.getElementById('g-recaptcha-response-2fa');
            
            let countdownInterval;
            let timeLeft = 60;
            let loginAttempts = 0;
            let loginLockoutInterval;
            let loginLockoutTimeLeft = 0;
            
            // Start countdown function
            function startCountdown() {
                timeLeft = 60;
                resendCode.classList.add('disabled');
                resendCode.style.pointerEvents = 'none';
                
                // Clear any existing interval
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }
                
                // Update countdown display
                updateCountdownDisplay();
                
                // Set interval to update countdown every second
                countdownInterval = setInterval(function() {
                    timeLeft--;
                    updateCountdownDisplay();
                    
                    if (timeLeft <= 0) {
                        clearInterval(countdownInterval);
                        resendCode.classList.remove('disabled');
                        resendCode.style.pointerEvents = 'auto';
                        countdownText.textContent = '';
                    }
                }, 1000);
            }
            
            // Update countdown display
            function updateCountdownDisplay() {
                if (timeLeft > 0) {
                    countdownText.textContent = `Resend code in ${timeLeft} seconds`;
                } else {
                    countdownText.textContent = '';
                }
            }
            
            // Start login lockout countdown
            function startLoginLockout() {
                loginLockoutTimeLeft = 30;
                const loginBtn = document.getElementById('loginBtn');
                loginBtn.disabled = true;
                
                // Clear any existing interval
                if (loginLockoutInterval) {
                    clearInterval(loginLockoutInterval);
                }
                
                // Show SweetAlert with countdown
                Swal.fire({
                    title: 'Too Many Attempts',
                    html: `You've reached the maximum number of login attempts. Please wait <b>${loginLockoutTimeLeft}</b> seconds before trying again.`,
                    icon: 'warning',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                
                // Set interval to update countdown every second
                loginLockoutInterval = setInterval(function() {
                    loginLockoutTimeLeft--;
                    
                    // Update SweetAlert content
                    Swal.update({
                        html: `You've reached the maximum number of login attempts. Please wait <b>${loginLockoutTimeLeft}</b> seconds before trying again.`
                    });
                    
                    if (loginLockoutTimeLeft <= 0) {
                        clearInterval(loginLockoutInterval);
                        loginBtn.disabled = false;
                        loginAttempts = 0;
                        Swal.close();
                    }
                }, 1000);
            }
            
            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
            
            // Handle login form submission
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Check if login is locked
                if (loginLockoutTimeLeft > 0) {
                    return;
                }
                
                const loginBtn = document.getElementById('loginBtn');
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                
                // Execute reCAPTCHA
                grecaptcha.ready(function() {
                    grecaptcha.execute('<?php echo env('NOCAPTCHA_SITEKEY'); ?>', {action: 'login'}).then(function(token) {
                        // Set the token in the hidden input
                        recaptchaResponse.value = token;
                        
                        // Submit the form
                        submitLoginForm();
                    });
                });
            });
            
            // Function to submit login form
            function submitLoginForm() {
                const formData = new FormData(loginForm);
                
                fetch('{{ route("plumber.login.submit") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reset attempts on successful login
                        loginAttempts = 0;
                        
                        if (data.requires_2fa) {
                            // Show 2FA modal
                            twoFactorModal.style.display = 'flex';
                            // Focus on first input
                            codeDigits[0].focus();
                            // Start countdown
                            startCountdown();
                        } else {
                            // Redirect to dashboard
                            window.location.href = data.redirect;
                        }
                    } else {
                        // Increment attempts
                        loginAttempts++;
                        
                        // Check if max attempts reached
                        if (loginAttempts >= 3) {
                            startLoginLockout();
                        } else {
                            // Show error message with remaining attempts
                            const remainingAttempts = 3 - loginAttempts;
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Failed',
                                text: `${data.message} You have ${remainingAttempts} ${remainingAttempts === 1 ? 'attempt' : 'attempts'} remaining.`,
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                    });
                })
                .finally(() => {
                    const loginBtn = document.getElementById('loginBtn');
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = '<span>Log In as Plumber</span>';
                });
            }
            
            // Close modal
            closeModal.addEventListener('click', function() {
                twoFactorModal.style.display = 'none';
                // Clear countdown when modal is closed
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }
            });
            
            // Handle code input
            codeDigits.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (this.value.length === 1) {
                        if (index < codeDigits.length - 1) {
                            codeDigits[index + 1].focus();
                        }
                    }
                });
                
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value === '' && index > 0) {
                        codeDigits[index - 1].focus();
                    }
                });
                
                // Only allow numbers
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });
            
            // Handle 2FA form submission
            twoFactorForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const verifyBtn = document.getElementById('verifyBtn');
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                
                // Execute reCAPTCHA for 2FA
                grecaptcha.ready(function() {
                    grecaptcha.execute('<?php echo env('NOCAPTCHA_SITEKEY'); ?>', {action: '2fa'}).then(function(token) {
                        // Set the token in the hidden input
                        recaptchaResponse2FA.value = token;
                        
                        // Submit the 2FA form
                        submitTwoFactorForm();
                    });
                });
            });
            
            // Function to submit 2FA form
            function submitTwoFactorForm() {
                // Get the full code
                let code = '';
                codeDigits.forEach(input => {
                    code += input.value;
                });
                
                fetch('{{ route("plumber.verify.2fa") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        code: code
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to dashboard
                        window.location.href = data.redirect;
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Verification Failed',
                            text: data.message,
                        });
                        // Clear inputs
                        codeDigits.forEach(input => {
                            input.value = '';
                        });
                        codeDigits[0].focus();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                    });
                })
                .finally(() => {
                    const verifyBtn = document.getElementById('verifyBtn');
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = 'Verify Code';
                });
            }
            
            // Handle resend code
            resendCode.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Check if resend is allowed (countdown is finished)
                if (resendCode.classList.contains('disabled')) {
                    return;
                }
                
                fetch('{{ route("plumber.resend.2fa") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Code Sent',
                            text: data.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                        // Start countdown again after successful resend
                        startCountdown();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                    });
                });
            });
        });
    </script>
</body>
</html>