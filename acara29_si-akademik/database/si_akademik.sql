CREATE DATABASE IF NOT EXISTS si_akademik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE si_akademik;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS prodi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(10) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    UNIQUE KEY uq_prodi_nama (nama),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    prodi_id INT NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    angkatan YEAR NOT NULL,
    status ENUM('aktif', 'cuti', 'lulus') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_mahasiswa_prodi FOREIGN KEY (prodi_id) REFERENCES prodi(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS matakuliah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(10) NOT NULL UNIQUE,
    nama VARCHAR(150) NOT NULL,
    sks TINYINT NOT NULL,
    prodi_id INT NOT NULL,
    UNIQUE KEY uq_matakuliah_nama (nama),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_matakuliah_prodi FOREIGN KEY (prodi_id) REFERENCES prodi(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

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

SET @prodi_name_index_exists = (
    SELECT COUNT(*) FROM information_schema.statistics
    WHERE table_schema = DATABASE() AND table_name = 'prodi' AND index_name = 'uq_prodi_nama'
);
SET @prodi_name_index_sql = IF(
    @prodi_name_index_exists = 0,
    'ALTER TABLE prodi ADD UNIQUE INDEX uq_prodi_nama (nama)',
    'SELECT 1'
);
PREPARE prodi_name_index_stmt FROM @prodi_name_index_sql;
EXECUTE prodi_name_index_stmt;
DEALLOCATE PREPARE prodi_name_index_stmt;

SET @matakuliah_name_index_exists = (
    SELECT COUNT(*) FROM information_schema.statistics
    WHERE table_schema = DATABASE() AND table_name = 'matakuliah' AND index_name = 'uq_matakuliah_nama'
);
SET @matakuliah_name_index_sql = IF(
    @matakuliah_name_index_exists = 0,
    'ALTER TABLE matakuliah ADD UNIQUE INDEX uq_matakuliah_nama (nama)',
    'SELECT 1'
);
PREPARE matakuliah_name_index_stmt FROM @matakuliah_name_index_sql;
EXECUTE matakuliah_name_index_stmt;
DEALLOCATE PREPARE matakuliah_name_index_stmt;

SET @mahasiswa_status_exists = (
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'mahasiswa' AND column_name = 'status'
);
SET @mahasiswa_status_sql = IF(
    @mahasiswa_status_exists = 0,
    'ALTER TABLE mahasiswa ADD COLUMN status ENUM(''aktif'', ''cuti'', ''lulus'') NOT NULL DEFAULT ''aktif''',
    'SELECT 1'
);
PREPARE mahasiswa_status_stmt FROM @mahasiswa_status_sql;
EXECUTE mahasiswa_status_stmt;
DEALLOCATE PREPARE mahasiswa_status_stmt;

SET @mahasiswa_foto_exists = (
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'mahasiswa' AND column_name = 'foto'
);
SET @mahasiswa_foto_sql = IF(
    @mahasiswa_foto_exists = 0,
    'ALTER TABLE mahasiswa ADD COLUMN foto VARCHAR(255) DEFAULT NULL AFTER prodi_id',
    'ALTER TABLE mahasiswa MODIFY COLUMN foto VARCHAR(255) DEFAULT NULL AFTER prodi_id'
);
PREPARE mahasiswa_foto_stmt FROM @mahasiswa_foto_sql;
EXECUTE mahasiswa_foto_stmt;
DEALLOCATE PREPARE mahasiswa_foto_stmt;

INSERT IGNORE INTO prodi (kode, nama) VALUES
('TI', 'Teknik Informatika'),
('SI', 'Sistem Informasi'),
('TK', 'Teknik Komputer');

INSERT IGNORE INTO mahasiswa (nim, nama, email, prodi_id, angkatan) VALUES
('2401001', 'Budi Santoso', 'budi@email.com', 1, 2024),
('2401002', 'Ani Wijaya', 'ani@email.com', 1, 2024),
('2402001', 'Citra Lestari', 'citra@email.com', 2, 2024);

INSERT IGNORE INTO matakuliah (kode, nama, sks, prodi_id) VALUES
('TI101', 'Pemrograman Dasar', 3, 1),
('TI102', 'Basis Data', 3, 1),
('SI101', 'Pengantar SI', 2, 2);

INSERT IGNORE INTO buku (kode, judul, penulis, penerbit, tahun_terbit) VALUES
('BK-001', 'Pemrograman Web', 'Andi Saputra', 'Penerbit Kampus', 2025),
('BK-002', 'Basis Data MySQL', 'Siti Rahma', 'Media Informatika', 2024),
('BK-003', 'Rekayasa Perangkat Lunak', 'Budi Santoso', 'Teknologi Press', 2023);
