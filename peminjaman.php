<?php
ob_start();
$page = 'peminjaman';
$page_title = 'Data Peminjaman';

include_once 'includes/db.php';
include_once 'includes/header.php';

$today = date('Y-m-d');
$all_buku = $conn->query("SELECT * FROM buku ORDER BY judul");
$anggota = $conn->query("SELECT * FROM anggota ORDER BY nama");

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = intval($_POST['id']);
  $anggota_id = intval($_POST['anggota_id']);
  $buku_id = intval($_POST['buku_id']);
  $tgl_pinjam = $_POST['tgl_pinjam'];
  $tgl_kembali = $_POST['tgl_kembali'];

  if ($tgl_kembali < $tgl_pinjam) {
    $error = 'Tanggal kembali tidak boleh sebelum tanggal pinjam!';
  } else {
    if ($id) {
      $old = $conn->query("SELECT buku_id FROM peminjaman WHERE id=$id")->fetch_assoc();
      $old_buku_id = $old['buku_id'];
      $conn->query("UPDATE peminjaman SET anggota_id=$anggota_id, buku_id=$buku_id, tgl_pinjam='$tgl_pinjam', tgl_kembali='$tgl_kembali' WHERE id=$id");
      if ($old_buku_id != $buku_id) {
        $conn->query("UPDATE buku SET status='tersedia' WHERE id=$old_buku_id");
        $conn->query("UPDATE buku SET status='dipinjam' WHERE id=$buku_id");
      }
      header('Location: peminjaman.php?msg=edit');
      exit;
    } else {
      $cek = $conn->query("SELECT status FROM buku WHERE id=$buku_id")->fetch_assoc();
      if ($cek['status'] === 'dipinjam') {
        $error = 'Buku sedang dipinjam!';
      } else {
        $conn->query("INSERT INTO peminjaman(anggota_id, buku_id, tgl_pinjam, tgl_kembali) VALUES($anggota_id, $buku_id, '$tgl_pinjam', '$tgl_kembali')");
        $conn->query("UPDATE buku SET status='dipinjam' WHERE id=$buku_id");
        header('Location: peminjaman.php?msg=tambah');
        exit;
      }
    }
  }
}

if (isset($_GET['del'])) {
  $id = intval($_GET['del']);
  $buku = $conn->query("SELECT buku_id FROM peminjaman WHERE id=$id")->fetch_assoc();
  if ($buku) {
    $conn->query("UPDATE buku SET status='tersedia' WHERE id={$buku['buku_id']}");
    $conn->query("DELETE FROM peminjaman WHERE id=$id");
  }
  header('Location: peminjaman.php?msg=hapus');
  exit;
}

