<?php 
global $GLOBAL_CONFIG;
$GLOBAL_CONFIG = array();

// 0 = nur return false, 1 = die();
$exit_on_db_failure = 0;
global $login_user;
global $login_user_passwd;
global $dbuser;
global $dbuser_passwd;
global $mail_address;
global $mail_passwd;
$login_user = 'sec_login';
$dbuser = 'schuefi';
$mail_address = 'schuelerfirma.hgr@gmx.de';
$GLOBAL_CONFIG['login_user'] = 'sec_login';
$GLOBAL_CONFIG['dbuser'] = 'schuefi';
$GLOBAL_CONFIG['mail_address'] = 'schuelerfirma.hgr@gmx.de';

//passwörter werden in includes/db_data.php gesetzt
$GLOBAL_CONFIG['login_user_passwd'] = '';
$GLOBAL_CONFIG['dbuser_passwd'] = '';
$GLOBAL_CONFIG['mail_passwd'] = '';
require 'includes/db_data.php';

//um in <select> angezeignet zu werden
global $faecher_lesbar;
$faecher_lesbar = array(
		"Wähle ein Fach",
		"Deutsch",
		"Mathematik",
		"Physik",
		"Chemie",
		"Biologie",
		"Englisch",
		"Französisch",
		"Russisch",
		"Spanisch",
		"Latein",
		"Geschichte",
		"grw",
		"Musik",
		"Ethik",
//		"evangelische Religion",
//		"katholische Religion",
);
//zum Speichern der Infos in der Datenbank
global $faecher;
$faecher = array(
		"",
		"de",
		"ma",
		"phy",
		"che",
		"bio",
		"eng",
		"fra",
		"rus",
		"spa",
		"lat",
		"ge",
		"grw",
		"mu",
		"eth",
//		"relev",
//		"relkat",
);

$GLOBAL_CONFIG['klassen'] = array(
		"a",
		"b",
		"c",
		"d",
		"l",
		"A",
		"B",
		"C",
		"D",
		"l",
		"l1",
		"l2",
		"L1",
		"L2",
		"1",
		"2",
		"3",
		"4",
		"5",
		"6"
);
?>