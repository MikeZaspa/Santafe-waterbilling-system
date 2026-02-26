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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo env('NOCAPTCHA_SITEKEY'); ?>"></script>
    <link rel="icon" type="image/png" href="image/santalogo.png">
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

        /* reCAPTCHA info text */
        .recaptcha-info {
            font-size: 0.7rem;
            color: #6c757d;
            margin-top: 0.5rem;
            text-align: center;
            }
        
        .recaptcha-info a {
            color: var(--primary-color);
            text-decoration: none;
            }
        
        /* Portal Access Redesign */
        .portal-launch-btn {
            border: 1px solid #dee2e6;
            background: #ffffff;
            color: #212529;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .portal-launch-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: #fff5f5;
        }

        .portal-modal .modal-content {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 14px 38px rgba(0, 0, 0, 0.16);
        }

        .portal-modal .modal-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f0eaea;
            background: linear-gradient(135deg, #ffffff 0%, #fff6f6 100%);
        }

        .portal-modal .modal-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #212529;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .portal-modal .modal-body {
            padding: 1rem 1.25rem 1.25rem;
        }

        .portal-modal-subtitle {
            margin: 0 0 0.9rem;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .portal-links {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }

        .portal-card {
            text-decoration: none;
            color: #212529;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 0.8rem 0.9rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .portal-card:hover {
            color: #212529;
            border-color: var(--primary-color);
            background: #fff8f8;
            transform: translateY(-1px);
        }

        .portal-card-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .portal-card-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #fdecec;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .portal-card-meta {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .portal-card-name {
            font-weight: 600;
        }

        .portal-card-desc {
            font-size: 0.78rem;
            color: #6c757d;
        }

        .portal-card-arrow {
            color: #adb5bd;
        }

        .mobile-download-modal .modal-content {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .mobile-download-modal .modal-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f3f5;
        }

        .mobile-download-modal .modal-body {
            padding: 1rem 1.25rem 1.25rem;
        }

        .mobile-download-copy {
            color: #495057;
            margin-bottom: 0.9rem;
        }

        .mobile-download-actions .btn {
            width: 100%;
        }

        .mobile-download-note {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.75rem;
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
                        @csrf
                        <!-- Hidden reCAPTCHA field -->
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

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
                    <div class="text-center mt-3">
                <button type="button" id="accessOtherPortalsBtn" class="portal-launch-btn" data-bs-toggle="modal" data-bs-target="#portalModal">
                    <i class="bi bi-grid"></i>
                    Access Other Portals
                    <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
        </div>
    </div>
    
    <!-- Portal Modal -->
    <div class="modal fade portal-modal" id="portalModal" tabindex="-1" aria-labelledby="portalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="portalModalLabel">
                        <i class="bi bi-grid-3x3-gap"></i>
                        Access Other Portals
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="portal-links">
                        <a href="{{ route('plumber.login') }}" class="portal-card">
                            <div class="portal-card-left">
                                <span class="portal-card-icon"><i class="fas fa-tools"></i></span>
                                <span class="portal-card-meta">
                                    <span class="portal-card-name">Plumber Portal</span>  
                                </span>
                            </div>
                            <i class="bi bi-chevron-right portal-card-arrow"></i>
                        </a>
                        <a href="{{ route('accountant.login') }}" class="portal-card">
                            <div class="portal-card-left">
                                <span class="portal-card-icon"><i class="fas fa-calculator"></i></span>
                                <span class="portal-card-meta">
                                    <span class="portal-card-name">Accountant Portal</span>
                                </span>
                            </div>
                            <i class="bi bi-chevron-right portal-card-arrow"></i>
                        </a>
                        <a href="{{ route('admin-login') }}" class="portal-card">
                            <div class="portal-card-left">
                                <span class="portal-card-icon"><i class="fas fa-user-shield"></i></span>
                                <span class="portal-card-meta">
                                    <span class="portal-card-name">Admin Portal</span>
                                </span>
                            </div>
                            <i class="bi bi-chevron-right portal-card-arrow"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile App Download Modal -->
    <div class="modal fade mobile-download-modal" id="androidAppModal" tabindex="-1" aria-labelledby="androidAppModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="androidAppModalLabel">Get The Mobile App</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mobile-download-copy">Download Santa Fe Water Billing for your phone.</p>
                    @php($iosAppStoreUrl = env('IOS_APP_STORE_URL'))
                    @php($iosIpaUrl = env('IOS_IPA_URL'))
                    @php($hasLocalIosIpa = file_exists(public_path('ios.ipa')))
                    <div class="mobile-download-actions d-flex flex-column gap-2">
                        <a href="{{ asset('android.apk') }}" id="downloadAndroidApp" class="btn btn-outline-success" download>
                            <i class="bi bi-android2 me-1"></i>Android APK
                        </a>
                        @if(!empty($iosIpaUrl) || $hasLocalIosIpa)
                            <a href="{{ !empty($iosIpaUrl) ? $iosIpaUrl : asset('ios.ipa') }}" id="downloadIosApp" class="btn btn-outline-dark" download>
                                <i class="bi bi-apple me-1"></i>iOS IPA
                            </a>
                        @elseif(!empty($iosAppStoreUrl))
                            <a href="{{ $iosAppStoreUrl }}" id="downloadIosApp" class="btn btn-outline-dark" target="_blank" rel="noopener">
                                <i class="bi bi-apple me-1"></i>iOS App Store
                            </a>
                        @else
                            <button type="button" class="btn btn-outline-secondary" disabled>
                                <i class="bi bi-apple me-1"></i>iOS Coming Soon
                            </button>
                        @endif
                    </div>
                </div>
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
                        <!-- Hidden reCAPTCHA field for 2FA -->
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-2fa">
                        
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

        const isTwoFactorPending = @json((bool) session('show2faModal'));

        const mobileAppFlagKey = 'hasDownloadedMobileAppV2';
        const legacyMobileAppFlagKey = 'hasDownloadedMobileApp';
        const queryParams = new URLSearchParams(window.location.search);

        function getCookieValue(key) {
            const cookie = document.cookie
                .split('; ')
                .find(function(item) {
                    return item.indexOf(key + '=') === 0;
                });

            return cookie ? cookie.substring((key + '=').length) : '';
        }

        function clearMobileDownloadFlag() {
            try {
                localStorage.removeItem(mobileAppFlagKey);
                localStorage.removeItem(legacyMobileAppFlagKey);
            } catch (error) {
                // Ignore storage errors.
            }

            const expires = 'expires=Thu, 01 Jan 1970 00:00:00 GMT';
            const basePath = '; path=/; SameSite=Lax';
            const cookieDomain = window.location.hostname.endsWith('santafe-waterbilling.com')
                ? '; domain=.santafe-waterbilling.com'
                : '';

            document.cookie = mobileAppFlagKey + '=; ' + expires + basePath + cookieDomain;
            document.cookie = legacyMobileAppFlagKey + '=; ' + expires + basePath + cookieDomain;
            document.cookie = mobileAppFlagKey + '=; ' + expires + basePath;
            document.cookie = legacyMobileAppFlagKey + '=; ' + expires + basePath;
        }

        function isStandaloneAppContext() {
            const userAgent = navigator.userAgent || '';
            const isStandaloneDisplay = window.matchMedia && (
                window.matchMedia('(display-mode: standalone)').matches
                || window.matchMedia('(display-mode: fullscreen)').matches
                || window.matchMedia('(display-mode: minimal-ui)').matches
            );
            const isIOSStandalone = window.navigator.standalone === true;
            const isAndroidWebView = /\bwv\b|WebView|Version\/[\d.]+.*Chrome\/[\d.]+ Mobile/i.test(userAgent);
            const isAndroidAppReferrer = (document.referrer || '').indexOf('android-app://') === 0;
            const isForcedAppContext = queryParams.get('app') === '1' || queryParams.get('app_context') === '1';

            return isForcedAppContext || isStandaloneDisplay || isIOSStandalone || isAndroidWebView || isAndroidAppReferrer;
        }

        function hasMobileDownloadFlag() {
            let hasLocalStorageFlag = false;
            try {
                hasLocalStorageFlag = localStorage.getItem(mobileAppFlagKey) === 'true'
                    || localStorage.getItem(legacyMobileAppFlagKey) === 'true';
            } catch (error) {
                hasLocalStorageFlag = false;
            }

            const hasCookieFlag = getCookieValue(mobileAppFlagKey) === 'true'
                || getCookieValue(legacyMobileAppFlagKey) === 'true';

            return hasLocalStorageFlag || hasCookieFlag;
        }

        function persistMobileDownloadFlag() {
            try {
                localStorage.setItem(mobileAppFlagKey, 'true');
            } catch (error) {
                // Ignore storage errors and continue with cookie fallback.
            }

            const oneYearInSeconds = 60 * 60 * 24 * 365;
            const cookieDomain = window.location.hostname.endsWith('santafe-waterbilling.com')
                ? '; domain=.santafe-waterbilling.com'
                : '';
            const secureFlag = window.location.protocol === 'https:' ? '; Secure' : '';
            document.cookie = mobileAppFlagKey + '=true; max-age=' + oneYearInSeconds + '; path=/; SameSite=Lax' + cookieDomain + secureFlag;
        }

        if (queryParams.get('reset_app_modal') === '1') {
            clearMobileDownloadFlag();
        }

        const forceShowMobileModal = queryParams.get('show_app_modal') === '1';
        const hasDownloadedMobileApp = hasMobileDownloadFlag();
        const isInMobileApp = isStandaloneAppContext();
        const accessOtherPortalsBtn = document.getElementById('accessOtherPortalsBtn');
        const portalModalEl = document.getElementById('portalModal');
        if (isInMobileApp) {
            if (accessOtherPortalsBtn) {
                accessOtherPortalsBtn.style.display = 'none';
            }

            if (portalModalEl) {
                portalModalEl.style.display = 'none';
            }
        }

        const androidAppModalEl = document.getElementById('androidAppModal');
        if (androidAppModalEl) {
            const androidAppModal = new bootstrap.Modal(androidAppModalEl);
            const shouldShowMobileDownloadModal = !isInMobileApp
                && !isTwoFactorPending
                && (forceShowMobileModal || !hasDownloadedMobileApp);

            if (shouldShowMobileDownloadModal) {
                setTimeout(function() {
                    androidAppModal.show();
                }, 700);
            }

            androidAppModalEl.addEventListener('hidden.bs.modal', function() {
                persistMobileDownloadFlag();
            });
        }

        $('#downloadAndroidApp, #downloadIosApp').on('click', function() {
            persistMobileDownloadFlag();
        });

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
        
        // Handle form submission with reCAPTCHA
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
            
            // Execute reCAPTCHA
            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo env('NOCAPTCHA_SITEKEY'); ?>', {action: 'login'}).then(function(token) {
                    // Set the token in the hidden input
                    $('#g-recaptcha-response').val(token);
                    
                    // Proceed with login
                    submitLoginForm();
                });
            });
            
            return false;
        });
        
        // Function to submit login form
        function submitLoginForm() {
            // Get form data
            const formData = {
                username: $('#username').val(),
                password: $('#password').val(),
                g_recaptcha_response: $('#g-recaptcha-response').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            // Disable login button during request
            $('#loginButton').prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i> Verifying...');
            
            // Send AJAX request
            $.ajax({
                url: $('#loginForm').attr('action'),
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
        }
        
        // Handle 2FA form submission with reCAPTCHA
        $('#twoFactorForm').on('submit', function(e) {
            e.preventDefault();
            
            // Execute reCAPTCHA for 2FA
            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo env('NOCAPTCHA_SITEKEY'); ?>', {action: '2fa'}).then(function(token) {
                    // Set the token in the hidden input
                    $('#g-recaptcha-response-2fa').val(token);
                    
                    // Submit 2FA form
                    submitTwoFactorForm();
                });
            });
            
            return false;
        });
        
        // Function to submit 2FA form
        function submitTwoFactorForm() {
            // Get form data
            const formData = {
                two_factor_code: $('#two_factor_code').val(),
                g_recaptcha_response: $('#g-recaptcha-response-2fa').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            // Disable verify button during request
            $('.btn-verify').prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i> Verifying...');
            
            // Send AJAX request
            $.ajax({
                url: $('#twoFactorForm').attr('action'),
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
        }
        
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
