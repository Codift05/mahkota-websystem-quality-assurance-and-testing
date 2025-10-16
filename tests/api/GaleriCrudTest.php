<?php
/**
 * Test Case: Galeri CRUD API
 * Testing endpoints: /api/galeri/*.php
 */

require_once __DIR__ . '/../helpers/ApiTestCase.php';

class GaleriCrudTest extends ApiTestCase
{
    /**
     * Test: Berhasil membuat galeri
     */
    public function testCreateGaleriSuccess()
    {
        $data = [
            'judul' => 'Galeri Test Baru',
            'deskripsi' => 'Deskripsi galeri untuk testing',
            'kategori' => 'Kegiatan'
        ];

        // Mock file upload
        $imagePath = TestHelper::createDummyImage('test_galeri.jpg');
        $_FILES = TestHelper::mockFileUpload('gambar', $imagePath, 'test_galeri.jpg');

        $response = $this->post('/api/galeri/create.php', $data);
        $result = $this->assertJsonResponse($response, 'success');

        $this->assertArrayHasKey('message', $result);
        $this->assertRecordCount('galeri', 1);

        // Cleanup
        unlink($imagePath);
    }

    /**
     * Test: Gagal membuat galeri tanpa judul
     */
    public function testCreateGaleriWithoutJudul()
    {
        $data = [
            'deskripsi' => 'Deskripsi tanpa judul',
            'kategori' => 'Kegiatan'
        ];

        $response = $this->post('/api/galeri/create.php', $data);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('judul', strtolower($result['error']));
        $this->assertRecordCount('galeri', 0);
    }

    /**
     * Test: Gagal membuat galeri tanpa gambar
     */
    public function testCreateGaleriWithoutImage()
    {
        $data = [
            'judul' => 'Galeri Tanpa Gambar',
            'deskripsi' => 'Deskripsi',
            'kategori' => 'Kegiatan'
        ];

        $response = $this->post('/api/galeri/create.php', $data);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('gambar', strtolower($result['error']));
    }

    /**
     * Test: Berhasil membaca list galeri
     */
    public function testReadGaleriList()
    {
        // Insert test data
        $this->createTestGaleri(['judul' => 'Galeri 1']);
        $this->createTestGaleri(['judul' => 'Galeri 2']);
        $this->createTestGaleri(['judul' => 'Galeri 3']);

        $response = $this->get('/api/galeri/read.php');
        $data = json_decode($response, true);

        $this->assertIsArray($data);
        $this->assertCount(3, $data);
    }

    /**
     * Test: Berhasil update galeri
     */
    public function testUpdateGaleriSuccess()
    {
        $id = $this->createTestGaleri([
            'judul' => 'Judul Lama',
            'deskripsi' => 'Deskripsi lama',
            'kategori' => 'Kegiatan'
        ]);

        $data = [
            'id' => $id,
            'judul' => 'Judul Baru',
            'deskripsi' => 'Deskripsi baru',
            'kategori' => 'Dokumentasi'
        ];

        $response = $this->post('/api/galeri/update.php', $data);
        $result = $this->assertJsonResponse($response, 'success');

        // Verify data terupdate
        $galeri = $this->assertDatabaseHas('galeri', $id);
        $this->assertEquals('Judul Baru', $galeri['judul']);
        $this->assertEquals('Dokumentasi', $galeri['kategori']);
    }

    /**
     * Test: Gagal update galeri tanpa ID
     */
    public function testUpdateGaleriWithoutId()
    {
        $data = [
            'judul' => 'Judul Baru',
            'deskripsi' => 'Deskripsi baru'
        ];

        $response = $this->post('/api/galeri/update.php', $data);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
    }

