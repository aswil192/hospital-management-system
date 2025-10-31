<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('patient');

$patient = getPatientByUserId(getUserId());
$patient_id = $patient['id'];

// Get statistics
$totalAppointments = countRecords('appointments', "patient_id = $patient_id");
$pendingAppointments = countRecords('appointments', "patient_id = $patient_id AND status = 'pending'");
$totalBills = countRecords('bills', "patient_id = $patient_id");
$unpaidBills = countRecords('bills', "patient_id = $patient_id AND status = 'unpaid'");

// Get recent appointments
$conn = getDBConnection();
$appointmentsQuery = "SELECT a.*, u.name as doctor_name, d.specialization 
                      FROM appointments a 
                      JOIN doctors d ON a.doctor_id = d.id 
                      JOIN users u ON d.user_id = u.id 
                      WHERE a.patient_id = ? 
                      ORDER BY a.appointment_date DESC, a.appointment_time DESC 
                      LIMIT 5";
$stmt = $conn->prepare($appointmentsQuery);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$recentAppointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get recent bills
$billsQuery = "SELECT b.*, u.name as doctor_name 
               FROM bills b 
               LEFT JOIN doctors d ON b.doctor_id = d.id 
               LEFT JOIN users u ON d.user_id = u.id 
               WHERE b.patient_id = ? 
               ORDER BY b.created_date DESC 
               LIMIT 5";
$stmt = $conn->prepare($billsQuery);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$recentBills = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

closeDBConnection($conn);

$pageTitle = 'Patient Dashboard - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-10">
            <div class="mb-4">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                <p class="text-muted">Patient Dashboard</p>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white">Total Appointments</h6>
                                    <h2 class="text-white mb-0"><?php echo $totalAppointments; ?></h2>
                                </div>
                                <i class="fas fa-calendar-check fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white">Pending Appointments</h6>
                                    <h2 class="text-white mb-0"><?php echo $pendingAppointments; ?></h2>
                                </div>
                                <i class="fas fa-clock fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white">Total Bills</h6>
                                    <h2 class="text-white mb-0"><?php echo $totalBills; ?></h2>
                                </div>
                                <i class="fas fa-file-invoice fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white">Unpaid Bills</h6>
                                    <h2 class="text-white mb-0"><?php echo $unpaidBills; ?></h2>
                                </div>
                                <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Recent Appointments -->
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Appointments</h5>
                                <a href="<?php echo SITE_URL; ?>/patient/appointments.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentAppointments)): ?>
                                <p class="text-muted text-center py-3">No appointments found.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Doctor</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentAppointments as $apt): ?>
                                            <tr>
                                                <td><?php echo formatDate($apt['appointment_date']); ?></td>
                                                <td>
                                                    <small class="d-block"><?php echo htmlspecialchars($apt['doctor_name']); ?></small>
                                                    <small class="text-muted"><?php echo htmlspecialchars($apt['specialization']); ?></small>
                                                </td>
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
                
                <!-- Recent Bills -->
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Bills</h5>
                                <a href="<?php echo SITE_URL; ?>/patient/bills.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentBills)): ?>
                                <p class="text-muted text-center py-3">No bills found.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentBills as $bill): ?>
                                            <tr>
                                                <td><?php echo formatDate($bill['created_date']); ?></td>
                                                <td><?php echo formatCurrency($bill['amount']); ?></td>
                                                <td>
                                                    <span class="badge <?php echo getStatusBadgeClass($bill['status']); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $bill['status'])); ?>
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
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
