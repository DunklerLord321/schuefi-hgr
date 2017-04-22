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
('change', 'includes/inc.change.php', 'k', 0),
('content', 'includes/inc.content.php', 'w', 1),
('input', 'input.php', 'k', 0),
('input_paar', 'input_paar.php', 'k', 0),
('mail', 'includes/inc.mail.php', 'kf', 1),
('output', 'includes/inc.output.php', 'w', 1),
('output_paar', 'output_paar.php', 'w', 0),
('registrate', 'includes/registrate.php', 'v', 1),
('settings', 'includes/inc.settings.php', 'kf', 1),
('user', 'includes/inc.user.php', 'w', 1),
('person', 'includes/inc.person.php', 'fk', 1),
('output_person' ,'includes/inc.output_person.php', 'w', 1);

