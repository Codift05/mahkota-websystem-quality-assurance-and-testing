<?php
// api/program/read.php
// Helper to prevent hard exit during tests
if (!function_exists('end_response')) {
    function end_response() {
        if (defined('TEST_MODE') && PHP_SAPI === 'cli') { return; }
        exit;
    }
}
if (session_status() === PHP_SESSION_NONE && PHP_SAPI !== 'cli' && !headers_sent()) { session_start(); }
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    return;
}
if (PHP_SAPI !== 'cli' && !headers_sent()) { header('Content-Type: application/json'); }
require_once dirname(__DIR__, 2) . '/db.php';
// Import global connection when included inside function scope
global $conn;

// Filters
$bidang = $_GET['bidang'] ?? '';
$status = $_GET['status'] ?? '';
$orderBy = $_GET['order_by'] ?? 'created_at';
$orderDir = strtolower($_GET['order_dir'] ?? 'desc');
$orderDir = $orderDir === 'asc' ? 'ASC' : 'DESC';

$query = 'SELECT id, nama_program, bidang, deskripsi, status, created_at FROM program WHERE 1=1';
$params = [];
$types = '';

if ($bidang) {
    $query .= ' AND bidang = ?';
    $types .= 's';
    $params[] = $bidang;
}
if ($status) {
    $query .= ' AND status = ?';
    $types .= 's';
    $params[] = $status;
}

// orderBy whitelist
$allowedOrder = ['id', 'nama_program', 'bidang', 'status', 'created_at'];
if (!in_array($orderBy, $allowedOrder, true)) {
    $orderBy = 'created_at';
}
// Primary ordering
$query .= " ORDER BY $orderBy $orderDir";
// Secondary tie-breaker to ensure deterministic ordering within the same timestamp
if ($orderBy !== 'id') {
    $query .= ", id $orderDir";
}

if ($params) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

$programs = [];
while ($row = $result->fetch_assoc()) {
    $programs[] = $row;
}
if (isset($stmt)) { $stmt->close(); }

echo json_encode($programs);
// Do not close $conn to allow multiple API calls in same process
