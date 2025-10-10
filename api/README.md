# 🚀 API Documentation - Mahkota Admin Panel

Dokumentasi lengkap untuk semua API endpoint yang tersedia di sistem admin Mahkota.

## 📂 Struktur Folder

```
api/
├── artikel/          # API untuk manajemen artikel
│   ├── create.php    # Tambah artikel
│   ├── read.php      # Baca artikel
│   ├── update.php    # Update artikel
│   ├── delete.php    # Hapus artikel
│   └── README.md     # Dokumentasi artikel
│
├── galeri/           # API untuk manajemen galeri
│   ├── create.php    # Upload gambar
│   ├── read.php      # Baca galeri
│   ├── update.php    # Update galeri
│   ├── delete.php    # Hapus galeri
│   └── README.md     # Dokumentasi galeri
│
└── program/          # API untuk manajemen program kerja
    ├── create.php    # Tambah program
    ├── read.php      # Baca program
    ├── update.php    # Update program
    ├── delete.php    # Hapus program
    └── README.md     # Dokumentasi program
```

## 🔐 Authentication

Semua endpoint memerlukan session admin yang valid:
- Session key: `is_admin`
- Value: `true`

Jika tidak terautentikasi, akan return:
```json
{
  "error": "Unauthorized"
}
```
HTTP Status: 403

## 📋 Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operasi berhasil"
}
```

### Error Response
```json
{
  "error": "Pesan error"
}
```

## 🎯 Quick Start

### 1. Artikel API
```javascript
// Create
fetch('api/artikel/create.php', {
  method: 'POST',
  body: formData
});

// Read
fetch('api/artikel/read.php')
  .then(res => res.json())
  .then(data => console.log(data));
```

### 2. Galeri API
```javascript
// Upload gambar
const formData = new FormData();
formData.append('judul', 'Judul Galeri');
formData.append('gambar', fileInput.files[0]);

fetch('api/galeri/create.php', {
  method: 'POST',
  body: formData
});
```

### 3. Program API
```javascript
// Tambah program
const formData = new FormData();
formData.append('nama_program', 'Workshop IT');
formData.append('bidang', 'PSDM');
formData.append('status', 'planned');

fetch('api/program/create.php', {
  method: 'POST',
  body: formData
});
```

## 📁 Upload Directory

Semua file upload disimpan di:
```
uploads/
├── artikel/    # Gambar artikel
├── galeri/     # Gambar galeri
└── program/    # Gambar program
```

## 🔧 Database Tables

### Tabel: artikel
```sql
- id (int, auto_increment)
- judul (varchar)
- kategori (varchar)
- isi (text)
- gambar (varchar)
- tanggal (timestamp)
```

### Tabel: galeri
```sql
- id (int, auto_increment)
- judul (varchar)
- deskripsi (text)
- kategori (varchar)
- gambar (varchar)
- tanggal (timestamp)
```

### Tabel: program
```sql
- id (int, auto_increment)
- nama_program (varchar)
- bidang (varchar)
- deskripsi (text)
- tanggal_mulai (date)
- tanggal_selesai (date)
- status (enum: planned, ongoing, completed)
- gambar (varchar)
- created_at (timestamp)
```

## 📝 Notes

- Semua endpoint menggunakan prepared statements untuk keamanan
- File upload otomatis divalidasi (type, size)
- Gambar lama otomatis dihapus saat update/delete
- Semua response dalam format JSON

## 🆘 Support

Untuk dokumentasi lengkap setiap modul, lihat README.md di masing-masing folder:
- [Artikel API](artikel/README.md)
- [Galeri API](galeri/README.md)
- [Program API](program/README.md)

---
**© 2025 Mahkota-Manado** | Developed with ❤️
