<div class="col-md-2 bg-light p-3">
    <div class="mb-4">
        <h6 class="text-muted">MENU</h6>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/patient/dashboard.php">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'book-appointment.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/patient/book-appointment.php">
            <i class="fas fa-calendar-plus me-2"></i> Book Appointment
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'appointments.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/patient/appointments.php">
            <i class="fas fa-calendar-check me-2"></i> My Appointments
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'doctors.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/patient/doctors.php">
            <i class="fas fa-user-md me-2"></i> View Doctors
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'bills.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/patient/bills.php">
            <i class="fas fa-file-invoice-dollar me-2"></i> My Bills
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/patient/profile.php">
            <i class="fas fa-user-circle me-2"></i> My Profile
        </a>
        <hr>
        <a class="nav-link text-danger" href="<?php echo SITE_URL; ?>/logout.php">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
    </nav>
</div>
