-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2025 at 10:29 AM
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
-- Database: `obumuvcg_stockdb`
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
  `meters` decimal(10,2) DEFAULT NULL COMMENT 'Approximate meters per coil (informational only)',
  `gauge` varchar(50) DEFAULT NULL COMMENT 'Material gauge/thickness (e.g., 0.45mm, 0.50mm)',
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

INSERT INTO `coils` (`id`, `code`, `name`, `color`, `color_id`, `net_weight`, `meters`, `gauge`, `category`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(49, 'D-35', 'I/beige Aluminium coil', 'IBeige', 9, 2850.00, NULL, NULL, 'aluminum', 'out_of_stock', 2, '2025-11-08 11:37:39', '2025-11-19 13:40:31', '2025-11-17 12:11:08'),
(50, 'D-212', 'I/ beige Aluminiun coil', 'IBeige', 9, 2650.00, NULL, NULL, 'aluminum', 'out_of_stock', 5, '2025-11-08 12:25:04', '2025-11-19 13:40:35', '2025-11-17 12:13:31'),
(51, 'D-218', 'TC/Red Aluninium coil', 'TCRed', 13, 2500.00, NULL, NULL, 'aluminum', 'out_of_stock', 5, '2025-11-08 12:44:47', '2025-11-17 14:21:31', '2025-11-17 12:13:50'),
(52, 'AS190', 'AS190 Alusteel coil', 'BGreen', 15, 2824.00, 1475.00, '0.20', 'alusteel', 'out_of_stock', 5, '2025-11-08 12:53:21', '2025-11-19 13:40:42', NULL),
(54, 'A333', 'I/white Aluminium coil', 'IWhite', 16, 2199.00, 2977.00, '0.45', 'aluminum', 'available', 5, '2025-11-10 13:29:56', '2025-11-14 12:37:55', NULL),
(55, 'A352', 'S/ blue Aluminium coil', 'SBlue', 11, 2059.00, 2809.00, '0.45', 'aluminum', 'available', 5, '2025-11-10 13:31:19', '2025-11-14 12:38:51', NULL),
(56, 'B89', 'I/white Aluminium coil', 'IWhite', 16, 2316.00, 3153.00, '0.45', 'aluminum', 'available', 5, '2025-11-10 13:32:53', '2025-11-14 12:37:02', NULL),
(57, 'C3', 'G/beige Aluminium coil', 'GBeige', 14, 2048.00, 1996.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:34:39', '2025-11-14 12:23:15', NULL),
(58, 'C5', 'G/beige Aluminium coil', 'GBeige', 14, 2048.00, 1995.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:36:15', '2025-11-14 12:26:04', NULL),
(59, 'C20', 'G/beige Aluminium coil', 'GBeige', 14, 2072.00, 2017.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:37:10', '2025-11-14 12:27:16', NULL),
(60, 'C44', 'G/beige Aluminium coil', 'GBeige', 14, 2060.00, 2002.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:37:58', '2025-11-14 12:27:54', NULL),
(61, 'C57', 'G/beige Aluminium coil', 'GBeige', 14, 2061.00, 2005.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:38:48', '2025-11-14 12:28:38', NULL),
(62, 'C71', 'G/beige Aluminium coil', 'GBeige', 14, 2050.00, 1992.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:39:52', '2025-11-14 12:29:20', NULL),
(63, 'C98', 'G/beige Aluminium coil', 'GBeige', 14, 2046.00, 1993.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:40:32', '2025-11-14 12:30:26', NULL),
(64, 'D13', 'S/blue Aluminium coil', 'SBlue', 11, 1763.00, 2507.00, '0.45', 'aluminum', 'available', 5, '2025-11-10 13:41:52', '2025-11-14 12:31:23', NULL),
(65, 'D14', 'S/blue Aluminium coil', 'SBlue', 11, 1375.00, 1895.00, '0.45', 'aluminum', 'available', 5, '2025-11-10 13:42:43', '2025-11-14 12:32:18', NULL),
(66, 'D29', 'S/blue Aluminium coil', 'SBlue', 11, 1375.00, 1895.00, '0.45', 'aluminum', 'available', 5, '2025-11-10 13:44:06', '2025-11-14 12:33:18', NULL),
(67, 'D31', 'I/beige Aluminium coil', 'IBeige', 9, 2073.00, 2000.00, '0.55', 'aluminum', 'out_of_stock', 5, '2025-11-10 13:45:50', '2025-11-19 13:40:46', NULL),
(68, 'D54', 'G/beige Aluminium coil', 'GBeige', 14, 2055.00, 1992.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:46:46', '2025-11-14 12:42:19', NULL),
(69, 'D57', 'I/beige Aluminium coil', 'IBeige', 9, 2161.00, 2243.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:48:03', '2025-11-14 12:41:41', NULL),
(70, 'D60', 'G/beige Aluminium coil', 'GBeige', 14, 2053.00, 1990.00, '0.55', 'aluminum', 'out_of_stock', 5, '2025-11-10 13:49:05', '2025-12-12 11:40:56', NULL),
(71, 'C30', 'G/beige Aluminium coil', 'GBeige', 14, 2064.00, 2010.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:50:02', '2025-11-14 12:44:18', NULL),
(72, 'D99', 'G/beige Aluminium coil', 'GBeige', 14, 2140.00, 2064.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:51:59', '2025-11-14 12:45:56', NULL),
(73, 'D101', 'G/beige Aluminium coil', 'GBeige', 14, 2137.00, 2063.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:52:46', '2025-11-14 12:46:49', NULL),
(74, 'D102', 'G/beige Aluminium coil', 'GBeige', 14, 2142.00, 2066.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:53:32', '2025-11-14 12:47:33', NULL),
(75, 'D109', 'I/beige Aluminium coil', 'IBeige', 9, 1856.00, 1789.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:54:37', '2025-11-14 12:48:53', NULL),
(76, 'D111', 'I/beige Aluminium coil', 'IBeige', 9, 2179.00, 2240.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:55:19', '2025-11-14 12:49:34', NULL),
(77, 'D116', 'I/beige Aluminium coil', 'IBeige', 9, 1856.00, 1789.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 13:56:03', '2025-11-14 12:50:13', NULL),
(78, 'D162', 'T/black Aluminium coil', 'TBlack', 12, 1798.00, 2476.00, '0.45', 'aluminum', 'available', 5, '2025-11-10 13:58:03', '2025-11-14 12:52:27', NULL),
(79, 'D192', 'T/black Aluminium coil', 'TBlack', 12, 1790.00, 2465.00, '0.45', 'aluminum', 'available', 5, '2025-11-10 14:02:32', '2025-11-14 12:53:30', NULL),
(80, 'D214', 'P/green Aluminium coil', 'PGreen', 10, 1952.00, 2105.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 14:04:39', '2025-11-14 12:55:21', NULL),
(81, 'D215', 'S/blue Aluminium coil', 'SBlue', 11, 1587.00, 1706.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 14:05:31', '2025-11-14 12:55:53', NULL),
(82, 'D235', 'G/beige Aluminium coil', 'GBeige', 14, 1848.00, 885.00, '0.7', 'aluminum', 'available', 5, '2025-11-10 14:06:25', '2025-11-14 12:57:01', NULL),
(83, 'D236', 'G/beige Aluminium coil', 'GBeige', 14, 1863.00, 890.00, '0.7', 'aluminum', 'available', 5, '2025-11-10 14:07:14', '2025-11-14 12:57:35', NULL),
(84, 'D239', 'B/green Aluminium coil', 'BGreen', 15, 1745.00, 834.00, '0.7', 'aluminum', 'available', 5, '2025-11-10 14:08:22', '2025-11-14 12:58:24', NULL),
(85, 'D246', 'S/blue Aluminium coil', 'SBlue', 11, 2025.00, 2166.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 14:09:19', '2025-11-14 13:00:56', NULL),
(86, 'D261', 'P/green Aluminium coil', 'PGreen', 10, 1977.00, 2131.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 14:09:59', '2025-11-14 13:01:37', NULL),
(87, 'D269', 'S/blue Aluminium coil', 'SBlue', 11, 2037.00, 2180.00, '0.55', 'aluminum', 'available', 5, '2025-11-10 14:10:45', '2025-11-14 13:02:12', NULL),
(88, 'AS50', 'AS50 Alusteel coil', 'SBlue', 11, 3584.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:23:51', '2025-11-17 10:55:35', '2025-11-17 10:55:35'),
(89, 'AS74', 'Alusteel coil', 'TBlack', 12, 3510.00, 2092.00, '0.18', 'alusteel', 'available', 5, '2025-11-10 14:26:28', '2025-11-17 10:51:24', NULL),
(90, 'AS75', 'Alusteel coil', 'TBlack', 12, 3584.00, 2120.00, '0.18', 'alusteel', 'available', 5, '2025-11-10 14:27:57', '2025-11-17 10:50:40', NULL),
(91, 'AS76', 'Alusteel coil', 'TBlack', 12, 3534.00, 2087.00, '0.18', 'alusteel', 'available', 5, '2025-11-10 14:28:50', '2025-11-17 10:49:38', NULL),
(92, 'AS193', 'Alusteel coil', 'TBlack', 12, 3376.00, 2048.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 14:30:05', '2025-11-14 13:15:05', NULL),
(93, 'AS198', 'Alusteel coil', 'SBlue', 11, 3146.00, 1870.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 14:33:21', '2025-11-14 13:14:22', NULL),
(94, 'AS199', 'Alusteel coil', 'SBlue', 11, 2266.00, 1327.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 14:34:02', '2025-11-14 13:13:34', NULL),
(95, 'AS200', 'Alusteel coil', 'SBlue', 11, 3302.00, 1955.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 14:34:57', '2025-11-14 13:16:09', NULL),
(96, 'AS230', 'Alusteel coil', 'TBlack', 12, 3510.00, 1570.00, '0.24', 'alusteel', 'out_of_stock', 5, '2025-11-10 14:35:43', '2025-12-12 11:33:17', NULL),
(97, 'AS89', 'Alusteel coil', 'SBlue', 11, 3440.00, 2347.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:37:00', '2025-11-14 13:25:11', NULL),
(98, 'AS91', 'Alusteel coil', 'SBlue', 11, 3511.00, 2375.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:37:53', '2025-11-14 13:24:16', NULL),
(99, 'AS92', 'Alusteel coil', 'SBlue', 11, 3598.00, 2457.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:39:14', '2025-11-14 13:23:08', NULL),
(100, 'AS93', 'Alusteel coil', 'SBlue', 11, 3222.00, 2143.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:39:56', '2025-11-14 13:22:22', NULL),
(101, 'AS94', 'Alusteel coil', 'SBlue', 11, 3000.00, 2011.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:41:01', '2025-11-14 13:21:48', NULL),
(102, 'AS95', 'Alusteel coil', 'SBlue', 11, 3034.00, 2006.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:41:56', '2025-11-14 13:20:49', NULL),
(103, 'AS96', 'Alusteel coil', 'SBlue', 11, 2342.00, 1522.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:42:41', '2025-11-14 13:19:55', NULL),
(104, 'AS104', 'Alusteel coil', 'SBlue', 11, 3602.00, 2444.00, '0.16', 'alusteel', 'out_of_stock', 5, '2025-11-10 14:43:33', '2025-12-12 11:21:29', NULL),
(105, 'AS105', 'Alusteel coil', 'SBlue', 11, 3212.00, 2199.00, '0.16', 'alusteel', 'out_of_stock', 5, '2025-11-10 14:44:12', '2025-12-12 11:20:38', NULL),
(106, 'AS106', 'Alusteel coil', 'IWhite', 16, 3196.00, 2164.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:45:25', '2025-11-14 13:27:23', NULL),
(107, 'AS108', 'Alusteel coil', 'IWhite', 16, 3412.00, 2321.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:46:06', '2025-11-14 13:27:58', NULL),
(108, 'AS110', 'Alusteel coil', 'IWhite', 16, 3656.00, 2457.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:47:05', '2025-11-14 13:28:36', NULL),
(109, 'AS111', 'Alusteel coil', 'IWhite', 16, 3322.00, 2239.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:47:42', '2025-11-14 13:29:10', NULL),
(110, 'AS112', 'Alusteel coil', 'IWhite', 16, 2764.00, 1840.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:48:35', '2025-11-14 13:29:59', NULL),
(111, 'AS113', 'Alusteel coil', 'IWhite', 16, 2226.00, 1505.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:49:18', '2025-11-14 13:32:05', NULL),
(112, 'AS114', 'Alusteel coil', 'GBeige', 14, 3426.00, 2264.00, '0.16', 'alusteel', 'out_of_stock', 5, '2025-11-10 14:50:11', '2025-12-12 11:19:16', NULL),
(113, 'AS115', 'Alusteel coil', 'GBeige', 14, 3368.00, 2242.00, '0.16', 'alusteel', 'out_of_stock', 5, '2025-11-10 14:50:48', '2025-12-12 11:18:34', NULL),
(114, 'AS119', 'Alusteel coil', 'GBeige', 14, 3036.00, 1993.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:51:41', '2025-11-14 13:34:03', NULL),
(115, 'AS121', 'Alusteel coil', 'GBeige', 14, 3114.00, 2046.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:52:45', '2025-11-14 13:35:55', NULL),
(116, 'AS122', 'Alusteel coil', 'TBlack', 12, 3496.00, 2320.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:53:41', '2025-11-14 13:36:37', NULL),
(117, 'AS126', 'Alusteel coil', 'TBlack', 12, 2800.00, 1843.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:54:49', '2025-11-14 13:37:23', NULL),
(118, 'AS130', 'Alusteel coil', 'BGreen', 15, 3044.00, 1999.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:55:42', '2025-11-14 13:38:26', NULL),
(119, 'AS131', 'Alusteel coil', 'BGreen', 15, 3084.00, 2034.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:56:36', '2025-11-14 13:39:10', NULL),
(120, 'AS133', 'Alusteel coil', 'BGreen', 15, 3016.00, 1992.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:57:31', '2025-11-14 13:41:18', NULL),
(121, 'AS134', 'Alusteel coil', 'BGreen', 15, 3504.00, 2321.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:58:25', '2025-11-14 13:41:47', NULL),
(122, 'AS138', 'Alusteel coil', 'BGreen', 15, 3366.00, 2223.00, '0.16', 'alusteel', 'out_of_stock', 5, '2025-11-10 14:59:25', '2025-12-12 11:16:56', NULL),
(123, 'AS136', 'Alusteel coil', 'BGreen', 15, 3444.00, 2295.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 15:00:17', '2025-11-14 13:44:37', NULL),
(124, 'AS137', 'Alusteel coil', 'BGreen', 15, 3466.00, 2308.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 15:01:11', '2025-11-14 13:45:23', NULL),
(125, 'AS139', 'Alusteel coil', 'TCRed', 13, 3022.00, 1991.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 15:05:24', '2025-11-14 13:46:38', NULL),
(126, 'AS140', 'Alusteel coil', 'TCRed', 13, 2940.00, 1941.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 15:06:11', '2025-11-14 13:47:10', NULL),
(127, 'AS142', 'Alusteel coil', 'TCRed', 13, 3100.00, 2044.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 15:06:59', '2025-11-14 13:47:55', NULL),
(128, 'AS143', 'Alusteel coil', 'TCRed', 13, 2994.00, 1957.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 15:07:41', '2025-11-14 13:48:33', NULL),
(129, 'AS144', 'Alusteel coil', 'TCRed', 13, 2910.00, 1923.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 15:08:37', '2025-11-14 13:49:14', NULL),
(130, 'AS145', 'Alusteel coil', 'TCRed', 13, 3574.00, 2373.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 15:09:17', '2025-11-14 13:49:48', NULL),
(131, 'AS146', 'Alusteel coil', 'TCRed', 13, 3344.00, 2225.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 15:09:59', '2025-11-14 13:50:26', NULL),
(132, 'AS157', 'Alusteel coil', 'SBlue', 11, 3372.00, 1271.00, '0.28', 'alusteel', 'available', 5, '2025-11-10 15:11:20', '2025-11-14 13:54:07', NULL),
(133, 'AS158', 'Alusteel coil', 'SBlue', 11, 3344.00, 1262.00, '0.28', 'alusteel', 'available', 5, '2025-11-10 15:12:11', '2025-11-14 13:54:55', NULL),
(134, 'AS159', 'Alusteel coil', 'BGreen', 15, 2806.00, 1465.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:13:09', '2025-11-14 13:55:58', NULL),
(135, 'AS160', 'Alusteel coil', 'BGreen', 15, 2216.00, 1145.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:13:52', '2025-11-14 13:57:03', NULL),
(136, 'AS161', 'Alusteel coil', 'BGreen', 15, 2104.00, 1085.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:16:17', '2025-11-14 13:57:44', NULL),
(137, 'AS162', 'Alusteel coil', 'BGreen', 15, 2142.00, 1105.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:16:51', '2025-11-14 13:58:23', NULL),
(138, 'AS163', 'Alusteel coil', 'BGreen', 15, 2146.00, 1105.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:17:29', '2025-11-14 13:59:00', NULL),
(139, 'AS164', 'Alusteel coil', 'BGreen', 15, 2166.00, 1115.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:18:18', '2025-11-14 13:59:34', NULL),
(140, 'AS165', 'Alusteel coil', 'BGreen', 15, 2122.00, 1095.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:19:02', '2025-11-14 14:00:32', NULL),
(141, 'AS166', 'Alusteel coil', 'BGreen', 15, 2074.00, 1075.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:19:42', '2025-11-14 14:03:00', NULL),
(142, 'AS167', 'Alusteel coil', 'BGreen', 15, 2098.00, 1075.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:20:44', '2025-11-14 14:03:44', NULL),
(143, 'AS168', 'Alusteel coil', 'BGreen', 15, 2772.00, 1445.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:21:22', '2025-11-14 14:04:18', NULL),
(144, 'AS169', 'Alusteel coil', 'BGreen', 15, 2774.00, 1445.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:22:13', '2025-11-14 14:05:56', NULL),
(145, 'AS170', 'Alusteel coil', 'BGreen', 15, 2774.00, 1445.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:23:00', '2025-11-14 14:06:55', NULL),
(146, 'AS171', 'Alusteel coil', 'BGreen', 15, 2780.00, 1445.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:23:44', '2025-11-14 14:07:40', NULL),
(147, 'AS172', 'Alusteel coil', 'BGreen', 15, 2740.00, 1425.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:24:23', '2025-11-14 14:08:32', NULL),
(148, 'AS173', 'Alusteel coil', 'BGreen', 15, 2840.00, 1475.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:25:01', '2025-11-14 14:09:07', NULL),
(149, 'AS174', 'Alusteel coil', 'BGreen', 15, 2840.00, 1475.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:25:35', '2025-11-14 14:09:53', NULL),
(150, 'AS175', 'Alusteel coil', 'BGreen', 15, 2172.00, 1115.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:26:10', '2025-11-14 14:10:25', NULL),
(151, 'AS176', 'Alusteel coil', 'BGreen', 15, 2814.00, 1465.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:26:49', '2025-11-14 14:11:03', NULL),
(152, 'AS177', 'Alusteel coil', 'BGreen', 15, 2144.00, 1105.00, '0.20', 'alusteel', 'available', 5, '2025-11-10 15:27:31', '2025-11-14 14:11:47', NULL),
(153, 'AS178', 'Alusteel coil', 'BGreen', 15, 2158.00, 1105.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 07:47:33', '2025-11-14 14:12:24', NULL),
(154, 'AS179', 'Alusteel coil', 'BGreen', 15, 2124.00, 1085.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 07:48:27', '2025-11-14 14:13:00', NULL),
(155, 'AS180', 'Alusteel coil', 'BGreen', 15, 2610.00, 1351.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 07:49:11', '2025-11-14 14:14:13', NULL),
(156, 'AS181', 'Alusteel coil', 'BGreen', 15, 2608.00, 1351.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 07:50:04', '2025-11-14 14:15:03', NULL),
(157, 'AS182', 'Alusteel coil', 'BGreen', 15, 3142.00, 1645.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 07:50:44', '2025-11-14 14:15:39', NULL),
(158, 'AS183', 'Alusteel coil', 'BGreen', 15, 2790.00, 1445.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 07:51:29', '2025-11-14 14:16:15', NULL),
(159, 'AS184', 'Alusteel coil', 'BGreen', 15, 2782.00, 1445.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 07:52:14', '2025-11-14 14:16:57', NULL),
(160, 'AS185', 'Alusteel coil', 'BGreen', 15, 2788.00, 1445.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 07:52:51', '2025-11-14 14:18:18', NULL),
(161, 'AS186', 'Alusteel coil', 'BGreen', 15, 2184.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:53:26', '2025-12-09 10:51:53', NULL),
(162, 'AS187', 'Alusteel coil', 'BGreen', 15, 2120.00, 1085.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 07:54:06', '2025-11-14 14:19:50', NULL),
(163, 'AS188', 'Alusteel coil', 'BGreen', 15, 2188.00, 1125.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 07:54:58', '2025-11-14 14:20:35', NULL),
(164, 'AS189', 'Alusteel coil', 'BGreen', 15, 2200.00, 1125.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 07:56:31', '2025-11-14 14:21:22', NULL),
(166, 'AS191', 'Alusteel coil', 'BGreen', 15, 2818.00, 1475.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 07:58:37', '2025-11-14 14:23:35', NULL),
(168, 'AS192', 'Alusteel coil', 'BGreen', 15, 2832.00, 1475.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 08:01:14', '2025-11-14 14:25:59', NULL),
(170, 'AS194', 'Alusteel coil', 'BGreen', 15, 2774.00, 1445.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 08:24:01', '2025-11-14 14:27:15', NULL),
(171, 'AS196', 'Alusteel coil', 'BGreen', 15, 2744.00, 1425.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 08:25:31', '2025-11-14 14:28:33', NULL),
(172, 'AS197', 'Alusteel coil', 'BGreen', 15, 2760.00, 1435.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 08:26:17', '2025-11-14 14:29:18', NULL),
(173, 'AS201', 'Alusteel coil', 'TCRed', 13, 3410.00, 2270.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:31:36', '2025-11-14 14:32:00', NULL),
(174, 'AS202', 'Alusteel coil', 'TCRed', 13, 3360.00, 2266.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:32:34', '2025-11-14 14:33:54', NULL),
(175, 'AS203', 'Alusteel coil', 'TCRed', 13, 3186.00, 2156.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:33:25', '2025-11-14 14:34:38', NULL),
(176, 'AS204', 'Alusteel coil', 'TCRed', 13, 3166.00, 844.60, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:34:11', '2025-12-09 10:51:41', NULL),
(177, 'AS205', 'Alusteel coil', 'SBlue', 11, 3412.00, 2293.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:34:58', '2025-11-14 14:36:13', NULL),
(178, 'AS206', 'Alusteel coil', 'SBlue', 11, 3438.00, 2298.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:35:40', '2025-11-14 14:37:20', NULL),
(179, 'AS207', 'Alusteel coil', 'SBlue', 11, 3428.00, 2291.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:36:29', '2025-11-14 14:38:03', NULL),
(180, 'AS208', 'Alusteel coil', 'SBlue', 11, 3298.00, 2230.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:37:05', '2025-11-14 14:39:05', NULL),
(181, 'AS209', 'Alusteel coil', 'SBlue', 11, 3296.00, 2233.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:37:47', '2025-11-14 14:39:49', NULL),
(182, 'AS210', 'Alusteel coil', 'SBlue', 11, 3392.00, 2266.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:40:47', '2025-11-14 14:40:42', NULL),
(183, 'AS211', 'Alusteel coil', 'BGreen', 15, 3338.00, 2236.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:41:27', '2025-11-14 14:41:34', NULL),
(184, 'AS212', 'Alusteel coil', 'BGreen', 15, 3430.00, 2298.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:42:10', '2025-11-14 14:42:30', NULL),
(186, 'AS214', 'Alusteel coil', 'BGreen', 15, 3344.00, 2249.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:43:55', '2025-11-14 14:43:26', NULL),
(187, 'AS215', 'Alusteel coil', 'BGreen', 15, 3560.00, 2390.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:44:51', '2025-11-14 14:45:29', NULL),
(188, 'AS216', 'Alusteel coil', 'BGreen', 15, 3312.00, 2233.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:45:33', '2025-11-14 14:46:57', NULL),
(189, 'AS217', 'Alusteel coil', 'BGreen', 15, 3564.00, 2402.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:46:09', '2025-11-14 14:51:19', NULL),
(190, 'AS218', 'Alusteel coil', 'TBlack', 12, 3316.00, 2232.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:46:48', '2025-11-14 15:04:00', NULL),
(191, 'AS219', 'Alusteel coil', 'IBeige', 9, 3378.00, 2262.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:51:27', '2025-11-14 15:05:52', NULL),
(192, 'AS221', 'Alusteel coil', 'IBeige', 9, 3146.00, 2120.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:52:22', '2025-11-14 15:08:00', NULL),
(193, 'AS222', 'Alusteel coil', 'IBeige', 9, 3150.00, 2127.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:53:06', '2025-11-14 15:09:00', NULL),
(194, 'AS229', 'Alusteel coil', 'TBlack', 12, 3266.00, 2209.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 08:54:30', '2025-11-14 15:09:59', NULL),
(195, 'AS234', 'Alusteel coil', 'PGreen', 10, 3608.00, 2155.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 08:56:29', '2025-11-14 15:11:45', NULL),
(196, 'AS235', 'Alusteel coil', 'TBlack', 12, 3254.00, 2209.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 08:57:15', '2025-11-14 15:12:41', NULL),
(197, 'AS236', 'Alusteel coil', 'GBeige', 14, 3314.00, 2262.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 08:57:55', '2025-11-14 15:13:48', NULL),
(198, 'AS237', 'Alusteel coil', 'GBeige', 14, 3328.00, 2243.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 08:58:40', '2025-11-14 15:14:42', NULL),
(199, 'AS238', 'Alusteel coil', 'GBeige', 14, 3374.00, 2251.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 08:59:26', '2025-11-14 15:15:53', NULL),
(200, 'AS239', 'Alusteel coil', 'TBlack', 12, 3332.00, 2243.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:00:08', '2025-11-14 15:16:44', NULL),
(201, 'AS240', 'Alusteel coil', 'TBlack', 12, 3336.00, 2253.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:00:48', '2025-11-14 15:17:33', NULL),
(202, 'AS242', 'Alusteel coil', 'PGreen', 10, 3050.00, 1765.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:01:29', '2025-11-27 11:53:31', NULL),
(203, 'AS243', 'Alusteel coil', 'PGreen', 10, 3890.00, 2303.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:02:13', '2025-11-14 15:19:42', NULL),
(204, 'AS244', 'Alusteel coil', 'IBeige', 9, 3202.00, 2161.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:02:55', '2025-11-27 11:53:57', NULL),
(205, 'AS245', 'Alusteel coil', 'BGreen', 15, 3184.00, 1896.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:03:40', '2025-11-14 15:21:49', NULL),
(206, 'AS246', 'Alusteel coil', 'PGreen', 10, 3478.00, 2085.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:04:22', '2025-11-27 11:54:20', NULL),
(207, 'AS247', 'Alusteel coil', 'IBeige', 9, 3554.00, 2109.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:05:00', '2025-11-14 15:23:43', NULL),
(208, 'AS248', 'Alusteel coil', 'IBeige', 9, 3560.00, 2118.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:05:46', '2025-11-14 15:24:39', NULL),
(209, 'AS249', 'Alusteel coil', 'IBeige', 9, 3600.00, 2164.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:06:44', '2025-11-14 15:25:56', NULL),
(210, 'AS250', 'Alusteel coil', 'IBeige', 9, 3186.00, 1901.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:07:35', '2025-11-14 15:26:58', NULL),
(211, 'AS251', 'Alusteel coil', 'IBeige', 9, 3174.00, 1891.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:08:25', '2025-11-14 15:27:40', NULL),
(212, 'AS257', 'Alusteel coil', 'IBeige', 9, 3578.00, 1579.00, '0.24', 'alusteel', 'out_of_stock', 5, '2025-11-11 09:09:25', '2025-12-12 11:23:16', NULL),
(213, 'AS262', 'Alusteel coil', 'IBeige', 9, 3492.00, 1550.00, '0.24', 'alusteel', 'out_of_stock', 5, '2025-11-11 09:10:13', '2025-12-12 11:22:50', NULL),
(214, 'AS264', 'Alusteel coil', 'IBeige', 9, 3272.00, 1450.00, '0.24', 'alusteel', 'out_of_stock', 5, '2025-11-11 09:11:09', '2025-12-12 11:22:06', NULL),
(215, 'AS269', 'Alusteel coil', 'IBeige', 9, 3462.00, 1312.00, '0.24', 'alusteel', 'out_of_stock', 5, '2025-11-11 09:12:00', '2025-12-12 11:36:02', NULL),
(216, 'AS270', 'Alusteel coil', 'IBeige', 9, 3434.00, 1289.00, '0.24', 'alusteel', 'out_of_stock', 5, '2025-11-11 09:12:39', '2025-12-12 11:31:38', NULL),
(217, 'AS271', 'Alusteel coil', 'IBeige', 9, 3152.00, 2134.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 09:13:28', '2025-11-14 15:35:18', NULL),
(218, 'AS272', 'Alusteel coil', 'IBeige', 9, 2582.00, 1732.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 09:14:09', '2025-11-17 09:13:01', NULL),
(219, 'AS273', 'Alusteel coil', 'BGreen', 15, 2572.00, 1714.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 09:16:58', '2025-11-17 09:12:20', NULL),
(220, 'AS275', 'Alusteel coil', 'BGreen', 15, 3178.00, 1884.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:17:54', '2025-11-17 09:10:50', NULL),
(221, 'AS276', 'Alusteel coil', 'BGreen', 15, 3190.00, 1901.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:18:34', '2025-11-17 09:10:14', NULL),
(222, 'AS277', 'Alusteel coil', 'PGreen', 10, 2876.00, 1700.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:19:23', '2025-11-17 09:09:39', NULL),
(223, 'AS278', 'Alusteel coil', 'BGreen', 15, 3150.00, 1888.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:20:06', '2025-11-17 09:09:06', NULL),
(224, 'AS279', 'Alusteel coil', 'BGreen', 15, 3160.00, 1892.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:20:57', '2025-11-17 09:08:34', NULL),
(225, 'AS280', 'Alusteel coil', 'IBeige', 9, 3226.00, 1927.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:21:44', '2025-11-17 09:07:49', NULL),
(226, 'AS282', 'Alusteel coil', 'IBeige', 9, 2558.00, 1722.00, '0.16', 'alusteel', 'available', 5, '2025-11-11 09:22:33', '2025-11-17 09:07:04', NULL),
(227, 'AS283', 'Alusteel coil', 'BGreen', 15, 3182.00, 1904.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:23:26', '2025-11-17 08:50:00', NULL),
(228, 'AS284', 'Alusteel coil', 'IBeige', 9, 3532.00, 2128.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:24:22', '2025-11-17 08:48:46', NULL),
(229, 'AS285', 'Alusteel coil', 'IBeige', 9, 3184.00, 1893.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:25:17', '2025-11-17 08:51:24', NULL),
(230, 'AS286', 'Alusteel coil', 'IBeige', 9, 3136.00, 1789.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:25:59', '2025-11-17 08:52:53', NULL),
(231, 'AS287', 'Alusteel coil', 'GBeige', 14, 3076.00, 1655.00, '0.20', 'alusteel', 'available', 5, '2025-11-11 09:26:59', '2025-11-17 08:53:45', NULL),
(232, 'AS290', 'Alusteel coil', 'SBlue', 11, 3192.00, 1448.00, '0.24', 'alusteel', 'out_of_stock', 5, '2025-11-11 09:28:12', '2025-12-12 11:35:38', NULL),
(233, 'AS291', 'Alusteel coil', 'BGreen', 15, 2536.00, 1522.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:29:18', '2025-11-17 09:16:29', NULL),
(234, 'AS292', 'Alusteel coil', 'BGreen', 15, 2562.00, 1524.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:30:01', '2025-11-17 08:55:17', NULL),
(235, 'AS293', 'Alusteel coil', 'IBeige', 9, 3564.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:30:53', '2025-11-17 09:15:40', '2025-11-17 09:15:40'),
(236, 'AS299', 'Alusteel coil', 'BGreen', 15, 2512.00, 1509.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:32:00', '2025-11-17 08:56:11', NULL),
(237, 'AS300', 'Alusteel coil', 'GBeige', 14, 3296.00, 1461.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:32:51', '2025-11-17 08:56:51', NULL),
(238, 'AS301', 'Alusteel coil', 'PGreen', 10, 3462.00, 2085.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:33:40', '2025-11-17 08:57:50', NULL),
(239, 'AS302', 'Alusteel coil', 'BGreen', 15, 2546.00, 1507.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:34:57', '2025-11-17 08:59:44', NULL),
(240, 'AS303', 'Alusteel coil', 'IBeige', 9, 3550.00, 2125.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:35:43', '2025-11-17 09:00:23', NULL),
(241, 'AS309', 'Alusteel coil', 'BGreen', 15, 2516.00, 1501.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:36:48', '2025-11-17 09:01:33', NULL),
(242, 'AS315', 'Alusteel coil', 'IBeige', 9, 3288.00, 1460.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:37:43', '2025-11-17 09:02:23', NULL),
(243, 'AS316', 'Alusteel coil', 'IBeige', 9, 3274.00, 1451.00, '0.24', 'alusteel', 'out_of_stock', 5, '2025-11-11 09:38:23', '2025-12-12 11:34:33', NULL),
(244, 'AS317', 'Alusteel coil', 'BGreen', 15, 2514.00, 1495.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:43:49', '2025-11-17 09:03:32', NULL),
(245, 'AS318', 'Alusteel coil', 'BGreen', 15, 2530.00, 1508.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:44:42', '2025-11-17 09:04:11', NULL),
(246, 'AS320', 'Alusteel coil', 'BGreen', 15, 2564.00, 1516.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:45:42', '2025-11-17 09:04:48', NULL),
(247, 'AS325', 'Alusteel coil', 'GBeige', 14, 3322.00, 1465.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:46:46', '2025-11-17 09:05:27', NULL),
(248, 'AS326', 'Alusteel coil', 'BGreen', 15, 2530.00, 1510.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:47:30', '2025-11-17 09:06:10', NULL),
(249, 'AS331', 'Alusteel coil', 'TBlack', 12, 3260.00, 1941.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:49:31', '2025-11-17 10:45:13', NULL),
(250, 'AS332', 'Alusteel coil', 'TBlack', 12, 3250.00, 1941.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:50:19', '2025-11-17 10:36:44', NULL),
(251, 'AS333', 'Alusteel coil', 'TBlack', 12, 3406.00, 2048.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:50:56', '2025-11-17 10:37:14', NULL),
(252, 'AS334', 'Alusteel coil', 'IWhite', 16, 2806.00, 1659.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:51:53', '2025-11-17 10:39:46', NULL),
(253, 'AS335', 'Alusteel coil', 'IWhite', 16, 2820.00, 1677.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:52:44', '2025-11-17 10:40:25', NULL),
(254, 'AS336', 'Alusteel coil', 'IWhite', 16, 2638.00, 1573.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:53:26', '2025-11-17 10:41:02', NULL),
(255, 'AS337', 'Alusteel coil', 'IWhite', 16, 3256.00, 1934.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:54:05', '2025-11-17 10:41:41', NULL),
(256, 'AS338', 'Alusteel coil', 'IWhite', 16, 3316.00, 1974.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:54:44', '2025-11-17 10:42:26', NULL),
(257, 'AS339', 'Alusteel coil', 'IWhite', 16, 3348.00, 2006.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:55:25', '2025-11-17 10:42:59', NULL),
(258, 'AS340', 'Alusteel coil', 'IWhite', 16, 3350.00, 1986.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:56:12', '2025-11-17 10:22:47', NULL),
(259, 'AS341', 'Alusteel coil', 'IWhite', 16, 3336.00, 2001.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:56:48', '2025-11-17 09:18:45', NULL),
(260, 'AS342', 'Alusteel coil', 'IWhite', 16, 3270.00, 1952.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:57:24', '2025-11-17 09:19:18', NULL),
(261, 'AS343', 'Alusteel coil', 'GBeige', 14, 3294.00, 1971.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:58:07', '2025-11-17 09:19:56', NULL),
(262, 'AS344', 'Alusteel coil', 'GBeige', 14, 3452.00, 2071.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 09:59:16', '2025-11-17 09:20:31', NULL),
(263, 'AS346', 'Alusteel coil', 'GBeige', 14, 3400.00, 2017.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 10:00:03', '2025-11-17 09:23:06', NULL),
(264, 'AS347', 'Alusteel coil', 'GBeige', 14, 3370.00, 2017.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 10:00:51', '2025-11-17 09:24:22', NULL),
(265, 'AS348', 'Alusteel coil', 'GBeige', 14, 3358.00, 2016.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 10:01:34', '2025-11-17 09:25:02', NULL),
(266, 'AS349', 'Alusteel coil', 'BGreen', 15, 3344.00, 1999.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 10:02:14', '2025-11-17 09:25:30', NULL),
(267, 'AS350', 'Alusteel coil', 'BGreen', 15, 3428.00, 2047.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 10:03:03', '2025-11-17 09:26:05', NULL),
(268, 'AS351', 'Alusteel coil', 'BGreen', 15, 3242.00, 1898.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 10:03:49', '2025-11-17 09:26:43', NULL),
(269, 'AS352', 'Alusteel coil', 'BGreen', 15, 3106.00, 1844.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 10:04:37', '2025-11-17 09:27:53', NULL),
(270, 'AS353', 'Alusteel coil', 'BGreen', 15, 2996.00, 1796.00, '0.18', 'alusteel', 'available', 5, '2025-11-11 10:05:16', '2025-11-17 10:46:12', NULL),
(271, 'AS354', 'Alusteel coil', 'TBlack', 12, 3310.00, 1610.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:06:07', '2025-11-17 09:29:04', NULL),
(272, 'AS355', 'Alusteel coil', 'TBlack', 12, 3258.00, 1582.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:06:45', '2025-11-17 09:30:09', NULL),
(273, 'AS356', 'Alusteel coil', 'TBlack', 12, 3342.00, 1629.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:07:26', '2025-11-17 09:30:55', NULL),
(275, 'AS358', 'Alusteel coil', 'TCRed', 13, 3380.00, 1649.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 10:09:20', '2025-12-12 11:28:34', NULL),
(276, 'AS359', 'Alusteel coil', 'TCRed', 13, 2734.00, 1329.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:10:32', '2025-11-17 09:32:45', NULL),
(277, 'AS360', 'Alusteel coil', 'TCRed', 13, 2746.00, 1334.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:11:20', '2025-11-17 09:33:25', NULL),
(278, 'AS361', 'Alusteel coil', 'TCRed', 13, 2844.00, 1396.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:11:55', '2025-11-17 09:33:56', NULL),
(279, 'AS362', 'Alusteel coil', 'SBlue', 11, 3172.00, 1535.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 10:12:41', '2025-12-12 11:23:45', NULL),
(280, 'AS363', 'Alusteel coil', 'SBlue', 11, 3200.00, 1551.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:13:28', '2025-11-17 09:35:03', NULL),
(281, 'AS364', 'Alusteel coil', 'SBlue', 11, 3162.00, 1535.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 10:14:13', '2025-12-12 11:35:20', NULL),
(282, 'AS365', 'Alusteel coil', 'SBlue', 11, 3142.00, 1532.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 10:15:52', '2025-12-12 11:31:58', NULL),
(283, 'AS366', 'Alusteel coil', 'SBlue', 11, 3248.00, 1583.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:16:32', '2025-11-17 09:37:03', NULL),
(284, 'AS367', 'Alusteel coil', 'IWhite', 16, 3246.00, 1560.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 10:17:14', '2025-12-12 11:36:40', NULL),
(285, 'AS368', 'Alusteel coil', 'IWhite', 16, 3276.00, 1576.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 10:18:03', '2025-12-12 11:37:06', NULL),
(286, 'AS369', 'Alusteel coil', 'IWhite', 16, 3360.00, 1630.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 10:18:50', '2025-12-12 11:36:19', NULL),
(287, 'AS370', 'Alusteel coil', 'IWhite', 16, 2714.00, 1319.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 10:19:40', '2025-12-03 09:00:10', NULL),
(288, 'AS371', 'Alusteel coil', 'IWhite', 16, 2722.00, 1321.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 10:20:24', '2025-12-12 11:29:27', NULL),
(289, 'AS372', 'Alusteel coil', 'IWhite', 16, 2840.00, 1397.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:21:07', '2025-11-17 09:41:55', NULL),
(290, 'AS373', 'Alusteel coil', 'IWhite', 16, 2968.00, 1432.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:21:47', '2025-11-17 09:42:37', NULL),
(291, 'AS374', 'Alusteel coil', 'IWhite', 16, 2936.00, 1418.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:22:32', '2025-11-17 09:43:17', NULL),
(292, 'AS375', 'Alusteel coil', 'IWhite', 16, 2976.00, 1445.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 10:23:53', '2025-12-12 11:24:09', NULL),
(293, 'AS376', 'Alusteel coil', 'GBeige', 14, 3440.00, 1676.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:24:54', '2025-11-17 09:44:41', NULL),
(294, 'AS377', 'Alusteel coil', 'GBeige', 14, 3302.00, 1612.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:31:06', '2025-12-12 11:28:12', NULL),
(295, 'AS378', 'Alusteel coil', 'GBeige', 14, 3436.00, 1665.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:31:50', '2025-12-12 11:27:48', NULL),
(296, 'AS379', 'Alusteel coil', 'GBeige', 14, 3310.00, 1601.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 13:32:23', '2025-11-17 09:46:42', NULL),
(297, 'AS380', 'Alusteel coil', 'GBeige', 14, 3286.00, 1599.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 13:33:10', '2025-11-17 09:47:30', NULL),
(298, 'AS381', 'Alusteel coil', 'GBeige', 14, 3098.00, 1509.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 13:34:05', '2025-11-17 09:48:42', NULL),
(299, 'AS382', 'Alusteel coil', 'BGreen', 15, 3134.00, 1514.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:34:54', '2025-12-12 11:16:09', NULL),
(300, 'AS383', 'Alusteel coil', 'BGreen', 15, 3246.00, 1575.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:35:35', '2025-12-12 11:34:58', NULL),
(301, 'AS385', 'Alusteel coil', 'BGreen', 15, 3124.00, 1514.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 13:36:24', '2025-11-17 09:51:00', NULL),
(302, 'AS387', 'Alusteel coil', 'TCRed', 13, 3198.00, 1312.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:38:00', '2025-11-17 10:20:25', NULL),
(303, 'AS388', 'Alusteel coil', 'TCRed', 13, 3190.00, 1308.00, '0.26', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:38:48', '2025-12-12 11:25:16', NULL),
(304, 'AS389', 'Alusteel coil', 'TCRed', 13, 3312.00, 1360.00, '0.26', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:39:32', '2025-12-12 11:27:23', NULL),
(305, 'AS390', 'Alusteel coil', 'TCRed', 13, 2910.00, 1194.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:40:11', '2025-11-17 10:23:28', NULL),
(306, 'AS391', 'Alusteel coil', 'TCRed', 13, 2978.00, 1222.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:40:49', '2025-11-17 10:24:01', NULL),
(307, 'AS392', 'Alusteel coil', 'IWhite', 16, 3202.00, 1313.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:41:35', '2025-11-17 10:24:30', NULL),
(308, 'AS393', 'Alusteel coil', 'IWhite', 16, 3202.00, 1310.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:42:23', '2025-11-17 10:25:10', NULL),
(309, 'AS394', 'Alusteel coil', 'IWhite', 16, 3350.00, 1374.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:43:02', '2025-11-17 10:25:38', NULL),
(310, 'AS395', 'Alusteel coil', 'IWhite', 16, 3202.00, 1312.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:44:28', '2025-11-17 10:26:11', NULL),
(311, 'AS396', 'Alusteel coil', 'IWhite', 16, 3208.00, 1310.00, '0.26', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:45:17', '2025-12-12 11:33:58', NULL),
(312, 'AS397', 'Alusteel coil', 'IWhite', 16, 3364.00, 1379.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:46:01', '2025-11-17 10:27:26', NULL),
(313, 'AS398', 'Alusteel coil', 'IWhite', 16, 2182.00, 889.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:46:46', '2025-11-17 10:27:58', NULL),
(314, 'AS399', 'Alusteel coil', 'IWhite', 16, 2316.00, 932.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:47:23', '2025-11-17 10:28:29', NULL),
(315, 'AS400', 'Alusteel coil', 'GBeige', 14, 3204.00, 1313.00, '0.26', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:48:55', '2025-12-12 11:34:13', NULL),
(316, 'AS401', 'Alusteel coil', 'GBeige', 14, 3202.00, 1312.00, '0.26', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:49:47', '2025-12-03 09:00:21', NULL),
(317, 'AS402', 'Alusteel coil', 'GBeige', 14, 3428.00, 1380.00, '0.26', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:50:34', '2025-12-12 11:30:50', NULL),
(318, 'AS403', 'Alusteel coil', 'GBeige', 14, 2994.00, 1229.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:51:15', '2025-11-17 10:30:41', NULL),
(319, 'AS404', 'Alusteel coil', 'GBeige', 14, 3196.00, 1319.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:51:50', '2025-11-17 10:47:20', NULL),
(320, 'AS405', 'Alusteel coil', 'GBeige', 14, 3206.00, 1317.00, '0.26', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:52:43', '2025-12-12 11:29:02', NULL),
(321, 'AS406', 'Alusteel coil', 'GBeige', 14, 3366.00, 1386.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:53:21', '2025-11-17 10:32:39', NULL),
(322, 'AS407', 'Alusteel coil', 'BGreen', 15, 3242.00, 1324.00, '0.26', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:54:07', '2025-12-12 11:32:59', NULL),
(323, 'AS408', 'Alusteel coil', 'BGreen', 15, 2998.00, 1220.00, '0.26', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:54:43', '2025-12-12 11:32:30', NULL),
(324, 'AS409', 'Alusteel coil', 'BGreen', 15, 3618.00, 1475.00, '0.26', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:55:24', '2025-12-12 11:30:13', NULL),
(325, 'K53', 'K Zinc coil', 'IWhite', 16, 2938.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:15:34', '2025-11-17 11:12:32', NULL),
(326, 'K54', 'K Zinc coil', 'IWhite', 16, 2880.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:16:24', '2025-11-17 11:13:14', NULL),
(327, 'K60', 'K Zinc coil', 'IWhite', 16, 2916.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:17:08', '2025-11-17 11:14:05', NULL),
(328, 'K63', 'K Zinc coil', 'IWhite', 16, 2920.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:17:45', '2025-11-17 11:14:40', NULL),
(329, 'K64', 'K Zinc coil', 'IWhite', 16, 2902.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:18:20', '2025-11-17 11:15:12', NULL),
(330, 'K65', 'K Zinc coil', 'IWhite', 16, 2940.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:18:52', '2025-11-17 11:15:44', NULL),
(331, 'K77', 'K Zinc coil', 'IWhite', 16, 2940.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:19:30', '2025-11-17 11:16:14', NULL),
(332, 'K78', 'K Zinc coil', 'IWhite', 16, 2910.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:20:09', '2025-11-17 11:16:47', NULL),
(333, 'K87', 'K Zinc coil', 'IBeige', 9, 2900.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:20:56', '2025-11-17 11:17:30', NULL),
(334, 'K88', 'K Zinc coil', 'IBeige', 9, 2900.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:21:36', '2025-11-17 11:18:06', NULL),
(335, 'K89', 'K Zinc coil', 'IBeige', 9, 2914.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:22:21', '2025-11-17 11:18:39', NULL),
(336, 'K91', 'K Zinc coil', 'GBeige', 14, 2904.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:23:53', '2025-11-17 11:19:26', NULL),
(337, 'K92', 'K Zinc coil', 'GBeige', 14, 2912.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:24:52', '2025-11-17 11:20:02', NULL),
(338, 'K96', 'K Zinc coil', 'GBeige', 14, 2924.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:25:34', '2025-11-17 11:21:46', NULL),
(339, 'K99', 'K Zinc coil', 'GBeige', 14, 2992.00, 1380.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:26:08', '2025-11-17 11:21:12', NULL),
(340, 'K102', 'K Zinc coil', 'SBlue', 11, 2844.00, 1350.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:26:51', '2025-11-17 11:22:47', NULL),
(341, 'K104', 'K Zinc coil', 'SBlue', 11, 2870.00, 1350.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:27:25', '2025-11-17 11:23:22', NULL),
(342, 'K146', 'K Zinc coil', 'IWhite', 16, 2742.00, 1800.00, '0.16', 'kzinc', 'available', 5, '2025-11-11 14:28:15', '2025-11-17 11:24:52', NULL),
(343, 'K161', 'K Zinc coil', 'IWhite', 16, 2159.00, 3505.00, '0.16', 'kzinc', 'available', 5, '2025-11-11 14:29:00', '2025-11-17 12:04:34', NULL),
(344, 'K237', 'K Zinc coil', 'SBlue', 11, 2926.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:31:07', '2025-11-17 11:39:10', NULL),
(345, 'K240', 'K Zinc coil', 'IBeige', 9, 2952.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:31:58', '2025-11-17 11:39:44', NULL),
(346, 'K242', 'K Zinc coil', 'IBeige', 9, 2940.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:32:39', '2025-11-17 11:40:16', NULL),
(347, 'K245', 'K Zinc coil', 'IBeige', 9, 2934.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:33:57', '2025-11-17 11:41:04', NULL),
(348, 'K248', 'K Zinc coil', 'SBlue', 11, 2930.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:35:02', '2025-11-17 11:46:01', NULL),
(349, 'K250', 'K Zinc coil', 'SBlue', 11, 2960.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:35:46', '2025-11-17 11:46:29', NULL),
(350, 'K251', 'K Zinc coil', 'SBlue', 11, 2924.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:36:26', '2025-11-17 11:46:59', NULL),
(351, 'K252', 'K Zinc coil', 'SBlue', 11, 2938.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:37:17', '2025-11-17 11:47:36', NULL),
(352, 'K253', 'K Zinc coil', 'SBlue', 11, 2942.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:37:54', '2025-11-17 11:48:14', NULL),
(353, 'K254', 'K Zinc coil', 'SBlue', 11, 2950.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:38:32', '2025-11-17 11:48:40', NULL),
(354, 'K262', 'K Zinc coil', 'BGreen', 15, 2954.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:39:17', '2025-11-17 11:49:17', NULL),
(355, 'K264', 'K Zinc coil', 'IBeige', 9, 2926.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:40:01', '2025-11-17 11:49:50', NULL),
(356, 'K265', 'K Zinc coil', 'IBeige', 9, 2946.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:40:45', '2025-11-17 11:50:29', NULL),
(357, 'K266', 'K Zinc coil', 'IBeige', 9, 2900.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:41:25', '2025-11-17 11:51:10', NULL),
(358, 'K272', 'K Zinc coil', 'IBeige', 9, 2936.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:44:14', '2025-11-17 11:51:41', NULL),
(359, 'K273', 'K Zinc coil', 'IBeige', 9, 2930.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:45:02', '2025-11-17 11:52:13', NULL),
(360, 'K278', 'K Zinc coil', 'TCRed', 13, 2910.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:45:47', '2025-11-17 11:52:56', NULL),
(361, 'K293', 'K Zinc coil', 'IBeige', 9, 2946.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:46:55', '2025-11-17 11:56:06', NULL),
(362, 'K294', 'K Zinc coil', 'IBeige', 9, 2930.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:47:33', '2025-11-17 11:56:36', NULL),
(363, 'K295', 'K Zinc coil', 'IBeige', 9, 3014.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:48:16', '2025-11-17 11:57:10', NULL),
(364, 'K297', 'K Zinc coil', 'IWhite', 16, 2960.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:48:59', '2025-11-17 11:57:49', NULL),
(365, 'K298', 'K Zinc coil', 'IWhite', 16, 2960.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:49:43', '2025-11-17 11:58:18', NULL),
(366, 'K299', 'K Zinc coil', 'IWhite', 16, 2960.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:50:26', '2025-11-17 11:58:55', NULL),
(367, 'K306', 'K Zinc coil', 'IWhite', 16, 2970.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:51:14', '2025-11-17 11:59:41', NULL),
(368, 'K307', 'K Zinc coil', 'IWhite', 16, 2992.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:51:48', '2025-11-17 12:00:11', NULL),
(369, 'K312', 'K Zinc coil', 'IWhite', 16, 2988.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:55:52', '2025-11-17 11:36:03', NULL),
(370, 'K313', 'K Zinc coil', 'IWhite', 16, 3046.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:56:28', '2025-11-17 11:35:35', NULL),
(371, 'K314', 'K Zinc coil', 'IWhite', 16, 2946.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:57:13', '2025-11-17 11:34:50', NULL),
(372, 'K315', 'K Zinc coil', 'IWhite', 16, 2956.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:58:12', '2025-11-17 11:34:15', NULL),
(373, 'K317', 'K Zinc coil', 'IWhite', 16, 3006.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:58:49', '2025-11-17 11:33:33', NULL),
(374, 'K318', 'K Zinc coil', 'IWhite', 16, 2972.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 14:59:43', '2025-11-17 11:32:57', NULL),
(375, 'K319', 'K Zinc coil', 'IWhite', 16, 2940.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:00:23', '2025-11-17 11:32:22', NULL),
(376, 'K320', 'K Zinc coil', 'IWhite', 16, 2990.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:00:57', '2025-11-17 11:31:42', NULL),
(377, 'K321', 'K Zinc coil', 'IWhite', 16, 2946.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:01:29', '2025-11-17 11:31:13', NULL),
(378, 'K322', 'K Zinc coil', 'IWhite', 16, 2976.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:02:20', '2025-11-17 11:30:37', NULL),
(379, 'K323', 'K Zinc coil', 'IWhite', 16, 2860.00, 1410.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:03:06', '2025-11-17 11:30:06', NULL),
(380, 'K324', 'K Zinc coil', 'IWhite', 16, 2816.00, 1395.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:03:53', '2025-11-17 11:29:26', NULL),
(381, 'K325', 'K Zinc coil', 'IWhite', 16, 2954.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:04:30', '2025-11-17 11:28:33', NULL),
(382, 'K326', 'K Zinc coil', 'IWhite', 16, 2960.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:05:13', '2025-11-17 11:04:45', NULL),
(383, 'K327', 'K Zinc coil', 'IWhite', 16, 2962.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:05:51', '2025-11-17 11:05:29', NULL),
(384, 'K337', 'K Zinc coil', 'IWhite', 16, 3030.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:10:46', '2025-11-17 11:06:20', NULL);
INSERT INTO `coils` (`id`, `code`, `name`, `color`, `color_id`, `net_weight`, `meters`, `gauge`, `category`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(385, 'K338', 'K Zinc coil', 'IWhite', 16, 2990.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:11:42', '2025-11-17 11:06:56', NULL),
(386, 'K339', 'K Zinc coil', 'IWhite', 16, 2996.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:12:37', '2025-11-17 11:26:45', NULL),
(387, 'K347', 'K Zinc coil', 'IWhite', 16, 3000.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:13:43', '2025-11-17 11:07:37', NULL),
(388, 'K348', 'K Zinc coil', 'IWhite', 16, 2972.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:14:23', '2025-11-17 11:08:16', NULL),
(389, 'K349', 'K Zinc coil', 'IWhite', 16, 2968.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:15:02', '2025-11-17 11:09:12', NULL),
(390, 'K350', 'K Zinc coil', 'IWhite', 16, 2960.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:15:45', '2025-11-17 11:09:45', NULL),
(391, 'K351', 'K Zinc coil', 'IWhite', 16, 2940.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-11 15:16:22', '2025-11-17 11:10:17', NULL),
(392, 'K379', 'K Zinc coil', '', 16, 2994.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:05:23', NULL, NULL),
(393, 'K383', 'K Zinc coil', '', 16, 3026.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:06:46', NULL, NULL),
(394, 'K384', 'K Zinc coil', '', 16, 2962.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:08:03', NULL, NULL),
(395, 'K385', 'K Zinc coil', '', 16, 2964.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:08:57', '2025-11-14 08:09:29', '2025-11-14 08:09:29'),
(396, 'K387', 'K Zinc coil', '', 16, 2992.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:10:33', NULL, NULL),
(397, 'K388', 'K Zinc coil', '', 16, 2994.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:11:45', NULL, NULL),
(398, 'K389', 'K Zinc coil', '', 16, 2860.00, 1830.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 08:13:10', NULL, NULL),
(399, 'K390', 'K Zinc coil', '', 16, 2816.00, 1830.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 08:14:22', NULL, NULL),
(400, 'K393', 'K Zinc coil', '', 16, 2858.00, 1830.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 08:15:28', NULL, NULL),
(401, 'K395', 'K Zinc coil', '', 16, 2844.00, 1830.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 08:16:30', NULL, NULL),
(402, 'K396', 'K Zinc coil', '', 16, 2832.00, 1830.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 08:17:35', NULL, NULL),
(403, 'K397', 'K Zinc coil', '', 16, 2862.00, 1830.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 08:18:37', NULL, NULL),
(404, 'K400', 'K Zinc coil', '', 16, 2814.00, 1830.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 08:19:55', NULL, NULL),
(405, 'K404', 'K Zinc coil', '', 16, 2948.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:21:19', NULL, NULL),
(406, 'K406', 'K Zinc coil', '', 16, 2968.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:22:55', NULL, NULL),
(407, 'K407', 'K Zinc coil', '', 16, 2966.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:23:57', NULL, NULL),
(408, 'K409', 'K Zinc coil', '', 16, 2956.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:24:56', NULL, NULL),
(409, 'K410', 'K Zinc coil', '', 16, 3038.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:26:04', NULL, NULL),
(410, 'K411', 'K Zinc coil', '', 16, 2972.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:26:55', NULL, NULL),
(411, 'K412', 'K Zinc coil', '', 16, 2956.00, 1470.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:29:10', NULL, NULL),
(412, 'K5', 'K Zinc coil', '', 13, 3212.00, 3698.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:30:27', NULL, NULL),
(413, 'K8', 'K Zinc coil', '', 11, 3186.00, 3630.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:31:20', NULL, NULL),
(414, 'K10', 'K Zinc coil', '', 12, 2578.00, 2975.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:32:16', NULL, NULL),
(415, 'K12', 'K Zinc coil', '', 11, 3212.00, 3688.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:33:17', NULL, NULL),
(416, 'K13', 'K Zinc coil', '', 16, 2232.00, 2547.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:34:08', NULL, NULL),
(417, 'K14', 'K Zinc coil', '', 16, 3226.00, 3694.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:35:17', NULL, NULL),
(418, 'K15', 'K Zinc coil', '', 16, 3320.00, 3818.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:36:46', NULL, NULL),
(419, 'K16', 'K Zinc coil', '', 16, 3646.00, 4258.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:38:07', NULL, NULL),
(420, 'K17', 'K Zinc coil', '', 16, 3232.00, 3704.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:39:18', NULL, NULL),
(421, 'K18', 'K Zinc coil', '', 16, 3226.00, 3773.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:40:07', '2025-11-14 08:40:21', NULL),
(422, 'K21', 'K Zinc coil', '', 16, 3232.00, 3806.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:41:25', NULL, NULL),
(423, 'K23', 'K Zinc coil', '', 16, 3210.00, 3764.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:42:28', NULL, NULL),
(424, 'K27', 'K Zinc coil', '', 14, 3314.00, 3839.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:43:29', NULL, NULL),
(425, 'K28', 'K Zinc coil', '', 18, 2738.00, 3171.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:45:06', NULL, NULL),
(426, 'K32', 'K Zinc coil', '', 14, 3234.00, 3727.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:46:16', NULL, NULL),
(427, 'K36', 'K Zinc coil', '', 14, 3290.00, 3794.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:47:13', NULL, NULL),
(428, 'K37', 'K Zinc coil', '', 15, 3076.00, 3564.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:48:12', '2025-11-14 08:48:26', NULL),
(429, 'K39', 'K Zinc coil', '', 14, 3266.00, 3765.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:49:38', NULL, NULL),
(430, 'K40', 'K Zinc coil', '', 14, 3278.00, 3748.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:50:35', NULL, NULL),
(431, 'K42', 'K Zinc coil', '', 15, 3270.00, 3840.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:51:37', NULL, NULL),
(432, 'K43', 'K Zinc coil', '', 15, 3056.00, 3357.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:52:30', NULL, NULL),
(433, 'K44', 'K Zinc coil', '', 15, 3128.00, 3644.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:53:19', NULL, NULL),
(434, 'K46', 'K Zinc coil', '', 18, 3366.00, 3876.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:54:29', NULL, NULL),
(435, 'K47', 'K Zinc coil', '', 18, 2676.00, 3080.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:55:13', '2025-11-14 08:56:30', NULL),
(436, 'K48', 'K Zinc coil', '', 18, 2646.00, 3052.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:56:03', NULL, NULL),
(437, 'K49', 'K Zinc coil', '', 16, 3262.00, 3746.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 08:57:44', '2025-11-14 09:02:06', NULL),
(438, 'K50', 'K Zinc coil', '', 11, 3262.00, 3749.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:01:27', NULL, NULL),
(439, 'Z1', 'K Zinc coil', '', 19, 2512.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:19:03', '2025-11-14 09:40:06', NULL),
(440, 'Z2', 'K Zinc coil', '', 19, 2522.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:19:59', '2025-11-14 09:39:33', NULL),
(441, 'Z3', 'K Zinc coil', '', 19, 2520.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:20:46', '2025-11-14 09:39:17', NULL),
(442, 'Z4', 'K Zinc coil', '', 19, 2518.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:21:38', '2025-11-14 09:39:00', NULL),
(443, 'Z5', 'K Zinc coil', '', 19, 2542.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:22:17', '2025-11-14 09:38:41', NULL),
(444, 'Z6', 'K Zinc coil', '', 19, 2454.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:22:59', '2025-11-14 09:38:20', NULL),
(445, 'Z7', 'K Zinc coil', '', 12, 2524.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:23:45', '2025-11-14 09:38:00', NULL),
(446, 'Z8', 'K Zinc coil', '', 12, 2516.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:24:34', '2025-11-14 09:37:37', NULL),
(447, 'Z9', 'K Zinc coil', '', 12, 2512.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:25:36', '2025-11-14 09:37:15', NULL),
(448, 'Z10', 'K Zinc coil', '', 12, 2508.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:26:25', '2025-11-14 09:36:58', NULL),
(449, 'Z11', 'K Zinc coil', '', 12, 2510.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:27:04', '2025-11-14 09:36:43', NULL),
(450, 'Z12', 'K Zinc coil', '', 12, 2514.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:27:56', '2025-11-14 09:36:10', NULL),
(451, 'Z13', 'K Zinc coil', '', 15, 2504.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:28:38', '2025-11-14 09:35:57', NULL),
(452, 'Z14', 'K Zinc coil', '', 15, 2516.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:29:30', '2025-11-14 09:35:42', NULL),
(453, 'Z15', 'K Zinc coil', '', 15, 2506.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:30:20', '2025-11-14 09:35:25', NULL),
(454, 'Z16', 'K Zinc coil', '', 15, 2502.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:30:56', '2025-11-14 09:35:12', NULL),
(455, 'Z17', 'K Zinc coil', '', 15, 2512.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:32:35', '2025-11-14 09:34:57', NULL),
(456, 'Z18', 'K Zinc coil', '', 15, 3002.00, 0.00, '0.2', 'kzinc', 'available', 5, '2025-11-14 09:33:40', '2025-11-14 09:34:37', NULL),
(457, 'Z19', 'K Zinc coil', '', 11, 2502.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:40:59', NULL, NULL),
(458, 'Z20', 'K Zinc coil', '', 11, 2386.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:41:45', NULL, NULL),
(459, 'Z21', 'K Zinc coil', '', 11, 2518.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:42:24', NULL, NULL),
(460, 'Z22', 'K Zinc coil', '', 11, 2526.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:43:06', NULL, NULL),
(461, 'Z23', 'K Zinc coil', '', 13, 2504.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:43:54', NULL, NULL),
(462, 'Z24', 'K Zinc coil', '', 13, 2520.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:44:30', NULL, NULL),
(463, 'Z25', 'K Zinc coil', '', 13, 2504.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:45:22', '2025-11-14 09:45:36', NULL),
(464, 'Z26', 'K Zinc coil', '', 13, 2230.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:46:23', NULL, NULL),
(465, 'Z27', 'K Zinc coil', '', 18, 2520.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:47:02', '2025-11-14 09:47:21', NULL),
(466, 'Z28', 'K Zinc coil', '', 18, 2494.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:48:19', '2025-11-14 09:48:29', NULL),
(467, 'Z29', 'K Zinc coil', '', 18, 2506.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:49:12', NULL, NULL),
(468, 'Z30', 'K Zinc coil', '', 18, 2256.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:49:57', NULL, NULL),
(469, 'Z31', 'K Zinc coil', '', 14, 2506.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:51:00', NULL, NULL),
(470, 'Z32', 'K Zinc coil', '', 14, 2524.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:51:41', NULL, NULL),
(471, 'Z33', 'K Zinc coil', '', 14, 2536.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:52:59', NULL, NULL),
(472, 'Z34', 'K Zinc coil', '', 14, 2516.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:53:35', NULL, NULL),
(473, 'Z35', 'K Zinc coil', '', 14, 2506.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:54:14', NULL, NULL),
(474, 'Z36', 'K Zinc coil', '', 14, 2508.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:54:55', NULL, NULL),
(475, 'Z37', 'K Zinc coil', '', 14, 2502.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:55:39', NULL, NULL),
(476, 'Z38', 'K Zinc coil', '', 14, 2540.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:56:18', NULL, NULL),
(477, 'Z39', 'K Zinc coil', '', 14, 2486.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:57:02', NULL, NULL),
(478, 'Z40', 'K Zinc coil', '', 14, 2536.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:57:42', NULL, NULL),
(479, 'Z41', 'K Zinc coil', '', 14, 2514.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:58:18', NULL, NULL),
(480, 'Z42', 'K Zinc coil', '', 14, 2518.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:58:52', NULL, NULL),
(481, 'Z43', 'K Zinc coil', '', 14, 2512.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 09:59:29', NULL, NULL),
(482, 'Z44', 'K Zinc coil', '', 14, 2520.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:00:02', NULL, NULL),
(483, 'Z45', 'K Zinc coil', '', 14, 2504.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:02:54', NULL, NULL),
(484, 'Z46', 'K Zinc coil', '', 14, 2536.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:03:38', NULL, NULL),
(485, 'Z47', 'K Zinc coil', '', 14, 2528.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:04:18', NULL, NULL),
(486, 'Z48', 'K Zinc coil', '', 14, 2388.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:04:58', NULL, NULL),
(487, 'Z49', 'K Zinc coil', '', 16, 2538.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:05:50', NULL, NULL),
(488, 'Z50', 'K Zinc coil', '', 16, 2604.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:06:27', NULL, NULL),
(489, 'Z51', 'K Zinc coil', '', 16, 2540.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:07:11', NULL, NULL),
(490, 'Z52', 'K Zinc coil', '', 16, 2554.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:08:02', NULL, NULL),
(491, 'Z53', 'K Zinc coil', '', 16, 2522.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:08:53', NULL, NULL),
(492, 'Z54', 'K Zinc coil', '', 16, 2552.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:09:37', NULL, NULL),
(493, 'Z55', 'K Zinc coil', '', 16, 2562.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:10:28', NULL, NULL),
(494, 'Z56', 'K Zinc coil', '', 16, 2554.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:11:03', NULL, NULL),
(495, 'Z57', 'K Zinc coil', '', 16, 2540.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:12:39', NULL, NULL),
(496, 'Z58', 'K Zinc coil', '', 16, 2524.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:13:23', NULL, NULL),
(497, 'Z59', 'K Zinc coil', '', 16, 2530.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:14:16', NULL, NULL),
(498, 'Z60', 'K Zinc coil', '', 16, 2554.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:15:00', NULL, NULL),
(499, 'Z61', 'K Zinc coil', '', 16, 2570.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:15:45', NULL, NULL),
(500, 'Z62', 'K Zinc coil', '', 16, 2526.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:16:23', NULL, NULL),
(501, 'Z63', 'K Zinc coil', '', 16, 2536.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:17:01', NULL, NULL),
(502, 'Z64', 'K Zinc coil', '', 16, 2556.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:17:39', NULL, NULL),
(503, 'Z65', 'K Zinc coil', '', 16, 2556.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:18:18', NULL, NULL),
(504, 'Z66', 'K Zinc coil', '', 16, 2570.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:18:52', NULL, NULL),
(505, 'Z67', 'K Zinc coil', '', 16, 2536.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:19:34', NULL, NULL),
(506, 'Z68', 'K Zinc coil', '', 16, 2546.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:20:16', NULL, NULL),
(507, 'Z69', 'K Zinc coil', '', 16, 2538.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:21:03', NULL, NULL),
(508, 'Z70', 'K Zinc coil', '', 16, 2578.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:23:08', NULL, NULL),
(509, 'Z71', 'K Zinc coil', '', 16, 2558.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:23:48', NULL, NULL),
(510, 'Z72', 'K Zinc coil', '', 16, 2322.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:24:28', NULL, NULL),
(511, 'Z73', 'K Zinc coil', '', 16, 2514.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:25:01', '2025-11-14 10:26:41', NULL),
(512, 'Z74', 'K Zinc coil', '', 16, 2526.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:25:38', '2025-11-14 10:26:25', NULL),
(513, 'Z75', 'K Zinc coil', '', 16, 2486.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:27:30', NULL, NULL),
(514, 'Z76', 'K Zinc coil', '', 16, 2526.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:28:06', NULL, NULL),
(515, 'Z77', 'K Zinc coil', '', 16, 2512.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:28:42', NULL, NULL),
(516, 'Z78', 'K Zinc coil', '', 16, 2516.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:29:33', NULL, NULL),
(517, 'Z79', 'K Zinc coil', '', 16, 2560.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:30:14', NULL, NULL),
(518, 'Z80', 'K Zinc coil', '', 16, 2482.00, 0.00, '0.20', 'kzinc', 'available', 5, '2025-11-14 10:30:53', NULL, NULL),
(519, 'Z81', 'K Zinc coil', '', 16, 2504.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:31:30', NULL, NULL),
(520, 'Z82', 'K Zinc coil', '', 16, 2504.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:32:06', NULL, NULL),
(521, 'Z83', 'K Zinc coil', '', 16, 2496.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:32:48', NULL, NULL),
(522, 'Z84', 'K Zinc coil', '', 16, 2544.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:33:23', NULL, NULL),
(523, 'Z85', 'K Zinc coil', '', 16, 2494.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:34:05', NULL, NULL),
(524, 'Z86', 'K Zinc coil', '', 16, 2544.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:34:47', NULL, NULL),
(525, 'Z87', 'K Zinc coil', '', 16, 2502.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:35:25', NULL, NULL),
(526, 'Z88', 'K Zinc coil', '', 16, 2496.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:36:05', NULL, NULL),
(527, 'Z89', 'K Zinc coil', '', 16, 2500.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:36:49', NULL, NULL),
(528, 'Z90', 'K Zinc coil', '', 16, 2504.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:37:40', NULL, NULL),
(529, 'Z91', 'K Zinc coil', '', 16, 2502.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:38:42', NULL, NULL),
(530, 'Z92', 'K Zinc coil', '', 16, 2510.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:39:21', NULL, NULL),
(531, 'Z93', 'K Zinc coil', '', 16, 2500.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:39:50', NULL, NULL),
(532, 'Z94', 'K Zinc coil', '', 16, 2512.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:40:28', NULL, NULL),
(533, 'Z95', 'K Zinc coil', '', 16, 2832.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:41:25', NULL, NULL),
(534, 'Z96', 'K Zinc coil', '', 16, 2790.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:42:10', NULL, NULL),
(535, 'Z97', 'K Zinc coil', '', 15, 2464.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:42:50', '2025-11-14 10:43:58', NULL),
(536, 'Z98', 'K Zinc coil', '', 15, 2460.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:43:34', NULL, NULL),
(537, 'Z99', 'K Zinc coil', '', 15, 2460.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:44:40', NULL, NULL),
(538, 'Z100', 'K Zinc coil', '', 15, 2466.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:45:25', NULL, NULL),
(539, 'Z101', 'K Zinc coil', '', 15, 2892.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:46:04', NULL, NULL),
(540, 'Z102', 'K Zinc coil', '', 15, 2896.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:46:50', NULL, NULL),
(541, 'Z103', 'K Zinc coil', '', 14, 2464.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:47:36', NULL, NULL),
(542, 'Z104', 'K Zinc coil', '', 14, 2464.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:48:17', NULL, NULL),
(543, 'Z105', 'K Zinc coil', '', 14, 2454.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:48:55', NULL, NULL),
(544, 'Z106', 'K Zinc coil', '', 14, 2438.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:49:28', NULL, NULL),
(545, 'Z107', 'K Zinc coil', '', 14, 2442.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:50:03', NULL, NULL),
(546, 'Z108', 'K Zinc coil', '', 14, 2458.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:50:42', NULL, NULL),
(547, 'Z109', 'K Zinc coil', '', 14, 2446.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:51:36', NULL, NULL),
(548, 'Z110', 'K Zinc coil', '', 14, 2446.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:52:14', NULL, NULL),
(549, 'Z111', 'K Zinc coil', '', 14, 2470.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:52:51', NULL, NULL),
(550, 'Z112', 'K Zinc coil', '', 14, 2470.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:53:27', NULL, NULL),
(551, 'Z113', 'K Zinc coil', '', 14, 2458.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:54:01', NULL, NULL),
(552, 'Z114', 'K Zinc coil', '', 14, 2456.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:54:36', NULL, NULL),
(553, 'Z115', 'K Zinc coil', '', 14, 2458.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:55:17', NULL, NULL),
(554, 'Z116', 'K Zinc coil', '', 14, 2472.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:55:56', NULL, NULL),
(555, 'Z117', 'K Zinc coil', '', 14, 2454.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 10:57:03', NULL, NULL),
(556, 'Z118', 'K Zinc coil', '', 14, 2444.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 11:59:12', NULL, NULL),
(557, 'Z119', 'K Zinc coil', '', 14, 2456.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 11:59:44', NULL, NULL),
(558, 'Z120', 'K Zinc coil', '', 14, 2462.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 12:00:25', NULL, NULL),
(559, 'Z121', 'K Zinc coil', '', 14, 2474.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 12:01:02', NULL, NULL),
(560, 'Z122', 'K Zinc coil', '', 14, 1814.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 12:01:37', NULL, NULL),
(561, 'Z123', 'K Zinc coil', '', 16, 2990.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 12:02:26', NULL, NULL),
(562, 'Z124', 'K Zinc coil', '', 16, 3010.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 12:03:15', NULL, NULL),
(563, 'Z126', 'K Zinc coil', '', 16, 3012.00, 0.00, '0.15', 'kzinc', 'available', 5, '2025-11-14 12:04:00', NULL, NULL),
(564, 'AS274', 'Alusteel coil', '', 18, 3154.00, 2141.00, '0.16', 'alusteel', 'available', 5, '2025-11-14 12:06:54', '2025-11-17 09:11:28', NULL),
(565, 'AS223', 'Alusteel coil', '', 18, 3468.00, 2304.00, '0.16', 'alusteel', 'available', 5, '2025-11-14 12:09:07', NULL, NULL),
(566, 'AS224', 'Alusteel coil', '', 18, 3500.00, 2331.00, '0.16', 'alusteel', 'available', 5, '2025-11-14 12:10:05', NULL, NULL),
(567, 'AS226', 'Alusteel coil', '', 18, 3252.00, 2163.00, '0.16', 'alusteel', 'available', 5, '2025-11-14 12:11:04', '2025-11-14 12:12:42', NULL),
(568, 'AS227', 'Alusteel coil', '', 18, 3474.00, 2322.00, '0.16', 'alusteel', 'out_of_stock', 5, '2025-11-14 12:11:45', '2025-12-12 11:17:41', NULL),
(569, 'AS345', 'Alusteel coil', '', 14, 3350.00, 1995.00, '0.18', 'alusteel', 'out_of_stock', 5, '2025-11-17 09:22:28', '2025-11-19 08:40:19', NULL),
(570, 'K244', 'K Zinc coil', '', 18, 2912.00, 1455.00, '0.20', 'alusteel', 'available', 5, '2025-11-17 11:42:12', '2025-11-19 13:37:35', '2025-11-19 13:37:35'),
(571, 'K229', 'K Zinc coil', '', 18, 2912.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-17 11:43:01', NULL, NULL),
(572, 'K227', 'K Zinc coil', '', 18, 2914.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-17 11:43:56', NULL, NULL),
(573, 'K225', 'K Zinc coil', '', 18, 2908.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-17 11:45:00', NULL, NULL),
(574, 'K282', 'K Zinc coil', '', 18, 2940.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-17 11:54:12', NULL, NULL),
(575, 'K283', 'K Zinc coil', '', 18, 2944.00, 1455.00, '0.20', 'kzinc', 'available', 5, '2025-11-17 11:55:28', NULL, NULL),
(576, 'A1', 'Aluminium coil', '', 9, 1845.00, 1180.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:21:29', NULL, NULL),
(577, 'A2', 'Aluminium coil', '', 9, 1804.00, 1155.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:22:24', NULL, NULL),
(578, 'A3', 'Aluminium coil', '', 9, 1782.00, 1220.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:23:31', NULL, NULL),
(579, 'A4', 'Aluminium coil', '', 9, 1805.00, 1226.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:24:36', NULL, NULL),
(580, 'A5', 'Aluminium coil', '', 14, 1792.00, 1147.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:25:55', NULL, NULL),
(581, 'A6', 'Aluminium coil', '', 14, 1899.00, 1225.00, '0.55', 'aluminum', 'out_of_stock', 5, '2025-11-25 12:26:50', '2025-12-12 11:38:12', NULL),
(582, 'A7', 'Aluminium coil', '', 14, 1849.00, 1272.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:27:42', NULL, NULL),
(583, 'A8', 'Aluminium coil', '', 13, 1772.00, 1211.00, '0.55', 'aluminum', 'out_of_stock', 5, '2025-11-25 12:28:29', '2025-12-12 11:38:32', NULL),
(584, 'A9', 'Aluminium coil', '', 13, 1777.00, 1215.00, '0.55', 'aluminum', 'out_of_stock', 5, '2025-11-25 12:29:12', '2025-12-12 11:38:58', NULL),
(585, 'A10', 'Aluminium coil', '', 13, 1911.00, 0.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:29:46', NULL, NULL),
(586, 'A11', 'Aluminium coil', '', 12, 1898.00, 1220.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:30:30', NULL, NULL),
(587, 'A12', 'Aluminium coil', '', 15, 1823.00, 1255.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:31:21', NULL, NULL),
(588, 'A13', 'Aluminium coil', '', 15, 1870.00, 1195.00, '0.55', 'aluminum', 'out_of_stock', 5, '2025-11-25 12:31:59', '2025-12-12 11:40:27', NULL),
(589, 'A14', 'Aluminium coil', '', 15, 1870.00, 1195.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:32:53', NULL, NULL),
(590, 'A15', 'Aluminium coil', '', 15, 1817.00, 1160.00, '0.5', 'aluminum', 'out_of_stock', 5, '2025-11-25 12:33:41', '2025-12-12 11:37:33', NULL),
(591, 'A16', 'Aluminium coil', '', 9, 1844.00, 1178.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:34:34', NULL, NULL),
(592, 'A17', 'Aluminium coil', '', 9, 1811.00, 1158.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:35:23', NULL, NULL),
(593, 'A18', 'Aluminium coil', '', 9, 1792.00, 1220.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:36:02', NULL, NULL),
(594, 'A19', 'Aluminium coil', '', 14, 1910.00, 1232.00, '0.55', 'aluminum', 'out_of_stock', 5, '2025-11-25 12:36:57', '2025-12-12 11:39:14', NULL),
(595, 'A20', 'Aluminium coil', '', 14, 1945.00, 1330.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:37:38', NULL, NULL),
(596, 'A21', 'Aluminium coil', '', 14, 1853.00, 1275.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:38:33', NULL, NULL),
(597, 'A22', 'Aluminium coil', '', 13, 1770.00, 1211.00, '0.55', 'aluminum', 'out_of_stock', 5, '2025-11-25 12:39:46', '2025-12-12 11:39:48', NULL),
(598, 'A23', 'Aluminium coil', '', 13, 1923.00, 1225.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:40:27', NULL, NULL),
(599, 'A24', 'Aluminium coil', '', 13, 1728.00, 1108.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:41:10', NULL, NULL),
(600, 'A25', 'Aluminium coil', '', 12, 1897.00, 1220.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:42:02', NULL, NULL),
(601, 'A26', 'Aluminium coil', '', 12, 1903.00, 1222.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:42:49', NULL, NULL),
(602, 'A27', 'Aluminium coil', '', 15, 1813.00, 1158.00, '0.55', 'aluminum', 'out_of_stock', 5, '2025-11-25 12:43:33', '2025-12-12 11:37:52', NULL),
(603, 'A28', 'Aluminium coil', '', 11, 1768.00, 1214.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:44:20', NULL, NULL),
(604, 'A29', 'Aluminium coil', '', 11, 1776.00, 1220.00, '0.55', 'aluminum', 'out_of_stock', 5, '2025-11-25 12:44:59', '2025-12-12 11:40:11', NULL),
(605, 'A30', 'Aluminium coil', '', 11, 1784.00, 1222.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:45:36', NULL, NULL),
(606, 'A31', 'Aluminium coil', '', 14, 1998.00, 1290.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:46:23', NULL, NULL),
(607, 'A32', 'Aluminium coil', '', 12, 2086.00, 2246.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:47:09', NULL, NULL),
(608, 'A33', 'Aluminium coil', '', 18, 2190.00, 2351.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:48:00', NULL, NULL),
(609, 'A34', 'Aluminium coil', '', 18, 2192.00, 2353.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:49:01', NULL, NULL),
(610, 'A35', 'Aluminium coil', '', 11, 2138.00, 2291.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:49:46', NULL, NULL),
(611, 'A36', 'Aluminium coil', '', 11, 2105.00, 2262.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:50:34', NULL, NULL),
(612, 'A37', 'Aluminium coil', '', 11, 2112.00, 2267.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:51:21', NULL, NULL),
(613, 'A38', 'Aluminium coil', '', 15, 2087.00, 1750.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:52:09', NULL, NULL),
(614, 'A39', 'Aluminium coil', '', 15, 2086.00, 1750.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:56:17', NULL, NULL),
(615, 'A40', 'Aluminium coil', '', 15, 2169.00, 2331.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:57:07', NULL, NULL),
(616, 'A41', 'Aluminium coil', '', 15, 2183.00, 2341.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:57:57', NULL, NULL),
(617, 'A42', 'Aluminium coil', '', 9, 2102.00, 1759.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:59:07', NULL, NULL),
(618, 'A43', 'Aluminium coil', '', 9, 2114.00, 1764.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:59:59', NULL, NULL),
(619, 'A44', 'Aluminium coil', '', 15, 1971.00, 1347.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:00:41', NULL, NULL),
(620, 'A45', 'Aluminium coil', '', 15, 1833.00, 1262.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:01:25', NULL, NULL),
(621, 'A46', 'Aluminium coil', '', 12, 2044.00, 2203.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:02:13', NULL, NULL),
(622, 'A47', 'Aluminium coil', '', 12, 2045.00, 2203.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:03:41', NULL, NULL),
(623, 'A48', 'Aluminium coil', '', 9, 1787.00, 1907.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:04:32', NULL, NULL),
(624, 'A49', 'Aluminium coil', '', 9, 1788.00, 1908.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:05:21', NULL, NULL),
(625, 'A50', 'Aluminium coil', '', 9, 1773.00, 1898.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:06:05', NULL, NULL),
(626, 'A51', 'Aluminium coil', '', 14, 2021.00, 1697.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:06:55', NULL, NULL),
(627, 'A52', 'Aluminium coil', '', 14, 2029.00, 1702.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:07:45', NULL, NULL),
(628, 'A53', 'Aluminium coil', '', 13, 2071.00, 2219.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:08:53', NULL, NULL),
(629, 'A54', 'Aluminium coil', '', 13, 2085.00, 1747.00, '0.55', 'kzinc', 'available', 5, '2025-11-25 13:48:09', '2025-12-11 12:08:52', NULL),
(630, 'A55', 'Aluminium coil', '', 13, 2080.00, 1745.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:48:58', NULL, NULL),
(631, 'A56', 'Aluminium coil', '', 13, 2079.00, 2225.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:49:39', NULL, NULL),
(632, 'A57', 'Aluminium coil', '', 13, 1914.00, 2054.00, '0.55', 'aluminum', 'out_of_stock', 5, '2025-11-25 13:50:21', '2025-12-03 08:59:58', NULL),
(633, 'D33', 'Aluminium coil', '', 12, 2147.00, 1417.90, '0.55', 'aluminum', 'available', 5, '2025-12-03 09:11:39', NULL, NULL),
(634, 'A295', 'Aluminium coil', '', 11, 2117.00, 1166.37, '0.45', 'aluminum', 'available', 5, '2025-12-03 13:59:26', NULL, NULL),
(635, 'D21', 'Aluminium coil', '', 9, 1620.00, 1588.30, '0.45', 'aluminum', 'available', 5, '2025-12-03 14:02:03', NULL, NULL),
(636, 'D47', 'Aluminium coil', '', 14, 1642.00, 1781.00, '0.45', 'aluminum', 'available', 5, '2025-12-03 14:04:42', NULL, NULL),
(637, 'D191', 'Aluminium coil', '', 12, 1711.00, 959.26, '0.45', 'aluminum', 'available', 5, '2025-12-03 14:05:57', NULL, NULL),
(638, 'B72', 'Aluminium coil', '', 15, 2128.00, 637.55, '0.45', 'aluminum', 'available', 5, '2025-12-03 14:07:43', NULL, NULL),
(639, 'D185', 'Aluminium coil', '', 13, 2027.00, 950.58, '0.45', 'aluminum', 'available', 5, '2025-12-03 14:09:45', NULL, NULL),
(640, 'D189', 'Aluminium coil', '', 18, 2219.00, 2672.40, '0.45', 'aluminum', 'available', 5, '2025-12-03 14:12:08', NULL, NULL),
(641, 'D262', 'Aluminium coil', '', 11, 2015.00, 1992.60, '0.55', 'aluminum', 'available', 5, '2025-12-03 14:14:30', NULL, NULL),
(642, 'B211', 'Aluminium coil', '', 11, 1735.00, 77.50, '0.55', 'aluminum', 'available', 5, '2025-12-03 14:16:14', NULL, NULL),
(643, 'D258', 'Aluminium coil', '', 9, 2021.00, 568.43, '0.55', 'aluminum', 'available', 5, '2025-12-03 14:20:57', NULL, NULL),
(644, 'D68', 'Aluminium coil', '', 15, 2040.00, 1595.20, '0.55', 'aluminum', 'available', 5, '2025-12-03 14:23:55', NULL, NULL),
(645, 'B88', 'Aluminium coil', '', 18, 2134.00, 130.23, '0.55', 'aluminum', 'available', 5, '2025-12-03 14:25:50', '2025-12-03 14:27:52', NULL),
(646, 'B102', 'Aluminium coil', '', 13, 1975.00, 87.64, '0.55', 'aluminum', 'available', 5, '2025-12-03 14:28:51', NULL, NULL),
(647, 'AS73', 'Alusteel coil', '', 12, 3586.00, 3219.58, '0.20', 'alusteel', 'available', 5, '2025-12-03 14:32:28', NULL, NULL),
(648, 'AS228', 'Alusteel coil', '', 18, 3446.00, 1852.50, '0.16', 'alusteel', 'available', 5, '2025-12-03 14:38:17', NULL, NULL),
(649, 'AS52', 'Alusteel coil', '', 13, 3744.00, 894.39, '0.20', 'alusteel', 'available', 5, '2025-12-03 14:42:33', NULL, NULL),
(652, 'AS64', 'Alusteel coil', '', 15, 3392.00, 289.30, '0.20', 'alusteel', 'available', 5, '2025-12-03 14:47:58', NULL, NULL),
(654, 'AS132', 'Alusteel coil', '', 15, 3066.00, 442.92, '0.16', 'alusteel', 'available', 5, '2025-12-03 14:58:56', NULL, NULL),
(655, 'AS298', 'Alusteel coil', '', 15, 1239.60, 3286.00, '0.24', 'alusteel', 'available', 5, '2025-12-03 15:01:48', NULL, NULL),
(656, 'AS281', 'Alusteel coil', '', 16, 2566.00, 707.00, '0.16', 'alusteel', 'available', 5, '2025-12-03 15:06:47', '2025-12-12 10:38:46', NULL),
(657, 'AS267', 'Alusteel coil', '', 16, 708.62, 3056.00, '0.28', 'alusteel', 'available', 5, '2025-12-03 15:08:58', '2025-12-13 10:01:33', NULL),
(658, 'AS289', 'Alusteel coil', '', 14, 2688.00, 956.90, '0.20', 'alusteel', 'available', 5, '2025-12-03 15:11:51', NULL, NULL),
(659, 'AS232', 'Alusteel coil', '', 14, 3340.00, 1936.33, '0.16', 'alusteel', 'available', 5, '2025-12-03 15:13:38', NULL, NULL),
(660, 'AS258', 'Alusteel coil', '', 14, 3298.00, 1010.50, '0.24', 'alusteel', 'available', 5, '2025-12-03 15:15:22', '2025-12-12 10:37:07', NULL),
(665, 'AS128', 'Alusteel coil', '', 12, 3054.00, 528.45, '0.16', 'alusteel', 'available', 5, '2025-12-08 12:04:52', '2025-12-12 10:36:02', NULL),
(671, 'AS259', 'Alusteel coil', '', 14, 3300.00, 1472.00, '0.24', 'alusteel', 'out_of_stock', 5, '2025-12-09 11:19:07', '2025-12-11 12:32:08', NULL),
(672, 'AS256', 'Alusteel coil', '', 14, 3302.00, 1474.00, '0.24', 'alusteel', 'out_of_stock', 5, '2025-12-09 12:26:18', '2025-12-11 12:31:55', NULL);

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
(9, 'IBeige', 'I/Beige', '#F5F0E1', 1, 5, '2025-11-11 20:27:37', '2025-11-12 06:58:56', NULL),
(10, 'PGreen', 'P/Green', '#008040', 1, 5, '2025-11-11 20:27:37', '2025-11-12 11:13:27', NULL),
(11, 'SBlue', 'S/Blue', '#C4D8E2', 1, 5, '2025-11-11 20:27:37', '2025-11-12 06:59:58', NULL),
(12, 'TBlack', 'T/Black', '#110C18', 1, 5, '2025-11-11 20:27:37', '2025-11-12 11:13:05', NULL),
(13, 'TCRed', 'TC/Red', NULL, 1, 5, '2025-11-11 20:27:37', '2025-11-11 20:50:09', NULL),
(14, 'GBeige', 'G/Beige', '#BEB6A6', 1, 5, '2025-11-11 20:27:37', '2025-11-12 06:59:19', NULL),
(15, 'BGreen', 'B/Green', '#009688', 1, 5, '2025-11-11 20:27:37', '2025-11-12 06:59:36', NULL),
(16, 'IWhite', 'I/White', '#FFFFF0', 1, 5, '2025-11-11 20:27:37', '2025-11-12 06:58:08', NULL),
(17, 'STest', 'S/Test', NULL, 0, 5, '2025-11-11 20:51:24', '2025-11-11 21:10:40', '2025-11-11 21:10:40'),
(18, 'N/Brown', 'N/brown', NULL, 1, 5, '2025-11-13 15:54:15', '2025-11-13 15:54:44', NULL),
(19, 'AL/Skin', 'AL/Skin', NULL, 1, 5, '2025-11-14 09:13:33', '2025-11-14 09:17:30', NULL),
(20, 'BLACK SHINGLE 01', 'BLACK SHINGLE', NULL, 1, 5, '2025-12-02 11:54:24', NULL, NULL),
(21, 'CBS_03', 'Claret black shingle', NULL, 1, 5, '2025-12-02 11:56:03', NULL, NULL),
(22, 'CB_02', 'Claret Shingle', NULL, 1, 5, '2025-12-02 11:57:01', NULL, NULL),
(23, 'GS_04', 'Green shingle', NULL, 1, 5, '2025-12-02 11:57:29', NULL, NULL),
(24, 'CBBPS_06', 'Claret brown &amp;black patch shingle', NULL, 1, 5, '2025-12-02 11:59:55', NULL, NULL),
(25, 'BRPS_07', 'Black and Red patch Shingle', NULL, 1, 5, '2025-12-02 12:00:42', NULL, NULL),
(26, 'BM_01', 'Black Milano', NULL, 1, 5, '2025-12-02 12:01:25', NULL, NULL),
(27, 'CBM_02', 'Claret Black Milano', NULL, 1, 5, '2025-12-02 12:02:32', NULL, NULL),
(28, 'CM_03', 'Claret Milano', NULL, 1, 5, '2025-12-02 12:03:05', NULL, NULL),
(29, 'BM_04', 'Blue Milano', NULL, 1, 5, '2025-12-02 12:03:47', NULL, NULL),
(30, 'GM_05', 'Green Milano', NULL, 1, 5, '2025-12-02 12:04:20', NULL, NULL),
(31, 'CBM__6', 'Claret Brown Milano', NULL, 1, 5, '2025-12-02 12:05:15', NULL, NULL),
(32, 'CBBM_07', 'Claret Brown Black Milano', NULL, 1, 5, '2025-12-02 12:06:05', NULL, NULL),
(33, 'BB_01', 'Black Bond', NULL, 1, 5, '2025-12-02 12:06:50', NULL, NULL),
(34, 'GB_03', 'Green Bond', NULL, 1, 5, '2025-12-02 12:07:37', NULL, NULL),
(35, 'BBD_04', 'Blue bond', NULL, 1, 5, '2025-12-02 12:08:04', NULL, NULL),
(36, 'cbbd', 'Claret Brown Bond', NULL, 1, 5, '2025-12-02 12:10:05', NULL, NULL),
(37, 'RBD_06', 'Red Bond', NULL, 1, 5, '2025-12-02 12:10:33', NULL, NULL),
(38, 'BC_01', 'Black Classic', NULL, 1, 5, '2025-12-02 12:11:19', NULL, NULL),
(39, 'CBC_02', 'Claret Brown Classic', NULL, 1, 5, '2025-12-02 12:12:23', NULL, NULL),
(40, 'GC_03', 'Green Classic', NULL, 1, 5, '2025-12-02 12:12:59', NULL, NULL),
(41, 'BCH_01', 'Black Check', NULL, 1, 5, '2025-12-02 12:13:42', NULL, NULL),
(42, 'CCK_02', 'Claret Check', NULL, 1, 5, '2025-12-02 12:14:27', NULL, NULL),
(43, 'BRM_01', 'Black Romania', NULL, 1, 5, '2025-12-02 12:15:13', NULL, NULL),
(44, 'CRM_02', 'Claret Romania', NULL, 1, 5, '2025-12-02 12:15:44', NULL, NULL),
(45, 'CBRM_04', 'claret and brown romania', NULL, 1, 5, '2025-12-02 12:19:09', NULL, NULL);

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
(1, 'Mr Lawal', 'customer1@example.com', '09039988198', 'NO. 4C, ZONE D, MILLIONAIRES QUARTERS, BYAZHIN, KUBWA, ABUJA', 'HEXA', 3, '2025-11-05 09:34:04', NULL, NULL),
(3, 'Stevo Aluminium', 'stevoaluminum@gmail.com', '08032218808', NULL, 'Stevo Aluminium IG LTD', 5, '2025-11-08 12:56:42', '2025-11-08 13:11:09', NULL),
(4, 'Usman Madugu', 'omale.ochigbo@obumek360.app', '07071132619', 'Kaduna State', 'Usman Aluminium Ltd', 5, '2025-11-17 12:31:01', '2025-12-13 10:07:19', '2025-12-13 10:07:19'),
(5, 'Adams', 'adamjames@yahoo.com', '08055084883', 'Kubwa Abuja', 'Adams Aluminium Ltd', 5, '2025-11-19 08:34:00', NULL, NULL),
(6, 'Elizabeth Enehe', NULL, '08098434014', NULL, NULL, 5, '2025-12-03 08:50:50', NULL, NULL),
(7, 'MONDAY JOB', NULL, '08098434014', NULL, NULL, 5, '2025-12-03 09:14:01', NULL, NULL),
(8, 'Murna Solomo', NULL, '08098434014', NULL, NULL, 5, '2025-12-03 11:13:08', NULL, NULL),
(9, 'Austine Ekeh', NULL, '08098434014', NULL, NULL, 5, '2025-12-03 11:19:44', NULL, NULL),
(10, 'Sunny Tech', NULL, '08098434014', NULL, NULL, 5, '2025-12-03 11:29:35', NULL, NULL),
(11, 'Joshua', NULL, '08098434014', NULL, NULL, 5, '2025-12-03 11:37:29', NULL, NULL),
(12, 'Sincere / Nevertheless', NULL, '08098434014', NULL, NULL, 5, '2025-12-03 11:47:06', NULL, NULL),
(13, 'Sunday Ekweonu', NULL, '08098434014', NULL, NULL, 5, '2025-12-03 11:56:01', NULL, NULL),
(14, 'Lijian Dery', NULL, '08098434014', NULL, NULL, 5, '2025-12-03 12:00:14', NULL, NULL),
(15, 'Skytech Alum', NULL, '08098434014', NULL, NULL, 5, '2025-12-03 12:06:26', NULL, NULL),
(16, 'Ibrahimama Alum', NULL, '08098434014', NULL, NULL, 5, '2025-12-03 12:19:44', NULL, NULL),
(17, 'Emmauel', NULL, '08098434014', NULL, NULL, 5, '2025-12-08 11:17:19', NULL, NULL),
(18, 'Stevoo Alum', NULL, '08098434014', NULL, NULL, 5, '2025-12-08 11:27:15', NULL, NULL),
(19, 'Abdulazeez Adamu', NULL, '08098434014', NULL, NULL, 5, '2025-12-08 11:48:53', NULL, NULL),
(20, 'Aminu Haruna', NULL, '08098434014', NULL, NULL, 5, '2025-12-08 11:56:27', NULL, NULL),
(21, 'Abba', NULL, '08098434014', NULL, NULL, 5, '2025-12-08 12:09:51', NULL, NULL),
(22, 'Mohammed Alabi', NULL, '08098434014', NULL, NULL, 5, '2025-12-08 12:12:49', NULL, NULL),
(23, 'Shuaibu Mohammed', NULL, '08098434014', NULL, NULL, 5, '2025-12-08 12:39:08', NULL, NULL),
(24, 'Ahmad Haruna', NULL, '08098434014', NULL, NULL, 5, '2025-12-08 12:49:28', NULL, NULL),
(25, 'Bawa Zarema', NULL, '08098434014', NULL, NULL, 5, '2025-12-08 12:52:09', NULL, NULL),
(26, 'Uche', NULL, '08098434014', NULL, NULL, 5, '2025-12-08 12:56:56', NULL, NULL),
(27, 'Mustapha Hassan Usman', NULL, '08098434014', NULL, NULL, 5, '2025-12-08 12:58:55', NULL, NULL),
(28, 'Anayo Joseph', NULL, '08098434014', NULL, NULL, 5, '2025-12-08 15:07:37', NULL, NULL),
(29, 'Ukwueze Anthony', NULL, '08098434014', NULL, NULL, 5, '2025-12-08 15:12:39', NULL, NULL),
(30, 'Kalu Uko Austine', NULL, '08098434014', NULL, NULL, 5, '2025-12-09 10:14:56', NULL, NULL),
(31, 'Ibrahimama Alum', NULL, '08098434014', NULL, NULL, 5, '2025-12-09 10:50:12', NULL, NULL),
(32, 'Supreme Global', NULL, '08098434014', NULL, NULL, 5, '2025-12-09 11:22:54', NULL, NULL),
(33, 'Yusuf Shamsudeen', NULL, '08098434014', NULL, NULL, 5, '2025-12-09 11:28:24', NULL, NULL),
(34, 'Innovative Global', NULL, '08098434014', NULL, NULL, 5, '2025-12-09 11:35:29', NULL, NULL),
(35, 'Usman Madign', NULL, '08098434014', NULL, NULL, 5, '2025-12-09 11:40:55', NULL, NULL),
(36, 'Kingsley', NULL, '08098434014', NULL, NULL, 5, '2025-12-11 14:34:19', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `designs`
--

CREATE TABLE `designs` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `designs`
--

INSERT INTO `designs` (`id`, `code`, `name`, `description`, `is_active`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'MILANO', 'Milano', 'Milano roofing tile design', 1, 5, '2025-11-28 20:11:09', '2025-12-02 09:23:10', '2025-12-02 09:23:10'),
(2, 'SHINGLE', 'Shingle', 'Shingle roofing tile design', 5, 1, '2025-11-28 20:11:09', '2025-12-02 09:23:14', '2025-12-02 09:23:14'),
(3, 'CORONA', 'Corona', 'Corona roofing tile design', 1, 5, '2025-11-28 20:11:09', '2025-12-02 09:26:53', '2025-12-02 09:26:53'),
(4, 'CLS02_02', 'Claret Shingle', 'Claret shingle roofing tiles', 1, 5, '2025-12-02 09:17:01', NULL, NULL),
(5, 'CBS_03', 'Claret black shingle', 'Claret black shingle roofing tiles', 1, 5, '2025-12-02 09:19:01', '2025-12-02 09:19:32', NULL),
(6, 'BLS_01', 'Black shingle', 'Black shingle roofing tiles', 1, 5, '2025-12-02 09:20:59', '2025-12-02 09:21:38', NULL),
(7, 'GS_04', 'Green shingle', 'Green shingle roofing tiles', 1, 5, '2025-12-02 09:22:52', NULL, NULL),
(8, 'CBS_05', 'Coffe brown shingle', 'coffe brown shingle roofing tiles', 1, 5, '2025-12-02 09:26:30', NULL, NULL),
(9, 'CBBPS_06', 'Claret Brown and Black patch shingle', 'Claret Brown and Black patch shingle', 1, 5, '2025-12-02 09:37:21', '2025-12-02 09:44:59', NULL),
(10, 'BRPS_07', 'Black and Red patch Shingle', 'Black and Red patch Shingle Roofing tiles', 1, 5, '2025-12-02 09:43:41', '2025-12-02 09:44:29', NULL),
(11, 'BM_01', 'Black Milano', 'Black Milano Roofing tiles', 1, 5, '2025-12-02 09:46:11', NULL, NULL),
(12, 'CBM_02', 'Claret Black Milano', 'Claret Black Milano Roofing Tiles', 1, 5, '2025-12-02 09:47:15', NULL, NULL),
(13, 'CM_03', 'Claret Milano', 'Claret Milano Roofing Tiles', 1, 5, '2025-12-02 09:48:30', NULL, NULL),
(14, 'BM_04', 'Blue Milano', 'Blue Milano Roofing Tiles', 1, 5, '2025-12-02 09:49:40', NULL, NULL),
(15, 'GM_05', 'Green Milano', 'Green Milano Roofing Tiles', 1, 5, '2025-12-02 09:50:38', NULL, NULL),
(16, 'CBM__6', 'Claret Brown Milano', 'Claret Brown Milano Roofing Tiles', 1, 5, '2025-12-02 09:52:10', NULL, NULL),
(17, 'CBBM_07', 'Claret Brown Black Milano', 'Claret Brown Black Milano Roofing Tiles', 1, 5, '2025-12-02 09:53:30', NULL, NULL),
(18, 'BB_01', 'Black Bond', 'Black Bond Roofing Tiles', 1, 5, '2025-12-02 09:54:40', NULL, NULL),
(19, 'CB_02', 'Claret Bond', 'Claret Bond Roofing Tiles', 1, 5, '2025-12-02 10:27:03', NULL, NULL),
(20, 'GB_03', 'Green Bond', 'Green Bond Roofing Tiles', 1, 5, '2025-12-02 10:27:58', NULL, NULL),
(21, 'BBD_04', 'Blue bond', 'Blue bond Roofing Tiles', 1, 5, '2025-12-02 10:29:04', NULL, NULL),
(22, 'CBD_05', 'Claret Brown Bond', 'Claret Brown Bond', 1, 5, '2025-12-02 10:30:32', NULL, NULL),
(23, 'RBD_06', 'Red Bond', 'Red Bond Roofing Tiles', 1, 5, '2025-12-02 10:31:42', NULL, NULL),
(24, 'BC_01', 'Black Classic', 'Black Classic Roofing Tiles', 1, 5, '2025-12-02 10:32:45', NULL, NULL),
(25, 'CBC_02', 'Claret Brown Classic', 'Claret Brown Classic Roofing Tiles', 1, 5, '2025-12-02 10:34:01', NULL, NULL),
(26, 'GC_03', 'Green Classic', 'Green Classic Roofing Tiles', 1, 5, '2025-12-02 10:34:57', NULL, NULL),
(27, 'BCH_01', 'Black Check', 'Black Check Roofing Tiles', 1, 5, '2025-12-02 10:36:21', NULL, NULL),
(28, 'CCK_02', 'Claret Check', 'Claret Check Roofing Tiles', 1, 5, '2025-12-02 10:37:33', NULL, NULL),
(29, 'BRM_01', 'Black Romania', 'Black Romania Roofing Tiles', 1, 5, '2025-12-02 10:38:53', NULL, NULL),
(30, 'CRM_02', 'Claret Romania', 'Claret Romania Roofing Tiles', 1, 5, '2025-12-02 10:39:58', NULL, NULL),
(31, 'BKRM_03', 'Black and Red Romania', 'Black and Red Romania Roofing Tiles', 1, 5, '2025-12-02 10:41:32', NULL, NULL),
(32, 'CBRM_04', 'Black and Red Romania', 'Black and Red Romania Roofing Tiles', 1, 5, '2025-12-02 10:43:06', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `sale_type` enum('coil_sale','tile_sale','production') DEFAULT NULL,
  `sale_reference_id` int(11) DEFAULT NULL,
  `production_id` int(11) DEFAULT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `invoice_shape` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Complete invoice data structure' CHECK (json_valid(`invoice_shape`)),
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
  `tax_value` decimal(15,2) DEFAULT 0.00,
  `tax_amount` decimal(15,2) DEFAULT 0.00,
  `discount_type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
  `discount_value` decimal(15,2) DEFAULT 0.00,
  `discount_amount` decimal(15,2) DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL,
  `tax` decimal(15,2) DEFAULT 0.00,
  `other_charges` decimal(15,2) DEFAULT 0.00,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `shipping` decimal(15,2) DEFAULT 0.00,
  `status` enum('unpaid','partial','paid','cancelled') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `immutable_hash` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `sale_id`, `sale_type`, `sale_reference_id`, `production_id`, `invoice_number`, `invoice_shape`, `subtotal`, `tax_type`, `tax_value`, `tax_amount`, `discount_type`, `discount_value`, `discount_amount`, `total`, `tax`, `other_charges`, `paid_amount`, `shipping`, `status`, `created_at`, `updated_at`, `immutable_hash`) VALUES
(35, 49, 'coil_sale', 49, NULL, 'INV-2025-000001', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Usman Madugu\\\",\\\"company\\\":\\\"Usman Aluminium Ltd\\\",\\\"email\\\":\\\"omale.ochigbo@obumek360.app\\\",\\\"phone\\\":\\\"07071132619\\\",\\\"address\\\":\\\"Kaduna State\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-11-17 12:51:31\\\",\\\"ref\\\":\\\"#SO-20251117-000049\\\",\\\"sale_id\\\":\\\"49\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"D31 - I\\\\\\/beige Aluminium coil\\\",\\\"quantity\\\":2073000,\\\"qty_text\\\":\\\"2,073,000.00 meters\\\",\\\"unit_price\\\":5800,\\\"subtotal\\\":12023400000}],\\\"subtotal\\\":12023400000,\\\"order_tax\\\":901755000,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":12925155000,\\\"paid\\\":0,\\\"due\\\":12925155000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 9999999999.99, 99999999.99, 0.00, 9999999999.99, 0.00, 'paid', '2025-11-17 12:51:31', '2025-11-28 20:11:53', '7c7814f9a19819350f8f7ddac27e19294ce7ce45a0f4f8be91e61d2698fa53d8'),
(36, 50, 'coil_sale', 50, NULL, 'INV-2025-000002', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Usman Madugu\\\",\\\"company\\\":\\\"Usman Aluminium Ltd\\\",\\\"email\\\":\\\"omale.ochigbo@obumek360.app\\\",\\\"phone\\\":\\\"07071132619\\\",\\\"address\\\":\\\"Kaduna State\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-11-18 11:43:49\\\",\\\"ref\\\":\\\"#SO-20251118-000050\\\",\\\"sale_id\\\":\\\"50\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS382 - Alusteel coil\\\",\\\"quantity\\\":2000,\\\"qty_text\\\":\\\"2,000.00 meters\\\",\\\"unit_price\\\":400,\\\"subtotal\\\":800000}],\\\"subtotal\\\":800000,\\\"order_tax\\\":60000,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":860000,\\\"paid\\\":0,\\\"due\\\":860000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 860000.00, 60000.00, 0.00, 860000.00, 0.00, 'paid', '2025-11-18 11:43:49', '2025-11-28 20:11:53', '398c81e4f784b8e027704a50d1ff6c1a1e7255e07abc570acab26b28e2a8bb30'),
(37, 51, 'coil_sale', 51, NULL, 'INV-2025-000003', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Adams\\\",\\\"company\\\":\\\"Adams Aluminium Ltd\\\",\\\"email\\\":\\\"adamjames@yahoo.com\\\",\\\"phone\\\":\\\"08055084883\\\",\\\"address\\\":\\\"Kubwa Abuja\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-11-19 13:23:23\\\",\\\"ref\\\":\\\"#SO-20251119-000051\\\",\\\"sale_id\\\":\\\"51\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS369 - Alusteel coil\\\",\\\"quantity\\\":6000,\\\"qty_text\\\":\\\"6,000.00 meters\\\",\\\"unit_price\\\":10800,\\\"subtotal\\\":64800000}],\\\"subtotal\\\":64800000,\\\"order_tax\\\":4860000,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":69660000,\\\"paid\\\":0,\\\"due\\\":69660000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 69660000.00, 4860000.00, 0.00, 69660000.00, 0.00, 'paid', '2025-11-19 13:23:23', '2025-12-11 08:24:47', '1f2fb187423386cbd8835bea12e071429905c8168c603daff928581c185765ca'),
(38, 53, NULL, NULL, NULL, 'INV-2025-000004', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"MONDAY JOB\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 10:15:05\\\",\\\"ref\\\":\\\"#SO-20251203-000053\\\",\\\"sale_id\\\":\\\"53\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"D60 - G\\\\\\/beige Aluminium coil\\\",\\\"quantity\\\":2053,\\\"qty_text\\\":\\\"2,053.00 kg\\\",\\\"unit_price\\\":2000,\\\"subtotal\\\":4106000}],\\\"subtotal\\\":4106000,\\\"order_tax\\\":307950,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":4413950,\\\"paid\\\":0,\\\"due\\\":4413950,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 4413950.00, 307950.00, 0.00, 4413950.00, 0.00, 'paid', '2025-12-03 10:15:05', '2025-12-11 08:24:13', '03d3e908175c004c8cdcecd1f192191923dcf65a7c75eaf6d33e6c1d358c6700'),
(39, NULL, 'tile_sale', 1, NULL, 'INV-2025-000005', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"name\":\"Adams\",\"phone\":\"08055084883\",\"address\":\"Kubwa Abuja\",\"email\":\"adamjames@yahoo.com\"},\"items\":[{\"product_code\":\"BB_01-TBLACK-THICK\",\"description\":\"Roofing Tile - Black Bond\",\"details\":\"Color: T\\/Black\\nGauge: Thick\\nDesign: BB_01\",\"quantity\":400,\"unit_price\":1000}],\"tax\":0,\"shipping\":0,\"discount\":0,\"notes\":{\"receipt_statement\":\"\",\"refund_policy\":\"All sales are final. No refunds or exchanges.\",\"additional_notes\":\"Thank you for your business!\"},\"meta\":{\"ref\":\"TILE-SALE-1\",\"type\":\"tile_sale\",\"sale_id\":\"1\",\"product_details\":{\"design\":\"Black Bond\",\"color\":\"T\\/Black\",\"gauge\":\"thick\"}}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 400000.00, 0.00, 0.00, 400000.00, 0.00, 'paid', '2025-12-03 10:24:13', '2025-12-11 08:23:51', '51df09732861bf18948c713c3795508f4fb184cf49a7e9b0cbcf2a3d463bb700'),
(40, NULL, 'tile_sale', 2, NULL, 'INV-2025-000006', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"name\":\"Elizabeth Enehe\",\"phone\":\"08098434014\",\"address\":\"\",\"email\":\"\"},\"items\":[{\"product_code\":\"BM_01-BM_01-LIGHT\",\"description\":\"Roofing Tile - Black Milano\",\"details\":\"Color: Black Milano\\nGauge: Light\\nDesign: BM_01\",\"quantity\":1000,\"unit_price\":1000}],\"tax\":0,\"shipping\":0,\"discount\":0,\"notes\":{\"receipt_statement\":\"\",\"refund_policy\":\"All sales are final. No refunds or exchanges.\",\"additional_notes\":\"Thank you for your business!\"},\"meta\":{\"ref\":\"TILE-SALE-2\",\"type\":\"tile_sale\",\"sale_id\":\"2\",\"product_details\":{\"design\":\"Black Milano\",\"color\":\"Black Milano\",\"gauge\":\"light\"}}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 1000000.00, 0.00, 0.00, 1000000.00, 0.00, 'paid', '2025-12-03 10:28:07', '2025-12-10 11:18:16', 'a7281b24f54c8e4322344172ee0912a14e62390198d266c628f3e7ace7d4e7e8'),
(41, 55, NULL, NULL, NULL, 'INV-2025-000007', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Elizabeth Enehe\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:04:08\\\",\\\"ref\\\":\\\"#SO-20251203-000055\\\",\\\"sale_id\\\":\\\"55\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS368 - Alusteel coil\\\",\\\"quantity\\\":3276,\\\"qty_text\\\":\\\"3,276.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5733000}],\\\"subtotal\\\":5733000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5733000,\\\"paid\\\":0,\\\"due\\\":5733000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5733000.00, 0.00, 0.00, 5733000.00, 0.00, 'paid', '2025-12-03 11:04:08', '2025-12-11 08:23:29', '61c6d614edae262f9479c3d45c50b9e9f349f8017140d63f5b542184a307607e'),
(42, 57, NULL, NULL, NULL, 'INV-2025-000008', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Elizabeth Enehe\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:07:19\\\",\\\"ref\\\":\\\"#SO-20251203-000057\\\",\\\"sale_id\\\":\\\"57\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS367 - Alusteel coil\\\",\\\"quantity\\\":3246,\\\"qty_text\\\":\\\"3,246.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5680500}],\\\"subtotal\\\":5680500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5680500,\\\"paid\\\":0,\\\"due\\\":5680500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5680500.00, 0.00, 0.00, 5680500.00, 0.00, 'paid', '2025-12-03 11:07:19', '2025-12-11 08:23:08', '4ae13177a48c7404b83fc9af43427d276d23943eab3b7725480f1ebae7fdc445'),
(43, 59, NULL, NULL, NULL, 'INV-2025-000009', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Elizabeth Enehe\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:09:48\\\",\\\"ref\\\":\\\"#SO-20251203-000059\\\",\\\"sale_id\\\":\\\"59\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS369 - Alusteel coil\\\",\\\"quantity\\\":3360,\\\"qty_text\\\":\\\"3,360.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5880000}],\\\"subtotal\\\":5880000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5880000,\\\"paid\\\":0,\\\"due\\\":5880000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5880000.00, 0.00, 0.00, 5880000.00, 0.00, 'paid', '2025-12-03 11:09:48', '2025-12-11 08:22:49', '7e4dea896e3e6d26ecb93b534fb01a2997a73c5f5f3ece8e34a55ee3384cea02'),
(44, 61, NULL, NULL, NULL, 'INV-2025-000010', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Murna Solomo\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:14:57\\\",\\\"ref\\\":\\\"#SO-20251203-000061\\\",\\\"sale_id\\\":\\\"61\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"D57 - I\\\\\\/beige Aluminium coil\\\",\\\"quantity\\\":2161,\\\"qty_text\\\":\\\"2,161.00 kg\\\",\\\"unit_price\\\":6200,\\\"subtotal\\\":13398200}],\\\"subtotal\\\":13398200,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":13398200,\\\"paid\\\":0,\\\"due\\\":13398200,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 13398200.00, 0.00, 0.00, 13398200.00, 0.00, 'paid', '2025-12-03 11:14:57', '2025-12-11 08:22:26', '34745b24121b5daa11b3bc9d6afe1930261572ab7a4ba768e1a5ceb5597a3f0f'),
(45, 63, NULL, NULL, NULL, 'INV-2025-000011', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Murna Solomo\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:17:49\\\",\\\"ref\\\":\\\"#SO-20251203-000063\\\",\\\"sale_id\\\":\\\"63\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"D246 - S\\\\\\/blue Aluminium coil\\\",\\\"quantity\\\":2025,\\\"qty_text\\\":\\\"2,025.00 kg\\\",\\\"unit_price\\\":6180,\\\"subtotal\\\":12514500}],\\\"subtotal\\\":12514500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":12514500,\\\"paid\\\":0,\\\"due\\\":12514500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 12514500.00, 0.00, 0.00, 12514500.00, 0.00, 'paid', '2025-12-03 11:17:49', '2025-12-11 08:22:05', '41501227b605cbbfffbf99ac853e2eb1bef3411ea6750f1ef8fb0d5e05618844'),
(46, 65, NULL, NULL, NULL, 'INV-2025-000012', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Austine Ekeh\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:22:00\\\",\\\"ref\\\":\\\"#SO-20251203-000065\\\",\\\"sale_id\\\":\\\"65\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"D269 - S\\\\\\/blue Aluminium coil\\\",\\\"quantity\\\":2037,\\\"qty_text\\\":\\\"2,037.00 kg\\\",\\\"unit_price\\\":6200,\\\"subtotal\\\":12629400}],\\\"subtotal\\\":12629400,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":12629400,\\\"paid\\\":0,\\\"due\\\":12629400,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 12629400.00, 0.00, 0.00, 12629400.00, 0.00, 'paid', '2025-12-03 11:22:00', '2025-12-11 08:21:43', '38c02036ede322f6a3b527ed894d92be09412465f8e6a66ef628e8a9dcc6f622'),
(47, 67, NULL, NULL, NULL, 'INV-2025-000013', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Austine Ekeh\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:24:27\\\",\\\"ref\\\":\\\"#SO-20251203-000067\\\",\\\"sale_id\\\":\\\"67\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS290 - Alusteel coil\\\",\\\"quantity\\\":3192,\\\"qty_text\\\":\\\"3,192.00 kg\\\",\\\"unit_price\\\":1800,\\\"subtotal\\\":5745600}],\\\"subtotal\\\":5745600,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5745600,\\\"paid\\\":0,\\\"due\\\":5745600,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5745600.00, 0.00, 0.00, 5745600.00, 0.00, 'paid', '2025-12-03 11:24:27', '2025-12-11 08:21:13', '0ae3631ccce9ace0ea6d5a7679906869d475deb205a1ee306cb3333fe6046241'),
(48, 69, NULL, NULL, NULL, 'INV-2025-000014', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Austine Ekeh\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:26:37\\\",\\\"ref\\\":\\\"#SO-20251203-000069\\\",\\\"sale_id\\\":\\\"69\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS364 - Alusteel coil\\\",\\\"quantity\\\":3162,\\\"qty_text\\\":\\\"3,162.00 kg\\\",\\\"unit_price\\\":1800,\\\"subtotal\\\":5691600}],\\\"subtotal\\\":5691600,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5691600,\\\"paid\\\":0,\\\"due\\\":5691600,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5691600.00, 0.00, 0.00, 5691600.00, 0.00, 'paid', '2025-12-03 11:26:37', '2025-12-11 08:20:39', '25ba30f5a6a401c6f5bd2963132969638a4031cbbbe578b44e7f0631ef6eff44'),
(49, 71, NULL, NULL, NULL, 'INV-2025-000015', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Austine Ekeh\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:28:50\\\",\\\"ref\\\":\\\"#SO-20251203-000071\\\",\\\"sale_id\\\":\\\"71\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS383 - Alusteel coil\\\",\\\"quantity\\\":3246,\\\"qty_text\\\":\\\"3,246.00 kg\\\",\\\"unit_price\\\":1800,\\\"subtotal\\\":5842800}],\\\"subtotal\\\":5842800,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5842800,\\\"paid\\\":0,\\\"due\\\":5842800,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5842800.00, 0.00, 0.00, 5842800.00, 0.00, 'paid', '2025-12-03 11:28:50', '2025-12-11 08:20:14', '6b39824085b3b65a9c9f67dfcb6d37eb822d2e65625bfa348b2391819f535c4f'),
(50, 73, NULL, NULL, NULL, 'INV-2025-000016', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Sunny Tech\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:31:50\\\",\\\"ref\\\":\\\"#SO-20251203-000073\\\",\\\"sale_id\\\":\\\"73\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS316 - Alusteel coil\\\",\\\"quantity\\\":3274,\\\"qty_text\\\":\\\"3,274.00 kg\\\",\\\"unit_price\\\":1780,\\\"subtotal\\\":5827720}],\\\"subtotal\\\":5827720,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5827720,\\\"paid\\\":0,\\\"due\\\":5827720,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5827720.00, 0.00, 0.00, 5827720.00, 0.00, 'paid', '2025-12-03 11:31:50', '2025-12-11 08:19:48', '7810574559552aa23cbe20f2d611e4b5ba27f4c73a84f5903e26f7855ee1e24e'),
(51, 75, NULL, NULL, NULL, 'INV-2025-000017', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Sunny Tech\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:34:17\\\",\\\"ref\\\":\\\"#SO-20251203-000075\\\",\\\"sale_id\\\":\\\"75\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS400 - Alusteel coil\\\",\\\"quantity\\\":3208,\\\"qty_text\\\":\\\"3,208.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5614000}],\\\"subtotal\\\":5614000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5614000,\\\"paid\\\":0,\\\"due\\\":5614000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5614000.00, 0.00, 0.00, 5614000.00, 0.00, 'paid', '2025-12-03 11:34:17', '2025-12-11 08:19:24', 'e8d7e3289abd3e93fa680c77d28b5c934f34b4104f21f9c86cb7f46a5eafa974'),
(52, 77, NULL, NULL, NULL, 'INV-2025-000018', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Sunny Tech\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:36:26\\\",\\\"ref\\\":\\\"#SO-20251203-000077\\\",\\\"sale_id\\\":\\\"77\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS396 - Alusteel coil\\\",\\\"quantity\\\":3204,\\\"qty_text\\\":\\\"3,204.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5607000}],\\\"subtotal\\\":5607000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5607000,\\\"paid\\\":0,\\\"due\\\":5607000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5607000.00, 0.00, 0.00, 5607000.00, 0.00, 'paid', '2025-12-03 11:36:26', '2025-12-11 08:19:01', '8634ed55a0c7e2d1cce44b07a3950e574d8fdb31fd8759321c6ebbc786715c70'),
(53, 79, NULL, NULL, NULL, 'INV-2025-000019', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Joshua\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:40:22\\\",\\\"ref\\\":\\\"#SO-20251203-000079\\\",\\\"sale_id\\\":\\\"79\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS230 - Alusteel coil\\\",\\\"quantity\\\":3510,\\\"qty_text\\\":\\\"3,510.00 kg\\\",\\\"unit_price\\\":2050,\\\"subtotal\\\":7195500}],\\\"subtotal\\\":7195500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":7195500,\\\"paid\\\":0,\\\"due\\\":7195500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 7195500.00, 0.00, 0.00, 7195500.00, 0.00, 'paid', '2025-12-03 11:40:22', '2025-12-11 08:18:35', '95c209028266208c7053ec5b1bce2a9ea42f268cafc94a5f233494cf8c94e4a5'),
(54, 81, NULL, NULL, NULL, 'INV-2025-000020', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Joshua\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:43:40\\\",\\\"ref\\\":\\\"#SO-20251203-000081\\\",\\\"sale_id\\\":\\\"81\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS407 - Alusteel coil\\\",\\\"quantity\\\":3242,\\\"qty_text\\\":\\\"3,242.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5673500}],\\\"subtotal\\\":5673500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5673500,\\\"paid\\\":0,\\\"due\\\":5673500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5673500.00, 0.00, 0.00, 5673500.00, 0.00, 'paid', '2025-12-03 11:43:40', '2025-12-11 08:18:11', '498db1ad09b22c1c7538b747bc20c88f35627d5864ed289411450a93f3cf1fdd'),
(55, 83, NULL, NULL, NULL, 'INV-2025-000021', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Joshua\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:45:38\\\",\\\"ref\\\":\\\"#SO-20251203-000083\\\",\\\"sale_id\\\":\\\"83\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS408 - Alusteel coil\\\",\\\"quantity\\\":2998,\\\"qty_text\\\":\\\"2,998.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5246500}],\\\"subtotal\\\":5246500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5246500,\\\"paid\\\":0,\\\"due\\\":5246500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5246500.00, 0.00, 0.00, 5246500.00, 0.00, 'paid', '2025-12-03 11:45:38', '2025-12-11 08:17:36', '317a7d819b23c38b5af1a8dc1c9b7c952cd2ece0dddb85e98b15df411ade32d7'),
(56, 85, NULL, NULL, NULL, 'INV-2025-000022', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Sincere \\\\\\/ Nevertheless\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:53:00\\\",\\\"ref\\\":\\\"#SO-20251203-000085\\\",\\\"sale_id\\\":\\\"85\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"A13 - Aluminium coil\\\",\\\"quantity\\\":1870,\\\"qty_text\\\":\\\"1,870.00 kg\\\",\\\"unit_price\\\":6300,\\\"subtotal\\\":11781000}],\\\"subtotal\\\":11781000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":11781000,\\\"paid\\\":0,\\\"due\\\":11781000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 11781000.00, 0.00, 0.00, 11781000.00, 0.00, 'paid', '2025-12-03 11:53:00', '2025-12-11 08:16:59', 'c4a04c2b3b1f7a9e03d117309ad1438330b07000921dcf400c80da0aa728daf8'),
(57, 87, NULL, NULL, NULL, 'INV-2025-000023', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Sincere \\\\\\/ Nevertheless\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:54:55\\\",\\\"ref\\\":\\\"#SO-20251203-000087\\\",\\\"sale_id\\\":\\\"87\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS365 - Alusteel coil\\\",\\\"quantity\\\":3142,\\\"qty_text\\\":\\\"3,142.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5498500}],\\\"subtotal\\\":5498500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5498500,\\\"paid\\\":0,\\\"due\\\":5498500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5498500.00, 0.00, 0.00, 5498500.00, 0.00, 'paid', '2025-12-03 11:54:55', '2025-12-11 08:16:06', '972cdb7bc894045448560c5aedf3d28f195232b7f94ec70dcadc1788f9a09c42'),
(58, 89, NULL, NULL, NULL, 'INV-2025-000024', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Sunday Ekweonu\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:57:37\\\",\\\"ref\\\":\\\"#SO-20251203-000089\\\",\\\"sale_id\\\":\\\"89\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"A29 - Aluminium coil\\\",\\\"quantity\\\":1776,\\\"qty_text\\\":\\\"1,776.00 kg\\\",\\\"unit_price\\\":6350,\\\"subtotal\\\":11277600}],\\\"subtotal\\\":11277600,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":11277600,\\\"paid\\\":0,\\\"due\\\":11277600,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 11277600.00, 0.00, 0.00, 11277600.00, 0.00, 'paid', '2025-12-03 11:57:37', '2025-12-11 08:15:38', 'f0b5e5b8c1a03da1132387ba5e26cc9d0cb0b6c3e645e42b735dcc34806e67bc'),
(59, 91, NULL, NULL, NULL, 'INV-2025-000025', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Sunday Ekweonu\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 11:59:17\\\",\\\"ref\\\":\\\"#SO-20251203-000091\\\",\\\"sale_id\\\":\\\"91\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"A22 - Aluminium coil\\\",\\\"quantity\\\":1770,\\\"qty_text\\\":\\\"1,770.00 kg\\\",\\\"unit_price\\\":6300,\\\"subtotal\\\":11151000}],\\\"subtotal\\\":11151000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":11151000,\\\"paid\\\":0,\\\"due\\\":11151000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 11151000.00, 0.00, 0.00, 11151000.00, 0.00, 'paid', '2025-12-03 11:59:17', '2025-12-11 08:15:02', '37078ce3c5098c284a9a78330631a27d1e650b47cdcd123f1608102c542dd510'),
(60, 93, NULL, NULL, NULL, 'INV-2025-000026', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Lijian Dery\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:01:54\\\",\\\"ref\\\":\\\"#SO-20251203-000093\\\",\\\"sale_id\\\":\\\"93\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS270 - Alusteel coil\\\",\\\"quantity\\\":3434,\\\"qty_text\\\":\\\"3,434.00 kg\\\",\\\"unit_price\\\":1780,\\\"subtotal\\\":6112520}],\\\"subtotal\\\":6112520,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":6112520,\\\"paid\\\":0,\\\"due\\\":6112520,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 6112520.00, 0.00, 0.00, 6112520.00, 0.00, 'paid', '2025-12-03 12:01:54', '2025-12-11 08:14:38', '387cda76ff16cafa75d64d681c942f29a7c1ac479285274a972ac5e62a9bae49'),
(61, 95, NULL, NULL, NULL, 'INV-2025-000027', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Lijian Dery\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:05:19\\\",\\\"ref\\\":\\\"#SO-20251203-000095\\\",\\\"sale_id\\\":\\\"95\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS269 - Alusteel coil\\\",\\\"quantity\\\":3462,\\\"qty_text\\\":\\\"3,462.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":6058500}],\\\"subtotal\\\":6058500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":6058500,\\\"paid\\\":0,\\\"due\\\":6058500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 6058500.00, 0.00, 0.00, 6058500.00, 0.00, 'paid', '2025-12-03 12:05:19', '2025-12-11 08:14:16', '73611675446768051cd2ede858fcccef7e709d865168fad83abfef87270ad878'),
(62, 97, NULL, NULL, NULL, 'INV-2025-000028', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Skytech Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:08:32\\\",\\\"ref\\\":\\\"#SO-20251203-000097\\\",\\\"sale_id\\\":\\\"97\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS402 - Alusteel coil\\\",\\\"quantity\\\":3428,\\\"qty_text\\\":\\\"3,428.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5999000}],\\\"subtotal\\\":5999000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5999000,\\\"paid\\\":0,\\\"due\\\":5999000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5999000.00, 0.00, 0.00, 5999000.00, 0.00, 'paid', '2025-12-03 12:08:32', '2025-12-11 08:13:56', '3844d0912f2dc0956a2f8c730a5669c61c1b7e98d6c68e27f5441b45993e50b9'),
(63, 99, NULL, NULL, NULL, 'INV-2025-000029', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Skytech Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:10:07\\\",\\\"ref\\\":\\\"#SO-20251203-000099\\\",\\\"sale_id\\\":\\\"99\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS409 - Alusteel coil\\\",\\\"quantity\\\":3618,\\\"qty_text\\\":\\\"3,618.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":6331500}],\\\"subtotal\\\":6331500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":6331500,\\\"paid\\\":0,\\\"due\\\":6331500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 6331500.00, 0.00, 0.00, 6331500.00, 0.00, 'paid', '2025-12-03 12:10:07', '2025-12-11 08:13:28', '80baf71a95327bc8e9f94d86529f19b4ef9df01678af0f556245f5e990cb78fe'),
(64, 101, NULL, NULL, NULL, 'INV-2025-000030', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Skytech Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:12:07\\\",\\\"ref\\\":\\\"#SO-20251203-000101\\\",\\\"sale_id\\\":\\\"101\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS408 - Alusteel coil\\\",\\\"quantity\\\":3366,\\\"qty_text\\\":\\\"3,366.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5890500}],\\\"subtotal\\\":5890500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5890500,\\\"paid\\\":0,\\\"due\\\":5890500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5890500.00, 0.00, 0.00, 5890500.00, 0.00, 'paid', '2025-12-03 12:12:07', '2025-12-11 08:12:56', 'c4a3d364eca3a2ace32cf76bca8541d220be7beaf9114d2697bb1615bde1813f'),
(65, 103, NULL, NULL, NULL, 'INV-2025-000031', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Skytech Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:13:50\\\",\\\"ref\\\":\\\"#SO-20251203-000103\\\",\\\"sale_id\\\":\\\"103\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS371 - Alusteel coil\\\",\\\"quantity\\\":2722,\\\"qty_text\\\":\\\"2,722.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":4763500}],\\\"subtotal\\\":4763500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":4763500,\\\"paid\\\":0,\\\"due\\\":4763500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 4763500.00, 0.00, 0.00, 4763500.00, 0.00, 'paid', '2025-12-03 12:13:50', '2025-12-11 08:12:26', '2840fa9d72d90ba2b153b6f2503236fac3c0a57d88988cd7c4cea9fc5f609f7f'),
(66, 105, NULL, NULL, NULL, 'INV-2025-000032', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Skytech Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:15:40\\\",\\\"ref\\\":\\\"#SO-20251203-000105\\\",\\\"sale_id\\\":\\\"105\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS405 - Alusteel coil\\\",\\\"quantity\\\":3206,\\\"qty_text\\\":\\\"3,206.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5610500}],\\\"subtotal\\\":5610500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5610500,\\\"paid\\\":0,\\\"due\\\":5610500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5610500.00, 0.00, 0.00, 5610500.00, 0.00, 'paid', '2025-12-03 12:15:40', '2025-12-10 19:42:59', '682f151ee8098dd2367d40aaf57b67bffd3e0428d2b62125b69c6f748d44db31'),
(67, 107, NULL, NULL, NULL, 'INV-2025-000033', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Skytech Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:18:20\\\",\\\"ref\\\":\\\"#SO-20251203-000107\\\",\\\"sale_id\\\":\\\"107\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS403 - Alusteel coil\\\",\\\"quantity\\\":2994,\\\"qty_text\\\":\\\"2,994.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5239500}],\\\"subtotal\\\":5239500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5239500,\\\"paid\\\":0,\\\"due\\\":5239500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5239500.00, 0.00, 0.00, 5239500.00, 0.00, 'paid', '2025-12-03 12:18:20', '2025-12-10 19:42:26', '9eee261561a1dcb29c336235bec5732e9230af1517cd4a2b33ad75d87f2d1df5'),
(68, 109, NULL, NULL, NULL, 'INV-2025-000034', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:21:31\\\",\\\"ref\\\":\\\"#SO-20251203-000109\\\",\\\"sale_id\\\":\\\"109\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS392 - Alusteel coil\\\",\\\"quantity\\\":3202,\\\"qty_text\\\":\\\"3,202.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5603500}],\\\"subtotal\\\":5603500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5603500,\\\"paid\\\":0,\\\"due\\\":5603500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5603500.00, 0.00, 0.00, 5603500.00, 0.00, 'paid', '2025-12-03 12:21:31', '2025-12-10 19:41:10', '7dfc4058a66248260d34e87d0543b4e9936e4419b16a7c3901ba6df2ba8deceb'),
(69, 111, NULL, NULL, NULL, 'INV-2025-000035', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:23:20\\\",\\\"ref\\\":\\\"#SO-20251203-000111\\\",\\\"sale_id\\\":\\\"111\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS393 - Alusteel coil\\\",\\\"quantity\\\":3202,\\\"qty_text\\\":\\\"3,202.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5603500}],\\\"subtotal\\\":5603500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5603500,\\\"paid\\\":0,\\\"due\\\":5603500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5603500.00, 0.00, 0.00, 5603500.00, 0.00, 'paid', '2025-12-03 12:23:20', '2025-12-11 08:11:57', '2fb5b05cc8e29dd6dc4f8d59866823cc97b1c10612c8fe2b9b5bb4c8870c3909');
INSERT INTO `invoices` (`id`, `sale_id`, `sale_type`, `sale_reference_id`, `production_id`, `invoice_number`, `invoice_shape`, `subtotal`, `tax_type`, `tax_value`, `tax_amount`, `discount_type`, `discount_value`, `discount_amount`, `total`, `tax`, `other_charges`, `paid_amount`, `shipping`, `status`, `created_at`, `updated_at`, `immutable_hash`) VALUES
(70, 113, NULL, NULL, NULL, 'INV-2025-000036', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:25:17\\\",\\\"ref\\\":\\\"#SO-20251203-000113\\\",\\\"sale_id\\\":\\\"113\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS395 - Alusteel coil\\\",\\\"quantity\\\":3202,\\\"qty_text\\\":\\\"3,202.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5603500}],\\\"subtotal\\\":5603500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5603500,\\\"paid\\\":0,\\\"due\\\":5603500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5603500.00, 0.00, 0.00, 5603500.00, 0.00, 'paid', '2025-12-03 12:25:17', '2025-12-11 08:11:28', '4f5b0047bd202467dc7aec0b0b318cc2e30f6243f0ad86c4d3e94f2d4011bd4a'),
(71, 115, NULL, NULL, NULL, 'INV-2025-000037', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:27:06\\\",\\\"ref\\\":\\\"#SO-20251203-000115\\\",\\\"sale_id\\\":\\\"115\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS394 - Alusteel coil\\\",\\\"quantity\\\":3350,\\\"qty_text\\\":\\\"3,350.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5862500}],\\\"subtotal\\\":5862500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5862500,\\\"paid\\\":0,\\\"due\\\":5862500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5862500.00, 0.00, 0.00, 5862500.00, 0.00, 'paid', '2025-12-03 12:27:06', '2025-12-11 08:11:09', 'a0fbbeb2635db1eac1e1c056536e16f96a27e431d31a98e0bd285d6d6e02efd6'),
(72, 117, NULL, NULL, NULL, 'INV-2025-000038', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:28:57\\\",\\\"ref\\\":\\\"#SO-20251203-000117\\\",\\\"sale_id\\\":\\\"117\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS397 - Alusteel coil\\\",\\\"quantity\\\":3364,\\\"qty_text\\\":\\\"3,364.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5887000}],\\\"subtotal\\\":5887000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5887000,\\\"paid\\\":0,\\\"due\\\":5887000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5887000.00, 0.00, 0.00, 5887000.00, 0.00, 'paid', '2025-12-03 12:28:57', '2025-12-11 08:10:41', 'f84bc12032b2abb7f7123f08997d2e1a6dfc3b12994168d58aebbdc72a63ec54'),
(73, 119, NULL, NULL, NULL, 'INV-2025-000039', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:30:42\\\",\\\"ref\\\":\\\"#SO-20251203-000119\\\",\\\"sale_id\\\":\\\"119\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS398 - Alusteel coil\\\",\\\"quantity\\\":2182,\\\"qty_text\\\":\\\"2,182.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":3818500}],\\\"subtotal\\\":3818500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":3818500,\\\"paid\\\":0,\\\"due\\\":3818500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 3818500.00, 0.00, 0.00, 3818500.00, 0.00, 'paid', '2025-12-03 12:30:42', '2025-12-11 08:10:16', '0db05a6560b7ce8f33ffc7d8869b9722a944afa38a09bb4d0e2c8e6b80df6c3f'),
(74, 121, NULL, NULL, NULL, 'INV-2025-000040', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:32:48\\\",\\\"ref\\\":\\\"#SO-20251203-000121\\\",\\\"sale_id\\\":\\\"121\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS399 - Alusteel coil\\\",\\\"quantity\\\":2316,\\\"qty_text\\\":\\\"2,316.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":4053000}],\\\"subtotal\\\":4053000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":4053000,\\\"paid\\\":0,\\\"due\\\":4053000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 4053000.00, 0.00, 0.00, 4053000.00, 0.00, 'paid', '2025-12-03 12:32:48', '2025-12-11 08:09:52', 'd5548e017c75f9c32ff97623c954b91e904a2ab37558b6bb032f2f46864d4954'),
(75, 123, NULL, NULL, NULL, 'INV-2025-000041', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:35:08\\\",\\\"ref\\\":\\\"#SO-20251203-000123\\\",\\\"sale_id\\\":\\\"123\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS401 - Alusteel coil\\\",\\\"quantity\\\":3202,\\\"qty_text\\\":\\\"3,202.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5603500}],\\\"subtotal\\\":5603500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5603500,\\\"paid\\\":0,\\\"due\\\":5603500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5603500.00, 0.00, 0.00, 5603500.00, 0.00, 'paid', '2025-12-03 12:35:08', '2025-12-11 08:09:26', 'fb723b3eb266795ca453d60995cdcb0fb9a848942b52d404dfe56b10cfceb2c7'),
(76, 125, NULL, NULL, NULL, 'INV-2025-000042', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-03 12:37:23\\\",\\\"ref\\\":\\\"#SO-20251203-000125\\\",\\\"sale_id\\\":\\\"125\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS404 - Alusteel coil\\\",\\\"quantity\\\":3196,\\\"qty_text\\\":\\\"3,196.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5593000}],\\\"subtotal\\\":5593000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5593000,\\\"paid\\\":0,\\\"due\\\":5593000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5593000.00, 0.00, 0.00, 5593000.00, 0.00, 'paid', '2025-12-03 12:37:23', '2025-12-11 08:08:59', '51e5fba3bed5879003cdb7a33a73e43b536ed682b08f77dda03258a369d6950e'),
(77, 126, NULL, NULL, 22, 'INV-2025-000043', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"17\",\"name\":\"Emmauel - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-08 11:24:20\",\"ref\":\"#SO-20251208-000126\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS232\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":3300,\"quantity\":9,\"subtotal\":29700}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":29700,\"paid\":0,\"due\":29700,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 29700.00, 0.00, 0.00, 29700.00, 0.00, 'paid', '2025-12-08 11:24:20', '2025-12-11 08:08:24', '44e3eedd136af39a20cd343980d912aa0b38ba66c8ae329908fd289a4d9fb976'),
(78, 127, NULL, NULL, 23, 'INV-2025-000044', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"18\",\"name\":\"Stevoo Alum - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-08 11:42:44\",\"ref\":\"#SO-20251208-000127\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS52\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":4300,\"quantity\":40.7,\"subtotal\":175010}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":175010,\"paid\":0,\"due\":175010,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 175010.00, 0.00, 0.00, 175010.00, 0.00, 'paid', '2025-12-08 11:42:44', '2025-12-11 08:07:56', '6e90e9457c930abd246efe9557d24c9e2271228c20570fc7fdcc4582fa1e67c1'),
(79, 128, NULL, NULL, 24, 'INV-2025-000045', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"19\",\"name\":\"Abdulazeez Adamu - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-08 11:54:11\",\"ref\":\"#SO-20251208-000128\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS251\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":4300,\"quantity\":14.8,\"subtotal\":63640},{\"product_code\":\"AS251\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":4300,\"quantity\":1.5,\"subtotal\":6450}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":70090,\"paid\":0,\"due\":70090,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 70090.00, 0.00, 0.00, 70090.00, 0.00, 'paid', '2025-12-08 11:54:11', '2025-12-11 08:07:19', 'f6833de03880f9d27ca8be6d87a368e3ff3602770652b0a0005f81f8ae8d7444'),
(80, 129, NULL, NULL, 25, 'INV-2025-000046', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"20\",\"name\":\"Aminu Haruna - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-08 12:07:13\",\"ref\":\"#SO-20251208-000129\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS128\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":3300,\"quantity\":30,\"subtotal\":99000}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":99000,\"paid\":0,\"due\":99000,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 99000.00, 0.00, 0.00, 99000.00, 0.00, 'paid', '2025-12-08 12:07:13', '2025-12-09 08:37:40', 'bd5cdf6662c0543626ab45fda214feb27a08054cc6cd2536f864f9b6d7245406'),
(81, 130, NULL, NULL, 26, 'INV-2025-000047', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"21\",\"name\":\"Abba - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-08 12:11:26\",\"ref\":\"#SO-20251208-000130\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS232\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":3200,\"quantity\":18,\"subtotal\":57600}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":57600,\"paid\":0,\"due\":57600,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 57600.00, 0.00, 0.00, 57600.00, 0.00, 'paid', '2025-12-08 12:11:26', '2025-12-09 08:37:09', 'b14355dad171c39ea7f53a729635e8ca7397a85a488ef0bb8e0f02c04140bd78'),
(82, 131, NULL, NULL, 27, 'INV-2025-000048', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"22\",\"name\":\"Mohammed Alabi - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-08 12:18:20\",\"ref\":\"#SO-20251208-000131\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS281\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":3300,\"quantity\":21,\"subtotal\":69300}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":69300,\"paid\":0,\"due\":69300,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 69300.00, 0.00, 0.00, 69300.00, 0.00, 'paid', '2025-12-08 12:18:20', '2025-12-11 08:01:45', '7c80350a13774a9c684dd91debdc3d4593cda647c11bf0706f2334158e3273bc'),
(83, 132, NULL, NULL, 28, 'INV-2025-000049', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"22\",\"name\":\"Mohammed Alabi - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-08 12:30:36\",\"ref\":\"#SO-20251208-000132\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS281\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":3300,\"quantity\":21,\"subtotal\":69300}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":69300,\"paid\":0,\"due\":69300,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 69300.00, 0.00, 0.00, 69300.00, 0.00, 'paid', '2025-12-08 12:30:36', '2025-12-11 08:01:15', 'f19603f03dd8823108d882d0aabad80c4b99a52d0fd4579372dceacc981c677a'),
(84, 133, NULL, NULL, 29, 'INV-2025-000050', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"23\",\"name\":\"Shuaibu Mohammed - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-08 12:43:50\",\"ref\":\"#SO-20251208-000133\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS232\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":3200,\"quantity\":31.5,\"subtotal\":100800}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":100800,\"paid\":0,\"due\":100800,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 100800.00, 0.00, 0.00, 100800.00, 0.00, 'paid', '2025-12-08 12:43:50', '2025-12-11 08:00:48', '5c5eaeb67fd43005b275109a27c3803ccdbf66d39c8b86653cf5599cc64a972d'),
(85, 134, NULL, NULL, 30, 'INV-2025-000051', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"24\",\"name\":\"Ahmad Haruna - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-08 12:51:09\",\"ref\":\"#SO-20251208-000134\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS232\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":3700,\"quantity\":36,\"subtotal\":133200}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":133200,\"paid\":0,\"due\":133200,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 133200.00, 0.00, 0.00, 133200.00, 0.00, 'paid', '2025-12-08 12:51:09', '2025-12-09 08:36:06', '3368069677c6eea9c6e1192063c7feb347df67667e5f1f1bb76f3441fb7d28a1'),
(86, 135, NULL, NULL, 31, 'INV-2025-000052', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"25\",\"name\":\"Bawa Zarema - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-08 12:54:04\",\"ref\":\"#SO-20251208-000135\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS289\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":4200,\"quantity\":24,\"subtotal\":100800}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":100800,\"paid\":0,\"due\":100800,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 100800.00, 0.00, 0.00, 100800.00, 0.00, 'paid', '2025-12-08 12:54:04', '2025-12-09 08:34:47', '3fddb820d660add653b232ecb8f3af0a2e9d0ea2e4aafa7cf8e57bcf0b6485cc'),
(87, 136, NULL, NULL, 32, 'INV-2025-000053', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"27\",\"name\":\"Mustapha Hassan Usman - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-08 13:09:59\",\"ref\":\"#SO-20251208-000136\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":5500,\"quantity\":214.6,\"subtotal\":1180300},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":5500,\"quantity\":5.2,\"subtotal\":28600},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":5500,\"quantity\":9.2,\"subtotal\":50599.99999999999},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":5500,\"quantity\":7.2,\"subtotal\":39600},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":5500,\"quantity\":5.2,\"subtotal\":28600},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - mainsheet\",\"unit_price\":5500,\"quantity\":3.2,\"subtotal\":17600},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - cladding\",\"unit_price\":5500,\"quantity\":11.1,\"subtotal\":61050},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - cladding\",\"unit_price\":5500,\"quantity\":14.8,\"subtotal\":81400},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":5500,\"quantity\":35,\"subtotal\":192500}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":1680250,\"paid\":0,\"due\":1680250,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 1680250.00, 0.00, 0.00, 1680250.00, 0.00, 'paid', '2025-12-08 13:09:59', '2025-12-09 08:33:59', '59613054ee53a85ed06d94c63c712eccf50c0539250f9b38ab2453fa2dd752fe'),
(88, 137, NULL, NULL, 33, 'INV-2025-000054', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"22\",\"name\":\"Mohammed Alabi - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-08 13:20:19\",\"ref\":\"#SO-20251208-000137\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS281\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":3300,\"quantity\":21,\"subtotal\":69300}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":69300,\"paid\":0,\"due\":69300,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 69300.00, 0.00, 0.00, 69300.00, 0.00, 'paid', '2025-12-08 13:20:19', '2025-12-11 08:00:17', 'e0a80b6c95b8e10a185e4f0c4b22850e696219b1e07f2345d21a2efd63f418ec'),
(89, 138, NULL, NULL, 34, 'INV-2025-000055', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"22\",\"name\":\"Mohammed Alabi - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-09 08:08:55\",\"ref\":\"#SO-20251209-000138\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS281\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":3300,\"quantity\":21,\"subtotal\":69300}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":69300,\"paid\":0,\"due\":69300,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 69300.00, 0.00, 0.00, 69300.00, 0.00, 'paid', '2025-12-09 08:08:55', '2025-12-09 08:32:55', '2a805c1a5f8d249a53fae22708cf7ee19970ebecb3d7ba2a88acf4cd53342627'),
(90, 139, NULL, NULL, 35, 'INV-2025-000056', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"28\",\"name\":\"Anayo Joseph - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-09 10:12:10\",\"ref\":\"#SO-20251209-000139\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"D21\",\"description\":\"Aluminium coil - flatsheet\",\"unit_price\":4600,\"quantity\":4,\"subtotal\":18400}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":18400,\"paid\":0,\"due\":18400,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 18400.00, 0.00, 0.00, 18400.00, 0.00, 'paid', '2025-12-09 10:12:10', '2025-12-09 10:20:32', 'f00389f938352c9a2c6197c82ce9c5605fd35e6e3382de4fa64cc0d670d2d9ea'),
(91, 140, NULL, NULL, 36, 'INV-2025-000057', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"29\",\"name\":\"Ukwueze Anthony - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-09 10:13:36\",\"ref\":\"#SO-20251209-000140\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"D47\",\"description\":\"Aluminium coil - flatsheet\",\"unit_price\":4700,\"quantity\":35,\"subtotal\":164500}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":164500,\"paid\":0,\"due\":164500,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 164500.00, 0.00, 0.00, 164500.00, 0.00, 'paid', '2025-12-09 10:13:36', '2025-12-09 10:20:10', '8201a5c91af0300ba2f4b0464c88cc808c59f4e782b3c9c6961667d027e546e9'),
(92, 141, NULL, NULL, 37, 'INV-2025-000058', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"30\",\"name\":\"Kalu Uko Austine - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-09 10:18:22\",\"ref\":\"#SO-20251209-000141\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"D189\",\"description\":\"Aluminium coil - flatsheet\",\"unit_price\":4700,\"quantity\":4,\"subtotal\":18800}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":18800,\"paid\":0,\"due\":18800,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 18800.00, 0.00, 0.00, 18800.00, 0.00, 'paid', '2025-12-09 10:18:22', '2025-12-09 10:19:44', '61d25b33eb73be57d75648643fecc4b29637e52557f8dd85d81deb470ff74621'),
(93, 143, NULL, NULL, NULL, 'INV-2025-000059', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 10:58:30\\\",\\\"ref\\\":\\\"#SO-20251209-000143\\\",\\\"sale_id\\\":\\\"143\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS358 - Alusteel coil\\\",\\\"quantity\\\":3380,\\\"qty_text\\\":\\\"3,380.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5915000}],\\\"subtotal\\\":5915000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5915000,\\\"paid\\\":0,\\\"due\\\":5915000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5915000.00, 0.00, 0.00, 5915000.00, 0.00, 'paid', '2025-12-09 10:58:30', '2025-12-11 07:59:47', '6eeb7c42f4632e0ba0ee5593f616237eb84652c230f0b3334790bae3dfd89819'),
(94, 145, NULL, NULL, NULL, 'INV-2025-000060', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:04:37\\\",\\\"ref\\\":\\\"#SO-20251209-000145\\\",\\\"sale_id\\\":\\\"145\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS377 - Alusteel coil\\\",\\\"quantity\\\":3302,\\\"qty_text\\\":\\\"3,302.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5778500}],\\\"subtotal\\\":5778500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5778500,\\\"paid\\\":0,\\\"due\\\":5778500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5778500.00, 0.00, 0.00, 5778500.00, 0.00, 'paid', '2025-12-09 11:04:37', '2025-12-11 07:55:11', '983abfaf4e698f34830f3709b63c82f34454139aa1bfcd839acdd0d9acd5d23b'),
(95, 147, NULL, NULL, NULL, 'INV-2025-000061', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:06:42\\\",\\\"ref\\\":\\\"#SO-20251209-000147\\\",\\\"sale_id\\\":\\\"147\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS378 - Alusteel coil\\\",\\\"quantity\\\":3436,\\\"qty_text\\\":\\\"3,436.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":6013000}],\\\"subtotal\\\":6013000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":6013000,\\\"paid\\\":0,\\\"due\\\":6013000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 6013000.00, 0.00, 0.00, 6013000.00, 0.00, 'paid', '2025-12-09 11:06:42', '2025-12-11 07:53:53', 'd90e66aaa3e0252b58ef2971bf046c183a47f9c4cae6cfad07b05017f0f0eb41'),
(96, 149, NULL, NULL, NULL, 'INV-2025-000062', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:10:36\\\",\\\"ref\\\":\\\"#SO-20251209-000149\\\",\\\"sale_id\\\":\\\"149\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS389 - Alusteel coil\\\",\\\"quantity\\\":3312,\\\"qty_text\\\":\\\"3,312.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5796000}],\\\"subtotal\\\":5796000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5796000,\\\"paid\\\":0,\\\"due\\\":5796000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5796000.00, 0.00, 0.00, 5796000.00, 0.00, 'paid', '2025-12-09 11:10:36', '2025-12-11 07:53:20', '26d982268797f123cb196441bc1b1c88cefa1c565c5091e80982fee7cc681ab5'),
(97, 151, NULL, NULL, NULL, 'INV-2025-000063', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:13:22\\\",\\\"ref\\\":\\\"#SO-20251209-000151\\\",\\\"sale_id\\\":\\\"151\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS388 - Alusteel coil\\\",\\\"quantity\\\":3190,\\\"qty_text\\\":\\\"3,190.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5582500}],\\\"subtotal\\\":5582500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5582500,\\\"paid\\\":0,\\\"due\\\":5582500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5582500.00, 0.00, 0.00, 5582500.00, 0.00, 'paid', '2025-12-09 11:13:22', '2025-12-11 07:52:52', '5bd5fab5a175616a8fd3b5a98f647b34bb890500af58adc79f3096ef492cc846'),
(98, 153, NULL, NULL, NULL, 'INV-2025-000064', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:21:22\\\",\\\"ref\\\":\\\"#SO-20251209-000153\\\",\\\"sale_id\\\":\\\"153\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS259 - Alusteel coil\\\",\\\"quantity\\\":3300,\\\"qty_text\\\":\\\"3,300.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5775000}],\\\"subtotal\\\":5775000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5775000,\\\"paid\\\":0,\\\"due\\\":5775000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5775000.00, 0.00, 0.00, 5775000.00, 0.00, 'paid', '2025-12-09 11:21:22', '2025-12-11 07:52:28', '42b1b2efac21dfb47765067aada38bb086a4c193b97198d52a83becb753f6392'),
(99, 155, NULL, NULL, NULL, 'INV-2025-000065', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Supreme Global\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:24:43\\\",\\\"ref\\\":\\\"#SO-20251209-000155\\\",\\\"sale_id\\\":\\\"155\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"A19 - Aluminium coil\\\",\\\"quantity\\\":1910,\\\"qty_text\\\":\\\"1,910.00 kg\\\",\\\"unit_price\\\":6300,\\\"subtotal\\\":12033000}],\\\"subtotal\\\":12033000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":12033000,\\\"paid\\\":0,\\\"due\\\":12033000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 12033000.00, 0.00, 0.00, 12033000.00, 0.00, 'paid', '2025-12-09 11:24:43', '2025-12-11 07:51:50', '8f6a2af5841e05cc1a51b2bf38bf632deef7d41e640bdab26bb44d07dc81335b'),
(100, 157, NULL, NULL, NULL, 'INV-2025-000066', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Supreme Global\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:27:13\\\",\\\"ref\\\":\\\"#SO-20251209-000157\\\",\\\"sale_id\\\":\\\"157\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"A9 - Aluminium coil\\\",\\\"quantity\\\":1777,\\\"qty_text\\\":\\\"1,777.00 kg\\\",\\\"unit_price\\\":6300,\\\"subtotal\\\":11195100}],\\\"subtotal\\\":11195100,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":11195100,\\\"paid\\\":0,\\\"due\\\":11195100,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 11195100.00, 0.00, 0.00, 11195100.00, 0.00, 'paid', '2025-12-09 11:27:13', '2025-12-11 07:51:26', '161cd224ee24de0ad282d818aea2198d06047adda8bdd8e61c765eb1a68a3947'),
(101, 159, NULL, NULL, NULL, 'INV-2025-000067', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Yusuf Shamsudeen\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:32:26\\\",\\\"ref\\\":\\\"#SO-20251209-000159\\\",\\\"sale_id\\\":\\\"159\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS375 - Alusteel coil\\\",\\\"quantity\\\":2976,\\\"qty_text\\\":\\\"2,976.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5208000}],\\\"subtotal\\\":5208000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5208000,\\\"paid\\\":0,\\\"due\\\":5208000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5208000.00, 0.00, 0.00, 5208000.00, 0.00, 'paid', '2025-12-09 11:32:26', '2025-12-11 07:51:03', 'b0c00aa3136d6afdc4a9668f4cbbd1909fa09fd123a13d284f98f4e253e138a6'),
(102, 161, NULL, NULL, NULL, 'INV-2025-000068', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Yusuf Shamsudeen\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:34:29\\\",\\\"ref\\\":\\\"#SO-20251209-000161\\\",\\\"sale_id\\\":\\\"161\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS362 - Alusteel coil\\\",\\\"quantity\\\":3172,\\\"qty_text\\\":\\\"3,172.00 kg\\\",\\\"unit_price\\\":1780,\\\"subtotal\\\":5646160}],\\\"subtotal\\\":5646160,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5646160,\\\"paid\\\":0,\\\"due\\\":5646160,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5646160.00, 0.00, 0.00, 5646160.00, 0.00, 'paid', '2025-12-09 11:34:29', '2025-12-11 07:50:36', '8189b360d684607a98385bb973707db138a0500ae6531e00fd809331dd741646'),
(103, 163, NULL, NULL, NULL, 'INV-2025-000069', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Innovative Global\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:37:57\\\",\\\"ref\\\":\\\"#SO-20251209-000163\\\",\\\"sale_id\\\":\\\"163\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"A8 - Aluminium coil\\\",\\\"quantity\\\":1772,\\\"qty_text\\\":\\\"1,772.00 kg\\\",\\\"unit_price\\\":6300,\\\"subtotal\\\":11163600}],\\\"subtotal\\\":11163600,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":11163600,\\\"paid\\\":0,\\\"due\\\":11163600,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 11163600.00, 0.00, 0.00, 11163600.00, 0.00, 'paid', '2025-12-09 11:37:57', '2025-12-11 07:50:10', '186859321df18502eaabe449bf01ba97fe9625c789fc5211166afd52ad908830'),
(104, 165, NULL, NULL, NULL, 'INV-2025-000070', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Innovative Global\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:39:40\\\",\\\"ref\\\":\\\"#SO-20251209-000165\\\",\\\"sale_id\\\":\\\"165\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"A6 - Aluminium coil\\\",\\\"quantity\\\":1899,\\\"qty_text\\\":\\\"1,899.00 kg\\\",\\\"unit_price\\\":6300,\\\"subtotal\\\":11963700}],\\\"subtotal\\\":11963700,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":11963700,\\\"paid\\\":0,\\\"due\\\":11963700,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 11963700.00, 0.00, 0.00, 11963700.00, 0.00, 'paid', '2025-12-09 11:39:40', '2025-12-11 07:49:43', 'c91d4c5a810e6bb1749bbacf115d77f13bc60113130e6f3b25a94e53cb5f0aa4'),
(105, 167, NULL, NULL, NULL, 'INV-2025-000071', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Usman Madign\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:43:57\\\",\\\"ref\\\":\\\"#SO-20251209-000167\\\",\\\"sale_id\\\":\\\"167\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"A27 - Aluminium coil\\\",\\\"quantity\\\":1813,\\\"qty_text\\\":\\\"1,813.00 kg\\\",\\\"unit_price\\\":6300,\\\"subtotal\\\":11421900}],\\\"subtotal\\\":11421900,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":11421900,\\\"paid\\\":0,\\\"due\\\":11421900,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 11421900.00, 0.00, 0.00, 11421900.00, 0.00, 'paid', '2025-12-09 11:43:57', '2025-12-11 07:49:11', '7631f1dec298a2c706cd50c68294cc389dd5ad86dd6a84b1ca4c6e327c4ecf18');
INSERT INTO `invoices` (`id`, `sale_id`, `sale_type`, `sale_reference_id`, `production_id`, `invoice_number`, `invoice_shape`, `subtotal`, `tax_type`, `tax_value`, `tax_amount`, `discount_type`, `discount_value`, `discount_amount`, `total`, `tax`, `other_charges`, `paid_amount`, `shipping`, `status`, `created_at`, `updated_at`, `immutable_hash`) VALUES
(106, 169, NULL, NULL, NULL, 'INV-2025-000072', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Usman Madign\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:45:38\\\",\\\"ref\\\":\\\"#SO-20251209-000169\\\",\\\"sale_id\\\":\\\"169\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"A15 - Aluminium coil\\\",\\\"quantity\\\":1817,\\\"qty_text\\\":\\\"1,817.00 kg\\\",\\\"unit_price\\\":6300,\\\"subtotal\\\":11447100}],\\\"subtotal\\\":11447100,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":11447100,\\\"paid\\\":0,\\\"due\\\":11447100,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 11447100.00, 0.00, 0.00, 11447100.00, 0.00, 'paid', '2025-12-09 11:45:38', '2025-12-11 07:48:39', '34d62acdec2fb2e39a8a4e9d5e742da5d3aa0e5755aa036738d58e78ba4a77af'),
(107, 171, NULL, NULL, NULL, 'INV-2025-000073', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:49:10\\\",\\\"ref\\\":\\\"#SO-20251209-000171\\\",\\\"sale_id\\\":\\\"171\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS257 - Alusteel coil\\\",\\\"quantity\\\":3578,\\\"qty_text\\\":\\\"3,578.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":6261500}],\\\"subtotal\\\":6261500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":6261500,\\\"paid\\\":0,\\\"due\\\":6261500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 6261500.00, 0.00, 0.00, 6261500.00, 0.00, 'paid', '2025-12-09 11:49:10', '2025-12-11 07:47:41', '3bad9f91fa475f887446e3a3b2d2c5b31f9b21f2ce08c46ef8be9425787aa1d6'),
(108, 173, NULL, NULL, NULL, 'INV-2025-000074', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:51:16\\\",\\\"ref\\\":\\\"#SO-20251209-000173\\\",\\\"sale_id\\\":\\\"173\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS262 - Alusteel coil\\\",\\\"quantity\\\":3492,\\\"qty_text\\\":\\\"3,492.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":6111000}],\\\"subtotal\\\":6111000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":6111000,\\\"paid\\\":0,\\\"due\\\":6111000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 6111000.00, 0.00, 0.00, 6111000.00, 0.00, 'paid', '2025-12-09 11:51:16', '2025-12-10 19:39:02', 'afdb734135c52cf5f2f6921268ced7607e634010d8471a1c3924fe5e8f5166fe'),
(109, 175, NULL, NULL, NULL, 'INV-2025-000075', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Usman Madign\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:53:35\\\",\\\"ref\\\":\\\"#SO-20251209-000175\\\",\\\"sale_id\\\":\\\"175\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS264 - Alusteel coil\\\",\\\"quantity\\\":3272,\\\"qty_text\\\":\\\"3,272.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5726000}],\\\"subtotal\\\":5726000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5726000,\\\"paid\\\":0,\\\"due\\\":5726000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5726000.00, 0.00, 0.00, 5726000.00, 0.00, 'paid', '2025-12-09 11:53:35', '2025-12-09 14:44:44', 'fc89ee643b08fc5ba0e1ade35f21f85c1ad9e1aa5544248e38a3a7589ed65d51'),
(110, 177, NULL, NULL, NULL, 'INV-2025-000076', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:57:14\\\",\\\"ref\\\":\\\"#SO-20251209-000177\\\",\\\"sale_id\\\":\\\"177\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS104 - Alusteel coil\\\",\\\"quantity\\\":3602,\\\"qty_text\\\":\\\"3,602.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":6303500}],\\\"subtotal\\\":6303500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":6303500,\\\"paid\\\":0,\\\"due\\\":6303500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 6303500.00, 0.00, 0.00, 6303500.00, 0.00, 'paid', '2025-12-09 11:57:14', '2025-12-09 14:44:01', '836eedadd70d9c5799fc269c81294c5870acd9751e375cf1a3f1516095128dcd'),
(111, 179, NULL, NULL, NULL, 'INV-2025-000077', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 11:59:40\\\",\\\"ref\\\":\\\"#SO-20251209-000179\\\",\\\"sale_id\\\":\\\"179\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS105 - Alusteel coil\\\",\\\"quantity\\\":3212,\\\"qty_text\\\":\\\"3,212.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5621000}],\\\"subtotal\\\":5621000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5621000,\\\"paid\\\":0,\\\"due\\\":5621000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5621000.00, 0.00, 0.00, 5621000.00, 0.00, 'paid', '2025-12-09 11:59:40', '2025-12-09 14:43:33', 'ed537d9fce900f74dfe708fd577fc37a18838cec68fbbc53b5c44d90c3be179e'),
(112, 181, NULL, NULL, NULL, 'INV-2025-000078', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 12:01:55\\\",\\\"ref\\\":\\\"#SO-20251209-000181\\\",\\\"sale_id\\\":\\\"181\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS114 - Alusteel coil\\\",\\\"quantity\\\":3426,\\\"qty_text\\\":\\\"3,426.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5995500}],\\\"subtotal\\\":5995500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5995500,\\\"paid\\\":0,\\\"due\\\":5995500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5995500.00, 0.00, 0.00, 5995500.00, 0.00, 'paid', '2025-12-09 12:01:55', '2025-12-09 14:43:08', 'ec50b1fd1183e675fc238d75a9e6ef6bb1c94fbef2bc8f371873e25af8d56cfb'),
(113, 183, NULL, NULL, NULL, 'INV-2025-000079', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 12:04:16\\\",\\\"ref\\\":\\\"#SO-20251209-000183\\\",\\\"sale_id\\\":\\\"183\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS115 - Alusteel coil\\\",\\\"quantity\\\":3368,\\\"qty_text\\\":\\\"3,368.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5894000}],\\\"subtotal\\\":5894000,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5894000,\\\"paid\\\":0,\\\"due\\\":5894000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5894000.00, 0.00, 0.00, 5894000.00, 0.00, 'paid', '2025-12-09 12:04:16', '2025-12-09 14:42:44', 'b6690f6e62980d3ad7dc82bb0b616e18cc20e240b0f41c820f6b3cbcb33238d5'),
(114, 185, NULL, NULL, NULL, 'INV-2025-000080', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 12:08:48\\\",\\\"ref\\\":\\\"#SO-20251209-000185\\\",\\\"sale_id\\\":\\\"185\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS227 - Alusteel coil\\\",\\\"quantity\\\":3474,\\\"qty_text\\\":\\\"3,474.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":6079500}],\\\"subtotal\\\":6079500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":6079500,\\\"paid\\\":0,\\\"due\\\":6079500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 6079500.00, 0.00, 0.00, 6079500.00, 0.00, 'paid', '2025-12-09 12:08:48', '2025-12-09 14:42:00', 'eba18d81b9ac25ab20494b183600caa5c46fc6d861c203433c8d0bf14d9bdd94'),
(115, 187, NULL, NULL, NULL, 'INV-2025-000081', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 12:11:21\\\",\\\"ref\\\":\\\"#SO-20251209-000187\\\",\\\"sale_id\\\":\\\"187\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS138 - Alusteel coil\\\",\\\"quantity\\\":3366,\\\"qty_text\\\":\\\"3,366.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5890500}],\\\"subtotal\\\":5890500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5890500,\\\"paid\\\":0,\\\"due\\\":5890500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5890500.00, 0.00, 0.00, 5890500.00, 0.00, 'paid', '2025-12-09 12:11:21', '2025-12-09 14:41:32', '2c7086d11f9482aee21b9ecc22ca2b4ad6fbad48d3fec5edf83cdf5263f6b6b5'),
(116, 189, NULL, NULL, NULL, 'INV-2025-000082', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 12:21:12\\\",\\\"ref\\\":\\\"#SO-20251209-000189\\\",\\\"sale_id\\\":\\\"189\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS382 - Alusteel coil\\\",\\\"quantity\\\":3134,\\\"qty_text\\\":\\\"3,134.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5484500}],\\\"subtotal\\\":5484500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5484500,\\\"paid\\\":0,\\\"due\\\":5484500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5484500.00, 0.00, 0.00, 5484500.00, 0.00, 'paid', '2025-12-09 12:21:12', '2025-12-09 14:40:22', '9d755266da94cf9cbf8d7ecc9cfe8c09278ccf32c9f3083d51907065867d65fc'),
(117, 191, NULL, NULL, NULL, 'INV-2025-000083', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Ibrahimama Alum\\\",\\\"company\\\":\\\"\\\",\\\"email\\\":\\\"\\\",\\\"phone\\\":\\\"08098434014\\\",\\\"address\\\":\\\"\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-12-09 12:27:20\\\",\\\"ref\\\":\\\"#SO-20251209-000191\\\",\\\"sale_id\\\":\\\"191\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS256 - Alusteel coil\\\",\\\"quantity\\\":3302,\\\"qty_text\\\":\\\"3,302.00 kg\\\",\\\"unit_price\\\":1750,\\\"subtotal\\\":5778500}],\\\"subtotal\\\":5778500,\\\"order_tax\\\":0,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":5778500,\\\"paid\\\":0,\\\"due\\\":5778500,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 5778500.00, 0.00, 0.00, 5778500.00, 0.00, 'paid', '2025-12-09 12:27:20', '2025-12-09 14:39:03', '8b606d579d5420cf6c7cdc4a382919de9b5a49871356cdd0c67d507f3d3a5213'),
(118, 192, NULL, NULL, 38, 'INV-2025-000084', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"26\",\"name\":\"Uche - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-09 12:37:23\",\"ref\":\"#SO-20251209-000192\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS204\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":3400,\"quantity\":18,\"subtotal\":61200}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":61200,\"paid\":0,\"due\":61200,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 61200.00, 0.00, 0.00, 61200.00, 0.00, 'paid', '2025-12-09 12:37:23', '2025-12-09 14:38:27', '41ef35fe11170c6af0e7ce97d8e18409f58109b29b926415b01c78e602f9c0a4'),
(119, 193, NULL, NULL, 39, 'INV-2025-000085', '{\"company\":{\"name\":\"Obumek Alluminium Company Ltd.\",\"address\":\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\",\"phone\":\"+2348065336645\",\"email\":\"info@obumekalluminium.com\"},\"customer\":{\"id\":\"36\",\"name\":\"Kingsley - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"meta\":{\"date\":\"2025-12-11 14:50:18\",\"ref\":\"#SO-20251211-000193\",\"payment_status\":\"Unpaid\"},\"items\":[{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":5500,\"quantity\":102,\"subtotal\":561000},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":5500,\"quantity\":53.6,\"subtotal\":294800},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":5500,\"quantity\":22.4,\"subtotal\":123199.99999999999},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":5500,\"quantity\":18.4,\"subtotal\":101199.99999999999},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":5500,\"quantity\":14.4,\"subtotal\":79200},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":5500,\"quantity\":11.2,\"subtotal\":61599.99999999999},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":5500,\"quantity\":7.2,\"subtotal\":39600},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":5500,\"quantity\":19.6,\"subtotal\":107800.00000000001},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":5500,\"quantity\":12.8,\"subtotal\":70400},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - cladding\",\"unit_price\":5500,\"quantity\":16.5,\"subtotal\":90750},{\"product_code\":\"AS258\",\"description\":\"Alusteel coil - flatsheet\",\"unit_price\":5500,\"quantity\":27,\"subtotal\":148500}],\"order_tax\":0,\"discount\":0,\"shipping\":0,\"grand_total\":1678050,\"paid\":0,\"due\":1678050,\"notes\":{\"receipt_statement\":\"Received the above goods in good condition.\",\"refund_policy\":\"No refund of money after payment\",\"custom_notes\":\"\"},\"signatures\":{\"customer\":null,\"for_company\":\"Obumek Alluminium Company Ltd.\"}}', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 1678050.00, 0.00, 0.00, 1678050.00, 0.00, 'paid', '2025-12-11 14:50:18', '2025-12-11 14:54:09', '1664202f287090e30212e1c7a7dcbe55e595c01234767e403e4aa78be4185395');

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
(22, 126, 4, '{\"production_reference\":\"PR-20251208-0126\",\"sale_id\":\"126\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"17\",\"name\":\"Emmauel - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"659\",\"coil\":{\"id\":\"659\",\"code\":\"AS232\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"weight\":\"3340.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":1,\"sheet_meter\":9,\"meters\":9,\"quantity\":0,\"pieces\":0,\"unit_price\":3300,\"row_subtotal\":29700}],\"total_meters\":9,\"total_amount\":29700,\"created_at\":\"2025-12-08 11:24:20\"}', 'completed', 5, '2025-12-08 11:24:20', NULL, '7350e0d3748c4eb2d0e3cde65c86ccc1fd28b5c2a0255ffd91bbacaf380e6390'),
(23, 127, 4, '{\"production_reference\":\"PR-20251208-0127\",\"sale_id\":\"127\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"18\",\"name\":\"Stevoo Alum - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"649\",\"coil\":{\"id\":\"649\",\"code\":\"AS52\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"weight\":\"3744.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":11,\"sheet_meter\":3.7,\"meters\":40.7,\"quantity\":0,\"pieces\":0,\"unit_price\":4300,\"row_subtotal\":175010}],\"total_meters\":40.7,\"total_amount\":175010,\"created_at\":\"2025-12-08 11:42:44\"}', 'completed', 5, '2025-12-08 11:42:44', NULL, '6764fa898c940e27844d15f484855a5ad874d69c4f406b2af3e29a7d8fdfa4d7'),
(24, 128, 4, '{\"production_reference\":\"PR-20251208-0128\",\"sale_id\":\"128\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"19\",\"name\":\"Abdulazeez Adamu - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"211\",\"coil\":{\"id\":\"211\",\"code\":\"AS251\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"weight\":\"3174.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":4,\"sheet_meter\":3.7,\"meters\":14.8,\"quantity\":0,\"pieces\":0,\"unit_price\":4300,\"row_subtotal\":63640},{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":1,\"sheet_meter\":1.5,\"meters\":1.5,\"quantity\":0,\"pieces\":0,\"unit_price\":4300,\"row_subtotal\":6450}],\"total_meters\":16.3,\"total_amount\":70090,\"created_at\":\"2025-12-08 11:54:11\"}', 'completed', 5, '2025-12-08 11:54:11', NULL, 'fe0b53d5fc9e3b47452529070a8bc2cc4514f24fa65b326ff5532dfc9ebb1627'),
(25, 129, 4, '{\"production_reference\":\"PR-20251208-0129\",\"sale_id\":\"129\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"20\",\"name\":\"Aminu Haruna - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"665\",\"coil\":{\"id\":\"665\",\"code\":\"AS128\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"weight\":\"3054.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":1,\"sheet_meter\":30,\"meters\":30,\"quantity\":0,\"pieces\":0,\"unit_price\":3300,\"row_subtotal\":99000}],\"total_meters\":30,\"total_amount\":99000,\"created_at\":\"2025-12-08 12:07:13\"}', 'completed', 5, '2025-12-08 12:07:13', NULL, 'd3c7e70975dcc5cfb8de35496a4868032c15b67e93809039c1abf559965bb634'),
(26, 130, 4, '{\"production_reference\":\"PR-20251208-0130\",\"sale_id\":\"130\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"21\",\"name\":\"Abba - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"659\",\"coil\":{\"id\":\"659\",\"code\":\"AS232\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"weight\":\"3340.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":1,\"sheet_meter\":18,\"meters\":18,\"quantity\":0,\"pieces\":0,\"unit_price\":3200,\"row_subtotal\":57600}],\"total_meters\":18,\"total_amount\":57600,\"created_at\":\"2025-12-08 12:11:26\"}', 'completed', 5, '2025-12-08 12:11:26', NULL, 'ec4d3c10c1306a48284fafcdc74748fb91b8410be7b75ebea215803bcc80f415'),
(27, 131, 4, '{\"production_reference\":\"PR-20251208-0131\",\"sale_id\":\"131\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"22\",\"name\":\"Mohammed Alabi - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"656\",\"coil\":{\"id\":\"656\",\"code\":\"AS281\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"weight\":\"2566.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":1,\"sheet_meter\":21,\"meters\":21,\"quantity\":0,\"pieces\":0,\"unit_price\":3300,\"row_subtotal\":69300}],\"total_meters\":21,\"total_amount\":69300,\"created_at\":\"2025-12-08 12:18:20\"}', 'completed', 5, '2025-12-08 12:18:20', NULL, '64752d26ade006a0bb687898ffd2db451e6231585c64b6813f1a849809f2d37d'),
(28, 132, 4, '{\"production_reference\":\"PR-20251208-0132\",\"sale_id\":\"132\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"22\",\"name\":\"Mohammed Alabi - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"656\",\"coil\":{\"id\":\"656\",\"code\":\"AS281\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"weight\":\"2566.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":1,\"sheet_meter\":21,\"meters\":21,\"quantity\":0,\"pieces\":0,\"unit_price\":3300,\"row_subtotal\":69300}],\"total_meters\":21,\"total_amount\":69300,\"created_at\":\"2025-12-08 12:30:36\"}', 'completed', 5, '2025-12-08 12:30:36', NULL, '2cf436764c4923ac705186a4360aeefd13593162ed8110f559d7b66af70b8df2'),
(29, 133, 4, '{\"production_reference\":\"PR-20251208-0133\",\"sale_id\":\"133\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"23\",\"name\":\"Shuaibu Mohammed - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"659\",\"coil\":{\"id\":\"659\",\"code\":\"AS232\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"weight\":\"3340.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":9,\"sheet_meter\":3.5,\"meters\":31.5,\"quantity\":0,\"pieces\":0,\"unit_price\":3200,\"row_subtotal\":100800}],\"total_meters\":31.5,\"total_amount\":100800,\"created_at\":\"2025-12-08 12:43:50\"}', 'completed', 5, '2025-12-08 12:43:50', NULL, '2a55051b0ec0b7a29c86aac98ef4c3df3198154574942c5e122014c23edbffb3'),
(30, 134, 4, '{\"production_reference\":\"PR-20251208-0134\",\"sale_id\":\"134\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"24\",\"name\":\"Ahmad Haruna - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"659\",\"coil\":{\"id\":\"659\",\"code\":\"AS232\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"weight\":\"3340.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":1,\"sheet_meter\":36,\"meters\":36,\"quantity\":0,\"pieces\":0,\"unit_price\":3700,\"row_subtotal\":133200}],\"total_meters\":36,\"total_amount\":133200,\"created_at\":\"2025-12-08 12:51:09\"}', 'completed', 5, '2025-12-08 12:51:09', NULL, 'c7eca90240ee0ebe36cbc9a0cc29d04e2b7b4e60b20f479de5145e157868e7a3'),
(31, 135, 4, '{\"production_reference\":\"PR-20251208-0135\",\"sale_id\":\"135\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"25\",\"name\":\"Bawa Zarema - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"658\",\"coil\":{\"id\":\"658\",\"code\":\"AS289\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"weight\":\"2688.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":1,\"sheet_meter\":24,\"meters\":24,\"quantity\":0,\"pieces\":0,\"unit_price\":4200,\"row_subtotal\":100800}],\"total_meters\":24,\"total_amount\":100800,\"created_at\":\"2025-12-08 12:54:04\"}', 'completed', 5, '2025-12-08 12:54:04', NULL, 'e4e5c0d67654b6702ecd7ffa3ad7207ac9a4b413f3c5475cdec0cd7fbfe16f63'),
(32, 136, 4, '{\"production_reference\":\"PR-20251208-0136\",\"sale_id\":\"136\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"27\",\"name\":\"Mustapha Hassan Usman - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"660\",\"coil\":{\"id\":\"660\",\"code\":\"AS258\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"weight\":\"3298.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":37,\"sheet_meter\":5.8,\"meters\":214.6,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":1180300},{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":2,\"sheet_meter\":2.6,\"meters\":5.2,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":28600},{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":4,\"sheet_meter\":2.3,\"meters\":9.2,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":50599.99999999999},{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":4,\"sheet_meter\":1.8,\"meters\":7.2,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":39600},{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":4,\"sheet_meter\":1.3,\"meters\":5.2,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":28600},{\"property_id\":\"mainsheet\",\"label\":\"Mainsheet\",\"sheet_qty\":4,\"sheet_meter\":0.8,\"meters\":3.2,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":17600},{\"property_id\":\"cladding\",\"label\":\"Cladding\",\"sheet_qty\":37,\"sheet_meter\":0.3,\"meters\":11.1,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":61050},{\"property_id\":\"cladding\",\"label\":\"Cladding\",\"sheet_qty\":37,\"sheet_meter\":0.4,\"meters\":14.8,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":81400},{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":1,\"sheet_meter\":35,\"meters\":35,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":192500}],\"total_meters\":305.49999999999994,\"total_amount\":1680250,\"created_at\":\"2025-12-08 13:09:59\"}', 'completed', 5, '2025-12-08 13:09:59', NULL, '1155bf639e9d2e3bacd95961d8ece12f4a64b3031bab4fae337c4fc4052d6f34'),
(33, 137, 4, '{\"production_reference\":\"PR-20251208-0137\",\"sale_id\":\"137\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"22\",\"name\":\"Mohammed Alabi - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"656\",\"coil\":{\"id\":\"656\",\"code\":\"AS281\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"weight\":\"2566.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":1,\"sheet_meter\":21,\"meters\":21,\"quantity\":0,\"pieces\":0,\"unit_price\":3300,\"row_subtotal\":69300}],\"total_meters\":21,\"total_amount\":69300,\"created_at\":\"2025-12-08 13:20:19\"}', 'completed', 5, '2025-12-08 13:20:19', NULL, 'f73f51352591dd86ced973006f567a78f95c68678e0638f3946d55e4fd3c9950'),
(34, 138, 4, '{\"production_reference\":\"PR-20251209-0138\",\"sale_id\":\"138\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"22\",\"name\":\"Mohammed Alabi - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"656\",\"coil\":{\"id\":\"656\",\"code\":\"AS281\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"weight\":\"2566.00\",\"status\":\"out_of_stock\"},\"properties\":[{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":1,\"sheet_meter\":21,\"meters\":21,\"quantity\":0,\"pieces\":0,\"unit_price\":3300,\"row_subtotal\":69300}],\"total_meters\":21,\"total_amount\":69300,\"created_at\":\"2025-12-09 08:08:55\"}', 'completed', 5, '2025-12-09 08:08:55', NULL, 'c3423be8b7a62fcadfd0a35e6bff63b9ef9038d8c81b3a407499adb874cd977c'),
(35, 139, 4, '{\"production_reference\":\"PR-20251209-0139\",\"sale_id\":\"139\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"28\",\"name\":\"Anayo Joseph - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"635\",\"coil\":{\"id\":\"635\",\"code\":\"D21\",\"name\":\"Aluminium coil\",\"category\":\"aluminum\",\"color_name\":\"I\\/Beige\",\"weight\":\"1620.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":1,\"sheet_meter\":4,\"meters\":4,\"quantity\":0,\"pieces\":0,\"unit_price\":4600,\"row_subtotal\":18400}],\"total_meters\":4,\"total_amount\":18400,\"created_at\":\"2025-12-09 10:12:10\"}', 'completed', 5, '2025-12-09 10:12:10', NULL, 'f22e8f3ed8cff1fa087b3b9106480a841c5ab2942916f24296434a8748e51e9a'),
(36, 140, 4, '{\"production_reference\":\"PR-20251209-0140\",\"sale_id\":\"140\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"29\",\"name\":\"Ukwueze Anthony - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"636\",\"coil\":{\"id\":\"636\",\"code\":\"D47\",\"name\":\"Aluminium coil\",\"category\":\"aluminum\",\"color_name\":\"G\\/Beige\",\"weight\":\"1642.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":1,\"sheet_meter\":35,\"meters\":35,\"quantity\":0,\"pieces\":0,\"unit_price\":4700,\"row_subtotal\":164500}],\"total_meters\":35,\"total_amount\":164500,\"created_at\":\"2025-12-09 10:13:36\"}', 'completed', 5, '2025-12-09 10:13:36', NULL, '03451e6d8d68eaadfe5fb13f715e55d9938850ff77a00942ba342a7d7adfc3ab'),
(37, 141, 4, '{\"production_reference\":\"PR-20251209-0141\",\"sale_id\":\"141\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"30\",\"name\":\"Kalu Uko Austine - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"640\",\"coil\":{\"id\":\"640\",\"code\":\"D189\",\"name\":\"Aluminium coil\",\"category\":\"aluminum\",\"color_name\":\"N\\/brown\",\"weight\":\"2219.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":1,\"sheet_meter\":4,\"meters\":4,\"quantity\":0,\"pieces\":0,\"unit_price\":4700,\"row_subtotal\":18800}],\"total_meters\":4,\"total_amount\":18800,\"created_at\":\"2025-12-09 10:18:22\"}', 'completed', 5, '2025-12-09 10:18:22', NULL, 'e82b8b31c0ffc070127550d9e647e659ce39e033bb8d9558d02800bc4ce5528d'),
(38, 192, 4, '{\"production_reference\":\"PR-20251209-0192\",\"sale_id\":\"192\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"26\",\"name\":\"Uche - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"176\",\"coil\":{\"id\":\"176\",\"code\":\"AS204\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"color_name\":\"TC\\/Red\",\"weight\":\"3166.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":1,\"sheet_meter\":18,\"meters\":18,\"quantity\":0,\"pieces\":0,\"unit_price\":3400,\"row_subtotal\":61200}],\"total_meters\":18,\"total_amount\":61200,\"created_at\":\"2025-12-09 12:37:23\"}', 'completed', 5, '2025-12-09 12:37:23', NULL, 'f3b4a6345492ae47c3650688d334d961efae7e80d7c2640d76947171e02a9028'),
(39, 193, 4, '{\"production_reference\":\"PR-20251211-0193\",\"sale_id\":\"193\",\"warehouse_id\":\"4\",\"customer\":{\"id\":\"36\",\"name\":\"Kingsley - 08098434014\",\"phone\":\"08098434014\",\"company\":\"\",\"address\":\"\"},\"warehouse\":{\"id\":\"4\",\"name\":\"Head Office                                                                                - EA 18 - 19 , Saburi District Dei - Dei Abuja\",\"code\":\"\"},\"coil_id\":\"660\",\"coil\":{\"id\":\"660\",\"code\":\"AS258\",\"name\":\"Alusteel coil\",\"category\":\"alusteel\",\"color_name\":\"G\\/Beige\",\"weight\":\"3298.00\",\"status\":\"available\"},\"properties\":[{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":12,\"sheet_meter\":8.5,\"meters\":102,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":561000},{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":8,\"sheet_meter\":6.7,\"meters\":53.6,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":294800},{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":4,\"sheet_meter\":5.6,\"meters\":22.4,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":123199.99999999999},{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":4,\"sheet_meter\":4.6,\"meters\":18.4,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":101199.99999999999},{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":4,\"sheet_meter\":3.6,\"meters\":14.4,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":79200},{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":4,\"sheet_meter\":2.8,\"meters\":11.2,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":61599.99999999999},{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":4,\"sheet_meter\":1.8,\"meters\":7.2,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":39600},{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":4,\"sheet_meter\":4.9,\"meters\":19.6,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":107800.00000000001},{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":4,\"sheet_meter\":3.2,\"meters\":12.8,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":70400},{\"property_id\":\"cladding\",\"label\":\"Cladding\",\"sheet_qty\":55,\"sheet_meter\":0.3,\"meters\":16.5,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":90750},{\"property_id\":\"flatsheet\",\"label\":\"Flatsheet\",\"sheet_qty\":1,\"sheet_meter\":27,\"meters\":27,\"quantity\":0,\"pieces\":0,\"unit_price\":5500,\"row_subtotal\":148500}],\"total_meters\":305.09999999999997,\"total_amount\":1678050,\"created_at\":\"2025-12-11 14:50:18\"}', 'completed', 5, '2025-12-11 14:50:18', NULL, 'c2bbbaae69024fbae2af3f7b4ccac0e70fba366c15bc165c9a6d0793bb69c403');

-- --------------------------------------------------------

--
-- Table structure for table `production_properties`
--

CREATE TABLE `production_properties` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('alusteel','aluminum','kzinc') NOT NULL,
  `property_type` enum('unit_based','meter_based','bundle_based') NOT NULL,
  `is_addon` tinyint(1) DEFAULT 0,
  `calculation_method` enum('fixed','percentage','per_unit') DEFAULT 'fixed',
  `applies_to` enum('subtotal','total','per_item') DEFAULT 'total',
  `is_refundable` tinyint(1) DEFAULT 0,
  `display_section` enum('production','addon','adjustment') DEFAULT 'production',
  `default_price` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Store additional config like pieces_per_bundle, calculation_notes, etc.' CHECK (json_valid(`metadata`)),
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_properties`
--

INSERT INTO `production_properties` (`id`, `code`, `name`, `category`, `property_type`, `is_addon`, `calculation_method`, `applies_to`, `is_refundable`, `display_section`, `default_price`, `is_active`, `sort_order`, `metadata`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'mainsheet', 'Mainsheet', 'alusteel', 'meter_based', 0, 'per_unit', 'total', 0, 'production', 10300.00, 1, 1, NULL, 2, '2025-12-15 09:25:23', '2025-12-15 09:25:40', NULL),
(2, 'flatsheet', 'Flatsheet', 'alusteel', 'meter_based', 0, 'per_unit', 'total', 0, 'production', 9800.00, 1, 2, NULL, 2, '2025-12-15 09:25:23', '2025-12-15 09:25:40', NULL),
(3, 'cladding', 'Cladding', 'alusteel', 'meter_based', 0, 'per_unit', 'total', 0, 'production', 11200.00, 1, 3, NULL, 2, '2025-12-15 09:25:23', '2025-12-15 09:25:40', NULL),
(4, 'mainsheet_alu', 'Mainsheet', 'aluminum', 'meter_based', 0, 'per_unit', 'total', 0, 'production', 10300.00, 1, 1, NULL, 2, '2025-12-15 09:25:23', '2025-12-15 09:25:40', NULL),
(5, 'flatsheet_alu', 'Flatsheet', 'aluminum', 'meter_based', 0, 'per_unit', 'total', 0, 'production', 9800.00, 1, 2, NULL, 2, '2025-12-15 09:25:23', '2025-12-15 09:25:40', NULL),
(6, 'cladding_alu', 'Cladding', 'aluminum', 'meter_based', 0, 'per_unit', 'total', 0, 'production', 11200.00, 1, 3, NULL, 2, '2025-12-15 09:25:23', '2025-12-15 09:25:40', NULL),
(7, 'scraps', 'Scraps', 'kzinc', 'unit_based', 0, 'per_unit', 'total', 0, 'production', 2500.00, 1, 1, NULL, 2, '2025-12-15 09:25:23', '2025-12-15 09:25:40', NULL),
(8, 'pieces', 'Pieces', 'kzinc', 'unit_based', 0, 'per_unit', 'total', 0, 'production', 4500.00, 1, 2, NULL, 2, '2025-12-15 09:25:23', '2025-12-15 09:25:40', NULL),
(9, 'bundles', 'Bundles', 'kzinc', 'bundle_based', 0, 'per_unit', 'total', 0, 'production', 64000.00, 1, 3, '{\"pieces_per_bundle\": 15}', 2, '2025-12-15 09:25:23', '2025-12-15 09:25:40', NULL),
(10, 'bending', 'Bending Service', 'alusteel', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 100, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(11, 'loading', 'Loading Charge', 'alusteel', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 101, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(12, 'freight', 'Freight/Shipping', 'alusteel', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 102, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(13, 'accessories', 'Accessories (Nails, Washers, etc)', 'alusteel', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 103, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(14, 'installation', 'Installation Service', 'alusteel', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 104, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(15, 'refund', 'Refund/Credit', 'alusteel', 'unit_based', 1, 'fixed', 'total', 1, 'adjustment', 0.00, 1, 105, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(16, 'bending_alu', 'Bending Service', 'aluminum', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 100, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(17, 'loading_alu', 'Loading Charge', 'aluminum', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 101, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(18, 'freight_alu', 'Freight/Shipping', 'aluminum', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 102, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(19, 'accessories_alu', 'Accessories (Nails, Washers, etc)', 'aluminum', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 103, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(20, 'installation_alu', 'Installation Service', 'aluminum', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 104, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(21, 'refund_alu', 'Refund/Credit', 'aluminum', 'unit_based', 1, 'fixed', 'total', 1, 'adjustment', 0.00, 1, 105, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(22, 'bending_kzinc', 'Bending Service', 'kzinc', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 100, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(23, 'loading_kzinc', 'Loading Charge', 'kzinc', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 101, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(24, 'freight_kzinc', 'Freight/Shipping', 'kzinc', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 102, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(25, 'accessories_kzinc', 'Accessories (Nails, Washers, etc)', 'kzinc', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 103, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(26, 'installation_kzinc', 'Installation Service', 'kzinc', 'unit_based', 1, 'fixed', 'total', 0, 'addon', 0.00, 1, 104, NULL, 2, '2025-12-15 09:25:40', NULL, NULL),
(27, 'refund_kzinc', 'Refund/Credit', 'kzinc', 'unit_based', 1, 'fixed', 'total', 1, 'adjustment', 0.00, 1, 105, NULL, 2, '2025-12-15 09:25:40', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `amount_paid` decimal(15,2) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'cash',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`id`, `invoice_id`, `amount_paid`, `reference`, `payment_method`, `created_by`, `created_at`) VALUES
(1, 35, 9999999999.99, '', 'cash', 5, '2025-11-17 14:13:42'),
(2, 36, 800000.00, '', 'cash', 5, '2025-11-18 11:46:28'),
(3, 36, 60000.00, '', 'cash', 5, '2025-11-18 11:47:09'),
(4, 89, 69300.00, '', 'pos', 5, '2025-12-09 08:32:55'),
(5, 87, 1680250.00, '', 'bank_transfer', 5, '2025-12-09 08:33:59'),
(6, 86, 100800.00, '', 'bank_transfer', 5, '2025-12-09 08:34:47'),
(7, 85, 133200.00, '', 'bank_transfer', 5, '2025-12-09 08:36:06'),
(8, 81, 57600.00, '', 'bank_transfer', 5, '2025-12-09 08:37:09'),
(9, 80, 99000.00, '', 'bank_transfer', 5, '2025-12-09 08:37:40'),
(10, 92, 18800.00, '', 'bank_transfer', 5, '2025-12-09 10:19:44'),
(11, 91, 164500.00, '', 'pos', 5, '2025-12-09 10:20:10'),
(12, 90, 18400.00, '', 'pos', 5, '2025-12-09 10:20:32'),
(13, 118, 61200.00, '', 'bank_transfer', 5, '2025-12-09 14:38:27'),
(14, 117, 5778500.00, '', 'bank_transfer', 5, '2025-12-09 14:39:03'),
(15, 116, 5484500.00, '', 'bank_transfer', 5, '2025-12-09 14:40:22'),
(16, 114, 5890500.00, '', 'pos', 5, '2025-12-09 14:40:55'),
(17, 115, 5890500.00, '', 'bank_transfer', 5, '2025-12-09 14:41:32'),
(18, 114, 189000.00, '', 'cash', 5, '2025-12-09 14:42:00'),
(19, 113, 5894000.00, '', 'bank_transfer', 5, '2025-12-09 14:42:44'),
(20, 112, 5995500.00, '', 'bank_transfer', 5, '2025-12-09 14:43:08'),
(21, 111, 5621000.00, '', 'pos', 5, '2025-12-09 14:43:33'),
(22, 110, 6303500.00, '', 'bank_transfer', 5, '2025-12-09 14:44:01'),
(23, 109, 5726000.00, '', 'bank_transfer', 5, '2025-12-09 14:44:44'),
(24, 40, 1000000.00, '', 'bank_transfer', 5, '2025-12-10 11:18:16'),
(25, 68, 5.00, '', 'pos', 5, '2025-12-10 14:18:50'),
(26, 68, 5.00, '', 'pos', 5, '2025-12-10 14:19:50'),
(27, 67, 5.00, '', 'bank_transfer', 5, '2025-12-10 14:20:40'),
(28, 66, 5.00, '', 'bank_transfer', 5, '2025-12-10 14:21:43'),
(29, 65, 4.00, '', 'pos', 5, '2025-12-10 14:22:09'),
(30, 64, 5.00, '', 'bank_transfer', 5, '2025-12-10 14:23:06'),
(31, 63, 6.00, '', 'pos', 5, '2025-12-10 14:23:32'),
(32, 62, 5.00, '', 'pos', 5, '2025-12-10 14:23:55'),
(33, 61, 6.00, '', 'bank_transfer', 5, '2025-12-10 14:24:19'),
(34, 60, 6.00, '', 'pos', 5, '2025-12-10 14:24:45'),
(35, 59, 11.00, '', 'bank_transfer', 5, '2025-12-10 14:25:27'),
(36, 108, 6.00, '', 'bank_transfer', 5, '2025-12-10 14:26:20'),
(37, 108, 6110994.00, '', 'cash', 2, '2025-12-10 19:39:02'),
(38, 68, 5603490.00, '', 'cash', 2, '2025-12-10 19:41:10'),
(39, 67, 5239495.00, '', 'cash', 2, '2025-12-10 19:42:26'),
(40, 66, 5610495.00, '', 'cash', 2, '2025-12-10 19:42:59'),
(41, 107, 6.00, '', 'pos', 5, '2025-12-11 07:33:15'),
(42, 107, 6.00, '', 'cash', 5, '2025-12-11 07:35:04'),
(43, 84, 100.00, '', 'cash', 5, '2025-12-11 07:41:26'),
(44, 84, 100.00, '', 'pos', 5, '2025-12-11 07:42:07'),
(45, 107, 6261488.00, '', 'cash', 5, '2025-12-11 07:47:41'),
(46, 106, 11447100.00, '', 'cash', 5, '2025-12-11 07:48:39'),
(47, 105, 11421900.00, '', 'bank_transfer', 5, '2025-12-11 07:49:11'),
(48, 104, 11963700.00, '', 'pos', 5, '2025-12-11 07:49:43'),
(49, 103, 11163600.00, '', 'bank_transfer', 5, '2025-12-11 07:50:10'),
(50, 102, 5646160.00, '', 'bank_transfer', 5, '2025-12-11 07:50:36'),
(51, 101, 5208000.00, '', 'pos', 5, '2025-12-11 07:51:03'),
(52, 100, 11195100.00, '', 'cash', 5, '2025-12-11 07:51:26'),
(53, 99, 12033000.00, '', 'bank_transfer', 5, '2025-12-11 07:51:50'),
(54, 98, 5775000.00, '', 'bank_transfer', 5, '2025-12-11 07:52:28'),
(55, 97, 5582500.00, '', 'bank_transfer', 5, '2025-12-11 07:52:52'),
(56, 96, 5796000.00, '', 'bank_transfer', 5, '2025-12-11 07:53:20'),
(57, 95, 6013000.00, '', 'pos', 5, '2025-12-11 07:53:53'),
(58, 94, 5778500.00, '', 'bank_transfer', 5, '2025-12-11 07:55:11'),
(59, 93, 5915000.00, '', 'pos', 5, '2025-12-11 07:59:47'),
(60, 88, 69300.00, '', 'bank_transfer', 5, '2025-12-11 08:00:17'),
(61, 84, 100600.00, '', 'pos', 5, '2025-12-11 08:00:48'),
(62, 83, 69300.00, '', 'bank_transfer', 5, '2025-12-11 08:01:15'),
(63, 82, 69300.00, '', 'pos', 5, '2025-12-11 08:01:45'),
(64, 79, 70090.00, '', 'cash', 5, '2025-12-11 08:07:19'),
(65, 78, 175010.00, '', 'pos', 5, '2025-12-11 08:07:56'),
(66, 77, 29700.00, '', 'bank_transfer', 5, '2025-12-11 08:08:24'),
(67, 76, 5593000.00, '', 'bank_transfer', 5, '2025-12-11 08:08:59'),
(68, 75, 5603500.00, '', 'bank_transfer', 5, '2025-12-11 08:09:26'),
(69, 74, 4053000.00, '', 'pos', 5, '2025-12-11 08:09:52'),
(70, 73, 3818500.00, '', 'pos', 5, '2025-12-11 08:10:16'),
(71, 72, 5887000.00, '', 'bank_transfer', 5, '2025-12-11 08:10:41'),
(72, 71, 5862500.00, '', 'pos', 5, '2025-12-11 08:11:09'),
(73, 70, 5603500.00, '', 'cash', 5, '2025-12-11 08:11:28'),
(74, 69, 5603500.00, '', 'pos', 5, '2025-12-11 08:11:57'),
(75, 65, 4763496.00, '', 'bank_transfer', 5, '2025-12-11 08:12:26'),
(76, 64, 5890495.00, '', 'pos', 5, '2025-12-11 08:12:56'),
(77, 63, 6331494.00, '', 'bank_transfer', 5, '2025-12-11 08:13:28'),
(78, 62, 5998995.00, '', 'bank_transfer', 5, '2025-12-11 08:13:56'),
(79, 61, 6058494.00, '', 'pos', 5, '2025-12-11 08:14:16'),
(80, 60, 6112514.00, '', 'pos', 5, '2025-12-11 08:14:38'),
(81, 59, 11150989.00, '', 'pos', 5, '2025-12-11 08:15:02'),
(82, 58, 11277600.00, '', 'bank_transfer', 5, '2025-12-11 08:15:38'),
(83, 57, 5498500.00, '', 'bank_transfer', 5, '2025-12-11 08:16:06'),
(84, 56, 11781000.00, 'bank', 'bank_transfer', 5, '2025-12-11 08:16:59'),
(85, 55, 5246500.00, '', 'pos', 5, '2025-12-11 08:17:36'),
(86, 54, 5673500.00, '', 'bank_transfer', 5, '2025-12-11 08:18:11'),
(87, 53, 7195500.00, '', 'pos', 5, '2025-12-11 08:18:35'),
(88, 52, 5607000.00, '', 'pos', 5, '2025-12-11 08:19:01'),
(89, 51, 5614000.00, '', 'pos', 5, '2025-12-11 08:19:24'),
(90, 50, 5827720.00, '', 'pos', 5, '2025-12-11 08:19:48'),
(91, 49, 5842800.00, '', 'bank_transfer', 5, '2025-12-11 08:20:14'),
(92, 48, 5691600.00, '', 'bank_transfer', 5, '2025-12-11 08:20:39'),
(93, 47, 5745600.00, '', 'pos', 5, '2025-12-11 08:21:13'),
(94, 46, 12629400.00, '', 'bank_transfer', 5, '2025-12-11 08:21:43'),
(95, 45, 12514500.00, '', 'bank_transfer', 5, '2025-12-11 08:22:05'),
(96, 44, 13398200.00, '', 'pos', 5, '2025-12-11 08:22:26'),
(97, 43, 5880000.00, '', 'bank_transfer', 5, '2025-12-11 08:22:49'),
(98, 42, 5680500.00, '', 'pos', 5, '2025-12-11 08:23:08'),
(99, 41, 5733000.00, '', 'pos', 5, '2025-12-11 08:23:29'),
(100, 39, 400000.00, '', 'pos', 5, '2025-12-11 08:23:51'),
(101, 38, 4413950.00, '', 'pos', 5, '2025-12-11 08:24:13'),
(102, 37, 69660000.00, '', 'pos', 5, '2025-12-11 08:24:47'),
(103, 119, 1678050.00, '', 'bank_transfer', 5, '2025-12-11 14:54:09');

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
  `weight_kg` decimal(10,2) DEFAULT NULL COMMENT 'Quantity in KG',
  `price_per_meter` decimal(15,2) NOT NULL,
  `price_per_kg` decimal(10,2) DEFAULT NULL COMMENT 'Price per KG',
  `total_amount` decimal(15,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_by` int(11) NOT NULL,
  `notes` text DEFAULT NULL COMMENT 'Sale notes/remarks',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `customer_id`, `coil_id`, `stock_entry_id`, `sale_type`, `meters`, `weight_kg`, `price_per_meter`, `price_per_kg`, `total_amount`, `status`, `created_by`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(11, 1, 49, 11, 'retail', 40.00, NULL, 3000.00, NULL, 120000.00, 'completed', 2, NULL, '2025-11-08 11:40:38', NULL, NULL),
(12, 1, 49, 11, 'retail', 288.70, NULL, 10300.00, NULL, 2973610.00, 'completed', 2, NULL, '2025-11-08 11:50:31', NULL, NULL),
(14, 1, 50, 12, 'retail', 400.00, NULL, 2000.00, NULL, 800000.00, 'completed', 5, NULL, '2025-11-08 12:38:10', NULL, NULL),
(15, 1, 50, 12, 'retail', 300.00, NULL, 2000.00, NULL, 600000.00, 'completed', 5, NULL, '2025-11-08 12:40:35', NULL, NULL),
(16, 1, 50, 12, 'retail', 400.00, NULL, 2000.00, NULL, 800000.00, 'completed', 5, NULL, '2025-11-08 12:41:44', NULL, NULL),
(17, 1, 51, 13, 'retail', 200.00, NULL, 2000.00, NULL, 400000.00, 'completed', 5, NULL, '2025-11-08 12:49:07', NULL, NULL),
(18, 1, 51, 13, 'retail', 400.00, NULL, 2000.00, NULL, 800000.00, 'completed', 5, NULL, '2025-11-08 12:50:39', NULL, NULL),
(19, 3, 52, 14, 'wholesale', 2500.00, NULL, 2000.00, NULL, 5000000.00, 'completed', 5, NULL, '2025-11-08 13:11:50', NULL, NULL),
(49, 4, 67, 32, 'available_stock', 2073000.00, NULL, 5800.00, NULL, 9999999999.99, 'completed', 5, NULL, '2025-11-17 12:51:31', NULL, NULL),
(50, 4, 299, 33, 'available_stock', 2000.00, NULL, 400.00, NULL, 800000.00, 'completed', 5, NULL, '2025-11-18 11:43:49', NULL, NULL),
(51, 5, 286, 38, 'available_stock', 6000.00, NULL, 10800.00, NULL, 64800000.00, 'completed', 5, NULL, '2025-11-19 13:23:23', NULL, NULL),
(52, 7, 70, 42, 'available_stock', 1990.00, 2053.00, 0.00, 2000.00, 4106000.00, 'completed', 2, '', '2025-12-03 10:15:05', NULL, NULL),
(53, 7, 70, 42, 'available_stock', 1990.00, 2053.00, 0.00, 2000.00, 4106000.00, 'completed', 2, '', '2025-12-03 10:15:05', NULL, NULL),
(54, 6, 285, 44, 'available_stock', 1576.00, 3276.00, 0.00, 1750.00, 5733000.00, 'completed', 5, '', '2025-12-03 11:04:08', NULL, NULL),
(55, 6, 285, 44, 'available_stock', 1576.00, 3276.00, 0.00, 1750.00, 5733000.00, 'completed', 5, '', '2025-12-03 11:04:08', NULL, NULL),
(56, 6, 284, 45, 'available_stock', 1560.00, 3246.00, 0.00, 1750.00, 5680500.00, 'completed', 5, '', '2025-12-03 11:07:19', NULL, NULL),
(57, 6, 284, 45, 'available_stock', 1560.00, 3246.00, 0.00, 1750.00, 5680500.00, 'completed', 5, '', '2025-12-03 11:07:19', NULL, NULL),
(58, 6, 286, 46, 'available_stock', 1630.00, 3360.00, 0.00, 1750.00, 5880000.00, 'completed', 5, '', '2025-12-03 11:09:48', NULL, NULL),
(59, 6, 286, 46, 'available_stock', 1630.00, 3360.00, 0.00, 1750.00, 5880000.00, 'completed', 5, '', '2025-12-03 11:09:48', NULL, NULL),
(60, 8, 69, 47, 'available_stock', 2243.00, 2161.00, 0.00, 6200.00, 13398200.00, 'completed', 5, '', '2025-12-03 11:14:57', NULL, NULL),
(61, 8, 69, 47, 'available_stock', 2243.00, 2161.00, 0.00, 6200.00, 13398200.00, 'completed', 5, '', '2025-12-03 11:14:57', NULL, NULL),
(62, 8, 85, 48, 'available_stock', 2166.00, 2025.00, 0.00, 6180.00, 12514500.00, 'completed', 5, '', '2025-12-03 11:17:49', NULL, NULL),
(63, 8, 85, 48, 'available_stock', 2166.00, 2025.00, 0.00, 6180.00, 12514500.00, 'completed', 5, '', '2025-12-03 11:17:49', NULL, NULL),
(64, 9, 87, 49, 'available_stock', 2180.00, 2037.00, 0.00, 6200.00, 12629400.00, 'completed', 5, '', '2025-12-03 11:22:00', NULL, NULL),
(65, 9, 87, 49, 'available_stock', 2180.00, 2037.00, 0.00, 6200.00, 12629400.00, 'completed', 5, '', '2025-12-03 11:22:00', NULL, NULL),
(66, 9, 232, 50, 'available_stock', 1448.00, 3192.00, 0.00, 1800.00, 5745600.00, 'completed', 5, '', '2025-12-03 11:24:27', NULL, NULL),
(67, 9, 232, 50, 'available_stock', 1448.00, 3192.00, 0.00, 1800.00, 5745600.00, 'completed', 5, '', '2025-12-03 11:24:27', NULL, NULL),
(68, 9, 281, 51, 'available_stock', 1535.00, 3162.00, 0.00, 1800.00, 5691600.00, 'completed', 5, '', '2025-12-03 11:26:37', NULL, NULL),
(69, 9, 281, 51, 'available_stock', 1535.00, 3162.00, 0.00, 1800.00, 5691600.00, 'completed', 5, '', '2025-12-03 11:26:37', NULL, NULL),
(70, 9, 300, 52, 'available_stock', 1575.00, 3246.00, 0.00, 1800.00, 5842800.00, 'completed', 5, '', '2025-12-03 11:28:50', NULL, NULL),
(71, 9, 300, 52, 'available_stock', 1575.00, 3246.00, 0.00, 1800.00, 5842800.00, 'completed', 5, '', '2025-12-03 11:28:50', NULL, NULL),
(72, 10, 243, 53, 'available_stock', 1451.00, 3274.00, 0.00, 1780.00, 5827720.00, 'completed', 5, '', '2025-12-03 11:31:50', NULL, NULL),
(73, 10, 243, 53, 'available_stock', 1451.00, 3274.00, 0.00, 1780.00, 5827720.00, 'completed', 5, '', '2025-12-03 11:31:50', NULL, NULL),
(74, 10, 315, 54, 'available_stock', 1313.00, 3208.00, 0.00, 1750.00, 5614000.00, 'completed', 5, '', '2025-12-03 11:34:17', NULL, NULL),
(75, 10, 315, 54, 'available_stock', 1313.00, 3208.00, 0.00, 1750.00, 5614000.00, 'completed', 5, '', '2025-12-03 11:34:17', NULL, NULL),
(76, 10, 311, 55, 'available_stock', 1310.00, 3204.00, 0.00, 1750.00, 5607000.00, 'completed', 5, '', '2025-12-03 11:36:26', NULL, NULL),
(77, 10, 311, 55, 'available_stock', 1310.00, 3204.00, 0.00, 1750.00, 5607000.00, 'completed', 5, '', '2025-12-03 11:36:26', NULL, NULL),
(78, 11, 96, 56, 'available_stock', 1570.00, 3510.00, 0.00, 2050.00, 7195500.00, 'completed', 5, '', '2025-12-03 11:40:22', NULL, NULL),
(79, 11, 96, 56, 'available_stock', 1570.00, 3510.00, 0.00, 2050.00, 7195500.00, 'completed', 5, '', '2025-12-03 11:40:22', NULL, NULL),
(80, 11, 322, 57, 'available_stock', 1324.00, 3242.00, 0.00, 1750.00, 5673500.00, 'completed', 5, '', '2025-12-03 11:43:40', NULL, NULL),
(81, 11, 322, 57, 'available_stock', 1324.00, 3242.00, 0.00, 1750.00, 5673500.00, 'completed', 5, '', '2025-12-03 11:43:40', NULL, NULL),
(82, 11, 323, 58, 'available_stock', 1220.00, 2998.00, 0.00, 1750.00, 5246500.00, 'completed', 5, '', '2025-12-03 11:45:38', NULL, NULL),
(83, 11, 323, 58, 'available_stock', 1220.00, 2998.00, 0.00, 1750.00, 5246500.00, 'completed', 5, '', '2025-12-03 11:45:38', NULL, NULL),
(84, 12, 588, 59, 'available_stock', 1195.00, 1870.00, 0.00, 6300.00, 11781000.00, 'completed', 5, '', '2025-12-03 11:53:00', NULL, NULL),
(85, 12, 588, 59, 'available_stock', 1195.00, 1870.00, 0.00, 6300.00, 11781000.00, 'completed', 5, '', '2025-12-03 11:53:00', NULL, NULL),
(86, 12, 282, 60, 'available_stock', 1532.00, 3142.00, 0.00, 1750.00, 5498500.00, 'completed', 5, '', '2025-12-03 11:54:55', NULL, NULL),
(87, 12, 282, 60, 'available_stock', 1532.00, 3142.00, 0.00, 1750.00, 5498500.00, 'completed', 5, '', '2025-12-03 11:54:55', NULL, NULL),
(88, 13, 604, 61, 'available_stock', 1220.00, 1776.00, 0.00, 6350.00, 11277600.00, 'completed', 5, '', '2025-12-03 11:57:37', NULL, NULL),
(89, 13, 604, 61, 'available_stock', 1220.00, 1776.00, 0.00, 6350.00, 11277600.00, 'completed', 5, '', '2025-12-03 11:57:37', NULL, NULL),
(90, 13, 597, 62, 'available_stock', 1211.00, 1770.00, 0.00, 6300.00, 11151000.00, 'completed', 5, '', '2025-12-03 11:59:17', NULL, NULL),
(91, 13, 597, 62, 'available_stock', 1211.00, 1770.00, 0.00, 6300.00, 11151000.00, 'completed', 5, '', '2025-12-03 11:59:17', NULL, NULL),
(92, 14, 216, 63, 'available_stock', 1289.00, 3434.00, 0.00, 1780.00, 6112520.00, 'completed', 5, '', '2025-12-03 12:01:54', NULL, NULL),
(93, 14, 216, 63, 'available_stock', 1289.00, 3434.00, 0.00, 1780.00, 6112520.00, 'completed', 5, '', '2025-12-03 12:01:54', NULL, NULL),
(94, 14, 215, 64, 'available_stock', 1312.00, 3462.00, 0.00, 1750.00, 6058500.00, 'completed', 5, '', '2025-12-03 12:05:19', NULL, NULL),
(95, 14, 215, 64, 'available_stock', 1312.00, 3462.00, 0.00, 1750.00, 6058500.00, 'completed', 5, '', '2025-12-03 12:05:19', NULL, NULL),
(96, 15, 317, 65, 'available_stock', 1380.00, 3428.00, 0.00, 1750.00, 5999000.00, 'completed', 5, '', '2025-12-03 12:08:32', NULL, NULL),
(97, 15, 317, 65, 'available_stock', 1380.00, 3428.00, 0.00, 1750.00, 5999000.00, 'completed', 5, '', '2025-12-03 12:08:32', NULL, NULL),
(98, 15, 324, 66, 'available_stock', 1475.00, 3618.00, 0.00, 1750.00, 6331500.00, 'completed', 5, '', '2025-12-03 12:10:07', NULL, NULL),
(99, 15, 324, 66, 'available_stock', 1475.00, 3618.00, 0.00, 1750.00, 6331500.00, 'completed', 5, '', '2025-12-03 12:10:07', NULL, NULL),
(100, 15, 323, 67, 'available_stock', 1220.00, 3366.00, 0.00, 1750.00, 5890500.00, 'completed', 5, '', '2025-12-03 12:12:07', NULL, NULL),
(101, 15, 323, 67, 'available_stock', 1220.00, 3366.00, 0.00, 1750.00, 5890500.00, 'completed', 5, '', '2025-12-03 12:12:07', NULL, NULL),
(102, 15, 288, 68, 'available_stock', 1321.00, 2722.00, 0.00, 1750.00, 4763500.00, 'completed', 5, '', '2025-12-03 12:13:50', NULL, NULL),
(103, 15, 288, 68, 'available_stock', 1321.00, 2722.00, 0.00, 1750.00, 4763500.00, 'completed', 5, '', '2025-12-03 12:13:50', NULL, NULL),
(104, 15, 320, 69, 'available_stock', 1317.00, 3206.00, 0.00, 1750.00, 5610500.00, 'completed', 5, '', '2025-12-03 12:15:40', NULL, NULL),
(105, 15, 320, 69, 'available_stock', 1317.00, 3206.00, 0.00, 1750.00, 5610500.00, 'completed', 5, '', '2025-12-03 12:15:40', NULL, NULL),
(106, 15, 318, 70, 'available_stock', 1229.00, 2994.00, 0.00, 1750.00, 5239500.00, 'completed', 5, '', '2025-12-03 12:18:20', NULL, NULL),
(107, 15, 318, 70, 'available_stock', 1229.00, 2994.00, 0.00, 1750.00, 5239500.00, 'completed', 5, '', '2025-12-03 12:18:20', NULL, NULL),
(108, 16, 307, 71, 'available_stock', 1229.00, 3202.00, 0.00, 1750.00, 5603500.00, 'completed', 5, '', '2025-12-03 12:21:31', NULL, NULL),
(109, 16, 307, 71, 'available_stock', 1229.00, 3202.00, 0.00, 1750.00, 5603500.00, 'completed', 5, '', '2025-12-03 12:21:31', NULL, NULL),
(110, 16, 308, 72, 'available_stock', 1310.00, 3202.00, 0.00, 1750.00, 5603500.00, 'completed', 5, '', '2025-12-03 12:23:20', NULL, NULL),
(111, 16, 308, 72, 'available_stock', 1310.00, 3202.00, 0.00, 1750.00, 5603500.00, 'completed', 5, '', '2025-12-03 12:23:20', NULL, NULL),
(112, 16, 310, 73, 'available_stock', 1312.00, 3202.00, 0.00, 1750.00, 5603500.00, 'completed', 5, '', '2025-12-03 12:25:17', NULL, NULL),
(113, 16, 310, 73, 'available_stock', 1312.00, 3202.00, 0.00, 1750.00, 5603500.00, 'completed', 5, '', '2025-12-03 12:25:17', NULL, NULL),
(114, 16, 309, 74, 'available_stock', 1374.00, 3350.00, 0.00, 1750.00, 5862500.00, 'completed', 5, '', '2025-12-03 12:27:06', NULL, NULL),
(115, 16, 309, 74, 'available_stock', 1374.00, 3350.00, 0.00, 1750.00, 5862500.00, 'completed', 5, '', '2025-12-03 12:27:06', NULL, NULL),
(116, 16, 312, 75, 'available_stock', 1379.00, 3364.00, 0.00, 1750.00, 5887000.00, 'completed', 5, '', '2025-12-03 12:28:57', NULL, NULL),
(117, 16, 312, 75, 'available_stock', 1379.00, 3364.00, 0.00, 1750.00, 5887000.00, 'completed', 5, '', '2025-12-03 12:28:57', NULL, NULL),
(118, 16, 313, 76, 'available_stock', 889.00, 2182.00, 0.00, 1750.00, 3818500.00, 'completed', 5, '', '2025-12-03 12:30:42', NULL, NULL),
(119, 16, 313, 76, 'available_stock', 889.00, 2182.00, 0.00, 1750.00, 3818500.00, 'completed', 5, '', '2025-12-03 12:30:42', NULL, NULL),
(120, 16, 314, 77, 'available_stock', 932.00, 2316.00, 0.00, 1750.00, 4053000.00, 'completed', 5, '', '2025-12-03 12:32:48', NULL, NULL),
(121, 16, 314, 77, 'available_stock', 932.00, 2316.00, 0.00, 1750.00, 4053000.00, 'completed', 5, '', '2025-12-03 12:32:48', NULL, NULL),
(122, 16, 316, 78, 'available_stock', 1312.00, 3202.00, 0.00, 1750.00, 5603500.00, 'completed', 5, '', '2025-12-03 12:35:08', NULL, NULL),
(123, 16, 316, 78, 'available_stock', 1312.00, 3202.00, 0.00, 1750.00, 5603500.00, 'completed', 5, '', '2025-12-03 12:35:08', NULL, NULL),
(124, 16, 319, 79, 'available_stock', 1319.00, 3196.00, 0.00, 1750.00, 5593000.00, 'completed', 5, '', '2025-12-03 12:37:23', NULL, NULL),
(125, 16, 319, 79, 'available_stock', 1319.00, 3196.00, 0.00, 1750.00, 5593000.00, 'completed', 5, '', '2025-12-03 12:37:23', NULL, NULL),
(126, 17, 659, 106, 'retail', 9.00, NULL, 3300.00, NULL, 29700.00, 'completed', 5, NULL, '2025-12-08 11:24:20', NULL, NULL),
(127, 18, 649, 108, 'retail', 40.70, NULL, 4300.00, NULL, 175010.00, 'completed', 5, NULL, '2025-12-08 11:42:44', NULL, NULL),
(128, 19, 211, 102, 'retail', 16.30, NULL, 4300.00, NULL, 70090.00, 'completed', 5, NULL, '2025-12-08 11:54:11', NULL, NULL),
(129, 20, 665, 109, 'retail', 30.00, NULL, 3300.00, NULL, 99000.00, 'completed', 5, NULL, '2025-12-08 12:07:13', NULL, NULL),
(130, 21, 659, 106, 'retail', 18.00, NULL, 3200.00, NULL, 57600.00, 'completed', 5, NULL, '2025-12-08 12:11:26', NULL, NULL),
(131, 22, 656, 103, 'retail', 21.00, NULL, 3300.00, NULL, 69300.00, 'completed', 5, NULL, '2025-12-08 12:18:20', NULL, NULL),
(132, 22, 656, 103, 'retail', 21.00, NULL, 3300.00, NULL, 69300.00, 'completed', 5, NULL, '2025-12-08 12:30:36', NULL, NULL),
(133, 23, 659, 106, 'retail', 31.50, NULL, 3200.00, NULL, 100800.00, 'completed', 5, NULL, '2025-12-08 12:43:50', NULL, NULL),
(134, 24, 659, 106, 'retail', 36.00, NULL, 3700.00, NULL, 133200.00, 'completed', 5, NULL, '2025-12-08 12:51:09', NULL, NULL),
(135, 25, 658, 105, 'retail', 24.00, NULL, 4200.00, NULL, 100800.00, 'completed', 5, NULL, '2025-12-08 12:54:04', NULL, NULL),
(136, 27, 660, 107, 'retail', 305.50, NULL, 5500.00, NULL, 1680250.00, 'completed', 5, NULL, '2025-12-08 13:09:59', NULL, NULL),
(137, 22, 656, 103, 'retail', 21.00, NULL, 3300.00, NULL, 69300.00, 'completed', 5, NULL, '2025-12-08 13:20:19', NULL, NULL),
(138, 22, 656, 113, 'retail', 21.00, NULL, 3300.00, NULL, 69300.00, 'completed', 5, NULL, '2025-12-09 08:08:55', NULL, NULL),
(139, 28, 635, 81, 'retail', 4.00, NULL, 4600.00, NULL, 18400.00, 'completed', 5, NULL, '2025-12-09 10:12:10', NULL, NULL),
(140, 29, 636, 112, 'retail', 35.00, NULL, 4700.00, NULL, 164500.00, 'completed', 5, NULL, '2025-12-09 10:13:36', NULL, NULL),
(141, 30, 640, 85, 'retail', 4.00, NULL, 4700.00, NULL, 18800.00, 'completed', 5, NULL, '2025-12-09 10:18:22', NULL, NULL),
(142, 31, 275, 114, 'available_stock', 1649.00, 3380.00, 0.00, 1750.00, 5915000.00, 'completed', 5, '', '2025-12-09 10:58:30', NULL, NULL),
(143, 31, 275, 114, 'available_stock', 1649.00, 3380.00, 0.00, 1750.00, 5915000.00, 'completed', 5, '', '2025-12-09 10:58:30', NULL, NULL),
(144, 31, 294, 115, 'available_stock', 1612.00, 3302.00, 0.00, 1750.00, 5778500.00, 'completed', 5, '', '2025-12-09 11:04:37', NULL, NULL),
(145, 31, 294, 115, 'available_stock', 1612.00, 3302.00, 0.00, 1750.00, 5778500.00, 'completed', 5, '', '2025-12-09 11:04:37', NULL, NULL),
(146, 31, 295, 116, 'available_stock', 1665.00, 3436.00, 0.00, 1750.00, 6013000.00, 'completed', 5, '', '2025-12-09 11:06:42', NULL, NULL),
(147, 31, 295, 116, 'available_stock', 1665.00, 3436.00, 0.00, 1750.00, 6013000.00, 'completed', 5, '', '2025-12-09 11:06:42', NULL, NULL),
(148, 31, 304, 117, 'available_stock', 1360.00, 3312.00, 0.00, 1750.00, 5796000.00, 'completed', 5, '', '2025-12-09 11:10:36', NULL, NULL),
(149, 31, 304, 117, 'available_stock', 1360.00, 3312.00, 0.00, 1750.00, 5796000.00, 'completed', 5, '', '2025-12-09 11:10:36', NULL, NULL),
(150, 31, 303, 118, 'available_stock', 1308.00, 3190.00, 0.00, 1750.00, 5582500.00, 'completed', 5, '', '2025-12-09 11:13:22', NULL, NULL),
(151, 31, 303, 118, 'available_stock', 1308.00, 3190.00, 0.00, 1750.00, 5582500.00, 'completed', 5, '', '2025-12-09 11:13:22', NULL, NULL),
(152, 31, 671, 119, 'available_stock', 1472.00, 3300.00, 0.00, 1750.00, 5775000.00, 'completed', 5, '', '2025-12-09 11:21:22', NULL, NULL),
(153, 31, 671, 119, 'available_stock', 1472.00, 3300.00, 0.00, 1750.00, 5775000.00, 'completed', 5, '', '2025-12-09 11:21:22', NULL, NULL),
(154, 32, 594, 120, 'available_stock', 1232.00, 1910.00, 0.00, 6300.00, 12033000.00, 'completed', 5, '', '2025-12-09 11:24:43', NULL, NULL),
(155, 32, 594, 120, 'available_stock', 1232.00, 1910.00, 0.00, 6300.00, 12033000.00, 'completed', 5, '', '2025-12-09 11:24:43', NULL, NULL),
(156, 32, 584, 121, 'available_stock', 1215.00, 1777.00, 0.00, 6300.00, 11195100.00, 'completed', 5, '', '2025-12-09 11:27:13', NULL, NULL),
(157, 32, 584, 121, 'available_stock', 1215.00, 1777.00, 0.00, 6300.00, 11195100.00, 'completed', 5, '', '2025-12-09 11:27:13', NULL, NULL),
(158, 33, 292, 122, 'available_stock', 1445.00, 2976.00, 0.00, 1750.00, 5208000.00, 'completed', 5, '', '2025-12-09 11:32:26', NULL, NULL),
(159, 33, 292, 122, 'available_stock', 1445.00, 2976.00, 0.00, 1750.00, 5208000.00, 'completed', 5, '', '2025-12-09 11:32:26', NULL, NULL),
(160, 33, 279, 123, 'available_stock', 1535.00, 3172.00, 0.00, 1780.00, 5646160.00, 'completed', 5, '', '2025-12-09 11:34:29', NULL, NULL),
(161, 33, 279, 123, 'available_stock', 1535.00, 3172.00, 0.00, 1780.00, 5646160.00, 'completed', 5, '', '2025-12-09 11:34:29', NULL, NULL),
(162, 34, 583, 124, 'available_stock', 1211.00, 1772.00, 0.00, 6300.00, 11163600.00, 'completed', 5, '', '2025-12-09 11:37:57', NULL, NULL),
(163, 34, 583, 124, 'available_stock', 1211.00, 1772.00, 0.00, 6300.00, 11163600.00, 'completed', 5, '', '2025-12-09 11:37:57', NULL, NULL),
(164, 34, 581, 125, 'available_stock', 1225.00, 1899.00, 0.00, 6300.00, 11963700.00, 'completed', 5, '', '2025-12-09 11:39:40', NULL, NULL),
(165, 34, 581, 125, 'available_stock', 1225.00, 1899.00, 0.00, 6300.00, 11963700.00, 'completed', 5, '', '2025-12-09 11:39:40', NULL, NULL),
(166, 35, 602, 126, 'available_stock', 1158.00, 1813.00, 0.00, 6300.00, 11421900.00, 'completed', 5, '', '2025-12-09 11:43:57', NULL, NULL),
(167, 35, 602, 126, 'available_stock', 1158.00, 1813.00, 0.00, 6300.00, 11421900.00, 'completed', 5, '', '2025-12-09 11:43:57', NULL, NULL),
(168, 35, 590, 127, 'available_stock', 1160.00, 1817.00, 0.00, 6300.00, 11447100.00, 'completed', 5, '', '2025-12-09 11:45:38', NULL, NULL),
(169, 35, 590, 127, 'available_stock', 1160.00, 1817.00, 0.00, 6300.00, 11447100.00, 'completed', 5, '', '2025-12-09 11:45:38', NULL, NULL),
(170, 31, 212, 128, 'available_stock', 1579.00, 3578.00, 0.00, 1750.00, 6261500.00, 'completed', 5, '', '2025-12-09 11:49:10', NULL, NULL),
(171, 31, 212, 128, 'available_stock', 1579.00, 3578.00, 0.00, 1750.00, 6261500.00, 'completed', 5, '', '2025-12-09 11:49:10', NULL, NULL),
(172, 31, 213, 129, 'available_stock', 1550.00, 3492.00, 0.00, 1750.00, 6111000.00, 'completed', 5, '', '2025-12-09 11:51:16', NULL, NULL),
(173, 31, 213, 129, 'available_stock', 1550.00, 3492.00, 0.00, 1750.00, 6111000.00, 'completed', 5, '', '2025-12-09 11:51:16', NULL, NULL),
(174, 35, 214, 130, 'available_stock', 1450.00, 3272.00, 0.00, 1750.00, 5726000.00, 'completed', 5, '', '2025-12-09 11:53:35', NULL, NULL),
(175, 35, 214, 130, 'available_stock', 1450.00, 3272.00, 0.00, 1750.00, 5726000.00, 'completed', 5, '', '2025-12-09 11:53:35', NULL, NULL),
(176, 31, 104, 131, 'available_stock', 2444.00, 3602.00, 0.00, 1750.00, 6303500.00, 'completed', 5, '', '2025-12-09 11:57:14', NULL, NULL),
(177, 31, 104, 131, 'available_stock', 2444.00, 3602.00, 0.00, 1750.00, 6303500.00, 'completed', 5, '', '2025-12-09 11:57:14', NULL, NULL),
(178, 31, 105, 132, 'available_stock', 2199.00, 3212.00, 0.00, 1750.00, 5621000.00, 'completed', 5, '', '2025-12-09 11:59:40', NULL, NULL),
(179, 31, 105, 132, 'available_stock', 2199.00, 3212.00, 0.00, 1750.00, 5621000.00, 'completed', 5, '', '2025-12-09 11:59:40', NULL, NULL),
(180, 31, 112, 133, 'available_stock', 2264.00, 3426.00, 0.00, 1750.00, 5995500.00, 'completed', 5, '', '2025-12-09 12:01:55', NULL, NULL),
(181, 31, 112, 133, 'available_stock', 2264.00, 3426.00, 0.00, 1750.00, 5995500.00, 'completed', 5, '', '2025-12-09 12:01:55', NULL, NULL),
(182, 31, 113, 134, 'available_stock', 2242.00, 3368.00, 0.00, 1750.00, 5894000.00, 'completed', 5, '', '2025-12-09 12:04:16', NULL, NULL),
(183, 31, 113, 134, 'available_stock', 2242.00, 3368.00, 0.00, 1750.00, 5894000.00, 'completed', 5, '', '2025-12-09 12:04:16', NULL, NULL),
(184, 31, 568, 135, 'available_stock', 2322.00, 3474.00, 0.00, 1750.00, 6079500.00, 'completed', 5, '', '2025-12-09 12:08:48', NULL, NULL),
(185, 31, 568, 135, 'available_stock', 2322.00, 3474.00, 0.00, 1750.00, 6079500.00, 'completed', 5, '', '2025-12-09 12:08:48', NULL, NULL),
(186, 31, 122, 136, 'available_stock', 2223.00, 3366.00, 0.00, 1750.00, 5890500.00, 'completed', 5, '', '2025-12-09 12:11:21', NULL, NULL),
(187, 31, 122, 136, 'available_stock', 2223.00, 3366.00, 0.00, 1750.00, 5890500.00, 'completed', 5, '', '2025-12-09 12:11:21', NULL, NULL),
(188, 31, 299, 137, 'available_stock', 1514.00, 3134.00, 0.00, 1750.00, 5484500.00, 'completed', 5, '', '2025-12-09 12:21:12', NULL, NULL),
(189, 31, 299, 137, 'available_stock', 1514.00, 3134.00, 0.00, 1750.00, 5484500.00, 'completed', 5, '', '2025-12-09 12:21:12', NULL, NULL),
(190, 31, 672, 138, 'available_stock', 1474.00, 3302.00, 0.00, 1750.00, 5778500.00, 'completed', 5, '', '2025-12-09 12:27:20', NULL, NULL),
(191, 31, 672, 138, 'available_stock', 1474.00, 3302.00, 0.00, 1750.00, 5778500.00, 'completed', 5, '', '2025-12-09 12:27:20', NULL, NULL),
(192, 26, 176, 139, 'retail', 18.00, NULL, 3400.00, NULL, 61200.00, 'completed', 5, NULL, '2025-12-09 12:37:23', NULL, NULL),
(193, 36, 660, 107, 'retail', 305.10, NULL, 5500.00, NULL, 1678050.00, 'completed', 5, NULL, '2025-12-11 14:50:18', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_entries`
--

CREATE TABLE `stock_entries` (
  `id` int(11) NOT NULL,
  `coil_id` int(11) NOT NULL,
  `meters` decimal(10,2) NOT NULL,
  `meters_remaining` decimal(10,2) NOT NULL,
  `weight_kg` decimal(10,2) DEFAULT NULL,
  `weight_kg_remaining` decimal(10,2) DEFAULT NULL,
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

INSERT INTO `stock_entries` (`id`, `coil_id`, `meters`, `meters_remaining`, `weight_kg`, `weight_kg_remaining`, `meters_used`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(11, 49, 2600.00, 2271.30, 20000.00, NULL, 0.00, 'factory_use', 2, '2025-11-08 11:38:09', '2025-12-02 16:24:49', '2025-11-19 13:40:31'),
(12, 50, 2650.00, 1550.00, 20000.00, NULL, 0.00, 'factory_use', 5, '2025-11-08 12:25:44', '2025-12-02 16:25:56', '2025-11-19 13:40:35'),
(13, 51, 2500.00, 1900.00, 20000.00, NULL, 0.00, 'factory_use', 5, '2025-11-08 12:45:41', '2025-12-02 16:25:56', '2025-11-17 14:21:31'),
(14, 52, 2500.00, 0.00, 20000.00, NULL, 0.00, 'available', 5, '2025-11-08 12:54:10', '2025-12-02 16:25:56', '2025-11-19 13:40:42'),
(31, 67, 5800.00, 2069273.00, 20000.00, NULL, 0.00, 'factory_use', 5, '2025-11-17 12:27:30', '2025-12-02 16:25:56', '2025-11-17 12:43:18'),
(32, 67, 2073000.00, 0.00, 20000.00, NULL, 2073000.00, 'sold', 5, '2025-11-17 12:43:55', '2025-12-02 16:25:56', '2025-11-19 13:40:46'),
(33, 299, 2000.00, 0.00, 20000.00, NULL, 2000.00, 'sold', 5, '2025-11-18 11:27:31', '2025-12-02 16:25:56', '2025-11-19 13:40:51'),
(34, 316, 40000.00, 40000.00, 20000.00, NULL, 0.00, 'factory_use', 5, '2025-11-18 13:31:46', '2025-12-03 09:00:21', '2025-12-03 09:00:21'),
(35, 569, 5000.00, 5000.00, 20000.00, NULL, 0.00, 'factory_use', 5, '2025-11-19 08:31:41', '2025-12-02 16:25:56', '2025-11-19 08:40:19'),
(36, 311, 5000.00, 5000.00, 20000.00, NULL, 0.00, 'factory_use', 5, '2025-11-19 08:41:07', '2025-12-03 09:00:16', '2025-12-03 09:00:16'),
(37, 287, 5000.00, 5000.00, 20000.00, NULL, 0.00, 'available', 5, '2025-11-19 08:55:10', '2025-12-03 09:00:10', '2025-12-03 09:00:10'),
(38, 286, 6000.00, 0.00, 20000.00, NULL, 6000.00, 'sold', 5, '2025-11-19 09:19:51', '2025-12-03 09:00:04', '2025-12-03 09:00:04'),
(39, 632, 203000.00, 203000.00, 20000.00, NULL, 0.00, 'available', 2, '2025-12-02 09:19:14', '2025-12-02 16:25:56', '2025-12-02 09:19:21'),
(40, 632, 20000.00, 20000.00, 45000.00, 45000.00, 0.00, 'available', 2, '2025-12-02 09:19:43', '2025-12-03 08:59:58', '2025-12-03 08:59:58'),
(41, 70, 0.10, 0.10, 2085.00, 2085.00, 0.00, 'available', 5, '2025-12-02 13:35:46', '2025-12-02 13:36:32', '2025-12-02 13:36:32'),
(42, 70, 1990.00, 0.00, 2053.00, 0.00, 1990.00, 'sold', 5, '2025-12-03 08:49:43', '2025-12-03 10:15:05', NULL),
(43, 633, 1417.90, 1417.90, NULL, NULL, 0.00, 'factory_use', 5, '2025-12-03 09:12:33', '2025-12-03 09:12:41', NULL),
(44, 285, 1576.00, 0.00, 3276.00, 0.00, 1576.00, 'sold', 5, '2025-12-03 11:02:38', '2025-12-03 11:04:08', NULL),
(45, 284, 1560.00, 0.00, 3246.00, 0.00, 1560.00, 'sold', 5, '2025-12-03 11:06:26', '2025-12-03 11:07:19', NULL),
(46, 286, 1630.00, 0.00, 3360.00, 0.00, 1630.00, 'sold', 5, '2025-12-03 11:09:04', '2025-12-03 11:09:48', NULL),
(47, 69, 2243.00, 0.00, 2161.00, 0.00, 2243.00, 'sold', 5, '2025-12-03 11:14:14', '2025-12-03 11:14:57', NULL),
(48, 85, 2166.00, 0.00, 2025.00, 0.00, 2166.00, 'sold', 5, '2025-12-03 11:17:06', '2025-12-03 11:17:49', NULL),
(49, 87, 2180.00, 0.00, 2037.00, 0.00, 2180.00, 'sold', 5, '2025-12-03 11:21:15', '2025-12-03 11:22:00', NULL),
(50, 232, 1448.00, 0.00, 3192.00, 0.00, 1448.00, 'sold', 5, '2025-12-03 11:23:41', '2025-12-03 11:24:27', NULL),
(51, 281, 1535.00, 0.00, 3162.00, 0.00, 1535.00, 'sold', 5, '2025-12-03 11:25:57', '2025-12-03 11:26:37', NULL),
(52, 300, 1575.00, 0.00, 3246.00, 0.00, 1575.00, 'sold', 5, '2025-12-03 11:28:16', '2025-12-03 11:28:50', NULL),
(53, 243, 1451.00, 0.00, 3274.00, 0.00, 1451.00, 'sold', 5, '2025-12-03 11:31:16', '2025-12-03 11:31:50', NULL),
(54, 315, 1313.00, 0.00, 3208.00, 0.00, 1313.00, 'sold', 5, '2025-12-03 11:33:10', '2025-12-03 11:34:17', NULL),
(55, 311, 1310.00, 0.00, 3204.00, 0.00, 1310.00, 'sold', 5, '2025-12-03 11:35:53', '2025-12-03 11:36:26', NULL),
(56, 96, 1570.00, 0.00, 3510.00, 0.00, 1570.00, 'sold', 5, '2025-12-03 11:39:26', '2025-12-03 11:40:22', NULL),
(57, 322, 1324.00, 0.00, 3242.00, 0.00, 1324.00, 'sold', 5, '2025-12-03 11:43:06', '2025-12-03 11:43:40', NULL),
(58, 323, 1220.00, 0.00, 2998.00, 0.00, 1220.00, 'sold', 5, '2025-12-03 11:44:57', '2025-12-03 11:45:38', NULL),
(59, 588, 1195.00, 0.00, 1870.00, 0.00, 1195.00, 'sold', 5, '2025-12-03 11:52:25', '2025-12-03 11:53:00', NULL),
(60, 282, 1532.00, 0.00, 3142.00, 0.00, 1532.00, 'sold', 5, '2025-12-03 11:54:20', '2025-12-03 11:54:55', NULL),
(61, 604, 1220.00, 0.00, 1776.00, 0.00, 1220.00, 'sold', 5, '2025-12-03 11:57:03', '2025-12-03 11:57:37', NULL),
(62, 597, 1211.00, 0.00, 1770.00, 0.00, 1211.00, 'sold', 5, '2025-12-03 11:58:45', '2025-12-03 11:59:17', NULL),
(63, 216, 1289.00, 0.00, 3434.00, 0.00, 1289.00, 'sold', 5, '2025-12-03 12:01:24', '2025-12-03 12:01:54', NULL),
(64, 215, 1312.00, 0.00, 3462.00, 0.00, 1312.00, 'sold', 5, '2025-12-03 12:03:03', '2025-12-03 12:05:19', NULL),
(65, 317, 1380.00, 0.00, 3428.00, 0.00, 1380.00, 'sold', 5, '2025-12-03 12:08:00', '2025-12-03 12:08:32', NULL),
(66, 324, 1475.00, 0.00, 3618.00, 0.00, 1475.00, 'sold', 5, '2025-12-03 12:09:41', '2025-12-03 12:10:07', NULL),
(67, 323, 1220.00, 0.00, 3366.00, 0.00, 1220.00, 'sold', 5, '2025-12-03 12:11:27', '2025-12-03 12:12:07', NULL),
(68, 288, 1321.00, 0.00, 2722.00, 0.00, 1321.00, 'sold', 5, '2025-12-03 12:13:17', '2025-12-03 12:13:50', NULL),
(69, 320, 1317.00, 0.00, 3206.00, 0.00, 1317.00, 'sold', 5, '2025-12-03 12:15:07', '2025-12-03 12:15:40', NULL),
(70, 318, 1229.00, 0.00, 2994.00, 0.00, 1229.00, 'sold', 5, '2025-12-03 12:16:40', '2025-12-03 12:18:20', NULL),
(71, 307, 1229.00, 0.00, 3202.00, 0.00, 1229.00, 'sold', 5, '2025-12-03 12:20:55', '2025-12-03 12:21:31', NULL),
(72, 308, 1310.00, 0.00, 3202.00, 0.00, 1310.00, 'sold', 5, '2025-12-03 12:22:51', '2025-12-03 12:23:20', NULL),
(73, 310, 1312.00, 0.00, 3202.00, 0.00, 1312.00, 'sold', 5, '2025-12-03 12:24:48', '2025-12-03 12:25:17', NULL),
(74, 309, 1374.00, 0.00, 3350.00, 0.00, 1374.00, 'sold', 5, '2025-12-03 12:26:33', '2025-12-03 12:27:06', NULL),
(75, 312, 1379.00, 0.00, 3364.00, 0.00, 1379.00, 'sold', 5, '2025-12-03 12:28:25', '2025-12-03 12:28:57', NULL),
(76, 313, 889.00, 0.00, 2182.00, 0.00, 889.00, 'sold', 5, '2025-12-03 12:30:10', '2025-12-03 12:30:42', NULL),
(77, 314, 932.00, 0.00, 2316.00, 0.00, 932.00, 'sold', 5, '2025-12-03 12:32:21', '2025-12-03 12:32:48', NULL),
(78, 316, 1312.00, 0.00, 3202.00, 0.00, 1312.00, 'sold', 5, '2025-12-03 12:34:26', '2025-12-03 12:35:08', NULL),
(79, 319, 1319.00, 0.00, 3196.00, 0.00, 1319.00, 'sold', 5, '2025-12-03 12:36:30', '2025-12-03 12:37:23', NULL),
(80, 634, 1166.37, 1166.37, 2117.00, 2117.00, 0.00, 'factory_use', 5, '2025-12-03 14:00:05', '2025-12-08 11:21:46', NULL),
(81, 635, 1588.30, 1584.30, 1620.00, 1620.00, 0.00, 'factory_use', 5, '2025-12-03 14:02:44', '2025-12-09 10:12:10', NULL),
(82, 637, 959.26, 959.26, 1711.00, 1711.00, 0.00, 'factory_use', 5, '2025-12-03 14:06:32', '2025-12-08 11:21:37', NULL),
(83, 638, 637.55, 637.55, 2128.00, 2128.00, 0.00, 'factory_use', 5, '2025-12-03 14:08:34', '2025-12-08 11:21:33', NULL),
(84, 639, 950.58, 950.58, 2027.00, 2027.00, 0.00, 'factory_use', 5, '2025-12-03 14:10:17', '2025-12-08 11:21:28', NULL),
(85, 640, 2672.40, 2668.40, 2219.00, 2219.00, 0.00, 'factory_use', 5, '2025-12-03 14:13:04', '2025-12-09 10:18:22', NULL),
(86, 641, 1992.60, 1992.60, 2015.00, 2015.00, 0.00, 'factory_use', 5, '2025-12-03 14:15:07', '2025-12-08 11:21:17', NULL),
(87, 642, 77.50, 77.50, 1735.00, 1735.00, 0.00, 'factory_use', 5, '2025-12-03 14:16:58', '2025-12-08 11:20:38', NULL),
(88, 57, 1632.38, 1632.38, 2048.00, 2048.00, 0.00, 'factory_use', 5, '2025-12-03 14:19:24', '2025-12-08 11:21:11', NULL),
(89, 643, 568.43, 568.43, 2021.00, 2021.00, 0.00, 'factory_use', 5, '2025-12-03 14:21:33', '2025-12-08 11:21:05', NULL),
(90, 644, 1595.20, 1595.20, 2040.00, 2040.00, 0.00, 'factory_use', 5, '2025-12-03 14:24:41', '2025-12-08 11:20:59', NULL),
(91, 645, 130.23, 130.23, 2134.00, 2134.00, 0.00, 'factory_use', 5, '2025-12-03 14:26:21', '2025-12-08 11:20:20', NULL),
(92, 646, 87.64, 87.64, 1975.00, 1975.00, 0.00, 'factory_use', 5, '2025-12-03 14:29:25', '2025-12-08 11:20:10', NULL),
(93, 633, 1005.30, 1005.30, 2077.00, 2077.00, 0.00, 'factory_use', 5, '2025-12-03 14:30:44', '2025-12-08 11:20:03', NULL),
(94, 647, 3219.58, 3219.58, 3586.00, 3586.00, 0.00, 'factory_use', 5, '2025-12-03 14:33:00', '2025-12-08 11:12:10', NULL),
(95, 273, 1585.50, 1585.50, 3342.00, 3342.00, 0.00, 'factory_use', 5, '2025-12-03 14:34:53', '2025-12-08 11:19:57', NULL),
(96, 212, 113.95, 113.95, 3456.00, 3456.00, 0.00, 'factory_use', 5, '2025-12-03 14:36:37', '2025-12-08 11:19:49', NULL),
(97, 648, 1851.50, 1851.50, 3446.00, 3446.00, 0.00, 'factory_use', 5, '2025-12-03 14:38:58', '2025-12-03 15:17:19', NULL),
(98, 313, 2327.00, 2327.00, 3440.00, 3440.00, 0.00, 'factory_use', 5, '2025-12-03 14:40:54', '2025-12-08 11:19:43', NULL),
(99, 652, 289.30, 289.30, 3392.00, 3392.00, 0.00, 'factory_use', 5, '2025-12-03 14:49:35', '2025-12-08 11:19:42', NULL),
(100, 654, 442.92, 442.92, 3066.00, 3066.00, 0.00, 'factory_use', 5, '2025-12-03 14:59:32', '2025-12-08 11:19:40', NULL),
(101, 655, 1239.60, 1239.60, 3286.00, 3286.00, 0.00, 'factory_use', 5, '2025-12-03 15:02:29', '2025-12-08 11:19:38', NULL),
(102, 211, 1757.30, 1741.00, 3174.00, 3174.00, 0.00, 'factory_use', 5, '2025-12-03 15:04:32', '2025-12-08 11:54:11', NULL),
(103, 656, 707.00, 644.00, 2566.00, 2566.00, 0.00, 'factory_use', 5, '2025-12-03 15:07:13', '2025-12-09 08:02:10', '2025-12-09 08:02:10'),
(104, 657, 708.62, 708.62, 3056.00, 3056.00, 0.00, 'factory_use', 5, '2025-12-03 15:09:47', '2025-12-08 11:19:33', NULL),
(105, 658, 956.90, 932.90, 2688.00, 2688.00, 0.00, 'factory_use', 5, '2025-12-03 15:12:26', '2025-12-08 12:54:04', NULL),
(106, 659, 1936.33, 1841.83, 3340.00, 3340.00, 0.00, 'factory_use', 5, '2025-12-03 15:14:18', '2025-12-08 12:51:09', NULL),
(107, 660, 1010.50, 399.90, 3298.00, 3298.00, 0.00, 'factory_use', 5, '2025-12-03 15:16:10', '2025-12-11 14:50:18', NULL),
(108, 649, 894.39, 853.69, NULL, NULL, 0.00, 'factory_use', 5, '2025-12-08 11:38:26', '2025-12-08 11:42:44', NULL),
(109, 665, 528.45, 498.45, NULL, NULL, 0.00, 'factory_use', 5, '2025-12-08 12:05:41', '2025-12-08 12:07:13', NULL),
(110, 656, 2566.00, 707.00, NULL, NULL, 0.00, 'factory_use', 5, '2025-12-08 12:15:56', '2025-12-08 12:21:42', '2025-12-08 12:21:42'),
(111, 656, 707.00, 707.00, NULL, NULL, 0.00, 'factory_use', 5, '2025-12-08 12:25:06', '2025-12-08 13:25:48', '2025-12-08 13:25:48'),
(112, 636, 1781.00, 1746.00, NULL, NULL, 0.00, 'factory_use', 5, '2025-12-08 15:16:04', '2025-12-09 10:13:36', NULL),
(113, 656, 707.00, 686.00, NULL, NULL, 0.00, 'factory_use', 5, '2025-12-09 08:05:36', '2025-12-09 08:08:55', NULL),
(114, 275, 1649.00, 0.00, 3380.00, 0.00, 1649.00, 'sold', 5, '2025-12-09 10:57:32', '2025-12-09 10:58:30', NULL),
(115, 294, 1612.00, 0.00, 3302.00, 0.00, 1612.00, 'sold', 5, '2025-12-09 11:04:00', '2025-12-09 11:04:37', NULL),
(116, 295, 1665.00, 0.00, 3436.00, 0.00, 1665.00, 'sold', 5, '2025-12-09 11:06:02', '2025-12-09 11:06:42', NULL),
(117, 304, 1360.00, 0.00, 3312.00, 0.00, 1360.00, 'sold', 5, '2025-12-09 11:09:59', '2025-12-09 11:10:36', NULL),
(118, 303, 1308.00, 0.00, 3190.00, 0.00, 1308.00, 'sold', 5, '2025-12-09 11:12:15', '2025-12-09 11:13:22', NULL),
(119, 671, 1472.00, 0.00, 3300.00, 0.00, 1472.00, 'sold', 5, '2025-12-09 11:20:46', '2025-12-09 11:21:22', NULL),
(120, 594, 1232.00, 0.00, 1910.00, 0.00, 1232.00, 'sold', 5, '2025-12-09 11:24:00', '2025-12-09 11:24:43', NULL),
(121, 584, 1215.00, 0.00, 1777.00, 0.00, 1215.00, 'sold', 5, '2025-12-09 11:26:39', '2025-12-09 11:27:13', NULL),
(122, 292, 1445.00, 0.00, 2976.00, 0.00, 1445.00, 'sold', 5, '2025-12-09 11:31:17', '2025-12-09 11:32:26', NULL),
(123, 279, 1535.00, 0.00, 3172.00, 0.00, 1535.00, 'sold', 5, '2025-12-09 11:33:54', '2025-12-09 11:34:29', NULL),
(124, 583, 1211.00, 0.00, 1772.00, 0.00, 1211.00, 'sold', 5, '2025-12-09 11:37:23', '2025-12-09 11:37:57', NULL),
(125, 581, 1225.00, 0.00, 1899.00, 0.00, 1225.00, 'sold', 5, '2025-12-09 11:39:05', '2025-12-09 11:39:40', NULL),
(126, 602, 1158.00, 0.00, 1813.00, 0.00, 1158.00, 'sold', 5, '2025-12-09 11:42:12', '2025-12-09 11:43:57', NULL),
(127, 590, 1160.00, 0.00, 1817.00, 0.00, 1160.00, 'sold', 5, '2025-12-09 11:44:58', '2025-12-09 11:45:38', NULL),
(128, 212, 1579.00, 0.00, 3578.00, 0.00, 1579.00, 'sold', 5, '2025-12-09 11:48:15', '2025-12-09 11:49:10', NULL),
(129, 213, 1550.00, 0.00, 3492.00, 0.00, 1550.00, 'sold', 5, '2025-12-09 11:50:39', '2025-12-09 11:51:16', NULL),
(130, 214, 1450.00, 0.00, 3272.00, 0.00, 1450.00, 'sold', 5, '2025-12-09 11:53:03', '2025-12-09 11:53:35', NULL),
(131, 104, 2444.00, 0.00, 3602.00, 0.00, 2444.00, 'sold', 5, '2025-12-09 11:56:40', '2025-12-09 11:57:14', NULL),
(132, 105, 2199.00, 0.00, 3212.00, 0.00, 2199.00, 'sold', 5, '2025-12-09 11:59:07', '2025-12-09 11:59:40', NULL),
(133, 112, 2264.00, 0.00, 3426.00, 0.00, 2264.00, 'sold', 5, '2025-12-09 12:01:18', '2025-12-09 12:01:55', NULL),
(134, 113, 2242.00, 0.00, 3368.00, 0.00, 2242.00, 'sold', 5, '2025-12-09 12:03:26', '2025-12-09 12:04:16', NULL),
(135, 568, 2322.00, 0.00, 3474.00, 0.00, 2322.00, 'sold', 5, '2025-12-09 12:08:17', '2025-12-09 12:08:48', NULL),
(136, 122, 2223.00, 0.00, 3366.00, 0.00, 2223.00, 'sold', 5, '2025-12-09 12:10:51', '2025-12-09 12:11:21', NULL),
(137, 299, 1514.00, 0.00, 3134.00, 0.00, 1514.00, 'sold', 5, '2025-12-09 12:20:39', '2025-12-09 12:21:12', NULL),
(138, 672, 1474.00, 0.00, 3302.00, 0.00, 1474.00, 'sold', 5, '2025-12-09 12:26:52', '2025-12-09 12:27:20', NULL),
(139, 176, 844.60, 826.60, 3166.00, 3166.00, 0.00, 'factory_use', 5, '2025-12-09 12:30:58', '2025-12-09 12:37:23', NULL),
(140, 161, 352.30, 352.30, 2184.00, 2184.00, 0.00, 'factory_use', 5, '2025-12-09 12:41:28', '2025-12-09 12:41:36', NULL),
(141, 280, 1583.00, 1583.00, 3248.00, 3248.00, 0.00, 'available', 5, '2025-12-11 12:47:11', NULL, NULL),
(142, 660, 1010.50, 1010.50, 3298.00, 3298.00, 0.00, 'factory_use', 5, '2025-12-11 14:37:58', '2025-12-11 14:38:07', NULL);

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
(17, 49, 11, 'inflow', 'Stock moved to factory use - Entry #11 (2600.00m available)', 2600.00, 0.00, 2600.00, 'stock_entry', 11, 2, '2025-11-08 11:38:39'),
(18, 49, 11, 'outflow', 'Retail sale to Mr Lawal (40m @ ₦3000/m)', 0.00, 40.00, 2560.00, 'sale', 11, 2, '2025-11-08 11:40:38'),
(19, 49, 11, 'outflow', 'Retail sale to Mr Lawal (288.7m @ ₦10300/m)', 0.00, 288.70, 2271.30, 'sale', 12, 2, '2025-11-08 11:50:31'),
(20, 50, 12, 'inflow', 'Stock moved to factory use - Entry #12 (2650.00m available)', 2650.00, 0.00, 2650.00, 'stock_entry', 12, 2, '2025-11-08 12:27:06'),
(21, 50, 12, 'outflow', 'Retail sale to Mr Lawal (400m @ ₦2000/m)', 0.00, 400.00, 2250.00, 'sale', 14, 5, '2025-11-08 12:38:10'),
(22, 50, 12, 'outflow', 'Retail sale to Mr Lawal (300m @ ₦2000/m)', 0.00, 300.00, 1950.00, 'sale', 15, 5, '2025-11-08 12:40:35'),
(23, 50, 12, 'outflow', 'Retail sale to Mr Lawal (400m @ ₦2000/m)', 0.00, 400.00, 1550.00, 'sale', 16, 5, '2025-11-08 12:41:44'),
(24, 51, 13, 'inflow', 'Stock moved to factory use - Entry #13 (2500.00m available)', 2500.00, 0.00, 2500.00, 'stock_entry', 13, 5, '2025-11-08 12:48:29'),
(25, 51, 13, 'outflow', 'Retail sale to Mr Lawal (200m @ ₦2000/m)', 0.00, 200.00, 2300.00, 'sale', 17, 5, '2025-11-08 12:49:07'),
(26, 51, 13, 'outflow', 'Retail sale to Mr Lawal (400m @ ₦2000/m)', 0.00, 400.00, 1900.00, 'sale', 18, 5, '2025-11-08 12:50:39'),
(27, 51, 13, 'outflow', 'Stock moved back to available - Entry #13 (removing 1900m from factory tracking)', 0.00, 1900.00, 0.00, 'status_change', 13, 5, '2025-11-14 15:36:56'),
(28, 51, 13, 'inflow', 'Stock moved to factory use - Entry #13 (1900.00m available)', 1900.00, 0.00, 1900.00, 'stock_entry', 13, 5, '2025-11-14 15:37:13'),
(29, 67, 31, 'inflow', 'Stock moved to factory use - Entry #31 (5800.00m available)', 5800.00, 0.00, 5800.00, 'stock_entry', 31, 5, '2025-11-17 12:27:52'),
(30, 299, 33, 'inflow', 'Stock moved to factory use - Entry #33 (2000.00m available)', 2000.00, 0.00, 2000.00, 'stock_entry', 33, 5, '2025-11-18 11:27:44'),
(31, 299, 33, 'outflow', 'Stock moved back to available - Entry #33 (removing 2000m from factory tracking)', 0.00, 2000.00, 0.00, 'status_change', 33, 5, '2025-11-18 11:37:54'),
(32, 316, 34, 'inflow', 'Stock moved to factory use - Entry #34 (40000.00m available)', 40000.00, 0.00, 40000.00, 'stock_entry', 34, 5, '2025-11-18 13:31:52'),
(33, 569, 35, 'inflow', 'Stock moved to factory use - Entry #35 (5000.00m available)', 5000.00, 0.00, 5000.00, 'stock_entry', 35, 5, '2025-11-19 08:34:57'),
(34, 311, 36, 'inflow', 'Stock moved to factory use - Entry #36 (5000.00m available)', 5000.00, 0.00, 5000.00, 'stock_entry', 36, 5, '2025-11-19 08:50:41'),
(35, 287, 37, 'inflow', 'Stock moved to factory use - Entry #37 (5000.00m available)', 5000.00, 0.00, 5000.00, 'stock_entry', 37, 5, '2025-11-19 08:55:17'),
(36, 286, 38, 'inflow', 'Stock moved to factory use - Entry #38 (6000.00m available)', 6000.00, 0.00, 6000.00, 'stock_entry', 38, 5, '2025-11-19 09:19:55'),
(37, 50, 12, 'outflow', 'Stock moved back to available - Entry #12 (removing 1550m from factory tracking)', 0.00, 1550.00, 0.00, 'status_change', 12, 2, '2025-11-19 13:13:23'),
(38, 50, 12, 'inflow', 'Stock moved to factory use - Entry #12 (1550.00m available)', 1550.00, 0.00, 1550.00, 'stock_entry', 12, 2, '2025-11-19 13:13:48'),
(39, 286, 38, 'outflow', 'Stock moved back to available - Entry #38 (removing 6000m from factory tracking)', 0.00, 6000.00, 0.00, 'status_change', 38, 5, '2025-11-19 13:18:44'),
(40, 287, 37, 'outflow', 'Stock moved back to available - Entry #37 (removing 5000m from factory tracking)', 0.00, 5000.00, 0.00, 'status_change', 37, 5, '2025-11-19 13:25:23'),
(41, 633, 43, 'inflow', 'Stock moved to factory use - Entry #43 (1417.90m available)', 1417.90, 0.00, 1417.90, 'stock_entry', 43, 5, '2025-12-03 09:12:41'),
(42, 648, 97, 'inflow', 'Stock moved to factory use - Entry #97 (1851.50m available)', 1851.50, 0.00, 1851.50, 'stock_entry', 97, 5, '2025-12-03 15:17:19'),
(43, 660, 107, 'inflow', 'Stock moved to factory use - Entry #107 (1010.50m available)', 1010.50, 0.00, 1010.50, 'stock_entry', 107, 5, '2025-12-03 15:17:25'),
(44, 660, 107, 'outflow', 'Stock moved back to available - Entry #107 (removing 1010.5m from factory tracking)', 0.00, 1010.50, 0.00, 'status_change', 107, 5, '2025-12-03 15:17:37'),
(45, 658, 105, 'inflow', 'Stock moved to factory use - Entry #105 (956.90m available)', 956.90, 0.00, 956.90, 'stock_entry', 105, 5, '2025-12-08 08:35:48'),
(46, 660, 107, 'inflow', 'Stock moved to factory use - Entry #107 (1010.50m available)', 1010.50, 0.00, 1010.50, 'stock_entry', 107, 5, '2025-12-08 08:36:13'),
(47, 660, 107, 'outflow', 'Stock moved back to available - Entry #107 (removing 1010.5m from factory tracking)', 0.00, 1010.50, 0.00, 'status_change', 107, 5, '2025-12-08 08:38:02'),
(48, 633, 93, 'inflow', 'Stock moved to factory use - Entry #93 (1005.30m available)', 1005.30, 0.00, 1005.30, 'stock_entry', 93, 5, '2025-12-08 11:11:27'),
(49, 633, 93, 'outflow', 'Stock moved back to available - Entry #93 (removing 1005.3m from factory tracking)', 0.00, 1005.30, 0.00, 'status_change', 93, 5, '2025-12-08 11:11:48'),
(50, 647, 94, 'inflow', 'Stock moved to factory use - Entry #94 (3219.58m available)', 3219.58, 0.00, 3219.58, 'stock_entry', 94, 5, '2025-12-08 11:12:10'),
(51, 659, 106, 'inflow', 'Stock moved to factory use - Entry #106 (1936.33m available)', 1936.33, 0.00, 1936.33, 'stock_entry', 106, 5, '2025-12-08 11:19:28'),
(52, 660, 107, 'inflow', 'Stock moved to factory use - Entry #107 (1010.50m available)', 1010.50, 0.00, 1010.50, 'stock_entry', 107, 5, '2025-12-08 11:19:30'),
(53, 657, 104, 'inflow', 'Stock moved to factory use - Entry #104 (708.62m available)', 708.62, 0.00, 708.62, 'stock_entry', 104, 5, '2025-12-08 11:19:33'),
(54, 656, 103, 'inflow', 'Stock moved to factory use - Entry #103 (707.00m available)', 707.00, 0.00, 707.00, 'stock_entry', 103, 5, '2025-12-08 11:19:34'),
(55, 211, 102, 'inflow', 'Stock moved to factory use - Entry #102 (1757.30m available)', 1757.30, 0.00, 1757.30, 'stock_entry', 102, 5, '2025-12-08 11:19:36'),
(56, 655, 101, 'inflow', 'Stock moved to factory use - Entry #101 (1239.60m available)', 1239.60, 0.00, 1239.60, 'stock_entry', 101, 5, '2025-12-08 11:19:38'),
(57, 654, 100, 'inflow', 'Stock moved to factory use - Entry #100 (442.92m available)', 442.92, 0.00, 442.92, 'stock_entry', 100, 5, '2025-12-08 11:19:40'),
(58, 652, 99, 'inflow', 'Stock moved to factory use - Entry #99 (289.30m available)', 289.30, 0.00, 289.30, 'stock_entry', 99, 5, '2025-12-08 11:19:42'),
(59, 313, 98, 'inflow', 'Stock moved to factory use - Entry #98 (2327.00m available)', 2327.00, 0.00, 2327.00, 'stock_entry', 98, 5, '2025-12-08 11:19:43'),
(60, 212, 96, 'inflow', 'Stock moved to factory use - Entry #96 (113.95m available)', 113.95, 0.00, 113.95, 'stock_entry', 96, 5, '2025-12-08 11:19:49'),
(61, 273, 95, 'inflow', 'Stock moved to factory use - Entry #95 (1585.50m available)', 1585.50, 0.00, 1585.50, 'stock_entry', 95, 5, '2025-12-08 11:19:57'),
(62, 633, 93, 'inflow', 'Stock moved to factory use - Entry #93 (1005.30m available)', 1005.30, 0.00, 1005.30, 'stock_entry', 93, 5, '2025-12-08 11:20:03'),
(63, 646, 92, 'inflow', 'Stock moved to factory use - Entry #92 (87.64m available)', 87.64, 0.00, 87.64, 'stock_entry', 92, 5, '2025-12-08 11:20:10'),
(64, 645, 91, 'inflow', 'Stock moved to factory use - Entry #91 (130.23m available)', 130.23, 0.00, 130.23, 'stock_entry', 91, 5, '2025-12-08 11:20:20'),
(65, 642, 87, 'inflow', 'Stock moved to factory use - Entry #87 (77.50m available)', 77.50, 0.00, 77.50, 'stock_entry', 87, 5, '2025-12-08 11:20:38'),
(66, 644, 90, 'inflow', 'Stock moved to factory use - Entry #90 (1595.20m available)', 1595.20, 0.00, 1595.20, 'stock_entry', 90, 5, '2025-12-08 11:20:59'),
(67, 643, 89, 'inflow', 'Stock moved to factory use - Entry #89 (568.43m available)', 568.43, 0.00, 568.43, 'stock_entry', 89, 5, '2025-12-08 11:21:05'),
(68, 57, 88, 'inflow', 'Stock moved to factory use - Entry #88 (1632.38m available)', 1632.38, 0.00, 1632.38, 'stock_entry', 88, 5, '2025-12-08 11:21:11'),
(69, 641, 86, 'inflow', 'Stock moved to factory use - Entry #86 (1992.60m available)', 1992.60, 0.00, 1992.60, 'stock_entry', 86, 5, '2025-12-08 11:21:17'),
(70, 640, 85, 'inflow', 'Stock moved to factory use - Entry #85 (2672.40m available)', 2672.40, 0.00, 2672.40, 'stock_entry', 85, 5, '2025-12-08 11:21:23'),
(71, 639, 84, 'inflow', 'Stock moved to factory use - Entry #84 (950.58m available)', 950.58, 0.00, 950.58, 'stock_entry', 84, 5, '2025-12-08 11:21:28'),
(72, 638, 83, 'inflow', 'Stock moved to factory use - Entry #83 (637.55m available)', 637.55, 0.00, 637.55, 'stock_entry', 83, 5, '2025-12-08 11:21:33'),
(73, 637, 82, 'inflow', 'Stock moved to factory use - Entry #82 (959.26m available)', 959.26, 0.00, 959.26, 'stock_entry', 82, 5, '2025-12-08 11:21:37'),
(74, 635, 81, 'inflow', 'Stock moved to factory use - Entry #81 (1588.30m available)', 1588.30, 0.00, 1588.30, 'stock_entry', 81, 5, '2025-12-08 11:21:41'),
(75, 634, 80, 'inflow', 'Stock moved to factory use - Entry #80 (1166.37m available)', 1166.37, 0.00, 1166.37, 'stock_entry', 80, 5, '2025-12-08 11:21:46'),
(76, 659, 106, 'outflow', 'Production drawdown for sale #126', 0.00, 9.00, 1927.33, 'sale', 126, 5, '2025-12-08 11:24:20'),
(77, 649, 108, 'inflow', 'Stock moved to factory use - Entry #108 (894.39m available)', 894.39, 0.00, 894.39, 'stock_entry', 108, 5, '2025-12-08 11:38:31'),
(78, 649, 108, 'outflow', 'Production drawdown for sale #127', 0.00, 40.70, 853.69, 'sale', 127, 5, '2025-12-08 11:42:44'),
(79, 211, 102, 'outflow', 'Production drawdown for sale #128', 0.00, 16.30, 1741.00, 'sale', 128, 5, '2025-12-08 11:54:11'),
(80, 665, 109, 'inflow', 'Stock moved to factory use - Entry #109 (528.45m available)', 528.45, 0.00, 528.45, 'stock_entry', 109, 5, '2025-12-08 12:05:45'),
(81, 665, 109, 'outflow', 'Production drawdown for sale #129', 0.00, 30.00, 498.45, 'sale', 129, 5, '2025-12-08 12:07:13'),
(82, 659, 106, 'outflow', 'Production drawdown for sale #130', 0.00, 18.00, 1909.33, 'sale', 130, 5, '2025-12-08 12:11:26'),
(83, 656, 110, 'inflow', 'Stock moved to factory use - Entry #110 (2566.00m available)', 2566.00, 0.00, 2566.00, 'stock_entry', 110, 5, '2025-12-08 12:15:59'),
(84, 656, 103, 'outflow', 'Production drawdown for sale #131', 0.00, 21.00, 686.00, 'sale', 131, 5, '2025-12-08 12:18:20'),
(85, 656, 111, 'inflow', 'Stock moved to factory use - Entry #111 (707.00m available)', 707.00, 0.00, 707.00, 'stock_entry', 111, 5, '2025-12-08 12:25:16'),
(86, 656, 103, 'outflow', 'Production drawdown for sale #132', 0.00, 21.00, 665.00, 'sale', 132, 5, '2025-12-08 12:30:36'),
(87, 656, 111, 'outflow', 'Stock moved back to available - Entry #111 (removing 707m from factory tracking)', 0.00, 707.00, 0.00, 'status_change', 111, 5, '2025-12-08 12:34:42'),
(88, 656, 111, 'inflow', 'Stock moved to factory use - Entry #111 (707.00m available)', 707.00, 0.00, 707.00, 'stock_entry', 111, 5, '2025-12-08 12:34:59'),
(89, 656, 111, 'outflow', 'Stock moved back to available - Entry #111 (removing 707m from factory tracking)', 0.00, 707.00, 0.00, 'status_change', 111, 5, '2025-12-08 12:35:01'),
(90, 659, 106, 'outflow', 'Production drawdown for sale #133', 0.00, 31.50, 1877.83, 'sale', 133, 5, '2025-12-08 12:43:50'),
(91, 659, 106, 'outflow', 'Production drawdown for sale #134', 0.00, 36.00, 1841.83, 'sale', 134, 5, '2025-12-08 12:51:09'),
(92, 658, 105, 'outflow', 'Production drawdown for sale #135', 0.00, 24.00, 932.90, 'sale', 135, 5, '2025-12-08 12:54:04'),
(93, 660, 107, 'outflow', 'Production drawdown for sale #136', 0.00, 305.50, 705.00, 'sale', 136, 5, '2025-12-08 13:09:59'),
(94, 656, 111, 'inflow', 'Stock moved to factory use - Entry #111 (707.00m available)', 707.00, 0.00, 707.00, 'stock_entry', 111, 5, '2025-12-08 13:17:58'),
(95, 656, 103, 'outflow', 'Production drawdown for sale #137', 0.00, 21.00, 644.00, 'sale', 137, 5, '2025-12-08 13:20:19'),
(96, 636, 112, 'inflow', 'Stock moved to factory use - Entry #112 (1781.00m available)', 1781.00, 0.00, 1781.00, 'stock_entry', 112, 5, '2025-12-08 15:16:07'),
(97, 656, 113, 'inflow', 'Stock moved to factory use - Entry #113 (707.00m available)', 707.00, 0.00, 707.00, 'stock_entry', 113, 5, '2025-12-09 08:05:42'),
(98, 656, 113, 'outflow', 'Production drawdown for sale #138', 0.00, 21.00, 686.00, 'sale', 138, 5, '2025-12-09 08:08:55'),
(99, 635, 81, 'outflow', 'Production drawdown for sale #139', 0.00, 4.00, 1584.30, 'sale', 139, 5, '2025-12-09 10:12:10'),
(100, 636, 112, 'outflow', 'Production drawdown for sale #140', 0.00, 35.00, 1746.00, 'sale', 140, 5, '2025-12-09 10:13:36'),
(101, 640, 85, 'outflow', 'Production drawdown for sale #141', 0.00, 4.00, 2668.40, 'sale', 141, 5, '2025-12-09 10:18:22'),
(102, 176, 139, 'inflow', 'Stock moved to factory use - Entry #139 (844.60m available)', 844.60, 0.00, 844.60, 'stock_entry', 139, 5, '2025-12-09 12:36:07'),
(103, 176, 139, 'outflow', 'Production drawdown for sale #192', 0.00, 18.00, 826.60, 'sale', 192, 5, '2025-12-09 12:37:23'),
(104, 161, 140, 'inflow', 'Stock moved to factory use - Entry #140 (352.30m available)', 352.30, 0.00, 352.30, 'stock_entry', 140, 5, '2025-12-09 12:41:36'),
(105, 660, 142, 'inflow', 'Stock moved to factory use - Entry #142 (1010.50m available)', 1010.50, 0.00, 1010.50, 'stock_entry', 142, 5, '2025-12-11 14:38:07'),
(106, 660, 107, 'outflow', 'Production drawdown for sale #193', 0.00, 305.10, 399.90, 'sale', 193, 5, '2025-12-11 14:50:18');

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
-- Table structure for table `tile_products`
--

CREATE TABLE `tile_products` (
  `id` int(11) NOT NULL,
  `code` varchar(100) NOT NULL COMMENT 'Format: DESIGN-COLOR-GAUGE',
  `design_id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL,
  `gauge` enum('thick','normal','light') NOT NULL,
  `status` enum('available','out_of_stock') DEFAULT 'out_of_stock',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tile_products`
--

INSERT INTO `tile_products` (`id`, `code`, `design_id`, `color_id`, `gauge`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'BB_01-TBLACK-THICK', 18, 12, 'thick', 'available', 5, '2025-12-02 10:53:35', '2025-12-02 12:21:24', NULL),
(2, 'BM_01-BM_01-LIGHT', 11, 26, 'light', 'available', 5, '2025-12-02 13:39:44', '2025-12-02 13:40:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tile_sales`
--

CREATE TABLE `tile_sales` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `tile_product_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tile_sales`
--

INSERT INTO `tile_sales` (`id`, `customer_id`, `tile_product_id`, `quantity`, `unit_price`, `total_amount`, `status`, `notes`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 5, 1, 400.00, 1000.00, 400000.00, 'completed', '', 2, '2025-12-03 10:24:13', NULL, NULL),
(2, 6, 2, 1000.00, 1000.00, 1000000.00, 'completed', '', 2, '2025-12-03 10:28:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tile_stock_ledger`
--

CREATE TABLE `tile_stock_ledger` (
  `id` int(11) NOT NULL,
  `tile_product_id` int(11) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `transaction_code` varchar(100) DEFAULT NULL,
  `quantity_in` decimal(10,2) DEFAULT 0.00,
  `quantity_out` decimal(10,2) DEFAULT 0.00,
  `balance` decimal(10,2) NOT NULL,
  `reference_type` enum('stock_in','sale','adjustment','return') NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tile_stock_ledger`
--

INSERT INTO `tile_stock_ledger` (`id`, `tile_product_id`, `transaction_date`, `transaction_code`, `quantity_in`, `quantity_out`, `balance`, `reference_type`, `reference_id`, `description`, `created_by`, `created_at`) VALUES
(1, 1, '2025-12-02 10:54:52', '', 2345.00, 0.00, 2345.00, 'stock_in', NULL, 'sales of black bond roofing tiles', 5, '2025-12-02 10:54:52'),
(2, 1, '2025-12-02 12:21:24', '', 2085.00, 0.00, 4430.00, 'stock_in', NULL, 'sales', 5, '2025-12-02 12:21:24'),
(3, 2, '2025-12-02 13:40:39', '', 2458.00, 0.00, 2458.00, 'stock_in', NULL, 'sales black milano', 5, '2025-12-02 13:40:39'),
(4, 1, '2025-12-03 10:24:13', NULL, 0.00, 400.00, 4030.00, 'sale', 1, 'Sale to customer', 2, '2025-12-03 10:24:13'),
(5, 2, '2025-12-03 10:28:07', NULL, 0.00, 1000.00, 1458.00, 'sale', 2, 'Sale to customer', 2, '2025-12-03 10:28:07');

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
(1, 'admin@example.com', '$2y$10$RBop9EOLBg.Vo9AKPBp.xOnx/18TGybfpkmU.//PgdAwLFSXr3GZ6', 'SHEMAIAH WAMBEBE YABA-SHIAKA', 'super_admin', '2025-11-05 08:37:50', '2025-12-15 09:28:46', '2025-12-15 09:28:38'),
(2, 'admin@obumek360.app', '$2y$10$RBop9EOLBg.Vo9AKPBp.xOnx/18TGybfpkmU.//PgdAwLFSXr3GZ6', 'Engineer Martin', 'super_admin', '2025-11-05 08:37:50', '2025-11-06 22:31:43', NULL),
(3, 'admin2@obumek360.app', '$2y$10$CeQL8JveXCejBemhfk2lS.L.0b8e.2qDn9KhTuGfpoQEXAYtJC5iS', 'Emeka Ezealisiji', 'super_admin', '2025-11-05 08:48:31', '2025-11-08 11:22:09', NULL),
(4, 'nkechi4ezealisisi@gmail.com', '$2y$10$JsrDljhm9SZujr8iwOwMGuxr0M5Yvry6RtPpnKemtL4hW/6V0exI6', 'Nkechi Ezealisiji', 'super_admin', '2025-11-07 13:21:22', NULL, NULL),
(5, 'omale.ochigbo@obumek360.app', '$2y$10$HbxZ08pNvzLIQbYtd9kjOeeR17jFJZk4UvWGtT5XTfG3GFl7i2l86', 'Ochigbo Omale', 'super_admin', '2025-11-08 11:23:02', '2025-11-08 12:35:33', NULL);

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
(10, 3, 'user_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 08:48:31', NULL),
(11, 3, 'customer_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 08:48:31', NULL),
(12, 3, 'stock_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 08:48:31', NULL),
(13, 3, 'sales_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-05 08:48:31', NULL),
(14, 3, 'reports', '[\"view\"]', '2025-11-05 08:48:31', NULL),
(15, 3, 'dashboard', '[\"view\"]', '2025-11-05 08:48:31', NULL),
(22, 4, 'user_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-07 13:21:22', NULL),
(23, 4, 'customer_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-07 13:21:22', NULL),
(24, 4, 'stock_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-07 13:21:22', NULL),
(25, 4, 'sales_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-07 13:21:22', NULL),
(26, 4, 'reports', '[\"view\"]', '2025-11-07 13:21:22', NULL),
(27, 4, 'dashboard', '[\"view\"]', '2025-11-07 13:21:22', NULL),
(44, 5, 'stock_management', '[\"view\",\"create\"]', '2025-11-08 12:32:11', NULL),
(45, 5, 'sales_management', '[\"view\",\"create\",\"edit\"]', '2025-11-08 12:32:11', NULL),
(46, 5, 'reports', '[\"view\"]', '2025-11-08 12:32:11', NULL),
(47, 5, 'dashboard', '[\"view\",\"create\"]', '2025-11-08 12:32:11', NULL),
(59, 2, 'user_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL),
(60, 2, 'customer_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL),
(61, 2, 'stock_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL),
(62, 2, 'sales_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL),
(63, 2, 'warehouse_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL),
(64, 2, 'color_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL),
(65, 2, 'production_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL),
(66, 2, 'invoice_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL),
(67, 2, 'supply_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL),
(68, 2, 'reports', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL),
(69, 2, 'dashboard', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL),
(70, 2, 'design_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL),
(71, 2, 'tile_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL),
(72, 2, 'tile_sales', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-28 20:13:04', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_production_properties`
-- (See below for the actual view)
--
CREATE TABLE `v_production_properties` (
`id` int(11)
,`code` varchar(50)
,`name` varchar(100)
,`category` enum('alusteel','aluminum','kzinc')
,`property_type` enum('unit_based','meter_based','bundle_based')
,`is_addon` tinyint(1)
,`calculation_method` enum('fixed','percentage','per_unit')
,`applies_to` enum('subtotal','total','per_item')
,`is_refundable` tinyint(1)
,`display_section` enum('production','addon','adjustment')
,`default_price` decimal(10,2)
,`sort_order` int(11)
,`is_active` tinyint(1)
,`metadata` longtext
,`property_category_display` varchar(10)
);

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
(4, 'Head Office', 'EA 18 - 19 , Saburi District Dei - Dei Abuja', '08065336645', 1, '2025-11-12 11:05:19', NULL, NULL),
(5, 'Timber shade', 'Timber shade, Dei dei Abuja.', '08172350500', 1, '2025-11-12 11:06:15', NULL, NULL),
(6, 'New Site Dei-dei Abuja', 'C1 Line Dei Dei, Building Material Market, New Site Dei-dei Abuja', '08036211176.', 1, '2025-11-12 11:06:48', NULL, NULL),
(7, 'Idu Branch', 'Idu Industrial Area Abuja', '09024293504', 1, '2025-11-12 11:08:10', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure for view `v_production_properties`
--
DROP TABLE IF EXISTS `v_production_properties`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_production_properties`  AS SELECT `production_properties`.`id` AS `id`, `production_properties`.`code` AS `code`, `production_properties`.`name` AS `name`, `production_properties`.`category` AS `category`, `production_properties`.`property_type` AS `property_type`, `production_properties`.`is_addon` AS `is_addon`, `production_properties`.`calculation_method` AS `calculation_method`, `production_properties`.`applies_to` AS `applies_to`, `production_properties`.`is_refundable` AS `is_refundable`, `production_properties`.`display_section` AS `display_section`, `production_properties`.`default_price` AS `default_price`, `production_properties`.`sort_order` AS `sort_order`, `production_properties`.`is_active` AS `is_active`, `production_properties`.`metadata` AS `metadata`, CASE WHEN `production_properties`.`is_addon` = 1 AND `production_properties`.`is_refundable` = 1 THEN 'Adjustment' WHEN `production_properties`.`is_addon` = 1 THEN 'Add-On' ELSE 'Production' END AS `property_category_display` FROM `production_properties` WHERE `production_properties`.`deleted_at` is null ;

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
  ADD KEY `color_id` (`color_id`),
  ADD KEY `idx_gauge` (`gauge`);

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
-- Indexes for table `designs`
--
ALTER TABLE `designs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_active` (`is_active`),
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
  ADD KEY `idx_invoice_number` (`invoice_number`),
  ADD KEY `idx_sale_reference` (`sale_type`,`sale_reference_id`);

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
-- Indexes for table `production_properties`
--
ALTER TABLE `production_properties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_category_active` (`category`,`is_active`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_sort_order` (`sort_order`),
  ADD KEY `idx_is_addon` (`is_addon`),
  ADD KEY `idx_display_section` (`display_section`),
  ADD KEY `idx_category_addon` (`category`,`is_addon`,`is_active`);

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
-- Indexes for table `tile_products`
--
ALTER TABLE `tile_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `unique_product` (`design_id`,`color_id`,`gauge`,`deleted_at`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_design` (`design_id`),
  ADD KEY `idx_color` (`color_id`),
  ADD KEY `idx_gauge` (`gauge`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_deleted` (`deleted_at`);

--
-- Indexes for table `tile_sales`
--
ALTER TABLE `tile_sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_product` (`tile_product_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_date` (`created_at`),
  ADD KEY `idx_deleted` (`deleted_at`),
  ADD KEY `idx_sales_reporting` (`created_at`,`status`,`deleted_at`);

--
-- Indexes for table `tile_stock_ledger`
--
ALTER TABLE `tile_stock_ledger`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_product` (`tile_product_id`),
  ADD KEY `idx_date` (`transaction_date`),
  ADD KEY `idx_reference` (`reference_type`,`reference_id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_ledger_balance_lookup` (`tile_product_id`,`created_at`,`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=673;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `designs`
--
ALTER TABLE `designs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `production`
--
ALTER TABLE `production`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `production_properties`
--
ALTER TABLE `production_properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT for table `stock_entries`
--
ALTER TABLE `stock_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `stock_ledger`
--
ALTER TABLE `stock_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `supply_delivery`
--
ALTER TABLE `supply_delivery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tile_products`
--
ALTER TABLE `tile_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tile_sales`
--
ALTER TABLE `tile_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tile_stock_ledger`
--
ALTER TABLE `tile_stock_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- Constraints for table `designs`
--
ALTER TABLE `designs`
  ADD CONSTRAINT `designs_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

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
-- Constraints for table `production_properties`
--
ALTER TABLE `production_properties`
  ADD CONSTRAINT `production_properties_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `tile_products`
--
ALTER TABLE `tile_products`
  ADD CONSTRAINT `tile_products_ibfk_1` FOREIGN KEY (`design_id`) REFERENCES `designs` (`id`),
  ADD CONSTRAINT `tile_products_ibfk_2` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`),
  ADD CONSTRAINT `tile_products_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `tile_sales`
--
ALTER TABLE `tile_sales`
  ADD CONSTRAINT `tile_sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `tile_sales_ibfk_2` FOREIGN KEY (`tile_product_id`) REFERENCES `tile_products` (`id`),
  ADD CONSTRAINT `tile_sales_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `tile_stock_ledger`
--
ALTER TABLE `tile_stock_ledger`
  ADD CONSTRAINT `tile_stock_ledger_ibfk_1` FOREIGN KEY (`tile_product_id`) REFERENCES `tile_products` (`id`),
  ADD CONSTRAINT `tile_stock_ledger_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
