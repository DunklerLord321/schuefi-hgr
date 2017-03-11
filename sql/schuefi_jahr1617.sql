-- phpMyAdmin SQL Dump
-- version 4.6.6deb1+deb.cihar.com~xenial.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 11, 2017 at 06:25 PM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `schuefi`
--
DROP DATABASE IF EXISTS `schuefi`;
CREATE DATABASE IF NOT EXISTS `schuefi` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci;
USE `schuefi`;

-- --------------------------------------------------------

--
-- Table structure for table `lehrer1617`
--

CREATE TABLE `lehrer1617` (
  `id` int(11) NOT NULL,
  `vname` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `nname` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `telefon` int(11) DEFAULT NULL,
  `klassenstufe` int(11) NOT NULL,
  `klasse` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `klassenlehrer_name` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `geburtstag` date DEFAULT NULL,
  `fach1` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `fach1_lehrer` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `fach1_nachweis` tinyint(1) DEFAULT '0',
  `fach2` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach2_lehrer` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach2_nachweis` tinyint(1) DEFAULT '0',
  `fach3` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach3_lehrer` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach3_nachweis` tinyint(1) DEFAULT '0',
  `status` enum('neu','noetig','ausstehend','nicht_moeglich') COLLATE latin1_general_ci DEFAULT NULL,
  `mo_anfang` time DEFAULT NULL,
  `mo_ende` time DEFAULT NULL,
  `di_anfang` time DEFAULT NULL,
  `di_ende` time DEFAULT NULL,
  `mi_anfang` time DEFAULT NULL,
  `mi_ende` time DEFAULT NULL,
  `do_anfang` time DEFAULT NULL,
  `do_ende` time DEFAULT NULL,
  `fr_anfang` time DEFAULT NULL,
  `fr_ende` time DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hinzugefügt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `1617` tinyint(1) DEFAULT '0',
  `1718` tinyint(1) DEFAULT '0',
  `1819` tinyint(1) DEFAULT '0',
  `comment` varchar(500) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `lehrer1617`
--

INSERT INTO `lehrer1617` (`id`, `vname`, `nname`, `email`, `telefon`, `klassenstufe`, `klasse`, `klassenlehrer_name`, `geburtstag`, `fach1`, `fach1_lehrer`, `fach1_nachweis`, `fach2`, `fach2_lehrer`, `fach2_nachweis`, `fach3`, `fach3_lehrer`, `fach3_nachweis`, `status`, `mo_anfang`, `mo_ende`, `di_anfang`, `di_ende`, `mi_anfang`, `mi_ende`, `do_anfang`, `do_ende`, `fr_anfang`, `fr_ende`, `last_update`, `hinzugefügt`, `1617`, `1718`, `1819`, `comment`) VALUES
(1, 'Martin', 'Zietz', 'martin.zietz@t-online.de', NULL, 12, '6', 'Frau Schimek', NULL, 'ma', 'Frau Schimek', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '15:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '2017-02-13 08:35:47', '2017-02-13 09:35:47', 0, 0, 0, NULL),
(2, 'Carolin', 'Seffer', 'carolin_seffer@yahoo.de', NULL, 12, '1', 'Frau Bachmann', NULL, 'eng', 'Frau Bachmann', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '14:45:00', '16:45:00', '00:00:00', '00:00:00', '12:45:00', '16:00:00', '12:45:00', '16:00:00', '14:00:00', '16:00:00', '2017-02-14 08:00:23', '2017-02-14 09:00:23', 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `paare`
--

CREATE TABLE `paare1617` (
  `id` int(11) NOT NULL,
  `id_lehrer` int(11) NOT NULL,
  `id_schueler` int(11) NOT NULL,
  `erstellungs_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `treff_zeit` time DEFAULT NULL,
  `treff_raum` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schueler`
--

CREATE TABLE `schueler1617` (
  `id` int(11) NOT NULL,
  `vname` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `nname` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `telefon` int(11) DEFAULT NULL,
  `klassenstufe` int(11) NOT NULL,
  `klasse` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `klassenlehrer_name` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `geburtstag` date DEFAULT NULL,
  `fach1` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `fach1_lehrer` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `fach1_themenbezogen` tinyint(1) DEFAULT '0',
  `fach2` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach2_lehrer` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach2_themenbezogen` tinyint(1) DEFAULT '0',
  `fach3` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach3_lehrer` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach3_themenbezogen` tinyint(1) DEFAULT '0',
  `status` enum('neu','noetig','ausstehend','nicht_moeglich','lehrer_gefunden') COLLATE latin1_general_ci DEFAULT NULL,
  `mo_anfang` time DEFAULT NULL,
  `mo_ende` time DEFAULT NULL,
  `di_anfang` time DEFAULT NULL,
  `di_ende` time DEFAULT NULL,
  `mi_anfang` time DEFAULT NULL,
  `mi_ende` time DEFAULT NULL,
  `do_anfang` time DEFAULT NULL,
  `do_ende` time DEFAULT NULL,
  `fr_anfang` time DEFAULT NULL,
  `fr_ende` time DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hinzugefügt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `1617` tinyint(1) DEFAULT '0',
  `1718` tinyint(1) DEFAULT '0',
  `1819` tinyint(1) DEFAULT '0',
  `comment` varchar(500) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `schueler1617`
--

INSERT INTO `schueler1617` (`id`, `vname`, `nname`, `email`, `telefon`, `klassenstufe`, `klasse`, `klassenlehrer_name`, `geburtstag`, `fach1`, `fach1_lehrer`, `fach1_themenbezogen`, `fach2`, `fach2_lehrer`, `fach2_themenbezogen`, `fach3`, `fach3_lehrer`, `fach3_themenbezogen`, `status`, `mo_anfang`, `mo_ende`, `di_anfang`, `di_ende`, `mi_anfang`, `mi_ende`, `do_anfang`, `do_ende`, `fr_anfang`, `fr_ende`, `last_update`, `hinzugefügt`, `1617`, `1718`, `1819`, `comment`) VALUES
(3, 'Albert', 'Sagawe', 'katrinsagawe@gmail.com', NULL, 7, 'b', 'Frau Rabe', NULL, 'eng', 'Frau Peschel', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '12:50:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '12:50:00', '00:00:00', '12:50:00', '00:00:00', '2017-03-04 06:42:22', '2017-03-04 07:42:22', 1, 0, 0, NULL),
(11, 'Marc', 'Roggentin', 'Marc_Sachsen@online.de', NULL, 9, 'b', 'Herr Stange', NULL, 'ma', 'Frau Peschel', 0, 'eng', 'Frau Bachmann', 0, NULL, NULL, 0, NULL, '14:15:00', '15:15:00', '00:00:00', '00:00:00', '14:15:00', '15:15:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '2017-02-11 11:17:42', '2017-02-11 12:17:42', 1, 0, 0, NULL),
(16, 'Isabelle', 'Sagawe', 'katrinsagawe@gmail.com', NULL, 6, 'a', 'Frau Ganzer', NULL, 'eng', 'Frau Peschel', 0, 'fra', 'Frau Muche', 0, NULL, NULL, 0, NULL, '00:00:00', '00:00:00', '11:50:00', '00:00:00', '11:50:00', '00:00:00', '14:00:00', '00:00:00', '00:00:00', '00:00:00', '2017-03-04 06:50:03', '2017-03-04 07:50:03', 1, 0, 0, NULL),
(17, 'Roberto', 'Oehmig', 'oehmig.carrasco@t-online.de', NULL, 9, 'a', 'Frau DÃ¶ring', NULL, 'ma', 'Frau Lange', 0, '', NULL, 0, '', NULL, 0, NULL, '00:00:00', '00:00:00', '00:00:00', '00:00:00', '14:00:00', '00:00:00', '00:00:00', '00:00:00', '14:00:00', '00:00:00', '2017-03-10 21:04:51', '2017-03-10 22:04:51', 0, 0, 0, NULL),
(18, 'Roberto', 'Oehmig', 'oehmig.carrasco@t-online.de', NULL, 9, 'a', 'Frau DÃ¶ring', NULL, 'ma', 'Frau Lange', 0, '', NULL, 0, '', NULL, 0, NULL, '00:00:00', '00:00:00', '00:00:00', '00:00:00', '14:00:00', '00:00:00', '00:00:00', '00:00:00', '14:00:00', '00:00:00', '2017-03-10 21:05:40', '2017-03-10 22:05:40', 0, 0, 0, NULL),
(19, 'Roberto', 'Oehmig', 'oehmig.carrasco@t-online.de', NULL, 9, 'a', 'Frau DÃ¶ring', NULL, 'ma', 'Frau Lange', 0, '', NULL, 0, '', NULL, 0, NULL, '00:00:00', '00:00:00', '00:00:00', '00:00:00', '14:00:00', '00:00:00', '00:00:00', '00:00:00', '14:00:00', '00:00:00', '2017-03-10 21:06:16', '2017-03-10 22:06:16', 0, 0, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lehrer`
--
ALTER TABLE `lehrer1617`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paare`
--
ALTER TABLE `paare1617`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paare_ibfk_2` (`id_schueler`),
  ADD KEY `paare_ibfk_1` (`id_lehrer`);

--
-- Indexes for table `schueler1617`
--
ALTER TABLE `schueler1617`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lehrer`
--
ALTER TABLE `lehrer1617`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `paare`
--
ALTER TABLE `paare1617`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `schueler`
--
ALTER TABLE `schueler1617`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `paare`
--
ALTER TABLE `paare1617`
  ADD CONSTRAINT `paare_ibfk_1` FOREIGN KEY (`id_lehrer`) REFERENCES `lehrer1617` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `paare_ibfk_2` FOREIGN KEY (`id_schueler`) REFERENCES `schueler1617` (`id`) ON UPDATE CASCADE;
--
-- Database: `schuefi_login`
--
DROP DATABASE IF EXISTS `schuefi_login`;
CREATE DATABASE IF NOT EXISTS `schuefi_login` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci;
USE `schuefi_login`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `vname` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `nname` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `passwd` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `account` enum('normal','root','view-only','') COLLATE latin1_general_ci NOT NULL DEFAULT 'normal',
  `createt_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `logged_in` tinyint(1) NOT NULL DEFAULT '0',
  `count_login` int(11) DEFAULT '0',
  `last_active` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `vname`, `nname`, `email`, `passwd`, `account`, `createt_time`, `update_time`, `last_login`, `logged_in`, `count_login`, `last_active`) VALUES
(1, 'Karla', 'Großer', 'karla.grosser@mail.com', 'test', 'normal', '2017-02-08 08:32:30', '2017-02-08 08:32:30', '2017-02-17 17:17:08', 0, 0, '2017-02-17 17:39:17'),
(6, 'Yannik', 'Weber', 'yajo10@yahoo.de', '$2y$10$rs27PVrKUl5I8hVP0V/U5eAjLoHHw5llQCZGbICaEb2R2J5TuypSC', 'root', '2017-02-13 10:42:52', '2017-02-13 10:42:52', '2017-02-17 17:17:08', 1, 4, '2017-03-09 16:12:27'),
(7, 'Christopher', 'Stäglich', 'joyajo108@gmail.com', '$2y$10$.3nbeE6OajtDUiFqj0wD0ePAn/3.rF0W0XueUzvh7xMTGuJT8JlWO', 'normal', '2017-02-14 09:37:42', '2017-02-14 09:37:42', '2017-02-17 17:17:08', 0, 3, '2017-03-09 15:13:29'),
(8, 'Vorsitzender', 'Schülerfirma', 'schuelerfirma.hgr@gmx.de', '$2y$10$Wu5yJYPTFO6Qs.YWUnf6jepPDRCXED5A/hf11BWABIPo69vBOL3By', 'normal', '2017-02-27 17:45:55', '2017-02-27 17:45:55', '2017-02-27 16:45:55', 0, 0, '2017-03-07 14:39:36'),
(9, 'gast', 'Schülerfirma', 'gast@gast.de', '$2y$10$jFttMshNesyHH86CQ0XOwegRO7bQluzN2pRd.3xSuGKrToCxLYtqy', 'view-only', '2017-03-02 15:42:58', '2017-03-02 15:42:58', '2017-03-02 14:42:58', 1, 4, '2017-03-09 16:28:06');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
