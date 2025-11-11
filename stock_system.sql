-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2025 at 12:29 AM
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
  `color_id` int(11) DEFAULT NULL,
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

INSERT INTO `coils` (`id`, `code`, `name`, `color`, `color_id`, `net_weight`, `category`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 'j169', 'Kzinc coil', 'IBeige', 9, 1214.00, 'kzinc', 'available', 2, '2025-11-05 17:34:42', '2025-11-11 21:28:35', NULL),
(27, 'C157', 'Aluminum Coil', 'T/Black', 12, 2035.00, 'aluminum', 'available', 2, '2025-10-30 10:56:08', '2025-11-11 21:48:33', NULL),
(28, 'C175', 'Aluminum Coil', 'P/Green', 10, 2035.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(29, 'C176', 'Aluminum Coil', 'T/Black', 12, 1972.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(30, 'C180', 'Aluminum Coil', 'I/White', 16, 1993.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(31, 'C184', 'Aluminum Coil', 'I/White', 16, 1666.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(32, 'C188', 'Aluminum Coil', 'T/Black', 12, 2042.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(33, 'C191', 'Aluminum Coil', 'I/White', 16, 2040.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(34, 'C192', 'Aluminum Coil', 'I/White', 16, 2032.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(35, 'C177', 'Aluminum Coil', 'T/Black', 12, 1972.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(36, 'C182', 'Aluminum Coil', 'S/Blue', 11, 2015.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(37, 'C187', 'Aluminum Coil', 'T/Black', 12, 2046.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(38, 'C194', 'Aluminum Coil', 'I/White', 16, 1999.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(39, 'C178', 'Aluminum Coil', 'T/Black', 12, 1959.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(40, 'C179', 'Aluminum Coil', 'I/White', 16, 2084.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(41, 'C181', 'Aluminum Coil', 'I/White', 16, 1348.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(42, 'C183', 'Aluminum Coil', 'S/Blue', 11, 2018.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(43, 'C185', 'Aluminum Coil', 'T/Black', 12, 1648.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(44, 'C186', 'Aluminum Coil', 'T/Black', 12, 1648.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(45, 'C189', 'Aluminum Coil', 'T/Black', 12, 2020.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(46, 'C190', 'Aluminum Coil', 'I/White', 16, 2024.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(47, 'C193', 'Aluminum Coil', 'I/White', 16, 2032.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(48, 'C195', 'Aluminum Coil', 'I/White', 16, 1968.00, 'aluminum', 'available', 2, '2025-10-31 12:34:14', '2025-11-11 21:48:33', NULL),
(49, 'B197', 'Premium steel coil', 'S/Blue', 11, 1289.00, 'alusteel', 'available', 2, '2025-11-06 20:47:08', '2025-11-11 21:48:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL COMMENT 'Short code like IBeige, PGreen',
  `name` varchar(100) NOT NULL COMMENT 'Display name like I/Beige, P/Green',
  `hex_code` varchar(7) DEFAULT NULL COMMENT 'Optional hex color code for UI display',
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`id`, `code`, `name`, `hex_code`, `is_active`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(9, 'IBeige', 'I/Beige', '#F5F5DC', 1, 2, '2025-11-11 21:27:37', '2025-11-11 21:50:09', NULL),
(10, 'PGreen', 'P/Green', '#008000', 1, 2, '2025-11-11 21:27:37', '2025-11-11 21:50:09', NULL),
(11, 'SBlue', 'S/Blue', '#0000FF', 1, 2, '2025-11-11 21:27:37', '2025-11-11 21:50:09', NULL),
(12, 'TBlack', 'T/Black', '#000000', 1, 2, '2025-11-11 21:27:37', '2025-11-11 21:50:09', NULL),
(13, 'TCRed', 'TC/Red', '#FF0000', 1, 2, '2025-11-11 21:27:37', '2025-11-11 21:50:09', NULL),
(14, 'GBeige', 'G/Beige', '#E6BE8A', 1, 2, '2025-11-11 21:27:37', '2025-11-11 21:50:09', NULL),
(15, 'BGreen', 'B/Green', '#006400', 1, 2, '2025-11-11 21:27:37', '2025-11-11 21:50:09', NULL),
(16, 'IWhite', 'I/White', '#FFFFFF', 1, 2, '2025-11-11 21:27:37', '2025-11-11 21:50:09', NULL),
(17, 'STest', 'S/Test', '#FFF700', 0, 2, '2025-11-11 21:51:24', '2025-11-11 22:10:40', '2025-11-11 22:10:40');

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
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount before tax and discount',
  `tax_type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
  `tax_value` decimal(10,2) DEFAULT 0.00 COMMENT 'Tax amount or percentage',
  `tax_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Calculated tax amount',
  `discount_type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
  `discount_value` decimal(10,2) DEFAULT 0.00 COMMENT 'Discount amount or percentage',
  `discount_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Calculated discount amount',
  `total` decimal(12,2) NOT NULL COMMENT 'Final amount after all calculations',
  `tax` decimal(10,2) DEFAULT 0.00,
  `other_charges` decimal(10,2) DEFAULT 0.00,
  `paid_amount` decimal(12,2) DEFAULT 0.00 COMMENT 'Amount paid by customer',
  `shipping` decimal(10,2) DEFAULT 0.00 COMMENT 'Shipping charges',
  `status` enum('unpaid','partial','paid','cancelled') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `immutable_hash` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `sale_id`, `production_id`, `invoice_number`, `invoice_shape`, `subtotal`, `tax_type`, `tax_value`, `tax_amount`, `discount_type`, `discount_value`, `discount_amount`, `total`, `tax`, `other_charges`, `paid_amount`, `shipping`, `status`, `created_at`, `updated_at`, `immutable_hash`) VALUES
(23, 37, NULL, 'INV-2025-000001', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Mr Lawal\\\",\\\"company\\\":\\\"HEXA\\\",\\\"email\\\":\\\"customer1@example.com\\\",\\\"phone\\\":\\\"09039988198\\\",\\\"address\\\":\\\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-11-09 02:51:16\\\",\\\"ref\\\":\\\"#SO-20251109-000037\\\",\\\"sale_id\\\":\\\"37\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"B197 - Premium steel coil\\\",\\\"quantity\\\":1000,\\\"unit_price\\\":1000,\\\"total\\\":1000000}],\\\"subtotal\\\":1000000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":1000000,\\\"paid\\\":0,\\\"due\\\":1000000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 1000000.00, 0.00, 0.00, 0.00, 0.00, 'unpaid', '2025-11-09 01:51:16', NULL, '437c569b3bfb48317f9eba7025cb45b5cbd6e347979f017d15fea48f37ac53bc'),
(24, 38, NULL, 'INV-2025-000002', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Mr Lawal\\\",\\\"company\\\":\\\"HEXA\\\",\\\"email\\\":\\\"customer1@example.com\\\",\\\"phone\\\":\\\"09039988198\\\",\\\"address\\\":\\\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-11-09 02:57:09\\\",\\\"ref\\\":\\\"#SO-20251109-000038\\\",\\\"sale_id\\\":\\\"38\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"C175 - Aluminum Coil\\\",\\\"quantity\\\":1298,\\\"unit_price\\\":999.99,\\\"total\\\":1297987.02}],\\\"subtotal\\\":1297987.02,\\\"order_tax\\\":12979.8702,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":1310966.8902,\\\"paid\\\":0,\\\"due\\\":1310966.8902,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 1310966.89, 12979.87, 0.00, 0.00, 0.00, 'unpaid', '2025-11-09 01:57:09', NULL, '8a121f27ab3ad8a471d31045fbf38d91bfe984a1f2f09ad8511a01dba9e9dc16'),
(25, 39, NULL, 'INV-2025-000003', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Mr Lawal\\\",\\\"company\\\":\\\"HEXA\\\",\\\"email\\\":\\\"customer1@example.com\\\",\\\"phone\\\":\\\"09039988198\\\",\\\"address\\\":\\\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-11-09 03:02:34\\\",\\\"ref\\\":\\\"#SO-20251109-000039\\\",\\\"sale_id\\\":\\\"39\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"C184 - Aluminum Coil\\\",\\\"quantity\\\":182,\\\"unit_price\\\":100,\\\"total\\\":18200}],\\\"subtotal\\\":18200,\\\"order_tax\\\":910,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":19110,\\\"paid\\\":0,\\\"due\\\":19110,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 19110.00, 910.00, 0.00, 0.00, 0.00, 'unpaid', '2025-11-09 02:02:34', NULL, '5b85d62c9803c0fb0e6b1b5dceb19993cb0f98dbe4c325920102ef7ef611db81'),
(26, 40, NULL, 'INV-2025-000004', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Mr Lawal\\\",\\\"company\\\":\\\"HEXA\\\",\\\"email\\\":\\\"customer1@example.com\\\",\\\"phone\\\":\\\"09039988198\\\",\\\"address\\\":\\\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-11-09 03:37:19\\\",\\\"ref\\\":\\\"#SO-20251109-000040\\\",\\\"sale_id\\\":\\\"40\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"C184 - Aluminum Coil\\\",\\\"quantity\\\":1000,\\\"qty_text\\\":\\\"1,000.00 meters\\\",\\\"unit_price\\\":1000,\\\"subtotal\\\":1000000}],\\\"subtotal\\\":1000000,\\\"order_tax\\\":75000,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":1075000,\\\"paid\\\":0,\\\"due\\\":1075000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 1075000.00, 75000.00, 0.00, 0.00, 0.00, 'unpaid', '2025-11-09 02:37:19', NULL, '32e61c7596c3a8adee89ddf3233dc7e16050c953272a42b9a59de7e6df7e3a2e'),
(27, 41, 14, 'INV-2025-000005', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"1\",\"name\":\"Mr Lawal - 09039988198\",\"phone\":\"09039988198\",\"company\":\"HEXA\",\"address\":\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\"},\"meta\":{\"date\":\"2025-11-09 04:29:03\",\"ref\":\"#SO-20251109-000041\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"B197\",\"description\":\"Premium steel coil - mainsheet\",\"unit_price\":1000,\"quantity\":100,\"subtotal\":100000}],\"order_tax\":1000,\"discount\":1000,\"shipping\":1000.01,\"grand_total\":101000.01,\"paid\":0,\"due\":101000.01,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 101000.01, 1000.00, 0.00, 0.00, 1000.01, 'unpaid', '2025-11-09 03:29:03', NULL, 'aa67d7f57addc2b5e01931750c86a6c43275e9e1b0f0c5ff33836d5a69c0d4cb'),
(28, 42, 15, 'INV-2025-000006', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"3\",\"name\":\"Mr. Danjuma - 0909880012\",\"phone\":\"0909880012\",\"company\":\"ABC INDUSTRIES\",\"address\":\"\"},\"meta\":{\"date\":\"2025-11-09 19:25:35\",\"ref\":\"#SO-20251109-000042\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"j169\",\"description\":\"Kzinc coil - scraps\",\"unit_price\":2500,\"quantity\":10,\"subtotal\":25000},{\"product_code\":\"j169\",\"description\":\"Kzinc coil - pieces\",\"unit_price\":4500,\"quantity\":10,\"subtotal\":45000},{\"product_code\":\"j169\",\"description\":\"Kzinc coil - bundles\",\"unit_price\":64000,\"quantity\":6,\"subtotal\":384000}],\"order_tax\":45400,\"discount\":45400,\"shipping\":100000,\"grand_total\":554000,\"paid\":0,\"due\":554000,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 554000.00, 45400.00, 0.00, 0.00, 100000.00, 'unpaid', '2025-11-09 18:25:35', NULL, 'c739c7a641bac9bbb37a49e78f8493a36b4dc507e1b183d22dc8e99315624115'),
(29, 43, 16, 'INV-2025-000007', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"1\",\"name\":\"Mr Lawal - 09039988198\",\"phone\":\"09039988198\",\"company\":\"HEXA\",\"address\":\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\"},\"meta\":{\"date\":\"2025-11-10 05:50:10\",\"ref\":\"#SO-20251110-000043\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"j169\",\"description\":\"Kzinc coil - bundles\",\"unit_price\":60000,\"quantity\":9,\"subtotal\":540000}],\"order_tax\":54000,\"discount\":4000,\"shipping\":10000,\"grand_total\":600000,\"paid\":0,\"due\":600000,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 600000.00, 54000.00, 0.00, 100000.00, 10000.00, 'partial', '2025-11-10 04:50:10', '2025-11-10 05:12:31', '0a4f5d6488ba8d4f62b266af98c5837ed248c68ee21d4a0e405d5e8ab4b85abc'),
(30, 44, 17, 'INV-2025-000008', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"1\",\"name\":\"Mr Lawal - 09039988198\",\"phone\":\"09039988198\",\"company\":\"HEXA\",\"address\":\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\"},\"meta\":{\"date\":\"2025-11-10 06:29:20\",\"ref\":\"#SO-20251110-000044\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"B197\",\"description\":\"Premium steel coil - flatsheet\",\"unit_price\":5000,\"quantity\":200,\"subtotal\":1000000},{\"product_code\":\"B197\",\"description\":\"Premium steel coil - mainsheet\",\"unit_price\":5000,\"quantity\":180,\"subtotal\":900000},{\"product_code\":\"B197\",\"description\":\"Premium steel coil - cladding\",\"unit_price\":5000,\"quantity\":27,\"subtotal\":135000}],\"order_tax\":203500,\"discount\":3500,\"shipping\":0,\"grand_total\":2235000,\"paid\":0,\"due\":2235000,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 2235000.00, 203500.00, 0.00, 1235000.00, 0.00, 'partial', '2025-11-10 05:29:20', '2025-11-11 18:42:03', '3cdf84d346b76e0743edea54a251724f6812d649508ebac41c4953ac5f6f1aa1'),
(31, 45, 18, 'INV-2025-000009', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"1\",\"name\":\"Mr Lawal - 09039988198\",\"phone\":\"09039988198\",\"company\":\"HEXA\",\"address\":\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\"},\"meta\":{\"date\":\"2025-11-10 06:32:13\",\"ref\":\"#SO-20251110-000045\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"j169\",\"description\":\"Kzinc coil - bundles\",\"unit_price\":63000,\"quantity\":2,\"subtotal\":126000}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":126000,\"paid\":0,\"due\":126000,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 126000.00, 0.00, 0.00, 126000.00, 0.00, 'paid', '2025-11-10 05:32:13', '2025-11-10 05:32:30', 'f38d5094f482315778f1f41a5fd4e914ff6b94ebd9d3c7311b27ac2308589c32'),
(32, 46, 19, 'INV-2025-000010', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"1\",\"name\":\"Mr Lawal - 09039988198\",\"phone\":\"09039988198\",\"company\":\"HEXA\",\"address\":\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\"},\"meta\":{\"date\":\"2025-11-11 23:26:27\",\"ref\":\"#SO-20251111-000046\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"B197\",\"description\":\"Premium steel coil - mainsheet\",\"unit_price\":1000,\"quantity\":93,\"subtotal\":93000}],\"order_tax\":9300,\"discount\":300,\"shipping\":8000,\"grand_total\":110000,\"paid\":0,\"due\":110000,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 110000.00, 9300.00, 0.00, 0.00, 8000.00, 'unpaid', '2025-11-11 22:26:27', NULL, 'e71ba34ed4171be575ab3a13eee6ed9e85ba76e955e7d155045278736fb630fb'),
(33, 47, 20, 'INV-2025-000011', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"1\",\"name\":\"Mr Lawal - 09039988198\",\"phone\":\"09039988198\",\"company\":\"HEXA\",\"address\":\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\"},\"meta\":{\"date\":\"2025-11-11 23:29:00\",\"ref\":\"#SO-20251111-000047\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"B197\",\"description\":\"Premium steel coil - mainsheet\",\"unit_price\":10000,\"quantity\":100,\"subtotal\":1000000}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":1000000,\"paid\":0,\"due\":1000000,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 1000000.00, 0.00, 0.00, 0.00, 0.00, 'unpaid', '2025-11-11 22:29:00', NULL, '74f348358ad884e9c11c565ddeb35f1325846efd666f6846a123faccbc68f1e5');

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

--
-- Dumping data for table `production`
--

INSERT INTO `production` (`id`, `sale_id`, `warehouse_id`, `production_paper`, `status`, `created_by`, `created_at`, `updated_at`, `immutable_hash`) VALUES
(14, 41, 1, '{\"production_reference\":\"PR-20251109-0041\",\"sale_id\":\"41\",\"warehouse_id\":\"1\",\"customer\":{\"id\":\"1\",\"name\":\"Mr Lawal - 09039988198\",\"phone\":\"09039988198\",\"company\":\"HEXA\",\"address\":\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\"},\"warehouse\":{\"id\":\"1\",\"name\":\"Head Office                                                                                - Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"code\":\"\"},\"coil_id\":\"49\",\"coil\":{\"id\":\"49\",\"code\":\"B197\",\"name\":\"Premium steel coil\",\"category\":\"alusteel\",\"color\":\"S\\/Blue\",\"weight\":\"1289.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":1,\"sheet_meter\":100,\"meters\":100,\"unit_price\":1000,\"row_subtotal\":100000}],\"total_meters\":100,\"total_amount\":100000,\"created_at\":\"2025-11-09 04:29:03\"}', 'completed', 2, '2025-11-09 03:29:03', NULL, 'de8231992522d31acbce1ab6b038874011d07af2c27acac44fddede8a103cb82'),
(15, 42, 3, '{\"production_reference\":\"PR-20251109-0042\",\"sale_id\":\"42\",\"warehouse_id\":\"3\",\"customer\":{\"id\":\"3\",\"name\":\"Mr. Danjuma - 0909880012\",\"phone\":\"0909880012\",\"company\":\"ABC INDUSTRIES\",\"address\":\"\"},\"warehouse\":{\"id\":\"3\",\"name\":\"Branch Office                                                                                - BRANCH\",\"code\":\"\"},\"coil_id\":\"4\",\"coil\":{\"id\":\"4\",\"code\":\"j169\",\"name\":\"Kzinc coil\",\"category\":\"kzinc\",\"color\":\"TC\\/Red\",\"weight\":\"1214.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"scraps\",\"label\":\"Scraps\",\"sheet_qty\":10,\"sheet_meter\":0,\"meters\":0,\"unit_price\":2500,\"row_subtotal\":25000},{\"property_id\":\"pieces\",\"label\":\"Pieces\",\"sheet_qty\":10,\"sheet_meter\":0,\"meters\":0,\"unit_price\":4500,\"row_subtotal\":45000},{\"property_id\":\"bundles\",\"label\":\"Bundles\",\"sheet_qty\":6,\"sheet_meter\":0,\"meters\":0,\"unit_price\":64000,\"row_subtotal\":384000}],\"total_meters\":0,\"total_amount\":454000,\"created_at\":\"2025-11-09 19:25:35\"}', 'completed', 2, '2025-11-09 18:25:35', NULL, 'a69fe6db03993c0ecbddb14fe04c621e2c6db4835f07d84249910097a8e80662'),
(16, 43, 1, '{\"production_reference\":\"PR-20251110-0043\",\"sale_id\":\"43\",\"warehouse_id\":\"1\",\"customer\":{\"id\":\"1\",\"name\":\"Mr Lawal - 09039988198\",\"phone\":\"09039988198\",\"company\":\"HEXA\",\"address\":\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\"},\"warehouse\":{\"id\":\"1\",\"name\":\"Head Office                                                                                - Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"code\":\"\"},\"coil_id\":\"4\",\"coil\":{\"id\":\"4\",\"code\":\"j169\",\"name\":\"Kzinc coil\",\"category\":\"kzinc\",\"color\":\"IBeige\",\"weight\":\"1214.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"bundles\",\"label\":\"Bundles\",\"sheet_qty\":9,\"sheet_meter\":0,\"meters\":0,\"quantity\":9,\"pieces\":135,\"unit_price\":60000,\"row_subtotal\":540000}],\"total_meters\":0,\"total_amount\":540000,\"created_at\":\"2025-11-10 05:50:10\"}', 'completed', 2, '2025-11-10 04:50:10', NULL, '993906d5e226cdb1a939a6a40e18a11ffc0a676fd715e52a0bda0f1734b244e9'),
(17, 44, 1, '{\"production_reference\":\"PR-20251110-0044\",\"sale_id\":\"44\",\"warehouse_id\":\"1\",\"customer\":{\"id\":\"1\",\"name\":\"Mr Lawal - 09039988198\",\"phone\":\"09039988198\",\"company\":\"HEXA\",\"address\":\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\"},\"warehouse\":{\"id\":\"1\",\"name\":\"Head Office                                                                                - Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"code\":\"\"},\"coil_id\":\"49\",\"coil\":{\"id\":\"49\",\"code\":\"B197\",\"name\":\"Premium steel coil\",\"category\":\"alusteel\",\"color\":\"S\\/Blue\",\"weight\":\"1289.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":20,\"sheet_meter\":10,\"meters\":200,\"quantity\":0,\"pieces\":0,\"unit_price\":5000,\"row_subtotal\":1000000},{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":24,\"sheet_meter\":7.5,\"meters\":180,\"quantity\":0,\"pieces\":0,\"unit_price\":5000,\"row_subtotal\":900000},{\"property_id\":\"cladding\",\"label\":\"Cladding\",\"sheet_qty\":54,\"sheet_meter\":0.5,\"meters\":27,\"quantity\":0,\"pieces\":0,\"unit_price\":5000,\"row_subtotal\":135000}],\"total_meters\":407,\"total_amount\":2035000,\"created_at\":\"2025-11-10 06:29:20\"}', 'completed', 2, '2025-11-10 05:29:20', NULL, '69953348608ae59e11cc3070bdb1237cba17bdc8dd2e794e24a6d0b1417ec45e'),
(18, 45, 1, '{\"production_reference\":\"PR-20251110-0045\",\"sale_id\":\"45\",\"warehouse_id\":\"1\",\"customer\":{\"id\":\"1\",\"name\":\"Mr Lawal - 09039988198\",\"phone\":\"09039988198\",\"company\":\"HEXA\",\"address\":\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\"},\"warehouse\":{\"id\":\"1\",\"name\":\"Head Office                                                                                - Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"code\":\"\"},\"coil_id\":\"4\",\"coil\":{\"id\":\"4\",\"code\":\"j169\",\"name\":\"Kzinc coil\",\"category\":\"kzinc\",\"color\":\"IBeige\",\"weight\":\"1214.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"bundles\",\"label\":\"Bundles\",\"sheet_qty\":2,\"sheet_meter\":0,\"meters\":0,\"quantity\":2,\"pieces\":30,\"unit_price\":63000,\"row_subtotal\":126000}],\"total_meters\":0,\"total_amount\":126000,\"created_at\":\"2025-11-10 06:32:13\"}', 'completed', 2, '2025-11-10 05:32:13', NULL, '6867a4aa5fa4f8c72c9400255fe1b4ece7ff9f2767706dbb97398065d53672b6'),
(19, 46, 1, '{\"production_reference\":\"PR-20251111-0046\",\"sale_id\":\"46\",\"warehouse_id\":\"1\",\"customer\":{\"id\":\"1\",\"name\":\"Mr Lawal - 09039988198\",\"phone\":\"09039988198\",\"company\":\"HEXA\",\"address\":\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\"},\"warehouse\":{\"id\":\"1\",\"name\":\"Head Office                                                                                - Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"code\":\"\"},\"coil_id\":\"49\",\"coil\":{\"id\":\"49\",\"code\":\"B197\",\"name\":\"Premium steel coil\",\"category\":\"alusteel\",\"color\":\"\",\"weight\":\"1289.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":1,\"sheet_meter\":93,\"meters\":93,\"quantity\":0,\"pieces\":0,\"unit_price\":1000,\"row_subtotal\":93000}],\"total_meters\":93,\"total_amount\":93000,\"created_at\":\"2025-11-11 23:26:27\"}', 'completed', 2, '2025-11-11 22:26:27', NULL, 'f2009aec5ffd0d9cf89545d24ec4eac61aa046c0fd656ab63ffe884263e90813'),
(20, 47, 1, '{\"production_reference\":\"PR-20251111-0047\",\"sale_id\":\"47\",\"warehouse_id\":\"1\",\"customer\":{\"id\":\"1\",\"name\":\"Mr Lawal - 09039988198\",\"phone\":\"09039988198\",\"company\":\"HEXA\",\"address\":\"NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA\"},\"warehouse\":{\"id\":\"1\",\"name\":\"Head Office                                                                                - Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"code\":\"\"},\"coil_id\":\"49\",\"coil\":{\"id\":\"49\",\"code\":\"B197\",\"name\":\"Premium steel coil\",\"category\":\"alusteel\",\"color\":\"\",\"weight\":\"1289.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":10,\"sheet_meter\":10,\"meters\":100,\"quantity\":0,\"pieces\":0,\"unit_price\":10000,\"row_subtotal\":1000000}],\"total_meters\":100,\"total_amount\":1000000,\"created_at\":\"2025-11-11 23:29:00\"}', 'completed', 2, '2025-11-11 22:29:00', NULL, '2a40257a5afff45470a5dea063f2bef534b332fb5193aaa61b11079e777ad30b');

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

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`id`, `invoice_id`, `amount_paid`, `reference`, `payment_method`, `created_by`, `created_at`) VALUES
(1, 29, 100000.00, 'Bank transfer to company', 'bank_transfer', 2, '2025-11-10 05:12:31'),
(2, 30, 235000.00, 'transfer to company account', 'bank_transfer', 2, '2025-11-10 05:30:16'),
(3, 31, 126000.00, 'cash at factory', 'cash', 2, '2025-11-10 05:32:30'),
(4, 30, 1000000.00, 'He wrote a cheque for us', 'cheque', 2, '2025-11-11 18:42:03');

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
(37, 1, 49, 26, 'available_stock', 1000.00, 1000.00, 1000000.00, 'completed', 2, '2025-11-09 01:51:16', NULL, NULL),
(38, 1, 28, 27, 'available_stock', 1298.00, 999.99, 1297987.02, 'completed', 2, '2025-11-09 01:57:09', NULL, NULL),
(39, 1, 31, 28, 'available_stock', 182.00, 100.00, 18200.00, 'completed', 2, '2025-11-09 02:02:34', NULL, NULL),
(40, 1, 31, 29, 'available_stock', 1000.00, 1000.00, 1000000.00, 'completed', 2, '2025-11-09 02:37:19', NULL, NULL),
(41, 1, 49, NULL, 'retail', 100.00, 1000.00, 100000.00, 'completed', 2, '2025-11-09 03:29:03', NULL, NULL),
(42, 3, 4, NULL, 'retail', 0.00, 0.00, 454000.00, 'completed', 2, '2025-11-09 18:25:35', NULL, NULL),
(43, 1, 4, NULL, 'retail', 0.00, 0.00, 540000.00, 'completed', 2, '2025-11-10 04:50:10', NULL, NULL),
(44, 1, 49, NULL, 'retail', 407.00, 5000.00, 2035000.00, 'completed', 2, '2025-11-10 05:29:20', NULL, NULL),
(45, 1, 4, NULL, 'retail', 0.00, 0.00, 126000.00, 'completed', 2, '2025-11-10 05:32:13', NULL, NULL),
(46, 1, 49, NULL, 'retail', 93.00, 1000.00, 93000.00, 'completed', 2, '2025-11-11 22:26:27', NULL, NULL),
(47, 1, 49, NULL, 'retail', 100.00, 10000.00, 1000000.00, 'completed', 2, '2025-11-11 22:29:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_entries`
--

CREATE TABLE `stock_entries` (
  `id` int(11) NOT NULL,
  `coil_id` int(11) NOT NULL,
  `meters` decimal(10,2) NOT NULL,
  `meters_remaining` decimal(10,2) NOT NULL,
  `meters_used` decimal(10,2) DEFAULT 0.00,
  `status` enum('available','factory_use','sold') NOT NULL DEFAULT 'available' COMMENT 'Stock entry status: available for direct sale or factory use',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stock entries with individual status tracking (available/factory_use)';

--
-- Dumping data for table `stock_entries`
--

INSERT INTO `stock_entries` (`id`, `coil_id`, `meters`, `meters_remaining`, `meters_used`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(24, 49, 1000.00, 300.00, 0.00, 'factory_use', 2, '2025-11-09 01:11:23', '2025-11-11 22:29:00', NULL),
(25, 4, 182.00, 182.00, 0.00, 'factory_use', 2, '2025-11-09 01:38:03', '2025-11-09 19:55:35', '2025-11-09 19:55:35'),
(26, 49, 1000.00, 0.00, 1000.00, 'sold', 2, '2025-11-09 01:50:08', '2025-11-09 02:01:25', NULL),
(27, 28, 1298.00, 0.00, 1298.00, 'sold', 2, '2025-11-09 01:55:50', '2025-11-09 02:01:33', NULL),
(28, 31, 182.00, 0.00, 182.00, 'sold', 2, '2025-11-09 02:02:00', '2025-11-09 02:02:34', NULL),
(29, 31, 1000.00, 0.00, 1000.00, 'sold', 2, '2025-11-09 02:36:57', '2025-11-09 02:37:19', NULL),
(30, 29, 1000.00, 1000.00, 0.00, 'factory_use', 2, '2025-11-09 02:43:37', '2025-11-09 02:43:41', NULL);

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
(31, 49, 24, 'inflow', 'Stock moved to factory use - Entry #24 (1000.00m available)', 1000.00, 0.00, 1000.00, 'stock_entry', 24, 2, '2025-11-09 01:25:55'),
(34, 49, 24, 'outflow', 'Stock moved back to available - Entry #24 (removing 1000m from factory tracking)', 0.00, 1000.00, 0.00, 'status_change', 24, 2, '2025-11-09 01:35:10'),
(35, 49, 24, 'inflow', 'Stock moved to factory use - Entry #24 (1000.00m available)', 1000.00, 0.00, 1000.00, 'stock_entry', 24, 2, '2025-11-09 01:35:20'),
(36, 4, 25, 'inflow', 'Stock moved to factory use - Entry #25 (182.00m available)', 182.00, 0.00, 182.00, 'stock_entry', 25, 2, '2025-11-09 01:38:06'),
(37, 29, 30, 'inflow', 'Stock moved to factory use - Entry #30 (1000.00m available)', 1000.00, 0.00, 1000.00, 'stock_entry', 30, 2, '2025-11-09 02:43:41'),
(38, 49, 24, 'outflow', 'Production drawdown for sale #41', 0.00, 100.00, 900.00, 'sale', 41, 2, '2025-11-09 03:29:03'),
(39, 49, 24, 'outflow', 'Production drawdown for sale #44', 0.00, 407.00, 493.00, 'sale', 44, 2, '2025-11-10 05:29:20'),
(40, 49, 24, 'outflow', 'Production drawdown for sale #46', 0.00, 93.00, 400.00, 'sale', 46, 2, '2025-11-11 22:26:27'),
(41, 49, 24, 'outflow', 'Production drawdown for sale #47', 0.00, 100.00, 300.00, 'sale', 47, 2, '2025-11-11 22:29:00');

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
(32, 4, 'dashboard', '[\"view\"]', '2025-11-06 20:44:39', NULL),
(33, 2, 'color_management', '[\"view\", \"create\", \"edit\", \"delete\"]', '2025-11-11 21:31:36', NULL),
(34, 3, 'color_management', '[\"view\", \"create\", \"edit\", \"delete\"]', '2025-11-11 21:31:36', NULL);

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
(1, 'Head Office', 'Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja', '+2348065336645', 1, '2025-11-07 07:51:17', NULL, NULL),
(2, 'Branch', 'BRANCH ADDRESS', '192019101912', 1, '2025-11-07 12:28:24', '2025-11-09 13:35:57', '2025-11-07 12:28:35'),
(3, 'Branch Office', 'BRANCH', '09012345678', 1, '2025-11-09 13:36:50', NULL, NULL);

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
  ADD KEY `idx_deleted` (`deleted_at`),
  ADD KEY `color_id` (`color_id`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_active` (`is_active`),
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
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `production`
--
ALTER TABLE `production`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `stock_entries`
--
ALTER TABLE `stock_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `stock_ledger`
--
ALTER TABLE `stock_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  ADD CONSTRAINT `coils_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `coils_ibfk_2` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`);

--
-- Constraints for table `colors`
--
ALTER TABLE `colors`
  ADD CONSTRAINT `colors_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

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
