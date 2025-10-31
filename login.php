<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirectToDashboard();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if ($user['status'] !== 'active') {
                $error = 'Your account has been deactivated. Please contact administrator.';
            } elseif (password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                regenerateSession();
                
                // Get role-specific ID
                if ($user['role'] === 'patient') {
                    $patientStmt = $conn->prepare("SELECT id FROM patients WHERE user_id = ?");
                    $patientStmt->bind_param("i", $user['id']);
                    $patientStmt->execute();
                    $patientResult = $patientStmt->get_result();
                    if ($patientRow = $patientResult->fetch_assoc()) {
                        $_SESSION['patient_id'] = $patientRow['id'];
                    }
                    $patientStmt->close();
                } elseif ($user['role'] === 'doctor') {
                    $doctorStmt = $conn->prepare("SELECT id FROM doctors WHERE user_id = ?");
                    $doctorStmt->bind_param("i", $user['id']);
                    $doctorStmt->execute();
                    $doctorResult = $doctorStmt->get_result();
                    if ($doctorRow = $doctorResult->fetch_assoc()) {
                        $_SESSION['doctor_id'] = $doctorRow['id'];
                    }
                    $doctorStmt->close();
                }
                
                setFlashMessage('Welcome back, ' . $user['name'] . '!', 'success');
                
                $stmt->close();
                closeDBConnection($conn);
                
                redirectToDashboard();
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
        
        $stmt->close();
        closeDBConnection($conn);
    }
}

$pageTitle = 'Login - ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-hospital fa-3x text-primary mb-3"></i>
                        <h2>Login</h2>
                        <p class="text-muted">Sign in to your account</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0">Don't have an account? 
                            <a href="<?php echo SITE_URL; ?>/register.php">Register as Patient</a>
                        </p>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <small class="text-muted">
                            <strong>Demo Credentials:</strong><br>
                            Admin: admin@hospital.com / admin123<br>
                            Doctor: john.smith@hospital.com / doctor123<br>
                            Patient: alice@example.com / patient123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
