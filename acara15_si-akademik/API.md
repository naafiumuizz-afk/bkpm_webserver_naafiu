# API Data Mahasiswa

Endpoint API tersedia di `api/mahasiswa.php` dan tidak memerlukan login. URL utama sesuai instruksi praktikum adalah `http://localhost/bkpm_webserver/acara15_si-akademik/api/mahasiswa`; URL dengan akhiran `.php` juga tetap dapat digunakan.

## GET semua mahasiswa

```http
GET /bkpm_webserver/acara15_si-akademik/api/mahasiswa
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
  "nim": "23005",
  "nama": "Dewi",
  "email": "dewi@gmail.com"
}
```

`prodi_id` dan `angkatan` boleh disertakan untuk menentukan nilai secara eksplisit. Jika tidak disertakan, API menggunakan program studi pertama yang tersedia dan tahun berjalan agar sesuai dengan kolom wajib pada skema database aplikasi. Pastikan database sudah diimpor dan memiliki minimal satu program studi. POST yang berhasil menghasilkan status `201 Created` dan data mahasiswa yang dibuat.

## Uji dengan cURL

```sh
curl http://localhost/bkpm_webserver/acara15_si-akademik/api/mahasiswa
curl "http://localhost/bkpm_webserver/acara15_si-akademik/api/mahasiswa?id=1"
curl -X POST http://localhost/bkpm_webserver/acara15_si-akademik/api/mahasiswa ^
  -H "Content-Type: application/json" ^
  -d "{\"nim\":\"23005\",\"nama\":\"Dewi\",\"email\":\"dewi@gmail.com\"}"
```
