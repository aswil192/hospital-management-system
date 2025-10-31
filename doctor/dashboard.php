<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('doctor');

$doctor = getDoctorByUserId(getUserId());
$doctor_id = $doctor['id'];

// Get statistics
$totalAppointments = countRecords('appointments', "doctor_id = $doctor_id");
$todayAppointments = countRecords('appointments', "doctor_id = $doctor_id AND appointment_date = CURDATE()");
$pendingAppointments = countRecords('appointments', "doctor_id = $doctor_id AND status = 'pending'");
$totalBills = countRecords('bills', "doctor_id = $doctor_id");

// Get today's appointments
$conn = getDBConnection();
$appointmentsQuery = "SELECT a.*, u.name as patient_name, p.blood_group 
                      FROM appointments a 
                      JOIN patients p ON a.patient_id = p.id 
                      JOIN users u ON p.user_id = u.id 
                      WHERE a.doctor_id = ? AND a.appointment_date = CURDATE()
                      ORDER BY a.appointment_time ASC";
$stmt = $conn->prepare($appointmentsQuery);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$todayAppts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get upcoming appointments
$upcomingQuery = "SELECT a.*, u.name as patient_name 
                  FROM appointments a 
                  JOIN patients p ON a.patient_id = p.id 
                  JOIN users u ON p.user_id = u.id 
                  WHERE a.doctor_id = ? AND a.appointment_date > CURDATE() AND a.status != 'cancelled'
                  ORDER BY a.appointment_date ASC, a.appointment_time ASC 
                  LIMIT 5";
$stmt = $conn->prepare($upcomingQuery);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$upcomingAppts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

closeDBConnection($conn);

$pageTitle = 'Doctor Dashboard - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>Welcome, Dr. <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                <p class="text-muted"><?php echo htmlspecialchars($doctor['specialization']); ?></p>
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
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white">Today's Appointments</h6>
                                    <h2 class="text-white mb-0"><?php echo $todayAppointments; ?></h2>
                                </div>
                                <i class="fas fa-calendar-day fa-3x opacity-50"></i>
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
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white">Total Bills Created</h6>
                                    <h2 class="text-white mb-0"><?php echo $totalBills; ?></h2>
                                </div>
                                <i class="fas fa-file-invoice fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Today's Appointments -->
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Today's Appointments</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($todayAppts)): ?>
                                <p class="text-muted text-center py-3">No appointments for today.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Time</th>
                                                <th>Patient</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($todayAppts as $apt): ?>
                                            <tr>
                                                <td><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></td>
                                                <td>
                                                    <?php echo htmlspecialchars($apt['patient_name']); ?>
                                                    <?php if ($apt['blood_group']): ?>
                                                        <br><small class="text-muted">Blood: <?php echo htmlspecialchars($apt['blood_group']); ?></small>
                                                    <?php endif; ?>
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
                
                <!-- Upcoming Appointments -->
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Upcoming Appointments</h5>
                                <a href="<?php echo SITE_URL; ?>/doctor/appointments.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($upcomingAppts)): ?>
                                <p class="text-muted text-center py-3">No upcoming appointments.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Patient</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($upcomingAppts as $apt): ?>
                                            <tr>
                                                <td>
                                                    <?php echo formatDate($apt['appointment_date']); ?><br>
                                                    <small class="text-muted"><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($apt['patient_name']); ?></td>
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
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
