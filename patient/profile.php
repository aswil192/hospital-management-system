<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('patient');

$user = getUserById(getUserId());
$patient = getPatientByUserId(getUserId());

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $blood_group = sanitizeInput($_POST['blood_group']);
    $medical_history = sanitizeInput($_POST['medical_history']);
    
    if (empty($name) || empty($phone)) {
        $error = 'Name and phone are required fields';
    } else {
        $conn = getDBConnection();
        $conn->begin_transaction();
        
        try {
            // Update users table
            $userStmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
            $userStmt->bind_param("sssi", $name, $phone, $address, $_SESSION['user_id']);
            $userStmt->execute();
            $userStmt->close();
            
            // Update patients table
            $patientStmt = $conn->prepare("UPDATE patients SET date_of_birth = ?, gender = ?, blood_group = ?, medical_history = ? WHERE user_id = ?");
            $patientStmt->bind_param("ssssi", $date_of_birth, $gender, $blood_group, $medical_history, $_SESSION['user_id']);
            $patientStmt->execute();
            $patientStmt->close();
            
            $conn->commit();
            
            $_SESSION['user_name'] = $name;
            
            $success = 'Profile updated successfully';
            
            // Refresh data
            $user = getUserById(getUserId());
            $patient = getPatientByUserId(getUserId());
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Failed to update profile. Please try again.';
        }
        
        closeDBConnection($conn);
    }
}

$pageTitle = 'My Profile - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>My Profile</h2>
                <p class="text-muted">Manage your personal information</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <form method="POST" action="">
                                <h5 class="mb-3">Personal Information</h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" 
                                               value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                        <small class="text-muted">Email cannot be changed</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number *</label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                               value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                               value="<?php echo htmlspecialchars($patient['date_of_birth'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-select" id="gender" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="male" <?php echo ($patient['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                                            <option value="female" <?php echo ($patient['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                                            <option value="other" <?php echo ($patient['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="blood_group" class="form-label">Blood Group</label>
                                    <select class="form-select" id="blood_group" name="blood_group">
                                        <option value="">Select Blood Group</option>
                                        <?php 
                                        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                        foreach ($bloodGroups as $bg): ?>
                                            <option value="<?php echo $bg; ?>" <?php echo ($patient['blood_group'] === $bg) ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($user['address']); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="medical_history" class="form-label">Medical History</label>
                                    <textarea class="form-control" id="medical_history" name="medical_history" rows="3" 
                                              placeholder="Any chronic conditions, allergies, or past medical issues..."><?php echo htmlspecialchars($patient['medical_history'] ?? ''); ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 100px; height: 100px;">
                                <i class="fas fa-user fa-4x"></i>
                            </div>
                            <h5><?php echo htmlspecialchars($user['name']); ?></h5>
                            <p class="text-muted">Patient</p>
                            <hr>
                            <div class="text-start">
                                <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                <p class="mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                                <p class="mb-2"><strong>Status:</strong> 
                                    <span class="badge <?php echo getStatusBadgeClass($user['status']); ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </p>
                                <p class="mb-0"><strong>Member Since:</strong> <?php echo formatDate($user['created_at']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
