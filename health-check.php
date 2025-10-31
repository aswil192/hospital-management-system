<?php
/**
 * System Health Check
 * This file helps verify that the Hospital Management System is properly installed
 */

$checks = [];
$allPassed = true;

// Check PHP version
$phpVersion = phpversion();
$checks['PHP Version'] = [
    'status' => version_compare($phpVersion, '7.4.0', '>='),
    'message' => $phpVersion,
    'required' => '7.4.0 or higher'
];
if (!$checks['PHP Version']['status']) $allPassed = false;

// Check if config files exist
$checks['Config Files'] = [
    'status' => file_exists(__DIR__ . '/config/config.php') && file_exists(__DIR__ . '/config/database.php'),
    'message' => file_exists(__DIR__ . '/config/config.php') ? 'Found' : 'Missing',
    'required' => 'config.php and database.php must exist'
];
if (!$checks['Config Files']['status']) $allPassed = false;

// Check database connection
try {
    require_once __DIR__ . '/config/database.php';
    $conn = getDBConnection();
    $checks['Database Connection'] = [
        'status' => $conn ? true : false,
        'message' => $conn ? 'Connected' : 'Failed',
        'required' => 'MySQL connection required'
    ];
    
    if ($conn) {
        // Check if tables exist
        $tables = ['users', 'patients', 'doctors', 'appointments', 'medicines', 'prescriptions', 'bills', 'payments', 'salaries'];
        $missingTables = [];
        
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows == 0) {
                $missingTables[] = $table;
            }
        }
        
        $checks['Database Tables'] = [
            'status' => empty($missingTables),
            'message' => empty($missingTables) ? 'All 9 tables found' : 'Missing: ' . implode(', ', $missingTables),
            'required' => '9 tables required'
        ];
        if (!$checks['Database Tables']['status']) $allPassed = false;
        
        // Check for admin user
        $adminCheck = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
        $adminCount = $adminCheck->fetch_assoc()['count'];
        
        $checks['Admin Account'] = [
            'status' => $adminCount > 0,
            'message' => $adminCount > 0 ? "$adminCount admin account(s) found" : 'No admin account',
            'required' => 'At least 1 admin account required'
        ];
        if (!$checks['Admin Account']['status']) $allPassed = false;
        
        closeDBConnection($conn);
    } else {
        $allPassed = false;
    }
} catch (Exception $e) {
    $checks['Database Connection'] = [
        'status' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'required' => 'MySQL connection required'
    ];
    $allPassed = false;
}

// Check required directories
$dirs = ['admin', 'doctor', 'patient', 'includes', 'config', 'database'];
$missingDirs = [];
foreach ($dirs as $dir) {
    if (!is_dir(__DIR__ . '/' . $dir)) {
        $missingDirs[] = $dir;
    }
}

$checks['Directory Structure'] = [
    'status' => empty($missingDirs),
    'message' => empty($missingDirs) ? 'All directories found' : 'Missing: ' . implode(', ', $missingDirs),
    'required' => '6 main directories required'
];
if (!$checks['Directory Structure']['status']) $allPassed = false;

// Check sessions
$checks['Session Support'] = [
    'status' => function_exists('session_start'),
    'message' => function_exists('session_start') ? 'Enabled' : 'Disabled',
    'required' => 'PHP sessions must be enabled'
];
if (!$checks['Session Support']['status']) $allPassed = false;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Health Check - Hospital Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="fas fa-heartbeat me-2"></i>
                            System Health Check
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if ($allPassed): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle fa-2x float-start me-3"></i>
                                <h5>All Systems Operational!</h5>
                                <p class="mb-0">Your Hospital Management System is properly installed and ready to use.</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle fa-2x float-start me-3"></i>
                                <h5>Installation Issues Detected</h5>
                                <p class="mb-0">Please review the issues below and refer to INSTALLATION_GUIDE.txt</p>
                            </div>
                        <?php endif; ?>
                        
                        <h5 class="mt-4 mb-3">System Checks:</h5>
                        
                        <div class="list-group">
                            <?php foreach ($checks as $name => $check): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <?php if ($check['status']): ?>
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                                <?php endif; ?>
                                                <?php echo $name; ?>
                                            </h6>
                                            <p class="mb-1"><strong>Status:</strong> <?php echo $check['message']; ?></p>
                                            <small class="text-muted"><?php echo $check['required']; ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if ($allPassed): ?>
                            <div class="mt-4">
                                <h5>Quick Links:</h5>
                                <div class="d-grid gap-2">
                                    <a href="index.php" class="btn btn-primary">
                                        <i class="fas fa-home me-2"></i>Go to Homepage
                                    </a>
                                    <a href="login.php" class="btn btn-outline-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login
                                    </a>
                                    <a href="register.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-user-plus me-2"></i>Register as Patient
                                    </a>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-light rounded">
                                <h6>Default Login Credentials:</h6>
                                <small>
                                    <strong>Admin:</strong> admin@hospital.com / admin123<br>
                                    <strong>Doctor:</strong> john.smith@hospital.com / doctor123<br>
                                    <strong>Patient:</strong> alice@example.com / patient123
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="mt-4">
                                <h5>Troubleshooting:</h5>
                                <ol>
                                    <li>Make sure XAMPP Apache and MySQL are running</li>
                                    <li>Import database/hospital_db.sql into phpMyAdmin</li>
                                    <li>Verify database credentials in config/database.php</li>
                                    <li>Check that all files are uploaded correctly</li>
                                    <li>Review INSTALLATION_GUIDE.txt for detailed instructions</li>
                                </ol>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <small>Hospital Management System v1.0 - Health Check Page</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
