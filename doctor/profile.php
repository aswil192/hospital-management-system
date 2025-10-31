<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('doctor');

$user = getUserById(getUserId());
$doctor = getDoctorByUserId(getUserId());

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $availability_status = $_POST['availability_status'];
    
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
            
            // Update doctors table
            $doctorStmt = $conn->prepare("UPDATE doctors SET availability_status = ? WHERE user_id = ?");
            $doctorStmt->bind_param("si", $availability_status, $_SESSION['user_id']);
            $doctorStmt->execute();
            $doctorStmt->close();
            
            $conn->commit();
            
            $_SESSION['user_name'] = $name;
            
            $success = 'Profile updated successfully';
            
            // Refresh data
            $user = getUserById(getUserId());
            $doctor = getDoctorByUserId(getUserId());
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
                <p class="text-muted">Manage your professional information</p>
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
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($user['address']); ?></textarea>
                                </div>
                                
                                <hr>
                                
                                <h5 class="mb-3">Professional Information</h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="specialization" class="form-label">Specialization</label>
                                        <input type="text" class="form-control" id="specialization" 
                                               value="<?php echo htmlspecialchars($doctor['specialization']); ?>" readonly>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="license_number" class="form-label">License Number</label>
                                        <input type="text" class="form-control" id="license_number" 
                                               value="<?php echo htmlspecialchars($doctor['license_number']); ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="qualification" class="form-label">Qualification</label>
                                        <input type="text" class="form-control" id="qualification" 
                                               value="<?php echo htmlspecialchars($doctor['qualification'] ?? 'N/A'); ?>" readonly>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="experience_years" class="form-label">Years of Experience</label>
                                        <input type="text" class="form-control" id="experience_years" 
                                               value="<?php echo $doctor['experience_years']; ?> years" readonly>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="availability_status" class="form-label">Availability Status</label>
                                    <select class="form-select" id="availability_status" name="availability_status">
                                        <option value="available" <?php echo ($doctor['availability_status'] === 'available') ? 'selected' : ''; ?>>Available</option>
                                        <option value="unavailable" <?php echo ($doctor['availability_status'] === 'unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                                        <option value="on_leave" <?php echo ($doctor['availability_status'] === 'on_leave') ? 'selected' : ''; ?>>On Leave</option>
                                    </select>
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
                                <i class="fas fa-user-md fa-4x"></i>
                            </div>
                            <h5><?php echo htmlspecialchars($user['name']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($doctor['specialization']); ?></p>
                            <hr>
                            <div class="text-start">
                                <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                <p class="mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                                <p class="mb-2"><strong>License:</strong> <?php echo htmlspecialchars($doctor['license_number']); ?></p>
                                <p class="mb-2"><strong>Salary:</strong> <?php echo formatCurrency($doctor['salary']); ?>/month</p>
                                <p class="mb-2"><strong>Status:</strong> 
                                    <span class="badge <?php echo getStatusBadgeClass($doctor['availability_status']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $doctor['availability_status'])); ?>
                                    </span>
                                </p>
                                <p class="mb-0"><strong>Joined:</strong> <?php echo formatDate($doctor['join_date']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
