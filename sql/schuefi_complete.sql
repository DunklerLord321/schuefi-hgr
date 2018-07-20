SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


DROP DATABASE IF EXISTS `schuefi`;
CREATE DATABASE IF NOT EXISTS `schuefi` DEFAULT CHARACTER SET utf8 COLLATE utf8_german2_ci;
USE `schuefi`;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `vname` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `nname` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `passwort` varchar(255) COLLATE utf8_german2_ci NOT NULL,
  `account` enum('k','f','v','g','c') COLLATE utf8_german2_ci NOT NULL DEFAULT 'g',
  `createt_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `count_login` int(11) DEFAULT 0,
  `aktiv` tinyint(4) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
	`id` int(11) NOT NULL,
	`vname` varchar(50) COLLATE utf8_german2_ci NOT NULL,
	`nname` varchar(50) COLLATE utf8_german2_ci NOT NULL,
	`email` varchar(50) COLLATE utf8_german2_ci DEFAULT NULL,
	`telefon` varchar(50) COLLATE utf8_german2_ci DEFAULT NULL,
	`geburtstag` date DEFAULT NULL,
	`hinzugefuegt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`aktiv` tinyint(1) DEFAULT 1
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
  `klassenlehrer_name` varchar(50) COLLATE utf8_german2_ci DEFAULT NULL,
  `comment` text COLLATE utf8_german2_ci,
  `hinzugefuegt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP	
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `bietet_an`;
CREATE TABLE `bietet_an` (
  `id` int(11) NOT NULL,
  `lid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `fachlehrer` varchar(50) COLLATE utf8_german2_ci DEFAULT NULL,
  `notenschnitt` varchar(50) COLLATE utf8_german2_ci DEFAULT NULL,
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
  `klassenlehrer_name` varchar(50) COLLATE utf8_german2_ci DEFAULT NULL,
  `comment` text COLLATE utf8_german2_ci,
  `hinzugefuegt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP	
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `fragt_nach`;
CREATE TABLE `fragt_nach` (
  `id` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `fachlehrer` varchar(50) COLLATE utf8_german2_ci DEFAULT NULL,
  `langfristig` tinyint(1) DEFAULT 1,
  `status` enum('neu','notwendig','ausstehend','nicht vermittelbar','vermittelt') COLLATE utf8_german2_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;


--
-- treff_raum bleibt erhalten, um Kompatibilität mit alten Backups
--
DROP TABLE IF EXISTS `unterricht`;
CREATE TABLE `unterricht` (
  `id` int(11) NOT NULL,
  `lid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `rid` int(11) DEFAULT NULL,
  `erstellungs_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `tag` enum('mo','di','mi','do','fr') COLLATE utf8_german2_ci NOT NULL,
  `treff_zeit` time DEFAULT NULL,
  `treff_zeit_ende` time DEFAULT NULL,
  `treff_raum` varchar(50) DEFAULT NULL,
  `lehrer_dokument` varchar(50) DEFAULT NULL,
  `schueler_dokument` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `raum`;
CREATE TABLE `raum` (
  `id` int(11) NOT NULL,
  `nummer` varchar(50) NOT NULL,
  `tag` enum('mo','di','mi','do','fr') COLLATE utf8_german2_ci NOT NULL,
  `stunde` enum('5','6','7','8') COLLATE utf8_german2_ci NOT NULL,
  `frei` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `finanzuebersicht`;
CREATE TABLE `finanzuebersicht` (
  `id` int(11) NOT NULL,
  `pid` int(11),
  `uid` int(11),
  `geldbetrag` float(11),
  `konto_bar` enum('bar', 'konto') COLLATE utf8_german2_ci DEFAULT NULL,
  `betreff` enum('schueler','lehrer','sonstiges') COLLATE utf8_german2_ci DEFAULT NULL,
  `bemerkung` text COLLATE utf8_german2_ci,
  `dokument` varchar(50) DEFAULT NULL,
  `datum` datetime DEFAULT CURRENT_TIMESTAMP
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

DROP TABLE IF EXISTS `nachhilfetreffen`;
CREATE TABLE `nachhilfetreffen` (
  `id` int(11) NOT NULL,
  `paar_id` int(11),
  `bemerkung` text COLLATE utf8_german2_ci,
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

ALTER TABLE `raum`
 	ADD PRIMARY KEY (`id`);
  
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

 ALTER TABLE `finanzuebersicht`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `nachhilfetreffen`
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
ALTER TABLE `raum` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `nachhilfetreffen` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `lehrer`
	ADD FOREIGN KEY (`pid`) REFERENCES `person` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `schueler`
	ADD FOREIGN KEY (`pid`) REFERENCES `person` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT;
	
ALTER TABLE `unterricht`
	ADD FOREIGN KEY (`lid`) REFERENCES `lehrer` (`id`)  ON UPDATE CASCADE ON DELETE RESTRICT,
	ADD FOREIGN KEY (`sid`) REFERENCES `schueler` (`id`)  ON UPDATE CASCADE ON DELETE RESTRICT,
	ADD FOREIGN KEY (`fid`) REFERENCES `faecher` (`id`)  ON UPDATE CASCADE ON DELETE RESTRICT,
	ADD FOREIGN KEY (`rid`) REFERENCES `raum` (`id`)  ON UPDATE CASCADE ON DELETE RESTRICT;
	
ALTER TABLE `zeit` ADD FOREIGN KEY (`sid`) REFERENCES `schueler` (`id`)  ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE `zeit` ADD FOREIGN KEY (`lid`) REFERENCES `lehrer` (`id`)  ON UPDATE CASCADE ON DELETE RESTRICT;
	
ALTER TABLE `bietet_an` ADD FOREIGN KEY (`lid`) REFERENCES `lehrer`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `bietet_an` ADD FOREIGN KEY (`fid`) REFERENCES `faecher`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `fragt_nach` ADD FOREIGN KEY (`sid`) REFERENCES `schueler`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `fragt_nach` ADD FOREIGN KEY (`fid`) REFERENCES `faecher`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `finanzuebersicht` ADD FOREIGN KEY (`pid`) REFERENCES `person`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `finanzuebersicht` ADD FOREIGN KEY (`uid`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `nachhilfetreffen` ADD FOREIGN KEY (`paar_id`) REFERENCES `unterricht`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE; 

DROP TABLE IF EXISTS `navigation`;
CREATE TABLE `navigation`(
	`kuerzel` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`path` varchar(255) COLLATE utf8_unicode_ci  NOT NULL,
	`allowed_users` enum('g','k','f','v','kf','c','a') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'w',
	`visible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- c - customer (Schüler/Lehrer), dürfen nur auf Seiten für customer zugreifen
