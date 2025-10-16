<?php
/**
 * Test Case: Artikel Create API
 * Testing endpoint: /api/artikel/create.php
 */

require_once __DIR__ . '/../helpers/ApiTestCase.php';

class ArtikelCreateTest extends ApiTestCase
{
    /**
     * Test: Berhasil membuat artikel dengan data lengkap
     */
    public function testCreateArtikelSuccess()
    {
        $data = [
            'judul' => 'Artikel Test Baru',
            'kategori' => 'Berita',
            'isi' => 'Ini adalah isi artikel yang sangat panjang dan detail untuk testing purposes.'
        ];

        $response = $this->post('/api/artikel/create.php', $data);
        $result = $this->assertJsonResponse($response, 'success');

        $this->assertArrayHasKey('message', $result);
        $this->assertStringContainsString('berhasil', strtolower($result['message']));

        // Verify data tersimpan di database
        $this->assertRecordCount('artikel', 1);
    }

    /**
     * Test: Gagal membuat artikel tanpa judul
     */
    public function testCreateArtikelWithoutJudul()
    {
        $data = [
            'kategori' => 'Berita',
            'isi' => 'Isi artikel tanpa judul'
        ];

        $response = $this->post('/api/artikel/create.php', $data);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('judul', strtolower($result['error']));

        // Verify tidak ada data tersimpan
        $this->assertRecordCount('artikel', 0);
    }

    /**
     * Test: Gagal membuat artikel tanpa kategori
     */
    public function testCreateArtikelWithoutKategori()
    {
        $data = [
            'judul' => 'Artikel Tanpa Kategori',
            'isi' => 'Isi artikel tanpa kategori'
        ];

        $response = $this->post('/api/artikel/create.php', $data);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
        $this->assertRecordCount('artikel', 0);
    }

    /**
     * Test: Gagal membuat artikel tanpa isi
     */
    public function testCreateArtikelWithoutIsi()
    {
        $data = [
            'judul' => 'Artikel Tanpa Isi',
            'kategori' => 'Berita'
        ];

        $response = $this->post('/api/artikel/create.php', $data);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
        $this->assertRecordCount('artikel', 0);
    }

    /**
     * Test: Membuat artikel dengan judul panjang
     */
    public function testCreateArtikelWithLongJudul()
    {
        $data = [
            'judul' => str_repeat('A', 250), // 250 karakter
            'kategori' => 'Berita',
            'isi' => 'Isi artikel dengan judul panjang'
        ];

        $response = $this->post('/api/artikel/create.php', $data);
        $result = $this->assertJsonResponse($response, 'success');

        $this->assertRecordCount('artikel', 1);
    }

    /**
     * Test: Membuat artikel dengan karakter khusus
     */
    public function testCreateArtikelWithSpecialCharacters()
    {
        $data = [
            'judul' => 'Artikel & Test <script>alert("XSS")</script>',
            'kategori' => 'Berita',
            'isi' => 'Isi dengan karakter khusus: @#$%^&*()'
        ];

        $response = $this->post('/api/artikel/create.php', $data);
        $result = $this->assertJsonResponse($response, 'success');

        $this->assertRecordCount('artikel', 1);
    }

    /**
     * Test: Membuat multiple artikel
     */
    public function testCreateMultipleArtikel()
    {
        for ($i = 1; $i <= 5; $i++) {
            $data = [
                'judul' => "Artikel Test $i",
                'kategori' => 'Berita',
                'isi' => "Isi artikel nomor $i"
            ];

            $response = $this->post('/api/artikel/create.php', $data);
            $this->assertJsonResponse($response, 'success');
        }

        $this->assertRecordCount('artikel', 5);
    }

    /**
     * Test: Gagal akses tanpa session admin
     */
    public function testCreateArtikelWithoutAdminSession()
    {
        TestHelper::clearSession();

        $data = [
            'judul' => 'Artikel Test',
            'kategori' => 'Berita',
            'isi' => 'Isi artikel'
        ];

        $response = $this->post('/api/artikel/create.php', $data);
        $result = json_decode($response, true);

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('unauthorized', strtolower($result['error']));
    }

    /**
     * Test: Request method selain POST
     */
    public function testCreateArtikelWithGetMethod()
    {
        $response = $this->get('/api/artikel/create.php');
        $result = json_decode($response, true);

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('method', strtolower($result['error']));
    }
}
