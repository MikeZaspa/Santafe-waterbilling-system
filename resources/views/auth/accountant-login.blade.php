<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Accountant Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
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
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
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
        
        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-overlay.active {
            display: flex;
        }
        
        .modal-container {
            background-color: var(--white);
            border-radius: 8px;
            width: 90%;
            max-width: 450px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            animation: modalFadeIn 0.3s ease-out;
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .modal-logo {
            width: 120px;
            height: 85px;
            margin-bottom: 1rem;
        }
        
        .modal-title {
            color: var(--text);
            font-size: 1.5rem;
            font-weight: 400;
            margin-bottom: 0.5rem;
        }
        
        .modal-subtitle {
            color: var(--primary);
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        
        .verification-info {
            background-color: var(--light);
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        .verification-info p {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .code-inputs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        
        .code-input {
            width: 45px;
            height: 45px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: 600;
            border: 1px solid var(--border);
            border-radius: 4px;
        }
        
        .code-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
        }
        
        .timer {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        .timer.warning {
            color: var(--warning);
        }
        
        .timer.expired {
            color: var(--error);
        }
        
        .btn-resend {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: underline;
            margin-top: 1rem;
        }
        
        .btn-resend:hover {
            color: var(--primary-dark);
        }
        
        .btn-resend:disabled {
            color: var(--text-light);
            cursor: not-allowed;
            text-decoration: none;
        }
        
        .modal-footer {
            margin-top: 1.5rem;
            text-align: center;
        }
        
        .cancel-btn {
            color: var(--text-light);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        .cancel-btn:hover {
            color: var(--text);
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem;
                border: none;
            }
            
            .modal-container {
                padding: 1.5rem;
            }
            
            .code-input {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="{{ asset('image/santafe.png') }}" class="login-logo" alt="Santa Fe Water">
            <h1 class="system-title">Santa Fe Water Billing System</h1>
            <h2 class="portal-title">Accountant Portal</h2>
        </div> 
        
        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <form id="login-form">
            @csrf
            
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
            
            <button type="submit" class="btn-login" id="login-btn">
                <span>Log In as Accountant</span>
            </button>
            
            <div class="back-link">
                <a href="{{ url('/admin-login') }}">
                    <i class="fas fa-arrow-left"></i> Back to Main Login
                </a>
            </div>
        </form>
    </div>
    
    <!-- 2FA Verification Modal -->
    <div class="modal-overlay" id="verification-modal">
        <div class="modal-container">
            <div class="modal-header">
                <img src="{{ asset('image/santafe.png') }}" class="modal-logo" alt="Santa Fe Water">
                <h1 class="modal-title">Santa Fe Water Billing System</h1>
                <h2 class="modal-subtitle">Two-Factor Authentication</h2>
            </div>
            
            <div class="verification-info">
                <p>A verification code has been sent to your email address.</p>
                <p>Please enter the 6-digit code below to continue.</p>
                <p class="timer" id="timer">Code expires in: <span id="countdown">10:00</span></p>
            </div>
            
            <form id="verification-form">
                @csrf
                <input type="hidden" id="accountant-id" name="accountant_id">
                
                <div class="code-inputs">
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                </div>
                
                <input type="hidden" id="verification-code" name="code">
                
                <button type="submit" class="btn-login" id="verify-btn">
                    <span>Verify Code</span>
                </button>
                
                <button type="button" class="btn-resend" id="resend-btn">
                    Resend Code
                </button>
            </form>
            
            <div class="modal-footer">
                <button type="button" class="cancel-btn" id="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const loginForm = document.getElementById('login-form');
            const loginBtn = document.getElementById('login-btn');
            
            // Modal elements
            const modal = document.getElementById('verification-modal');
            const verificationForm = document.getElementById('verification-form');
            const verifyBtn = document.getElementById('verify-btn');
            const resendBtn = document.getElementById('resend-btn');
            const cancelBtn = document.getElementById('cancel-btn');
            const codeInputs = document.querySelectorAll('.code-input');
            const codeInput = document.getElementById('verification-code');
            const countdownEl = document.getElementById('countdown');
            const timerEl = document.getElementById('timer');
            
            let timeLeft = 600; // 10 minutes in seconds
            let timerInterval;
            let accountantId = null;
            
            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
            
            // Handle form submission
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Disable button and show loading
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                
                const formData = new FormData(loginForm);
                
                // First, validate credentials and send 2FA code
                fetch('/accountant-login/submit', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Store accountant ID
                        accountantId = data.accountant_id;
                        document.getElementById('accountant-id').value = accountantId;
                        
                        // Show 2FA modal
                        modal.classList.add('active');
                        startTimer();
                        
                        // Reset login form
                        loginForm.reset();
                        loginBtn.disabled = false;
                        loginBtn.innerHTML = '<span>Log In as Accountant</span>';
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: data.message,
                        });
                        
                        // Re-enable button
                        loginBtn.disabled = false;
                        loginBtn.innerHTML = '<span>Log In as Accountant</span>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                    });
                    
                    // Re-enable button
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = '<span>Log In as Accountant</span>';
                });
            });
            
            // Modal functionality
            cancelBtn.addEventListener('click', function() {
                closeModal();
            });
            
            // Auto-focus next input
            codeInputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (this.value.length === 1) {
                        if (index < codeInputs.length - 1) {
                            codeInputs[index + 1].focus();
                        }
                    }
                    
                    updateCodeHiddenInput();
                });
                
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                        codeInputs[index - 1].focus();
                    }
                });
                
                // Only allow numbers
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });
            
            function updateCodeHiddenInput() {
                let code = '';
                codeInputs.forEach(input => {
                    code += input.value;
                });
                codeInput.value = code;
            }
            
            // Timer countdown
            function startTimer() {
                timeLeft = 600; // Reset to 10 minutes
                timerEl.classList.remove('warning', 'expired');
                timerEl.innerHTML = 'Code expires in: <span id="countdown">10:00</span>';
                
                clearInterval(timerInterval);
                timerInterval = setInterval(updateTimer, 1000);
            }
            
            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                countdownEl.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                
                if (timeLeft <= 60) {
                    timerEl.classList.add('warning');
                }
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    timerEl.classList.remove('warning');
                    timerEl.classList.add('expired');
                    timerEl.innerHTML = 'Code has expired. Please <button type="button" id="resend-expired">request a new one</button>.';
                    
                    document.getElementById('resend-expired').addEventListener('click', function() {
                        resendCode();
                    });
                    
                    verifyBtn.disabled = true;
                }
                
                timeLeft--;
            }
            
            // Verification form submission
            verificationForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const code = codeInput.value;
                if (code.length !== 6) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Code',
                        text: 'Please enter all 6 digits of the verification code.',
                    });
                    return;
                }
                
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                
                const formData = new FormData(verificationForm);
                
                fetch('/accountant-2fa/verify', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                        });
                        
                        verifyBtn.disabled = false;
                        verifyBtn.innerHTML = '<span>Verify Code</span>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                    });
                    
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = '<span>Verify Code</span>';
                });
            });
            
            // Resend code
            function resendCode() {
                if (!accountantId) return;
                
                resendBtn.disabled = true;
                resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                
                fetch('/accountant-2fa/resend', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        accountant_id: accountantId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Code Sent',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Reset timer
                        startTimer();
                        
                        // Clear inputs
                        codeInputs.forEach(input => {
                            input.value = '';
                        });
                        codeInput.value = '';
                        codeInputs[0].focus();
                        
                        // Enable verify button
                        verifyBtn.disabled = false;
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
                })
                .finally(() => {
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = 'Resend Code';
                });
            }
            
            resendBtn.addEventListener('click', resendCode);
            
            function closeModal() {
                modal.classList.remove('active');
                clearInterval(timerInterval);
                
                // Clear inputs
                codeInputs.forEach(input => {
                    input.value = '';
                });
                codeInput.value = '';
                
                // Reset accountant ID
                accountantId = null;
            }
        });
    </script>
</body>
</html>