<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('doctor');

$doctor = getDoctorByUserId(getUserId());
$doctor_id = $doctor['id'];

// Handle status update
if (isset($_GET['update']) && isset($_GET['status'])) {
    $appointment_id = intval($_GET['update']);
    $new_status = $_GET['status'];
    
    $allowedStatuses = ['confirmed', 'completed'];
    if (in_array($new_status, $allowedStatuses)) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?");
        $stmt->bind_param("sii", $new_status, $appointment_id, $doctor_id);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            setFlashMessage('Appointment status updated successfully', 'success');
        }
        $stmt->close();
        closeDBConnection($conn);
        header('Location: ' . SITE_URL . '/doctor/appointments.php');
        exit();
    }
}

// Get all appointments
$conn = getDBConnection();
$query = "SELECT a.*, u.name as patient_name, u.phone as patient_phone, 
          p.blood_group, p.medical_history 
          FROM appointments a 
          JOIN patients p ON a.patient_id = p.id 
          JOIN users u ON p.user_id = u.id 
          WHERE a.doctor_id = ? 
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
closeDBConnection($conn);

$pageTitle = 'My Appointments - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>My Appointments</h2>
                <p class="text-muted">View and manage patient appointments</p>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($appointments)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No appointments found.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date & Time</th>
                                        <th>Patient Info</th>
                                        <th>Symptoms</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $apt): ?>
                                    <tr>
                                        <td>#<?php echo $apt['id']; ?></td>
                                        <td>
                                            <?php echo formatDate($apt['appointment_date']); ?><br>
                                            <small class="text-muted"><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></small>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($apt['patient_name']); ?></strong><br>
                                            <small class="text-muted">
                                                <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($apt['patient_phone']); ?><br>
                                                <?php if ($apt['blood_group']): ?>
                                                    <i class="fas fa-tint me-1"></i>Blood: <?php echo htmlspecialchars($apt['blood_group']); ?>
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php 
                                            $symptoms = $apt['symptoms'] ?? 'N/A';
                                            echo htmlspecialchars(substr($symptoms, 0, 50)) . (strlen($symptoms) > 50 ? '...' : ''); 
                                            ?>
                                            <?php if (strlen($symptoms) > 50): ?>
                                                <button class="btn btn-sm btn-link p-0" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $apt['id']; ?>">
                                                    View More
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo getStatusBadgeClass($apt['status']); ?>">
                                                <?php echo ucfirst($apt['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($apt['status'] === 'pending'): ?>
                                                <a href="?update=<?php echo $apt['id']; ?>&status=confirmed" 
                                                   class="btn btn-sm btn-success mb-1">
                                                    <i class="fas fa-check"></i> Confirm
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($apt['status'] === 'confirmed'): ?>
                                                <a href="?update=<?php echo $apt['id']; ?>&status=completed" 
                                                   class="btn btn-sm btn-primary mb-1">
                                                    <i class="fas fa-check-circle"></i> Complete
                                                </a>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-info mb-1" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $apt['id']; ?>">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Detail Modal -->
                                    <div class="modal fade" id="detailModal<?php echo $apt['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Appointment Details #<?php echo $apt['id']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Patient Name:</strong><br>
                                                            <?php echo htmlspecialchars($apt['patient_name']); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Phone:</strong><br>
                                                            <?php echo htmlspecialchars($apt['patient_phone']); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Appointment Date:</strong><br>
                                                            <?php echo formatDate($apt['appointment_date']); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Appointment Time:</strong><br>
                                                            <?php echo date('h:i A', strtotime($apt['appointment_time'])); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Blood Group:</strong><br>
                                                            <?php echo htmlspecialchars($apt['blood_group'] ?? 'N/A'); ?>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Status:</strong><br>
                                                            <span class="badge <?php echo getStatusBadgeClass($apt['status']); ?>">
                                                                <?php echo ucfirst($apt['status']); ?>
                                                            </span>
                                                        </div>
                                                        <div class="col-12 mb-3">
                                                            <strong>Symptoms:</strong><br>
                                                            <?php echo nl2br(htmlspecialchars($apt['symptoms'] ?? 'N/A')); ?>
                                                        </div>
                                                        <div class="col-12 mb-3">
                                                            <strong>Medical History:</strong><br>
                                                            <?php echo nl2br(htmlspecialchars($apt['medical_history'] ?? 'No medical history available')); ?>
                                                        </div>
                                                        <?php if ($apt['notes']): ?>
                                                        <div class="col-12 mb-3">
                                                            <strong>Notes:</strong><br>
                                                            <?php echo nl2br(htmlspecialchars($apt['notes'])); ?>
                                                        </div>
                                                        <?php endif; ?>
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
