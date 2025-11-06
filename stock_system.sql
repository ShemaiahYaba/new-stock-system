-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2025 at 08:01 AM
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
-- Database: `stock_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `coils`
--

CREATE TABLE `coils` (
  `id` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(50) NOT NULL,
  `net_weight` decimal(10,2) NOT NULL,
  `category` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'available',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coils`
--

INSERT INTO `coils` (`id`, `code`, `name`, `color`, `net_weight`, `category`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 'j169', 'Kzinc coil', 'red', 1214.00, 'kzinc', 'available', 2, '2025-11-05 17:34:42', '2025-11-06 06:42:44', NULL),
(27, 'C157', 'Aluminum Coil', 'custom', 2035.00, 'aluminum', 'available', 2, '2025-10-30 10:56:08', '2025-11-06 06:57:44', NULL),
(28, 'C175', 'Aluminum Coil', 'orange', 2035.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:56:10', NULL),
(29, 'C176', 'Aluminum Coil', 'custom', 1972.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(30, 'C180', 'Aluminum Coil', 'silver', 1993.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(31, 'C184', 'Aluminum Coil', 'silver', 1666.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(32, 'C188', 'Aluminum Coil', 'custom', 2042.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(33, 'C191', 'Aluminum Coil', 'silver', 2040.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(34, 'C192', 'Aluminum Coil', 'silver', 2032.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(35, 'C177', 'Aluminum Coil', 'custom', 1972.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-10-31 12:34:14', NULL),
(36, 'C182', 'Aluminum Coil', 'blue', 2015.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-10-31 12:34:14', NULL),
(37, 'C187', 'Aluminum Coil', 'custom', 2046.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-10-31 12:34:14', NULL),
(38, 'C194', 'Aluminum Coil', 'silver', 1999.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-10-31 12:34:14', NULL),
(39, 'C178', 'Aluminum Coil', 'custom', 1959.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(40, 'C179', 'Aluminum Coil', 'silver', 2084.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(41, 'C181', 'Aluminum Coil', 'silver', 1348.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(42, 'C183', 'Aluminum Coil', 'blue', 2018.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(43, 'C185', 'Aluminum Coil', 'custom', 1648.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(44, 'C186', 'Aluminum Coil', 'custom', 1648.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(45, 'C189', 'Aluminum Coil', 'custom', 2020.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(46, 'C190', 'Aluminum Coil', 'silver', 2024.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(47, 'C193', 'Aluminum Coil', 'silver', 2032.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(48, 'C195', 'Aluminum Coil', 'silver', 1968.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) NOT NULL,
  `address` text DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `company`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Mr Lawal', 'customer1@example.com', '09039988198', 'NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA', 'HEXA', 3, '2025-11-05 10:34:04', NULL, NULL),
(2, 'Mr Lawal Benson', 'customer2@example.com', '09039988197', 'NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA', 'HEXA', 3, '2025-11-05 10:34:39', '2025-11-05 10:56:34', '2025-11-05 10:56:34');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `coil_id` int(11) NOT NULL,
  `stock_entry_id` int(11) DEFAULT NULL,
  `sale_type` varchar(50) NOT NULL,
  `meters` decimal(10,2) NOT NULL,
  `price_per_meter` decimal(10,2) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `customer_id`, `coil_id`, `stock_entry_id`, `sale_type`, `meters`, `price_per_meter`, `total_amount`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(8, 1, 4, 10, 'retail', 203.00, 1000.00, 203000.00, 'completed', 2, '2025-11-06 06:48:13', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_entries`
--

CREATE TABLE `stock_entries` (
  `id` int(11) NOT NULL,
  `coil_id` int(11) NOT NULL,
  `meters` decimal(10,2) NOT NULL,
  `meters_remaining` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'available',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stock entries with individual status tracking (available/factory_use)';

--
-- Dumping data for table `stock_entries`
--

INSERT INTO `stock_entries` (`id`, `coil_id`, `meters`, `meters_remaining`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(10, 4, 15203.00, 15000.00, 'factory_use', 2, '2025-11-06 06:47:37', '2025-11-06 06:48:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_ledger`
--

CREATE TABLE `stock_ledger` (
  `id` int(11) NOT NULL,
  `coil_id` int(11) NOT NULL,
  `stock_entry_id` int(11) DEFAULT NULL,
  `transaction_type` enum('inflow','outflow') NOT NULL,
  `description` varchar(255) NOT NULL,
  `inflow_meters` decimal(10,2) DEFAULT 0.00,
  `outflow_meters` decimal(10,2) DEFAULT 0.00,
  `balance_meters` decimal(10,2) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL COMMENT 'sale, wastage, adjustment, stock_entry',
  `reference_id` int(11) DEFAULT NULL COMMENT 'ID of the referenced record',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracks all stock movements with running balance for factory-use coils';

--
-- Dumping data for table `stock_ledger`
--

INSERT INTO `stock_ledger` (`id`, `coil_id`, `stock_entry_id`, `transaction_type`, `description`, `inflow_meters`, `outflow_meters`, `balance_meters`, `reference_type`, `reference_id`, `created_by`, `created_at`) VALUES
(13, 4, 10, 'inflow', 'Stock moved to factory use - Entry #10 (15203.00m available)', 15203.00, 0.00, 15203.00, 'stock_entry', 10, 2, '2025-11-06 06:47:41'),
(14, 4, 10, 'outflow', 'Retail sale to Mr Lawal (203m @ ₦1000/m)', 0.00, 203.00, 15000.00, 'sale', 8, 2, '2025-11-06 06:48:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'viewer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'admin@example.com', '$2y$10$RBop9EOLBg.Vo9AKPBp.xOnx/18TGybfpkmU.//PgdAwLFSXr3GZ6', 'SHEMAIAH WAMBEBE YABA-SHIAKA', 'super_admin', '2025-11-05 09:37:50', NULL, NULL),
(3, 'admin2@example.com', '$2y$10$CeQL8JveXCejBemhfk2lS.L.0b8e.2qDn9KhTuGfpoQEXAYtJC5iS', 'Mr Obumek', 'super_admin', '2025-11-05 09:48:31', '2025-11-05 09:58:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `module` varchar(100) NOT NULL,
  `actions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`actions`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `module`, `actions`, `created_at`, `updated_at`) VALUES
(10, 3, 'user_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 09:48:31', NULL),
(11, 3, 'customer_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 09:48:31', NULL),
(12, 3, 'stock_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 09:48:31', NULL),
(13, 3, 'sales_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 09:48:31', NULL),
(14, 3, 'reports', '[\"view\"]', '2025-11-05 09:48:31', NULL),
(15, 3, 'dashboard', '[\"view\"]', '2025-11-05 09:48:31', NULL),
(16, 2, 'user_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 09:59:21', NULL),
(17, 2, 'customer_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 09:59:21', NULL),
(18, 2, 'stock_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 09:59:21', NULL),
(19, 2, 'sales_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 09:59:21', NULL),
(20, 2, 'reports', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 09:59:21', NULL),
(21, 2, 'dashboard', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 09:59:21', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coils`
--
ALTER TABLE `coils`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_deleted` (`deleted_at`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_deleted` (`deleted_at`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_entry_id` (`stock_entry_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_coil_id` (`coil_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_deleted` (`deleted_at`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `stock_entries`
--
ALTER TABLE `stock_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_coil_id` (`coil_id`),
  ADD KEY `idx_deleted` (`deleted_at`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `stock_ledger`
--
ALTER TABLE `stock_ledger`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_entry_id` (`stock_entry_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_coil_id` (`coil_id`),
  ADD KEY `idx_transaction_type` (`transaction_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_deleted` (`deleted_at`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_module` (`user_id`,`module`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `coils`
--
ALTER TABLE `coils`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stock_entries`
--
ALTER TABLE `stock_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `stock_ledger`
--
ALTER TABLE `stock_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `coils`
--
ALTER TABLE `coils`
  ADD CONSTRAINT `coils_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`coil_id`) REFERENCES `coils` (`id`),
  ADD CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`stock_entry_id`) REFERENCES `stock_entries` (`id`),
  ADD CONSTRAINT `sales_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `stock_entries`
--
ALTER TABLE `stock_entries`
  ADD CONSTRAINT `stock_entries_ibfk_1` FOREIGN KEY (`coil_id`) REFERENCES `coils` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_entries_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `stock_ledger`
--
ALTER TABLE `stock_ledger`
  ADD CONSTRAINT `stock_ledger_ibfk_1` FOREIGN KEY (`coil_id`) REFERENCES `coils` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_ledger_ibfk_2` FOREIGN KEY (`stock_entry_id`) REFERENCES `stock_entries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_ledger_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
