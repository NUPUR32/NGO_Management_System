-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2025 at 07:29 AM
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
-- Database: `donation_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `donor_name` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('approved','pending','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` varchar(50) DEFAULT NULL,
  `item` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `donor_name`, `amount`, `status`, `created_at`, `category`, `item`, `payment_method`) VALUES
(1, 'NUPUR', 40.00, 'approved', '2025-06-25 07:32:47', NULL, NULL, NULL),
(15, 'REHAN', 50.00, 'pending', '2025-06-30 06:50:23', NULL, NULL, NULL),
(33, 'nupur', 34.00, 'approved', '2025-07-02 05:23:00', 'Disaster Relief', 'Stationery', 'Credit Card'),
(36, 'Rajat', 56.00, 'approved', '2025-07-04 01:48:00', 'Education', 'Textbooks', 'Credit Card'),
(37, 'Ayush', 67.00, 'approved', '2025-07-04 01:53:00', 'Disaster Relief', 'Medical Kits', 'Credit Card'),
(39, 'kush123', 23.00, 'pending', '2025-07-04 02:16:51', 'Education', 'Stationery', 'Credit Card'),
(40, 'ADITYA', 45.00, 'pending', '2025-07-04 02:33:44', 'Education', 'Stationery', 'Credit Card'),
(42, 'NAMAN', 12.00, 'pending', '2025-07-07 02:15:14', 'Education', 'Textbooks', 'Credit Card'),
(43, 'NAMAN', 10.00, 'approved', '2025-07-22 08:58:00', 'Education', 'Stationery', 'Credit Card'),
(44, 'NAMAN', 30.00, 'pending', '2025-07-23 08:24:48', 'Proposal', 'Disaster', 'N/A'),
(45, 'NAMAN', 20.00, 'pending', '2025-07-23 08:24:57', 'Proposal', 'Disaster', 'N/A'),
(46, 'NAMAN', 8.00, 'pending', '2025-07-23 08:35:07', 'Proposal', 'Eduction', 'N/A');

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

CREATE TABLE `donors` (
  `id` int(11) NOT NULL,
  `donor_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`id`, `donor_name`, `email`, `password`, `created_at`, `profile_pic`) VALUES
(1, 'test_donor', 'test@donor.com', '2ee4fb160945b17a31dbf73621d7cede2a1cb833', '2025-06-25 07:18:29', NULL),
(2, 'NAMAN', 'donor@example.com', 'donor123', '2025-07-02 04:30:00', NULL),
(3, 'kush123', 'kush@example.com', 'donor123', '2025-07-02 06:26:21', NULL),
(4, 'Rajat_1751592510', 'Rajat@email.com', 'admin123', '2025-07-04 01:28:30', NULL),
(5, 'Rajat', 'rajat@example.com', 'admin123', '2025-07-04 01:45:17', NULL),
(6, 'Ayush', 'ayush@gmail.com', 'admin123', '2025-07-04 01:49:42', NULL),
(7, 'NAVEEN', 'NAVEEN@EXAMPLE.COM', 'admin123', '2025-07-04 02:10:11', NULL),
(8, 'ADITYA', 'ADITYA@EXAMPLE.COM', 'admin123', '2025-07-04 02:33:21', NULL),
(9, 'Ravi', 'Ravi@email.com', 'admin123', '2025-07-04 02:49:48', NULL),
(10, 'Tanish_1751853750', 'Tanish@example.com', 'tanish123', '2025-07-07 02:02:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `donor_name` varchar(255) NOT NULL,
  `feedback` text NOT NULL,
  `rating` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `starred` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `donor_name`, `feedback`, `rating`, `created_at`, `starred`) VALUES
(1, 'nupur', 'its amazing', 4, '2025-07-02 10:59:21', 0),
(2, 'Ayush', 'amazing experience', 5, '2025-07-04 07:24:47', 0),
(3, 'Ravi_1751597388', 'i really had a great experience', 5, '2025-07-04 08:20:24', 0),
(4, 'Tanish_1751853750', 'great', 5, '2025-07-07 07:32:47', 0),
(5, 'Ravi', 'my choice', 4, '2025-07-07 07:36:05', 0),
(6, 'NAMAN', 'trty', 5, '2025-07-07 07:45:40', 0),
(7, 'NAMAN', 'hrytyte', 4, '2025-07-22 14:30:06', 0);

-- --------------------------------------------------------

--
-- Table structure for table `proposals`
--

CREATE TABLE `proposals` (
  `proposal_id` varchar(13) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proposals`
--

INSERT INTO `proposals` (`proposal_id`, `heading`, `details`, `amount`, `image_path`, `created_at`, `updated_at`) VALUES
('68809bdd46b84', 'Eduction', 'xyz', 10.00, 'uploads/proposals/68809bdd46c2b_ai generate a image of flute and peacock gfeature.jpg', '2025-07-23 13:52:53', NULL),
('68809c3929e19', 'Disaster', 'releif kits', 100.00, NULL, '2025-07-23 13:54:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `proposal_donations`
--

CREATE TABLE `proposal_donations` (
  `id` int(11) NOT NULL,
  `proposal_id` varchar(13) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `donated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proposal_donations`
--

INSERT INTO `proposal_donations` (`id`, `proposal_id`, `donation_id`, `amount`, `donated_at`) VALUES
(1, '68809c3929e19', 44, 30.00, '2025-07-23 13:54:48'),
(2, '68809c3929e19', 45, 20.00, '2025-07-23 13:54:57'),
(3, '68809bdd46b84', 46, 8.00, '2025-07-23 14:05:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', 'f865b53623b121fd34ee5426c792e5c33af8c227');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `donor_name` (`donor_name`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `proposals`
--
ALTER TABLE `proposals`
  ADD PRIMARY KEY (`proposal_id`);

--
-- Indexes for table `proposal_donations`
--
ALTER TABLE `proposal_donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proposal_id` (`proposal_id`),
  ADD KEY `donation_id` (`donation_id`);

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
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `donors`
--
ALTER TABLE `donors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `proposal_donations`
--
ALTER TABLE `proposal_donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `proposal_donations`
--
ALTER TABLE `proposal_donations`
  ADD CONSTRAINT `proposal_donations_ibfk_1` FOREIGN KEY (`proposal_id`) REFERENCES `proposals` (`proposal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `proposal_donations_ibfk_2` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
