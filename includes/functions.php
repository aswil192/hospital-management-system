<?php
// Utility functions for the hospital management system

// Format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Format datetime
function formatDateTime($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

// Format currency
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

// Get user by ID
function getUserById($userId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    closeDBConnection($conn);
    return $user;
}

// Get patient by user ID
function getPatientByUserId($userId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT p.*, u.name, u.email, u.phone, u.address 
                            FROM patients p 
                            JOIN users u ON p.user_id = u.id 
                            WHERE u.id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
    $stmt->close();
    closeDBConnection($conn);
    return $patient;
}

// Get doctor by user ID
function getDoctorByUserId($userId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT d.*, u.name, u.email, u.phone, u.address 
                            FROM doctors d 
                            JOIN users u ON d.user_id = u.id 
                            WHERE u.id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $doctor = $result->fetch_assoc();
    $stmt->close();
    closeDBConnection($conn);
    return $doctor;
}

// Get all doctors
function getAllDoctors() {
    $conn = getDBConnection();
    $query = "SELECT d.*, u.name, u.email, u.phone 
              FROM doctors d 
              JOIN users u ON d.user_id = u.id 
              WHERE u.status = 'active'
              ORDER BY u.name";
    $result = $conn->query($query);
    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
    closeDBConnection($conn);
    return $doctors;
}

// Get available doctors
function getAvailableDoctors() {
    $conn = getDBConnection();
    $query = "SELECT d.*, u.name, u.email, u.phone 
              FROM doctors d 
              JOIN users u ON d.user_id = u.id 
              WHERE u.status = 'active' AND d.availability_status = 'available'
              ORDER BY u.name";
    $result = $conn->query($query);
    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
    closeDBConnection($conn);
    return $doctors;
}

// Get all medicines
function getAllMedicines() {
    $conn = getDBConnection();
    $query = "SELECT * FROM medicines WHERE stock_quantity > 0 ORDER BY name";
    $result = $conn->query($query);
    $medicines = [];
    while ($row = $result->fetch_assoc()) {
        $medicines[] = $row;
    }
    closeDBConnection($conn);
    return $medicines;
}

// Get specializations
function getSpecializations() {
    $conn = getDBConnection();
    $query = "SELECT DISTINCT specialization FROM doctors ORDER BY specialization";
    $result = $conn->query($query);
    $specializations = [];
    while ($row = $result->fetch_assoc()) {
        $specializations[] = $row['specialization'];
    }
    closeDBConnection($conn);
    return $specializations;
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate phone
function isValidPhone($phone) {
    return preg_match('/^[0-9]{10,15}$/', $phone);
}

// Get status badge class
function getStatusBadgeClass($status) {
    $classes = [
        'pending' => 'bg-warning',
        'confirmed' => 'bg-info',
        'completed' => 'bg-success',
        'cancelled' => 'bg-danger',
        'paid' => 'bg-success',
        'unpaid' => 'bg-danger',
        'partially_paid' => 'bg-warning',
        'active' => 'bg-success',
        'inactive' => 'bg-secondary',
        'available' => 'bg-success',
        'unavailable' => 'bg-danger',
        'on_leave' => 'bg-warning'
    ];
    return $classes[$status] ?? 'bg-secondary';
}

// Count records
function countRecords($table, $condition = '') {
    $conn = getDBConnection();
    $query = "SELECT COUNT(*) as count FROM $table";
    if ($condition) {
        $query .= " WHERE $condition";
    }
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    closeDBConnection($conn);
    return $row['count'];
}

// Pagination helper
function getPaginationData($currentPage, $totalRecords, $recordsPerPage = 10) {
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $offset = ($currentPage - 1) * $recordsPerPage;
    
    return [
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'total_records' => $totalRecords,
        'records_per_page' => $recordsPerPage,
        'offset' => $offset
    ];
}
?>