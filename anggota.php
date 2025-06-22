<?php
ob_start();
$page = 'anggota'; 
$page_title = 'Data Anggota';
include_once 'includes/db.php';
include_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = intval($_POST['id']);
  $nama = $conn->real_escape_string($_POST['nama']);
  $email = $conn->real_escape_string($_POST['email']);
  $telepon = $conn->real_escape_string($_POST['telepon']);

  $cek = $conn->query("SELECT id FROM anggota WHERE (email='$email' OR telepon='$telepon')" . ($id ? " AND id != $id" : ""));
  if ($cek->num_rows > 0) {
    header('Location: anggota.php?msg=duplikat');
    exit;
  }

  if ($id) {
    $conn->query("UPDATE anggota SET nama='$nama', email='$email', telepon='$telepon' WHERE id=$id");
    header('Location: anggota.php?msg=edit');
  } else {
    $conn->query("INSERT INTO anggota(nama,email,telepon) VALUES('$nama','$email','$telepon')");
    header('Location: anggota.php?msg=tambah');
  }
  exit;
}

if (isset($_GET['del'])) {
  $conn->query("DELETE FROM anggota WHERE id=" . intval($_GET['del']));
  header('Location: anggota.php?msg=hapus');
  exit;
}

$data = $conn->query("SELECT * FROM anggota ORDER BY id DESC");
?>

<!-- === CSS & JS Resources === -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- === Styling === -->
<style>
  body {
    background-color: #e8f0fe;
    font-family: 'Segoe UI', sans-serif;
  }
  .custom-card {
    background-color: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  }
  .table th, .table td {
    vertical-align: middle;
  }
</style>

<!-- === UI === -->
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary">👥 Data Anggota Perpustakaan</h2>
    <button class="btn btn-primary shadow" data-bs-toggle="modal" data-bs-target="#modalAnggota" onclick="resetForm()">
      <i class="bi bi-person-plus"></i> Tambah Anggota
    </button>
  </div>

  <div class="custom-card">
    <table id="anggotaTable" class="table table-striped table-bordered align-middle">
      <thead class="table-primary text-center">
        <tr>
          <th>#</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Telepon</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; while($a = $data->fetch_assoc()): ?>
        <tr>
          <td class="text-center"><?= $i++ ?></td>
          <td><?= htmlspecialchars($a['nama']) ?></td>
          <td><?= htmlspecialchars($a['email']) ?></td>
          <td><?= htmlspecialchars($a['telepon']) ?></td>
          <td class="text-center">
            <button class="btn btn-warning btn-sm" onclick='editAnggota(<?= json_encode($a) ?>)' title="Edit">
              <i class="bi bi-pencil-square"></i>
            </button>
            <button class="btn btn-danger btn-sm" onclick="confirmHapus(<?= $a['id'] ?>)" title="Hapus">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>
        <?php endwhile ?>
      </tbody>
    </table>
  </div>
</div>

<!-- === Modal Form Anggota === -->
<div class="modal fade" id="modalAnggota" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content shadow-sm" method="post">
      <input type="hidden" name="id" id="anggota-id">
      <div class="modal-header">
        <h5 class="modal-title">Form Anggota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama</label>
          <input type="text" class="form-control" name="nama" id="anggota-nama" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" id="anggota-email" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Telepon</label>
          <input type="text" class="form-control" name="telepon" id="anggota-telepon" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">💾 Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- === Script === -->
<script>
function editAnggota(data) {
  document.getElementById('anggota-id').value = data.id;
  document.getElementById('anggota-nama').value = data.nama;
  document.getElementById('anggota-email').value = data.email;
  document.getElementById('anggota-telepon').value = data.telepon;
  new bootstrap.Modal(document.getElementById('modalAnggota')).show();
}

function resetForm() {
  document.getElementById('anggota-id').value = '';
  document.getElementById('anggota-nama').value = '';
  document.getElementById('anggota-email').value = '';
  document.getElementById('anggota-telepon').value = '';
}

function confirmHapus(id) {
  Swal.fire({
    title: 'Hapus Anggota?',
    text: "Data akan dihapus permanen!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "?del=" + id;
    }
  });
}

$(document).ready(function () {
  $('#anggotaTable').DataTable({
    paging: true,
    searching: true,
    lengthMenu: [5, 10, 25, 50],
    pageLength: 10,
    responsive: true,
    language: {
      search: "_INPUT_",
      searchPlaceholder: "🔍 Cari anggota...",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
      paginate: {
        next: "Berikutnya",
        previous: "Sebelumnya"
      }
    }
  });
});
</script>

<!-- === Notifikasi === -->
<?php if (isset($_GET['msg'])): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
  let msg = '<?= $_GET['msg'] ?>';
  let title = 'Berhasil';
  let text = '';
  let icon = 'success';

  switch (msg) {
    case 'tambah': text = 'Anggota berhasil ditambahkan!'; break;
    case 'edit': text = 'Data anggota berhasil diperbarui!'; break;
    case 'hapus': text = 'Data anggota berhasil dihapus!'; break;
    case 'duplikat': 
      title = 'Duplikat!';
      text = 'Email atau telepon sudah digunakan.';
      icon = 'error';
      break;
  }

  Swal.fire({ icon, title, text, confirmButtonColor: '#6c63ff' });
});
</script>
<?php endif; ?>

<?php 
include 'includes/footer.php'; 
ob_end_flush(); 
?>
