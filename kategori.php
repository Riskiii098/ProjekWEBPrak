<?php
ob_start(); // Hindari error header
$page = 'kategori';
$page_title = 'Data Kategori Buku';
include 'includes/db.php';

$duplikat = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = intval($_POST['id']);
  $nama = strtolower(trim($conn->real_escape_string($_POST['nama'])));

  $cek = $conn->query("SELECT id FROM kategori WHERE LOWER(nama) = '$nama'" . ($id ? " AND id != $id" : ""));
  if ($cek->num_rows > 0) {
    $duplikat = true;
  } else {
    if ($id) {
      $conn->query("UPDATE kategori SET nama='$nama' WHERE id=$id");
      header('Location: kategori.php?msg=edit');
      exit;
    } else {
      $conn->query("INSERT INTO kategori(nama) VALUES('$nama')");
      header('Location: kategori.php?msg=tambah');
      exit;
    }
  }
}

if (isset($_GET['del'])) {
  $conn->query("DELETE FROM kategori WHERE id=" . intval($_GET['del']));
  header('Location: kategori.php?msg=hapus');
  exit;
}

$data = $conn->query("SELECT * FROM kategori ORDER BY id DESC");

include 'includes/header.php';
?>

<!-- === CSS/JS Resource === -->
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
</style>

<!-- === UI === -->
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary">🏷️ Kategori Buku Perpustakaan</h2>
    <button class="btn btn-primary shadow" data-bs-toggle="modal" data-bs-target="#modalKategori" onclick="resetForm()">
      <i class="bi bi-plus-circle"></i> Tambah Kategori
    </button>
  </div>

  <div class="custom-card">
    <table id="kategoriTable" class="table table-striped table-bordered align-middle">
      <thead class="table-primary text-center">
        <tr>
          <th>#</th>
          <th>Nama Kategori</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; while($k = $data->fetch_assoc()): ?>
        <tr>
          <td class="text-center"><?= $i++ ?></td>
          <td class="text-capitalize"><?= htmlspecialchars($k['nama']) ?></td>
          <td class="text-center">
            <button class="btn btn-warning btn-sm me-1" onclick='editKategori(<?= json_encode($k) ?>)' title="Edit kategori">
              <i class="bi bi-pencil-square"></i>
            </button>
            <button class="btn btn-danger btn-sm" onclick="confirmHapus(<?= $k['id'] ?>)" title="Hapus kategori">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>
        <?php endwhile ?>
      </tbody>
    </table>
  </div>
</div>

<!-- === Modal === -->
<div class="modal fade" id="modalKategori" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content shadow-sm" method="post">
      <input type="hidden" name="id" id="kategori-id">
      <div class="modal-header">
        <h5 class="modal-title">Form Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="kategori-nama" class="form-label">Nama Kategori</label>
          <input type="text" class="form-control" name="nama" id="kategori-nama" required>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" type="submit">💾 Simpan</button>
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- === Script === -->
<script>
function editKategori(data) {
  document.getElementById('kategori-id').value = data.id;
  document.getElementById('kategori-nama').value = data.nama;
  new bootstrap.Modal(document.getElementById('modalKategori')).show();
}

function resetForm() {
  document.getElementById('kategori-id').value = '';
  document.getElementById('kategori-nama').value = '';
}

function confirmHapus(id) {
  Swal.fire({
    title: 'Hapus Kategori?',
    text: "Data ini akan dihapus permanen!",
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
  $('#kategoriTable').DataTable({
    paging: true,
    searching: true,
    lengthMenu: [5, 10, 25, 50],
    pageLength: 10,
    responsive: true,
    language: {
      search: "_INPUT_",
      searchPlaceholder: "🔍 Cari kategori...",
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

<!-- === SweetAlert Notifikasi === -->
<?php if (isset($_GET['msg']) || $duplikat): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  let pesan = '';
  let icon = 'success';
  let title = 'Berhasil';

  <?php if ($duplikat): ?>
    pesan = 'Kategori dengan nama tersebut sudah ada!';
    icon = 'error';
    title = 'Duplikat!';
  <?php else: ?>
    switch ("<?= $_GET['msg'] ?>") {
      case 'tambah': pesan = 'Kategori berhasil ditambahkan!'; break;
      case 'edit': pesan = 'Kategori berhasil diperbarui!'; break;
      case 'hapus': pesan = 'Kategori berhasil dihapus!'; break;
      default: pesan = 'Perubahan disimpan.';
    }
  <?php endif; ?>

  Swal.fire({
    icon: icon,
    title: title,
    text: pesan,
    confirmButtonColor: icon === 'success' ? '#6c63ff' : '#d33',
    confirmButtonText: 'OK'
  });
});
</script>
<?php endif; ?>

<?php
include 'includes/footer.php';
ob_end_flush(); // Akhiri buffering setelah semua selesai
?>
