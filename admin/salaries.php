<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

$error = '';
$success = '';

// Handle pay salary
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_salary'])) {
    $doctor_id = intval($_POST['doctor_id']);
    $amount = floatval($_POST['amount']);
    $payment_month = sanitizeInput($_POST['payment_month']);
    $notes = sanitizeInput($_POST['notes']);
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO salaries (doctor_id, amount, payment_month, status, notes) VALUES (?, ?, ?, 'paid', ?)");
    $stmt->bind_param("idss", $doctor_id, $amount, $payment_month, $notes);
    
    if ($stmt->execute()) {
        $success = 'Salary payment recorded successfully';
        $_POST = [];
    } else {
        $error = 'Failed to record salary payment';
    }
    
    $stmt->close();
    closeDBConnection($conn);
}

// Get all doctors with salary info
$conn = getDBConnection();
$doctorsQuery = "SELECT d.*, u.name, u.email 
                 FROM doctors d 
                 JOIN users u ON d.user_id = u.id 
                 WHERE u.status = 'active'
                 ORDER BY u.name";
$doctors = $conn->query($doctorsQuery)->fetch_all(MYSQLI_ASSOC);

// Get all salary records
$salariesQuery = "SELECT s.*, u.name as doctor_name 
                  FROM salaries s 
                  JOIN doctors d ON s.doctor_id = d.id 
                  JOIN users u ON d.user_id = u.id 
                  ORDER BY s.payment_date DESC";
$salaries = $conn->query($salariesQuery)->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);

$pageTitle = 'Doctor Salaries - ' . SITE_NAME;
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
                        <h2>Doctor Salaries</h2>
                        <p class="text-muted">Manage doctor salary payments</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paySalaryModal">
                        <i class="fas fa-money-bill-wave me-2"></i>Pay Salary
                    </button>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <!-- Doctors Salary Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Doctor Salary Information</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Doctor Name</th>
                                    <th>Monthly Salary</th>
                                    <th>Specialization</th>
                                    <th>Join Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($doctors as $doctor): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                                    <td class="fw-bold"><?php echo formatCurrency($doctor['salary']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                                    <td><?php echo formatDate($doctor['join_date']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Salary Payment History -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Payment History</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($salaries)): ?>
                        <p class="text-muted text-center py-3">No salary payments recorded yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Doctor</th>
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
                                        <td><?php echo htmlspecialchars($salary['doctor_name']); ?></td>
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

<!-- Pay Salary Modal -->
<div class="modal fade" id="paySalaryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pay Doctor Salary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Doctor *</label>
                        <select class="form-select" id="doctor_select" name="doctor_id" required>
                            <option value="">Choose a doctor...</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?php echo $doctor['id']; ?>" data-salary="<?php echo $doctor['salary']; ?>">
                                    <?php echo htmlspecialchars($doctor['name']); ?> - <?php echo formatCurrency($doctor['salary']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Amount ($) *</label>
                        <input type="number" class="form-control" id="salary_amount" name="amount" step="0.01" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Payment Month *</label>
                        <input type="month" class="form-control" name="payment_month" 
                               value="<?php echo date('Y-m'); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2" 
                                  placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="pay_salary" class="btn btn-primary">Process Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('doctor_select').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const salary = selectedOption.getAttribute('data-salary');
    document.getElementById('salary_amount').value = salary || '';
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
