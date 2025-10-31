<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = '403 Forbidden - ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="py-5">
                <i class="fas fa-ban fa-5x text-danger mb-4"></i>
                <h1 class="display-1 fw-bold">403</h1>
                <h2 class="mb-4">Access Forbidden</h2>
                <p class="text-muted mb-4">You don't have permission to access this page.</p>
                <a href="<?php echo SITE_URL; ?>/index.php" class="btn btn-primary rounded-pill py-3 px-5">
                    <i class="fas fa-home me-2"></i>Go Home
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
