-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 16, 2026 at 05:02 AM
-- Server version: 11.8.6-MariaDB-6 from Debian
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan_pgri`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `user_name`, `aktivitas`, `deskripsi`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 1, 'Administrator Sekolah', 'SYSTEM_INIT', 'Inisialisasi sistem perpustakaan SMK PGRI sukses.', '127.0.0.1', '2026-08-12 15:49:50', '2026-08-12 15:49:50'),
(2, 1, 'Administrator Sekolah', 'USER_LOGIN', 'User logged in with role admin', '127.0.0.1', '2026-08-12 16:50:24', '2026-08-12 16:50:24'),
(3, 1, 'Administrator Sekolah', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-12 16:51:05', '2026-08-12 16:51:05'),
(4, NULL, 'Budi Santoso', 'USER_LOGIN', 'User logged in with role mahasiswa', '127.0.0.1', '2026-08-12 16:54:15', '2026-08-12 16:54:15'),
(5, NULL, 'Budi Santoso', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-12 16:54:28', '2026-08-12 16:54:28'),
(6, 1, 'Administrator Sekolah', 'USER_LOGIN', 'User logged in with role admin', '127.0.0.1', '2026-08-12 16:55:04', '2026-08-12 16:55:04'),
(7, 1, 'Administrator Sekolah', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-12 16:55:22', '2026-08-12 16:55:22'),
(8, 1, 'Administrator Sekolah', 'USER_LOGIN', 'User logged in with role admin', '127.0.0.1', '2026-08-12 17:02:40', '2026-08-12 17:02:40'),
(9, 1, 'Administrator Sekolah', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-12 17:03:30', '2026-08-12 17:03:30'),
(10, NULL, 'Budi Santoso', 'USER_LOGIN', 'User logged in with role mahasiswa', '127.0.0.1', '2026-08-12 17:03:57', '2026-08-12 17:03:57'),
(11, NULL, 'Budi Santoso', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-12 17:11:29', '2026-08-12 17:11:29'),
(12, 1, 'Administrator Sekolah', 'USER_LOGIN', 'User logged in with role admin', '127.0.0.1', '2026-08-12 17:33:28', '2026-08-12 17:33:28'),
(13, 1, 'Administrator Sekolah', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-12 17:33:37', '2026-08-12 17:33:37'),
(14, 1, 'Administrator Sekolah', 'USER_LOGIN', 'User logged in with role admin', '127.0.0.1', '2026-08-12 17:33:50', '2026-08-12 17:33:50'),
(15, 1, 'Administrator Sekolah', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-12 17:38:34', '2026-08-12 17:38:34'),
(16, 1, 'Administrator Sekolah', 'USER_LOGIN', 'Staff logged in via Admin Portal (admin)', '127.0.0.1', '2026-08-12 18:30:53', '2026-08-12 18:30:53'),
(17, NULL, 'Budi Santoso', 'USER_LOGIN', 'Siswa logged in via Student Portal (siswa@smkpgri.sch.id)', '127.0.0.1', '2026-08-13 04:38:59', '2026-08-13 04:38:59'),
(18, NULL, 'Budi Santoso', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-13 06:32:27', '2026-08-13 06:32:27'),
(19, NULL, 'm ihwal maulana', 'USER_REGISTER', 'Siswa baru mendaftar akun perpustakaan (tesss@gmail.com - NISN: 087766787)', '127.0.0.1', '2026-08-13 17:25:08', '2026-08-13 17:25:08'),
(20, NULL, 'm ihwal maulana', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-13 17:26:33', '2026-08-13 17:26:33'),
(21, 1, 'Administrator Sekolah', 'USER_LOGIN', 'Staff logged in via Admin Portal (admin)', '127.0.0.1', '2026-08-13 17:26:45', '2026-08-13 17:26:45'),
(22, 1, 'Administrator Sekolah', 'HAPUS_BUKU', 'Menghapus buku: Clean Code: Panduan Pemrograman Rapi & Efisien', '127.0.0.1', '2026-08-13 17:28:05', '2026-08-13 17:28:05'),
(23, 1, 'Administrator Sekolah', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-13 18:08:32', '2026-08-13 18:08:32'),
(24, NULL, 'm ihwal maulana', 'USER_LOGIN', 'Siswa logged in via Student Portal (tesss@gmail.com)', '127.0.0.1', '2026-08-13 18:09:29', '2026-08-13 18:09:29'),
(25, NULL, 'm ihwal maulana', 'UPDATE_PROFIL', 'Siswa memperbarui data profil & pas foto resmi.', '127.0.0.1', '2026-08-13 18:15:03', '2026-08-13 18:15:03'),
(26, NULL, 'm ihwal maulana', 'UPDATE_PROFIL', 'Siswa memperbarui data profil & pas foto resmi.', '127.0.0.1', '2026-08-13 18:31:13', '2026-08-13 18:31:13'),
(27, NULL, 'm ihwal maulana', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-13 18:35:01', '2026-08-13 18:35:01'),
(28, NULL, 'm ihwal maulana', 'USER_LOGIN', 'Siswa logged in via Student Portal (tesss@gmail.com)', '127.0.0.1', '2026-08-13 18:35:20', '2026-08-13 18:35:20'),
(29, NULL, 'm ihwal maulana', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-13 19:02:22', '2026-08-13 19:02:22'),
(30, 1, 'Administrator Sekolah', 'USER_LOGIN', 'Staff logged in via Admin Portal (admin)', '127.0.0.1', '2026-08-13 19:02:42', '2026-08-13 19:02:42'),
(31, 1, 'Administrator Sekolah', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-13 19:09:39', '2026-08-13 19:09:39'),
(32, NULL, 'm ihwal maulana', 'USER_LOGIN', 'Siswa logged in via Student Portal (tesss@gmail.com)', '127.0.0.1', '2026-08-13 19:09:50', '2026-08-13 19:09:50'),
(33, NULL, 'm ihwal maulana', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-13 19:11:35', '2026-08-13 19:11:35'),
(34, 1, 'Administrator Sekolah', 'USER_LOGIN', 'Staff logged in via Admin Portal (admin)', '127.0.0.1', '2026-08-13 19:11:59', '2026-08-13 19:11:59'),
(35, 1, 'Administrator Sekolah', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-13 19:12:12', '2026-08-13 19:12:12'),
(36, NULL, 'Siti Rahmawati, S.Pd', 'USER_LOGIN', 'Staff logged in via Admin Portal (pustakawan)', '127.0.0.1', '2026-08-13 19:12:26', '2026-08-13 19:12:26'),
(37, NULL, 'Siti Rahmawati, S.Pd', 'BUAT_RESERVASI', 'Reservasi buku \'Administrasi Infrastruktur Jaringan (AIJ) SMK TKJ\' dibuat.', '127.0.0.1', '2026-08-13 19:14:34', '2026-08-13 19:14:34'),
(38, NULL, 'Siti Rahmawati, S.Pd', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-13 19:15:02', '2026-08-13 19:15:02'),
(39, NULL, 'm ihwal maulana', 'USER_LOGIN', 'Siswa logged in via Student Portal (tesss@gmail.com)', '127.0.0.1', '2026-08-13 19:15:17', '2026-08-13 19:15:17'),
(40, NULL, 'm ihwal maulana', 'BUAT_RESERVASI', 'Reservasi buku \'Administrasi Infrastruktur Jaringan (AIJ) SMK TKJ\' dibuat.', '127.0.0.1', '2026-08-13 19:15:28', '2026-08-13 19:15:28'),
(41, NULL, 'm ihwal maulana', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-13 19:15:47', '2026-08-13 19:15:47'),
(42, 1, 'Administrator Sekolah', 'USER_LOGIN', 'Staff logged in via Admin Portal (admin)', '127.0.0.1', '2026-08-13 19:15:59', '2026-08-13 19:15:59'),
(43, 1, 'Administrator Sekolah', 'USER_LOGIN', 'Staff logged in via Admin Portal (admin)', '127.0.0.1', '2026-08-14 02:40:58', '2026-08-14 02:40:58'),
(44, 1, 'Administrator Sekolah', 'PROSES_RESERVASI', 'Reservasi RES-202608-JTQPS disetujui & diubah ke Peminjaman TRX-20260813-ZOFBQ.', '127.0.0.1', '2026-08-14 02:48:12', '2026-08-14 02:48:12'),
(45, 1, 'Administrator Sekolah', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-14 02:48:30', '2026-08-14 02:48:30'),
(46, NULL, 'm ihwal maulana', 'USER_LOGIN', 'Siswa logged in via Student Portal (tesss@gmail.com)', '127.0.0.1', '2026-08-14 02:48:43', '2026-08-14 02:48:43'),
(47, NULL, 'm ihwal maulana', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-14 02:49:47', '2026-08-14 02:49:47'),
(48, NULL, 'Siti Rahmawati, S.Pd', 'USER_LOGIN', 'Staff logged in via Admin Portal (pustakawan)', '127.0.0.1', '2026-08-14 02:50:02', '2026-08-14 02:50:02'),
(49, NULL, 'Siti Rahmawati, S.Pd', 'TAMBAH_DENDA', 'Pustakawan menetapkan denda Rp 100,000 kepada m ihwal maulana (lambat pengembalian)', '127.0.0.1', '2026-08-14 02:51:02', '2026-08-14 02:51:02'),
(50, NULL, 'Siti Rahmawati, S.Pd', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-14 02:51:08', '2026-08-14 02:51:08'),
(51, NULL, 'm ihwal maulana', 'USER_LOGIN', 'Siswa logged in via Student Portal (tesss@gmail.com)', '127.0.0.1', '2026-08-14 02:51:16', '2026-08-14 02:51:16'),
(52, NULL, 'm ihwal maulana', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-14 03:55:52', '2026-08-14 03:55:52'),
(53, 1, 'Administrator Sekolah', 'USER_LOGIN', 'Staff logged in via Admin Portal (admin)', '127.0.0.1', '2026-08-14 03:56:06', '2026-08-14 03:56:06'),
(54, 1, 'Administrator Sekolah', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-14 04:00:37', '2026-08-14 04:00:37'),
(55, NULL, 'm ihwal maulana', 'USER_LOGIN', 'Siswa logged in via Student Portal (tesss@gmail.com)', '127.0.0.1', '2026-08-14 04:01:31', '2026-08-14 04:01:31'),
(56, NULL, 'm ihwal maulana', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-14 04:07:46', '2026-08-14 04:07:46'),
(57, 1, 'Administrator Sekolah', 'USER_LOGIN', 'Staff logged in via Admin Portal (admin)', '127.0.0.1', '2026-08-14 04:08:05', '2026-08-14 04:08:05'),
(58, 1, 'Administrator Sekolah', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-14 04:47:54', '2026-08-14 04:47:54'),
(59, 1, 'Administrator Sekolah', 'USER_LOGIN', 'Staff logged in via Admin Portal (admin)', '127.0.0.1', '2026-08-14 08:30:13', '2026-08-14 08:30:13'),
(60, 1, 'Administrator Sekolah', 'USER_LOGIN', 'Staff logged in via Admin Portal (admin)', '127.0.0.1', '2026-08-14 10:37:58', '2026-08-14 10:37:58'),
(61, 1, 'Administrator Sekolah', 'USER_LOGOUT', 'User logged out', '127.0.0.1', '2026-08-14 11:01:35', '2026-08-14 11:01:35'),
(62, 1, 'Administrator Sekolah', 'USER_LOGIN', 'Login pengguna: admin@smkpgri.sch.id', '127.0.0.1', '2026-08-14 12:59:10', '2026-08-14 12:59:10'),
(63, 1, 'Administrator Sekolah', 'USER_LOGOUT', 'User logout dari sistem perpustakaan', '127.0.0.1', '2026-08-14 13:22:09', '2026-08-14 13:22:09'),
(64, 1, 'Administrator Sekolah', 'ADMIN_LOGIN', 'Admin/Pengelola berhasil login: admin@smkpgri.sch.id', '127.0.0.1', '2026-08-14 18:08:23', '2026-08-14 18:08:23'),
(65, 1, 'Administrator Sekolah', 'ADMIN_LOGOUT', 'Pengelola logout dari sistem perpustakaan', '127.0.0.1', '2026-08-14 18:26:10', '2026-08-14 18:26:10'),
(66, 1, 'Administrator Sekolah', 'UPDATE_PENGATURAN', 'Memperbarui konfigurasi sistem perpustakaan', NULL, '2026-08-14 19:17:51', '2026-08-14 19:17:51'),
(67, 1, 'Administrator Sekolah', 'TRANSAKSI_PINJAM', 'Mencatat peminjaman TRX-20260814-HJNK (1 buku)', NULL, '2026-08-14 19:17:56', '2026-08-14 19:17:56'),
(68, 1, 'Administrator Sekolah', 'TRANSAKSI_KEMBALI', 'Buku transaksi TRX-20260814-HJNK telah berhasil dikembalikan.', NULL, '2026-08-14 19:17:56', '2026-08-14 19:17:56'),
(69, 1, 'Administrator Sekolah', 'ADMIN_LOGIN', 'Admin/Pengelola berhasil login: admin@smkpgri.sch.id', '127.0.0.1', '2026-08-15 03:37:36', '2026-08-15 03:37:36'),
(70, 1, 'Administrator Sekolah', 'TRANSAKSI_KEMBALI', 'Buku transaksi TRX-20260813-ZOFBQ telah berhasil dikembalikan.', '127.0.0.1', '2026-08-15 03:44:29', '2026-08-15 03:44:29'),
(71, 1, 'Administrator Sekolah', 'TRANSAKSI_PINJAM', 'Mencatat peminjaman TRX-20260815-C6XC untuk Ahmad Rizki Pratama (1 buku)', NULL, '2026-08-15 04:01:42', '2026-08-15 04:01:42'),
(72, 1, 'Administrator Sekolah', 'TRANSAKSI_KEMBALI', 'Buku transaksi TRX-20260815-C6XC telah berhasil dikembalikan.', NULL, '2026-08-15 04:01:42', '2026-08-15 04:01:42'),
(73, 1, 'Administrator Sekolah', 'ADMIN_LOGIN', 'Admin/Pengelola berhasil login: admin@smkpgri.sch.id', '127.0.0.1', '2026-08-15 08:13:42', '2026-08-15 08:13:42'),
(74, 1, 'Administrator Sekolah', 'ADMIN_LOGOUT', 'Pengelola logout dari sistem perpustakaan', '127.0.0.1', '2026-08-15 09:56:58', '2026-08-15 09:56:58'),
(75, 1, 'Administrator Sekolah', 'ADMIN_LOGIN', 'Admin/Pengelola berhasil login: admin@smkpgri.sch.id', '127.0.0.1', '2026-08-15 10:02:32', '2026-08-15 10:02:32'),
(76, 1, 'Administrator Sekolah', 'ADMIN_LOGOUT', 'Pengelola logout dari sistem perpustakaan', '127.0.0.1', '2026-08-15 10:25:28', '2026-08-15 10:25:28'),
(77, 1, 'Administrator Sekolah', 'ADMIN_LOGIN', 'Admin/Pengelola berhasil login: admin@smkpgri.sch.id', '127.0.0.1', '2026-08-15 10:32:12', '2026-08-15 10:32:12'),
(78, 1, 'Administrator Sekolah', 'ADMIN_LOGOUT', 'Pengelola logout dari sistem perpustakaan', '127.0.0.1', '2026-08-15 10:40:54', '2026-08-15 10:40:54'),
(79, 1, 'Administrator Sekolah', 'ADMIN_LOGIN', 'Admin/Pengelola berhasil login: admin@smkpgri.sch.id', '127.0.0.1', '2026-08-15 10:44:02', '2026-08-15 10:44:02'),
(80, 1, 'Administrator Sekolah', 'UPDATE_PENGATURAN', 'Memperbarui konfigurasi sistem & identitas perpustakaan', NULL, '2026-08-15 10:46:19', '2026-08-15 10:46:19'),
(81, 1, 'Administrator Sekolah', 'ADMIN_LOGIN', 'Admin/Pengelola berhasil login: admin@smkpgri.sch.id', '127.0.0.1', '2026-08-15 15:50:27', '2026-08-15 15:50:27'),
(82, 1, 'Administrator Sekolah', 'ADMIN_LOGOUT', 'Pengelola logout dari sistem perpustakaan', '127.0.0.1', '2026-08-15 16:03:53', '2026-08-15 16:03:53'),
(83, 1, 'Administrator Sekolah', 'ADMIN_LOGIN', 'Admin/Pengelola berhasil login: admin@smkpgri.sch.id', '127.0.0.1', '2026-08-15 16:50:44', '2026-08-15 16:50:44'),
(84, 1, 'Administrator Sekolah', 'UPDATE_PENGATURAN', 'Memperbarui konfigurasi sistem & identitas perpustakaan', '127.0.0.1', '2026-08-15 16:53:57', '2026-08-15 16:53:57'),
(85, 1, 'Administrator Sekolah', 'UPDATE_PENGATURAN', 'Memperbarui konfigurasi sistem & identitas perpustakaan', '127.0.0.1', '2026-08-15 17:29:51', '2026-08-15 17:29:51'),
(86, 1, 'Administrator Sekolah', 'ADMIN_LOGOUT', 'Pengelola logout dari sistem perpustakaan', '127.0.0.1', '2026-08-15 18:12:20', '2026-08-15 18:12:20'),
(87, 1, 'Administrator Sekolah', 'ADMIN_LOGIN', 'Admin/Pengelola berhasil login: admin@smkpgri.sch.id', '127.0.0.1', '2026-08-15 18:19:49', '2026-08-15 18:19:49'),
(88, 1, 'Administrator Sekolah', 'ADMIN_LOGOUT', 'Pengelola logout dari sistem perpustakaan', '127.0.0.1', '2026-08-15 18:43:16', '2026-08-15 18:43:16'),
(89, 1, 'Administrator Sekolah', 'ADMIN_LOGIN', 'Admin/Pengelola berhasil login: admin@smkpgri.sch.id', '127.0.0.1', '2026-08-16 06:29:43', '2026-08-16 06:29:43'),
(90, 1, 'Administrator Sekolah', 'ADMIN_LOGOUT', 'Pengelola logout dari sistem perpustakaan', '127.0.0.1', '2026-08-16 06:36:23', '2026-08-16 06:36:23'),
(91, 1, 'Administrator Sekolah', 'ADMIN_LOGIN', 'Admin/Pengelola berhasil login: admin@smkpgri.sch.id', '127.0.0.1', '2026-08-16 06:57:30', '2026-08-16 06:57:30');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `isbn` varchar(255) DEFAULT NULL,
  `judul` varchar(255) NOT NULL,
  `penulis_id` bigint(20) UNSIGNED DEFAULT NULL,
  `penerbit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kategori_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rak_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rak_laci_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tahun_terbit` int(11) NOT NULL,
  `total_quantity` int(11) NOT NULL DEFAULT 1,
  `available_quantity` int(11) NOT NULL DEFAULT 1,
  `sinopsis` text DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('tersedia','tidak_tersedia') NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `file_pdf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id`, `isbn`, `judul`, `penulis_id`, `penerbit_id`, `kategori_id`, `rak_id`, `rak_laci_id`, `tahun_terbit`, `total_quantity`, `available_quantity`, `sinopsis`, `cover`, `view_count`, `status`, `created_at`, `updated_at`, `file_pdf`) VALUES
(1, '978-602-244-123-4', 'Pemrograman Web dan Perangkat Bergerak Kelas XII SMK', 3, 1, 2, 2, 4, 2022, 1, 1, 'Buku teks pembelajaran Kurikulum Merdeka Keahlian RPL mencakup dasar HTML, CSS, JavaScript, PHP, dan framework modern.', 'covers/cover_buku_rpl.jpg', 187, 'tersedia', '2026-08-12 15:49:50', '2026-08-16 06:59:19', NULL),
(2, '978-602-8759-41-0', 'Administrasi Infrastruktur Jaringan (AIJ) SMK TKJ', 1, 2, 1, 1, 1, 2021, 1, 1, 'Panduan praktis konfigurasi VLAN, Routing MikroTik, Firewall, dan manajemen bandwidth untuk siswa kejuruan TKJ.', 'covers/cover_buku_rpl.jpg', 144, 'tersedia', '2026-08-12 15:49:50', '2026-08-15 17:31:09', NULL),
(5, '978-602-244-990-1', 'Bahasa Indonesia SMK/MAK Kelas XII Kurikulum Merdeka', 1, 1, 4, 3, 7, 2024, 10, 10, 'Buku teks utama Bahasa Indonesia untuk SMK tingkat XII dengan materi literasi, tata bahasa, dan komunikasi profesional.', NULL, 1, 'tersedia', '2026-08-15 16:49:25', '2026-08-15 18:11:07', NULL),
(6, '978-602-244-991-8', 'English for Vocational School: Kejuruan Teknik & Bisnis Kelas XII', 1, 1, 4, 3, 7, 2024, 8, 8, 'Modul Bahasa Inggris terapan bidang kejuruan teknik dan bisnis untuk persiapan kerja lulusan SMK.', NULL, 3, 'tersedia', '2026-08-15 16:49:25', '2026-08-15 17:30:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1786849109),
('5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1786849109;', 1786849109);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama`, `slug`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Teknik Komputer & Jaringan', 'tkj', 'Buku jaringan komputer, router, mikrotik, server, dan keamanan siber', '2026-08-12 15:49:49', '2026-08-12 15:49:49'),
(2, 'Rekayasa Perangkat Lunak', 'rpl', 'Buku pemetaan web, pemrograman dasar, basis data, dan aplikasi mobile', '2026-08-12 15:49:49', '2026-08-12 15:49:49'),
(3, 'Akuntansi & Keuangan', 'akuntansi', 'Perbankan, pembukuan keuangan, dan perpajakan sekolah', '2026-08-12 15:49:49', '2026-08-12 15:49:49'),
(4, 'Pelajaran Umum & Sastra', 'umum-sastra', 'Bahasa Indonesia, Matematika SMK, Bahasa Inggris, dan Novel', '2026-08-12 15:49:49', '2026-08-12 15:49:49');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_08_12_043613_create_library_system_tables', 1),
(5, '2026_08_14_050000_update_library_to_simple_workflow', 2),
(6, '2026_08_14_120000_add_borrower_fields_to_peminjaman_table', 3),
(7, '2026_08_15_100000_create_super_admin_role_and_rak_laci_tables', 4),
(8, '2026_08_16_000000_cleanup_unused_legacy_tables', 5);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_peminjaman` varchar(255) NOT NULL,
  `nama_peminjam` varchar(255) DEFAULT NULL,
  `jurusan` varchar(255) DEFAULT NULL,
  `nomor_induk` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `buku_id` bigint(20) UNSIGNED NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_jatuh_tempo` date NOT NULL,
  `jumlah_perpanjangan` int(11) NOT NULL DEFAULT 0,
  `status` enum('dipinjam','dikembalikan','terlambat') NOT NULL DEFAULT 'dipinjam',
  `waktu_kembali` timestamp NULL DEFAULT NULL,
  `petugas_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `kode_peminjaman`, `nama_peminjam`, `jurusan`, `nomor_induk`, `user_id`, `buku_id`, `jumlah`, `tanggal_pinjam`, `tanggal_jatuh_tempo`, `jumlah_perpanjangan`, `status`, `waktu_kembali`, `petugas_id`, `created_at`, `updated_at`) VALUES
(5, 'TRX-20260814-HJNK', NULL, NULL, NULL, 1, 1, 1, '2026-08-14', '2026-08-14', 0, 'dikembalikan', '2026-08-14 19:17:56', 1, '2026-08-14 19:17:56', '2026-08-14 19:17:56'),
(6, 'TRX-20260815-C6XC', 'Ahmad Rizki Pratama', 'XII RPL 1', '202611094', 1, 1, 1, '2026-08-15', '2026-08-15', 0, 'dikembalikan', '2026-08-15 04:01:42', 1, '2026-08-15 04:01:41', '2026-08-15 04:01:42');

-- --------------------------------------------------------

--
-- Table structure for table `penerbit`
--

CREATE TABLE `penerbit` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `kota` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penerbit`
--

