<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Santa Fe Water</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #1a73e8;
            --success-color: #198754;
            --danger-color: #dc3545;
        }
        
        body {
            background: linear-gradient(135deg, #1a73e8, #0d5bba);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .reset-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
        }
        
        .reset-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), #34a853);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo img {
            max-width: 180px;
            transition: transform 0.3s ease;
        }
        
        .logo img:hover {
            transform: scale(1.05);
        }
        
        .logo h3 {
            color: #333;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 5px;
        }
        
        .logo p {
            color: #6c757d;
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(26, 115, 232, 0.15);
        }
        
        .password-input-container {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 2;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #0d5bba);
            border: none;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 115, 232, 0.3);
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 0.75rem 1rem;
        }
        
        .alert-success {
            background-color: rgba(25, 135, 84, 0.1);
            color: var(--success-color);
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }
        
        .back-link {
            color: #6c757d;
            transition: color 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        
        .back-link:hover {
            color: var(--primary-color);
        }
        
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.85rem;
        }
        
        .strength-meter {
            height: 5px;
            border-radius: 5px;
            margin-top: 5px;
            background-color: #e9ecef;
            overflow: hidden;
        }
        
        .strength-meter-fill {
            height: 100%;
            width: 0;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .password-requirements {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
        }
        
        .requirement i {
            margin-right: 5px;
            font-size: 0.7rem;
        }
        
        .requirement.valid {
            color: var(--success-color);
        }
        
        .text-danger small {
            font-size: 0.8rem;
        }
        
        .text-success small {
            font-size: 0.8rem;
        }
        
        @media (max-width: 576px) {
            .reset-container {
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="logo">
            <img src="{{ asset('image/santafe.png') }}" alt="Santa Fe Water">
            <h3 class="mt-3">Reset Your Password</h3>
            <p>Create a new secure password for your account</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" id="resetForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <div class="password-input-container">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required>
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                
                <div class="password-strength">
                    <span id="strength-text">Password strength</span>
                    <div class="strength-meter">
                        <div class="strength-meter-fill" id="strength-meter-fill"></div>
                    </div>
                </div>
                
                <div class="password-requirements">
                    <div class="requirement" id="req-length">
                        <i class="fas fa-circle"></i> At least 8 characters
                    </div>
                    <div class="requirement" id="req-uppercase">
                        <i class="fas fa-circle"></i> At least one uppercase letter
                    </div>
                    <div class="requirement" id="req-lowercase">
                        <i class="fas fa-circle"></i> At least one lowercase letter
                    </div>
                    <div class="requirement" id="req-number">
                        <i class="fas fa-circle"></i> At least one number
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <div class="password-input-container">
                    <input type="password" class="form-control" 
                           id="password_confirmation" name="password_confirmation" required>
                    <button type="button" class="password-toggle" id="togglePasswordConfirmation">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="mt-1" id="password-match-message"></div>
            </div>

            <button type="submit" class="btn btn-primary mt-3" id="submitBtn">
                <i class="fas fa-key me-2"></i>Reset Password
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="{{ route('admin-login') }}" class="back-link">
                <i class="fas fa-arrow-left me-2"></i>Back to Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            const togglePassword = document.getElementById('togglePassword');
            const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');
            
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
            
            togglePasswordConfirmation.addEventListener('click', function() {
                const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmation.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
            
            // Password strength checker
            password.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                checkPasswordMatch();
            });
            
            passwordConfirmation.addEventListener('input', checkPasswordMatch);
            
            function checkPasswordStrength(password) {
                let strength = 0;
                const requirements = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /[0-9]/.test(password)
                };
                
                // Update requirement indicators
                document.getElementById('req-length').className = requirements.length ? 
                    'requirement valid' : 'requirement';
                document.getElementById('req-uppercase').className = requirements.uppercase ? 
                    'requirement valid' : 'requirement';
                document.getElementById('req-lowercase').className = requirements.lowercase ? 
                    'requirement valid' : 'requirement';
                document.getElementById('req-number').className = requirements.number ? 
                    'requirement valid' : 'requirement';
                
                // Calculate strength
                if (requirements.length) strength += 25;
                if (requirements.uppercase) strength += 25;
                if (requirements.lowercase) strength += 25;
                if (requirements.number) strength += 25;
                
                // Update strength meter
                const strengthMeter = document.getElementById('strength-meter-fill');
                const strengthText = document.getElementById('strength-text');
                
                strengthMeter.style.width = strength + '%';
                
                if (strength < 50) {
                    strengthMeter.style.backgroundColor = '#dc3545';
                    strengthText.textContent = 'Weak password';
                    strengthText.style.color = '#dc3545';
                } else if (strength < 75) {
                    strengthMeter.style.backgroundColor = '#fd7e14';
                    strengthText.textContent = 'Medium password';
                    strengthText.style.color = '#fd7e14';
                } else {
                    strengthMeter.style.backgroundColor = '#198754';
                    strengthText.textContent = 'Strong password';
                    strengthText.style.color = '#198754';
                }
            }
            
            function checkPasswordMatch() {
                const passwordValue = password.value;
                const confirmValue = passwordConfirmation.value;
                const messageElement = document.getElementById('password-match-message');
                
                if (confirmValue === '') {
                    messageElement.textContent = '';
                    messageElement.className = '';
                    return;
                }
                
                if (passwordValue === confirmValue) {
                    messageElement.innerHTML = '<i class="fas fa-check-circle me-1"></i>Passwords match';
                    messageElement.className = 'text-success small';
                } else {
                    messageElement.innerHTML = '<i class="fas fa-times-circle me-1"></i>Passwords do not match';
                    messageElement.className = 'text-danger small';
                }
            }
            
            // Form submission with SweetAlert
            document.getElementById('resetForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const passwordValue = password.value;
                const confirmValue = passwordConfirmation.value;
                
                // Check if passwords match
                if (passwordValue !== confirmValue) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Mismatch',
                        text: 'Please make sure your passwords match.',
                        confirmButtonColor: '#1a73e8'
                    });
                    return;
                }
                
                // Check password strength
                let strength = 0;
                if (passwordValue.length >= 8) strength += 25;
                if (/[A-Z]/.test(passwordValue)) strength += 25;
                if (/[a-z]/.test(passwordValue)) strength += 25;
                if (/[0-9]/.test(passwordValue)) strength += 25;
                
                if (strength < 75) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Weak Password',
                        html: 'For better security, consider using a stronger password with:<br>' +
                              '- At least 8 characters<br>' +
                              '- Uppercase and lowercase letters<br>' +
                              '- Numbers',
                        showCancelButton: true,
                        confirmButtonText: 'Use Anyway',
                        cancelButtonText: 'Improve Password',
                        confirmButtonColor: '#1a73e8',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit the form
                            this.submit();
                        }
                    });
                } else {
                    // Show success message and submit
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Reset Successfully!',
                        text: 'Your password has been updated. You can now log in with your new password.',
                        confirmButtonColor: '#1a73e8'
                    }).then(() => {
                        // Submit the form
                        this.submit();
                    });
                }
            });
        });
    </script>
</body>
</html>