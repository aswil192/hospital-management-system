<div class="col-md-2 bg-light p-3">
    <div class="mb-4">
        <h6 class="text-muted">ADMIN MENU</h6>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/admin/dashboard.php">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'patients.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/admin/patients.php">
            <i class="fas fa-users me-2"></i> Manage Patients
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'doctors.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/admin/doctors.php">
            <i class="fas fa-user-md me-2"></i> Manage Doctors
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'pending-doctors.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/admin/pending-doctors.php">
            <i class="fas fa-user-clock me-2"></i> Pending Approvals
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'appointments.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/admin/appointments.php">
            <i class="fas fa-calendar-check me-2"></i> Appointments
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'medicines.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/admin/medicines.php">
            <i class="fas fa-pills me-2"></i> Manage Medicines
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'bills.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/admin/bills.php">
            <i class="fas fa-file-invoice-dollar me-2"></i> Bills & Payments
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'salaries.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/admin/salaries.php">
            <i class="fas fa-money-bill-wave me-2"></i> Doctor Salaries
        </a>
        <hr>
        <a class="nav-link text-danger" href="<?php echo SITE_URL; ?>/logout.php">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
    </nav>
</div>
