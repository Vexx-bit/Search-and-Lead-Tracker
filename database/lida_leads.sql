-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2025 at 09:38 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lida_leads`
--

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `niche` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `status` enum('new','contacted','follow_up','converted','not_interested') DEFAULT 'new',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `platform` varchar(100) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `company_name`, `niche`, `location`, `contact_person`, `email`, `phone`, `status`, `notes`, `created_at`, `updated_at`, `platform`, `url`) VALUES
(7, 'Exceeding Expectations every step of the way#successStory#fyp ...', 'Law Firm', 'Ruiru', '', '', '', 'new', '', '2025-01-24 09:19:58', '2025-01-24 10:11:09', 'tiktok.com', 'https://www.tiktok.com/@hudsonfurnishing/video/7344282170304875782'),
(8, 'MJ Afrilaw | LinkedIn', 'Law Firm', 'Nairobi', '', '', '', 'new', '', '2025-02-07 13:02:29', '2025-02-07 13:02:29', 'linkedin.com', 'https://tz.linkedin.com/company/mj-afrilaw-tz'),
(9, 'Opulence Realtors | LinkedIn', 'Real Estate', 'Nairobi', '', '', '', 'new', '', '2025-02-20 08:57:08', '2025-02-20 08:57:08', 'linkedin.com', 'https://ke.linkedin.com/company/opulence-realtors'),
(10, 'Bills Partnership Ltd | LinkedIn', 'Real Estate', 'Nairobi', '', '', '', 'new', '', '2025-02-20 09:06:59', '2025-02-20 09:06:59', 'linkedin.com', 'https://ke.linkedin.com/company/billspartnershipltd');

-- --------------------------------------------------------

--
-- Table structure for table `lead_tags`
--

CREATE TABLE `lead_tags` (
  `lead_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`) VALUES
(14, 'Eastleigh'),
(7, 'Gigiri'),
(15, 'Hurlingham'),
(5, 'Industrial Area'),
(6, 'Karen'),
(12, 'Kiambu Road'),
(3, 'Kilimani'),
(11, 'Lavington'),
(8, 'Mombasa Road'),
(1, 'Nairobi CBD'),
(10, 'Ngong Road'),
(9, 'Parklands'),
(13, 'Thika Road'),
(4, 'Upper Hill'),
(2, 'Westlands');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lead_tags`
--
ALTER TABLE `lead_tags`
  ADD PRIMARY KEY (`lead_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lead_tags`
--
ALTER TABLE `lead_tags`
  ADD CONSTRAINT `lead_tags_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`),
  ADD CONSTRAINT `lead_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
