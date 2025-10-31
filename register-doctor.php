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
    $specialization = sanitizeInput($_POST['specialization']);
    $license_number = sanitizeInput($_POST['license_number']);
    $qualification = sanitizeInput($_POST['qualification']);
    $experience_years = intval($_POST['experience_years']);
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($specialization) || empty($license_number)) {
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
                // Insert into users table - Set status as 'inactive' for approval
                $status = 'inactive';
                $userStmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone, address, status) VALUES (?, ?, ?, 'doctor', ?, ?, ?)");
                $userStmt->bind_param("ssssss", $name, $email, $hashed_password, $phone, $address, $status);
                $userStmt->execute();
                $user_id = $conn->insert_id;
                $userStmt->close();
                
                // Insert into doctors table
                $join_date = date('Y-m-d');
                $salary = 0.00; // Admin will set this later
                $doctorStmt = $conn->prepare("INSERT INTO doctors (user_id, specialization, license_number, qualification, experience_years, salary, join_date, availability_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'unavailable')");
                $doctorStmt->bind_param("isssids", $user_id, $specialization, $license_number, $qualification, $experience_years, $salary, $join_date);
                $doctorStmt->execute();
                $doctorStmt->close();
                
                $conn->commit();
                
                $success = 'Registration successful! Your account is pending approval by the administrator. You will be notified once approved.';
                
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

$pageTitle = 'Doctor Registration - ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                        <h2>Doctor Registration</h2>
                        <p class="text-muted">Join our team of healthcare professionals</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                            <br><a href="<?php echo SITE_URL; ?>/login.php" class="alert-link">Click here to login after approval</a>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <h5 class="mb-3 text-primary">Personal Information</h5>
                        
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
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-3 text-primary">Professional Information</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="specialization" class="form-label">Specialization *</label>
                                <select class="form-select" id="specialization" name="specialization" required>
                                    <option value="">Select Specialization</option>
                                    <option value="Cardiology" <?php echo (($_POST['specialization'] ?? '') === 'Cardiology') ? 'selected' : ''; ?>>Cardiology</option>
                                    <option value="Pediatrics" <?php echo (($_POST['specialization'] ?? '') === 'Pediatrics') ? 'selected' : ''; ?>>Pediatrics</option>
                                    <option value="Orthopedics" <?php echo (($_POST['specialization'] ?? '') === 'Orthopedics') ? 'selected' : ''; ?>>Orthopedics</option>
                                    <option value="Neurology" <?php echo (($_POST['specialization'] ?? '') === 'Neurology') ? 'selected' : ''; ?>>Neurology</option>
                                    <option value="Dermatology" <?php echo (($_POST['specialization'] ?? '') === 'Dermatology') ? 'selected' : ''; ?>>Dermatology</option>
                                    <option value="Pulmonology" <?php echo (($_POST['specialization'] ?? '') === 'Pulmonology') ? 'selected' : ''; ?>>Pulmonology</option>
                                    <option value="Ophthalmology" <?php echo (($_POST['specialization'] ?? '') === 'Ophthalmology') ? 'selected' : ''; ?>>Ophthalmology</option>
                                    <option value="Gynecology" <?php echo (($_POST['specialization'] ?? '') === 'Gynecology') ? 'selected' : ''; ?>>Gynecology</option>
                                    <option value="Psychiatry" <?php echo (($_POST['specialization'] ?? '') === 'Psychiatry') ? 'selected' : ''; ?>>Psychiatry</option>
                                    <option value="General Medicine" <?php echo (($_POST['specialization'] ?? '') === 'General Medicine') ? 'selected' : ''; ?>>General Medicine</option>
                                    <option value="Other" <?php echo (($_POST['specialization'] ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="license_number" class="form-label">Medical License Number *</label>
                                <input type="text" class="form-control" id="license_number" name="license_number" 
                                       value="<?php echo htmlspecialchars($_POST['license_number'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="qualification" class="form-label">Qualification</label>
                                <input type="text" class="form-control" id="qualification" name="qualification" 
                                       placeholder="e.g., MBBS, MD, MS" 
                                       value="<?php echo htmlspecialchars($_POST['qualification'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="experience_years" class="form-label">Years of Experience</label>
                                <input type="number" class="form-control" id="experience_years" name="experience_years" 
                                       min="0" value="<?php echo htmlspecialchars($_POST['experience_years'] ?? '0'); ?>">
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Your registration will be reviewed by our admin team. You will be notified via email once your account is approved.
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3 mt-3">
                            <i class="fas fa-user-plus me-2"></i>Register as Doctor
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0">Already have an account? 
                            <a href="<?php echo SITE_URL; ?>/login.php">Login</a>
                        </p>
                        <p class="mb-0 mt-2">
                            <a href="<?php echo SITE_URL; ?>/register.php">Register as Patient instead</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
