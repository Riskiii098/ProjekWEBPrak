<?php
session_start();
if(isset($_SESSION['admin'])) {
  header('Location: buku.php'); exit;
}
require 'includes/db.php';
$msg = '';
if($_SERVER['REQUEST_METHOD']=='POST') {
  $u = $conn->real_escape_string($_POST['username']);
  $p = hash('sha256', $_POST['password']);
  $res = $conn->query("SELECT * FROM admin WHERE username='$u' AND password='$p'");
  if($res->num_rows) {
    $_SESSION['admin'] = $u;
    header('Location: buku.php'); exit;
  } else $msg = 'Username atau password salah';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login • SmartLib</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="bg-white shadow-xl rounded-3xl overflow-hidden w-full max-w-5xl flex flex-col md:flex-row">
    <div class="hidden md:flex flex-col items-center justify-center bg-blue-600 text-white w-1/2 p-10">
      <img src="https://cdn-icons-png.flaticon.com/512/3064/3064197.png" alt="Library Icon" class="w-24 h-24 mb-4">
      <h2 class="text-3xl font-bold mb-2">Welcome to SmartLib</h2>
      <p class="text-center text-sm opacity-90">Sistem Perpustakaan Digital untuk Mahasiswa & Admin</p>
    </div>
    <div class="w-full md:w-1/2 p-8 md:p-10">
      <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">Admin Login</h2>

      <?php if($msg): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
          <?= $msg ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="space-y-5">
        <div>
          <label class="block mb-1 text-sm font-medium text-gray-700">Username</label>
          <input name="username" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" />
        </div>

        <div>
          <label class="block mb-1 text-sm font-medium text-gray-700">Password</label>
          <input name="password" type="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" />
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition-all duration-200">
          Masuk
        </button>
      </form>

      <p class="text-xs text-center text-gray-400 mt-6">© <?= date('Y') ?> SmartLib. All rights reserved.</p>
    </div>
  </div>
</body>
</html>
