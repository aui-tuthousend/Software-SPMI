# Software-SPMI

## Deskripsi Proyek
Proyek ini adalah aplikasi yang dibangun menggunakan framework Laravel dan Vue.js, dirancang untuk memudahkan pengelolaan data dan laporan.

## Persyaratan
- PHP versi 8.2 atau lebih tinggi (https://www.php.net/downloads)
- Composer untuk manajemen dependensi PHP (https://getcomposer.org/download/)
- Node.js dan npm untuk manajemen dependensi JavaScript (https://nodejs.org/en/download/)

## Langkah Awal
ikuti langkah-langkah berikut:

1. **Clone Repository**:
   Gunakan perintah berikut untuk meng-clone repository:
   ```bash
   git clone https://github.com/Burzess/Software-SPMI.git
   ```

2. **Masuk ke Direktori Proyek**:
   ```bash
   cd Software-SPMI
   ```

3. **Salin File `.env`**:
   Jika file `.env` belum ada, salin file contoh `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```

4. **Instal Dependensi PHP**:
   Jalankan perintah berikut untuk menginstal dependensi PHP:
   ```bash
   composer install
   ```

5. **Instal Dependensi JavaScript**:
   Setelah menginstal dependensi PHP, jalankan:
   ```bash
   npm install
   ```

6. **Generate Kunci Aplikasi**:
   Jalankan perintah berikut untuk menghasilkan kunci aplikasi:
   ```bash
   php artisan key:generate
   ```

7. **Konfigurasi Database**:
   Edit file `.env` untuk mengonfigurasi pengaturan database sesuai kebutuhan proyek Anda. Berikut adalah contoh pengaturan untuk menggunakan MySQL:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=nama_pengguna
   DB_PASSWORD=kata_sandi
   ```

   Gantilah `nama_database`, `nama_pengguna`, dan `kata_sandi` dengan informasi yang sesuai untuk database Anda.

8. **Migrasi Database**:
   Jalankan perintah berikut untuk melakukan migrasi database:
   ```bash
   php artisan migrate
   ```

9. **Menjalankan Seeder User**:
   Untuk mengisi database dengan data pengguna awal, jalankan perintah berikut:
   ```bash
   php artisan db:seed --class=UserSeeder
   ```

## Menjalankan Aplikasi

1. **Menjalankan Server Laravel**:
   ```bash
   php artisan serve
   ```

2. **Menjalankan Server Pengembangan Vite**:
   ```bash
   npm run dev
   ```

## Catatan
