<?php
$page = 'denda'; $page_title = 'Data Denda Peminjaman';
include 'includes/db.php';
include 'includes/header.php';

$data = $conn->query("
  SELECT p.id, a.nama AS anggota, b.judul AS buku, p.tgl_pinjam, p.tgl_kembali,
    CASE 
      WHEN CURDATE() > p.tgl_kembali THEN DATEDIFF(CURDATE(), p.tgl_kembali) * 1000
      ELSE 0
    END AS denda
  FROM peminjaman p
  JOIN anggota a ON p.anggota_id = a.id
  JOIN buku b ON p.buku_id = b.id
");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold">💸 Denda Peminjaman</h2>
</div>

<div class="card shadow-sm p-3">
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-danger text-center">
      <tr>
        <th>#</th>
        <th>Nama Anggota</th>
        <th>Judul Buku</th>
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
        <td><?= date('d M Y', strtotime($row['tgl_pinjam'])) ?></td>
        <td><?= date('d M Y', strtotime($row['tgl_kembali'])) ?></td>
        <td class="text-end fw-bold">
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

<?php include 'includes/footer.php'; ?>
