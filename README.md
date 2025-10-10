# Mahkota Web System - Quality Assurance and Testing

Website sistem manajemen untuk Mahkota dengan fitur admin panel lengkap untuk mengelola konten, artikel, galeri, dan program.

## 🚀 Fitur Utama

### Frontend
- **Landing Page** - Halaman utama dengan desain modern dan responsif
- **Portfolio/Galeri** - Showcase foto dan dokumentasi kegiatan
- **Artikel/Blog** - Sistem artikel dengan kategori dan detail
- **Program & Bidang** - Informasi program dan bidang kerja
- **Kontak** - Informasi kontak dengan integrasi maps dan sosial media
- **Privacy & Terms** - Halaman kebijakan privasi dan syarat ketentuan

### Backend/Admin Panel
- **Dashboard Admin** - Overview statistik dan aktivitas terbaru
- **Manajemen Artikel** - CRUD artikel dengan upload gambar
- **Manajemen Galeri** - Upload dan kelola foto galeri
- **Manajemen Program** - Kelola program dan bidang kerja
- **Manajemen Kontak** - Update informasi kontak dan sosial media
- **Sistem Login** - Autentikasi admin dengan session management
- **Activity Logging** - Pencatatan aktivitas admin

## 🛠️ Teknologi yang Digunakan

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP (Native)
- **Database**: MySQL
- **Server**: Apache (Laragon/XAMPP)
- **Version Control**: Git & GitHub

## 📋 Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Apache Web Server
- Web Browser modern (Chrome, Firefox, Edge, Safari)

## 🔧 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/Codift05/mahkota-websystem-quality-assurance-and-testing.git
cd mahkota-websystem-quality-assurance-and-testing
```

### 2. Setup Database
```sql
-- Buat database baru
CREATE DATABASE mahkota_db;

-- Import struktur database (jika ada file SQL)
-- Atau jalankan setup.php untuk membuat tabel otomatis
```

### 3. Konfigurasi Database
Edit file `db.php` atau `koneksi.php` sesuai dengan konfigurasi database Anda:
```php
$host = 'localhost';
$dbname = 'mahkota_db';
$username = 'root';
$password = '';
```

### 4. Setup Admin
Jalankan file `setup.php` di browser untuk membuat tabel dan admin pertama:
```
http://localhost/mahkota-websystem-quality-assurance-and-testing/setup.php
```

Atau gunakan `create_admin.php` untuk membuat admin baru.

### 5. Login ke Admin Panel
```
URL: http://localhost/mahkota-websystem-quality-assurance-and-testing/login-page.php
Default Username: admin
Default Password: (sesuai yang dibuat saat setup)
```

## 📁 Struktur Folder

```
mahkota-websystem-quality-assurance-and-testing/
├── admin/              # Halaman admin panel
├── api/                # REST API endpoints
│   ├── artikel/        # API artikel (CRUD)
│   ├── galeri/         # API galeri (CRUD)
│   └── program/        # API program (CRUD)
├── assets/             # Asset statis
│   ├── css/            # Stylesheet
│   ├── js/             # JavaScript
│   └── img/            # Gambar
├── uploads/            # Folder upload file
├── forms/              # Form handler
├── db.php              # Konfigurasi database
├── koneksi.php         # Koneksi database
├── login-page.php      # Halaman login
├── setup.php           # Setup database & admin
└── index.html          # Homepage
```

## 🔐 Keamanan

- ✅ Password hashing menggunakan `password_hash()`
- ✅ Session management untuk autentikasi
- ✅ Prepared statements untuk mencegah SQL injection
- ✅ File upload validation
- ✅ Admin access control
- ✅ Activity logging

## 📝 API Endpoints

### Artikel
- `GET /api/artikel/read.php` - Get semua artikel
- `POST /api/artikel/create.php` - Buat artikel baru
- `PUT /api/artikel/update.php` - Update artikel
- `DELETE /api/artikel/delete.php` - Hapus artikel

### Galeri
- `GET /api/galeri/read.php` - Get semua galeri
- `POST /api/galeri/create.php` - Upload foto baru
- `PUT /api/galeri/update.php` - Update galeri
- `DELETE /api/galeri/delete.php` - Hapus foto

### Program
- `GET /api/program/read.php` - Get semua program
- `POST /api/program/create.php` - Buat program baru
- `PUT /api/program/update.php` - Update program
- `DELETE /api/program/delete.php` - Hapus program

## 🧪 Testing

Project ini sedang dalam tahap Quality Assurance dan Testing untuk memastikan:
- ✅ Fungsionalitas semua fitur
- ✅ Keamanan sistem
- ✅ Performa dan optimasi
- ✅ Responsivitas UI/UX
- ✅ Kompatibilitas browser

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:
1. Fork repository ini
2. Buat branch baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Project ini dibuat untuk keperluan internal Mahkota.

## 👨‍💻 Developer

Developed by **Codift05**

## 📞 Kontak & Support

Jika ada pertanyaan atau issue, silakan buat issue di repository ini atau hubungi developer.

---

⭐ Jangan lupa berikan star jika project ini bermanfaat!
