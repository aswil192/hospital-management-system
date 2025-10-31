<?php
/**
 * One-time Admin Account Creation
 * Delete this file after creating the admin account
 */

require_once __DIR__ . '/config/database.php';

$success = '';
$error = '';

// Check if admin already exists
$conn = getDBConnection();
$checkStmt = $conn->prepare("SELECT id FROM users WHERE email = 'admin@gmail.com'");
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    $error = 'Admin account with email admin@gmail.com already exists!';
} else {
    // Create new admin account
    $name = 'System Administrator';
    $email = 'admin@gmail.com';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $role = 'admin';
    $phone = '0000000000';
    $address = 'Hospital Administration Office';
    $status = 'active';
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone, address, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $password, $role, $phone, $address, $status);
    
    if ($stmt->execute()) {
        $success = 'Admin account created successfully!<br><br>
                    <strong>Email:</strong> admin@gmail.com<br>
                    <strong>Password:</strong> admin123<br><br>
                    <a href="login.php" class="btn btn-primary">Go to Login</a><br><br>
                    <span class="text-danger">⚠️ Please delete this file (create-admin.php) after use for security!</span>';
    } else {
        $error = 'Failed to create admin account: ' . $stmt->error;
    }
    
    $stmt->close();
}

$checkStmt->close();
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-shield fa-3x text-primary mb-3"></i>
                            <h2>Create Admin Account</h2>
                        </div>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                                <br><br>
                                <a href="login.php" class="btn btn-primary">Go to Login</a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!$success && !$error): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                This page will create an admin account with:<br><br>
                                <strong>Email:</strong> admin@gmail.com<br>
                                <strong>Password:</strong> admin123
                            </div>
                            <p class="text-center">
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-primary">
                                    Create Admin Account
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
