<?php
ob_start();
$page = 'buku';
$page_title = 'Data Buku';

include_once 'includes/db.php';
include_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = intval($_POST['id']);
  $judul = $conn->real_escape_string(trim($_POST['judul']));
  $penulis = $conn->real_escape_string(trim($_POST['penulis']));
  $kat = intval($_POST['kategori_id']);
  $gambar = $_FILES['gambar']['name'] ?? '';
  $nama_file = '';

  if ($gambar != '') {
    $ext = pathinfo($gambar, PATHINFO_EXTENSION);
    $nama_file = uniqid() . '.' . $ext;
    $upload_path = 'uploads/' . $nama_file;
    if (!is_dir('uploads')) mkdir('uploads');
    move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path);
  }

  $cek = $conn->query("SELECT id FROM buku WHERE judul='$judul' AND penulis='$penulis' AND kategori_id=$kat" . ($id > 0 ? " AND id != $id" : ""));

  if ($cek->num_rows > 0) {
    header('Location: buku.php?msg=duplikat');
    exit;
  } else {
    if ($id > 0) {
      $old = $conn->query("SELECT status, gambar FROM buku WHERE id=$id")->fetch_assoc();
      $status = $conn->real_escape_string($old['status']);
      $final_gambar = $gambar != '' ? $nama_file : $old['gambar'];
      $conn->query("UPDATE buku SET judul='$judul', penulis='$penulis', kategori_id=$kat, status='$status', gambar='$final_gambar' WHERE id=$id");
      header('Location: buku.php?msg=edit');
    } else {
      $status = 'tersedia';
      $conn->query("INSERT INTO buku(judul, penulis, kategori_id, status, gambar) VALUES('$judul','$penulis',$kat,'$status','$nama_file')");
      header('Location: buku.php?msg=tambah');
    }
    exit;
  }
}

if (isset($_GET['del'])) {
  $conn->query("DELETE FROM buku WHERE id=" . intval($_GET['del']));
  header('Location: buku.php?msg=hapus');
  exit;
}

$rst = $conn->query("SELECT b.*, k.nama AS kategori FROM buku b JOIN kategori k ON b.kategori_id=k.id ORDER BY b.id DESC");
$rk = $conn->query("SELECT * FROM kategori ORDER BY nama");
?>

<!-- Styles & Scripts -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<style>
  body {
    background-color: #e8f0fe;
    font-family: 'Segoe UI', sans-serif;
  }
  .custom-card {
    background-color: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }
  .img-thumb {
    width: 50px;
    height: auto;
    border-radius: 4px;
  }
  .badge-status {
    padding: 5px 10px;
    font-size: 0.75rem;
    border-radius: 6px;
  }
  .btn-action {
    padding: 6px 10px;
    font-size: 0.9rem;
    margin: 0 2px;
  }
</style>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold text-primary">📚 Data Buku Perpustakaan</h2>
    <button class="btn btn-success" onclick="resetForm()" data-bs-toggle="modal" data-bs-target="#modalBuku">
      <i class="bi bi-plus-circle"></i> Tambah Buku
    </button>
  </div>

  <div class="custom-card">
    <table id="bukuTable" class="table table-bordered table-hover">
      <thead class="table-primary text-center">
        <tr>
          <th>No</th>
          <th>Gambar</th>
          <th>Judul</th>
          <th>Penulis</th>
          <th>Kategori</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; while ($row = $rst->fetch_assoc()): ?>
          <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center">
              <?php if ($row['gambar']): ?>
                <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>" class="img-thumb">
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['judul']) ?></td>
            <td><?= htmlspecialchars($row['penulis']) ?></td>
            <td><?= htmlspecialchars($row['kategori']) ?></td>
            <td class="text-center">
              <span class="badge-status <?= $row['status'] === 'tersedia' ? 'bg-success text-white' : 'bg-danger text-white' ?>">
                <?= ucfirst($row['status']) ?>
              </span>
            </td>
            <td class="text-center">
              <button class="btn btn-warning btn-action" onclick='editBuku(<?= json_encode($row) ?>)' title="Edit">
                <i class="bi bi-pencil-square"></i>
              </button>
              <button class="btn btn-danger btn-action" onclick="confirmHapus(<?= $row['id'] ?>)" title="Hapus">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalBuku" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" enctype="multipart/form-data" onsubmit="return confirmSimpan(event)">
      <input type="hidden" name="id" id="buku-id">
      <div class="modal-header">
        <h5 class="modal-title">Form Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Judul Buku</label>
          <input type="text" class="form-control" name="judul" id="buku-judul" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Penulis</label>
          <input type="text" class="form-control" name="penulis" id="buku-penulis" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Kategori</label>
          <select class="form-select" name="kategori_id" id="buku-kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <?php while ($k = $rk->fetch_assoc()): ?>
              <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Gambar Buku</label>
          <input type="file" class="form-control" name="gambar" accept="image/*">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<?php if (isset($_GET['msg'])): ?>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const msg = '<?= $_GET['msg'] ?>';
    let title = 'Berhasil', text = '', icon = 'success';
    if (msg === 'tambah') text = 'Data buku berhasil ditambahkan!';
    else if (msg === 'edit') text = 'Data buku berhasil diperbarui!';
    else if (msg === 'hapus') text = 'Data buku berhasil dihapus!';
    else if (msg === 'duplikat') {
      title = 'Gagal!';
      text = 'Judul dan penulis dengan kategori tersebut sudah ada!';
      icon = 'error';
    }
    Swal.fire({ icon, title, text, confirmButtonColor: '#6c63ff' });
  });
</script>
<?php endif; ?>

<script>
function editBuku(data) {
  document.getElementById('buku-id').value = data.id;
  document.getElementById('buku-judul').value = data.judul;
  document.getElementById('buku-penulis').value = data.penulis;
  document.getElementById('buku-kategori').value = data.kategori_id;
  new bootstrap.Modal(document.getElementById('modalBuku')).show();
}
function resetForm() {
  document.getElementById('buku-id').value = '';
  document.getElementById('buku-judul').value = '';
  document.getElementById('buku-penulis').value = '';
  document.getElementById('buku-kategori').value = '';
}
function confirmHapus(id) {
  Swal.fire({
    title: 'Hapus Buku?',
    text: 'Data buku akan dihapus secara permanen!',
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
function confirmSimpan(e) {
  e.preventDefault();
  Swal.fire({
    title: 'Simpan Data?',
    text: 'Pastikan data sudah benar.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, simpan',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      e.target.submit();
    }
  });
}
$(document).ready(() => {
  $('#bukuTable').DataTable();
});
</script>

<?php
include_once 'includes/footer.php';
ob_end_flush();
?>
