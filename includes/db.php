<?php
// db.php — koneksi ke MySQL
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'smartlib';
$conn = new mysqli($host,$user,$pass,$db);
if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
