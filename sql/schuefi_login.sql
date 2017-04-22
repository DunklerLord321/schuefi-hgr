-- phpMyAdmin SQL Dump
-- version 4.6.6deb1+deb.cihar.com~xenial.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 26, 2017 at 01:36 PM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `schuefi_login`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `vname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `nname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `passwd` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `account` enum('k','f','v','w') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'w',
  `createt_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `count_login` int(11) DEFAULT 0,
  `logged_in` tinyint(1) NOT NULL DEFAULT '0',
  `last_active` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `vname`, `nname`, `email`, `passwd`, `account`, `createt_time`, `update_time`, `last_login`, `count_login`, `logged_in`, `last_active`) VALUES
(1, 'Karla', 'Großer', 'karla.grosser@mail.com', 'test', 'k', '2017-02-08 08:32:30', '2017-02-08 08:32:30', '2017-02-17 17:17:08', 0, 0, '2017-02-17 17:39:17'),
(6, 'Yannik', 'Weber', 'yajo10@yahoo.de', '$2y$10$rs27PVrKUl5I8hVP0V/U5eAjLoHHw5llQCZGbICaEb2R2J5TuypSC', 'v', '2017-02-13 10:42:52', '2017-02-13 10:42:52', '2017-02-17 17:17:08', 1, 4, '2017-03-09 16:12:27'),
(7, 'Christopher', 'Stäglich', 'joyajo108@gmail.com', '$2y$10$.3nbeE6OajtDUiFqj0wD0ePAn/3.rF0W0XueUzvh7xMTGuJT8JlWO', 'v', '2017-02-14 09:37:42', '2017-02-14 09:37:42', '2017-02-17 17:17:08', 0, 3, '2017-03-09 15:13:29'),
(8, 'Vorsitzender', 'Schülerfirma', 'schuelerfirma.hgr@gmx.de', '$2y$10$Wu5yJYPTFO6Qs.YWUnf6jepPDRCXED5A/hf11BWABIPo69vBOL3By', 'v', '2017-02-27 17:45:55', '2017-02-27 17:45:55', '2017-02-27 16:45:55', 0, 0, '2017-03-07 14:39:36'),
(9, 'gast', 'Schülerfirma', 'gast@gast.de', '$2y$10$jFttMshNesyHH86CQ0XOwegRO7bQluzN2pRd.3xSuGKrToCxLYtqy', 'f', '2017-03-02 15:42:58', '2017-03-02 15:42:58', '2017-03-02 14:42:58', 1, 4, '2017-03-09 16:28:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
