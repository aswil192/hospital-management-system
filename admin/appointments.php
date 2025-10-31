<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

// Get all appointments
$conn = getDBConnection();
$query = "SELECT a.*, u1.name as patient_name, u2.name as doctor_name, d.specialization 
          FROM appointments a 
          JOIN patients p ON a.patient_id = p.id 
          JOIN users u1 ON p.user_id = u1.id 
          JOIN doctors d ON a.doctor_id = d.id 
          JOIN users u2 ON d.user_id = u2.id 
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$appointments = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
closeDBConnection($conn);

$pageTitle = 'All Appointments - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>All Appointments</h2>
                <p class="text-muted">View all system appointments</p>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date & Time</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Specialization</th>
                                    <th>Status</th>
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
                                    <td><?php echo htmlspecialchars($apt['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($apt['doctor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($apt['specialization']); ?></td>
                                    <td>
                                        <span class="badge <?php echo getStatusBadgeClass($apt['status']); ?>">
                                            <?php echo ucfirst($apt['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
