# Dokumentasi API CRUD - Tugas Praktik Mandiri

API menyediakan operasi CRUD mahasiswa dan buku menggunakan JSON, HTTP, dan pola MVC. Sebelum pengujian, jalankan Apache dan MySQL Laragon, lalu impor `database/si_akademik.sql` ke MySQL. Skrip membuat tabel `buku` bersama tabel akademik lain. Jika database sudah ada, jalankan kembali skrip agar tabel baru dibuat.

## Susunan folder proyek

Struktur berikut mengikuti urutan lapisan pada prosedur kerja. Karena proyek ini sudah memiliki aplikasi web, beberapa lapisan memiliki folder tambahan (repository, service, middleware, dan views) tanpa mengubah pemisahan tanggung jawab MVC.

```text
acara18_si-akademik/
├── app/                         # kode aplikasi
│   ├── config/
│   │   ├── app.php              # konfigurasi mode aplikasi
│   │   └── database.php         # konfigurasi koneksi MySQL
│   ├── Core/
│   │   ├── autoload.php         # pemuat class App\ otomatis
│   │   ├── Database.php         # koneksi PDO
│   │   ├── Response.php         # response JSON dan HTTP terstandar
│   │   └── Router.php           # pencocokan URL dan HTTP method
│   ├── Models/
│   │   ├── Mahasiswa.php
│   │   └── Buku.php
│   ├── Repositories/            # query database menggunakan PDO
│   ├── Services/                # validasi dan aturan bisnis mahasiswa
│   └── Controllers/
│       ├── MahasiswaApiController.php
│       └── BukuApiController.php
├── routes/
│   └── web.php                  # daftar endpoint API dan aplikasi web
├── public/                      # document root / folder akses browser
│   ├── front.php                # front controller
│   └── .htaccess                # rewrite request ke index.php
│   └── views/                   # view aplikasi web
├── database/
│   └── si_akademik.sql
└── API.md
```

Pada materi dasar, class model dapat langsung menjalankan query. Proyek ini sudah memisahkan query ke `Repositories/` agar selaras dengan arsitektur aplikasi yang ada. Folder `public/views/` berisi view aplikasi web; endpoint API membalas JSON dan tidak menggunakan view HTML.

## URL dasar

Jika Laragon menggunakan document root `C:\laragon\www`, gunakan:

```text
http://localhost/bkpm_webserver/acara19_si-akademik/public
```

Jika virtual host atau document root diarahkan langsung ke folder `public/`, gunakan URL host tersebut tanpa tambahan `/public`. Apache harus mengaktifkan `mod_rewrite` dan mengizinkan aturan `.htaccess`.

### Menjalankan aplikasi web

Jalankan Apache dan MySQL dari Laragon, lalu impor `database/si_akademik.sql` ke MySQL. Buka URL dasar aplikasi di browser untuk masuk ke halaman login. Akun demo yang tersedia adalah `admin` dengan kata sandi `admin123`; setelah login, dashboard menyediakan tautan ke daftar mahasiswa seperti tampilan aplikasi.

### URL pengujian API buku

Pengujian praktikum ini diarahkan ke resource buku. Dengan URL dasar di atas, gunakan URL berikut di Postman:

| Operasi | Method | URL pengujian |
| --- | --- | --- |
| Menampilkan semua buku | GET | `http://localhost/bkpm_webserver/acara18_si-akademik/public/api/buku` |
| Menampilkan buku berdasarkan ID | GET | `http://localhost/bkpm_webserver/acara18_si-akademik/public/api/buku/1` |
| Menambahkan buku | POST | `http://localhost/bkpm_webserver/acara18_si-akademik/public/api/buku` |
| Memperbarui buku berdasarkan ID | PUT | `http://localhost/bkpm_webserver/acara18_si-akademik/public/api/buku/1` |
| Menghapus buku berdasarkan ID | DELETE | `http://localhost/bkpm_webserver/acara18_si-akademik/public/api/buku/1` |

Ganti angka `1` dengan ID buku yang dikembalikan oleh API. POST dan PUT memakai header `Content-Type: application/json` dan contoh body pada bagian endpoint buku. Jika document root atau virtual host diarahkan ke `public/`, gunakan URL host tersebut dan hilangkan bagian `/bkpm_webserver/acara18_si-akademik/public`.

### Konfigurasi koneksi database

Konfigurasi aplikasi berada di `app/config/database.php`. Sesuaikan nilainya dengan server MySQL Laragon:

```php
return [
    'host' => '127.0.0.1',
    'dbname' => 'si_akademik',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];
```

Pastikan database `si_akademik` sudah dibuat dan skrip `database/si_akademik.sql` sudah dijalankan. Jika MySQL memakai host, username, atau password berbeda, sesuaikan nilai konfigurasi dengan pengaturan lokal.

### Format response JSON dan mode pengembangan

