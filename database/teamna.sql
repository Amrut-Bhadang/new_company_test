-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 31, 2022 at 09:00 AM
-- Server version: 8.0.29-0ubuntu0.20.04.3
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `teamna`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_notification`
--

CREATE TABLE `admin_notification` (
  `id` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text,
  `notification_type` enum('Send','Received') DEFAULT NULL,
  `notification_for` enum('All','Users','FacilityOwner') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin_notification`
--

INSERT INTO `admin_notification` (`id`, `title`, `message`, `notification_type`, `notification_for`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Test Notification', 'Test notification message', 'Send', 'Users', 1, '2022-01-11 08:45:38', '2022-01-11 08:45:38'),
(2, 'ssfsf', 'fafasfsaf', 'Send', 'All', 1, '2022-04-08 06:34:50', '2022-04-08 06:34:50'),
(3, 'Testing', 'Hello', 'Send', 'FacilityOwner', 1, '2022-04-18 09:50:44', '2022-04-18 09:50:44');

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `id` bigint NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`id`, `name`, `image`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Rest Room', 'MAY2022/1652345586-amenity.png', 1, '2022-03-21 08:11:22', '2022-05-30 10:36:51'),
(3, 'Parking', 'MAY2022/1652345574-amenity.png', 1, '2022-03-21 23:02:50', '2022-05-30 10:34:32'),
(4, 'Coffee', 'MAY2022/1652345565-amenity.png', 1, '2022-03-21 23:34:22', '2022-05-30 10:36:30'),
(5, 'Tea and Milk', 'MAY2022/1652345551-amenity.png', 1, '2022-03-21 23:35:36', '2022-05-30 10:36:11'),
(6, 'Water', 'MAY2022/1652345541-amenity.png', 0, '2022-04-05 08:51:25', '2022-05-12 08:52:21'),
(7, 'Medical', 'APR2022/1650523235-amenity.png', 1, '2022-04-21 06:40:35', '2022-05-30 10:35:43'),
(8, 'Coach', 'APR2022/1650523302-amenity.png', 1, '2022-04-21 06:41:42', '2022-05-30 10:35:24');

-- --------------------------------------------------------

--
-- Table structure for table `amenities_lang`
--

CREATE TABLE `amenities_lang` (
  `id` bigint NOT NULL,
  `amenity_id` bigint DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `lang` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `amenities_lang`
--

INSERT INTO `amenities_lang` (`id`, `amenity_id`, `name`, `lang`, `created_at`, `updated_at`) VALUES
(1, 2, 'Rest Room', 'en', '2022-03-21 08:11:22', '2022-05-30 10:36:51'),
(2, 2, 'غرفة الاستراحة', 'ar', '2022-03-21 08:11:22', '2022-05-30 10:36:51'),
(3, 3, 'Parking', 'en', '2022-03-21 23:02:50', '2022-05-30 10:34:32'),
(4, 3, 'موقف سيارات', 'ar', '2022-03-21 23:02:50', '2022-05-30 10:34:32'),
(5, 4, 'Coffee', 'en', '2022-03-21 23:34:22', '2022-05-30 10:36:30'),
(6, 4, 'قهوة', 'ar', '2022-03-21 23:34:22', '2022-05-30 10:36:30'),
(7, 5, 'Tea and Milk', 'en', '2022-03-21 23:35:36', '2022-05-30 10:36:11'),
(8, 5, 'شاي وحليب', 'ar', '2022-03-21 23:35:36', '2022-05-30 10:36:11'),
(9, 6, 'Water', 'en', '2022-04-05 08:51:25', '2022-05-12 08:52:21'),
(10, 6, 'ماء', 'ar', '2022-04-05 08:51:25', '2022-05-12 08:52:21'),
(11, 7, 'Medical', 'en', '2022-04-21 06:40:35', '2022-05-30 10:35:43'),
(12, 7, 'طبي', 'ar', '2022-04-21 06:40:35', '2022-05-30 10:35:43'),
(13, 8, 'Coach', 'en', '2022-04-21 06:41:42', '2022-05-30 10:35:24'),
(14, 8, 'مدرب رياضي', 'ar', '2022-04-21 06:41:42', '2022-05-30 10:35:24');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` bigint NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `type` enum('facility','court') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type_id` bigint DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `title`, `description`, `image`, `type`, `type_id`, `status`, `created_at`, `updated_at`) VALUES
(5, 'Tahadiyaat', 'Test Banner', 'APR2022/1650373104-banner.jpeg', 'court', 8, 1, '2022-03-22 22:43:09', '2022-04-19 12:58:24'),
(6, 'Stadium', 'Test Banner  2', 'APR2022/1650373035-banner.jpeg', 'facility', 12, 1, '2022-03-22 22:43:56', '2022-04-19 12:57:31'),
(7, 'PlayGo', 'Test Banner  3', 'APR2022/1650373002-banner.jpeg', 'facility', 19, 1, '2022-03-22 22:44:26', '2022-04-19 12:57:04');

-- --------------------------------------------------------

--
-- Table structure for table `banners_lang`
--

CREATE TABLE `banners_lang` (
  `id` bigint NOT NULL,
  `banner_id` bigint DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `lang` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `banners_lang`
--

INSERT INTO `banners_lang` (`id`, `banner_id`, `title`, `lang`, `created_at`, `updated_at`) VALUES
(1, 1, 'Title English', 'en', '2022-03-22 07:10:48', '2022-03-22 07:10:48'),
(2, 1, 'Title  Arabic', 'ar', '2022-03-22 07:10:48', '2022-03-22 07:10:48'),
(3, 2, 'Title English2', 'en', '2022-03-22 07:12:23', '2022-03-22 07:50:10'),
(4, 2, 'Title  Arabic2', 'ar', '2022-03-22 07:12:23', '2022-03-22 07:50:10'),
(5, 3, 'Title English', 'en', '2022-03-22 08:09:34', '2022-03-22 08:09:34'),
(6, 3, 'Title  Arabic', 'ar', '2022-03-22 08:09:34', '2022-03-22 08:09:34'),
(7, 4, 'fdf', 'en', '2022-03-22 08:11:49', '2022-03-22 08:11:49'),
(8, 4, 'dfd', 'ar', '2022-03-22 08:11:49', '2022-03-22 08:11:49'),
(9, 5, 'Tahadiyaat', 'en', '2022-03-22 22:43:09', '2022-04-19 12:58:24'),
(10, 5, 'Tahadiyaat', 'ar', '2022-03-22 22:43:09', '2022-04-19 12:58:24'),
(11, 6, 'Stadium', 'en', '2022-03-22 22:43:56', '2022-04-19 12:57:31'),
(12, 6, 'Stadium', 'ar', '2022-03-22 22:43:56', '2022-04-19 12:57:31'),
(13, 7, 'PlayGo', 'en', '2022-03-22 22:44:26', '2022-04-19 12:57:04'),
(14, 7, 'PlayGo', 'ar', '2022-03-22 22:44:26', '2022-04-19 12:57:04'),
(15, 8, 'Holy', 'en', '2022-04-08 06:32:42', '2022-04-08 06:32:42'),
(16, 8, 'Holy', 'ar', '2022-04-08 06:32:42', '2022-04-08 06:32:42'),
(17, 9, 'MAR50', 'en', '2022-04-08 06:32:55', '2022-04-08 06:32:55'),
(18, 9, 'MAR50', 'ar', '2022-04-08 06:32:55', '2022-04-08 06:32:55'),
(19, 10, 'Holy', 'en', '2022-04-18 09:47:16', '2022-04-18 09:47:16'),
(20, 10, 'MAR50', 'ar', '2022-04-18 09:47:16', '2022-04-18 09:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `booking_challenges`
--

CREATE TABLE `booking_challenges` (
  `id` bigint NOT NULL,
  `court_booking_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `payment_type` enum('online','cash') DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `challenge_status` enum('Pending','Accepted','Decline') NOT NULL DEFAULT 'Pending',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `booking_challenges`
--

INSERT INTO `booking_challenges` (`id`, `court_booking_id`, `user_id`, `amount`, `payment_type`, `transaction_id`, `challenge_status`, `status`, `created_at`, `updated_at`) VALUES
(1, 36, 37, 12112, 'online', '1212121212', 'Accepted', 1, '2022-04-07 00:28:14', '2022-04-07 00:28:14'),
(2, 37, 37, 12112, 'online', '1212121212', 'Accepted', 1, '2022-04-07 00:50:26', '2022-04-07 00:50:26'),
(3, 36, 40, 43434, 'online', '434343', 'Accepted', 1, '2022-04-08 09:57:38', '2022-04-08 09:57:38'),
(4, 26, 47, 300, 'online', '2234323424', 'Accepted', 1, '2022-04-08 10:12:29', '2022-04-08 10:12:29'),
(5, 27, 40, 100, 'online', '1212121212', 'Accepted', 1, '2022-04-08 13:42:11', '2022-04-08 13:42:11'),
(6, 32, 57, 100, 'online', '2234323424', 'Accepted', 1, '2022-04-11 05:35:49', '2022-04-11 05:35:49'),
(7, 33, 62, 1, 'online', '2234323424', 'Accepted', 1, '2022-04-11 09:28:20', '2022-04-11 09:28:20'),
(8, 34, 41, 100, 'online', '2234323424', 'Accepted', 1, '2022-04-11 10:02:29', '2022-04-11 10:02:29'),
(9, 35, 41, 1, 'online', '2234323424', 'Accepted', 1, '2022-04-11 10:09:49', '2022-04-11 10:09:49'),
(10, 39, 64, 1500, 'online', '2234323424', 'Accepted', 1, '2022-04-11 11:08:34', '2022-04-11 11:08:34'),
(11, 40, 64, 1500, 'online', '2234323424', 'Accepted', 1, '2022-04-11 11:11:32', '2022-04-11 11:11:32'),
(12, 41, 67, 1, 'online', '2234323424', 'Accepted', 1, '2022-04-11 11:32:04', '2022-04-11 11:32:04'),
(13, 43, 69, 2, 'online', '2234323424', 'Accepted', 1, '2022-04-13 09:26:09', '2022-04-13 09:26:09'),
(14, 45, 72, 900, 'online', '2234323424', 'Accepted', 1, '2022-04-14 05:44:29', '2022-04-14 05:44:29'),
(15, 19, 47, 1500, 'cash', '', 'Accepted', 1, '2022-04-14 09:01:22', '2022-04-14 09:01:22'),
(16, 7, 47, 100, 'online', '342423333', 'Accepted', 1, '2022-04-14 09:05:10', '2022-04-14 09:05:10'),
(17, 19, 73, 900, 'cash', '', 'Accepted', 1, '2022-04-15 05:41:35', '2022-04-15 05:41:35'),
(18, 27, 9, 43434, 'online', '434343', 'Accepted', 1, '2022-04-15 05:44:08', '2022-04-15 05:44:08'),
(19, 49, 73, 200, 'online', '2234323424', 'Accepted', 1, '2022-04-15 05:55:57', '2022-04-15 05:55:57'),
(20, 6, 73, 2, 'cash', '', 'Accepted', 1, '2022-04-15 05:58:15', '2022-04-15 05:58:15'),
(21, 33, 9, 43434, 'online', '434343', 'Accepted', 1, '2022-04-15 06:17:27', '2022-04-15 06:17:27'),
(22, 45, 73, 900, 'cash', '', 'Accepted', 1, '2022-04-15 06:35:35', '2022-04-15 06:35:35'),
(23, 41, 69, 1, 'cash', '', 'Accepted', 1, '2022-04-15 10:50:09', '2022-04-15 10:50:09'),
(24, 55, 80, 900, 'online', '2234323424', 'Accepted', 1, '2022-04-15 12:37:34', '2022-04-15 12:37:34'),
(25, 56, 79, 22, 'online', '2234323424', 'Accepted', 1, '2022-04-15 12:52:23', '2022-04-15 12:52:23'),
(26, 57, 79, 100, 'online', '2234323424', 'Accepted', 1, '2022-04-15 13:14:55', '2022-04-15 13:14:55'),
(27, 49, 79, 200, 'cash', '', 'Accepted', 1, '2022-04-15 13:15:45', '2022-04-15 13:15:45'),
(28, 58, 79, 171, 'cash', '', 'Accepted', 1, '2022-04-15 13:16:07', '2022-04-15 13:16:07'),
(29, 59, 47, 513, 'cash', '', 'Accepted', 1, '2022-04-18 05:52:18', '2022-04-18 05:52:18'),
(30, 34, 87, 100, 'cash', '', 'Accepted', 1, '2022-04-18 13:42:42', '2022-04-18 13:42:42'),
(31, 60, 47, 333, 'cash', '', 'Accepted', 1, '2022-04-19 04:54:37', '2022-04-19 04:54:37'),
(32, 35, 41, 1, 'cash', '', 'Accepted', 1, '2022-04-19 12:42:32', '2022-04-19 12:42:32'),
(33, 61, 47, 100, 'cash', '', 'Accepted', 1, '2022-04-20 09:22:01', '2022-04-20 09:22:01'),
(34, 62, 47, 200, 'cash', '', 'Accepted', 1, '2022-04-20 09:25:29', '2022-04-20 09:25:29'),
(35, 63, 87, 1, 'cash', '', 'Accepted', 1, '2022-04-20 13:58:01', '2022-04-20 13:58:01'),
(36, 64, 58, 171, 'cash', '', 'Accepted', 1, '2022-04-20 14:01:01', '2022-04-20 14:01:01'),
(37, 65, 91, 15, 'cash', '', 'Accepted', 1, '2022-04-21 05:28:20', '2022-04-21 05:28:20'),
(38, 63, 41, 1, 'cash', '', 'Accepted', 1, '2022-04-21 05:38:19', '2022-04-21 05:38:19'),
(39, 65, 41, 15, 'cash', '', 'Accepted', 1, '2022-04-21 05:39:11', '2022-04-21 05:39:11'),
(40, 66, 93, 1, 'cash', '', 'Accepted', 1, '2022-04-21 11:56:57', '2022-04-21 11:56:57'),
(41, 39, 87, 1500, 'online', '342423333', 'Accepted', 1, '2022-04-22 12:55:39', '2022-04-22 12:55:39'),
(42, 64, 91, 171, 'cash', '', 'Accepted', 1, '2022-04-25 07:21:57', '2022-04-25 07:21:57'),
(43, 77, 97, 100, 'cash', '', 'Accepted', 1, '2022-04-25 14:09:39', '2022-04-25 14:09:39'),
(44, 79, 91, 12112, 'online', '1212121212', 'Accepted', 1, '2022-05-02 09:31:39', '2022-05-02 09:31:39'),
(45, 84, 80, 123, 'cash', '', 'Accepted', 1, '2022-05-03 10:12:23', '2022-05-03 10:12:23'),
(46, 85, 103, 10, 'cash', '', 'Accepted', 1, '2022-05-03 12:37:16', '2022-05-03 12:37:16'),
(47, 87, 103, 500, 'cash', '', 'Accepted', 1, '2022-05-03 12:55:08', '2022-05-03 12:55:08'),
(48, 90, 103, 900, 'cash', '', 'Accepted', 1, '2022-05-03 13:12:55', '2022-05-03 13:12:55'),
(49, 99, 47, 100, 'cash', '', 'Accepted', 1, '2022-05-05 07:28:38', '2022-05-05 07:28:38'),
(50, 99, 104, 100, 'cash', '', 'Accepted', 1, '2022-05-05 09:01:19', '2022-05-05 09:01:19'),
(51, 104, 104, 171, 'cash', '', 'Accepted', 1, '2022-05-05 09:05:20', '2022-05-05 09:05:20'),
(52, 84, 104, 123, 'cash', '', 'Accepted', 1, '2022-05-05 09:05:50', '2022-05-05 09:05:50'),
(53, 105, 97, 12112, 'online', '1212121212', 'Accepted', 1, '2022-05-05 09:21:31', '2022-05-05 09:21:31'),
(54, 106, 97, 12112, 'online', '1212121212', 'Accepted', 1, '2022-05-05 09:43:07', '2022-05-05 09:43:07'),
(55, 107, 97, 12112, 'online', '1212121212', 'Accepted', 1, '2022-05-05 10:09:53', '2022-05-05 10:09:53'),
(56, 108, 97, 12112, 'online', '1212121212', 'Accepted', 1, '2022-05-05 10:10:22', '2022-05-05 10:10:22'),
(57, 109, 91, 100, 'cash', '', 'Accepted', 1, '2022-05-05 10:35:56', '2022-05-05 10:35:56'),
(58, 112, 91, 100, 'cash', '', 'Accepted', 1, '2022-05-05 11:13:56', '2022-05-05 11:13:56'),
(59, 113, 91, 171, 'cash', '', 'Accepted', 1, '2022-05-05 11:24:29', '2022-05-05 11:24:29'),
(60, 114, 91, 171, 'cash', '', 'Accepted', 1, '2022-05-05 11:25:40', '2022-05-05 11:25:40'),
(61, 115, 91, 8, 'cash', '', 'Accepted', 1, '2022-05-05 11:25:58', '2022-05-05 11:25:58'),
(62, 116, 91, 171, 'cash', '', 'Accepted', 1, '2022-05-05 11:59:05', '2022-05-05 11:59:05'),
(63, 116, 87, 171, 'cash', '', 'Accepted', 1, '2022-05-06 12:38:27', '2022-05-06 12:38:27'),
(64, 121, 87, 1, 'cash', '', 'Accepted', 1, '2022-05-06 12:44:50', '2022-05-06 12:44:50'),
(65, 124, 91, 200, 'cash', '', 'Accepted', 1, '2022-05-06 13:30:04', '2022-05-06 13:30:04'),
(66, 126, 87, 15, 'cash', '', 'Accepted', 1, '2022-05-06 13:34:12', '2022-05-06 13:34:12'),
(67, 126, 97, 15, 'cash', '11123455587', 'Accepted', 1, '2022-05-09 12:51:02', '2022-05-09 12:51:02'),
(68, 129, 87, 100, 'cash', '', 'Accepted', 1, '2022-05-10 06:50:44', '2022-05-10 06:50:44'),
(69, 131, 87, 100, 'cash', '', 'Accepted', 1, '2022-05-10 07:30:28', '2022-05-10 07:30:28'),
(70, 132, 87, 5, 'cash', '', 'Accepted', 1, '2022-05-10 08:36:56', '2022-05-10 08:36:56'),
(71, 131, 91, 100, 'cash', '', 'Accepted', 1, '2022-05-10 09:06:15', '2022-05-10 09:06:15'),
(72, 135, 109, 500, 'cash', '', 'Accepted', 1, '2022-05-10 09:48:18', '2022-05-10 09:48:18'),
(73, 136, 109, 15, 'cash', '', 'Accepted', 1, '2022-05-10 09:48:32', '2022-05-10 09:48:32'),
(74, 137, 109, 300, 'cash', '', 'Accepted', 1, '2022-05-10 09:48:54', '2022-05-10 09:48:54'),
(75, 138, 87, 123, 'cash', '', 'Accepted', 1, '2022-05-10 10:38:49', '2022-05-10 10:38:49'),
(76, 139, 87, 300, 'cash', '', 'Accepted', 1, '2022-05-10 10:40:43', '2022-05-10 10:40:43'),
(77, 137, 87, 300, 'cash', '', 'Accepted', 1, '2022-05-10 10:54:51', '2022-05-10 10:54:51'),
(78, 154, 87, 15, 'cash', '', 'Accepted', 1, '2022-05-11 13:27:30', '2022-05-11 13:27:30'),
(79, 158, 91, 100, 'cash', '', 'Accepted', 1, '2022-05-13 09:08:50', '2022-05-13 09:08:50'),
(80, 160, 91, 22, 'cash', '', 'Accepted', 1, '2022-05-13 10:07:39', '2022-05-13 10:07:39'),
(81, 161, 97, 185, '', '', 'Accepted', 1, '2022-05-17 11:41:38', '2022-05-17 11:41:38'),
(82, 162, 97, 150, '', '', 'Accepted', 1, '2022-05-17 13:32:16', '2022-05-17 13:32:16'),
(83, 165, 112, 500, 'cash', '', 'Accepted', 1, '2022-05-23 06:45:28', '2022-05-23 06:45:28'),
(84, 166, 114, 22, 'cash', '', 'Accepted', 1, '2022-05-23 12:14:27', '2022-05-23 12:14:27'),
(85, 167, 114, 22, 'cash', '', 'Accepted', 1, '2022-05-23 12:15:26', '2022-05-23 12:15:26'),
(86, 129, 114, 100, 'cash', '', 'Accepted', 1, '2022-05-23 13:27:43', '2022-05-23 13:27:43'),
(87, 135, 116, 500, 'cash', '', 'Accepted', 1, '2022-05-25 05:33:33', '2022-05-25 05:33:33'),
(88, 168, 116, 5, 'cash', '', 'Accepted', 1, '2022-05-25 05:36:03', '2022-05-25 05:36:03'),
(89, 136, 87, 15, 'cash', '', 'Accepted', 1, '2022-05-25 06:06:03', '2022-05-25 06:06:03'),
(90, 106, 87, 500, 'cash', '', 'Accepted', 1, '2022-05-25 06:08:54', '2022-05-25 06:08:54'),
(91, 107, 87, 500, 'cash', '', 'Accepted', 1, '2022-05-25 06:11:03', '2022-05-25 06:11:03'),
(92, 158, 87, 100, 'cash', '', 'Accepted', 1, '2022-05-25 06:16:58', '2022-05-25 06:16:58'),
(93, 161, 87, 185, 'cash', '', 'Accepted', 1, '2022-05-25 06:18:10', '2022-05-25 06:18:10'),
(94, 108, 87, 500, 'cash', '', 'Accepted', 1, '2022-05-25 06:59:52', '2022-05-25 06:59:52'),
(95, 160, 87, 22, 'cash', '', 'Accepted', 1, '2022-05-25 07:00:20', '2022-05-25 07:00:20'),
(96, 165, 87, 500, 'cash', '', 'Accepted', 1, '2022-05-25 07:00:53', '2022-05-25 07:00:53'),
(97, 166, 87, 22, 'cash', '', 'Accepted', 1, '2022-05-25 07:01:38', '2022-05-25 07:01:38'),
(98, 168, 87, 5, 'cash', '', 'Accepted', 1, '2022-05-25 07:02:17', '2022-05-25 07:02:17'),
(99, 167, 87, 22, 'cash', '', 'Accepted', 1, '2022-05-25 07:02:58', '2022-05-25 07:02:58'),
(100, 169, 87, 100, 'cash', '', 'Accepted', 1, '2022-05-25 07:21:09', '2022-05-25 07:21:09'),
(101, 170, 115, 5, 'cash', '', 'Accepted', 1, '2022-05-25 08:45:43', '2022-05-25 08:45:43'),
(102, 138, 71, 123, 'cash', '', 'Accepted', 1, '2022-05-26 04:56:30', '2022-05-26 04:56:30'),
(103, 171, 71, 5, 'cash', '', 'Accepted', 1, '2022-05-26 05:50:31', '2022-05-26 05:50:31'),
(104, 172, 71, 171, 'cash', '', 'Accepted', 1, '2022-05-26 09:25:09', '2022-05-26 09:25:09'),
(105, 173, 71, 5, 'cash', '', 'Accepted', 1, '2022-05-26 09:28:57', '2022-05-26 09:28:57'),
(106, 175, 102, 600, 'cash', '', 'Accepted', 1, '2022-05-26 12:51:03', '2022-05-26 12:51:03'),
(107, 176, 47, 5, 'cash', '', 'Accepted', 1, '2022-05-27 07:26:10', '2022-05-27 07:26:10'),
(108, 177, 87, 500, 'cash', '', 'Accepted', 1, '2022-05-27 09:03:30', '2022-05-27 09:03:30'),
(109, 178, 87, 500, 'online', '2234323424', 'Accepted', 1, '2022-05-27 09:06:29', '2022-05-27 09:06:29'),
(110, 179, 87, 15, 'cash', '', 'Accepted', 1, '2022-05-27 09:07:14', '2022-05-27 09:07:14'),
(111, 180, 87, 600, 'cash', '', 'Accepted', 1, '2022-05-27 10:04:28', '2022-05-27 10:04:28'),
(112, 181, 87, 500, 'cash', '', 'Accepted', 1, '2022-05-30 05:48:14', '2022-05-30 05:48:14'),
(113, 182, 87, 600, 'cash', '', 'Accepted', 1, '2022-05-30 05:49:19', '2022-05-30 05:49:19'),
(114, 185, 71, 1, 'cash', '', 'Accepted', 1, '2022-05-30 06:01:26', '2022-05-30 06:01:26'),
(115, 181, 49, 500, 'cash', '', 'Accepted', 1, '2022-05-30 06:02:41', '2022-05-30 06:02:41'),
(116, 182, 49, 600, 'cash', '', 'Accepted', 1, '2022-05-30 06:03:48', '2022-05-30 06:03:48'),
(117, 187, 47, 5, 'cash', '', 'Accepted', 1, '2022-05-30 09:07:47', '2022-05-30 09:07:47'),
(118, 188, 47, 100, 'cash', '', 'Accepted', 1, '2022-05-30 09:34:51', '2022-05-30 09:34:51'),
(119, 189, 47, 1, 'cash', '', 'Accepted', 1, '2022-05-30 09:35:09', '2022-05-30 09:35:09'),
(120, 190, 87, 456, 'cash', '', 'Accepted', 1, '2022-05-30 09:37:28', '2022-05-30 09:37:28'),
(121, 192, 49, 10, 'cash', '', 'Accepted', 1, '2022-05-30 10:38:20', '2022-05-30 10:38:20'),
(122, 194, 49, 1, 'cash', '', 'Accepted', 1, '2022-05-30 10:39:34', '2022-05-30 10:39:34'),
(123, 197, 87, 300, 'cash', '', 'Accepted', 1, '2022-05-30 10:49:02', '2022-05-30 10:49:02'),
(124, 198, 87, 2, 'cash', '', 'Accepted', 1, '2022-05-30 10:49:42', '2022-05-30 10:49:42'),
(125, 204, 121, 5, 'cash', '', 'Accepted', 1, '2022-05-30 11:11:18', '2022-05-30 11:11:18'),
(126, 210, 123, 123, 'cash', '', 'Accepted', 1, '2022-05-30 11:20:27', '2022-05-30 11:20:27'),
(127, 214, 124, 500, '', '', 'Accepted', 1, '2022-05-30 11:44:30', '2022-05-30 11:44:30'),
(128, 214, 97, 500, 'cash', '11123455587', 'Accepted', 1, '2022-05-30 11:55:40', '2022-05-30 11:55:40'),
(129, 218, 124, 5, 'cash', '', 'Accepted', 1, '2022-05-30 12:01:13', '2022-05-30 12:01:13'),
(130, 219, 124, 5, 'cash', '', 'Accepted', 1, '2022-05-30 12:02:22', '2022-05-30 12:02:22');

-- --------------------------------------------------------

--
-- Table structure for table `commissions`
--

CREATE TABLE `commissions` (
  `id` bigint NOT NULL,
  `facility_id` bigint DEFAULT NULL,
  `court_id` bigint DEFAULT NULL,
  `amount` int DEFAULT NULL COMMENT 'percentage',
  `status` tinyint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `commissions`
--

INSERT INTO `commissions` (`id`, `facility_id`, `court_id`, `amount`, `status`, `created_at`, `updated_at`) VALUES
(5, 8, 6, 22, 1, '2022-03-28 13:55:45', '2022-05-06 10:55:33'),
(6, 9, 8, 12, 1, '2022-03-30 13:14:46', '2022-05-06 10:56:16'),
(7, 12, 9, 30, 1, '2022-03-30 14:40:50', '2022-04-01 12:43:29'),
(8, 9, 7, 30, 1, '2022-03-30 14:40:50', '2022-05-06 10:55:53'),
(9, 12, 10, 10, 1, '2022-04-04 13:32:32', '2022-05-06 10:56:29'),
(10, 9, 11, 12, 1, '2022-04-05 07:32:15', '2022-04-05 07:32:52'),
(11, 15, 12, 12, 1, '2022-04-05 08:29:49', '2022-05-06 10:56:47'),
(12, 10, 13, 12, 1, '2022-04-05 08:33:42', '2022-05-06 10:16:25'),
(13, 11, 14, 12, 1, '2022-04-05 08:34:19', '2022-05-06 10:57:02'),
(14, 11, 15, 12, 1, '2022-04-05 08:35:00', '2022-05-06 10:57:24'),
(15, 13, 16, 12, 1, '2022-04-05 08:35:30', '2022-05-30 09:30:43'),
(16, 10, 17, 15, 1, '2022-04-05 08:36:02', '2022-05-10 06:14:49'),
(17, 15, 18, 40, 1, '2022-04-08 05:55:23', '2022-05-06 10:52:21'),
(18, 19, 19, 12, 0, '2022-04-08 06:12:43', '2022-04-18 09:34:08'),
(19, 19, 20, 15, 1, '2022-04-08 06:15:43', '2022-05-06 10:07:18'),
(20, 21, 21, 12, 1, '2022-04-08 10:55:16', '2022-05-06 10:55:10'),
(21, 22, 22, 10, 1, '2022-04-09 11:35:09', '2022-05-06 05:45:55'),
(22, 23, 23, 12, 1, '2022-04-13 10:51:25', '2022-05-06 10:11:35'),
(23, 23, 24, 12, 1, '2022-04-18 10:04:04', '2022-05-06 10:11:05'),
(24, 26, 25, 12, 1, '2022-04-18 10:06:15', '2022-05-06 10:10:41'),
(25, 19, 26, 20, 1, '2022-05-10 06:38:35', '2022-05-10 11:26:32');

-- --------------------------------------------------------

--
-- Table structure for table `contents`
--

CREATE TABLE `contents` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(50) DEFAULT NULL,
  `description` longtext NOT NULL,
  `status` tinyint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `contents`
--

INSERT INTO `contents` (`id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'About Us', 'about-us', '<p>Welcome To&nbsp;Tahadiyaat<br />\r\nTahadiyaat&nbsp;is a Professional&nbsp;Gaming&nbsp;Platform. Here we will provide you only interesting content, which you will like very much. We&#39;re dedicated to providing you the best of&nbsp;Gaming, with a focus on dependability and&nbsp;Make teams for playing game. We&#39;re working to turn our passion for&nbsp;Gaming&nbsp;into a booming&nbsp;<a href=\"http://www.tahadiyaat.com/\" target=\"_blank\">online website</a>. We hope you enjoy our&nbsp;Gaming&nbsp;as much as we enjoy offering them to you.<br />\r\nI will keep posting more important posts on my Website for all of you. Please give your support and love.<br />\r\n<strong>Thanks For Visiting Our Site</strong></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>Have a nice day !</strong></p>', 1, '2020-07-21 01:27:20', '2022-05-20 07:20:00'),
(7, 'Privacy Policy', 'private-policy', '<p>At Tahadiyaat, accessible from Www.tahadiyaat.com, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by Tahadiyaat and how we use it.</p>\r\n\r\n<p>If you have additional questions or require more information about our Privacy Policy, do not hesitate to contact us.</p>\r\n\r\n<p>This Privacy Policy applies only to our online activities and is valid for visitors to our website with regards to the information that they shared and/or collect in Tahadiyaat. This policy is not applicable to any information collected offline or via channels other than this website. Our Privacy Policy was created with the help of the <a href=\"https://www.termsfeed.com/privacy-policy-generator/\">Free Privacy Policy Generator</a>.</p>\r\n\r\n<h2>Consent</h2>\r\n\r\n<p>By using our website, you hereby consent to our Privacy Policy and agree to its terms.</p>\r\n\r\n<h2>Information we collect</h2>\r\n\r\n<p>The personal information that you are asked to provide, and the reasons why you are asked to provide it, will be made clear to you at the point we ask you to provide your personal information.</p>\r\n\r\n<p>If you contact us directly, we may receive additional information about you such as your name, email address, phone number, the contents of the message and/or attachments you may send us, and any other information you may choose to provide.</p>\r\n\r\n<p>When you register for an Account, we may ask for your contact information, including items such as name, company name, address, email address, and telephone number.</p>\r\n\r\n<h2>How we use your information</h2>\r\n\r\n<p>We use the information we collect in various ways, including to:</p>\r\n\r\n<ul>\r\n	<li>Provide, operate, and maintain our website</li>\r\n	<li>Improve, personalize, and expand our website</li>\r\n	<li>Understand and analyze how you use our website</li>\r\n	<li>Develop new products, services, features, and functionality</li>\r\n	<li>Communicate with you, either directly or through one of our partners, including for customer service, to provide you with updates and other information relating to the website, and for marketing and promotional purposes</li>\r\n	<li>Send you emails</li>\r\n	<li>Find and prevent fraud</li>\r\n</ul>\r\n\r\n<h2>Log Files</h2>\r\n\r\n<p>Tahadiyaat follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services&#39; analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. The purpose of the information is for analyzing trends, administering the site, tracking users&#39; movement on the website, and gathering demographic information.</p>\r\n\r\n<h2>Cookies and Web Beacons</h2>\r\n\r\n<p>Like any other website, Tahadiyaat uses &#39;cookies&#39;. These cookies are used to store information including visitors&#39; preferences, and the pages on the website that the visitor accessed or visited. The information is used to optimize the users&#39; experience by customizing our web page content based on visitors&#39; browser type and/or other information.</p>\r\n\r\n<p>For more general information on cookies, please read <a href=\"https://www.termsfeed.com/blog/sample-cookies-policy-template/#What_Are_Cookies\">the Cookies article on TermsFeed website</a>.</p>\r\n\r\n<h2>Advertising Partners Privacy Policies</h2>\r\n\r\n<p>You may consult this list to find the Privacy Policy for each of the advertising partners of Tahadiyaat.</p>\r\n\r\n<p>Third-party ad servers or ad networks uses technologies like cookies, JavaScript, or Web Beacons that are used in their respective advertisements and links that appear on Tahadiyaat, which are sent directly to users&#39; browser. They automatically receive your IP address when this occurs. These technologies are used to measure the effectiveness of their advertising campaigns and/or to personalize the advertising content that you see on websites that you visit.</p>\r\n\r\n<p>Note that Tahadiyaat has no access to or control over these cookies that are used by third-party advertisers.</p>\r\n\r\n<h2>Third Party Privacy Policies</h2>\r\n\r\n<p>Tahadiyaat&#39;s Privacy Policy does not apply to other advertisers or websites. Thus, we are advising you to consult the respective Privacy Policies of these third-party ad servers for more detailed information. It may include their practices and instructions about how to opt-out of certain options.</p>\r\n\r\n<p>You can choose to disable cookies through your individual browser options. To know more detailed information about cookie management with specific web browsers, it can be found at the browsers&#39; respective websites.</p>\r\n\r\n<h2>CCPA Privacy Rights (Do Not Sell My Personal Information)</h2>\r\n\r\n<p>Under the CCPA, among other rights, California consumers have the right to:</p>\r\n\r\n<p>Request that a business that collects a consumer&#39;s personal data disclose the categories and specific pieces of personal data that a business has collected about consumers.</p>\r\n\r\n<p>Request that a business delete any personal data about the consumer that a business has collected.</p>\r\n\r\n<p>Request that a business that sells a consumer&#39;s personal data, not sell the consumer&#39;s personal data.</p>\r\n\r\n<p>If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.</p>\r\n\r\n<h2>GDPR Data Protection Rights</h2>\r\n\r\n<p>We would like to make sure you are fully aware of all of your data protection rights. Every user is entitled to the following:</p>\r\n\r\n<p>The right to access &ndash; You have the right to request copies of your personal data. We may charge you a small fee for this service.</p>\r\n\r\n<p>The right to rectification &ndash; You have the right to request that we correct any information you believe is inaccurate. You also have the right to request that we complete the information you believe is incomplete.</p>\r\n\r\n<p>The right to erasure &ndash; You have the right to request that we erase your personal data, under certain conditions.</p>\r\n\r\n<p>The right to restrict processing &ndash; You have the right to request that we restrict the processing of your personal data, under certain conditions.</p>\r\n\r\n<p>The right to object to processing &ndash; You have the right to object to our processing of your personal data, under certain conditions.</p>\r\n\r\n<p>The right to data portability &ndash; You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions.</p>\r\n\r\n<p>If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.</p>\r\n\r\n<h2>Children&#39;s Information</h2>\r\n\r\n<p>Another part of our priority is adding protection for children while using the internet. We encourage parents and guardians to observe, participate in, and/or monitor and guide their online activity.</p>\r\n\r\n<p>Tahadiyaat does not knowingly collect any Personal Identifiable Information from children under the age of 13. If you think that your child provided this kind of information on our website, we strongly encourage you to contact us immediately and we will do our best efforts to promptly remove such information from our records.</p>', 1, '2020-07-21 04:55:05', '2022-05-20 07:19:19'),
(8, 'Terms of use', 'terms-of-use', '<p>Welcome to Tahadiyaat!</p>\r\n\r\n<p>These terms and conditions outline the rules and regulations for the use of Tahadiyaat&#39;s Website, located at Www.tahadiyaat.com.</p>\r\n\r\n<p>By accessing this website we assume you accept these terms and conditions. Do not continue to use Tahadiyaat if you do not agree to take all of the terms and conditions stated on this page.</p>\r\n\r\n<p>The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: &quot;Client&quot;, &quot;You&quot; and &quot;Your&quot; refers to you, the person log on this website and compliant to the Company&rsquo;s terms and conditions. &quot;The Company&quot;, &quot;Ourselves&quot;, &quot;We&quot;, &quot;Our&quot; and &quot;Us&quot;, refers to our Company. &quot;Party&quot;, &quot;Parties&quot;, or &quot;Us&quot;, refers to both the Client and ourselves. All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client&rsquo;s needs in respect of provision of the Company&rsquo;s stated services, in accordance with and subject to, prevailing law of Netherlands. Any use of the above terminology or other words in the singular, plural, capitalization and/or he/she or they, are taken as interchangeable and therefore as referring to same.</p>\r\n\r\n<h3><strong>Cookies</strong></h3>\r\n\r\n<p>We employ the use of cookies. By accessing Tahadiyaat, you agreed to use cookies in agreement with the Tahadiyaat&#39;s Privacy Policy.</p>\r\n\r\n<p>Most interactive websites use cookies to let us retrieve the user&rsquo;s details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate/advertising partners may also use cookies.</p>\r\n\r\n<h3><strong>License</strong></h3>\r\n\r\n<p>Unless otherwise stated, Tahadiyaat and/or its licensors own the intellectual property rights for all material on Tahadiyaat. All intellectual property rights are reserved. You may access this from Tahadiyaat for your own personal use subjected to restrictions set in these terms and conditions.</p>\r\n\r\n<p>You must not:</p>\r\n\r\n<ul>\r\n	<li>Republish material from Tahadiyaat</li>\r\n	<li>Sell, rent or sub-license material from Tahadiyaat</li>\r\n	<li>Reproduce, duplicate or copy material from Tahadiyaat</li>\r\n	<li>Redistribute content from Tahadiyaat</li>\r\n</ul>\r\n\r\n<p>This Agreement shall begin on the date hereof. Our Terms and Conditions were created with the help of the <a href=\"https://www.privacypolicies.com/blog/sample-terms-conditions-template/\">Terms And Conditions Template</a>.</p>\r\n\r\n<p>Parts of this website offer an opportunity for users to post and exchange opinions and information in certain areas of the website. Tahadiyaat does not filter, edit, publish or review Comments prior to their presence on the website. Comments do not reflect the views and opinions of Tahadiyaat,its agents and/or affiliates. Comments reflect the views and opinions of the person who post their views and opinions. To the extent permitted by applicable laws, Tahadiyaat shall not be liable for the Comments or for any liability, damages or expenses caused and/or suffered as a result of any use of and/or posting of and/or appearance of the Comments on this website.</p>\r\n\r\n<p>Tahadiyaat reserves the right to monitor all Comments and to remove any Comments which can be considered inappropriate, offensive or causes breach of these Terms and Conditions.</p>\r\n\r\n<p>You warrant and represent that:</p>\r\n\r\n<ul>\r\n	<li>You are entitled to post the Comments on our website and have all necessary licenses and consents to do so;</li>\r\n	<li>The Comments do not invade any intellectual property right, including without limitation copyright, patent or trademark of any third party;</li>\r\n	<li>The Comments do not contain any defamatory, libelous, offensive, indecent or otherwise unlawful material which is an invasion of privacy</li>\r\n	<li>The Comments will not be used to solicit or promote business or custom or present commercial activities or unlawful activity.</li>\r\n</ul>\r\n\r\n<p>You hereby grant Tahadiyaat a non-exclusive license to use, reproduce, edit and authorize others to use, reproduce and edit any of your Comments in any and all forms, formats or media.</p>\r\n\r\n<h3><strong>Hyperlinking to our Content</strong></h3>\r\n\r\n<p>The following organizations may link to our Website without prior written approval:</p>\r\n\r\n<ul>\r\n	<li>Government agencies;</li>\r\n	<li>Search engines;</li>\r\n	<li>News organizations;</li>\r\n	<li>Online directory distributors may link to our Website in the same manner as they hyperlink to the Websites of other listed businesses; and</li>\r\n	<li>System wide Accredited Businesses except soliciting non-profit organizations, charity shopping malls, and charity fundraising groups which may not hyperlink to our Web site.</li>\r\n</ul>\r\n\r\n<p>These organizations may link to our home page, to publications or to other Website information so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products and/or services; and (c) fits within the context of the linking party&rsquo;s site.</p>\r\n\r\n<p>We may consider and approve other link requests from the following types of organizations:</p>\r\n\r\n<ul>\r\n	<li>commonly-known consumer and/or business information sources;</li>\r\n	<li>dot.com community sites;</li>\r\n	<li>associations or other groups representing charities;</li>\r\n	<li>online directory distributors;</li>\r\n	<li>internet portals;</li>\r\n	<li>accounting, law and consulting firms; and</li>\r\n	<li>educational institutions and trade associations.</li>\r\n</ul>\r\n\r\n<p>We will approve link requests from these organizations if we decide that: (a) the link would not make us look unfavorably to ourselves or to our accredited businesses; (b) the organization does not have any negative records with us; (c) the benefit to us from the visibility of the hyperlink compensates the absence of Tahadiyaat; and (d) the link is in the context of general resource information.</p>\r\n\r\n<p>These organizations may link to our home page so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products or services; and (c) fits within the context of the linking party&rsquo;s site.</p>\r\n\r\n<p>If you are one of the organizations listed in paragraph 2 above and are interested in linking to our website, you must inform us by sending an e-mail to Tahadiyaat. Please include your name, your organization name, contact information as well as the URL of your site, a list of any URLs from which you intend to link to our Website, and a list of the URLs on our site to which you would like to link. Wait 2-3 weeks for a response.</p>\r\n\r\n<p>Approved organizations may hyperlink to our Website as follows:</p>\r\n\r\n<ul>\r\n	<li>By use of our corporate name; or</li>\r\n	<li>By use of the uniform resource locator being linked to; or</li>\r\n	<li>By use of any other description of our Website being linked to that makes sense within the context and format of content on the linking party&rsquo;s site.</li>\r\n</ul>\r\n\r\n<p>No use of Tahadiyaat&#39;s logo or other artwork will be allowed for linking absent a trademark license agreement.</p>\r\n\r\n<h3><strong>iFrames</strong></h3>\r\n\r\n<p>Without prior approval and written permission, you may not create frames around our Webpages that alter in any way the visual presentation or appearance of our Website.</p>\r\n\r\n<h3><strong>Content Liability</strong></h3>\r\n\r\n<p>We shall not be hold responsible for any content that appears on your Website. You agree to protect and defend us against all claims that is rising on your Website. No link(s) should appear on any Website that may be interpreted as libelous, obscene or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of, any third party rights.</p>\r\n\r\n<h3><strong>Your Privacy</strong></h3>\r\n\r\n<p>Please read Privacy Policy</p>\r\n\r\n<h3><strong>Reservation of Rights</strong></h3>\r\n\r\n<p>We reserve the right to request that you remove all links or any particular link to our Website. You approve to immediately remove all links to our Website upon request. We also reserve the right to amen these terms and conditions and it&rsquo;s linking policy at any time. By continuously linking to our Website, you agree to be bound to and follow these linking terms and conditions.</p>\r\n\r\n<h3><strong>Removal of links from our website</strong></h3>\r\n\r\n<p>If you find any link on our Website that is offensive for any reason, you are free to contact and inform us any moment. We will consider requests to remove links but we are not obligated to or so or to respond to you directly.</p>\r\n\r\n<p>We do not ensure that the information on this website is correct, we do not warrant its completeness or accuracy; nor do we promise to ensure that the website remains available or that the material on the website is kept up to date.</p>\r\n\r\n<h3><strong>Disclaimer</strong></h3>\r\n\r\n<p>To the maximum extent permitted by applicable law, we exclude all representations, warranties and conditions relating to our website and the use of this website. Nothing in this disclaimer will:</p>\r\n\r\n<ul>\r\n	<li>limit or exclude our or your liability for death or personal injury;</li>\r\n	<li>limit or exclude our or your liability for fraud or fraudulent misrepresentation;</li>\r\n	<li>limit any of our or your liabilities in any way that is not permitted under applicable law; or</li>\r\n	<li>exclude any of our or your liabilities that may not be excluded under applicable law.</li>\r\n</ul>\r\n\r\n<p>The limitations and prohibitions of liability set in this Section and elsewhere in this disclaimer: (a) are subject to the preceding paragraph; and (b) govern all liabilities arising under the disclaimer, including liabilities arising in contract, in tort and for breach of statutory duty.</p>\r\n\r\n<p>As long as the website and the information and services on the website are provided free of charge, we will not be liable for any loss or damage of any nature.</p>', 1, '2020-07-21 04:55:05', '2022-05-20 07:19:35'),
(9, 'how_its_work', 'how_its_work', '<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>\r\n\r\n<h2>Why do we use it?</h2>\r\n\r\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using &#39;Content here, content here&#39;, making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for &#39;lorem ipsum&#39; will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h2>Where does it come from?</h2>\r\n\r\n<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of &quot;de Finibus Bonorum et Malorum&quot; (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, &quot;Lorem ipsum dolor sit amet..&quot;, comes from a line in section 1.10.32.</p>\r\n\r\n<p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from &quot;de Finibus Bonorum et Malorum&quot; by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p>\r\n\r\n<h2>Where can I get some?</h2>\r\n\r\n<p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don&#39;t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn&#39;t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p>', 1, '2020-07-21 04:55:05', '2022-05-10 11:14:21');

-- --------------------------------------------------------

--
-- Table structure for table `contents_lang`
--

CREATE TABLE `contents_lang` (
  `id` int NOT NULL,
  `content_id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` longtext,
  `lang` varchar(10) NOT NULL DEFAULT 'en',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `contents_lang`
--

INSERT INTO `contents_lang` (`id`, `content_id`, `name`, `description`, `lang`, `created_at`, `updated_at`) VALUES
(1, 1, 'About Us', '<p>Welcome To&nbsp;Tahadiyaat<br />\r\nTahadiyaat&nbsp;is a Professional&nbsp;Gaming&nbsp;Platform. Here we will provide you only interesting content, which you will like very much. We&#39;re dedicated to providing you the best of&nbsp;Gaming, with a focus on dependability and&nbsp;Make teams for playing game. We&#39;re working to turn our passion for&nbsp;Gaming&nbsp;into a booming&nbsp;<a href=\"http://www.tahadiyaat.com/\" target=\"_blank\">online website</a>. We hope you enjoy our&nbsp;Gaming&nbsp;as much as we enjoy offering them to you.<br />\r\nI will keep posting more important posts on my Website for all of you. Please give your support and love.<br />\r\n<strong>Thanks For Visiting Our Site</strong></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>Have a nice day !</strong></p>', 'en', '2020-08-21 12:50:31', '2022-05-20 07:20:00'),
(2, 1, 'About Us', '<p>Welcome To&nbsp;Tahadiyaat<br />\r\nTahadiyaat&nbsp;is a Professional&nbsp;Gaming&nbsp;Platform. Here we will provide you only interesting content, which you will like very much. We&#39;re dedicated to providing you the best of&nbsp;Gaming, with a focus on dependability and&nbsp;Make teams for playing game. We&#39;re working to turn our passion for&nbsp;Gaming&nbsp;into a booming&nbsp;<a href=\"http://www.tahadiyaat.com/\" target=\"_blank\">online website</a>. We hope you enjoy our&nbsp;Gaming&nbsp;as much as we enjoy offering them to you.<br />\r\nI will keep posting more important posts on my Website for all of you. Please give your support and love.<br />\r\n<strong>Thanks For Visiting Our Site</strong></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>Have a nice day !</strong></p>', 'ar', '2020-08-21 12:50:31', '2022-05-20 07:20:00'),
(3, 7, 'Privacy Policy', '<p>At Tahadiyaat, accessible from Www.tahadiyaat.com, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by Tahadiyaat and how we use it.</p>\r\n\r\n<p>If you have additional questions or require more information about our Privacy Policy, do not hesitate to contact us.</p>\r\n\r\n<p>This Privacy Policy applies only to our online activities and is valid for visitors to our website with regards to the information that they shared and/or collect in Tahadiyaat. This policy is not applicable to any information collected offline or via channels other than this website. Our Privacy Policy was created with the help of the <a href=\"https://www.termsfeed.com/privacy-policy-generator/\">Free Privacy Policy Generator</a>.</p>\r\n\r\n<h2>Consent</h2>\r\n\r\n<p>By using our website, you hereby consent to our Privacy Policy and agree to its terms.</p>\r\n\r\n<h2>Information we collect</h2>\r\n\r\n<p>The personal information that you are asked to provide, and the reasons why you are asked to provide it, will be made clear to you at the point we ask you to provide your personal information.</p>\r\n\r\n<p>If you contact us directly, we may receive additional information about you such as your name, email address, phone number, the contents of the message and/or attachments you may send us, and any other information you may choose to provide.</p>\r\n\r\n<p>When you register for an Account, we may ask for your contact information, including items such as name, company name, address, email address, and telephone number.</p>\r\n\r\n<h2>How we use your information</h2>\r\n\r\n<p>We use the information we collect in various ways, including to:</p>\r\n\r\n<ul>\r\n	<li>Provide, operate, and maintain our website</li>\r\n	<li>Improve, personalize, and expand our website</li>\r\n	<li>Understand and analyze how you use our website</li>\r\n	<li>Develop new products, services, features, and functionality</li>\r\n	<li>Communicate with you, either directly or through one of our partners, including for customer service, to provide you with updates and other information relating to the website, and for marketing and promotional purposes</li>\r\n	<li>Send you emails</li>\r\n	<li>Find and prevent fraud</li>\r\n</ul>\r\n\r\n<h2>Log Files</h2>\r\n\r\n<p>Tahadiyaat follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services&#39; analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. The purpose of the information is for analyzing trends, administering the site, tracking users&#39; movement on the website, and gathering demographic information.</p>\r\n\r\n<h2>Cookies and Web Beacons</h2>\r\n\r\n<p>Like any other website, Tahadiyaat uses &#39;cookies&#39;. These cookies are used to store information including visitors&#39; preferences, and the pages on the website that the visitor accessed or visited. The information is used to optimize the users&#39; experience by customizing our web page content based on visitors&#39; browser type and/or other information.</p>\r\n\r\n<p>For more general information on cookies, please read <a href=\"https://www.termsfeed.com/blog/sample-cookies-policy-template/#What_Are_Cookies\">the Cookies article on TermsFeed website</a>.</p>\r\n\r\n<h2>Advertising Partners Privacy Policies</h2>\r\n\r\n<p>You may consult this list to find the Privacy Policy for each of the advertising partners of Tahadiyaat.</p>\r\n\r\n<p>Third-party ad servers or ad networks uses technologies like cookies, JavaScript, or Web Beacons that are used in their respective advertisements and links that appear on Tahadiyaat, which are sent directly to users&#39; browser. They automatically receive your IP address when this occurs. These technologies are used to measure the effectiveness of their advertising campaigns and/or to personalize the advertising content that you see on websites that you visit.</p>\r\n\r\n<p>Note that Tahadiyaat has no access to or control over these cookies that are used by third-party advertisers.</p>\r\n\r\n<h2>Third Party Privacy Policies</h2>\r\n\r\n<p>Tahadiyaat&#39;s Privacy Policy does not apply to other advertisers or websites. Thus, we are advising you to consult the respective Privacy Policies of these third-party ad servers for more detailed information. It may include their practices and instructions about how to opt-out of certain options.</p>\r\n\r\n<p>You can choose to disable cookies through your individual browser options. To know more detailed information about cookie management with specific web browsers, it can be found at the browsers&#39; respective websites.</p>\r\n\r\n<h2>CCPA Privacy Rights (Do Not Sell My Personal Information)</h2>\r\n\r\n<p>Under the CCPA, among other rights, California consumers have the right to:</p>\r\n\r\n<p>Request that a business that collects a consumer&#39;s personal data disclose the categories and specific pieces of personal data that a business has collected about consumers.</p>\r\n\r\n<p>Request that a business delete any personal data about the consumer that a business has collected.</p>\r\n\r\n<p>Request that a business that sells a consumer&#39;s personal data, not sell the consumer&#39;s personal data.</p>\r\n\r\n<p>If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.</p>\r\n\r\n<h2>GDPR Data Protection Rights</h2>\r\n\r\n<p>We would like to make sure you are fully aware of all of your data protection rights. Every user is entitled to the following:</p>\r\n\r\n<p>The right to access &ndash; You have the right to request copies of your personal data. We may charge you a small fee for this service.</p>\r\n\r\n<p>The right to rectification &ndash; You have the right to request that we correct any information you believe is inaccurate. You also have the right to request that we complete the information you believe is incomplete.</p>\r\n\r\n<p>The right to erasure &ndash; You have the right to request that we erase your personal data, under certain conditions.</p>\r\n\r\n<p>The right to restrict processing &ndash; You have the right to request that we restrict the processing of your personal data, under certain conditions.</p>\r\n\r\n<p>The right to object to processing &ndash; You have the right to object to our processing of your personal data, under certain conditions.</p>\r\n\r\n<p>The right to data portability &ndash; You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions.</p>\r\n\r\n<p>If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.</p>\r\n\r\n<h2>Children&#39;s Information</h2>\r\n\r\n<p>Another part of our priority is adding protection for children while using the internet. We encourage parents and guardians to observe, participate in, and/or monitor and guide their online activity.</p>\r\n\r\n<p>Tahadiyaat does not knowingly collect any Personal Identifiable Information from children under the age of 13. If you think that your child provided this kind of information on our website, we strongly encourage you to contact us immediately and we will do our best efforts to promptly remove such information from our records.</p>', 'en', '2020-08-21 12:51:36', '2022-05-20 07:19:19'),
(4, 7, 'Privacy Policy', '<p>At Tahadiyaat, accessible from Www.tahadiyaat.com, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by Tahadiyaat and how we use it.</p>\r\n\r\n<p>If you have additional questions or require more information about our Privacy Policy, do not hesitate to contact us.</p>\r\n\r\n<p>This Privacy Policy applies only to our online activities and is valid for visitors to our website with regards to the information that they shared and/or collect in Tahadiyaat. This policy is not applicable to any information collected offline or via channels other than this website. Our Privacy Policy was created with the help of the <a href=\"https://www.termsfeed.com/privacy-policy-generator/\">Free Privacy Policy Generator</a>.</p>\r\n\r\n<h2>Consent</h2>\r\n\r\n<p>By using our website, you hereby consent to our Privacy Policy and agree to its terms.</p>\r\n\r\n<h2>Information we collect</h2>\r\n\r\n<p>The personal information that you are asked to provide, and the reasons why you are asked to provide it, will be made clear to you at the point we ask you to provide your personal information.</p>\r\n\r\n<p>If you contact us directly, we may receive additional information about you such as your name, email address, phone number, the contents of the message and/or attachments you may send us, and any other information you may choose to provide.</p>\r\n\r\n<p>When you register for an Account, we may ask for your contact information, including items such as name, company name, address, email address, and telephone number.</p>\r\n\r\n<h2>How we use your information</h2>\r\n\r\n<p>We use the information we collect in various ways, including to:</p>\r\n\r\n<ul>\r\n	<li>Provide, operate, and maintain our website</li>\r\n	<li>Improve, personalize, and expand our website</li>\r\n	<li>Understand and analyze how you use our website</li>\r\n	<li>Develop new products, services, features, and functionality</li>\r\n	<li>Communicate with you, either directly or through one of our partners, including for customer service, to provide you with updates and other information relating to the website, and for marketing and promotional purposes</li>\r\n	<li>Send you emails</li>\r\n	<li>Find and prevent fraud</li>\r\n</ul>\r\n\r\n<h2>Log Files</h2>\r\n\r\n<p>Tahadiyaat follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services&#39; analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. The purpose of the information is for analyzing trends, administering the site, tracking users&#39; movement on the website, and gathering demographic information.</p>\r\n\r\n<h2>Cookies and Web Beacons</h2>\r\n\r\n<p>Like any other website, Tahadiyaat uses &#39;cookies&#39;. These cookies are used to store information including visitors&#39; preferences, and the pages on the website that the visitor accessed or visited. The information is used to optimize the users&#39; experience by customizing our web page content based on visitors&#39; browser type and/or other information.</p>\r\n\r\n<p>For more general information on cookies, please read <a href=\"https://www.termsfeed.com/blog/sample-cookies-policy-template/#What_Are_Cookies\">the Cookies article on TermsFeed website</a>.</p>\r\n\r\n<h2>Advertising Partners Privacy Policies</h2>\r\n\r\n<p>You may consult this list to find the Privacy Policy for each of the advertising partners of Tahadiyaat.</p>\r\n\r\n<p>Third-party ad servers or ad networks uses technologies like cookies, JavaScript, or Web Beacons that are used in their respective advertisements and links that appear on Tahadiyaat, which are sent directly to users&#39; browser. They automatically receive your IP address when this occurs. These technologies are used to measure the effectiveness of their advertising campaigns and/or to personalize the advertising content that you see on websites that you visit.</p>\r\n\r\n<p>Note that Tahadiyaat has no access to or control over these cookies that are used by third-party advertisers.</p>\r\n\r\n<h2>Third Party Privacy Policies</h2>\r\n\r\n<p>Tahadiyaat&#39;s Privacy Policy does not apply to other advertisers or websites. Thus, we are advising you to consult the respective Privacy Policies of these third-party ad servers for more detailed information. It may include their practices and instructions about how to opt-out of certain options.</p>\r\n\r\n<p>You can choose to disable cookies through your individual browser options. To know more detailed information about cookie management with specific web browsers, it can be found at the browsers&#39; respective websites.</p>\r\n\r\n<h2>CCPA Privacy Rights (Do Not Sell My Personal Information)</h2>\r\n\r\n<p>Under the CCPA, among other rights, California consumers have the right to:</p>\r\n\r\n<p>Request that a business that collects a consumer&#39;s personal data disclose the categories and specific pieces of personal data that a business has collected about consumers.</p>\r\n\r\n<p>Request that a business delete any personal data about the consumer that a business has collected.</p>\r\n\r\n<p>Request that a business that sells a consumer&#39;s personal data, not sell the consumer&#39;s personal data.</p>\r\n\r\n<p>If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.</p>\r\n\r\n<h2>GDPR Data Protection Rights</h2>\r\n\r\n<p>We would like to make sure you are fully aware of all of your data protection rights. Every user is entitled to the following:</p>\r\n\r\n<p>The right to access &ndash; You have the right to request copies of your personal data. We may charge you a small fee for this service.</p>\r\n\r\n<p>The right to rectification &ndash; You have the right to request that we correct any information you believe is inaccurate. You also have the right to request that we complete the information you believe is incomplete.</p>\r\n\r\n<p>The right to erasure &ndash; You have the right to request that we erase your personal data, under certain conditions.</p>\r\n\r\n<p>The right to restrict processing &ndash; You have the right to request that we restrict the processing of your personal data, under certain conditions.</p>\r\n\r\n<p>The right to object to processing &ndash; You have the right to object to our processing of your personal data, under certain conditions.</p>\r\n\r\n<p>The right to data portability &ndash; You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions.</p>\r\n\r\n<p>If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.</p>\r\n\r\n<h2>Children&#39;s Information</h2>\r\n\r\n<p>Another part of our priority is adding protection for children while using the internet. We encourage parents and guardians to observe, participate in, and/or monitor and guide their online activity.</p>\r\n\r\n<p>Tahadiyaat does not knowingly collect any Personal Identifiable Information from children under the age of 13. If you think that your child provided this kind of information on our website, we strongly encourage you to contact us immediately and we will do our best efforts to promptly remove such information from our records.</p>', 'ar', '2020-08-21 12:51:36', '2022-05-20 07:19:19'),
(5, 8, 'Terms of use', '<p>Welcome to Tahadiyaat!</p>\r\n\r\n<p>These terms and conditions outline the rules and regulations for the use of Tahadiyaat&#39;s Website, located at Www.tahadiyaat.com.</p>\r\n\r\n<p>By accessing this website we assume you accept these terms and conditions. Do not continue to use Tahadiyaat if you do not agree to take all of the terms and conditions stated on this page.</p>\r\n\r\n<p>The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: &quot;Client&quot;, &quot;You&quot; and &quot;Your&quot; refers to you, the person log on this website and compliant to the Company&rsquo;s terms and conditions. &quot;The Company&quot;, &quot;Ourselves&quot;, &quot;We&quot;, &quot;Our&quot; and &quot;Us&quot;, refers to our Company. &quot;Party&quot;, &quot;Parties&quot;, or &quot;Us&quot;, refers to both the Client and ourselves. All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client&rsquo;s needs in respect of provision of the Company&rsquo;s stated services, in accordance with and subject to, prevailing law of Netherlands. Any use of the above terminology or other words in the singular, plural, capitalization and/or he/she or they, are taken as interchangeable and therefore as referring to same.</p>\r\n\r\n<h3><strong>Cookies</strong></h3>\r\n\r\n<p>We employ the use of cookies. By accessing Tahadiyaat, you agreed to use cookies in agreement with the Tahadiyaat&#39;s Privacy Policy.</p>\r\n\r\n<p>Most interactive websites use cookies to let us retrieve the user&rsquo;s details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate/advertising partners may also use cookies.</p>\r\n\r\n<h3><strong>License</strong></h3>\r\n\r\n<p>Unless otherwise stated, Tahadiyaat and/or its licensors own the intellectual property rights for all material on Tahadiyaat. All intellectual property rights are reserved. You may access this from Tahadiyaat for your own personal use subjected to restrictions set in these terms and conditions.</p>\r\n\r\n<p>You must not:</p>\r\n\r\n<ul>\r\n	<li>Republish material from Tahadiyaat</li>\r\n	<li>Sell, rent or sub-license material from Tahadiyaat</li>\r\n	<li>Reproduce, duplicate or copy material from Tahadiyaat</li>\r\n	<li>Redistribute content from Tahadiyaat</li>\r\n</ul>\r\n\r\n<p>This Agreement shall begin on the date hereof. Our Terms and Conditions were created with the help of the <a href=\"https://www.privacypolicies.com/blog/sample-terms-conditions-template/\">Terms And Conditions Template</a>.</p>\r\n\r\n<p>Parts of this website offer an opportunity for users to post and exchange opinions and information in certain areas of the website. Tahadiyaat does not filter, edit, publish or review Comments prior to their presence on the website. Comments do not reflect the views and opinions of Tahadiyaat,its agents and/or affiliates. Comments reflect the views and opinions of the person who post their views and opinions. To the extent permitted by applicable laws, Tahadiyaat shall not be liable for the Comments or for any liability, damages or expenses caused and/or suffered as a result of any use of and/or posting of and/or appearance of the Comments on this website.</p>\r\n\r\n<p>Tahadiyaat reserves the right to monitor all Comments and to remove any Comments which can be considered inappropriate, offensive or causes breach of these Terms and Conditions.</p>\r\n\r\n<p>You warrant and represent that:</p>\r\n\r\n<ul>\r\n	<li>You are entitled to post the Comments on our website and have all necessary licenses and consents to do so;</li>\r\n	<li>The Comments do not invade any intellectual property right, including without limitation copyright, patent or trademark of any third party;</li>\r\n	<li>The Comments do not contain any defamatory, libelous, offensive, indecent or otherwise unlawful material which is an invasion of privacy</li>\r\n	<li>The Comments will not be used to solicit or promote business or custom or present commercial activities or unlawful activity.</li>\r\n</ul>\r\n\r\n<p>You hereby grant Tahadiyaat a non-exclusive license to use, reproduce, edit and authorize others to use, reproduce and edit any of your Comments in any and all forms, formats or media.</p>\r\n\r\n<h3><strong>Hyperlinking to our Content</strong></h3>\r\n\r\n<p>The following organizations may link to our Website without prior written approval:</p>\r\n\r\n<ul>\r\n	<li>Government agencies;</li>\r\n	<li>Search engines;</li>\r\n	<li>News organizations;</li>\r\n	<li>Online directory distributors may link to our Website in the same manner as they hyperlink to the Websites of other listed businesses; and</li>\r\n	<li>System wide Accredited Businesses except soliciting non-profit organizations, charity shopping malls, and charity fundraising groups which may not hyperlink to our Web site.</li>\r\n</ul>\r\n\r\n<p>These organizations may link to our home page, to publications or to other Website information so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products and/or services; and (c) fits within the context of the linking party&rsquo;s site.</p>\r\n\r\n<p>We may consider and approve other link requests from the following types of organizations:</p>\r\n\r\n<ul>\r\n	<li>commonly-known consumer and/or business information sources;</li>\r\n	<li>dot.com community sites;</li>\r\n	<li>associations or other groups representing charities;</li>\r\n	<li>online directory distributors;</li>\r\n	<li>internet portals;</li>\r\n	<li>accounting, law and consulting firms; and</li>\r\n	<li>educational institutions and trade associations.</li>\r\n</ul>\r\n\r\n<p>We will approve link requests from these organizations if we decide that: (a) the link would not make us look unfavorably to ourselves or to our accredited businesses; (b) the organization does not have any negative records with us; (c) the benefit to us from the visibility of the hyperlink compensates the absence of Tahadiyaat; and (d) the link is in the context of general resource information.</p>\r\n\r\n<p>These organizations may link to our home page so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products or services; and (c) fits within the context of the linking party&rsquo;s site.</p>\r\n\r\n<p>If you are one of the organizations listed in paragraph 2 above and are interested in linking to our website, you must inform us by sending an e-mail to Tahadiyaat. Please include your name, your organization name, contact information as well as the URL of your site, a list of any URLs from which you intend to link to our Website, and a list of the URLs on our site to which you would like to link. Wait 2-3 weeks for a response.</p>\r\n\r\n<p>Approved organizations may hyperlink to our Website as follows:</p>\r\n\r\n<ul>\r\n	<li>By use of our corporate name; or</li>\r\n	<li>By use of the uniform resource locator being linked to; or</li>\r\n	<li>By use of any other description of our Website being linked to that makes sense within the context and format of content on the linking party&rsquo;s site.</li>\r\n</ul>\r\n\r\n<p>No use of Tahadiyaat&#39;s logo or other artwork will be allowed for linking absent a trademark license agreement.</p>\r\n\r\n<h3><strong>iFrames</strong></h3>\r\n\r\n<p>Without prior approval and written permission, you may not create frames around our Webpages that alter in any way the visual presentation or appearance of our Website.</p>\r\n\r\n<h3><strong>Content Liability</strong></h3>\r\n\r\n<p>We shall not be hold responsible for any content that appears on your Website. You agree to protect and defend us against all claims that is rising on your Website. No link(s) should appear on any Website that may be interpreted as libelous, obscene or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of, any third party rights.</p>\r\n\r\n<h3><strong>Your Privacy</strong></h3>\r\n\r\n<p>Please read Privacy Policy</p>\r\n\r\n<h3><strong>Reservation of Rights</strong></h3>\r\n\r\n<p>We reserve the right to request that you remove all links or any particular link to our Website. You approve to immediately remove all links to our Website upon request. We also reserve the right to amen these terms and conditions and it&rsquo;s linking policy at any time. By continuously linking to our Website, you agree to be bound to and follow these linking terms and conditions.</p>\r\n\r\n<h3><strong>Removal of links from our website</strong></h3>\r\n\r\n<p>If you find any link on our Website that is offensive for any reason, you are free to contact and inform us any moment. We will consider requests to remove links but we are not obligated to or so or to respond to you directly.</p>\r\n\r\n<p>We do not ensure that the information on this website is correct, we do not warrant its completeness or accuracy; nor do we promise to ensure that the website remains available or that the material on the website is kept up to date.</p>\r\n\r\n<h3><strong>Disclaimer</strong></h3>\r\n\r\n<p>To the maximum extent permitted by applicable law, we exclude all representations, warranties and conditions relating to our website and the use of this website. Nothing in this disclaimer will:</p>\r\n\r\n<ul>\r\n	<li>limit or exclude our or your liability for death or personal injury;</li>\r\n	<li>limit or exclude our or your liability for fraud or fraudulent misrepresentation;</li>\r\n	<li>limit any of our or your liabilities in any way that is not permitted under applicable law; or</li>\r\n	<li>exclude any of our or your liabilities that may not be excluded under applicable law.</li>\r\n</ul>\r\n\r\n<p>The limitations and prohibitions of liability set in this Section and elsewhere in this disclaimer: (a) are subject to the preceding paragraph; and (b) govern all liabilities arising under the disclaimer, including liabilities arising in contract, in tort and for breach of statutory duty.</p>\r\n\r\n<p>As long as the website and the information and services on the website are provided free of charge, we will not be liable for any loss or damage of any nature.</p>', 'en', '2020-08-21 12:51:36', '2022-05-20 07:19:36'),
(6, 8, 'Terms of use', '<p>Welcome to Tahadiyaat!</p>\r\n\r\n<p>These terms and conditions outline the rules and regulations for the use of Tahadiyaat&#39;s Website, located at Www.tahadiyaat.com.</p>\r\n\r\n<p>By accessing this website we assume you accept these terms and conditions. Do not continue to use Tahadiyaat if you do not agree to take all of the terms and conditions stated on this page.</p>\r\n\r\n<p>The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: &quot;Client&quot;, &quot;You&quot; and &quot;Your&quot; refers to you, the person log on this website and compliant to the Company&rsquo;s terms and conditions. &quot;The Company&quot;, &quot;Ourselves&quot;, &quot;We&quot;, &quot;Our&quot; and &quot;Us&quot;, refers to our Company. &quot;Party&quot;, &quot;Parties&quot;, or &quot;Us&quot;, refers to both the Client and ourselves. All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client&rsquo;s needs in respect of provision of the Company&rsquo;s stated services, in accordance with and subject to, prevailing law of Netherlands. Any use of the above terminology or other words in the singular, plural, capitalization and/or he/she or they, are taken as interchangeable and therefore as referring to same.</p>\r\n\r\n<h3><strong>Cookies</strong></h3>\r\n\r\n<p>We employ the use of cookies. By accessing Tahadiyaat, you agreed to use cookies in agreement with the Tahadiyaat&#39;s Privacy Policy.</p>\r\n\r\n<p>Most interactive websites use cookies to let us retrieve the user&rsquo;s details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate/advertising partners may also use cookies.</p>\r\n\r\n<h3><strong>License</strong></h3>\r\n\r\n<p>Unless otherwise stated, Tahadiyaat and/or its licensors own the intellectual property rights for all material on Tahadiyaat. All intellectual property rights are reserved. You may access this from Tahadiyaat for your own personal use subjected to restrictions set in these terms and conditions.</p>\r\n\r\n<p>You must not:</p>\r\n\r\n<ul>\r\n	<li>Republish material from Tahadiyaat</li>\r\n	<li>Sell, rent or sub-license material from Tahadiyaat</li>\r\n	<li>Reproduce, duplicate or copy material from Tahadiyaat</li>\r\n	<li>Redistribute content from Tahadiyaat</li>\r\n</ul>\r\n\r\n<p>This Agreement shall begin on the date hereof. Our Terms and Conditions were created with the help of the <a href=\"https://www.privacypolicies.com/blog/sample-terms-conditions-template/\">Terms And Conditions Template</a>.</p>\r\n\r\n<p>Parts of this website offer an opportunity for users to post and exchange opinions and information in certain areas of the website. Tahadiyaat does not filter, edit, publish or review Comments prior to their presence on the website. Comments do not reflect the views and opinions of Tahadiyaat,its agents and/or affiliates. Comments reflect the views and opinions of the person who post their views and opinions. To the extent permitted by applicable laws, Tahadiyaat shall not be liable for the Comments or for any liability, damages or expenses caused and/or suffered as a result of any use of and/or posting of and/or appearance of the Comments on this website.</p>\r\n\r\n<p>Tahadiyaat reserves the right to monitor all Comments and to remove any Comments which can be considered inappropriate, offensive or causes breach of these Terms and Conditions.</p>\r\n\r\n<p>You warrant and represent that:</p>\r\n\r\n<ul>\r\n	<li>You are entitled to post the Comments on our website and have all necessary licenses and consents to do so;</li>\r\n	<li>The Comments do not invade any intellectual property right, including without limitation copyright, patent or trademark of any third party;</li>\r\n	<li>The Comments do not contain any defamatory, libelous, offensive, indecent or otherwise unlawful material which is an invasion of privacy</li>\r\n	<li>The Comments will not be used to solicit or promote business or custom or present commercial activities or unlawful activity.</li>\r\n</ul>\r\n\r\n<p>You hereby grant Tahadiyaat a non-exclusive license to use, reproduce, edit and authorize others to use, reproduce and edit any of your Comments in any and all forms, formats or media.</p>\r\n\r\n<h3><strong>Hyperlinking to our Content</strong></h3>\r\n\r\n<p>The following organizations may link to our Website without prior written approval:</p>\r\n\r\n<ul>\r\n	<li>Government agencies;</li>\r\n	<li>Search engines;</li>\r\n	<li>News organizations;</li>\r\n	<li>Online directory distributors may link to our Website in the same manner as they hyperlink to the Websites of other listed businesses; and</li>\r\n	<li>System wide Accredited Businesses except soliciting non-profit organizations, charity shopping malls, and charity fundraising groups which may not hyperlink to our Web site.</li>\r\n</ul>\r\n\r\n<p>These organizations may link to our home page, to publications or to other Website information so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products and/or services; and (c) fits within the context of the linking party&rsquo;s site.</p>\r\n\r\n<p>We may consider and approve other link requests from the following types of organizations:</p>\r\n\r\n<ul>\r\n	<li>commonly-known consumer and/or business information sources;</li>\r\n	<li>dot.com community sites;</li>\r\n	<li>associations or other groups representing charities;</li>\r\n	<li>online directory distributors;</li>\r\n	<li>internet portals;</li>\r\n	<li>accounting, law and consulting firms; and</li>\r\n	<li>educational institutions and trade associations.</li>\r\n</ul>\r\n\r\n<p>We will approve link requests from these organizations if we decide that: (a) the link would not make us look unfavorably to ourselves or to our accredited businesses; (b) the organization does not have any negative records with us; (c) the benefit to us from the visibility of the hyperlink compensates the absence of Tahadiyaat; and (d) the link is in the context of general resource information.</p>\r\n\r\n<p>These organizations may link to our home page so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products or services; and (c) fits within the context of the linking party&rsquo;s site.</p>\r\n\r\n<p>If you are one of the organizations listed in paragraph 2 above and are interested in linking to our website, you must inform us by sending an e-mail to Tahadiyaat. Please include your name, your organization name, contact information as well as the URL of your site, a list of any URLs from which you intend to link to our Website, and a list of the URLs on our site to which you would like to link. Wait 2-3 weeks for a response.</p>\r\n\r\n<p>Approved organizations may hyperlink to our Website as follows:</p>\r\n\r\n<ul>\r\n	<li>By use of our corporate name; or</li>\r\n	<li>By use of the uniform resource locator being linked to; or</li>\r\n	<li>By use of any other description of our Website being linked to that makes sense within the context and format of content on the linking party&rsquo;s site.</li>\r\n</ul>\r\n\r\n<p>No use of Tahadiyaat&#39;s logo or other artwork will be allowed for linking absent a trademark license agreement.</p>\r\n\r\n<h3><strong>iFrames</strong></h3>\r\n\r\n<p>Without prior approval and written permission, you may not create frames around our Webpages that alter in any way the visual presentation or appearance of our Website.</p>\r\n\r\n<h3><strong>Content Liability</strong></h3>\r\n\r\n<p>We shall not be hold responsible for any content that appears on your Website. You agree to protect and defend us against all claims that is rising on your Website. No link(s) should appear on any Website that may be interpreted as libelous, obscene or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of, any third party rights.</p>\r\n\r\n<h3><strong>Your Privacy</strong></h3>\r\n\r\n<p>Please read Privacy Policy</p>\r\n\r\n<h3><strong>Reservation of Rights</strong></h3>\r\n\r\n<p>We reserve the right to request that you remove all links or any particular link to our Website. You approve to immediately remove all links to our Website upon request. We also reserve the right to amen these terms and conditions and it&rsquo;s linking policy at any time. By continuously linking to our Website, you agree to be bound to and follow these linking terms and conditions.</p>\r\n\r\n<h3><strong>Removal of links from our website</strong></h3>\r\n\r\n<p>If you find any link on our Website that is offensive for any reason, you are free to contact and inform us any moment. We will consider requests to remove links but we are not obligated to or so or to respond to you directly.</p>\r\n\r\n<p>We do not ensure that the information on this website is correct, we do not warrant its completeness or accuracy; nor do we promise to ensure that the website remains available or that the material on the website is kept up to date.</p>\r\n\r\n<h3><strong>Disclaimer</strong></h3>\r\n\r\n<p>To the maximum extent permitted by applicable law, we exclude all representations, warranties and conditions relating to our website and the use of this website. Nothing in this disclaimer will:</p>\r\n\r\n<ul>\r\n	<li>limit or exclude our or your liability for death or personal injury;</li>\r\n	<li>limit or exclude our or your liability for fraud or fraudulent misrepresentation;</li>\r\n	<li>limit any of our or your liabilities in any way that is not permitted under applicable law; or</li>\r\n	<li>exclude any of our or your liabilities that may not be excluded under applicable law.</li>\r\n</ul>\r\n\r\n<p>The limitations and prohibitions of liability set in this Section and elsewhere in this disclaimer: (a) are subject to the preceding paragraph; and (b) govern all liabilities arising under the disclaimer, including liabilities arising in contract, in tort and for breach of statutory duty.</p>\r\n\r\n<p>As long as the website and the information and services on the website are provided free of charge, we will not be liable for any loss or damage of any nature.</p>', 'ar', '2020-08-21 12:51:36', '2022-05-20 07:19:36'),
(7, 9, 'how_its_work', '<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>\r\n\r\n<h2>Why do we use it?</h2>\r\n\r\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using &#39;Content here, content here&#39;, making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for &#39;lorem ipsum&#39; will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h2>Where does it come from?</h2>\r\n\r\n<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of &quot;de Finibus Bonorum et Malorum&quot; (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, &quot;Lorem ipsum dolor sit amet..&quot;, comes from a line in section 1.10.32.</p>\r\n\r\n<p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from &quot;de Finibus Bonorum et Malorum&quot; by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p>\r\n\r\n<h2>Where can I get some?</h2>\r\n\r\n<p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don&#39;t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn&#39;t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p>', 'en', '2020-08-21 12:51:36', '2022-05-10 11:14:21'),
(8, 9, 'how_its_work', '<p>لوريم إيبسوم هو ببساطة نص شكلي يستخدم في صناعة الطباعة والتنضيد. كان Lorem Ipsum هو النص الوهمي القياسي في الصناعة منذ القرن الخامس عشر الميلادي ، عندما أخذت طابعة غير معروفة لوحًا من النوع وتدافعت عليه لعمل كتاب عينة. لقد نجت ليس فقط خمسة قرون ، ولكن أيضًا القفزة في التنضيد الإلكتروني ، وظلت دون تغيير جوهري. تم نشره في الستينيات من القرن الماضي بإصدار أوراق Letraset التي تحتوي على مقاطع Lorem Ipsum ، ومؤخرًا مع برامج النشر المكتبي مثل Aldus PageMaker بما في ذلك إصدارات Lorem Ipsum.</p>\r\n\r\n<p>لماذا نستخدمه؟<br />\r\nهناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص أو شكل توضع الفقرات في الصفحة التي يقرأها. الهدف من استخدام لوريم إيبسوم هو أنه يحتوي على توزيع طبيعي -إلى حد ما- للأحرف ، بدلاً من استخدام &quot;هنا يوجد محتوى نصي ، هنا يوجد محتوى نصي&quot; ، مما يجعلها تبدو وكأنها إنجليزية قابلة للقراءة. تستخدم العديد من حزم النشر المكتبي ومحرري صفحات الويب الآن Lorem Ipsum كنص نموذج افتراضي ، وسيكشف البحث عن &quot;lorem ipsum&quot; عن العديد من مواقع الويب التي لا تزال في مهدها. تطورت إصدارات مختلفة على مر السنين ، أحيانًا عن طريق الصدفة ، وأحيانًا عن قصد (روح الدعابة المحقونة وما شابه ذلك).</p>\r\n\r\n<p>حيث أنها لا تأتي من؟<br />\r\nخلافًا للاعتقاد الشائع ، فإن Lorem Ipsum ليس مجرد نص عشوائي. لها جذور في قطعة من الأدب اللاتيني الكلاسيكي من 45 قبل الميلاد ، مما يجعلها أكثر من 2000 عام. قام ريتشارد مكلينتوك ، الأستاذ اللاتيني في كلية هامبدن سيدني في فيرجينيا ، بالبحث عن واحدة من أكثر الكلمات اللاتينية غموضًا ، consectetur ، من مقطع لوريم إيبسوم ، وتصفح اقتباسات الكلمة في الأدب الكلاسيكي ، اكتشف المصدر الذي لا شك فيه. يأتي لوريم إيبسوم من الأقسام 1.10.32 و 1.10.33 من &quot;de Finibus Bonorum et Malorum&quot; (أقصى الخير والشر) بقلم شيشرون ، الذي كتبه عام 45 قبل الميلاد. هذا الكتاب عبارة عن أطروحة حول نظرية الأخلاق ، وقد حظيت بشعبية كبيرة خلال عصر النهضة. السطر الأول من Lorem Ipsum ، &quot;Lorem ipsum dolor sit amet ..&quot; ، يأتي من سطر في القسم 1.10.32.</p>\r\n\r\n<p>الجزء القياسي من لوريم إيبسوم المستخدم منذ القرن الخامس عشر مستنسخ أدناه للمهتمين. تم أيضًا نسخ الأقسام 1.10.32 و 1.10.33 من &quot;de Finibus Bonorum et Malorum&quot; بواسطة Cicero في شكلها الأصلي الدقيق ، مصحوبة بنسخ باللغة الإنجليزية من ترجمة عام 1914 بواسطة H. Rackham.</p>\r\n\r\n<p>من أين يمكنني الحصول على البعض؟<br />\r\nهناك العديد من الأشكال المتوفرة لنصوص لوريم إيبسوم ، ولكن الغالبية قد تعرضت للتغيير بشكل ما ، عن طريق إدخال بعض الفكاهة أو الكلمات العشوائية التي لا تبدو قابلة للتصديق إلى حد ما. إذا كنت ستستخدم مقطعًا من لوريم إيبسوم ، فعليك التأكد من عدم وجود أي شيء محرج مخفي في منتصف النص. تميل جميع مولدات Lorem Ipsum على الإنترنت إلى تكرار الأجزاء المحددة مسبقًا حسب الضرورة ، مما يجعل هذا أول مولد حقيقي على الإنترنت. يستخدم قاموسًا يضم أكثر من 200 كلمة لاتينية ، جنبًا إلى جنب مع حفنة من تراكيب الجملة النموذجية ، لتوليد Lorem Ipsum الذي يبدو معقولًا. لذلك فإن Lorem Ipsum الذي تم إنشاؤه يكون دائمًا خاليًا من التكرار أو الدعابة المحقونة أو الكلمات غير المميزة وما إلى ذلك.</p>', 'ar', '2020-08-21 12:51:36', '2022-05-10 11:14:21');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int NOT NULL,
  `sortname` varchar(3) NOT NULL,
  `name` varchar(150) NOT NULL,
  `phonecode` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `sortname`, `name`, `phonecode`) VALUES
(1, 'AF', 'Afghanistan', '+93'),
(2, 'AL', 'Albania', '+355'),
(3, 'DZ', 'Algeria', '+213'),
(4, 'AS', 'American Samoa', '+1684'),
(5, 'AD', 'Andorra', '+376'),
(6, 'AO', 'Angola', '+244'),
(7, 'AI', 'Anguilla', '+1264'),
(8, 'AQ', 'Antarctica', '+672'),
(9, 'AG', 'Antigua And Barbuda', '+1268'),
(10, 'AR', 'Argentina', '+54'),
(11, 'AM', 'Armenia', '+374'),
(12, 'AW', 'Aruba', '+297'),
(13, 'AU', 'Australia', '+61'),
(14, 'AT', 'Austria', '+43'),
(15, 'AZ', 'Azerbaijan', '+994'),
(16, 'BS', 'Bahamas The', '+1242'),
(17, 'BH', 'Bahrain', '+973'),
(18, 'BD', 'Bangladesh', '+880'),
(19, 'BB', 'Barbados', '+1246'),
(20, 'BY', 'Belarus', '+375'),
(21, 'BE', 'Belgium', '+32'),
(22, 'BZ', 'Belize', '+501'),
(23, 'BJ', 'Benin', '+229'),
(24, 'BM', 'Bermuda', '+1441'),
(25, 'BT', 'Bhutan', '+975'),
(26, 'BO', 'Bolivia', '+591'),
(27, 'BA', 'Bosnia and Herzegovina', '+387'),
(28, 'BW', 'Botswana', '+267'),
(30, 'BR', 'Brazil', '+55'),
(31, 'IO', 'British Indian Ocean Territory', '+246'),
(32, 'BN', 'Brunei', '+673'),
(33, 'BG', 'Bulgaria', '+359'),
(34, 'BF', 'Burkina Faso', '+226'),
(35, 'BI', 'Burundi', '+257'),
(36, 'KH', 'Cambodia', '+855'),
(37, 'CM', 'Cameroon', '+237'),
(38, 'CA', 'Canada', '+1'),
(39, 'CV', 'Cape Verde', '+238'),
(40, 'KY', 'Cayman Islands', '+1345'),
(41, 'CF', 'Central African Republic', '+236'),
(42, 'TD', 'Chad', '+235'),
(43, 'CL', 'Chile', '+56'),
(44, 'CN', 'China', '+86'),
(45, 'CX', 'Christmas Island', '+61'),
(46, 'CC', 'Cocos (Keeling) Islands', '+672'),
(47, 'CO', 'Colombia', '+57'),
(48, 'KM', 'Comoros', '+269'),
(49, 'CG', 'Republic Of The Congo', '+242'),
(50, 'CD', 'Democratic Republic Of The Congo', '+242'),
(51, 'CK', 'Cook Islands', '+682'),
(52, 'CR', 'Costa Rica', '+506'),
(53, 'CI', 'Cote D\'Ivoire (Ivory Coast)', '+225'),
(54, 'HR', 'Croatia (Hrvatska)', '+385'),
(55, 'CU', 'Cuba', '+53'),
(56, 'CY', 'Cyprus', '+357'),
(57, 'CZ', 'Czech Republic', '+420'),
(58, 'DK', 'Denmark', '+45'),
(59, 'DJ', 'Djibouti', '+253'),
(60, 'DM', 'Dominica', '+1767'),
(61, 'DO', 'Dominican Republic', '+1809'),
(62, 'TP', 'East Timor', '+670'),
(63, 'EC', 'Ecuador', '+593'),
(64, 'EG', 'Egypt', '+20'),
(65, 'SV', 'El Salvador', '+503'),
(66, 'GQ', 'Equatorial Guinea', '+240'),
(67, 'ER', 'Eritrea', '+291'),
(68, 'EE', 'Estonia', '+372'),
(69, 'ET', 'Ethiopia', '+251'),
(70, 'XA', 'External Territories of Australia', '+61'),
(71, 'FK', 'Falkland Islands', '+500'),
(72, 'FO', 'Faroe Islands', '+298'),
(73, 'FJ', 'Fiji Islands', '+679'),
(74, 'FI', 'Finland', '+358'),
(75, 'FR', 'France', '+33'),
(76, 'GF', 'French Guiana', '+594'),
(77, 'PF', 'French Polynesia', '+689'),
(79, 'GA', 'Gabon', '+241'),
(80, 'GM', 'Gambia The', '+220'),
(81, 'GE', 'Georgia', '+995'),
(82, 'DE', 'Germany', '+49'),
(83, 'GH', 'Ghana', '+233'),
(84, 'GI', 'Gibraltar', '+350'),
(85, 'GR', 'Greece', '+30'),
(86, 'GL', 'Greenland', '+299'),
(87, 'GD', 'Grenada', '+1473'),
(88, 'GP', 'Guadeloupe', '+590'),
(89, 'GU', 'Guam', '+1671'),
(90, 'GT', 'Guatemala', '+502'),
(91, 'XU', 'Guernsey and Alderney', '+44'),
(92, 'GN', 'Guinea', '+224'),
(93, 'GW', 'Guinea-Bissau', '+245'),
(94, 'GY', 'Guyana', '+592'),
(95, 'HT', 'Haiti', '+509'),
(97, 'HN', 'Honduras', '+504'),
(98, 'HK', 'Hong Kong S.A.R.', '+852'),
(99, 'HU', 'Hungary', '+36'),
(100, 'IS', 'Iceland', '+354'),
(101, 'IN', 'India', '+91'),
(102, 'ID', 'Indonesia', '+62'),
(103, 'IR', 'Iran', '+98'),
(104, 'IQ', 'Iraq', '+964'),
(105, 'IE', 'Ireland', '+353'),
(106, 'IL', 'Israel', '+972'),
(107, 'IT', 'Italy', '+39'),
(108, 'JM', 'Jamaica', '+1876'),
(109, 'JP', 'Japan', '+81'),
(110, 'XJ', 'Jersey', '+44'),
(111, 'JO', 'Jordan', '+962'),
(112, 'KZ', 'Kazakhstan', '+7'),
(113, 'KE', 'Kenya', '+254'),
(114, 'KI', 'Kiribati', '+686'),
(115, 'KP', 'Korea North', '+850'),
(116, 'KR', 'Korea South', '+82'),
(117, 'KW', 'Kuwait', '+965'),
(118, 'KG', 'Kyrgyzstan', '+996'),
(119, 'LA', 'Laos', '+856'),
(120, 'LV', 'Latvia', '+371'),
(121, 'LB', 'Lebanon', '+961'),
(122, 'LS', 'Lesotho', '+266'),
(123, 'LR', 'Liberia', '+231'),
(124, 'LY', 'Libya', '+218'),
(125, 'LI', 'Liechtenstein', '+423'),
(126, 'LT', 'Lithuania', '+370'),
(127, 'LU', 'Luxembourg', '+352'),
(128, 'MO', 'Macau S.A.R.', '+853'),
(129, 'MK', 'Macedonia', '+389'),
(130, 'MG', 'Madagascar', '+261'),
(131, 'MW', 'Malawi', '+265'),
(132, 'MY', 'Malaysia', '+60'),
(133, 'MV', 'Maldives', '+960'),
(134, 'ML', 'Mali', '+223'),
(135, 'MT', 'Malta', '+356'),
(136, 'XM', 'Man (Isle of)', '+44'),
(137, 'MH', 'Marshall Islands', '+692'),
(138, 'MQ', 'Martinique', '+596'),
(139, 'MR', 'Mauritania', '+222'),
(140, 'MU', 'Mauritius', '+230'),
(141, 'YT', 'Mayotte', '+269'),
(142, 'MX', 'Mexico', '+52'),
(143, 'FM', 'Micronesia', '+691'),
(144, 'MD', 'Moldova', '+373'),
(145, 'MC', 'Monaco', '+377'),
(146, 'MN', 'Mongolia', '+976'),
(147, 'MS', 'Montserrat', '+1664'),
(148, 'MA', 'Morocco', '+212'),
(149, 'MZ', 'Mozambique', '+258'),
(150, 'MM', 'Myanmar', '+95'),
(151, 'NA', 'Namibia', '+264'),
(152, 'NR', 'Nauru', '+674'),
(153, 'NP', 'Nepal', '+977'),
(154, 'AN', 'Netherlands Antilles', '+599'),
(155, 'NL', 'Netherlands The', '+31'),
(156, 'NC', 'New Caledonia', '+687'),
(157, 'NZ', 'New Zealand', '+64'),
(158, 'NI', 'Nicaragua', '+505'),
(159, 'NE', 'Niger', '+227'),
(160, 'NG', 'Nigeria', '+234'),
(161, 'NU', 'Niue', '+683'),
(162, 'NF', 'Norfolk Island', '+672'),
(163, 'MP', 'Northern Mariana Islands', '+1670'),
(164, 'NO', 'Norway', '+47'),
(165, 'OM', 'Oman', '+968'),
(166, 'PK', 'Pakistan', '+92'),
(167, 'PW', 'Palau', '+680'),
(168, 'PS', 'Palestinian Territory Occupied', '+970'),
(169, 'PA', 'Panama', '+507'),
(170, 'PG', 'Papua new Guinea', '+675'),
(171, 'PY', 'Paraguay', '+595'),
(172, 'PE', 'Peru', '+51'),
(173, 'PH', 'Philippines', '+63'),
(174, 'PN', 'Pitcairn Island', '+64'),
(175, 'PL', 'Poland', '+48'),
(176, 'PT', 'Portugal', '+351'),
(177, 'PR', 'Puerto Rico', '+1787'),
(178, 'QA', 'Qatar', '+974'),
(179, 'RE', 'Reunion', '+262'),
(180, 'RO', 'Romania', '+40'),
(181, 'RU', 'Russia', '+70'),
(182, 'RW', 'Rwanda', '+250'),
(183, 'SH', 'Saint Helena', '+290'),
(184, 'KN', 'Saint Kitts And Nevis', '+1869'),
(185, 'LC', 'Saint Lucia', '+1758'),
(186, 'PM', 'Saint Pierre and Miquelon', '+508'),
(187, 'VC', 'Saint Vincent And The Grenadines', '+1784'),
(188, 'WS', 'Samoa', '+684'),
(189, 'SM', 'San Marino', '+378'),
(190, 'ST', 'Sao Tome and Principe', '+239'),
(191, 'SA', 'Saudi Arabia', '+966'),
(192, 'SN', 'Senegal', '+221'),
(193, 'RS', 'Serbia', '+381'),
(194, 'SC', 'Seychelles', '+248'),
(195, 'SL', 'Sierra Leone', '+232'),
(196, 'SG', 'Singapore', '+65'),
(197, 'SK', 'Slovakia', '+421'),
(198, 'SI', 'Slovenia', '+386'),
(199, 'XG', 'Smaller Territories of the UK', '+44'),
(200, 'SB', 'Solomon Islands', '+677'),
(201, 'SO', 'Somalia', '+252'),
(202, 'ZA', 'South Africa', '+27'),
(203, 'GS', 'South Georgia', '+500'),
(204, 'SS', 'South Sudan', '+211'),
(205, 'ES', 'Spain', '+34'),
(206, 'LK', 'Sri Lanka', '+94'),
(207, 'SD', 'Sudan', '+249'),
(208, 'SR', 'Suriname', '+597'),
(209, 'SJ', 'Svalbard And Jan Mayen Islands', '+47'),
(210, 'SZ', 'Swaziland', '+268'),
(211, 'SE', 'Sweden', '+46'),
(212, 'CH', 'Switzerland', '+41'),
(213, 'SY', 'Syria', '+963'),
(214, 'TW', 'Taiwan', '+886'),
(215, 'TJ', 'Tajikistan', '+992'),
(216, 'TZ', 'Tanzania', '+255'),
(217, 'TH', 'Thailand', '+66'),
(218, 'TG', 'Togo', '+228'),
(219, 'TK', 'Tokelau', '+690'),
(220, 'TO', 'Tonga', '+676'),
(221, 'TT', 'Trinidad And Tobago', '+1868'),
(222, 'TN', 'Tunisia', '+216'),
(223, 'TR', 'Turkey', '+90'),
(224, 'TM', 'Turkmenistan', '+7370'),
(225, 'TC', 'Turks And Caicos Islands', '+1649'),
(226, 'TV', 'Tuvalu', '+688'),
(227, 'UG', 'Uganda', '+256'),
(228, 'UA', 'Ukraine', '+380'),
(229, 'AE', 'United Arab Emirates', '+971'),
(230, 'GB', 'United Kingdom', '+44'),
(231, 'US', 'United States', '+1'),
(232, 'UM', 'United States Minor Outlying Islands', '+1'),
(233, 'UY', 'Uruguay', '+598'),
(234, 'UZ', 'Uzbekistan', '+998'),
(235, 'VU', 'Vanuatu', '+678'),
(236, 'VA', 'Vatican City State (Holy See)', '+39'),
(237, 'VE', 'Venezuela', '+58'),
(238, 'VN', 'Vietnam', '+84'),
(239, 'VG', 'Virgin Islands (British)', '+1284'),
(240, 'VI', 'Virgin Islands (US)', '+1340'),
(241, 'WF', 'Wallis And Futuna Islands', '+681'),
(242, 'EH', 'Western Sahara', '+212'),
(243, 'YE', 'Yemen', '+967'),
(244, 'YU', 'Yugoslavia', '+38'),
(245, 'ZM', 'Zambia', '+260'),
(246, 'ZW', 'Zimbabwe', '+263');

-- --------------------------------------------------------

--
-- Table structure for table `courts`
--

CREATE TABLE `courts` (
  `id` int NOT NULL,
  `facility_id` varchar(255) DEFAULT NULL,
  `facility_owner_id` bigint DEFAULT NULL,
  `category_id` varchar(255) DEFAULT NULL,
  `court_name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `minimum_hour_book` int DEFAULT NULL,
  `hourly_price` float(10,2) DEFAULT NULL,
  `start_time` varchar(255) DEFAULT NULL,
  `end_time` varchar(255) DEFAULT NULL,
  `timeslot` varchar(50) DEFAULT NULL,
  `popular_day` varchar(50) DEFAULT NULL,
  `popular_start_time` varchar(50) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `is_featured` tinyint DEFAULT '0',
  `average_rating` float(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `courts`
--

INSERT INTO `courts` (`id`, `facility_id`, `facility_owner_id`, `category_id`, `court_name`, `image`, `address`, `latitude`, `longitude`, `minimum_hour_book`, `hourly_price`, `start_time`, `end_time`, `timeslot`, `popular_day`, `popular_start_time`, `status`, `is_featured`, `average_rating`, `created_at`, `updated_at`) VALUES
(6, '9', 2, '2', 'Dubai Stadium', 'APR2022/1651238089-court.jpg', '5779+36 Dubai - United Arab Emirates', '25.16266322339413', '55.26803621796874', 1, 1.00, '09:00', '21:00', '60', 'Sunday', '02:28', 1, 0, NULL, '2022-03-25 04:16:13', '2022-05-06 10:55:33'),
(7, '9', 2, '2', 'Iranian Club Dubai', NULL, '678R+VC Dubai - United Arab Emirates', '25.21724732341151', '55.29110661225585', 1, 200.00, '02:28', '17:28', '40', 'Sunday', '02:28', 1, 0, 5.00, '2022-03-29 11:59:32', '2022-05-30 07:31:14'),
(8, '9', 2, '2', 'Cosmos Sports Aca', NULL, '68RP+6G Dubai - United Arab Emirates', '25.240567121014646', '55.336357445996086', 2, 200.00, '18:44', '19:44', '25', 'Sunday', '02:28', 1, 0, NULL, '2022-03-30 13:14:46', '2022-05-06 10:56:16'),
(9, '12', 45, '2', 'easy', NULL, 'Marina Mall - Dubai - United Arab Emirates', '25.07643', '55.140504', 1, 200.00, '18:39', '22:40', '60', 'Sunday', '02:28', 1, 0, NULL, '2022-03-30 14:40:50', '2022-04-05 07:30:20'),
(10, '12', 2, '1', 'Facon', NULL, '31 Street 24 - Al Barsha - Al Barsha 1 - Dubai - United Arab Emirates', '25.11242781924315', '55.196179830908214', NULL, 15.00, '00:05', '23:55', '120', 'Sunday', '02:28', 1, 1, 5.00, '2022-04-04 13:32:32', '2022-05-30 05:32:44'),
(11, '9', 2, '2', 'Court 1', NULL, 'UAM Azcapotzalco, Avenida San Pablo Xalpa, Reynosa Tamaulipas, Mexico City, CDMX, Mexico', '19.5034187', '-99.18697279999999', NULL, 0.00, '13:01', '18:01', '20', 'Sunday', '02:28', 0, 1, NULL, '2022-04-05 07:32:15', '2022-05-06 10:14:55'),
(12, '15', 46, '3', 'Iranian Club Dubai', NULL, '7954+RF Dubai - United Arab Emirates', '25.259615315269947', '55.35622752753907', NULL, 30.00, '13:59', '16:59', '20', 'Sunday', '02:28', 1, 1, NULL, '2022-04-05 08:29:49', '2022-05-06 10:56:47'),
(13, '10', 2, '2', 'Court 2', NULL, 'UAE Pavilion - جناح دولة الإمارات العربية المتحدة - Dubai - United Arab Emirates', '24.9612564', '55.1521009', NULL, 342.00, '09:03', '22:00', '20', 'Sunday', '02:28', 1, 0, 4.00, '2022-04-05 08:33:42', '2022-05-13 10:22:03'),
(14, '11', 2, '2', 'Court 3', NULL, '6CGX+4R Dubai - United Arab Emirates', '25.225286363836442', '55.44956621752577', NULL, 4534.00, '14:04', '14:04', '45', 'Sunday', '02:28', 1, 0, NULL, '2022-04-05 08:34:19', '2022-05-06 10:57:02'),
(15, '11', 2, '2', 'Court 4', NULL, 'Building 9, Golden Mile Galleria, Palm - The Palm Jumeirah - Dubai - United Arab Emirates', '25.111682234858698', '55.14119064550783', NULL, 43.00, '14:04', '18:04', '20', 'Sunday', '02:28', 1, 0, NULL, '2022-04-05 08:35:00', '2022-05-06 10:57:24'),
(16, '13', 4, '2', 'Court 5', NULL, 'Dubai Outlet Mall, Route 66 - Al Ain - Dubai Road - Dubai - United Arab Emirates', '25.0724998', '55.4007009', NULL, 43.00, '14:05', '14:05', '25', 'Sunday', '02:28', 1, 0, NULL, '2022-04-05 08:35:30', '2022-05-30 09:31:02'),
(17, '10', 2, '2', 'Court 6', NULL, 'Swiss International Scientific School in Dubai Dubai Healthcare City Phase 2 Al jaddaf 505002 - Al Jaddaf - Dubai - United Arab Emirates', '25.20935342912944', '55.3315509274414', NULL, 342.00, '14:05', '21:03', '20', 'Sunday', '02:28', 1, 0, 4.50, '2022-04-05 08:36:02', '2022-05-30 06:04:51'),
(18, '15', 46, '2', 'Iranian Club Dubai', NULL, 'Burj Khalifa - Sheikh Mohammed bin Rashid Boulevard - Dubai - United Arab Emirates', '25.197197', '55.27437639999999', NULL, 122.00, '10:00', '18:24', '40', 'Sunday', '02:28', 1, 0, NULL, '2022-04-08 05:55:23', '2022-05-06 10:52:21'),
(19, '19', 50, '3', 'Pratap Nagar', NULL, 'Qatar - Dubai - United Arab Emirates', '25.2280866', '55.1732869', NULL, 600.00, '07:30', '20:30', '40', 'Sunday', '02:28', 1, 1, 5.00, '2022-04-08 06:12:43', '2022-05-30 05:32:26'),
(20, '19', 50, '3', 'DMS Stadium', NULL, 'Dubai - United Arab Emirates', '25.2048493', '55.2707828', NULL, 500.00, '10:00', '22:45', '40', 'Sunday', '02:28', 1, 1, 5.00, '2022-04-08 06:15:43', '2022-05-30 05:33:09'),
(21, '21', 46, '2', 'RD', NULL, '56MV+XX3 - near business bay - Al Safa - Dubai - United Arab Emirates', '25.185743727685924', '55.24486193208007', NULL, 456.00, '07:24', '18:25', '40', 'Sunday', '02:28', 1, 1, NULL, '2022-04-08 10:55:16', '2022-05-06 10:55:10'),
(22, '8', 3, '2', 'Sandeep Court', 'APR2022/1651239671-court.jpg', 'Dubai - United Arab Emirates', '25.2048493', '55.2707828', NULL, 10.00, '17:04', '22:04', '15', 'Sunday', '02:28', 1, 1, 3.00, '2022-04-09 11:35:09', '2022-05-30 06:02:03'),
(23, '10', 2, '3', 'The Dubai Court', 'APR2022/1649847085-court.jpg', 'The Dubai Mall - Dubai - United Arab Emirates', '25.198765', '55.2796053', NULL, 200.00, '09:00', '19:00', '30', 'Sunday', '02:28', 1, 1, NULL, '2022-04-13 10:51:25', '2022-05-06 10:11:35'),
(24, '19', 50, '3', 'Hello', 'APR2022/1650287696-court.jpg', 'Dubai - United Arab Emirates', '25.2048493', '55.2707828', NULL, 123.00, '07:55', '15:33', '30', 'Sunday', '02:28', 1, 1, 4.50, '2022-04-18 10:04:04', '2022-05-27 13:37:52'),
(25, '19', 50, '3', 'HMS', 'APR2022/1650287712-court.jpeg', 'Doha, Qatar', '25.2854473', '51.53103979999999', NULL, 333.00, '15:35', '23:35', '20', 'Sunday', '02:28', 0, 1, NULL, '2022-04-18 10:06:15', '2022-05-06 10:10:41'),
(26, '19', 50, '3', 'Abu Dhabi Court', 'MAY2022/1652164715-court.jpg', 'Abu Dhabi - United Arab Emirates', '24.453884', '54.3773438', NULL, 100.00, '08:00', '18:00', '25', 'Sunday', '02:28', 1, 1, 5.00, '2022-05-10 06:38:35', '2022-05-27 13:38:08');

-- --------------------------------------------------------

--
-- Table structure for table `courts_lang`
--

CREATE TABLE `courts_lang` (
  `id` int NOT NULL,
  `court_id` int DEFAULT NULL,
  `court_name` varchar(255) DEFAULT NULL,
  `lang` varchar(10) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `courts_lang`
--

INSERT INTO `courts_lang` (`id`, `court_id`, `court_name`, `lang`, `created_at`, `updated_at`) VALUES
(1, 1, 'Test', 'en', '2022-03-02 07:04:53', '2022-03-02 07:04:53'),
(2, 1, 'test', 'ar', '2022-03-02 07:04:53', '2022-03-02 07:04:53'),
(3, 2, 'Aktiv Nation', 'en', '2022-03-02 07:18:15', '2022-03-04 13:41:11'),
(4, 2, 'Aktiv Nation', 'ar', '2022-03-02 07:18:15', '2022-03-04 13:41:11'),
(5, 3, 'FC Academic City', 'en', '2022-03-02 07:21:56', '2022-03-04 13:40:48'),
(6, 3, 'FC Academic City', 'ar', '2022-03-02 07:21:56', '2022-03-04 13:40:48'),
(7, 4, 'FC Jumeirah', 'en', '2022-03-02 07:22:45', '2022-03-04 13:40:20'),
(8, 4, 'FC Jumeirah', 'ar', '2022-03-02 07:22:45', '2022-03-04 13:40:20'),
(9, 5, 'Koora Dome', 'en', '2022-03-02 09:05:50', '2022-03-14 11:05:14'),
(10, 5, 'Koora Dome', 'ar', '2022-03-02 09:05:50', '2022-03-14 11:05:14'),
(11, 6, 'Dubai Stadium', 'en', '2022-03-25 04:16:13', '2022-05-06 10:55:33'),
(12, 6, 'استاد دبي', 'ar', '2022-03-25 04:16:13', '2022-05-06 10:55:33'),
(13, 7, 'Iranian Club Dubai', 'en', '2022-03-29 11:59:32', '2022-05-06 10:55:53'),
(14, 7, 'النادي الإيراني بدبي', 'ar', '2022-03-29 11:59:32', '2022-05-06 10:55:53'),
(15, 8, 'Cosmos Sports Aca', 'en', '2022-03-30 13:14:46', '2022-05-06 10:56:16'),
(16, 8, 'كوزموس سبورتس أكا', 'ar', '2022-03-30 13:14:46', '2022-05-06 10:56:16'),
(17, 9, 'easy', 'en', '2022-03-30 14:40:50', '2022-04-01 12:43:29'),
(18, 9, 'ايزي', 'ar', '2022-03-30 14:40:50', '2022-04-01 12:43:29'),
(19, 10, 'Facon', 'en', '2022-04-04 13:32:32', '2022-05-06 10:56:29'),
(20, 10, 'فاكون', 'ar', '2022-04-04 13:32:32', '2022-05-06 10:56:29'),
(21, 11, 'Court 1', 'en', '2022-04-05 07:32:15', '2022-04-05 07:32:52'),
(22, 11, 'sdgsd', 'ar', '2022-04-05 07:32:15', '2022-04-05 07:32:52'),
(23, 12, 'Iranian Club Dubai', 'en', '2022-04-05 08:29:49', '2022-05-06 10:56:47'),
(24, 12, 'النادي الإيراني بدبي', 'ar', '2022-04-05 08:29:49', '2022-05-06 10:56:47'),
(25, 13, 'Court 2', 'en', '2022-04-05 08:33:42', '2022-05-06 10:16:25'),
(26, 13, 'المحكمة 2', 'ar', '2022-04-05 08:33:42', '2022-05-06 10:16:25'),
(27, 14, 'Court 3', 'en', '2022-04-05 08:34:19', '2022-05-06 10:57:02'),
(28, 14, 'المحكمة 3', 'ar', '2022-04-05 08:34:19', '2022-05-06 10:57:02'),
(29, 15, 'Court 4', 'en', '2022-04-05 08:35:00', '2022-05-06 10:57:24'),
(30, 15, 'المحكمة 4', 'ar', '2022-04-05 08:35:00', '2022-05-06 10:57:24'),
(31, 16, 'Court 5', 'en', '2022-04-05 08:35:30', '2022-05-30 09:30:43'),
(32, 16, 'المحكمة 5', 'ar', '2022-04-05 08:35:30', '2022-05-30 09:30:43'),
(33, 17, 'Court 6', 'en', '2022-04-05 08:36:02', '2022-05-06 10:52:52'),
(34, 17, 'المحكمة 6', 'ar', '2022-04-05 08:36:02', '2022-05-06 10:52:52'),
(35, 18, 'Iranian Club Dubai', 'en', '2022-04-08 05:55:23', '2022-05-06 10:52:21'),
(36, 18, 'النادي الإيراني بدبي', 'ar', '2022-04-08 05:55:23', '2022-05-06 10:52:21'),
(37, 19, 'Pratap Nagar', 'en', '2022-04-08 06:12:43', '2022-04-08 06:12:43'),
(38, 19, 'براتاب ناجار', 'ar', '2022-04-08 06:12:43', '2022-04-08 06:12:43'),
(39, 20, 'DMS Stadium', 'en', '2022-04-08 06:15:43', '2022-05-06 10:07:18'),
(40, 20, 'استاد DMS', 'ar', '2022-04-08 06:15:43', '2022-05-06 10:07:18'),
(41, 21, 'RD', 'en', '2022-04-08 10:55:16', '2022-05-06 10:55:10'),
(42, 21, 'ED', 'ar', '2022-04-08 10:55:16', '2022-05-06 10:55:10'),
(43, 22, 'Sandeep Court', 'en', '2022-04-09 11:35:09', '2022-05-06 05:45:55'),
(44, 22, 'محكمة سانديب', 'ar', '2022-04-09 11:35:09', '2022-05-06 05:45:55'),
(45, 23, 'The Dubai Court', 'en', '2022-04-13 10:51:25', '2022-05-06 10:11:35'),
(46, 23, 'محكمة دبي', 'ar', '2022-04-13 10:51:25', '2022-05-06 10:11:35'),
(47, 24, 'Hello', 'en', '2022-04-18 10:04:04', '2022-05-06 10:11:05'),
(48, 24, 'براتاب ناجار', 'ar', '2022-04-18 10:04:04', '2022-05-06 10:11:05'),
(49, 25, 'HMS', 'en', '2022-04-18 10:06:15', '2022-05-06 10:10:41'),
(50, 25, 'HMS', 'ar', '2022-04-18 10:06:15', '2022-05-06 10:10:41'),
(51, 26, 'Abu Dhabi Court', 'en', '2022-05-10 06:38:35', '2022-05-10 06:38:35'),
(52, 26, 'محكمة أبوظبي', 'ar', '2022-05-10 06:38:35', '2022-05-10 06:38:35');

-- --------------------------------------------------------

--
-- Table structure for table `court_booking`
--

CREATE TABLE `court_booking` (
  `id` bigint NOT NULL,
  `user_id` int DEFAULT NULL,
  `court_id` int DEFAULT NULL,
  `booking_type` enum('normal','challenge') NOT NULL DEFAULT 'normal',
  `booking_date` date DEFAULT NULL,
  `end_booking_date` date DEFAULT NULL,
  `facility_id` bigint DEFAULT NULL,
  `hourly_price` int DEFAULT NULL,
  `minimum_hour_book` int DEFAULT NULL,
  `total_amount` int DEFAULT NULL,
  `admin_commission_percentage` int DEFAULT NULL,
  `admin_commission_amount` float(10,2) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_type` enum('online','cash') DEFAULT NULL,
  `challenge_type` enum('public','private') NOT NULL DEFAULT 'public',
  `payment_received_status` enum('Pending','Received','NotReceived') NOT NULL DEFAULT 'Pending',
  `order_status` enum('Pending','Completed','Accepted','Cancelled') NOT NULL DEFAULT 'Pending',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `court_booking`
--

INSERT INTO `court_booking` (`id`, `user_id`, `court_id`, `booking_type`, `booking_date`, `end_booking_date`, `facility_id`, `hourly_price`, `minimum_hour_book`, `total_amount`, `admin_commission_percentage`, `admin_commission_amount`, `transaction_id`, `payment_type`, `challenge_type`, `payment_received_status`, `order_status`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 7, 'normal', '2022-03-05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Cancelled', 1, '2022-03-04 12:40:46', '2022-04-11 10:44:05'),
(2, 1, 2, 'normal', '2022-03-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Completed', 1, '2022-03-04 13:42:57', '2022-05-06 13:50:38'),
(3, 1, 5, 'normal', '2022-03-11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Cancelled', 1, '2022-03-04 13:46:39', '2022-04-04 10:07:22'),
(4, 1, 5, 'normal', '2022-03-05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Completed', 1, '2022-03-04 13:46:57', '2022-05-06 13:50:38'),
(5, 1, 5, 'normal', '2022-03-14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Cancelled', 1, '2022-03-07 08:32:26', '2022-04-05 10:58:30'),
(6, 1, 5, 'normal', '2022-03-13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Cancelled', 1, '2022-03-07 08:33:05', '2022-04-05 11:16:50'),
(7, 1, 5, 'normal', '2022-03-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Cancelled', 1, '2022-03-07 08:38:46', '2022-04-07 08:47:53'),
(8, 1, 5, 'normal', '2022-03-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Completed', 1, '2022-03-07 13:43:55', '2022-05-06 13:50:38'),
(9, 1, 5, 'normal', '2022-03-09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Completed', 1, '2022-03-08 04:34:19', '2022-05-06 13:50:38'),
(10, 1, 5, 'normal', '2022-03-15', '2022-03-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Completed', 1, '2022-03-09 09:26:33', '2022-05-06 13:50:38'),
(11, 1, 5, 'normal', '2022-04-01', '2022-04-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Completed', 1, '2022-03-09 15:19:23', '2022-05-06 13:50:38'),
(12, 9, 6, 'normal', '2022-03-28', '2022-03-30', 9, 1, 1, 1000, 10, 100.00, NULL, NULL, 'public', 'Pending', 'Cancelled', 1, '2022-03-29 12:22:56', '2022-04-11 10:58:35'),
(13, 40, 6, 'normal', '2022-03-28', '2022-03-30', 9, 1, 1, 1000, 10, 100.00, NULL, NULL, 'public', 'Pending', 'Cancelled', 1, '2022-03-29 12:23:50', '2022-04-11 12:48:56'),
(14, 40, 6, 'normal', '2022-03-28', '2022-03-30', 9, 1, 1, 1000, 10, 100.00, NULL, NULL, 'public', 'Pending', 'Completed', 1, '2022-03-31 04:31:23', '2022-05-06 13:50:38'),
(15, 40, 6, 'normal', '2022-03-28', '2022-03-28', 9, 1, 1, 1000, 10, 100.00, NULL, NULL, 'public', 'Pending', 'Completed', 1, '2022-03-31 12:56:04', '2022-05-06 13:50:38'),
(16, 9, 7, 'normal', '2022-04-02', '2022-04-02', 9, 200, 1, 400, 0, 0.00, NULL, NULL, 'public', 'Pending', 'Completed', 1, '2022-03-31 13:04:42', '2022-05-06 13:50:38'),
(17, 40, 6, 'normal', '2022-03-28', '2022-03-28', 9, 1, 1, 1000, 10, 100.00, NULL, NULL, 'public', 'Pending', 'Cancelled', 1, '2022-03-31 13:05:37', '2022-04-07 10:14:07'),
(18, 9, 9, 'normal', '2022-04-03', '2022-04-03', 12, 200, 1, 200, 12, 24.00, NULL, NULL, 'public', 'Pending', 'Completed', 1, '2022-03-31 13:21:02', '2022-03-31 13:21:02'),
(19, 40, 8, 'normal', '2022-03-28', '2022-03-30', 9, 200, 2, 1000, 12, NULL, '1212121212', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-04 05:47:52', '2022-04-04 05:47:52'),
(21, 1, 17, 'normal', '2022-04-06', '2022-04-11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Cancelled', 1, '2022-04-05 12:50:11', '2022-04-11 10:42:58'),
(22, 48, 17, 'normal', '2022-04-06', '2022-04-06', 10, 342, NULL, 342, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-05 13:02:04', '2022-04-11 10:38:28'),
(23, 47, 17, 'normal', '2022-04-08', '2022-04-08', 10, 342, NULL, 1026, 15, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-07 07:18:54', '2022-04-11 10:40:53'),
(24, 47, 17, 'normal', '2022-04-08', '2022-04-08', 10, 342, NULL, 684, 15, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-07 07:23:47', '2022-04-11 05:01:27'),
(25, 47, 8, 'normal', '2022-04-09', '2022-04-09', 9, 200, 2, 200, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-08 09:36:09', '2022-04-11 09:21:17'),
(26, 47, 7, 'challenge', '2022-04-09', '2022-04-09', 9, 200, 1, 600, 30, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-08 10:12:29', '2022-04-12 10:12:44'),
(27, 40, 19, 'challenge', '2022-03-30', '2022-03-30', 11, 600, NULL, 200, 12, NULL, '1212121212', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-08 13:42:11', '2022-04-11 06:16:24'),
(28, 58, 21, 'normal', '2022-04-10', '2022-04-10', 21, 456, NULL, 456, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-09 11:06:57', '2022-04-11 09:21:09'),
(29, 58, 22, 'normal', '2022-04-10', '2022-04-10', 22, 10, NULL, 10, 10, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-09 11:35:58', '2022-04-11 09:18:43'),
(30, 60, 22, 'normal', '2022-04-11', '2022-04-15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Cancelled', 1, '2022-04-09 11:38:14', '2022-04-11 09:18:37'),
(31, 57, 21, 'normal', '2022-04-12', '2022-04-12', 21, 456, NULL, 456, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-11 05:08:19', '2022-04-11 05:08:29'),
(32, 57, 8, 'challenge', '2022-04-12', '2022-04-12', 9, 200, 2, 200, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-11 05:35:49', '2022-04-11 10:01:46'),
(33, 62, 6, 'challenge', '2022-04-12', '2022-04-12', 8, 1, 1, 1, 22, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-11 09:28:20', '2022-05-06 13:50:38'),
(34, 41, 7, 'challenge', '2022-04-12', '2022-04-12', 9, 200, 1, 200, 30, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-11 10:02:29', '2022-04-22 06:03:43'),
(35, 41, 6, 'challenge', '2022-04-13', '2022-04-13', 8, 1, 1, 1, 22, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-11 10:09:49', '2022-05-06 13:50:38'),
(36, 47, 22, 'normal', '2022-04-12', '2022-04-12', 22, 10, NULL, 20, 10, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-11 10:44:49', '2022-04-12 10:32:08'),
(37, 41, 22, 'normal', '2022-04-16', '2022-04-16', 22, 10, NULL, 20, 10, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-11 10:46:37', '2022-05-06 13:50:38'),
(38, 64, 20, 'normal', '2022-04-12', '2022-04-12', 19, 500, NULL, 3500, 15, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-11 11:05:25', '2022-05-06 13:50:38'),
(39, 64, 19, 'challenge', '2022-04-12', '2022-04-12', 19, 600, NULL, 3000, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-11 11:08:34', '2022-05-06 13:50:38'),
(40, 64, 19, 'challenge', '2022-04-13', '2022-04-13', 19, 600, NULL, 3000, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-11 11:11:32', '2022-05-06 13:50:38'),
(41, 67, 6, 'challenge', '2022-04-16', '2022-04-16', 8, 1, 1, 2, 22, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-11 11:32:04', '2022-05-06 13:50:38'),
(42, 69, 20, 'normal', '2022-04-15', '2022-04-15', 19, 500, NULL, 1000, 15, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-13 09:25:24', '2022-04-13 09:29:41'),
(43, 69, 6, 'challenge', '2022-04-15', '2022-04-15', 8, 1, 1, 3, 22, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-13 09:26:09', '2022-04-13 09:29:52'),
(44, 72, 23, 'normal', '2022-04-15', '2022-04-15', 23, 200, NULL, 200, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-14 05:34:23', '2022-05-06 13:50:38'),
(45, 72, 19, 'challenge', '2022-04-15', '2022-04-15', 19, 600, NULL, 1800, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-14 05:44:29', '2022-05-06 13:50:38'),
(46, 47, 23, 'normal', '2022-04-15', '2022-04-15', 23, 200, NULL, 200, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-14 11:37:50', '2022-05-05 05:19:04'),
(47, 73, 23, 'normal', '2022-04-16', '2022-04-16', 23, 200, NULL, 400, 12, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-15 05:22:58', '2022-04-15 05:23:07'),
(48, 73, 22, 'normal', '2022-04-16', '2022-04-16', 22, 10, NULL, 20, 10, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-04-15 05:34:51', '2022-05-06 13:50:38'),
(49, 73, 9, 'challenge', '2022-04-16', '2022-04-16', 12, 200, 1, 400, 30, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-15 05:55:57', '2022-05-06 13:50:38'),
(50, 53, 23, 'normal', '2022-04-17', '2022-04-17', 23, 200, NULL, 600, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-04-15 10:03:09', '2022-05-06 13:50:38'),
(51, 53, 22, 'normal', '2022-04-17', '2022-04-17', 22, 10, NULL, 40, 10, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-04-15 10:04:46', '2022-05-06 13:50:38'),
(52, 53, 21, 'normal', '2022-04-17', '2022-04-17', 21, 456, NULL, 1368, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-15 10:05:21', '2022-05-06 13:50:38'),
(53, 80, 23, 'normal', '2022-04-17', '2022-04-17', 23, 200, NULL, 400, 12, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-15 12:36:02', '2022-04-15 13:34:25'),
(54, 80, 22, 'normal', '2022-04-18', '2022-04-18', 22, 10, NULL, 20, 10, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-15 12:36:35', '2022-05-06 13:50:38'),
(55, 80, 19, 'challenge', '2022-04-16', '2022-04-16', 19, 600, NULL, 1800, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-15 12:37:34', '2022-05-06 13:50:38'),
(56, 79, 15, 'challenge', '2022-04-16', '2022-04-16', 11, 43, NULL, 43, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-15 12:52:23', '2022-05-06 13:50:38'),
(57, 79, 8, 'challenge', '2022-04-16', '2022-04-16', 9, 200, 2, 200, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-04-15 13:14:55', '2022-05-06 13:50:38'),
(58, 79, 13, 'challenge', '2022-04-16', '2022-04-16', 10, 342, NULL, 342, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-04-15 13:16:07', '2022-05-06 13:50:38'),
(59, 47, 17, 'challenge', '2022-04-19', '2022-04-19', 10, 342, NULL, 1026, 15, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-18 05:52:18', '2022-04-21 12:56:43'),
(60, 47, 25, 'challenge', '2022-04-20', '2022-04-20', 26, 333, NULL, 666, 12, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-19 04:54:37', '2022-04-22 08:56:39'),
(61, 47, 8, 'challenge', '2022-04-21', '2022-04-21', 9, 200, 2, 200, 12, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-20 09:22:01', '2022-05-04 08:35:50'),
(62, 47, 9, 'challenge', '2022-04-21', '2022-04-21', 12, 200, 1, 400, 30, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-20 09:25:29', '2022-04-20 09:55:05'),
(63, 87, 6, 'challenge', '2022-04-21', '2022-04-21', 8, 1, 1, 2, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-20 13:58:01', '2022-04-22 04:41:17'),
(64, 58, 13, 'challenge', '2022-04-21', '2022-04-21', 10, 342, NULL, 342, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-04-20 14:01:01', '2022-05-06 13:50:38'),
(65, 91, 10, 'challenge', '2022-04-22', '2022-04-22', 12, 15, NULL, 30, 10, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-21 05:28:20', '2022-05-05 10:35:06'),
(66, 93, 6, 'challenge', '2022-04-22', '2022-04-22', 8, 1, 1, 2, 22, NULL, '', 'cash', 'public', 'Pending', 'Accepted', 1, '2022-04-21 11:56:57', '2022-04-21 13:22:22'),
(67, 93, 25, 'normal', '2022-04-22', '2022-04-22', 26, 333, NULL, 666, 12, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-21 12:02:05', '2022-04-21 12:55:11'),
(68, 96, 25, 'normal', '2022-04-23', '2022-04-23', 26, 333, NULL, 333, 12, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-21 13:50:00', '2022-04-21 14:50:52'),
(69, 96, 25, 'normal', '2022-04-22', '2022-04-22', 26, 333, NULL, 666, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-04-21 14:40:43', '2022-05-06 13:50:38'),
(70, 87, 6, 'normal', '2022-04-23', '2022-04-23', 8, 1, 1, 2, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-22 12:50:00', '2022-05-06 12:41:04'),
(71, 87, 25, 'normal', '2022-04-23', '2022-04-23', 26, 333, NULL, 666, 12, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-04-22 12:59:53', '2022-05-06 12:43:52'),
(72, 87, 25, 'normal', '2022-04-23', '2022-04-23', 26, 333, NULL, 666, 12, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-22 13:01:16', '2022-05-06 12:43:46'),
(73, 91, 25, 'normal', '2022-04-23', '2022-04-23', 26, 333, NULL, 666, 12, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-22 13:40:31', '2022-05-05 10:34:56'),
(74, 91, 24, 'normal', '2022-04-23', '2022-04-23', 23, 123, NULL, 246, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-04-22 13:40:40', '2022-04-25 13:00:02'),
(75, 91, 24, 'normal', '2022-04-26', '2022-04-26', 23, 123, NULL, 123, 12, NULL, '', 'cash', 'public', 'Pending', 'Accepted', 1, '2022-04-25 06:20:56', '2022-04-25 13:36:37'),
(76, 91, 6, 'normal', '2022-04-26', '2022-04-26', 8, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-25 07:11:46', '2022-04-25 13:36:04'),
(77, 97, 7, 'challenge', '2022-04-26', '2022-04-26', 9, 200, 1, 200, 30, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-04-25 14:09:39', '2022-05-06 13:50:38'),
(78, 91, 6, 'normal', '2022-04-30', '2022-04-30', 8, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-04-29 10:33:31', '2022-05-03 13:27:25'),
(79, 91, 25, 'challenge', '2022-04-08', '2022-04-08', 27, 333, NULL, 1000, 12, NULL, '1212121212', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-05-02 09:31:39', '2022-05-05 10:35:09'),
(80, 91, 6, 'normal', '2022-05-03', '2022-05-03', 9, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-02 11:57:07', '2022-05-05 10:35:03'),
(81, 80, 20, 'normal', '2022-05-04', '2022-05-04', 19, 500, NULL, 1000, 15, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-03 08:44:14', '2022-05-06 13:50:38'),
(82, 80, 22, 'normal', '2022-05-04', '2022-05-04', 22, 10, NULL, 20, 10, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-03 10:09:28', '2022-05-06 13:50:38'),
(83, 80, 25, 'normal', '2022-05-04', '2022-05-04', 26, 333, NULL, 666, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-03 10:11:11', '2022-05-06 13:50:38'),
(84, 80, 24, 'challenge', '2022-05-04', '2022-05-04', 23, 123, NULL, 246, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-03 10:12:23', '2022-05-06 13:50:38'),
(85, 103, 22, 'challenge', '2022-05-04', '2022-05-04', 22, 10, NULL, 20, 10, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-03 12:37:16', '2022-05-03 13:07:14'),
(86, 103, 22, 'normal', '2022-05-04', '2022-05-04', 22, 10, NULL, 20, 10, NULL, '2234323424', 'online', 'public', 'Pending', 'Cancelled', 1, '2022-05-03 12:38:02', '2022-05-03 12:40:06'),
(87, 103, 20, 'challenge', '2022-05-04', '2022-05-04', 19, 500, NULL, 1000, 15, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-03 12:55:08', '2022-05-03 13:07:17'),
(88, 103, 22, 'normal', '2022-05-04', '2022-05-04', 22, 10, NULL, 10, 10, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-03 13:00:50', '2022-05-03 13:03:51'),
(89, 103, 22, 'normal', '2022-05-04', '2022-05-04', 22, 10, NULL, 20, 10, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-03 13:03:17', '2022-05-03 13:07:11'),
(90, 103, 19, 'challenge', '2022-05-04', '2022-05-04', 19, 600, NULL, 1800, 0, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-03 13:12:55', '2022-05-03 13:13:02'),
(91, 103, 6, 'normal', '2022-05-04', '2022-05-04', 9, 1, 1, 2, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-03 13:13:51', '2022-05-03 13:18:46'),
(92, 103, 6, 'normal', '2022-05-04', '2022-05-04', 9, 1, 1, 2, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-03 13:14:05', '2022-05-03 13:18:50'),
(93, 103, 6, 'normal', '2022-05-04', '2022-05-04', 9, 1, 1, 3, 22, NULL, '', 'cash', 'public', 'Pending', 'Accepted', 1, '2022-05-03 13:14:21', '2022-05-03 13:22:07'),
(94, 103, 6, 'normal', '2022-05-04', '2022-05-04', 9, 1, 1, 2, 22, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-03 13:20:26', '2022-05-06 13:50:38'),
(95, 91, 6, 'normal', '2022-05-04', '2022-05-04', 9, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-03 13:27:16', '2022-05-05 10:35:00'),
(96, 47, 6, 'normal', '2022-05-05', '2022-05-05', 9, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-04 04:53:09', '2022-05-05 05:19:33'),
(97, 47, 6, 'normal', '2022-05-05', '2022-05-05', 9, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-04 05:14:38', '2022-05-05 05:19:36'),
(98, 47, 6, 'normal', '2022-05-06', '2022-05-06', 9, 1, 1, 2, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-05 05:18:20', '2022-05-05 05:19:41'),
(99, 47, 8, 'challenge', '2022-05-06', '2022-05-06', 9, 200, 2, 200, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-05 07:28:38', '2022-05-10 12:33:13'),
(100, 47, 6, 'normal', '2022-05-06', '2022-05-06', 9, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-05 07:32:54', '2022-05-06 11:24:21'),
(101, 47, 22, 'normal', '2022-05-06', '2022-05-06', 22, 10, NULL, 10, 10, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-05 07:33:05', '2022-05-10 12:33:13'),
(102, 104, 6, 'normal', '2022-05-06', '2022-05-06', 9, 1, 1, 2, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-05 09:01:02', '2022-05-05 09:01:43'),
(103, 104, 6, 'normal', '2022-05-06', '2022-05-06', 9, 1, 1, 2, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-05 09:02:22', '2022-05-05 09:04:26'),
(104, 104, 13, 'challenge', '2022-05-06', '2022-05-06', 10, 342, NULL, 342, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-05 09:05:20', '2022-05-10 12:33:13'),
(105, 97, 22, 'challenge', '2022-05-12', '2022-05-12', 12, 10, NULL, 1000, 10, NULL, '1212121212', 'online', 'public', 'Pending', 'Accepted', 1, '2022-05-05 09:21:31', '2022-05-11 08:37:50'),
(106, 97, 21, 'challenge', '2022-05-12', '2022-05-12', 12, 456, NULL, 1000, 12, NULL, '1212121212', 'online', 'public', 'Pending', 'Completed', 1, '2022-05-05 09:43:07', '2022-05-26 12:44:32'),
(107, 97, 20, 'challenge', '2022-05-12', '2022-05-12', 12, 500, NULL, 1000, 15, NULL, '1212121212', 'online', 'public', 'Pending', 'Completed', 1, '2022-05-05 10:09:53', '2022-05-26 12:44:32'),
(108, 97, 19, 'challenge', '2022-05-12', '2022-05-12', 12, 600, NULL, 1000, 0, NULL, '1212121212', 'online', 'public', 'Pending', 'Completed', 1, '2022-05-05 10:10:22', '2022-05-26 12:44:32'),
(109, 91, 7, 'challenge', '2022-05-06', '2022-05-06', 9, 200, 1, 200, 30, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-05 10:35:56', '2022-05-05 10:36:46'),
(110, 91, 6, 'normal', '2022-05-06', '2022-05-06', 9, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-05 10:36:14', '2022-05-05 10:36:39'),
(111, 91, 22, 'normal', '2022-05-06', '2022-05-06', 22, 10, NULL, 20, 10, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-05 10:36:31', '2022-05-05 10:36:43'),
(112, 91, 7, 'challenge', '2022-05-06', '2022-05-06', 9, 200, 1, 200, 30, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-05 11:13:56', '2022-05-05 11:24:57'),
(113, 91, 17, 'challenge', '2022-05-06', '2022-05-06', 10, 342, NULL, 342, 15, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-05 11:24:29', '2022-05-05 11:24:37'),
(114, 91, 13, 'challenge', '2022-05-06', '2022-05-06', 10, 342, NULL, 342, 12, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-05 11:25:40', '2022-05-05 11:26:11'),
(115, 91, 10, 'challenge', '2022-05-06', '2022-05-06', 12, 15, NULL, 15, 10, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-05 11:25:58', '2022-05-05 11:26:07'),
(116, 91, 13, 'challenge', '2022-05-06', '2022-05-06', 10, 342, NULL, 342, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-05 11:59:05', '2022-05-10 12:33:13'),
(117, 47, 6, 'normal', '2022-05-07', '2022-05-07', 9, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-06 11:24:09', '2022-05-06 11:24:43'),
(118, 47, 6, 'normal', '2022-05-07', '2022-05-07', 9, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-06 11:24:53', '2022-05-10 12:33:13'),
(119, 87, 22, 'normal', '2022-05-07', '2022-05-07', 8, 10, NULL, 20, 10, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-06 12:40:47', '2022-05-06 12:43:42'),
(120, 87, 6, 'normal', '2022-05-07', '2022-05-07', 9, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-06 12:41:53', '2022-05-06 12:42:45'),
(121, 87, 6, 'challenge', '2022-05-07', '2022-05-07', 9, 1, 1, 2, 22, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-06 12:44:50', '2022-05-10 12:33:13'),
(122, 97, 24, 'normal', '2022-05-02', '2022-05-02', 19, 123, NULL, 738, 12, NULL, '2325666666666', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-06 12:45:38', '2022-05-06 13:50:38'),
(123, 87, 6, 'normal', '2022-05-07', '2022-05-07', 9, 1, 1, 2, 22, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-06 12:51:13', '2022-05-10 12:33:13'),
(124, 91, 7, 'challenge', '2022-05-07', '2022-05-07', 9, 200, 1, 400, 30, NULL, '', 'cash', 'public', 'Pending', 'Accepted', 1, '2022-05-06 13:30:04', '2022-05-10 06:12:45'),
(125, 91, 22, 'normal', '2022-05-07', '2022-05-07', 8, 10, NULL, 30, 10, NULL, '', 'cash', 'public', 'Pending', 'Accepted', 1, '2022-05-06 13:30:48', '2022-05-10 05:59:57'),
(126, 87, 22, 'challenge', '2022-05-13', '2022-05-13', 8, 10, NULL, 30, 10, NULL, '', 'cash', 'public', 'Pending', 'Accepted', 1, '2022-05-06 13:34:12', '2022-05-10 05:56:35'),
(127, 91, 6, 'normal', '2022-05-11', '2022-05-11', 9, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-10 06:19:15', '2022-05-13 10:05:13'),
(128, 87, 26, 'normal', '2022-05-11', '2022-05-11', 19, 100, NULL, 200, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-10 06:47:29', '2022-05-26 12:44:32'),
(129, 87, 26, 'challenge', '2022-05-11', '2022-05-11', 19, 100, NULL, 200, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-10 06:50:44', '2022-05-26 12:44:32'),
(130, 1, 26, 'normal', '2022-05-10', '2022-05-31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public', 'Pending', 'Cancelled', 1, '2022-05-10 07:28:40', '2022-05-10 09:00:23'),
(131, 87, 26, 'challenge', '2022-05-11', '2022-05-11', 19, 100, NULL, 200, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-10 07:30:28', '2022-05-26 12:44:32'),
(132, 87, 22, 'challenge', '2022-05-11', '2022-05-11', 8, 10, NULL, 10, 10, NULL, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-10 08:36:56', '2022-05-10 09:00:29'),
(133, 102, 6, 'normal', '2022-05-11', '2022-05-11', 9, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-10 09:18:36', '2022-05-26 12:44:32'),
(134, 102, 6, 'normal', '2022-05-11', '2022-05-11', 9, 1, 1, 1, 22, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-10 09:19:35', '2022-05-26 12:44:32'),
(135, 109, 20, 'challenge', '2022-05-11', '2022-05-11', 19, 500, NULL, 1000, 15, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-10 09:48:18', '2022-05-26 12:44:32'),
(136, 109, 22, 'challenge', '2022-05-11', '2022-05-11', 8, 10, NULL, 30, 10, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-10 09:48:32', '2022-05-26 12:44:32'),
(137, 109, 9, 'challenge', '2022-05-11', '2022-05-11', 12, 200, 1, 600, 30, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-10 09:48:54', '2022-05-26 12:44:32'),
(138, 87, 24, 'challenge', '2022-05-11', '2022-05-11', 19, 123, NULL, 246, 12, NULL, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-10 10:38:49', '2022-05-26 12:44:32'),
(139, 87, 19, 'challenge', '2022-05-12', '2022-05-12', 19, 600, NULL, 600, 0, NULL, '', 'cash', 'public', 'Pending', 'Accepted', 1, '2022-05-10 10:40:43', '2022-05-11 08:36:00'),
(140, 87, 6, 'normal', '2022-05-11', '2022-05-11', 9, 1, 1, 2, 22, NULL, '', 'cash', 'public', 'Pending', 'Accepted', 1, '2022-05-10 10:55:23', '2022-05-11 08:35:34'),
(141, 87, 6, 'normal', '2022-05-11', '2022-05-11', 9, 1, 1, 2, 22, NULL, '', 'cash', 'public', 'Pending', 'Accepted', 1, '2022-05-10 10:58:03', '2022-05-10 11:27:54'),
(142, 87, 6, 'normal', '2022-05-11', '2022-05-11', 9, 1, 1, 3, 22, 0.66, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-10 11:23:15', '2022-05-10 11:27:14'),
(143, 109, 22, 'normal', '2022-05-12', '2022-05-12', 8, 10, NULL, 20, 10, 2.00, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-11 06:42:43', '2022-05-26 12:44:32'),
(144, 47, 6, 'normal', '2022-05-12', '2022-05-12', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-11 11:07:24', '2022-05-26 12:44:32'),
(145, 87, 22, 'normal', '2022-05-12', '2022-05-12', 8, 10, NULL, 20, 10, 2.00, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-11 11:11:06', '2022-05-26 12:44:32'),
(146, 87, 6, 'normal', '2022-05-12', '2022-05-12', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-11 11:13:21', '2022-05-26 12:44:32'),
(147, 111, 6, 'normal', '2022-05-12', '2022-05-12', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-11 11:15:20', '2022-05-11 11:24:46'),
(148, 111, 22, 'normal', '2022-05-12', '2022-05-12', 8, 10, NULL, 10, 10, 1.00, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-11 11:16:12', '2022-05-11 11:36:45'),
(149, 111, 24, 'normal', '2022-05-13', '2022-05-13', 19, 123, NULL, 246, 12, 29.52, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-11 11:17:05', '2022-05-26 12:44:32'),
(150, 111, 17, 'normal', '2022-05-12', '2022-05-12', 10, 342, NULL, 342, 15, 51.30, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-11 11:18:01', '2022-05-11 11:25:16'),
(151, 111, 22, 'normal', '2022-05-12', '2022-05-12', 8, 10, NULL, 30, 10, 3.00, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-11 11:19:29', '2022-05-26 12:44:32'),
(152, 111, 22, 'normal', '2022-05-14', '2022-05-14', 8, 10, NULL, 30, 10, 3.00, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-11 11:20:37', '2022-05-26 12:44:32'),
(153, 111, 6, 'normal', '2022-05-12', '2022-05-12', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-11 11:31:40', '2022-05-26 12:44:32'),
(154, 87, 22, 'challenge', '2022-05-14', '2022-05-14', 8, 10, NULL, 30, 10, 3.00, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-11 13:27:30', '2022-05-26 12:44:32'),
(155, 47, 6, 'normal', '2022-05-13', '2022-05-13', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-12 11:14:21', '2022-05-26 12:44:32'),
(156, 91, 6, 'normal', '2022-05-14', '2022-05-14', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-13 08:51:13', '2022-05-26 12:44:32'),
(157, 91, 6, 'normal', '2022-05-14', '2022-05-14', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-13 09:06:33', '2022-05-26 12:44:32'),
(158, 91, 7, 'challenge', '2022-05-14', '2022-05-14', 9, 200, 1, 200, 30, 60.00, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-13 09:08:50', '2022-05-26 12:44:32'),
(159, 91, 6, 'normal', '2022-05-14', '2022-05-14', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-13 10:06:26', '2022-05-26 12:44:32'),
(160, 91, 15, 'challenge', '2022-05-14', '2022-05-14', 11, 43, NULL, 43, 12, 5.16, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-13 10:07:39', '2022-05-26 12:44:32'),
(161, 97, 24, 'challenge', '2022-05-17', '2022-05-17', 19, 123, NULL, 369, 12, 44.28, '2325666666666', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-17 11:41:38', '2022-05-26 12:44:32'),
(162, 97, 26, 'challenge', '2022-05-23', '2022-05-23', 19, 100, NULL, 300, 20, 60.00, '2325666666666', 'cash', 'public', 'Pending', 'Accepted', 1, '2022-05-17 13:32:15', '2022-05-18 04:57:16'),
(163, 87, 6, 'normal', '2022-05-19', '2022-05-19', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-18 05:01:07', '2022-05-26 12:44:32'),
(164, 87, 22, 'normal', '2022-05-19', '2022-05-19', 8, 10, NULL, 20, 10, 2.00, '2234323424', 'online', 'public', 'Pending', 'Completed', 1, '2022-05-18 05:02:56', '2022-05-26 12:44:32'),
(165, 112, 20, 'challenge', '2022-05-24', '2022-05-24', 19, 500, NULL, 1000, 15, 150.00, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-23 06:45:28', '2022-05-25 12:47:38'),
(166, 114, 15, 'challenge', '2022-05-24', '2022-05-24', 11, 43, NULL, 43, 12, 5.16, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-23 12:14:27', '2022-05-26 12:44:32'),
(167, 114, 15, 'challenge', '2022-05-24', '2022-05-24', 11, 43, NULL, 43, 12, 5.16, '', 'cash', 'private', 'Pending', 'Completed', 1, '2022-05-23 12:15:26', '2022-05-26 12:44:32'),
(168, 116, 22, 'challenge', '2022-05-26', '2022-05-26', 8, 10, NULL, 10, 10, 1.00, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-25 05:36:03', '2022-05-30 04:16:59'),
(169, 87, 7, 'challenge', '2022-05-26', '2022-05-26', 9, 200, 1, 200, 30, 60.00, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-25 07:21:09', '2022-05-30 04:16:59'),
(170, 115, 22, 'challenge', '2022-05-26', '2022-05-26', 8, 10, NULL, 10, 10, 1.00, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-25 08:45:43', '2022-05-30 04:16:59'),
(171, 71, 22, 'challenge', '2022-05-27', '2022-05-27', 8, 10, NULL, 10, 10, 1.00, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-26 05:50:31', '2022-05-30 04:16:59'),
(172, 71, 17, 'challenge', '2022-05-27', '2022-05-27', 10, 342, NULL, 342, 15, 51.30, '', 'cash', 'private', 'Pending', 'Completed', 1, '2022-05-26 09:25:09', '2022-05-30 04:16:59'),
(173, 71, 22, 'challenge', '2022-05-28', '2022-05-28', 8, 10, NULL, 10, 10, 1.00, '', 'cash', 'private', 'Pending', 'Completed', 1, '2022-05-26 09:28:57', '2022-05-30 04:16:59'),
(174, 102, 7, 'normal', '2022-05-27', '2022-05-27', 9, 200, 1, 400, 30, 120.00, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-26 12:49:12', '2022-05-30 04:16:59'),
(175, 102, 19, 'challenge', '2022-05-27', '2022-05-27', 19, 600, NULL, 1200, 0, 0.00, '', 'cash', 'private', 'Pending', 'Cancelled', 1, '2022-05-26 12:51:03', '2022-05-26 12:52:11'),
(176, 47, 22, 'challenge', '2022-05-28', '2022-05-28', 8, 10, NULL, 10, 10, 1.00, '', 'cash', 'private', 'Pending', 'Completed', 1, '2022-05-27 07:26:10', '2022-05-30 04:16:59'),
(177, 87, 20, 'challenge', '2022-05-28', '2022-05-28', 19, 500, NULL, 1000, 15, 150.00, '', 'cash', 'public', 'Pending', 'Completed', 1, '2022-05-27 09:03:30', '2022-05-30 04:16:59'),
(178, 87, 20, 'challenge', '2022-05-28', '2022-05-28', 19, 500, NULL, 1000, 15, 150.00, '2234323424', 'online', 'private', 'Pending', 'Cancelled', 1, '2022-05-27 09:06:29', '2022-05-27 09:06:59'),
(179, 87, 10, 'challenge', '2022-05-28', '2022-05-28', 12, 15, NULL, 30, 10, 3.00, '', 'cash', 'private', 'Pending', 'Completed', 1, '2022-05-27 09:07:14', '2022-05-30 04:16:59'),
(180, 87, 19, 'challenge', '2022-05-28', '2022-05-28', 19, 600, NULL, 1200, 0, 0.00, '', 'cash', 'private', 'Pending', 'Completed', 1, '2022-05-27 10:04:28', '2022-05-30 04:16:59'),
(181, 87, 20, 'challenge', '2022-05-31', '2022-05-31', 19, 500, NULL, 1000, 15, 150.00, '', 'cash', 'private', 'Pending', 'Pending', 1, '2022-05-30 05:48:14', '2022-05-30 05:48:14'),
(182, 87, 19, 'challenge', '2022-05-31', '2022-05-31', 19, 600, NULL, 1200, 0, 0.00, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 05:49:19', '2022-05-30 05:49:19'),
(183, 87, 6, 'normal', '2022-05-31', '2022-05-31', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 05:50:20', '2022-05-30 05:50:20'),
(184, 87, 6, 'normal', '2022-05-31', '2022-05-31', 9, 1, 1, 2, 22, 0.44, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 05:57:11', '2022-05-30 05:57:11'),
(185, 71, 6, 'challenge', '2022-05-31', '2022-05-31', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 06:01:25', '2022-05-30 06:01:25'),
(186, 87, 24, 'normal', '2022-05-31', '2022-05-31', 19, 123, NULL, 246, 12, 29.52, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 06:13:31', '2022-05-30 06:13:31'),
(187, 47, 22, 'challenge', '2022-05-31', '2022-05-31', 8, 10, NULL, 10, 10, 1.00, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 09:07:47', '2022-05-30 09:07:47'),
(188, 47, 7, 'challenge', '2022-06-01', '2022-06-01', 9, 200, 1, 200, 30, 60.00, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 09:34:51', '2022-05-30 09:34:51'),
(189, 47, 6, 'challenge', '2022-05-31', '2022-05-31', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 09:35:09', '2022-05-30 09:35:09'),
(190, 87, 21, 'challenge', '2022-05-31', '2022-05-31', 21, 456, NULL, 912, 12, 109.44, '', 'cash', 'private', 'Pending', 'Pending', 1, '2022-05-30 09:37:28', '2022-05-30 09:37:28'),
(191, 49, 6, 'normal', '2022-05-31', '2022-05-31', 9, 1, 1, 2, 22, 0.44, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 10:38:01', '2022-05-30 10:38:01'),
(192, 49, 22, 'challenge', '2022-05-31', '2022-05-31', 8, 10, NULL, 20, 10, 2.00, '', 'cash', 'private', 'Pending', 'Pending', 1, '2022-05-30 10:38:20', '2022-05-30 10:38:20'),
(193, 87, 6, 'normal', '2022-05-31', '2022-05-31', 9, 1, 1, 2, 22, 0.44, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 10:39:00', '2022-05-30 10:39:00'),
(194, 49, 6, 'challenge', '2022-06-01', '2022-06-01', 9, 1, 1, 2, 22, 0.44, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 10:39:34', '2022-05-30 10:39:34'),
(195, 87, 22, 'normal', '2022-05-31', '2022-05-31', 8, 10, NULL, 20, 10, 2.00, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 10:48:20', '2022-05-30 10:48:20'),
(196, 87, 26, 'normal', '2022-05-31', '2022-05-31', 19, 100, NULL, 300, 20, 60.00, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 10:48:41', '2022-05-30 10:48:41'),
(197, 87, 7, 'challenge', '2022-05-31', '2022-05-31', 9, 200, 1, 600, 30, 180.00, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 10:49:02', '2022-05-30 10:49:02'),
(198, 87, 6, 'challenge', '2022-06-02', '2022-06-02', 9, 1, 1, 3, 22, 0.66, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-30 10:49:42', '2022-05-30 11:08:15'),
(199, 87, 22, 'normal', '2022-05-31', '2022-05-31', 8, 10, NULL, 20, 10, 2.00, '', 'cash', 'public', 'Received', 'Accepted', 1, '2022-05-30 10:51:08', '2022-05-30 10:51:41'),
(200, 47, 24, 'normal', '2022-06-01', '2022-06-01', 19, 123, NULL, 123, 12, 14.76, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 10:57:43', '2022-05-30 10:57:43'),
(201, 47, 6, 'normal', '2022-06-05', '2022-06-05', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 11:02:46', '2022-05-30 11:02:46'),
(202, 47, 21, 'normal', '2022-05-31', '2022-05-31', 21, 456, NULL, 456, 12, 54.72, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 11:06:05', '2022-05-30 11:06:05'),
(203, 121, 26, 'normal', '2022-05-31', '2022-05-31', 19, 100, NULL, 100, 20, 20.00, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 11:10:53', '2022-05-30 11:10:53'),
(204, 121, 22, 'challenge', '2022-05-31', '2022-05-31', 8, 10, NULL, 10, 10, 1.00, '', 'cash', 'public', 'Pending', 'Cancelled', 1, '2022-05-30 11:11:18', '2022-05-30 11:12:00'),
(205, 121, 6, 'normal', '2022-05-31', '2022-05-31', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 11:12:27', '2022-05-30 11:12:27'),
(206, 122, 26, 'normal', '2022-05-31', '2022-05-31', 19, 100, NULL, 100, 20, 20.00, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 11:14:15', '2022-05-30 11:14:15'),
(207, 122, 22, 'normal', '2022-06-01', '2022-06-01', 8, 10, NULL, 10, 10, 1.00, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 11:15:17', '2022-05-30 11:15:17'),
(208, 123, 6, 'normal', '2022-05-31', '2022-05-31', 9, 1, 1, 2, 22, 0.44, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 11:18:55', '2022-05-30 11:18:55'),
(209, 123, 22, 'normal', '2022-05-31', '2022-05-31', 8, 10, NULL, 20, 10, 2.00, '2234323424', 'online', 'public', 'Pending', 'Pending', 1, '2022-05-30 11:19:24', '2022-05-30 11:19:24'),
(210, 123, 24, 'challenge', '2022-05-31', '2022-05-31', 19, 123, NULL, 246, 12, 29.52, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 11:20:27', '2022-05-30 11:20:27'),
(211, 87, 6, 'normal', '2022-06-06', '2022-06-06', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 11:41:00', '2022-05-30 11:41:00'),
(212, 124, 6, 'normal', '2022-06-02', '2022-06-02', 9, 1, 1, 2, 22, 0.44, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 11:42:44', '2022-05-30 11:42:44'),
(213, 124, 22, 'normal', '2022-06-01', '2022-06-01', 8, 10, NULL, 20, 10, 2.00, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 11:43:14', '2022-05-30 11:43:14'),
(214, 124, 20, 'challenge', '2022-05-31', '2022-05-31', 19, 500, NULL, 1000, 15, 150.00, '2325666666666', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 11:44:30', '2022-05-30 11:44:30'),
(215, 87, 22, 'normal', '2022-08-24', '2022-08-24', 10, 10, NULL, 1000, 10, 100.00, '1212121212', 'online', 'private', 'Pending', 'Pending', 1, '2022-05-30 11:45:19', '2022-05-30 11:45:19'),
(216, 87, 22, 'normal', '2022-08-25', '2022-08-25', 10, 10, NULL, 1000, 10, 100.00, '1212121212', 'online', 'private', 'Pending', 'Pending', 1, '2022-05-30 11:46:02', '2022-05-30 11:46:02'),
(217, 124, 6, 'normal', '2022-06-01', '2022-06-01', 9, 1, 1, 1, 22, 0.22, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 12:00:47', '2022-05-30 12:00:47'),
(218, 124, 22, 'challenge', '2022-05-31', '2022-05-31', 8, 10, NULL, 10, 10, 1.00, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 12:01:13', '2022-05-30 12:01:13'),
(219, 124, 22, 'challenge', '2022-06-02', '2022-06-02', 8, 10, NULL, 10, 10, 1.00, '', 'cash', 'public', 'Pending', 'Pending', 1, '2022-05-30 12:02:22', '2022-05-30 12:02:22'),
(220, 87, 22, 'normal', '2022-08-26', '2022-08-26', 10, 10, NULL, 1000, 10, 100.00, '1212121212', 'online', 'private', 'Pending', 'Pending', 1, '2022-05-30 12:13:11', '2022-05-30 12:13:11');

-- --------------------------------------------------------

--
-- Table structure for table `court_booking_slots`
--

CREATE TABLE `court_booking_slots` (
  `id` bigint NOT NULL,
  `court_booking_id` int DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_start_time` time DEFAULT NULL,
  `booking_end_time` time DEFAULT NULL,
  `booking_start_datetime` datetime DEFAULT NULL,
  `booking_end_datetime` datetime DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `court_booking_slots`
--

INSERT INTO `court_booking_slots` (`id`, `court_booking_id`, `booking_date`, `booking_start_time`, `booking_end_time`, `booking_start_datetime`, `booking_end_datetime`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '2022-03-05', '00:55:00', '02:25:00', '2022-03-05 00:55:00', '2022-03-05 02:25:00', 1, '2022-03-04 12:40:46', '2022-03-04 12:40:46'),
(2, 1, '2022-03-05', '02:25:00', '03:55:00', '2022-03-05 02:25:00', '2022-03-05 03:55:00', 1, '2022-03-04 12:40:47', '2022-03-04 12:40:47'),
(3, 1, '2022-03-05', '03:55:00', '05:25:00', '2022-03-05 03:55:00', '2022-03-05 05:25:00', 1, '2022-03-04 12:40:47', '2022-03-04 12:40:47'),
(4, 1, '2022-03-05', '09:55:00', '11:25:00', '2022-03-05 09:55:00', '2022-03-05 11:25:00', 1, '2022-03-04 12:40:47', '2022-03-04 12:40:47'),
(5, 1, '2022-03-05', '12:55:00', '14:25:00', '2022-03-05 12:55:00', '2022-03-05 14:25:00', 1, '2022-03-04 12:40:47', '2022-03-04 12:40:47'),
(6, 1, '2022-03-05', '18:55:00', '20:25:00', '2022-03-05 18:55:00', '2022-03-05 20:25:00', 1, '2022-03-04 12:40:47', '2022-03-04 12:40:47'),
(7, 2, '2022-03-08', '10:00:00', '11:00:00', '2022-03-08 10:00:00', '2022-03-08 11:00:00', 1, '2022-03-04 13:42:57', '2022-03-04 13:42:57'),
(8, 2, '2022-03-08', '16:00:00', '17:00:00', '2022-03-08 16:00:00', '2022-03-08 17:00:00', 1, '2022-03-04 13:42:57', '2022-03-04 13:42:57'),
(9, 3, '2022-03-11', '00:05:00', '01:05:00', '2022-03-11 00:05:00', '2022-03-11 01:05:00', 1, '2022-03-04 13:46:39', '2022-03-04 13:46:39'),
(10, 3, '2022-03-11', '01:05:00', '02:05:00', '2022-03-11 01:05:00', '2022-03-11 02:05:00', 1, '2022-03-04 13:46:39', '2022-03-04 13:46:39'),
(11, 3, '2022-03-11', '02:05:00', '03:05:00', '2022-03-11 02:05:00', '2022-03-11 03:05:00', 1, '2022-03-04 13:46:39', '2022-03-04 13:46:39'),
(12, 3, '2022-03-11', '03:05:00', '04:05:00', '2022-03-11 03:05:00', '2022-03-11 04:05:00', 1, '2022-03-04 13:46:39', '2022-03-04 13:46:39'),
(13, 3, '2022-03-11', '04:05:00', '05:05:00', '2022-03-11 04:05:00', '2022-03-11 05:05:00', 1, '2022-03-04 13:46:39', '2022-03-04 13:46:39'),
(14, 3, '2022-03-11', '05:05:00', '06:05:00', '2022-03-11 05:05:00', '2022-03-11 06:05:00', 1, '2022-03-04 13:46:39', '2022-03-04 13:46:39'),
(15, 4, '2022-03-05', '00:05:00', '01:05:00', '2022-03-05 00:05:00', '2022-03-05 01:05:00', 1, '2022-03-04 13:46:57', '2022-03-04 13:46:57'),
(16, 4, '2022-03-05', '01:05:00', '02:05:00', '2022-03-05 01:05:00', '2022-03-05 02:05:00', 1, '2022-03-04 13:46:57', '2022-03-04 13:46:57'),
(17, 4, '2022-03-05', '02:05:00', '03:05:00', '2022-03-05 02:05:00', '2022-03-05 03:05:00', 1, '2022-03-04 13:46:57', '2022-03-04 13:46:57'),
(18, 5, '2022-03-14', '00:05:00', '01:05:00', '2022-03-14 00:05:00', '2022-03-14 01:05:00', 1, '2022-03-07 08:32:26', '2022-03-07 08:32:26'),
(19, 5, '2022-03-14', '06:05:00', '07:05:00', '2022-03-14 06:05:00', '2022-03-14 07:05:00', 1, '2022-03-07 08:32:26', '2022-03-07 08:32:26'),
(20, 5, '2022-03-14', '12:05:00', '13:05:00', '2022-03-14 12:05:00', '2022-03-14 13:05:00', 1, '2022-03-07 08:32:26', '2022-03-07 08:32:26'),
(21, 5, '2022-03-14', '18:05:00', '19:05:00', '2022-03-14 18:05:00', '2022-03-14 19:05:00', 1, '2022-03-07 08:32:26', '2022-03-07 08:32:26'),
(23, 6, '2022-03-13', '00:05:00', '01:05:00', '2022-03-13 00:05:00', '2022-03-13 01:05:00', 1, '2022-03-07 08:33:23', '2022-03-07 08:33:23'),
(24, 6, '2022-03-13', '01:05:00', '02:05:00', '2022-03-13 01:05:00', '2022-03-13 02:05:00', 1, '2022-03-07 08:33:23', '2022-03-07 08:33:23'),
(25, 6, '2022-03-13', '06:05:00', '07:05:00', '2022-03-13 06:05:00', '2022-03-13 07:05:00', 1, '2022-03-07 08:33:23', '2022-03-07 08:33:23'),
(26, 6, '2022-03-13', '07:05:00', '08:05:00', '2022-03-13 07:05:00', '2022-03-13 08:05:00', 1, '2022-03-07 08:33:23', '2022-03-07 08:33:23'),
(49, 7, '2022-03-08', '01:05:00', '02:05:00', '2022-03-08 01:05:00', '2022-03-08 02:05:00', 1, '2022-03-07 13:44:51', '2022-03-07 13:44:51'),
(50, 7, '2022-03-08', '13:05:00', '14:05:00', '2022-03-08 13:05:00', '2022-03-08 14:05:00', 1, '2022-03-07 13:44:51', '2022-03-07 13:44:51'),
(51, 7, '2022-03-08', '19:05:00', '20:05:00', '2022-03-08 19:05:00', '2022-03-08 20:05:00', 1, '2022-03-07 13:44:51', '2022-03-07 13:44:51'),
(52, 7, '2022-03-08', '21:05:00', '22:05:00', '2022-03-08 21:05:00', '2022-03-08 22:05:00', 1, '2022-03-07 13:44:51', '2022-03-07 13:44:51'),
(53, 9, '2022-03-09', '01:05:00', '02:05:00', '2022-03-09 01:05:00', '2022-03-09 02:05:00', 1, '2022-03-08 04:34:19', '2022-03-08 04:34:19'),
(54, 9, '2022-03-09', '02:05:00', '03:05:00', '2022-03-09 02:05:00', '2022-03-09 03:05:00', 1, '2022-03-08 04:34:19', '2022-03-08 04:34:19'),
(55, 9, '2022-03-09', '07:05:00', '08:05:00', '2022-03-09 07:05:00', '2022-03-09 08:05:00', 1, '2022-03-08 04:34:19', '2022-03-08 04:34:19'),
(56, 9, '2022-03-09', '08:05:00', '09:05:00', '2022-03-09 08:05:00', '2022-03-09 09:05:00', 1, '2022-03-08 04:34:19', '2022-03-08 04:34:19'),
(57, 9, '2022-03-09', '13:05:00', '14:05:00', '2022-03-09 13:05:00', '2022-03-09 14:05:00', 1, '2022-03-08 04:34:19', '2022-03-08 04:34:19'),
(58, 9, '2022-03-09', '14:05:00', '15:05:00', '2022-03-09 14:05:00', '2022-03-09 15:05:00', 1, '2022-03-08 04:34:19', '2022-03-08 04:34:19'),
(59, 9, '2022-03-09', '19:05:00', '20:05:00', '2022-03-09 19:05:00', '2022-03-09 20:05:00', 1, '2022-03-08 04:34:19', '2022-03-08 04:34:19'),
(60, 9, '2022-03-09', '20:05:00', '21:05:00', '2022-03-09 20:05:00', '2022-03-09 21:05:00', 1, '2022-03-08 04:34:19', '2022-03-08 04:34:19'),
(61, 8, '2022-03-10', '07:05:00', '08:05:00', '2022-03-10 07:05:00', '2022-03-25 08:05:00', 1, '2022-03-09 09:26:00', '2022-03-09 09:26:00'),
(62, 8, '2022-03-10', '08:05:00', '09:05:00', '2022-03-10 08:05:00', '2022-03-25 09:05:00', 1, '2022-03-09 09:26:00', '2022-03-09 09:26:00'),
(63, 8, '2022-03-10', '13:05:00', '14:05:00', '2022-03-10 13:05:00', '2022-03-25 14:05:00', 1, '2022-03-09 09:26:00', '2022-03-09 09:26:00'),
(64, 8, '2022-03-10', '14:05:00', '15:05:00', '2022-03-10 14:05:00', '2022-03-25 15:05:00', 1, '2022-03-09 09:26:00', '2022-03-09 09:26:00'),
(79, 11, '2022-04-01', '19:05:00', '20:05:00', '2022-04-01 19:05:00', '2022-04-30 20:05:00', 1, '2022-03-09 15:19:23', '2022-03-09 15:19:23'),
(90, 10, '2022-03-15', '04:05:00', '06:05:00', '2022-03-15 04:05:00', '2022-03-17 06:05:00', 1, '2022-03-15 15:05:54', '2022-03-15 15:05:54'),
(91, 10, '2022-03-15', '16:05:00', '18:05:00', '2022-03-15 16:05:00', '2022-03-17 18:05:00', 1, '2022-03-15 15:05:54', '2022-03-15 15:05:54'),
(92, 12, '2022-03-28', '08:00:00', '10:00:00', '2022-03-28 08:00:00', '2022-03-28 10:00:00', 1, '2022-03-29 12:22:56', '2022-03-29 12:22:56'),
(93, 13, '2022-03-28', '08:00:00', '10:00:00', '2022-03-28 08:00:00', '2022-03-28 10:00:00', 1, '2022-03-29 12:23:50', '2022-03-29 12:23:50'),
(94, 14, '2022-03-28', '08:00:00', '10:00:00', '2022-03-28 08:00:00', '2022-03-28 10:00:00', 1, '2022-03-31 04:31:23', '2022-03-31 04:31:23'),
(95, 15, '2022-03-28', '18:39:00', '19:09:00', '2022-03-28 18:39:00', '2022-03-28 19:09:00', 1, '2022-03-31 12:56:04', '2022-03-31 12:56:04'),
(96, 15, '2022-03-28', '19:09:00', '19:39:00', '2022-03-28 19:09:00', '2022-03-28 19:39:00', 1, '2022-03-31 12:56:04', '2022-03-31 12:56:04'),
(97, 16, '2022-04-02', '07:08:00', '07:48:00', '2022-04-02 07:08:00', '2022-04-02 07:48:00', 1, '2022-03-31 13:04:42', '2022-03-31 13:04:42'),
(98, 16, '2022-04-02', '07:48:00', '08:28:00', '2022-04-02 07:48:00', '2022-04-02 08:28:00', 1, '2022-03-31 13:04:42', '2022-03-31 13:04:42'),
(99, 17, '2022-03-28', '18:39:00', '19:09:00', '2022-03-28 18:39:00', '2022-03-28 19:09:00', 1, '2022-03-31 13:05:37', '2022-03-31 13:05:37'),
(100, 18, '2022-04-03', '22:39:00', '23:39:00', '2022-04-03 22:39:00', '2022-04-03 23:39:00', 1, '2022-03-31 13:21:02', '2022-03-31 13:21:02'),
(101, 19, '2022-03-28', '08:00:00', '10:00:00', '2022-03-28 08:00:00', '2022-03-28 10:00:00', 1, '2022-04-04 05:47:52', '2022-04-04 05:47:52'),
(102, 20, '2022-03-28', '08:00:00', '10:00:00', '2022-03-28 08:00:00', '2022-03-28 10:00:00', 1, '2022-04-04 08:12:41', '2022-04-04 08:12:41'),
(103, 21, '2022-04-06', '14:05:00', '14:25:00', '2022-04-06 14:05:00', '2022-04-11 14:25:00', 1, '2022-04-05 12:50:11', '2022-04-05 12:50:11'),
(104, 21, '2022-04-06', '14:25:00', '14:45:00', '2022-04-06 14:25:00', '2022-04-11 14:45:00', 1, '2022-04-05 12:50:11', '2022-04-05 12:50:11'),
(105, 21, '2022-04-06', '14:45:00', '15:05:00', '2022-04-06 14:45:00', '2022-04-11 15:05:00', 1, '2022-04-05 12:50:11', '2022-04-05 12:50:11'),
(106, 21, '2022-04-06', '15:05:00', '15:25:00', '2022-04-06 15:05:00', '2022-04-11 15:25:00', 1, '2022-04-05 12:50:11', '2022-04-05 12:50:11'),
(107, 21, '2022-04-06', '15:25:00', '15:45:00', '2022-04-06 15:25:00', '2022-04-11 15:45:00', 1, '2022-04-05 12:50:11', '2022-04-05 12:50:11'),
(108, 21, '2022-04-06', '15:45:00', '16:05:00', '2022-04-06 15:45:00', '2022-04-11 16:05:00', 1, '2022-04-05 12:50:11', '2022-04-05 12:50:11'),
(109, 21, '2022-04-06', '16:05:00', '16:25:00', '2022-04-06 16:05:00', '2022-04-11 16:25:00', 1, '2022-04-05 12:50:11', '2022-04-05 12:50:11'),
(110, 21, '2022-04-06', '16:25:00', '16:45:00', '2022-04-06 16:25:00', '2022-04-11 16:45:00', 1, '2022-04-05 12:50:11', '2022-04-05 12:50:11'),
(111, 21, '2022-04-06', '16:45:00', '17:05:00', '2022-04-06 16:45:00', '2022-04-11 17:05:00', 1, '2022-04-05 12:50:11', '2022-04-05 12:50:11'),
(112, 21, '2022-04-06', '17:05:00', '17:25:00', '2022-04-06 17:05:00', '2022-04-11 17:25:00', 1, '2022-04-05 12:50:11', '2022-04-05 12:50:11'),
(113, 21, '2022-04-06', '17:25:00', '17:45:00', '2022-04-06 17:25:00', '2022-04-11 17:45:00', 1, '2022-04-05 12:50:11', '2022-04-05 12:50:11'),
(114, 22, '2022-04-06', '14:25:00', '14:45:00', '2022-04-06 14:25:00', '2022-04-06 14:45:00', 1, '2022-04-05 13:02:04', '2022-04-05 13:02:04'),
(115, 23, '2022-04-08', '14:05:00', '14:25:00', '2022-04-08 14:05:00', '2022-04-08 14:25:00', 1, '2022-04-07 07:18:54', '2022-04-07 07:18:54'),
(116, 23, '2022-04-08', '14:25:00', '14:45:00', '2022-04-08 14:25:00', '2022-04-08 14:45:00', 1, '2022-04-07 07:18:54', '2022-04-07 07:18:54'),
(117, 23, '2022-04-08', '14:45:00', '15:05:00', '2022-04-08 14:45:00', '2022-04-08 15:05:00', 1, '2022-04-07 07:18:54', '2022-04-07 07:18:54'),
(118, 24, '2022-04-08', '19:25:00', '19:45:00', '2022-04-08 19:25:00', '2022-04-08 19:45:00', 1, '2022-04-07 07:23:47', '2022-04-07 07:23:47'),
(119, 24, '2022-04-08', '19:45:00', '20:05:00', '2022-04-08 19:45:00', '2022-04-08 20:05:00', 1, '2022-04-07 07:23:47', '2022-04-07 07:23:47'),
(120, 25, '2022-04-09', '19:34:00', '19:59:00', '2022-04-09 19:34:00', '2022-04-09 19:59:00', 1, '2022-04-08 09:36:09', '2022-04-08 09:36:09'),
(121, 26, '2022-04-09', '07:08:00', '07:48:00', '2022-04-09 07:08:00', '2022-04-09 07:48:00', 1, '2022-04-08 10:12:29', '2022-04-08 10:12:29'),
(122, 26, '2022-04-09', '07:48:00', '08:28:00', '2022-04-09 07:48:00', '2022-04-09 08:28:00', 1, '2022-04-08 10:12:29', '2022-04-08 10:12:29'),
(123, 26, '2022-04-09', '08:28:00', '09:08:00', '2022-04-09 08:28:00', '2022-04-09 09:08:00', 1, '2022-04-08 10:12:29', '2022-04-08 10:12:29'),
(124, 27, '2022-03-30', '08:00:00', '10:00:00', '2022-03-30 08:00:00', '2022-03-30 10:00:00', 1, '2022-04-08 13:42:11', '2022-04-08 13:42:11'),
(125, 28, '2022-04-10', '08:44:00', '09:24:00', '2022-04-10 08:44:00', '2022-04-10 09:24:00', 1, '2022-04-09 11:06:57', '2022-04-09 11:06:57'),
(126, 29, '2022-04-10', '18:04:00', '18:19:00', '2022-04-10 18:04:00', '2022-04-10 18:19:00', 1, '2022-04-09 11:35:58', '2022-04-09 11:35:58'),
(127, 30, '2022-04-11', '17:19:00', '17:34:00', '2022-04-11 17:19:00', '2022-04-15 17:34:00', 1, '2022-04-09 11:38:14', '2022-04-09 11:38:14'),
(128, 30, '2022-04-11', '18:49:00', '19:04:00', '2022-04-11 18:49:00', '2022-04-15 19:04:00', 1, '2022-04-09 11:38:14', '2022-04-09 11:38:14'),
(129, 31, '2022-04-12', '16:04:00', '16:44:00', '2022-04-12 16:04:00', '2022-04-12 16:44:00', 1, '2022-04-11 05:08:19', '2022-04-11 05:08:19'),
(130, 32, '2022-04-12', '19:34:00', '19:59:00', '2022-04-12 19:34:00', '2022-04-12 19:59:00', 1, '2022-04-11 05:35:49', '2022-04-11 05:35:49'),
(131, 33, '2022-04-12', '20:00:00', '21:00:00', '2022-04-12 20:00:00', '2022-04-12 21:00:00', 1, '2022-04-11 09:28:20', '2022-04-11 09:28:20'),
(132, 34, '2022-04-12', '11:48:00', '12:28:00', '2022-04-12 11:48:00', '2022-04-12 12:28:00', 1, '2022-04-11 10:02:29', '2022-04-11 10:02:29'),
(133, 35, '2022-04-13', '17:00:00', '18:00:00', '2022-04-13 17:00:00', '2022-04-13 18:00:00', 1, '2022-04-11 10:09:49', '2022-04-11 10:09:49'),
(134, 36, '2022-04-12', '20:19:00', '20:34:00', '2022-04-12 20:19:00', '2022-04-12 20:34:00', 1, '2022-04-11 10:44:49', '2022-04-11 10:44:49'),
(135, 36, '2022-04-12', '20:34:00', '20:49:00', '2022-04-12 20:34:00', '2022-04-12 20:49:00', 1, '2022-04-11 10:44:49', '2022-04-11 10:44:49'),
(136, 37, '2022-04-16', '17:04:00', '17:19:00', '2022-04-16 17:04:00', '2022-04-16 17:19:00', 1, '2022-04-11 10:46:37', '2022-04-11 10:46:37'),
(137, 37, '2022-04-16', '17:19:00', '17:34:00', '2022-04-16 17:19:00', '2022-04-16 17:34:00', 1, '2022-04-11 10:46:37', '2022-04-11 10:46:37'),
(138, 38, '2022-04-12', '10:00:00', '10:40:00', '2022-04-12 10:00:00', '2022-04-12 10:40:00', 1, '2022-04-11 11:05:25', '2022-04-11 11:05:25'),
(139, 38, '2022-04-12', '10:40:00', '11:20:00', '2022-04-12 10:40:00', '2022-04-12 11:20:00', 1, '2022-04-11 11:05:25', '2022-04-11 11:05:25'),
(140, 38, '2022-04-12', '11:20:00', '12:00:00', '2022-04-12 11:20:00', '2022-04-12 12:00:00', 1, '2022-04-11 11:05:25', '2022-04-11 11:05:25'),
(141, 38, '2022-04-12', '12:00:00', '12:40:00', '2022-04-12 12:00:00', '2022-04-12 12:40:00', 1, '2022-04-11 11:05:25', '2022-04-11 11:05:25'),
(142, 38, '2022-04-12', '12:40:00', '13:20:00', '2022-04-12 12:40:00', '2022-04-12 13:20:00', 1, '2022-04-11 11:05:25', '2022-04-11 11:05:25'),
(143, 38, '2022-04-12', '13:20:00', '14:00:00', '2022-04-12 13:20:00', '2022-04-12 14:00:00', 1, '2022-04-11 11:05:25', '2022-04-11 11:05:25'),
(144, 38, '2022-04-12', '14:00:00', '14:40:00', '2022-04-12 14:00:00', '2022-04-12 14:40:00', 1, '2022-04-11 11:05:25', '2022-04-11 11:05:25'),
(145, 39, '2022-04-12', '07:30:00', '08:10:00', '2022-04-12 07:30:00', '2022-04-12 08:10:00', 1, '2022-04-11 11:08:34', '2022-04-11 11:08:34'),
(146, 39, '2022-04-12', '08:10:00', '08:50:00', '2022-04-12 08:10:00', '2022-04-12 08:50:00', 1, '2022-04-11 11:08:34', '2022-04-11 11:08:34'),
(147, 39, '2022-04-12', '08:50:00', '09:30:00', '2022-04-12 08:50:00', '2022-04-12 09:30:00', 1, '2022-04-11 11:08:34', '2022-04-11 11:08:34'),
(148, 39, '2022-04-12', '09:30:00', '10:10:00', '2022-04-12 09:30:00', '2022-04-12 10:10:00', 1, '2022-04-11 11:08:34', '2022-04-11 11:08:34'),
(149, 39, '2022-04-12', '10:10:00', '10:50:00', '2022-04-12 10:10:00', '2022-04-12 10:50:00', 1, '2022-04-11 11:08:34', '2022-04-11 11:08:34'),
(150, 40, '2022-04-13', '14:10:00', '14:50:00', '2022-04-13 14:10:00', '2022-04-13 14:50:00', 1, '2022-04-11 11:11:32', '2022-04-11 11:11:32'),
(151, 40, '2022-04-13', '11:30:00', '12:10:00', '2022-04-13 11:30:00', '2022-04-13 12:10:00', 1, '2022-04-11 11:11:32', '2022-04-11 11:11:32'),
(152, 40, '2022-04-13', '12:10:00', '12:50:00', '2022-04-13 12:10:00', '2022-04-13 12:50:00', 1, '2022-04-11 11:11:32', '2022-04-11 11:11:32'),
(153, 40, '2022-04-13', '12:50:00', '13:30:00', '2022-04-13 12:50:00', '2022-04-13 13:30:00', 1, '2022-04-11 11:11:32', '2022-04-11 11:11:32'),
(154, 40, '2022-04-13', '13:30:00', '14:10:00', '2022-04-13 13:30:00', '2022-04-13 14:10:00', 1, '2022-04-11 11:11:32', '2022-04-11 11:11:32'),
(155, 41, '2022-04-16', '09:00:00', '10:00:00', '2022-04-16 09:00:00', '2022-04-16 10:00:00', 1, '2022-04-11 11:32:04', '2022-04-11 11:32:04'),
(156, 41, '2022-04-16', '10:00:00', '11:00:00', '2022-04-16 10:00:00', '2022-04-16 11:00:00', 1, '2022-04-11 11:32:04', '2022-04-11 11:32:04'),
(157, 42, '2022-04-15', '10:00:00', '10:40:00', '2022-04-15 10:00:00', '2022-04-15 10:40:00', 1, '2022-04-13 09:25:24', '2022-04-13 09:25:24'),
(158, 42, '2022-04-15', '10:40:00', '11:20:00', '2022-04-15 10:40:00', '2022-04-15 11:20:00', 1, '2022-04-13 09:25:24', '2022-04-13 09:25:24'),
(159, 43, '2022-04-15', '09:00:00', '10:00:00', '2022-04-15 09:00:00', '2022-04-15 10:00:00', 1, '2022-04-13 09:26:09', '2022-04-13 09:26:09'),
(160, 43, '2022-04-15', '10:00:00', '11:00:00', '2022-04-15 10:00:00', '2022-04-15 11:00:00', 1, '2022-04-13 09:26:09', '2022-04-13 09:26:09'),
(161, 43, '2022-04-15', '11:00:00', '12:00:00', '2022-04-15 11:00:00', '2022-04-15 12:00:00', 1, '2022-04-13 09:26:09', '2022-04-13 09:26:09'),
(162, 44, '2022-04-15', '09:00:00', '09:30:00', '2022-04-15 09:00:00', '2022-04-15 09:30:00', 1, '2022-04-14 05:34:23', '2022-04-14 05:34:23'),
(163, 45, '2022-04-15', '07:30:00', '08:10:00', '2022-04-15 07:30:00', '2022-04-15 08:10:00', 1, '2022-04-14 05:44:29', '2022-04-14 05:44:29'),
(164, 45, '2022-04-15', '08:10:00', '08:50:00', '2022-04-15 08:10:00', '2022-04-15 08:50:00', 1, '2022-04-14 05:44:29', '2022-04-14 05:44:29'),
(165, 45, '2022-04-15', '08:50:00', '09:30:00', '2022-04-15 08:50:00', '2022-04-15 09:30:00', 1, '2022-04-14 05:44:29', '2022-04-14 05:44:29'),
(166, 46, '2022-04-15', '18:30:00', '19:00:00', '2022-04-15 18:30:00', '2022-04-15 19:00:00', 1, '2022-04-14 11:37:50', '2022-04-14 11:37:50'),
(167, 47, '2022-04-16', '14:00:00', '14:30:00', '2022-04-16 14:00:00', '2022-04-16 14:30:00', 1, '2022-04-15 05:22:58', '2022-04-15 05:22:58'),
(168, 47, '2022-04-16', '14:30:00', '15:00:00', '2022-04-16 14:30:00', '2022-04-16 15:00:00', 1, '2022-04-15 05:22:58', '2022-04-15 05:22:58'),
(169, 48, '2022-04-16', '19:34:00', '19:49:00', '2022-04-16 19:34:00', '2022-04-16 19:49:00', 1, '2022-04-15 05:34:51', '2022-04-15 05:34:51'),
(170, 48, '2022-04-16', '19:49:00', '20:04:00', '2022-04-16 19:49:00', '2022-04-16 20:04:00', 1, '2022-04-15 05:34:51', '2022-04-15 05:34:51'),
(171, 49, '2022-04-16', '19:39:00', '20:39:00', '2022-04-16 19:39:00', '2022-04-16 20:39:00', 1, '2022-04-15 05:55:57', '2022-04-15 05:55:57'),
(172, 49, '2022-04-16', '20:39:00', '21:39:00', '2022-04-16 20:39:00', '2022-04-16 21:39:00', 1, '2022-04-15 05:55:57', '2022-04-15 05:55:57'),
(173, 50, '2022-04-17', '09:00:00', '09:30:00', '2022-04-17 09:00:00', '2022-04-17 09:30:00', 1, '2022-04-15 10:03:09', '2022-04-15 10:03:09'),
(174, 50, '2022-04-17', '09:30:00', '10:00:00', '2022-04-17 09:30:00', '2022-04-17 10:00:00', 1, '2022-04-15 10:03:10', '2022-04-15 10:03:10'),
(175, 50, '2022-04-17', '10:00:00', '10:30:00', '2022-04-17 10:00:00', '2022-04-17 10:30:00', 1, '2022-04-15 10:03:10', '2022-04-15 10:03:10'),
(176, 51, '2022-04-17', '17:34:00', '17:49:00', '2022-04-17 17:34:00', '2022-04-17 17:49:00', 1, '2022-04-15 10:04:46', '2022-04-15 10:04:46'),
(177, 51, '2022-04-17', '17:49:00', '18:04:00', '2022-04-17 17:49:00', '2022-04-17 18:04:00', 1, '2022-04-15 10:04:46', '2022-04-15 10:04:46'),
(178, 51, '2022-04-17', '18:04:00', '18:19:00', '2022-04-17 18:04:00', '2022-04-17 18:19:00', 1, '2022-04-15 10:04:46', '2022-04-15 10:04:46'),
(179, 51, '2022-04-17', '18:19:00', '18:34:00', '2022-04-17 18:19:00', '2022-04-17 18:34:00', 1, '2022-04-15 10:04:46', '2022-04-15 10:04:46'),
(180, 52, '2022-04-17', '08:44:00', '09:24:00', '2022-04-17 08:44:00', '2022-04-17 09:24:00', 1, '2022-04-15 10:05:21', '2022-04-15 10:05:21'),
(181, 52, '2022-04-17', '09:24:00', '10:04:00', '2022-04-17 09:24:00', '2022-04-17 10:04:00', 1, '2022-04-15 10:05:21', '2022-04-15 10:05:21'),
(182, 52, '2022-04-17', '10:04:00', '10:44:00', '2022-04-17 10:04:00', '2022-04-17 10:44:00', 1, '2022-04-15 10:05:21', '2022-04-15 10:05:21'),
(183, 53, '2022-04-17', '10:30:00', '11:00:00', '2022-04-17 10:30:00', '2022-04-17 11:00:00', 1, '2022-04-15 12:36:02', '2022-04-15 12:36:02'),
(184, 53, '2022-04-17', '11:00:00', '11:30:00', '2022-04-17 11:00:00', '2022-04-17 11:30:00', 1, '2022-04-15 12:36:02', '2022-04-15 12:36:02'),
(185, 54, '2022-04-18', '17:19:00', '17:34:00', '2022-04-18 17:19:00', '2022-04-18 17:34:00', 1, '2022-04-15 12:36:35', '2022-04-15 12:36:35'),
(186, 54, '2022-04-18', '17:34:00', '17:49:00', '2022-04-18 17:34:00', '2022-04-18 17:49:00', 1, '2022-04-15 12:36:35', '2022-04-15 12:36:35'),
(187, 55, '2022-04-16', '07:30:00', '08:10:00', '2022-04-16 07:30:00', '2022-04-16 08:10:00', 1, '2022-04-15 12:37:34', '2022-04-15 12:37:34'),
(188, 55, '2022-04-16', '08:10:00', '08:50:00', '2022-04-16 08:10:00', '2022-04-16 08:50:00', 1, '2022-04-15 12:37:34', '2022-04-15 12:37:34'),
(189, 55, '2022-04-16', '08:50:00', '09:30:00', '2022-04-16 08:50:00', '2022-04-16 09:30:00', 1, '2022-04-15 12:37:34', '2022-04-15 12:37:34'),
(190, 56, '2022-04-16', '17:44:00', '18:04:00', '2022-04-16 17:44:00', '2022-04-16 18:04:00', 1, '2022-04-15 12:52:23', '2022-04-15 12:52:23'),
(191, 57, '2022-04-16', '19:34:00', '19:59:00', '2022-04-16 19:34:00', '2022-04-16 19:59:00', 1, '2022-04-15 13:14:55', '2022-04-15 13:14:55'),
(192, 58, '2022-04-16', '20:43:00', '21:03:00', '2022-04-16 20:43:00', '2022-04-16 21:03:00', 1, '2022-04-15 13:16:07', '2022-04-15 13:16:07'),
(193, 59, '2022-04-19', '17:25:00', '17:45:00', '2022-04-19 17:25:00', '2022-04-19 17:45:00', 1, '2022-04-18 05:52:18', '2022-04-18 05:52:18'),
(194, 59, '2022-04-19', '17:45:00', '18:05:00', '2022-04-19 17:45:00', '2022-04-19 18:05:00', 1, '2022-04-18 05:52:18', '2022-04-18 05:52:18'),
(195, 59, '2022-04-19', '17:05:00', '17:25:00', '2022-04-19 17:05:00', '2022-04-19 17:25:00', 1, '2022-04-18 05:52:18', '2022-04-18 05:52:18'),
(196, 60, '2022-04-20', '15:55:00', '16:15:00', '2022-04-20 15:55:00', '2022-04-20 16:15:00', 1, '2022-04-19 04:54:37', '2022-04-19 04:54:37'),
(197, 60, '2022-04-20', '16:15:00', '16:35:00', '2022-04-20 16:15:00', '2022-04-20 16:35:00', 1, '2022-04-19 04:54:37', '2022-04-19 04:54:37'),
(198, 61, '2022-04-21', '19:34:00', '19:59:00', '2022-04-21 19:34:00', '2022-04-21 19:59:00', 1, '2022-04-20 09:22:01', '2022-04-20 09:22:01'),
(199, 62, '2022-04-21', '21:39:00', '22:39:00', '2022-04-21 21:39:00', '2022-04-21 22:39:00', 1, '2022-04-20 09:25:29', '2022-04-20 09:25:29'),
(200, 62, '2022-04-21', '22:39:00', '23:39:00', '2022-04-21 22:39:00', '2022-04-21 23:39:00', 1, '2022-04-20 09:25:29', '2022-04-20 09:25:29'),
(201, 63, '2022-04-21', '09:00:00', '10:00:00', '2022-04-21 09:00:00', '2022-04-21 10:00:00', 1, '2022-04-20 13:58:01', '2022-04-20 13:58:01'),
(202, 63, '2022-04-21', '10:00:00', '11:00:00', '2022-04-21 10:00:00', '2022-04-21 11:00:00', 1, '2022-04-20 13:58:01', '2022-04-20 13:58:01'),
(203, 64, '2022-04-21', '11:23:00', '11:43:00', '2022-04-21 11:23:00', '2022-04-21 11:43:00', 1, '2022-04-20 14:01:01', '2022-04-20 14:01:01'),
(204, 65, '2022-04-22', '02:05:00', '04:05:00', '2022-04-22 02:05:00', '2022-04-22 04:05:00', 1, '2022-04-21 05:28:20', '2022-04-21 05:28:20'),
(205, 65, '2022-04-22', '04:05:00', '06:05:00', '2022-04-22 04:05:00', '2022-04-22 06:05:00', 1, '2022-04-21 05:28:20', '2022-04-21 05:28:20'),
(206, 66, '2022-04-22', '09:00:00', '10:00:00', '2022-04-22 09:00:00', '2022-04-22 10:00:00', 1, '2022-04-21 11:56:57', '2022-04-21 11:56:57'),
(207, 66, '2022-04-22', '10:00:00', '11:00:00', '2022-04-22 10:00:00', '2022-04-22 11:00:00', 1, '2022-04-21 11:56:57', '2022-04-21 11:56:57'),
(208, 67, '2022-04-22', '15:35:00', '15:55:00', '2022-04-22 15:35:00', '2022-04-22 15:55:00', 1, '2022-04-21 12:02:05', '2022-04-21 12:02:05'),
(209, 67, '2022-04-22', '15:55:00', '16:15:00', '2022-04-22 15:55:00', '2022-04-22 16:15:00', 1, '2022-04-21 12:02:05', '2022-04-21 12:02:05'),
(210, 68, '2022-04-23', '16:55:00', '17:15:00', '2022-04-23 16:55:00', '2022-04-23 17:15:00', 1, '2022-04-21 13:50:00', '2022-04-21 13:50:00'),
(211, 69, '2022-04-22', '18:55:00', '19:15:00', '2022-04-22 18:55:00', '2022-04-22 19:15:00', 1, '2022-04-21 14:40:43', '2022-04-21 14:40:43'),
(212, 69, '2022-04-22', '19:15:00', '19:35:00', '2022-04-22 19:15:00', '2022-04-22 19:35:00', 1, '2022-04-21 14:40:43', '2022-04-21 14:40:43'),
(213, 70, '2022-04-23', '09:00:00', '10:00:00', '2022-04-23 09:00:00', '2022-04-23 10:00:00', 1, '2022-04-22 12:50:00', '2022-04-22 12:50:00'),
(214, 70, '2022-04-23', '10:00:00', '11:00:00', '2022-04-23 10:00:00', '2022-04-23 11:00:00', 1, '2022-04-22 12:50:00', '2022-04-22 12:50:00'),
(215, 71, '2022-04-23', '15:35:00', '15:55:00', '2022-04-23 15:35:00', '2022-04-23 15:55:00', 1, '2022-04-22 12:59:53', '2022-04-22 12:59:53'),
(216, 71, '2022-04-23', '15:55:00', '16:15:00', '2022-04-23 15:55:00', '2022-04-23 16:15:00', 1, '2022-04-22 12:59:53', '2022-04-22 12:59:53'),
(217, 72, '2022-04-23', '16:15:00', '16:35:00', '2022-04-23 16:15:00', '2022-04-23 16:35:00', 1, '2022-04-22 13:01:16', '2022-04-22 13:01:16'),
(218, 72, '2022-04-23', '16:35:00', '16:55:00', '2022-04-23 16:35:00', '2022-04-23 16:55:00', 1, '2022-04-22 13:01:16', '2022-04-22 13:01:16'),
(219, 73, '2022-04-23', '17:55:00', '18:15:00', '2022-04-23 17:55:00', '2022-04-23 18:15:00', 1, '2022-04-22 13:40:31', '2022-04-22 13:40:31'),
(220, 73, '2022-04-23', '18:15:00', '18:35:00', '2022-04-23 18:15:00', '2022-04-23 18:35:00', 1, '2022-04-22 13:40:31', '2022-04-22 13:40:31'),
(221, 74, '2022-04-23', '12:55:00', '13:25:00', '2022-04-23 12:55:00', '2022-04-23 13:25:00', 1, '2022-04-22 13:40:40', '2022-04-22 13:40:40'),
(222, 74, '2022-04-23', '12:25:00', '12:55:00', '2022-04-23 12:25:00', '2022-04-23 12:55:00', 1, '2022-04-22 13:40:40', '2022-04-22 13:40:40'),
(223, 75, '2022-04-26', '09:55:00', '10:25:00', '2022-04-26 09:55:00', '2022-04-26 10:25:00', 1, '2022-04-25 06:20:56', '2022-04-25 06:20:56'),
(224, 76, '2022-04-26', '13:00:00', '14:00:00', '2022-04-26 13:00:00', '2022-04-26 14:00:00', 1, '2022-04-25 07:11:46', '2022-04-25 07:11:46'),
(225, 77, '2022-04-26', '03:08:00', '03:48:00', '2022-04-26 03:08:00', '2022-04-26 03:48:00', 1, '2022-04-25 14:09:39', '2022-04-25 14:09:39'),
(226, 78, '2022-04-30', '19:00:00', '20:00:00', '2022-04-30 19:00:00', '2022-04-30 20:00:00', 1, '2022-04-29 10:33:31', '2022-04-29 10:33:31'),
(227, 79, '2022-04-08', '08:00:00', '10:00:00', '2022-04-08 08:00:00', '2022-04-08 10:00:00', 1, '2022-05-02 09:31:39', '2022-05-02 09:31:39'),
(228, 79, '2022-04-08', '11:00:00', '12:00:00', '2022-04-08 11:00:00', '2022-04-08 12:00:00', 1, '2022-05-02 09:31:39', '2022-05-02 09:31:39'),
(229, 80, '2022-05-03', '13:00:00', '14:00:00', '2022-05-03 13:00:00', '2022-05-03 14:00:00', 1, '2022-05-02 11:57:07', '2022-05-02 11:57:07'),
(230, 81, '2022-05-04', '10:00:00', '10:40:00', '2022-05-04 10:00:00', '2022-05-04 10:40:00', 1, '2022-05-03 08:44:14', '2022-05-03 08:44:14'),
(231, 81, '2022-05-04', '10:40:00', '11:20:00', '2022-05-04 10:40:00', '2022-05-04 11:20:00', 1, '2022-05-03 08:44:14', '2022-05-03 08:44:14'),
(232, 82, '2022-05-04', '17:04:00', '17:19:00', '2022-05-04 17:04:00', '2022-05-04 17:19:00', 1, '2022-05-03 10:09:28', '2022-05-03 10:09:28'),
(233, 82, '2022-05-04', '17:19:00', '17:34:00', '2022-05-04 17:19:00', '2022-05-04 17:34:00', 1, '2022-05-03 10:09:28', '2022-05-03 10:09:28'),
(234, 83, '2022-05-04', '15:55:00', '16:15:00', '2022-05-04 15:55:00', '2022-05-04 16:15:00', 1, '2022-05-03 10:11:11', '2022-05-03 10:11:11'),
(235, 83, '2022-05-04', '16:15:00', '16:35:00', '2022-05-04 16:15:00', '2022-05-04 16:35:00', 1, '2022-05-03 10:11:11', '2022-05-03 10:11:11'),
(236, 84, '2022-05-04', '07:55:00', '08:25:00', '2022-05-04 07:55:00', '2022-05-04 08:25:00', 1, '2022-05-03 10:12:23', '2022-05-03 10:12:23'),
(237, 84, '2022-05-04', '08:25:00', '08:55:00', '2022-05-04 08:25:00', '2022-05-04 08:55:00', 1, '2022-05-03 10:12:23', '2022-05-03 10:12:23'),
(238, 85, '2022-05-04', '17:34:00', '17:49:00', '2022-05-04 17:34:00', '2022-05-04 17:49:00', 1, '2022-05-03 12:37:16', '2022-05-03 12:37:16'),
(239, 85, '2022-05-04', '17:49:00', '18:04:00', '2022-05-04 17:49:00', '2022-05-04 18:04:00', 1, '2022-05-03 12:37:16', '2022-05-03 12:37:16'),
(240, 86, '2022-05-04', '18:04:00', '18:19:00', '2022-05-04 18:04:00', '2022-05-04 18:19:00', 1, '2022-05-03 12:38:02', '2022-05-03 12:38:02'),
(241, 86, '2022-05-04', '18:19:00', '18:34:00', '2022-05-04 18:19:00', '2022-05-04 18:34:00', 1, '2022-05-03 12:38:02', '2022-05-03 12:38:02'),
(242, 87, '2022-05-04', '11:20:00', '12:00:00', '2022-05-04 11:20:00', '2022-05-04 12:00:00', 1, '2022-05-03 12:55:08', '2022-05-03 12:55:08'),
(243, 87, '2022-05-04', '12:00:00', '12:40:00', '2022-05-04 12:00:00', '2022-05-04 12:40:00', 1, '2022-05-03 12:55:08', '2022-05-03 12:55:08'),
(244, 88, '2022-05-04', '21:19:00', '21:34:00', '2022-05-04 21:19:00', '2022-05-04 21:34:00', 1, '2022-05-03 13:00:50', '2022-05-03 13:00:50'),
(245, 89, '2022-05-04', '21:34:00', '21:49:00', '2022-05-04 21:34:00', '2022-05-04 21:49:00', 1, '2022-05-03 13:03:17', '2022-05-03 13:03:17'),
(246, 89, '2022-05-04', '21:49:00', '22:04:00', '2022-05-04 21:49:00', '2022-05-04 22:04:00', 1, '2022-05-03 13:03:17', '2022-05-03 13:03:17'),
(247, 90, '2022-05-04', '07:30:00', '08:10:00', '2022-05-04 07:30:00', '2022-05-04 08:10:00', 1, '2022-05-03 13:12:55', '2022-05-03 13:12:55'),
(248, 90, '2022-05-04', '08:10:00', '08:50:00', '2022-05-04 08:10:00', '2022-05-04 08:50:00', 1, '2022-05-03 13:12:55', '2022-05-03 13:12:55'),
(249, 90, '2022-05-04', '08:50:00', '09:30:00', '2022-05-04 08:50:00', '2022-05-04 09:30:00', 1, '2022-05-03 13:12:55', '2022-05-03 13:12:55'),
(250, 91, '2022-05-04', '09:00:00', '10:00:00', '2022-05-04 09:00:00', '2022-05-04 10:00:00', 1, '2022-05-03 13:13:51', '2022-05-03 13:13:51'),
(251, 91, '2022-05-04', '10:00:00', '11:00:00', '2022-05-04 10:00:00', '2022-05-04 11:00:00', 1, '2022-05-03 13:13:51', '2022-05-03 13:13:51'),
(252, 92, '2022-05-04', '19:00:00', '20:00:00', '2022-05-04 19:00:00', '2022-05-04 20:00:00', 1, '2022-05-03 13:14:05', '2022-05-03 13:14:05'),
(253, 92, '2022-05-04', '20:00:00', '21:00:00', '2022-05-04 20:00:00', '2022-05-04 21:00:00', 1, '2022-05-03 13:14:05', '2022-05-03 13:14:05'),
(254, 93, '2022-05-04', '11:00:00', '12:00:00', '2022-05-04 11:00:00', '2022-05-04 12:00:00', 1, '2022-05-03 13:14:21', '2022-05-03 13:14:21'),
(255, 93, '2022-05-04', '12:00:00', '13:00:00', '2022-05-04 12:00:00', '2022-05-04 13:00:00', 1, '2022-05-03 13:14:21', '2022-05-03 13:14:21'),
(256, 93, '2022-05-04', '13:00:00', '14:00:00', '2022-05-04 13:00:00', '2022-05-04 14:00:00', 1, '2022-05-03 13:14:21', '2022-05-03 13:14:21'),
(257, 94, '2022-05-04', '16:00:00', '17:00:00', '2022-05-04 16:00:00', '2022-05-04 17:00:00', 1, '2022-05-03 13:20:26', '2022-05-03 13:20:26'),
(258, 94, '2022-05-04', '17:00:00', '18:00:00', '2022-05-04 17:00:00', '2022-05-04 18:00:00', 1, '2022-05-03 13:20:26', '2022-05-03 13:20:26'),
(259, 95, '2022-05-04', '18:00:00', '19:00:00', '2022-05-04 18:00:00', '2022-05-04 19:00:00', 1, '2022-05-03 13:27:16', '2022-05-03 13:27:16'),
(260, 96, '2022-05-05', '13:00:00', '14:00:00', '2022-05-05 13:00:00', '2022-05-05 14:00:00', 1, '2022-05-04 04:53:09', '2022-05-04 04:53:09'),
(261, 97, '2022-05-05', '14:00:00', '15:00:00', '2022-05-05 14:00:00', '2022-05-05 15:00:00', 1, '2022-05-04 05:14:38', '2022-05-04 05:14:38'),
(262, 98, '2022-05-06', '09:00:00', '10:00:00', '2022-05-06 09:00:00', '2022-05-06 10:00:00', 1, '2022-05-05 05:18:20', '2022-05-05 05:18:20'),
(263, 98, '2022-05-06', '10:00:00', '11:00:00', '2022-05-06 10:00:00', '2022-05-06 11:00:00', 1, '2022-05-05 05:18:20', '2022-05-05 05:18:20'),
(264, 99, '2022-05-06', '19:09:00', '19:34:00', '2022-05-06 19:09:00', '2022-05-06 19:34:00', 1, '2022-05-05 07:28:38', '2022-05-05 07:28:38'),
(265, 100, '2022-05-06', '12:00:00', '13:00:00', '2022-05-06 12:00:00', '2022-05-06 13:00:00', 1, '2022-05-05 07:32:54', '2022-05-05 07:32:54'),
(266, 101, '2022-05-06', '18:49:00', '19:04:00', '2022-05-06 18:49:00', '2022-05-06 19:04:00', 1, '2022-05-05 07:33:05', '2022-05-05 07:33:05'),
(267, 102, '2022-05-06', '09:00:00', '10:00:00', '2022-05-06 09:00:00', '2022-05-06 10:00:00', 1, '2022-05-05 09:01:02', '2022-05-05 09:01:02'),
(268, 102, '2022-05-06', '10:00:00', '11:00:00', '2022-05-06 10:00:00', '2022-05-06 11:00:00', 1, '2022-05-05 09:01:02', '2022-05-05 09:01:02'),
(269, 103, '2022-05-06', '09:00:00', '10:00:00', '2022-05-06 09:00:00', '2022-05-06 10:00:00', 1, '2022-05-05 09:02:22', '2022-05-05 09:02:22'),
(270, 103, '2022-05-06', '10:00:00', '11:00:00', '2022-05-06 10:00:00', '2022-05-06 11:00:00', 1, '2022-05-05 09:02:22', '2022-05-05 09:02:22'),
(271, 104, '2022-05-06', '09:23:00', '09:43:00', '2022-05-06 09:23:00', '2022-05-06 09:43:00', 1, '2022-05-05 09:05:20', '2022-05-05 09:05:20'),
(272, 105, '2022-05-12', '08:00:00', '10:00:00', '2022-05-12 08:00:00', '2022-05-12 10:00:00', 1, '2022-05-05 09:21:31', '2022-05-05 09:21:31'),
(273, 105, '2022-05-12', '11:00:00', '12:00:00', '2022-05-12 11:00:00', '2022-05-12 12:00:00', 1, '2022-05-05 09:21:31', '2022-05-05 09:21:31'),
(274, 106, '2022-05-12', '08:00:00', '10:00:00', '2022-05-12 08:00:00', '2022-05-12 10:00:00', 1, '2022-05-05 09:43:07', '2022-05-05 09:43:07'),
(275, 106, '2022-05-12', '11:00:00', '12:00:00', '2022-05-12 11:00:00', '2022-05-12 12:00:00', 1, '2022-05-05 09:43:07', '2022-05-05 09:43:07'),
(276, 107, '2022-05-12', '08:00:00', '10:00:00', '2022-05-12 08:00:00', '2022-05-12 10:00:00', 1, '2022-05-05 10:09:53', '2022-05-05 10:09:53'),
(277, 107, '2022-05-12', '11:00:00', '12:00:00', '2022-05-12 11:00:00', '2022-05-12 12:00:00', 1, '2022-05-05 10:09:53', '2022-05-05 10:09:53'),
(278, 108, '2022-05-12', '08:00:00', '10:00:00', '2022-05-12 08:00:00', '2022-05-12 10:00:00', 1, '2022-05-05 10:10:22', '2022-05-05 10:10:22'),
(279, 108, '2022-05-12', '11:00:00', '12:00:00', '2022-05-12 11:00:00', '2022-05-12 12:00:00', 1, '2022-05-05 10:10:22', '2022-05-05 10:10:22'),
(280, 109, '2022-05-06', '05:08:00', '05:48:00', '2022-05-06 05:08:00', '2022-05-06 05:48:00', 1, '2022-05-05 10:35:56', '2022-05-05 10:35:56'),
(281, 110, '2022-05-06', '13:00:00', '14:00:00', '2022-05-06 13:00:00', '2022-05-06 14:00:00', 1, '2022-05-05 10:36:14', '2022-05-05 10:36:14'),
(282, 111, '2022-05-06', '20:19:00', '20:34:00', '2022-05-06 20:19:00', '2022-05-06 20:34:00', 1, '2022-05-05 10:36:31', '2022-05-05 10:36:31'),
(283, 111, '2022-05-06', '20:34:00', '20:49:00', '2022-05-06 20:34:00', '2022-05-06 20:49:00', 1, '2022-05-05 10:36:31', '2022-05-05 10:36:31'),
(284, 112, '2022-05-06', '07:08:00', '07:48:00', '2022-05-06 07:08:00', '2022-05-06 07:48:00', 1, '2022-05-05 11:13:56', '2022-05-05 11:13:56'),
(285, 113, '2022-05-06', '14:25:00', '14:45:00', '2022-05-06 14:25:00', '2022-05-06 14:45:00', 1, '2022-05-05 11:24:29', '2022-05-05 11:24:29'),
(286, 114, '2022-05-06', '10:03:00', '10:23:00', '2022-05-06 10:03:00', '2022-05-06 10:23:00', 1, '2022-05-05 11:25:40', '2022-05-05 11:25:40'),
(287, 115, '2022-05-06', '08:05:00', '10:05:00', '2022-05-06 08:05:00', '2022-05-06 10:05:00', 1, '2022-05-05 11:25:58', '2022-05-05 11:25:58'),
(288, 116, '2022-05-06', '09:03:00', '09:23:00', '2022-05-06 09:03:00', '2022-05-06 09:23:00', 1, '2022-05-05 11:59:05', '2022-05-05 11:59:05'),
(289, 117, '2022-05-07', '09:00:00', '10:00:00', '2022-05-07 09:00:00', '2022-05-07 10:00:00', 1, '2022-05-06 11:24:09', '2022-05-06 11:24:09'),
(290, 118, '2022-05-07', '09:00:00', '10:00:00', '2022-05-07 09:00:00', '2022-05-07 10:00:00', 1, '2022-05-06 11:24:53', '2022-05-06 11:24:53'),
(291, 119, '2022-05-07', '17:04:00', '17:19:00', '2022-05-07 17:04:00', '2022-05-07 17:19:00', 1, '2022-05-06 12:40:47', '2022-05-06 12:40:47'),
(292, 119, '2022-05-07', '17:19:00', '17:34:00', '2022-05-07 17:19:00', '2022-05-07 17:34:00', 1, '2022-05-06 12:40:47', '2022-05-06 12:40:47'),
(293, 120, '2022-05-07', '10:00:00', '11:00:00', '2022-05-07 10:00:00', '2022-05-07 11:00:00', 1, '2022-05-06 12:41:53', '2022-05-06 12:41:53'),
(294, 121, '2022-05-07', '10:00:00', '11:00:00', '2022-05-07 10:00:00', '2022-05-07 11:00:00', 1, '2022-05-06 12:44:50', '2022-05-06 12:44:50'),
(295, 121, '2022-05-07', '11:00:00', '12:00:00', '2022-05-07 11:00:00', '2022-05-07 12:00:00', 1, '2022-05-06 12:44:50', '2022-05-06 12:44:50'),
(296, 122, '2022-05-02', '07:55:00', '08:25:00', '2022-05-02 07:55:00', '2022-05-02 08:25:00', 1, '2022-05-06 12:45:38', '2022-05-06 12:45:38'),
(297, 122, '2022-05-02', '08:25:00', '08:55:00', '2022-05-02 08:25:00', '2022-05-02 08:55:00', 1, '2022-05-06 12:45:38', '2022-05-06 12:45:38'),
(298, 122, '2022-05-02', '08:55:00', '09:25:00', '2022-05-02 08:55:00', '2022-05-02 09:25:00', 1, '2022-05-06 12:45:38', '2022-05-06 12:45:38'),
(299, 122, '2022-05-02', '09:55:00', '10:25:00', '2022-05-02 09:55:00', '2022-05-02 10:25:00', 1, '2022-05-06 12:45:38', '2022-05-06 12:45:38'),
(300, 122, '2022-05-02', '10:25:00', '10:55:00', '2022-05-02 10:25:00', '2022-05-02 10:55:00', 1, '2022-05-06 12:45:38', '2022-05-06 12:45:38'),
(301, 122, '2022-05-02', '10:55:00', '11:25:00', '2022-05-02 10:55:00', '2022-05-02 11:25:00', 1, '2022-05-06 12:45:38', '2022-05-06 12:45:38'),
(302, 123, '2022-05-07', '12:00:00', '13:00:00', '2022-05-07 12:00:00', '2022-05-07 13:00:00', 1, '2022-05-06 12:51:13', '2022-05-06 12:51:13'),
(303, 123, '2022-05-07', '13:00:00', '14:00:00', '2022-05-07 13:00:00', '2022-05-07 14:00:00', 1, '2022-05-06 12:51:13', '2022-05-06 12:51:13'),
(304, 124, '2022-05-07', '02:28:00', '03:08:00', '2022-05-07 02:28:00', '2022-05-07 03:08:00', 1, '2022-05-06 13:30:04', '2022-05-06 13:30:04'),
(305, 124, '2022-05-07', '03:08:00', '03:48:00', '2022-05-07 03:08:00', '2022-05-07 03:48:00', 1, '2022-05-06 13:30:04', '2022-05-06 13:30:04'),
(306, 125, '2022-05-07', '17:04:00', '17:19:00', '2022-05-07 17:04:00', '2022-05-07 17:19:00', 1, '2022-05-06 13:30:48', '2022-05-06 13:30:48'),
(307, 125, '2022-05-07', '17:19:00', '17:34:00', '2022-05-07 17:19:00', '2022-05-07 17:34:00', 1, '2022-05-06 13:30:48', '2022-05-06 13:30:48'),
(308, 125, '2022-05-07', '17:34:00', '17:49:00', '2022-05-07 17:34:00', '2022-05-07 17:49:00', 1, '2022-05-06 13:30:48', '2022-05-06 13:30:48'),
(309, 126, '2022-05-13', '17:04:00', '17:19:00', '2022-05-13 17:04:00', '2022-05-13 17:19:00', 1, '2022-05-06 13:34:12', '2022-05-06 13:34:12'),
(310, 126, '2022-05-13', '17:19:00', '17:34:00', '2022-05-13 17:19:00', '2022-05-13 17:34:00', 1, '2022-05-06 13:34:12', '2022-05-06 13:34:12'),
(311, 126, '2022-05-13', '17:34:00', '17:49:00', '2022-05-13 17:34:00', '2022-05-13 17:49:00', 1, '2022-05-06 13:34:12', '2022-05-06 13:34:12'),
(312, 127, '2022-05-11', '17:00:00', '18:00:00', '2022-05-11 17:00:00', '2022-05-11 18:00:00', 1, '2022-05-10 06:19:15', '2022-05-10 06:19:15'),
(313, 128, '2022-05-11', '08:00:00', '08:25:00', '2022-05-11 08:00:00', '2022-05-11 08:25:00', 1, '2022-05-10 06:47:29', '2022-05-10 06:47:29'),
(314, 128, '2022-05-11', '08:25:00', '08:50:00', '2022-05-11 08:25:00', '2022-05-11 08:50:00', 1, '2022-05-10 06:47:29', '2022-05-10 06:47:29'),
(315, 129, '2022-05-11', '08:50:00', '09:15:00', '2022-05-11 08:50:00', '2022-05-11 09:15:00', 1, '2022-05-10 06:50:44', '2022-05-10 06:50:44'),
(316, 129, '2022-05-11', '09:15:00', '09:40:00', '2022-05-11 09:15:00', '2022-05-11 09:40:00', 1, '2022-05-10 06:50:44', '2022-05-10 06:50:44'),
(341, 131, '2022-05-11', '09:40:00', '10:05:00', '2022-05-11 09:40:00', '2022-05-11 10:05:00', 1, '2022-05-10 07:30:28', '2022-05-10 07:30:28'),
(342, 131, '2022-05-11', '10:05:00', '10:30:00', '2022-05-11 10:05:00', '2022-05-11 10:30:00', 1, '2022-05-10 07:30:28', '2022-05-10 07:30:28'),
(343, 132, '2022-05-11', '17:04:00', '17:19:00', '2022-05-11 17:04:00', '2022-05-11 17:19:00', 1, '2022-05-10 08:36:56', '2022-05-10 08:36:56'),
(344, 133, '2022-05-11', '16:00:00', '17:00:00', '2022-05-11 16:00:00', '2022-05-11 17:00:00', 1, '2022-05-10 09:18:36', '2022-05-10 09:18:36'),
(345, 134, '2022-05-11', '11:00:00', '12:00:00', '2022-05-11 11:00:00', '2022-05-11 12:00:00', 1, '2022-05-10 09:19:35', '2022-05-10 09:19:35'),
(346, 130, '2022-05-10', '08:00:00', '08:25:00', '2022-05-10 08:00:00', '2022-06-01 08:25:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(347, 130, '2022-05-10', '08:25:00', '08:50:00', '2022-05-10 08:25:00', '2022-06-01 08:50:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(348, 130, '2022-05-10', '08:50:00', '09:15:00', '2022-05-10 08:50:00', '2022-06-01 09:15:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(349, 130, '2022-05-10', '09:15:00', '09:40:00', '2022-05-10 09:15:00', '2022-06-01 09:40:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(350, 130, '2022-05-10', '09:40:00', '10:05:00', '2022-05-10 09:40:00', '2022-06-01 10:05:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(351, 130, '2022-05-10', '10:30:00', '10:55:00', '2022-05-10 10:30:00', '2022-06-01 10:55:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(352, 130, '2022-05-10', '10:55:00', '11:20:00', '2022-05-10 10:55:00', '2022-06-01 11:20:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(353, 130, '2022-05-10', '12:10:00', '12:35:00', '2022-05-10 12:10:00', '2022-06-01 12:35:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(354, 130, '2022-05-10', '12:35:00', '13:00:00', '2022-05-10 12:35:00', '2022-06-01 13:00:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(355, 130, '2022-05-10', '13:00:00', '13:25:00', '2022-05-10 13:00:00', '2022-06-01 13:25:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(356, 130, '2022-05-10', '13:25:00', '13:50:00', '2022-05-10 13:25:00', '2022-06-01 13:50:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(357, 130, '2022-05-10', '14:15:00', '14:40:00', '2022-05-10 14:15:00', '2022-06-01 14:40:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(358, 130, '2022-05-10', '14:40:00', '15:05:00', '2022-05-10 14:40:00', '2022-06-01 15:05:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(359, 130, '2022-05-10', '17:35:00', '18:00:00', '2022-05-10 17:35:00', '2022-06-01 18:00:00', 1, '2022-05-10 09:47:06', '2022-05-10 09:47:06'),
(360, 135, '2022-05-11', '10:00:00', '10:40:00', '2022-05-11 10:00:00', '2022-05-11 10:40:00', 1, '2022-05-10 09:48:18', '2022-05-10 09:48:18'),
(361, 135, '2022-05-11', '10:40:00', '11:20:00', '2022-05-11 10:40:00', '2022-05-11 11:20:00', 1, '2022-05-10 09:48:18', '2022-05-10 09:48:18'),
(362, 136, '2022-05-11', '17:04:00', '17:19:00', '2022-05-11 17:04:00', '2022-05-11 17:19:00', 1, '2022-05-10 09:48:32', '2022-05-10 09:48:32'),
(363, 136, '2022-05-11', '17:19:00', '17:34:00', '2022-05-11 17:19:00', '2022-05-11 17:34:00', 1, '2022-05-10 09:48:32', '2022-05-10 09:48:32'),
(364, 136, '2022-05-11', '17:34:00', '17:49:00', '2022-05-11 17:34:00', '2022-05-11 17:49:00', 1, '2022-05-10 09:48:32', '2022-05-10 09:48:32'),
(365, 137, '2022-05-11', '18:39:00', '19:39:00', '2022-05-11 18:39:00', '2022-05-11 19:39:00', 1, '2022-05-10 09:48:54', '2022-05-10 09:48:54'),
(366, 137, '2022-05-11', '19:39:00', '20:39:00', '2022-05-11 19:39:00', '2022-05-11 20:39:00', 1, '2022-05-10 09:48:54', '2022-05-10 09:48:54'),
(367, 137, '2022-05-11', '20:39:00', '21:39:00', '2022-05-11 20:39:00', '2022-05-11 21:39:00', 1, '2022-05-10 09:48:54', '2022-05-10 09:48:54'),
(368, 138, '2022-05-11', '07:55:00', '08:25:00', '2022-05-11 07:55:00', '2022-05-11 08:25:00', 1, '2022-05-10 10:38:49', '2022-05-10 10:38:49'),
(369, 138, '2022-05-11', '08:25:00', '08:55:00', '2022-05-11 08:25:00', '2022-05-11 08:55:00', 1, '2022-05-10 10:38:49', '2022-05-10 10:38:49'),
(370, 139, '2022-05-12', '07:30:00', '08:10:00', '2022-05-12 07:30:00', '2022-05-12 08:10:00', 1, '2022-05-10 10:40:43', '2022-05-10 10:40:43'),
(371, 140, '2022-05-11', '09:00:00', '10:00:00', '2022-05-11 09:00:00', '2022-05-11 10:00:00', 1, '2022-05-10 10:55:23', '2022-05-10 10:55:23'),
(372, 140, '2022-05-11', '10:00:00', '11:00:00', '2022-05-11 10:00:00', '2022-05-11 11:00:00', 1, '2022-05-10 10:55:23', '2022-05-10 10:55:23'),
(373, 141, '2022-05-11', '12:00:00', '13:00:00', '2022-05-11 12:00:00', '2022-05-11 13:00:00', 1, '2022-05-10 10:58:03', '2022-05-10 10:58:03'),
(374, 141, '2022-05-11', '13:00:00', '14:00:00', '2022-05-11 13:00:00', '2022-05-11 14:00:00', 1, '2022-05-10 10:58:03', '2022-05-10 10:58:03'),
(375, 142, '2022-05-11', '19:00:00', '20:00:00', '2022-05-11 19:00:00', '2022-05-11 20:00:00', 1, '2022-05-10 11:23:15', '2022-05-10 11:23:15'),
(376, 142, '2022-05-11', '20:00:00', '21:00:00', '2022-05-11 20:00:00', '2022-05-11 21:00:00', 1, '2022-05-10 11:23:15', '2022-05-10 11:23:15'),
(377, 142, '2022-05-11', '18:00:00', '19:00:00', '2022-05-11 18:00:00', '2022-05-11 19:00:00', 1, '2022-05-10 11:23:15', '2022-05-10 11:23:15'),
(378, 143, '2022-05-12', '17:19:00', '17:34:00', '2022-05-12 17:19:00', '2022-05-12 17:34:00', 1, '2022-05-11 06:42:43', '2022-05-11 06:42:43'),
(379, 143, '2022-05-12', '17:34:00', '17:49:00', '2022-05-12 17:34:00', '2022-05-12 17:49:00', 1, '2022-05-11 06:42:43', '2022-05-11 06:42:43'),
(380, 144, '2022-05-12', '16:00:00', '17:00:00', '2022-05-12 16:00:00', '2022-05-12 17:00:00', 1, '2022-05-11 11:07:24', '2022-05-11 11:07:24'),
(381, 145, '2022-05-12', '17:49:00', '18:04:00', '2022-05-12 17:49:00', '2022-05-12 18:04:00', 1, '2022-05-11 11:11:06', '2022-05-11 11:11:06'),
(382, 145, '2022-05-12', '18:04:00', '18:19:00', '2022-05-12 18:04:00', '2022-05-12 18:19:00', 1, '2022-05-11 11:11:06', '2022-05-11 11:11:06'),
(383, 146, '2022-05-12', '09:00:00', '10:00:00', '2022-05-12 09:00:00', '2022-05-12 10:00:00', 1, '2022-05-11 11:13:21', '2022-05-11 11:13:21'),
(384, 147, '2022-05-12', '10:00:00', '11:00:00', '2022-05-12 10:00:00', '2022-05-12 11:00:00', 1, '2022-05-11 11:15:20', '2022-05-11 11:15:20'),
(385, 148, '2022-05-12', '18:19:00', '18:34:00', '2022-05-12 18:19:00', '2022-05-12 18:34:00', 1, '2022-05-11 11:16:12', '2022-05-11 11:16:12'),
(386, 149, '2022-05-13', '07:55:00', '08:25:00', '2022-05-13 07:55:00', '2022-05-13 08:25:00', 1, '2022-05-11 11:17:05', '2022-05-11 11:17:05'),
(387, 149, '2022-05-13', '08:25:00', '08:55:00', '2022-05-13 08:25:00', '2022-05-13 08:55:00', 1, '2022-05-11 11:17:05', '2022-05-11 11:17:05'),
(388, 150, '2022-05-12', '14:05:00', '14:25:00', '2022-05-12 14:05:00', '2022-05-12 14:25:00', 1, '2022-05-11 11:18:01', '2022-05-11 11:18:01'),
(389, 151, '2022-05-12', '18:34:00', '18:49:00', '2022-05-12 18:34:00', '2022-05-12 18:49:00', 1, '2022-05-11 11:19:29', '2022-05-11 11:19:29'),
(390, 151, '2022-05-12', '18:49:00', '19:04:00', '2022-05-12 18:49:00', '2022-05-12 19:04:00', 1, '2022-05-11 11:19:29', '2022-05-11 11:19:29'),
(391, 151, '2022-05-12', '19:04:00', '19:19:00', '2022-05-12 19:04:00', '2022-05-12 19:19:00', 1, '2022-05-11 11:19:29', '2022-05-11 11:19:29'),
(392, 152, '2022-05-14', '18:34:00', '18:49:00', '2022-05-14 18:34:00', '2022-05-14 18:49:00', 1, '2022-05-11 11:20:37', '2022-05-11 11:20:37'),
(393, 152, '2022-05-14', '18:49:00', '19:04:00', '2022-05-14 18:49:00', '2022-05-14 19:04:00', 1, '2022-05-11 11:20:37', '2022-05-11 11:20:37'),
(394, 152, '2022-05-14', '19:04:00', '19:19:00', '2022-05-14 19:04:00', '2022-05-14 19:19:00', 1, '2022-05-11 11:20:37', '2022-05-11 11:20:37'),
(395, 153, '2022-05-12', '17:00:00', '18:00:00', '2022-05-12 17:00:00', '2022-05-12 18:00:00', 1, '2022-05-11 11:31:40', '2022-05-11 11:31:40'),
(396, 154, '2022-05-14', '19:34:00', '19:49:00', '2022-05-14 19:34:00', '2022-05-14 19:49:00', 1, '2022-05-11 13:27:30', '2022-05-11 13:27:30'),
(397, 154, '2022-05-14', '19:49:00', '20:04:00', '2022-05-14 19:49:00', '2022-05-14 20:04:00', 1, '2022-05-11 13:27:30', '2022-05-11 13:27:30'),
(398, 154, '2022-05-14', '19:19:00', '19:34:00', '2022-05-14 19:19:00', '2022-05-14 19:34:00', 1, '2022-05-11 13:27:30', '2022-05-11 13:27:30'),
(399, 155, '2022-05-13', '19:00:00', '20:00:00', '2022-05-13 19:00:00', '2022-05-13 20:00:00', 1, '2022-05-12 11:14:21', '2022-05-12 11:14:21'),
(400, 156, '2022-05-14', '18:00:00', '19:00:00', '2022-05-14 18:00:00', '2022-05-14 19:00:00', 1, '2022-05-13 08:51:13', '2022-05-13 08:51:13'),
(401, 157, '2022-05-14', '17:00:00', '18:00:00', '2022-05-14 17:00:00', '2022-05-14 18:00:00', 1, '2022-05-13 09:06:33', '2022-05-13 09:06:33'),
(402, 158, '2022-05-14', '10:28:00', '11:08:00', '2022-05-14 10:28:00', '2022-05-14 11:08:00', 1, '2022-05-13 09:08:50', '2022-05-13 09:08:50'),
(403, 159, '2022-05-14', '12:00:00', '13:00:00', '2022-05-14 12:00:00', '2022-05-14 13:00:00', 1, '2022-05-13 10:06:26', '2022-05-13 10:06:26'),
(404, 160, '2022-05-14', '14:04:00', '14:24:00', '2022-05-14 14:04:00', '2022-05-14 14:24:00', 1, '2022-05-13 10:07:39', '2022-05-13 10:07:39'),
(405, 161, '2022-05-17', '07:55:00', '08:25:00', '2022-05-17 07:55:00', '2022-05-17 08:25:00', 1, '2022-05-17 11:41:38', '2022-05-17 11:41:38'),
(406, 161, '2022-05-17', '08:25:00', '08:55:00', '2022-05-17 08:25:00', '2022-05-17 08:55:00', 1, '2022-05-17 11:41:38', '2022-05-17 11:41:38'),
(407, 161, '2022-05-17', '10:25:00', '10:55:00', '2022-05-17 10:25:00', '2022-05-17 10:55:00', 1, '2022-05-17 11:41:38', '2022-05-17 11:41:38'),
(408, 162, '2022-05-23', '08:00:00', '08:25:00', '2022-05-23 08:00:00', '2022-05-23 08:25:00', 1, '2022-05-17 13:32:15', '2022-05-17 13:32:15'),
(409, 162, '2022-05-23', '08:25:00', '08:50:00', '2022-05-23 08:25:00', '2022-05-23 08:50:00', 1, '2022-05-17 13:32:16', '2022-05-17 13:32:16'),
(410, 162, '2022-05-23', '08:50:00', '09:15:00', '2022-05-23 08:50:00', '2022-05-23 09:15:00', 1, '2022-05-17 13:32:16', '2022-05-17 13:32:16'),
(411, 163, '2022-05-19', '09:00:00', '10:00:00', '2022-05-19 09:00:00', '2022-05-19 10:00:00', 1, '2022-05-18 05:01:07', '2022-05-18 05:01:07'),
(412, 164, '2022-05-19', '17:04:00', '17:19:00', '2022-05-19 17:04:00', '2022-05-19 17:19:00', 1, '2022-05-18 05:02:56', '2022-05-18 05:02:56'),
(413, 164, '2022-05-19', '17:19:00', '17:34:00', '2022-05-19 17:19:00', '2022-05-19 17:34:00', 1, '2022-05-18 05:02:56', '2022-05-18 05:02:56'),
(414, 165, '2022-05-24', '10:00:00', '10:40:00', '2022-05-24 10:00:00', '2022-05-24 10:40:00', 1, '2022-05-23 06:45:28', '2022-05-23 06:45:28'),
(415, 165, '2022-05-24', '10:40:00', '11:20:00', '2022-05-24 10:40:00', '2022-05-24 11:20:00', 1, '2022-05-23 06:45:28', '2022-05-23 06:45:28'),
(416, 166, '2022-05-24', '14:24:00', '14:44:00', '2022-05-24 14:24:00', '2022-05-24 14:44:00', 1, '2022-05-23 12:14:27', '2022-05-23 12:14:27'),
(417, 167, '2022-05-24', '14:44:00', '15:04:00', '2022-05-24 14:44:00', '2022-05-24 15:04:00', 1, '2022-05-23 12:15:26', '2022-05-23 12:15:26'),
(418, 168, '2022-05-26', '21:49:00', '22:04:00', '2022-05-26 21:49:00', '2022-05-26 22:04:00', 1, '2022-05-25 05:36:03', '2022-05-25 05:36:03'),
(419, 169, '2022-05-26', '02:28:00', '03:08:00', '2022-05-26 02:28:00', '2022-05-26 03:08:00', 1, '2022-05-25 07:21:09', '2022-05-25 07:21:09'),
(420, 170, '2022-05-26', '17:04:00', '17:19:00', '2022-05-26 17:04:00', '2022-05-26 17:19:00', 1, '2022-05-25 08:45:43', '2022-05-25 08:45:43'),
(421, 171, '2022-05-27', '17:19:00', '17:34:00', '2022-05-27 17:19:00', '2022-05-27 17:34:00', 1, '2022-05-26 05:50:31', '2022-05-26 05:50:31');
INSERT INTO `court_booking_slots` (`id`, `court_booking_id`, `booking_date`, `booking_start_time`, `booking_end_time`, `booking_start_datetime`, `booking_end_datetime`, `status`, `created_at`, `updated_at`) VALUES
(422, 172, '2022-05-27', '14:25:00', '14:45:00', '2022-05-27 14:25:00', '2022-05-27 14:45:00', 1, '2022-05-26 09:25:09', '2022-05-26 09:25:09'),
(423, 173, '2022-05-28', '17:04:00', '17:19:00', '2022-05-28 17:04:00', '2022-05-28 17:19:00', 1, '2022-05-26 09:28:57', '2022-05-26 09:28:57'),
(424, 174, '2022-05-27', '03:08:00', '03:48:00', '2022-05-27 03:08:00', '2022-05-27 03:48:00', 1, '2022-05-26 12:49:12', '2022-05-26 12:49:12'),
(425, 174, '2022-05-27', '03:48:00', '04:28:00', '2022-05-27 03:48:00', '2022-05-27 04:28:00', 1, '2022-05-26 12:49:12', '2022-05-26 12:49:12'),
(426, 175, '2022-05-27', '07:30:00', '08:10:00', '2022-05-27 07:30:00', '2022-05-27 08:10:00', 1, '2022-05-26 12:51:03', '2022-05-26 12:51:03'),
(427, 175, '2022-05-27', '08:10:00', '08:50:00', '2022-05-27 08:10:00', '2022-05-27 08:50:00', 1, '2022-05-26 12:51:03', '2022-05-26 12:51:03'),
(428, 176, '2022-05-28', '17:19:00', '17:34:00', '2022-05-28 17:19:00', '2022-05-28 17:34:00', 1, '2022-05-27 07:26:10', '2022-05-27 07:26:10'),
(429, 177, '2022-05-28', '10:00:00', '10:40:00', '2022-05-28 10:00:00', '2022-05-28 10:40:00', 1, '2022-05-27 09:03:30', '2022-05-27 09:03:30'),
(430, 177, '2022-05-28', '10:40:00', '11:20:00', '2022-05-28 10:40:00', '2022-05-28 11:20:00', 1, '2022-05-27 09:03:30', '2022-05-27 09:03:30'),
(431, 178, '2022-05-28', '11:20:00', '12:00:00', '2022-05-28 11:20:00', '2022-05-28 12:00:00', 1, '2022-05-27 09:06:29', '2022-05-27 09:06:29'),
(432, 178, '2022-05-28', '12:00:00', '12:40:00', '2022-05-28 12:00:00', '2022-05-28 12:40:00', 1, '2022-05-27 09:06:29', '2022-05-27 09:06:29'),
(433, 179, '2022-05-28', '00:05:00', '02:05:00', '2022-05-28 00:05:00', '2022-05-28 02:05:00', 1, '2022-05-27 09:07:14', '2022-05-27 09:07:14'),
(434, 179, '2022-05-28', '02:05:00', '04:05:00', '2022-05-28 02:05:00', '2022-05-28 04:05:00', 1, '2022-05-27 09:07:14', '2022-05-27 09:07:14'),
(435, 180, '2022-05-28', '07:30:00', '08:10:00', '2022-05-28 07:30:00', '2022-05-28 08:10:00', 1, '2022-05-27 10:04:28', '2022-05-27 10:04:28'),
(436, 180, '2022-05-28', '08:10:00', '08:50:00', '2022-05-28 08:10:00', '2022-05-28 08:50:00', 1, '2022-05-27 10:04:28', '2022-05-27 10:04:28'),
(437, 181, '2022-05-31', '10:00:00', '10:40:00', '2022-05-31 10:00:00', '2022-05-31 10:40:00', 1, '2022-05-30 05:48:14', '2022-05-30 05:48:14'),
(438, 181, '2022-05-31', '10:40:00', '11:20:00', '2022-05-31 10:40:00', '2022-05-31 11:20:00', 1, '2022-05-30 05:48:14', '2022-05-30 05:48:14'),
(439, 182, '2022-05-31', '09:30:00', '10:10:00', '2022-05-31 09:30:00', '2022-05-31 10:10:00', 1, '2022-05-30 05:49:19', '2022-05-30 05:49:19'),
(440, 182, '2022-05-31', '10:10:00', '10:50:00', '2022-05-31 10:10:00', '2022-05-31 10:50:00', 1, '2022-05-30 05:49:19', '2022-05-30 05:49:19'),
(441, 183, '2022-05-31', '11:00:00', '12:00:00', '2022-05-31 11:00:00', '2022-05-31 12:00:00', 1, '2022-05-30 05:50:21', '2022-05-30 05:50:21'),
(442, 184, '2022-05-31', '09:00:00', '10:00:00', '2022-05-31 09:00:00', '2022-05-31 10:00:00', 1, '2022-05-30 05:57:11', '2022-05-30 05:57:11'),
(443, 184, '2022-05-31', '10:00:00', '11:00:00', '2022-05-31 10:00:00', '2022-05-31 11:00:00', 1, '2022-05-30 05:57:11', '2022-05-30 05:57:11'),
(444, 185, '2022-05-31', '13:00:00', '14:00:00', '2022-05-31 13:00:00', '2022-05-31 14:00:00', 1, '2022-05-30 06:01:25', '2022-05-30 06:01:25'),
(445, 186, '2022-05-31', '07:55:00', '08:25:00', '2022-05-31 07:55:00', '2022-05-31 08:25:00', 1, '2022-05-30 06:13:31', '2022-05-30 06:13:31'),
(446, 186, '2022-05-31', '08:25:00', '08:55:00', '2022-05-31 08:25:00', '2022-05-31 08:55:00', 1, '2022-05-30 06:13:31', '2022-05-30 06:13:31'),
(447, 187, '2022-05-31', '17:19:00', '17:34:00', '2022-05-31 17:19:00', '2022-05-31 17:34:00', 1, '2022-05-30 09:07:47', '2022-05-30 09:07:47'),
(448, 188, '2022-06-01', '02:28:00', '03:08:00', '2022-06-01 02:28:00', '2022-06-01 03:08:00', 1, '2022-05-30 09:34:51', '2022-05-30 09:34:51'),
(449, 189, '2022-05-31', '12:00:00', '13:00:00', '2022-05-31 12:00:00', '2022-05-31 13:00:00', 1, '2022-05-30 09:35:09', '2022-05-30 09:35:09'),
(450, 190, '2022-05-31', '07:24:00', '08:04:00', '2022-05-31 07:24:00', '2022-05-31 08:04:00', 1, '2022-05-30 09:37:28', '2022-05-30 09:37:28'),
(451, 190, '2022-05-31', '08:04:00', '08:44:00', '2022-05-31 08:04:00', '2022-05-31 08:44:00', 1, '2022-05-30 09:37:28', '2022-05-30 09:37:28'),
(452, 191, '2022-05-31', '14:00:00', '15:00:00', '2022-05-31 14:00:00', '2022-05-31 15:00:00', 1, '2022-05-30 10:38:01', '2022-05-30 10:38:01'),
(453, 191, '2022-05-31', '15:00:00', '16:00:00', '2022-05-31 15:00:00', '2022-05-31 16:00:00', 1, '2022-05-30 10:38:01', '2022-05-30 10:38:01'),
(454, 192, '2022-05-31', '17:34:00', '17:49:00', '2022-05-31 17:34:00', '2022-05-31 17:49:00', 1, '2022-05-30 10:38:20', '2022-05-30 10:38:20'),
(455, 192, '2022-05-31', '17:49:00', '18:04:00', '2022-05-31 17:49:00', '2022-05-31 18:04:00', 1, '2022-05-30 10:38:20', '2022-05-30 10:38:20'),
(456, 193, '2022-05-31', '19:00:00', '20:00:00', '2022-05-31 19:00:00', '2022-05-31 20:00:00', 1, '2022-05-30 10:39:00', '2022-05-30 10:39:00'),
(457, 193, '2022-05-31', '20:00:00', '21:00:00', '2022-05-31 20:00:00', '2022-05-31 21:00:00', 1, '2022-05-30 10:39:00', '2022-05-30 10:39:00'),
(458, 194, '2022-06-01', '09:00:00', '10:00:00', '2022-06-01 09:00:00', '2022-06-01 10:00:00', 1, '2022-05-30 10:39:34', '2022-05-30 10:39:34'),
(459, 194, '2022-06-01', '10:00:00', '11:00:00', '2022-06-01 10:00:00', '2022-06-01 11:00:00', 1, '2022-05-30 10:39:34', '2022-05-30 10:39:34'),
(460, 195, '2022-05-31', '18:04:00', '18:19:00', '2022-05-31 18:04:00', '2022-05-31 18:19:00', 1, '2022-05-30 10:48:20', '2022-05-30 10:48:20'),
(461, 195, '2022-05-31', '18:19:00', '18:34:00', '2022-05-31 18:19:00', '2022-05-31 18:34:00', 1, '2022-05-30 10:48:20', '2022-05-30 10:48:20'),
(462, 196, '2022-05-31', '08:00:00', '08:25:00', '2022-05-31 08:00:00', '2022-05-31 08:25:00', 1, '2022-05-30 10:48:41', '2022-05-30 10:48:41'),
(463, 196, '2022-05-31', '08:25:00', '08:50:00', '2022-05-31 08:25:00', '2022-05-31 08:50:00', 1, '2022-05-30 10:48:41', '2022-05-30 10:48:41'),
(464, 196, '2022-05-31', '08:50:00', '09:15:00', '2022-05-31 08:50:00', '2022-05-31 09:15:00', 1, '2022-05-30 10:48:41', '2022-05-30 10:48:41'),
(465, 197, '2022-05-31', '02:28:00', '03:08:00', '2022-05-31 02:28:00', '2022-05-31 03:08:00', 1, '2022-05-30 10:49:02', '2022-05-30 10:49:02'),
(466, 197, '2022-05-31', '03:08:00', '03:48:00', '2022-05-31 03:08:00', '2022-05-31 03:48:00', 1, '2022-05-30 10:49:02', '2022-05-30 10:49:02'),
(467, 197, '2022-05-31', '03:48:00', '04:28:00', '2022-05-31 03:48:00', '2022-05-31 04:28:00', 1, '2022-05-30 10:49:02', '2022-05-30 10:49:02'),
(468, 198, '2022-06-02', '09:00:00', '10:00:00', '2022-06-02 09:00:00', '2022-06-02 10:00:00', 1, '2022-05-30 10:49:42', '2022-05-30 10:49:42'),
(469, 198, '2022-06-02', '10:00:00', '11:00:00', '2022-06-02 10:00:00', '2022-06-02 11:00:00', 1, '2022-05-30 10:49:42', '2022-05-30 10:49:42'),
(470, 198, '2022-06-02', '11:00:00', '12:00:00', '2022-06-02 11:00:00', '2022-06-02 12:00:00', 1, '2022-05-30 10:49:42', '2022-05-30 10:49:42'),
(471, 199, '2022-05-31', '18:34:00', '18:49:00', '2022-05-31 18:34:00', '2022-05-31 18:49:00', 1, '2022-05-30 10:51:08', '2022-05-30 10:51:08'),
(472, 199, '2022-05-31', '18:49:00', '19:04:00', '2022-05-31 18:49:00', '2022-05-31 19:04:00', 1, '2022-05-30 10:51:08', '2022-05-30 10:51:08'),
(473, 200, '2022-06-01', '08:55:00', '09:25:00', '2022-06-01 08:55:00', '2022-06-01 09:25:00', 1, '2022-05-30 10:57:43', '2022-05-30 10:57:43'),
(474, 201, '2022-06-05', '09:00:00', '10:00:00', '2022-06-05 09:00:00', '2022-06-05 10:00:00', 1, '2022-05-30 11:02:46', '2022-05-30 11:02:46'),
(475, 202, '2022-05-31', '08:44:00', '09:24:00', '2022-05-31 08:44:00', '2022-05-31 09:24:00', 1, '2022-05-30 11:06:05', '2022-05-30 11:06:05'),
(476, 203, '2022-05-31', '09:15:00', '09:40:00', '2022-05-31 09:15:00', '2022-05-31 09:40:00', 1, '2022-05-30 11:10:53', '2022-05-30 11:10:53'),
(477, 204, '2022-05-31', '17:04:00', '17:19:00', '2022-05-31 17:04:00', '2022-05-31 17:19:00', 1, '2022-05-30 11:11:18', '2022-05-30 11:11:18'),
(478, 205, '2022-05-31', '16:00:00', '17:00:00', '2022-05-31 16:00:00', '2022-05-31 17:00:00', 1, '2022-05-30 11:12:27', '2022-05-30 11:12:27'),
(479, 206, '2022-05-31', '09:40:00', '10:05:00', '2022-05-31 09:40:00', '2022-05-31 10:05:00', 1, '2022-05-30 11:14:15', '2022-05-30 11:14:15'),
(480, 207, '2022-06-01', '17:04:00', '17:19:00', '2022-06-01 17:04:00', '2022-06-01 17:19:00', 1, '2022-05-30 11:15:17', '2022-05-30 11:15:17'),
(481, 208, '2022-05-31', '17:00:00', '18:00:00', '2022-05-31 17:00:00', '2022-05-31 18:00:00', 1, '2022-05-30 11:18:55', '2022-05-30 11:18:55'),
(482, 208, '2022-05-31', '18:00:00', '19:00:00', '2022-05-31 18:00:00', '2022-05-31 19:00:00', 1, '2022-05-30 11:18:55', '2022-05-30 11:18:55'),
(483, 209, '2022-05-31', '19:04:00', '19:19:00', '2022-05-31 19:04:00', '2022-05-31 19:19:00', 1, '2022-05-30 11:19:24', '2022-05-30 11:19:24'),
(484, 209, '2022-05-31', '19:19:00', '19:34:00', '2022-05-31 19:19:00', '2022-05-31 19:34:00', 1, '2022-05-30 11:19:24', '2022-05-30 11:19:24'),
(485, 210, '2022-05-31', '08:55:00', '09:25:00', '2022-05-31 08:55:00', '2022-05-31 09:25:00', 1, '2022-05-30 11:20:27', '2022-05-30 11:20:27'),
(486, 210, '2022-05-31', '09:25:00', '09:55:00', '2022-05-31 09:25:00', '2022-05-31 09:55:00', 1, '2022-05-30 11:20:27', '2022-05-30 11:20:27'),
(487, 211, '2022-06-06', '13:00:00', '14:00:00', '2022-06-06 13:00:00', '2022-06-06 14:00:00', 1, '2022-05-30 11:41:00', '2022-05-30 11:41:00'),
(488, 212, '2022-06-02', '09:00:00', '10:00:00', '2022-06-02 09:00:00', '2022-06-02 10:00:00', 1, '2022-05-30 11:42:44', '2022-05-30 11:42:44'),
(489, 212, '2022-06-02', '10:00:00', '11:00:00', '2022-06-02 10:00:00', '2022-06-02 11:00:00', 1, '2022-05-30 11:42:44', '2022-05-30 11:42:44'),
(490, 213, '2022-06-01', '17:19:00', '17:34:00', '2022-06-01 17:19:00', '2022-06-01 17:34:00', 1, '2022-05-30 11:43:14', '2022-05-30 11:43:14'),
(491, 213, '2022-06-01', '17:34:00', '17:49:00', '2022-06-01 17:34:00', '2022-06-01 17:49:00', 1, '2022-05-30 11:43:14', '2022-05-30 11:43:14'),
(492, 214, '2022-05-31', '22:00:00', '22:40:00', '2022-05-31 22:00:00', '2022-05-31 22:40:00', 1, '2022-05-30 11:44:30', '2022-05-30 11:44:30'),
(493, 214, '2022-05-31', '22:40:00', '23:20:00', '2022-05-31 22:40:00', '2022-05-31 23:20:00', 1, '2022-05-30 11:44:30', '2022-05-30 11:44:30'),
(494, 215, '2022-08-24', '08:00:00', '10:00:00', '2022-08-24 08:00:00', '2022-08-24 10:00:00', 1, '2022-05-30 11:45:19', '2022-05-30 11:45:19'),
(495, 215, '2022-08-24', '11:00:00', '12:00:00', '2022-08-24 11:00:00', '2022-08-24 12:00:00', 1, '2022-05-30 11:45:19', '2022-05-30 11:45:19'),
(496, 216, '2022-08-25', '08:00:00', '10:00:00', '2022-08-25 08:00:00', '2022-08-25 10:00:00', 1, '2022-05-30 11:46:02', '2022-05-30 11:46:02'),
(497, 216, '2022-08-25', '11:00:00', '12:00:00', '2022-08-25 11:00:00', '2022-08-25 12:00:00', 1, '2022-05-30 11:46:02', '2022-05-30 11:46:02'),
(498, 217, '2022-06-01', '11:00:00', '12:00:00', '2022-06-01 11:00:00', '2022-06-01 12:00:00', 1, '2022-05-30 12:00:47', '2022-05-30 12:00:47'),
(499, 218, '2022-05-31', '17:04:00', '17:19:00', '2022-05-31 17:04:00', '2022-05-31 17:19:00', 1, '2022-05-30 12:01:13', '2022-05-30 12:01:13'),
(500, 219, '2022-06-02', '17:34:00', '17:49:00', '2022-06-02 17:34:00', '2022-06-02 17:49:00', 1, '2022-05-30 12:02:22', '2022-05-30 12:02:22'),
(501, 220, '2022-08-26', '08:00:00', '10:00:00', '2022-08-26 08:00:00', '2022-08-26 10:00:00', 1, '2022-05-30 12:13:11', '2022-05-30 12:13:11'),
(502, 220, '2022-08-26', '11:00:00', '12:00:00', '2022-08-26 11:00:00', '2022-08-26 12:00:00', 1, '2022-05-30 12:13:11', '2022-05-30 12:13:11');

-- --------------------------------------------------------

--
-- Table structure for table `court_categories`
--

CREATE TABLE `court_categories` (
  `id` bigint NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `court_categories`
--

INSERT INTO `court_categories` (`id`, `name`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Skating', 'MAR2022/1648215013-court_category.png', 1, '2022-03-23 03:00:27', '2022-03-25 13:30:13'),
(2, 'Football', 'MAR2022/1648214970-court_category.png', 1, '2022-03-23 03:04:41', '2022-03-25 13:29:30'),
(3, 'Cricket', 'MAR2022/1648214944-court_category.png', 1, '2022-03-23 03:19:10', '2022-04-08 06:19:56'),
(4, 'Tennis', 'MAY2022/1653022276-court_category.png', 1, '2022-04-18 09:35:31', '2022-05-20 04:51:16');

-- --------------------------------------------------------

--
-- Table structure for table `court_categories_lang`
--

CREATE TABLE `court_categories_lang` (
  `id` bigint NOT NULL,
  `court_category_id` bigint DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `lang` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `court_categories_lang`
--

INSERT INTO `court_categories_lang` (`id`, `court_category_id`, `name`, `lang`, `created_at`, `updated_at`) VALUES
(1, 1, 'Skating', 'en', '2022-03-23 03:00:27', '2022-03-25 13:30:13'),
(2, 1, 'Skating', 'ar', '2022-03-23 03:00:27', '2022-03-25 13:30:13'),
(3, 2, 'Football', 'en', '2022-03-23 03:04:41', '2022-03-25 13:29:30'),
(4, 2, 'Football', 'ar', '2022-03-23 03:04:41', '2022-03-25 13:29:30'),
(5, 3, 'Cricket', 'en', '2022-03-23 03:19:10', '2022-03-25 13:29:04'),
(6, 3, 'Cricket', 'ar', '2022-03-23 03:19:10', '2022-03-25 13:29:04'),
(7, 4, 'Tennis', 'en', '2022-04-18 09:35:31', '2022-05-20 04:51:16'),
(8, 4, 'Tennis', 'ar', '2022-04-18 09:35:31', '2022-05-20 04:51:16');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_price`
--

CREATE TABLE `delivery_price` (
  `id` int NOT NULL,
  `common_commission_percentage` int DEFAULT NULL,
  `cancellation_charge` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `delivery_price`
--

INSERT INTO `delivery_price` (`id`, `common_commission_percentage`, `cancellation_charge`, `created_at`, `updated_at`) VALUES
(1, 12, 15, '2020-08-12 06:26:34', '2022-04-25 13:11:47');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci,
  `footer` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `name`, `slug`, `subject`, `description`, `footer`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'FORGOT YOUR PASSWORD!', 'forgot_password', 'Hello [NAME],', 'Your forgot password OTP is: [OTP]', 'Copyright Â© 2021 Tahadiyaat. All rights reserved.', 1, '2021-01-19 05:39:11', '2022-03-25 13:11:22', NULL),
(3, 'WELCOME [NAME]!', 'register', 'You have successfully registered on Tahadiyaat app.', 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.', 'Copyright Â© 2021 Tahadiyaat. All rights reserved.', 1, '2021-01-19 05:40:15', '2022-03-25 13:10:56', NULL),
(4, 'Global Notification', 'report', 'KiloPoints Reminder', '[YOUR_QUERY]\r\n<br><br>\r\n[REPLY]', 'Copyright Â© 2021 Tahadiyaat. All rights reserved.', 1, '2021-01-19 05:40:15', '2022-03-25 13:11:09', NULL),
(5, 'Order Notification', 'order', 'Your order is received, Store will contact you soon.', '<tr>\r\n  <td style=\"padding: 50px 30px;\">\r\n\r\n    <h3 style=\"margin: 0px auto 20px;font-size: 35px;color: #383838;max-width: 80%;font-weight: 700;line-height: 1.2;\">[NAME]</h3>\r\n    <p style=\"margin-bottom: 5px;margin-top: 0;color: #7a7a7a;font-weight: 600;font-size: 15px;\">Date: [ORDER_DATE]</p>\r\n    <p style=\"margin-bottom: 5px;margin-top: 0;color: #7a7a7a;font-weight: 600;font-size: 15px;\">Order ID: #[ORDER_ID]</p>\r\n    <table style=\"text-align: center;width: 100%;border-collapse: collapse;margin: 40px 0;\">\r\n      <tr>\r\n        <td style=\"border-right: 1px solid #ddd;width: 33.33%\">\r\n          <p style=\"margin-bottom: 10px;\">Order Status</p>\r\n          <h3 style=\"margin-top: 0px;\">[ORDER_STATUS]</h3>\r\n        </td>\r\n        <td style=\"width: 33.33%\">\r\n          <p style=\"margin-bottom: 10px;\">Price</p>\r\n          <h3 style=\"margin-top: 0px;\">QAR[PRICE]</h3>\r\n        </td>\r\n      </tr>\r\n    </table>\r\n\r\n    <p style=\"margin-bottom: 0px;margin-top: 0;color: #7a7a7a;font-weight: 600;\">User Name: <span style=\"display: block;font-size: 14px;font-weight: normal;margin-top: 5px;\">[USERNAME]</span></p>\r\n  </td>\r\n</tr>', 'Copyright Â© 2021 Tahadiyaat. All rights reserved.', 1, '2021-01-19 05:39:11', '2022-03-25 13:11:40', NULL),
(6, 'New Facility Added', 'facility', 'New Facility Added', 'Hello [NAME],\r\n\r\nYou have added new facility [FACILITY_NAME]. Please check and review.', '2022 © Tahadiyaat. All Rights Reserved', 1, '2021-01-19 11:09:11', '2022-03-31 10:34:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_template_lang`
--

CREATE TABLE `email_template_lang` (
  `id` int NOT NULL,
  `email_id` int DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `description` longtext,
  `footer` varchar(255) DEFAULT NULL,
  `lang` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `email_template_lang`
--

INSERT INTO `email_template_lang` (`id`, `email_id`, `name`, `subject`, `description`, `footer`, `lang`, `created_at`, `updated_at`) VALUES
(1, 3, 'WELCOME [NAME]!', 'You have successfully registered on Tahadiyaat app.', 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.', 'Copyright Â© 2021 Tahadiyaat. All rights reserved.', 'en', '2021-01-19 01:30:25', '2022-03-25 13:10:56'),
(2, 3, 'WELCOME [NAME]!', 'You have successfully registered on Tahadiyaat app.', 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.', 'Copyright Â© 2021 Tahadiyaat. All rights reserved.', 'ar', '2021-01-19 01:30:25', '2022-03-25 13:10:56'),
(3, 2, 'FORGOT YOUR PASSWORD!', 'Hello [NAME],', 'Your forgot password OTP is: [OTP]', 'Copyright Â© 2021 Tahadiyaat. All rights reserved.', 'en', '2021-01-19 01:33:09', '2022-03-25 13:11:22'),
(4, 2, 'FORGOT YOUR PASSWORD!', 'Hello [NAME],', 'There was a request to change your password!', 'Copyright Â© 2021 Tahadiyaat. All rights reserved.', 'ar', '2021-01-19 01:33:09', '2022-03-25 13:11:22'),
(7, 4, 'Global Notification', 'KiloPoints Reminder', '[YOUR_QUERY]\r\n<br><br>\r\n[REPLY]', 'Copyright Â© 2021 Tahadiyaat. All rights reserved.', 'en', '2021-01-19 03:52:45', '2022-03-25 13:11:09'),
(8, 4, 'Global Notification', 'KiloPoints Reminder', '[YOUR_QUERY]\r\n<br><br>\r\n[REPLY]', 'Copyright Â© 2021 Tahadiyaat. All rights reserved.', 'ar', '2021-01-19 03:52:46', '2022-03-25 13:11:09'),
(29, 5, 'Order Notification', 'Your order is received, Store will contact you soon.', '<tr>\r\n  <td style=\"padding: 50px 30px;\">\r\n\r\n    <h3 style=\"margin: 0px auto 20px;font-size: 35px;color: #383838;max-width: 80%;font-weight: 700;line-height: 1.2;\">[NAME]</h3>\r\n    <p style=\"margin-bottom: 5px;margin-top: 0;color: #7a7a7a;font-weight: 600;font-size: 15px;\">Date: [ORDER_DATE]</p>\r\n    <p style=\"margin-bottom: 5px;margin-top: 0;color: #7a7a7a;font-weight: 600;font-size: 15px;\">Order ID: #[ORDER_ID]</p>\r\n    <table style=\"text-align: center;width: 100%;border-collapse: collapse;margin: 40px 0;\">\r\n      <tr>\r\n        <td style=\"border-right: 1px solid #ddd;width: 33.33%\">\r\n          <p style=\"margin-bottom: 10px;\">Order Status</p>\r\n          <h3 style=\"margin-top: 0px;\">[ORDER_STATUS]</h3>\r\n        </td>\r\n        <td style=\"width: 33.33%\">\r\n          <p style=\"margin-bottom: 10px;\">Price</p>\r\n          <h3 style=\"margin-top: 0px;\">QAR[PRICE]</h3>\r\n        </td>\r\n      </tr>\r\n    </table>\r\n\r\n    <p style=\"margin-bottom: 0px;margin-top: 0;color: #7a7a7a;font-weight: 600;\">User Name: <span style=\"display: block;font-size: 14px;font-weight: normal;margin-top: 5px;\">[USERNAME]</span></p>\r\n  </td>\r\n</tr>', 'Copyright Â© 2021 Tahadiyaat. All rights reserved.', 'en', '2021-01-19 03:52:45', '2022-03-25 13:11:40'),
(30, 5, 'إشعار الطلب', 'تم استلام طلبك ، سيتصل بك المتجر قريبًا.', '<tr>\r\n  <td style=\"padding: 50px 30px;\">\r\n\r\n    <h3 style=\"margin: 0px auto 20px;font-size: 35px;color: #383838;max-width: 80%;font-weight: 700;line-height: 1.2;\">[NAME]</h3>\r\n    <p style=\"margin-bottom: 5px;margin-top: 0;color: #7a7a7a;font-weight: 600;font-size: 15px;\">تاريخ: [ORDER_DATE]</p>\r\n    <p style=\"margin-bottom: 5px;margin-top: 0;color: #7a7a7a;font-weight: 600;font-size: 15px;\">رقم التعريف الخاص بالطلب: #[ORDER_ID]</p>\r\n    <table style=\"text-align: center;width: 100%;border-collapse: collapse;margin: 40px 0;\">\r\n      <tr>\r\n        <td style=\"border-right: 1px solid #ddd;width: 33.33%\">\r\n          <p style=\"margin-bottom: 10px;\">حالة الطلب</p>\r\n          <h3 style=\"margin-top: 0px;\">[ORDER_STATUS]</h3>\r\n        </td>\r\n        <td style=\"width: 33.33%\">\r\n          <p style=\"margin-bottom: 10px;\">سعر</p>\r\n          <h3 style=\"margin-top: 0px;\">QAR[PRICE]</h3>\r\n        </td>\r\n      </tr>\r\n    </table>\r\n\r\n    <p style=\"margin-bottom: 0px;margin-top: 0;color: #7a7a7a;font-weight: 600;\">اسم االمستخدم: <span style=\"display: block;font-size: 14px;font-weight: normal;margin-top: 5px;\">[USERNAME]</span></p>\r\n  </td>\r\n</tr>', 'حقوق النشر Â © 2021 Tahadiyaat. كل الحقوق محفوظة.', 'ar', '2021-01-19 03:52:46', '2022-03-25 13:11:40'),
(31, 6, 'New Facility Added', 'New Facility Added', 'Hello [NAME],\r\n\r\nYou have added new facility [FACILITY_NAME]. Please check and review.', '2022 © Tahadiyaat. All Rights Reserved', 'en', '2021-01-19 09:22:45', '2022-03-31 10:34:35'),
(32, 6, 'إشعار الطلب', 'تم استلام طلبك ، سيتصل بك المتجر قريبًا.', '<tr>\r\n <td style=\"padding: 50px 30px;\">\r\n\r\n <h3 style=\"margin: 0px auto 20px;font-size: 35px;color: #383838;max-width: 80%;font-weight: 700;line-height: 1.2;\">[NAME]</h3>\r\n <p style=\"margin-bottom: 5px;margin-top: 0;color: #7a7a7a;font-weight: 600;font-size: 15px;\">تاريخ: [ORDER_DATE]</p>\r\n <p style=\"margin-bottom: 5px;margin-top: 0;color: #7a7a7a;font-weight: 600;font-size: 15px;\">رقم التعريف الخاص بالطلب: #[ORDER_ID]</p>\r\n <table style=\"text-align: center;width: 100%;border-collapse: collapse;margin: 40px 0;\">\r\n <tr>\r\n <td style=\"border-right: 1px solid #ddd;width: 33.33%\">\r\n <p style=\"margin-bottom: 10px;\">حالة الطلب</p>\r\n <h3 style=\"margin-top: 0px;\">[ORDER_STATUS]</h3>\r\n </td>\r\n <td style=\"width: 33.33%\">\r\n <p style=\"margin-bottom: 10px;\">سعر</p>\r\n <h3 style=\"margin-top: 0px;\">QAR[PRICE]</h3>\r\n </td>\r\n </tr>\r\n </table>\r\n\r\n <p style=\"margin-bottom: 0px;margin-top: 0;color: #7a7a7a;font-weight: 600;\">اسم االمستخدم: <span style=\"display: block;font-size: 14px;font-weight: normal;margin-top: 5px;\">[USERNAME]</span></p>\r\n </td>\r\n</tr>', '2022 © Tahadiyaat. All Rights Reserved', 'ar', '2021-01-19 09:22:46', '2022-03-31 10:34:35');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` bigint NOT NULL,
  `facility_owner_id` bigint DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `address` text,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `average_rating` float(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `facility_owner_id`, `name`, `image`, `address`, `latitude`, `longitude`, `status`, `average_rating`, `created_at`, `updated_at`) VALUES
(8, 3, 'Shark Jetski Dubai', 'APR2022/1651238315-facility.jpg', 'Burj Khalifa - Sheikh Mohammed bin Rashid Boulevard - Dubai - United Arab Emirates', '25.197197', '55.27437639999999', 1, NULL, '2022-03-22 01:27:37', '2022-05-06 10:44:58'),
(9, 2, 'Sport Factory', 'APR2022/1651238299-facility.jpg', 'Dubai Expo 2020 - Al Wasl Avenue - Dubai - United Arab Emirates', '24.9619209', '55.1487732', 1, NULL, '2022-03-22 03:13:05', '2022-05-06 10:45:32'),
(10, 2, 'Shark Jetski Dubai', 'APR2022/1651238278-facility.jpg', 'Mall Of Emirates - Al Barsha Road - Dubai - United Arab Emirates', '25.1158136', '55.203267', 1, NULL, '2022-03-25 12:56:11', '2022-05-06 10:46:03'),
(11, 2, 'Flamingo Basketball', 'APR2022/1651238257-facility.jpg', 'Dubai Marina Mall - Sheikh Zayed Road - Dubai - United Arab Emirates', '25.07643', '55.140504', 1, NULL, '2022-03-25 13:17:32', '2022-05-06 10:44:38'),
(12, 2, 'SMART WAY', NULL, 'The Dubai Mall - Dubai - United Arab Emirates', '25.198765', '55.2796053', 1, NULL, '2022-03-30 14:36:15', '2022-04-05 06:57:25'),
(13, 4, 'Facility f1', NULL, 'Dubai - United Arab Emirates', '25.2048493', '55.2707828', 1, NULL, '2022-04-05 06:57:57', '2022-05-06 10:47:07'),
(14, 3, 'Facility f2', NULL, 'UAE Flower Farm - Fujairah - United Arab Emirates', '25.406641', '56.150016', 1, NULL, '2022-04-05 06:58:37', '2022-04-05 06:58:37'),
(15, 46, 'Facility f3', NULL, 'UAE - Dubai - United Arab Emirates', '25.2048493', '55.2707828', 1, NULL, '2022-04-05 06:59:02', '2022-05-06 10:04:29'),
(16, 45, 'Facility f4', NULL, 'UAE Pavilion - Abu Dhabi - United Arab Emirates', '24.5332218', '54.418228', 1, NULL, '2022-04-05 06:59:31', '2022-05-06 10:46:43'),
(17, 5, 'Facility f5', NULL, 'UAE Flower Farm - Fujairah - United Arab Emirates', '25.406641', '56.150016', 1, NULL, '2022-04-05 07:00:01', '2022-04-20 07:30:11'),
(18, 4, 'Facility f6', NULL, 'UAE Flower Farm - Fujairah - United Arab Emirates', '25.406641', '56.150016', 1, NULL, '2022-04-05 07:00:24', '2022-05-06 10:19:58'),
(19, 50, 'Malik', 'APR2022/1651238202-facility.jpg', 'Dubai - United Arab Emirates', '25.2048493', '55.2707828', 1, NULL, '2022-04-06 04:30:55', '2022-05-06 10:03:49'),
(20, 46, 'New Facility test', NULL, 'Dubai Festival City Mall - Crescent Drive - Dubai - United Arab Emirates', '25.2219515', '55.35279430000001', 1, NULL, '2022-04-08 05:57:34', '2022-05-06 10:44:02'),
(21, 46, 'Facility 12', 'APR2022/1651238231-facility.jpg', 'The Dubai Mall - Dubai - United Arab Emirates', '25.198765', '55.2796053', 1, NULL, '2022-04-08 10:23:19', '2022-05-06 10:02:47'),
(22, 60, 'Sandeep Stadium', 'APR2022/1651238216-facility.jpg', 'Dubai - United Arab Emirates', '25.2048493', '55.2707828', 0, NULL, '2022-04-09 11:34:08', '2022-05-06 04:58:05'),
(23, 46, 'Malik', 'APR2022/1649846848-facility.jpg', 'Dubai Hills Mall, Dubai Hills Estate - Dubai - United Arab Emirates', '25.1044748', '55.2372069', 0, NULL, '2022-04-13 10:47:28', '2022-05-06 04:58:09'),
(24, 61, 'Testing', 'APR2022/1650014166-facility.jpg', 'UAE', '23.424076', '53.847818', 0, NULL, '2022-04-15 09:16:06', '2022-05-06 04:58:11'),
(25, 46, 'Testing', 'APR2022/1650287817-facility.jpeg', 'Dubai - United Arab Emirates', '25.2048493', '55.2707828', 0, NULL, '2022-04-18 10:01:10', '2022-05-06 04:57:50'),
(26, 61, 'Diamond', 'APR2022/1650287807-facility.jpg', 'Burj Khalifa - Sheikh Mohammed bin Rashid Boulevard - Dubai - United Arab Emirates', '25.197197', '55.27437639999999', 0, NULL, '2022-04-18 10:02:06', '2022-05-06 04:58:00'),
(27, 3, 'Shark Jetski Dubai', NULL, 'Jaipur, Rajasthan, India', '26.9124336', '75.7872709', 0, NULL, '2022-04-20 07:31:26', '2022-05-10 06:51:53');

-- --------------------------------------------------------

--
-- Table structure for table `facilities_lang`
--

CREATE TABLE `facilities_lang` (
  `id` bigint NOT NULL,
  `facility_id` bigint DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `lang` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facilities_lang`
--

INSERT INTO `facilities_lang` (`id`, `facility_id`, `name`, `lang`, `created_at`, `updated_at`) VALUES
(1, 10, 'Shark Jetski Dubai', 'en', '2022-03-24 04:02:32', '2022-05-06 10:46:03'),
(2, 10, 'شارك جيتسكي دبي', 'ar', '2022-03-24 04:02:32', '2022-05-06 10:46:03'),
(3, 9, 'Sport Factory', 'en', '2022-03-25 12:56:11', '2022-05-06 10:45:32'),
(4, 9, 'مصنع الرياضة', 'ar', '2022-03-25 12:56:11', '2022-05-06 10:45:32'),
(5, 8, 'Shark Jetski Dubai', 'en', '2022-03-25 12:56:11', '2022-05-06 10:44:58'),
(6, 8, 'شارك جيتسكي دبي', 'ar', '2022-03-25 12:56:11', '2022-05-06 10:44:58'),
(7, 11, 'Flamingo Basketball', 'en', '2022-03-25 13:17:32', '2022-05-06 10:44:38'),
(8, 11, 'كرة السلة فلامنغو', 'ar', '2022-03-25 13:17:32', '2022-05-06 10:44:38'),
(9, 12, 'SMART WAY', 'en', '2022-03-30 14:36:15', '2022-04-05 06:57:25'),
(10, 12, 'سمارت واي', 'ar', '2022-03-30 14:36:15', '2022-04-05 06:57:25'),
(11, 13, 'Facility f1', 'en', '2022-04-05 06:57:57', '2022-05-06 10:47:07'),
(12, 13, 'مرفق f1', 'ar', '2022-04-05 06:57:57', '2022-05-06 10:47:07'),
(13, 14, 'Facility f2', 'en', '2022-04-05 06:58:37', '2022-04-05 06:58:37'),
(14, 14, 'dgfgd', 'ar', '2022-04-05 06:58:37', '2022-04-05 06:58:37'),
(15, 15, 'Facility f3', 'en', '2022-04-05 06:59:02', '2022-05-06 10:04:29'),
(16, 15, 'مرفق f3', 'ar', '2022-04-05 06:59:02', '2022-05-06 10:04:29'),
(17, 16, 'Facility f4', 'en', '2022-04-05 06:59:31', '2022-05-06 10:46:43'),
(18, 16, 'مرفق f4', 'ar', '2022-04-05 06:59:31', '2022-05-06 10:46:43'),
(19, 17, 'Facility f5', 'en', '2022-04-05 07:00:01', '2022-04-20 07:30:11'),
(20, 17, 'dgfgd', 'ar', '2022-04-05 07:00:01', '2022-04-20 07:30:11'),
(21, 18, 'Facility f6', 'en', '2022-04-05 07:00:24', '2022-05-06 10:19:58'),
(22, 18, 'مرفق f6', 'ar', '2022-04-05 07:00:24', '2022-05-06 10:19:58'),
(23, 19, 'Malik', 'en', '2022-04-06 04:30:55', '2022-05-06 10:03:49'),
(24, 19, 'مالك', 'ar', '2022-04-06 04:30:55', '2022-05-06 10:03:49'),
(25, 20, 'New Facility test', 'en', '2022-04-08 05:57:34', '2022-05-06 10:44:02'),
(26, 20, 'New Facility test', 'ar', '2022-04-08 05:57:34', '2022-05-06 10:44:02'),
(27, 21, 'Facility 12', 'en', '2022-04-08 10:23:19', '2022-05-06 10:02:48'),
(28, 21, 'مرفق 12', 'ar', '2022-04-08 10:23:19', '2022-05-06 10:02:48'),
(29, 22, 'Sandeep Stadium', 'en', '2022-04-09 11:34:08', '2022-05-05 13:36:28'),
(30, 22, 'ملعب سديب', 'ar', '2022-04-09 11:34:08', '2022-05-05 13:36:28'),
(31, 23, 'Malik', 'en', '2022-04-13 10:47:28', '2022-04-21 06:41:59'),
(32, 23, 'مالك', 'ar', '2022-04-13 10:47:28', '2022-04-21 06:41:59'),
(33, 24, 'Testing', 'en', '2022-04-15 09:16:06', '2022-04-15 09:16:06'),
(34, 24, 'sdfffd', 'ar', '2022-04-15 09:16:06', '2022-04-15 09:16:06'),
(35, 25, 'Testing', 'en', '2022-04-18 10:01:10', '2022-04-18 13:16:57'),
(36, 25, 'Diamond', 'ar', '2022-04-18 10:01:10', '2022-04-18 13:16:57'),
(37, 26, 'Diamond', 'en', '2022-04-18 10:02:06', '2022-04-20 07:29:04'),
(38, 26, 'Mini', 'ar', '2022-04-18 10:02:06', '2022-04-20 07:29:04'),
(39, 27, 'Shark Jetski Dubai', 'en', '2022-04-20 07:31:26', '2022-05-10 06:51:53'),
(40, 27, 'شارك جيتسكي دبي', 'ar', '2022-04-20 07:31:26', '2022-05-10 06:51:53');

-- --------------------------------------------------------

--
-- Table structure for table `facility_amenities`
--

CREATE TABLE `facility_amenities` (
  `id` bigint NOT NULL,
  `facility_id` bigint DEFAULT NULL,
  `amenity_id` bigint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facility_amenities`
--

INSERT INTO `facility_amenities` (`id`, `facility_id`, `amenity_id`, `created_at`, `updated_at`) VALUES
(1, 6, 5, '2022-03-22 00:21:21', '2022-03-22 00:21:21'),
(18, 7, 3, '2022-03-22 03:10:58', '2022-03-22 03:10:58'),
(19, 7, 5, '2022-03-22 03:10:58', '2022-03-22 03:10:58'),
(20, 5, 4, '2022-03-22 03:11:53', '2022-03-22 03:11:53'),
(21, 5, 5, '2022-03-22 03:11:53', '2022-03-22 03:11:53'),
(22, 1, 4, '2022-03-22 03:12:03', '2022-03-22 03:12:03'),
(23, 1, 5, '2022-03-22 03:12:03', '2022-03-22 03:12:03'),
(51, 12, 3, '2022-04-05 06:57:25', '2022-04-05 06:57:25'),
(52, 12, 4, '2022-04-05 06:57:25', '2022-04-05 06:57:25'),
(55, 14, 4, '2022-04-05 06:58:37', '2022-04-05 06:58:37'),
(83, 24, 3, '2022-04-15 09:16:06', '2022-04-15 09:16:06'),
(89, 25, 3, '2022-04-18 13:16:57', '2022-04-18 13:16:57'),
(94, 26, 3, '2022-04-20 07:29:04', '2022-04-20 07:29:04'),
(95, 26, 5, '2022-04-20 07:29:04', '2022-04-20 07:29:04'),
(96, 17, 3, '2022-04-20 07:30:11', '2022-04-20 07:30:11'),
(102, 23, 2, '2022-04-21 06:41:59', '2022-04-21 06:41:59'),
(103, 23, 3, '2022-04-21 06:41:59', '2022-04-21 06:41:59'),
(104, 23, 4, '2022-04-21 06:41:59', '2022-04-21 06:41:59'),
(105, 23, 5, '2022-04-21 06:41:59', '2022-04-21 06:41:59'),
(106, 23, 7, '2022-04-21 06:41:59', '2022-04-21 06:41:59'),
(107, 23, 8, '2022-04-21 06:41:59', '2022-04-21 06:41:59'),
(120, 22, 2, '2022-05-05 13:36:28', '2022-05-05 13:36:28'),
(121, 22, 3, '2022-05-05 13:36:28', '2022-05-05 13:36:28'),
(122, 21, 3, '2022-05-06 10:02:47', '2022-05-06 10:02:47'),
(123, 19, 3, '2022-05-06 10:03:49', '2022-05-06 10:03:49'),
(124, 19, 4, '2022-05-06 10:03:49', '2022-05-06 10:03:49'),
(125, 19, 5, '2022-05-06 10:03:49', '2022-05-06 10:03:49'),
(126, 15, 3, '2022-05-06 10:04:29', '2022-05-06 10:04:29'),
(127, 18, 3, '2022-05-06 10:19:58', '2022-05-06 10:19:58'),
(140, 20, 2, '2022-05-06 10:44:02', '2022-05-06 10:44:02'),
(141, 20, 3, '2022-05-06 10:44:02', '2022-05-06 10:44:02'),
(142, 20, 4, '2022-05-06 10:44:02', '2022-05-06 10:44:02'),
(143, 20, 5, '2022-05-06 10:44:02', '2022-05-06 10:44:02'),
(144, 11, 3, '2022-05-06 10:44:38', '2022-05-06 10:44:38'),
(145, 8, 4, '2022-05-06 10:44:58', '2022-05-06 10:44:58'),
(146, 9, 4, '2022-05-06 10:45:32', '2022-05-06 10:45:32'),
(147, 9, 5, '2022-05-06 10:45:32', '2022-05-06 10:45:32'),
(148, 10, 2, '2022-05-06 10:46:03', '2022-05-06 10:46:03'),
(149, 10, 4, '2022-05-06 10:46:03', '2022-05-06 10:46:03'),
(150, 16, 4, '2022-05-06 10:46:43', '2022-05-06 10:46:43'),
(151, 13, 3, '2022-05-06 10:47:07', '2022-05-06 10:47:07'),
(153, 27, 3, '2022-05-10 06:51:53', '2022-05-10 06:51:53');

-- --------------------------------------------------------

--
-- Table structure for table `facility_categories`
--

CREATE TABLE `facility_categories` (
  `id` bigint NOT NULL,
  `facility_id` bigint DEFAULT NULL,
  `category_id` bigint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facility_categories`
--

INSERT INTO `facility_categories` (`id`, `facility_id`, `category_id`, `created_at`, `updated_at`) VALUES
(27, 12, 1, '2022-04-05 06:57:25', '2022-04-05 06:57:25'),
(28, 12, 2, '2022-04-05 06:57:25', '2022-04-05 06:57:25'),
(29, 12, 3, '2022-04-05 06:57:25', '2022-04-05 06:57:25'),
(32, 14, 2, '2022-04-05 06:58:37', '2022-04-05 06:58:37'),
(56, 24, 3, '2022-04-15 09:16:06', '2022-04-15 09:16:06'),
(60, 25, 2, '2022-04-18 13:16:57', '2022-04-18 13:16:57'),
(63, 26, 3, '2022-04-20 07:29:04', '2022-04-20 07:29:04'),
(64, 17, 1, '2022-04-20 07:30:11', '2022-04-20 07:30:11'),
(65, 17, 2, '2022-04-20 07:30:11', '2022-04-20 07:30:11'),
(71, 23, 3, '2022-04-21 06:41:59', '2022-04-21 06:41:59'),
(82, 22, 2, '2022-05-05 13:36:28', '2022-05-05 13:36:28'),
(83, 21, 2, '2022-05-06 10:02:48', '2022-05-06 10:02:48'),
(84, 19, 1, '2022-05-06 10:03:49', '2022-05-06 10:03:49'),
(85, 19, 3, '2022-05-06 10:03:49', '2022-05-06 10:03:49'),
(86, 15, 2, '2022-05-06 10:04:29', '2022-05-06 10:04:29'),
(87, 15, 3, '2022-05-06 10:04:29', '2022-05-06 10:04:29'),
(88, 18, 2, '2022-05-06 10:19:58', '2022-05-06 10:19:58'),
(100, 20, 1, '2022-05-06 10:44:02', '2022-05-06 10:44:02'),
(101, 20, 2, '2022-05-06 10:44:02', '2022-05-06 10:44:02'),
(102, 20, 3, '2022-05-06 10:44:02', '2022-05-06 10:44:02'),
(103, 11, 2, '2022-05-06 10:44:38', '2022-05-06 10:44:38'),
(104, 8, 2, '2022-05-06 10:44:58', '2022-05-06 10:44:58'),
(105, 9, 2, '2022-05-06 10:45:32', '2022-05-06 10:45:32'),
(106, 10, 1, '2022-05-06 10:46:03', '2022-05-06 10:46:03'),
(107, 10, 2, '2022-05-06 10:46:03', '2022-05-06 10:46:03'),
(108, 10, 3, '2022-05-06 10:46:03', '2022-05-06 10:46:03'),
(109, 16, 3, '2022-05-06 10:46:43', '2022-05-06 10:46:43'),
(110, 13, 2, '2022-05-06 10:47:07', '2022-05-06 10:47:07'),
(112, 27, 2, '2022-05-10 06:51:53', '2022-05-10 06:51:53');

-- --------------------------------------------------------

--
-- Table structure for table `facility_rules`
--

CREATE TABLE `facility_rules` (
  `id` bigint NOT NULL,
  `facility_id` bigint DEFAULT NULL,
  `rules` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facility_rules`
--

INSERT INTO `facility_rules` (`id`, `facility_id`, `rules`, `created_at`, `updated_at`) VALUES
(1, 26, 'dsd', '2022-04-20 07:28:52', '2022-04-20 07:28:52'),
(10, 27, 'Test 1', '2022-05-10 06:51:53', '2022-05-10 06:51:53');

-- --------------------------------------------------------

--
-- Table structure for table `facility_rules_lang`
--

CREATE TABLE `facility_rules_lang` (
  `id` bigint NOT NULL,
  `facility_rule_id` bigint DEFAULT NULL,
  `rules` text,
  `lang` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facility_rules_lang`
--

INSERT INTO `facility_rules_lang` (`id`, `facility_rule_id`, `rules`, `lang`, `created_at`, `updated_at`) VALUES
(1, 1, 'dsd', 'en', '2022-04-20 07:28:52', '2022-04-20 07:28:52'),
(2, 1, 'sd', 'ar', '2022-04-20 07:28:52', '2022-04-20 07:28:52'),
(19, 10, 'Test 1', 'en', '2022-05-10 06:51:53', '2022-05-10 06:51:53'),
(20, 10, 'Test 1 AR', 'ar', '2022-05-10 06:51:53', '2022-05-10 06:51:53');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `question` text,
  `answer` text,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `type`, `question`, `answer`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Wallet', 'Test1', 'Testing', 1, '2021-09-08 12:38:04', '2022-02-15 12:54:13'),
(2, 'Wallet', 'What I do not have been on my way home from work and I do not have been on?', 'What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?', 1, '2021-09-09 06:09:01', '2021-09-09 06:09:01'),
(3, 'Order', 'dfasd', 'fdas', 1, '2021-09-09 13:23:07', '2021-09-09 13:23:07'),
(4, 'Order', 'The food', 'Food is any substance consumed to provide nutritional support for an organism. Food is usually of plant, animal or fungal origin, and contains essential nutrients, such as carbohydrates, fats, proteins, vitamins, or minerals. The substance is ingested by an organism and assimilated by the organism\'s cells to provide energy, maintain life, or stimulate growth. Different species of animals have different feeding behaviours that satisfy the needs of their unique metabolisms, often evolved to fill a specific ecological niche within specific geographical contexts.', 1, '2021-09-14 05:54:36', '2021-09-14 05:54:36'),
(5, 'Order', 'How do I Cancel the Order,I placed it?', 'Order can be cancelled till the product is not yet dispatched.You would see an option to cancel within \'My Orders\' section, under the main menu of your App/Website then select the items or order you want to cancel.In case you are unable to cancel the order from \'My Orders\' section, you can refuse it at time of delivery and refund will be processed into the source account, if order amount was paid online.', 1, '2022-02-15 13:03:52', '2022-02-15 13:03:52'),
(6, 'Order', 'How do I create the return Request?', 'You can create a return request in three simple steps \r\n1-Tap on My Orders\r\n2-Choose the items to be returned\r\n3-Enter details requested and create return request', 1, '2022-02-15 13:07:13', '2022-02-15 13:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `faq_lang`
--

CREATE TABLE `faq_lang` (
  `id` int NOT NULL,
  `faq_id` int DEFAULT NULL,
  `question` text,
  `answer` text,
  `lang` varchar(10) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `faq_lang`
--

INSERT INTO `faq_lang` (`id`, `faq_id`, `question`, `answer`, `lang`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Test1', 'Testing', 'en', 1, '2021-09-08 12:38:04', '2022-02-15 12:54:13'),
(2, 1, 'Test1', 'Testing', 'ar', 1, '2021-09-08 12:38:04', '2022-02-15 12:54:13'),
(3, 2, 'What I do not have been on my way home from work and I do not have been on?', 'What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?', 'en', 1, '2021-09-09 06:09:01', '2021-09-09 06:09:01'),
(4, 2, 'What I do not have been on my way home from work and I do not have been on?', 'What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?	What I do not have been on my way home from work and I do not have been on?', 'ar', 1, '2021-09-09 06:09:01', '2021-09-09 06:09:01'),
(5, 3, 'dfasd', 'fdas', 'en', 1, '2021-09-09 13:23:07', '2021-09-09 13:23:07'),
(6, 3, 'fdsaf', 'fds', 'ar', 1, '2021-09-09 13:23:07', '2021-09-09 13:23:07'),
(7, 4, 'The food', 'Food is any substance consumed to provide nutritional support for an organism. Food is usually of plant, animal or fungal origin, and contains essential nutrients, such as carbohydrates, fats, proteins, vitamins, or minerals. The substance is ingested by an organism and assimilated by the organism\'s cells to provide energy, maintain life, or stimulate growth. Different species of animals have different feeding behaviours that satisfy the needs of their unique metabolisms, often evolved to fill a specific ecological niche within specific geographical contexts.', 'en', 1, '2021-09-14 05:54:36', '2021-09-14 05:54:36'),
(8, 4, 'the food', 'Food is any substance consumed to provide nutritional support for an organism. Food is usually of plant, animal or fungal origin, and contains essential nutrients, such as carbohydrates, fats, proteins, vitamins, or minerals. The substance is ingested by an organism and assimilated by the organism\'s cells to provide energy, maintain life, or stimulate growth. Different species of animals have different feeding behaviours that satisfy the needs of their unique metabolisms, often evolved to fill a specific ecological niche within specific geographical contexts.', 'ar', 1, '2021-09-14 05:54:36', '2021-09-14 05:54:36'),
(9, 5, 'How do I Cancel the Order,I placed it?', 'Order can be cancelled till the product is not yet dispatched.You would see an option to cancel within \'My Orders\' section, under the main menu of your App/Website then select the items or order you want to cancel.In case you are unable to cancel the order from \'My Orders\' section, you can refuse it at time of delivery and refund will be processed into the source account, if order amount was paid online.', 'en', 1, '2022-02-15 13:03:52', '2022-02-15 13:03:52'),
(10, 5, 'How do I Cancel the Order,I placed it?', 'Order can be cancelled till the product is not yet dispatched.You would see an option to cancel within \'My Orders\' section, under the main menu of your App/Website then select the items or order you want to cancel.In case you are unable to cancel the order from \'My Orders\' section, you can refuse it at time of delivery and refund will be processed into the source account, if order amount was paid online.', 'ar', 1, '2022-02-15 13:03:52', '2022-02-15 13:03:52'),
(11, 6, 'How do I create the return Request?', 'You can create a return request in three simple steps \r\n1-Tap on My Orders\r\n2-Choose the items to be returned\r\n3-Enter details requested and create return request', 'en', 1, '2022-02-15 13:07:13', '2022-02-15 13:07:13'),
(12, 6, 'How do I create the return Request?', 'You can create a return request in three simple steps \r\n1-Tap on My Orders\r\n2-Choose the items to be returned\r\n3-Enter details requested and create return request', 'ar', 1, '2022-02-15 13:07:13', '2022-02-15 13:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `lang` varchar(25) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `language`, `lang`, `created_at`, `updated_at`) VALUES
(1, 'English', 'en', '2020-07-06 08:46:52', '2020-07-06 08:48:34'),
(2, 'Arabic', 'ar', '2020-07-06 08:49:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(161, 'App\\User', 1),
(162, 'App\\User', 1),
(165, 'App\\User', 1),
(148, 'App\\User', 2),
(149, 'App\\User', 2),
(150, 'App\\User', 2),
(151, 'App\\User', 2),
(163, 'App\\User', 2),
(164, 'App\\User', 2),
(165, 'App\\User', 2),
(166, 'App\\User', 2),
(171, 'App\\User', 2),
(172, 'App\\User', 2),
(173, 'App\\User', 2),
(174, 'App\\User', 2),
(148, 'App\\User', 4),
(149, 'App\\User', 4),
(150, 'App\\User', 4),
(151, 'App\\User', 4),
(148, 'App\\User', 46),
(149, 'App\\User', 46),
(150, 'App\\User', 46),
(151, 'App\\User', 46),
(163, 'App\\User', 46),
(164, 'App\\User', 46),
(165, 'App\\User', 46),
(166, 'App\\User', 46),
(171, 'App\\User', 46),
(172, 'App\\User', 46),
(173, 'App\\User', 46),
(174, 'App\\User', 46),
(215, 'App\\User', 46),
(216, 'App\\User', 46),
(217, 'App\\User', 46),
(218, 'App\\User', 46),
(148, 'App\\User', 59),
(149, 'App\\User', 59),
(150, 'App\\User', 59),
(151, 'App\\User', 59),
(163, 'App\\User', 59),
(164, 'App\\User', 59),
(165, 'App\\User', 59),
(166, 'App\\User', 59),
(171, 'App\\User', 59),
(172, 'App\\User', 59),
(173, 'App\\User', 59),
(174, 'App\\User', 59),
(148, 'App\\User', 60),
(149, 'App\\User', 60),
(150, 'App\\User', 60),
(151, 'App\\User', 60),
(163, 'App\\User', 60),
(164, 'App\\User', 60),
(165, 'App\\User', 60),
(166, 'App\\User', 60),
(171, 'App\\User', 60),
(172, 'App\\User', 60),
(173, 'App\\User', 60),
(174, 'App\\User', 60),
(148, 'App\\User', 61),
(149, 'App\\User', 61),
(150, 'App\\User', 61),
(151, 'App\\User', 61),
(163, 'App\\User', 61),
(164, 'App\\User', 61),
(165, 'App\\User', 61),
(166, 'App\\User', 61),
(171, 'App\\User', 61),
(172, 'App\\User', 61),
(173, 'App\\User', 61),
(174, 'App\\User', 61),
(148, 'App\\User', 88),
(149, 'App\\User', 88),
(150, 'App\\User', 88),
(151, 'App\\User', 88),
(163, 'App\\User', 88),
(164, 'App\\User', 88),
(165, 'App\\User', 88),
(166, 'App\\User', 88),
(171, 'App\\User', 88),
(172, 'App\\User', 88),
(173, 'App\\User', 88),
(174, 'App\\User', 88);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\User', 1),
(4, 'App\\User', 2),
(4, 'App\\User', 3);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_type` tinyint DEFAULT '0',
  `notification_type` tinyint DEFAULT '1',
  `notification_for` varchar(50) DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci,
  `user_id` bigint DEFAULT NULL,
  `order_id` bigint DEFAULT NULL,
  `is_read` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `lang` char(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_type`, `notification_type`, `notification_for`, `title`, `message`, `user_id`, `order_id`, `is_read`, `status`, `lang`, `created_at`, `updated_at`) VALUES
(5, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 80, 83, 1, 1, 'en', '2022-05-03 10:11:11', '2022-05-03 10:11:25'),
(6, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 80, 84, 0, 1, 'en', '2022-05-03 10:12:23', '2022-05-03 10:12:23'),
(7, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 101, 101, 1, 1, 'en', '2022-05-03 10:14:01', '2022-05-03 10:15:13'),
(9, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 103, 85, 0, 1, 'en', '2022-05-03 12:37:16', '2022-05-03 12:37:16'),
(10, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 103, 86, 0, 1, 'en', '2022-05-03 12:38:02', '2022-05-03 12:38:02'),
(11, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 103, 86, 0, 1, 'en', '2022-05-03 12:40:06', '2022-05-03 12:40:06'),
(12, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 103, 87, 0, 1, 'en', '2022-05-03 12:55:09', '2022-05-03 12:55:09'),
(13, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 103, 88, 0, 1, 'en', '2022-05-03 13:00:51', '2022-05-03 13:00:51'),
(14, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 103, 89, 0, 1, 'en', '2022-05-03 13:03:17', '2022-05-03 13:03:17'),
(15, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 103, 88, 0, 1, 'en', '2022-05-03 13:03:51', '2022-05-03 13:03:51'),
(16, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 103, 89, 0, 1, 'en', '2022-05-03 13:07:11', '2022-05-03 13:07:11'),
(17, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 103, 85, 0, 1, 'en', '2022-05-03 13:07:14', '2022-05-03 13:07:14'),
(18, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 103, 87, 0, 1, 'en', '2022-05-03 13:07:17', '2022-05-03 13:07:17'),
(19, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 103, 87, 0, 1, 'en', '2022-05-03 13:07:24', '2022-05-03 13:07:24'),
(20, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 103, 87, 0, 1, 'en', '2022-05-03 13:07:48', '2022-05-03 13:07:48'),
(21, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 103, 87, 0, 1, 'en', '2022-05-03 13:08:29', '2022-05-03 13:08:29'),
(22, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 103, 90, 0, 1, 'en', '2022-05-03 13:12:55', '2022-05-03 13:12:55'),
(23, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 103, 90, 0, 1, 'en', '2022-05-03 13:13:02', '2022-05-03 13:13:02'),
(24, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 103, 90, 1, 1, 'en', '2022-05-03 13:13:06', '2022-05-03 13:34:08'),
(25, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 103, 91, 0, 1, 'en', '2022-05-03 13:13:51', '2022-05-03 13:13:51'),
(26, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 103, 92, 0, 1, 'en', '2022-05-03 13:14:05', '2022-05-03 13:14:05'),
(27, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 103, 93, 0, 1, 'en', '2022-05-03 13:14:21', '2022-05-03 13:14:21'),
(28, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 103, 91, 1, 1, 'en', '2022-05-03 13:18:46', '2022-05-03 13:33:44'),
(29, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 103, 92, 0, 1, 'en', '2022-05-03 13:18:50', '2022-05-03 13:18:50'),
(30, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 103, 93, 0, 1, 'en', '2022-05-03 13:18:53', '2022-05-03 13:18:53'),
(31, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 103, 94, 0, 1, 'en', '2022-05-03 13:20:26', '2022-05-03 13:20:26'),
(45, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 104, 104, 0, 1, 'en', '2022-05-05 09:00:10', '2022-05-05 09:00:10'),
(46, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 104, 102, 0, 1, 'en', '2022-05-05 09:01:02', '2022-05-05 09:01:02'),
(47, 3, 3, 'join_challenge', 'Join challenge', 'Join challenge successfully', 104, 99, 0, 1, 'en', '2022-05-05 09:01:19', '2022-05-05 09:01:19'),
(49, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 104, 102, 0, 1, 'en', '2022-05-05 09:01:43', '2022-05-05 09:01:43'),
(50, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 104, 103, 0, 1, 'en', '2022-05-05 09:02:22', '2022-05-05 09:02:22'),
(51, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 104, 103, 0, 1, 'en', '2022-05-05 09:04:26', '2022-05-05 09:04:26'),
(52, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 104, 104, 0, 1, 'en', '2022-05-05 09:05:20', '2022-05-05 09:05:20'),
(53, 3, 3, 'join_challenge', 'Join challenge', 'Join challenge successfully', 104, 84, 0, 1, 'en', '2022-05-05 09:05:50', '2022-05-05 09:05:50'),
(54, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 80, 84, 0, 1, 'en', '2022-05-05 09:05:50', '2022-05-05 09:05:50'),
(55, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 97, 105, 0, 1, 'en', '2022-05-05 09:21:31', '2022-05-05 09:21:31'),
(56, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 97, 106, 0, 1, 'en', '2022-05-05 09:43:07', '2022-05-05 09:43:07'),
(57, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 97, 107, 0, 1, 'en', '2022-05-05 10:09:53', '2022-05-05 10:09:53'),
(58, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 97, 108, 0, 1, 'en', '2022-05-05 10:10:22', '2022-05-05 10:10:22'),
(79, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 105, 105, 0, 1, 'en', '2022-05-05 13:13:08', '2022-05-05 13:13:08'),
(80, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 106, 106, 0, 1, 'en', '2022-05-05 13:32:47', '2022-05-05 13:32:47'),
(81, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 47, 117, 1, 1, 'en', '2022-05-06 11:24:09', '2022-05-11 11:26:58'),
(82, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 47, 100, 1, 1, 'en', '2022-05-06 11:24:21', '2022-05-11 11:26:58'),
(83, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 47, 117, 1, 1, 'en', '2022-05-06 11:24:43', '2022-05-11 11:26:58'),
(84, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 47, 118, 1, 1, 'en', '2022-05-06 11:24:53', '2022-05-11 11:26:58'),
(95, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 97, 122, 0, 1, 'en', '2022-05-06 12:45:38', '2022-05-06 12:45:38'),
(100, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 107, 107, 0, 1, 'en', '2022-05-08 21:41:09', '2022-05-08 21:41:09'),
(101, 3, 3, 'join_challenge', 'Join challenge', 'Join challenge successfully', 97, 126, 0, 1, 'en', '2022-05-09 12:51:02', '2022-05-09 12:51:02'),
(112, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 109, 109, 1, 1, 'en', '2022-05-10 09:44:02', '2022-05-10 09:45:18'),
(113, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 109, 135, 1, 1, 'en', '2022-05-10 09:48:18', '2022-05-10 13:22:32'),
(115, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 109, 137, 1, 1, 'en', '2022-05-10 09:48:54', '2022-05-10 13:22:08'),
(123, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 109, 143, 1, 1, 'en', '2022-05-11 06:42:43', '2022-05-11 06:42:49'),
(124, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 110, 110, 1, 1, 'en', '2022-05-11 10:57:55', '2022-05-11 10:59:42'),
(125, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 47, 144, 1, 1, 'en', '2022-05-11 11:07:24', '2022-05-11 11:26:58'),
(128, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 111, 111, 1, 1, 'en', '2022-05-11 11:14:45', '2022-05-11 11:33:19'),
(129, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 111, 147, 1, 1, 'en', '2022-05-11 11:15:20', '2022-05-11 11:33:19'),
(130, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 111, 148, 1, 1, 'en', '2022-05-11 11:16:12', '2022-05-11 11:33:19'),
(131, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 111, 149, 1, 1, 'en', '2022-05-11 11:17:05', '2022-05-11 11:33:19'),
(132, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 111, 150, 1, 1, 'en', '2022-05-11 11:18:01', '2022-05-11 11:33:19'),
(133, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 111, 151, 1, 1, 'en', '2022-05-11 11:19:29', '2022-05-11 11:33:19'),
(134, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 111, 152, 1, 1, 'en', '2022-05-11 11:20:37', '2022-05-11 11:33:19'),
(135, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 111, 147, 1, 1, 'en', '2022-05-11 11:24:46', '2022-05-11 11:33:19'),
(136, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 111, 150, 1, 1, 'en', '2022-05-11 11:25:16', '2022-05-11 11:33:19'),
(137, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 111, 153, 1, 1, 'en', '2022-05-11 11:31:40', '2022-05-11 11:33:19'),
(138, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 111, 148, 1, 1, 'en', '2022-05-11 11:36:45', '2022-05-11 11:38:53'),
(140, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 47, 155, 1, 1, 'en', '2022-05-12 11:14:21', '2022-05-12 11:28:58'),
(147, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 97, 161, 0, 1, 'en', '2022-05-17 11:41:38', '2022-05-17 11:41:38'),
(148, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 97, 162, 0, 1, 'en', '2022-05-17 13:32:16', '2022-05-17 13:32:16'),
(154, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 114, 114, 1, 1, 'en', '2022-05-23 10:15:43', '2022-05-23 10:31:38'),
(155, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 114, 166, 0, 1, 'en', '2022-05-23 12:14:27', '2022-05-23 12:14:27'),
(156, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 114, 167, 0, 1, 'en', '2022-05-23 12:15:26', '2022-05-23 12:15:26'),
(157, 3, 3, 'join_challenge', 'Join challenge', 'Join challenge successfully', 114, 129, 0, 1, 'en', '2022-05-23 13:27:43', '2022-05-23 13:27:43'),
(159, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 115, 115, 0, 1, '', '2022-05-24 14:42:03', '2022-05-24 14:42:03'),
(160, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 116, 116, 0, 1, '', '2022-05-25 05:32:59', '2022-05-25 05:32:59'),
(161, 3, 3, 'join_challenge', 'Join challenge', 'Join challenge successfully', 116, 135, 0, 1, '', '2022-05-25 05:33:33', '2022-05-25 05:33:33'),
(162, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 109, 135, 0, 1, '', '2022-05-25 05:33:33', '2022-05-25 05:33:33'),
(163, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 116, 168, 0, 1, '', '2022-05-25 05:36:03', '2022-05-25 05:36:03'),
(165, 3, 3, 'accepted_challenge', 'قبول التحدي', 'تم قبول التحدي الخاص بك', 109, 136, 0, 1, '', '2022-05-25 06:06:06', '2022-05-25 06:06:06'),
(167, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 97, 106, 0, 1, '', '2022-05-25 06:08:54', '2022-05-25 06:08:54'),
(169, 3, 3, 'accepted_challenge', 'قبول التحدي', 'تم قبول التحدي الخاص بك', 97, 107, 0, 1, '', '2022-05-25 06:11:06', '2022-05-25 06:11:06'),
(172, 3, 3, 'accepted_challenge', 'قبول التحدي', 'تم قبول التحدي الخاص بك', 91, 158, 0, 1, 'ar', '2022-05-25 06:16:58', '2022-05-25 06:16:58'),
(173, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 91, 158, 0, 1, 'en', '2022-05-25 06:16:58', '2022-05-25 06:16:58'),
(176, 3, 3, 'accepted_challenge', 'قبول التحدي', 'تم قبول التحدي الخاص بك', 97, 161, 0, 1, 'ar', '2022-05-25 06:18:16', '2022-05-25 06:18:16'),
(177, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 97, 161, 0, 1, 'en', '2022-05-25 06:18:16', '2022-05-25 06:18:16'),
(180, 3, 3, 'accepted_challenge', 'قبول التحدي', 'تم قبول التحدي الخاص بك', 97, 108, 1, 1, 'ar', '2022-05-25 06:59:52', '2022-05-25 10:33:58'),
(181, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 97, 108, 0, 1, 'en', '2022-05-25 06:59:53', '2022-05-25 06:59:53'),
(184, 3, 3, 'accepted_challenge', 'قبول التحدي', 'تم قبول التحدي الخاص بك', 91, 160, 0, 1, 'ar', '2022-05-25 07:00:20', '2022-05-25 07:00:20'),
(185, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 91, 160, 0, 1, 'en', '2022-05-25 07:00:20', '2022-05-25 07:00:20'),
(190, 3, 3, 'join_challenge', 'الانضمام إلى التحدي', 'انضم إلى التحدي بنجاح', 87, 166, 1, 1, 'ar', '2022-05-25 07:01:38', '2022-05-27 10:16:06'),
(191, 3, 3, 'join_challenge', 'Join challenge', 'Join challenge successfully', 87, 166, 1, 1, 'en', '2022-05-25 07:01:38', '2022-05-27 10:16:06'),
(192, 3, 3, 'accepted_challenge', 'قبول التحدي', 'تم قبول التحدي الخاص بك', 114, 166, 0, 1, 'ar', '2022-05-25 07:01:44', '2022-05-25 07:01:44'),
(193, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 114, 166, 0, 1, 'en', '2022-05-25 07:01:44', '2022-05-25 07:01:44'),
(194, 3, 3, 'join_challenge', 'الانضمام إلى التحدي', 'انضم إلى التحدي بنجاح', 87, 168, 1, 1, 'ar', '2022-05-25 07:02:17', '2022-05-27 10:16:06'),
(195, 3, 3, 'join_challenge', 'Join challenge', 'Join challenge successfully', 87, 168, 1, 1, 'en', '2022-05-25 07:02:17', '2022-05-27 10:16:06'),
(196, 3, 3, 'accepted_challenge', 'قبول التحدي', 'تم قبول التحدي الخاص بك', 116, 168, 0, 1, 'ar', '2022-05-25 07:02:17', '2022-05-25 07:02:17'),
(197, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 116, 168, 0, 1, 'en', '2022-05-25 07:02:17', '2022-05-25 07:02:17'),
(198, 3, 3, 'join_challenge', 'الانضمام إلى التحدي', 'انضم إلى التحدي بنجاح', 87, 167, 1, 1, 'ar', '2022-05-25 07:02:58', '2022-05-27 10:16:06'),
(199, 3, 3, 'join_challenge', 'Join challenge', 'Join challenge successfully', 87, 167, 1, 1, 'en', '2022-05-25 07:02:58', '2022-05-27 10:16:06'),
(200, 3, 3, 'accepted_challenge', 'قبول التحدي', 'تم قبول التحدي الخاص بك', 114, 167, 0, 1, 'ar', '2022-05-25 07:02:58', '2022-05-25 07:02:58'),
(201, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 114, 167, 0, 1, 'en', '2022-05-25 07:02:58', '2022-05-25 07:02:58'),
(202, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 87, 169, 1, 1, 'ar', '2022-05-25 07:21:09', '2022-05-27 10:16:06'),
(203, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 87, 169, 1, 1, 'en', '2022-05-25 07:21:09', '2022-05-27 10:16:06'),
(204, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 115, 170, 0, 1, 'ar', '2022-05-25 08:45:43', '2022-05-25 08:45:43'),
(205, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 115, 170, 0, 1, 'en', '2022-05-25 08:45:43', '2022-05-25 08:45:43'),
(206, 3, 3, 'create_user', 'أهلا وسهلا بك في تحديات', 'لقد تم تسجيلك بنجاح في تحديات', 117, 117, 0, 1, 'ar', '2022-05-25 09:29:11', '2022-05-25 09:29:11'),
(207, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 117, 117, 0, 1, 'en', '2022-05-25 09:29:11', '2022-05-25 09:29:11'),
(208, 3, 3, 'create_user', 'أهلا وسهلا بك في تحديات', 'لقد تم تسجيلك بنجاح في تحديات', 118, 118, 0, 1, 'ar', '2022-05-25 10:32:45', '2022-05-25 10:32:45'),
(209, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 118, 118, 0, 1, 'en', '2022-05-25 10:32:45', '2022-05-25 10:32:45'),
(212, 3, 3, 'create_user', 'أهلا وسهلا بك في تحديات', 'لقد تم تسجيلك بنجاح في تحديات', 119, 119, 0, 1, 'ar', '2022-05-26 03:30:26', '2022-05-26 03:30:26'),
(213, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 119, 119, 0, 1, 'en', '2022-05-26 03:30:26', '2022-05-26 03:30:26'),
(214, 3, 3, 'create_user', 'أهلا وسهلا بك في تحديات', 'لقد تم تسجيلك بنجاح في تحديات', 120, 120, 0, 1, 'ar', '2022-05-26 04:41:08', '2022-05-26 04:41:08'),
(215, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 120, 120, 0, 1, 'en', '2022-05-26 04:41:08', '2022-05-26 04:41:08'),
(218, 3, 3, 'accepted_challenge', 'قبول التحدي', 'تم قبول التحدي الخاص بك', 87, 138, 1, 1, 'ar', '2022-05-26 04:56:30', '2022-05-27 10:16:06'),
(219, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 87, 138, 1, 1, 'en', '2022-05-26 04:56:30', '2022-05-27 10:16:06'),
(228, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 102, 175, 0, 1, 'ar', '2022-05-26 12:51:03', '2022-05-26 12:51:03'),
(229, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 102, 175, 1, 1, 'en', '2022-05-26 12:51:03', '2022-05-26 12:52:25'),
(230, 3, 3, 'booking_cancel', 'إلغاء الحجز', 'تم إلغاء الحجز بنجاح', 102, 175, 0, 1, 'ar', '2022-05-26 12:52:11', '2022-05-26 12:52:11'),
(231, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 102, 175, 1, 1, 'en', '2022-05-26 12:52:11', '2022-05-26 12:52:29'),
(246, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 47, 176, 0, 1, 'ar', '2022-05-27 07:26:10', '2022-05-27 07:26:10'),
(247, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 47, 176, 0, 1, 'en', '2022-05-27 07:26:10', '2022-05-27 07:26:10'),
(251, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 87, 177, 1, 1, 'en', '2022-05-27 09:03:30', '2022-05-27 10:16:06'),
(252, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 87, 178, 1, 1, 'ar', '2022-05-27 09:06:29', '2022-05-27 10:16:06'),
(253, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 87, 178, 1, 1, 'en', '2022-05-27 09:06:29', '2022-05-27 10:16:06'),
(254, 3, 3, 'booking_cancel', 'إلغاء الحجز', 'تم إلغاء الحجز بنجاح', 87, 178, 1, 1, 'ar', '2022-05-27 09:06:59', '2022-05-27 10:16:06'),
(255, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 87, 178, 1, 1, 'en', '2022-05-27 09:06:59', '2022-05-27 10:16:06'),
(256, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 87, 179, 1, 1, 'ar', '2022-05-27 09:07:14', '2022-05-27 10:16:06'),
(257, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 87, 179, 1, 1, 'en', '2022-05-27 09:07:14', '2022-05-27 10:16:06'),
(260, 3, 3, 'invite_player', 'دعوة لاعب', 'أنت تدعو للتحدي', 87, 179, 1, 1, 'ar', '2022-05-27 09:11:52', '2022-05-27 10:16:06'),
(261, 3, 3, 'invite_player', 'Invite Player', 'You Invite for challenge', 87, 179, 1, 1, 'en', '2022-05-27 09:11:52', '2022-05-27 10:16:06'),
(262, 3, 3, 'invite_player', 'دعوة لاعب', 'أنت تدعو للتحدي', 109, 179, 0, 1, 'ar', '2022-05-27 09:12:39', '2022-05-27 09:12:39'),
(263, 3, 3, 'invite_player', 'Invite Player', 'You Invite for challenge', 109, 179, 0, 1, 'en', '2022-05-27 09:12:39', '2022-05-27 09:12:39'),
(268, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 87, 180, 1, 1, 'ar', '2022-05-27 10:04:28', '2022-05-27 10:16:06'),
(269, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 87, 180, 1, 1, 'en', '2022-05-27 10:04:28', '2022-05-27 10:16:06'),
(270, 3, 3, 'invite_player', 'دعوة لاعب', 'أنت تدعو للتحدي', 41, 180, 0, 1, 'ar', '2022-05-27 10:05:36', '2022-05-27 10:05:36'),
(271, 3, 3, 'invite_player', 'Invite Player', 'You Invite for challenge', 41, 180, 1, 1, 'en', '2022-05-27 10:05:36', '2022-05-27 13:40:20'),
(272, 3, 3, 'invite_player', 'دعوة لاعب', 'أنت تدعو للتحدي', 41, 180, 0, 1, 'ar', '2022-05-27 10:06:22', '2022-05-27 10:06:22'),
(273, 3, 3, 'invite_player', 'Invite Player', 'You Invite for challenge', 41, 180, 1, 1, 'en', '2022-05-27 10:06:22', '2022-05-27 10:09:02'),
(274, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 87, 181, 0, 1, 'ar', '2022-05-30 05:48:14', '2022-05-30 05:48:14'),
(275, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 87, 181, 1, 1, 'en', '2022-05-30 05:48:14', '2022-05-30 05:52:37'),
(276, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 87, 182, 0, 1, 'ar', '2022-05-30 05:49:19', '2022-05-30 05:49:19'),
(277, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 87, 182, 1, 1, 'en', '2022-05-30 05:49:19', '2022-05-30 05:52:35'),
(279, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 87, 183, 1, 1, 'en', '2022-05-30 05:50:21', '2022-05-30 05:52:32'),
(280, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 87, 184, 0, 1, 'ar', '2022-05-30 05:57:11', '2022-05-30 05:57:11'),
(281, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 87, 184, 1, 1, 'en', '2022-05-30 05:57:11', '2022-05-30 06:04:49'),
(288, 3, 3, 'accepted_challenge', 'قبول التحدي', 'تم قبول التحدي الخاص بك', 87, 181, 0, 1, 'ar', '2022-05-30 06:02:41', '2022-05-30 06:02:41'),
(289, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 87, 181, 1, 1, 'en', '2022-05-30 06:02:41', '2022-05-30 06:02:56'),
(292, 3, 3, 'accepted_challenge', 'قبول التحدي', 'تم قبول التحدي الخاص بك', 87, 182, 0, 1, 'ar', '2022-05-30 06:03:48', '2022-05-30 06:03:48'),
(293, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 87, 182, 1, 1, 'en', '2022-05-30 06:03:48', '2022-05-30 06:03:54'),
(294, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 87, 186, 0, 1, 'ar', '2022-05-30 06:13:31', '2022-05-30 06:13:31'),
(295, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 87, 186, 1, 1, 'en', '2022-05-30 06:13:31', '2022-05-30 08:41:21'),
(296, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 47, 187, 0, 1, 'ar', '2022-05-30 09:07:47', '2022-05-30 09:07:47'),
(297, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 47, 187, 0, 1, 'en', '2022-05-30 09:07:47', '2022-05-30 09:07:47'),
(298, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 47, 188, 0, 1, 'ar', '2022-05-30 09:34:51', '2022-05-30 09:34:51'),
(299, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 47, 188, 0, 1, 'en', '2022-05-30 09:34:51', '2022-05-30 09:34:51'),
(300, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 47, 189, 0, 1, 'ar', '2022-05-30 09:35:09', '2022-05-30 09:35:09'),
(301, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 47, 189, 0, 1, 'en', '2022-05-30 09:35:09', '2022-05-30 09:35:09'),
(302, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 87, 190, 0, 1, 'ar', '2022-05-30 09:37:28', '2022-05-30 09:37:28'),
(303, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 87, 190, 1, 1, 'en', '2022-05-30 09:37:28', '2022-05-30 09:37:37'),
(304, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 49, 191, 0, 1, 'ar', '2022-05-30 10:38:01', '2022-05-30 10:38:01'),
(305, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 49, 191, 0, 1, 'en', '2022-05-30 10:38:01', '2022-05-30 10:38:01'),
(306, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 49, 192, 0, 1, 'ar', '2022-05-30 10:38:20', '2022-05-30 10:38:20'),
(307, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 49, 192, 0, 1, 'en', '2022-05-30 10:38:20', '2022-05-30 10:38:20'),
(308, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 87, 193, 0, 1, 'ar', '2022-05-30 10:39:00', '2022-05-30 10:39:00'),
(309, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 87, 193, 0, 1, 'en', '2022-05-30 10:39:00', '2022-05-30 10:39:00'),
(310, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 49, 194, 0, 1, 'ar', '2022-05-30 10:39:34', '2022-05-30 10:39:34'),
(311, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 49, 194, 0, 1, 'en', '2022-05-30 10:39:34', '2022-05-30 10:39:34'),
(312, 3, 3, 'invite_player', 'دعوة لاعب', 'أنت تدعو للتحدي', 71, 192, 0, 1, 'ar', '2022-05-30 10:42:46', '2022-05-30 10:42:46'),
(313, 3, 3, 'invite_player', 'Invite Player', 'You Invite for challenge', 71, 192, 0, 1, 'en', '2022-05-30 10:42:46', '2022-05-30 10:42:46'),
(314, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 87, 195, 0, 1, 'ar', '2022-05-30 10:48:20', '2022-05-30 10:48:20'),
(315, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 87, 195, 0, 1, 'en', '2022-05-30 10:48:20', '2022-05-30 10:48:20'),
(316, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 87, 196, 0, 1, 'ar', '2022-05-30 10:48:41', '2022-05-30 10:48:41'),
(317, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 87, 196, 1, 1, 'en', '2022-05-30 10:48:41', '2022-05-30 11:08:51'),
(318, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 87, 197, 0, 1, 'ar', '2022-05-30 10:49:02', '2022-05-30 10:49:02'),
(319, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 87, 197, 0, 1, 'en', '2022-05-30 10:49:02', '2022-05-30 10:49:02'),
(320, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 87, 198, 0, 1, 'ar', '2022-05-30 10:49:42', '2022-05-30 10:49:42'),
(321, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 87, 198, 0, 1, 'en', '2022-05-30 10:49:42', '2022-05-30 10:49:42'),
(322, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 87, 199, 0, 1, 'ar', '2022-05-30 10:51:08', '2022-05-30 10:51:08'),
(323, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 87, 199, 1, 1, 'en', '2022-05-30 10:51:08', '2022-05-30 11:08:39'),
(324, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 47, 200, 0, 1, 'ar', '2022-05-30 10:57:43', '2022-05-30 10:57:43'),
(325, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 47, 200, 0, 1, 'en', '2022-05-30 10:57:43', '2022-05-30 10:57:43'),
(326, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 47, 201, 0, 1, 'ar', '2022-05-30 11:02:46', '2022-05-30 11:02:46'),
(327, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 47, 201, 0, 1, 'en', '2022-05-30 11:02:46', '2022-05-30 11:02:46'),
(328, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 47, 202, 0, 1, 'ar', '2022-05-30 11:06:05', '2022-05-30 11:06:05'),
(329, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 47, 202, 0, 1, 'en', '2022-05-30 11:06:05', '2022-05-30 11:06:05'),
(330, 3, 3, 'booking_cancel', 'إلغاء الحجز', 'تم إلغاء الحجز بنجاح', 87, 198, 0, 1, 'ar', '2022-05-30 11:08:15', '2022-05-30 11:08:15'),
(331, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 87, 198, 1, 1, 'en', '2022-05-30 11:08:15', '2022-05-30 11:08:33'),
(332, 3, 3, 'create_user', 'أهلا وسهلا بك في تحديات', 'لقد تم تسجيلك بنجاح في تحديات', 121, 121, 0, 1, 'ar', '2022-05-30 11:10:24', '2022-05-30 11:10:24'),
(333, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 121, 121, 1, 1, 'en', '2022-05-30 11:10:24', '2022-05-30 11:10:32'),
(334, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 121, 203, 0, 1, 'ar', '2022-05-30 11:10:54', '2022-05-30 11:10:54'),
(335, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 121, 203, 0, 1, 'en', '2022-05-30 11:10:54', '2022-05-30 11:10:54'),
(336, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 121, 204, 0, 1, 'ar', '2022-05-30 11:11:18', '2022-05-30 11:11:18'),
(337, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 121, 204, 0, 1, 'en', '2022-05-30 11:11:18', '2022-05-30 11:11:18'),
(338, 3, 3, 'booking_cancel', 'إلغاء الحجز', 'تم إلغاء الحجز بنجاح', 121, 204, 0, 1, 'ar', '2022-05-30 11:12:00', '2022-05-30 11:12:00'),
(339, 3, 3, 'booking_cancel', 'Booking Cancel', 'Booking cancelled successfully', 121, 204, 0, 1, 'en', '2022-05-30 11:12:00', '2022-05-30 11:12:00'),
(340, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 121, 205, 0, 1, 'ar', '2022-05-30 11:12:27', '2022-05-30 11:12:27'),
(341, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 121, 205, 0, 1, 'en', '2022-05-30 11:12:27', '2022-05-30 11:12:27'),
(342, 3, 3, 'create_user', 'أهلا وسهلا بك في تحديات', 'لقد تم تسجيلك بنجاح في تحديات', 122, 122, 0, 1, 'ar', '2022-05-30 11:14:01', '2022-05-30 11:14:01'),
(343, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 122, 122, 0, 1, 'en', '2022-05-30 11:14:01', '2022-05-30 11:14:01'),
(344, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 122, 206, 0, 1, 'ar', '2022-05-30 11:14:15', '2022-05-30 11:14:15'),
(345, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 122, 206, 0, 1, 'en', '2022-05-30 11:14:15', '2022-05-30 11:14:15'),
(346, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 122, 207, 0, 1, 'ar', '2022-05-30 11:15:17', '2022-05-30 11:15:17'),
(347, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 122, 207, 0, 1, 'en', '2022-05-30 11:15:17', '2022-05-30 11:15:17'),
(348, 3, 3, 'create_user', 'أهلا وسهلا بك في تحديات', 'لقد تم تسجيلك بنجاح في تحديات', 123, 123, 0, 1, 'ar', '2022-05-30 11:18:33', '2022-05-30 11:18:33'),
(349, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 123, 123, 0, 1, 'en', '2022-05-30 11:18:33', '2022-05-30 11:18:33'),
(350, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 123, 208, 0, 1, 'ar', '2022-05-30 11:18:55', '2022-05-30 11:18:55'),
(351, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 123, 208, 0, 1, 'en', '2022-05-30 11:18:55', '2022-05-30 11:18:55'),
(352, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 123, 209, 0, 1, 'ar', '2022-05-30 11:19:24', '2022-05-30 11:19:24'),
(353, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 123, 209, 0, 1, 'en', '2022-05-30 11:19:24', '2022-05-30 11:19:24'),
(354, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 123, 210, 0, 1, 'ar', '2022-05-30 11:20:27', '2022-05-30 11:20:27'),
(355, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 123, 210, 0, 1, 'en', '2022-05-30 11:20:27', '2022-05-30 11:20:27'),
(356, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 87, 211, 0, 1, 'ar', '2022-05-30 11:41:00', '2022-05-30 11:41:00'),
(357, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 87, 211, 0, 1, 'en', '2022-05-30 11:41:00', '2022-05-30 11:41:00'),
(358, 3, 3, 'create_user', 'أهلا وسهلا بك في تحديات', 'لقد تم تسجيلك بنجاح في تحديات', 124, 124, 0, 1, 'ar', '2022-05-30 11:42:24', '2022-05-30 11:42:24'),
(359, 3, 3, 'create_user', 'Welcome on Tahadiyaat', 'You are successfully registered on Tahadiyaat', 124, 124, 1, 1, 'en', '2022-05-30 11:42:24', '2022-05-30 11:45:41'),
(360, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 124, 212, 0, 1, 'ar', '2022-05-30 11:42:44', '2022-05-30 11:42:44'),
(361, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 124, 212, 1, 1, 'en', '2022-05-30 11:42:44', '2022-05-30 11:45:42'),
(362, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 124, 213, 0, 1, 'ar', '2022-05-30 11:43:14', '2022-05-30 11:43:14'),
(363, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 124, 213, 0, 1, 'en', '2022-05-30 11:43:14', '2022-05-30 11:43:14'),
(364, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 124, 214, 0, 1, '', '2022-05-30 11:44:30', '2022-05-30 11:44:30'),
(365, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 87, 215, 0, 1, 'ar', '2022-05-30 11:45:19', '2022-05-30 11:45:19'),
(366, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 87, 215, 0, 1, 'en', '2022-05-30 11:45:19', '2022-05-30 11:45:19'),
(367, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 87, 216, 0, 1, 'ar', '2022-05-30 11:46:02', '2022-05-30 11:46:02'),
(368, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 87, 216, 0, 1, 'en', '2022-05-30 11:46:02', '2022-05-30 11:46:02'),
(369, 3, 3, 'join_challenge', 'الانضمام إلى التحدي', 'انضم إلى التحدي بنجاح', 97, 214, 0, 1, 'ar', '2022-05-30 11:55:40', '2022-05-30 11:55:40'),
(370, 3, 3, 'join_challenge', 'Join challenge', 'Join challenge successfully', 97, 214, 0, 1, 'en', '2022-05-30 11:55:40', '2022-05-30 11:55:40'),
(371, 3, 3, 'accepted_challenge', 'قبول التحدي', 'تم قبول التحدي الخاص بك', 124, 214, 0, 1, 'ar', '2022-05-30 11:55:40', '2022-05-30 11:55:40'),
(372, 3, 3, 'accepted_challenge', 'Accepted challenge', 'Your challenge Accepted', 124, 214, 0, 1, 'en', '2022-05-30 11:55:40', '2022-05-30 11:55:40'),
(373, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 124, 217, 0, 1, 'ar', '2022-05-30 12:00:47', '2022-05-30 12:00:47'),
(374, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 124, 217, 0, 1, 'en', '2022-05-30 12:00:47', '2022-05-30 12:00:47'),
(375, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 124, 218, 0, 1, 'ar', '2022-05-30 12:01:13', '2022-05-30 12:01:13'),
(376, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 124, 218, 0, 1, 'en', '2022-05-30 12:01:13', '2022-05-30 12:01:13'),
(377, 3, 3, 'create_challenge', 'خلق التحدي', 'تم إنشاء التحدي بنجاح', 124, 219, 0, 1, 'ar', '2022-05-30 12:02:22', '2022-05-30 12:02:22'),
(378, 3, 3, 'create_challenge', 'Create challenge', 'Challenge created Successfully', 124, 219, 0, 1, 'en', '2022-05-30 12:02:22', '2022-05-30 12:02:22'),
(379, 3, 3, 'book_court', 'كتاب المحكمة', 'تم الحجز للمحكمة بنجاح', 87, 220, 0, 1, 'ar', '2022-05-30 12:13:11', '2022-05-30 12:13:11'),
(380, 3, 3, 'book_court', 'Court Book', 'Court booking Successfully', 87, 220, 0, 1, 'en', '2022-05-30 12:13:11', '2022-05-30 12:13:11');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint NOT NULL,
  `court_id` int DEFAULT NULL,
  `facility_id` varchar(255) DEFAULT NULL,
  `booking_datetime` datetime DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_country_code` varchar(10) DEFAULT NULL,
  `user_phone_number` varchar(50) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `discount_amount` float(10,2) DEFAULT NULL,
  `tax_amount` float(10,2) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `admin_amount` float(10,2) DEFAULT NULL,
  `admin_commission` float(10,2) DEFAULT NULL,
  `order_status` enum('Pending','Accepted','Prepare','Deliver','Cancel','Complete') NOT NULL DEFAULT 'Pending',
  `payment_type` enum('Cash','Card','Net-Banking','Spilt-Bill','Pay-By-Other','Wallet') DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `court_id`, `facility_id`, `booking_datetime`, `user_id`, `user_name`, `user_country_code`, `user_phone_number`, `user_email`, `discount_amount`, `tax_amount`, `amount`, `admin_amount`, `admin_commission`, `order_status`, `payment_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 'Facility 1', '2022-03-08 10:51:05', 1, 'Test User', '91', '78465978456', 'admin@yopmail.com', NULL, NULL, 3000.00, 500.00, 10.00, 'Pending', 'Cash', 1, '2022-03-08 15:22:31', '2022-03-10 09:36:27'),
(2, 3, 'Facility 3', '2022-03-10 10:51:05', 2, 'New User', '91', '78465978456', 'newuser@yopmail.com', NULL, NULL, 5000.00, 500.00, 10.00, 'Pending', 'Card', 1, '2022-03-10 15:22:31', '2022-03-10 09:36:27'),
(3, 2, 'Facility 2', '2022-03-10 11:58:48', 2, 'New User', '91', '78465978456', 'newuser@yopmail.com', NULL, NULL, 2000.00, 200.00, 10.00, 'Pending', 'Cash', 1, '2022-03-10 15:22:31', '2022-03-10 09:36:27'),
(4, 4, 'Facility 2', '2022-03-11 11:58:48', 2, 'New User', '91', '78465978456', 'newuser@yopmail.com', NULL, NULL, 1000.00, 100.00, 10.00, 'Accepted', 'Cash', 1, '2022-03-11 15:22:31', '2022-03-15 15:07:00'),
(5, 5, 'Facility 2', '2022-03-09 11:58:48', 2, 'New User', '91', '78465978456', 'newuser@yopmail.com', NULL, NULL, 1000.00, 100.00, 10.00, 'Pending', 'Cash', 1, '2022-03-09 15:22:31', '2022-03-10 09:36:27'),
(6, 5, 'Facility 1', '2022-03-11 11:58:48', 2, 'New User', '91', '78465978456', 'newuser@yopmail.com', NULL, NULL, 1000.00, 100.00, 10.00, 'Pending', 'Cash', 1, '2022-03-11 15:22:31', '2022-03-10 09:36:27'),
(7, 2, 'Facility 2', '2022-03-11 11:58:48', 2, 'New User', '91', '78465978456', 'newuser@yopmail.com', NULL, NULL, 1000.00, 100.00, 10.00, 'Pending', 'Cash', 1, '2022-03-11 15:22:31', '2022-03-10 09:36:27'),
(8, 3, 'Facility 3', '2022-03-11 11:58:48', 2, 'New User', '91', '78465978456', 'newuser@yopmail.com', NULL, NULL, 1000.00, 100.00, 10.00, 'Pending', 'Cash', 1, '2022-03-11 15:22:31', '2022-03-10 09:36:27'),
(9, 2, 'Facility 2', '2022-03-08 10:51:05', 1, 'Test User', '91', '78465978456', 'admin@yopmail.com', NULL, NULL, 5000.00, 500.00, 10.00, 'Pending', 'Cash', 1, '2022-03-08 15:22:31', '2022-03-10 09:36:27'),
(10, 3, 'Facility 3', '2022-03-08 10:51:05', 1, 'Test User', '91', '78465978456', 'admin@yopmail.com', NULL, NULL, 4000.00, 400.00, 10.00, 'Pending', 'Cash', 1, '2022-03-08 15:22:31', '2022-03-10 09:36:27'),
(11, 3, 'Facility 3', '2022-03-06 10:51:05', 1, 'Test User', '91', '78465978456', 'admin@yopmail.com', NULL, NULL, 6000.00, 600.00, 10.00, 'Pending', 'Cash', 1, '2022-03-06 15:22:31', '2022-03-10 09:36:27');

-- --------------------------------------------------------

--
-- Table structure for table `panel_notifications`
--

CREATE TABLE `panel_notifications` (
  `id` int NOT NULL,
  `user_type` tinyint DEFAULT '0',
  `notification_type` tinyint DEFAULT '1',
  `notification_for` varchar(50) DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_general_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci,
  `user_id` bigint DEFAULT NULL,
  `order_id` bigint DEFAULT NULL,
  `parent_cart_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `is_read` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `panel_notifications`
--

INSERT INTO `panel_notifications` (`id`, `user_type`, `notification_type`, `notification_for`, `title`, `message`, `user_id`, `order_id`, `parent_cart_id`, `product_id`, `is_read`, `status`, `created_at`, `updated_at`) VALUES
(1, 0, 3, 'FAQ-Request', 'FAQ-Request', 'New FAQ Request From User Tester<br/> Question: Ydksrsjarusrsesrsrjsru', 724, NULL, NULL, NULL, 1, 1, '2022-01-05 06:49:12', '2022-01-17 09:31:57');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('sandeep@yahoo.com', '$2y$10$baRBDLGte961uTfQffXQDurwQAKS/EBT17mDy8qENfrUfDVNecySq', '2022-03-29 08:50:37'),
('admin@yopmail.com', '$2y$10$l7BiIm3wyC2OoIxFnMqN2O3F2dfoR4ltB9BRvxP92xrDADoa6ioOy', '2022-04-05 06:12:11'),
('mukeshmalik@gmail.com', 'P3ueqjHnZQQ4Z53ASTV1PcPttMiVXhKTRRCAJqqtiqqPCc8oIoOKO27d9Xr5', '2022-04-05 08:40:38'),
('facilityowner@mailinator.com', 'BlSogZuCzG6aCvLVZgiW9MqHO2vUeyfVI74j7doMHHEdjde9TlfpPnJBdaWr', '2022-04-09 11:29:08'),
('makkmalik@gmail.com', 't3JcsorO71xVLhn9tdLWvkRVRJcshgB4uoFVo2JrIQMwdn78bILWQ8Eruwuw', '2022-04-11 08:57:14'),
('mukeshmalik0@gmail.com', '$2y$10$jcVfeGLkcNXcmUHOAzfqweqWte3To91JklynRlecwsqbknSzus.au', '2022-04-11 08:58:10'),
('testing@ymail.com', 'eCekRY7cqvaSKhkHOhlRnaLv73GF58uef5ieOJG8M90TFsR3v8HbvRQugfCk', '2022-04-18 09:30:09');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `is_show` enum('Yes','No') CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Yes',
  `position` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `is_show`, `position`, `created_at`, `updated_at`) VALUES
(136, 'Facility_owner-create', 'web', 'Yes', 33, '2020-03-11 01:16:01', '2020-03-11 01:16:01'),
(137, 'Facility_owner-delete', 'web', 'Yes', 33, '2020-03-11 01:16:30', '2020-03-11 01:16:30'),
(138, 'Facility_owner-edit', 'web', 'Yes', 33, '2020-03-11 01:17:13', '2020-03-11 01:17:13'),
(139, 'Facility_owner-section', 'web', 'Yes', 33, '2020-03-11 01:17:24', '2020-03-11 01:17:24'),
(148, 'Court-section', 'web', 'Yes', 34, '2022-03-14 11:02:30', '2022-03-14 11:02:30'),
(149, 'Court-create', 'web', 'Yes', 34, '2022-03-14 11:02:30', '2022-03-14 11:02:30'),
(150, 'Court-edit', 'web', 'Yes', 34, '2022-03-14 11:05:46', '2022-03-14 11:05:46'),
(151, 'Court-delete', 'web', 'Yes', 34, '2022-03-14 11:05:46', '2022-03-14 11:05:46'),
(160, 'Permission-section', 'web', 'Yes', 34, '2022-03-14 11:02:30', '2022-03-14 11:02:30'),
(161, 'Permission-role', 'web', 'Yes', 34, '2022-03-14 11:05:46', '2022-03-14 11:05:46'),
(162, 'Permission-user', 'web', 'Yes', 34, '2022-03-14 11:05:46', '2022-03-14 11:05:46'),
(163, 'Order-section', 'web', 'Yes', 35, '2022-03-14 11:02:30', '2022-03-14 11:02:30'),
(164, 'Order-create', 'web', 'Yes', 35, '2022-03-14 11:02:30', '2022-03-14 11:02:30'),
(165, 'Order-edit', 'web', 'Yes', 35, '2022-03-14 11:05:46', '2022-03-14 11:05:46'),
(166, 'Order-delete', 'web', 'Yes', 35, '2022-03-14 11:05:46', '2022-03-14 11:05:46'),
(167, 'Player-section', 'web', 'Yes', 36, '2022-03-14 16:32:30', '2022-03-14 16:32:30'),
(168, 'Player-create', 'web', 'Yes', 36, '2022-03-14 16:32:30', '2022-03-14 16:32:30'),
(169, 'Player-edit', 'web', 'Yes', 36, '2022-03-14 16:35:46', '2022-03-14 16:35:46'),
(170, 'Player-delete', 'web', 'Yes', 36, '2022-03-14 16:35:46', '2022-03-14 16:35:46'),
(171, 'Facility-section', 'web', 'Yes', 37, '2022-03-14 16:32:30', '2022-03-14 16:32:30'),
(172, 'Facility-create', 'web', 'Yes', 37, '2022-03-14 16:32:30', '2022-03-14 16:32:30'),
(173, 'Facility-edit', 'web', 'Yes', 37, '2022-03-14 16:35:46', '2022-03-14 16:35:46'),
(174, 'Facility-delete', 'web', 'Yes', 37, '2022-03-14 16:35:46', '2022-03-14 16:35:46'),
(175, 'Amenity-section', 'web', 'Yes', 38, '2022-03-14 16:32:30', '2022-03-14 16:32:30'),
(176, 'Amenity-create', 'web', 'Yes', 38, '2022-03-14 16:32:30', '2022-03-14 16:32:30'),
(177, 'Amenity-edit', 'web', 'Yes', 38, '2022-03-14 16:35:46', '2022-03-14 16:35:46'),
(178, 'Amenity-delete', 'web', 'Yes', 38, '2022-03-14 16:35:46', '2022-03-14 16:35:46'),
(179, 'Banner-section', 'web', 'Yes', 39, '2022-03-14 16:32:30', '2022-03-14 16:32:30'),
(180, 'Banner-create', 'web', 'Yes', 39, '2022-03-14 16:32:30', '2022-03-14 16:32:30'),
(181, 'Banner-edit', 'web', 'Yes', 39, '2022-03-14 16:35:46', '2022-03-14 16:35:46'),
(182, 'Banner-delete', 'web', 'Yes', 39, '2022-03-14 16:35:46', '2022-03-14 16:35:46'),
(183, 'CourtCategory-section', 'web', 'Yes', 40, '2022-03-14 16:32:30', '2022-03-14 16:32:30'),
(184, 'CourtCategory-create', 'web', 'Yes', 40, '2022-03-14 16:32:30', '2022-03-14 16:32:30'),
(185, 'CourtCategory-edit', 'web', 'Yes', 40, '2022-03-14 16:35:46', '2022-03-14 16:35:46'),
(186, 'CourtCategory-delete', 'web', 'Yes', 40, '2022-03-14 16:35:46', '2022-03-14 16:35:46'),
(187, 'Commission-section', 'web', 'Yes', 41, '2022-03-14 16:32:30', '2022-03-14 16:32:30'),
(188, 'Commission-create', 'web', 'Yes', 41, '2022-03-14 16:32:30', '2022-03-14 16:32:30'),
(189, 'Commission-edit', 'web', 'Yes', 41, '2022-03-14 16:35:46', '2022-03-14 16:35:46'),
(190, 'Commission-delete', 'web', 'Yes', 41, '2022-03-14 16:35:46', '2022-03-14 16:35:46'),
(191, 'Content-delete', 'web', 'Yes', 43, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(192, 'Content-edit', 'web', 'Yes', 43, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(193, 'Content-create', 'web', 'Yes', 43, '2022-03-14 22:02:30', '2022-03-14 22:02:30'),
(194, 'Content-section', 'web', 'Yes', 43, '2022-03-14 22:02:30', '2022-03-14 22:02:30'),
(195, 'Email-Template-section', 'web', 'Yes', 44, '2022-03-14 22:02:30', '2022-03-14 22:02:30'),
(196, 'Email-Template-create', 'web', 'Yes', 44, '2022-03-14 22:02:30', '2022-03-14 22:02:30'),
(197, 'Email-Template-edit', 'web', 'Yes', 44, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(198, 'Email-Template-delete', 'web', 'Yes', 44, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(199, 'Notification-delete', 'web', 'Yes', 45, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(200, 'Notification-edit', 'web', 'Yes', 45, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(201, 'Notification-create', 'web', 'Yes', 45, '2022-03-14 22:02:30', '2022-03-14 22:02:30'),
(202, 'Notification-section', 'web', 'Yes', 45, '2022-03-14 22:02:30', '2022-03-14 22:02:30'),
(203, 'AdminSetting-delete', 'web', 'Yes', 46, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(204, 'AdminSetting-edit', 'web', 'Yes', 46, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(205, 'AdminSetting-create', 'web', 'Yes', 46, '2022-03-14 22:02:30', '2022-03-14 22:02:30'),
(206, 'AdminSetting-section', 'web', 'Yes', 46, '2022-03-14 22:02:30', '2022-03-14 22:02:30'),
(207, 'Testimonial-delete', 'web', 'Yes', 47, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(208, 'Testimonial-edit', 'web', 'Yes', 47, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(209, 'Testimonial-create', 'web', 'Yes', 47, '2022-03-14 22:02:30', '2022-03-14 22:02:30'),
(210, 'Testimonial-section', 'web', 'Yes', 47, '2022-03-14 22:02:30', '2022-03-14 22:02:30'),
(211, 'Cash-settlement-delete', 'web', 'Yes', 48, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(212, 'Cash-settlement-edit', 'web', 'Yes', 48, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(213, 'Cash-settlement-create', 'web', 'Yes', 48, '2022-03-14 22:02:30', '2022-03-14 22:02:30'),
(214, 'Cash-settlement-section', 'web', 'Yes', 48, '2022-03-14 22:02:30', '2022-03-14 22:02:30'),
(215, 'User_bank_detail-delete', 'web', 'Yes', 49, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(216, 'User_bank_detail-edit', 'web', 'Yes', 49, '2022-03-14 22:05:46', '2022-03-14 22:05:46'),
(217, 'User_bank_detail-create', 'web', 'Yes', 49, '2022-03-14 22:02:30', '2022-03-14 22:02:30'),
(218, 'User_bank_detail-section', 'web', 'Yes', 49, '2022-03-14 22:02:30', '2022-03-14 22:02:30');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `type` tinyint DEFAULT NULL COMMENT 'facility = 0, court=1,',
  `type_id` bigint DEFAULT NULL,
  `review` text,
  `rating` tinyint DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `type`, `type_id`, `review`, `rating`, `status`, `created_at`, `updated_at`) VALUES
(14, 48, 1, 9, 'Ddjjbbv', 4, 1, '2022-04-05 12:00:03', '2022-04-05 12:00:03'),
(15, 48, 1, 17, 'Dddd', 4, 1, '2022-04-05 13:06:11', '2022-04-05 13:06:11'),
(16, 9, 1, 8, NULL, 4, 1, '2022-04-07 06:14:04', '2022-04-07 06:14:04'),
(17, 91, 1, 24, NULL, 4, 1, '2022-05-03 09:57:43', '2022-05-03 09:57:43'),
(18, 91, 1, 13, NULL, 4, 1, '2022-05-13 10:22:03', '2022-05-13 10:22:03'),
(19, 87, 1, 22, 'Hello', 5, 1, '2022-05-27 13:37:27', '2022-05-27 13:37:27'),
(20, 87, 1, 24, NULL, 5, 1, '2022-05-27 13:37:52', '2022-05-27 13:37:52'),
(21, 87, 1, 26, 'Test', 5, 1, '2022-05-27 13:38:08', '2022-05-27 13:38:08'),
(22, 87, 1, 19, NULL, 5, 1, '2022-05-30 05:32:26', '2022-05-30 05:32:26'),
(23, 87, 1, 10, 'Asgdgdhs', 5, 1, '2022-05-30 05:32:44', '2022-05-30 05:32:44'),
(24, 87, 1, 20, 'Kya h ye', 5, 1, '2022-05-30 05:33:09', '2022-05-30 05:33:09'),
(25, 71, 1, 22, 'Fggdgfg', 1, 1, '2022-05-30 06:02:03', '2022-05-30 06:02:03'),
(26, 71, 1, 17, 'Sfsf', 5, 1, '2022-05-30 06:04:51', '2022-05-30 06:04:51'),
(27, 87, 1, 7, 'Ye h Kay', 5, 1, '2022-05-30 07:31:14', '2022-05-30 07:31:14');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(0, 'User', 'web', '2020-07-19 23:16:20', '2020-07-19 23:16:20'),
(1, 'Admin', 'web', '2020-03-19 06:12:42', '2020-03-19 06:12:42'),
(4, 'Facility_owner', 'web', '2020-03-19 06:12:42', '2020-03-19 06:12:42');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(136, 1),
(137, 1),
(138, 1),
(139, 1),
(148, 1),
(149, 1),
(150, 1),
(151, 1),
(160, 1),
(161, 1),
(162, 1),
(163, 1),
(164, 1),
(165, 1),
(166, 1),
(167, 1),
(168, 1),
(169, 1),
(170, 1),
(171, 1),
(172, 1),
(173, 1),
(174, 1),
(175, 1),
(176, 1),
(177, 1),
(178, 1),
(179, 1),
(180, 1),
(181, 1),
(182, 1),
(183, 1),
(184, 1),
(185, 1),
(186, 1),
(187, 1),
(188, 1),
(189, 1),
(190, 1),
(191, 1),
(192, 1),
(193, 1),
(194, 1),
(195, 1),
(196, 1),
(197, 1),
(198, 1),
(199, 1),
(200, 1),
(201, 1),
(202, 1),
(203, 1),
(204, 1),
(205, 1),
(206, 1),
(207, 1),
(208, 1),
(209, 1),
(210, 1),
(211, 1),
(212, 1),
(213, 1),
(214, 1),
(215, 1),
(216, 1),
(217, 1),
(218, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shared_challenges`
--

CREATE TABLE `shared_challenges` (
  `id` bigint NOT NULL,
  `court_booking_id` bigint DEFAULT NULL,
  `from_id` bigint DEFAULT NULL,
  `to_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shared_challenges`
--

INSERT INTO `shared_challenges` (`id`, `court_booking_id`, `from_id`, `to_id`, `created_at`, `updated_at`) VALUES
(1, 173, 71, 71, '2022-05-27 07:04:03', '2022-05-27 07:04:03'),
(2, 173, 71, 71, '2022-05-27 07:11:17', '2022-05-27 07:11:17'),
(3, 173, 71, 71, '2022-05-27 07:11:35', '2022-05-27 07:11:35'),
(4, 172, 71, 71, '2022-05-27 07:16:52', '2022-05-27 07:16:52'),
(5, 172, 71, 71, '2022-05-27 07:18:54', '2022-05-27 07:18:54'),
(6, 172, 71, 71, '2022-05-27 07:22:33', '2022-05-27 07:22:33'),
(7, 172, 71, 71, '2022-05-27 07:22:58', '2022-05-27 07:22:58'),
(8, 176, 47, 71, '2022-05-27 07:26:27', '2022-05-27 07:26:27'),
(9, 179, 87, 71, '2022-05-27 09:09:00', '2022-05-27 09:09:00'),
(10, 179, 87, 87, '2022-05-27 09:11:52', '2022-05-27 09:11:52'),
(11, 179, 87, 109, '2022-05-27 09:12:39', '2022-05-27 09:12:39'),
(12, 172, 71, 71, '2022-05-27 09:16:13', '2022-05-27 09:16:13'),
(13, 172, 71, 71, '2022-05-27 09:16:45', '2022-05-27 09:16:45'),
(14, 180, 87, 41, '2022-05-27 10:05:36', '2022-05-27 10:05:36'),
(15, 180, 87, 41, '2022-05-27 10:06:22', '2022-05-27 10:06:22'),
(16, 181, 87, 49, '2022-05-30 06:01:46', '2022-05-30 06:01:46'),
(17, 192, 49, 71, '2022-05-30 10:42:46', '2022-05-30 10:42:46');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` bigint NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `title`, `description`, `status`, `created_at`, `updated_at`) VALUES
(7, 'Testing', 'Happy Holy', 1, '2022-04-18 09:48:51', '2022-04-18 09:49:08');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials_lang`
--

CREATE TABLE `testimonials_lang` (
  `id` bigint NOT NULL,
  `testimonial_id` bigint DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `lang` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `testimonials_lang`
--

INSERT INTO `testimonials_lang` (`id`, `testimonial_id`, `title`, `description`, `lang`, `created_at`, `updated_at`) VALUES
(9, 5, 'Title English', 'Description  Arabic', 'en', '2022-04-08 09:42:27', '2022-04-15 14:34:08'),
(10, 5, 'Title  Arabic', 'Description  Arabic', 'ar', '2022-04-08 09:42:27', '2022-04-15 14:34:08'),
(11, 6, 'Holy', 'Happy Holy', 'en', '2022-04-08 11:17:32', '2022-04-08 11:17:32'),
(12, 6, 'Holy', 'Happy Holy', 'ar', '2022-04-08 11:17:32', '2022-04-08 11:17:32'),
(13, 7, 'Testing', 'Happy Holy', 'en', '2022-04-18 09:48:51', '2022-04-18 09:49:08'),
(14, 7, 'testing', 'Happy Holy', 'ar', '2022-04-18 09:48:51', '2022-04-18 09:49:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_type` enum('url','local') CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'local',
  `mobile` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `age` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'admin = 0, user = 2, facility_owner = 1',
  `gender` enum('Male','Female','Other') CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `marital_status` enum('Single','Married') CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci,
  `country_code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `referral_code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `share_code` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `genres` int DEFAULT NULL,
  `food_license` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `license_number` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `license_image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `social_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `social_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `bio` text CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci,
  `points` int DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_profile_updated` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `new_email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_email_token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `parent_chef_id` int DEFAULT NULL,
  `restaurant_id` int DEFAULT NULL,
  `gift_user_id` int DEFAULT NULL,
  `gift_access_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `gift_secret_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `main_access_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `main_secret_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `first_name`, `last_name`, `email`, `image`, `image_type`, `mobile`, `dob`, `age`, `type`, `gender`, `marital_status`, `address`, `country_code`, `referral_code`, `share_code`, `latitude`, `longitude`, `genres`, `food_license`, `license_number`, `license_image`, `social_type`, `social_id`, `bio`, `points`, `email_verified_at`, `password`, `remember_token`, `is_profile_updated`, `created_at`, `updated_at`, `deleted_at`, `new_email`, `new_email_token`, `status`, `parent_chef_id`, `restaurant_id`, `gift_user_id`, `gift_access_key`, `gift_secret_key`, `main_access_key`, `main_secret_key`) VALUES
(1, 'Admin .', 'Admin', '.', 'admin@yopmail.com', 'APR2022/1649305520-user.jpeg', 'local', '98765432', NULL, NULL, '0', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-19 04:03:35', '$2y$10$3VWfkpjwigQN2KKMRPkq2uYhVhL62ujwXNi30CKF7Yd9zvXOcPgUu', 'M2Ysilq8LQNPaN7Xb3mgeuv6hCaNdw37tXTEtqHqBE41y3fiwNV0WWj872Ef', 0, '2020-03-18 12:20:30', '2022-05-13 11:38:47', NULL, 'rajashishg1@gmail.com', '741c1f0705d058bb48ccf63581b39b2a95aede847f2981060668a4bf5e80b2d0', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'New User', 'New', 'User', 'newuser@yopmail.com', 'MAR2022/1646401117-user.png', 'local', '55355444475', NULL, NULL, '1', 'Male', 'Single', NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-19 04:03:35', '$2y$10$FDAeZmgVQIbMTc9T0GRJ3uyZq4zk4eA/7qHTLSBHFq.4bwoCz2rGi', 'bsrDsMA6CRS2oHjgVE5vZ3e3aFCCOhTe8pUk9DbTNRZmPE2U3e8LWnTHSmnE', 0, '2020-03-18 12:20:30', '2022-03-04 13:38:37', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'new onw', NULL, NULL, 'newone@mailinator.com', NULL, 'local', '3216549870', NULL, NULL, '1', 'Female', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-14 13:53:06', '$2y$10$QLMYeqhTDt1zgR9UT3C8QeYYPpzI.XQse8dPzcOeHyDk/isQqhxaC', NULL, 0, '2022-03-14 13:53:06', '2022-03-25 13:08:14', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'facility', 'Jugal', 'Kishor', 'admin@125783gmail.com', 'MAR2022/1648039592-user-image.jpg', 'local', '1472583690', '2022-01-25', NULL, '1', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-14 13:55:12', '$2y$10$PT6Rc/5Alwe3vNKxixgFquayR4mW5cV8bjKVb.c8doCUVfmON.3wa', NULL, 1, '2022-03-14 13:55:12', '2022-04-08 08:41:51', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Owner1', NULL, NULL, 'owner@mailinator.com', NULL, 'local', '9999999966', NULL, NULL, '1', 'Female', NULL, NULL, '+91', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-15 13:17:49', '$2y$10$MDVyU1H92563iBV.6f3DGu5o9e5tAbmafKQRi.cgXLDw0czLd6Jne', NULL, 0, '2022-03-15 13:17:49', '2022-03-26 08:21:01', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, '', NULL, NULL, NULL, NULL, 'local', '99999999663', NULL, NULL, '3', NULL, NULL, NULL, '91', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-15 13:40:57', '$2y$10$4kCueTH90cN0xNeFkZ1n5OTZb2OFxqNmCeFdJDek8gy6X4Qvw.NO.', NULL, 0, '2022-03-15 13:40:57', '2022-03-15 13:40:57', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, '', NULL, NULL, NULL, NULL, 'local', '1234567891', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-16 06:23:47', '$2y$10$Adi3wJ3Xx9pHGhviYLRZQuUxigPFaAuiP1AqlPz.afK9aMoQwZ8cC', NULL, 0, '2022-03-16 06:23:47', '2022-03-16 06:23:47', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, '', NULL, NULL, NULL, NULL, 'local', '9999999963', NULL, NULL, '3', NULL, NULL, NULL, '91', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-16 06:45:32', '$2y$10$Q4AD8yog.0noS2rsUjRsnOSD/3VWlNB1CFt/CGiSTlKYfFD30eQpa', NULL, 0, '2022-03-16 06:45:32', '2022-03-16 06:45:32', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'The Mouse', 'The', 'Mouse', 'admin@12573gmail.coz', 'MAR2022/1648213306-user-image.jpg', 'local', '1234567890', '2022-03-18', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-16 07:09:42', '$2y$10$H8Xo2/L.5cl1E3Xc4NiCj.BHcyWdnHCVDte0nwf6gA8W/j.4kLllm', NULL, 1, '2022-03-16 07:09:42', '2022-04-14 12:41:50', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, '', NULL, NULL, NULL, NULL, 'local', '12345678900', NULL, NULL, NULL, NULL, NULL, NULL, '91', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-16 07:57:16', '$2y$10$aq3UqAaLoVzqWX4V6Ih0o.kIhrAtkZ7Vg.T1YH3wpzO3q5JdTrzkS', NULL, 0, '2022-03-16 07:57:16', '2022-03-16 07:57:16', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, '', NULL, NULL, NULL, NULL, 'local', '1234567892', NULL, NULL, NULL, NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-16 10:15:13', '$2y$10$IGqKIvZ3Pb8za5jBduGlf.cygh71UE8Rh7VqSrIN.gIAQeNOUVdFy', NULL, 0, '2022-03-16 10:15:13', '2022-03-16 10:15:13', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, '', NULL, NULL, NULL, NULL, 'local', '1122334455', NULL, NULL, NULL, NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-16 10:35:01', '$2y$10$G6K8/5Ksf8b8HeKD/4kfFe5LEyxdhkgtLvAZq5g08XG2IE96.3hKq', NULL, 0, '2022-03-16 10:35:01', '2022-03-16 10:35:01', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'Jugal Kishor', 'Jugal', 'Kishor', 'Abc@gmail.com', NULL, 'local', '1234567893', NULL, NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-16 10:59:08', '$2y$10$6gZ4ccLEBUoWunGgpnggYu0fk2xxibQmgfdD0vKDRf/6iSkt8mWOm', NULL, 0, '2022-03-16 10:59:08', '2022-03-16 11:30:37', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, '', NULL, NULL, NULL, NULL, 'local', '3692581470', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-16 11:34:25', '$2y$10$Kq2TEpcd8LzV9WS9jprVduRpmGUbFAcJsSh6piIywlIZCCMJTy6z6', NULL, 0, '2022-03-16 11:34:25', '2022-03-16 11:34:25', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, '', NULL, NULL, NULL, NULL, 'local', '1452369870', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-16 12:54:19', '$2y$10$4khAxCusi00seaAXLmTg2upR4NSuWq.YUyQQIgJ0mEWcifx8Kj1LS', NULL, 0, '2022-03-16 12:54:19', '2022-03-16 12:54:19', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'gh', 'gh', NULL, 'gfhfg@gmail.com', NULL, 'local', '1236987145', NULL, NULL, '3', 'Female', NULL, NULL, '+255', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-21 08:44:38', '$2y$10$kT3oxu8vTm8QVCVXA.XCSuSZMqqLjD9Ck/yrwCDPnwYgR/Rbeg7ya', NULL, 0, '2022-03-21 08:44:38', '2022-04-08 06:03:22', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'Test User', 'Test', 'User', 'Sgsjd@gmail.com', NULL, 'local', '9876543210', NULL, NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-22 09:07:42', '$2y$10$0CxcDWdrO3unex.fn6rvP.S529w3HzWFfW1vb/j2qpZ5cr6KEjpWe', NULL, 0, '2022-03-22 09:07:42', '2022-03-22 09:11:49', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, '', NULL, NULL, NULL, NULL, 'local', '6464646434', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-22 10:02:12', '$2y$10$2PPVs6Z6AFgB06anCoiHj.9pwcZBjmijXQp4pLhWjgPuKvtB/d28y', NULL, 0, '2022-03-22 10:02:12', '2022-03-22 10:02:12', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, '', NULL, NULL, NULL, NULL, 'local', '53333112', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-22 10:20:29', '$2y$10$od0gRLOJz2zfjnv8ZpKuOuX.vUtFFwzn1F2dFqtstTrhK7tGtplmu', NULL, 0, '2022-03-22 10:20:29', '2022-03-22 10:20:29', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'Sjdjs Hshshs', 'Sjdjs', 'Hshshs', 'Sudhjdjdjd@gmail.com', NULL, 'local', '69,356646', NULL, NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-22 10:22:48', '$2y$10$RntoAz/SzGgr54AhiW/foe7eBT91GKz9614TMaCMkZ6l/AbutmwgW', NULL, 0, '2022-03-22 10:22:48', '2022-03-22 10:23:59', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'Munesh Saini', 'Munesh Saini', NULL, 'munesh@gmail.com', 'MAR2022/1647946246-user.jpg', 'local', '999999999999', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-22 10:50:46', '', NULL, 0, '2022-03-22 10:50:46', '2022-03-22 10:50:58', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, '', NULL, NULL, NULL, NULL, 'local', '1236547890', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-22 11:06:34', '$2y$10$ap/eTtDMTLHBEk/0cbQtzOOwoWt4hQWGVJ4T4fUnJr.lw.9E5LbpW', NULL, 0, '2022-03-22 11:06:34', '2022-03-22 11:06:34', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'Spider Man', 'Spider', 'Man', 'spider@gmail.com', NULL, 'local', '3214569870', '2022-03-15', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-23 05:06:02', '$2y$10$OuKxjuLqoGOaB0PREUzQBeDtd99Mj4d4eUtsNVkXmlul/4iHshaCW', NULL, 0, '2022-03-23 05:06:02', '2022-03-23 05:12:50', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'Spider Man', 'Spider', 'Man', 'siderman@gmail.com', NULL, 'local', '1234567899', '2022-03-15', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-23 05:41:05', '$2y$10$oXIXCYlRYMYjkZp12pLGru.cuXp69uRMuHP3Gg/JJEnRLQF1k98xW', NULL, 0, '2022-03-23 05:41:05', '2022-03-23 05:45:04', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'Sports woman', 'dfsd', 'sdfsfd', 'sfddsdf@dfs.ccx', 'MAR2022/1648212711-user.jpg', 'local', '1234567877', '2022-03-16', NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-23 06:13:03', '$2y$10$qupELY38HP.6LRGuNPxMJ.JDao/yqeFZgso4Dgp5oo2enXiTHmOrW', NULL, 1, '2022-03-23 06:13:03', '2022-03-25 12:51:51', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, '', NULL, NULL, NULL, NULL, 'local', '7062258117', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-25 13:01:35', '$2y$10$WgOsUTqERFYjt/u76qkyc.3G.X2MKtVet2ZqgvE/YGiwue9mjCsWW', NULL, 0, '2022-03-25 13:01:35', '2022-03-25 13:01:35', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, '', NULL, NULL, NULL, NULL, 'local', '7062258118', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-25 13:03:09', '$2y$10$IVF8F7xXJGr3HbNtz2T85.PSTQFFxaI1IfZxsUpGr6aTdTVv.W0oS', NULL, 0, '2022-03-25 13:03:09', '2022-03-25 13:03:09', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, '', NULL, NULL, NULL, NULL, 'local', '7012345690', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-25 13:04:30', '$2y$10$e65Zg09DlmpBvfVrHD7R.et/LQ67Vrv0xofq9mmRLlCqCTULfqCwC', NULL, 0, '2022-03-25 13:04:30', '2022-03-25 13:04:30', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, '', NULL, NULL, NULL, NULL, 'local', '6464646666', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-25 13:05:30', '$2y$10$TU2nBXTfmHt4kwNz7HC47uJ05jtj3OA4T8h9424PpziDPelM7Sd8y', NULL, 0, '2022-03-25 13:05:30', '2022-03-25 13:05:30', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, '', NULL, NULL, NULL, NULL, 'local', '3221546788', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-25 13:07:14', '$2y$10$l/3AkK3gXxBb.Cyq/Fj9TeNwLWKHikWNmsvrlWgkhJd3y/q1/h06S', NULL, 0, '2022-03-25 13:07:14', '2022-03-25 13:07:14', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'Munesh Saini', NULL, NULL, 'munesh321@gmail.com', NULL, 'local', '1239876540', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-25 13:20:14', '$2y$10$U9n5dgjReMVy2Ba/rnlZeun3ZwLxDaI/CRaFfQo4D6bb7OJrwYz42', NULL, 0, '2022-03-25 13:20:14', '2022-03-25 13:42:54', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 'Ddddd Cvgg', 'Ddddd', 'Cvgg', 'zzggff@hhh.bbb', NULL, 'local', '4455667788', '2022-03-13', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-25 13:23:18', '$2y$10$2WqwOOTIfT4Ojt0Zhtu9UO/ZKuq39ueO71.q/gmyfUq2MTLQZC.uq', NULL, 1, '2022-03-25 13:23:18', '2022-03-25 13:23:37', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 'Ehdah Cxg', 'Ehdah', 'Cxg', 'zvmzfnzf@yytjd.com', 'MAR2022/1648214871-user-image.jpg', 'local', '3366998855', '2022-03-17', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-25 13:27:20', '$2y$10$ycvj/GXFNz144PNwLo9Xvu3JltPBeB4eTPFB3KYgbCZtFWTdcHdcO', NULL, 1, '2022-03-25 13:27:20', '2022-03-25 13:27:51', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'Vsvsv Csvsvs', 'Vsvsv', 'Csvsvs', 'gdhdhd@gd.com', NULL, 'local', '5454840787', '1986-07-10', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-26 05:48:18', '$2y$10$r91V2JQMMcjqYWsNiKA3Y.qkgcWsocy0sqUj.F8x21h/k64MXR0XK', NULL, 1, '2022-03-26 05:48:18', '2022-03-26 08:11:42', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 'Sandeep Agrawal', 'Sandeep Agrawal', NULL, 'sandeep@yahoo.com', 'MAR2022/1648282561-user.jpg', 'local', '7845784512', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-26 08:16:01', '', NULL, 0, '2022-03-26 08:16:01', '2022-03-26 08:16:01', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, '', NULL, NULL, NULL, NULL, 'local', '7062225811', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-28 06:33:37', '$2y$10$ULAzSxrv2UcWQ7zdSS.LZuLbJ5CORPVCIY00wwVeERr9URS/RC5we', NULL, 0, '2022-03-28 06:33:37', '2022-03-28 06:33:37', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, '', NULL, NULL, NULL, NULL, 'local', '14785223', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-28 06:41:51', '$2y$10$A0Ogk3r.wdkJTfymkZytfONnuIVtL6t.of6bbuIEHb6tCjxaQoOkS', NULL, 0, '2022-03-28 06:41:51', '2022-03-28 06:41:51', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, '', NULL, NULL, NULL, NULL, 'local', '9632587865', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-28 08:44:35', '$2y$10$ODM6vneBtPfKkuaz0Fc1wugpcdeM8m7SeMSsE0igDmGuxQZyceLPO', NULL, 0, '2022-03-28 08:44:35', '2022-03-28 08:44:35', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, '', NULL, NULL, NULL, NULL, 'local', '122556699', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-29 06:11:37', '$2y$10$AHqG27gmfuzEGdnNdgbRKusFKiJmRoOQ0Yx6WyGWUWn.EotXSq29G', NULL, 0, '2022-03-29 06:11:37', '2022-03-29 06:11:37', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 'gfgf ', 'gfgf', NULL, 'admrin@12573gmail.com', 'https://graph.facebook.com/3010747402571459/picture', 'local', '9999999999', NULL, NULL, '3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'facebook', '54321112122e', NULL, NULL, '2022-03-29 12:01:12', '$2y$10$qSksoxgzoCRVek8tbQwqbu/6i4.LQVkanDFhRot8CPX5t7s15x.L.', NULL, 1, '2022-03-29 12:01:12', '2022-04-08 04:08:21', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 'invent colabs', 'invent', 'colabs', 'dev.inventcolabs@gmail.com', 'APR2022/1650374175-user-image.jpg', 'local', '9523565586', '2022-03-10', NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'google', 'WobSs0i95jPLfeFY5grCbWGhcd23', NULL, NULL, '2022-03-30 05:20:54', '$2y$10$kcweJjm.jD04cI7BHeYyceyigY20vl76hcaxB9RN/HNewH3SBJaxu', NULL, 1, '2022-03-30 05:20:54', '2022-05-27 10:04:04', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 'Gggh Jhrh', 'Gggh', 'Jhrh', 'apps@cerebrum.net', 'MAR2022/1648635010-user-image.jpg', 'url', '658325466', '2022-03-11', NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'apple', 'l6aD9UEmBkQynKdLwsn3tbxvDVU2', NULL, NULL, '2022-03-30 06:02:07', '$2y$10$lzyWAQ5g3hiL14yDPRcg0egc6NBJdlbPkB3NmLrtqOQrqRRXOJX3G', NULL, 1, '2022-03-30 06:02:07', '2022-05-30 10:44:44', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, '', NULL, NULL, NULL, NULL, 'local', '336699885', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-30 06:31:58', '$2y$10$OjJK5Oy/8cMcuDfaIdKqYumW7Hrsv1O/89RqCck0BXSb4U8w9ngAe', NULL, 0, '2022-03-30 06:31:58', '2022-03-30 06:31:58', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 'Vvsgsc Fbevx', 'Vvsgsc', 'Fbevx', 'febdfs@dvsfd.chd', 'MAR2022/1648635969-user-image.jpg', 'local', '4818144448', '2022-03-08', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-30 10:25:38', '$2y$10$hA5fGQ.iotMcT4rzADx61OFT3o8k9kKN6C9wwGn0feYpPdRdW1M5y', NULL, 1, '2022-03-30 10:25:38', '2022-03-30 10:26:09', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'Speedy Sport', NULL, NULL, 'fakeemail@gmail.com', NULL, 'local', '0503254445', NULL, NULL, '1', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-30 14:34:00', '$2y$10$EeHffoRMY1fy3LsCHKRLru935ju.iLRkMmfxHSJcR8KWIeygNA43S', NULL, 0, '2022-03-30 14:34:00', '2022-04-25 11:55:36', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 'Test', NULL, NULL, 'munesh.mali@inventcolab.com', NULL, 'local', '9999449', NULL, NULL, '1', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-01 12:45:13', '$2y$10$wuflX1TCuxJSAqKYexLPVusc4zkgAoDbSjLcB074kpqsllRLcaBb6', 'zOfVub2CshQkwFOzewLuQFaEnvryE63EKp1noPy5ySCHB9VAsqCtBQFGTypR', 0, '2022-04-01 12:45:13', '2022-05-03 13:44:05', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 'Www R', 'Www', 'R', 'whoissudhanshu@gmail.com', 'MAY2022/1653904738-user-image.jpg', 'local', '987654320', NULL, NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'google', '5fxlCnqtjBgwqE0bE6TYOw669OB2', NULL, NULL, '2022-04-04 13:31:22', '$2y$10$BQSNbIcI5Vg68Z9Ci/MjS.iaeb7NlDTyQY7ofXC6Sl6O.fRldeFF2', NULL, 1, '2022-04-04 13:31:22', '2022-05-30 09:58:58', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'The Spider', 'The', 'Spider', 'spider@yopmail.com', 'APR2022/1649143523-user.jpg', 'local', '5222000000', '2022-04-01', NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-05 05:18:33', '$2y$10$ZHKn.xRF38TFQCv5vxSW7.Z8lYBN.CBr3Yq625lXkpnjySGcLCS4m', NULL, 1, '2022-04-05 05:18:33', '2022-04-05 07:25:34', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 'Mukesh Malik ', 'Mukesh Malik', NULL, 'mukeshmalik004@gmail.com', 'MAY2022/1653890811-user-image.jpg', 'local', '9667136636', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-05 07:28:23', '$2y$10$h3AkFhVZ75xeZox5ZtlCuO7qmufO1GhCJy0DQSXJV8tqO5YzTYOH6', NULL, 1, '2022-04-05 07:28:23', '2022-05-30 06:06:51', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 'Mukesh', NULL, NULL, 'mukeshmalik0@gmail.com', 'APR2022/1649148038-user.jpeg', 'local', '9358815181', NULL, NULL, '1', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-05 08:40:38', '', NULL, 0, '2022-04-05 08:40:38', '2022-04-11 08:56:16', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'Mukesh Malik', 'Mukesh', 'Malik', 'abcqwe@gmail.com', 'APR2022/1649326532-user-image.jpg', 'local', '98765432', '2022-04-06', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-07 10:10:21', '$2y$10$YHwLVkEoWXJTqzyJqp1i.O1SQSRmn7z9deQ9dUbNVnQlFuLlRUHba', NULL, 1, '2022-04-07 10:10:21', '2022-04-07 10:15:32', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, '', NULL, NULL, NULL, NULL, 'local', '987654321', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-07 10:16:23', '$2y$10$Ga71QWxmzH5lADOXxUJxc.JBt4Q8j10lsYWKFK6tfL5JEVaMMB/P.', NULL, 0, '2022-04-07 10:16:23', '2022-04-07 10:16:23', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 'Mukesh Malik', 'Mukesh', 'Malik', 'malik@ymail.com', 'APR2022/1649326796-user-image.jpg', 'local', '9988776655', '2022-04-03', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-07 10:19:00', '$2y$10$E4l5qljVb8mPuv6XHpbWVeHP7JoZ/6lzDOhzhXSSSxFEIkskIRj.G', NULL, 1, '2022-04-07 10:19:00', '2022-04-15 09:49:08', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, '', NULL, NULL, NULL, NULL, 'local', '8558585585258', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-07 10:48:08', '$2y$10$dx6sZGEi/8uT9VIIdORKMebRQdKDQEWD1jKvEixv5dH8fZRLmK6iO', NULL, 0, '2022-04-07 10:48:08', '2022-04-07 10:48:08', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, '', NULL, NULL, NULL, NULL, 'local', '1234564971', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-07 11:09:44', '$2y$10$UZ3jw2suOa9YaDSAffgsBe00HpFLoi57wb2Cb17VbAq/shBaJr3A.', NULL, 0, '2022-04-07 11:09:44', '2022-04-18 09:22:59', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, '', NULL, NULL, NULL, NULL, 'local', '112233653', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-07 11:56:24', '$2y$10$z/Ac1mKEqdTkvgcqksc9r.yrY4/oTtg7CXYw34g2AcnosOHKpDJTS', NULL, 0, '2022-04-07 11:56:24', '2022-04-07 11:56:24', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 'Guga ', 'Guga', NULL, 'xxx@gmail.com', NULL, 'local', '1234567888', NULL, NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-08 12:46:14', '$2y$10$pR4qCTDZzzflyBJNXzZJZefMo3zDBKvwsIfA1kCr0uKaPeMFVmJLe', NULL, 1, '2022-04-08 12:46:14', '2022-04-11 09:27:12', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 'Khalifa Almenhali', 'Khalifa', 'Almenhali', 'noemail@hotmail.com', NULL, 'local', '0504444445', '2000-02-04', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-08 18:36:50', '$2y$10$JyCEmXghjFSZgMq/T02WeeIbMApIPxqzAPcPJP.jYMSVt8C5ZCRRy', NULL, 1, '2022-04-08 18:36:50', '2022-04-08 18:38:12', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 'facilityowner', NULL, NULL, 'facilityowner@mailinator.com', NULL, 'local', '78784512', NULL, NULL, '1', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-09 11:29:07', '', NULL, 0, '2022-04-09 11:29:07', '2022-04-09 11:29:07', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 'sandeep agrawal', NULL, NULL, 'sandeepalpha86@gmail.com', NULL, 'local', '787845178', NULL, NULL, '1', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-09 11:30:13', '$2y$10$2QxyZDQKWEiOZ97Ak5EI2eOltO5Efy/3mrKB3Ubu9BnTC74LEn7c.', NULL, 0, '2022-04-09 11:30:13', '2022-04-09 11:30:41', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 'Makk Malik', NULL, NULL, 'makkmalik@gmail.com', NULL, 'local', '96671366', NULL, NULL, '1', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-11 08:57:14', '$2y$10$4RYionGX3v9yJtl96WwSVe4mYpvp0N32QnseXTy4TBrxtmliK2PQO', NULL, 0, '2022-04-11 08:57:14', '2022-04-18 13:21:46', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 'Amit ', 'Amit', NULL, NULL, NULL, 'local', '568658585', NULL, NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-11 09:08:48', '$2y$10$0pdIxCPwer4f.WCiASUDn.7BsDFMgVkjatVlhFBZbMbQtxhfv3e0y', NULL, 1, '2022-04-11 09:08:48', '2022-04-11 09:09:25', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, '', NULL, NULL, NULL, NULL, 'local', '9876543219', NULL, NULL, NULL, NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-11 09:41:46', '$2y$10$IkAy0doTRj1b6bpTRta1euTFfRwvvSPwznL85vfhajYjAviLw3Txy', NULL, 0, '2022-04-11 09:41:46', '2022-04-11 09:42:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 'Mukesh Malik', 'Mukesh', 'Malik', 'malikmak@gmail.com', NULL, 'local', '9813153538', '1991-03-14', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-11 10:48:18', '$2y$10$dK..CjnFihiUPAuckOdOF.d1ATtSZUqXmIyLNryvRVqZ.SVK.dyzy', NULL, 1, '2022-04-11 10:48:18', '2022-04-14 05:48:19', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 'Test ', 'Test', NULL, NULL, NULL, 'local', '8523698745', NULL, NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-11 11:27:13', '$2y$10$wZr5HTkyklXIw00hcAIfR.4lbYZz/rwaRkJ7Yw36KFJgHGyjUO5Sm', NULL, 1, '2022-04-11 11:27:13', '2022-04-11 11:27:39', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 'Hello ', 'Hello', NULL, NULL, NULL, 'local', '2546784512', NULL, NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-11 11:28:40', '$2y$10$mJmlI2WKxu8AWB5MrTzejOBtgqvMZrmGkk307KewuIzs0Uzm0wwta', NULL, 1, '2022-04-11 11:28:40', '2022-04-18 09:22:51', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 'Testing ', 'Testing', NULL, NULL, NULL, 'local', '6358678585', NULL, NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-11 11:29:20', '$2y$10$cb8I8o4u0r1K/xZuF1JDeeBp2CHciRyOJUfvgIGvAwf8Z8mw1g1GS', NULL, 1, '2022-04-11 11:29:20', '2022-04-11 11:46:23', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 'Hello', 'Hello', NULL, 'hello@gmaill.com', NULL, 'local', '992551660', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-11 11:57:33', '', NULL, 0, '2022-04-11 11:57:33', '2022-04-11 11:58:31', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 'Shahid Kapoor', 'Shahid', 'Kapoor', 'makk@gmail.com', 'APR2022/1649841631-user-image.jpg', 'local', '123456789', '1991-03-14', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-13 09:16:23', '$2y$10$haJ1uHBxqVEueNQ4EB6sYeKEn6IHFezu9O6G7sUNim1Seax/hnou2', NULL, 1, '2022-04-13 09:16:23', '2022-04-13 09:23:59', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 'Sandeep Agrawal', 'Sandeep', 'Agrawal', 'sandeepalphyh6@gmail.com', 'APR2022/1649856351-user-image.jpg', 'local', '123456766', '2010-04-07', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-13 13:24:33', '$2y$10$JKzz/E1n.4GFHOI3fFBvkuhdYzczFpRHSWNiwDX0EFAjK1AbmiBt6', NULL, 1, '2022-04-13 13:24:33', '2022-04-13 13:25:51', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 'Sudhanshu Sharma', 'Sudhanshu', 'Sharma', 'sudhanshu.rinwa7737@gmail.com', 'https://lh3.googleusercontent.com/a-/AOh14GiT6TbORwPB1YuPGBgnPyjC69meRyKHi-6x0uR7PQ=s96-c', 'url', '9461512826', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'google', 'awQF9IqoHyaAexEVDC6dtXDR12z1', NULL, NULL, '2022-04-14 04:49:30', '$2y$10$n1YD8QtbbtDt7PnphSKQfeopcADI8zIvDREqfdDrzErlxSc2uXgt2', NULL, 0, '2022-04-14 04:49:30', '2022-05-26 09:14:49', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(72, '', NULL, NULL, NULL, NULL, 'local', '987654258', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-14 05:18:36', '$2y$10$7wi/KDr5pAX1MFCI/7maNOy4S3tT2kyEekUo6hNOdidcxBqGdwkcm', NULL, 0, '2022-04-14 05:18:36', '2022-04-14 05:18:36', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 'Shaktiman Jjjj', 'Shaktiman', 'Jjjj', 'ab@ffff.bbb', NULL, 'local', '321657894', '2010-04-08', NULL, '3', 'Male', NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 05:11:16', '$2y$10$Ho7mdsciO.aj.MmgGrO/h.u8zgn7hwt48MNrs3P8Y6Srdo9AdPtaq', NULL, 1, '2022-04-15 05:11:16', '2022-04-15 05:51:12', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, '', NULL, NULL, NULL, NULL, 'local', '11447722', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 09:43:17', '$2y$10$8v5mOnJ7sC9.uc/hTtZRzeTMgeePdf4A2AuKAo3/3wRm2nlEF0Dvq', NULL, 0, '2022-04-15 09:43:17', '2022-04-15 09:43:17', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(75, '', NULL, NULL, NULL, NULL, 'local', '9649186636', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 10:12:36', '$2y$10$bFgGV5eHV87stNe3g4oaBuPSL8rwU6Q3sv1pAfVJqDvEBR6PEfw8u', NULL, 0, '2022-04-15 10:12:36', '2022-04-15 10:12:36', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(76, '', NULL, NULL, NULL, NULL, 'local', '9648253652', NULL, NULL, '3', NULL, NULL, NULL, '971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 10:16:52', '$2y$10$srXKttxa3Ia0ThR/PjdPpevzYAf0/RDlP4VvoQZYh8HDRiIsXj.ji', NULL, 0, '2022-04-15 10:16:52', '2022-04-15 10:16:52', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 'Mukesh ', 'Mukesh', NULL, NULL, NULL, 'local', '987524125', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 11:04:08', '$2y$10$oyc.jWlktg/1LBQFwW7Xi.dv6h2q6lSCHtb3lTH/4z738r/Ntqlge', NULL, 1, '2022-04-15 11:04:08', '2022-04-15 11:04:22', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(78, '', NULL, NULL, NULL, NULL, 'local', '123456789', NULL, NULL, NULL, NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 11:16:31', '$2y$10$wDutIthISPvoKQNXMkSvMuLYPlCP35QdHK9n7W.5rRVSURmnTahnK', NULL, 0, '2022-04-15 11:16:31', '2022-04-15 11:18:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(79, '', NULL, NULL, NULL, NULL, 'local', '1122336655', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 11:48:28', '$2y$10$9.a6otx0Y7IKapNt07.JLem9yBJe7G2m7Db.CRrG7FoHfxfWXG0t2', NULL, 0, '2022-04-15 11:48:28', '2022-04-15 11:48:28', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 'Malik ', 'Malik', NULL, 'makk@milinator.com', 'MAY2022/1651562230-user-image.jpg', 'local', '964918663', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 12:35:09', '$2y$10$V54TzBf5tKELRRJY0sS5E.LBcvQLXKutgTHJ9oJ6RH9NPwnR6S1tW', NULL, 1, '2022-04-15 12:35:09', '2022-05-03 07:17:10', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(81, '', NULL, NULL, NULL, NULL, 'local', '963258741', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 13:40:25', '$2y$10$Umm14OsOlDplgvfqtcHE0.ikJ3T/5WOiNYkaCbLgk9ZMRPHoUArl.', NULL, 0, '2022-04-15 13:40:25', '2022-04-15 13:40:25', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(82, '', NULL, NULL, NULL, NULL, 'local', '867887878', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 13:59:46', '$2y$10$CgYBAPrY3n0gvEiwCuSCtuaTCaZUV4WlRSXt/fjK.eiK9B.dFwTkG', NULL, 0, '2022-04-15 13:59:46', '2022-04-15 13:59:46', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(83, '', NULL, NULL, NULL, NULL, 'local', '1233346666', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 14:01:17', '$2y$10$Z759nyjjjuuRIi7LW0iNmOzksGytfmKZIc6dUiMcB6QluQmvJhTFS', NULL, 0, '2022-04-15 14:01:17', '2022-04-15 14:01:17', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(84, '', NULL, NULL, NULL, NULL, 'local', '8888555666', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 14:04:55', '$2y$10$Upn8yQEzK0TLlZov/xZK0.HqMine5lAFXPYEOkvHJc74Dvwah.Xqa', NULL, 0, '2022-04-15 14:04:55', '2022-04-15 14:04:55', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(85, '', NULL, NULL, NULL, NULL, 'local', '1238888874', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 14:12:30', '$2y$10$wKW5YfB86wt5GO9HEIxeauiOcVCBM0iIbeTjfjT4BGJsu2dvNKHrq', NULL, 0, '2022-04-15 14:12:30', '2022-04-15 14:12:30', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(86, '', NULL, NULL, NULL, NULL, 'local', '95645575', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-15 14:15:50', '$2y$10$vB5JBZI/HfNY/8MA80gweOfopNMJHPEZXNQR2.POh7JV7nBggOkYe', NULL, 0, '2022-04-15 14:15:50', '2022-04-18 09:45:46', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 'Mukesh Malik', 'Mukesh', 'Malik', 'mukesh@gmail.com', 'APR2022/1650274162-user.jpeg', 'local', '9887136636', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-18 09:29:02', '$2y$10$kXRFQuQjr55qWNyRfuw8du3zHMYR3E8k1oo8Ryv9SOOpprx8TQ2BG', NULL, 1, '2022-04-18 09:29:02', '2022-05-27 10:16:25', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 'testing', NULL, NULL, 'testing@ymail.com', 'APR2022/1650274224-user.jpg', 'local', '6598745631', NULL, NULL, '1', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-18 09:30:09', '', NULL, 0, '2022-04-18 09:30:09', '2022-04-25 13:01:29', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(89, '', NULL, NULL, NULL, NULL, 'local', '1234567890', NULL, NULL, NULL, NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-18 13:23:50', '$2y$10$BMCqEVXTesMrGExGwdFFH.bRhI589wFU42YMkWLu63lPUiW.JWvte', NULL, 0, '2022-04-18 13:23:50', '2022-04-18 13:27:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(90, '', NULL, NULL, NULL, NULL, 'local', '1122334455', NULL, NULL, NULL, NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-18 13:33:36', '$2y$10$qt0PccEuSi3E3RkaVvqHFOQWcPERc9mFm72Y.pXAr54jlJ2VooGtO', NULL, 0, '2022-04-18 13:33:36', '2022-04-18 13:33:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 'Jugal Kishor', 'Jugal', 'Kishor', 'mij@ymail.com', 'MAY2022/1652173837-user-image.jpg', 'local', '8890209205', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-19 05:12:32', '$2y$10$FhP0PBLiGvhA8toPF9Y7xu1FFVDx2l9tbAGp04HO3D7dUEHfiw7kq', NULL, 1, '2022-04-19 05:12:32', '2022-05-10 09:11:41', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(92, '', NULL, NULL, NULL, NULL, 'local', '9649186636', NULL, NULL, NULL, NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-20 11:37:42', '$2y$10$X4dj/3ZzpaOwPKzL7vQ/U./yYFSKxLLfv42qLc7UBu8OLwHGEj7eq', NULL, 0, '2022-04-20 11:37:42', '2022-04-20 11:37:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, '', NULL, NULL, NULL, NULL, 'local', '9876543212', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-21 11:54:35', '$2y$10$ThVk9T147jciqFbAbdLeIenPiKrZ8yz1HYR3HFe33wp2VmoAxieRi', NULL, 0, '2022-04-21 11:54:35', '2022-04-21 11:54:35', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 'Makk Malik', 'Makk', 'Malik', NULL, 'https://graph.facebook.com/5704043102958088/picture', 'url', NULL, NULL, NULL, '3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'facebook', 'wcsGgtJDcAa8c2ro1SuFGa322n93', NULL, NULL, '2022-04-21 12:59:21', '$2y$10$kTodn5JF61HoY63fIf59/uqD4MLpK139ZRyKZDjyG6NL8z9dlYw7.', NULL, 0, '2022-04-21 12:59:21', '2022-04-21 12:59:21', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 'Sona Mahi', 'Sona', 'Mahi', 'mahhisona001@gmail.com', 'https://lh3.googleusercontent.com/a/AATXAJycCe2042665_S24xDGv0lFf-tRZqUZW3Fm5beG=s96-c', 'url', NULL, NULL, NULL, '3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'google', '8sKKGAJh8Hd17y3S37VnpXucgcn2', NULL, NULL, '2022-04-21 13:01:28', '$2y$10$i45ynstVTRjQNeak8DMV4OP572j28ZSM19lhiFBs4lcwEULoUPWDK', NULL, 0, '2022-04-21 13:01:28', '2022-04-21 13:01:28', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 'Asdf ', 'Asdf', NULL, 'thasd@gmail.com', NULL, 'local', '9863258147', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-21 13:34:09', '$2y$10$jv2YpAW83x0xBSsD7SFBiO97LuRk1HWYAnTj5Ph1U80l.nfmlCHFC', NULL, 1, '2022-04-21 13:34:09', '2022-04-25 13:08:25', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 'Khalifa Mohamed', 'Khalifa', 'Mohamed', 'khalifa@hotmail.com', 'APR2022/1651235960-user-image.png', 'local', '8058564636', NULL, NULL, '3', 'Male', NULL, NULL, '+91', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-25 14:05:44', '$2y$10$awDy1YbB1MCE/ERJABEcL.xUnevqRqTuwI71./MIVtOhWa6EpMS0W', NULL, 1, '2022-04-25 14:05:44', '2022-05-18 08:34:58', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 'Mukesh Malik', 'Mukesh', 'Malik', 'malik@ynail.com', NULL, 'local', '941001535', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-29 13:12:25', '$2y$10$zRLMzA7y4pk7ay7XbbxJj.0IV0n0KN8AhNk8d7fplBft5AX/XrAJm', NULL, 1, '2022-04-29 13:12:25', '2022-04-29 13:12:44', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(99, '', NULL, NULL, NULL, NULL, 'local', '9588240576', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-02 06:58:44', '$2y$10$y3/.WJ6mEsQUQRWrw0up8.FdqspKQuq.LOIkJJh6UfEzzSLwWMk4e', NULL, 0, '2022-05-02 06:58:44', '2022-05-02 06:58:44', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(100, 'Jugal Kishor Suthar', 'Jugal', 'Kishor Suthar', 'sutharjugalkishor@gmail.com', 'https://lh3.googleusercontent.com/a-/AOh14GhZnODwdYVUyfGcA4GVb0d9E6BCDa7Yd1z2ZWkrGg=s96-c', 'url', NULL, NULL, NULL, '3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'google', 'hlH9EOWzUGQzAo9xogKF8vwRjvi2', NULL, NULL, '2022-05-03 07:49:59', '$2y$10$s7Nxl7B01sIz443.aGuZdOW2qbZQpjNOXzOSQeM60SFzoMRvr./e6', NULL, 0, '2022-05-03 07:49:59', '2022-05-03 07:49:59', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 'Hello Dost', 'Hello', 'Dost', 'hello@tgf.com', NULL, 'local', '963852741', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-03 10:14:01', '$2y$10$Ak3/scfhrtsWGOXX3alfD.TprvZ2JgBxLUGp5d2BvH5GVt2PDLtp6', NULL, 1, '2022-05-03 10:14:01', '2022-05-03 10:14:27', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(102, 'Mukesh Malik', 'Mukesh', 'Malik', 'delivery@inventcolabs.com', NULL, 'local', '96325874', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'apple', 'm2pj44l3OOY1saN00O8xR7bfx9I2', NULL, NULL, '2022-05-03 11:50:59', '$2y$10$8Vbg3X8lrE2J5z0OVdHiU.FodwlRK2USofYlHWQm371co4UxN.NuS', NULL, 1, '2022-05-03 11:50:59', '2022-05-26 12:43:21', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 'Praveen ', 'Praveen', NULL, 'praveen@gmail.com', NULL, 'local', '9001869090', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-03 12:01:42', '$2y$10$SCcre1HdRCtkSSl52QOiJOJzRVNeAK0SjnuIgBVKfH/Ec8/oKnkqC', NULL, 1, '2022-05-03 12:01:42', '2022-05-03 12:34:00', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(104, '', NULL, NULL, NULL, NULL, 'local', '9672512896', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-05 09:00:10', '$2y$10$rq4y03WgUMrM1vSDo/C0TORpgvd77/KHYQ6pf8rlaJdgBUFHXY//u', NULL, 0, '2022-05-05 09:00:10', '2022-05-05 09:00:10', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(105, '', NULL, NULL, NULL, NULL, 'local', '8058564636', NULL, NULL, '3', NULL, NULL, NULL, '+975', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-05 13:13:08', '$2y$10$v6QBhdZjhvFLH5K00p3u7uV7CC2rrJMdCMpgtxmuM15mWY5FLxoIm', NULL, 0, '2022-05-05 13:13:08', '2022-05-05 13:13:08', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, '', NULL, NULL, NULL, NULL, 'local', '5555555555', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-05 13:32:47', '$2y$10$VU.M3AqYj6K.8YyGSTs40.d0bWBQbh8ClcxzAerGAMWtoX/gkTfr6', NULL, 0, '2022-05-05 13:32:47', '2022-05-05 13:32:47', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(107, '', NULL, NULL, NULL, NULL, 'local', '123456789', NULL, NULL, '3', NULL, NULL, NULL, '+974', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-08 21:41:09', '$2y$10$dOq6c89xgQMO0qFypIdv3O7YwtkGO.FLwYat6yZmi/ZdKJY4yc5KW', NULL, 0, '2022-05-08 21:41:09', '2022-05-08 21:41:09', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 'sandeep', 'sandeep', NULL, 'sandeep@oo.com', 'MAY2022/1652162524-user.png', 'local', '78784578', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-10 06:02:04', '', NULL, 0, '2022-05-10 06:02:04', '2022-05-10 09:40:44', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(109, 'Test ', 'Test', NULL, 'testingt@gmail.com', NULL, 'local', '9876543219', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-10 09:44:02', '$2y$10$hfA9kpkBElg4hPUICJi06ew8XZkUwpG2APSCFwnlRLYC/mTkL2mUG', NULL, 1, '2022-05-10 09:44:02', '2022-05-11 07:02:16', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 'Qwe Q', 'Qwe', 'Q', NULL, NULL, 'local', '91827364', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-11 10:57:55', '$2y$10$.2GR5JNNFHAEOkPz5mlz.OzVMqvuFChkEjoFxOmlkJHkkZ2M1rPUq', NULL, 1, '2022-05-11 10:57:55', '2022-05-11 10:58:06', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 'Qwe ', 'Qwe', NULL, NULL, NULL, 'local', '98127634', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-11 11:14:45', '$2y$10$o5OcrFn/77BGJPjByQW7ueQ8o6LGU6hdZ7h/x2VExsCckqRRP2Ske', NULL, 1, '2022-05-11 11:14:45', '2022-05-11 11:14:55', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(112, '', NULL, NULL, NULL, NULL, 'local', '7339932700', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-20 14:32:25', '$2y$10$wUGeZxJcCdXzXcv9n7zZpO5.t77TwzdLIkHoLAssQUpsNDHBvJO/6', NULL, 0, '2022-05-20 14:32:25', '2022-05-20 14:32:25', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(113, '', NULL, NULL, NULL, NULL, 'local', '7062258117', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-20 15:26:39', '$2y$10$JSTb.Cm00Ija8O9UsryLeetapZdIWfg10j5oFNLR7MIAhJgoN2hZ.', NULL, 0, '2022-05-20 15:26:39', '2022-05-20 15:26:39', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(114, '', NULL, NULL, NULL, NULL, 'local', '1231231230', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-23 10:15:43', '$2y$10$Mf8cnHZtPGVi53VpUYhPR.fDAVzyK/4P4hSy2JSmaFzPxhp.CFXKi', NULL, 0, '2022-05-23 10:15:43', '2022-05-23 10:15:43', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(115, '', NULL, NULL, NULL, NULL, 'local', '7339932800', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-24 14:42:03', '$2y$10$iXAwn34eFx2Tl1XOeF21butLOvRk4Us4.wur6jUNWgsjXIS2ZHO.m', NULL, 0, '2022-05-24 14:42:03', '2022-05-24 14:42:03', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(116, '', NULL, NULL, NULL, NULL, 'local', '9413560787', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-25 05:32:59', '$2y$10$ITi4rWfb/DXCj4NE/9kfsekIlsEpyPUIya4RmpCwy2EK78sbx9ZEO', NULL, 0, '2022-05-25 05:32:59', '2022-05-25 05:32:59', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(117, '', NULL, NULL, NULL, NULL, 'local', '7062258111', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-25 09:29:11', '$2y$10$7Olvjwbbk30PuiNmTIH5d.vUZSRpTf5.DH6HfliaxflPF0szd46hu', NULL, 0, '2022-05-25 09:29:11', '2022-05-25 09:29:11', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(118, '', NULL, NULL, NULL, NULL, 'local', '7062258112', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-25 10:32:44', '$2y$10$4TmiGZd0SplB4TqhcV/1DejzucYthtxvHhnCwHOUBCa3Hz6jxwIkO', NULL, 0, '2022-05-25 10:32:44', '2022-05-25 10:32:44', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(119, '', NULL, NULL, NULL, NULL, 'local', '9462761961', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-26 03:30:25', '$2y$10$BLpyKvEnEIMSIdRv9WAIbOswJby.WgReaM0367YO/IFX7v10FjoMi', NULL, 0, '2022-05-26 03:30:25', '2022-05-26 03:30:25', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(120, '', NULL, NULL, NULL, NULL, 'local', '8949919899', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-26 04:41:08', '$2y$10$QrTQq2Z/yLLcj4LF0WrzUeUIJSzABUJk3BDJRchLLXmJti426lgd6', NULL, 0, '2022-05-26 04:41:08', '2022-05-26 04:41:08', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(121, '', NULL, NULL, NULL, NULL, 'local', '7062258222', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-30 11:10:24', '$2y$10$AZ8LwfLMtOy0Hndua5qbtOoHhitGFa87n4Zgt9w47g5CSMbxyilZW', NULL, 0, '2022-05-30 11:10:24', '2022-05-30 11:10:24', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(122, '', NULL, NULL, NULL, NULL, 'local', '6345434353', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-30 11:14:01', '$2y$10$Iwz35l347PuFKcKQr3/8t.oInC3k50jdD8QOyv/KtIYVlD1kijbbG', NULL, 0, '2022-05-30 11:14:01', '2022-05-30 11:14:01', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(123, '', NULL, NULL, NULL, NULL, 'local', '8814062142', NULL, NULL, '3', NULL, NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-30 11:18:33', '$2y$10$LpB22A3.kKeVJyY28jKQHu/kgF1RQefwHM2k8zYzQMitfNV7lpnra', NULL, 0, '2022-05-30 11:18:33', '2022-05-30 11:18:33', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `users` (`id`, `name`, `first_name`, `last_name`, `email`, `image`, `image_type`, `mobile`, `dob`, `age`, `type`, `gender`, `marital_status`, `address`, `country_code`, `referral_code`, `share_code`, `latitude`, `longitude`, `genres`, `food_license`, `license_number`, `license_image`, `social_type`, `social_id`, `bio`, `points`, `email_verified_at`, `password`, `remember_token`, `is_profile_updated`, `created_at`, `updated_at`, `deleted_at`, `new_email`, `new_email_token`, `status`, `parent_chef_id`, `restaurant_id`, `gift_user_id`, `gift_access_key`, `gift_secret_key`, `main_access_key`, `main_secret_key`) VALUES
(124, 'Malik ', 'Malik', NULL, 'mass@gmail.com', 'MAY2022/1653911231-user-image.jpg', 'local', '967531668', NULL, NULL, '3', 'Male', NULL, NULL, '+971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-30 11:42:24', '$2y$10$uVxmMjrri0VnO59cT57ZZOsIslf8EEUTq13jZTmiB5M5QpHECzSQi', NULL, 1, '2022-05-30 11:42:24', '2022-05-30 11:47:11', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_address`
--

CREATE TABLE `user_address` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `address` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `building_number` varchar(255) DEFAULT NULL,
  `building_name` varchar(255) DEFAULT NULL,
  `address_type` varchar(255) DEFAULT NULL,
  `is_defauld_address` tinyint NOT NULL DEFAULT '0',
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `user_bank_detail`
--

CREATE TABLE `user_bank_detail` (
  `id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_address` text,
  `bank_code` varchar(255) DEFAULT NULL,
  `account_number` bigint DEFAULT NULL,
  `account_type` varchar(255) DEFAULT NULL,
  `account_holder_name` varchar(255) DEFAULT NULL,
  `passbook_image` text,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_bank_detail`
--

INSERT INTO `user_bank_detail` (`id`, `user_id`, `bank_name`, `bank_address`, `bank_code`, `account_number`, `account_type`, `account_holder_name`, `passbook_image`, `status`, `created_at`, `updated_at`) VALUES
(1, 46, 'state bank', 'jaipur', 'sss333d', 61142271259, 'saving', 'munesh saini', 'MAY2022/1653055331-bank_detail.png', 1, '2022-05-20 14:02:11', '2022-05-20 14:02:17'),
(2, 3, 'sads', 'sdsdsdsd', 'sdadsadsd', 6546545645, 'sadsdd', '231321321', NULL, 1, '2022-05-30 09:33:18', '2022-05-30 09:33:18');

-- --------------------------------------------------------

--
-- Table structure for table `user_car`
--

CREATE TABLE `user_car` (
  `id` bigint NOT NULL,
  `user_id` int DEFAULT NULL,
  `car_color` varchar(255) DEFAULT NULL,
  `car_number` varchar(255) DEFAULT NULL,
  `car_brand` varchar(255) DEFAULT NULL,
  `is_defauld_car` tinyint DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `user_devices`
--

CREATE TABLE `user_devices` (
  `id` int NOT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `device_type` enum('Android','IOS') CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Android',
  `device_token` text CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_devices`
--

INSERT INTO `user_devices` (`id`, `user_id`, `device_type`, `device_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 97, 'IOS', 'cN2wmJzSD0S9tImLKdon6R:APA91bHcSpgKf-5orA9kKOnPE3plRLF6DP2sZ1ZAPguI1tsEkfWJJlhVhzs-oUL_lBbJF1ZED5zW3T_I2RsDtfngkI5dQV7w2NjtizmfujnTN39t3Uskvs-uZsB_4O8Yq2DnM5hdLAvk', '2022-05-02 05:44:25', '2022-05-05 13:42:49', NULL),
(2, 91, 'Android', 'fG45t1gqQcKYh34tYpZCNS:APA91bEuYZkez51rXvmobeyuxT487NNeOOxHhJI5FR-CD6k-Fd2o2bF4098T7jQJzShjXlpZbH_2uxkqFsQn1vykolnSHHjV1i6N5wsXS_BeIov-8SfBVbbn0AHkC2VGtH8s2TW3wK16', '2022-05-02 06:02:02', '2022-05-13 10:19:55', NULL),
(3, 99, 'Android', 'eOZuM_tqTiGgoA8DYB248Z:APA91bFey0v3AaMqh7u4lgvsIOYjRE_EMAruI-cBmnBw0MyM9k7VICelvhYQbX8MHWDwjbtAn4XwpN9W9t0N3fcUyq-fDcYME19HZ3V0h18xAtPCMis1tJ_nE6Chn2U--4RE1UtBD3i8', '2022-05-02 06:58:45', '2022-05-02 06:58:45', NULL),
(4, 80, 'Android', 'cRtJr3b2SSSUKaBuPFUe6Z:APA91bGDqLEfhdYhUHi5aK4N_1DvNuWWXbXVUY_jL5PbVJa-sJ7LeTuICNs75uT11cDUXZLtvh2ZEEBZoUR67b2tNMwbr2djFSNi3flFCYm_kGjRa9nZv3iAhvPZy1nvE29M6qKK53O1', '2022-05-03 05:50:23', '2022-05-03 09:43:50', NULL),
(5, 101, 'Android', 'cRtJr3b2SSSUKaBuPFUe6Z:APA91bGDqLEfhdYhUHi5aK4N_1DvNuWWXbXVUY_jL5PbVJa-sJ7LeTuICNs75uT11cDUXZLtvh2ZEEBZoUR67b2tNMwbr2djFSNi3flFCYm_kGjRa9nZv3iAhvPZy1nvE29M6qKK53O1', '2022-05-03 10:14:01', '2022-05-03 10:14:01', NULL),
(6, 103, 'IOS', 'et5eYmaK6UYkpqdpPZQdNr:APA91bHppzDqRSRNpESAWM1i17i7aQQg1qk2zraUUOFX6xAsp-D7TpzqPr1K4mgt_8PDGFq0AVYc9at4sgWA7Xy_66ReH_VD37Y8IK7FXkkNcD7HnyMAwESgj2-XKt2eOLuaahHrBj7n', '2022-05-03 12:01:42', '2022-05-03 12:01:42', NULL),
(7, 87, 'IOS', 'eowuFzy6ukomvCYmgEDvGt:APA91bH3j2dNxg4Zu7t9rZ5Z3Fl9D5_CbcGuMn6FEJT7f7hcr5oNzuWL9E-SE9XDLSgSWBTEgYqEJ9wBk1imIDjMZ8l_OOnqqARvm0hpxbpN3oVLzWcjGRSo64UX5j_APK-LwP3dN7As', '2022-05-04 09:54:38', '2022-05-30 10:45:46', NULL),
(8, 104, 'IOS', 'fbJRs_09XUsmu5e6h261pa:APA91bGz-p4cJ6aIpYFv2Zzhn36awQtEWqu6jm8Tth0_3kpXlKqEiQrox929rRvwpfynQGEKbrhlI95icM238MyyVqnu1ylWqVweJkNU4XUAZQOEeJQnVD-XVCsoiY3eCkDQMN0KxXHa', '2022-05-05 09:00:10', '2022-05-05 09:00:10', NULL),
(9, 106, 'IOS', 'cMAaCZby70ioqd-nDbgswD:APA91bGw28ZEGQXP6dBb-ZMA-MzoYCmcvdJFN7DsPJ3kLZV_OTKRj8_t6I7vdCJePjdWzcvZ8xvmNnd3MtKbWERoVgws8zvj0a2e27b31juvCcrkipwMsI7JkPvrK8F4EM0D1TRXUHPz', '2022-05-05 13:32:47', '2022-05-05 13:32:47', NULL),
(10, 109, 'Android', 'dKDBrl-ARJCVqJb4HGIZFd:APA91bFdV7CTTBclxVoDsBkM2AD843Yj74oRZE-3j6anFm8Szefbo8DoAKzeDvNy4OnBtbsACEfqN_qW1y3eTSWpScSn2tBA0uKOhKCKfKrXDMT5iVBLOBGVuzRLXBpiD0lmcBfj67iX', '2022-05-10 09:44:02', '2022-05-10 13:26:18', NULL),
(11, 110, 'Android', 'ePl22Tg0TC65puEIgTvy4o:APA91bEPKs8MFyQArNdplak6TWXXIrBooRwPq9K7DdxeNuhuiERgK01tg5-_ITjA-bkBDzAdo026rEuA3rjeuZNKyjAmYh-UhgrMTxeZCgudaWDKmu9_Kdvhcg6M-r5FcQ9FeKtUY6Ie', '2022-05-11 10:57:55', '2022-05-11 10:57:55', NULL),
(12, 111, 'Android', 'ePl22Tg0TC65puEIgTvy4o:APA91bEPKs8MFyQArNdplak6TWXXIrBooRwPq9K7DdxeNuhuiERgK01tg5-_ITjA-bkBDzAdo026rEuA3rjeuZNKyjAmYh-UhgrMTxeZCgudaWDKmu9_Kdvhcg6M-r5FcQ9FeKtUY6Ie', '2022-05-11 11:14:45', '2022-05-11 11:14:45', NULL),
(13, 112, 'Android', 'eaKteWrqSJuQu0bXww3r8D:APA91bHi0iPZgHLHQ6AacK0wsXCUP0pRonu96Weo_OO2S6f4DP9MzhzKpyEiFbgob-J6dvnXtF7Owy7y-kxvqFccmkwwYTt6xt2Wa2u5fTfZk-ZvkBnMoAeavBGEEDY61VGR_qSdceKW', '2022-05-20 14:32:25', '2022-05-20 14:32:25', NULL),
(14, 113, 'Android', 'c4fzVfJQRVuSEYxzuwbr_J:APA91bE2_xC8ost-ho3Vl-jAu-Cr1mqE_M5hK1UrBeed6wEvegVOws7AzbPzRjxmERwvjTNCUj1esH8cQH7E9ksCDztytvjsNDD50TePPCE3oAadNj2lUGzVqvGQgV612naSVgi-9HD-', '2022-05-20 15:26:39', '2022-05-20 15:26:39', NULL),
(15, 114, 'Android', 'cQEXZ4U5RO64o6rt7MpeNp:APA91bGC1uMAhCwaBi3l_YMB7HVJndscsyUdP2qfgxAqbbjkJfEARH-8xjEd8vNki8zjPCnROM_o1BhNr9pAp9hTxm5LF3TylQ0HZmCtSfUDlE1gigMM7v1pyv3L2o4-3b4QNqq9noHA', '2022-05-23 10:15:43', '2022-05-23 10:24:13', NULL),
(16, 115, 'IOS', 'dknNfbyJAUM2ojdb6ax9Lm:APA91bHvlfcFfjOnbrSGLJOAc-Dg2cTFSWpO2IorAUktJhVzNp9BO5K550jzsWDkBo-48HSyfx6obRAw6n2yvi7mY0OCcxGWmWxvgeo6WWI63W3qbkUBCl8okYlzSfcdoeKcGHEHnO4Q', '2022-05-24 14:42:03', '2022-05-24 14:42:03', NULL),
(17, 116, 'IOS', 'dcT4cgmHyEHBoIZm4d_L5q:APA91bGMBEpYlRl15F-uuVr1OjMVkCNNuOY-Gl3CbjINhBrAZEqeCbDM5yFZJXV5MmdpTd8IX0U_FiwInO05jI2mWmZgfmaxfj8Tzk3bCZMBsTTbw0uQHCyDR8dPCVhdeIicSeHkCCMU', '2022-05-25 05:32:59', '2022-05-25 05:32:59', NULL),
(18, 117, 'IOS', 'clEKOcHdqUh9nE88e2pXam:APA91bEqbcgJImOMMgTxeoHziBL_GS9G5UNJCYLR33A5kc3f7ZkxScX5jezDam4UbWDGQ9N58XmYz3pFrKT2MC0CAgWdQeFB5v6MNncgZnxILAcgInD7A3SmFXhpMxo-wNUs9obnD2Ex', '2022-05-25 09:29:11', '2022-05-25 09:29:11', NULL),
(19, 118, 'IOS', 'fblb4KCeq04VikRKDhkWtF:APA91bFjXnC1VuG8L6h_qIZNjQ-z_Vf3P_Zq67cmDqVU9DOPLV5SYhOb2SW_ZNzWF_cqzkah9TrPpgdY8-6hfTM01e9HsievdEezmruSo9KTKRIUAjRV5Y8Qgbi0z991G7qNmzMuW3aw', '2022-05-25 10:32:45', '2022-05-25 11:37:11', NULL),
(20, 119, 'Android', 'eVyMKrhdTM2--uUA3sx7r1:APA91bG5Fy2S62q-u49ckCMXNv68_EZeTwByJK3WcyLxSnobXjQXBlLC_aNUuzAoRgJTyatfQnDZVRUfC0-_CzGPHvOm9cZG5D-3CIUG21hkV4_ZEAJ3ptRsTYx7eB7gweapZjcqEroQ', '2022-05-26 03:30:26', '2022-05-26 03:30:26', NULL),
(21, 120, 'IOS', 'c12F1SEdtkb3signnMEg6F:APA91bGplbs8BeSwbkhTLA346467E8R4F0MphH9JPID0ePS4MJOxorpGRGGfdnjH9xb-1keMXNTl1eXWZENiuWzS_sNV4mylSEwmwI4ctOU0fuIG5LyxnS93QFO2patrpXvB7QBJ5ZTa', '2022-05-26 04:41:08', '2022-05-26 04:41:08', NULL),
(22, 49, 'IOS', 'eowuFzy6ukomvCYmgEDvGt:APA91bH3j2dNxg4Zu7t9rZ5Z3Fl9D5_CbcGuMn6FEJT7f7hcr5oNzuWL9E-SE9XDLSgSWBTEgYqEJ9wBk1imIDjMZ8l_OOnqqARvm0hpxbpN3oVLzWcjGRSo64UX5j_APK-LwP3dN7As', '2022-05-30 06:01:12', '2022-05-30 10:31:36', NULL),
(23, 121, 'IOS', 'cNnfDN8zwkVHhD70X688pt:APA91bF3er75P7tVIkMuhiBNkqGtcIPO6gnFfywIL_Naijm4jhbPfhhHduNsAoJpWNxjXjS6jDrDp_rdEYo0hGTwRmALh-hR5zwW6RCfIZVJfFHeDJhoUBsCSkXKyF8kti3WtJGA2LWt', '2022-05-30 11:10:24', '2022-05-30 11:10:24', NULL),
(24, 122, 'IOS', 'cNnfDN8zwkVHhD70X688pt:APA91bF3er75P7tVIkMuhiBNkqGtcIPO6gnFfywIL_Naijm4jhbPfhhHduNsAoJpWNxjXjS6jDrDp_rdEYo0hGTwRmALh-hR5zwW6RCfIZVJfFHeDJhoUBsCSkXKyF8kti3WtJGA2LWt', '2022-05-30 11:14:01', '2022-05-30 11:14:01', NULL),
(25, 123, 'IOS', 'eowuFzy6ukomvCYmgEDvGt:APA91bH3j2dNxg4Zu7t9rZ5Z3Fl9D5_CbcGuMn6FEJT7f7hcr5oNzuWL9E-SE9XDLSgSWBTEgYqEJ9wBk1imIDjMZ8l_OOnqqARvm0hpxbpN3oVLzWcjGRSo64UX5j_APK-LwP3dN7As', '2022-05-30 11:18:33', '2022-05-30 11:18:33', NULL),
(26, 124, 'IOS', 'eowuFzy6ukomvCYmgEDvGt:APA91bH3j2dNxg4Zu7t9rZ5Z3Fl9D5_CbcGuMn6FEJT7f7hcr5oNzuWL9E-SE9XDLSgSWBTEgYqEJ9wBk1imIDjMZ8l_OOnqqARvm0hpxbpN3oVLzWcjGRSo64UX5j_APK-LwP3dN7As', '2022-05-30 11:42:24', '2022-05-30 11:42:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_otps`
--

CREATE TABLE `user_otps` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `otp` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user_otps`
--

INSERT INTO `user_otps` (`id`, `user_id`, `country_code`, `mobile`, `otp`, `created_at`, `updated_at`) VALUES
(1, NULL, '91', '9999999966', '8045', '2022-03-14 09:07:42', '2022-03-15 13:23:46'),
(2, NULL, '91', '1234567890', '0189', '2022-03-14 10:19:36', '2022-03-24 04:10:38'),
(3, NULL, '971', '1234567890', '3782', '2022-03-14 11:14:20', '2022-05-26 08:48:32'),
(4, NULL, '971', '1234657890', '1920', '2022-03-14 11:25:28', '2022-03-14 11:25:54'),
(5, NULL, '971', '3216549870', '7398', '2022-03-14 13:49:06', '2022-03-14 13:49:06'),
(6, NULL, '971', '1472583690', '0379', '2022-03-14 13:54:38', '2022-03-14 13:54:38'),
(7, NULL, '91', '99999999663', '7905', '2022-03-15 13:40:38', '2022-03-15 13:40:38'),
(8, NULL, '971', '8523697410', '6842', '2022-03-16 06:13:07', '2022-03-16 06:13:07'),
(9, NULL, '971', '1478523960', '7524', '2022-03-16 06:13:41', '2022-03-16 06:13:41'),
(10, NULL, '971', '3214569870', '6751', '2022-03-16 06:14:49', '2022-03-23 05:05:29'),
(11, NULL, '971', '1234567891', '3150', '2022-03-16 06:23:23', '2022-03-26 07:04:59'),
(12, NULL, '91', '9999999963', '6320', '2022-03-16 06:45:17', '2022-03-16 06:45:17'),
(13, NULL, '971', '147852369', '8659', '2022-03-16 06:51:14', '2022-03-16 06:51:14'),
(14, NULL, '971', '1478523690', '0547', '2022-03-16 07:22:11', '2022-03-16 08:35:14'),
(15, NULL, '91', '12345678900', '0825', '2022-03-16 07:57:06', '2022-03-16 07:57:06'),
(16, NULL, '971', '1236987450', '6049', '2022-03-16 08:57:25', '2022-03-16 08:57:25'),
(17, NULL, '971', '1239687150', '1649', '2022-03-16 08:58:14', '2022-03-16 08:58:14'),
(18, NULL, '971', '9632587410', '1279', '2022-03-16 09:09:20', '2022-03-16 09:09:20'),
(19, NULL, '971', '1122334477', '4082', '2022-03-16 09:35:56', '2022-03-16 09:35:56'),
(20, NULL, '971', '1234567892', '8502', '2022-03-16 10:14:34', '2022-03-16 10:14:34'),
(21, NULL, '971', '1122334455', '5034', '2022-03-16 10:34:42', '2022-03-16 10:34:42'),
(22, NULL, '971', '1234567893', '5240', '2022-03-16 10:58:47', '2022-03-16 10:58:47'),
(23, NULL, '971', '3692581470', '1908', '2022-03-16 11:34:01', '2022-03-16 11:34:01'),
(24, NULL, '971', '1452369870', '5431', '2022-03-16 12:53:56', '2022-03-16 12:53:56'),
(25, NULL, '971', '123459580', '7351', '2022-03-21 07:28:34', '2022-03-21 07:28:34'),
(26, NULL, '971', '1236987145', '7695', '2022-03-21 08:39:35', '2022-03-21 08:39:35'),
(27, NULL, '971', '123456789', '8196', '2022-03-22 06:42:10', '2022-05-25 13:25:02'),
(28, NULL, '971', '11223344', '5901', '2022-03-22 06:43:35', '2022-03-22 06:43:35'),
(29, NULL, '971', '9876543210', '6023', '2022-03-22 09:07:14', '2022-03-22 09:07:14'),
(30, NULL, '971', '6464646434', '7104', '2022-03-22 10:01:17', '2022-03-22 10:01:17'),
(31, NULL, '971', '53333112', '4013', '2022-03-22 10:19:24', '2022-03-22 10:19:24'),
(32, NULL, '971', '6,9494994', '4590', '2022-03-22 10:22:05', '2022-03-22 10:22:05'),
(33, NULL, '971', '69,356646', '7095', '2022-03-22 10:22:33', '2022-03-22 10:22:33'),
(34, NULL, '971', '1236547890', '8791', '2022-03-22 11:06:05', '2022-03-22 11:06:05'),
(35, NULL, '91', '1234567668900', '9478', '2022-03-22 11:56:54', '2022-03-22 11:56:54'),
(36, NULL, '971', '1325555000', '6712', '2022-03-22 13:08:44', '2022-03-22 13:08:44'),
(37, NULL, '971', '1234567899', '5829', '2022-03-23 05:40:45', '2022-03-23 05:40:45'),
(38, NULL, '971', '1234567877', '6237', '2022-03-23 06:12:10', '2022-03-23 06:12:10'),
(39, NULL, '91', '963258741', '1320', '2022-03-24 05:27:08', '2022-03-24 05:57:37'),
(40, NULL, '91', '7062258117', '2814', '2022-03-24 05:59:13', '2022-03-24 05:59:13'),
(41, NULL, 'AE', '96321512', '1274', '2022-03-24 06:00:36', '2022-03-24 06:00:36'),
(42, NULL, 'IN', '96321512', '3692', '2022-03-24 06:00:52', '2022-03-24 06:00:52'),
(43, NULL, 'IN', '963258741', '0479', '2022-03-24 06:01:40', '2022-03-24 06:01:59'),
(44, NULL, 'AE', '963258741', '9486', '2022-03-24 06:02:50', '2022-03-28 12:42:15'),
(45, NULL, 'AE', '7062258117', '1526', '2022-03-24 06:03:53', '2022-03-24 06:03:53'),
(46, NULL, 'AE', '3699999999', '2781', '2022-03-24 06:06:49', '2022-03-24 06:06:49'),
(47, NULL, 'AE', '25896321', '5721', '2022-03-24 06:07:02', '2022-03-24 06:07:02'),
(48, NULL, 'undefined', 'undefined', '2107', '2022-03-24 06:07:02', '2022-03-24 06:44:46'),
(49, NULL, 'AE', '6513111332', '5736', '2022-03-24 06:07:59', '2022-03-24 06:07:59'),
(50, NULL, 'AE', '9874522323', '9103', '2022-03-24 06:08:35', '2022-03-24 06:12:00'),
(51, NULL, 'AE', '96733737', '6351', '2022-03-24 06:14:21', '2022-03-24 06:14:21'),
(52, NULL, 'AE', '555555555', '5082', '2022-03-24 06:15:05', '2022-03-24 06:15:05'),
(53, NULL, 'AE', '8945613123', '3145', '2022-03-24 06:16:02', '2022-03-24 06:16:02'),
(54, NULL, 'AE', '8645615313', '3467', '2022-03-24 06:23:19', '2022-03-24 06:23:19'),
(55, NULL, 'AE', '8974616651', '6819', '2022-03-24 06:25:16', '2022-03-24 06:25:16'),
(56, NULL, 'AE', '8465531353', '2578', '2022-03-24 06:25:45', '2022-03-24 06:25:45'),
(57, NULL, 'AE', '6845653133', '9518', '2022-03-24 06:26:09', '2022-03-24 06:27:00'),
(58, NULL, '91', '954945455', '9305', '2022-03-24 06:33:25', '2022-03-24 06:35:05'),
(59, NULL, '91', '9549458465455', '1408', '2022-03-24 06:35:14', '2022-03-24 06:36:26'),
(60, NULL, 'AE', '9874651531', '3184', '2022-03-24 06:42:46', '2022-03-24 06:42:46'),
(61, NULL, 'AE', '9848665561', '5623', '2022-03-24 06:43:13', '2022-03-24 06:43:13'),
(62, NULL, 'AE', '8465135335', '9315', '2022-03-24 06:43:31', '2022-03-24 06:43:31'),
(63, NULL, 'AE', '222222222', '4569', '2022-03-24 06:43:56', '2022-03-24 06:44:18'),
(64, NULL, 'AE', '9845643145', '5709', '2022-03-24 06:44:46', '2022-03-24 06:44:46'),
(65, NULL, 'AE', '8456523123', '4701', '2022-03-24 06:45:19', '2022-03-24 06:45:19'),
(66, NULL, 'AE', '8945615321', '8356', '2022-03-24 06:46:11', '2022-03-24 09:21:41'),
(67, NULL, 'AE', '9486153151', '1984', '2022-03-24 09:22:24', '2022-03-24 09:22:24'),
(68, NULL, '971', '7062258117', '1895', '2022-03-25 13:00:41', '2022-05-26 11:35:57'),
(69, NULL, '971', '7062258118', '5263', '2022-03-25 13:02:55', '2022-03-25 13:02:55'),
(70, NULL, '971', '7012345690', '5638', '2022-03-25 13:04:06', '2022-03-25 13:04:06'),
(71, NULL, '971', '6464646666', '9327', '2022-03-25 13:05:18', '2022-03-25 13:05:18'),
(72, NULL, '971', '3221546788', '3965', '2022-03-25 13:06:57', '2022-03-25 13:06:57'),
(73, NULL, '971', '1239876540', '7809', '2022-03-25 13:19:50', '2022-03-25 13:19:50'),
(74, NULL, '971', '4455667788', '3672', '2022-03-25 13:22:59', '2022-03-25 13:22:59'),
(75, NULL, '971', '4433669908', '0254', '2022-03-25 13:26:27', '2022-03-25 13:26:27'),
(76, NULL, '971', '3366998855', '8620', '2022-03-25 13:26:55', '2022-03-25 13:26:55'),
(77, NULL, '971', '8898458788', '1407', '2022-03-26 05:34:31', '2022-03-26 05:34:31'),
(78, NULL, '971', '8787878787', '1658', '2022-03-26 05:35:23', '2022-03-26 05:35:23'),
(79, NULL, '971', '5454840787', '4732', '2022-03-26 05:36:27', '2022-03-26 07:05:59'),
(80, NULL, '971', '235659854', '0497', '2022-03-28 05:32:46', '2022-03-28 05:32:46'),
(81, NULL, '971', '3265649790', '7490', '2022-03-28 05:40:27', '2022-03-28 05:40:27'),
(82, NULL, '971', '396282850', '8195', '2022-03-28 05:43:54', '2022-03-28 05:43:54'),
(83, NULL, '971', '7062225811', '0263', '2022-03-28 05:45:39', '2022-03-28 05:45:39'),
(84, NULL, '971', '1234567863', '6987', '2022-03-28 05:46:00', '2022-03-28 05:46:00'),
(85, NULL, '971', '74248560', '3628', '2022-03-28 05:48:07', '2022-03-28 05:48:07'),
(86, NULL, '971', '58387158850', '9104', '2022-03-28 05:49:30', '2022-03-28 05:49:30'),
(87, NULL, '971', '358357744460', '9483', '2022-03-28 05:49:49', '2022-03-28 06:02:43'),
(88, NULL, '971', '1234565224', '8370', '2022-03-28 06:40:06', '2022-03-28 06:40:06'),
(89, NULL, '971', '157523956', '2150', '2022-03-28 06:40:37', '2022-03-28 06:40:37'),
(90, NULL, '971', '14785223', '1735', '2022-03-28 06:41:18', '2022-03-28 06:41:18'),
(91, NULL, '971', '9632587865', '9604', '2022-03-28 08:39:06', '2022-03-28 08:39:06'),
(92, NULL, '971', '7062258633', '2136', '2022-03-28 08:46:19', '2022-03-28 08:46:19'),
(93, NULL, 'AE', '6865626265', '0915', '2022-03-28 09:22:23', '2022-03-28 09:22:23'),
(94, NULL, 'AE', '9761661616', '5079', '2022-03-28 09:23:16', '2022-03-28 09:23:16'),
(95, NULL, 'AE', '8486515665', '4076', '2022-03-28 09:56:17', '2022-03-28 09:56:17'),
(96, NULL, 'AE', '987456321', '8705', '2022-03-28 09:57:04', '2022-03-28 11:42:34'),
(97, NULL, 'AE', '5655556655', '1657', '2022-03-28 11:08:19', '2022-03-28 11:08:19'),
(98, NULL, 'AE', '6515234655', '2095', '2022-03-28 11:53:37', '2022-03-28 11:53:37'),
(99, NULL, 'AE', '5651458646', '6107', '2022-03-28 11:53:57', '2022-03-28 11:53:57'),
(100, NULL, '971', '123456698', '2019', '2022-03-28 13:16:50', '2022-03-28 13:16:50'),
(101, NULL, '971', '122556699', '6173', '2022-03-29 06:11:17', '2022-03-29 06:11:17'),
(102, NULL, '971', '336699885', '7690', '2022-03-30 06:21:56', '2022-03-30 06:27:05'),
(103, NULL, '971', '4818144448', '9675', '2022-03-30 10:25:16', '2022-03-30 10:25:16'),
(104, NULL, '971', '123456709', '0826', '2022-04-01 06:23:37', '2022-04-01 06:23:37'),
(105, NULL, '971', '1234458898', '7528', '2022-04-01 11:35:20', '2022-04-01 11:35:20'),
(106, NULL, '971', '5222000000', '4659', '2022-04-05 05:11:53', '2022-04-05 05:14:39'),
(107, NULL, '971', '9667136636', '6458', '2022-04-07 10:03:14', '2022-04-07 11:35:20'),
(108, NULL, '971', '98765432', '0521', '2022-04-07 10:07:05', '2022-05-25 08:39:15'),
(109, NULL, '971', '987654321', '3862', '2022-04-07 10:16:03', '2022-05-26 09:07:20'),
(110, NULL, '971', '9988776655', '1785', '2022-04-07 10:18:43', '2022-04-07 10:34:23'),
(111, NULL, '971', '12356668', '8102', '2022-04-07 10:43:55', '2022-04-07 10:43:55'),
(112, NULL, '971', '8558585585258', '0315', '2022-04-07 10:44:19', '2022-04-07 10:45:30'),
(113, NULL, '971', '1234564971', '6870', '2022-04-07 11:07:35', '2022-04-07 11:07:35'),
(114, NULL, '971', '9876543219', '9486', '2022-04-07 11:11:42', '2022-04-11 09:42:33'),
(115, NULL, '971', '112233653', '3470', '2022-04-07 11:55:47', '2022-04-07 11:55:47'),
(116, NULL, '971', '1234567888', '9176', '2022-04-08 12:43:36', '2022-04-08 12:43:36'),
(117, NULL, '971', '0504444445', '3682', '2022-04-08 18:35:23', '2022-04-08 18:35:23'),
(118, NULL, '971', '568658585', '8375', '2022-04-11 09:08:03', '2022-04-11 09:08:03'),
(119, NULL, '971', '4264264265', '5936', '2022-04-11 09:38:57', '2022-04-11 09:38:57'),
(120, NULL, '971', '9813153538', '0983', '2022-04-11 10:47:54', '2022-04-11 11:55:41'),
(121, NULL, '971', '8523698745', '3027', '2022-04-11 11:26:51', '2022-04-11 11:26:51'),
(122, NULL, '971', '2546784512', '6524', '2022-04-11 11:28:15', '2022-04-11 11:28:15'),
(123, NULL, '971', '6358678585', '6784', '2022-04-11 11:29:02', '2022-04-11 11:29:02'),
(124, NULL, '971', '992551660', '7052', '2022-04-11 11:58:35', '2022-04-11 11:58:35'),
(125, NULL, '971', '9358815181', '7846', '2022-04-11 12:45:53', '2022-04-11 12:45:53'),
(126, NULL, '971', '123456766', '6957', '2022-04-13 13:24:19', '2022-04-13 13:24:19'),
(127, NULL, '971', '987654258', '0872', '2022-04-14 05:16:06', '2022-04-14 05:17:20'),
(128, NULL, '91', '9588240576', '8760', '2022-04-15 04:17:01', '2022-05-20 14:24:19'),
(129, NULL, '971', '321657894', '1786', '2022-04-15 05:10:31', '2022-04-15 05:10:31'),
(130, NULL, '971', '5588866552', '9245', '2022-04-15 09:02:42', '2022-04-15 09:02:42'),
(131, NULL, '+971', '11447788', '7391', '2022-04-15 09:37:21', '2022-04-15 09:37:21'),
(132, NULL, '+971', '11447722', '0631', '2022-04-15 09:42:50', '2022-04-15 09:42:50'),
(133, NULL, '971', '9649186636', '2470', '2022-04-15 10:10:23', '2022-04-15 10:14:16'),
(134, NULL, '971', '9648253652', '0574', '2022-04-15 10:16:01', '2022-04-15 10:16:01'),
(135, NULL, '+971', '987524125', '1703', '2022-04-15 11:03:19', '2022-04-15 11:03:19'),
(136, NULL, '+971', '123456789', '6271', '2022-04-15 11:15:45', '2022-04-15 11:18:00'),
(137, NULL, '+971', '1122336655', '4216', '2022-04-15 11:48:04', '2022-04-15 11:48:04'),
(138, NULL, '+971', '964918663', '7508', '2022-04-15 12:34:46', '2022-04-15 12:34:46'),
(139, NULL, '+971', '963258741', '7851', '2022-04-15 13:39:59', '2022-04-15 13:39:59'),
(140, NULL, '+971', '867887878', '0294', '2022-04-15 13:59:19', '2022-04-15 13:59:19'),
(141, NULL, '+971', '1233346666', '4306', '2022-04-15 14:00:50', '2022-04-15 14:00:50'),
(142, NULL, '+971', '8888555666', '1297', '2022-04-15 14:04:10', '2022-04-15 14:04:10'),
(143, NULL, '+971', '1238888874', '0462', '2022-04-15 14:11:43', '2022-04-15 14:11:43'),
(144, NULL, '+971', '95645575', '8510', '2022-04-15 14:15:26', '2022-04-15 14:15:26'),
(145, NULL, '+971', '9887136636', '5961', '2022-04-18 10:20:53', '2022-04-22 12:47:44'),
(146, NULL, '+971', '9667136636', '3417', '2022-04-18 13:18:18', '2022-05-30 06:00:46'),
(147, NULL, '+971', '96671366', '5917', '2022-04-18 13:21:25', '2022-04-18 13:26:25'),
(148, NULL, '+971', '1234567890', '4817', '2022-04-18 13:23:30', '2022-04-18 13:26:59'),
(149, NULL, '+971', '1122334455', '6349', '2022-04-18 13:33:19', '2022-04-18 13:34:19'),
(150, NULL, '+971', '8890209205', '5801', '2022-04-19 05:12:10', '2022-05-11 11:01:14'),
(151, NULL, '+971', '9649186636', '4027', '2022-04-20 11:37:23', '2022-04-20 11:37:23'),
(152, NULL, '+971', '9876543212', '0721', '2022-04-21 11:51:59', '2022-04-21 11:51:59'),
(153, NULL, '+971', '8856472369', '1850', '2022-04-21 13:21:40', '2022-04-21 13:21:40'),
(154, NULL, '+971', '9863258147', '7241', '2022-04-21 13:32:42', '2022-04-21 13:32:42'),
(155, NULL, '+971', '0503254445', '2413', '2022-04-25 11:55:12', '2022-04-25 11:55:12'),
(156, NULL, '+971', '00000000', '6219', '2022-04-25 14:04:31', '2022-04-25 14:04:31'),
(157, NULL, '+971', '5541651265', '6109', '2022-04-29 11:46:17', '2022-04-29 11:46:17'),
(158, NULL, '+971', '9876543219', '7835', '2022-04-29 11:52:49', '2022-05-10 09:43:41'),
(159, NULL, '+971', '941001535', '1839', '2022-04-29 13:12:07', '2022-04-29 13:12:07'),
(160, NULL, '+971', '8058564636', '0396', '2022-05-02 06:57:31', '2022-05-02 06:57:31'),
(161, NULL, '+971', '9588240576', '5417', '2022-05-02 06:58:18', '2022-05-02 06:58:18'),
(162, NULL, '+971', '963852741', '0349', '2022-05-03 10:13:42', '2022-05-03 10:13:42'),
(163, NULL, '+971', '000000000', '6015', '2022-05-03 11:57:40', '2022-05-03 11:57:40'),
(164, NULL, '+971', '9001869090', '5946', '2022-05-03 11:59:01', '2022-05-03 12:01:05'),
(165, NULL, '+971', '0000000000', '4308', '2022-05-03 13:28:52', '2022-05-03 13:28:52'),
(166, NULL, '+971', '555223855', '7103', '2022-05-04 07:04:11', '2022-05-04 07:04:11'),
(167, NULL, '+971', '885655223', '1794', '2022-05-04 07:04:45', '2022-05-04 07:04:45'),
(168, NULL, '+971', '5546885895', '8237', '2022-05-04 07:05:48', '2022-05-04 07:05:48'),
(169, NULL, '+971', '77755555', '7615', '2022-05-04 07:08:18', '2022-05-04 07:09:32'),
(170, NULL, '+971', '987654321', '3568', '2022-05-05 08:57:13', '2022-05-05 08:57:13'),
(171, NULL, '+971', '9672512896', '2374', '2022-05-05 08:59:41', '2022-05-05 08:59:41'),
(172, NULL, '+975', '8058564636', '4907', '2022-05-05 13:11:26', '2022-05-05 13:11:40'),
(173, NULL, '+971', '8888888888', '0273', '2022-05-05 13:31:46', '2022-05-05 13:31:46'),
(174, NULL, '+971', '5555555555', '5074', '2022-05-05 13:32:00', '2022-05-05 13:32:00'),
(175, NULL, '+974', '123456789', '0167', '2022-05-08 20:56:44', '2022-05-08 20:56:45'),
(176, NULL, '+971', '444555666', '8503', '2022-05-10 11:16:33', '2022-05-10 11:16:33'),
(177, NULL, '+974', '132456678', '7682', '2022-05-10 16:04:43', '2022-05-10 16:05:05'),
(178, NULL, '+971', '9182734650', '6357', '2022-05-11 10:31:37', '2022-05-11 10:31:37'),
(179, NULL, '+971', '91827346', '1968', '2022-05-11 10:31:54', '2022-05-11 10:32:23'),
(180, NULL, '+971', '91827364', '9076', '2022-05-11 10:56:49', '2022-05-11 10:56:49'),
(181, NULL, '+971', '567986868', '1240', '2022-05-11 11:06:11', '2022-05-11 11:06:11'),
(182, NULL, '+971', '98127634', '1347', '2022-05-11 11:14:20', '2022-05-11 11:14:20'),
(183, NULL, '+971', '7676767676', '2015', '2022-05-13 10:34:35', '2022-05-13 10:34:35'),
(184, NULL, '+91', '8058564636', '1483', '2022-05-18 08:33:59', '2022-05-18 08:33:59'),
(185, NULL, '971', '7339932700', '6192', '2022-05-20 14:25:02', '2022-05-23 09:42:39'),
(186, NULL, '+971', '7339932700', '9471', '2022-05-20 14:32:12', '2022-05-20 14:32:12'),
(187, NULL, '+971', '7062258117', '1325', '2022-05-20 15:26:17', '2022-05-20 15:26:17'),
(188, NULL, '+971', '1231231230', '0328', '2022-05-23 10:15:00', '2022-05-23 10:15:00'),
(189, NULL, '971', '5555555444', '0945', '2022-05-24 13:33:18', '2022-05-24 13:33:18'),
(190, NULL, '971', '885566885', '8409', '2022-05-24 13:34:23', '2022-05-24 13:34:23'),
(191, NULL, '971', '444666685', '0921', '2022-05-24 13:37:35', '2022-05-24 13:37:35'),
(192, NULL, '971', '8886645885', '0165', '2022-05-24 13:38:23', '2022-05-24 13:38:23'),
(193, NULL, '971', '8890209966', '3205', '2022-05-24 13:41:35', '2022-05-24 13:41:35'),
(194, NULL, '971', '44465584', '4972', '2022-05-24 13:42:19', '2022-05-24 13:42:19'),
(195, NULL, '971', '777855555', '1290', '2022-05-24 13:44:03', '2022-05-24 13:44:03'),
(196, NULL, '971', '8855466555', '4702', '2022-05-24 13:50:32', '2022-05-24 13:50:32'),
(197, NULL, '971', '5554488444', '8017', '2022-05-24 13:50:45', '2022-05-24 13:50:45'),
(198, NULL, '971', '8855556666', '5874', '2022-05-24 13:54:29', '2022-05-24 13:54:29'),
(199, NULL, '971', '44444444', '6387', '2022-05-24 13:55:37', '2022-05-24 13:55:37'),
(200, NULL, '+971', '7339932800', '0893', '2022-05-24 14:41:47', '2022-05-24 14:41:47'),
(201, NULL, '+971', '9413560787', '3572', '2022-05-25 05:32:15', '2022-05-25 05:32:15'),
(202, NULL, '971', '99999999', '1369', '2022-05-25 07:27:47', '2022-05-26 08:44:27'),
(203, NULL, '+971', '7062258111', '5173', '2022-05-25 09:28:33', '2022-05-25 09:28:33'),
(204, NULL, '971', '6756678687', '6712', '2022-05-25 09:36:54', '2022-05-25 09:36:54'),
(205, NULL, '971', '5435353453', '2589', '2022-05-25 09:46:37', '2022-05-25 09:46:37'),
(206, NULL, '971', '3454535435', '0598', '2022-05-25 10:02:33', '2022-05-25 10:02:33'),
(207, NULL, '971', '9889899989', '4790', '2022-05-25 10:04:39', '2022-05-25 10:04:39'),
(208, NULL, '971', '987654332', '5061', '2022-05-25 10:11:57', '2022-05-25 10:11:57'),
(209, NULL, '971', '98765433', '3806', '2022-05-25 10:15:05', '2022-05-25 10:15:05'),
(210, NULL, '+971', '7062258112', '8314', '2022-05-25 10:32:15', '2022-05-25 10:32:15'),
(211, NULL, '971', '12344566', '1763', '2022-05-25 13:11:06', '2022-05-25 13:11:06'),
(212, NULL, '971', '7777777777', '9013', '2022-05-25 13:12:10', '2022-05-25 13:12:10'),
(213, NULL, '971', '12345678', '4285', '2022-05-25 13:17:38', '2022-05-25 13:17:38'),
(214, NULL, '971', '111111111', '5961', '2022-05-25 13:28:45', '2022-05-25 13:28:45'),
(215, NULL, '971', '222222222', '7850', '2022-05-25 13:29:33', '2022-05-25 13:29:33'),
(216, NULL, '+971', '9462761961', '3965', '2022-05-26 03:30:04', '2022-05-26 03:30:04'),
(217, NULL, '971', '1111111111', '1096', '2022-05-26 03:31:43', '2022-05-26 03:31:43'),
(218, NULL, '971', '151151515', '2179', '2022-05-26 03:40:16', '2022-05-26 03:40:16'),
(219, NULL, '971', '6118815166', '7910', '2022-05-26 03:41:39', '2022-05-26 03:41:39'),
(220, NULL, '+971', '8949919899', '0319', '2022-05-26 04:40:52', '2022-05-26 04:40:52'),
(221, NULL, '971', '9983939008', '9704', '2022-05-26 04:44:04', '2022-05-26 04:44:04'),
(222, NULL, '971', '9062258117', '0258', '2022-05-26 04:52:16', '2022-05-26 04:52:16'),
(223, NULL, '971', '999999999', '4620', '2022-05-26 07:20:08', '2022-05-26 07:20:08'),
(224, NULL, '971', '9461512826', '4739', '2022-05-26 07:21:39', '2022-05-26 09:14:46'),
(225, NULL, '971', '998989899', '0156', '2022-05-26 08:42:29', '2022-05-26 08:42:29'),
(226, NULL, '971', '987654320', '1270', '2022-05-26 08:56:18', '2022-05-26 09:07:32'),
(227, NULL, '+92', '8058564636', '4731', '2022-05-26 10:20:56', '2022-05-26 10:22:16'),
(228, NULL, '+971', '503254445', '4286', '2022-05-26 11:04:28', '2022-05-26 11:04:28'),
(229, NULL, '971', '963258741', '0857', '2022-05-26 12:43:00', '2022-05-26 12:43:00'),
(230, NULL, '971', '96325874', '5796', '2022-05-26 12:43:15', '2022-05-26 12:43:15'),
(231, NULL, '971', '9887136636', '3082', '2022-05-27 07:25:01', '2022-05-30 10:44:01'),
(232, NULL, '971', '9523565586', '4016', '2022-05-27 10:03:57', '2022-05-27 10:03:57'),
(233, NULL, '971', '658325466', '8541', '2022-05-30 10:44:40', '2022-05-30 10:44:40'),
(234, NULL, '+971', '7062258222', '4016', '2022-05-30 11:10:12', '2022-05-30 11:10:12'),
(235, NULL, '+971', '6345434353', '3147', '2022-05-30 11:13:48', '2022-05-30 11:13:48'),
(236, NULL, '+971', '8814062142', '2097', '2022-05-30 11:18:10', '2022-05-30 11:18:10'),
(237, NULL, '+971', '8814062140', '6593', '2022-05-30 11:40:28', '2022-05-30 11:40:28'),
(238, NULL, '+971', '967531668', '9516', '2022-05-30 11:41:57', '2022-05-30 11:41:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_platforms`
--

CREATE TABLE `user_platforms` (
  `id` bigint NOT NULL,
  `user_id` int DEFAULT NULL,
  `platform` enum('KP','Shopya') DEFAULT NULL,
  `uuid` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `platform_token` text,
  `callback_url` varchar(255) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `user_wallets`
--

CREATE TABLE `user_wallets` (
  `id` bigint NOT NULL,
  `user_id` int DEFAULT NULL,
  `transaction_type` enum('DR','CR') DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `comment` text,
  `transaction_id` varchar(255) DEFAULT NULL,
  `transaction_json` text,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_notification`
--
ALTER TABLE `admin_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `amenities_lang`
--
ALTER TABLE `amenities_lang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banners_lang`
--
ALTER TABLE `banners_lang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_challenges`
--
ALTER TABLE `booking_challenges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `commissions`
--
ALTER TABLE `commissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contents`
--
ALTER TABLE `contents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contents_lang`
--
ALTER TABLE `contents_lang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courts`
--
ALTER TABLE `courts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courts_lang`
--
ALTER TABLE `courts_lang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `court_booking`
--
ALTER TABLE `court_booking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `court_booking_slots`
--
ALTER TABLE `court_booking_slots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `court_categories`
--
ALTER TABLE `court_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `court_categories_lang`
--
ALTER TABLE `court_categories_lang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_price`
--
ALTER TABLE `delivery_price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_template_lang`
--
ALTER TABLE `email_template_lang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facilities_lang`
--
ALTER TABLE `facilities_lang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facility_amenities`
--
ALTER TABLE `facility_amenities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facility_categories`
--
ALTER TABLE `facility_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facility_rules`
--
ALTER TABLE `facility_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facility_rules_lang`
--
ALTER TABLE `facility_rules_lang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq_lang`
--
ALTER TABLE `faq_lang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_type` (`user_type`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `panel_notifications`
--
ALTER TABLE `panel_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `shared_challenges`
--
ALTER TABLE `shared_challenges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials_lang`
--
ALTER TABLE `testimonials_lang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_address`
--
ALTER TABLE `user_address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_bank_detail`
--
ALTER TABLE `user_bank_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_car`
--
ALTER TABLE `user_car`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_devices`
--
ALTER TABLE `user_devices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_otps`
--
ALTER TABLE `user_otps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_platforms`
--
ALTER TABLE `user_platforms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_wallets`
--
ALTER TABLE `user_wallets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_notification`
--
ALTER TABLE `admin_notification`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `amenities_lang`
--
ALTER TABLE `amenities_lang`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `banners_lang`
--
ALTER TABLE `banners_lang`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `booking_challenges`
--
ALTER TABLE `booking_challenges`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `contents`
--
ALTER TABLE `contents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contents_lang`
--
ALTER TABLE `contents_lang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `courts`
--
ALTER TABLE `courts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `courts_lang`
--
ALTER TABLE `courts_lang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `court_booking`
--
ALTER TABLE `court_booking`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `court_booking_slots`
--
ALTER TABLE `court_booking_slots`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=503;

--
-- AUTO_INCREMENT for table `court_categories`
--
ALTER TABLE `court_categories`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `court_categories_lang`
--
ALTER TABLE `court_categories_lang`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `delivery_price`
--
ALTER TABLE `delivery_price`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `email_template_lang`
--
ALTER TABLE `email_template_lang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `facilities_lang`
--
ALTER TABLE `facilities_lang`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `facility_amenities`
--
ALTER TABLE `facility_amenities`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `facility_categories`
--
ALTER TABLE `facility_categories`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `facility_rules`
--
ALTER TABLE `facility_rules`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `facility_rules_lang`
--
ALTER TABLE `facility_rules_lang`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `faq_lang`
--
ALTER TABLE `faq_lang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=381;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `panel_notifications`
--
ALTER TABLE `panel_notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `shared_challenges`
--
ALTER TABLE `shared_challenges`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `testimonials_lang`
--
ALTER TABLE `testimonials_lang`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `user_address`
--
ALTER TABLE `user_address`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_bank_detail`
--
ALTER TABLE `user_bank_detail`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_car`
--
ALTER TABLE `user_car`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_devices`
--
ALTER TABLE `user_devices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `user_otps`
--
ALTER TABLE `user_otps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=239;

--
-- AUTO_INCREMENT for table `user_platforms`
--
ALTER TABLE `user_platforms`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_wallets`
--
ALTER TABLE `user_wallets`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
