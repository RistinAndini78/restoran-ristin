# Panduan Instalasi: Restoran Prime E-Commerce

Aplikasi E-Commerce Restoran Modern berbasis PHP Native dan MySQL.

## Prasyarat
- XAMPP atau Laragon (PHP >= 7.4)
- Web Browser (Chrome/Edge/Firefox)

## Langkah-Langkah Instalasi

### 1. Persiapan Folder
Ekstrak atau letakkan seluruh folder project `restoran` ke dalam direktori:
- **XAMPP**: `C:\xampp\htdocs\restoran`
- **Laragon**: `C:\laragon\www\restoran`

### 2. Impor Database
1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Buat database baru dengan nama: `restoran_ecommerce`.
3. Klik tab **Import**, lalu pilih file `database.sql` yang ada di root folder project ini.
4. Klik **Go** dan tunggu hingga proses selesai.

### 3. Konfigurasi Database (Opsional)
Jika Anda menggunakan username/password database yang berbeda dari default root:
- Buka file `config/database.php`.
- Sesuaikan nilai `$user` dan `$pass`.

### 4. Menjalankan Aplikasi
1. Start modul **Apache** dan **MySQL** di Control Panel XAMPP/Laragon.
2. Buka browser dan akses: `http://localhost/restoran`.

---

## Akun Demo

### Admin
- **Username**: `admin`
- **Password**: `admin123`

### User (Silakan Daftar Sendiri)
- Gunakan menu **Daftar** di halaman utama.

---

## Fitur Utama
- **Role-Based Auth**: Admin & User.
- **Admin**: Dashboard, CRUD Produk/Kategori, Kelola Pesanan, Kelola User.
- **User**: Menu Produk, Detail, Keranjang, Checkout, Riwayat Pesanan.
- **Keamanan**: Prepared Statements (PDO), Password Hashing.
- **Design**: Modern Premium, Bootstrap 5, Responsif.
