<?php
$page = 'anggota'; $page_title = 'Data Anggota';
include 'includes/db.php';
include 'includes/header.php';

if($_SERVER['REQUEST_METHOD']=='POST') {
  $id = intval($_POST['id']);
  $nama = $conn->real_escape_string($_POST['nama']);
  $email = $conn->real_escape_string($_POST['email']);
  $telepon = $conn->real_escape_string($_POST['telepon']);

  if($id) {
    $conn->query("UPDATE anggota SET nama='$nama', email='$email', telepon='$telepon' WHERE id=$id");
  } else {
    $conn->query("INSERT INTO anggota(nama,email,telepon) VALUES('$nama','$email','$telepon')");
  }
  header('Location: anggota.php'); exit;
}

if(isset($_GET['del'])) {
  $conn->query("DELETE FROM anggota WHERE id=".intval($_GET['del']));
  header('Location: anggota.php'); exit;
}

$data = $conn->query("SELECT * FROM anggota");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold">👤 Data Anggota Perpustakaan</h2>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAnggota">
    <i class="bi bi-plus-circle"></i> Tambah Anggota
  </button>
</div>

<div class="card shadow-sm p-3">
  <table class="table table-striped table-bordered align-middle">
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
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($a['nama']) ?></td>
        <td><?= htmlspecialchars($a['email']) ?></td>
        <td><?= htmlspecialchars($a['telepon']) ?></td>
        <td class="text-center">
          <button class="btn btn-warning btn-sm mb-1"
                  onclick='editAnggota(<?= json_encode($a) ?>)'>
            ✏️ Edit
          </button>
          <a href="?del=<?= $a['id'] ?>" class="btn btn-danger btn-sm"
             onclick="return confirm('Yakin ingin menghapus anggota ini?')">
            🗑️ Hapus
          </a>
        </td>
      </tr>
    <?php endwhile ?>
    </tbody>
  </table>
</div>

<!-- Modal Tambah/Edit Anggota -->
<div class="modal fade" id="modalAnggota" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" onsubmit="return confirm('Simpan data anggota ini?')">
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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function editAnggota(data) {
  document.getElementById('anggota-id').value = data.id;
  document.getElementById('anggota-nama').value = data.nama;
  document.getElementById('anggota-email').value = data.email;
  document.getElementById('anggota-telepon').value = data.telepon;
  new bootstrap.Modal(document.getElementById('modalAnggota')).show();
}
</script>

<?php include 'includes/footer.php'; ?>
