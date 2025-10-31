<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'About Us - ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<!-- Page Header Start -->
<div class="container-fluid bg-primary py-5 mb-5">
    <div class="container py-5">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-4 text-white animated slideInDown">About Us</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a class="text-white" href="<?php echo SITE_URL; ?>/index.php">Home</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">About</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- Page Header End -->

<!-- About Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="position-relative overflow-hidden h-100" style="min-height: 400px;">
                    <img class="position-absolute w-100 h-100" src="<?php echo SITE_URL; ?>/img/about-team.jpg" alt="About Us" style="object-fit: cover;">
                </div>
            </div>
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                <h6 class="text-primary text-uppercase mb-2">About Us</h6>
                <h1 class="mb-4">Welcome to Hospital Management System</h1>
                <p class="mb-4">Our Hospital Management System is a comprehensive platform designed to streamline healthcare operations and improve patient care. We provide cutting-edge technology solutions for hospitals, clinics, and healthcare facilities.</p>
                <p class="mb-4">With our system, healthcare providers can efficiently manage appointments, patient records, billing, prescriptions, and much more. We're committed to making healthcare management easier, more efficient, and more accessible.</p>
                
                <div class="row g-3">
                    <div class="col-sm-6">
                        <h6 class="mb-3"><i class="fa fa-check text-primary me-2"></i>24/7 Support</h6>
                        <h6 class="mb-3"><i class="fa fa-check text-primary me-2"></i>Professional Doctors</h6>
                        <h6 class="mb-3"><i class="fa fa-check text-primary me-2"></i>Modern Equipment</h6>
                    </div>
                    <div class="col-sm-6">
                        <h6 class="mb-3"><i class="fa fa-check text-primary me-2"></i>Online Appointments</h6>
                        <h6 class="mb-3"><i class="fa fa-check text-primary me-2"></i>Secure Records</h6>
                        <h6 class="mb-3"><i class="fa fa-check text-primary me-2"></i>Easy Billing</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- About End -->

<!-- Mission & Vision Start -->
<div class="container-xxl py-5 bg-light">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="mb-3">Our Mission & Vision</h1>
            <p>Committed to excellence in healthcare management</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-bullseye fa-2x"></i>
                            </div>
                            <h3 class="ms-3 mb-0">Our Mission</h3>
                        </div>
                        <p class="mb-0">To provide innovative and efficient healthcare management solutions that enhance the quality of patient care, streamline hospital operations, and empower healthcare professionals with the tools they need to deliver exceptional medical services.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-eye fa-2x"></i>
                            </div>
                            <h3 class="ms-3 mb-0">Our Vision</h3>
                        </div>
                        <p class="mb-0">To become the leading healthcare management platform globally, revolutionizing the way hospitals and clinics operate through cutting-edge technology, ensuring accessible, efficient, and high-quality healthcare for all.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Mission & Vision End -->

<!-- Services Overview Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="mb-3">Our Services</h1>
            <p>Comprehensive healthcare management solutions</p>
        </div>
        <div class="row g-4">
            <?php
            $services = [
                ['icon' => 'fa-heartbeat', 'title' => 'Cardiology', 'desc' => 'Expert heart care and cardiovascular treatment with state-of-the-art equipment and experienced cardiologists.'],
                ['icon' => 'fa-baby', 'title' => 'Pediatrics', 'desc' => 'Comprehensive care for infants, children, and adolescents with specialized pediatric facilities.'],
                ['icon' => 'fa-bone', 'title' => 'Orthopedics', 'desc' => 'Treatment for bone, joint, and muscle conditions with advanced surgical and non-surgical options.'],
                ['icon' => 'fa-brain', 'title' => 'Neurology', 'desc' => 'Diagnosis and treatment of nervous system disorders with cutting-edge neurological technology.'],
                ['icon' => 'fa-lungs', 'title' => 'Pulmonology', 'desc' => 'Respiratory system and lung disease treatment with comprehensive pulmonary care services.'],
                ['icon' => 'fa-eye', 'title' => 'Ophthalmology', 'desc' => 'Complete eye care and vision treatment services including surgery and corrective procedures.'],
                ['icon' => 'fa-tooth', 'title' => 'Dentistry', 'desc' => 'Full dental care services including preventive, restorative, and cosmetic dentistry.'],
                ['icon' => 'fa-user-md', 'title' => 'General Medicine', 'desc' => 'Primary healthcare services for diagnosis and treatment of common medical conditions.']
            ];
            
            foreach ($services as $index => $service):
            ?>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="<?php echo ($index * 0.1); ?>s">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas <?php echo $service['icon']; ?> fa-2x text-primary"></i>
                        </div>
                        <h5 class="mb-3"><?php echo $service['title']; ?></h5>
                        <p class="text-muted mb-0"><?php echo $service['desc']; ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- Services Overview End -->

<!-- Why Choose Us Start -->
<div class="container-xxl py-5 bg-light">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="mb-3">Why Choose Us</h1>
            <p>Excellence in healthcare management</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="text-center mb-3">Secure & Reliable</h5>
                        <p class="text-muted text-center mb-0">Your medical data is protected with industry-standard security measures and encryption.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <i class="fas fa-clock fa-3x text-primary"></i>
                        </div>
                        <h5 class="text-center mb-3">24/7 Availability</h5>
                        <p class="text-muted text-center mb-0">Access our system anytime, anywhere with round-the-clock support and services.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <i class="fas fa-users fa-3x text-primary"></i>
                        </div>
                        <h5 class="text-center mb-3">Expert Team</h5>
                        <p class="text-muted text-center mb-0">Highly qualified doctors and medical professionals dedicated to your health.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <i class="fas fa-laptop-medical fa-3x text-primary"></i>
                        </div>
                        <h5 class="text-center mb-3">Modern Technology</h5>
                        <p class="text-muted text-center mb-0">Latest medical equipment and advanced healthcare management technology.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <i class="fas fa-dollar-sign fa-3x text-primary"></i>
                        </div>
                        <h5 class="text-center mb-3">Affordable Care</h5>
                        <p class="text-muted text-center mb-0">Quality healthcare services at competitive prices with flexible payment options.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <i class="fas fa-award fa-3x text-primary"></i>
                        </div>
                        <h5 class="text-center mb-3">Quality Assurance</h5>
                        <p class="text-muted text-center mb-0">Certified and accredited healthcare services meeting international standards.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Why Choose Us End -->

<!-- Statistics Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="card bg-primary text-white text-center p-4">
                    <i class="fas fa-user-injured fa-3x mb-3"></i>
                    <h2 class="text-white mb-2"><?php echo countRecords('patients'); ?>+</h2>
                    <p class="mb-0">Happy Patients</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="card bg-success text-white text-center p-4">
                    <i class="fas fa-user-md fa-3x mb-3"></i>
                    <h2 class="text-white mb-2"><?php echo countRecords('doctors'); ?>+</h2>
                    <p class="mb-0">Expert Doctors</p>
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
                    <p class="mb-0">Medicines Available</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Statistics End -->

<?php include __DIR__ . '/includes/footer.php'; ?>
