# RESTful API Data Mahasiswa

API ini tidak memerlukan login. Sebelum menguji, jalankan Laragon/Apache dan MySQL, lalu impor `database/si_akademik.sql`. Skrip tersebut menyediakan data program studi awal; `prodi_id` yang dikirim harus merujuk ke program studi yang tersedia.

Antarmuka web dapat dibuka melalui `http://localhost/bkpm_webserver/acara16_si-akademik/public/`. Halaman awal akan menampilkan login; gunakan akun demo `admin` / `admin123`. Setelah login, dashboard menyediakan navigasi ke beranda, mahasiswa, program studi, dan mata kuliah.

Endpoint mengikuti pola REST berikut. Karena proyek ini berada di `C:\laragon\www\bkpm_webserver\acara16_si-akademik`, base URL Laragon default-nya menyertakan folder `bkpm_webserver`. Jika DocumentRoot atau virtual host Apache Anda menunjuk langsung ke folder `bkpm_webserver`, gunakan URL tanpa bagian `/bkpm_webserver`.

| Method | Endpoint | Hasil sukses |
| --- | --- | --- |
| GET | `/api/mahasiswa` | `200 OK`, daftar mahasiswa |
| GET | `/api/mahasiswa/{id}` | `200 OK`, satu mahasiswa |
| POST | `/api/mahasiswa` | `201 Created`, mahasiswa baru |
| PUT | `/api/mahasiswa/{id}` | `200 OK`, mahasiswa yang diperbarui |
| DELETE | `/api/mahasiswa/{id}` | `200 OK`, data mahasiswa yang dihapus |

Contoh base URL lokal: `http://localhost/bkpm_webserver/acara16_si-akademik/api/mahasiswa`. Rewrite URL REST memerlukan `mod_rewrite` Apache dan izin `.htaccess`. Endpoint lama `api/mahasiswa.php` dan `api/mahasiswa.php?id=1` tetap didukung. Untuk memeriksa apakah PHP dan database dapat diakses sebelum mencoba rewrite REST, buka `http://localhost/bkpm_webserver/acara16_si-akademik/api/mahasiswa.php`; URL ini seharusnya menghasilkan response JSON.

Jika mengakses lewat folder `public`, URL lokalnya adalah `http://localhost/bkpm_webserver/acara16_si-akademik/public/api/mahasiswa`. Folder tersebut meneruskan request ke API yang sama; jika DocumentRoot Apache diarahkan langsung ke folder `public`, gunakan `/api/mahasiswa`.

## Request POST dan PUT

Pilih **Body → raw → JSON** di Postman dan kirim header `Content-Type: application/json`. POST dan PUT memakai semua field berikut. `prodi_id` dan `angkatan` diperlukan oleh skema database proyek ini selain NIM, nama, dan email.

```json
{
  "nim": "23005",
  "nama": "Rina",
  "email": "rina@gmail.com",
  "prodi_id": 1,
  "angkatan": 2026
}
```

Contoh response sukses POST (`201 Created`):

```json
{
  "success": true,
  "message": "Data mahasiswa berhasil ditambahkan.",
  "data": {
    "id": 4,
    "nim": "23005",
    "nama": "Rina",
    "email": "rina@gmail.com",
    "prodi_id": 1,
    "angkatan": 2026
  }
}
```

## Response error dan pengujian

Semua response menggunakan JSON dengan struktur `success`, `message`, dan `data`. Validasi field juga menyertakan object `errors`.

| Kondisi yang diuji | Status |
| --- | --- |
| ID tidak ditemukan | `404 Not Found` |
| NIM kosong, body tidak lengkap, atau email tidak valid | `422 Unprocessable Content` |
| JSON rusak atau body bukan object JSON | `400 Bad Request` |
| Content-Type bukan `application/json` | `415 Unsupported Media Type` |
| Method tidak didukung | `405 Method Not Allowed` |
| Gangguan database | `500 Internal Server Error` |

Contoh response validasi error:

```json
{
  "success": false,
  "message": "Data mahasiswa tidak valid.",
  "data": null,
  "errors": {
    "email": "Email tidak valid atau melebihi 100 karakter."
  }
}
```

Koleksi Postman tersedia pada [`postman/Acara16 RESTful API Mahasiswa.postman_collection.json`](./postman/Acara16%20RESTful%20API%20Mahasiswa.postman_collection.json). Import koleksi ke Postman, ubah variable `base_url` jika folder proyek berbeda, lalu jalankan GET, POST, GET by ID, PUT, dan DELETE secara berurutan. Ganti nilai contoh NIM agar belum pernah digunakan. Untuk menguji kondisi error, kirim ID yang tidak tersedia, hilangkan field `nim`, atau gunakan email yang tidak valid. Simpan screenshot hasil pengujian dari Postman sebagai bukti praktikum.
