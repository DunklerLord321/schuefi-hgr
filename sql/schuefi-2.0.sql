-- phpMyAdmin SQL Dump
-- version 4.6.6deb1+deb.cihar.com~xenial.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 19, 2017 at 12:05 PM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

-- Version 1.0

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
CREATE DATABASE IF NOT EXISTS `schuefi` DEFAULT CHARACTER SET utf8 COLLATE utf8_german2_ci;
USE `schuefi`;

-- --------------------------------------------------------

--
-- Table structure for table `lehrer-1617`
--

DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
	`id` int(11) NOT NULL,
	`vname` varchar(50) COLLATE utf8_german2_ci NOT NULL,
	`nname` varchar(50) COLLATE utf8_german2_ci NOT NULL,
	`email` varchar(50) COLLATE utf8_german2_ci DEFAULT NULL,
	`telefon` varchar(50) COLLATE utf8_german2_ci DEFAULT NULL,
	`geburtstag` date DEFAULT NULL,
	`hinzugefuegt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP	
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `faecher`;
CREATE TABLE `faecher` (
	`id` int(11) NOT NULL,
	`kuerzel` varchar(5) COLLATE utf8_german2_ci NOT NULL,
	`name` varchar(50) COLLATE utf8_german2_ci NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `zeit`;
CREATE TABLE `zeit` (
	`id` int(11) NOT NULL,
	`sid` int(11),
	`lid` int(11),
	`tag` enum('mo','di','mi','do','fr') COLLATE utf8_german2_ci NOT NULL,
	`anfang` time NOT NULL,
	`ende` time NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `lehrer`;
CREATE TABLE `lehrer` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `schuljahr` int(11) NOT NULL,
  `klassenstufe` int(11) NOT NULL,
  `klasse` varchar(3) COLLATE utf8_german2_ci NOT NULL,
  `klassenlehrer_name` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `comment` text COLLATE utf8_german2_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `bietet_an`;
CREATE TABLE `bietet_an` (
  `id` int(11) NOT NULL,
  `lid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `nachweis_vorhanden` tinyint(1) DEFAULT 1,
  `status` enum('neu','ausstehend','schueler_gefunden') COLLATE utf8_german2_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `schueler`;
CREATE TABLE `schueler` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `schuljahr` int(11) NOT NULL,
  `klassenstufe` int(11) NOT NULL,
  `klasse` varchar(3) COLLATE utf8_german2_ci NOT NULL,
  `klassenlehrer_name` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `comment` text COLLATE utf8_german2_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `fragt_nach`;
CREATE TABLE `fragt_nach` (
  `id` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `langfristig` tinyint(1) DEFAULT 1,
  `status` enum('neu','noetig','ausstehend','nicht_moeglich','lehrer_gefunden') COLLATE utf8_german2_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `unterricht`;
CREATE TABLE `unterricht` (
  `id` int(11) NOT NULL,
  `lid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `erstellungs_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `treff_zeit` time DEFAULT NULL,
  `treff_raum` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Indexes
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `schueler`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `lehrer`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `faecher`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `zeit`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `fragt_nach`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bietet_an`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `person` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `lehrer` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `schueler` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `faecher` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `zeit` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `bietet_an` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `fragt_nach` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `paare-1617`
--

ALTER TABLE `lehrer`
	ADD FOREIGN KEY (`pid`) REFERENCES `person` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `schueler`
	ADD FOREIGN KEY (`pid`) REFERENCES `person` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT;
	
ALTER TABLE `unterricht`
	ADD FOREIGN KEY (`lid`) REFERENCES `lehrer` (`id`)  ON UPDATE CASCADE ON DELETE RESTRICT,
	ADD FOREIGN KEY (`sid`) REFERENCES `schueler` (`id`)  ON UPDATE CASCADE ON DELETE RESTRICT,
	ADD FOREIGN KEY (`fid`) REFERENCES `faecher` (`id`)  ON UPDATE CASCADE ON DELETE RESTRICT;
	
ALTER TABLE `zeit` ADD FOREIGN KEY (`sid`) REFERENCES `schueler` (`id`)  ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE `zeit` ADD FOREIGN KEY (`lid`) REFERENCES `lehrer` (`id`)  ON UPDATE CASCADE ON DELETE RESTRICT;
	
ALTER TABLE `bietet_an` ADD FOREIGN KEY (`lid`) REFERENCES `lehrer`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `bietet_an` ADD FOREIGN KEY (`fid`) REFERENCES `faecher`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `fragt_nach` ADD FOREIGN KEY (`sid`) REFERENCES `schueler`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `fragt_nach` ADD FOREIGN KEY (`fid`) REFERENCES `faecher`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE; 


INSERT INTO `faecher` (`kuerzel`,`name`) VALUE ('eng','Englisch'),('ma','Mathematik'),('de','Deutsch'),('frz','Französisch'),('phy','Physik'),('ch','Chemie'),('bio','Biologie'),('geo','Geographie');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