INSERT INTO `penerbit` (`id`, `nama`, `kota`, `created_at`, `updated_at`) VALUES
(1, 'Erlangga', 'Jakarta', '2026-08-12 15:49:50', '2026-08-12 15:49:50'),
(2, 'Informatika Bandung', 'Bandung', '2026-08-12 15:49:50', '2026-08-12 15:49:50'),
(3, 'Andi Publisher', 'Yogyakarta', '2026-08-12 15:49:50', '2026-08-12 15:49:50');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `label` varchar(255) NOT NULL,
  `tipe` varchar(255) NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `key`, `value`, `label`, `tipe`, `created_at`, `updated_at`) VALUES
(1, 'max_buku_pinjam', '3', 'Max Buku Pinjam', 'number', '2026-08-12 15:49:50', '2026-08-15 10:46:19'),
(2, 'durasi_pinjam_hari', '1', 'Durasi Pinjam Hari', 'number', '2026-08-12 15:49:50', '2026-08-15 10:46:19'),
(3, 'max_perpanjangan', '2', 'Maksimal Perpanjangan Online', 'number', '2026-08-12 15:49:50', '2026-08-12 15:49:50'),
(4, 'denda_per_hari', '2000', 'Denda Keterlambatan Per Hari (Rp)', 'number', '2026-08-12 15:49:51', '2026-08-12 15:49:51'),
(5, 'denda_buku_rusak', '30000', 'Denda Buku Rusak (Rp)', 'number', '2026-08-12 15:49:51', '2026-08-12 15:49:51'),
(6, 'denda_buku_hilang', '100000', 'Denda Buku Hilang (Rp)', 'number', '2026-08-12 15:49:51', '2026-08-12 15:49:51'),
(7, 'nama_perpustakaan', 'Sistem Perpustakaan', 'Nama Perpustakaan', 'text', '2026-08-12 15:49:51', '2026-08-16 06:38:44'),
(8, 'jam_operasional', 'Senin - Kamis: 07.00 - 15.30 WIB', 'Jam Operasional', 'text', '2026-08-12 15:49:51', '2026-08-15 10:46:19'),
(9, 'alamat', 'Jl. Pendidikan No. 45, Gedung Utama Lt. 2 Perpustakaan SMK PGRI Pekanbaru.', 'Alamat', 'text', '2026-08-14 19:17:51', '2026-08-15 10:46:19'),
(10, 'nama_sekolah', 'SMK PGRI Pekanbaru', 'Nama Sekolah', 'text', '2026-08-15 10:46:19', '2026-08-15 10:46:19'),
(11, 'npsn', '10404456', 'Npsn', 'text', '2026-08-15 10:46:19', '2026-08-15 10:46:19'),
(12, 'kepala_perpustakaan', 'Dra. Hj. Nurhayati, M.Pd', 'Kepala Perpustakaan', 'text', '2026-08-15 10:46:19', '2026-08-15 10:46:19'),
(13, 'nip_kepala_perpustakaan', '19750812 200212 2 003', 'Nip Kepala Perpustakaan', 'text', '2026-08-15 10:46:19', '2026-08-15 10:46:19'),
(14, 'email_perpustakaan', 'perpustakaan@smkpgri.sch.id', 'Email Perpustakaan', 'text', '2026-08-15 10:46:19', '2026-08-15 10:46:19'),
(15, 'telepon', '08988098238', 'Telepon', 'text', '2026-08-15 10:46:19', '2026-08-15 17:29:51'),
(16, 'website_sekolah', 'https://smkpgripekanbaru.sch.id', 'Website Sekolah', 'text', '2026-08-15 10:46:19', '2026-08-15 10:46:19'),
(17, 'jam_operasional_jumat', 'Jumat: 07.00 - 11.30 WIB', 'Jam Operasional Jumat', 'text', '2026-08-15 10:46:19', '2026-08-15 10:46:19'),
(18, 'pesan_sirkulasi', 'Untuk meminjam buku fisik ini, silakan datangi meja pengelola perpustakaan. Buku dipinjam dan dikembalikan pada hari yang sama.', 'Pesan Sirkulasi', 'text', '2026-08-15 10:46:19', '2026-08-15 10:46:19'),
(19, 'syarat_peminjaman', 'Siswa aktif SMK PGRI Pekanbaru dengan menyebutkan nama lengkap & kelas.', 'Syarat Peminjaman', 'text', '2026-08-15 10:46:19', '2026-08-15 10:46:19'),
(20, 'judul_hero', 'Perpustakaan Digital SMK PGRI Pekanbaru', 'Judul Hero', 'text', '2026-08-15 10:46:19', '2026-08-15 10:46:19'),
(21, 'subjudul_hero', 'Pusat rujukan literasi, modul kejuruan, dan sirkulasi buku fisik SMK PGRI Pekanbaru.', 'Subjudul Hero', 'text', '2026-08-15 10:46:19', '2026-08-15 10:46:19'),
(22, 'buku_per_halaman', '12', 'Buku Per Halaman', 'text', '2026-08-15 10:46:19', '2026-08-15 10:46:19');

