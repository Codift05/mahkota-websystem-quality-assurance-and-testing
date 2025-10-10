# API Galeri - Dokumentasi

## 📁 Struktur File

```
api/galeri/
├── create.php   - Upload gambar galeri baru
├── read.php     - Ambil semua galeri
├── update.php   - Update galeri
└── delete.php   - Hapus galeri
```

## 🔗 Endpoint

### 1. Create Galeri
**File:** `create.php`  
**Method:** POST  
**Parameters:**
- `judul` (required) - Judul galeri
- `deskripsi` (optional) - Deskripsi gambar
- `kategori` (optional) - Kategori galeri (event, kegiatan, dll)
- `gambar` (required, file) - File gambar (JPG, PNG, GIF, WEBP)

**Response:**
```json
{
  "success": true,
  "message": "Galeri berhasil ditambahkan"
}
```

### 2. Read Galeri
**File:** `read.php`  
**Method:** GET  
**Query Parameters:**
- `kategori` (optional) - Filter by kategori

**Response:**
```json
[
  {
    "id": 1,
    "judul": "...",
    "deskripsi": "...",
    "kategori": "...",
    "gambar": "uploads/galeri/...",
    "tanggal": "..."
  }
]
```

### 3. Update Galeri
**File:** `update.php`  
**Method:** POST  
**Parameters:**
- `id` (required)
- `judul` (required)
- `deskripsi` (optional)
- `kategori` (optional)
- `gambar` (optional, file) - Gambar baru jika ingin diganti

### 4. Delete Galeri
**File:** `delete.php`  
**Method:** POST  
**Parameters:**
- `id` (required)

**Note:** File gambar akan otomatis dihapus dari server.

## 📝 Cara Penggunaan

```javascript
// Upload galeri
const formData = new FormData();
formData.append('judul', 'Kegiatan Maskena 2024');
formData.append('kategori', 'event');
formData.append('gambar', fileInput.files[0]);

fetch('api/galeri/create.php', {
  method: 'POST',
  body: formData
});
```
