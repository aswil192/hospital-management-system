<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Site configuration
define('SITE_NAME', 'Hospital Management System');
define('SITE_URL', 'http://localhost/hospital');

// Timezone
date_default_timezone_set('UTC');

// Include database configuration
require_once __DIR__ . '/database.php';

// Security: Prevent session fixation
function regenerateSession() {
    session_regenerate_id(true);
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Get current user role
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

// Get current user ID
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Redirect to login if not authenticated
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit();
    }
}

// Redirect to appropriate dashboard based on role
function redirectToDashboard() {
    $role = getUserRole();
    
    switch ($role) {
        case 'admin':
            header('Location: ' . SITE_URL . '/admin/dashboard.php');
            break;
        case 'doctor':
            header('Location: ' . SITE_URL . '/doctor/dashboard.php');
            break;
        case 'patient':
            header('Location: ' . SITE_URL . '/patient/dashboard.php');
            break;
        default:
            header('Location: ' . SITE_URL . '/login.php');
            break;
    }
    exit();
}

// Check if user has specific role
function hasRole($role) {
    return getUserRole() === $role;
}

// Require specific role
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header('Location: ' . SITE_URL . '/403.php');
        exit();
    }
}

// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Flash message functions
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}
?>