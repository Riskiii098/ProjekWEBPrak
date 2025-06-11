<?php
$page = 'buku';
$page_title = 'Data Buku';
include 'includes/db.php';
include 'includes/header.php';

// Handle Create / Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = intval($_POST['id']);
  $judul = $conn->real_escape_string(trim($_POST['judul']));
  $penulis = $conn->real_escape_string(trim($_POST['penulis']));
  $kat = intval($_POST['kategori_id']);
  $status = isset($_POST['status']) ? trim($_POST['status']) : '';

  // Validasi status hanya boleh 'tersedia' atau 'dipinjam'
  $validStatus = ['tersedia', 'dipinjam'];
  if (!in_array($status, $validStatus)) {
    die("❌ Status tidak valid.");
  }
  $status = $conn->real_escape_string($status);

  if ($id > 0) {
    $conn->query("UPDATE buku SET judul='$judul', penulis='$penulis', kategori_id=$kat, status='$status' WHERE id=$id");
  } else {
    $conn->query("INSERT INTO buku(judul, penulis, kategori_id, status) VALUES('$judul','$penulis',$kat,'$status')");
  }

  header('Location: buku.php');
  exit;
}

// Handle Delete
if (isset($_GET['del'])) {
  $conn->query("DELETE FROM buku WHERE id=" . intval($_GET['del']));
  header('Location: buku.php');
  exit;
}

// Ambil data buku dan kategori
$rst = $conn->query("SELECT b.*, k.nama AS kategori FROM buku b JOIN kategori k ON b.kategori_id=k.id ORDER BY b.id");
$rk = $conn->query("SELECT * FROM kategori");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold">📚 Data Buku</h2>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuku">
    <i class="bi bi-plus-circle"></i> Tambah Buku
  </button>
</div>

<div class="card shadow-sm p-3">
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-primary text-center">
      <tr>
        <th>#</th>
        <th>Judul</th>
        <th>Penulis</th>
        <th>Kategori</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php $i = 1; while ($row = $rst->fetch_assoc()): ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($row['judul']) ?></td>
        <td><?= htmlspecialchars($row['penulis']) ?></td>
        <td><?= htmlspecialchars($row['kategori']) ?></td>
        <td>
          <span class="badge <?= $row['status'] == 'tersedia' ? 'bg-success' : 'bg-danger' ?>">
            <?= ucfirst(htmlspecialchars($row['status'])) ?>
          </span>
        </td>
        <td class="text-center">
          <button class="btn btn-warning btn-sm mb-1"
            onclick='editBuku(<?= json_encode($row) ?>)'>✏️ Edit</button>
          <a href="?del=<?= $row['id'] ?>"
             class="btn btn-danger btn-sm"
             onclick="return confirm('Yakin ingin menghapus buku ini?')">🗑️ Hapus</a>
        </td>
      </tr>
    <?php endwhile ?>
    </tbody>
  </table>
</div>

<!-- Modal Tambah/Edit Buku -->
<div class="modal fade" id="modalBuku" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" onsubmit="return confirm('Simpan data buku ini?')">
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
            <option value="">-- Pilih --</option>
            <?php while ($k = $rk->fetch_assoc()): ?>
              <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
            <?php endwhile ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Status</label>
          <select class="form-select" name="status" id="buku-status" required>
            <option value="tersedia">Tersedia</option>
            <option value="dipinjam">Dipinjam</option>
          </select>
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
function editBuku(data) {
  document.getElementById('buku-id').value = data.id;
  document.getElementById('buku-judul').value = data.judul;
  document.getElementById('buku-penulis').value = data.penulis;
  document.getElementById('buku-kategori').value = data.kategori_id;
  document.getElementById('buku-status').value = data.status;
  new bootstrap.Modal(document.getElementById('modalBuku')).show();
}
</script>

<?php include 'includes/footer.php'; ?>
