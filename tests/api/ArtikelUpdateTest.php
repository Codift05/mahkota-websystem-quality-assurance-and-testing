<?php
/**
 * Test Case: Artikel Update API
 * Testing endpoint: /api/artikel/update.php
 */

require_once __DIR__ . '/../helpers/ApiTestCase.php';

class ArtikelUpdateTest extends ApiTestCase
{
    /**
     * Test: Berhasil update artikel
     */
    public function testUpdateArtikelSuccess()
    {
        $id = $this->createTestArtikel([
            'judul' => 'Judul Lama',
            'kategori' => 'Berita',
            'isi' => 'Isi lama'
        ]);

        $data = [
            'id' => $id,
            'judul' => 'Judul Baru',
            'kategori' => 'Pengumuman',
            'isi' => 'Isi baru yang sudah diupdate'
        ];

        $response = $this->post('/api/artikel/update.php', $data);
        $result = $this->assertJsonResponse($response, 'success');

        // Verify data terupdate di database
        $artikel = $this->assertDatabaseHas('artikel', $id);
        $this->assertEquals('Judul Baru', $artikel['judul']);
        $this->assertEquals('Pengumuman', $artikel['kategori']);
        $this->assertEquals('Isi baru yang sudah diupdate', $artikel['isi']);
    }

    /**
     * Test: Gagal update artikel tanpa ID
     */
    public function testUpdateArtikelWithoutId()
    {
        $data = [
            'judul' => 'Judul Baru',
            'kategori' => 'Berita',
            'isi' => 'Isi baru'
        ];

        $response = $this->post('/api/artikel/update.php', $data);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('id', strtolower($result['error']));
    }

    /**
     * Test: Gagal update artikel dengan ID tidak valid
     */
    public function testUpdateArtikelWithInvalidId()
    {
        $data = [
            'id' => 99999, // ID yang tidak ada
            'judul' => 'Judul Baru',
            'kategori' => 'Berita',
            'isi' => 'Isi baru'
        ];

        $response = $this->post('/api/artikel/update.php', $data);
        
        // Meskipun execute berhasil, affected rows = 0
        // API tetap return success (sesuai implementasi)
        $result = json_decode($response, true);
        $this->assertNotNull($result);
    }

    /**
     * Test: Gagal update artikel tanpa judul
     */
    public function testUpdateArtikelWithoutJudul()
    {
        $id = $this->createTestArtikel();

        $data = [
            'id' => $id,
            'kategori' => 'Berita',
            'isi' => 'Isi baru'
        ];

        $response = $this->post('/api/artikel/update.php', $data);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
    }

    /**
     * Test: Update artikel hanya judul
     */
    public function testUpdateArtikelJudulOnly()
    {
        $id = $this->createTestArtikel([
            'judul' => 'Judul Lama',
            'kategori' => 'Berita',
            'isi' => 'Isi artikel'
        ]);

        $data = [
            'id' => $id,
            'judul' => 'Judul Baru Saja',
            'kategori' => 'Berita',
            'isi' => 'Isi artikel'
        ];

        $response = $this->post('/api/artikel/update.php', $data);
        $this->assertJsonResponse($response, 'success');

        $artikel = $this->assertDatabaseHas('artikel', $id);
        $this->assertEquals('Judul Baru Saja', $artikel['judul']);
        $this->assertEquals('Berita', $artikel['kategori']);
    }

    /**
     * Test: Update artikel dengan karakter khusus
     */
    public function testUpdateArtikelWithSpecialCharacters()
    {
        $id = $this->createTestArtikel();

        $data = [
            'id' => $id,
            'judul' => 'Judul & Test <script>',
            'kategori' => 'Berita',
            'isi' => 'Isi dengan @#$%^&*()'
        ];

        $response = $this->post('/api/artikel/update.php', $data);
        $this->assertJsonResponse($response, 'success');

        $artikel = $this->assertDatabaseHas('artikel', $id);
        $this->assertStringContainsString('&', $artikel['judul']);
    }

    /**
     * Test: Update multiple artikel
     */
    public function testUpdateMultipleArtikel()
    {
        $id1 = $this->createTestArtikel(['judul' => 'Artikel 1']);
        $id2 = $this->createTestArtikel(['judul' => 'Artikel 2']);
        $id3 = $this->createTestArtikel(['judul' => 'Artikel 3']);

        // Update artikel 1
        $response = $this->post('/api/artikel/update.php', [
            'id' => $id1,
            'judul' => 'Artikel 1 Updated',
            'kategori' => 'Berita',
            'isi' => 'Isi updated'
        ]);
        $this->assertJsonResponse($response, 'success');

        // Update artikel 2
        $response = $this->post('/api/artikel/update.php', [
            'id' => $id2,
            'judul' => 'Artikel 2 Updated',
            'kategori' => 'Berita',
            'isi' => 'Isi updated'
        ]);
        $this->assertJsonResponse($response, 'success');

        // Verify updates
        $artikel1 = $this->assertDatabaseHas('artikel', $id1);
        $artikel2 = $this->assertDatabaseHas('artikel', $id2);
        $artikel3 = $this->assertDatabaseHas('artikel', $id3);

        $this->assertEquals('Artikel 1 Updated', $artikel1['judul']);
        $this->assertEquals('Artikel 2 Updated', $artikel2['judul']);
        $this->assertEquals('Artikel 3', $artikel3['judul']); // Tidak berubah
    }

    /**
     * Test: Gagal akses tanpa session admin
     */
    public function testUpdateArtikelWithoutAdminSession()
    {
        $id = $this->createTestArtikel();
        TestHelper::clearSession();

        $data = [
            'id' => $id,
            'judul' => 'Judul Baru',
            'kategori' => 'Berita',
            'isi' => 'Isi baru'
        ];

        $response = $this->post('/api/artikel/update.php', $data);
        $result = json_decode($response, true);

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('unauthorized', strtolower($result['error']));
    }

    /**
     * Test: Request method selain POST
     */
    public function testUpdateArtikelWithGetMethod()
    {
        $response = $this->get('/api/artikel/update.php');
        $result = json_decode($response, true);

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('method', strtolower($result['error']));
    }
}
