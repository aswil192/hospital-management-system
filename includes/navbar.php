<?php
require_once __DIR__ . '/../config/config.php';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
            <i class="fas fa-hospital"></i> <?php echo SITE_NAME; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/about.php">About</a>
                </li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo $_SESSION['user_name'] ?? 'User'; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?php 
                                    echo SITE_URL . '/' . getUserRole() . '/dashboard.php'; 
                                ?>">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/login.php">Login</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle btn btn-light text-primary ms-2" href="#" role="button" data-bs-toggle="dropdown">
                            Register
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?php echo SITE_URL; ?>/register.php">
                                    <i class="fas fa-user me-2"></i>As Patient
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo SITE_URL; ?>/register-doctor.php">
                                    <i class="fas fa-user-md me-2"></i>As Doctor
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
