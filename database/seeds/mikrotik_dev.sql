-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2017 at 01:04 AM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mikrotik_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `meetroom`
--

CREATE TABLE `meetroom` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `profile` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meetroom`
--

INSERT INTO `meetroom` (`id`, `name`, `profile`, `description`, `created_at`) VALUES
(1, 'Suzuka', 'meeting_profile_suzuka', '1231', '2017-08-18 03:28:44'),
(2, 'Monza', 'meeting_profile_monza', 'dua\r\n', '2017-08-18 04:16:05'),
(3, 'Monaco', 'meeting_profile_monaco', '', '2017-08-21 00:00:00'),
(4, 'Sepang', 'meeting_profile_sepang', '', '2017-08-21 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `mikrotik`
--

CREATE TABLE `mikrotik` (
  `id` int(11) NOT NULL,
  `mikrotik_id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `room` varchar(50) DEFAULT NULL,
  `checkin` date NOT NULL,
  `checkout` date NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `islock` tinyint(1) NOT NULL,
  `ishidden` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `name`, `description`, `islock`, `ishidden`, `created_at`) VALUES
(1, '101', 'ab', 1, 0, '2017-08-14 02:27:05'),
(2, '102', 'test', 0, 1, '2017-08-14 03:57:26'),
(3, '103', 'test', 0, 1, '2017-08-17 08:41:35');

-- --------------------------------------------------------

--
-- Table structure for table `tb_role`
--

CREATE TABLE `tb_role` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_role`
--

INSERT INTO `tb_role` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'SPV', 'Supervisor', '2017-07-04 00:00:00', '2017-07-04 00:00:00'),
(2, 'FO', 'Front Office', '2017-07-04 00:00:00', '2017-07-04 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tb_users`
--

CREATE TABLE `tb_users` (
  `id` int(11) NOT NULL,
  `role_id` int(6) DEFAULT NULL,
  `password` varchar(64) NOT NULL,
  `username` varchar(100) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `avatar` varchar(100) DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `last_login` datetime NOT NULL,
  `remember_token` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tb_users`
--

INSERT INTO `tb_users` (`id`, `role_id`, `password`, `username`, `first_name`, `last_name`, `avatar`, `active`, `last_login`, `remember_token`, `created_at`, `updated_at`, `created_by`) VALUES
(118, 1, '$2y$10$tM5gK5FFKx/huDDZMTppHuOk0QUfcYGmApC2NnQE/kaKy7qA/zlxe', 'nungky', 'asep', 'hendro', NULL, NULL, '2017-08-21 05:52:06', 'APxbL29nT3PmySIBNIH1SoT2jgctKBEwibnCkBcrwKKgpZBeess7hE5vCSpe', '2017-08-01 00:04:56', '2017-08-20 23:03:22', 0),
(122, 2, '$2y$10$2NyKgM1R/Yw5nPAlcC288.OEJ6pSjrn2enUccvhu530xEePsSJghO', 'indra', 'indra', 'gunawan', NULL, NULL, '2017-08-21 06:03:48', 'SLpVrhFWmJguHnMRr9Yo99Ktv3z5qeDizXFwyGvZkDxKtt70Ljbgmnzn0h3V', '2017-08-19 16:36:32', '2017-08-20 08:16:40', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `meetroom`
--
ALTER TABLE `meetroom`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mikrotik`
--
ALTER TABLE `mikrotik`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_role`
--
ALTER TABLE `tb_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `meetroom`
--
ALTER TABLE `meetroom`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `mikrotik`
--
ALTER TABLE `mikrotik`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tb_role`
--
ALTER TABLE `tb_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
