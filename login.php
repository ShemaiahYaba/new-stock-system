<?php
/**
 * Login Page
 *
 * User authentication entry point
 */

session_start();

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/utils/helpers.php';
require_once __DIR__ . '/utils/auth_middleware.php';

// Redirect if already authenticated
redirectIfAuthenticated();

$pageTitle = 'Login - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 20px;
        }
        
        .login-wrapper {
            width: 100%;
            max-width: 480px;
        }
        
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 20px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        
        .login-header {
            padding: 48px 40px 32px;
            text-align: center;
            background: #ffffff;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .logo-container {
            margin-bottom: 24px;
        }
        
        .logo-container img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        
        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        
        .logo-icon i {
            font-size: 40px;
            color: white;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }
        
        .app-name {
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            letter-spacing: 0.3px;
        }
        
        .login-body {
            padding: 32px 40px 48px;
        }
        
        .login-title {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 24px;
            text-align: center;
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .form-label i {
            font-size: 14px;
            margin-right: 6px;
            color: #6b7280;
        }
        
        .form-control {
            height: 48px;
            padding: 12px 16px;
            font-size: 15px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.2s ease;
            background: #ffffff;
        }
        
        .form-control:hover {
            border-color: #d1d5db;
        }
        
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: #ffffff;
        }
        
        .form-control::placeholder {
            color: #9ca3af;
        }
        
        .btn-login {
            height: 48px;
            background: #3b82f6;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            color: white;
            transition: all 0.2s ease;
            letter-spacing: 0.3px;
        }
        
        .btn-login:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 12px 16px;
            font-size: 14px;
            margin-bottom: 24px;
        }
        
        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border-left: 3px solid #10b981;
        }
        
        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 3px solid #ef4444;
        }
        
        .alert-warning {
            background: #fffbeb;
            color: #92400e;
            border-left: 3px solid #f59e0b;
        }
        
        .alert-info {
            background: #eff6ff;
            color: #1e40af;
            border-left: 3px solid #3b82f6;
        }
        
        .btn-close {
            font-size: 12px;
        }
        
        .invalid-feedback {
            font-size: 13px;
            margin-top: 6px;
        }
        
        .footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: #6b7280;
        }
        
        @media (max-width: 576px) {
            .login-header {
                padding: 36px 24px 24px;
            }
            
            .login-body {
                padding: 24px 24px 36px;
            }
            
            .company-name {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <?php if (file_exists('assets/logo.png')): ?>
                        <img src="/new-stock-system/assets/logo.png" alt="<?php echo htmlspecialchars(COMPANY_NAME); ?>">
                    <?php else: ?>
                        <div class="logo-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <h1 class="company-name"><?php echo COMPANY_NAME; ?></h1>
                <p class="app-name"><?php echo APP_NAME; ?></p>
            </div>
            
            <div class="login-body">
                <?php
                // Display flash messages
                if (hasFlashMessage()) {
                    $flash = getFlashMessage();
                    $alertClass = [
                        'success' => 'alert-success',
                        'error' => 'alert-danger',
                        'warning' => 'alert-warning',
                        'info' => 'alert-info',
                    ][$flash['type']] ?? 'alert-info';
                    ?>
                    <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                }

                // Check for timeout parameter
                if (isset($_GET['timeout'])) { ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Your session has expired. Please log in again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php }
                ?>
                
                <h2 class="login-title">Sign In</h2>
                
                <form action="/new-stock-system/controllers/auth/login/index.php" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i>Email Address
                        </label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               placeholder="name@company.com"
                               required
                               autocomplete="email">
                        <div class="invalid-feedback">
                            Please provide a valid email address.
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i>Password
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Enter your password"
                               required
                               autocomplete="current-password">
                        <div class="invalid-feedback">
                            Please provide your password.
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-login">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="footer-text">
            &copy; <?php echo date('Y'); ?> <?php echo COMPANY_NAME; ?>. All rights reserved.
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>