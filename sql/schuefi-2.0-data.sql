-- phpMyAdmin SQL Dump
-- version 4.6.6deb1+deb.cihar.com~xenial.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 13, 2017 at 02:03 PM
-- Server version: 5.7.18-0ubuntu0.16.04.1
-- PHP Version: 7.0.18-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `schuefi`
--

--
-- Dumping data for table `bietet_an`
--
INSERT INTO `person` (`id`, `vname`, `nname`, `email`, `telefon`, `geburtstag`, `hinzugefuegt`) VALUES
(1, 'Marc', 'Roggentin', 'Marc_Sachsen@online.de', '', NULL, '2017-04-20 18:38:53'),
(2, 'Aaron', 'Böhme', 'boeanj@web.de', '03528410457', NULL, '2017-04-20 18:39:58'),
(3, 'Lea', 'Probst', 'leapro@freenet.de', '03528418453', NULL, '2017-05-14 07:38:51'),
(4, 'Valenta', 'Krämer', 'valenta.kraemer@gmail.com', '03520177700; 015233644119', NULL, '2017-05-14 07:55:54'),
(5, 'Simon', 'Weber', 'simon-weber99@web.de', '015209828815', NULL, '2017-05-15 10:56:56'),
(6, 'Merle', 'Sivher', 'wiebke_sihver@hotmail.com', '01633790732', NULL, '2017-05-15 11:50:28'),
(7, 'Claudia', 'Meißner', 'claudia.meissner2@gmail.com', '', NULL, '2017-05-15 11:57:23'),
(8, 'Lukas', 'Schneider', 'dani.s72@web.de', '01721969611', NULL, '2017-05-15 11:59:30'),
(9, 'Helene', 'Gebel', 'helene@gebel.de', '017620745720', '2003-01-11', '2017-05-19 16:11:36'),
(10, 'Sarah', 'Striemka', 'SarahStrimke2001@web.de', '01624562146', NULL, '2017-05-23 11:19:19');

--
-- Dumping data for table `faecher`
--

INSERT INTO `faecher` (`id`, `kuerzel`, `name`) VALUES
(1, 'ma', 'Mathematik'),
(2, 'eng', 'Englisch'),
(3, 'de', 'Deutsch'),
(4, 'frz', 'Französisch'),
(5, 'phy', 'Physik'),
(6, 'ch', 'Chemie'),
(7, 'bio', 'Biologie'),
(8, 'rus', 'Russisch'),
(9, 'la', 'Latein');

--
-- Dumping data for table `fragt_nach`
--


--
-- Dumping data for table `lehrer`
--

INSERT INTO `lehrer` (`id`, `pid`, `schuljahr`, `klassenstufe`, `klasse`, `klassenlehrer_name`, `comment`, `hinzugefuegt`) VALUES
(1, 1, 1617, 8, 'a', 'Frau Bau', '', '2017-05-14 05:32:28'),
(2, 3, 1617, 9, 'a', 'Frau Döring', '', '2017-05-14 07:48:43'),
(3, 4, 1617, 8, 'l', 'Frau Bau', '', '2017-05-14 07:57:30'),
(4, 5, 1617, 11, '3', 'Herr Madel', '', '2017-05-15 10:58:23'),
(5, 7, 1617, 10, 'l', 'Herr Loitsch', '', '2017-05-15 11:58:19'),
(6, 9, 1617, 8, 'l', 'Frau Bau', '', '2017-05-19 16:13:19');

--
-- Dumping data for table `person`
--

--
-- Dumping data for table `schueler`
--

INSERT INTO `schueler` (`id`, `pid`, `schuljahr`, `klassenstufe`, `klasse`, `klassenlehrer_name`, `comment`, `hinzugefuegt`) VALUES
(1, 2, 1617, 8, 'b', 'Frau Burkhardt', '', '2017-05-14 07:35:09'),
(2, 6, 1617, 8, 'b', 'Frau Burkhardt', '', '2017-05-15 11:51:26'),
(3, 8, 1617, 8, 'b', 'Frau Burkhardt', '', '2017-05-15 12:01:11'),
(4, 10, 1617, 8, 'b', 'Frau Burkhardt', '', '2017-05-23 11:20:12');

--
-- Dumping data for table `unterricht`
--

INSERT INTO `fragt_nach` (`id`, `sid`, `fid`, `fachlehrer`, `langfristig`, `status`) VALUES
(2, 2, 9, 'Frau Krenzke', 1, 'neu'),
(3, 3, 4, 'Frau Michael', 1, 'neu'),
(33, 1, 2, 'Herr Loitsch', 1, 'neu'),
(41, 4, 1, 'Frau Valley', 1, 'neu');


INSERT INTO `bietet_an` (`id`, `lid`, `fid`, `fachlehrer`, `notenschnitt`, `nachweis_vorhanden`, `status`) VALUES
(3, 4, 1, 'Herr Unbekannt', '0.0', 1, 'neu'),
(4, 5, 1, 'Herr Loitsch', '0,0', 1, 'neu'),
(5, 6, 2, 'Herr Diestel', '1,2', 1, 'neu'),
(9, 3, 4, 'Frau Bau', '1.2', 1, 'neu'),
(10, 2, 8, 'Frau Döring', '1,3', 1, 'neu'),
(11, 1, 2, 'Frau Bachmann', '1.2', 1, 'neu');


--
-- Dumping data for table `zeit`
--

INSERT INTO `zeit` (`id`, `sid`, `lid`, `tag`, `anfang`, `ende`) VALUES
(4, NULL, 4, 'mo', '14:00:00', '15:30:00'),
(5, NULL, 4, 'di', '14:00:00', '14:45:00'),
(6, NULL, 4, 'mi', '14:00:00', '14:45:00'),
(7, 2, NULL, 'mo', '14:00:00', '16:00:00'),
(8, 2, NULL, 'do', '13:00:00', '14:00:00'),
(9, NULL, 5, 'mi', '14:00:00', '15:00:00'),
(10, 3, NULL, 'di', '13:15:00', '15:00:00'),
(11, 3, NULL, 'do', '14:00:00', '17:00:00'),
(12, 3, NULL, 'mi', '13:15:00', '15:00:00'),
(13, NULL, 6, 'do', '14:00:00', '15:30:00'),
(16, 1, NULL, 'mo', '13:00:00', '14:00:00'),
(20, 4, NULL, 'mo', '13:00:00', '15:00:00'),
(28, NULL, 3, 'mo', '13:00:00', '14:00:00'),
(29, NULL, 1, 'mo', '13:00:00', '16:00:00'),
(30, NULL, 1, 'mi', '15:00:00', '16:00:00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;