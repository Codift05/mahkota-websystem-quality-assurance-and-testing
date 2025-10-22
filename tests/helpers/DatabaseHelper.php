<?php
/**
 * Database Helper Class
 * Mengelola database testing
 */

class DatabaseHelper
{
    private $conn;
    private $testDbName;

    public function __construct()
    {
        $this->testDbName = getenv('DB_NAME') ?: 'mahkota_test';
        $this->connect();
    }

    /**
     * Connect ke database
     */
    private function connect()
    {
        $host = getenv('DB_HOST') ?: 'localhost';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';

        $this->conn = new mysqli($host, $user, $pass);
        
        if ($this->conn->connect_error) {
            throw new Exception('Database connection failed: ' . $this->conn->connect_error);
        }

        // Pastikan koneksi menggunakan database test jika sudah ada
        // Ini mencegah error "No database selected" saat instance baru dibuat di setUp()
        // Database mungkin belum ada di bootstrap awal, namun akan dibuat di setupTestDatabase().
        // Seleksi ini aman karena akan diulang lagi saat setupTestDatabase dipanggil.
        @$this->conn->select_db($this->testDbName);
        $this->conn->set_charset('utf8mb4');
    }

    /**
     * Setup test database
     */
    public function setupTestDatabase()
    {
        // Create test database if not exists
        $this->conn->query("CREATE DATABASE IF NOT EXISTS {$this->testDbName}");
        $this->conn->select_db($this->testDbName);
        $this->conn->set_charset('utf8mb4');

        // Create tables
        $this->createTables();
    }

    /**
     * Create necessary tables
     */
    private function createTables()
    {
        // Table: artikel
        $sql = "CREATE TABLE IF NOT EXISTS artikel (
            id INT AUTO_INCREMENT PRIMARY KEY,
            judul VARCHAR(255) NOT NULL,
            kategori VARCHAR(100) NOT NULL,
            isi TEXT NOT NULL,
            gambar VARCHAR(255),
            tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_kategori (kategori),
            INDEX idx_tanggal (tanggal)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->conn->query($sql);

        // Table: galeri
        $sql = "CREATE TABLE IF NOT EXISTS galeri (
            id INT AUTO_INCREMENT PRIMARY KEY,
            judul VARCHAR(255) NOT NULL,
            deskripsi TEXT,
            kategori VARCHAR(100),
            gambar VARCHAR(255) NOT NULL,
            tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_kategori (kategori),
            INDEX idx_tanggal (tanggal)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->conn->query($sql);

        // Table: program
        $sql = "CREATE TABLE IF NOT EXISTS program (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_program VARCHAR(255) NOT NULL,
            bidang VARCHAR(100) NOT NULL,
            deskripsi TEXT,
            tanggal_mulai DATE,
            tanggal_selesai DATE,
            status ENUM('planned', 'ongoing', 'completed') DEFAULT 'planned',
            gambar VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_bidang (bidang),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->conn->query($sql);

        // Table: admin (untuk testing authentication)
        $sql = "CREATE TABLE IF NOT EXISTS admin (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->conn->query($sql);
    }

    /**
     * Clean all test data
     */
    public function cleanDatabase()
    {
        $tables = ['artikel', 'galeri', 'program', 'admin'];
        foreach ($tables as $table) {
            $this->conn->query("TRUNCATE TABLE $table");
        }
    }

    /**
     * Drop test database
     */
    public function dropTestDatabase()
    {
        $this->conn->query("DROP DATABASE IF EXISTS {$this->testDbName}");
    }

    /**
     * Get connection
     */
    public function getConnection()
    {
        if (!$this->conn->ping()) {
            $this->connect();
            $this->conn->select_db($this->testDbName);
        }
        return $this->conn;
    }

    /**
     * Insert test data
     */
    public function insertTestData($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        
        $types = str_repeat('s', count($data));
        $values = array_values($data);
        $stmt->bind_param($types, ...$values);
        
        $stmt->execute();
        $insertId = $stmt->insert_id;
        $stmt->close();
        
        return $insertId;
    }

    /**
     * Get test data by ID
     */
    public function getById($table, $id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        return $data;
    }

    /**
     * Count records in table
     */
    public function countRecords($table, $where = '')
    {
        $sql = "SELECT COUNT(*) as total FROM $table";
        if ($where) {
            $sql .= " WHERE $where";
        }
        
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        
        return (int)$row['total'];
    }
}
