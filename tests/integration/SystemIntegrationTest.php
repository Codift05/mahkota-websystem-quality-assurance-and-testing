<?php
/**
 * Integration Test: System Integration
 * Testing integrasi antar modul sistem
 */

require_once __DIR__ . '/../helpers/ApiTestCase.php';

class SystemIntegrationTest extends ApiTestCase
{
    /**
     * Test: Integrasi lengkap workflow artikel
     */
    public function testArtikelCompleteWorkflow()
    {
        // 1. Create artikel
        $createData = [
            'judul' => 'Artikel Integrasi Test',
            'kategori' => 'Berita',
            'isi' => 'Isi artikel untuk integration testing'
        ];
        $response = $this->post('/api/artikel/create.php', $createData);
        $this->assertJsonResponse($response, 'success');

        // 2. Read artikel
        $response = $this->get('/api/artikel/read.php');
        $articles = json_decode($response, true);
        $this->assertCount(1, $articles);
        $artikel = $articles[0];
        $id = $artikel['id'];

        // 3. Update artikel
        $updateData = [
            'id' => $id,
            'judul' => 'Artikel Integrasi Test Updated',
            'kategori' => 'Pengumuman',
            'isi' => 'Isi artikel yang sudah diupdate'
        ];
        $response = $this->post('/api/artikel/update.php', $updateData);
        $this->assertJsonResponse($response, 'success');

        // 4. Verify update
        $response = $this->get('/api/artikel/read.php');
        $articles = json_decode($response, true);
        $this->assertEquals('Artikel Integrasi Test Updated', $articles[0]['judul']);

        // 5. Delete artikel
        $response = $this->post('/api/artikel/delete.php', ['id' => $id]);
        $this->assertJsonResponse($response, 'success');

        // 6. Verify deletion
        $response = $this->get('/api/artikel/read.php');
        $articles = json_decode($response, true);
        $this->assertCount(0, $articles);
    }

    /**
     * Test: Integrasi lengkap workflow program
     */
    public function testProgramCompleteWorkflow()
    {
        // Create -> Read -> Update Status -> Delete
        $id = $this->createTestProgram([
            'nama_program' => 'Program Integrasi',
            'bidang' => 'Pendidikan',
            'status' => 'planned'
        ]);

        // Read
        $response = $this->get('/api/program/read.php');
        $programs = json_decode($response, true);
        $this->assertCount(1, $programs);

        // Update status progression
        $statuses = ['ongoing', 'completed'];
        foreach ($statuses as $status) {
            $response = $this->post('/api/program/update.php', [
                'id' => $id,
                'nama_program' => 'Program Integrasi',
                'bidang' => 'Pendidikan',
                'status' => $status
            ]);
            $this->assertJsonResponse($response, 'success');

            $program = $this->assertDatabaseHas('program', $id);
            $this->assertEquals($status, $program['status']);
        }

        // Delete
        $response = $this->post('/api/program/delete.php', ['id' => $id]);
        $this->assertJsonResponse($response, 'success');
    }

    /**
     * Test: Integrasi lengkap workflow galeri
     */
    public function testGaleriCompleteWorkflow()
    {
        // Create -> Read -> Update -> Delete
        $id = $this->createTestGaleri([
            'judul' => 'Galeri Integrasi',
            'kategori' => 'Kegiatan'
        ]);

        $response = $this->get('/api/galeri/read.php');
        $galleries = json_decode($response, true);
        $this->assertCount(1, $galleries);

        $response = $this->post('/api/galeri/update.php', [
            'id' => $id,
            'judul' => 'Galeri Integrasi Updated',
            'kategori' => 'Dokumentasi'
        ]);
        $this->assertJsonResponse($response, 'success');

        $response = $this->post('/api/galeri/delete.php', ['id' => $id]);
        $this->assertJsonResponse($response, 'success');
    }

