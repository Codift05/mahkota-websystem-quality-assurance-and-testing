<?php
use PHPUnit\Framework\TestCase;

class ArtikelWhiteBoxTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('TEST_MODE')) { define('TEST_MODE', true); }
        putenv('DB_NAME=mahkota_test');

        if (!defined('PROJECT_ROOT')) { define('PROJECT_ROOT', dirname(__DIR__, 2)); }

        if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
        if (!isset($_SESSION) || !is_array($_SESSION)) { $_SESSION = []; }
        $_SESSION['is_admin'] = true;

        require_once PROJECT_ROOT . '/tests/helpers/DatabaseHelper.php';
        $dbHelper = new DatabaseHelper();
        $dbHelper->setupTestDatabase();
        $dbHelper->cleanDatabase();
    }

    private function callApi(string $relativePath, string $method, array $post = [], array $get = []): array
    {
        $_SERVER['REQUEST_METHOD'] = strtoupper($method);
        $_POST = $post;
        $_GET = $get;

        $prevCode = http_response_code();
        ob_start();
        include PROJECT_ROOT . $relativePath;
        $out = ob_get_clean();
        $code = http_response_code();
        return ['body' => $out, 'code' => $code ?: ($prevCode ?: 200)];
    }

    private function fetchArtikelById(int $id): ?array
    {
        require_once PROJECT_ROOT . '/tests/helpers/DatabaseHelper.php';
        $db = new DatabaseHelper();
        $conn = $db->getConnection();
        $stmt = $conn->prepare('SELECT * FROM artikel WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row ?: null;
    }

    public function testCreateArtikel_Success_WithAllFields()
    {
        $resp = $this->callApi('/api/artikel/create.php', 'POST', [
            'judul' => 'Artikel A',
            'kategori' => 'Umum',
            'isi' => 'Konten artikel A',
        ]);
        $data = json_decode($resp['body'], true);

        $this->assertEquals(200, $resp['code']);
        $this->assertTrue($data['success'] ?? false);
        $this->assertNotEmpty($data['id']);

        $row = $this->fetchArtikelById((int)$data['id']);
        $this->assertNotNull($row);
        $this->assertEquals('Artikel A', $row['judul']);
        $this->assertEquals('Umum', $row['kategori']);
        $this->assertEquals('Konten artikel A', $row['isi']);
    }

    public function testCreateArtikel_Validation_MissingJudul()
    {
        $resp = $this->callApi('/api/artikel/create.php', 'POST', [
            'kategori' => 'Berita',
            // judul & isi omitted
        ]);
        $data = json_decode($resp['body'], true);

        $this->assertEquals(200, $resp['code']);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Judul, kategori, dan isi wajib diisi', $data['error']);
    }

    public function testUpdateArtikel_Success()
    {
        $create = $this->callApi('/api/artikel/create.php', 'POST', [
            'judul' => 'Sebelum',
            'kategori' => 'Umum',
            'isi' => 'Isi awal',
        ]);
        $cData = json_decode($create['body'], true);
        $cid = (int)($cData['id'] ?? 0);
        $this->assertGreaterThan(0, $cid);

        $update = $this->callApi('/api/artikel/update.php', 'POST', [
            'id' => $cid,
            'judul' => 'Sesudah',
            'kategori' => 'Berita',
            'isi' => 'Isi diubah',
        ]);
        $uData = json_decode($update['body'], true);
        $this->assertTrue($uData['success'] ?? false);

        $row = $this->fetchArtikelById($cid);
        $this->assertNotNull($row);
        $this->assertEquals('Sesudah', $row['judul']);
        $this->assertEquals('Berita', $row['kategori']);
        $this->assertEquals('Isi diubah', $row['isi']);
    }

    public function testUpdateArtikel_Validation_MissingId()
    {
        $resp = $this->callApi('/api/artikel/update.php', 'POST', [
            'judul' => 'X',
            'kategori' => 'Umum',
            'isi' => 'Konten',
        ]);
        $data = json_decode($resp['body'], true);

        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('ID, judul, kategori, dan isi wajib diisi', $data['error']);
    }

    public function testDeleteArtikel_Success()
    {
        $c = $this->callApi('/api/artikel/create.php', 'POST', [
            'judul' => 'Hapus',
            'kategori' => 'Umum',
            'isi' => 'Isi singkat',
        ]);
        $cData = json_decode($c['body'], true);
        $cid = (int)($cData['id'] ?? 0);
        $this->assertGreaterThan(0, $cid);

        $d = $this->callApi('/api/artikel/delete.php', 'POST', ['id' => $cid]);
        $dd = json_decode($d['body'], true);
        $this->assertTrue($dd['success'] ?? false);

        $row = $this->fetchArtikelById($cid);
        $this->assertNull($row);
    }

    public function testDeleteArtikel_Validation_MissingId()
    {
        $resp = $this->callApi('/api/artikel/delete.php', 'POST', []);
        $data = json_decode($resp['body'], true);

        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('ID wajib diisi', $data['error']);
    }

    public function testReadArtikel_OrderByTanggalDesc()
    {
        $this->callApi('/api/artikel/create.php', 'POST', ['judul' => 'Older', 'kategori' => 'Umum', 'isi' => 'X']);
        $this->callApi('/api/artikel/create.php', 'POST', ['judul' => 'Newer', 'kategori' => 'Umum', 'isi' => 'Y']);

        $respList = $this->callApi('/api/artikel/read.php', 'GET', [], []);
        $list = json_decode($respList['body'], true);
        $this->assertIsArray($list);
        $this->assertGreaterThanOrEqual(2, count($list));
        $this->assertEquals('Newer', $list[0]['judul'] ?? null);
    }

    public function testUnauthorizedEndpoints_Return403()
    {
        $_SESSION['is_admin'] = false;

        $c = $this->callApi('/api/artikel/create.php', 'POST', ['judul' => 'X', 'kategori' => 'Umum', 'isi' => 'Z']);
        $cBody = json_decode($c['body'], true);
        $this->assertEquals(403, $c['code']);
        $this->assertEquals('Unauthorized', $cBody['error'] ?? null);

        $r = $this->callApi('/api/artikel/read.php', 'GET', [], []);
        $rBody = json_decode($r['body'], true);
        $this->assertEquals(403, $r['code']);
        $this->assertEquals('Unauthorized', $rBody['error'] ?? null);
    }

    public function testCreateArtikel_NonPost_ReturnsInvalidMethod()
    {
        $resp = $this->callApi('/api/artikel/create.php', 'GET', [], []);
        $data = json_decode($resp['body'], true);
        $this->assertEquals('Invalid request method', $data['error'] ?? null);
    }
}