<?php
use PHPUnit\Framework\TestCase;

class ProgramWhiteBoxTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('TEST_MODE')) {
            define('TEST_MODE', true);
        }
        putenv('DB_NAME=mahkota_test');

        if (!defined('PROJECT_ROOT')) {
            define('PROJECT_ROOT', dirname(__DIR__, 2));
        }

        // Aktifkan session di CLI agar $_SESSION tersedia konsisten
        if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
        if (!isset($_SESSION) || !is_array($_SESSION)) { $_SESSION = []; }
        $_SESSION['is_admin'] = true;

        // Setup database test via helper (jangan pre-include db.php di sini)
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

    private function fetchProgramById(int $id): ?array
    {
        // Gunakan DatabaseHelper agar tidak mengganggu require_once db.php di API
        require_once PROJECT_ROOT . '/tests/helpers/DatabaseHelper.php';
        $db = new DatabaseHelper();
        $conn = $db->getConnection();
        $stmt = $conn->prepare('SELECT * FROM program WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row ?: null;
    }

    public function testCreateProgram_Success_WithAllFields()
    {
        $resp = $this->callApi('/api/program/create.php', 'POST', [
            'nama_program' => 'Prog A',
            'bidang' => 'Pendidikan',
            'deskripsi' => 'Desc A',
            'status' => 'ongoing',
        ]);
        $data = json_decode($resp['body'], true);

        $this->assertEquals(200, $resp['code']);
        $this->assertTrue($data['success'] ?? false);
        $this->assertNotEmpty($data['id']);

        $row = $this->fetchProgramById((int)$data['id']);
        $this->assertNotNull($row);
        $this->assertEquals('Prog A', $row['nama_program']);
        $this->assertEquals('Pendidikan', $row['bidang']);
        $this->assertEquals('Desc A', $row['deskripsi']);
        $this->assertEquals('ongoing', $row['status']);
    }

    public function testCreateProgram_Validation_MissingNamaProgram()
    {
        $resp = $this->callApi('/api/program/create.php', 'POST', [
            'bidang' => 'Umum',
        ]);
        $data = json_decode($resp['body'], true);

        $this->assertEquals(200, $resp['code']);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Nama program dan bidang wajib diisi', $data['error']);
    }

    public function testCreateProgram_DefaultStatus_PlannedWhenOmitted()
    {
        $resp = $this->callApi('/api/program/create.php', 'POST', [
            'nama_program' => 'Prog Default',
            'bidang' => 'Kesehatan',
            'deskripsi' => 'Opsional',
            // status omitted
        ]);
        $data = json_decode($resp['body'], true);

        $this->assertTrue($data['success'] ?? false);
        $id = (int)$data['id'];
        $row = $this->fetchProgramById($id);
        $this->assertNotNull($row);
        $this->assertEquals('planned', $row['status']);
    }

    public function testUpdateProgram_Success()
    {
        // Create first
        $create = $this->callApi('/api/program/create.php', 'POST', [
            'nama_program' => 'Before',
            'bidang' => 'Ekonomi',
            'status' => 'planned',
        ]);
        $createData = json_decode($create['body'], true);
        $cid = (int)($createData['id'] ?? 0);
        $this->assertGreaterThan(0, $cid);

        // Update
        $update = $this->callApi('/api/program/update.php', 'POST', [
            'id' => $cid,
            'nama_program' => 'After',
            'bidang' => 'Kesehatan',
            'status' => 'ongoing',
        ]);
        $ud = json_decode($update['body'], true);
        $this->assertTrue($ud['success'] ?? false);

        $row = $this->fetchProgramById($cid);
        $this->assertNotNull($row);
        $this->assertEquals('After', $row['nama_program']);
        $this->assertEquals('Kesehatan', $row['bidang']);
        $this->assertEquals('ongoing', $row['status']);
    }

    public function testUpdateProgram_Validation_MissingId()
    {
        $resp = $this->callApi('/api/program/update.php', 'POST', [
            'nama_program' => 'X',
            'bidang' => 'Umum',
        ]);
        $data = json_decode($resp['body'], true);

        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('ID, nama program, dan bidang wajib diisi', $data['error']);
    }

    public function testUpdateProgram_NonexistentId_DoesNotChangeDatabase()
    {
        // Create baseline row
        $c = $this->callApi('/api/program/create.php', 'POST', [
            'nama_program' => 'Keep',
            'bidang' => 'Umum',
            'status' => 'planned',
        ]);
        $cData = json_decode($c['body'], true);
        $cid = (int)($cData['id'] ?? 0);

        // Update non-existent
        $resp = $this->callApi('/api/program/update.php', 'POST', [
            'id' => 999999,
            'nama_program' => 'Nope',
            'bidang' => 'Umum',
            'status' => 'completed',
        ]);
        $data = json_decode($resp['body'], true);

        // Current behavior returns success; verify DB baseline is unchanged
        $this->assertTrue($data['success'] ?? true);

        $row = $this->fetchProgramById($cid);
        $this->assertNotNull($row);
        $this->assertEquals('Keep', $row['nama_program']);
        $this->assertEquals('planned', $row['status']);
    }

    public function testDeleteProgram_Success()
    {
        $c = $this->callApi('/api/program/create.php', 'POST', [
            'nama_program' => 'ToDelete',
            'bidang' => 'Umum',
        ]);
        $cData = json_decode($c['body'], true);
        $cid = (int)($cData['id'] ?? 0);
        $this->assertGreaterThan(0, $cid);

        $d = $this->callApi('/api/program/delete.php', 'POST', ['id' => $cid]);
        $dd = json_decode($d['body'], true);
        $this->assertTrue($dd['success'] ?? false);

        $row = $this->fetchProgramById($cid);
        $this->assertNull($row);
    }

    public function testDeleteProgram_Validation_MissingId()
    {
        $resp = $this->callApi('/api/program/delete.php', 'POST', []);
        $data = json_decode($resp['body'], true);

        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('ID wajib diisi', $data['error']);
    }

    public function testReadProgram_FiltersAndCombined()
    {
        // Create multiple
        $cases = [
            ['nama_program' => 'Prog P-Kes', 'bidang' => 'Kesehatan', 'status' => 'planned'],
            ['nama_program' => 'Prog O-Kes', 'bidang' => 'Kesehatan', 'status' => 'ongoing'],
            ['nama_program' => 'Prog C-Eko', 'bidang' => 'Ekonomi',   'status' => 'completed'],
            ['nama_program' => 'Prog P-Pen', 'bidang' => 'Pendidikan','status' => 'planned'],
        ];
        foreach ($cases as $p) {
            $this->callApi('/api/program/create.php', 'POST', $p);
        }

        $respAll = $this->callApi('/api/program/read.php', 'GET', [], []);
        $all = json_decode($respAll['body'], true);
        $this->assertIsArray($all);
        $this->assertGreaterThanOrEqual(4, count($all));

        $respPlanned = $this->callApi('/api/program/read.php', 'GET', [], ['status' => 'planned']);
        $planned = json_decode($respPlanned['body'], true);
        $this->assertIsArray($planned);
        $this->assertGreaterThanOrEqual(2, count($planned));

        $respKes = $this->callApi('/api/program/read.php', 'GET', [], ['bidang' => 'Kesehatan']);
        $kes = json_decode($respKes['body'], true);
        $this->assertIsArray($kes);
        $this->assertGreaterThanOrEqual(2, count($kes));

        $respCombo = $this->callApi('/api/program/read.php', 'GET', [], ['status' => 'planned', 'bidang' => 'Kesehatan']);
        $combo = json_decode($respCombo['body'], true);
        $this->assertIsArray($combo);
        $this->assertEquals(1, count($combo));
    }

    public function testReadProgram_FilterNonexistent_ReturnsEmpty()
    {
        $respEmpty = $this->callApi('/api/program/read.php', 'GET', [], ['status' => 'nonexistent']);
        $res = json_decode($respEmpty['body'], true);
        $this->assertIsArray($res);
        $this->assertEquals(0, count($res));
    }

    public function testReadProgram_InjectionAttempt_IgnoredByPreparedStatement()
    {
        // Attempt injection-like status value
        $respInj = $this->callApi('/api/program/read.php', 'GET', [], ['status' => "' OR 1=1 --"]);
        $inj = json_decode($respInj['body'], true);
        $this->assertIsArray($inj);
        // Should not match; unless such literal status exists, expect zero
        $this->assertEquals(0, count($inj));
    }

    public function testUnauthorizedEndpoints_Return403()
    {
        $_SESSION['is_admin'] = false;

        $c = $this->callApi('/api/program/create.php', 'POST', ['nama_program' => 'X', 'bidang' => 'Umum']);
        $cBody = json_decode($c['body'], true);
        $this->assertEquals(403, $c['code']);
        $this->assertEquals('Unauthorized', $cBody['error'] ?? null);

        $r = $this->callApi('/api/program/read.php', 'GET', [], []);
        $rBody = json_decode($r['body'], true);
        $this->assertEquals(403, $r['code']);
        $this->assertEquals('Unauthorized', $rBody['error'] ?? null);
    }

    public function testCreateProgram_NonPost_ReturnsInvalidMethod()
    {
        $resp = $this->callApi('/api/program/create.php', 'GET', [], []);
        $data = json_decode($resp['body'], true);

        $this->assertEquals('Invalid request method', $data['error'] ?? null);
    }

    public function testReadProgram_OrderByCreatedAtDesc()
    {
        $this->callApi('/api/program/create.php', 'POST', ['nama_program' => 'Older', 'bidang' => 'Umum', 'status' => 'planned']);
        $this->callApi('/api/program/create.php', 'POST', ['nama_program' => 'Newer', 'bidang' => 'Umum', 'status' => 'planned']);

        $respList = $this->callApi('/api/program/read.php', 'GET', [], []);
        $list = json_decode($respList['body'], true);
        $this->assertIsArray($list);
        $this->assertGreaterThanOrEqual(2, count($list));
        $this->assertEquals('Newer', $list[0]['nama_program'] ?? null);
    }
}