    /**
     * Test: Multiple entities interaction
     */
    public function testMultipleEntitiesInteraction()
    {
        // Create multiple artikel
        for ($i = 1; $i <= 3; $i++) {
            $this->createTestArtikel(['judul' => "Artikel $i"]);
        }

        // Create multiple program
        for ($i = 1; $i <= 3; $i++) {
            $this->createTestProgram(['nama_program' => "Program $i"]);
        }

        // Create multiple galeri
        for ($i = 1; $i <= 3; $i++) {
            $this->createTestGaleri(['judul' => "Galeri $i"]);
        }

        // Verify counts
        $this->assertRecordCount('artikel', 3);
        $this->assertRecordCount('program', 3);
        $this->assertRecordCount('galeri', 3);

        // Read all
        $response = $this->get('/api/artikel/read.php');
        $this->assertCount(3, json_decode($response, true));

        $response = $this->get('/api/program/read.php');
        $this->assertCount(3, json_decode($response, true));

        $response = $this->get('/api/galeri/read.php');
        $this->assertCount(3, json_decode($response, true));
    }

    /**
     * Test: Concurrent operations
     */
    public function testConcurrentOperations()
    {
        // Simulate concurrent creates
        $artikelIds = [];
        $programIds = [];

        for ($i = 1; $i <= 5; $i++) {
            $artikelIds[] = $this->createTestArtikel(['judul' => "Concurrent Artikel $i"]);
            $programIds[] = $this->createTestProgram(['nama_program' => "Concurrent Program $i"]);
        }

        // Verify all created
        $this->assertRecordCount('artikel', 5);
        $this->assertRecordCount('program', 5);

        // Concurrent updates
        foreach ($artikelIds as $i => $id) {
            $this->post('/api/artikel/update.php', [
                'id' => $id,
                'judul' => "Updated Artikel " . ($i + 1),
                'kategori' => 'Berita',
                'isi' => 'Updated content'
            ]);
        }

        // Verify updates
        $response = $this->get('/api/artikel/read.php');
        $articles = json_decode($response, true);
        foreach ($articles as $artikel) {
            $this->assertStringContainsString('Updated', $artikel['judul']);
        }
    }

    /**
     * Test: Data consistency across operations
     */
    public function testDataConsistency()
    {
        // Create artikel
        $id = $this->createTestArtikel([
            'judul' => 'Consistency Test',
            'kategori' => 'Berita',
            'isi' => 'Original content'
        ]);

        // Multiple reads should return same data
        for ($i = 0; $i < 5; $i++) {
            $response = $this->get('/api/artikel/read.php');
            $articles = json_decode($response, true);
            $this->assertEquals('Consistency Test', $articles[0]['judul']);
        }

        // Update
        $this->post('/api/artikel/update.php', [
            'id' => $id,
            'judul' => 'Consistency Test Updated',
            'kategori' => 'Berita',
            'isi' => 'Updated content'
        ]);

        // Multiple reads should return updated data
        for ($i = 0; $i < 5; $i++) {
            $response = $this->get('/api/artikel/read.php');
            $articles = json_decode($response, true);
            $this->assertEquals('Consistency Test Updated', $articles[0]['judul']);
        }
    }

    /**
     * Test: Error handling across modules
     */
    public function testErrorHandlingAcrossModules()
    {
        // Test invalid operations on all modules
        $invalidId = 99999;

        // Artikel
        $response = $this->post('/api/artikel/update.php', [
            'id' => $invalidId,
            'judul' => 'Test',
            'kategori' => 'Berita',
            'isi' => 'Test'
        ]);
        $result = json_decode($response, true);
        $this->assertNotNull($result);

        // Program
        $response = $this->post('/api/program/update.php', [
            'id' => $invalidId,
            'nama_program' => 'Test',
            'bidang' => 'Test'
        ]);
        $result = json_decode($response, true);
        $this->assertNotNull($result);

        // Galeri
        $response = $this->post('/api/galeri/update.php', [
            'id' => $invalidId,
            'judul' => 'Test'
        ]);
        $result = json_decode($response, true);
        $this->assertNotNull($result);
    }

    /**
     * Test: System load handling
     */
    public function testSystemLoadHandling()
    {
        // Create load
        $startTime = microtime(true);

        for ($i = 1; $i <= 20; $i++) {
            $this->createTestArtikel(['judul' => "Load Test Artikel $i"]);
            $this->createTestProgram(['nama_program' => "Load Test Program $i"]);
            $this->createTestGaleri(['judul' => "Load Test Galeri $i"]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verify all created
        $this->assertRecordCount('artikel', 20);
        $this->assertRecordCount('program', 20);
        $this->assertRecordCount('galeri', 20);

        // Assert reasonable execution time (< 10 seconds for 60 inserts)
        $this->assertLessThan(10.0, $executionTime, 'System should handle load efficiently');
    }
}
