<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirectToDashboard();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $blood_group = sanitizeInput($_POST['blood_group']);
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($phone)) {
        $error = 'Please fill in all required fields';
    } elseif (!isValidEmail($email)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $conn = getDBConnection();
        
        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $error = 'Email address already registered';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Begin transaction
            $conn->begin_transaction();
            
            try {
                // Insert into users table
                $userStmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, 'patient', ?, ?)");
                $userStmt->bind_param("sssss", $name, $email, $hashed_password, $phone, $address);
                $userStmt->execute();
                $user_id = $conn->insert_id;
                $userStmt->close();
                
                // Insert into patients table
                $patientStmt = $conn->prepare("INSERT INTO patients (user_id, date_of_birth, gender, blood_group) VALUES (?, ?, ?, ?)");
                $patientStmt->bind_param("isss", $user_id, $date_of_birth, $gender, $blood_group);
                $patientStmt->execute();
                $patientStmt->close();
                
                $conn->commit();
                
                $success = 'Registration successful! You can now login.';
                
                // Clear form data
                $_POST = [];
            } catch (Exception $e) {
                $conn->rollback();
                $error = 'Registration failed. Please try again.';
            }
        }
        
        $checkStmt->close();
        closeDBConnection($conn);
    }
}

$pageTitle = 'Patient Registration - ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                        <h2>Patient Registration</h2>
                        <p class="text-muted">Create your account</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                            <a href="<?php echo SITE_URL; ?>/login.php" class="alert-link">Click here to login</a>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="text-muted">At least 6 characters</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                       value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male" <?php echo (($_POST['gender'] ?? '') === 'male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo (($_POST['gender'] ?? '') === 'female') ? 'selected' : ''; ?>>Female</option>
                                    <option value="other" <?php echo (($_POST['gender'] ?? '') === 'other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="blood_group" class="form-label">Blood Group</label>
                                <select class="form-select" id="blood_group" name="blood_group">
                                    <option value="">Select Blood Group</option>
                                    <option value="A+" <?php echo (($_POST['blood_group'] ?? '') === 'A+') ? 'selected' : ''; ?>>A+</option>
                                    <option value="A-" <?php echo (($_POST['blood_group'] ?? '') === 'A-') ? 'selected' : ''; ?>>A-</option>
                                    <option value="B+" <?php echo (($_POST['blood_group'] ?? '') === 'B+') ? 'selected' : ''; ?>>B+</option>
                                    <option value="B-" <?php echo (($_POST['blood_group'] ?? '') === 'B-') ? 'selected' : ''; ?>>B-</option>
                                    <option value="AB+" <?php echo (($_POST['blood_group'] ?? '') === 'AB+') ? 'selected' : ''; ?>>AB+</option>
                                    <option value="AB-" <?php echo (($_POST['blood_group'] ?? '') === 'AB-') ? 'selected' : ''; ?>>AB-</option>
                                    <option value="O+" <?php echo (($_POST['blood_group'] ?? '') === 'O+') ? 'selected' : ''; ?>>O+</option>
                                    <option value="O-" <?php echo (($_POST['blood_group'] ?? '') === 'O-') ? 'selected' : ''; ?>>O-</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0">Already have an account? 
                            <a href="<?php echo SITE_URL; ?>/login.php">Login</a>
                        </p>
                        <p class="mb-0 mt-2">
                            <a href="<?php echo SITE_URL; ?>/register-doctor.php">Register as Doctor instead</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
