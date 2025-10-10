# 🔐 Panduan Setup Login Admin - Mahkota

## 📋 Langkah-langkah Setup

### 1️⃣ Setup Database
Jalankan file setup untuk membuat tabel dan admin default:
```
http://localhost/Devin/setup.php
```

File ini akan:
- ✅ Membuat tabel `admin` jika belum ada
- ✅ Membuat tabel `admin_logs` jika belum ada  
- ✅ Membuat user admin default dengan kredensial:
  - **Username:** admin
  - **Email:** admin@mahkota.com
  - **Password:** admin123

### 2️⃣ Login ke Sistem
Setelah setup selesai, klik tombol "Login Sekarang" atau akses:
```
http://localhost/Devin/login.html
```

### 3️⃣ Akses dari Index
Di halaman utama (`index.html`), sudah ada link "Login" di menu navigasi yang akan mengarah ke halaman login.

## 🗂️ Struktur File

### File Login
- **login.html** - Halaman login dengan tampilan modern
- **login.php** - Backend untuk proses autentikasi
- **setup.php** - Setup awal database dan admin
- **db.php** - Konfigurasi koneksi database

### Fitur Login
✅ Login menggunakan username atau email  
✅ Password hashing dengan `password_hash()` (PHP)  
✅ Session management  
✅ Toggle show/hide password  
✅ Loading spinner saat proses login  
✅ Error handling yang informatif  
✅ Responsive design  

## 🔒 Keamanan

### Password Hashing
Sistem menggunakan `password_hash()` dan `password_verify()` untuk keamanan password.

### Backward Compatibility
Sistem juga support plain text password untuk kompatibilitas dengan data lama, tapi **sangat disarankan** menggunakan hashed password.

### Session Management
Setelah login berhasil, sistem menyimpan:
- `$_SESSION['is_admin']` - Status admin
- `$_SESSION['user_id']` - ID user
- `$_SESSION['admin_username']` - Username
- `$_SESSION['admin_email']` - Email

## 🎨 Tampilan Login

Halaman login memiliki:
- 🎨 Gradient background modern
- 🖼️ Split layout dengan ilustrasi
- 📱 Responsive untuk mobile
- ⚡ Smooth animations
- 🔄 Loading states
- ❌ Error messages yang jelas

## 🔧 Konfigurasi Database

File `db.php` menggunakan konfigurasi:
```php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'mahkota';
```

Pastikan database `mahkota` sudah dibuat di MySQL/MariaDB.

## 📝 Catatan Penting

⚠️ **Setelah login pertama kali, segera ganti password default!**

⚠️ **File `setup.php` sebaiknya dihapus atau diamankan setelah setup selesai** untuk mencegah akses tidak sah.

⚠️ **Jangan commit kredensial database ke repository publik!**

## 🚀 Flow Login

1. User mengakses `login.html`
2. User memasukkan username/email dan password
3. Form submit ke `login.php` via AJAX
4. `login.php` validasi kredensial dari database
5. Jika berhasil, set session dan redirect ke `admin-dashboard.php`
6. Jika gagal, tampilkan error message

## 🆘 Troubleshooting

### Error: "Database connection failed"
- Pastikan MySQL/MariaDB sudah running
- Cek konfigurasi di `db.php`
- Pastikan database `mahkota` sudah dibuat

### Error: "Username atau email tidak ditemukan"
- Jalankan `setup.php` untuk membuat admin default
- Atau cek apakah data admin ada di tabel `admin`

### Error: "Password salah"
- Pastikan menggunakan password yang benar
- Default password: `admin123`

### Redirect tidak bekerja
- Pastikan file `admin-dashboard.php` ada
- Atau ubah redirect target di `login.html` line 320

## 📞 Support

Jika ada masalah, hubungi developer atau cek dokumentasi lebih lanjut.

---
**© 2025 Mahkota-Manado** | Developed with ❤️
