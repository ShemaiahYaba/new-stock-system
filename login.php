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
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-header i {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="bi bi-box-seam"></i>
                <h3><?php echo APP_NAME; ?></h3>
                <p class="mb-0"><?php echo COMPANY_NAME; ?></p>
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
                        'info' => 'alert-info'
                    ][$flash['type']] ?? 'alert-info';
                    ?>
                    <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                }
                
                // Check for timeout parameter
                if (isset($_GET['timeout'])) {
                    ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Your session has expired. Please log in again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                }
                ?>
                
                <h4 class="mb-4 text-center">Sign In</h4>
                
                <form action="/new-stock-system/controllers/auth/login/index.php" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i> Email Address
                        </label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               placeholder="Enter your email"
                               required>
                        <div class="invalid-feedback">
                            Please provide a valid email address.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Password
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Enter your password"
                               required>
                        <div class="invalid-feedback">
                            Please provide your password.
                        </div>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-login">
                            <i class="bi bi-box-arrow-in-right"></i> Sign In
                        </button>
                    </div>
                    
                    <!-- Registration has been disabled -->
                </form>
            </div>
        </div>
        
        <div class="text-center mt-3 text-white">
            <small>&copy; <?php echo date('Y'); ?> <?php echo COMPANY_NAME; ?>. All rights reserved.</small>
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
