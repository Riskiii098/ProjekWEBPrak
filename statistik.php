<?php
$page = 'statistik'; $page_title = 'Statistik Pemakaian';
include 'includes/db.php';
include 'includes/header.php';

// Hitung data
$total_buku = $conn->query("SELECT COUNT(*) as total FROM buku")->fetch_assoc()['total'];
$total_anggota = $conn->query("SELECT COUNT(*) as total FROM anggota")->fetch_assoc()['total'];
$pinjam_aktif = $conn->query("SELECT COUNT(*) as total FROM peminjaman")->fetch_assoc()['total'];
$dipinjam = $conn->query("SELECT COUNT(*) as total FROM buku WHERE status='dipinjam'")->fetch_assoc()['total'];
$tersedia = $conn->query("SELECT COUNT(*) as total FROM buku WHERE status='tersedia'")->fetch_assoc()['total'];
?>

<div class="container py-4">
  <h2 class="fw-bold mb-4">📊 Statistik Pemakaian SmartLib</h2>
  <div class="row g-4">
    <div class="col-md-3">
      <div class="card text-bg-primary">
        <div class="card-body">
          <h5>Total Buku</h5>
          <h2><?= $total_buku ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-bg-success">
        <div class="card-body">
          <h5>Total Anggota</h5>
          <h2><?= $total_anggota ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-bg-warning">
        <div class="card-body">
          <h5>Buku Dipinjam</h5>
          <h2><?= $dipinjam ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-bg-info">
        <div class="card-body">
          <h5>Buku Tersedia</h5>
          <h2><?= $tersedia ?></h2>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
