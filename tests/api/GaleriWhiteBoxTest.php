<?php
use PHPUnit\Framework\TestCase;

class GaleriWhiteBoxTest extends TestCase
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

    private function fetchGaleriById(int $id): ?array
    {
        require_once PROJECT_ROOT . '/tests/helpers/DatabaseHelper.php';
        $db = new DatabaseHelper();
        $conn = $db->getConnection();
        $stmt = $conn->prepare('SELECT * FROM galeri WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row ?: null;
    }

    private function createGaleriDirect(array $fields = []): int
    {
        require_once PROJECT_ROOT . '/tests/helpers/DatabaseHelper.php';
        $db = new DatabaseHelper();
        $conn = $db->getConnection();
        $judul = $fields['judul'] ?? 'Galeri Direct';
        $deskripsi = $fields['deskripsi'] ?? 'Desc';
        $kategori = $fields['kategori'] ?? 'Kegiatan';
        $gambar = $fields['gambar'] ?? 'uploads/galeri/dummy.jpg';
        $stmt = $conn->prepare('INSERT INTO galeri (judul, deskripsi, kategori, gambar, tanggal) VALUES (?, ?, ?, ?, NOW())');
        $stmt->bind_param('ssss', $judul, $deskripsi, $kategori, $gambar);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return (int)$id;
    }

    public function testCreateGaleri_Validation_MissingImage()
    {
        $resp = $this->callApi('/api/galeri/create.php', 'POST', [
            'judul' => 'Galeri Tanpa Gambar',
            'kategori' => 'Kegiatan',
            'deskripsi' => 'Desc',
        ]);
        $data = json_decode($resp['body'], true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Gambar wajib diupload', $data['error']);
    }

    public function testReadGaleri_OrderByTanggalDesc()
    {
        $this->createGaleriDirect(['judul' => 'Older']);
        $this->createGaleriDirect(['judul' => 'Newer']);

        $respList = $this->callApi('/api/galeri/read.php', 'GET', [], []);
        $list = json_decode($respList['body'], true);
        $this->assertIsArray($list);
        $this->assertGreaterThanOrEqual(2, count($list));
        $this->assertEquals('Newer', $list[0]['judul'] ?? null);
    }

    public function testUpdateGaleri_Success()
    {
        $id = $this->createGaleriDirect(['judul' => 'Judul Lama', 'kategori' => 'Kegiatan']);
        $this->assertGreaterThan(0, $id);

        $resp = $this->callApi('/api/galeri/update.php', 'POST', [
            'id' => $id,
            'judul' => 'Judul Baru',
            'kategori' => 'Dokumentasi',
            'deskripsi' => 'Diubah',
        ]);
        $data = json_decode($resp['body'], true);
        $this->assertTrue($data['success'] ?? false);

        $row = $this->fetchGaleriById($id);
        $this->assertNotNull($row);
        $this->assertEquals('Judul Baru', $row['judul']);
        $this->assertEquals('Dokumentasi', $row['kategori']);
    }

    public function testUpdateGaleri_Validation_MissingId()
    {
        $resp = $this->callApi('/api/galeri/update.php', 'POST', [
            'judul' => 'X',
            'kategori' => 'Kegiatan',
        ]);
        $data = json_decode($resp['body'], true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('ID, judul, dan kategori wajib diisi', $data['error']);
    }

    public function testDeleteGaleri_Success()
    {
        $id = $this->createGaleriDirect(['judul' => 'Akan Dihapus']);
        $this->assertGreaterThan(0, $id);

        $resp = $this->callApi('/api/galeri/delete.php', 'POST', ['id' => $id]);
        $data = json_decode($resp['body'], true);
        $this->assertTrue($data['success'] ?? false);

        $row = $this->fetchGaleriById($id);
        $this->assertNull($row);
    }

    public function testDeleteGaleri_Validation_MissingId()
    {
        $resp = $this->callApi('/api/galeri/delete.php', 'POST', []);
        $data = json_decode($resp['body'], true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('ID wajib diisi', $data['error']);
    }

    public function testGaleriResponseStructure()
    {
        $this->createGaleriDirect();
        $resp = $this->callApi('/api/galeri/read.php', 'GET', [], []);
        $data = json_decode($resp['body'], true);
        $galeri = $data[0];
        $this->assertArrayHasKey('id', $galeri);
        $this->assertArrayHasKey('judul', $galeri);
        $this->assertArrayHasKey('deskripsi', $galeri);
        $this->assertArrayHasKey('kategori', $galeri);
        $this->assertArrayHasKey('gambar', $galeri);
        $this->assertArrayHasKey('tanggal', $galeri);
    }

    public function testUnauthorizedEndpoints_Return403()
    {
        $_SESSION['is_admin'] = false;

        $c = $this->callApi('/api/galeri/create.php', 'POST', ['judul' => 'X', 'kategori' => 'Kegiatan']);
        $cBody = json_decode($c['body'], true);
        $this->assertEquals(403, $c['code']);
        $this->assertEquals('Unauthorized', $cBody['error'] ?? null);

        $r = $this->callApi('/api/galeri/read.php', 'GET', [], []);
        $rBody = json_decode($r['body'], true);
        $this->assertEquals(403, $r['code']);
        $this->assertEquals('Unauthorized', $rBody['error'] ?? null);

        $u = $this->callApi('/api/galeri/update.php', 'POST', ['id' => 1]);
        $uBody = json_decode($u['body'], true);
        $this->assertEquals(403, $u['code']);
        $this->assertEquals('Unauthorized', $uBody['error'] ?? null);

        $d = $this->callApi('/api/galeri/delete.php', 'POST', ['id' => 1]);
        $dBody = json_decode($d['body'], true);
        $this->assertEquals(403, $d['code']);
        $this->assertEquals('Unauthorized', $dBody['error'] ?? null);
    }

    public function testCreateGaleri_NonPost_ReturnsInvalidMethod()
    {
        $resp = $this->callApi('/api/galeri/create.php', 'GET', [], []);
        $data = json_decode($resp['body'], true);
        $this->assertEquals('Invalid request method', $data['error'] ?? null);
    }
}