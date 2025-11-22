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

    <!-- Favicons for all devices -->
    <link rel="icon" type="image/png" href="/new-stock-system/assets/logo.png">
    <link rel="apple-touch-icon" href="/new-stock-system/assets/logo.png">
    <link rel="shortcut icon" href="/new-stock-system/assets/logo.png">

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
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transition: background 0.6s ease-in-out;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 460px;
        }

        .login-card {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(229, 231, 235, 0.6);
            background: #ffffff;
        }

        .login-header {
            background: #1e3a8a;
            color: white;
            text-align: center;
            padding: 30px 20px;
        }

        .login-header img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-bottom: 12px;
        }

        .login-header h1 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .login-header p {
            font-size: 14px;
            color: #dbeafe;
            margin: 0;
        }

        .login-body {
            padding: 32px 36px 40px;
            background: #ffffff;
        }

        .login-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 24px;
            text-align: center;
        }

        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
        }

        .form-control {
            height: 46px;
            padding: 12px 14px;
            font-size: 15px;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.15);
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            border: none;
            background: none;
            cursor: pointer;
            color: #6b7280;
            font-size: 18px;
        }

        .toggle-password:hover {
            color: #1e3a8a;
        }

        .btn-login {
            height: 46px;
            background: #1e3a8a;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            color: white;
            transition: all 0.25s ease;
            letter-spacing: 0.3px;
        }

        .btn-login:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(30, 64, 175, 0.35);
        }

        .login-footer {
            background: #f9fafb;
            text-align: center;
            padding: 16px;
            font-size: 13px;
            color: #6b7280;
        }

        @media (max-width: 576px) {
            .login-body {
                padding: 24px;
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
                            <i class="bi bi-lock"></i> Password
                        </label>
                        <div class="password-wrapper">
                            <input type="password"
                                   class="form-control"
                                   id="password"
                                   name="password"
                                   placeholder="Enter your password"
                                   required
                                   autocomplete="current-password">
                            <button type="button" class="toggle-password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
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
            
             <div class="login-footer">
                &copy; <?php echo date('Y'); ?> <?php echo COMPANY_NAME; ?>. All rights reserved.
            </div>
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
    
    <script>
        // Random background images
        const backgrounds = [
            '/new-stock-system/assets/bg1.png',
            '/new-stock-system/assets/bg2.png',
            '/new-stock-system/assets/bg3.png'
        ];
    
        const randomIndex = Math.floor(Math.random() * backgrounds.length);
        const chosenBg = backgrounds[randomIndex];
    
        // Apply lighter overlay for clearer images
        document.body.style.backgroundImage =
            `linear-gradient(rgba(17, 24, 39, 0.35), rgba(17, 24, 39, 0.35)), url('${chosenBg}')`;
    
        // Password toggle functionality
        const togglePassword = document.querySelector('.toggle-password');
        const passwordField = document.getElementById('password');
    
        togglePassword.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.innerHTML = type === 'password'
                ? '<i class="bi bi-eye"></i>'
                : '<i class="bi bi-eye-slash"></i>';
        });
    </script>

</body>
</html>