<?php
/**
 * Installation Script
 * 
 * One-click installation for the Stock Taking System
 * Run this once to set up the database
 * 
 * IMPORTANT: Delete this file after installation for security!
 */

// Check if already installed
$configFile = __DIR__ . '/config/db.php';
$migrationFile = __DIR__ . '/migrations/001_initial_schema.sql';

// Configuration
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'stock_system';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Connect to MySQL (without database)
        $conn = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database
        $conn->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Connect to the new database
        $conn->exec("USE `$dbName`");
        
        // Read and execute migration file
        $sql = file_get_contents($migrationFile);
        
        // Split into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $conn->exec($statement);
            }
        }
        
        $success = true;
        
    } catch (PDOException $e) {
        $errors[] = "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        $errors[] = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - Stock Taking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .install-card {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .install-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="install-card">
        <div class="install-header">
            <i class="bi bi-box-seam" style="font-size: 48px;"></i>
            <h2 class="mt-3">Stock Taking System</h2>
            <p class="mb-0">Installation Wizard</p>
        </div>
        
        <div class="p-4">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i>
                    <strong>Installation Successful!</strong>
                </div>
                
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h5>Default Login Credentials:</h5>
                        <p class="mb-1"><strong>Email:</strong> admin@example.com</p>
                        <p class="mb-0"><strong>Password:</strong> admin123</p>
                    </div>
                </div>
                
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Important Security Steps:</strong>
                    <ol class="mb-0 mt-2">
                        <li>Delete this <code>install.php</code> file immediately</li>
                        <li>Change the default admin password after first login</li>
                        <li>Review security settings in <code>.htaccess</code></li>
                    </ol>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="/new-stock-system/login.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Go to Login
                    </a>
                    <a href="/new-stock-system/README.md" class="btn btn-secondary" target="_blank">
                        <i class="bi bi-book"></i> Read Documentation
                    </a>
                </div>
                
            <?php elseif (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle"></i>
                    <strong>Installation Failed!</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="alert alert-info">
                    <strong>Troubleshooting:</strong>
                    <ul class="mb-0">
                        <li>Ensure MySQL is running in XAMPP</li>
                        <li>Check database credentials in this file</li>
                        <li>Verify you have permission to create databases</li>
                    </ul>
                </div>
                
                <form method="POST" class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise"></i> Try Again
                    </button>
                </form>
                
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    This wizard will set up your database and create the necessary tables.
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Pre-Installation Checklist</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="check1" required>
                            <label class="form-check-label" for="check1">
                                XAMPP Apache and MySQL are running
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="check2" required>
                            <label class="form-check-label" for="check2">
                                I have backed up any existing data
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check3" required>
                            <label class="form-check-label" for="check3">
                                I understand this will create a new database
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Database Configuration</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th>Host:</th>
                                <td><?php echo $dbHost; ?></td>
                            </tr>
                            <tr>
                                <th>Database:</th>
                                <td><?php echo $dbName; ?></td>
                            </tr>
                            <tr>
                                <th>Username:</th>
                                <td><?php echo $dbUser; ?></td>
                            </tr>
                        </table>
                        <small class="text-muted">
                            To change these settings, edit the variables at the top of this file.
                        </small>
                    </div>
                </div>
                
                <form method="POST" id="installForm" class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" id="installBtn" disabled>
                        <i class="bi bi-download"></i> Install Now
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable install button only when all checkboxes are checked
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        const installBtn = document.getElementById('installBtn');
        
        if (checkboxes.length > 0) {
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    installBtn.disabled = !allChecked;
                });
            });
        }
        
        // Confirm before installation
        const installForm = document.getElementById('installForm');
        if (installForm) {
            installForm.addEventListener('submit', (e) => {
                if (!confirm('Are you sure you want to proceed with the installation?')) {
                    e.preventDefault();
                }
            });
        }
    </script>
</body>
</html>
