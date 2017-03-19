-- phpMyAdmin SQL Dump
-- version 4.6.6deb1+deb.cihar.com~xenial.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 19, 2017 at 12:05 PM
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
CREATE DATABASE IF NOT EXISTS `schuefi` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci;
USE `schuefi`;

-- --------------------------------------------------------

--
-- Table structure for table `lehrer-1617`
--

DROP TABLE IF EXISTS `lehrer-1617`;
CREATE TABLE `lehrer-1617` (
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
  `hinzugefuegt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` varchar(500) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `lehrer-1617`
--

INSERT INTO `lehrer-1617` (`id`, `vname`, `nname`, `email`, `telefon`, `klassenstufe`, `klasse`, `klassenlehrer_name`, `geburtstag`, `fach1`, `fach1_lehrer`, `fach1_nachweis`, `fach2`, `fach2_lehrer`, `fach2_nachweis`, `fach3`, `fach3_lehrer`, `fach3_nachweis`, `status`, `mo_anfang`, `mo_ende`, `di_anfang`, `di_ende`, `mi_anfang`, `mi_ende`, `do_anfang`, `do_ende`, `fr_anfang`, `fr_ende`, `last_update`, `hinzugefuegt`, `comment`) VALUES
(1, 'Martin', 'Zietz', 'martin.zietz@t-online.de', NULL, 12, '6', 'Frau Schimek', NULL, 'ma', 'Frau Schimek', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '15:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '2017-02-13 08:35:47', '2017-02-13 09:35:47', NULL),
(2, 'Carolin', 'Seffer', 'carolin_seffer@yahoo.de', NULL, 12, '1', 'Frau Bachmann', NULL, 'eng', 'Frau Bachmann', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '14:45:00', '16:45:00', '00:00:00', '00:00:00', '12:45:00', '16:00:00', '12:45:00', '16:00:00', '14:00:00', '16:00:00', '2017-02-14 08:00:23', '2017-02-14 09:00:23', NULL);

-- --------------------------------------------------------
-- --------------------------------------------------------

--
-- Table structure for table `paare-1617`
--

DROP TABLE IF EXISTS `paare-1617`;
CREATE TABLE `paare-1617` (
  `id` int(11) NOT NULL,
  `id_lehrer` int(11) NOT NULL,
  `id_schueler` int(11) NOT NULL,
  `erstellungs_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `treff_zeit` time DEFAULT NULL,
  `treff_raum` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schueler-1617`
--

DROP TABLE IF EXISTS `schueler-1617`;
CREATE TABLE `schueler-1617` (
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
  `hinzugefuegt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` varchar(500) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `schueler-1617`
--

INSERT INTO `schueler-1617` (`id`, `vname`, `nname`, `email`, `telefon`, `klassenstufe`, `klasse`, `klassenlehrer_name`, `geburtstag`, `fach1`, `fach1_lehrer`, `fach1_themenbezogen`, `fach2`, `fach2_lehrer`, `fach2_themenbezogen`, `fach3`, `fach3_lehrer`, `fach3_themenbezogen`, `status`, `mo_anfang`, `mo_ende`, `di_anfang`, `di_ende`, `mi_anfang`, `mi_ende`, `do_anfang`, `do_ende`, `fr_anfang`, `fr_ende`, `last_update`, `hinzugefuegt`, `comment`) VALUES
(3, 'Albert', 'Sagawe', 'katrinsagawe@gmail.com', NULL, 7, 'b', 'Frau Rabe', NULL, 'eng', 'Frau Peschel', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '12:50:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '12:50:00', '00:00:00', '12:50:00', '00:00:00', '2017-03-04 06:42:22', '2017-03-04 07:42:22', NULL),
(11, 'Marc', 'Roggentin', 'Marc_Sachsen@online.de', NULL, 9, 'b', 'Herr Stange', NULL, 'ma', 'Frau Peschel', 0, 'eng', 'Frau Bachmann', 0, NULL, NULL, 0, NULL, '14:15:00', '15:15:00', '00:00:00', '00:00:00', '14:15:00', '15:15:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '2017-02-11 11:17:42', '2017-02-11 12:17:42', NULL),
(16, 'Isabelle', 'Sagawe', 'katrinsagawe@gmail.com', NULL, 6, 'a', 'Frau Ganzer', NULL, 'eng', 'Frau Peschel', 0, 'fra', 'Frau Muche', 0, NULL, NULL, 0, NULL, '00:00:00', '00:00:00', '11:50:00', '00:00:00', '11:50:00', '00:00:00', '14:00:00', '00:00:00', '00:00:00', '00:00:00', '2017-03-04 06:50:03', '2017-03-04 07:50:03', NULL),
(17, 'Roberto', 'Oehmig', 'oehmig.carrasco@t-online.de', NULL, 9, 'a', 'Frau DÃ¶ring', NULL, 'ma', 'Frau Lange', 0, '', NULL, 0, '', NULL, 0, NULL, '00:00:00', '00:00:00', '00:00:00', '00:00:00', '14:00:00', '00:00:00', '00:00:00', '00:00:00', '14:00:00', '00:00:00', '2017-03-10 21:04:51', '2017-03-10 22:04:51', NULL);

-- --------------------------------------------------------
-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `key` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `value` varchar(500) COLLATE latin1_general_ci NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key`, `value`, `last_update`) VALUES
('all_years', '1617', '2017-03-12 08:31:02'),
('current_year', '1617', '2017-03-12 08:31:02'),
('next_year', '1718', '2017-03-12 08:31:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lehrer-1617`
--
ALTER TABLE `lehrer-1617`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paare-1617`
--
ALTER TABLE `paare-1617`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paare_ibfk_2` (`id_schueler`),
  ADD KEY `paare_ibfk_1` (`id_lehrer`);
--
-- Indexes for table `schueler-1617`
--
ALTER TABLE `schueler-1617`
  ADD PRIMARY KEY (`id`);
--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lehrer-1617`
--
ALTER TABLE `lehrer-1617`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `paare-1617`
--
ALTER TABLE `paare-1617`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `schueler-1617`
--
ALTER TABLE `schueler-1617`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `paare-1617`
--
ALTER TABLE `paare-1617`
  ADD FOREIGN KEY (`id_lehrer`) REFERENCES `lehrer-1617` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD FOREIGN KEY (`id_schueler`) REFERENCES `schueler-1617` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