    /**
     * Test: Berhasil delete galeri
     */
    public function testDeleteGaleriSuccess()
    {
        $id = $this->createTestGaleri([
            'judul' => 'Galeri Yang Akan Dihapus'
        ]);

        $this->assertDatabaseHas('galeri', $id);

        $response = $this->post('/api/galeri/delete.php', ['id' => $id]);
        $result = $this->assertJsonResponse($response, 'success');

        $this->assertDatabaseMissing('galeri', $id);
    }

    /**
     * Test: Gagal delete galeri tanpa ID
     */
    public function testDeleteGaleriWithoutId()
    {
        $response = $this->post('/api/galeri/delete.php', []);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
    }

    /**
     * Test: Galeri dengan berbagai kategori
     */
    public function testGaleriWithDifferentCategories()
    {
        $this->createTestGaleri(['kategori' => 'Kegiatan']);
        $this->createTestGaleri(['kategori' => 'Dokumentasi']);
        $this->createTestGaleri(['kategori' => 'Event']);

        $response = $this->get('/api/galeri/read.php');
        $data = json_decode($response, true);

        $categories = array_column($data, 'kategori');
        $this->assertContains('Kegiatan', $categories);
        $this->assertContains('Dokumentasi', $categories);
        $this->assertContains('Event', $categories);
    }

    /**
     * Test: Response structure galeri
     */
    public function testGaleriResponseStructure()
    {
        $this->createTestGaleri();

        $response = $this->get('/api/galeri/read.php');
        $data = json_decode($response, true);

        $galeri = $data[0];
        $this->assertArrayHasKey('id', $galeri);
        $this->assertArrayHasKey('judul', $galeri);
        $this->assertArrayHasKey('deskripsi', $galeri);
        $this->assertArrayHasKey('kategori', $galeri);
        $this->assertArrayHasKey('gambar', $galeri);
        $this->assertArrayHasKey('tanggal', $galeri);
    }

    /**
     * Test: CRUD lengkap untuk satu galeri
     */
    public function testCompleteCrudCycle()
    {
        // CREATE
        $id = $this->createTestGaleri([
            'judul' => 'Test CRUD Galeri',
            'deskripsi' => 'Deskripsi awal',
            'kategori' => 'Kegiatan'
        ]);
        $this->assertDatabaseHas('galeri', $id);

        // READ
        $response = $this->get('/api/galeri/read.php');
        $data = json_decode($response, true);
        $this->assertCount(1, $data);
        $this->assertEquals('Test CRUD Galeri', $data[0]['judul']);

        // UPDATE
        $updateData = [
            'id' => $id,
            'judul' => 'Test CRUD Galeri Updated',
            'deskripsi' => 'Deskripsi updated',
            'kategori' => 'Dokumentasi'
        ];
        $response = $this->post('/api/galeri/update.php', $updateData);
        $this->assertJsonResponse($response, 'success');

        $galeri = $this->assertDatabaseHas('galeri', $id);
        $this->assertEquals('Test CRUD Galeri Updated', $galeri['judul']);

        // DELETE
        $response = $this->post('/api/galeri/delete.php', ['id' => $id]);
        $this->assertJsonResponse($response, 'success');
        $this->assertDatabaseMissing('galeri', $id);
    }

    /**
     * Test: Gagal akses tanpa session admin
     */
    public function testGaleriAccessWithoutAdminSession()
    {
        TestHelper::clearSession();

        // Test create
        $response = $this->post('/api/galeri/create.php', ['judul' => 'Test']);
        $result = json_decode($response, true);
        $this->assertArrayHasKey('error', $result);

        // Test read
        $response = $this->get('/api/galeri/read.php');
        $result = json_decode($response, true);
        $this->assertArrayHasKey('error', $result);

        // Test update
        $response = $this->post('/api/galeri/update.php', ['id' => 1]);
        $result = json_decode($response, true);
        $this->assertArrayHasKey('error', $result);

        // Test delete
        $response = $this->post('/api/galeri/delete.php', ['id' => 1]);
        $result = json_decode($response, true);
        $this->assertArrayHasKey('error', $result);
    }
}
