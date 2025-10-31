<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('doctor');

$doctor = getDoctorByUserId(getUserId());
$doctor_id = $doctor['id'];

// Get completed appointments
$conn = getDBConnection();
$query = "SELECT a.*, u.name as patient_name 
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

$medicines = getAllMedicines();
closeDBConnection($conn);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = intval($_POST['appointment_id']);
    $medicine_ids = $_POST['medicine_id'] ?? [];
    $dosages = $_POST['dosage'] ?? [];
    $frequencies = $_POST['frequency'] ?? [];
    $durations = $_POST['duration'] ?? [];
    $instructions_array = $_POST['instructions'] ?? [];
    
    if (empty($appointment_id) || empty($medicine_ids)) {
        $error = 'Please select appointment and at least one medicine';
    } else {
        $conn = getDBConnection();
        $success_count = 0;
        
        foreach ($medicine_ids as $index => $medicine_id) {
            if (!empty($medicine_id) && !empty($dosages[$index])) {
                $stmt = $conn->prepare("INSERT INTO prescriptions (appointment_id, medicine_id, dosage, frequency, duration, instructions) 
                                        VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iissss", $appointment_id, $medicine_id, $dosages[$index], 
                                  $frequencies[$index], $durations[$index], $instructions_array[$index]);
                if ($stmt->execute()) {
                    $success_count++;
                }
                $stmt->close();
            }
        }
        
        closeDBConnection($conn);
        
        if ($success_count > 0) {
            $success = "Successfully prescribed $success_count medicine(s)";
            $_POST = [];
        } else {
            $error = 'Failed to create prescription. Please try again.';
        }
    }
}

$pageTitle = 'Prescribe Medicine - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>Prescribe Medicine</h2>
                <p class="text-muted">Create prescriptions for patients</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="" id="prescriptionForm">
                        <div class="mb-4">
                            <label for="appointment_id" class="form-label">Select Appointment *</label>
                            <select class="form-select" id="appointment_id" name="appointment_id" required>
                                <option value="">Choose a completed appointment...</option>
                                <?php foreach ($appointments as $apt): ?>
                                    <option value="<?php echo $apt['id']; ?>">
                                        #<?php echo $apt['id']; ?> - <?php echo htmlspecialchars($apt['patient_name']); ?> 
                                        (<?php echo formatDate($apt['appointment_date']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <h5 class="mb-3">Medicines</h5>
                        <div id="medicineRows">
                            <div class="medicine-row border rounded p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Medicine *</label>
                                        <select class="form-select" name="medicine_id[]" required>
                                            <option value="">Select Medicine</option>
                                            <?php foreach ($medicines as $med): ?>
                                                <option value="<?php echo $med['id']; ?>">
                                                    <?php echo htmlspecialchars($med['name']); ?> (Stock: <?php echo $med['stock_quantity']; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">Dosage *</label>
                                        <input type="text" class="form-control" name="dosage[]" placeholder="e.g., 1 tablet" required>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">Frequency</label>
                                        <input type="text" class="form-control" name="frequency[]" placeholder="e.g., Twice daily">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">Duration</label>
                                        <input type="text" class="form-control" name="duration[]" placeholder="e.g., 7 days">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Instructions</label>
                                        <input type="text" class="form-control" name="instructions[]" placeholder="After meals">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-secondary mb-3" id="addMedicineBtn">
                            <i class="fas fa-plus me-2"></i>Add Another Medicine
                        </button>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-prescription me-2"></i>Create Prescription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('addMedicineBtn').addEventListener('click', function() {
    const medicineRows = document.getElementById('medicineRows');
    const newRow = medicineRows.querySelector('.medicine-row').cloneNode(true);
    
    // Clear values
    newRow.querySelectorAll('input').forEach(input => input.value = '');
    newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
    
    // Add remove button
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn btn-danger btn-sm mt-2';
    removeBtn.innerHTML = '<i class="fas fa-times"></i> Remove';
    removeBtn.onclick = function() { this.closest('.medicine-row').remove(); };
    newRow.appendChild(removeBtn);
    
    medicineRows.appendChild(newRow);
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
