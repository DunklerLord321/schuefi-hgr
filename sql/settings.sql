-- phpMyAdmin SQL Dump
-- version 4.6.6deb1+deb.cihar.com~xenial.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 11, 2017 at 07:50 AM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.13-0ubuntu0.16.04.1

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
-- Table structure for table `lehrer`
--
CREATE TABLE `settings` (
    `key` varchar(50) NOT NULL,
    `value` varchar(500) NOT NULL,
    `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

ALTER TABLE `settings`
    ADD PRIMARY KEY (`key`);

INSERT INTO `settings` (`key`, `value`, `last_update`)
VALUES ('current_year', '1617',NULL),('all_years','1617_1718',NULL),('next_year','1718',NULL);