$data = $conn->query("SELECT p.*, a.nama AS anggota, b.judul AS buku, b.penulis, p.anggota_id, p.buku_id
                      FROM peminjaman p 
                      JOIN anggota a ON p.anggota_id = a.id 
                      JOIN buku b ON p.buku_id = b.id 
                      ORDER BY p.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $page_title ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #e8f0fe; font-family: 'Segoe UI', sans-serif; }
    .custom-card { background: #fff; border-radius:12px; padding:20px; box-shadow:0 4px 10px rgba(0,0,0,0.05); }
    .btn-action { margin: 0 3px; }
  </style>
</head>
<body>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold text-primary">📖 Data Peminjaman</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalPinjam" onclick="resetForm()">
      <i class="bi bi-plus-circle"></i> Tambah
    </button>
  </div>

  <?php if($error): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      Swal.fire({ icon: 'error', title: 'Gagal', text: '<?= $error ?>', confirmButtonColor: '#6c63ff' });
    </script>
  <?php endif; ?>

  <?php if(isset($_GET['msg'])): ?>
    <script>
      const M = '<?= $_GET['msg'] ?>';
      const txt = M === 'tambah' ? 'Berhasil ditambahkan!' : M === 'edit' ? 'Berhasil diperbarui!' : 'Berhasil dihapus!';
      Swal.fire({ icon: 'success', title: 'Berhasil', text: txt, confirmButtonColor: '#6c63ff' });
    </script>
  <?php endif; ?>

  <div class="custom-card">
    <table id="pinjamTable" class="table table-striped table-bordered align-middle">
      <thead class="table-primary text-center">
        <tr>
          <th>No</th><th>Anggota</th><th>Buku</th><th>Penulis</th><th>Pinjam</th><th>Kembali</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while($r = $data->fetch_assoc()): ?>
        <tr>
          <td class="text-center"><?= $no++ ?></td>
          <td><?= htmlspecialchars($r['anggota']) ?></td>
          <td><?= htmlspecialchars($r['buku']) ?></td>
          <td><?= htmlspecialchars($r['penulis']) ?></td>
          <td><?= $r['tgl_pinjam'] ?></td>
          <td><?= $r['tgl_kembali'] ?></td>
          <td class="text-center">
            <button class="btn btn-warning btn-action" onclick='editPinjam(<?= htmlspecialchars(json_encode($r), ENT_QUOTES, "UTF-8") ?>)' title="Edit">
              <i class="bi bi-pencil-square"></i>
            </button>
            <button class="btn btn-danger btn-action" onclick="confirmHapus(<?= $r['id'] ?>)" title="Hapus">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalPinjam" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" onsubmit="return confirmSave(event)">
      <input type="hidden" name="id" id="form-id">
      <div class="modal-header">
        <h5 class="modal-title">Form Peminjaman</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Anggota</label>
          <select id="form-anggota" name="anggota_id" class="form-select" required>
            <option value="">-- Pilih --</option>
            <?php foreach($anggota as $a): ?>
              <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nama']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Buku</label>
          <select id="form-buku" name="buku_id" class="form-select" required>
            <option value="">-- Pilih --</option>
            <?php foreach($all_buku as $b): ?>
              <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['judul']) ?> — <?= htmlspecialchars($b['penulis']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Tanggal Pinjam</label>
          <input type="date" id="form-tgl-pinjam" name="tgl_pinjam" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Tanggal Kembali</label>
          <input type="date" id="form-tgl-kembali" name="tgl_kembali" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
function editPinjam(d) {
  $('#form-id').val(d.id);
  $('#form-anggota').val(d.anggota_id);
  $('#form-buku').val(d.buku_id);
  $('#form-tgl-pinjam').val(d.tgl_pinjam);
  $('#form-tgl-kembali').val(d.tgl_kembali).attr('min', d.tgl_pinjam);
  new bootstrap.Modal($('#modalPinjam')).show();
}

function resetForm() {
  $('#form-id, #form-anggota, #form-buku').val('');
  $('#form-tgl-pinjam').val('<?= $today ?>');
  $('#form-tgl-kembali').val('').attr('min','<?= $today ?>');
}

function confirmHapus(id){
  Swal.fire({
    title:'Hapus Peminjaman?',
    text:'Data akan dihapus permanen!',
    icon:'warning',showCancelButton:true,
    confirmButtonColor:'#d33',
    cancelButtonText:'Batal'
  }).then(r=> r.isConfirmed && (window.location.href='?del='+id));
}

function confirmSave(e){
  e.preventDefault();
  Swal.fire({
    title:'Simpan Data?',
    icon:'question',
    showCancelButton:true,
    confirmButtonText:'Ya, Simpan',
    cancelButtonText:'Batal'
  }).then(r=> r.isConfirmed && e.target.submit());
}

$(document).ready(()=>{
  $('#pinjamTable').DataTable({
    paging:true,
    searching:true,
    lengthMenu:[5,10,25],
    pageLength:5,
    responsive:true,
    language:{
      search:'_INPUT_',
      searchPlaceholder:'🔍 Cari...',
      lengthMenu:'Tampilkan _MENU_ entri',
      paginate:{next:'>>',previous:'<<'}
    }
  });
  $('[data-bs-toggle="tooltip"]').tooltip();
  resetForm();
});
</script>
</body>
</html>

<?php include 'includes/footer.php'; ?>
<?php ob_end_flush(); ?>
