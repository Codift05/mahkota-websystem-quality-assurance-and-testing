<?php
/**
 * Base API Test Case
 * Base class untuk semua API tests
 */

use PHPUnit\Framework\TestCase;

abstract class ApiTestCase extends TestCase
{
    protected $db;
    protected $testHelper;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->db = new DatabaseHelper();
        $this->testHelper = new TestHelper();
        
        // Clean database sebelum setiap test
        $this->db->cleanDatabase();
        
        // Setup admin session untuk testing
        TestHelper::mockAdminSession();
    }

    protected function tearDown(): void
    {
        // Cleanup setelah test
        TestHelper::clearSession();
        
        parent::tearDown();
    }

    /**
     * Simulate HTTP POST request
     */
    protected function post($url, $data = [], $files = [])
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = $data;
        $_FILES = $files;

        ob_start();
        include PROJECT_ROOT . $url;
        $response = ob_get_clean();

        return $response;
    }

    /**
     * Simulate HTTP GET request
     */
    protected function get($url, $params = [])
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = $params;

        ob_start();
        include PROJECT_ROOT . $url;
        $response = ob_get_clean();

        return $response;
    }

    /**
     * Assert JSON response
     */
    protected function assertJsonResponse($response, $expectedStatus = 'success')
    {
        $data = json_decode($response, true);
        
        $this->assertNotNull($data, 'Response should be valid JSON');
        
        if ($expectedStatus === 'success') {
            $this->assertTrue(
                isset($data['success']) && $data['success'] === true,
                'Response should indicate success'
            );
        } elseif ($expectedStatus === 'error') {
            $this->assertTrue(
                isset($data['error']),
                'Response should contain error message'
            );
        }
        
        return $data;
    }

    /**
     * Assert response contains keys
     */
    protected function assertResponseHasKeys($response, $keys)
    {
        $data = json_decode($response, true);
        
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $data, "Response should contain key: $key");
        }
    }

    /**
     * Create test artikel
     */
    protected function createTestArtikel($overrides = [])
    {
        $data = array_merge([
            'judul' => 'Test Artikel ' . TestHelper::randomString(5),
            'kategori' => 'Berita',
            'isi' => 'Ini adalah isi artikel untuk testing',
            'gambar' => 'uploads/test.jpg'
        ], $overrides);

        return $this->db->insertTestData('artikel', $data);
    }

    /**
     * Create test program
     */
    protected function createTestProgram($overrides = [])
    {
        $data = array_merge([
            'nama_program' => 'Test Program ' . TestHelper::randomString(5),
            'bidang' => 'Pendidikan',
            'deskripsi' => 'Deskripsi program testing',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+30 days')),
            'status' => 'planned',
            'gambar' => 'uploads/program/test.jpg'
        ], $overrides);

        return $this->db->insertTestData('program', $data);
    }

    /**
     * Create test galeri
     */
    protected function createTestGaleri($overrides = [])
    {
        $data = array_merge([
            'judul' => 'Test Galeri ' . TestHelper::randomString(5),
            'deskripsi' => 'Deskripsi galeri testing',
            'kategori' => 'Kegiatan',
            'gambar' => 'uploads/galeri/test.jpg'
        ], $overrides);

        return $this->db->insertTestData('galeri', $data);
    }

    /**
     * Assert database has record
     */
    protected function assertDatabaseHas($table, $id)
    {
        $record = $this->db->getById($table, $id);
        $this->assertNotNull($record, "Record with ID $id should exist in $table");
        return $record;
    }

    /**
     * Assert database missing record
     */
    protected function assertDatabaseMissing($table, $id)
    {
        $record = $this->db->getById($table, $id);
        $this->assertNull($record, "Record with ID $id should not exist in $table");
    }

    /**
     * Assert record count
     */
    protected function assertRecordCount($table, $expectedCount, $where = '')
    {
        $actualCount = $this->db->countRecords($table, $where);
        $this->assertEquals(
            $expectedCount,
            $actualCount,
            "Expected $expectedCount records in $table, found $actualCount"
        );
    }
}
