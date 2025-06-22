<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- Sidebar -->
<nav id="sidebarMenu" class="fw-semibold p-3"
  style="width: 250px; min-height: 100vh; transition: width 0.3s; background-color: #4e73df;" data-state="open">

  <!-- Judul -->
  <div class="mb-3">
    <h4 class="text-white fw-bold" id="sidebarTitle" style="font-family: 'Montserrat', cursive; font-size: 20px;">SmartLib</h4>
  </div>

  <!-- Menu -->
  <ul class="nav flex-column mt-3">
    <li class="nav-item">
      <a class="nav-link text-white mt-2 <?= ($currentPage == 'buku') ? 'bg-opacity-50 bg-white rounded' : '' ?>" href="buku.php">
        <i class="bi bi-journal-bookmark-fill"></i> <span class="link-text"> Buku</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-white mt-2 <?= ($currentPage == 'anggota') ? 'bg-opacity-50 bg-white rounded' : '' ?>" href="anggota.php">
        <i class="bi bi-people-fill"></i> <span class="link-text"> Anggota</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-white mt-2 <?= ($currentPage == 'kategori') ? 'bg-opacity-50 bg-white rounded' : '' ?>" href="kategori.php">
        <i class="bi bi-tags-fill"></i> <span class="link-text"> Kategori</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-white mt-2 <?= ($currentPage == 'peminjaman') ? 'bg-opacity-50 bg-white rounded' : '' ?>" href="peminjaman.php">
        <i class="bi bi-arrow-left-right"></i> <span class="link-text"> Peminjaman</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-white mt-2 <?= ($currentPage == 'denda') ? 'bg-opacity-50 bg-white rounded' : '' ?>" href="denda.php">
        <i class="bi bi-cash-coin"></i> <span class="link-text"> Denda</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-white mt-2 <?= ($currentPage == 'statistik') ? 'bg-opacity-50 bg-white rounded' : '' ?>" href="statistik.php">
        <i class="bi bi-bar-chart-fill"></i> <span class="link-text"> Statistik</span>
      </a>
    </li>
    <li class="nav-item mt-3">
      <!-- Tombol logout akan menggunakan SweetAlert -->
      <a class="nav-link fw-semibold bg-danger text-white rounded" href="#" id="logoutBtn">
        <i class="bi bi-box-arrow-right"></i> <span class="link-text"> Logout</span>
      </a>
    </li>
  </ul>

  <!-- Collapse Button -->
  <button id="toggleSidebar" class="btn btn-outline-light d-flex align-items-center gap-2 mt-4" type="button">
    <span class="toggle-icon"><i class="bi bi-arrow-left-circle-fill"></i></span>
  </button>
</nav>

<!-- Collapse Script -->
<script>
  const toggleBtn = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('sidebarMenu');
  const texts = document.querySelectorAll('.link-text');
  const title = document.getElementById('sidebarTitle');
  const toggleIcon = document.querySelector('.toggle-icon i');

  toggleBtn.addEventListener('click', function () {
    const isOpen = sidebar.getAttribute('data-state') === 'open';

    if (isOpen) {
      sidebar.style.width = '80px';
      texts.forEach(t => t.style.display = 'none');
      title.style.display = 'none';
      toggleIcon.className = 'bi bi-arrow-right-circle-fill';
      sidebar.setAttribute('data-state', 'collapsed');
    } else {
      sidebar.style.width = '250px';
      texts.forEach(t => t.style.display = 'inline');
      title.style.display = 'block';
      toggleIcon.className = 'bi bi-arrow-left-circle-fill';
      sidebar.setAttribute('data-state', 'open');
    }
  });
</script>

<!-- SweetAlert Logout -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.getElementById('logoutBtn').addEventListener('click', function (e) {
    e.preventDefault();
    Swal.fire({
      title: 'Yakin ingin logout?',
      text: "Sesi Anda akan diakhiri.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, logout',
      cancelButtonText: 'Batal',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'logout.php';
      }
    });
  });
</script>
