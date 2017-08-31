-- phpMyAdmin SQL Dump
-- version 4.6.6deb1+deb.cihar.com~xenial.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 19, 2017 at 12:05 PM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

-- Version 2.0

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


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `vname` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `nname` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `passwort` varchar(255) COLLATE utf8_german2_ci NOT NULL,
  `account` enum('k','f','v','w') COLLATE utf8_german2_ci NOT NULL DEFAULT 'w',
  `createt_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `count_login` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

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
  `comment` text COLLATE utf8_german2_ci,
  `hinzugefuegt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP	
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `bietet_an`;
CREATE TABLE `bietet_an` (
  `id` int(11) NOT NULL,
  `lid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `fachlehrer` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `notenschnitt` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `nachweis_vorhanden` tinyint(1) DEFAULT 1,
  `status` enum('neu','ausstehend','vermittelt') COLLATE utf8_german2_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `schueler`;
CREATE TABLE `schueler` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `schuljahr` int(11) NOT NULL,
  `klassenstufe` int(11) NOT NULL,
  `klasse` varchar(3) COLLATE utf8_german2_ci NOT NULL,
  `klassenlehrer_name` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `comment` text COLLATE utf8_german2_ci,
  `hinzugefuegt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP	
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `fragt_nach`;
CREATE TABLE `fragt_nach` (
  `id` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `fachlehrer` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `langfristig` tinyint(1) DEFAULT 1,
  `status` enum('neu','notwendig','ausstehend','nicht vermittelbar','vermittelt') COLLATE utf8_german2_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `unterricht`;
CREATE TABLE `unterricht` (
  `id` int(11) NOT NULL,
  `lid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `erstellungs_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `tag` enum('mo','di','mi','do','fr') COLLATE utf8_german2_ci NOT NULL,
  `treff_zeit` time DEFAULT NULL,
  `treff_zeit_ende` time DEFAULT NULL,
  `treff_raum` int(11) DEFAULT NULL,
  `lehrer_dokument` varchar(50) DEFAULT NULL,
  `schueler_dokument` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `finanzuebersicht`;
CREATE TABLE `finanzuebersicht` (
  `id` int(11) NOT NULL,
  `pid` int(11),
  `uid` int(11),
  `geldbetrag` int(11) NOT NULL,
  `konto_bar` enum('bar', 'konto') COLLATE utf8_german2_ci DEFAULT NULL,
  `betreff` enum('schueler','lehrer','sonstiges') COLLATE utf8_german2_ci DEFAULT NULL,
  `bemerkung` text COLLATE utf8_german2_ci,
  `dokument` varchar(50) DEFAULT NULL,
  `datum` datetime DEFAULT CURRENT_TIMESTAMP
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
  
 ALTER TABLE `unterricht`
 	ADD PRIMARY KEY (`id`);
  
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

 ALTER TABLE `finanzuebersicht`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `person` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `lehrer` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `schueler` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `faecher` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `zeit` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `bietet_an` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `fragt_nach` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `unterricht` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `users`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `finanzuebersicht` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
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
ALTER TABLE `finanzuebersicht` ADD FOREIGN KEY (`pid`) REFERENCES `person`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `finanzuebersicht` ADD FOREIGN KEY (`uid`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE; 

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
