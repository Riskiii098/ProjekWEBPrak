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
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login • SmartLib</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh;">
  <div class="card p-4" style="width:360px;">
    <h3 class="text-center mb-4">SmartLib Login</h3>
    <?php if($msg): ?>
      <div class="alert alert-danger"><?= $msg ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="mb-3"><label class="form-label">Username</label>
        <input name="username" class="form-control" required>
      </div>
      <div class="mb-4"><label class="form-label">Password</label>
        <input name="password" type="password" class="form-control" required>
      </div>
      <button class="btn btn-primary w-100">Login</button>
    </form>
  </div>
</body>
</html>
