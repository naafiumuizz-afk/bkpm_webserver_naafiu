# Laporan Pengujian Manual dan Blackbox — Acara 23

## Informasi pengujian

- **Pokok bahasan:** Manual testing dan Blackbox testing
- **Praktikum:** Minggu 12 / Pertemuan 1
- **Tempat:** Politeknik Negeri Jember
- **Tanggal pelaksanaan:** 20 November 2026
- **Metode:** Blackbox testing dengan equivalence partitioning
- **Lingkungan:** Apache, PHP 8.1.10, MySQL Laragon, dan API lokal
- **Base URL:** `http://localhost/bkpm_webserver/acara23_si-akademik`

## Ruang Lingkup Pengujian

Endpoint berikut dipilih berdasarkan peta route pada `routes/web.php`:

| Method | Endpoint | Controller / aksi | Akses |
| --- | --- | --- | --- |
| POST | `/register` | `AuthController::register()` | Publik |
| POST | `/api/login` | `AuthController::apiLogin()` | Publik |
| GET | `/api/mahasiswa` | `MahasiswaApiController::index()` | Publik |
| GET | `/api/mahasiswa/{id}` | `MahasiswaApiController::show()` | Publik |
| POST | `/api/mahasiswa` | `MahasiswaApiController::store()` | Bearer token |
| PUT | `/api/mahasiswa/{id}` | `MahasiswaApiController::update()` | Bearer token |
| DELETE | `/api/mahasiswa/{id}` | `MahasiswaApiController::destroy()` | Bearer token |

`POST /login` pada proyek ini digunakan untuk form login web berbasis session, sedangkan pengujian login API menggunakan `POST /api/login`.

Pengujian dilakukan terhadap endpoint autentikasi dan CRUD mahasiswa. Request dijalankan pada server lokal melalui HTTP; skenario dan data uji di bawah dapat diulang melalui Postman. Semua operasi tulis mahasiswa memerlukan `Authorization: Bearer <token>` dan body POST/PUT menggunakan `Content-Type: application/json`.

> **Pemetaan route:** Pada proyek ini API login adalah `POST /api/login`. `POST /login` adalah form login web berbasis session. Registrasi tersedia pada `POST /register`. Operasi update dan delete menggunakan `PUT /api/mahasiswa/{id}` dan `DELETE /api/mahasiswa/{id}`.

## Persiapan Postman

1. Pastikan Apache dan MySQL Laragon aktif dan database `si_akademik` tersedia.
2. Atur `base_url` ke URL dasar di atas.
3. Daftarkan akun uji melalui `POST {{base_url}}/register`:

   ```json
   {
     "username": "akun_uji_unik",
     "email": "akun_uji_unik@example.test",
     "password": "KataSandiUji!23"
   }
   ```

4. Login melalui `POST {{base_url}}/api/login` dengan username dan password akun uji. Salin `data.token` dari response.
5. Untuk operasi tulis yang memerlukan autentikasi, tambahkan:

   ```http
   Authorization: Bearer <token>
   Content-Type: application/json
   ```

6. Untuk pengujian NIM duplikat, gunakan NIM yang telah terdaftar, misalnya `2402001`. Untuk ID yang tidak ditemukan, gunakan ID positif yang belum ada di database, misalnya `2147483647`.

## Test Case dengan Hasil Pengujian

Tabel berikut memuat test case untuk input valid dan tidak valid beserta hasil aktual dari pengujian HTTP pada server lokal. Contoh NIM `220100123` harus diganti dengan NIM unik jika sudah digunakan.

| ID Test Case | Skenario / partisi ekuivalen | Data uji | Hasil yang diharapkan | Hasil aktual | Status |
| --- | --- | --- | --- | --- | --- |
| TC01 | Store (`MahasiswaApiController::store`) — data valid | NIM unik (contoh `220100123`), nama/email valid, `prodi_id=1`, `angkatan=2025` | `201 Created` | `201 Created` | Pass |
| TC02 | Store — NIM kosong (partisi tidak valid) | `nim=""`; field lain valid | `400` atau `422` | `422 Unprocessable Content` | Pass |
| TC03 | Store — NIM duplikat (partisi tidak valid) | NIM yang sudah dimiliki data lain, contoh `2402001` | `422 Unprocessable Content` | `422 Unprocessable Content` | Pass |
| TC04 | Login API (`AuthController::apiLogin`) — kredensial benar | Username dan password akun uji yang terdaftar | `200 OK`, token diterima | `200 OK`, token diterima | Pass |
| TC05 | Login API — password salah | Username terdaftar dan password salah | `401 Unauthorized` | `401 Unauthorized` | Pass |
| TC06 | Store — tanpa autentikasi (`Auth::requireAuth`) | Body valid, tanpa header `Authorization` | `401 Unauthorized` | `401 Unauthorized` | Pass |
| TCU01 | Update — seluruh data valid | Body lengkap, NIM milik record yang sama | `200 OK` | `200 OK` | Pass |
| TCU02 | Update — field wajib tidak dikirim | Body tidak memuat `angkatan` | `422 Unprocessable Content` | `422 Unprocessable Content` | Pass |
| TCU03 | Update — NIM tidak valid | `nim=NIM-UJI` | `422 Unprocessable Content` | `422 Unprocessable Content` | Pass |
| TCU04 | Update — NIM milik mahasiswa lain | `nim=2402001` | `422 Unprocessable Content` | `422 Unprocessable Content` | Pass |
| TCU05 | Update — ID tidak ditemukan | ID `2147483647`, body valid | `404 Not Found` | `404 Not Found` | Pass |
| TCU06 | Update — tanpa autentikasi | Header `Authorization` tidak dikirim | `401 Unauthorized` | `401 Unauthorized` | Pass |
| TCD01 | Destroy — ID tidak ditemukan | ID `2147483647` | `404 Not Found` | `404 Not Found` | Pass |
| TCD02 | Destroy — record valid | ID record khusus yang dibuat untuk pengujian | `200 OK` | `200 OK` | Pass |
| TCD03 | Destroy — ID yang telah dihapus | Ulangi DELETE untuk ID TCD02 | `404 Not Found` | `404 Not Found` | Pass |
| TCD04 | Destroy — tanpa autentikasi | Header `Authorization` tidak dikirim | `401 Unauthorized` | `401 Unauthorized` | Pass |