Endpoint API mengembalikan envelope yang konsisten berisi `success`, `message`, `data`, `errors`, dan `timestamp`. `errors` selalu berupa array kosong jika tidak ada kesalahan validasi; saat validasi gagal, properti tersebut memuat pesan berdasarkan nama field. Format JSON compact digunakan secara default. Untuk mengaktifkan `JSON_PRETTY_PRINT` saat pengembangan, atur environment variable `APP_ENV=development` sebelum menjalankan PHP. Nilai lain atau variabel yang tidak disetel menggunakan mode production.

## Alur MVC

```text
Client (Postman)
       │ HTTP + JSON
       ▼
public/index.php (Front Controller)
       ▼
app/Core/Router.php (Router)
       ▼
app/Controllers/*ApiController.php (Controller)
       ▼
app/Services/ → app/Repositories/ → app/Models/
       ▼
MySQL (si_akademik)
       │ hasil query
       └──────────────► JSON response kembali ke Client
```

Controller menangani request dan response, service mahasiswa menjalankan validasi bisnis, repository menjalankan query PDO prepared statement, dan model memvalidasi/mewakili data.

## Endpoint mahasiswa

| Method | Endpoint | Hasil sukses |
| --- | --- | --- |
| GET | `/api/mahasiswa` | `200 OK`, daftar mahasiswa |
| GET | `/api/mahasiswa/{id}` | `200 OK`, satu mahasiswa |
| POST | `/api/mahasiswa` | `201 Created`, mahasiswa baru |
| PUT | `/api/mahasiswa/{id}` | `200 OK`, mahasiswa yang diperbarui |
| DELETE | `/api/mahasiswa/{id}` | `200 OK`, data mahasiswa dihapus |

POST dan PUT memerlukan header `Content-Type: application/json` dan body:

```json
{
  "nim": "2501001",
  "nama": "Rina Putri",
  "email": "rina@example.com",
  "prodi_id": 1,
  "angkatan": 2025
}
```

Field harus mengikuti skema tabel. `prodi_id` harus merujuk ke program studi yang ada; NIM harus angka dan unik. Keunikan diperiksa saat validasi Model/Service dan dijamin kembali oleh constraint `UNIQUE` pada database. Jika NIM sudah digunakan, API menolak POST atau PUT dengan status `422 Unprocessable Content`.

Contoh response saat NIM duplikat:

```json
{
  "success": false,
  "message": "Data gagal disimpan. NIM tersebut sudah terdaftar.",
  "data": null,
  "errors": {
    "nim": "NIM tersebut sudah terdaftar."
  },
  "timestamp": "2026-10-26T13:38:52+07:00"
}
```

Contoh response sukses POST (`201 Created`):

```json
{
  "success": true,
  "message": "Data mahasiswa berhasil ditambahkan.",
  "data": {
    "id": 4,
    "nim": "2501001",
    "nama": "Rina Putri",
    "email": "rina@example.com"
  },
  "errors": [],
  "timestamp": "2026-10-26T13:38:52+07:00"
}
```

## Endpoint buku

Resource Buku menggunakan kolom `kode`, `judul`, `penulis`, `penerbit`, dan `tahun_terbit`. `kode` harus unik.

| Method | Endpoint | Hasil sukses |
| --- | --- | --- |
| GET | `/api/buku` | `200 OK`, daftar buku |
| GET | `/api/buku/{id}` | `200 OK`, satu buku |
| POST | `/api/buku` | `201 Created`, buku baru |
| PUT | `/api/buku/{id}` | `200 OK`, buku yang diperbarui |
| DELETE | `/api/buku/{id}` | `200 OK`, data buku dihapus |

POST dan PUT memerlukan `Content-Type: application/json` dan body:

```json
{
  "kode": "BK-001",
  "judul": "Pemrograman Web",
  "penulis": "Andi Saputra",
  "penerbit": "Penerbit Kampus",
  "tahun_terbit": 2025
}
```

Contoh response sukses POST:

```json
{
  "success": true,
  "message": "Data buku berhasil ditambahkan.",
  "data": {
    "id": 1,
    "kode": "BK-001",
    "judul": "Pemrograman Web",
    "penulis": "Andi Saputra",
    "penerbit": "Penerbit Kampus",
    "tahun_terbit": 2025
  },
  "errors": [],
  "timestamp": "2026-10-26T13:38:52+07:00"
}
```

### Membuat tabel dan memasukkan data buku

Tabel `buku` sudah disertakan di `database/si_akademik.sql`. Jika ingin membuatnya secara terpisah, pilih database `si_akademik` di phpMyAdmin lalu jalankan:

```sql
CREATE TABLE IF NOT EXISTS buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) NOT NULL UNIQUE,
    judul VARCHAR(150) NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    penerbit VARCHAR(100) NOT NULL,
    tahun_terbit SMALLINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

Skrip utama `database/si_akademik.sql` juga memasukkan tiga data contoh buku secara otomatis menggunakan `INSERT IGNORE`. Untuk memasukkan data secara manual melalui phpMyAdmin, gunakan query berikut:

```sql
INSERT INTO buku (kode, judul, penulis, penerbit, tahun_terbit)
VALUES
    ('BK-001', 'Pemrograman Web', 'Andi Saputra', 'Penerbit Kampus', 2025),
    ('BK-002', 'Basis Data MySQL', 'Siti Rahma', 'Media Informatika', 2024),
    ('BK-003', 'Rekayasa Perangkat Lunak', 'Budi Santoso', 'Teknologi Press', 2023);
