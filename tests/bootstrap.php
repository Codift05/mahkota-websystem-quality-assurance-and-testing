<?php
/**
 * PHPUnit Bootstrap File
 * Inisialisasi environment untuk testing
 */

// Load Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Define test constants
define('TEST_MODE', true);
define('PROJECT_ROOT', dirname(__DIR__));

// Load test helpers
require_once __DIR__ . '/helpers/TestHelper.php';
require_once __DIR__ . '/helpers/DatabaseHelper.php';
require_once __DIR__ . '/helpers/ApiTestCase.php';

// Initialize test database
$dbHelper = new DatabaseHelper();
$dbHelper->setupTestDatabase();
