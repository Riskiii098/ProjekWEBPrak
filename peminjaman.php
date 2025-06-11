<?php
$page = 'peminjaman'; $page_title = 'Data Peminjaman';
include 'includes/db.php';
include 'includes/header.php';

// Ambil semua buku (baik tersedia maupun dipinjam) untuk edit
$all_buku = $conn->query("SELECT * FROM buku ORDER BY judul");

// POST Create & Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = intval($_POST['id']);
  $anggota_id = intval($_POST['anggota_id']);
  $buku_id = intval($_POST['buku_id']);
  $tgl_pinjam = $_POST['tgl_pinjam'];
  $tgl_kembali = $_POST['tgl_kembali'];

  if ($id) {
    $old = $conn->query("SELECT buku_id FROM peminjaman WHERE id=$id")->fetch_assoc();
    $old_buku_id = $old['buku_id'];

    $conn->query("UPDATE peminjaman SET anggota_id=$anggota_id, buku_id=$buku_id, tgl_pinjam='$tgl_pinjam', tgl_kembali='$tgl_kembali' WHERE id=$id");

    if ($old_buku_id != $buku_id) {
      $conn->query("UPDATE buku SET status='tersedia' WHERE id=$old_buku_id");
      $conn->query("UPDATE buku SET status='dipinjam' WHERE id=$buku_id");
    }
  } else {
    $conn->query("INSERT INTO peminjaman(anggota_id, buku_id, tgl_pinjam, tgl_kembali) VALUES($anggota_id, $buku_id, '$tgl_pinjam', '$tgl_kembali')");
    $conn->query("UPDATE buku SET status='dipinjam' WHERE id=$buku_id");
  }

  header('Location: peminjaman.php');
  exit;
}

// Hapus
if (isset($_GET['del'])) {
  $id = intval($_GET['del']);
  $buku = $conn->query("SELECT buku_id FROM peminjaman WHERE id=$id")->fetch_assoc();
  $conn->query("UPDATE buku SET status='tersedia' WHERE id={$buku['buku_id']}");
  $conn->query("DELETE FROM peminjaman WHERE id=$id");
  header('Location: peminjaman.php');
  exit;
}

// Data peminjaman
$data = $conn->query("SELECT p.*, a.nama AS anggota, b.judul AS buku FROM peminjaman p
                      JOIN anggota a ON p.anggota_id = a.id
                      JOIN buku b ON p.buku_id = b.id
                      ORDER BY p.id DESC");

$anggota = $conn->query("SELECT * FROM anggota ORDER BY nama");
$buku_tersedia = $conn->query("SELECT * FROM buku WHERE status='tersedia' ORDER BY judul");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold">📖 Peminjaman Buku</h2>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPinjam" onclick="resetForm()">
    <i class="bi bi-plus-circle"></i> Tambah Peminjaman
  </button>
</div>

<div class="card shadow-sm p-3">
  <table class="table table-striped align-middle">
    <thead class="table-primary">
      <tr>
        <th>#</th>
        <th>Nama Anggota</th>
        <th>Judul Buku</th>
        <th>Tgl Pinjam</th>
        <th>Tgl Kembali</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php $i = 1; while ($p = $data->fetch_assoc()): ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($p['anggota']) ?></td>
        <td><?= htmlspecialchars($p['buku']) ?></td>
        <td><?= $p['tgl_pinjam'] ?></td>
        <td><?= $p['tgl_kembali'] ?></td>
        <td>
          <button class="btn btn-warning btn-sm mb-1" onclick='editPinjam(<?= json_encode($p) ?>)'>
            ✏️ Edit
          </button>
          <a href="?del=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
             onclick="return confirm('Yakin hapus peminjaman ini?')" title="Hapus">
            🗑️ Hapus
          </a>
        </td>
      </tr>
    <?php endwhile ?>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="modalPinjam" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" onsubmit="return confirm('Simpan data peminjaman ini?')">
      <input type="hidden" name="id" id="form-id">
      <div class="modal-header">
        <h5 class="modal-title">Form Peminjaman</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Anggota</label>
          <select name="anggota_id" id="form-anggota" class="form-select" required>
            <option value="">-- Pilih Anggota --</option>
            <?php $anggota->data_seek(0); while ($a = $anggota->fetch_assoc()): ?>
              <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nama']) ?></option>
            <?php endwhile ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Buku</label>
          <select name="buku_id" id="form-buku" class="form-select" required>
            <option value="">-- Pilih Buku --</option>
            <?php $all_buku->data_seek(0); while ($b = $all_buku->fetch_assoc()): ?>
              <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['judul']) ?></option>
            <?php endwhile ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Tanggal Pinjam</label>
          <input type="date" name="tgl_pinjam" id="form-tgl-pinjam" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Tanggal Kembali</label>
          <input type="date" name="tgl_kembali" id="form-tgl-kembali" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" type="submit">💾 Simpan</button>
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">❌ Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function editPinjam(data) {
  document.getElementById('form-id').value = data.id;
  document.getElementById('form-anggota').value = data.anggota_id;
  document.getElementById('form-buku').value = data.buku_id;
  document.getElementById('form-tgl-pinjam').value = data.tgl_pinjam;
  document.getElementById('form-tgl-kembali').value = data.tgl_kembali;
  new bootstrap.Modal(document.getElementById('modalPinjam')).show();
}

function resetForm() {
  document.getElementById('form-id').value = '';
  document.getElementById('form-anggota').value = '';
  document.getElementById('form-buku').value = '';
  document.getElementById('form-tgl-pinjam').value = '';
  document.getElementById('form-tgl-kembali').value = '';
}
</script>

<?php include 'includes/footer.php'; ?>
