<?php
// Proteksi halaman dengan session
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../login-page.php');
    exit();
}

// Koneksi ke database
require_once('../db.php');
require_once('../lib_log.php');

// Fungsi untuk mencatat aktivitas admin
function logActivity($conn, $user_id, $activity) {
    $sql = "INSERT INTO admin_logs (user_id, activity, log_time) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $activity);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Kontak - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include('sidebar.php'); ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h1 class="h2 mb-4">Informasi Kontak</h1>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Informasi Kontak Utama</h5>
                            </div>
                            <div class="card-body">
                                <form id="kontakForm">
                                    <div class="mb-3">
                                        <label for="alamat" class="form-label">Alamat Lengkap</label>
                                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="telepon" class="form-label">Nomor Telepon</label>
                                        <input type="text" class="form-control" id="telepon" name="telepon" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="whatsapp" class="form-label">WhatsApp</label>
                                        <input type="text" class="form-control" id="whatsapp" name="whatsapp">
                                        <small class="text-muted">Format: 628xxxxxxxxxx (tanpa tanda + atau -)</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="jam_operasional" class="form-label">Jam Operasional</label>
                                        <input type="text" class="form-control" id="jam_operasional" name="jam_operasional" placeholder="Senin-Jumat: 08.00-16.00">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Simpan Informasi</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Lokasi Peta</h5>
                            </div>
                            <div class="card-body">
                                <form id="mapForm">
                                    <div class="mb-3">
                                        <label for="google_maps_embed" class="form-label">Google Maps Embed Code</label>
                                        <textarea class="form-control" id="google_maps_embed" name="google_maps_embed" rows="4"></textarea>
                                        <small class="text-muted">Salin kode embed dari Google Maps</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="latitude" class="form-label">Latitude</label>
                                        <input type="text" class="form-control" id="latitude" name="latitude">
                                    </div>
                                    <div class="mb-3">
                                        <label for="longitude" class="form-label">Longitude</label>
                                        <input type="text" class="form-control" id="longitude" name="longitude">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Simpan Lokasi</button>
                                </form>
                                
                                <div class="mt-3" id="mapPreview">
                                    <!-- Map preview will be shown here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Media Sosial</h5>
                            </div>
                            <div class="card-body">
                                <form id="sosmedForm">
                                    <div class="mb-3">
                                        <label for="facebook" class="form-label">Facebook</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-facebook"></i></span>
                                            <input type="text" class="form-control" id="facebook" name="facebook" placeholder="URL Facebook">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="instagram" class="form-label">Instagram</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                                            <input type="text" class="form-control" id="instagram" name="instagram" placeholder="URL Instagram">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="twitter" class="form-label">Twitter</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-twitter"></i></span>
                                            <input type="text" class="form-control" id="twitter" name="twitter" placeholder="URL Twitter">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="youtube" class="form-label">YouTube</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-youtube"></i></span>
                                            <input type="text" class="form-control" id="youtube" name="youtube" placeholder="URL YouTube">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="linkedin" class="form-label">LinkedIn</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-linkedin"></i></span>
                                            <input type="text" class="form-control" id="linkedin" name="linkedin" placeholder="URL LinkedIn">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tiktok" class="form-label">TikTok</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-tiktok"></i></span>
                                            <input type="text" class="form-control" id="tiktok" name="tiktok" placeholder="URL TikTok">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Simpan Media Sosial</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Pesan dari Pengunjung</h5>
                                <button class="btn btn-sm btn-outline-secondary" id="refreshMessages">
                                    <i class="bi bi-arrow-clockwise"></i> Refresh
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="messagesTableBody">
                                            <!-- Data will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- View Message Modal -->
    <div class="modal fade" id="viewMessageModal" tabindex="-1" aria-labelledby="viewMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewMessageModalLabel">Detail Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama:</label>
                        <p id="view_nama"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email:</label>
                        <p id="view_email"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subjek:</label>
                        <p id="view_subjek"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pesan:</label>
                        <p id="view_pesan"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal:</label>
                        <p id="view_tanggal"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status:</label>
                        <select class="form-select" id="view_status">
                            <option value="baru">Baru</option>
                            <option value="dibaca">Dibaca</option>
                            <option value="dibalas">Dibalas</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btnUpdateStatus">Perbarui Status</button>
                    <button type="button" class="btn btn-danger" id="btnDeleteMessage">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load kontak data
            loadKontakData();
            
            // Load messages
            loadMessages();
            
            // Refresh messages
            document.getElementById('refreshMessages').addEventListener('click', function() {
                loadMessages();
            });
            
            // Submit kontak form
            document.getElementById('kontakForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('../kontak_update.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Informasi kontak berhasil disimpan!');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan informasi kontak.');
                });
            });
            
            // Submit map form
            document.getElementById('mapForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('../kontak_update_map.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Informasi lokasi berhasil disimpan!');
                        updateMapPreview();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan informasi lokasi.');
                });
            });
            
            // Submit sosmed form
            document.getElementById('sosmedForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('../kontak_update_sosmed.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Informasi media sosial berhasil disimpan!');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan informasi media sosial.');
                });
            });
            
            // Update message status
            document.getElementById('btnUpdateStatus').addEventListener('click', function() {
                const messageId = this.getAttribute('data-id');
                const status = document.getElementById('view_status').value;
                
                fetch('../pesan_update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${messageId}&status=${status}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Status pesan berhasil diperbarui!');
                        document.getElementById('viewMessageModal').querySelector('.btn-close').click();
                        loadMessages();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memperbarui status pesan.');
                });
            });
            
            // Delete message
            document.getElementById('btnDeleteMessage').addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus pesan ini?')) {
                    const messageId = this.getAttribute('data-id');
                    
                    fetch('../pesan_delete.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${messageId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert('Pesan berhasil dihapus!');
                            document.getElementById('viewMessageModal').querySelector('.btn-close').click();
                            loadMessages();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus pesan.');
                    });
                }
            });
        });
        
        // Function to load kontak data
        function loadKontakData() {
            fetch('../kontak_get.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const kontak = data.kontak;
                        
                        // Fill kontak form
                        document.getElementById('alamat').value = kontak.alamat || '';
                        document.getElementById('email').value = kontak.email || '';
                        document.getElementById('telepon').value = kontak.telepon || '';
                        document.getElementById('whatsapp').value = kontak.whatsapp || '';
                        document.getElementById('jam_operasional').value = kontak.jam_operasional || '';
                        
                        // Fill map form
                        document.getElementById('google_maps_embed').value = kontak.google_maps_embed || '';
                        document.getElementById('latitude').value = kontak.latitude || '';
                        document.getElementById('longitude').value = kontak.longitude || '';
                        
                        // Fill sosmed form
                        document.getElementById('facebook').value = kontak.facebook || '';
                        document.getElementById('instagram').value = kontak.instagram || '';
                        document.getElementById('twitter').value = kontak.twitter || '';
                        document.getElementById('youtube').value = kontak.youtube || '';
                        document.getElementById('linkedin').value = kontak.linkedin || '';
                        document.getElementById('tiktok').value = kontak.tiktok || '';
                        
                        // Update map preview
                        updateMapPreview();
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        
        // Function to update map preview
        function updateMapPreview() {
            const embedCode = document.getElementById('google_maps_embed').value;
            if (embedCode) {
                document.getElementById('mapPreview').innerHTML = embedCode;
            } else {
                const lat = document.getElementById('latitude').value;
                const lng = document.getElementById('longitude').value;
                if (lat && lng) {
                    document.getElementById('mapPreview').innerHTML = `
                        <iframe
                            width="100%"
                            height="300"
                            frameborder="0"
                            scrolling="no"
                            marginheight="0"
                            marginwidth="0"
                            src="https://maps.google.com/maps?q=${lat},${lng}&hl=id&z=14&output=embed"
                        ></iframe>
                    `;
                } else {
                    document.getElementById('mapPreview').innerHTML = '<p class="text-muted">Belum ada data lokasi</p>';
                }
            }
        }
        
        // Function to load messages
        function loadMessages() {
            fetch('../pesan_read.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderMessagesTable(data.pesan);
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        
        // Function to render messages table
        function renderMessagesTable(messages) {
            const tableBody = document.getElementById('messagesTableBody');
            tableBody.innerHTML = '';
            
            if (messages.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="5" class="text-center">Tidak ada pesan</td>';
                tableBody.appendChild(row);
                return;
            }
            
            messages.forEach(message => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${formatDate(message.tanggal)}</td>
                    <td>${message.nama}</td>
                    <td>${message.email}</td>
                    <td>
                        <span class="badge ${getBadgeClass(message.status)}">
                            ${message.status}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewMessage(${message.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
        
        // Function to get badge class based on status
        function getBadgeClass(status) {
            switch (status) {
                case 'baru':
                    return 'bg-danger';
                case 'dibaca':
                    return 'bg-warning';
                case 'dibalas':
                    return 'bg-success';
                default:
                    return 'bg-secondary';
            }
        }
        
        // Function to format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        // Function to view message
        function viewMessage(id) {
            fetch(`../pesan_get.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const message = data.pesan;
                        document.getElementById('view_nama').textContent = message.nama;
                        document.getElementById('view_email').textContent = message.email;
                        document.getElementById('view_subjek').textContent = message.subjek;
                        document.getElementById('view_pesan').textContent = message.pesan;
                        document.getElementById('view_tanggal').textContent = formatDate(message.tanggal);
                        document.getElementById('view_status').value = message.status;
                        
                        // Set data-id for update and delete buttons
                        document.getElementById('btnUpdateStatus').setAttribute('data-id', message.id);
                        document.getElementById('btnDeleteMessage').setAttribute('data-id', message.id);
                        
                        // Show modal
                        new bootstrap.Modal(document.getElementById('viewMessageModal')).show();
                        
                        // If status is 'baru', update to 'dibaca'
                        if (message.status === 'baru') {
                            fetch('../pesan_update_status.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `id=${message.id}&status=dibaca`
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    document.getElementById('view_status').value = 'dibaca';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                        }
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil data pesan.');
                });
        }
    </script>
</body>
</html>