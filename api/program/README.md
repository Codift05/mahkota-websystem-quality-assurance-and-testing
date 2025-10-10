# API Program - Dokumentasi

## 📁 Struktur File

```
api/program/
├── create.php   - Tambah program/bidang baru
├── read.php     - Ambil semua program
├── update.php   - Update program
└── delete.php   - Hapus program
```

## 🔗 Endpoint

### 1. Create Program
**File:** `create.php`  
**Method:** POST  
**Parameters:**
- `nama_program` (required) - Nama program kerja
- `bidang` (required) - Bidang organisasi (PSDM, KOMINFO, PTKP, Event)
- `deskripsi` (optional) - Deskripsi program
- `tanggal_mulai` (optional) - Tanggal mulai (YYYY-MM-DD)
- `tanggal_selesai` (optional) - Tanggal selesai (YYYY-MM-DD)
- `status` (optional) - Status: planned, ongoing, completed (default: planned)
- `gambar` (optional, file) - Gambar program

**Response:**
```json
{
  "success": true,
  "message": "Program berhasil ditambahkan"
}
```

### 2. Read Program
**File:** `read.php`  
**Method:** GET  
**Query Parameters:**
- `bidang` (optional) - Filter by bidang
- `status` (optional) - Filter by status

**Response:**
```json
[
  {
    "id": 1,
    "nama_program": "...",
    "bidang": "...",
    "deskripsi": "...",
    "tanggal_mulai": "...",
    "tanggal_selesai": "...",
    "status": "...",
    "gambar": "...",
    "created_at": "..."
  }
]
```

### 3. Update Program
**File:** `update.php`  
**Method:** POST  
**Parameters:**
- `id` (required)
- `nama_program` (required)
- `bidang` (required)
- `deskripsi` (optional)
- `tanggal_mulai` (optional)
- `tanggal_selesai` (optional)
- `status` (optional)
- `gambar` (optional, file)

### 4. Delete Program
**File:** `delete.php`  
**Method:** POST  
**Parameters:**
- `id` (required)

## 📝 Contoh Bidang

- **PSDM** - Pengembangan Sumber Daya Manusia
- **KOMINFO** - Komunikasi dan Informasi
- **PTKP** - Perguruan Tinggi, Kemahasiswaan dan Pemuda
- **Event** - Event dan Budaya

## 📝 Status Program

- **planned** - Direncanakan
- **ongoing** - Sedang Berjalan
- **completed** - Selesai
