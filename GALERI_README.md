# 📸 Dokumentasi Fitur Galeri Kegiatan

## Deskripsi
Fitur galeri kegiatan memungkinkan admin untuk mengelola dokumentasi visual dari seluruh aktivitas dan momen penting organisasi Mahkota. Pengunjung dapat melihat galeri dengan filter kategori dan lightbox untuk tampilan gambar yang lebih besar.

---

## 🗂️ Struktur File

### File Publik
- **`galeri.php`** - Halaman galeri untuk pengunjung umum
  - Menampilkan semua foto galeri
  - Filter berdasarkan kategori
  - Lightbox untuk melihat gambar full size
  - Responsive design

### File Admin
- **`admin/galeri.php`** - Halaman manajemen galeri (CRUD)
  - Form tambah galeri baru
  - Tabel daftar galeri
  - Tombol hapus galeri
  - Link ke halaman edit
  
- **`admin/edit_galeri.php`** - Halaman edit galeri
  - Form edit judul, deskripsi, kategori
  - Preview gambar saat ini
  - Upload gambar baru (opsional)
  - Log aktivitas admin

---

## 🗄️ Struktur Database

### Tabel: `galeri`
```sql
CREATE TABLE `galeri` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text,
  `kategori` varchar(100) DEFAULT 'Lainnya',
  `file_path` varchar(500) NOT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `kategori` (`kategori`),
  KEY `tanggal_upload` (`tanggal_upload`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Field Penjelasan:
- **id**: Primary key, auto increment
- **judul**: Judul/nama galeri (max 255 karakter)
- **deskripsi**: Deskripsi lengkap galeri (text)
- **kategori**: Kategori galeri (Kegiatan, Dokumentasi, Acara, Lainnya)
- **file_path**: Path file gambar relatif dari root (contoh: `assets/img/galeri/123456_foto.jpg`)
- **tanggal_upload**: Timestamp otomatis saat upload

---

## 🚀 Setup & Instalasi

### 1. Setup Database
Jalankan file setup untuk membuat tabel otomatis:
```
http://localhost/Devin/setup.php
```

Atau jalankan SQL manual:
```sql
CREATE TABLE IF NOT EXISTS `galeri` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text,
  `kategori` varchar(100) DEFAULT 'Lainnya',
  `file_path` varchar(500) NOT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `kategori` (`kategori`),
  KEY `tanggal_upload` (`tanggal_upload`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2. Buat Folder Upload
Pastikan folder untuk menyimpan gambar sudah ada dan memiliki permission write:
```
assets/img/galeri/
```

Jika belum ada, folder akan dibuat otomatis saat upload pertama kali.

---

## 📋 Cara Penggunaan

### A. Untuk Admin

#### 1. Login ke Admin Panel
```
http://localhost/Devin/login-page.php
```
Kredensial default:
- Username: `admin`
- Password: `admin123`

#### 2. Akses Halaman Galeri
Dari sidebar admin, klik menu **"Galeri"** atau akses:
```
http://localhost/Devin/admin/galeri.php
```

#### 3. Tambah Galeri Baru
1. Scroll ke form "Tambah Galeri Baru"
2. Isi form:
   - **Judul**: Nama/judul foto (wajib)
   - **Kategori**: Pilih kategori (wajib)
   - **Deskripsi**: Deskripsi singkat (wajib)
   - **Upload Gambar**: Pilih file gambar (wajib)
3. Klik tombol **"Simpan"**
4. Gambar akan otomatis tersimpan di `assets/img/galeri/`

**Format Gambar yang Didukung:**
- JPG / JPEG
- PNG
- GIF

**Ukuran Maksimal:** 2MB (dapat diubah di kode)

#### 4. Edit Galeri
1. Dari tabel daftar galeri, klik tombol **"Edit"** (icon pensil)
2. Ubah data yang diperlukan
3. Upload gambar baru (opsional) - gambar lama akan otomatis terhapus
4. Klik **"Update Galeri"**

#### 5. Hapus Galeri
1. Dari tabel daftar galeri, klik tombol **"Hapus"** (icon trash)
2. Konfirmasi penghapusan
3. File gambar dan data database akan terhapus

**⚠️ Perhatian:** Penghapusan bersifat permanen dan tidak dapat dikembalikan!

---

### B. Untuk Pengunjung

#### 1. Akses Halaman Galeri
Dari homepage, klik card **"Galeri Kegiatan"** atau akses langsung:
```
http://localhost/Devin/galeri.php
```

#### 2. Filter Kategori
- Klik tombol kategori di bagian atas untuk filter
- Klik **"Semua"** untuk melihat semua galeri

#### 3. Lihat Gambar Full Size
- Klik pada gambar untuk membuka lightbox
- Gunakan arrow keys atau tombol navigasi untuk berpindah gambar
- Klik area gelap atau tombol close untuk menutup

---

## 🎨 Fitur Utama

### Halaman Publik (galeri.php)
✅ **Desain Modern & Responsive**
- Grid layout yang rapi
- Hover effect pada gambar
- Gradient overlay dengan informasi

✅ **Filter Kategori**
- Filter dinamis berdasarkan kategori yang ada di database
- Active state pada kategori terpilih

✅ **Lightbox Gallery**
- Menggunakan GLightbox
- Touch navigation support
- Keyboard navigation
- Auto-play untuk video (jika ada)

✅ **Informasi Lengkap**
- Judul galeri
- Deskripsi (dipotong 80 karakter)
- Badge kategori
- Tanggal upload

### Halaman Admin (admin/galeri.php)
✅ **CRUD Lengkap**
- Create: Form tambah dengan preview
- Read: Tabel daftar dengan thumbnail
- Update: Link ke halaman edit
- Delete: Hapus dengan konfirmasi

✅ **Upload Validation**
- Validasi tipe file (JPG, JPEG, PNG, GIF)
- Validasi ukuran file
- Penamaan file unik dengan timestamp

✅ **Log Aktivitas**
- Setiap aksi (tambah, edit, hapus) dicatat
- Menyimpan admin_id dan timestamp

✅ **User Experience**
- Alert success/error
- Preview gambar sebelum upload
- Responsive table
- Bootstrap icons

### Halaman Edit (admin/edit_galeri.php)
✅ **Edit Fleksibel**
- Edit tanpa mengubah gambar
- Upload gambar baru (menghapus yang lama)
- Preview gambar saat ini
- Preview gambar baru sebelum save

✅ **Validasi**
- Cek ID valid
- Redirect jika galeri tidak ditemukan
- Validasi form input

---

## 🔧 Kustomisasi

### Mengubah Kategori
Edit file `admin/galeri.php` dan `admin/edit_galeri.php` pada bagian select kategori:
```php
<select class="form-select" id="kategori" name="kategori" required>
    <option value="">Pilih Kategori</option>
    <option value="Kegiatan">Kegiatan</option>
    <option value="Dokumentasi">Dokumentasi</option>
    <option value="Acara">Acara</option>
    <option value="Lainnya">Lainnya</option>
    <!-- Tambah kategori baru di sini -->
</select>
```

### Mengubah Ukuran Maksimal Upload
Edit file `admin/galeri.php` pada bagian validasi:
```php
// Ubah nilai 2MB sesuai kebutuhan
$maxFileSize = 2 * 1024 * 1024; // 2MB dalam bytes
if ($_FILES["gambar"]["size"] > $maxFileSize) {
    $error = "Ukuran file terlalu besar (max 2MB)";
}
```

### Mengubah Folder Upload
Edit file `admin/galeri.php` dan `admin/edit_galeri.php`:
```php
$target_dir = "../assets/img/galeri/"; // Ubah path sesuai kebutuhan
```

### Mengubah Jumlah Karakter Deskripsi
Edit file `galeri.php` pada bagian output deskripsi:
```php
// Ubah angka 80 sesuai kebutuhan
<?php echo htmlspecialchars(substr($row['deskripsi'], 0, 80)) . (strlen($row['deskripsi']) > 80 ? '...' : ''); ?>
```

---

## 🐛 Troubleshooting

### Error: "Tabel galeri tidak ditemukan"
**Solusi:** Jalankan `setup.php` atau buat tabel manual dengan SQL di atas.

### Error: "Permission denied" saat upload
**Solusi:** 
1. Pastikan folder `assets/img/galeri/` ada
2. Set permission folder ke 777 (Linux/Mac):
   ```bash
   chmod -R 777 assets/img/galeri/
   ```
3. Di Windows, pastikan folder tidak read-only

### Gambar tidak muncul di halaman publik
**Solusi:**
1. Cek path file di database (field `file_path`)
2. Pastikan path relatif dari root (contoh: `assets/img/galeri/123_foto.jpg`)
3. Cek apakah file fisik ada di folder tersebut

### Lightbox tidak berfungsi
**Solusi:**
1. Pastikan file GLightbox sudah ter-load:
   - `assets/vendor/glightbox/css/glightbox.min.css`
   - `assets/vendor/glightbox/js/glightbox.min.js`
2. Cek console browser untuk error JavaScript

### Upload file terlalu besar
**Solusi:**
1. Ubah setting PHP di `php.ini`:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```
2. Restart web server (Apache/Nginx)

---

## 📊 Log Aktivitas

Setiap aksi admin pada galeri akan tercatat di tabel `log_aktivitas`:

```sql
SELECT * FROM log_aktivitas WHERE aktivitas LIKE '%galeri%' ORDER BY tanggal DESC;
```

Format log:
- **Tambah**: "Menambahkan galeri: [judul]"
- **Update**: "Mengupdate galeri: [judul]"
- **Hapus**: "Menghapus galeri: [judul]"

---

## 🔐 Keamanan

### Proteksi Admin
- Session-based authentication
- Redirect ke login jika belum login
- Validasi `is_admin` di setiap halaman admin

### Upload Security
- Validasi tipe file (whitelist)
- Validasi ukuran file
- Penamaan file dengan timestamp (mencegah overwrite)
- Escape string untuk SQL injection

### XSS Protection
- Menggunakan `htmlspecialchars()` untuk output
- Prepared statement untuk query database

---

## 📞 Support

Jika ada pertanyaan atau masalah, hubungi:
- **Email**: admin@mahkota.com
- **Developer**: Tim IT Mahkota

---

## 📝 Changelog

### Version 1.0 (2025-10-12)
- ✅ Halaman galeri publik dengan filter kategori
- ✅ CRUD lengkap di admin panel
- ✅ Lightbox untuk view gambar
- ✅ Upload validation
- ✅ Log aktivitas admin
- ✅ Responsive design
- ✅ Integration dengan database

---

**© 2025 Mahkota Insight - All Rights Reserved**
