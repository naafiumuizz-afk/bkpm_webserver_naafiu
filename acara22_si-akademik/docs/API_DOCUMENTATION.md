# Dokumentasi API SI Akademik

## Ringkasan endpoint

Tabel berikut memetakan endpoint API, method controller yang menangani request, dan kebutuhan autentikasinya. `{id}` adalah ID berupa bilangan positif. Ganti `{{base_url}}` dengan URL aplikasi sesuai konfigurasi Apache/Laragon; endpoint pada tabel ditambahkan setelah URL dasar tersebut.

| Method | Endpoint | Controller dan method | Akses |
| --- | --- | --- | --- |
| POST | `/api/login` | `AuthController::apiLogin()` | Publik |
| POST | `/register` | `AuthController::register()` | Publik |
| GET | `/api/mahasiswa` | `MahasiswaApiController::index()` | Publik |
| GET | `/api/mahasiswa/{id}` | `MahasiswaApiController::show()` | Publik |
| POST | `/api/mahasiswa` | `MahasiswaApiController::store()` | Bearer token |
| PUT | `/api/mahasiswa/{id}` | `MahasiswaApiController::update()` | Bearer token |
| DELETE | `/api/mahasiswa/{id}` | `MahasiswaApiController::destroy()` | Bearer token |
| GET | `/api/buku` | `BukuApiController::index()` | Publik |
| GET | `/api/buku/{id}` | `BukuApiController::show()` | Publik |
| POST | `/api/buku` | `BukuApiController::store()` | Publik |
| PUT | `/api/buku/{id}` | `BukuApiController::update()` | Publik |
| DELETE | `/api/buku/{id}` | `BukuApiController::destroy()` | Publik |

File API berikut juga tersedia sebagai jalur langsung: `POST /api/login.php` memanggil `AuthController::apiLogin()`, `POST /api/register.php` memanggil `AuthController::register()`. `api/mahasiswa.php` menyediakan operasi mahasiswa langsung; untuk jalur ini, ID dapat dikirim melalui query `?id={id}`. Operasi tulisnya juga memerlukan Bearer token.

## Format request dan response

Request yang mengirim body JSON (login, registrasi, POST, dan PUT) harus menyertakan:

```http
Content-Type: application/json
```

Operasi POST, PUT, dan DELETE mahasiswa memerlukan token hasil login:

```http
Authorization: Bearer <token>
```

Response API menggunakan format JSON dengan properti `success`, `message`, `data`, `errors`, dan `timestamp`. Kesalahan umum mencakup `400 Bad Request` untuk JSON tidak valid, `401 Unauthorized` untuk kredensial atau token yang tidak valid, `404 Not Found` untuk data yang tidak ditemukan, `415 Unsupported Media Type` untuk Content-Type yang tidak sesuai, `422 Unprocessable Content` untuk input tidak valid, dan `500 Internal Server Error` untuk gangguan server.

## Autentikasi

### POST `/api/login`

Controller: `AuthController::apiLogin()`. Endpoint publik untuk memperoleh JWT.

Request body:

```json
{
  "username": "mahasiswa",
  "password": "kata-sandi"
}
```

Response berhasil `200 OK` berisi token pada `data.token`; masa berlaku token adalah 300 detik. Kredensial salah menghasilkan `401 Unauthorized`.

### POST `/register`

Controller: `AuthController::register()`. Endpoint publik untuk mendaftarkan pengguna.

Request body:

```json
{
  "username": "mahasiswa",
  "email": "mahasiswa@example.com",
  "password": "kata-sandi"
}
```

Registrasi berhasil menghasilkan `201 Created`. Input yang tidak valid menghasilkan `422 Unprocessable Content`, sedangkan username atau email yang sudah terdaftar menghasilkan `409 Conflict`.

## Mahasiswa

Controller: `MahasiswaApiController`.

| Method | Endpoint | Keterangan |
| --- | --- | --- |
| GET | `/api/mahasiswa` | Mengambil seluruh data mahasiswa, tanpa autentikasi |
| GET | `/api/mahasiswa/{id}` | Mengambil satu mahasiswa, tanpa autentikasi |
| POST | `/api/mahasiswa` | Menambahkan mahasiswa, memerlukan Bearer token |
| PUT | `/api/mahasiswa/{id}` | Memperbarui mahasiswa, memerlukan Bearer token |
| DELETE | `/api/mahasiswa/{id}` | Menghapus mahasiswa, memerlukan Bearer token |

Body untuk POST dan PUT:

```json
{
  "nim": "2501001",
  "nama": "Rina Putri",
  "email": "rina@example.com",
  "prodi_id": 1,
  "angkatan": 2025
}
```

Field `nim`, `nama`, `email`, `prodi_id`, dan `angkatan` wajib dikirim. POST berhasil menghasilkan `201 Created`; PUT dan DELETE berhasil menghasilkan `200 OK`.

## Buku

Controller: `BukuApiController`. Seluruh operasi buku saat ini tidak memerlukan autentikasi.

| Method | Endpoint | Keterangan |
| --- | --- | --- |
| GET | `/api/buku` | Mengambil seluruh data buku |
| GET | `/api/buku/{id}` | Mengambil satu buku |
| POST | `/api/buku` | Menambahkan buku |
| PUT | `/api/buku/{id}` | Memperbarui buku |
| DELETE | `/api/buku/{id}` | Menghapus buku |

Body untuk POST dan PUT:

```json
{
  "kode": "BK-001",
  "judul": "Pemrograman Web",
  "penulis": "Andi Saputra",
  "penerbit": "Penerbit Kampus",
  "tahun_terbit": 2025
}
```

## Referensi

Peta route bersumber dari `routes/web.php`; detail request, response, dan pengujian tambahan tersedia di [API.md](../API.md). Koleksi Postman Acara 19/20 berada di `postman/Acara19 API Sistem Informasi Akademik.postman_collection.json`.
