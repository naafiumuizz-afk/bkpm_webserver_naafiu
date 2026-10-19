# API Data Mahasiswa

Endpoint API tersedia sebagai file mandiri di `api/mahasiswa.php` dan tidak memerlukan login. Dengan proyek berada di `C:\laragon\www\bkpm_webserver\acara15_si-akademik`, URL-nya `http://localhost/bkpm_webserver/acara15_si-akademik/api/mahasiswa.php`.

## GET semua mahasiswa

```http
GET /bkpm_webserver/acara15_si-akademik/api/mahasiswa.php
```

Contoh response:

```json
{
  "success": true,
  "message": "Data mahasiswa berhasil diambil.",
  "data": [
    {
      "id": 1,
      "nim": "2401001",
      "nama": "Budi Santoso",
      "email": "budi@email.com"
    }
  ]
}
```

## GET mahasiswa berdasarkan ID

```http
GET /bkpm_webserver/acara15_si-akademik/api/mahasiswa.php?id=1
```

Response menggunakan format yang sama, dengan satu object pada `data`. ID yang tidak ditemukan menghasilkan status `404`.

## POST mahasiswa

Kirim request ke `http://localhost/bkpm_webserver/acara15_si-akademik/api/mahasiswa.php` dengan header `Content-Type: application/json` dan body:

```json
{
  "nim": "23004",
  "nama": "Dewi",
  "email": "dewi@gmail.com",
  "prodi_id": 1,
  "angkatan": 2026
}
```

Tabel mahasiswa pada proyek ini juga mewajibkan `prodi_id` dan `angkatan`; karena itu kedua field tersebut harus disertakan selain tiga field pada contoh tugas. `prodi_id` harus merujuk pada program studi yang sudah ada di database. POST yang berhasil menghasilkan status `201 Created` dan data mahasiswa yang dibuat.

## Uji dengan cURL

```sh
curl http://localhost/bkpm_webserver/acara15_si-akademik/api/mahasiswa.php
curl "http://localhost/bkpm_webserver/acara15_si-akademik/api/mahasiswa.php?id=1"
curl -X POST http://localhost/bkpm_webserver/acara15_si-akademik/api/mahasiswa.php ^
  -H "Content-Type: application/json" ^
  -d "{\"nim\":\"23004\",\"nama\":\"Dewi\",\"email\":\"dewi@gmail.com\",\"prodi_id\":1,\"angkatan\":2026}"
```
