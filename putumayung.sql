-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 24, 2025 at 03:03 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `putumayung`
--

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `id_bank` int NOT NULL,
  `nama_bank` varchar(255) NOT NULL,
  `nomor_rekening` varchar(50) NOT NULL,
  `nama_pemilik` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `banks`
--

INSERT INTO `banks` (`id_bank`, `nama_bank`, `nomor_rekening`, `nama_pemilik`) VALUES
(1, 'Bank BCA', '1234567890', 'Muhammad Rendy Krisna'),
(2, 'Bank MANDIRI', '0987654321', 'Muhammad Rendy Krisna'),
(3, 'DANA ', '1122334455', 'Muhammad Rendy Krisna'),
(4, 'Bank BCA', '1234567890', 'Muhammad Rendy Krisna'),
(5, 'Bank MANDIRI', '0987654321', 'Muhammad Rendy Krisna'),
(6, 'DANA ', '1122334455', 'Muhammad Rendy Krisna');

-- --------------------------------------------------------

--
-- Table structure for table `ongkir`
--

CREATE TABLE `ongkir` (
  `id_ongkir` int NOT NULL,
  `daerah` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tarif` int NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ongkir`
--

INSERT INTO `ongkir` (`id_ongkir`, `daerah`, `tarif`, `keterangan`, `created_at`) VALUES
(1, 'Medan ', 20000, 'hallo', '2025-02-25 02:38:42');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `ongkir` int NOT NULL,
  `total_harga` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `alamat`, `ongkir`, `total_harga`, `created_at`) VALUES
(7, 'tembug', 20000, 30000, '2025-03-01 15:27:58'),
(12, 'tembung', 20000, 40000, '2025-03-01 15:45:11'),
(18, 'medan selayang', 30000, 150000, '2025-03-06 02:37:14'),
(20, 'Polmed Jl. Almamater No. 1 Kampus USU Medan – Sumatera Utara +62 61- 821 046', 20000, 40000, '2025-03-06 03:06:43'),
(21, 'tembung', 20000, 60000, '2025-03-06 03:12:10'),
(22, 'tembong', 20000, 40000, '2025-03-13 01:45:26'),
(23, 'Ph2', 20000, 40000, '2025-03-13 01:46:56'),
(24, 'tembung', 20000, 60000, '2025-03-13 01:53:46'),
(25, 'medan', 20000, 230000, '2025-04-10 15:29:28'),
(26, 'medan area', 20000, 50000, '2025-04-10 15:30:09');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `id_order` int NOT NULL,
  `id_product` int NOT NULL,
  `jumlah` int NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `id_order`, `id_product`, `jumlah`, `harga`, `total`) VALUES
(18, 20, 4, 1, 20000.00, 20000.00),
(19, 21, 4, 2, 20000.00, 40000.00),
(20, 22, 4, 1, 20000.00, 20000.00),
(21, 23, 4, 1, 20000.00, 20000.00),
(22, 24, 4, 2, 20000.00, 40000.00),
(23, 25, 8, 12, 15000.00, 180000.00),
(24, 25, 5, 3, 10000.00, 30000.00),
(25, 26, 5, 3, 10000.00, 30000.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id_payment` int NOT NULL,
  `id_order` int NOT NULL,
  `id_bank` int NOT NULL,
  `nama_bank` varchar(255) NOT NULL,
  `bukti_pembayaran` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','confirmed','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id_payment`, `id_order`, `id_bank`, `nama_bank`, `bukti_pembayaran`, `created_at`, `status`) VALUES
(1, 7, 1, '', '../../uploads/kode 1.png', '2025-03-01 15:30:51', 'pending'),
(4, 12, 1, '', '../../uploads/kode 5.png', '2025-03-01 15:46:28', 'pending'),
(5, 18, 2, 'Bank MANDIRI', '../../uploads/logo ig halo mi.jpg', '2025-03-06 02:37:29', 'confirmed'),
(6, 20, 2, 'Bank MANDIRI', '../../uploads/atmmandiri10.jpg', '2025-03-06 03:07:18', 'pending'),
(7, 26, 4, 'Bank BCA', '../../uploads/WhatsApp Image 2025-04-10 at 16.23.12.jpeg', '2025-04-10 15:35:16', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id_product` int NOT NULL,
  `nama_product` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `gambar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `harga` int NOT NULL,
  `rasa` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stok` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id_product`, `nama_product`, `deskripsi`, `gambar`, `harga`, `rasa`, `stok`, `created_at`) VALUES
(4, 'Putu Mayang Durian', 'Durian Asli', '1741229938_Putu-Mayang.png', 20000, 'Durian', 992, '2025-03-06 02:58:58'),
(5, 'Putu mayang', 'Coklat Asli', '1741230241_5f65c793548f7.jpg', 10000, 'Coklat', 982, '2025-03-06 03:04:01'),
(6, 'Putu Mayang', 'Santan Asli dan aren', '1741230359_istockphoto-1444346396-612x612.jpg', 15000, 'Santan & Aren', 1000, '2025-03-06 03:05:59'),
(7, 'Kopi Susu Aren', 'Terbuat dari biji kopi asli dan susu asli', '1741700385_es-kopi-nako-nusantara.jpg', 12000, 'Gula Aren', 100, '2025-03-11 13:39:45'),
(8, 'Green Thea', 'Terbuat dari Macha Asli dan susu', '1741700582_riau24_1631866966.jpg', 15000, 'Matcha', 88, '2025-03-11 13:43:02'),
(9, 'Es Kopi Americano', 'Terbuat dari biji kopi asli', '1741700693_istockphoto-1081763076-612x612.jpg', 8000, 'Americano', 100, '2025-03-11 13:44:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `phone`, `name`, `username`, `password`, `google_id`, `created_at`) VALUES
(1, 'muhammadrendykrisna@gmail.com', '085764133658', 'Muhammad Rendy Krisna', 'Rendy', '$2y$10$2JezqHgXjefAp2WHGf7mveCvDjHvRaMjjqB6VK81gKRx09XseIwum', NULL, '2025-02-24 15:02:41'),
(16, 'mifdhal@gmail.com', '2093902901', 'Muhammad Mifdhal Harahap', 'mifdhalkece', '$2y$10$fhd4NQCEGZc5llIZRlrk5O/XNFQCgz3nTodK2w8ihNET5wlx9MSCK', NULL, '2025-03-06 03:17:14'),
(23, 'randykarna@gmail.com', '71467124671246', 'Randy Karna', 'Randy2005', '$2y$10$nhyPtMJKoYBtCSsphxE8wugpXTBNOry3OA6x0LnRiRDtRWKx7Yrc.', NULL, '2025-04-10 14:57:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id_bank`);

--
-- Indexes for table `ongkir`
--
ALTER TABLE `ongkir`
  ADD PRIMARY KEY (`id_ongkir`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_product` (`id_product`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id_payment`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_bank` (`id_bank`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_product`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `google_id` (`google_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `id_bank` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ongkir`
--
ALTER TABLE `ongkir`
  MODIFY `id_ongkir` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id_payment` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id_product` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`id_bank`) REFERENCES `banks` (`id_bank`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
