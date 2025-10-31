<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $patient_id = intval($_GET['delete']);
    $conn = getDBConnection();
    
    // Get user_id first
    $stmt = $conn->prepare("SELECT user_id FROM patients WHERE id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $user_id = $row['user_id'];
        // Delete user (cascade will delete patient)
        $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $deleteStmt->bind_param("i", $user_id);
        if ($deleteStmt->execute()) {
            setFlashMessage('Patient deleted successfully', 'success');
        }
        $deleteStmt->close();
    }
    $stmt->close();
    closeDBConnection($conn);
    header('Location: ' . SITE_URL . '/admin/patients.php');
    exit();
}

// Get all patients
$conn = getDBConnection();
$query = "SELECT p.*, u.name, u.email, u.phone, u.address, u.status, u.created_at 
          FROM patients p 
          JOIN users u ON p.user_id = u.id 
          ORDER BY u.created_at DESC";
$patients = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
closeDBConnection($conn);

$pageTitle = 'Manage Patients - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>Manage Patients</h2>
                <p class="text-muted">View and manage all patients</p>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($patients)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No patients found.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>DOB</th>
                                        <th>Blood Group</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($patients as $patient): ?>
                                    <tr>
                                        <td>#<?php echo $patient['id']; ?></td>
                                        <td><?php echo htmlspecialchars($patient['name']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['email']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                                        <td><?php echo $patient['date_of_birth'] ? formatDate($patient['date_of_birth']) : 'N/A'; ?></td>
                                        <td><?php echo htmlspecialchars($patient['blood_group'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge <?php echo getStatusBadgeClass($patient['status']); ?>">
                                                <?php echo ucfirst($patient['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($patient['created_at']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $patient['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="?delete=<?php echo $patient['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this patient?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    
                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal<?php echo $patient['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Patient Details #<?php echo $patient['id']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Name:</strong><br>
                                                            <?php echo htmlspecialchars($patient['name']); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Email:</strong><br>
                                                            <?php echo htmlspecialchars($patient['email']); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Phone:</strong><br>
                                                            <?php echo htmlspecialchars($patient['phone']); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Date of Birth:</strong><br>
                                                            <?php echo $patient['date_of_birth'] ? formatDate($patient['date_of_birth']) : 'N/A'; ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Gender:</strong><br>
                                                            <?php echo ucfirst($patient['gender'] ?? 'N/A'); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Blood Group:</strong><br>
                                                            <?php echo htmlspecialchars($patient['blood_group'] ?? 'N/A'); ?>
                                                        </div>
                                                        <div class="col-12 mb-3">
                                                            <strong>Address:</strong><br>
                                                            <?php echo nl2br(htmlspecialchars($patient['address'] ?? 'N/A')); ?>
                                                        </div>
                                                        <div class="col-12 mb-3">
                                                            <strong>Medical History:</strong><br>
                                                            <?php echo nl2br(htmlspecialchars($patient['medical_history'] ?? 'No medical history')); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
