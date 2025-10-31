<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('patient');

$patient = getPatientByUserId(getUserId());
$patient_id = $patient['id'];

// Handle appointment cancellation
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $appointment_id = intval($_GET['cancel']);
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND patient_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $appointment_id, $patient_id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        setFlashMessage('Appointment cancelled successfully', 'success');
    } else {
        setFlashMessage('Unable to cancel appointment', 'danger');
    }
    $stmt->close();
    closeDBConnection($conn);
    header('Location: ' . SITE_URL . '/patient/appointments.php');
    exit();
}

// Get all appointments
$conn = getDBConnection();
$query = "SELECT a.*, u.name as doctor_name, d.specialization 
          FROM appointments a 
          JOIN doctors d ON a.doctor_id = d.id 
          JOIN users u ON d.user_id = u.id 
          WHERE a.patient_id = ? 
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2>My Appointments</h2>
                        <p class="text-muted">View and manage your appointments</p>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/patient/book-appointment.php" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i>Book New Appointment
                    </a>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($appointments)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No appointments found.</p>
                            <a href="<?php echo SITE_URL; ?>/patient/book-appointment.php" class="btn btn-primary">Book Your First Appointment</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date & Time</th>
                                        <th>Doctor</th>
                                        <th>Specialization</th>
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
                                        <td><?php echo htmlspecialchars($apt['doctor_name']); ?></td>
                                        <td><?php echo htmlspecialchars($apt['specialization']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($apt['symptoms'] ?? 'N/A', 0, 50)) . (strlen($apt['symptoms'] ?? '') > 50 ? '...' : ''); ?></td>
                                        <td>
                                            <span class="badge <?php echo getStatusBadgeClass($apt['status']); ?>">
                                                <?php echo ucfirst($apt['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($apt['status'] === 'pending'): ?>
                                                <a href="?cancel=<?php echo $apt['id']; ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                    <i class="fas fa-times"></i> Cancel
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
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
