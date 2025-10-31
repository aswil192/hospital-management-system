<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('patient');

$patient = getPatientByUserId(getUserId());
$patient_id = $patient['id'];

$doctors = getAvailableDoctors();
$specializations = getSpecializations();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = intval($_POST['doctor_id']);
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $symptoms = sanitizeInput($_POST['symptoms']);
    
    if (empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
        $error = 'Please fill in all required fields';
    } elseif (strtotime($appointment_date) < strtotime(date('Y-m-d'))) {
        $error = 'Appointment date cannot be in the past';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, symptoms, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iisss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $symptoms);
        
        if ($stmt->execute()) {
            $success = 'Appointment booked successfully! Our team will confirm it soon.';
            $_POST = [];
        } else {
            $error = 'Failed to book appointment. Please try again.';
        }
        
        $stmt->close();
        closeDBConnection($conn);
    }
}

$pageTitle = 'Book Appointment - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>Book Appointment</h2>
                <p class="text-muted">Schedule an appointment with our doctors</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                    <a href="<?php echo SITE_URL; ?>/patient/appointments.php" class="alert-link">View Appointments</a>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <form method="POST" action="" id="appointmentForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="specialization" class="form-label">Specialization (Optional)</label>
                                        <select class="form-select" id="specialization">
                                            <option value="">All Specializations</option>
                                            <?php foreach ($specializations as $spec): ?>
                                                <option value="<?php echo htmlspecialchars($spec); ?>"><?php echo htmlspecialchars($spec); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="doctor_id" class="form-label">Select Doctor *</label>
                                        <select class="form-select" id="doctor_id" name="doctor_id" required>
                                            <option value="">Choose a doctor...</option>
                                            <?php foreach ($doctors as $doctor): ?>
                                                <option value="<?php echo $doctor['id']; ?>" 
                                                        data-specialization="<?php echo htmlspecialchars($doctor['specialization']); ?>"
                                                        <?php echo (isset($_POST['doctor_id']) && $_POST['doctor_id'] == $doctor['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($doctor['name']) . ' - ' . htmlspecialchars($doctor['specialization']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="appointment_date" class="form-label">Appointment Date *</label>
                                        <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                                               min="<?php echo date('Y-m-d'); ?>" 
                                               value="<?php echo htmlspecialchars($_POST['appointment_date'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="appointment_time" class="form-label">Appointment Time *</label>
                                        <input type="time" class="form-control" id="appointment_time" name="appointment_time" 
                                               value="<?php echo htmlspecialchars($_POST['appointment_time'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="symptoms" class="form-label">Symptoms / Reason for Visit</label>
                                    <textarea class="form-control" id="symptoms" name="symptoms" rows="4" 
                                              placeholder="Describe your symptoms or reason for appointment..."><?php echo htmlspecialchars($_POST['symptoms'] ?? ''); ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-calendar-plus me-2"></i>Book Appointment
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card shadow-sm bg-light">
                        <div class="card-body">
                            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Important Information</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Appointments are subject to doctor availability</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>You will receive confirmation via email</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Please arrive 15 minutes early</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Bring your medical records if any</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Cancellations should be made 24 hours in advance</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('specialization').addEventListener('change', function() {
    const selectedSpec = this.value;
    const doctorSelect = document.getElementById('doctor_id');
    const options = doctorSelect.getElementsByTagName('option');
    
    for (let i = 1; i < options.length; i++) {
        const option = options[i];
        if (selectedSpec === '' || option.getAttribute('data-specialization') === selectedSpec) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    }
    
    doctorSelect.value = '';
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
