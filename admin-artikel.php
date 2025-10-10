<?php
// admin-artikel.php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Artikel</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body>
  <header class="p-3 bg-primary text-white">
    <div class="container">
      <h1 class="h3">Manajemen Artikel</h1>
      <a href="admin-dashboard.php" class="btn btn-light btn-sm">Kembali ke Dashboard</a>
    </div>
  </header>
  <main class="container my-4">
    <div class="mb-4">
      <h2 class="h5">Tambah Artikel</h2>
      <form id="formTambah" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-4">
          <input type="text" name="judul" class="form-control" placeholder="Judul artikel" required>
        </div>
        <div class="col-md-4">
          <select name="kategori" class="form-control" required>
            <option value="">Pilih Kategori</option>
            <option value="Inspirasi">Inspirasi</option>
            <option value="Berita">Berita</option>
            <option value="Kegiatan">Kegiatan</option>
            <option value="Lainnya">Lainnya</option>
          </select>
        </div>
        <div class="col-md-4">
          <input type="file" name="gambar" class="form-control">
        </div>
        <div class="col-12">
          <textarea name="isi" class="form-control" placeholder="Isi artikel" rows="4" required></textarea>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-success">Tambah</button>
        </div>
        <div id="tambahMsg" class="text-danger"></div>
      </form>
    </div>
    <hr>
    <h2 class="h5">Daftar Artikel</h2>
    <div id="artikelList"></div>
  </main>
  <script>
    // Tampilkan daftar artikel
    function loadArtikel() {
      fetch('artikel_read.php')
        .then(res => res.json())
        .then(data => {
          let html = '<table class="table table-bordered"><thead><tr><th>Judul</th><th>Kategori</th><th>Gambar</th><th>Tanggal</th><th>Aksi</th></tr></thead><tbody>';
          data.forEach(a => {
            html += `<tr>
              <td>${a.judul}</td>
              <td>${a.kategori || ''}</td>
              <td>${a.gambar ? `<img src=\"${a.gambar}\" width=\"80\">` : ''}</td>
              <td>${a.tanggal}</td>
              <td>
                <button class="btn btn-warning btn-sm" onclick="editArtikel(${a.id}, '${encodeURIComponent(a.judul)}', '${encodeURIComponent(a.kategori)}', '${encodeURIComponent(a.isi)}')">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="hapusArtikel(${a.id})">Hapus</button>
              </td>
            </tr>`;
          });
          html += '</tbody></table>';
          document.getElementById('artikelList').innerHTML = html;
        });
    }
    loadArtikel();

    // Tambah artikel
    document.getElementById('formTambah').onsubmit = function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch('artikel_create.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          this.reset();
          document.getElementById('tambahMsg').textContent = '';
          loadArtikel();
        } else {
          document.getElementById('tambahMsg').textContent = data.error || 'Gagal menambah artikel';
        }
      });
    };

    // Hapus artikel
    function hapusArtikel(id) {
      if (!confirm('Yakin hapus artikel ini?')) return;
      const formData = new FormData();
      formData.append('id', id);
      fetch('artikel_delete.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) loadArtikel();
        else alert(data.error || 'Gagal hapus artikel');
      });
    }

    // Edit artikel (popup sederhana)
    function editArtikel(id, judul, kategori, isi) {
      judul = decodeURIComponent(judul);
      kategori = decodeURIComponent(kategori);
      isi = decodeURIComponent(isi);
      const newJudul = prompt('Edit Judul:', judul);
      if (newJudul === null) return;
      const newKategori = prompt('Edit Kategori:', kategori);
      if (newKategori === null) return;
      const newIsi = prompt('Edit Isi:', isi);
      if (newIsi === null) return;
      const formData = new FormData();
      formData.append('id', id);
      formData.append('judul', newJudul);
      formData.append('kategori', newKategori);
      formData.append('isi', newIsi);
      fetch('artikel_update.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) loadArtikel();
        else alert(data.error || 'Gagal update artikel');
      });
    }
  </script>
</body>
</html>
