<?php
/**
 * Registration Page
 * 
 * New user registration entry point
 */

session_start();

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/utils/helpers.php';
require_once __DIR__ . '/utils/auth_middleware.php';

// Redirect if already authenticated
redirectIfAuthenticated();

$pageTitle = 'Register - ' . APP_NAME;
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
            padding: 20px 0;
        }
        
        .register-container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
        }
        
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .register-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .register-header i {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .register-body {
            padding: 40px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <i class="bi bi-person-plus"></i>
                <h3>Create Account</h3>
                <p class="mb-0"><?php echo APP_NAME; ?></p>
            </div>
            
            <div class="register-body">
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
                ?>
                
                <form action="/new-stock-system/controllers/auth/register/index.php" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-person"></i> Full Name
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               placeholder="Enter your full name"
                               required>
                        <div class="invalid-feedback">
                            Please provide your name.
                        </div>
                    </div>
                    
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
                               placeholder="Create a password"
                               minlength="6"
                               required>
                        <div class="invalid-feedback">
                            Password must be at least 6 characters long.
                        </div>
                        <small class="form-text text-muted">Minimum 6 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">
                            <i class="bi bi-lock-fill"></i> Confirm Password
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="confirm_password" 
                               name="confirm_password" 
                               placeholder="Confirm your password"
                               required>
                        <div class="invalid-feedback">
                            Please confirm your password.
                        </div>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-register">
                            <i class="bi bi-person-check"></i> Create Account
                        </button>
                    </div>
                    
                    <div class="text-center">
                        <p class="mb-0">
                            Already have an account? 
                            <a href="/new-stock-system/login.php" class="text-decoration-none">Sign in here</a>
                        </p>
                    </div>
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
                    
                    // Check if passwords match
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    if (password !== confirmPassword) {
                        event.preventDefault();
                        event.stopPropagation();
                        document.getElementById('confirm_password').setCustomValidity('Passwords do not match');
                    } else {
                        document.getElementById('confirm_password').setCustomValidity('');
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>
