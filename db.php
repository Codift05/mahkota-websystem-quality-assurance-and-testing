<?php
// db.php - mysqli connection wrapper with test/environment overrides

// Allow environment overrides for tests (phpunit.xml) or runtime
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';

// Default DB name is 'mahkota', but if running tests (TEST_MODE) or DB_NAME is set, use that
if (getenv('DB_NAME')) {
    $DB_NAME = getenv('DB_NAME');
} else if (defined('TEST_MODE') && TEST_MODE === true) {
    $DB_NAME = 'mahkota_test';
} else {
    $DB_NAME = 'mahkota';
}

// Ensure mysqli extension is available
if (!class_exists('mysqli')) {
    error_log('PHP mysqli extension is not loaded. Enable it in php.ini for CLI/Apache.');
    die('PHP mysqli extension not loaded');
}

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    error_log('DB connection error: ' . $conn->connect_error);
    die('Database connection failed');
}
// set charset
$conn->set_charset('utf8mb4');

// Expose in $GLOBALS so includes inside function scope can access via global
$GLOBALS['conn'] = $conn;
?>