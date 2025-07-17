-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 17, 2025 at 12:40 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

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
  `id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `appointment_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `description`, `price`) VALUES
(1, 'Paracetamol', 'Obat penurun demam dan pereda nyeri. Dosis umum 500mg.', 5000.00),
(2, 'Amoxicillin', 'Antibiotik untuk infeksi bakteri. Gunakan sesuai resep dokter.', 10000.00),
(3, 'Loperamide', 'Obat untuk diare akut. Dosis dewasa 2mg per tablet.', 7500.00),
(4, 'Cazetin', 'Obat untuk diare akut. Dosis dewasa 2mg per tablet.', 35000.00),
(5, 'Asterol', 'Obat untuk diare akut. Dosis dewasa 2mg per tablet.', 14500.00),
(6, 'Cetirizin', 'Obat untuk diare akut. Dosis dewasa 2mg per tablet.', 17500.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(16, 'agung', 99000.00, '2025-06-17', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `medicine_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price_per_item` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(17, 16, 6, 1, 17500.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin'),
(2, 'agung', 'e59cd3ce33a68f536c19fedb82a7936f', 'user'),
(3, 'asep', '202cb962ac59075b964b07152d234b70', 'user');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
