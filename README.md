# 🏫 Absensi Multi-SaaS Platform

Aplikasi Absensi Digital berbasis GPS dengan arsitektur Multi-Tenant (Satu sistem untuk banyak sekolah). Dibangun menggunakan **Laravel 12**, **Tailwind CSS v4**, **Alpine.js**, dan **Laravel Sanctum** untuk sistem API.

---

## ✨ Fitur Utama

### 1. 🏢 Multi-Tenant Architecture
- Satu codebase yang melayani banyak sekolah/institusi sekaligus.
- Setiap institusi (tenant) memiliki pengaturan tersendiri seperti **batas jam keterlambatan**, **jam pulang**, dan **titik lokasi GPS**.
- Keamanan data terjamin melalui `EnsureTenantAccess` middleware, sehingga Admin Sekolah hanya dapat melihat dan mengelola data milik sekolahnya sendiri.

### 2. 📍 Validasi Kehadiran GPS (Haversine Formula)
- Proses check-in dan check-out absensi memvalidasi radius jarak pengguna dari lokasi sekolah.
- Menggunakan perhitungan akurat **Haversine Formula**.
- Radius GPS dapat diatur secara fleksibel melalui panel admin (contoh: maksimal 80 meter dari titik koordinat).

### 3. 🛡️ Sistem Autentikasi Ganda
- **Web Admin Panel**: Pengelolaan data menggunakan autentikasi sesi berbasis role (Super Admin & Admin Sekolah).
- **REST API**: Pengguna (Siswa dan Guru) melakukan absensi menggunakan aplikasi client terpisah melalui API yang diamankan menggunakan **Laravel Sanctum**.
  - **Siswa**: Login menggunakan NISN
  - **Guru**: Login menggunakan NIP

### 4. 💻 Premium Admin Panel UI/UX
- **Desain Modern UI**: Dibangun dengan sentuhan premium, glassmorphism, dan color palette estetik dari **Tailwind CSS**.
- **UX Interaktif**: Sidebar dinamis dengan animasi _collapse/expand_ dan komponen reaktif menggunakan **Alpine.js**.
- **Konfirmasi Aman**: Menggunakan integrasi **SweetAlert2** untuk pencegahan aksi tidak sengaja (saat menghapus data).

### 5. 📊 Dashboard & Laporan
- **Real-time Analytics**: Menampilkan total sekolah, siswa, guru, dan ringkasan absensi harian pada dashboard.
- **Filter Data Absensi Kompleks**: Admin dapat menyaring data absensi berdasarkan tanggal, jenis (check-in/check-out), status (telat/tepat waktu), maupun nama.

---

## 🚀 Kebutuhan Sistem
* PHP >= 8.2
* Composer
* Node.js & NPM
* MySQL / MariaDB

---

## 🛠️ Langkah Instalasi

Ikuti panduan berikut untuk menjalankan proyek ini di mesin lokal/server Anda:

1. **Jalankan Terminal** pada direktori project
2. **Install dependensi PHP via Composer:**
   ```bash
   composer install
   ```
3. **Persiapkan Environment:**
   Duplikat file `.env.example` menjadi `.env`. Sesuaikan kredensial koneksi ke dalam database MySQL Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=absensi
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```
5. **Install Laravel Sanctum API Configuration:**
   Jika file `routes/api.php` belum terpublikasi secara utuh, jalankan utilitas instalasi API (ketika ditanya overwrite file route, jawab sesuai kebutuhan, secara default proyek ini sudah membawa konfigurasinya):
   ```bash
   php artisan install:api
   ```
6. **Install Dependensi Frontend (NPM):**
   ```bash
   npm install
   ```
7. **Jalankan Database Migrations + Seeder:**
   Proses ini akan membuat semua tabel database beserta akun dummy dan dummy konfigurasi Tenant.
   ```bash
   php artisan migrate:fresh --seed
   ```
8. **Tautkan Folder Storage (Symlink):**
   Agar logo maupun file upload dapat diakses via web.
   ```bash
   php artisan storage:link
   ```
9. **Jalankan Aplikasi:**
   Buka 2 tab terminal baru untuk menjalankan Vite (assets bundle) dan Dev Server PHP:
   ```bash
   # Terminal 1 - Asset Compiler
   npm run build  # Atau gunakan npm run dev saat tahap development
   
   # Terminal 2 - PHP Local Server
   php artisan serve
   ```

Akses Admin Panel melalui browser di: `http://localhost:8000` (Port menyesuaikan pengaturan serve Artisan Anda, atau akses public folder bila via XAMPP contoh: `http://localhost/absensi/public`).

---

## 🔑 Data Login Bawaan (Berdasarkan Seeder)

Aplikasi memiliki kredensial awalan berikut untuk kebutuhan testing:

### Admin Web Panel:
- **Tipe Akun:** Super Admin
  - **Email:** `superadmin@absensi.com`
  - **Password:** `password`
- **Tipe Akun:** Admin Sekolah (SMPN 6 Sudimoro)
  - **Email:** `admin@smpn6sudimoro.sch.id`
  - **Password:** `password`

### API Pengguna App (Untuk ditautkan dengan App Mobile):
Secara default, password untuk setiap pengguna dikonfigurasi sama dengan **NPSN Sekolah**.
- **Kredensial Siswa SMPN 6 (Contoh):**
  - **NISN:** `0012345001`
  - **Password:** `20512345` (Menggunakan NPSN: 20512345)
- **Kredensial Guru SMPN 6 (Contoh):**
  - **NIP:** `198501012010011001`
  - **Password:** `20512345`  

*(Anda dapat menemukan pengguna API lainnya pada file `StudentSeeder.php` dan `TeacherSeeder.php`)*

---
> ⚠️ **Catatan Penting:** Demi alasan keamanan, jika sebelumnya Anda pernah menggunakan script semacam `setup.php` atau `fix_setup.php` di direktori public, segera hapus file script tersebut di lingkungan Production.

**Built With Care - Ready for the Multi-SaaS Era.**
