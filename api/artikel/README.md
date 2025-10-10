# API Artikel - Dokumentasi

## 📁 Struktur File

```
api/artikel/
├── create.php   - Tambah artikel baru
├── read.php     - Ambil semua artikel
├── update.php   - Update artikel
└── delete.php   - Hapus artikel
```

## 🔗 Endpoint

### 1. Create Artikel
**File:** `create.php`  
**Method:** POST  
**Parameters:**
- `judul` (required)
- `kategori` (required)
- `isi` (required)
- `gambar` (optional, file upload)

**Response:**
```json
{
  "success": true,
  "message": "Artikel berhasil ditambahkan"
}
```

### 2. Read Artikel
**File:** `read.php`  
**Method:** GET  
**Response:**
```json
[
  {
    "id": 1,
    "judul": "...",
    "kategori": "...",
    "isi": "...",
    "gambar": "...",
    "tanggal": "..."
  }
]
```

### 3. Update Artikel
**File:** `update.php`  
**Method:** POST  
**Parameters:**
- `id` (required)
- `judul` (required)
- `kategori` (required)
- `isi` (required)
- `gambar` (optional, file upload)

### 4. Delete Artikel
**File:** `delete.php`  
**Method:** POST  
**Parameters:**
- `id` (required)

## 📝 Cara Penggunaan di Frontend

```javascript
// Create
fetch('api/artikel/create.php', {
  method: 'POST',
  body: formData
});

// Read
fetch('api/artikel/read.php');

// Update
fetch('api/artikel/update.php', {
  method: 'POST',
  body: formData
});

// Delete
fetch('api/artikel/delete.php', {
  method: 'POST',
  body: formData
});
```
