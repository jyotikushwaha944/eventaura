-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2024 at 06:59 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eventaura`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `num_tickets` int(11) NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id`, `user_id`, `event_id`, `num_tickets`, `status`) VALUES
(1, 3, 9, 40, 'booked'),
(2, 3, 9, 4, 'booked'),
(3, 3, 9, 4, 'booked'),
(4, 3, 9, 5, 'booked'),
(5, 3, 9, 8, 'booked'),
(6, 3, 8, 4, 'booked'),
(7, 3, 5, 6, 'pending'),
(8, 3, 5, 6, 'pending'),
(9, 3, 5, 6, 'pending'),
(10, 3, 5, 5, 'pending'),
(11, 3, 5, 4, 'pending'),
(12, 3, 5, 5, 'pending'),
(13, 3, 8, 5, 'booked'),
(14, 3, 8, 9, 'pending'),
(15, 3, 7, 10, 'pending'),
(16, 3, 7, 10, 'pending'),
(17, 3, 7, 1, 'pending'),
(18, 3, 7, 1, 'booked'),
(19, 7, 13, 10, 'pending'),
(20, 7, 13, 25, 'pending'),
(21, 8, 13, 1, 'booked'),
(22, 8, 13, 6, 'booked'),
(23, 8, 12, 1, 'booked'),
(24, 8, 11, 3, 'booked'),
(25, 3, 11, 1, 'booked'),
(26, 3, 5, 7, 'booked'),
(27, 3, 5, 3, 'booked'),
(28, 3, 6, 5, 'pending'),
(29, 3, 7, 2, 'booked'),
(30, 3, 8, 3, 'booked'),
(31, 3, 9, 8, 'booked'),
(32, 3, 10, 1, 'booked'),
(33, 3, 5, 6, 'pending'),
(34, 3, 6, 7, 'booked'),
(35, 3, 7, 5, 'booked'),
(36, 3, 8, 9, 'booked'),
(37, 3, 9, 10, 'pending'),
(38, 3, 10, 4, 'booked'),
(39, 3, 5, 3, 'booked'),
(40, 3, 6, 2, 'pending'),
(41, 3, 7, 4, 'booked'),
(42, 3, 8, 6, 'booked'),
(43, 3, 9, 7, 'booked'),
(44, 3, 10, 2, 'booked'),
(45, 3, 5, 8, 'booked'),
(46, 3, 6, 3, 'pending'),
(47, 3, 7, 1, 'booked'),
(48, 3, 8, 2, 'booked'),
(49, 3, 9, 4, 'pending'),
(50, 3, 10, 3, 'booked'),
(51, 3, 5, 5, 'booked'),
(52, 3, 6, 6, 'pending'),
(53, 3, 7, 2, 'booked'),
(54, 3, 8, 3, 'booked'),
(55, 3, 9, 9, 'pending'),
(56, 3, 14, 1, 'booked'),
(57, 9, 20, 1, 'booked'),
(58, 9, 15, 1, 'booked'),
(59, 3, 18, 2, 'booked'),
(60, 3, 28, 1, 'booked');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Corporate Events'),
(2, 'social Events'),
(3, 'Community Events'),
(4, 'Cultural Events'),
(5, 'Educational Events'),
(6, 'Holidays');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `participant_count` int(11) NOT NULL,
  `description` varchar(500) NOT NULL,
  `venue` varchar(250) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `created_datetime` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `price` double NOT NULL,
  `category_id` int(11) NOT NULL,
  `image_large` varchar(255) DEFAULT NULL,
  `image_small` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id`, `name`, `participant_count`, `description`, `venue`, `latitude`, `longitude`, `start_datetime`, `end_datetime`, `IsActive`, `created_datetime`, `created_by`, `price`, `category_id`, `image_large`, `image_small`) VALUES
(5, 'good bye summer', 9, 'dfdsfasfsf', 'Balaju, Kathmandu-16, Kathmandu, Kathmandu Metropolitan City, Kathmandu, Bagmati Province, 44611, Nepal', 30.8325804, 77.4011654, '2024-07-30 05:02:00', '2024-07-25 15:03:00', 1, '2024-07-26 05:05:47', 1, 8, 6, NULL, NULL),
(6, 'terdfas', 4, 'dfdsaf', 'Balah, Pastoral Unincorporated Area, South Australia, 5417, Australia', -33.66218575, 139.85457713708846, '2024-07-25 11:23:00', '2024-07-25 23:24:00', 1, '2024-07-26 05:23:30', 3, 2, 1, NULL, NULL),
(7, 'Holi ', 6, 'Holi festival', 'Birgunj, Parsa, Madhesh Province, 44300, Nepal', 27.0135196, 84.8763804, '2024-07-01 23:25:00', '2024-07-25 23:25:00', 1, '2024-07-26 05:25:49', 3, 10, 2, NULL, NULL),
(8, 'Holi Festival latest', 10, 'This is holi in Kathmandu', 'Kathmandu, Kathmandu Metropolitan City, Kathmandu, Bagmati Province, 46000, Nepal', 27.708317, 85.3205817, '2024-07-26 12:03:00', '2024-07-26 00:06:00', 1, '2024-07-26 06:04:12', 3, 50, 4, NULL, NULL),
(9, 'teej', 6, 'vvrfwa', 'Raxaul, East Champaran, Bihar, India', 26.9555713, 84.80513570177044, '2024-07-28 01:45:00', '2024-07-29 01:45:00', 1, '2024-07-26 07:45:23', 3, 78, 4, NULL, NULL),
(10, 'Test', 4, 'fasdfasdfasfa', 'Balaju, Kathmandu-16, Kathmandu, Kathmandu Metropolitan City, Kathmandu, Bagmati Province, 44611, Nepal', 27.7270105, 85.3045555, '2024-08-05 22:44:00', '2024-08-23 22:44:00', 1, '2024-08-06 04:44:38', 6, 400, 1, 'img/uploads/large_img_66b18e161c46f2.36452460.jpg', 'img/uploads/small_img_66b18e161ca958.81506509.jpg'),
(11, 'Upload test', 4, 'fdsafasdfa', 'Balaj, Lushnja, Fier County, Southern Albania, 9002, Albania', 40.946688, 19.7777462, '2024-08-07 22:48:00', '2024-08-23 22:48:00', 1, '2024-08-06 04:49:35', 6, 50, 1, 'img/uploads/large_img_66b18f3f4b2450.41930435.jpg', 'img/uploads/small_img_66b18f3f4b4319.76084371.jpg'),
(12, 'Test Upload Image', 5, 'Test upload image test sample', 'balaj, Rajgarh, Sirmaur, Himachal Pradesh, India', 30.8325804, 77.4011654, '2024-08-01 22:53:00', '2024-08-08 22:53:00', 1, '2024-08-06 04:54:13', 6, 500, 1, 'img/uploads/large_img_66b190555e84f9.37761384.png', 'img/uploads/small_img_66b190555ee3d0.26451706.png'),
(13, 'Friendship Hospital Event', 200, 'This is test event ', 'Nepal Beijing Friendship Hospital, Sheetal Marga, Narayan Tole, Maharajganj, Kathmandu-03, Kathmandu, Kathmandu Metropolitan City, Kathmandu, Bagmati Province, 44616, Nepal', 27.73144295, 85.3300147700006, '2024-08-06 15:29:00', '2024-08-07 15:29:00', 1, '2024-08-06 21:29:30', 7, 50, 1, 'img/uploads/large_img_66b2799a7325b2.50179671.png', 'img/uploads/small_img_66b2799a73d3b9.15862394.png'),
(14, 'Hope Walk for Humanity', 7, 'This is social event', 'birgunj', 0, 0, '2024-08-23 12:36:00', '2024-08-31 12:36:00', 1, '2024-08-09 18:37:21', 3, 5, 2, '', ''),
(15, 'Hope Walk for Humanity', 7, 'This is social event', 'birgunj', 0, 0, '2024-08-23 12:36:00', '2024-08-31 12:36:00', 1, '2024-08-09 19:18:52', 3, 5, 2, '', ''),
(16, 'Hope Walk for Humanity', 7, 'This is social event', 'birgunj', 0, 0, '2024-08-23 12:36:00', '2024-08-31 12:36:00', 1, '2024-08-09 19:22:43', 3, 5, 2, '', ''),
(17, 'Hope Walk for Humanity', 7, 'This is social event', 'birgunj', 0, 0, '2024-08-23 12:36:00', '2024-08-31 12:36:00', 1, '2024-08-09 19:31:06', 3, 5, 2, '', ''),
(18, 'Hope Walk for Humanity', 7, 'This is social event', 'birgunj', 0, 0, '2024-08-23 12:36:00', '2024-08-31 12:36:00', 1, '2024-08-09 19:31:13', 3, 5, 2, '', ''),
(19, 'jkjkjk', 222, 'venyevenue', 'venue', 0, 0, '2024-08-09 13:33:00', '2024-08-10 13:33:00', 1, '2024-08-09 19:33:39', 3, 2, 1, '', ''),
(20, 'chhath', 500, 'this is social event', 'Kalanki, Kathmandu-14, Kathmandu, Kathmandu Metropolitan City, Kathmandu, Bagmati Province, 44618, Nepal', 27.6932983, 85.2816525, '2024-08-11 16:41:00', '2024-08-10 19:41:00', 1, '2024-08-10 22:42:00', 3, 2, 2, '', ''),
(21, 'goa trip', 30, 'this is a test desc', 'Osaka, B Deck, Grand Front Osaka, Kita Ward, Osaka, Osaka Prefecture, 530-8558, Japan', 34.7021912, 135.4955866, '2024-08-12 23:01:00', '2024-08-24 23:01:00', 1, '2024-08-12 05:02:25', 9, 1, 6, '', ''),
(22, 'Test Event 101', 50, 'Test', 'Băla, Mureș, Romania', 46.7130898, 24.5001897, '2024-08-20 01:54:00', '2024-08-31 01:54:00', 1, '2024-08-16 07:55:09', 7, 50, 1, '', ''),
(23, 'Test Event 102', 40, 'Test Balaju Event', 'Balaju, Kathmandu-16, Kathmandu, Kathmandu Metropolitan City, Kathmandu, Bagmati Province, 44611, Nepal', 27.7270105, 85.3045555, '2024-08-17 02:15:00', '2024-08-21 02:15:00', 1, '2024-08-16 08:15:27', 3, 50, 1, 'img/uploads/large_img_66beee7fd5e478.76852097.png', 'img/uploads/small_img_66beee7fd647f9.32125890.png'),
(24, 'Dashain', 400, 'hello from virginia', 'Virginia, United States', 37.1232245, -78.4927721, '2024-08-21 00:56:00', '2024-08-27 00:56:00', 1, '2024-08-20 06:57:01', 3, 3, 4, '', ''),
(25, 'The Pool Party', 150, 'welcome All', 'Washington, District of Columbia, United States', 38.8950368, -77.0365427, '2024-08-22 01:00:00', '2024-08-23 01:00:00', 1, '2024-08-20 07:00:50', 3, 10, 6, '', ''),
(26, 'Dubai trip', 20, 'hello all', 'Gainesville, Prince William County, Virginia, 20155, United States', 38.7938615, -77.63319337032087, '2024-08-28 01:49:00', '2024-08-31 01:49:00', 1, '2024-08-20 07:50:19', 3, 1, 6, '', ''),
(27, 'Food Festival', 300, 'this is a test message', 'NewYork-Presbyterian Medical Group, 1230, Avenue U, Brooklyn, Kings County, New York, 11229, United States', 40.5984208, -73.9590401, '2024-10-29 23:32:00', '2024-11-07 23:32:00', 1, '2024-08-24 05:33:47', 3, 1, 4, 'img/uploads/large_img_66c9549b962c47.33286554.png', ''),
(28, 'IT Fest', 100, 'try to join', 'Manassas, Virginia, United States', 38.7509488, -77.4752667, '2024-11-28 23:30:00', '2024-12-28 23:30:00', 1, '2024-08-26 05:31:00', 3, 5, 5, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(250) NOT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `usertype_id` int(11) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `email`, `created_datetime`, `modified_datetime`, `usertype_id`, `firstname`, `lastname`, `IsActive`) VALUES
(3, 'bijaykush', '$2y$10$DDGi6xQjVHl9TBJirb3E7OSK7IVarWqTFcCFwFo0IAIdKMl4K1c0O', 'bijay@gmail.com', '2024-07-24 22:24:32', NULL, 1, 'Bijay', 'Kushawahaa', 1),
(7, 'admin', '$2y$10$whic1G509fr0pnhpr4Smg..nYWw/q7CB7QvI6Yk0PAwjmhbSD8VZq', 'admin@gmail.com', '2024-08-06 06:25:46', NULL, 3, 'Admin', 'User', 1),
(8, 'eva04', '$2y$10$mTbmGzWAcAIowul0ZgDA9.S6oa035N433E4UeQaX/0CqKjRa6v88a', 'eva@gmail', '2024-08-07 00:16:41', NULL, 2, 'evana', 'mehta', 1),
(9, 'binitasap01', '$2y$10$vMAMaZgvoTQEYzlMnvR3WurpMsLTdx6br7spS2Tz60YzrPvEc.3Gy', 'binita25@gmail.com', '2024-08-11 07:48:43', NULL, 2, 'Binita', 'Sapkota', 1),
(10, 'Kushpreeti', '$2y$10$xmZyjsTGp.XxOLkVIRCU5OafNkhFCCCg1cbRqj1B3tG3HNuW5zpyq', 'preeti@gmail.com', '2024-08-20 00:34:56', NULL, 1, 'Preeti', 'Kusha', 1),
(11, 'rubi03', '$2y$10$eWEPpQj8b9QJg9rDyTSBg.tpnrDd3xz3MNpkvFPn5mgDW5jTyg05q', 'rubimesh116@gmail.com', '2024-08-20 00:49:47', NULL, 1, 'rubi', 'kush', 0);

-- --------------------------------------------------------

--
-- Table structure for table `userpreference`
--

CREATE TABLE `userpreference` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usertype`
--

CREATE TABLE `usertype` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usertype`
--

INSERT INTO `usertype` (`id`, `type`) VALUES
(1, 'Individual'),
(2, 'Organization'),
(3, 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id_booking` (`user_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usertype` (`usertype_id`);

--
-- Indexes for table `userpreference`
--
ALTER TABLE `userpreference`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id_userpreference` (`user_id`),
  ADD KEY `fk_category_id_userpreference` (`category_id`);

--
-- Indexes for table `usertype`
--
ALTER TABLE `usertype`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `userpreference`
--
ALTER TABLE `userpreference`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usertype`
--
ALTER TABLE `usertype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_user_id_booking` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `fk_user_id_new` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_category_id_event` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_usertype` FOREIGN KEY (`usertype_id`) REFERENCES `usertype` (`id`);

--
-- Constraints for table `userpreference`
--
ALTER TABLE `userpreference`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `usertype` (`id`),
  ADD CONSTRAINT `fk_category_id_userpreference` FOREIGN KEY (`category_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `usertype` (`id`),
  ADD CONSTRAINT `fk_user_id_userpreference` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
