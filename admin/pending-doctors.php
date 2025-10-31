<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

// Handle approval
if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
    $user_id = intval($_GET['approve']);
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ? AND role = 'doctor'");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        // Also set doctor availability to available
        $doctorStmt = $conn->prepare("UPDATE doctors SET availability_status = 'available' WHERE user_id = ?");
        $doctorStmt->bind_param("i", $user_id);
        $doctorStmt->execute();
        $doctorStmt->close();
        
        setFlashMessage('Doctor approved successfully', 'success');
    }
    
    $stmt->close();
    closeDBConnection($conn);
    header('Location: ' . SITE_URL . '/admin/pending-doctors.php');
    exit();
}

// Handle rejection
if (isset($_GET['reject']) && is_numeric($_GET['reject'])) {
    $user_id = intval($_GET['reject']);
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'doctor' AND status = 'inactive'");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        setFlashMessage('Doctor registration rejected', 'info');
    }
    
    $stmt->close();
    closeDBConnection($conn);
    header('Location: ' . SITE_URL . '/admin/pending-doctors.php');
    exit();
}

// Get pending doctor registrations
$conn = getDBConnection();
$query = "SELECT d.*, u.id as user_id, u.name, u.email, u.phone, u.address, u.created_at 
          FROM doctors d 
          JOIN users u ON d.user_id = u.id 
          WHERE u.status = 'inactive' AND u.role = 'doctor'
          ORDER BY u.created_at DESC";
$pendingDoctors = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
closeDBConnection($conn);

$pageTitle = 'Pending Doctor Approvals - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>Pending Doctor Approvals</h2>
                <p class="text-muted">Review and approve doctor registrations</p>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($pendingDoctors)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted">No pending doctor approvals.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Specialization</th>
                                        <th>License</th>
                                        <th>Experience</th>
                                        <th>Applied On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingDoctors as $doctor): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                                        <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                                        <td><?php echo htmlspecialchars($doctor['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                                        <td><?php echo htmlspecialchars($doctor['license_number']); ?></td>
                                        <td><?php echo $doctor['experience_years']; ?> years</td>
                                        <td><?php echo formatDate($doctor['created_at']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $doctor['user_id']; ?>">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <a href="?approve=<?php echo $doctor['user_id']; ?>" 
                                               class="btn btn-sm btn-success" 
                                               onclick="return confirm('Are you sure you want to approve this doctor?')">
                                                <i class="fas fa-check"></i> Approve
                                            </a>
                                            <a href="?reject=<?php echo $doctor['user_id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to reject this application?')">
                                                <i class="fas fa-times"></i> Reject
                                            </a>
                                        </td>
                                    </tr>
                                    
                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal<?php echo $doctor['user_id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Doctor Application Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Name:</strong><br>
                                                            <?php echo htmlspecialchars($doctor['name']); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Email:</strong><br>
                                                            <?php echo htmlspecialchars($doctor['email']); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Phone:</strong><br>
                                                            <?php echo htmlspecialchars($doctor['phone']); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Specialization:</strong><br>
                                                            <?php echo htmlspecialchars($doctor['specialization']); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>License Number:</strong><br>
                                                            <?php echo htmlspecialchars($doctor['license_number']); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Qualification:</strong><br>
                                                            <?php echo htmlspecialchars($doctor['qualification'] ?? 'N/A'); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Experience:</strong><br>
                                                            <?php echo $doctor['experience_years']; ?> years
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Applied On:</strong><br>
                                                            <?php echo formatDateTime($doctor['created_at']); ?>
                                                        </div>
                                                        <div class="col-12 mb-3">
                                                            <strong>Address:</strong><br>
                                                            <?php echo nl2br(htmlspecialchars($doctor['address'] ?? 'N/A')); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <a href="?approve=<?php echo $doctor['user_id']; ?>" 
                                                       class="btn btn-success">
                                                        <i class="fas fa-check"></i> Approve
                                                    </a>
                                                    <a href="?reject=<?php echo $doctor['user_id']; ?>" 
                                                       class="btn btn-danger">
                                                        <i class="fas fa-times"></i> Reject
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
