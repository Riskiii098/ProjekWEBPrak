<?php
$page = 'statistik';
$page_title = 'Statistik Pemakaian';
include 'includes/db.php';
include 'includes/header.php';

$total_buku = $conn->query("SELECT COUNT(*) as total FROM buku")->fetch_assoc()['total'];
$total_anggota = $conn->query("SELECT COUNT(*) as total FROM anggota")->fetch_assoc()['total'];
$dipinjam = $conn->query("SELECT COUNT(*) as total FROM buku WHERE status='dipinjam'")->fetch_assoc()['total'];
$tersedia = $conn->query("SELECT COUNT(*) as total FROM buku WHERE status='tersedia'")->fetch_assoc()['total'];

// Statistik kategori buku (berdasarkan tabel kategori)
$kategori_data = $conn->query("SELECT k.nama AS kategori, COUNT(*) AS jumlah FROM buku b JOIN kategori k ON b.kategori_id = k.id GROUP BY k.nama");
$kategori_labels = [];
$kategori_counts = [];
while ($k = $kategori_data->fetch_assoc()) {
  $kategori_labels[] = $k['kategori'];
  $kategori_counts[] = $k['jumlah'];
}

// Statistik peminjaman bulanan
$bulan_data = $conn->query("SELECT MONTH(tgl_pinjam) as bulan, COUNT(*) as jumlah FROM peminjaman GROUP BY MONTH(tgl_pinjam)");
$bulan_labels = [];
$bulan_counts = [];
$nama_bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
$bulan_map = array_fill(0, 12, 0);
while ($b = $bulan_data->fetch_assoc()) {
  $bulan_map[$b['bulan'] - 1] = $b['jumlah'];
}
$bulan_labels = json_encode($nama_bulan);
$bulan_counts = json_encode(array_values($bulan_map));

$kategori_labels_json = json_encode($kategori_labels);
$kategori_counts_json = json_encode($kategori_counts);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
  body { background-color: #e8f0fe; font-family: 'Segoe UI', sans-serif; }
  .custom-card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); transition: 0.3s ease; }
  .custom-card:hover { transform: translateY(-3px); }
  .counter { font-size: 2rem; font-weight: bold; }
  .chart-wrapper { max-width: 320px; margin: auto; }
</style>

<div class="container py-4">
  <h2 class="fw-bold text-primary mb-4"><i class="bi bi-bar-chart-line-fill me-2"></i>Statistik SmartLib</h2>
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card text-bg-primary custom-card text-center">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-journal-bookmark-fill me-2"></i>Total Buku</h6>
          <div class="counter" id="totalBuku"><?= $total_buku ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-bg-success custom-card text-center">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-people-fill me-2"></i>Total Anggota</h6>
          <div class="counter" id="totalAnggota"><?= $total_anggota ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-bg-warning custom-card text-center">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-book-fill me-2"></i>Buku Dipinjam</h6>
          <div class="counter" id="bukuDipinjam"><?= $dipinjam ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-bg-info custom-card text-center">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-book-half me-2"></i>Buku Tersedia</h6>
          <div class="counter" id="bukuTersedia"><?= $tersedia ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-6">
      <div class="card custom-card p-4">
        <h6 class="fw-semibold text-center mb-3"><i class="bi bi-graph-up-arrow me-2"></i>Grafik Peminjaman Bulanan</h6>
        <canvas id="barChart" height="200"></canvas>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card custom-card p-4 text-center">
        <h6 class="fw-semibold mb-3"><i class="bi bi-pie-chart-fill me-2"></i>Donut Chart Buku</h6>
        <div class="chart-wrapper">
          <canvas id="donutChart" height="200"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="card custom-card p-4">
        <h6 class="fw-semibold text-center mb-3"><i class="bi bi-layout-text-window me-2"></i>Statistik Kategori Buku</h6>
        <canvas id="genreChart" height="100"></canvas>
      </div>
    </div>
  </div>
</div>

<script>
function animateCounter(id, end) {
  let current = 0;
  const stepTime = Math.max(Math.floor(1000 / end), 20);
  const obj = document.getElementById(id);
  const timer = setInterval(() => {
    obj.textContent = current;
    if (current >= end) clearInterval(timer);
    current++;
  }, stepTime);
}
animateCounter('totalBuku', <?= $total_buku ?>);
animateCounter('totalAnggota', <?= $total_anggota ?>);
animateCounter('bukuDipinjam', <?= $dipinjam ?>);
animateCounter('bukuTersedia', <?= $tersedia ?>);

// Donut Chart
new Chart(document.getElementById('donutChart'), {
  type: 'doughnut',
  data: {
    labels: ['Dipinjam', 'Tersedia'],
    datasets: [{
      data: [<?= $dipinjam ?>, <?= $tersedia ?>],
      backgroundColor: ['#ffc107', '#0dcaf0'],
      borderColor: ['#fff', '#fff'],
      borderWidth: 2
    }]
  },
  options: {
    responsive: true,
    animation: { duration: 0 },
    transitions: {
      active: { animation: { duration: 0 } },
      show: { animations: { radius: { duration: 0 }, outerRadius: { duration: 0 }, innerRadius: { duration: 0 } } },
      hide: { animations: { radius: { duration: 0 }, outerRadius: { duration: 0 }, innerRadius: { duration: 0 } } }
    },
    plugins: { legend: { display: true, position: 'bottom' } },
    cutout: '60%'
  }
});

// Bar Chart Bulanan
new Chart(document.getElementById('barChart'), {
  type: 'bar',
  data: {
    labels: <?= $bulan_labels ?>,
    datasets: [{
      label: 'Peminjaman',
      data: <?= $bulan_counts ?>,
      backgroundColor: '#0d6efd'
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { precision: 0 }
      }
    }
  }
});

// Kategori Chart
new Chart(document.getElementById('genreChart'), {
  type: 'bar',
  data: {
    labels: <?= $kategori_labels_json ?>,
    datasets: [{
      label: 'Jumlah Buku',
      data: <?= $kategori_counts_json ?>,
      backgroundColor: '#20c997'
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { precision: 0 }
      }
    }
  }
});
</script>

<?php include 'includes/footer.php'; ?>
