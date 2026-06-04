-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2026 at 09:36 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi_qrcode`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensis`
--

CREATE TABLE `absensis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `id_shift` bigint(20) UNSIGNED NOT NULL,
  `id_barcode` bigint(20) UNSIGNED NOT NULL,
  `tanggal_absen` date NOT NULL,
  `waktu_absen` datetime NOT NULL,
  `status` enum('hadir','terlambat') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `absensis`
--

INSERT INTO `absensis` (`id`, `id_user`, `id_shift`, `id_barcode`, `tanggal_absen`, `waktu_absen`, `status`, `created_at`, `updated_at`) VALUES
(1, 12, 2, 2, '2026-01-27', '2026-01-27 15:46:22', 'hadir', '2026-01-27 08:46:22', '2026-01-27 08:46:22'),
(2, 12, 5, 5, '2026-04-03', '2026-04-03 14:15:19', 'hadir', '2026-04-03 07:15:19', '2026-04-03 07:15:19');

-- --------------------------------------------------------

--
-- Table structure for table `barcodes`
--

CREATE TABLE `barcodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_shift` bigint(20) UNSIGNED NOT NULL,
  `kode_barcode` char(36) NOT NULL,
  `waktu_mulai` datetime NOT NULL,
  `waktu_akhir` datetime NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barcodes`
--

