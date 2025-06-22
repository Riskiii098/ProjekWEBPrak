# 📚 SmartLib – Aplikasi Manajemen Perpustakaan Berbasis Web

**SmartLib** adalah sistem informasi perpustakaan berbasis web yang dibangun menggunakan PHP Native dan MySQL. Aplikasi ini dirancang untuk memudahkan pengelolaan buku, anggota, peminjaman, pengembalian, serta perhitungan denda keterlambatan. SmartLib mendukung interface yang modern dengan integrasi Bootstrap dan DataTables.

---

## 🧭 Ringkasan Proyek

Aplikasi ini dirancang untuk memenuhi kebutuhan pengelolaan perpustakaan sederhana secara digital. Admin dapat mengelola koleksi buku, kategori, data anggota, mencatat peminjaman, serta memantau keterlambatan yang otomatis menghasilkan denda.

> 🎓 *Mata Kuliah*: Pemrograman Web  
> 🧑‍💻 *Dikembangkan oleh*:
> - M. Riski P - 2317051037
> - Nabila Cahaya Putri - 2357051010
> - Nurul Aisyah - 2317051031	
> - Rahayu Indah Lestari – 2317051073
>
> 🏫 *Universitas*: Universitas Lampung, Prodi Ilmu Komputer – 2024/2025

---

## 📸 Tampilan Antarmuka

![image](https://github.com/user-attachments/assets/f6374e95-ff26-49bb-b13c-d26da762a914)

![image](https://github.com/user-attachments/assets/09ea3e7d-3b43-4cbb-811c-de5141ecd4b4)

![image](https://github.com/user-attachments/assets/e546df11-2422-4e1d-926c-06601b375755)

![image](https://github.com/user-attachments/assets/8e98e7ee-3018-4137-bba2-ddff2201adb2)

![image](https://github.com/user-attachments/assets/ec353df8-9a84-40e9-9a35-dd437aae4f2a)

![image](https://github.com/user-attachments/assets/2f0d590b-c375-4b8a-9862-e02515f05b6c)

![image](https://github.com/user-attachments/assets/7be19bc7-0a72-4731-b880-4ed95d94090c)






---

## 🛠 Teknologi yang Digunakan

| Komponen         | Teknologi                         |
|------------------|------------------------------------|
| Backend          | PHP Native                         |
| Frontend         | HTML, CSS, Bootstrap 5             |
| Database         | MySQL                              |
| Tabel Dinamis    | DataTables.js                      |
| SweetAlert       | SweetAlert2                        |
| Server Lokal     | Laragon / XAMPP                    |

---

## 🗂️ Fitur Utama

- 🔍 **Manajemen Buku** (CRUD + gambar cover)
- 🧑‍🤝‍🧑 **Manajemen Anggota**
- 🏷️ **Kategori Buku**
- 📖 **Peminjaman & Pengembalian**
- 💸 **Perhitungan Denda Otomatis**
- 📊 **Statistik Ringkas**
- 🔐 **Login Admin**
- 💬 **Notifikasi interaktif (SweetAlert2)**

---
## ⚙️ Cara Instalasi (Localhost)
1. Clone repository ini ke folder Laragon:
   ```bash
   git clone https://github.com/Riskiii098/ProjekWEBPrak.git

2. Pindahkan ke direktori:
   ```bash
D:\laragon\www\smartlib

3. Import database smartlib.sql ke phpMyAdmin

4. Konfigurasi file includes/db.php:
   ```bash
   $host = "localhost";
$user = "root";
$pass = "";
$db   = "smartlib";

5.Jalankan di browser:
   ```bash
   http://localhost/smartlib/login.php

6. Login Admin:
```bash
username: admin
Password: admin123

---
---
##📄 Lisensi
Proyek ini dikembangkan untuk keperluan akademik dan pembelajaran.
