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
        
        /* Sliding Puzzle Captcha Styles */
        .captcha-container {
            position: relative;
            width: 100%;
            height: 300px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: #f0f0f0;
        }
        
        .puzzle-image {
            width: 100%;
            height: 100%;
            position: relative;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .puzzle-piece {
            position: absolute;
            width: 60px;
            height: 60px;
            background-size: 400px 300px;
            border: 1px solid #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            cursor: move;
            z-index: 10;
        }
        
        .puzzle-slot {
            position: absolute;
            width: 60px;
            height: 60px;
            border: 2px dashed rgba(255, 255, 255, 0.7);
            background-color: rgba(0, 0, 0, 0.2);
        }
        
        .puzzle-slider {
            width: 100%;
            margin-top: 20px;
        }
        
        .slider-track {
            width: 100%;
            height: 40px;
            background-color: #f1f3f4;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .slider-progress {
            height: 100%;
            width: 0;
            background-color: var(--primary);
            border-radius: 20px;
            transition: width 0.3s ease;
        }
        
        .slider-button {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            background-color: var(--white);
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            cursor: grab;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }
        
        .slider-button:active {
            cursor: grabbing;
        }
        
        .slider-button i {
            color: var(--primary);
            font-size: 20px;
        }
        
        .slider-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: var(--text-light);
            font-size: 14px;
            pointer-events: none;
            z-index: 1;
        }
        
        .captcha-refresh {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(255, 255, 255, 0.8);
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 20;
            transition: all 0.2s ease;
        }
        
        .captcha-refresh:hover {
            background-color: rgba(255, 255, 255, 1);
            transform: rotate(90deg);
        }
        
        .captcha-success {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(6, 214, 160, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 30;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .captcha-success.show {
            opacity: 1;
            visibility: visible;
        }
        
        .captcha-success i {
            color: white;
            font-size: 48px;
        }
        
        .captcha-instructions {
            margin-top: 15px;
            font-size: 14px;
            color: var(--text-light);
        }
        
        .captcha-error {
            color: var(--error);
            font-size: 14px;
            margin-top: 10px;
            height: 20px;
        }
        
        /* Loading indicator for puzzle image */
        .puzzle-loading {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 25;
        }
        
        .puzzle-loading i {
            color: var(--primary);
            font-size: 24px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Fallback image styling */
        .puzzle-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a73e8, #4285f4);
            color: white;
            font-size: 24px;
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

        <!-- Sliding Puzzle Captcha Modal -->
        <div class="modal fade" id="captchaModal" tabindex="-1" aria-labelledby="captchaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title" id="captchaModalLabel">Security Verification</h5>
                    </div>
                    <div class="modal-body">
                        <p class="text-center mb-3">Please complete the puzzle to verify you're human</p>
                        
                        <div class="captcha-container">
                            <div class="puzzle-image" id="puzzleImage">
                                <div class="puzzle-loading" id="puzzleLoading">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                                <div class="puzzle-slot" id="puzzleSlot"></div>
                                <div class="puzzle-piece" id="puzzlePiece"></div>
                                <div class="captcha-success" id="captchaSuccess">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <button class="captcha-refresh" id="refreshCaptcha">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        
                        <div class="puzzle-slider">
                            <div class="slider-track">
                                <div class="slider-progress" id="sliderProgress"></div>
                                <div class="slider-button" id="sliderButton">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <div class="slider-text">Slide to complete</div>
                            </div>
                        </div>
                        
                        <div class="captcha-instructions">
                            Drag the puzzle piece to the correct position
                        </div>
                        
                        <div class="captcha-error" id="captchaError"></div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" id="cancelCaptcha">Cancel</button>
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
        
        // Captcha elements
        const captchaModal = new bootstrap.Modal(document.getElementById('captchaModal'));
        const puzzlePiece = document.getElementById('puzzlePiece');
        const puzzleSlot = document.getElementById('puzzleSlot');
        const puzzleImage = document.getElementById('puzzleImage');
        const puzzleLoading = document.getElementById('puzzleLoading');
        const sliderButton = document.getElementById('sliderButton');
        const sliderProgress = document.getElementById('sliderProgress');
        const refreshCaptcha = document.getElementById('refreshCaptcha');
        const captchaSuccess = document.getElementById('captchaSuccess');
        const captchaError = document.getElementById('captchaError');
        const cancelCaptcha = document.getElementById('cancelCaptcha');
        
        // Track login attempts
        let loginAttempts = 0;
        const maxAttempts = 3;
        let lockoutTime = 30; // seconds
        let isLocked = false;
        let countdownInterval;
        
        // Puzzle variables
        let puzzlePosition = { x: 0, y: 0 };
        let slotPosition = { x: 0, y: 0 };
        let isDragging = false;
        let isSliding = false;
        let puzzleSolved = false;
        let tolerance = 10; // Tolerance for puzzle solution in pixels
        let captchaVerified = false; // Track if captcha has been verified
        
        // Array of water-related images for the puzzle
        const waterImages = [
            'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1519904981063-b0cf448d479e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1559827260-dc66d52bef19?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1584464491033-06628f3a6b7b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1516972810927-8038500ca84e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80'
        ];
        
        // Initialize puzzle
        function initPuzzle() {
            // Reset puzzle state
            puzzleSolved = false;
            captchaVerified = false;
            captchaSuccess.classList.remove('show');
            captchaError.textContent = '';
            
            // Show loading indicator
            puzzleLoading.style.display = 'flex';
            
            // Select a random water-related image
            const randomIndex = Math.floor(Math.random() * waterImages.length);
            const selectedImage = waterImages[randomIndex];
            
            // Preload the image
            const img = new Image();
            img.crossOrigin = "Anonymous"; // This helps with CORS issues
            
            img.onload = function() {
                // Hide loading indicator
                puzzleLoading.style.display = 'none';
                
                // Set the background image
                puzzleImage.style.backgroundImage = `url('${selectedImage}')`;
                puzzlePiece.style.backgroundImage = `url('${selectedImage}')`;
                
                // Generate random positions
                const maxX = 340; // Maximum X position (container width - piece width)
                const maxY = 240; // Maximum Y position (container height - piece height)
                
                // Set random position for the slot
                slotPosition.x = Math.floor(Math.random() * maxX) + 30;
                slotPosition.y = Math.floor(Math.random() * maxY) + 30;
                
                // Set initial position for the piece (left side)
                puzzlePosition.x = 30;
                puzzlePosition.y = slotPosition.y;
                
                // Apply positions
                puzzleSlot.style.left = slotPosition.x + 'px';
                puzzleSlot.style.top = slotPosition.y + 'px';
                puzzlePiece.style.left = puzzlePosition.x + 'px';
                puzzlePiece.style.top = puzzlePosition.y + 'px';
                
                // Set background position for the piece to match the slot
                puzzlePiece.style.backgroundPosition = `-${slotPosition.x}px -${slotPosition.y}px`;
                
                // Reset slider
                sliderButton.style.left = '10px';
                sliderProgress.style.width = '0px';
            };
            
            img.onerror = function() {
                // Fallback to a default image if the selected one fails to load
                console.error('Image failed to load, using fallback');
                puzzleLoading.style.display = 'none';
                
                // Create a fallback gradient background
                puzzleImage.style.background = 'linear-gradient(135deg, #1a73e8, #4285f4)';
                puzzleImage.style.backgroundImage = 'none';
                
                // Add a water icon as fallback
                puzzleImage.innerHTML = '<div class="puzzle-fallback"><i class="fas fa-tint"></i></div>';
                
                // Re-add the puzzle elements
                puzzleImage.innerHTML += `
                    <div class="puzzle-loading" id="puzzleLoading" style="display:none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div class="puzzle-slot" id="puzzleSlot"></div>
                    <div class="puzzle-piece" id="puzzlePiece"></div>
                    <div class="captcha-success" id="captchaSuccess">
                        <i class="fas fa-check-circle"></i>
                    </div>
                `;
                
                // Re-select the elements after modifying the DOM
                const newPuzzleSlot = document.getElementById('puzzleSlot');
                const newPuzzlePiece = document.getElementById('puzzlePiece');
                
                // Generate random positions
                const maxX = 340; // Maximum X position (container width - piece width)
                const maxY = 240; // Maximum Y position (container height - piece height)
                
                // Set random position for the slot
                slotPosition.x = Math.floor(Math.random() * maxX) + 30;
                slotPosition.y = Math.floor(Math.random() * maxY) + 30;
                
                // Set initial position for the piece (left side)
                puzzlePosition.x = 30;
                puzzlePosition.y = slotPosition.y;
                
                // Apply positions
                newPuzzleSlot.style.left = slotPosition.x + 'px';
                newPuzzleSlot.style.top = slotPosition.y + 'px';
                newPuzzlePiece.style.left = puzzlePosition.x + 'px';
                newPuzzlePiece.style.top = puzzlePosition.y + 'px';
                
                // Create a simple gradient for the puzzle piece
                newPuzzlePiece.style.background = 'linear-gradient(135deg, #1a73e8, #4285f4)';
                
                // Reset slider
                sliderButton.style.left = '10px';
                sliderProgress.style.width = '0px';
            };
            
            // Start loading the image
            img.src = selectedImage;
        }
        
        // Toggle password visibility
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        // Puzzle piece dragging
        puzzlePiece.addEventListener('mousedown', startDragging);
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', stopDragging);
        
        // Touch events for mobile
        puzzlePiece.addEventListener('touchstart', startDragging);
        document.addEventListener('touchmove', drag);
        document.addEventListener('touchend', stopDragging);
        
        function startDragging(e) {
            if (puzzleSolved) return;
            
            isDragging = true;
            
            const touch = e.touches ? e.touches[0] : e;
            const rect = puzzlePiece.getBoundingClientRect();
            const containerRect = puzzlePiece.parentElement.getBoundingClientRect();
            
            // Store the initial offset
            puzzlePiece.dataset.offsetX = touch.clientX - rect.left;
            puzzlePiece.dataset.offsetY = touch.clientY - rect.top;
            
            e.preventDefault();
        }
        
        function drag(e) {
            if (!isDragging || puzzleSolved) return;
            
            const touch = e.touches ? e.touches[0] : e;
            const containerRect = puzzlePiece.parentElement.getBoundingClientRect();
            
            // Calculate new position
            let newX = touch.clientX - containerRect.left - parseInt(puzzlePiece.dataset.offsetX);
            let newY = touch.clientY - containerRect.top - parseInt(puzzlePiece.dataset.offsetY);
            
            // Constrain to container
            newX = Math.max(0, Math.min(newX, containerRect.width - puzzlePiece.offsetWidth));
            newY = Math.max(0, Math.min(newY, containerRect.height - puzzlePiece.offsetHeight));
            
            // Update position
            puzzlePosition.x = newX;
            puzzlePosition.y = newY;
            puzzlePiece.style.left = newX + 'px';
            puzzlePiece.style.top = newY + 'px';
            
            e.preventDefault();
        }
        
        function stopDragging() {
            if (!isDragging || puzzleSolved) return;
            
            isDragging = false;
            
            // Check if puzzle is solved
            checkPuzzleSolution();
        }
        
        // Slider functionality
        sliderButton.addEventListener('mousedown', startSliding);
        document.addEventListener('mousemove', slide);
        document.addEventListener('mouseup', stopSliding);
        
        // Touch events for mobile
        sliderButton.addEventListener('touchstart', startSliding);
        document.addEventListener('touchmove', slide);
        document.addEventListener('touchend', stopSliding);
        
        function startSliding(e) {
            if (puzzleSolved) return;
            
            isSliding = true;
            
            const touch = e.touches ? e.touches[0] : e;
            const rect = sliderButton.getBoundingClientRect();
            const containerRect = sliderButton.parentElement.getBoundingClientRect();
            
            // Store the initial offset
            sliderButton.dataset.offsetX = touch.clientX - rect.left;
            
            e.preventDefault();
        }
        
        function slide(e) {
            if (!isSliding || puzzleSolved) return;
            
            const touch = e.touches ? e.touches[0] : e;
            const containerRect = sliderButton.parentElement.getBoundingClientRect();
            
            // Calculate new position
            let newX = touch.clientX - containerRect.left - parseInt(sliderButton.dataset.offsetX);
            
            // Constrain to container
            newX = Math.max(10, Math.min(newX, containerRect.width - sliderButton.offsetWidth - 10));
            
            // Update position
            sliderButton.style.left = newX + 'px';
            
            // Update progress bar
            const progress = ((newX - 10) / (containerRect.width - sliderButton.offsetWidth - 20)) * 100;
            sliderProgress.style.width = progress + '%';
            
            // Move puzzle piece proportionally
            const puzzleContainer = document.querySelector('.puzzle-image');
            const puzzleMaxX = puzzleContainer.offsetWidth - puzzlePiece.offsetWidth;
            const puzzleNewX = (progress / 100) * puzzleMaxX;
            
            puzzlePosition.x = puzzleNewX;
            puzzlePiece.style.left = puzzleNewX + 'px';
            
            e.preventDefault();
        }
        
        function stopSliding() {
            if (!isSliding || puzzleSolved) return;
            
            isSliding = false;
            
            // Check if puzzle is solved
            checkPuzzleSolution();
        }
        
        function checkPuzzleSolution() {
            // Calculate distance between piece and slot
            const distance = Math.sqrt(
                Math.pow(puzzlePosition.x - slotPosition.x, 2) + 
                Math.pow(puzzlePosition.y - slotPosition.y, 2)
            );
            
            if (distance <= tolerance) {
                // Puzzle solved
                puzzleSolved = true;
                captchaVerified = true;
                captchaSuccess.classList.add('show');
                
                // Snap piece to slot
                puzzlePiece.style.left = slotPosition.x + 'px';
                puzzlePiece.style.top = slotPosition.y + 'px';
                
                // After a short delay, close modal
                setTimeout(() => {
                    captchaModal.hide();
                }, 1000);
            } else {
                // Show error
                captchaError.textContent = 'Please try again. The puzzle piece is not in the correct position.';
                
                // Reset after a short delay
                setTimeout(() => {
                    puzzlePosition.x = 30;
                    puzzlePosition.y = slotPosition.y;
                    puzzlePiece.style.left = '30px';
                    puzzlePiece.style.top = slotPosition.y + 'px';
                    
                    sliderButton.style.left = '10px';
                    sliderProgress.style.width = '0px';
                    
                    captchaError.textContent = '';
                }, 1000);
            }
        }
        
        // Refresh captcha
        refreshCaptcha.addEventListener('click', initPuzzle);
        
        // Cancel captcha
        cancelCaptcha.addEventListener('click', function() {
            captchaModal.hide();
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
            
            // Check if captcha has been verified
            if (!captchaVerified) {
                // Show captcha modal
                captchaModal.show();
                initPuzzle();
                return;
            }
            
            // Execute reCAPTCHA
            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo env('NOCAPTCHA_SITEKEY'); ?>', {action: 'login'}).then(function(token) {
                    // Set the token in the hidden input
                    recaptchaResponse.value = token;
                    
                    // Submit the form via AJAX to handle failed attempts
                    submitFormWithAjax();
                });
            });
        });
        
        function submitFormWithAjax() {
            const formData = new FormData(form);
            
            // Show loading state
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
            
            fetch(form.action, {
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
                    // Login successful, redirect
                    window.location.href = data.redirect || '/admin-dashboard';
                } else {
                    // Login failed
                    loginAttempts++;
                    
                    // Reset captcha verification
                    captchaVerified = false;
                    
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