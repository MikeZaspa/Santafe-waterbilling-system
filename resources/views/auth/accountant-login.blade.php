<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Accountant Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <h2 class="portal-title">Accountant Portal</h2>
        </div> 
        
        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('accountant.login.submit') }}">
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
            
            <button type="submit" class="btn-login">
                <span>Log In as Accountant</span>
            </button>
            
            <div class="back-link">
                <a href="{{ url('/admin-login') }}">
                    <i class="fas fa-arrow-left"></i> Back to Main Login
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>
</html>