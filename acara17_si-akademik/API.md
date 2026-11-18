# Dokumentasi API CRUD - Acara 17

API menyediakan operasi CRUD mahasiswa dan buku menggunakan JSON, HTTP, dan pola MVC. Sebelum pengujian, jalankan Apache dan MySQL Laragon, lalu impor `database/si_akademik.sql` ke MySQL. Skrip membuat tabel `buku` bersama tabel akademik lain. Jika database sudah ada, jalankan kembali skrip agar tabel baru dibuat.

## Susunan folder proyek

Struktur berikut mengikuti urutan lapisan pada prosedur kerja. Karena proyek ini sudah memiliki aplikasi web, beberapa lapisan memiliki folder tambahan (repository, service, middleware, dan views) tanpa mengubah pemisahan tanggung jawab MVC.

```text
acara17_si-akademik/
├── app/                         # kode aplikasi
│   ├── config/
│   │   └── database.php         # konfigurasi koneksi MySQL
│   ├── Core/
│   │   ├── autoload.php         # pemuat class App\ otomatis
│   │   ├── Database.php         # koneksi PDO
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
│   ├── index.php                # front controller
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
http://localhost/bkpm_webserver/acara17_si-akademik/public
```

Jika virtual host atau document root diarahkan langsung ke folder `public/`, gunakan URL host tersebut tanpa tambahan `/public`. Apache harus mengaktifkan `mod_rewrite` dan mengizinkan aturan `.htaccess`.

Halaman awal membuka form login. Setelah login, aplikasi menampilkan dashboard dan menu Mahasiswa, Program Studi, serta Mata Kuliah. Halaman alur API tetap tersedia di `/api-flow`.

```text
http://localhost/bkpm_webserver/acara17_si-akademik/public/
```

Login demo: `admin` / `admin123`. Pastikan database `si_akademik` sudah dibuat dengan mengimpor `database/si_akademik.sql`.

### URL pengujian API buku

Pengujian praktikum ini diarahkan ke resource buku. Dengan URL dasar di atas, gunakan URL berikut di Postman:

| Operasi | Method | URL pengujian |
| --- | --- | --- |
| Menampilkan semua buku | GET | `http://localhost/bkpm_webserver/acara17_si-akademik/public/api/buku` |
| Menampilkan buku berdasarkan ID | GET | `http://localhost/bkpm_webserver/acara17_si-akademik/public/api/buku/1` |
| Menambahkan buku | POST | `http://localhost/bkpm_webserver/acara17_si-akademik/public/api/buku` |
| Memperbarui buku berdasarkan ID | PUT | `http://localhost/bkpm_webserver/acara17_si-akademik/public/api/buku/1` |
| Menghapus buku berdasarkan ID | DELETE | `http://localhost/bkpm_webserver/acara17_si-akademik/public/api/buku/1` |

Ganti angka `1` dengan ID buku yang dikembalikan oleh API. POST dan PUT memakai header `Content-Type: application/json` dan contoh body pada bagian endpoint buku. Jika document root atau virtual host diarahkan ke `public/`, gunakan URL host tersebut dan hilangkan bagian `/bkpm_webserver/acara17_si-akademik/public`.

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

Field harus mengikuti skema tabel. `prodi_id` harus merujuk ke program studi yang ada; NIM harus angka dan unik. Keunikan diperiksa saat validasi dan dijamin kembali oleh constraint `UNIQUE` pada database.

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
  }
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
  }
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

Semua response berbentuk JSON dengan `success`, `message`, dan `data`. Untuk pengujian buku, jalankan alur POST → GET → GET berdasarkan ID → PUT → DELETE, lalu GET kembali untuk memastikan data sudah terhapus. Koleksi Postman Acara 17 tersedia pada [`postman/Acara17 API CRUD.postman_collection.json`](./postman/Acara17%20API%20CRUD.postman_collection.json) dan request di dalamnya diarahkan ke endpoint buku. Ubah variable `base_url` jika konfigurasi lokal berbeda. Simpan screenshot request dan response sebagai bukti praktikum.
