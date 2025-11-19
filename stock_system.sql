-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2025 at 08:03 AM
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
(49, 'D-35', 'I/beige Aluminium coil', 'IBeige', 9, 2850.00, NULL, NULL, 'aluminum', 'available', 2, '2025-11-08 11:37:39', '2025-11-12 06:56:10', NULL),
(50, 'D-212', 'I/ beige Aluminiun coil', 'IBeige', 9, 2650.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-08 12:25:04', '2025-11-12 06:56:10', NULL),
(51, 'D-218', 'TC/Red Aluninium coil', 'TCRed', 13, 2500.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-08 12:44:47', '2025-11-12 06:56:10', NULL),
(52, 'AS-190', 'AS190 Alusteel coil', 'BGreen', 15, 2500.00, NULL, NULL, 'alusteel', 'out_of_stock', 5, '2025-11-08 12:53:21', '2025-11-12 06:56:10', NULL),
(54, 'A333', 'I/white Aluminium coil', 'IWhite', 16, 2199.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:29:56', '2025-11-12 06:56:10', NULL),
(55, 'A352', 'S/ blue Aluminium coil', 'SBlue', 11, 2059.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:31:19', '2025-11-12 06:56:10', NULL),
(56, 'B89', 'I/white Aluminium coil', 'IWhite', 16, 2316.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:32:53', '2025-11-12 06:56:10', NULL),
(57, 'C3', 'G/beige Aluminium coil', 'GBeige', 14, 2048.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:34:39', '2025-11-12 06:56:10', NULL),
(58, 'C5', 'G/beige Aluminium coil', 'GBeige', 14, 2048.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:36:15', '2025-11-12 06:56:10', NULL),
(59, 'C20', 'G/beige Aluminium coil', 'GBeige', 14, 2072.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:37:10', '2025-11-12 06:56:10', NULL),
(60, 'C44', 'G/beige Aluminium coil', 'GBeige', 14, 2060.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:37:58', '2025-11-12 06:56:10', NULL),
(61, 'C57', 'G/beige Aluminium coil', 'GBeige', 14, 2061.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:38:48', '2025-11-12 06:56:10', NULL),
(62, 'C71', 'G/beige Aluminium coil', 'GBeige', 14, 2050.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:39:52', '2025-11-12 06:56:10', NULL),
(63, 'C98', 'G/beige Aluminium coil', 'GBeige', 14, 2046.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:40:32', '2025-11-12 06:56:10', NULL),
(64, 'D13', 'S/blue Aluminium coil', 'SBlue', 11, 1763.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:41:52', '2025-11-12 06:56:10', NULL),
(65, 'D14', 'S/blue Aluminium coil', 'SBlue', 11, 1375.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:42:43', '2025-11-12 06:56:10', NULL),
(66, 'D29', 'S/blue Aluminium coil', 'SBlue', 11, 1375.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:44:06', '2025-11-12 06:56:10', NULL),
(67, 'D31', 'I/beige Aluminium coil', 'IBeige', 9, 2073.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:45:50', '2025-11-12 06:56:10', NULL),
(68, 'D54', 'G/beige Aluminium coil', 'GBeige', 14, 2055.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:46:46', '2025-11-12 06:56:10', NULL),
(69, 'D57', 'I/beige Aluminium coil', 'IBeige', 9, 2161.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:48:03', '2025-11-12 06:56:10', NULL),
(70, 'D60', 'G/beige Aluminium coil', 'GBeige', 14, 2053.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:49:05', '2025-11-12 06:56:10', NULL),
(71, 'C30', 'G/beige Aluminium coil', 'GBeige', 14, 2064.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:50:02', '2025-11-12 06:56:10', NULL),
(72, 'D99', 'G/beige Aluminium coil', 'GBeige', 14, 2140.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:51:59', '2025-11-12 06:56:10', NULL),
(73, 'D101', 'G/beige Aluminium coil', 'GBeige', 14, 2137.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:52:46', '2025-11-12 06:56:10', NULL),
(74, 'D102', 'G/beige Aluminium coil', 'GBeige', 14, 2142.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:53:32', '2025-11-12 06:56:10', NULL),
(75, 'D109', 'I/beige Aluminium coil', 'IBeige', 9, 1856.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:54:37', '2025-11-12 06:56:10', NULL),
(76, 'D111', 'I/beige Aluminium coil', 'IBeige', 9, 2179.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:55:19', '2025-11-12 06:56:10', NULL),
(77, 'D116', 'I/beige Aluminium coil', 'IBeige', 9, 1856.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:56:03', '2025-11-12 06:56:10', NULL),
(78, 'D162', 'T/black Aluminium coil', 'TBlack', 12, 1798.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 13:58:03', '2025-11-12 06:56:10', NULL),
(79, 'D192', 'T/black Aluminium coil', 'TBlack', 12, 1790.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 14:02:32', '2025-11-12 06:56:10', NULL),
(80, 'D214', 'P/green Aluminium coil', 'PGreen', 10, 1952.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 14:04:39', '2025-11-12 06:56:10', NULL),
(81, 'D215', 'S/blue Aluminium coil', 'SBlue', 11, 1587.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 14:05:31', '2025-11-12 06:56:10', NULL),
(82, 'D235', 'G/beige Aluminium coil', 'GBeige', 14, 1848.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 14:06:25', '2025-11-12 06:56:10', NULL),
(83, 'D236', 'G/beige Aluminium coil', 'GBeige', 14, 1863.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 14:07:14', '2025-11-12 06:56:10', NULL),
(84, 'D239', 'B/green Aluminium coil', 'BGreen', 15, 1745.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 14:08:22', '2025-11-12 06:56:10', NULL),
(85, 'D246', 'S/blue Aluminium coil', 'SBlue', 11, 2025.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 14:09:19', '2025-11-12 06:56:10', NULL),
(86, 'D261', 'P/green Aluminium coil', 'PGreen', 10, 1977.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 14:09:59', '2025-11-12 06:56:10', NULL),
(87, 'D269', 'S/blue Aluminium coil', 'SBlue', 11, 2037.00, NULL, NULL, 'aluminum', 'available', 5, '2025-11-10 14:10:45', '2025-11-12 06:56:10', NULL),
(88, 'AS50', 'AS50 Alusteel coil', 'SBlue', 11, 3584.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:23:51', '2025-11-12 06:56:10', NULL),
(89, 'AS74', 'Alusteel coil', 'TBlack', 12, 3510.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:26:28', '2025-11-12 06:56:10', NULL),
(90, 'AS75', 'Alusteel coil', 'TBlack', 12, 3584.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:27:57', '2025-11-12 06:56:10', NULL),
(91, 'AS76', 'Alusteel coil', 'TBlack', 12, 3534.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:28:50', '2025-11-12 06:56:10', NULL),
(92, 'AS193', 'Alusteel coil', 'TBlack', 12, 3376.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:30:05', '2025-11-12 06:56:10', NULL),
(93, 'AS198', 'Alusteel coil', 'SBlue', 11, 3146.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:33:21', '2025-11-12 06:56:10', NULL),
(94, 'AS199', 'Alusteel coil', 'SBlue', 11, 2266.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:34:02', '2025-11-12 06:56:10', NULL),
(95, 'AS200', 'Alusteel coil', 'SBlue', 11, 3302.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:34:57', '2025-11-12 06:56:10', NULL),
(96, 'AS230', 'Alusteel coil', 'TBlack', 12, 3510.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:35:43', '2025-11-12 06:56:10', NULL),
(97, 'AS89', 'Alusteel coil', 'SBlue', 11, 3440.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:37:00', '2025-11-12 06:56:10', NULL),
(98, 'AS91', 'Alusteel coil', 'SBlue', 11, 3511.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:37:53', '2025-11-12 06:56:10', NULL),
(99, 'AS92', 'Alusteel coil', 'SBlue', 11, 3598.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:39:14', '2025-11-12 06:56:10', NULL),
(100, 'AS93', 'Alusteel coil', 'SBlue', 11, 3222.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:39:56', '2025-11-12 06:56:10', NULL),
(101, 'AS94', 'Alusteel coil', 'SBlue', 11, 3000.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:41:01', '2025-11-12 06:56:10', NULL),
(102, 'AS95', 'Alusteel coil', 'SBlue', 11, 3034.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:41:56', '2025-11-12 06:56:10', NULL),
(103, 'AS96', 'Alusteel coil', 'SBlue', 11, 2342.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:42:41', '2025-11-12 06:56:10', NULL),
(104, 'AS104', 'Alusteel coil', 'SBlue', 11, 3602.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:43:33', '2025-11-12 06:56:10', NULL),
(105, 'AS105', 'Alusteel coil', 'SBlue', 11, 3212.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:44:12', '2025-11-12 06:56:10', NULL),
(106, 'AS106', 'Alusteel coil', 'IWhite', 16, 3196.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:45:25', '2025-11-12 06:56:10', NULL),
(107, 'AS108', 'Alusteel coil', 'IWhite', 16, 3412.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:46:06', '2025-11-12 06:56:10', NULL),
(108, 'AS110', 'Alusteel coil', 'IWhite', 16, 3656.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:47:05', '2025-11-12 06:56:10', NULL),
(109, 'AS111', 'Alusteel coil', 'IWhite', 16, 3322.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:47:42', '2025-11-12 06:56:10', NULL),
(110, 'AS112', 'Alusteel coil', 'IWhite', 16, 2764.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:48:35', '2025-11-12 06:56:10', NULL),
(111, 'AS113', 'Alusteel coil', 'IWhite', 16, 2226.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:49:18', '2025-11-12 06:56:10', NULL),
(112, 'AS114', 'Alusteel coil', 'GBeige', 14, 3426.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:50:11', '2025-11-12 06:56:10', NULL),
(113, 'AS115', 'Alusteel coil', 'GBeige', 14, 3368.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:50:48', '2025-11-12 06:56:10', NULL),
(114, 'AS119', 'Alusteel coil', 'GBeige', 14, 3036.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:51:41', '2025-11-12 06:56:10', NULL),
(115, 'AS121', 'Alusteel coil', 'GBeige', 14, 3114.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:52:45', '2025-11-12 06:56:10', NULL),
(116, 'AS122', 'Alusteel coil', 'TBlack', 12, 3496.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:53:41', '2025-11-12 06:56:10', NULL),
(117, 'AS126', 'Alusteel coil', 'TBlack', 12, 2800.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:54:49', '2025-11-12 06:56:10', NULL),
(118, 'AS130', 'Alusteel coil', 'BGreen', 15, 3044.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:55:42', '2025-11-12 06:56:10', NULL),
(119, 'AS131', 'Alusteel coil', 'BGreen', 15, 3084.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:56:36', '2025-11-12 06:56:10', NULL),
(120, 'AS133', 'Alusteel coil', 'BGreen', 15, 3016.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:57:31', '2025-11-12 06:56:10', NULL),
(121, 'AS134', 'Alusteel coil', 'BGreen', 15, 3504.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:58:25', '2025-11-12 06:56:10', NULL),
(122, 'AS138', 'Alusteel coil', 'BGreen', 15, 3370.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 14:59:25', '2025-11-12 06:56:10', NULL),
(123, 'AS136', 'Alusteel coil', 'BGreen', 15, 3444.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:00:17', '2025-11-12 06:56:10', NULL),
(124, 'AS137', 'Alusteel coil', 'BGreen', 15, 3466.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:01:11', '2025-11-12 06:56:10', NULL),
(125, 'AS139', 'Alusteel coil', 'TCRed', 13, 3022.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:05:24', '2025-11-12 06:56:10', NULL),
(126, 'AS140', 'Alusteel coil', 'TCRed', 13, 2940.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:06:11', '2025-11-12 06:56:10', NULL),
(127, 'AS142', 'Alusteel coil', 'TCRed', 13, 3100.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:06:59', '2025-11-12 06:56:10', NULL),
(128, 'AS143', 'Alusteel coil', 'TCRed', 13, 2994.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:07:41', '2025-11-12 06:56:10', NULL),
(129, 'AS144', 'Alusteel coil', 'TCRed', 13, 2910.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:08:37', '2025-11-12 06:56:10', NULL),
(130, 'AS145', 'Alusteel coil', 'TCRed', 13, 3574.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:09:17', '2025-11-12 06:56:10', NULL),
(131, 'AS146', 'Alusteel coil', 'TCRed', 13, 3344.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:09:59', '2025-11-12 06:56:10', NULL),
(132, 'AS157', 'Alusteel coil', 'SBlue', 11, 3372.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:11:20', '2025-11-12 06:56:10', NULL),
(133, 'AS158', 'Alusteel coil', 'SBlue', 11, 3344.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:12:11', '2025-11-12 06:56:10', NULL),
(134, 'AS159', 'Alusteel coil', 'BGreen', 15, 2806.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:13:09', '2025-11-12 06:56:10', NULL),
(135, 'AS160', 'Alusteel coil', 'BGreen', 15, 2216.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:13:52', '2025-11-12 06:56:10', NULL),
(136, 'AS161', 'Alusteel coil', 'BGreen', 15, 2104.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:16:17', '2025-11-12 06:56:10', NULL),
(137, 'AS162', 'Alusteel coil', 'BGreen', 15, 2142.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:16:51', '2025-11-12 06:56:10', NULL),
(138, 'AS163', 'Alusteel coil', 'BGreen', 15, 2146.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:17:29', '2025-11-12 06:56:10', NULL),
(139, 'AS164', 'Alusteel coil', 'BGreen', 15, 2166.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:18:18', '2025-11-12 06:56:10', NULL),
(140, 'AS165', 'Alusteel coil', 'BGreen', 15, 2122.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:19:02', '2025-11-12 06:56:10', NULL),
(141, 'AS166', 'Alusteel coil', 'BGreen', 15, 2074.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:19:42', '2025-11-12 06:56:10', NULL),
(142, 'AS167', 'Alusteel coil', 'BGreen', 15, 2098.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:20:44', '2025-11-12 06:56:10', NULL),
(143, 'AS168', 'Alusteel coil', 'BGreen', 15, 2772.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:21:22', '2025-11-12 06:56:10', NULL),
(144, 'AS169', 'Alusteel coil', 'BGreen', 15, 2774.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:22:13', '2025-11-12 06:56:10', NULL),
(145, 'AS170', 'Alusteel coil', 'BGreen', 15, 2774.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:23:00', '2025-11-12 06:56:10', NULL),
(146, 'AS171', 'Alusteel coil', 'BGreen', 15, 2780.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:23:44', '2025-11-12 06:56:10', NULL),
(147, 'AS172', 'Alusteel coil', 'BGreen', 15, 2740.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:24:23', '2025-11-12 06:56:10', NULL),
(148, 'AS173', 'Alusteel coil', 'BGreen', 15, 2840.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:25:01', '2025-11-12 06:56:10', NULL),
(149, 'AS174', 'Alusteel coil', 'BGreen', 15, 2840.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:25:35', '2025-11-12 06:56:10', NULL),
(150, 'AS175', 'Alusteel coil', 'BGreen', 15, 2172.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:26:10', '2025-11-12 06:56:10', NULL),
(151, 'AS176', 'Alusteel coil', 'BGreen', 15, 2814.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:26:49', '2025-11-12 06:56:10', NULL),
(152, 'AS177', 'Alusteel coil', 'BGreen', 15, 2144.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-10 15:27:31', '2025-11-12 06:56:10', NULL),
(153, 'AS178', 'Alusteel coil', 'BGreen', 15, 2158.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:47:33', '2025-11-12 06:56:10', NULL),
(154, 'AS179', 'Alusteel coil', 'BGreen', 15, 2124.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:48:27', '2025-11-12 06:56:10', NULL),
(155, 'AS180', 'Alusteel coil', 'BGreen', 15, 2610.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:49:11', '2025-11-12 06:56:10', NULL),
(156, 'AS181', 'Alusteel coil', 'BGreen', 15, 2608.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:50:04', '2025-11-12 06:56:10', NULL),
(157, 'AS182', 'Alusteel coil', 'BGreen', 15, 3142.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:50:44', '2025-11-12 06:56:10', NULL),
(158, 'AS183', 'Alusteel coil', 'BGreen', 15, 2790.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:51:29', '2025-11-12 06:56:10', NULL),
(159, 'AS184', 'Alusteel coil', 'BGreen', 15, 2782.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:52:14', '2025-11-12 06:56:10', NULL),
(160, 'AS185', 'Alusteel coil', 'BGreen', 15, 2788.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:52:51', '2025-11-12 06:56:10', NULL),
(161, 'AS186', 'Alusteel coil', 'BGreen', 15, 2184.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:53:26', '2025-11-12 06:56:10', NULL),
(162, 'AS187', 'Alusteel coil', 'BGreen', 15, 2120.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:54:06', '2025-11-12 06:56:10', NULL),
(163, 'AS188', 'Alusteel coil', 'BGreen', 15, 2188.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:54:58', '2025-11-12 06:56:10', NULL),
(164, 'AS189', 'Alusteel coil', 'BGreen', 15, 2200.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:56:31', '2025-11-12 06:56:10', NULL),
(166, 'AS191', 'Alusteel coil', 'BGreen', 15, 2818.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 07:58:37', '2025-11-12 06:56:10', NULL),
(168, 'AS192', 'Alusteel coil', 'BGreen', 15, 2832.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:01:14', '2025-11-12 06:56:10', NULL),
(170, 'AS194', 'Alusteel coil', 'BGreen', 15, 2774.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:24:01', '2025-11-12 06:56:10', NULL),
(171, 'AS196', 'Alusteel coil', 'BGreen', 15, 2744.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:25:31', '2025-11-12 06:56:10', NULL),
(172, 'AS197', 'Alusteel coil', 'BGreen', 15, 2760.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:26:17', '2025-11-12 06:56:10', NULL),
(173, 'AS201', 'Alusteel coil', 'TCRed', 13, 3410.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:31:36', '2025-11-12 06:56:10', NULL),
(174, 'AS202', 'Alusteel coil', 'TCRed', 13, 3360.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:32:34', '2025-11-12 06:56:10', NULL),
(175, 'AS203', 'Alusteel coil', 'TCRed', 13, 3186.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:33:25', '2025-11-12 06:56:10', NULL),
(176, 'AS204', 'Alusteel coil', 'TCRed', 13, 3166.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:34:11', '2025-11-12 06:56:10', NULL),
(177, 'AS205', 'Alusteel coil', 'SBlue', 11, 3412.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:34:58', '2025-11-12 06:56:10', NULL),
(178, 'AS206', 'Alusteel coil', 'SBlue', 11, 3438.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:35:40', '2025-11-12 06:56:10', NULL),
(179, 'AS207', 'Alusteel coil', 'SBlue', 11, 3428.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:36:29', '2025-11-12 06:56:10', NULL),
(180, 'AS208', 'Alusteel coil', 'SBlue', 11, 3298.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:37:05', '2025-11-12 06:56:10', NULL),
(181, 'AS209', 'Alusteel coil', 'SBlue', 11, 3296.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:37:47', '2025-11-12 06:56:10', NULL),
(182, 'AS210', 'Alusteel coil', 'SBlue', 11, 3392.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:40:47', '2025-11-12 06:56:10', NULL),
(183, 'AS211', 'Alusteel coil', 'BGreen', 15, 3338.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:41:27', '2025-11-12 06:56:10', NULL),
(184, 'AS212', 'Alusteel coil', 'BGreen', 15, 3430.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:42:10', '2025-11-12 06:56:10', NULL),
(186, 'AS214', 'Alusteel coil', 'BGreen', 15, 3344.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:43:55', '2025-11-12 06:56:10', NULL),
(187, 'AS215', 'Alusteel coil', 'BGreen', 15, 3560.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:44:51', '2025-11-12 06:56:10', NULL),
(188, 'AS216', 'Alusteel coil', 'BGreen', 15, 3312.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:45:33', '2025-11-12 06:56:10', NULL),
(189, 'AS217', 'Alusteel coil', 'BGreen', 15, 3564.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:46:09', '2025-11-12 06:56:10', NULL),
(190, 'AS218', 'Alusteel coil', 'TBlack', 12, 3316.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:46:48', '2025-11-12 06:56:10', NULL),
(191, 'AS219', 'Alusteel coil', 'IBeige', 9, 3378.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:51:27', '2025-11-12 06:56:10', NULL),
(192, 'AS221', 'Alusteel coil', 'IBeige', 9, 3146.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:52:22', '2025-11-12 06:56:10', NULL),
(193, 'AS222', 'Alusteel coil', 'IBeige', 9, 3150.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:53:06', '2025-11-12 06:56:10', NULL),
(194, 'AS229', 'Alusteel coil', 'TBlack', 12, 3266.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:54:30', '2025-11-12 06:56:10', NULL),
(195, 'AS234', 'Alusteel coil', 'PGreen', 10, 2608.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:56:29', '2025-11-12 06:56:10', NULL),
(196, 'AS235', 'Alusteel coil', 'TBlack', 12, 3254.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:57:15', '2025-11-12 06:56:10', NULL),
(197, 'AS236', 'Alusteel coil', 'GBeige', 14, 3314.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:57:55', '2025-11-12 06:56:10', NULL),
(198, 'AS237', 'Alusteel coil', 'GBeige', 14, 3328.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:58:40', '2025-11-12 06:56:10', NULL),
(199, 'AS238', 'Alusteel coil', 'GBeige', 14, 3374.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 08:59:26', '2025-11-12 06:56:10', NULL),
(200, 'AS239', 'Alusteel coil', 'TBlack', 12, 3332.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:00:08', '2025-11-12 06:56:10', NULL),
(201, 'AS240', 'Alusteel coil', 'TBlack', 12, 3336.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:00:48', '2025-11-12 06:56:10', NULL),
(202, 'AS242', 'Alusteel coil', 'PGreen', 10, 3050.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:01:29', '2025-11-12 06:56:10', NULL),
(203, 'AS243', 'Alusteel coil', 'PGreen', 10, 3890.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:02:13', '2025-11-12 06:56:10', NULL),
(204, 'AS244', 'Alusteel coil', 'IBeige', 9, 3202.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:02:55', '2025-11-12 06:56:10', NULL),
(205, 'AS245', 'Alusteel coil', 'BGreen', 15, 3184.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:03:40', '2025-11-12 06:56:10', NULL),
(206, 'AS246', 'Alusteel coil', 'PGreen', 10, 3478.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:04:22', '2025-11-12 06:56:10', NULL),
(207, 'AS247', 'Alusteel coil', 'IBeige', 9, 3554.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:05:00', '2025-11-12 06:56:10', NULL),
(208, 'AS248', 'Alusteel coil', 'IBeige', 9, 3560.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:05:46', '2025-11-12 06:56:10', NULL),
(209, 'AS249', 'Alusteel coil', 'IBeige', 9, 3600.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:06:44', '2025-11-12 06:56:10', NULL),
(210, 'AS250', 'Alusteel coil', 'IBeige', 9, 3186.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:07:35', '2025-11-12 06:56:10', NULL),
(211, 'AS251', 'Alusteel coil', 'IBeige', 9, 3174.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:08:25', '2025-11-12 06:56:10', NULL),
(212, 'AS257', 'Alusteel coil', 'IBeige', 9, 3578.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:09:25', '2025-11-12 06:56:10', NULL),
(213, 'AS262', 'Alusteel coil', 'IBeige', 9, 3492.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:10:13', '2025-11-12 06:56:10', NULL),
(214, 'AS264', 'Alusteel coil', 'IBeige', 9, 3272.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:11:09', '2025-11-12 06:56:10', NULL),
(215, 'AS269', 'Alusteel coil', 'IBeige', 9, 3462.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:12:00', '2025-11-12 06:56:10', NULL),
(216, 'AS270', 'Alusteel coil', 'IBeige', 9, 3434.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:12:39', '2025-11-12 06:56:10', NULL),
(217, 'AS271', 'Alusteel coil', 'IBeige', 9, 3152.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:13:28', '2025-11-12 06:56:10', NULL),
(218, 'AS272', 'Alusteel coil', 'IBeige', 9, 2582.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:14:09', '2025-11-12 06:56:10', NULL),
(219, 'AS273', 'Alusteel coil', 'BGreen', 15, 2572.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:16:58', '2025-11-12 06:56:10', NULL),
(220, 'AS275', 'Alusteel coil', 'BGreen', 15, 3178.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:17:54', '2025-11-12 06:56:10', NULL),
(221, 'AS276', 'Alusteel coil', 'BGreen', 15, 3190.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:18:34', '2025-11-12 06:56:10', NULL),
(222, 'AS277', 'Alusteel coil', 'PGreen', 10, 2876.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:19:23', '2025-11-12 06:56:10', NULL),
(223, 'AS278', 'Alusteel coil', 'BGreen', 15, 3150.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:20:06', '2025-11-12 06:56:10', NULL),
(224, 'AS279', 'Alusteel coil', 'BGreen', 15, 3160.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:20:57', '2025-11-12 06:56:10', NULL),
(225, 'AS280', 'Alusteel coil', 'IBeige', 9, 3226.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:21:44', '2025-11-12 06:56:10', NULL),
(226, 'AS282', 'Alusteel coil', 'IBeige', 9, 2558.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:22:33', '2025-11-12 06:56:10', NULL),
(227, 'AS283', 'Alusteel coil', 'BGreen', 15, 3182.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:23:26', '2025-11-12 06:56:10', NULL),
(228, 'AS284', 'Alusteel coil', 'IBeige', 9, 3532.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:24:22', '2025-11-12 06:56:10', NULL),
(229, 'AS285', 'Alusteel coil', 'IBeige', 9, 3184.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:25:17', '2025-11-12 06:56:10', NULL),
(230, 'AS286', 'Alusteel coil', 'IBeige', 9, 3136.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:25:59', '2025-11-12 06:56:10', NULL),
(231, 'AS287', 'Alusteel coil', 'GBeige', 14, 3076.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:26:59', '2025-11-12 06:56:10', NULL),
(232, 'AS290', 'Alusteel coil', 'SBlue', 11, 3192.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:28:12', '2025-11-12 06:56:10', NULL),
(233, 'AS291', 'Alusteel coil', 'BGreen', 15, 2536.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:29:18', '2025-11-12 06:56:10', NULL),
(234, 'AS292', 'Alusteel coil', 'BGreen', 15, 2562.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:30:01', '2025-11-12 06:56:10', NULL),
(235, 'AS293', 'Alusteel coil', 'IBeige', 9, 3564.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:30:53', '2025-11-12 06:56:10', NULL),
(236, 'AS299', 'Alusteel coil', 'BGreen', 15, 2512.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:32:00', '2025-11-12 06:56:10', NULL),
(237, 'AS300', 'Alusteel coil', 'GBeige', 14, 3296.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:32:51', '2025-11-12 06:56:10', NULL),
(238, 'AS301', 'Alusteel coil', 'PGreen', 10, 3462.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:33:40', '2025-11-12 06:56:10', NULL),
(239, 'AS302', 'Alusteel coil', 'BGreen', 15, 2546.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:34:57', '2025-11-12 06:56:10', NULL),
(240, 'AS303', 'Alusteel coil', 'IBeige', 9, 3550.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:35:43', '2025-11-12 06:56:10', NULL),
(241, 'AS309', 'Alusteel coil', 'BGreen', 15, 2516.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:36:48', '2025-11-12 06:56:10', NULL),
(242, 'AS315', 'Alusteel coil', 'IBeige', 9, 3288.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:37:43', '2025-11-12 06:56:10', NULL),
(243, 'AS316', 'Alusteel coil', 'IBeige', 9, 3274.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:38:23', '2025-11-12 06:56:10', NULL),
(244, 'AS317', 'Alusteel coil', 'BGreen', 15, 2514.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:43:49', '2025-11-12 06:56:10', NULL),
(245, 'AS318', 'Alusteel coil', 'BGreen', 15, 2530.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:44:42', '2025-11-12 06:56:10', NULL),
(246, 'AS320', 'Alusteel coil', 'BGreen', 15, 2564.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:45:42', '2025-11-12 06:56:10', NULL),
(247, 'AS325', 'Alusteel coil', 'GBeige', 14, 3322.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:46:46', '2025-11-12 06:56:10', NULL),
(248, 'AS326', 'Alusteel coil', 'BGreen', 15, 2530.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:47:30', '2025-11-12 06:56:10', NULL),
(249, 'AS331', 'Alusteel coil', 'TBlack', 12, 3260.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:49:31', '2025-11-12 06:56:10', NULL),
(250, 'AS332', 'Alusteel coil', 'TBlack', 12, 3250.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:50:19', '2025-11-12 06:56:10', NULL),
(251, 'AS333', 'Alusteel coil', 'TBlack', 12, 3406.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:50:56', '2025-11-12 06:56:10', NULL),
(252, 'AS334', 'Alusteel coil', 'IWhite', 16, 2806.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:51:53', '2025-11-12 06:56:10', NULL),
(253, 'AS335', 'Alusteel coil', 'IWhite', 16, 2820.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:52:44', '2025-11-12 06:56:10', NULL),
(254, 'AS336', 'Alusteel coil', 'IWhite', 16, 2638.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:53:26', '2025-11-12 06:56:10', NULL),
(255, 'AS337', 'Alusteel coil', 'IWhite', 16, 3256.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:54:05', '2025-11-12 06:56:10', NULL),
(256, 'AS338', 'Alusteel coil', 'IWhite', 16, 3316.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:54:44', '2025-11-12 06:56:10', NULL),
(257, 'AS339', 'Alusteel coil', 'IWhite', 16, 3348.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:55:25', '2025-11-12 06:56:10', NULL),
(258, 'AS340', 'Alusteel coil', 'IWhite', 16, 3350.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:56:12', '2025-11-12 06:56:10', NULL),
(259, 'AS341', 'Alusteel coil', 'IWhite', 16, 3336.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:56:48', '2025-11-12 06:56:10', NULL),
(260, 'AS342', 'Alusteel coil', 'IWhite', 16, 3270.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:57:24', '2025-11-12 06:56:10', NULL),
(261, 'AS343', 'Alusteel coil', 'GBeige', 14, 3294.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:58:07', '2025-11-12 06:56:10', NULL),
(262, 'AS344', 'Alusteel coil', 'GBeige', 14, 3452.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 09:59:16', '2025-11-12 06:56:10', NULL),
(263, 'AS346', 'Alusteel coil', 'GBeige', 14, 3400.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:00:03', '2025-11-12 06:56:10', NULL),
(264, 'AS347', 'Alusteel coil', 'GBeige', 14, 3370.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:00:51', '2025-11-12 06:56:10', NULL),
(265, 'AS348', 'Alusteel coil', 'GBeige', 14, 3358.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:01:34', '2025-11-12 06:56:10', NULL),
(266, 'AS349', 'Alusteel coil', 'BGreen', 15, 3344.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:02:14', '2025-11-12 06:56:10', NULL),
(267, 'AS350', 'Alusteel coil', 'BGreen', 15, 3428.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:03:03', '2025-11-12 06:56:10', NULL),
(268, 'AS351', 'Alusteel coil', 'BGreen', 15, 3242.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:03:49', '2025-11-12 06:56:10', NULL),
(269, 'AS352', 'Alusteel coil', 'BGreen', 15, 3106.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:04:37', '2025-11-12 06:56:10', NULL),
(270, 'AS353', 'Alusteel coil', 'BGreen', 15, 2996.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:05:16', '2025-11-12 06:56:10', NULL),
(271, 'AS354', 'Alusteel coil', 'TBlack', 12, 3310.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:06:07', '2025-11-12 06:56:10', NULL),
(272, 'AS355', 'Alusteel coil', 'TBlack', 12, 3258.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:06:45', '2025-11-12 06:56:10', NULL),
(273, 'AS356', 'Alusteel coil', 'TBlack', 12, 3342.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:07:26', '2025-11-12 06:56:10', NULL),
(275, 'AS358', 'Alusteel coil', 'TCRed', 13, 3380.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:09:20', '2025-11-12 06:56:10', NULL),
(276, 'AS359', 'Alusteel coil', 'TCRed', 13, 2734.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:10:32', '2025-11-12 06:56:10', NULL),
(277, 'AS360', 'Alusteel coil', 'TCRed', 13, 2746.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:11:20', '2025-11-12 06:56:10', NULL),
(278, 'AS361', 'Alusteel coil', 'TCRed', 13, 2844.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:11:55', '2025-11-12 06:56:10', NULL),
(279, 'AS362', 'Alusteel coil', 'SBlue', 11, 3172.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:12:41', '2025-11-12 06:56:10', NULL),
(280, 'AS363', 'Alusteel coil', 'SBlue', 11, 3200.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:13:28', '2025-11-12 06:56:10', NULL),
(281, 'AS364', 'Alusteel coil', 'SBlue', 11, 3162.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:14:13', '2025-11-12 06:56:10', NULL),
(282, 'AS365', 'Alusteel coil', 'SBlue', 11, 3142.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:15:52', '2025-11-12 06:56:10', NULL),
(283, 'AS366', 'Alusteel coil', 'SBlue', 11, 3248.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:16:32', '2025-11-12 06:56:10', NULL),
(284, 'AS367', 'Alusteel coil', 'IWhite', 16, 3246.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:17:14', '2025-11-12 06:56:10', NULL),
(285, 'AS368', 'Alusteel coil', 'IWhite', 16, 3276.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:18:03', '2025-11-12 06:56:10', NULL),
(286, 'AS369', 'Alusteel coil', 'IWhite', 16, 3360.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:18:50', '2025-11-12 06:56:10', NULL),
(287, 'AS370', 'Alusteel coil', 'IWhite', 16, 2714.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:19:40', '2025-11-12 06:56:10', NULL),
(288, 'AS371', 'Alusteel coil', 'IWhite', 16, 2722.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:20:24', '2025-11-12 06:56:10', NULL),
(289, 'AS372', 'Alusteel coil', 'IWhite', 16, 2840.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:21:07', '2025-11-12 06:56:10', NULL),
(290, 'AS373', 'Alusteel coil', 'IWhite', 16, 2968.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:21:47', '2025-11-12 06:56:10', NULL),
(291, 'AS374', 'Alusteel coil', 'IWhite', 16, 2936.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:22:32', '2025-11-12 06:56:10', NULL),
(292, 'AS375', 'Alusteel coil', 'IWhite', 16, 2976.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:23:53', '2025-11-12 06:56:10', NULL),
(293, 'AS376', 'Alusteel coil', 'GBeige', 14, 3440.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 10:24:54', '2025-11-12 06:56:10', NULL),
(294, 'AS377', 'Alusteel coil', 'GBeige', 14, 3302.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:31:06', '2025-11-12 06:56:10', NULL),
(295, 'AS378', 'Alusteel coil', 'GBeige', 14, 3436.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:31:50', '2025-11-12 06:56:10', NULL),
(296, 'AS379', 'Alusteel coil', 'GBeige', 14, 3310.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:32:23', '2025-11-12 06:56:10', NULL),
(297, 'AS380', 'Alusteel coil', 'GBeige', 14, 3286.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:33:10', '2025-11-12 06:56:10', NULL),
(298, 'AS381', 'Alusteel coil', 'GBeige', 14, 3098.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:34:05', '2025-11-12 06:56:10', NULL),
(299, 'AS382', 'Alusteel coil', 'BGreen', 15, 3134.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:34:54', '2025-11-12 06:56:10', NULL),
(300, 'AS383', 'Alusteel coil', 'BGreen', 15, 3246.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:35:35', '2025-11-12 06:56:10', NULL),
(301, 'AS385', 'Alusteel coil', 'BGreen', 15, 3124.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:36:24', '2025-11-12 06:56:10', NULL),
(302, 'AS387', 'Alusteel coil', 'TCRed', 13, 3198.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:38:00', '2025-11-12 06:56:10', NULL),
(303, 'AS388', 'Alusteel coil', 'TCRed', 13, 3190.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:38:48', '2025-11-12 06:56:10', NULL),
(304, 'AS389', 'Alusteel coil', 'TCRed', 13, 3312.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:39:32', '2025-11-12 06:56:10', NULL),
(305, 'AS390', 'Alusteel coil', 'TCRed', 13, 2910.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:40:11', '2025-11-12 06:56:10', NULL),
(306, 'AS391', 'Alusteel coil', 'TCRed', 13, 2978.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:40:49', '2025-11-12 06:56:10', NULL),
(307, 'AS392', 'Alusteel coil', 'IWhite', 16, 3202.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:41:35', '2025-11-12 06:56:10', NULL),
(308, 'AS393', 'Alusteel coil', 'IWhite', 16, 3202.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:42:23', '2025-11-12 06:56:10', NULL),
(309, 'AS394', 'Alusteel coil', 'IWhite', 16, 3350.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:43:02', '2025-11-12 06:56:10', NULL),
(310, 'AS395', 'Alusteel coil', 'IWhite', 16, 3202.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:44:28', '2025-11-12 06:56:10', NULL),
(311, 'AS396', 'Alusteel coil', 'IWhite', 16, 3208.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:45:17', '2025-11-12 06:56:10', NULL),
(312, 'AS397', 'Alusteel coil', 'IWhite', 16, 3364.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:46:01', '2025-11-12 06:56:10', NULL),
(313, 'AS398', 'Alusteel coil', 'IWhite', 16, 2182.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:46:46', '2025-11-12 06:56:10', NULL),
(314, 'AS399', 'Alusteel coil', 'IWhite', 16, 2316.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:47:23', '2025-11-12 06:56:10', NULL),
(315, 'AS400', 'Alusteel coil', 'GBeige', 14, 3204.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:48:55', '2025-11-12 06:56:10', NULL),
(316, 'AS401', 'Alusteel coil', 'GBeige', 14, 3202.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:49:47', '2025-11-12 06:56:10', NULL),
(317, 'AS402', 'Alusteel coil', 'GBeige', 14, 3428.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:50:34', '2025-11-12 06:56:10', NULL),
(318, 'AS403', 'Alusteel coil', 'GBeige', 14, 2994.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:51:15', '2025-11-12 06:56:10', NULL),
(319, 'AS404', 'Alusteel coil', 'GBeige', 14, 3196.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:51:50', '2025-11-12 06:56:10', NULL),
(320, 'AS405', 'Alusteel coil', 'GBeige', 14, 3206.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:52:43', '2025-11-12 06:56:10', NULL),
(321, 'AS406', 'Alusteel coil', 'GBeige', 14, 3366.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:53:21', '2025-11-12 06:56:10', NULL),
(322, 'AS407', 'Alusteel coil', 'BGreen', 15, 3242.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:54:07', '2025-11-12 06:56:10', NULL),
(323, 'AS408', 'Alusteel coil', 'BGreen', 15, 2998.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:54:43', '2025-11-12 06:56:10', NULL),
(324, 'AS409', 'Alusteel coil', 'BGreen', 15, 3618.00, NULL, NULL, 'alusteel', 'available', 5, '2025-11-11 13:55:24', '2025-11-12 06:56:10', NULL),
(325, 'K53', 'K Zinc coil', 'IWhite', 16, 2938.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:15:34', '2025-11-12 06:56:10', NULL),
(326, 'K54', 'K Zinc coil', 'IWhite', 16, 2880.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:16:24', '2025-11-12 06:56:10', NULL),
(327, 'K60', 'K Zinc coil', 'IWhite', 16, 2916.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:17:08', '2025-11-12 06:56:10', NULL),
(328, 'K63', 'K Zinc coil', 'IWhite', 16, 2920.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:17:45', '2025-11-12 06:56:10', NULL),
(329, 'K64', 'K Zinc coil', 'IWhite', 16, 2902.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:18:20', '2025-11-12 06:56:10', NULL),
(330, 'K65', 'K Zinc coil', 'IWhite', 16, 2940.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:18:52', '2025-11-12 06:56:10', NULL),
(331, 'K77', 'K Zinc coil', 'IWhite', 16, 2940.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:19:30', '2025-11-12 06:56:10', NULL),
(332, 'K78', 'K Zinc coil', 'IWhite', 16, 2910.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:20:09', '2025-11-12 06:56:10', NULL),
(333, 'K87', 'K Zinc coil', 'IBeige', 9, 2900.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:20:56', '2025-11-12 06:56:10', NULL),
(334, 'K88', 'K Zinc coil', 'IBeige', 9, 2900.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:21:36', '2025-11-12 06:56:10', NULL),
(335, 'K89', 'K Zinc coil', 'IBeige', 9, 2914.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:22:21', '2025-11-12 06:56:10', NULL),
(336, 'K91', 'K Zinc coil', 'GBeige', 14, 2904.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:23:53', '2025-11-12 06:56:10', NULL),
(337, 'K92', 'K Zinc coil', 'GBeige', 14, 2912.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:24:52', '2025-11-12 06:56:10', NULL),
(338, 'K96', 'K Zinc coil', 'GBeige', 14, 2924.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:25:34', '2025-11-12 06:56:10', NULL),
(339, 'K99', 'K Zinc coil', 'GBeige', 14, 2992.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:26:08', '2025-11-12 06:56:10', NULL),
(340, 'K102', 'K Zinc coil', 'SBlue', 11, 2844.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:26:51', '2025-11-12 06:56:10', NULL),
(341, 'K104', 'K Zinc coil', 'SBlue', 11, 2870.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:27:25', '2025-11-12 06:56:10', NULL),
(342, 'K146', 'K Zinc coil', 'IWhite', 16, 2742.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:28:15', '2025-11-12 06:56:10', NULL),
(343, 'K161', 'K Zinc coil', 'IWhite', 16, 2159.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:29:00', '2025-11-12 06:56:10', NULL),
(344, 'K237', 'K Zinc coil', 'SBlue', 11, 2926.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:31:07', '2025-11-12 06:56:10', NULL),
(345, 'K240', 'K Zinc coil', 'IBeige', 9, 2952.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:31:58', '2025-11-12 06:56:10', NULL),
(346, 'K242', 'K Zinc coil', 'IBeige', 9, 2940.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:32:39', '2025-11-12 06:56:10', NULL),
(347, 'K245', 'K Zinc coil', 'IBeige', 9, 2934.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:33:57', '2025-11-12 06:56:10', NULL),
(348, 'K248', 'K Zinc coil', 'SBlue', 11, 2930.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:35:02', '2025-11-12 06:56:10', NULL),
(349, 'K250', 'K Zinc coil', 'SBlue', 11, 2960.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:35:46', '2025-11-12 06:56:10', NULL),
(350, 'K251', 'K Zinc coil', 'SBlue', 11, 2924.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:36:26', '2025-11-12 06:56:10', NULL),
(351, 'K252', 'K Zinc coil', 'SBlue', 11, 2938.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:37:17', '2025-11-12 06:56:10', NULL),
(352, 'K253', 'K Zinc coil', 'SBlue', 11, 2942.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:37:54', '2025-11-12 06:56:10', NULL),
(353, 'K254', 'K Zinc coil', 'SBlue', 11, 2950.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:38:32', '2025-11-12 06:56:10', NULL),
(354, 'K262', 'K Zinc coil', 'BGreen', 15, 2954.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:39:17', '2025-11-12 06:56:10', NULL),
(355, 'K264', 'K Zinc coil', 'IBeige', 9, 2926.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:40:01', '2025-11-12 06:56:10', NULL),
(356, 'K265', 'K Zinc coil', 'IBeige', 9, 2946.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:40:45', '2025-11-12 06:56:10', NULL),
(357, 'K266', 'K Zinc coil', 'IBeige', 9, 2900.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:41:25', '2025-11-12 06:56:10', NULL),
(358, 'K272', 'K Zinc coil', 'IBeige', 9, 2936.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:44:14', '2025-11-12 06:56:10', NULL),
(359, 'K273', 'K Zinc coil', 'IBeige', 9, 2930.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:45:02', '2025-11-12 06:56:10', NULL),
(360, 'K278', 'K Zinc coil', 'TCRed', 13, 2910.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:45:47', '2025-11-12 06:56:10', NULL),
(361, 'K293', 'K Zinc coil', 'IBeige', 9, 2946.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:46:55', '2025-11-12 06:56:10', NULL),
(362, 'K294', 'K Zinc coil', 'IBeige', 9, 2930.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:47:33', '2025-11-12 06:56:10', NULL),
(363, 'K295', 'K Zinc coil', 'IBeige', 9, 3014.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:48:16', '2025-11-12 06:56:10', NULL),
(364, 'K297', 'K Zinc coil', 'IWhite', 16, 2960.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:48:59', '2025-11-12 06:56:10', NULL),
(365, 'K298', 'K Zinc coil', 'IWhite', 16, 2960.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:49:43', '2025-11-12 06:56:10', NULL),
(366, 'K299', 'K Zinc coil', 'IWhite', 16, 2960.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:50:26', '2025-11-12 06:56:10', NULL),
(367, 'K306', 'K Zinc coil', 'IWhite', 16, 2970.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:51:14', '2025-11-12 06:56:10', NULL),
(368, 'K307', 'K Zinc coil', 'IWhite', 16, 2992.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:51:48', '2025-11-12 06:56:10', NULL),
(369, 'K312', 'K Zinc coil', 'IWhite', 16, 2988.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:55:52', '2025-11-12 06:56:10', NULL),
(370, 'K313', 'K Zinc coil', 'IWhite', 16, 3046.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:56:28', '2025-11-12 06:56:10', NULL),
(371, 'K314', 'K Zinc coil', 'IWhite', 16, 2946.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:57:13', '2025-11-12 06:56:10', NULL),
(372, 'K315', 'K Zinc coil', 'IWhite', 16, 2956.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:58:12', '2025-11-12 06:56:10', NULL),
(373, 'K317', 'K Zinc coil', 'IWhite', 16, 3006.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:58:49', '2025-11-12 06:56:10', NULL),
(374, 'K318', 'K Zinc coil', 'IWhite', 16, 2972.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 14:59:43', '2025-11-12 06:56:10', NULL),
(375, 'K319', 'K Zinc coil', 'IWhite', 16, 2940.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:00:23', '2025-11-12 06:56:10', NULL),
(376, 'K320', 'K Zinc coil', 'IWhite', 16, 2990.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:00:57', '2025-11-12 06:56:10', NULL),
(377, 'K321', 'K Zinc coil', 'IWhite', 16, 2946.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:01:29', '2025-11-12 06:56:10', NULL),
(378, 'K322', 'K Zinc coil', 'IWhite', 16, 2976.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:02:20', '2025-11-12 06:56:10', NULL),
(379, 'K323', 'K Zinc coil', 'IWhite', 16, 2860.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:03:06', '2025-11-12 06:56:10', NULL),
(380, 'K324', 'K Zinc coil', 'IWhite', 16, 2816.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:03:53', '2025-11-12 06:56:10', NULL),
(381, 'K325', 'K Zinc coil', 'IWhite', 16, 2954.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:04:30', '2025-11-12 06:56:10', NULL),
(382, 'K326', 'K Zinc coil', 'IWhite', 16, 2960.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:05:13', '2025-11-12 06:56:10', NULL),
(383, 'K327', 'K Zinc coil', 'IWhite', 16, 2962.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:05:51', '2025-11-12 06:56:10', NULL),
(384, 'K337', 'K Zinc coil', 'IWhite', 16, 3030.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:10:46', '2025-11-12 06:56:10', NULL),
(385, 'K338', 'K Zinc coil', 'IWhite', 16, 2990.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:11:42', '2025-11-12 06:56:10', NULL),
(386, 'K339', 'K Zinc coil', 'IWhite', 16, 2996.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:12:37', '2025-11-12 06:56:10', NULL),
(387, 'K347', 'K Zinc coil', 'IWhite', 16, 3000.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:13:43', '2025-11-12 06:56:10', NULL),
(388, 'K348', 'K Zinc coil', 'IWhite', 16, 2972.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:14:23', '2025-11-12 06:56:10', NULL),
(389, 'K349', 'K Zinc coil', 'IWhite', 16, 2968.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:15:02', '2025-11-12 06:56:10', NULL),
(390, 'K350', 'K Zinc coil', 'IWhite', 16, 2960.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:15:45', '2025-11-12 06:56:10', NULL),
(391, 'K351', 'K Zinc coil', 'IWhite', 16, 2940.00, NULL, NULL, 'kzinc', 'available', 5, '2025-11-11 15:16:22', '2025-11-12 06:56:10', NULL);

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
(10, 'PGreen', 'P/Green', NULL, 1, 5, '2025-11-11 20:27:37', '2025-11-11 20:50:09', NULL),
(11, 'SBlue', 'S/Blue', '#C4D8E2', 1, 5, '2025-11-11 20:27:37', '2025-11-12 06:59:58', NULL),
(12, 'TBlack', 'T/Black', NULL, 1, 5, '2025-11-11 20:27:37', '2025-11-11 20:50:09', NULL),
(13, 'TCRed', 'TC/Red', NULL, 1, 5, '2025-11-11 20:27:37', '2025-11-11 20:50:09', NULL),
(14, 'GBeige', 'G/Beige', '#BEB6A6', 1, 5, '2025-11-11 20:27:37', '2025-11-12 06:59:19', NULL),
(15, 'BGreen', 'B/Green', '#009688', 1, 5, '2025-11-11 20:27:37', '2025-11-12 06:59:36', NULL),
(16, 'IWhite', 'I/White', '#FFFFF0', 1, 5, '2025-11-11 20:27:37', '2025-11-12 06:58:08', NULL),
(17, 'STest', 'S/Test', NULL, 0, 5, '2025-11-11 20:51:24', '2025-11-11 21:10:40', '2025-11-11 21:10:40');

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
(3, 'Stevo Aluminium', 'stevoaluminum@gmail.com', '08032218808', NULL, 'Stevo Aluminium IG LTD', 5, '2025-11-08 12:56:42', '2025-11-08 13:11:09', NULL);

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

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `customer_id`, `coil_id`, `stock_entry_id`, `sale_type`, `meters`, `price_per_meter`, `total_amount`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(11, 1, 49, 11, 'retail', 40.00, 3000.00, 120000.00, 'completed', 2, '2025-11-08 11:40:38', NULL, NULL),
(12, 1, 49, 11, 'retail', 288.70, 10300.00, 2973610.00, 'completed', 2, '2025-11-08 11:50:31', NULL, NULL),
(14, 1, 50, 12, 'retail', 400.00, 2000.00, 800000.00, 'completed', 5, '2025-11-08 12:38:10', NULL, NULL),
(15, 1, 50, 12, 'retail', 300.00, 2000.00, 600000.00, 'completed', 5, '2025-11-08 12:40:35', NULL, NULL),
(16, 1, 50, 12, 'retail', 400.00, 2000.00, 800000.00, 'completed', 5, '2025-11-08 12:41:44', NULL, NULL),
(17, 1, 51, 13, 'retail', 200.00, 2000.00, 400000.00, 'completed', 5, '2025-11-08 12:49:07', NULL, NULL),
(18, 1, 51, 13, 'retail', 400.00, 2000.00, 800000.00, 'completed', 5, '2025-11-08 12:50:39', NULL, NULL),
(19, 3, 52, 14, 'wholesale', 2500.00, 2000.00, 5000000.00, 'completed', 5, '2025-11-08 13:11:50', NULL, NULL);

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
(11, 49, 2600.00, 2271.30, 0.00, 'factory_use', 2, '2025-11-08 11:38:09', '2025-11-08 11:50:31', NULL),
(12, 50, 2650.00, 1550.00, 0.00, 'factory_use', 5, '2025-11-08 12:25:44', '2025-11-08 12:41:44', NULL),
(13, 51, 2500.00, 1900.00, 0.00, 'factory_use', 5, '2025-11-08 12:45:41', '2025-11-08 12:50:39', NULL),
(14, 52, 2500.00, 0.00, 0.00, 'available', 5, '2025-11-08 12:54:10', '2025-11-08 13:11:50', NULL);

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
(26, 51, 13, 'outflow', 'Retail sale to Mr Lawal (400m @ ₦2000/m)', 0.00, 400.00, 1900.00, 'sale', 18, 5, '2025-11-08 12:50:39');

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
(1, 'admin@example.com', '$2y$10$RBop9EOLBg.Vo9AKPBp.xOnx/18TGybfpkmU.//PgdAwLFSXr3GZ6', 'SHEMAIAH WAMBEBE YABA-SHIAKA', 'super_admin', '2025-11-05 08:37:50', '2025-11-12 07:02:43', '2025-11-12 07:02:43'),
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
(48, 2, 'user_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-12 07:02:23', NULL),
(49, 2, 'customer_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-12 07:02:23', NULL),
(50, 2, 'stock_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-12 07:02:23', NULL),
(51, 2, 'sales_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-12 07:02:23', NULL),
(52, 2, 'warehouse_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-12 07:02:23', NULL),
(53, 2, 'color_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-12 07:02:23', NULL),
(54, 2, 'production_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-12 07:02:23', NULL),
(55, 2, 'invoice_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-12 07:02:23', NULL),
(56, 2, 'supply_management', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-12 07:02:23', NULL),
(57, 2, 'reports', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-12 07:02:23', NULL),
(58, 2, 'dashboard', '[\"view\",\"create\",\"edit\",\"delete\"]', '2025-11-12 07:02:23', NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=392;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `production`
--
ALTER TABLE `production`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `stock_entries`
--
ALTER TABLE `stock_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `stock_ledger`
--
ALTER TABLE `stock_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `supply_delivery`
--
ALTER TABLE `supply_delivery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

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
