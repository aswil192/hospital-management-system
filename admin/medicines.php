<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

$error = '';
$success = '';

// Handle add medicine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_medicine'])) {
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $manufacturer = sanitizeInput($_POST['manufacturer']);
    $expiry_date = $_POST['expiry_date'];
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO medicines (name, description, price, stock_quantity, manufacturer, expiry_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiss", $name, $description, $price, $stock_quantity, $manufacturer, $expiry_date);
    
    if ($stmt->execute()) {
        $success = 'Medicine added successfully';
        $_POST = [];
    } else {
        $error = 'Failed to add medicine';
    }
    
    $stmt->close();
    closeDBConnection($conn);
}

// Handle update stock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $medicine_id = intval($_POST['medicine_id']);
    $stock_quantity = intval($_POST['stock_quantity']);
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE medicines SET stock_quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $stock_quantity, $medicine_id);
    
    if ($stmt->execute()) {
        setFlashMessage('Stock updated successfully', 'success');
    }
    
    $stmt->close();
    closeDBConnection($conn);
    header('Location: ' . SITE_URL . '/admin/medicines.php');
    exit();
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $medicine_id = intval($_GET['delete']);
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM medicines WHERE id = ?");
    $stmt->bind_param("i", $medicine_id);
    
    if ($stmt->execute()) {
        setFlashMessage('Medicine deleted successfully', 'success');
    }
    
    $stmt->close();
    closeDBConnection($conn);
    header('Location: ' . SITE_URL . '/admin/medicines.php');
    exit();
}

// Get all medicines
$conn = getDBConnection();
$query = "SELECT * FROM medicines ORDER BY name";
$medicines = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
closeDBConnection($conn);

$pageTitle = 'Manage Medicines - ' . SITE_NAME;
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
                        <h2>Manage Medicines</h2>
                        <p class="text-muted">Add and manage medicine inventory</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
                        <i class="fas fa-plus me-2"></i>Add New Medicine
                    </button>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Manufacturer</th>
                                    <th>Expiry Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($medicines as $med): ?>
                                <tr class="<?php echo ($med['stock_quantity'] < 50) ? 'table-warning' : ''; ?>">
                                    <td>#<?php echo $med['id']; ?></td>
                                    <td><?php echo htmlspecialchars($med['name']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($med['description'] ?? '', 0, 30)) . '...'; ?></td>
                                    <td><?php echo formatCurrency($med['price']); ?></td>
                                    <td>
                                        <?php echo $med['stock_quantity']; ?>
                                        <?php if ($med['stock_quantity'] < 50): ?>
                                            <i class="fas fa-exclamation-triangle text-warning" title="Low stock"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($med['manufacturer'] ?? 'N/A'); ?></td>
                                    <td><?php echo $med['expiry_date'] ? formatDate($med['expiry_date']) : 'N/A'; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#updateStockModal<?php echo $med['id']; ?>">
                                            <i class="fas fa-box"></i> Stock
                                        </button>
                                        <a href="?delete=<?php echo $med['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                
                                <!-- Update Stock Modal -->
                                <div class="modal fade" id="updateStockModal<?php echo $med['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Stock: <?php echo htmlspecialchars($med['name']); ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="">
                                                <div class="modal-body">
                                                    <input type="hidden" name="medicine_id" value="<?php echo $med['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Current Stock: <?php echo $med['stock_quantity']; ?></label>
                                                        <input type="number" class="form-control" name="stock_quantity" 
                                                               value="<?php echo $med['stock_quantity']; ?>" min="0" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="update_stock" class="btn btn-success">Update Stock</button>
                                                </div>
                                            </form>
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

<!-- Add Medicine Modal -->
<div class="modal fade" id="addMedicineModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Medicine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Medicine Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Manufacturer</label>
                            <input type="text" class="form-control" name="manufacturer">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price ($) *</label>
                            <input type="number" class="form-control" name="price" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock Quantity *</label>
                            <input type="number" class="form-control" name="stock_quantity" value="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" name="expiry_date">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_medicine" class="btn btn-primary">Add Medicine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
