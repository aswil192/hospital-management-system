<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('patient');

$doctors = getAllDoctors();
$specializations = getSpecializations();

// Filter by specialization
$filterSpec = $_GET['specialization'] ?? '';
if ($filterSpec) {
    $doctors = array_filter($doctors, function($doctor) use ($filterSpec) {
        return $doctor['specialization'] === $filterSpec;
    });
}

$pageTitle = 'View Doctors - ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="col-md-10">
            <div class="mb-4">
                <h2>Our Doctors</h2>
                <p class="text-muted">Browse our experienced medical professionals</p>
            </div>
            
            <!-- Filter -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="specialization" class="form-label">Filter by Specialization</label>
                                <select class="form-select" id="specialization" name="specialization" onchange="this.form.submit()">
                                    <option value="">All Specializations</option>
                                    <?php foreach ($specializations as $spec): ?>
                                        <option value="<?php echo htmlspecialchars($spec); ?>" <?php echo ($filterSpec === $spec) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($spec); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php if ($filterSpec): ?>
                            <div class="col-md-2">
                                <a href="<?php echo SITE_URL; ?>/patient/doctors.php" class="btn btn-secondary">Clear Filter</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Doctors Grid -->
            <div class="row g-4">
                <?php foreach ($doctors as $doctor): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px;">
                                    <i class="fas fa-user-md fa-3x"></i>
                                </div>
                            </div>
                            <h5 class="card-title text-center"><?php echo htmlspecialchars($doctor['name']); ?></h5>
                            <p class="text-center text-primary mb-3"><?php echo htmlspecialchars($doctor['specialization']); ?></p>
                            
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-graduation-cap text-muted me-2"></i>
                                    <small><?php echo htmlspecialchars($doctor['qualification'] ?? 'MD'); ?></small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-award text-muted me-2"></i>
                                    <small><?php echo $doctor['experience_years']; ?> years experience</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-envelope text-muted me-2"></i>
                                    <small><?php echo htmlspecialchars($doctor['email']); ?></small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-phone text-muted me-2"></i>
                                    <small><?php echo htmlspecialchars($doctor['phone']); ?></small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-circle text-muted me-2"></i>
                                    <small>
                                        <span class="badge <?php echo getStatusBadgeClass($doctor['availability_status']); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $doctor['availability_status'])); ?>
                                        </span>
                                    </small>
                                </li>
                            </ul>
                            
                            <?php if ($doctor['availability_status'] === 'available'): ?>
                                <a href="<?php echo SITE_URL; ?>/patient/book-appointment.php" class="btn btn-primary w-100 mt-3">
                                    <i class="fas fa-calendar-plus me-2"></i>Book Appointment
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100 mt-3" disabled>
                                    <i class="fas fa-times-circle me-2"></i>Not Available
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
