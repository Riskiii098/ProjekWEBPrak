<?php
$page = 'kategori'; $page_title = 'Data Kategori Buku';
include 'includes/db.php';
include 'includes/header.php';

if($_SERVER['REQUEST_METHOD']=='POST') {
  $id = intval($_POST['id']);
  $nama = $conn->real_escape_string($_POST['nama']);
  if($id) {
    $conn->query("UPDATE kategori SET nama='$nama' WHERE id=$id");
  } else {
    $conn->query("INSERT INTO kategori(nama) VALUES('$nama')");
  }
  header('Location: kategori.php'); exit;
}

if(isset($_GET['del'])) {
  $conn->query("DELETE FROM kategori WHERE id=".intval($_GET['del']));
  header('Location: kategori.php'); exit;
}

$data = $conn->query("SELECT * FROM kategori");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold">🏷️ Kategori Buku</h2>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKategori">
    <i class="bi bi-plus-circle"></i> Tambah Kategori
  </button>
</div>

<div class="card shadow-sm p-3">
  <table class="table table-striped align-middle">
    <thead class="table-primary">
      <tr>
        <th>#</th>
        <th>Nama Kategori</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php $i=1; while($k = $data->fetch_assoc()): ?>
      <tr>
        <td><?= $i++ ?></td>
        <td class="text-capitalize"><?= htmlspecialchars($k['nama']) ?></td>
        <td>
          <button class="btn btn-warning btn-sm me-1"
                  onclick='editKategori(<?= json_encode($k) ?>)' title="Edit kategori">
            <i class="bi bi-pencil"></i>✏️ Edit
          </button>
          <a href="?del=<?= $k['id'] ?>" class="btn btn-danger btn-sm"
             onclick="return confirm('Yakin ingin menghapus kategori ini?')" title="Hapus kategori">
            <i class="bi bi-trash"></i>🗑️ Hapus
          </a>
        </td>
      </tr>
    <?php endwhile ?>
    </tbody>
  </table>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalKategori" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" onsubmit="return confirm('Simpan kategori ini?')">
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
        <button class="btn btn-success" type="submit">Simpan</button>
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function editKategori(data) {
  document.getElementById('kategori-id').value = data.id;
  document.getElementById('kategori-nama').value = data.nama;
  new bootstrap.Modal(document.getElementById('modalKategori')).show();
}
</script>

<?php include 'includes/footer.php'; ?>
