-- phpMyAdmin SQL Dump
-- version 4.6.6deb1+deb.cihar.com~xenial.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 22, 2017 at 09:05 AM
-- Server version: 5.7.17-0ubuntu0.16.04.2
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

DELETE FROM `faecher`;
INSERT INTO `faecher` (`id`, `kuerzel`, `name`) VALUES
(1, 'eng', 'Englisch'),
(2, 'ma', 'Mathematik'),
(3, 'de', 'Deutsch'),
(4, 'frz', 'Französisch'),
(5, 'phy', 'Physik'),
(6, 'ch', 'Chemie'),
(7, 'bio', 'Biologie'),
(8, 'geo', 'Geographie');

DELETE FROM `person`;
INSERT INTO `person` (`id`, `vname`, `nname`, `email`, `telefon`, `geburtstag`, `hinzugefuegt`) VALUES
(1, 'Marc', 'Roggentin', 'Marc_Sachsen@online.de', '', NULL, '2017-04-20 18:38:53'),
(2, 'Aaron', 'Böhme', 'boeanj@web.de', '03528410457', NULL, '2017-04-20 18:39:58');

DELETE FROM `lehrer`;
INSERT INTO `lehrer` (`id`, `pid`, `schuljahr`, `klassenstufe`, `klasse`, `klassenlehrer_name`, `comment`) VALUES
(1, 1, 1617, 8, 'a', 'Frau Bau', '');

DELETE FROM `schueler`;

DELETE FROM `zeit`;
INSERT INTO `zeit` (`id`, `sid`, `lid`, `tag`, `anfang`, `ende`) VALUES
(1, NULL, 1, 'mo', '15:00:00', '16:00:00'),
(3, NULL, 1, 'mi', '15:00:00', '16:00:00');

DELETE FROM `bietet_an`;
INSERT INTO `bietet_an` (`id`, `lid`, `fid`, `nachweis_vorhanden`, `status`) VALUES
(1, 1, 1, 0, 'neu');

DELETE FROM `fragt_nach`;
DELETE FROM `unterricht`;