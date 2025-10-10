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
    <title>Manajemen Program Bidang - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .program-card {
            transition: transform 0.3s;
        }
        .program-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include('sidebar.php'); ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h1 class="h2 mb-4">Manajemen Program Bidang</h1>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Tambah Program Bidang Baru</h5>
                    </div>
                    <div class="card-body">
                        <form id="programForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="nama_program" class="form-label">Nama Program</label>
                                <input type="text" class="form-control" id="nama_program" name="nama_program" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="icon" class="form-label">Icon (Bootstrap Icons class)</label>
                                <input type="text" class="form-control" id="icon" name="icon" placeholder="bi-award" required>
                                <small class="text-muted">Contoh: bi-award, bi-book, bi-people. Lihat <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a></small>
                            </div>
                            <div class="mb-3">
                                <label for="gambar" class="form-label">Gambar (Opsional)</label>
                                <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="urutan" class="form-label">Urutan</label>
                                <input type="number" class="form-control" id="urutan" name="urutan" min="1" value="1">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan Program</button>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Daftar Program Bidang</h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnViewCards">
                                <i class="bi bi-grid"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary active" id="btnViewTable">
                                <i class="bi bi-list"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Table View (Default) -->
                        <div id="tableView">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Program</th>
                                        <th>Icon</th>
                                        <th>Status</th>
                                        <th>Urutan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="programTableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Card View (Alternative) -->
                        <div id="cardView" class="row g-4" style="display: none;">
                            <!-- Cards will be loaded here -->
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Program Bidang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" enctype="multipart/form-data">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_nama_program" class="form-label">Nama Program</label>
                            <input type="text" class="form-control" id="edit_nama_program" name="nama_program" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_icon" class="form-label">Icon (Bootstrap Icons class)</label>
                            <input type="text" class="form-control" id="edit_icon" name="icon" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_gambar" class="form-label">Gambar Baru (Opsional)</label>
                            <input type="file" class="form-control" id="edit_gambar" name="gambar" accept="image/*">
                            <div id="current_image_container" class="mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_urutan" class="form-label">Urutan</label>
                            <input type="number" class="form-control" id="edit_urutan" name="urutan" min="1">
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSaveEdit">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load program data
            loadPrograms();
            
            // Toggle view
            document.getElementById('btnViewCards').addEventListener('click', function() {
                document.getElementById('tableView').style.display = 'none';
                document.getElementById('cardView').style.display = 'flex';
                this.classList.add('active');
                document.getElementById('btnViewTable').classList.remove('active');
            });
            
            document.getElementById('btnViewTable').addEventListener('click', function() {
                document.getElementById('tableView').style.display = 'block';
                document.getElementById('cardView').style.display = 'none';
                this.classList.add('active');
                document.getElementById('btnViewCards').classList.remove('active');
            });
            
            // Form submission
            document.getElementById('programForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('../program_create.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Program bidang berhasil ditambahkan!');
                        document.getElementById('programForm').reset();
                        loadPrograms();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menambahkan program bidang.');
                });
            });
            
            // Edit form submission
            document.getElementById('btnSaveEdit').addEventListener('click', function() {
                const formData = new FormData(document.getElementById('editForm'));
                
                fetch('../program_update.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Program bidang berhasil diperbarui!');
                        document.getElementById('editModal').querySelector('.btn-close').click();
                        loadPrograms();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memperbarui program bidang.');
                });
            });
        });
        
        // Function to load programs
        function loadPrograms() {
            fetch('../program_read.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderTableView(data.programs);
                        renderCardView(data.programs);
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        
        // Function to render table view
        function renderTableView(programs) {
            const tableBody = document.getElementById('programTableBody');
            tableBody.innerHTML = '';
            
            programs.forEach((program, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${program.nama_program}</td>
                    <td><i class="${program.icon}"></i> ${program.icon}</td>
                    <td>
                        <span class="badge ${program.status === 'aktif' ? 'bg-success' : 'bg-secondary'}">
                            ${program.status}
                        </span>
                    </td>
                    <td>${program.urutan}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editProgram(${program.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProgram(${program.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
        
        // Function to render card view
        function renderCardView(programs) {
            const cardContainer = document.getElementById('cardView');
            cardContainer.innerHTML = '';
            
            programs.forEach(program => {
                const card = document.createElement('div');
                card.className = 'col-md-4';
                card.innerHTML = `
                    <div class="card h-100 program-card">
                        <div class="card-body text-center">
                            <div class="display-1 mb-3">
                                <i class="${program.icon}"></i>
                            </div>
                            <h5 class="card-title">${program.nama_program}</h5>
                            <p class="card-text">${program.deskripsi.substring(0, 100)}${program.deskripsi.length > 100 ? '...' : ''}</p>
                            <div class="mt-3">
                                <span class="badge ${program.status === 'aktif' ? 'bg-success' : 'bg-secondary'}">
                                    ${program.status}
                                </span>
                                <span class="badge bg-info">Urutan: ${program.urutan}</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center">
                            <button class="btn btn-sm btn-primary" onclick="editProgram(${program.id})">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProgram(${program.id})">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                `;
                cardContainer.appendChild(card);
            });
        }
        
        // Function to edit program
        function editProgram(id) {
            fetch(`../program_get.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const program = data.program;
                        document.getElementById('edit_id').value = program.id;
                        document.getElementById('edit_nama_program').value = program.nama_program;
                        document.getElementById('edit_deskripsi').value = program.deskripsi;
                        document.getElementById('edit_icon').value = program.icon;
                        document.getElementById('edit_urutan').value = program.urutan;
                        document.getElementById('edit_status').value = program.status;
                        
                        // Show current image if exists
                        const imageContainer = document.getElementById('current_image_container');
                        if (program.gambar) {
                            imageContainer.innerHTML = `
                                <p>Gambar saat ini:</p>
                                <img src="../uploads/${program.gambar}" alt="Current Image" style="max-width: 100px; max-height: 100px;">
                                <input type="hidden" name="current_gambar" value="${program.gambar}">
                            `;
                        } else {
                            imageContainer.innerHTML = '<p>Tidak ada gambar saat ini</p>';
                        }
                        
                        // Show modal
                        new bootstrap.Modal(document.getElementById('editModal')).show();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil data program.');
                });
        }
        
        // Function to delete program
        function deleteProgram(id) {
            if (confirm('Apakah Anda yakin ingin menghapus program bidang ini?')) {
                fetch('../program_delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Program bidang berhasil dihapus!');
                        loadPrograms();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus program bidang.');
                });
            }
        }
    </script>
</body>
</html>