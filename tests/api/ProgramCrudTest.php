<?php
/**
 * Test Case: Program CRUD API
 * Testing endpoints: /api/program/*.php
 */

require_once __DIR__ . '/../helpers/ApiTestCase.php';

class ProgramCrudTest extends ApiTestCase
{
    /**
     * Test: Berhasil membuat program
     */
    public function testCreateProgramSuccess()
    {
        $data = [
            'nama_program' => 'Program Test Baru',
            'bidang' => 'Pendidikan',
            'deskripsi' => 'Deskripsi program untuk testing',
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-12-31',
            'status' => 'planned'
        ];

        $response = $this->post('/api/program/create.php', $data);
        $result = $this->assertJsonResponse($response, 'success');

        $this->assertArrayHasKey('message', $result);
        $this->assertRecordCount('program', 1);
    }

    /**
     * Test: Gagal membuat program tanpa nama
     */
    public function testCreateProgramWithoutNama()
    {
        $data = [
            'bidang' => 'Pendidikan',
            'deskripsi' => 'Deskripsi'
        ];

        $response = $this->post('/api/program/create.php', $data);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('nama', strtolower($result['error']));
        $this->assertRecordCount('program', 0);
    }

    /**
     * Test: Gagal membuat program tanpa bidang
     */
    public function testCreateProgramWithoutBidang()
    {
        $data = [
            'nama_program' => 'Program Tanpa Bidang',
            'deskripsi' => 'Deskripsi'
        ];

        $response = $this->post('/api/program/create.php', $data);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('bidang', strtolower($result['error']));
    }

    /**
     * Test: Membuat program dengan status berbeda
     */
    public function testCreateProgramWithDifferentStatus()
    {
        $statuses = ['planned', 'ongoing', 'completed'];

        foreach ($statuses as $status) {
            $data = [
                'nama_program' => "Program $status",
                'bidang' => 'Pendidikan',
                'status' => $status
            ];

            $response = $this->post('/api/program/create.php', $data);
            $this->assertJsonResponse($response, 'success');
        }

        $this->assertRecordCount('program', 3);
    }

    /**
     * Test: Berhasil membaca list program
     */
    public function testReadProgramList()
    {
        // Insert test data
        $this->createTestProgram(['nama_program' => 'Program 1']);
        $this->createTestProgram(['nama_program' => 'Program 2']);
        $this->createTestProgram(['nama_program' => 'Program 3']);

        $response = $this->get('/api/program/read.php');
        $data = json_decode($response, true);

        $this->assertIsArray($data);
        $this->assertCount(3, $data);
    }

    /**
     * Test: Program dengan berbagai bidang
     */
    public function testProgramWithDifferentBidang()
    {
        $this->createTestProgram(['bidang' => 'Pendidikan']);
        $this->createTestProgram(['bidang' => 'Kesehatan']);
        $this->createTestProgram(['bidang' => 'Sosial']);
        $this->createTestProgram(['bidang' => 'Ekonomi']);

        $response = $this->get('/api/program/read.php');
        $data = json_decode($response, true);

        $bidangs = array_column($data, 'bidang');
        $this->assertContains('Pendidikan', $bidangs);
        $this->assertContains('Kesehatan', $bidangs);
        $this->assertContains('Sosial', $bidangs);
        $this->assertContains('Ekonomi', $bidangs);
    }

    /**
     * Test: Response structure program
     */
    public function testProgramResponseStructure()
    {
        $this->createTestProgram();

        $response = $this->get('/api/program/read.php');
        $data = json_decode($response, true);

        $program = $data[0];
        $this->assertArrayHasKey('id', $program);
        $this->assertArrayHasKey('nama_program', $program);
        $this->assertArrayHasKey('bidang', $program);
        $this->assertArrayHasKey('deskripsi', $program);
        $this->assertArrayHasKey('tanggal_mulai', $program);
        $this->assertArrayHasKey('tanggal_selesai', $program);
        $this->assertArrayHasKey('status', $program);
        $this->assertArrayHasKey('gambar', $program);
        $this->assertArrayHasKey('created_at', $program);
    }

    /**
     * Test: Berhasil update program
     */
    public function testUpdateProgramSuccess()
    {
        $id = $this->createTestProgram([
            'nama_program' => 'Program Lama',
            'bidang' => 'Pendidikan',
            'status' => 'planned'
        ]);

        $data = [
            'id' => $id,
            'nama_program' => 'Program Baru',
            'bidang' => 'Kesehatan',
            'deskripsi' => 'Deskripsi updated',
            'status' => 'ongoing'
        ];

        $response = $this->post('/api/program/update.php', $data);
        $result = $this->assertJsonResponse($response, 'success');

        // Verify data terupdate
        $program = $this->assertDatabaseHas('program', $id);
        $this->assertEquals('Program Baru', $program['nama_program']);
        $this->assertEquals('Kesehatan', $program['bidang']);
        $this->assertEquals('ongoing', $program['status']);
    }

    /**
     * Test: Update status program dari planned ke completed
     */
    public function testUpdateProgramStatusProgression()
    {
        $id = $this->createTestProgram([
            'nama_program' => 'Program Test',
            'bidang' => 'Pendidikan',
            'status' => 'planned'
        ]);

        // Update ke ongoing
        $response = $this->post('/api/program/update.php', [
            'id' => $id,
            'nama_program' => 'Program Test',
            'bidang' => 'Pendidikan',
            'status' => 'ongoing'
        ]);
        $this->assertJsonResponse($response, 'success');

        $program = $this->assertDatabaseHas('program', $id);
        $this->assertEquals('ongoing', $program['status']);

        // Update ke completed
        $response = $this->post('/api/program/update.php', [
            'id' => $id,
            'nama_program' => 'Program Test',
            'bidang' => 'Pendidikan',
            'status' => 'completed'
        ]);
        $this->assertJsonResponse($response, 'success');

        $program = $this->assertDatabaseHas('program', $id);
        $this->assertEquals('completed', $program['status']);
    }

