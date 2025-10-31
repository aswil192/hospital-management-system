<div class="col-md-2 bg-light p-3">
    <div class="mb-4">
        <h6 class="text-muted">MENU</h6>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/doctor/dashboard.php">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'appointments.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/doctor/appointments.php">
            <i class="fas fa-calendar-check me-2"></i> My Appointments
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'create-bill.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/doctor/create-bill.php">
            <i class="fas fa-file-invoice-dollar me-2"></i> Create Bill
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'prescribe.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/doctor/prescribe.php">
            <i class="fas fa-pills me-2"></i> Prescribe Medicine
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'salary.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/doctor/salary.php">
            <i class="fas fa-money-bill-wave me-2"></i> My Salary
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="<?php echo SITE_URL; ?>/doctor/profile.php">
            <i class="fas fa-user-circle me-2"></i> My Profile
        </a>
        <hr>
        <a class="nav-link text-danger" href="<?php echo SITE_URL; ?>/logout.php">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
    </nav>
</div>
