-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2025 at 05:56 PM
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
-- Database: `appointment_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `appointment_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `full_name`, `phone_number`, `email`, `appointment_date`, `created_at`) VALUES
(1, 'asep', '085865650985', 'agung10104@gmail.com', '2025-06-11', '2025-06-11 15:44:00');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `description`, `price`) VALUES
(1, 'Vismodegib 150 mg', 'Inhibitor jalur Hedgehog, yang menghentikan pertumbuhan sel kanker', 5000.00),
(2, 'Sonidegib 200 Mg', 'Tidak boleh digunakan oleh ibu hamil karena berisiko tinggi pada janin.', 10000.00),
(3, 'Brag 150 Mg', 'Merangsang sistem kekebalan tubuh lokal untuk menyerang sel-sel abnormal dengan meningkatkan produksi interferon dan sitokin lain.', 7500.00),
(4, 'Vemurafenib 960 Mg', 'Menargetkan mutasi BRAF spesifik yang menyebabkan pertumbuhan kanker; bekerja secara selektif pada sel yang membawa mutasi tersebut.', 35000.00),
(5, 'Dabrafenib 150 Mg', 'Menargetkan jalur MEK yang berada di bawah BRAF dalam jalur sinyal MAPK, memperlambat pertumbuhan kanker', 14500.00),
(6, 'Encorafenib 450 Mg', 'Menghambat pembelahan sel kanker, digunakan untuk melanoma yang tidak bisa dioperasi', 17500.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `total_price`, `order_date`, `status`) VALUES
(6, 'Agung Wijaya', 25000.00, '2025-06-08', 'Completed'),
(7, 'Hendi a', 70000.00, '2025-06-11', 'Completed'),
(9, 'agung', 10000.00, '2025-06-13', 'Completed'),
(10, 'asep', 5000.00, '2025-06-13', 'Completed'),
(11, 'asep', 0.00, '2025-06-13', 'Completed'),
(12, 'asep', 5000.00, '2025-06-13', 'Completed'),
(13, 'agung', 22500.00, '2025-06-16', 'Completed'),
(14, 'agung', 57500.00, '2025-06-17', 'Completed'),
(15, 'agung', 57500.00, '2025-06-17', 'Completed'),
(16, 'agung', 99000.00, '2025-06-17', 'Completed'),
(17, 'Dava', 34000.00, '2025-06-29', 'Completed'),
(18, 'dadad', 41500.00, '2025-06-29', 'Completed'),
(19, 'Dshdbd', 76500.00, '2025-07-04', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_item` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `medicine_id`, `quantity`, `price_per_item`) VALUES
(1, 9, 2, 1, 10000.00),
(2, 7, 4, 2, 35000.00),
(3, 13, 1, 1, 5000.00),
(4, 13, 2, 1, 10000.00),
(5, 13, 3, 1, 7500.00),
(6, 14, 1, 1, 5000.00),
(7, 14, 2, 1, 10000.00),
(8, 14, 3, 1, 7500.00),
(9, 14, 4, 1, 35000.00),
(10, 15, 1, 1, 5000.00),
(11, 15, 2, 1, 10000.00),
(12, 15, 3, 1, 7500.00),
(13, 15, 4, 1, 35000.00),
(14, 16, 2, 2, 10000.00),
(15, 16, 3, 1, 7500.00),
(16, 16, 4, 1, 35000.00),
(17, 16, 6, 1, 17500.00),
(18, 17, 1, 1, 5000.00),
(19, 17, 2, 1, 10000.00),
(20, 18, 1, 1, 5000.00),
(21, 18, 2, 1, 10000.00),
(22, 18, 3, 1, 7500.00),
(23, 19, 1, 1, 5000.00),
(24, 19, 2, 1, 10000.00),
(25, 19, 3, 1, 7500.00),
(26, 19, 4, 1, 35000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin'),
(2, 'agung', 'e59cd3ce33a68f536c19fedb82a7936f', 'user'),
(3, 'asep', '202cb962ac59075b964b07152d234b70', 'user'),
(4, 'Dava', '452eec53ba9d2d2ecf44375c9e4da4e6', 'user'),
(5, 'dadad', '368da99defba4970891ab91e27be01be', 'user'),
(6, 'Dshdbd', '3e9eb3c7ac0efd1cea1a455a0b3ca04d', 'user'),
(7, 'Ciu', '6b9136da2ac40e27327d2875c5a7798c', 'user'),
(8, 'md vj sdv', 'cdb42d29e0b79c58067c7397c23e0a2e', 'user'),
(9, 'Asu', '438a696618bc88fd1d8cd916e9d9a8b7', 'user'),
(10, 'tdtdjbhb', 'ba2cbf4647bd7863e2e2d6cd1554c028', 'user'),
(11, 'fjbjb', '45788c5d7f14cf6ebf32e662fa88e596', 'user'),
(12, 'ADkdnq', 'bfec91ff45f4d46096b5659201edd39d', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