    /**
     * Test: Gagal update program tanpa ID
     */
    public function testUpdateProgramWithoutId()
    {
        $data = [
            'nama_program' => 'Program Baru',
            'bidang' => 'Pendidikan'
        ];

        $response = $this->post('/api/program/update.php', $data);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
    }

    /**
     * Test: Berhasil delete program
     */
    public function testDeleteProgramSuccess()
    {
        $id = $this->createTestProgram([
            'nama_program' => 'Program Yang Akan Dihapus'
        ]);

        $this->assertDatabaseHas('program', $id);

        $response = $this->post('/api/program/delete.php', ['id' => $id]);
        $result = $this->assertJsonResponse($response, 'success');

        $this->assertDatabaseMissing('program', $id);
    }

    /**
     * Test: Gagal delete program tanpa ID
     */
    public function testDeleteProgramWithoutId()
    {
        $response = $this->post('/api/program/delete.php', []);
        $result = $this->assertJsonResponse($response, 'error');

        $this->assertArrayHasKey('error', $result);
    }

    /**
     * Test: Filter program berdasarkan status
     */
    public function testFilterProgramByStatus()
    {
        $this->createTestProgram(['status' => 'planned']);
        $this->createTestProgram(['status' => 'planned']);
        $this->createTestProgram(['status' => 'ongoing']);
        $this->createTestProgram(['status' => 'completed']);

        $this->assertRecordCount('program', 4);
        $this->assertRecordCount('program', 2, "status = 'planned'");
        $this->assertRecordCount('program', 1, "status = 'ongoing'");
        $this->assertRecordCount('program', 1, "status = 'completed'");
    }

    /**
     * Test: Program dengan tanggal mulai dan selesai
     */
    public function testProgramWithDates()
    {
        $id = $this->createTestProgram([
            'nama_program' => 'Program Dengan Tanggal',
            'bidang' => 'Pendidikan',
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-12-31'
        ]);

        $program = $this->assertDatabaseHas('program', $id);
        $this->assertEquals('2025-01-01', $program['tanggal_mulai']);
        $this->assertEquals('2025-12-31', $program['tanggal_selesai']);
    }

    /**
     * Test: CRUD lengkap untuk satu program
     */
    public function testCompleteCrudCycle()
    {
        // CREATE
        $id = $this->createTestProgram([
            'nama_program' => 'Test CRUD Program',
            'bidang' => 'Pendidikan',
            'status' => 'planned'
        ]);
        $this->assertDatabaseHas('program', $id);

        // READ
        $response = $this->get('/api/program/read.php');
        $data = json_decode($response, true);
        $this->assertCount(1, $data);
        $this->assertEquals('Test CRUD Program', $data[0]['nama_program']);

        // UPDATE
        $updateData = [
            'id' => $id,
            'nama_program' => 'Test CRUD Program Updated',
            'bidang' => 'Kesehatan',
            'status' => 'ongoing'
        ];
        $response = $this->post('/api/program/update.php', $updateData);
        $this->assertJsonResponse($response, 'success');

        $program = $this->assertDatabaseHas('program', $id);
        $this->assertEquals('Test CRUD Program Updated', $program['nama_program']);
        $this->assertEquals('ongoing', $program['status']);

        // DELETE
        $response = $this->post('/api/program/delete.php', ['id' => $id]);
        $this->assertJsonResponse($response, 'success');
        $this->assertDatabaseMissing('program', $id);
    }

    /**
     * Test: Gagal akses tanpa session admin
     */
    public function testProgramAccessWithoutAdminSession()
    {
        TestHelper::clearSession();

        // Test create
        $response = $this->post('/api/program/create.php', [
            'nama_program' => 'Test',
            'bidang' => 'Pendidikan'
        ]);
        $result = json_decode($response, true);
        $this->assertArrayHasKey('error', $result);

        // Test read
        $response = $this->get('/api/program/read.php');
        $result = json_decode($response, true);
        $this->assertArrayHasKey('error', $result);

        // Test update
        $response = $this->post('/api/program/update.php', ['id' => 1]);
        $result = json_decode($response, true);
        $this->assertArrayHasKey('error', $result);

        // Test delete
        $response = $this->post('/api/program/delete.php', ['id' => 1]);
        $result = json_decode($response, true);
        $this->assertArrayHasKey('error', $result);
    }

    /**
     * Test: Performance dengan banyak program
     */
    public function testProgramPerformance()
    {
        // Create 50 program
        for ($i = 1; $i <= 50; $i++) {
            $this->createTestProgram([
                'nama_program' => "Program $i",
                'bidang' => 'Pendidikan'
            ]);
        }

        $startTime = microtime(true);
        $response = $this->get('/api/program/read.php');
        $endTime = microtime(true);

        $executionTime = $endTime - $startTime;

        $data = json_decode($response, true);
        $this->assertCount(50, $data);

        // Assert execution time kurang dari 1 detik
        $this->assertLessThan(1.0, $executionTime, 'Read operation should complete within 1 second');
    }
}
