# Quality Assurance Testing - Mahkota Web System

Dokumentasi lengkap untuk unit testing dan integration testing sistem CRUD Mahkota.

## 📋 Daftar Isi

- [Overview](#overview)
- [Struktur Testing](#struktur-testing)
- [Instalasi](#instalasi)
- [Menjalankan Tests](#menjalankan-tests)
- [Test Coverage](#test-coverage)
- [Penjelasan Test Cases](#penjelasan-test-cases)

## 🎯 Overview

Testing suite ini dibuat untuk memastikan kualitas dan reliability dari sistem CRUD Mahkota Web System. Testing mencakup:

- **Unit Testing**: Testing individual API endpoints
- **Integration Testing**: Testing interaksi antar modul
- **Performance Testing**: Testing performa sistem dengan load

### Framework & Tools

- **PHPUnit 9.5**: Framework testing untuk PHP
- **Custom Test Helpers**: Helper classes untuk mempermudah testing
- **Database Testing**: Isolated test database untuk testing

## 📁 Struktur Testing

```
tests/
├── api/                          # Unit tests untuk API endpoints
│   ├── ArtikelCreateTest.php    # Test create artikel
│   ├── ArtikelReadTest.php      # Test read artikel
│   ├── ArtikelUpdateTest.php    # Test update artikel
│   ├── ArtikelDeleteTest.php    # Test delete artikel
│   ├── GaleriCrudTest.php       # Test CRUD galeri
│   └── ProgramCrudTest.php      # Test CRUD program
│
├── integration/                  # Integration tests
│   └── SystemIntegrationTest.php # Test integrasi sistem
│
├── helpers/                      # Helper classes
│   ├── TestHelper.php           # Utility functions
│   ├── DatabaseHelper.php       # Database management
│   └── ApiTestCase.php          # Base test case class
│
├── bootstrap.php                 # Bootstrap file untuk PHPUnit
├── phpunit.xml                   # Konfigurasi PHPUnit
├── composer.json                 # Dependencies
├── run_tests.bat                 # Script untuk Windows
├── run_tests.sh                  # Script untuk Linux/Mac
└── README.md                     # Dokumentasi ini

```

## 🚀 Instalasi

### 1. Install Composer Dependencies

Jalankan command berikut di folder `tests/`:

```bash
composer install
```

### 2. Setup Test Database

Testing menggunakan database terpisah (`mahkota_test`) untuk menghindari konflik dengan data production.

Database akan otomatis dibuat saat menjalankan tests pertama kali.

**Konfigurasi Database** (di `phpunit.xml`):
```xml
<php>
    <env name="DB_HOST" value="localhost"/>
    <env name="DB_USER" value="root"/>
    <env name="DB_PASS" value=""/>
    <env name="DB_NAME" value="mahkota_test"/>
</php>
```

### 3. Verifikasi Instalasi

```bash
vendor/bin/phpunit --version
```

Output yang diharapkan:
```
PHPUnit 9.5.x by Sebastian Bergmann and contributors.
```

## ▶️ Menjalankan Tests

### Windows

Double-click file `run_tests.bat` atau jalankan di command prompt:

```cmd
run_tests.bat
```

### Linux/Mac

Berikan permission execute terlebih dahulu:

```bash
chmod +x run_tests.sh
./run_tests.sh
```

### Manual dengan PHPUnit

```bash
# Jalankan semua tests
vendor/bin/phpunit

# Jalankan dengan format testdox (lebih readable)
vendor/bin/phpunit --testdox

# Jalankan test specific
vendor/bin/phpunit tests/api/ArtikelCreateTest.php

# Jalankan test suite tertentu
vendor/bin/phpunit --testsuite "API Tests"

# Jalankan dengan coverage report
vendor/bin/phpunit --coverage-html coverage
```

## 📊 Test Coverage

### API Tests (Unit Testing)

#### Artikel API
- ✅ **ArtikelCreateTest** (9 test cases)
  - Create artikel dengan data lengkap
  - Validasi field required (judul, kategori, isi)
  - Handling judul panjang
  - Handling karakter khusus
  - Multiple create operations
  - Authorization testing
  - HTTP method validation

- ✅ **ArtikelReadTest** (7 test cases)
  - Read empty list
  - Read list dengan data
  - Ordering by date DESC
  - Response structure validation
  - Multiple categories
  - Authorization testing
  - Performance testing (50 records)

- ✅ **ArtikelUpdateTest** (10 test cases)
  - Update artikel success
  - Validasi ID required
  - Update dengan ID invalid
  - Partial updates
  - Special characters handling
  - Multiple updates
  - Authorization testing
  - HTTP method validation

- ✅ **ArtikelDeleteTest** (10 test cases)
  - Delete artikel success
  - Validasi ID required
  - Delete dengan ID invalid
  - Multiple deletes
  - Delete all records
  - Double delete handling
  - Authorization testing
  - Performance testing (100 deletes)

#### Galeri API
- ✅ **GaleriCrudTest** (13 test cases)
  - Complete CRUD operations
  - Image upload validation
  - Field validations
  - Multiple categories
  - Response structure
  - Authorization testing

#### Program API
- ✅ **ProgramCrudTest** (15 test cases)
  - Complete CRUD operations
  - Status progression (planned → ongoing → completed)
  - Field validations
  - Multiple bidang
  - Date handling
  - Response structure
  - Authorization testing
  - Performance testing (50 records)

### Integration Tests

- ✅ **SystemIntegrationTest** (8 test cases)
  - Complete workflow testing (Artikel, Program, Galeri)
  - Multiple entities interaction
  - Concurrent operations
  - Data consistency
  - Error handling across modules
  - System load handling (60 inserts)

### Total Test Cases: **72 Test Cases**

## 📝 Penjelasan Test Cases

### 1. Unit Tests

Unit tests menguji individual API endpoints secara isolated.

**Contoh: ArtikelCreateTest**

```php
public function testCreateArtikelSuccess()
{
    $data = [
        'judul' => 'Artikel Test Baru',
        'kategori' => 'Berita',
        'isi' => 'Isi artikel...'
    ];

    $response = $this->post('/api/artikel/create.php', $data);
    $result = $this->assertJsonResponse($response, 'success');

    // Verify data tersimpan
    $this->assertRecordCount('artikel', 1);
}
```

### 2. Integration Tests

Integration tests menguji interaksi antar modul dan workflow lengkap.

**Contoh: Complete Workflow Test**

```php
public function testArtikelCompleteWorkflow()
{
    // 1. Create
    // 2. Read
    // 3. Update
    // 4. Verify Update
    // 5. Delete
    // 6. Verify Deletion
}
```

### 3. Performance Tests

Performance tests memastikan sistem dapat handle load dengan baik.

```php
public function testProgramPerformance()
{
    // Create 50 records
    // Measure execution time
    // Assert time < 1 second
}
```

## 🔧 Helper Classes

### TestHelper

Utility functions untuk testing:
- `randomString()`: Generate random string
- `randomEmail()`: Generate random email
- `createDummyImage()`: Create test image
- `mockFileUpload()`: Mock file upload
- `mockAdminSession()`: Mock admin session

### DatabaseHelper

Database management untuk testing:
- `setupTestDatabase()`: Setup test database
- `cleanDatabase()`: Clean all test data
- `insertTestData()`: Insert test data
- `getById()`: Get record by ID
- `countRecords()`: Count records

### ApiTestCase

Base class untuk semua test cases:
- `post()`: Simulate POST request
- `get()`: Simulate GET request
- `assertJsonResponse()`: Assert JSON response
- `assertDatabaseHas()`: Assert record exists
- `assertDatabaseMissing()`: Assert record not exists
- `createTestArtikel()`: Create test artikel
- `createTestProgram()`: Create test program
- `createTestGaleri()`: Create test galeri

## 📈 Best Practices

### 1. Test Isolation
Setiap test case harus independent dan tidak bergantung pada test lain.

### 2. Database Cleanup
Database dibersihkan sebelum setiap test untuk memastikan clean state.

### 3. Descriptive Test Names
Gunakan nama test yang jelas mendeskripsikan apa yang ditest.

### 4. Arrange-Act-Assert Pattern
```php
// Arrange: Setup test data
$data = ['judul' => 'Test'];

// Act: Execute action
$response = $this->post('/api/artikel/create.php', $data);

// Assert: Verify result
$this->assertJsonResponse($response, 'success');
```

### 5. Test Edge Cases
Selalu test edge cases seperti:
- Empty data
- Invalid data
- Missing required fields
- Special characters
- Large data sets

## 🐛 Troubleshooting

### Error: "Database connection failed"

**Solusi**: Pastikan MySQL running dan credentials di `phpunit.xml` benar.

### Error: "Class not found"

**Solusi**: Jalankan `composer install` untuk install dependencies.

### Error: "Permission denied"

**Solusi**: (Linux/Mac) Berikan permission: `chmod +x run_tests.sh`

### Tests Gagal Semua

**Solusi**: 
1. Check database connection
2. Pastikan test database `mahkota_test` bisa dibuat
3. Check PHP version (minimum PHP 7.3)

## 📞 Support

Jika ada pertanyaan atau issue, silakan hubungi tim development.

## 📄 License

Copyright © 2025 Mahkota Web System. All rights reserved.
