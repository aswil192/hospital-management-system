<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('doctor');

$doctor = getDoctorByUserId(getUserId());
$doctor_id = $doctor['id'];

// Get completed appointments for this doctor
$conn = getDBConnection();
$query = "SELECT a.*, u.name as patient_name, p.id as patient_id 
          FROM appointments a 
          JOIN patients p ON a.patient_id = p.id 
          JOIN users u ON p.user_id = u.id 
          WHERE a.doctor_id = ? AND a.status = 'completed'
          ORDER BY a.appointment_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
closeDBConnection($conn);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = intval($_POST['appointment_id']);
    $patient_id = intval($_POST['patient_id']);
    $amount = floatval($_POST['amount']);
    $description = sanitizeInput($_POST['description']);
    $due_date = $_POST['due_date'];
    
    if (empty($patient_id) || $amount <= 0) {
        $error = 'Please fill in all required fields with valid values';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO bills (patient_id, doctor_id, appointment_id, amount, description, status, due_date) 
                                VALUES (?, ?, ?, ?, ?, 'unpaid', ?)");
        $stmt->bind_param("iiidss", $patient_id, $doctor_id, $appointment_id, $amount, $description, $due_date);
        
        if ($stmt->execute()) {
            $success = 'Bill created successfully!';
            $_POST = [];
        } else {
            $error = 'Failed to create bill. Please try again.';
        }
        
        $stmt->close();
        closeDBConnection($conn);
    }
}

$pageTitle = 'Create Bill - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>Create Patient Bill</h2>
                <p class="text-muted">Generate billing for completed appointments</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <form method="POST" action="" id="billForm">
                                <div class="mb-3">
                                    <label for="appointment_id" class="form-label">Select Appointment *</label>
                                    <select class="form-select" id="appointment_id" name="appointment_id" required>
                                        <option value="">Choose an appointment...</option>
                                        <?php foreach ($appointments as $apt): ?>
                                            <option value="<?php echo $apt['id']; ?>" 
                                                    data-patient-id="<?php echo $apt['patient_id']; ?>"
                                                    data-patient-name="<?php echo htmlspecialchars($apt['patient_name']); ?>"
                                                    data-date="<?php echo formatDate($apt['appointment_date']); ?>">
                                                #<?php echo $apt['id']; ?> - <?php echo htmlspecialchars($apt['patient_name']); ?> 
                                                (<?php echo formatDate($apt['appointment_date']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <input type="hidden" id="patient_id" name="patient_id" value="">
                                
                                <div class="mb-3">
                                    <label class="form-label">Patient Name</label>
                                    <input type="text" class="form-control" id="patient_name_display" readonly>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="amount" class="form-label">Bill Amount ($) *</label>
                                        <input type="number" class="form-control" id="amount" name="amount" 
                                               step="0.01" min="0" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="due_date" class="form-label">Due Date</label>
                                        <input type="date" class="form-control" id="due_date" name="due_date" 
                                               min="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description / Services Provided *</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" 
                                              placeholder="Consultation fee, medications, procedures, etc." required></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-file-invoice me-2"></i>Create Bill
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card shadow-sm bg-light">
                        <div class="card-body">
                            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Billing Guidelines</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Select completed appointment</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Enter accurate billing amount</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Provide detailed description</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Set appropriate due date</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Patient will be notified</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('appointment_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const patientId = selectedOption.getAttribute('data-patient-id');
    const patientName = selectedOption.getAttribute('data-patient-name');
    
    document.getElementById('patient_id').value = patientId || '';
    document.getElementById('patient_name_display').value = patientName || '';
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
