<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('doctor');

$doctor = getDoctorByUserId(getUserId());
$doctor_id = $doctor['id'];

// Get salary information
$conn = getDBConnection();
$query = "SELECT * FROM salaries WHERE doctor_id = ? ORDER BY payment_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$salaries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calculate totals
$totalPaid = 0;
$totalPending = 0;
foreach ($salaries as $salary) {
    if ($salary['status'] === 'paid') {
        $totalPaid += $salary['amount'];
    } else {
        $totalPending += $salary['amount'];
    }
}

closeDBConnection($conn);

$pageTitle = 'My Salary - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>My Salary</h2>
                <p class="text-muted">View your salary information and payment history</p>
            </div>
            
            <!-- Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="text-white">Monthly Salary</h6>
                            <h2 class="text-white mb-0"><?php echo formatCurrency($doctor['salary']); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="text-white">Total Paid</h6>
                            <h2 class="text-white mb-0"><?php echo formatCurrency($totalPaid); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="text-white">Pending Payment</h6>
                            <h2 class="text-white mb-0"><?php echo formatCurrency($totalPending); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Salary History -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Payment History</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($salaries)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No salary records found.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Payment Month</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($salaries as $salary): ?>
                                    <tr>
                                        <td>#<?php echo $salary['id']; ?></td>
                                        <td><?php echo htmlspecialchars($salary['payment_month']); ?></td>
                                        <td class="fw-bold"><?php echo formatCurrency($salary['amount']); ?></td>
                                        <td><?php echo formatDateTime($salary['payment_date']); ?></td>
                                        <td>
                                            <span class="badge <?php echo getStatusBadgeClass($salary['status']); ?>">
                                                <?php echo ucfirst($salary['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($salary['notes'] ?? '-'); ?></td>
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
