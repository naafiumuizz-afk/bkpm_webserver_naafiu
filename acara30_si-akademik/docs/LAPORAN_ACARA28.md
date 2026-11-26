# Laporan Deployment dan Pengujian Produksi — Acara 28

## Informasi

- **Pokok bahasan:** Deployment
- **Praktikum:** Minggu 14 / Pertemuan 2
- **Aplikasi:** Sistem Informasi Akademik
- **Hosting:** Rumahweb
- **Tanggal pemeriksaan:** 22 November 2026

## Tujuan

Mencatat penyesuaian alamat aplikasi untuk environment Postman Production, kendala yang ditemukan, dan hasil pemeriksaan endpoint setelah aplikasi dipasang di hosting.

## Hasil pemeriksaan

| Pemeriksaan | Hasil | Keterangan |
| --- | --- | --- |
| Test case Acara 23 | Pass pada pengujian lokal | Dokumen [TEST_CASE.md](./TEST_CASE.md) mencatat 16 test case lokal berstatus Pass. Hasil ini bukan pengujian ulang seluruh test case pada server produksi. |
| URL `https://alwiyaa.my.id/Naafiu/Public` | Tidak sesuai | URL tersebut merespons HTTP 404 pada saat diperiksa. |
| Routing aplikasi | Berhasil pada root `/Naafiu` | `router-entry.php` dan aturan rewrite melayani route aplikasi dari root proyek. Contoh endpoint yang berhasil diuji adalah `/api/mahasiswa`. |
| GET daftar mahasiswa | Berhasil | Request read-only ke `/api/mahasiswa` merespons HTTP 200 dengan JSON sukses. |
| GET detail mahasiswa | Berhasil | Request read-only untuk ID yang tersedia merespons HTTP 200 dengan JSON sukses. |
| Endpoint yang digunakan `app.js` | Berhasil | `app.js` mengambil data dari `../../api/mahasiswa.php`; endpoint hosting `/Naafiu/api/mahasiswa.php` merespons HTTP 200. `app.js` tidak memiliki konstanta `BASE_URL`. |
| Login API dan operasi tulis | Belum diuji di produksi | POST login, POST mahasiswa, PUT, dan DELETE tidak dijalankan dalam pemeriksaan ini. POST/PUT/DELETE dapat mengubah data hosting; collection Postman hanya menyiapkan request-nya. |
| Environment dan Collection Postman | Disiapkan dan JSON valid | Environment `Production` memakai base URL yang sesuai dengan routing aktif. Collection Acara 28 berisi request GET, login, dan CRUD mahasiswa. |

## Kendala dan solusi

### Base URL dengan `/Public` menghasilkan 404

Alamat yang awalnya direncanakan adalah `https://alwiyaa.my.id/Naafiu/Public`. Namun, alamat tersebut tidak sesuai dengan routing yang aktif pada hosting. Aplikasi dapat diakses melalui prefix `/Naafiu`, dan request API berhasil pada prefix tersebut.

**Solusi:** gunakan `https://alwiyaa.my.id/Naafiu` sebagai `base_url` di Environment Production. Endpoint kemudian ditambahkan setelah base URL, misalnya `{{base_url}}/api/mahasiswa`.

### Tidak ada konstanta `BASE_URL` di `app.js`

`public/views/app.js` menggunakan URL relatif `../../api/mahasiswa.php`, bukan konstanta `BASE_URL`. Halaman `public/views/index.html` memuat script tersebut, dan endpoint API yang dirujuk berhasil diuji pada hosting.

**Solusi:** tidak mengubah `app.js`; base URL hosting disimpan pada Environment Postman. Jika lokasi halaman atau struktur deployment berubah, URL relatif tersebut perlu diuji kembali.

## Environment dan Collection Postman

- Environment: [Production.postman_environment.json](../postman/Production.postman_environment.json)
- Collection: [Acara_28_Production_Testing.postman_collection.json](../postman/Acara_28_Production_Testing.postman_collection.json)
- Nilai `base_url`: `https://alwiyaa.my.id/Naafiu`
- File tidak mencantumkan password atau token.

GET daftar dan detail dapat diuji tanpa token. Untuk POST, PUT, dan DELETE diperlukan token admin. Login API menghasilkan token yang berlaku terbatas. Request POST membuat data mahasiswa uji; PUT mengubahnya; DELETE menghapus data tersebut. Jalankan operasi tulis hanya jika memang ingin menguji dan gunakan ID uji dari POST, bukan ID data yang sudah ada.

## Status dan tindak lanjut

Pemilik proyek menyampaikan bahwa aplikasi sudah di-hosting, database sudah dibuat dan diimpor, serta login ke dashboard berhasil. Pemeriksaan tersebut dicatat sebagai informasi dari pemilik proyek, bukan sebagai hasil verifikasi independen.

Pemeriksaan yang dilakukan pada sesi ini hanya mencakup validasi file JSON Postman dan request GET read-only ke endpoint hosting. Untuk melengkapi bukti deployment:

1. Jalankan kembali test case Acara 23 di environment Production jika aman dan sesuai izin.
2. Bila melakukan pengujian CRUD, gunakan data uji khusus dan bersihkan hanya data uji yang dibuat.
3. Pastikan folder `app/` berada di luar document root (`public_html`) atau pastikan akses HTTP langsung ke file di dalamnya ditolak. Struktur aktual di cPanel belum diverifikasi pada sesi ini.
4. Simpan bukti hasil pengujian dan tangkapan layar Postman pada dokumentasi/laporan praktikum.

Tidak ada perubahan database hosting yang dilakukan selama pemeriksaan.
