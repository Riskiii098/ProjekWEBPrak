<nav class="sidebar p-3">
  <a class="navbar-brand text-white mb-4 d-block" href="buku.php">SmartLib</a>
  <ul class="nav flex-column">
    <li class="nav-item"><a class="nav-link <?= $page=='buku'?'active':'' ?>" href="buku.php">Buku</a></li>
    <li class="nav-item"><a class="nav-link <?= $page=='anggota'?'active':'' ?>" href="anggota.php">Anggota</a></li>
    <li class="nav-item"><a class="nav-link <?= $page=='kategori'?'active':'' ?>" href="kategori.php">Kategori</a></li>
    <li class="nav-item"><a class="nav-link <?= $page=='peminjaman'?'active':'' ?>" href="peminjaman.php">Peminjaman</a></li>
    <li class="nav-item"><a class="nav-link <?= $page=='denda'?'active':'' ?>" href="denda.php">Denda</a></li>
    <li class="nav-item"><a class="nav-link <?= $page=='statistik'?'active':'' ?>" href="statistik.php">Statistik</a></li>
    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
  </ul>
</nav>
