-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2025 at 10:51 AM
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
(70, 'D60', 'G/beige Aluminium coil', 'GBeige', 14, 2053.00, 1990.00, '0.55', 'aluminum', 'out_of_stock', 5, '2025-11-10 13:49:05', '2025-12-02 13:36:32', NULL),
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
(96, 'AS230', 'Alusteel coil', 'TBlack', 12, 3510.00, 1570.00, '0.24', 'alusteel', 'available', 5, '2025-11-10 14:35:43', '2025-11-14 13:17:28', NULL),
(97, 'AS89', 'Alusteel coil', 'SBlue', 11, 3440.00, 2347.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:37:00', '2025-11-14 13:25:11', NULL),
(98, 'AS91', 'Alusteel coil', 'SBlue', 11, 3511.00, 2375.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:37:53', '2025-11-14 13:24:16', NULL),
(99, 'AS92', 'Alusteel coil', 'SBlue', 11, 3598.00, 2457.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:39:14', '2025-11-14 13:23:08', NULL),
(100, 'AS93', 'Alusteel coil', 'SBlue', 11, 3222.00, 2143.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:39:56', '2025-11-14 13:22:22', NULL),
(101, 'AS94', 'Alusteel coil', 'SBlue', 11, 3000.00, 2011.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:41:01', '2025-11-14 13:21:48', NULL),
(102, 'AS95', 'Alusteel coil', 'SBlue', 11, 3034.00, 2006.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:41:56', '2025-11-14 13:20:49', NULL),
(103, 'AS96', 'Alusteel coil', 'SBlue', 11, 2342.00, 1522.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:42:41', '2025-11-14 13:19:55', NULL),
(104, 'AS104', 'Alusteel coil', 'SBlue', 11, 3602.00, 2444.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:43:33', '2025-11-14 13:19:05', NULL),
(105, 'AS105', 'Alusteel coil', 'SBlue', 11, 3212.00, 2199.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:44:12', '2025-11-14 13:26:42', NULL),
(106, 'AS106', 'Alusteel coil', 'IWhite', 16, 3196.00, 2164.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:45:25', '2025-11-14 13:27:23', NULL),
(107, 'AS108', 'Alusteel coil', 'IWhite', 16, 3412.00, 2321.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:46:06', '2025-11-14 13:27:58', NULL),
(108, 'AS110', 'Alusteel coil', 'IWhite', 16, 3656.00, 2457.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:47:05', '2025-11-14 13:28:36', NULL),
(109, 'AS111', 'Alusteel coil', 'IWhite', 16, 3322.00, 2239.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:47:42', '2025-11-14 13:29:10', NULL),
(110, 'AS112', 'Alusteel coil', 'IWhite', 16, 2764.00, 1840.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:48:35', '2025-11-14 13:29:59', NULL),
(111, 'AS113', 'Alusteel coil', 'IWhite', 16, 2226.00, 1505.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:49:18', '2025-11-14 13:32:05', NULL),
(112, 'AS114', 'Alusteel coil', 'GBeige', 14, 3426.00, 2264.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:50:11', '2025-11-14 13:32:46', NULL),
(113, 'AS115', 'Alusteel coil', 'GBeige', 14, 3368.00, 2242.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:50:48', '2025-11-14 13:33:20', NULL),
(114, 'AS119', 'Alusteel coil', 'GBeige', 14, 3036.00, 1993.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:51:41', '2025-11-14 13:34:03', NULL),
(115, 'AS121', 'Alusteel coil', 'GBeige', 14, 3114.00, 2046.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:52:45', '2025-11-14 13:35:55', NULL),
(116, 'AS122', 'Alusteel coil', 'TBlack', 12, 3496.00, 2320.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:53:41', '2025-11-14 13:36:37', NULL),
(117, 'AS126', 'Alusteel coil', 'TBlack', 12, 2800.00, 1843.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:54:49', '2025-11-14 13:37:23', NULL),
(118, 'AS130', 'Alusteel coil', 'BGreen', 15, 3044.00, 1999.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:55:42', '2025-11-14 13:38:26', NULL),
(119, 'AS131', 'Alusteel coil', 'BGreen', 15, 3084.00, 2034.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:56:36', '2025-11-14 13:39:10', NULL),
(120, 'AS133', 'Alusteel coil', 'BGreen', 15, 3016.00, 1992.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:57:31', '2025-11-14 13:41:18', NULL),
(121, 'AS134', 'Alusteel coil', 'BGreen', 15, 3504.00, 2321.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:58:25', '2025-11-14 13:41:47', NULL),
(122, 'AS138', 'Alusteel coil', 'BGreen', 15, 3366.00, 2223.00, '0.16', 'alusteel', 'available', 5, '2025-11-10 14:59:25', '2025-11-14 13:43:51', NULL),
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
(161, 'AS186', 'Alusteel coil', 'BGreen', 15, 2184.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:53:26', '2025-11-14 14:19:02', '2025-11-14 14:19:02'),
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
(176, 'AS204', 'Alusteel coil', 'TCRed', 13, 3166.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:34:11', '2025-11-14 14:35:27', '2025-11-14 14:35:27'),
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
(212, 'AS257', 'Alusteel coil', 'IBeige', 9, 3578.00, 1579.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:09:25', '2025-11-14 15:28:39', NULL),
(213, 'AS262', 'Alusteel coil', 'IBeige', 9, 3492.00, 1550.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:10:13', '2025-11-14 15:29:29', NULL),
(214, 'AS264', 'Alusteel coil', 'IBeige', 9, 3272.00, 1450.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:11:09', '2025-11-14 15:30:15', NULL),
(215, 'AS269', 'Alusteel coil', 'IBeige', 9, 3462.00, 1312.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:12:00', '2025-11-14 15:31:00', NULL),
(216, 'AS270', 'Alusteel coil', 'IBeige', 9, 3434.00, 1289.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:12:39', '2025-11-14 15:31:56', NULL),
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
(232, 'AS290', 'Alusteel coil', 'SBlue', 11, 3192.00, 1448.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:28:12', '2025-11-17 08:54:30', NULL),
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
(243, 'AS316', 'Alusteel coil', 'IBeige', 9, 3274.00, 1451.00, '0.24', 'alusteel', 'available', 5, '2025-11-11 09:38:23', '2025-11-17 09:02:56', NULL),
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
(275, 'AS358', 'Alusteel coil', 'TCRed', 13, 3380.00, 1649.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:09:20', '2025-11-17 09:31:45', NULL),
(276, 'AS359', 'Alusteel coil', 'TCRed', 13, 2734.00, 1329.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:10:32', '2025-11-17 09:32:45', NULL),
(277, 'AS360', 'Alusteel coil', 'TCRed', 13, 2746.00, 1334.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:11:20', '2025-11-17 09:33:25', NULL),
(278, 'AS361', 'Alusteel coil', 'TCRed', 13, 2844.00, 1396.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:11:55', '2025-11-17 09:33:56', NULL),
(279, 'AS362', 'Alusteel coil', 'SBlue', 11, 3172.00, 1535.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:12:41', '2025-11-17 09:34:29', NULL),
(280, 'AS363', 'Alusteel coil', 'SBlue', 11, 3200.00, 1551.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:13:28', '2025-11-17 09:35:03', NULL),
(281, 'AS364', 'Alusteel coil', 'SBlue', 11, 3162.00, 1535.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:14:13', '2025-11-17 09:35:40', NULL),
(282, 'AS365', 'Alusteel coil', 'SBlue', 11, 3142.00, 1532.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:15:52', '2025-11-17 09:36:29', NULL),
(283, 'AS366', 'Alusteel coil', 'SBlue', 11, 3248.00, 1583.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:16:32', '2025-11-17 09:37:03', NULL),
(284, 'AS367', 'Alusteel coil', 'IWhite', 16, 3246.00, 1560.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:17:14', '2025-11-17 09:38:56', NULL),
(285, 'AS368', 'Alusteel coil', 'IWhite', 16, 3276.00, 1576.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:18:03', '2025-11-17 09:39:27', NULL),
(286, 'AS369', 'Alusteel coil', 'IWhite', 16, 3360.00, 1630.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 10:18:50', '2025-12-03 09:00:04', NULL),
(287, 'AS370', 'Alusteel coil', 'IWhite', 16, 2714.00, 1319.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 10:19:40', '2025-12-03 09:00:10', NULL),
(288, 'AS371', 'Alusteel coil', 'IWhite', 16, 2722.00, 1321.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:20:24', '2025-11-17 09:41:23', NULL),
(289, 'AS372', 'Alusteel coil', 'IWhite', 16, 2840.00, 1397.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:21:07', '2025-11-17 09:41:55', NULL),
(290, 'AS373', 'Alusteel coil', 'IWhite', 16, 2968.00, 1432.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:21:47', '2025-11-17 09:42:37', NULL),
(291, 'AS374', 'Alusteel coil', 'IWhite', 16, 2936.00, 1418.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:22:32', '2025-11-17 09:43:17', NULL),
(292, 'AS375', 'Alusteel coil', 'IWhite', 16, 2976.00, 1445.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:23:53', '2025-11-17 09:43:57', NULL),
(293, 'AS376', 'Alusteel coil', 'GBeige', 14, 3440.00, 1676.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 10:24:54', '2025-11-17 09:44:41', NULL),
(294, 'AS377', 'Alusteel coil', 'GBeige', 14, 3302.00, 1612.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 13:31:06', '2025-11-17 09:45:13', NULL),
(295, 'AS378', 'Alusteel coil', 'GBeige', 14, 3436.00, 1665.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 13:31:50', '2025-11-17 09:46:04', NULL),
(296, 'AS379', 'Alusteel coil', 'GBeige', 14, 3310.00, 1601.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 13:32:23', '2025-11-17 09:46:42', NULL),
(297, 'AS380', 'Alusteel coil', 'GBeige', 14, 3286.00, 1599.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 13:33:10', '2025-11-17 09:47:30', NULL),
(298, 'AS381', 'Alusteel coil', 'GBeige', 14, 3098.00, 1509.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 13:34:05', '2025-11-17 09:48:42', NULL),
(299, 'AS382', 'Alusteel coil', 'BGreen', 15, 3134.00, 1514.00, '0.22', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:34:54', '2025-11-19 13:40:51', NULL),
(300, 'AS383', 'Alusteel coil', 'BGreen', 15, 3246.00, 1575.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 13:35:35', '2025-11-17 09:50:05', NULL),
(301, 'AS385', 'Alusteel coil', 'BGreen', 15, 3124.00, 1514.00, '0.22', 'alusteel', 'available', 5, '2025-11-11 13:36:24', '2025-11-17 09:51:00', NULL),
(302, 'AS387', 'Alusteel coil', 'TCRed', 13, 3198.00, 1312.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:38:00', '2025-11-17 10:20:25', NULL),
(303, 'AS388', 'Alusteel coil', 'TCRed', 13, 3190.00, 1308.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:38:48', '2025-11-17 10:19:42', NULL),
(304, 'AS389', 'Alusteel coil', 'TCRed', 13, 3312.00, 1360.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:39:32', '2025-11-17 10:21:04', NULL),
(305, 'AS390', 'Alusteel coil', 'TCRed', 13, 2910.00, 1194.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:40:11', '2025-11-17 10:23:28', NULL),
(306, 'AS391', 'Alusteel coil', 'TCRed', 13, 2978.00, 1222.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:40:49', '2025-11-17 10:24:01', NULL),
(307, 'AS392', 'Alusteel coil', 'IWhite', 16, 3202.00, 1313.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:41:35', '2025-11-17 10:24:30', NULL),
(308, 'AS393', 'Alusteel coil', 'IWhite', 16, 3202.00, 1310.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:42:23', '2025-11-17 10:25:10', NULL),
(309, 'AS394', 'Alusteel coil', 'IWhite', 16, 3350.00, 1374.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:43:02', '2025-11-17 10:25:38', NULL),
(310, 'AS395', 'Alusteel coil', 'IWhite', 16, 3202.00, 1312.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:44:28', '2025-11-17 10:26:11', NULL),
(311, 'AS396', 'Alusteel coil', 'IWhite', 16, 3208.00, 1310.00, '0.26', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:45:17', '2025-12-03 09:00:16', NULL),
(312, 'AS397', 'Alusteel coil', 'IWhite', 16, 3364.00, 1379.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:46:01', '2025-11-17 10:27:26', NULL),
(313, 'AS398', 'Alusteel coil', 'IWhite', 16, 2182.00, 889.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:46:46', '2025-11-17 10:27:58', NULL),
(314, 'AS399', 'Alusteel coil', 'IWhite', 16, 2316.00, 932.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:47:23', '2025-11-17 10:28:29', NULL),
(315, 'AS400', 'Alusteel coil', 'GBeige', 14, 3204.00, 1313.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:48:55', '2025-11-17 10:29:02', NULL),
(316, 'AS401', 'Alusteel coil', 'GBeige', 14, 3202.00, 1312.00, '0.26', 'alusteel', 'out_of_stock', 5, '2025-11-11 13:49:47', '2025-12-03 09:00:21', NULL),
(317, 'AS402', 'Alusteel coil', 'GBeige', 14, 3428.00, 1380.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:50:34', '2025-11-17 10:30:05', NULL),
(318, 'AS403', 'Alusteel coil', 'GBeige', 14, 2994.00, 1229.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:51:15', '2025-11-17 10:30:41', NULL),
(319, 'AS404', 'Alusteel coil', 'GBeige', 14, 3196.00, 1319.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:51:50', '2025-11-17 10:47:20', NULL),
(320, 'AS405', 'Alusteel coil', 'GBeige', 14, 3206.00, 1317.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:52:43', '2025-11-17 10:31:23', NULL),
(321, 'AS406', 'Alusteel coil', 'GBeige', 14, 3366.00, 1386.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:53:21', '2025-11-17 10:32:39', NULL),
(322, 'AS407', 'Alusteel coil', 'BGreen', 15, 3242.00, 1324.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:54:07', '2025-11-17 10:33:18', NULL),
(323, 'AS408', 'Alusteel coil', 'BGreen', 15, 2998.00, 1220.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:54:43', '2025-11-17 10:33:45', NULL),
(324, 'AS409', 'Alusteel coil', 'BGreen', 15, 3618.00, 1475.00, '0.26', 'alusteel', 'available', 5, '2025-11-11 13:55:24', '2025-11-17 10:34:26', NULL),
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
(568, 'AS227', 'Alusteel coil', '', 18, 3474.00, 2322.00, '0.16', 'alusteel', 'available', 5, '2025-11-14 12:11:45', '2025-11-14 12:12:23', NULL),
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
(581, 'A6', 'Aluminium coil', '', 14, 1899.00, 1225.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:26:50', NULL, NULL),
(582, 'A7', 'Aluminium coil', '', 14, 1849.00, 1272.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:27:42', NULL, NULL),
(583, 'A8', 'Aluminium coil', '', 13, 1772.00, 1211.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:28:29', NULL, NULL),
(584, 'A9', 'Aluminium coil', '', 13, 1777.00, 1215.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:29:12', NULL, NULL),
(585, 'A10', 'Aluminium coil', '', 13, 1911.00, 0.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:29:46', NULL, NULL),
(586, 'A11', 'Aluminium coil', '', 12, 1898.00, 1220.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:30:30', NULL, NULL),
(587, 'A12', 'Aluminium coil', '', 15, 1823.00, 1255.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:31:21', NULL, NULL),
(588, 'A13', 'Aluminium coil', '', 15, 1870.00, 0.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:31:59', NULL, NULL),
(589, 'A14', 'Aluminium coil', '', 15, 1870.00, 1195.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:32:53', NULL, NULL),
(590, 'A15', 'Aluminium coil', '', 15, 1817.00, 1160.00, '0.5', 'aluminum', 'available', 5, '2025-11-25 12:33:41', NULL, NULL),
(591, 'A16', 'Aluminium coil', '', 9, 1844.00, 1178.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:34:34', NULL, NULL),
(592, 'A17', 'Aluminium coil', '', 9, 1811.00, 1158.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:35:23', NULL, NULL),
(593, 'A18', 'Aluminium coil', '', 9, 1792.00, 1220.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:36:02', NULL, NULL),
(594, 'A19', 'Aluminium coil', '', 14, 1910.00, 1232.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:36:57', NULL, NULL),
(595, 'A20', 'Aluminium coil', '', 14, 1945.00, 1330.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:37:38', NULL, NULL),
(596, 'A21', 'Aluminium coil', '', 14, 1853.00, 1275.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:38:33', NULL, NULL),
(597, 'A22', 'Aluminium coil', '', 13, 1770.00, 1211.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:39:46', NULL, NULL),
(598, 'A23', 'Aluminium coil', '', 13, 1923.00, 1225.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:40:27', NULL, NULL),
(599, 'A24', 'Aluminium coil', '', 13, 1728.00, 1108.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:41:10', NULL, NULL),
(600, 'A25', 'Aluminium coil', '', 12, 1897.00, 1220.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:42:02', NULL, NULL),
(601, 'A26', 'Aluminium coil', '', 12, 1903.00, 1222.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:42:49', NULL, NULL),
(602, 'A27', 'Aluminium coil', '', 15, 1813.00, 1158.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:43:33', NULL, NULL),
(603, 'A28', 'Aluminium coil', '', 11, 1768.00, 1214.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:44:20', NULL, NULL),
(604, 'A29', 'Aluminium coil', '', 11, 1776.00, 1220.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 12:44:59', NULL, NULL),
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
(629, 'A54', 'Aluminium coil', '', 13, 2085.00, 1747.00, '0.55', 'kzinc', 'available', 5, '2025-11-25 13:48:09', NULL, NULL),
(630, 'A55', 'Aluminium coil', '', 13, 2080.00, 1745.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:48:58', NULL, NULL),
(631, 'A56', 'Aluminium coil', '', 13, 2079.00, 2225.00, '0.55', 'aluminum', 'available', 5, '2025-11-25 13:49:39', NULL, NULL),
(632, 'A57', 'Aluminium coil', '', 13, 1914.00, 2054.00, '0.55', 'aluminum', 'out_of_stock', 5, '2025-11-25 13:50:21', '2025-12-03 08:59:58', NULL),
(633, 'D33', 'Aluminium coil', '', 12, 2147.00, 1417.90, '0.55', 'aluminum', 'available', 5, '2025-12-03 09:11:39', NULL, NULL);

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
(4, 'Usman Madugu', 'omale.ochigbo@obumek360.app', '07071132619', 'Kaduna State', 'Usman Aluminium Ltd', 5, '2025-11-17 12:31:01', '2025-11-17 14:17:29', NULL),
(5, 'Adams', 'adamjames@yahoo.com', '08055084883', 'Kubwa Abuja', 'Adams Aluminium Ltd', 5, '2025-11-19 08:34:00', NULL, NULL),
(6, 'Elizabeth Enehe', NULL, '08098434014', NULL, NULL, 5, '2025-12-03 08:50:50', NULL, NULL),
(7, 'MONDAY JOB', NULL, '08098434014', NULL, NULL, 5, '2025-12-03 09:14:01', NULL, NULL);

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
(37, 51, 'coil_sale', 51, NULL, 'INV-2025-000003', '\"{\\\"company\\\":{\\\"name\\\":\\\"Obumek Alluminium Company Ltd.\\\",\\\"address\\\":\\\"Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja\\\",\\\"phone\\\":\\\"+2348065336645\\\",\\\"email\\\":\\\"info@obumekalluminium.com\\\"},\\\"customer\\\":{\\\"name\\\":\\\"Adams\\\",\\\"company\\\":\\\"Adams Aluminium Ltd\\\",\\\"email\\\":\\\"adamjames@yahoo.com\\\",\\\"phone\\\":\\\"08055084883\\\",\\\"address\\\":\\\"Kubwa Abuja\\\"},\\\"meta\\\":{\\\"date\\\":\\\"2025-11-19 13:23:23\\\",\\\"ref\\\":\\\"#SO-20251119-000051\\\",\\\"sale_id\\\":\\\"51\\\",\\\"payment_status\\\":\\\"Unpaid\\\"},\\\"items\\\":[{\\\"description\\\":\\\"AS369 - Alusteel coil\\\",\\\"quantity\\\":6000,\\\"qty_text\\\":\\\"6,000.00 meters\\\",\\\"unit_price\\\":10800,\\\"subtotal\\\":64800000}],\\\"subtotal\\\":64800000,\\\"order_tax\\\":4860000,\\\"discount\\\":0,\\\"shipping\\\":0,\\\"grand_total\\\":69660000,\\\"paid\\\":0,\\\"due\\\":69660000,\\\"notes\\\":{\\\"receipt_statement\\\":\\\"Received the above goods in good condition.\\\",\\\"refund_policy\\\":\\\"No refund of money after payment\\\",\\\"custom_notes\\\":\\\"\\\"},\\\"signatures\\\":{\\\"customer\\\":null,\\\"for_company\\\":\\\"Obumek Alluminium Company Ltd.\\\"}}\"', 0.00, 'fixed', 0.00, 0.00, 'fixed', 0.00, 0.00, 69660000.00, 4860000.00, 0.00, 0.00, 0.00, 'unpaid', '2025-11-19 13:23:23', '2025-11-28 20:11:53', '1f2fb187423386cbd8835bea12e071429905c8168c603daff928581c185765ca');

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
(3, 36, 60000.00, '', 'cash', 5, '2025-11-18 11:47:09');

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
(51, 5, 286, 38, 'available_stock', 6000.00, NULL, 10800.00, NULL, 64800000.00, 'completed', 5, NULL, '2025-11-19 13:23:23', NULL, NULL);

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
(42, 70, 1990.00, 1990.00, 2053.00, 2053.00, 0.00, 'available', 5, '2025-12-03 08:49:43', NULL, NULL),
(43, 633, 1417.90, 1417.90, NULL, NULL, 0.00, 'factory_use', 5, '2025-12-03 09:12:33', '2025-12-03 09:12:41', NULL);

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
(41, 633, 43, 'inflow', 'Stock moved to factory use - Entry #43 (1417.90m available)', 1417.90, 0.00, 1417.90, 'stock_entry', 43, 5, '2025-12-03 09:12:41');

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
(3, 2, '2025-12-02 13:40:39', '', 2458.00, 0.00, 2458.00, 'stock_in', NULL, 'sales black milano', 5, '2025-12-02 13:40:39');

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
(1, 'admin@example.com', '$2y$10$RBop9EOLBg.Vo9AKPBp.xOnx/18TGybfpkmU.//PgdAwLFSXr3GZ6', 'SHEMAIAH WAMBEBE YABA-SHIAKA', 'super_admin', '2025-11-05 08:37:50', '2025-11-28 20:15:07', '2025-11-28 20:14:58'),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=634;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `designs`
--
ALTER TABLE `designs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `production`
--
ALTER TABLE `production`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `stock_entries`
--
ALTER TABLE `stock_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

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
-- AUTO_INCREMENT for table `tile_products`
--
ALTER TABLE `tile_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tile_sales`
--
ALTER TABLE `tile_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tile_stock_ledger`
--
ALTER TABLE `tile_stock_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
