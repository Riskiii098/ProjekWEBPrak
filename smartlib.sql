-- Buat database
CREATE DATABASE IF NOT EXISTS smartlib CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smartlib;

-- Tabel admin (login)
CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL
);

-- Masukkan satu user default (password: admin123)
INSERT INTO admin (username, password)
VALUES ('admin', SHA2('admin123',256));

-- Tabel kategori
CREATE TABLE kategori (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL
);

-- Tabel anggota
CREATE TABLE anggota (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  telepon VARCHAR(20)
);

-- Tabel buku
CREATE TABLE buku (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(200) NOT NULL,
  penulis VARCHAR(100) NOT NULL,
  kategori_id INT NOT NULL,
  status ENUM('available','borrowed') NOT NULL DEFAULT 'available',
  FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE
);

-- Tabel peminjaman
CREATE TABLE peminjaman (
  id INT AUTO_INCREMENT PRIMARY KEY,
  anggota_id INT NOT NULL,
  buku_id INT NOT NULL,
  tgl_pinjam DATE NOT NULL,
  tgl_kembali DATE DEFAULT NULL,
  FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON DELETE CASCADE,
  FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE CASCADE
);

-- Tabel denda
CREATE TABLE denda (
  id INT AUTO_INCREMENT PRIMARY KEY,
  peminjaman_id INT NOT NULL,
  jumlah DECIMAL(10,2) NOT NULL,
  status ENUM('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  FOREIGN KEY (peminjaman_id) REFERENCES peminjaman(id) ON DELETE CASCADE
);