<?php
/**
 * Test Helper Class
 * Utility functions untuk testing
 */

class TestHelper
{
    /**
     * Generate random string untuk testing
     */
    public static function randomString($length = 10)
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }

    /**
     * Generate random email untuk testing
     */
    public static function randomEmail()
    {
        return 'test_' . self::randomString(8) . '@example.com';
    }

    /**
     * Create dummy image file untuk testing upload
     */
    public static function createDummyImage($filename = 'test_image.jpg')
    {
        $width = 100;
        $height = 100;
        $image = imagecreatetruecolor($width, $height);
        
        // Fill with random color
        $color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
        imagefill($image, 0, 0, $color);
        
        $tempFile = sys_get_temp_dir() . '/' . $filename;
        imagejpeg($image, $tempFile);
        imagedestroy($image);
        
        return $tempFile;
    }

    /**
     * Clean up test files
     */
    public static function cleanupTestFiles($pattern)
    {
        $files = glob($pattern);
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Mock $_FILES array untuk testing file upload
     */
    public static function mockFileUpload($fieldName, $filePath, $originalName = 'test.jpg')
    {
        return [
            $fieldName => [
                'name' => $originalName,
                'type' => mime_content_type($filePath),
                'tmp_name' => $filePath,
                'error' => UPLOAD_ERR_OK,
                'size' => filesize($filePath)
            ]
        ];
    }

    /**
     * Simulate session untuk testing
     */
    public static function mockAdminSession()
    {
        // Pastikan session aktif agar $_SESSION tersedia di semua include
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        if (!isset($_SESSION) || !is_array($_SESSION)) {
            $_SESSION = [];
        }
        $_SESSION['is_admin'] = true;
        $_SESSION['username'] = 'test_admin';
    }

    /**
     * Clear session
     */
    public static function clearSession()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Bersihkan isi session dan hancurkan
            session_unset();
            @session_destroy();
        }
        $_SESSION = [];
    }

    /**
     * Assert JSON response structure
     */
    public static function assertJsonStructure($json, $expectedKeys)
    {
        $data = json_decode($json, true);
        foreach ($expectedKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new Exception("Expected key '$key' not found in JSON response");
            }
        }
        return true;
    }
}
