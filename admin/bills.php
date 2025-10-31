<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

// Get all bills and payments
$conn = getDBConnection();
$query = "SELECT b.*, u1.name as patient_name, u2.name as doctor_name,
          (SELECT SUM(amount) FROM payments WHERE bill_id = b.id AND status = 'completed') as total_paid
          FROM bills b 
          JOIN patients p ON b.patient_id = p.id 
          JOIN users u1 ON p.user_id = u1.id 
          LEFT JOIN doctors d ON b.doctor_id = d.id 
          LEFT JOIN users u2 ON d.user_id = u2.id 
          ORDER BY b.created_date DESC";
$bills = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
closeDBConnection($conn);

$pageTitle = 'Bills & Payments - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>Bills & Payments</h2>
                <p class="text-muted">View all bills and payment information</p>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Bill ID</th>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bills as $bill): ?>
                                <tr>
                                    <td>#<?php echo $bill['id']; ?></td>
                                    <td><?php echo formatDate($bill['created_date']); ?></td>
                                    <td><?php echo htmlspecialchars($bill['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($bill['doctor_name'] ?? 'N/A'); ?></td>
                                    <td class="fw-bold"><?php echo formatCurrency($bill['amount']); ?></td>
                                    <td><?php echo formatCurrency($bill['total_paid'] ?? 0); ?></td>
                                    <td>
                                        <span class="badge <?php echo getStatusBadgeClass($bill['status']); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $bill['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $bill['id']; ?>">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Detail Modal -->
                                <div class="modal fade" id="detailModal<?php echo $bill['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Bill Details #<?php echo $bill['id']; ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Patient:</strong> <?php echo htmlspecialchars($bill['patient_name']); ?></p>
                                                <p><strong>Doctor:</strong> <?php echo htmlspecialchars($bill['doctor_name'] ?? 'N/A'); ?></p>
                                                <p><strong>Bill Amount:</strong> <?php echo formatCurrency($bill['amount']); ?></p>
                                                <p><strong>Amount Paid:</strong> <?php echo formatCurrency($bill['total_paid'] ?? 0); ?></p>
                                                <p><strong>Balance:</strong> <?php echo formatCurrency($bill['amount'] - ($bill['total_paid'] ?? 0)); ?></p>
                                                <p><strong>Status:</strong> 
                                                    <span class="badge <?php echo getStatusBadgeClass($bill['status']); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $bill['status'])); ?>
                                                    </span>
                                                </p>
                                                <p><strong>Created Date:</strong> <?php echo formatDateTime($bill['created_date']); ?></p>
                                                <?php if ($bill['due_date']): ?>
                                                <p><strong>Due Date:</strong> <?php echo formatDate($bill['due_date']); ?></p>
                                                <?php endif; ?>
                                                <?php if ($bill['description']): ?>
                                                <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($bill['description'])); ?></p>
                                                <?php endif; ?>
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
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
