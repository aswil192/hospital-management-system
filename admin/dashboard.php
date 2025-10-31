<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

// Get statistics
$totalPatients = countRecords('patients');
$totalDoctors = countRecords('doctors');
$pendingDoctors = countRecords('users', "role = 'doctor' AND status = 'inactive'");
$totalAppointments = countRecords('appointments');
$todayAppointments = countRecords('appointments', "appointment_date = CURDATE()");
$totalBills = countRecords('bills');
$unpaidBills = countRecords('bills', "status = 'unpaid'");
$totalMedicines = countRecords('medicines');

// Calculate financial statistics
$conn = getDBConnection();

// Total revenue
$revenueQuery = "SELECT SUM(amount) as total FROM payments WHERE status = 'completed'";
$revenueResult = $conn->query($revenueQuery);
$totalRevenue = $revenueResult->fetch_assoc()['total'] ?? 0;

// Pending payments
$pendingQuery = "SELECT SUM(amount) as total FROM bills WHERE status IN ('unpaid', 'partially_paid')";
$pendingResult = $conn->query($pendingQuery);
$pendingPayments = $pendingResult->fetch_assoc()['total'] ?? 0;

// Recent appointments
$recentApptQuery = "SELECT a.*, u1.name as patient_name, u2.name as doctor_name 
                    FROM appointments a 
                    JOIN patients p ON a.patient_id = p.id 
                    JOIN users u1 ON p.user_id = u1.id 
                    JOIN doctors d ON a.doctor_id = d.id 
                    JOIN users u2 ON d.user_id = u2.id 
                    ORDER BY a.created_at DESC LIMIT 5";
$recentAppts = $conn->query($recentApptQuery)->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);

$pageTitle = 'Admin Dashboard - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>Admin Dashboard</h2>
                <p class="text-muted">System Overview and Statistics</p>
                <?php if ($pendingDoctors > 0): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong><?php echo $pendingDoctors; ?></strong> doctor registration(s) pending approval. 
                        <a href="<?php echo SITE_URL; ?>/admin/pending-doctors.php" class="alert-link">Review now</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Main Statistics -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white">Total Patients</h6>
                                    <h2 class="text-white mb-0"><?php echo $totalPatients; ?></h2>
                                </div>
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-white bg-opacity-10">
                            <a href="<?php echo SITE_URL; ?>/admin/patients.php" class="text-white text-decoration-none">
                                View Details <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white">Total Doctors</h6>
                                    <h2 class="text-white mb-0"><?php echo $totalDoctors; ?></h2>
                                </div>
                                <i class="fas fa-user-md fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-white bg-opacity-10">
                            <a href="<?php echo SITE_URL; ?>/admin/doctors.php" class="text-white text-decoration-none">
                                View Details <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white">Total Appointments</h6>
                                    <h2 class="text-white mb-0"><?php echo $totalAppointments; ?></h2>
                                </div>
                                <i class="fas fa-calendar-check fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-white bg-opacity-10">
                            <small class="text-white">Today: <?php echo $todayAppointments; ?></small>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white">Total Medicines</h6>
                                    <h2 class="text-white mb-0"><?php echo $totalMedicines; ?></h2>
                                </div>
                                <i class="fas fa-pills fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-white bg-opacity-10">
                            <a href="<?php echo SITE_URL; ?>/admin/medicines.php" class="text-white text-decoration-none">
                                Manage Stock <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Financial Statistics -->
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="text-primary">Total Revenue</h6>
                            <h3><?php echo formatCurrency($totalRevenue); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-danger">
                        <div class="card-body">
                            <h6 class="text-danger">Pending Payments</h6>
                            <h3><?php echo formatCurrency($pendingPayments); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-warning">
                        <div class="card-body">
                            <h6 class="text-warning">Unpaid Bills</h6>
                            <h3><?php echo $unpaidBills; ?> Bills</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Appointments -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Appointments</h5>
                        <a href="<?php echo SITE_URL; ?>/admin/appointments.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($recentAppts)): ?>
                        <p class="text-muted text-center py-3">No appointments found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentAppts as $apt): ?>
                                    <tr>
                                        <td>#<?php echo $apt['id']; ?></td>
                                        <td><?php echo htmlspecialchars($apt['patient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($apt['doctor_name']); ?></td>
                                        <td><?php echo formatDate($apt['appointment_date']); ?></td>
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
