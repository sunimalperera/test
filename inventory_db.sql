-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2025 at 01:17 AM
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
-- Database: `inventory_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `part_prices`
--

CREATE TABLE `part_prices` (
  `id` int(11) NOT NULL,
  `part_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `part_prices`
--

INSERT INTO `part_prices` (`id`, `part_id`, `supplier_id`, `price`, `created_at`) VALUES
(43, 19, 1, 9.00, '2025-03-14 21:30:01'),
(44, 19, 2, 6.00, '2025-03-14 21:30:01'),
(45, 20, 1, 120.00, '2025-03-14 21:30:58'),
(46, 20, 2, 130.00, '2025-03-14 21:30:58'),
(47, 21, 1, 2.00, '2025-03-14 21:42:43'),
(48, 21, 2, 1.50, '2025-03-14 21:42:43'),
(49, 21, 1, 2.00, '2025-03-14 21:42:43'),
(51, 18, 3, 7.00, '2025-03-17 20:50:13'),
(52, 22, 1, 4.00, '2025-03-17 21:18:06'),
(53, 22, 3, 3.00, '2025-03-17 21:18:06');

-- --------------------------------------------------------

--
-- Table structure for table `spare_parts`
--

CREATE TABLE `spare_parts` (
  `id` int(11) NOT NULL,
  `part_name` varchar(255) NOT NULL,
  `part_number` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `issued_quantity` int(11) DEFAULT 0,
  `reorder_level` int(11) NOT NULL,
  `bin_number` varchar(50) DEFAULT NULL,
  `supplier` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spare_parts`
--

INSERT INTO `spare_parts` (`id`, `part_name`, `part_number`, `quantity`, `issued_quantity`, `reorder_level`, `bin_number`, `supplier`, `photo`, `created_at`) VALUES
(18, 'cat', '4321', 6, 3, 1, 'B2', NULL, NULL, '2025-03-14 20:02:45'),
(19, 'dog', '78063', 2, 1, 1, 'B4', NULL, NULL, '2025-03-14 21:18:18'),
(20, 'cap', '97464', 10, 0, 1, 'B9', NULL, NULL, '2025-03-14 21:30:58'),
(21, 'nut', '143637', 100, 0, 10, 'B13', NULL, NULL, '2025-03-14 21:42:43'),
(22, 'bolt', '97474', 20, 0, 1, 'B43', NULL, NULL, '2025-03-17 21:18:06');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `contact_info`, `created_at`) VALUES
(1, 'Knitter Supply', 'contact@suppliera.com', '2025-03-14 01:14:05'),
(2, 'Supplier B', 'contact@supplierb.com', '2025-03-14 01:14:05'),
(3, 'Amtex', 'sunimaliperera@yahoo.com', '2025-03-17 19:29:24'),
(4, 'Henderson Machinery', '', '2025-03-18 23:57:52');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `part_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_type` enum('issue','add') NOT NULL,
  `quantity` int(11) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-03-14 01:13:40'),
(2, 'staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', '2025-03-14 01:13:40'),
(3, 'sunimal', '$2y$10$rAImWtVecHENVWUMJ3CVUupwuOuM5ZRWMXyfZF69lDUC5S.DhMFBy', 'staff', '2025-03-14 01:17:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `part_prices`
--
ALTER TABLE `part_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `part_id` (`part_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `spare_parts`
--
ALTER TABLE `spare_parts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `part_name` (`part_name`),
  ADD UNIQUE KEY `part_number` (`part_number`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `part_id` (`part_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `part_prices`
--
ALTER TABLE `part_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `spare_parts`
--
ALTER TABLE `spare_parts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `part_prices`
--
ALTER TABLE `part_prices`
  ADD CONSTRAINT `part_prices_ibfk_1` FOREIGN KEY (`part_id`) REFERENCES `spare_parts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `part_prices_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`part_id`) REFERENCES `spare_parts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
