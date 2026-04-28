-- =============================================
-- DATABASE: db_monitoring_pkl_smk
-- UNTUK: SMKN 1 SUBANG
-- =============================================

-- --------------------------------------------------------
-- Table structure for table `roles`
-- --------------------------------------------------------
CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` enum('admin','guru','siswa','perusahaan') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `nisn` varchar(20) DEFAULT NULL,              -- untuk siswa
  `nip` varchar(20) DEFAULT NULL,               -- untuk guru
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_nisn_unique` (`nisn`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `gurus`
-- --------------------------------------------------------
CREATE TABLE `gurus` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nip` varchar(20) NOT NULL,
  `nuptk` varchar(20) DEFAULT NULL,
  `mata_pelajaran` varchar(255) DEFAULT NULL,
  `pendidikan_terakhir` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gurus_user_id_unique` (`user_id`),
  UNIQUE KEY `gurus_nip_unique` (`nip`),
  CONSTRAINT `gurus_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `perusahaans`
-- --------------------------------------------------------
CREATE TABLE `perusahaans` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `radius` int(11) NOT NULL DEFAULT 100,        -- radius absensi dalam meter
  `telepon` varchar(15) NOT NULL,
  `bidang_usaha` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `kontak_person` varchar(255) NOT NULL,
  `jabatan_kontak` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `perusahaans_user_id_foreign` (`user_id`),
  CONSTRAINT `perusahaans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `kelas`
-- --------------------------------------------------------
CREATE TABLE `kelas` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(100) NOT NULL,           -- contoh: XII RPL 1, XII TKJ 2
  `jurusan` enum('RPL','TKJ','MM','BC','DKV') NOT NULL,
  `tingkat` enum('10','11','12') NOT NULL,
  `wali_kelas_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tahun_ajaran` varchar(20) NOT NULL,         -- contoh: 2024/2025
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kelas_wali_kelas_id_foreign` (`wali_kelas_id`),
  CONSTRAINT `kelas_wali_kelas_id_foreign` FOREIGN KEY (`wali_kelas_id`) REFERENCES `gurus` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `siswas`
-- --------------------------------------------------------
CREATE TABLE `siswas` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `kelas_id` bigint(20) UNSIGNED DEFAULT NULL,
  `perusahaan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `guru_pembimbing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal_mulai_pkl` date DEFAULT NULL,
  `tanggal_selesai_pkl` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siswas_user_id_unique` (`user_id`),
  UNIQUE KEY `siswas_nisn_unique` (`nisn`),
  KEY `siswas_kelas_id_foreign` (`kelas_id`),
  KEY `siswas_perusahaan_id_foreign` (`perusahaan_id`),
  KEY `siswas_guru_pembimbing_id_foreign` (`guru_pembimbing_id`),
  CONSTRAINT `siswas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `siswas_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswas_perusahaan_id_foreign` FOREIGN KEY (`perusahaan_id`) REFERENCES `perusahaans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswas_guru_pembimbing_id_foreign` FOREIGN KEY (`guru_pembimbing_id`) REFERENCES `gurus` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `absensis`
-- --------------------------------------------------------
CREATE TABLE `absensis` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `status` enum('hadir','izin','sakit','alpha','terlambat') NOT NULL DEFAULT 'alpha',
  `keterangan` text DEFAULT NULL,
  `bukti_foto` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_valid_location` tinyint(1) NOT NULL DEFAULT 0,
  `guru_absen_id` bigint(20) UNSIGNED DEFAULT NULL,
  `guru_absen_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `absensis_siswa_id_tanggal_unique` (`siswa_id`,`tanggal`),
  KEY `absensis_guru_absen_id_foreign` (`guru_absen_id`),
  CONSTRAINT `absensis_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `absensis_guru_absen_id_foreign` FOREIGN KEY (`guru_absen_id`) REFERENCES `gurus` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `logbooks`
-- --------------------------------------------------------
CREATE TABLE `logbooks` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `kegiatan` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `dokumentasi` varchar(255) DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `status_hari` enum('normal','izin','sakit') NOT NULL DEFAULT 'normal',
  `catatan_guru` text DEFAULT NULL,
  `catatan_perusahaan` text DEFAULT NULL,
  `approved_by_guru` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by_perusahaan` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at_guru` timestamp NULL DEFAULT NULL,
  `approved_at_perusahaan` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logbooks_siswa_id_foreign` (`siswa_id`),
  KEY `logbooks_approved_by_guru_foreign` (`approved_by_guru`),
  KEY `logbooks_approved_by_perusahaan_foreign` (`approved_by_perusahaan`),
  CONSTRAINT `logbooks_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `logbooks_approved_by_guru_foreign` FOREIGN KEY (`approved_by_guru`) REFERENCES `gurus` (`id`) ON DELETE SET NULL,
  CONSTRAINT `logbooks_approved_by_perusahaan_foreign` FOREIGN KEY (`approved_by_perusahaan`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `ijin_sakit`
-- --------------------------------------------------------
CREATE TABLE `ijin_sakit` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `jenis` enum('izin','sakit') NOT NULL,
  `alasan` text NOT NULL,
  `bukti_foto` varchar(255) DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `catatan_guru` text DEFAULT NULL,
  `approved_by_guru` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ijin_sakit_siswa_id_foreign` (`siswa_id`),
  KEY `ijin_sakit_approved_by_guru_foreign` (`approved_by_guru`),
  CONSTRAINT `ijin_sakit_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ijin_sakit_approved_by_guru_foreign` FOREIGN KEY (`approved_by_guru`) REFERENCES `gurus` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `laporan_pkl`
-- --------------------------------------------------------
CREATE TABLE `laporan_pkl` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `judul_laporan` varchar(255) NOT NULL,
  `abstrak` text NOT NULL,
  `file_laporan` varchar(255) NOT NULL,
  `status` enum('draft','diajukan','direview','disetujui','ditolak') NOT NULL DEFAULT 'draft',
  `catatan_revisi` text DEFAULT NULL,
  `reviewer_guru_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `laporan_pkl_siswa_id_foreign` (`siswa_id`),
  KEY `laporan_pkl_reviewer_guru_id_foreign` (`reviewer_guru_id`),
  CONSTRAINT `laporan_pkl_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `laporan_pkl_reviewer_guru_id_foreign` FOREIGN KEY (`reviewer_guru_id`) REFERENCES `gurus` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `penilaians`
-- --------------------------------------------------------
CREATE TABLE `penilaians` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `penilai` enum('guru','perusahaan') NOT NULL,
  `nilai_kehadiran` int(11) DEFAULT NULL,
  `nilai_logbook` int(11) DEFAULT NULL,
  `nilai_laporan` int(11) DEFAULT NULL,
  `nilai_sikap` int(11) DEFAULT NULL,
  `nilai_kinerja` int(11) DEFAULT NULL,
  `nilai_kedisiplinan` int(11) DEFAULT NULL,
  `nilai_kerjasama` int(11) DEFAULT NULL,
  `nilai_inisiatif` int(11) DEFAULT NULL,
  `nilai_akhir` double DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `penilai_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `penilaians_siswa_id_foreign` (`siswa_id`),
  KEY `penilaians_penilai_id_foreign` (`penilai_id`),
  CONSTRAINT `penilaians_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penilaians_penilai_id_foreign` FOREIGN KEY (`penilai_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `notifikasis`
-- --------------------------------------------------------
CREATE TABLE `notifikasis` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `pesan` text NOT NULL,
  `tipe` enum('info','success','warning','error') NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifikasis_user_id_foreign` (`user_id`),
  CONSTRAINT `notifikasis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `sessions`
-- --------------------------------------------------------
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `cache`
-- --------------------------------------------------------
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `jobs`
-- --------------------------------------------------------
CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DATA SEEDER
-- =============================================

-- Insert Roles
INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'admin', NOW(), NOW()),
(2, 'guru', NOW(), NOW()),
(3, 'siswa', NOW(), NOW()),
(4, 'perusahaan', NOW(), NOW());

