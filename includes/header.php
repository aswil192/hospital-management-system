<?php
require_once __DIR__ . '/../config/config.php';

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/lib/animate/animate.css">
    <style>
        body {
            background-image: url('<?php echo SITE_URL; ?>/img/bg-pattern.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    
<?php if ($flash): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>
