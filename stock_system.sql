-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2025 at 01:00 PM
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
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) NOT NULL,
  `action` enum('create','update','delete','restore') NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `changed_by` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(48, 'C195', 'Aluminum Coil', 'silver', 1968.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-06 06:57:44', NULL),
(49, 'B197', 'Premium steel coil', 'blue', 1289.00, 'alusteel', 'available', 2, '2025-11-06 20:47:08', '2025-11-07 11:56:10', NULL);

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
(2, 'Mr Lawal Benson', 'customer2@example.com', '09039988197', 'NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA', 'HEXA', 3, '2025-11-05 10:34:39', '2025-11-05 10:56:34', '2025-11-05 10:56:34'),
(3, 'Mr. Danjuma', 'danjuma@customer.com', '0909880012', NULL, 'ABC INDUSTRIES', 2, '2025-11-06 20:45:57', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `production_id` int(11) DEFAULT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `invoice_shape` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Complete invoice data structure' CHECK (json_valid(`invoice_shape`)),
  `total` decimal(12,2) NOT NULL,
  `tax` decimal(10,2) DEFAULT 0.00,
  `shipping` decimal(10,2) DEFAULT 0.00,
  `paid_amount` decimal(12,2) DEFAULT 0.00,
  `status` enum('unpaid','partial','paid','cancelled') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `immutable_hash` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production`
--

CREATE TABLE `production` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `production_paper` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Stores complete production details' CHECK (json_valid(`production_paper`)),
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `immutable_hash` varchar(64) NOT NULL COMMENT 'SHA256 hash for immutability verification'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `amount_paid` decimal(12,2) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'cash',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(12, 49, 10000.00, 10000.00, 'available', 2, '2025-11-07 11:57:09', NULL, NULL);

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

-- --------------------------------------------------------

--
-- Table structure for table `supply_delivery`
--

CREATE TABLE `supply_delivery` (
  `id` int(11) NOT NULL,
  `production_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `status` enum('pending','supplied','returned') DEFAULT 'pending',
  `delivered_at` timestamp NULL DEFAULT NULL,
  `return_requested_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(3, 'admin2@example.com', '$2y$10$CeQL8JveXCejBemhfk2lS.L.0b8e.2qDn9KhTuGfpoQEXAYtJC5iS', 'Mr Obumek', 'super_admin', '2025-11-05 09:48:31', '2025-11-05 09:58:46', NULL),
(4, 'johnernest@example.com', '$2y$10$0CU1mMEehn9i7fbgRvQU/eU0OsV4VpwcmVfH85.v7OFmz1hbmdCT6', 'John Ernest', 'accountant', '2025-11-06 19:49:26', '2025-11-06 20:19:48', NULL);

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
(21, 2, 'dashboard', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 09:59:21', NULL),
(29, 4, 'stock_management', '[\"view\",\"create\"]', '2025-11-06 20:44:39', NULL),
(30, 4, 'sales_management', '[\"view\",\"create\"]', '2025-11-06 20:44:39', NULL),
(31, 4, 'reports', '[\"view\"]', '2025-11-06 20:44:39', NULL),
(32, 4, 'dashboard', '[\"view\"]', '2025-11-06 20:44:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` text DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `location`, `contact`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Head Office', 'Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja', '+2348065336645', 1, '2025-11-07 07:51:17', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `changed_by` (`changed_by`),
  ADD KEY `idx_table_record` (`table_name`,`record_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

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
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_invoice_number` (`invoice_number`),
  ADD UNIQUE KEY `unique_immutable_hash` (`immutable_hash`),
  ADD KEY `idx_sale_id` (`sale_id`),
  ADD KEY `idx_production_id` (`production_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_invoice_number` (`invoice_number`);

--
-- Indexes for table `production`
--
ALTER TABLE `production`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_immutable_hash` (`immutable_hash`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_sale_id` (`sale_id`),
  ADD KEY `idx_warehouse_id` (`warehouse_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_immutable_hash` (`immutable_hash`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_invoice_id` (`invoice_id`),
  ADD KEY `idx_created_at` (`created_at`);

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
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_warehouse_id` (`customer_id`);

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
-- Indexes for table `supply_delivery`
--
ALTER TABLE `supply_delivery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_production_id` (`production_id`),
  ADD KEY `idx_warehouse_id` (`warehouse_id`),
  ADD KEY `idx_status` (`status`),
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
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_deleted` (`deleted_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coils`
--
ALTER TABLE `coils`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production`
--
ALTER TABLE `production`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stock_entries`
--
ALTER TABLE `stock_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `stock_ledger`
--
ALTER TABLE `stock_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `supply_delivery`
--
ALTER TABLE `supply_delivery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

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
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`production_id`) REFERENCES `production` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `production`
--
ALTER TABLE `production`
  ADD CONSTRAINT `production_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `production_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`),
  ADD CONSTRAINT `production_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  ADD CONSTRAINT `receipts_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

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
-- Constraints for table `supply_delivery`
--
ALTER TABLE `supply_delivery`
  ADD CONSTRAINT `supply_delivery_ibfk_1` FOREIGN KEY (`production_id`) REFERENCES `production` (`id`),
  ADD CONSTRAINT `supply_delivery_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
