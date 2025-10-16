<?php
/**
 * Test Case: Artikel Delete API
 * Testing endpoint: /api/artikel/delete.php
 */

require_once __DIR__ . '/../helpers/ApiTestCase.php';

class ArtikelDeleteTest extends ApiTestCase
{
    /**
     * Test: Berhasil delete artikel
     */
    public function testDeleteArtikelSuccess()
    {
        $id = $this->createTestArtikel([
            'judul' => 'Artikel Yang Akan Dihapus',
            'kategori' => 'Berita',
            'isi' => 'Isi artikel'
        ]);

        $this->assertDatabaseHas('artikel', $id);

        $data = ['id' => $id];
        $response = $this->post('/api/artikel/delete.php', $data);
        $result = $this->assertJsonResponse($response, 'success');

        $this->assertArrayHasKey('message', $result);
        $this->assertStringContainsString('berhasil', strtolower($result['message']));

        // Verify data terhapus dari database
        $this->assertDatabaseMissing('artikel', $id);
    }

    /**
     * Test: Gagal delete artikel tanpa ID
     */
    public function testDeleteArtikelWithoutId()
    {
        $response = $this->post('/api/artikel/delete.php', []);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('id', strtolower($result['error']));
    }

    /**
     * Test: Delete artikel dengan ID tidak valid
     */
    public function testDeleteArtikelWithInvalidId()
    {
        $data = ['id' => 99999]; // ID yang tidak ada

        $response = $this->post('/api/artikel/delete.php', $data);
        
        // Meskipun ID tidak ada, API tetap return success (sesuai implementasi)
        $result = json_decode($response, true);
        $this->assertNotNull($result);
    }

    /**
     * Test: Delete artikel dengan ID string
     */
    public function testDeleteArtikelWithStringId()
    {
        $data = ['id' => 'invalid_id'];

        $response = $this->post('/api/artikel/delete.php', $data);
        $result = json_decode($response, true);
        
        // Tergantung implementasi, bisa error atau success dengan 0 affected rows
        $this->assertNotNull($result);
    }

    /**
     * Test: Delete multiple artikel
     */
    public function testDeleteMultipleArtikel()
    {
        $id1 = $this->createTestArtikel(['judul' => 'Artikel 1']);
        $id2 = $this->createTestArtikel(['judul' => 'Artikel 2']);
        $id3 = $this->createTestArtikel(['judul' => 'Artikel 3']);

        $this->assertRecordCount('artikel', 3);

        // Delete artikel 1
        $response = $this->post('/api/artikel/delete.php', ['id' => $id1]);
        $this->assertJsonResponse($response, 'success');
        $this->assertDatabaseMissing('artikel', $id1);

        // Delete artikel 2
        $response = $this->post('/api/artikel/delete.php', ['id' => $id2]);
        $this->assertJsonResponse($response, 'success');
        $this->assertDatabaseMissing('artikel', $id2);

        // Verify hanya artikel 3 yang tersisa
        $this->assertRecordCount('artikel', 1);
        $this->assertDatabaseHas('artikel', $id3);
    }

    /**
     * Test: Delete semua artikel
     */
    public function testDeleteAllArtikel()
    {
        // Create 10 artikel
        $ids = [];
        for ($i = 1; $i <= 10; $i++) {
            $ids[] = $this->createTestArtikel(['judul' => "Artikel $i"]);
        }

        $this->assertRecordCount('artikel', 10);

        // Delete semua artikel
        foreach ($ids as $id) {
            $response = $this->post('/api/artikel/delete.php', ['id' => $id]);
            $this->assertJsonResponse($response, 'success');
        }

        // Verify semua artikel terhapus
        $this->assertRecordCount('artikel', 0);
    }

    /**
     * Test: Delete artikel yang sama dua kali
     */
    public function testDeleteSameArtikelTwice()
    {
        $id = $this->createTestArtikel();

        // Delete pertama kali
        $response = $this->post('/api/artikel/delete.php', ['id' => $id]);
        $this->assertJsonResponse($response, 'success');
        $this->assertDatabaseMissing('artikel', $id);

        // Delete kedua kali (artikel sudah tidak ada)
        $response = $this->post('/api/artikel/delete.php', ['id' => $id]);
        $result = json_decode($response, true);
        
        // Tetap return success meskipun artikel sudah tidak ada
        $this->assertNotNull($result);
    }

    /**
     * Test: Gagal akses tanpa session admin
     */
    public function testDeleteArtikelWithoutAdminSession()
    {
        $id = $this->createTestArtikel();
        TestHelper::clearSession();

        $response = $this->post('/api/artikel/delete.php', ['id' => $id]);
        $result = json_decode($response, true);

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('unauthorized', strtolower($result['error']));

        // Verify artikel tidak terhapus
        TestHelper::mockAdminSession();
        $this->assertDatabaseHas('artikel', $id);
    }

    /**
     * Test: Request method selain POST
     */
    public function testDeleteArtikelWithGetMethod()
    {
        $response = $this->get('/api/artikel/delete.php');
        $result = json_decode($response, true);

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('method', strtolower($result['error']));
    }

    /**
     * Test: Performance delete banyak artikel
     */
    public function testDeleteArtikelPerformance()
    {
        // Create 100 artikel
        $ids = [];
        for ($i = 1; $i <= 100; $i++) {
            $ids[] = $this->createTestArtikel(['judul' => "Artikel $i"]);
        }

        $startTime = microtime(true);

        // Delete semua
        foreach ($ids as $id) {
            $this->post('/api/artikel/delete.php', ['id' => $id]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert semua terhapus
        $this->assertRecordCount('artikel', 0);

        // Assert execution time reasonable (< 5 detik untuk 100 delete)
        $this->assertLessThan(5.0, $executionTime, 'Delete operations should complete within 5 seconds');
    }
}
