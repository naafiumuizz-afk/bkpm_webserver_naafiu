# Laporan Praktikum Acara 26

- **Pokok bahasan:** Manajemen konten, upload file, dan manajemen basis data
- **Praktikum:** Minggu 13 / Pertemuan 2
- **Tempat:** Politeknik Negeri Jember
- **Alokasi waktu:** 2 x 170 menit

## Jadwal backup

Lakukan backup basis data `si_akademik` pada akhir **setiap sesi praktikum**, sebelum menghentikan MySQL. Di phpMyAdmin, pilih database `si_akademik`, lalu pilih **Export** → **Quick** → format **SQL** → **Export**. Simpan file dengan nama bertanggal, misalnya `si_akademik_2026-11-22.sql`, di folder `backup` lokal di luar `public/`. Setelah ekspor, pastikan file tersimpan dan ukurannya tidak nol.

## Prosedur

1. Pastikan kolom foto sudah tersedia. Untuk database yang telah dibuat sebelum praktikum ini, jalankan `ALTER TABLE mahasiswa ADD COLUMN foto VARCHAR(255) DEFAULT NULL AFTER prodi_id;` satu kali melalui phpMyAdmin. Skema proyek memakai `prodi_id` (bukan `prodi`).
2. Buka phpMyAdmin dan pilih basis data `si_akademik`.
3. Pilih **Export**, gunakan format **SQL**, lalu simpan berkas hasil ekspor dengan nama yang mencantumkan tanggal sesi.
4. Catat waktu backup, nama berkas, dan hasil ekspor pada log praktikum.
5. Untuk pemeriksaan restore, buat basis data percobaan, impor berkas SQL, lalu pastikan tabel dan data dapat dibaca.