<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Home - ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<!-- Hero Section Start -->
<div class="container-fluid bg-primary py-5 mb-5 hero-header">
    <div class="container py-5">
        <div class="row justify-content-center py-5">
            <div class="col-lg-10 text-center">
                <h1 class="display-3 text-white animated slideInDown">Welcome to Hospital Management System</h1>
                <p class="fs-4 text-white mb-4 pb-2">Providing Quality Healthcare Services with Modern Technology</p>
                <?php if (!isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-light rounded-pill py-3 px-5 me-3 animated slideInLeft">
                        <i class="fas fa-user-plus me-2"></i>Register as Patient
                    </a>
                    <a href="<?php echo SITE_URL; ?>/register-doctor.php" class="btn btn-success rounded-pill py-3 px-5 me-3 animated slideInUp">
                        <i class="fas fa-user-md me-2"></i>Register as Doctor
                    </a>
                    <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-outline-light rounded-pill py-3 px-5 animated slideInRight">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL . '/' . getUserRole() . '/dashboard.php'; ?>" class="btn btn-light rounded-pill py-3 px-5 animated slideInUp">
                        <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- Hero Section End -->

<!-- Features Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="mb-3">Our Features</h1>
            <p>Experience modern healthcare management with our comprehensive system</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-calendar-check fa-3x text-primary"></i>
                        </div>
                        <h5 class="mb-3">Easy Appointment Booking</h5>
                        <p class="text-muted">Book appointments with your preferred doctors quickly and easily through our online system.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-user-md fa-3x text-primary"></i>
                        </div>
                        <h5 class="mb-3">Expert Doctors</h5>
                        <p class="text-muted">Access to highly qualified doctors with various specializations available 24/7.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-file-invoice-dollar fa-3x text-primary"></i>
                        </div>
                        <h5 class="mb-3">Online Billing & Payment</h5>
                        <p class="text-muted">View and pay your medical bills securely online with multiple payment options.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-pills fa-3x text-primary"></i>
                        </div>
                        <h5 class="mb-3">Medicine Prescription</h5>
                        <p class="text-muted">Get digital prescriptions from doctors with detailed dosage and instructions.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-history fa-3x text-primary"></i>
                        </div>
                        <h5 class="mb-3">Medical History</h5>
                        <p class="text-muted">Keep track of your complete medical history and past appointments in one place.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="mb-3">Secure & Private</h5>
                        <p class="text-muted">Your medical data is protected with industry-standard security measures.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Features End -->

<!-- Services Start -->
<div class="container-xxl py-5 bg-light">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="mb-3">Our Services</h1>
            <p>Comprehensive healthcare services for all your medical needs</p>
        </div>
        <div class="row g-4">
            <?php
            $services = [
                ['icon' => 'fa-heartbeat', 'title' => 'Cardiology', 'desc' => 'Expert heart care and cardiovascular treatment'],
                ['icon' => 'fa-baby', 'title' => 'Pediatrics', 'desc' => 'Comprehensive care for infants, children, and adolescents'],
                ['icon' => 'fa-bone', 'title' => 'Orthopedics', 'desc' => 'Treatment for bone, joint, and muscle conditions'],
                ['icon' => 'fa-brain', 'title' => 'Neurology', 'desc' => 'Diagnosis and treatment of nervous system disorders'],
                ['icon' => 'fa-lungs', 'title' => 'Pulmonology', 'desc' => 'Respiratory system and lung disease treatment'],
                ['icon' => 'fa-eye', 'title' => 'Ophthalmology', 'desc' => 'Complete eye care and vision treatment services']
            ];
            
            foreach ($services as $index => $service):
            ?>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="<?php echo ($index * 0.2); ?>s">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="fas <?php echo $service['icon']; ?> fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0"><?php echo $service['title']; ?></h5>
                            </div>
                        </div>
                        <p class="text-muted mb-0"><?php echo $service['desc']; ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- Services End -->

<!-- Statistics Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="card bg-primary text-white text-center p-4">
                    <i class="fas fa-user-injured fa-3x mb-3"></i>
                    <h2 class="text-white mb-2"><?php echo countRecords('patients'); ?>+</h2>
                    <p class="mb-0">Patients</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="card bg-success text-white text-center p-4">
                    <i class="fas fa-user-md fa-3x mb-3"></i>
                    <h2 class="text-white mb-2"><?php echo countRecords('doctors'); ?>+</h2>
                    <p class="mb-0">Doctors</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="card bg-info text-white text-center p-4">
                    <i class="fas fa-calendar-check fa-3x mb-3"></i>
                    <h2 class="text-white mb-2"><?php echo countRecords('appointments'); ?>+</h2>
                    <p class="mb-0">Appointments</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                <div class="card bg-warning text-white text-center p-4">
                    <i class="fas fa-pills fa-3x mb-3"></i>
                    <h2 class="text-white mb-2"><?php echo countRecords('medicines'); ?>+</h2>
                    <p class="mb-0">Medicines</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Statistics End -->

<!-- Call to Action Start -->
<div class="container-fluid bg-primary py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 wow fadeInLeft" data-wow-delay="0.1s">
                <h2 class="text-white mb-3">Ready to Get Started?</h2>
                <p class="text-white mb-lg-0">Register now as a patient to book appointments, or join our team as a healthcare professional.</p>
            </div>
            <div class="col-lg-4 text-lg-end wow fadeInRight" data-wow-delay="0.3s">
                <?php if (!isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-light rounded-pill py-3 px-4 mb-2 me-2">
                        <i class="fas fa-user-plus me-2"></i>Patient
                    </a>
                    <a href="<?php echo SITE_URL; ?>/register-doctor.php" class="btn btn-success rounded-pill py-3 px-4 mb-2">
                        <i class="fas fa-user-md me-2"></i>Doctor
                    </a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL . '/' . getUserRole() . '/dashboard.php'; ?>" class="btn btn-light rounded-pill py-3 px-5">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- Call to Action End -->

<style>
.hero-header {
    background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('img/carousel-1.png');
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}

.card {
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-10px);
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
