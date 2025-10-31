<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

$error = '';
$success = '';

// Handle add doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_doctor'])) {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $specialization = sanitizeInput($_POST['specialization']);
    $license_number = sanitizeInput($_POST['license_number']);
    $qualification = sanitizeInput($_POST['qualification']);
    $experience_years = intval($_POST['experience_years']);
    $salary = floatval($_POST['salary']);
    $join_date = $_POST['join_date'];
    
    $conn = getDBConnection();
    
    // Check if email exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        $error = 'Email already exists';
    } else {
        $conn->begin_transaction();
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $userStmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, 'doctor', ?, ?)");
            $userStmt->bind_param("sssss", $name, $email, $hashed_password, $phone, $address);
            $userStmt->execute();
            $user_id = $conn->insert_id;
            $userStmt->close();
            
            // Insert doctor
            $doctorStmt = $conn->prepare("INSERT INTO doctors (user_id, specialization, license_number, qualification, experience_years, salary, join_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $doctorStmt->bind_param("isssiDs", $user_id, $specialization, $license_number, $qualification, $experience_years, $salary, $join_date);
            $doctorStmt->execute();
            $doctorStmt->close();
            
            $conn->commit();
            $success = 'Doctor added successfully';
            $_POST = [];
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Failed to add doctor';
        }
    }
    $checkStmt->close();
    closeDBConnection($conn);
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $doctor_id = intval($_GET['delete']);
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT user_id FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $user_id = $row['user_id'];
        $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $deleteStmt->bind_param("i", $user_id);
        if ($deleteStmt->execute()) {
            setFlashMessage('Doctor deleted successfully', 'success');
        }
        $deleteStmt->close();
    }
    $stmt->close();
    closeDBConnection($conn);
    header('Location: ' . SITE_URL . '/admin/doctors.php');
    exit();
}

// Get all doctors
$conn = getDBConnection();
$query = "SELECT d.*, u.name, u.email, u.phone, u.address, u.status 
          FROM doctors d 
          JOIN users u ON d.user_id = u.id 
          ORDER BY u.name";
$doctors = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
closeDBConnection($conn);

$pageTitle = 'Manage Doctors - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2>Manage Doctors</h2>
                        <p class="text-muted">Add and manage doctors</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                        <i class="fas fa-plus me-2"></i>Add New Doctor
                    </button>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Specialization</th>
                                    <th>License</th>
                                    <th>Experience</th>
                                    <th>Salary</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($doctors as $doctor): ?>
                                <tr>
                                    <td>#<?php echo $doctor['id']; ?></td>
                                    <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['license_number']); ?></td>
                                    <td><?php echo $doctor['experience_years']; ?> years</td>
                                    <td><?php echo formatCurrency($doctor['salary']); ?></td>
                                    <td>
                                        <span class="badge <?php echo getStatusBadgeClass($doctor['availability_status']); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $doctor['availability_status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $doctor['id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="?delete=<?php echo $doctor['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                
                                <!-- View Modal -->
                                <div class="modal fade" id="viewModal<?php echo $doctor['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Doctor Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-2"><strong>Name:</strong> <?php echo htmlspecialchars($doctor['name']); ?></div>
                                                    <div class="col-md-6 mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($doctor['email']); ?></div>
                                                    <div class="col-md-6 mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($doctor['phone']); ?></div>
                                                    <div class="col-md-6 mb-2"><strong>Specialization:</strong> <?php echo htmlspecialchars($doctor['specialization']); ?></div>
                                                    <div class="col-md-6 mb-2"><strong>License:</strong> <?php echo htmlspecialchars($doctor['license_number']); ?></div>
                                                    <div class="col-md-6 mb-2"><strong>Qualification:</strong> <?php echo htmlspecialchars($doctor['qualification'] ?? 'N/A'); ?></div>
                                                    <div class="col-md-6 mb-2"><strong>Experience:</strong> <?php echo $doctor['experience_years']; ?> years</div>
                                                    <div class="col-md-6 mb-2"><strong>Salary:</strong> <?php echo formatCurrency($doctor['salary']); ?>/month</div>
                                                    <div class="col-md-6 mb-2"><strong>Join Date:</strong> <?php echo formatDate($doctor['join_date']); ?></div>
                                                    <div class="col-md-6 mb-2"><strong>Status:</strong> 
                                                        <span class="badge <?php echo getStatusBadgeClass($doctor['availability_status']); ?>">
                                                            <?php echo ucfirst(str_replace('_', ' ', $doctor['availability_status'])); ?>
                                                        </span>
                                                    </div>
                                                    <div class="col-12 mb-2"><strong>Address:</strong><br><?php echo nl2br(htmlspecialchars($doctor['address'] ?? 'N/A')); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Doctor Modal -->
<div class="modal fade" id="addDoctorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Doctor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Specialization *</label>
                            <input type="text" class="form-control" name="specialization" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">License Number *</label>
                            <input type="text" class="form-control" name="license_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Qualification</label>
                            <input type="text" class="form-control" name="qualification">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Experience (years)</label>
                            <input type="number" class="form-control" name="experience_years" value="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Monthly Salary ($) *</label>
                            <input type="number" class="form-control" name="salary" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Join Date *</label>
                            <input type="date" class="form-control" name="join_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_doctor" class="btn btn-primary">Add Doctor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