-- a - alle dürfen auf Seite zugreifen (auch customer)
-- g - Gäste dürfen auf Seite zugreifen
-- k - BLoß Kundenbetreuer+Vorstand
-- f - Bloß Finanzler+Vorstand
-- v - bloß der Vorstand
-- fk/kf - Finanzler und Kundenbetreuer


--
-- visible:
-- 1 = sichtbar
-- 0 = im Moment nicht sichtbar
--


ALTER TABLE `navigation`
	ADD PRIMARY KEY (`kuerzel`);

	-- -- wenn Pfad der inc.login.php geändert wird, muss Pfad in index.php ebenfalls geändert werden
	
INSERT INTO `navigation` (`kuerzel`, `path`, `allowed_users`, `visible`) VALUES
('change', 'scripts/inc.change.php', 'k', 1),
('content', 'scripts/inc.content.php', 'g', 1),
('input', 'scripts/inc.input.php', 'k', 1),
('input_paar', 'scripts/inc.input_paar.php', 'k', 1),
('mail', 'scripts/inc.mail.php', 'kf', 1),
('output', 'scripts/inc.output.php', 'g', 1),
('delete', 'scripts/inc.delete.php', 'k', 1),
('registrate', 'scripts/inc.registrate.php', 'v', 1),
('settings', 'scripts/inc.settings.php', 'kf', 1),
('user', 'scripts/inc.user.php', 'v', 1),
('change_passwd', 'scripts/inc.change_passwd.php', 'a', 1),
('person', 'scripts/inc.person.php', 'k', 1),
('imap', 'scripts/inc.imapmail.php', 'v', 0),
('info', 'includes/info.php', 'v', 0),
('filter', 'scripts/inc.filter.php', 'g', 1),
('create_doc', 'scripts/inc.create_doc.php', 'k',1),
('output_person' ,'scripts/inc.output_person.php', 'g', 1),
('input_finanzen', 'scripts/inc.input_finanz.php','f',1),
('output_finanzen', 'scripts/inc.output_finanz.php', 'g', 1),
('export_finanzen', 'scripts/inc.export_finanz.php', 'f', 1),
('backup_data', 'scripts/inc.backup_data.php', 'v',1),
('input_raum', 'scripts/inc.input_raum.php', 'k', 1),
('output_raum', 'scripts/inc.output_raum.php', 'g', 1);


INSERT INTO `users` (`id`, `vname`, `nname`, `email`, `passwort`, `account`, `createt_time`, `update_time`, `count_login`, `aktiv`) VALUES
(6,	'Extern',	'Systemaufgaben',	'system@system.de',	'$2y$10$bFI39TeqDc.6LpF777nHc.f1wOWYDx9fhqBk3tBXgD4z3Mcou5fJW',	'k',	'2018-02-21 09:46:43',	'2018-02-21 09:46:43',	0,	1),
(8, 'Vorstand', 'Schülerfirma', 'schuelerfirma.hgr@gmx.de', '$2y$10$GPNqeeX7XGZx0mmydSMWpupnEYCEVstwzvDvJZNbAiZdT0bJG1I2q', 'v', '2017-08-06 16:42:15', '2017-08-06 16:42:15', 0, 1),
(9, 'Finanzvorstand', 'Schülerfirma', 'schuelerfirma.hgr.finanzen@test.de', '$2y$10$DVmbZkojvWRGKdbl3hbA2u1dB4nSrgHrrL/Lvyu0YyAY6jSswTqvW', 'f', '2017-08-06 16:45:35', '2017-08-06 16:45:35', 0, 1),
(10, 'Kundenbetreuer', 'Schülerfirma', 'schuelerfirma.hgr.kunden@test.de', '$2y$10$.kmQ9xuJRUvvTC18ZharVOBI2I.rwoQGv5JCe6W93xWnNapO4wJIm', 'k', '2017-08-06 16:49:03', '2017-08-06 16:49:03', 0, 1),
(11, 'Yannik', 'Weber', 'yajo10@yahoo.de', '$2y$10$i5rueGBkBTwMKMhPUmSk5uxtoxMg5LNEpgrsDytjF9yyxqfA7L5Gi', 'v', '2017-08-06 16:50:05', '2017-08-06 16:50:05', 0, 1);


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