INSERT INTO `barcodes` (`id`, `id_shift`, `kode_barcode`, `waktu_mulai`, `waktu_akhir`, `status`, `created_at`, `updated_at`) VALUES
(2, 2, '97e6a5c7-c9ca-4144-ab5a-94fc297a0f75', '2026-01-27 15:00:00', '2026-01-27 17:00:00', 'nonaktif', '2026-01-27 08:05:19', '2026-01-27 08:05:19'),
(4, 5, '072bf6f7-9a30-4fce-8bed-7cb4b01eb1f8', '2026-04-03 14:03:29', '2026-04-03 14:08:29', 'aktif', '2026-04-03 07:03:29', '2026-04-03 07:03:29'),
(5, 5, '1c45db47-e6df-43e6-99e6-7a810ab97ba9', '2026-04-03 14:14:46', '2026-04-03 14:19:46', 'aktif', '2026-04-03 07:14:46', '2026-04-03 07:14:46');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(4, '2025_12_03_155209_create_shifts_table', 1),
(5, '2025_12_03_155254_create_barcodes_table', 1),
(6, '2025_12_03_155312_create_absensis_table', 1);

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
('8efTbIUP23uvdGoakZCj8qx1uDWLDTewM0twLbMs', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRERUc0ZNUEhzdWQwRUg1M0JLaFhYSlVxVzRnZkFnS3NlZHRsMzFxSiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6MTU6ImFkbWluLmRhc2hib2FyZCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1775200587),
('KGuUhLzYjnJNZYKybbRhSgdVwh0H4WvDpcxwMEIJ', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRnRYNnhvZnR6VHVLOTNCRzA4UHFaUG1Zc3VEVkVSTlBJMVViRmFURSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9iYXJjb2RlLWRpc3BsYXkvNSI7czo1OiJyb3V0ZSI7czoyMToiYWRtaW4uYmFyY29kZS1kaXNwbGF5Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1775201767),
('VgJKByTluA2gt8KMpNxVirCVVHBaDkkL6loFv03e', 12, '192.168.18.24', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_4_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/146.0.7680.151 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTjd3ak1xRlVkS3R5dVo0alFxN1dlaFhhbnF0Y1RQZGZjbmhrbXFaMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xOTIuMTY4LjE4LjIyOjgwMDAvYXBpL3FyLWFjdGl2ZSI7czo1OiJyb3V0ZSI7czoxMzoiYXBpLnFyLWFjdGl2ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEyO30=', 1775200748),
('ZvmLFoCC4hV31rGtgn2CMrcWWtKHMopWmiC1CrUR', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSFJlUTVmYlVXbjgxcHBHdWx4a210SGFCQnFrSEx0R1oxQnBwTkgySiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTE4OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vZ2VuZXJhdGUtYmFyY29kZT9fdG9rZW49SFJlUTVmYlVXbjgxcHBHdWx4a210SGFCQnFrSEx0R1oxQnBwTkgySiZkdXJhc2lfbWVuaXQ9NSZpZF9zaGlmdD01IjtzOjU6InJvdXRlIjtzOjIyOiJhZG1pbi5nZW5lcmF0ZS1iYXJjb2RlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1775200441);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_shift` varchar(100) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_akhir` time NOT NULL,
  `batas_telat` int(11) NOT NULL COMMENT 'Batas menit toleransi keterlambatan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `nama_shift`, `jam_mulai`, `jam_akhir`, `batas_telat`, `created_at`, `updated_at`) VALUES
(2, 'Sore', '16:00:00', '00:00:00', 15, '2026-01-27 08:05:19', '2026-01-27 08:05:19'),
(5, 'FLEKSI', '13:59:00', '14:30:00', 15, '2026-04-03 06:58:43', '2026-04-03 06:58:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid_user` char(36) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nip` varchar(20) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','karyawan') NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `departemen` varchar(100) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `status_karyawan` enum('tetap','kontrak','probation') NOT NULL DEFAULT 'probation',
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uuid_user`, `nama`, `nip`, `username`, `email`, `password`, `role`, `jabatan`, `departemen`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_telepon`, `status_karyawan`, `tanggal_masuk`, `tanggal_keluar`, `foto`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '7606153b-29b0-4960-8e9a-c64e2090401a', 'Administrator', 'ADM001', 'admin', 'admin@absensi.com', '$2y$12$iOCZIf1/40w9VCEm4VLLaOuT7Kf/n7s/eAl5k4qMyXCBr6s/dPGI2', 'admin', 'System Administrator', 'IT', 'L', 'Jakarta', '1985-05-15', 'Jl. Admin No. 1, Jakarta Pusat', '081234567890', 'tetap', '2020-01-01', NULL, NULL, NULL, '2026-01-27 08:05:16', '2026-01-27 08:05:16'),
(2, 'de984f91-d2b2-4497-96ef-7af4beee8ff3', 'Budi Santoso', 'KRY001', 'budi', 'budi.santoso@company.com', '$2y$12$UHgd50tpy6pygf93muyUMuAS5z4Mc738.pnPFqoqT18ZFUj8tt8o.', 'karyawan', 'Staff IT', 'IT', 'L', 'Bandung', '1990-08-20', 'Jl. Merdeka No. 45, Bandung', '081234567891', 'tetap', '2021-03-15', NULL, NULL, NULL, '2026-01-27 08:05:19', '2026-01-27 08:05:19'),
(3, '66536013-b954-449d-814f-071095ce38f4', 'Siti Aminah', 'KRY002', 'siti', 'siti.aminah@company.com', '$2y$12$Z/Y/hhmqKHa39ReUerAOGOT3XnyK3yUEwef4rsJjeYDMY.y8CqMxy', 'karyawan', 'HR Staff', 'Human Resources', 'P', 'Surabaya', '1992-11-10', 'Jl. Diponegoro No. 12, Surabaya', '081234567892', 'tetap', '2021-06-01', NULL, NULL, NULL, '2026-01-27 08:05:19', '2026-01-27 08:05:19'),
(4, '75c571bd-b3fd-43a4-bcc0-4af69853cebb', 'Ahmad Fauzi', 'KRY003', 'ahmad', 'ahmad.fauzi@company.com', '$2y$12$kFAIoZkIS/JmUG7gAnncWO4Vk.bD1OZpD/8nmgcb/P2YHHa/x62Am', 'karyawan', 'Marketing Executive', 'Marketing', 'L', 'Semarang', '1988-03-25', 'Jl. Gajah Mada No. 78, Semarang', '081234567893', 'kontrak', '2023-01-10', '2024-01-09', NULL, NULL, '2026-01-27 08:05:19', '2026-01-27 08:05:19'),
(5, '1aa3c3d7-05f6-4deb-a1cc-c0fa6e4cc395', 'Rina Melati', 'KRY004', 'rina', 'rina.melati@company.com', '$2y$12$X9lu.iWppJrCX2ftP2egTO4ALLhryfStO3OmwLdMeFbao.G7jHqOm', 'karyawan', 'Finance Officer', 'Finance', 'P', 'Yogyakarta', '1995-07-14', 'Jl. Malioboro No. 56, Yogyakarta', '081234567894', 'tetap', '2022-08-20', NULL, NULL, NULL, '2026-01-27 08:05:19', '2026-01-27 08:05:19'),
(6, '75b077c7-fa3e-41dd-b95c-5676f1314dad', 'Dewi Anggraini', 'KRY005', 'dewi', 'dewi.anggraini@company.com', '$2y$12$BlZMD0S81fkra3R9EcrmhOhOm7sT.JQdrPK5lqXUoiD.68f3Xx99C', 'karyawan', 'Customer Service', 'Customer Service', 'P', 'Malang', '1993-09-30', 'Jl. Ijen No. 23, Malang', '081234567895', 'probation', '2025-11-27', NULL, NULL, NULL, '2026-01-27 08:05:19', '2026-01-27 08:05:19'),
(7, '7a1988d8-eb27-424f-96f5-0fd85ac64498', 'Joko Susilo', 'KRY006', 'joko', 'joko.susilo@company.com', '$2y$12$2FIFN9tver8Dv7wcsPU1s.vrtAiA8QVCoz4asKxnJph8jpry9Ke6S', 'karyawan', 'Production Supervisor', 'Production', 'L', 'Solo', '1987-12-05', 'Jl. Slamet Riyadi No. 34, Solo', '081234567896', 'tetap', '2020-11-15', NULL, NULL, NULL, '2026-01-27 08:05:19', '2026-01-27 08:05:19'),
(8, 'a0da3e35-048a-4a05-8ca6-8fcc8166bbd7', 'Maya Sari', 'KRY007', 'maya', 'maya.sari@company.com', '$2y$12$wtJKnco8Kit/CZKWFB0EauOyV8P4Ko4OBbYVRjS4WUadi2JXSO8nC', 'karyawan', 'Quality Control', 'Quality Assurance', 'P', 'Medan', '1991-04-18', 'Jl. Gatot Subroto No. 67, Medan', '081234567897', 'tetap', '2021-09-10', NULL, NULL, NULL, '2026-01-27 08:05:19', '2026-01-27 08:05:19'),
(9, '97e1fa39-145b-47a3-b593-8ee10f37cb13', 'Rizki Pratama', 'KRY008', 'rizki', 'rizki.pratama@company.com', '$2y$12$Wp7H3zIuo54r9ls2CM.cIOuTz7Yy6d1MMMsovndqMwBabCr6xH556', 'karyawan', 'Warehouse Staff', 'Logistics', 'L', 'Bekasi', '1994-02-22', 'Jl. Juanda No. 89, Bekasi', '081234567898', 'kontrak', '2023-03-01', '2024-03-01', NULL, NULL, '2026-01-27 08:05:19', '2026-01-27 08:05:19'),
(10, 'f56ecfb4-b3f5-4364-90d7-f45686ddac18', 'Linda Wati', 'KRY009', 'linda', 'linda.wati@company.com', '$2y$12$N1n70WS5YMrZMeRTHkXb3uZTpkWTJBA9P3uUZ5xXyO.oQuGOWY.Bi', 'karyawan', 'Sales Representative', 'Sales', 'P', 'Tangerang', '1989-06-12', 'Jl. Sudirman No. 45, Tangerang', '081234567899', 'tetap', '2019-05-05', NULL, NULL, NULL, '2026-01-27 08:05:19', '2026-01-27 08:05:19'),
(11, 'adb8df92-4665-4b46-a309-f6deb1859f48', 'Hendra Gunawan', 'KRY010', 'hendra', 'hendra.gunawan@company.com', '$2y$12$MSPheWQ5yuFNi4WZHewYUeTs8AeD91wpBW2S.iRZBuiwNRvqbz.s.', 'karyawan', 'Maintenance Technician', 'Maintenance', 'L', 'Bogor', '1986-10-08', 'Jl. Pajajaran No. 11, Bogor', '081234567810', 'tetap', '2018-12-20', '2023-12-19', NULL, NULL, '2026-01-27 08:05:19', '2026-01-27 08:05:19'),
(12, '2e1b3ccd-cbfb-434e-bed9-02f12c81de7e', 'Deny Kurniawan', '23456789', 'denykunp', 'denykunp@gmail.com', '$2y$12$8UivawLtCEUGJ0B56e17g.f9zQpfK2mlv3FovJNzmnRVNkZmJoDDG', 'karyawan', 'kitchen', 'Kitchen', 'L', 'Kediri', '2000-12-15', 'Kediri', '345678', 'tetap', '2026-01-27', NULL, NULL, NULL, '2026-01-27 08:06:38', '2026-01-27 08:06:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensis`
--
ALTER TABLE `absensis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `absensis_id_user_foreign` (`id_user`),
  ADD KEY `absensis_id_shift_foreign` (`id_shift`),
  ADD KEY `absensis_id_barcode_foreign` (`id_barcode`);

--
-- Indexes for table `barcodes`
--
ALTER TABLE `barcodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barcodes_kode_barcode_unique` (`kode_barcode`),
  ADD KEY `barcodes_id_shift_foreign` (`id_shift`);

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
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_uuid_user_unique` (`uuid_user`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_nip_unique` (`nip`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensis`
--
ALTER TABLE `absensis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `barcodes`
--
ALTER TABLE `barcodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensis`
--
ALTER TABLE `absensis`
  ADD CONSTRAINT `absensis_id_barcode_foreign` FOREIGN KEY (`id_barcode`) REFERENCES `barcodes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absensis_id_shift_foreign` FOREIGN KEY (`id_shift`) REFERENCES `shifts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absensis_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `barcodes`
--
ALTER TABLE `barcodes`
  ADD CONSTRAINT `barcodes_id_shift_foreign` FOREIGN KEY (`id_shift`) REFERENCES `shifts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