**Penjelasan equivalence partitioning:** Data masukan dibagi menjadi kelas yang diperkirakan menghasilkan perilaku sama. Pada NIM, kelas valid diwakili NIM unik, sedangkan kelas tidak valid diwakili NIM kosong atau duplikat. Pada login, kelas valid menghasilkan token dan password salah menghasilkan penolakan. Akses store tanpa token merupakan kelas request tidak terautentikasi. Cukup menguji satu atau beberapa perwakilan dari tiap kelas, bukan seluruh kemungkinan nilai.

Akun dan data mahasiswa uji yang dibuat untuk pengujian sukses dibersihkan setelahnya.

## Langkah Pengujian Manual

1. Buka Postman, pilih environment dengan `base_url` yang sesuai, lalu pastikan Apache dan MySQL aktif. Gunakan akun uji untuk login ke `POST {{base_url}}/api/login`; simpan token dari `data.token` untuk request yang memerlukan autentikasi.
2. Jalankan request satu per satu sesuai method, endpoint, dan data uji pada tabel. Untuk request POST/PUT, pilih body **raw → JSON** dan kirim `Content-Type: application/json`. Tambahkan header `Authorization: Bearer <token>` kecuali pada skenario tanpa token (TC06, TCU06, dan TCD04) atau skenario login.
3. Jalankan TC01 dengan NIM baru yang unik. Simpan `data.id` dari response sebagai ID record uji untuk TCU01–TCU04, TCU06, dan TCD02–TCD04. Jalankan skenario update terlebih dahulu; jalankan TCD02 untuk menghapus record uji setelah skenario lain selesai, kemudian ulangi DELETE tersebut untuk TCD03. Untuk TCU05 dan TCD01, gunakan ID positif yang tidak ada di database.
4. Setelah setiap request, catat status HTTP yang tampil di Postman pada kolom **Hasil aktual**. Bandingkan dengan **Hasil yang diharapkan**; isi **Pass** jika sesuai dan **Fail** jika berbeda. Catat response body atau pesan error yang relevan untuk membantu analisis.

Saat pengujian selesai, hapus hanya record mahasiswa dan akun sementara yang dibuat khusus untuk pengujian. Jangan gunakan ID data yang sudah ada untuk skenario DELETE berhasil.

### Contoh body untuk update

Gunakan body lengkap berikut untuk TCU01 atau TCU05. Ganti NIM dan email dengan data valid yang unik untuk record uji, kecuali saat melakukan skenario duplikasi TCU04.

```json
{
  "nim": "269900001",
  "nama": "Mahasiswa Uji",
  "email": "mahasiswa.uji@example.test",
  "prodi_id": 1,
  "angkatan": 2025
}
```

Untuk TCU02, hapus field `angkatan`. Untuk TCU03, ubah NIM menjadi `NIM-UJI`. Untuk TCU04, gunakan NIM milik data lain yang sudah ada.

## Analisis dan kesimpulan

Pengujian mencakup partisi input valid dan tidak valid untuk tambah/ubah, data duplikat, ID yang tersedia dan tidak tersedia, serta akses dengan dan tanpa token. Seluruh 16 test case menghasilkan status HTTP yang sesuai dengan hasil yang diharapkan. Tidak ditemukan status **Fail**, sehingga tidak ada perubahan pada Model atau Controller dan tidak diperlukan retest setelah perbaikan.

## Pembersihan data uji

Hapus record yang dibuat saat POST berhasil dan akun registrasi sementara setelah pengujian. Jangan menghapus data mahasiswa atau pengguna yang sudah ada sebelum pengujian. Token uji tidak dicantumkan dalam laporan.
