USE `schuefi`;

DROP TABLE IF EXISTS `navigation`;
CREATE TABLE `navigation`(
	`kuerzel` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`path` varchar(255) COLLATE utf8_unicode_ci  NOT NULL,
	`allowed_users` enum('w','k','f','v','kf') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'w',
	`visible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- w - Alle dürfen auf Seite zugreifen
-- k - BLoß Kundenbetreuer
-- f - Bloß Finanzler
-- v - bloß der Vorstand


--
-- visible:
-- 1 = sichtbar
-- 0 = im Moment nicht sichtbar
--


ALTER TABLE `navigation`
	ADD PRIMARY KEY (`kuerzel`);

INSERT INTO `navigation` (`kuerzel`, `path`, `allowed_users`, `visible`) VALUES
('change', 'includes/inc.change.php', 'k', 1),
('content', 'includes/inc.content.php', 'w', 1),
('input', 'includes/inc.input.php', 'k', 1),
('input_paar', 'includes/inc.input_paar.php', 'k', 1),
('mail', 'includes/inc.mail.php', 'kf', 1),
('output', 'includes/inc.output.php', 'w', 1),
('registrate', 'includes/inc.registrate.php', 'v', 1),
('settings', 'includes/inc.settings.php', 'kf', 1),
('user', 'includes/inc.user.php', 'v', 1),
('change_passwd', 'includes/inc.change_passwd.php', 'w', 1),
('person', 'includes/inc.person.php', 'k', 1),
('imap', 'includes/inc.imapmail.php', 'v', 1),
('filter', 'includes/inc.filter.php', 'w', 1),
('create_doc', 'includes/inc.create_doc.php', 'k',1),
('output_person' ,'includes/inc.output_person.php', 'w', 1),
('input_finanzen', 'includes/inc.input_finanz.php','f',1),
('output_finanzen', 'includes/inc.output_finanz.php', 'w', 1),
('backup_data', 'includes/inc.backup_data.php', 'v',1),
('input_raum', 'includes/inc.input_raum.php', 'k', 1),
('output_raum', 'includes/inc.output_raum.php', 'w', 1);