-- Insert Admin User
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@smkn1subang.sch.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, NOW(), NOW());

-- Insert Guru User
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'Budi Santoso, S.Kom', 'guru@smkn1subang.sch.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 1, NOW(), NOW());

-- Insert Guru Detail
INSERT INTO `gurus` (`user_id`, `nip`, `mata_pelajaran`, `created_at`, `updated_at`) VALUES
(2, '198001012005011001', 'Pemrograman Web', NOW(), NOW());

-- Insert Perusahaan User
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 'PT. Teknologi Nusantara', 'perusahaan1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 1, NOW(), NOW());

-- Insert Perusahaan Detail
INSERT INTO `perusahaans` (`user_id`, `nama_perusahaan`, `alamat`, `latitude`, `longitude`, `radius`, `telepon`, `bidang_usaha`, `kontak_person`, `jabatan_kontak`, `created_at`, `updated_at`) VALUES
(3, 'PT. Teknologi Nusantara', 'Jl. Merdeka No. 123, Subang', -6.56960000, 107.75830000, 100, '08123456789', 'Teknologi Informasi', 'Andi Wijaya', 'HR Manager', NOW(), NOW());

-- Insert Kelas
INSERT INTO `kelas` (`nama_kelas`, `jurusan`, `tingkat`, `tahun_ajaran`, `created_at`, `updated_at`) VALUES
('XII RPL 1', 'RPL', '12', '2024/2025', NOW(), NOW()),
('XII RPL 2', 'RPL', '12', '2024/2025', NOW(), NOW()),
('XII TKJ 1', 'TKJ', '12', '2024/2025', NOW(), NOW());

-- Insert Siswa User
INSERT INTO `users` (`id`, `name`, `email`, `password`, `nisn`, `role_id`, `is_active`, `created_at`, `updated_at`) VALUES
(4, 'Ahmad Fauzi', 'siswa1@smkn1subang.sch.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0012345678', 3, 1, NOW(), NOW());

-- Insert Siswa Detail
INSERT INTO `siswas` (`user_id`, `nisn`, `kelas_id`, `perusahaan_id`, `guru_pembimbing_id`, `tanggal_mulai_pkl`, `tanggal_selesai_pkl`, `created_at`, `updated_at`) VALUES
(4, '0012345678', 1, 1, 1, '2026-01-01', '2026-03-31', NOW(), NOW());