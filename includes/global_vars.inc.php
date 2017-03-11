<?php 
global $login_user;
global $login_user_passwd;
global $dbuser;
global $dbuser_passwd;
$login_user = 'sec_login';
$dbuser = 'schuefi';
require 'includes/db_data.php';
global $year;
$year = '1617';
global $lehrer_table;
$lehrer_table = "lehrer1617";
global $schueler_table;
$schueler_table = "schueler1617";
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

global $klassen;
$klassen = array(
		"a",
		"b",
		"c",
		"d",
		"l",
		"l1",
		"l2",
		"1",
		"2",
		"3",
		"4",
		"5",
		"6"
);
?>