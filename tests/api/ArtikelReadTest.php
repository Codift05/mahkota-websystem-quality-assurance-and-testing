<?php
/**
 * Test Case: Artikel Read API
 * Testing endpoint: /api/artikel/read.php
 */

require_once __DIR__ . '/../helpers/ApiTestCase.php';

class ArtikelReadTest extends ApiTestCase
{
    /**
     * Test: Berhasil membaca list artikel kosong
     */
    public function testReadEmptyArtikelList()
    {
        $response = $this->get('/api/artikel/read.php');
        $data = json_decode($response, true);

        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    /**
     * Test: Berhasil membaca list artikel dengan data
     */
    public function testReadArtikelListWithData()
    {
        // Insert test data
        $this->createTestArtikel(['judul' => 'Artikel 1']);
        $this->createTestArtikel(['judul' => 'Artikel 2']);
        $this->createTestArtikel(['judul' => 'Artikel 3']);

        $response = $this->get('/api/artikel/read.php');
        $data = json_decode($response, true);

        $this->assertIsArray($data);
        $this->assertCount(3, $data);
    }

    /**
     * Test: Artikel diurutkan berdasarkan tanggal DESC
     */
    public function testArtikelOrderedByDateDesc()
    {
        // Insert artikel dengan delay
        $id1 = $this->createTestArtikel(['judul' => 'Artikel Pertama']);
        sleep(1);
        $id2 = $this->createTestArtikel(['judul' => 'Artikel Kedua']);
        sleep(1);
        $id3 = $this->createTestArtikel(['judul' => 'Artikel Ketiga']);

        $response = $this->get('/api/artikel/read.php');
        $data = json_decode($response, true);

        // Artikel terbaru harus di posisi pertama
        $this->assertEquals($id3, $data[0]['id']);
        $this->assertEquals($id2, $data[1]['id']);
        $this->assertEquals($id1, $data[2]['id']);
    }

    /**
     * Test: Response memiliki struktur yang benar
     */
    public function testArtikelResponseStructure()
    {
        $this->createTestArtikel();

        $response = $this->get('/api/artikel/read.php');
        $data = json_decode($response, true);

        $this->assertIsArray($data);
        $this->assertGreaterThan(0, count($data));

        $artikel = $data[0];
        $this->assertArrayHasKey('id', $artikel);
        $this->assertArrayHasKey('judul', $artikel);
        $this->assertArrayHasKey('kategori', $artikel);
        $this->assertArrayHasKey('isi', $artikel);
        $this->assertArrayHasKey('gambar', $artikel);
        $this->assertArrayHasKey('tanggal', $artikel);
    }

    /**
     * Test: Membaca artikel dengan berbagai kategori
     */
    public function testReadArtikelWithDifferentCategories()
    {
        $this->createTestArtikel(['kategori' => 'Berita']);
        $this->createTestArtikel(['kategori' => 'Pengumuman']);
        $this->createTestArtikel(['kategori' => 'Artikel']);

        $response = $this->get('/api/artikel/read.php');
        $data = json_decode($response, true);

        $this->assertCount(3, $data);

        $categories = array_column($data, 'kategori');
        $this->assertContains('Berita', $categories);
        $this->assertContains('Pengumuman', $categories);
        $this->assertContains('Artikel', $categories);
    }

    /**
     * Test: Gagal akses tanpa session admin
     */
    public function testReadArtikelWithoutAdminSession()
    {
        TestHelper::clearSession();

        $response = $this->get('/api/artikel/read.php');
        $result = json_decode($response, true);

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('unauthorized', strtolower($result['error']));
    }

    /**
     * Test: Performance dengan banyak data
     */
    public function testReadArtikelPerformance()
    {
        // Insert 50 artikel
        for ($i = 1; $i <= 50; $i++) {
            $this->createTestArtikel(['judul' => "Artikel $i"]);
        }

        $startTime = microtime(true);
        $response = $this->get('/api/artikel/read.php');
        $endTime = microtime(true);

        $executionTime = $endTime - $startTime;

        $data = json_decode($response, true);
        $this->assertCount(50, $data);

        // Assert execution time kurang dari 1 detik
        $this->assertLessThan(1.0, $executionTime, 'Read operation should complete within 1 second');
    }
}
