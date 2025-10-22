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
            
            <div class="recaptcha-info">
                This site is protected by reCAPTCHA and the Google 
                <a href="https://policies.google.com/privacy" target="_blank">Privacy Policy</a> and
                <a href="https://policies.google.com/terms" target="_blank">Terms of Service</a> apply.
            </div>
            
            <div class="text-center mt-3">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#portalModal">
                    Access Other Portals
                </button>
            </div>
        </form>

        <!-- Portal Modal -->
        <div class="modal fade" id="portalModal" tabindex="-1" aria-labelledby="portalModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title" id="portalModalLabel">Access Other Portals</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="divider my-3 text-muted">Choose a portal below</div>
                        
                        <div class="portal-links d-flex flex-column gap-3">
                            <a href="{{ route('plumber.login') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-tools"></i> Plumber Portal
                            </a>
                            <a href="{{ route('accountant.login') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-calculator"></i> Accountant Portal
                            </a>
                            <a href="{{ route('consumer.portal') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-users"></i> Consumer Portal
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
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
            
            let loginAttempts = 0;
            let isLocked = false;
            let lockoutTime = null;
            
            // Check if there's a lockout from previous session
            checkLockoutStatus();
            
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
                
                if (isLocked) {
                    showLockoutAlert();
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
                        
                        // Increment attempts counter
                        loginAttempts++;
                        localStorage.setItem('loginAttempts', loginAttempts);
                        localStorage.setItem('lastAttemptTime', new Date().getTime());
                        
                        // Check if should lock
                        if (loginAttempts >= 3) {
                            lockoutTime = new Date().getTime();
                            localStorage.setItem('lockoutTime', lockoutTime);
                            isLocked = true;
                            loginBtn.disabled = true;
                            
                            showLockoutAlert();
                            startCountdown();
                        } else {
                            // Submit the form if not locked
                            form.submit();
                        }
                    });
                });
            });
            
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
            
            function checkLockoutStatus() {
                const storedAttempts = localStorage.getItem('loginAttempts');
                const storedLockoutTime = localStorage.getItem('lockoutTime');
                
                if (storedAttempts) {
                    loginAttempts = parseInt(storedAttempts);
                }
                
                if (storedLockoutTime) {
                    const now = new Date().getTime();
                    const lockoutDuration = 30000; // 30 seconds
                    const timePassed = now - parseInt(storedLockoutTime);
                    
                    if (timePassed < lockoutDuration) {
                        isLocked = true;
                        loginBtn.disabled = true;
                        startCountdown();
                    } else {
                        // Reset if lockout time has passed
                        localStorage.removeItem('loginAttempts');
                        localStorage.removeItem('lockoutTime');
                        localStorage.removeItem('lastAttemptTime');
                        loginAttempts = 0;
                        isLocked = false;
                        loginBtn.disabled = false;
                    }
                }
            }
            
            function showLockoutAlert() {
                Swal.fire({
                    icon: 'error',
                    title: 'Too Many Attempts',
                    html: 'You have exceeded the maximum login attempts. Please wait <strong>30 seconds</strong> before trying again.',
                    confirmButtonColor: '#1a73e8',
                    confirmButtonText: 'OK'
                });
            }
            
            function startCountdown() {
                const lockoutTime = parseInt(localStorage.getItem('lockoutTime'));
                const countdownElement = document.createElement('div');
                countdownElement.className = 'countdown-timer';
                countdownElement.innerHTML = 'Please wait <span id="countdown">30</span> seconds before trying again.';
                
                // Insert countdown after login button
                loginBtn.parentNode.insertBefore(countdownElement, loginBtn.nextSibling);
                
                const countdownInterval = setInterval(function() {
                    const now = new Date().getTime();
                    const timeLeft = 30000 - (now - lockoutTime);
                    const secondsLeft = Math.ceil(timeLeft / 1000);
                    
                    document.getElementById('countdown').textContent = secondsLeft;
                    
                    if (timeLeft <= 0) {
                        clearInterval(countdownInterval);
                        countdownElement.remove();
                        isLocked = false;
                        loginBtn.disabled = false;
                        localStorage.removeItem('loginAttempts');
                        localStorage.removeItem('lockoutTime');
                        localStorage.removeItem('lastAttemptTime');
                        loginAttempts = 0;
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Ready to Try Again',
                            text: 'You can now attempt to login again.',
                            confirmButtonColor: '#1a73e8',
                            confirmButtonText: 'OK'
                        });
                    }
                }, 1000);
            }
            
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