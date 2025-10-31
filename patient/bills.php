<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('patient');

$patient = getPatientByUserId(getUserId());
$patient_id = $patient['id'];

// Handle payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_bill'])) {
    $bill_id = intval($_POST['bill_id']);
    $payment_method = sanitizeInput($_POST['payment_method']);
    $amount = floatval($_POST['amount']);
    
    $conn = getDBConnection();
    $conn->begin_transaction();
    
    try {
        // Insert payment
        $stmt = $conn->prepare("INSERT INTO payments (bill_id, amount, payment_method, status) VALUES (?, ?, ?, 'completed')");
        $stmt->bind_param("ids", $bill_id, $amount, $payment_method);
        $stmt->execute();
        $stmt->close();
        
        // Calculate total paid for this bill
        $paidStmt = $conn->prepare("SELECT SUM(amount) as total_paid FROM payments WHERE bill_id = ? AND status = 'completed'");
        $paidStmt->bind_param("i", $bill_id);
        $paidStmt->execute();
        $paidResult = $paidStmt->get_result()->fetch_assoc();
        $total_paid = $paidResult['total_paid'];
        $paidStmt->close();
        
        // Get bill amount
        $billStmt = $conn->prepare("SELECT amount FROM bills WHERE id = ?");
        $billStmt->bind_param("i", $bill_id);
        $billStmt->execute();
        $billResult = $billStmt->get_result()->fetch_assoc();
        $bill_amount = $billResult['amount'];
        $billStmt->close();
        
        // Update bill status
        $status = 'unpaid';
        if ($total_paid >= $bill_amount) {
            $status = 'paid';
        } elseif ($total_paid > 0) {
            $status = 'partially_paid';
        }
        
        $updateStmt = $conn->prepare("UPDATE bills SET status = ? WHERE id = ?");
        $updateStmt->bind_param("si", $status, $bill_id);
        $updateStmt->execute();
        $updateStmt->close();
        
        $conn->commit();
        setFlashMessage('Payment processed successfully', 'success');
    } catch (Exception $e) {
        $conn->rollback();
        setFlashMessage('Payment failed. Please try again.', 'danger');
    }
    
    closeDBConnection($conn);
    header('Location: ' . SITE_URL . '/patient/bills.php');
    exit();
}

// Get all bills
$conn = getDBConnection();
$query = "SELECT b.*, u.name as doctor_name 
          FROM bills b 
          LEFT JOIN doctors d ON b.doctor_id = d.id 
          LEFT JOIN users u ON d.user_id = u.id 
          WHERE b.patient_id = ? 
          ORDER BY b.created_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$bills = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
closeDBConnection($conn);

$pageTitle = 'My Bills - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>My Bills</h2>
                <p class="text-muted">View and pay your medical bills</p>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($bills)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No bills found.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Bill ID</th>
                                        <th>Date</th>
                                        <th>Doctor</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bills as $bill): ?>
                                    <tr>
                                        <td>#<?php echo $bill['id']; ?></td>
                                        <td><?php echo formatDate($bill['created_date']); ?></td>
                                        <td><?php echo htmlspecialchars($bill['doctor_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($bill['description'] ?? 'Medical services'); ?></td>
                                        <td class="fw-bold"><?php echo formatCurrency($bill['amount']); ?></td>
                                        <td>
                                            <span class="badge <?php echo getStatusBadgeClass($bill['status']); ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $bill['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($bill['status'] !== 'paid'): ?>
                                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#payModal<?php echo $bill['id']; ?>">
                                                    <i class="fas fa-credit-card"></i> Pay Now
                                                </button>
                                            <?php else: ?>
                                                <span class="text-success"><i class="fas fa-check-circle"></i> Paid</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    
                                    <!-- Payment Modal -->
                                    <div class="modal fade" id="payModal<?php echo $bill['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Pay Bill #<?php echo $bill['id']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="bill_id" value="<?php echo $bill['id']; ?>">
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Bill Amount</label>
                                                            <input type="text" class="form-control" value="<?php echo formatCurrency($bill['amount']); ?>" readonly>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label for="amount<?php echo $bill['id']; ?>" class="form-label">Payment Amount *</label>
                                                            <input type="number" class="form-control" id="amount<?php echo $bill['id']; ?>" name="amount" 
                                                                   step="0.01" max="<?php echo $bill['amount']; ?>" value="<?php echo $bill['amount']; ?>" required>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label for="payment_method<?php echo $bill['id']; ?>" class="form-label">Payment Method *</label>
                                                            <select class="form-select" id="payment_method<?php echo $bill['id']; ?>" name="payment_method" required>
                                                                <option value="">Select Payment Method</option>
                                                                <option value="cash">Cash</option>
                                                                <option value="card">Credit/Debit Card</option>
                                                                <option value="online">Online Payment</option>
                                                                <option value="insurance">Insurance</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="pay_bill" class="btn btn-success">
                                                            <i class="fas fa-check"></i> Confirm Payment
                                                        </button>
                                                    </div>
                                                </form>
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
