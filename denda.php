<?php
$page = 'denda'; 
$page_title = 'Data Denda Peminjaman';
include_once 'includes/db.php';
include_once 'includes/header.php';

$data = $conn->query("
  SELECT p.id, a.nama AS anggota, b.judul AS buku, p.tgl_pinjam, p.tgl_kembali,
    CURDATE() AS today,
    CASE 
      WHEN CURDATE() > p.tgl_kembali THEN DATEDIFF(CURDATE(), p.tgl_kembali) * 1000
      ELSE 0
    END AS denda
  FROM peminjaman p
  JOIN anggota a ON p.anggota_id = a.id
  JOIN buku b ON p.buku_id = b.id
");
?>

<!-- Styles & DataTables CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
  body {
    background-color: #e8f0fe;
    font-family: 'Segoe UI', sans-serif;
  }
  .custom-card {
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  }
  .dataTables_filter input {
    border-radius: 8px; 
    border: 1px solid #ccc;
    padding: 6px 10px;
  }
</style>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary">💸 Denda Peminjaman</h2>
  </div>

  <div class="custom-card p-3">
    <table id="dendaTable" class="table table-bordered table-striped align-middle">
      <thead class="table-primary text-center">
        <tr>
          <th>#</th>
          <th>Nama Anggota</th>
          <th>Judul Buku</th>
          <th>Tanggal Hari Ini</th>
          <th>Tgl Pinjam</th>
          <th>Tgl Kembali</th>
          <th>Denda</th>
        </tr>
      </thead>
      <tbody>
      <?php $i = 1; while ($row = $data->fetch_assoc()): ?>
        <tr class="<?= $row['denda'] > 0 ? 'table-warning' : '' ?>">
          <td class="text-center"><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['anggota']) ?></td>
          <td><?= htmlspecialchars($row['buku']) ?></td>
          <td><?= date('d M Y', strtotime($row['today'])) ?></td>
          <td><?= date('d M Y', strtotime($row['tgl_pinjam'])) ?></td>
          <td><?= date('d M Y', strtotime($row['tgl_kembali'])) ?></td>
          <td class="text-center fw-bold">
            <?php if ($row['denda'] > 0): ?>
              <span class="badge bg-danger text-light">
                Rp <?= number_format($row['denda'], 0, ',', '.') ?>
              </span>
            <?php else: ?>
              <span class="badge bg-success">Tidak Ada Denda</span>
            <?php endif ?>
          </td>
        </tr>
      <?php endwhile ?>
      </tbody>
    </table>
  </div>
</div>

<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function() {
    $('#dendaTable').DataTable({
      paging: true,
      searching: true,
      pageLength: 5,
      lengthMenu: [5, 10, 25, 50],
      language: {
        search: "_INPUT_",
        searchPlaceholder: "🔍 Cari data...",
        lengthMenu: "Tampilkan _MENU_ entri",
        paginate: {
          previous: "←",
          next: "→"
        }
      }
    });
  });
</script>

<?php include 'includes/footer.php'; ?>
