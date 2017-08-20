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

INSERT INTO `users` (`id`, `vname`, `nname`, `email`, `passwort`, `account`, `createt_time`, `update_time`, `count_login`) VALUES
(8, 'Vorstand', 'Schülerfirma', 'schuelerfirma.hgr@gmx.de', '$2y$10$GPNqeeX7XGZx0mmydSMWpupnEYCEVstwzvDvJZNbAiZdT0bJG1I2q', 'v', '2017-08-06 16:42:15', '2017-08-06 16:42:15', 0),
(9, 'Finanzvorstand', 'Schülerfirma', 'schuelerfirma.hgr.finanzen@test.de', '$2y$10$DVmbZkojvWRGKdbl3hbA2u1dB4nSrgHrrL/Lvyu0YyAY6jSswTqvW', 'f', '2017-08-06 16:45:35', '2017-08-06 16:45:35', 0),
(10, 'Kundenbetreuer', 'Schülerfirma', 'schuelerfirma.hgr.kunden@test.de', '$2y$10$.kmQ9xuJRUvvTC18ZharVOBI2I.rwoQGv5JCe6W93xWnNapO4wJIm', 'k', '2017-08-06 16:49:03', '2017-08-06 16:49:03', 0),
(11, 'Yannik', 'Weber', 'yajo10@yahoo.de', '$2y$10$i5rueGBkBTwMKMhPUmSk5uxtoxMg5LNEpgrsDytjF9yyxqfA7L5Gi', 'v', '2017-08-06 16:50:05', '2017-08-06 16:50:05', 0),
(12, 'Karla', 'Großer', 'karla-grosser@gmx.de', '$2y$10$3fR1SlvXlDun04Z7CdiDDuOBuEPaD4DDrcFnhRIYIS8YL/8T2QEOe', 'v', '2017-08-06 16:51:10', '2017-08-06 16:51:10', 0),
(13, 'Lena', 'Haucke', 'lenahaucke@gmail.com', '$2y$10$5IrcN0HSZA5gw9khZQRBwuYhw4iC9R3JqnMmeBqGPPXF73HDlvpDe', 'k', '2017-08-06 16:52:18', '2017-08-06 16:52:18', 0),
(14, 'Anna', 'Hornhauer', 'annahornhauer@aol.com', '$2y$10$40Jb/5TJHlDsUpGyKT.qBOh6ouKSkMklUJQVud3.QMnbafWvWyK62', 'k', '2017-08-06 16:52:49', '2017-08-06 16:52:49', 0),
(15, 'Hanna', 'Liebig', 'hanna.liebig.2000@gmail.com', '$2y$10$3DwmedxLgSeDqEqp4j427eGj8DMCzmTqkGKOY7Ji7Wm0uawHjZukm', 'k', '2017-08-06 16:53:08', '2017-08-06 16:53:08', 0),
(16, 'Isabelle', 'Sickert', 'isa312@gmx.de', '$2y$10$7CsWOTfGtWAsckDpDFnOvulHOSX04XCi2ik8/8OH1Gaau.CDVOAEO', 'k', '2017-08-06 16:53:39', '2017-08-06 16:53:39', 0),
(17, 'Laura', 'Böhme', 'boehmelaura@gmx.de', '$2y$10$zyQElqp1kYzMMzJiU09aVeALeDgEwgE6tpNL8WAZQHAXCXXs5.o0.', 'k', '2017-08-06 16:54:29', '2017-08-06 16:54:29', 0);
