<?php
/**
 * Simple Test Runner - No PHPUnit Required
 * Runs basic API tests without external dependencies
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Global cookie handling for HTTP requests
$GLOBALS['__COOKIE'] = '';
$GLOBALS['__COOKIE_FILE'] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'devin_tests_cookie.txt';

class SimpleTestRunner {
    private $passed = 0;
    private $failed = 0;
    private $tests = [];
    
    public function test($name, $callback) {
        $this->tests[] = ['name' => $name, 'callback' => $callback];
    }
    
    public function run() {
        echo "\n========================================\n";
        echo " Mahkota Web System - Simple QA Tests\n";
        echo "========================================\n\n";
        
        foreach ($this->tests as $test) {
            echo "Testing: {$test['name']}... ";
            
            try {
                $result = call_user_func($test['callback']);
                if ($result === true) {
                    echo "✓ PASSED\n";
                    $this->passed++;
                } else {
                    echo "✗ FAILED: $result\n";
                    $this->failed++;
                }
            } catch (Exception $e) {
                echo "✗ ERROR: " . $e->getMessage() . "\n";
                $this->failed++;
            }
        }
        
        echo "\n========================================\n";
        echo " Results: {$this->passed} passed, {$this->failed} failed\n";
        echo "========================================\n\n";
        
        return $this->failed === 0;
    }
}

// Helper function to make HTTP requests
function makeRequest($url, $method = 'GET', $data = []) {
    // Use cURL if available
    if (function_exists('curl_init')) {
        $ch = curl_init();
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true); // include headers in response

        // Use cookie jar for session persistence
        if (!empty($GLOBALS['__COOKIE_FILE'])) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $GLOBALS['__COOKIE_FILE']);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $GLOBALS['__COOKIE_FILE']);
        }

        // Also send in-memory cookie if available (fallback)
        if (!empty($GLOBALS['__COOKIE'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Cookie: ' . $GLOBALS['__COOKIE'] ]);
        }

        $responseRaw = curl_exec($ch);
        if ($responseRaw === false) {
            $error = curl_error($ch);
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headerPart = substr($responseRaw, 0, $headerSize);
        $response = substr($responseRaw, $headerSize);
        curl_close($ch);
        
        // Parse Set-Cookie from headers into in-memory cookie store
        if (!empty($headerPart)) {
            if (preg_match_all('/^Set-Cookie:\s*([^;\r\n]+)/mi', $headerPart, $matches)) {
                // Prefer PHPSESSID
                foreach ($matches[1] as $cookieStr) {
                    if (stripos($cookieStr, 'PHPSESSID=') !== false) {
                        $GLOBALS['__COOKIE'] = $cookieStr; // e.g., PHPSESSID=abc123
                        break;
                    }
                }
                // If PHPSESSID not found, take the last cookie
                if (empty($GLOBALS['__COOKIE']) && !empty($matches[1])) {
                    $GLOBALS['__COOKIE'] = end($matches[1]);
                }
            }
        }

        return [
            'code' => $httpCode,
            'body' => $response,
            'json' => json_decode($response, true)
        ];
    }
    
    // Fallback to file_get_contents if cURL is not available
    $contextOptions = [
        'http' => [
            'method' => $method,
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'ignore_errors' => true
        ]
    ];

    if ($method === 'POST') {
        $contextOptions['http']['content'] = http_build_query($data);
    }
    // Send cookie if available
    if (!empty($GLOBALS['__COOKIE'])) {
        $contextOptions['http']['header'] .= "\r\n" . 'Cookie: ' . $GLOBALS['__COOKIE'];
    }
    
    $context = stream_context_create($contextOptions);
    $response = @file_get_contents($url, false, $context);
    
    // Parse HTTP status code from response headers
    $httpCode = 0;
    if (isset($http_response_header) && is_array($http_response_header)) {
        foreach ($http_response_header as $header) {
            if (preg_match('#HTTP/\\d+\.\\d+\s+(\\d+)#', $header, $matches)) {
                $httpCode = (int) $matches[1];
                break;
            }
        }
        // Capture Set-Cookie into in-memory store
        foreach ($http_response_header as $header) {
            if (stripos($header, 'Set-Cookie:') === 0) {
                // Extract cookie key=value only
                if (preg_match('/Set-Cookie:\s*([^;\r\n]+)/i', $header, $m)) {
                    $cookieKV = $m[1];
                    if (stripos($cookieKV, 'PHPSESSID=') !== false) {
                        $GLOBALS['__COOKIE'] = $cookieKV;
                    } else if (empty($GLOBALS['__COOKIE'])) {
                        $GLOBALS['__COOKIE'] = $cookieKV;
                    }
                }
            }
        }
    }
    
    return [
        'code' => $httpCode,
        'body' => $response !== false ? $response : '',
        'json' => json_decode($response, true)
    ];
}

// Initialize test runner
$runner = new SimpleTestRunner();

// Base URL for API
// Sesuaikan dengan server yang aktif:
// - Laragon Apache (disarankan): 'http://localhost/Devin/api'
// - PHP built-in server (butuh ekstensi mysqli aktif): 'http://localhost:8000/api'
$baseUrl = 'http://localhost/Devin/api';
// Base root untuk endpoint non-API (mis. login.php)
$baseRoot = preg_replace('/\/api$/', '', $baseUrl);

// ========================================
// AUTH SETUP
// ========================================

$runner->test('Login - Admin', function() use ($baseRoot) {
    $response = makeRequest($baseRoot . '/login.php', 'POST', [
        'username' => 'admin',
        'password' => 'admin123'
    ]);
    if ($response['code'] !== 200) {
        return "HTTP {$response['code']} expected 200";
    }
    if (!isset($response['json']['success']) || !$response['json']['success']) {
        return 'Login failed';
    }
    return true;
});

// ========================================
// ARTIKEL TESTS
// ========================================

$runner->test('Artikel - Read All', function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/artikel/read.php");
    
    if ($response['code'] !== 200) {
        return "HTTP {$response['code']} expected 200";
    }
    
    $data = $response['json'];
    if (!is_array($data)) {
        return "Response should be JSON array";
    }
    
    return true;
});

$runner->test('Artikel - Create (Missing Title)', function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/artikel/create.php", 'POST', [
        'kategori' => 'Test',
        'isi' => 'Test content'
    ]);
    
    $data = $response['json'];
    if (!isset($data['error'])) {
        return "Should return error for missing title";
    }
    
    return true;
});

// ========================================
// PROGRAM TESTS
// ========================================

$runner->test('Program - Read All', function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/program/read.php");
    
    if ($response['code'] !== 200) {
        return "HTTP {$response['code']} expected 200";
    }
    
    $data = $response['json'];
    if (!is_array($data)) {
        return "Response should be JSON array";
    }
    
    return true;
});

$runner->test('Program - Create (Missing Title)', function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/program/create.php", 'POST', [
        'deskripsi' => 'Test',
        'tanggal_mulai' => '2024-01-01'
    ]);
    
    $data = $response['json'];
    if (!isset($data['error'])) {
        return "Should return error for missing title";
    }
    
    return true;
});

// ========================================
// GALERI TESTS
// ========================================

$runner->test('Galeri - Read All', function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/galeri/read.php");
    
    if ($response['code'] !== 200) {
        return "HTTP {$response['code']} expected 200";
    }
    
    $data = $response['json'];
    if (!is_array($data)) {
        return "Response should be JSON array";
    }
    
    return true;
});

$runner->test('Galeri - Create (Missing Title)', function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/galeri/create.php", 'POST', [
        'deskripsi' => 'Test'
    ]);
    
    $data = $response['json'];
    if (!isset($data['error'])) {
        return "Should return error for missing title";
    }
    
    return true;
});

// ========================================
// DATABASE CONNECTION TEST
// ========================================

$runner->test('Database - Connection', function() {
    // Skip if mysqli extension is not loaded in CLI
    if (!extension_loaded('mysqli')) {
        echo "(SKIP: ekstensi mysqli belum aktif di CLI) ";
        return true;
    }
    $dbFile = dirname(__DIR__) . '/db.php';
    if (!file_exists($dbFile)) {
        return "db.php not found";
    }
    
    require_once $dbFile;
    
    if (!isset($conn)) {
        return "Database connection not established";
    }
    
    if ($conn->connect_error) {
        return "Connection failed: " . $conn->connect_error;
    }
    
    return true;
});

// ========================================
// API ENDPOINT TESTS
// ========================================

$runner->test('API - Artikel Endpoints Exist', function() use ($baseUrl) {
    $endpoints = ['read.php', 'create.php', 'update.php', 'delete.php'];
    foreach ($endpoints as $endpoint) {
        $file = dirname(__DIR__) . "/api/artikel/$endpoint";
        if (!file_exists($file)) {
            return "Missing endpoint: artikel/$endpoint";
        }
    }
    return true;
});

$runner->test('API - Program Endpoints Exist', function() use ($baseUrl) {
    $endpoints = ['read.php', 'create.php', 'update.php', 'delete.php'];
    foreach ($endpoints as $endpoint) {
        $file = dirname(__DIR__) . "/api/program/$endpoint";
        if (!file_exists($file)) {
            return "Missing endpoint: program/$endpoint";
        }
    }
    return true;
});

$runner->test('API - Galeri Endpoints Exist', function() use ($baseUrl) {
    $endpoints = ['read.php', 'create.php', 'update.php', 'delete.php'];
    foreach ($endpoints as $endpoint) {
        $file = dirname(__DIR__) . "/api/galeri/$endpoint";
        if (!file_exists($file)) {
            return "Missing endpoint: galeri/$endpoint";
        }
    }
    return true;
});

// Run all tests
$success = $runner->run();

// Exit with appropriate code
exit($success ? 0 : 1);