```

Kolom `kode` memiliki constraint `UNIQUE`, jadi pastikan kode-kode tersebut belum digunakan. Verifikasi hasilnya dengan:

```sql
SELECT id, kode, judul, penulis, penerbit, tahun_terbit
FROM buku
ORDER BY id DESC;
```

## Response dan pengujian buku

| Kondisi | Status HTTP |
| --- | --- |
| Request berhasil | `200 OK` atau `201 Created` |
| Endpoint atau ID tidak ditemukan | `404 Not Found` |
| JSON rusak / body bukan object JSON | `400 Bad Request` |
| Field tidak valid atau kode buku duplikat | `422 Unprocessable Content` |
| Content-Type bukan JSON | `415 Unsupported Media Type` |
| Method tidak didukung pada endpoint yang tersedia | `405 Method Not Allowed` |
| Endpoint tidak dikenal | `404 Not Found` |
| Gangguan database | `500 Internal Server Error` |

## Pemetaan Tugas Praktik Mandiri

1. **Resource Buku:** `app/Models/Buku.php` merepresentasikan data, `app/Repositories/BukuRepository.php` mengelola query PDO, dan `app/Controllers/BukuApiController.php` menangani request serta response. Endpoint buku didaftarkan di `routes/web.php` dan dirakit oleh `app/Core/Router.php`.
2. **Validasi NIM unik:** `app/Models/Mahasiswa.php` dan `app/Services/MahasiswaService.php` memeriksa duplikasi saat tambah maupun ubah. `app/Repositories/MahasiswaRepository.php` melakukan pemeriksaan ke database, sedangkan constraint `UNIQUE` pada `database/si_akademik.sql` menjadi perlindungan tambahan.
3. **Dokumentasi dan bukti pengujian:** bagian endpoint di dokumen ini mencantumkan method, URL, contoh request, response sukses, response kesalahan duplikasi, dan diagram alur Router → Controller → Service/Repository → Model/Database. Jalankan alur POST → GET → GET berdasarkan ID → PUT → DELETE untuk buku, lalu GET kembali untuk memastikan data sudah terhapus. Simpan screenshot request dan response sebagai bukti praktikum.

Semua response berbentuk JSON dengan `success`, `message`, `data`, `errors`, dan `timestamp`. Koleksi Postman tersedia pada [`postman/Acara17 API CRUD.postman_collection.json`](./postman/Acara17%20API%20CRUD.postman_collection.json) dan request di dalamnya diarahkan ke endpoint buku. Ubah variable `base_url` jika konfigurasi lokal berbeda.

## Acara 19 — Registrasi dan environment Postman

Skrip `database/si_akademik.sql` membuat tabel `users` dengan username dan email unik. Jalankan atau impor ulang skrip tersebut sebelum mencoba registrasi. Endpoint `POST /register` menerima JSON berikut:

```json
{
  "username": "mahasiswa",
  "email": "mahasiswa@example.com",
  "password": "kata-sandi"
}
```

Kirim header `Content-Type: application/json`. Registrasi berhasil mengembalikan `201 Created` dan ID, username, serta email; kata sandi tidak disertakan dalam response dan disimpan sebagai hash. Input kosong atau email tidak valid mengembalikan `422 Unprocessable Content`; username/email yang sudah terdaftar mengembalikan `409 Conflict`.

Model `app/Models/User.php` memvalidasi format email dengan `filter_var(..., FILTER_VALIDATE_EMAIL)` sebelum query insert. Controller didaftarkan di `routes/web.php`, lalu dirakit oleh `app/Core/Router.php`. Request ini hanya mendaftarkan akun; proses login berbasis tabel users tidak termasuk langkah Acara 19.

Koleksi Postman Acara 19 tersedia di [`postman/Acara19 API Sistem Informasi Akademik.postman_collection.json`](./postman/Acara19%20API%20Sistem%20Informasi%20Akademik.postman_collection.json). Impor koleksi bersama environment [`Local`](./postman/Local.postman_environment.json) atau [`Staging`](./postman/Staging.postman_environment.json), lalu pilih environment yang sesuai. Nilai staging sengaja berupa contoh `https://staging.example.com`; ganti dengan URL staging milik Anda. Environment Local memakai `http://localhost/bkpm_webserver/acara19_si-akademik`. Request mahasiswa koleksi memakai `{{base_url}}/api/mahasiswa` dan registrasi memakai `{{base_url}}/api/register.php`; file PHP langsung ini meneruskan request ke `AuthController`, sehingga dapat diuji tanpa route Front Controller `public/`.