-- --------------------------------------------------------

--
-- Table structure for table `penulis`
--

CREATE TABLE `penulis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `biografi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penulis`
--

INSERT INTO `penulis` (`id`, `nama`, `biografi`, `created_at`, `updated_at`) VALUES
(1, 'Dwi Ahmad, S.T.', 'Praktisi Jaringan Komputer dan Guru Kejuruan SMK', '2026-08-12 15:49:49', '2026-08-12 15:49:49'),
(2, 'Robert C. Martin', 'Penulis Clean Code dan Arsitektur Software', '2026-08-12 15:49:49', '2026-08-12 15:49:49'),
(3, 'Drs. Supriyanto, M.M.', 'Penulis Buku Pelajaran Kejuruan Produktif SMK', '2026-08-12 15:49:49', '2026-08-12 15:49:49');

-- --------------------------------------------------------

--
-- Table structure for table `rak`
--

CREATE TABLE `rak` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_rak` varchar(255) NOT NULL,
  `nama_rak` varchar(255) NOT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `kategori_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rak`
--

INSERT INTO `rak` (`id`, `kode_rak`, `nama_rak`, `lokasi`, `kategori_id`, `deskripsi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'RAK-TKJ-01', 'Rak Komputer & Jaringan', 'Lantai 1 - Gedung Utama', 1, NULL, 'aktif', '2026-08-12 15:49:49', '2026-08-12 15:49:49'),
(2, 'RAK-RPL-01', 'Rak Pemrograman & Web', 'Lantai 1 - Gedung Utama', 2, NULL, 'aktif', '2026-08-12 15:49:49', '2026-08-12 15:49:49'),
(3, 'RAK-UM-01', 'Rak Mata Pelajaran Umum', 'Lantai 2 - Ruang Baca', 4, NULL, 'aktif', '2026-08-12 15:49:49', '2026-08-12 15:49:49');

-- --------------------------------------------------------

--
-- Table structure for table `rak_laci`
--

CREATE TABLE `rak_laci` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rak_id` bigint(20) UNSIGNED NOT NULL,
  `nomor_laci` int(11) NOT NULL DEFAULT 1,
  `nama_laci` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rak_laci`
--

INSERT INTO `rak_laci` (`id`, `rak_id`, `nomor_laci`, `nama_laci`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Laci 1', 'Tingkat 1 pada Rak Komputer & Jaringan', '2026-08-15 16:42:45', '2026-08-15 16:42:45'),
(2, 1, 2, 'Laci 2', 'Tingkat 2 pada Rak Komputer & Jaringan', '2026-08-15 16:42:45', '2026-08-15 16:42:45'),
(3, 1, 3, 'Laci 3', 'Tingkat 3 pada Rak Komputer & Jaringan', '2026-08-15 16:42:45', '2026-08-15 16:42:45'),
(4, 2, 1, 'Laci 1', 'Tingkat 1 pada Rak Pemrograman & Web', '2026-08-15 16:42:45', '2026-08-15 16:42:45'),
(5, 2, 2, 'Laci 2', 'Tingkat 2 pada Rak Pemrograman & Web', '2026-08-15 16:42:45', '2026-08-15 16:42:45'),
(6, 2, 3, 'Laci 3', 'Tingkat 3 pada Rak Pemrograman & Web', '2026-08-15 16:42:45', '2026-08-15 16:42:45'),
(7, 3, 1, 'Laci 1', 'Tingkat 1 pada Rak Mata Pelajaran Umum', '2026-08-15 16:42:46', '2026-08-15 16:42:46'),
(8, 3, 2, 'Laci 2', 'Tingkat 2 pada Rak Mata Pelajaran Umum', '2026-08-15 16:42:46', '2026-08-15 16:42:46'),
(9, 3, 3, 'Laci 3', 'Tingkat 3 pada Rak Mata Pelajaran Umum', '2026-08-15 16:42:46', '2026-08-15 16:42:46');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'Super Administrator', '2026-08-12 15:49:46', '2026-08-15 16:42:41'),
(5, 'admin', 'Admin Perpustakaan', '2026-08-15 16:42:41', '2026-08-15 16:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('7Dgjwc0IiFXpEL0k9INpiodAaxBRHs3PqjgCJ9TD', 1, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSjh5dTVtWEhmMDlFTmNzMXZWcWVyNVczTnREZ2F2Z3RPY3hXNnMxNSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1786849280),
('8ngNiCXhf0uzn0uOJvHYZlflIJeMcnExZ5iRwfqr', NULL, '127.0.0.1', 'Symfony', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYVFtMmNtZklFZDdnanZIZTh4bmFuT1JkREp3dTYyZ2ppZVdSN1hGayI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cDovL2xvY2FsaG9zdC9hZG1pbi9kYXNoYm9hcmQiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozMjoiaHR0cDovL2xvY2FsaG9zdC9hZG1pbi9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1786848488),
('Ep657QJFEjkYcRwx1YV0ZrrKsTHk2IE7aJma1P8M', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYUE5Zk9MOVpjRmNOSDdqQjJlb2d5S0RwUjFnOTFWSlVpWDFLampQUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9rYXRhbG9nIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1786848952),
('nRDHH0DOmXTLG7rwEipi9AKCupI58E1rLQwyoQ5U', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib0I5RlpWQkowZEhhY2NsMkQwN0JXdXJObkUzME9rdlFIUU4zQnluWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ha3Nlc3BlcnB1c3BncmkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1786848952),
('uVWbf2fSIroOav40nENxRl5BSApmtfZwoHOkh2Eg', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVlYxaHhZb0pHVEl2eFEwbVV1RGJNU0pKcGtmSG5KMFZMY2I2M3hJcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1786848945);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 1, 'Administrator Sekolah', 'admin@smkpgri.sch.id', '081234567890', NULL, '$2y$12$HsciFF4ObWVgIpeHhlQRo.rVsYaRXZyHuFkcxpPo2fq2yY/ADaVgO', 'active', 'JkpjlxCsuoR10ZX5UVY5RB3tTQXtWekytakpJEKUqa7tyw7u6h2ApTjewjFP', '2026-08-12 15:49:47', '2026-08-14 16:20:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `buku_isbn_unique` (`isbn`),
  ADD KEY `buku_penulis_id_foreign` (`penulis_id`),
  ADD KEY `buku_penerbit_id_foreign` (`penerbit_id`),
  ADD KEY `buku_kategori_id_foreign` (`kategori_id`),
  ADD KEY `buku_rak_id_foreign` (`rak_id`),
  ADD KEY `buku_rak_laci_id_foreign` (`rak_laci_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kategori_nama_unique` (`nama`),
  ADD UNIQUE KEY `kategori_slug_unique` (`slug`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `peminjaman_kode_peminjaman_unique` (`kode_peminjaman`),
  ADD KEY `peminjaman_user_id_foreign` (`user_id`),
  ADD KEY `peminjaman_buku_id_foreign` (`buku_id`),
  ADD KEY `peminjaman_petugas_id_foreign` (`petugas_id`);

--
-- Indexes for table `penerbit`
--
ALTER TABLE `penerbit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengaturan_key_unique` (`key`);

--
-- Indexes for table `penulis`
--
ALTER TABLE `penulis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rak`
--
ALTER TABLE `rak`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rak_kode_rak_unique` (`kode_rak`),
  ADD KEY `rak_kategori_id_foreign` (`kategori_id`);

--
-- Indexes for table `rak_laci`
--
ALTER TABLE `rak_laci`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rak_laci_rak_id_foreign` (`rak_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `penerbit`
--
ALTER TABLE `penerbit`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `penulis`
--
ALTER TABLE `penulis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rak`
--
ALTER TABLE `rak`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rak_laci`
--
ALTER TABLE `rak_laci`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `buku`
--
ALTER TABLE `buku`
  ADD CONSTRAINT `buku_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `buku_penerbit_id_foreign` FOREIGN KEY (`penerbit_id`) REFERENCES `penerbit` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `buku_penulis_id_foreign` FOREIGN KEY (`penulis_id`) REFERENCES `penulis` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `buku_rak_id_foreign` FOREIGN KEY (`rak_id`) REFERENCES `rak` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `buku_rak_laci_id_foreign` FOREIGN KEY (`rak_laci_id`) REFERENCES `rak_laci` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_buku_id_foreign` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjaman_petugas_id_foreign` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `peminjaman_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rak`
--
ALTER TABLE `rak`
  ADD CONSTRAINT `rak_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rak_laci`
--
ALTER TABLE `rak_laci`
  ADD CONSTRAINT `rak_laci_rak_id_foreign` FOREIGN KEY (`rak_id`) REFERENCES `rak` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
