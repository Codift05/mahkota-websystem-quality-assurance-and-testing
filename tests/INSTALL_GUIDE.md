# Panduan Instalasi Testing Dependencies

## ⚠️ Problem: OpenSSL Extension Tidak Aktif

Composer membutuhkan OpenSSL extension untuk download packages dari https://packagist.org

## 🔧 Solusi 1: Aktifkan OpenSSL di PHP (Recommended)

### Langkah-langkah:

1. **Buka Laragon Menu**
   - Klik kanan icon Laragon di system tray
   - Pilih **PHP** → **php.ini**

2. **Edit php.ini**
   - Cari baris: `;extension=openssl`
   - Hapus tanda `;` di depannya menjadi: `extension=openssl`
   - Save file

3. **Restart Laragon**
   - Klik kanan icon Laragon
   - Pilih **Stop All**
   - Kemudian **Start All**

4. **Verify OpenSSL Aktif**
   ```cmd
   php -m | findstr openssl
   ```
   Output harus menampilkan: `openssl`

5. **Install Dependencies**
   ```cmd
   cd C:\laragon\www\Devin\tests
   C:\laragon\bin\composer\composer.bat install
   ```

---

## 🔧 Solusi 2: Download Dependencies Manual (Alternatif)

Jika tidak bisa aktifkan OpenSSL, download PHPUnit manual:

### Download PHPUnit PHAR

1. **Download PHPUnit**
   - Buka: https://phar.phpunit.de/phpunit-9.5.phar
   - Save as: `C:\laragon\www\Devin\tests\phpunit.phar`

2. **Test PHPUnit**
   ```cmd
   cd C:\laragon\www\Devin\tests
   php phpunit.phar --version
   ```

3. **Run Tests**
   ```cmd
   php phpunit.phar --testdox
   ```

---

## 🔧 Solusi 3: Install via Composer dengan HTTP (Tidak Aman)

**⚠️ WARNING: Hanya untuk development lokal!**

```cmd
cd C:\laragon\www\Devin\tests

REM Set config untuk disable SSL
C:\laragon\bin\composer\composer.bat config -g secure-http false
C:\laragon\bin\composer\composer.bat config -g disable-tls true

REM Install
C:\laragon\bin\composer\composer.bat install
```

---

## ✅ Verifikasi Instalasi

Setelah berhasil install, cek folder vendor:

```cmd
cd C:\laragon\www\Devin\tests
dir vendor
```

Harus ada folder:
- vendor/
  - phpunit/
  - bin/
  - autoload.php

---

## 🚀 Run Tests

Setelah dependencies terinstall:

### Windows
```cmd
run_tests.bat
```

### Manual
```cmd
vendor\bin\phpunit --testdox
```

---

## 🐛 Troubleshooting

### Error: "openssl extension not available"
**Fix**: Ikuti Solusi 1 di atas

### Error: "composer not found"
**Fix**: Gunakan path lengkap: `C:\laragon\bin\composer\composer.bat`

### Error: "vendor directory not found"
**Fix**: Dependencies belum terinstall, jalankan `composer install`

### Error: "Class not found"
**Fix**: Run `composer dump-autoload`

---

## 📞 Need Help?

Jika masih ada masalah:
1. Check PHP version: `php -v` (minimum PHP 7.3)
2. Check extensions: `php -m`
3. Check Composer: `C:\laragon\bin\composer\composer.bat --version`

---

## 📝 Quick Commands Reference

```cmd
# Check PHP version
php -v

# Check PHP extensions
php -m

# Check Composer
C:\laragon\bin\composer\composer.bat --version

# Install dependencies
C:\laragon\bin\composer\composer.bat install

# Run tests
vendor\bin\phpunit --testdox

# Run specific test
vendor\bin\phpunit tests/api/ArtikelCreateTest.php
```

---

## ▶️ Jalankan Simple Test Runner (Tanpa PHPUnit)

Untuk cepat memverifikasi endpoint API tanpa dependency eksternal, gunakan script sederhana:

1) Pastikan Laragon sudah berjalan (Apache + MySQL aktif)
2) Jalankan perintah berikut:

```cmd
cd C:\laragon\www\Devin
php tests\simple_test_runner.php
```

Script ini akan:
- Login admin otomatis (username: `admin`, password: `admin123`)
- Menguji endpoint API dasar: Artikel, Program, Galeri
- Menampilkan ringkasan pass/fail

Jika ingin menggunakan PHP built-in server, pastikan ekstensi `mysqli` dan `curl` aktif di CLI. Bila ragu, gunakan Laragon Apache dengan base URL `http://localhost/Devin` (disarankan).

---

## 🗄️ Setup Database untuk API Tests

Jika Anda melihat error seperti:
- `Table 'mahkota.program' doesn't exist`
- `Unknown column 'tanggal' in 'order clause'`

Maka database belum selaras dengan kebutuhan API. Ikuti langkah berikut:

1) Buka phpMyAdmin: `http://localhost/phpmyadmin`
2) Pilih database yang digunakan di `db.php` (contoh: `mahkota`)
3) Jalankan SQL berikut untuk membuat tabel `program` yang digunakan oleh API:

```sql
CREATE TABLE IF NOT EXISTS `program` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_program` VARCHAR(255) NOT NULL,
  `bidang` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT,
  `tanggal_mulai` DATE,
  `tanggal_selesai` DATE,
  `status` ENUM('planned','ongoing','completed') DEFAULT 'planned',
  `gambar` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

4) Perbaiki struktur tabel `galeri` agar kompatibel dengan API:

```sql
-- Tambahkan kolom 'tanggal' jika belum ada
ALTER TABLE `galeri` ADD COLUMN `tanggal` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;

-- Opsional: Jika kolom gambar yang digunakan API adalah 'gambar',
-- tetapi saat ini Anda menyimpan path di 'file_path', bisa tambahkan kolom 'gambar'
-- atau sesuaikan endpoint sesuai kebutuhan.
ALTER TABLE `galeri` ADD COLUMN `gambar` VARCHAR(255);
```

Catatan:
- Beberapa instalasi mungkin sudah memiliki tabel `galeri` dengan kolom `tanggal_upload` atau `file_path`. Endpoint API default mengurutkan berdasarkan `tanggal`. Menambahkan kolom `tanggal` akan mencegah error saat read.
- Jika ingin menyelaraskan sepenuhnya dengan dokumentasi API, pertimbangkan migrasi untuk menyesuaikan kolom menjadi `gambar` dan `tanggal`.

5) Alternatif cepat: Jalankan `http://localhost/Devin/setup.php` untuk membuat sebagian tabel dasar. Namun script ini menggunakan variasi struktur (mis. `program_kerja`, `tanggal_upload`, `file_path`). Jika menggunakan API di folder `api/`, ikuti SQL di atas agar konsisten.

---

## 🔌 Ekstensi PHP yang Dibutuhkan (CLI)

Jika Anda menjalankan server built-in (`php -S`) atau test dari CLI yang memanggil HTTP:
- Aktifkan `mysqli` dan `curl` di `php.ini` CLI.
- Cara cepat: Laragon menu → PHP → `php.ini`, ubah baris berikut dan restart Laragon:

```
extension=mysqli
extension=curl
```

Verifikasi:
```cmd
php -m | findstr mysqli
php -m | findstr curl
```

Jika tidak aktif, gunakan Laragon Apache (base URL `http://localhost/Devin`) untuk menjalankan endpoint API tanpa perlu mengubah konfigurasi CLI.
