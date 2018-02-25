<?php
// global $GLOBAL_CONFIG;
$GLOBAL_CONFIG = array();

// 0 = nur return false, 1 = die();
$GLOBAL_CONFIG['$exit_on_db_failure'] = 0;
$GLOBAL_CONFIG['dbuser'] = '';
$GLOBAL_CONFIG['host'] = '';
$GLOBAL_CONFIG['dbname'] = '';
$GLOBAL_CONFIG['mail_address'] = 'schuelerfirma.sender.hgr@gmx.de';
$GLOBAL_CONFIG['bauarbeiten'] = false;

// passwörter werden in includes/db_data.php gesetzt
$GLOBAL_CONFIG['dbuser_passwd'] = '';
$GLOBAL_CONFIG['mail_passwd'] = '';
if (file_exists('includes/db_data.php')) {
	require 'includes/db_data.php';
}else if (file_exists('db_data.php')) {
	require 'db_data.php';
}

$GLOBAL_CONFIG['backup_dir'] = 'docs/backup/';
$GLOBAL_CONFIG['doc_dir'] = 'docs/unterricht/';
$GLOBAL_CONFIG['settings_file'] = "docs/settings.xml";



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
		"L", 
		"l1", 
		"l2", 
		"L1", 
		"L2", 
		"1", 
		"2", 
		"3", 
		"4", 
		"5", 
		"6", 
		"7"
);
$GLOBAL_CONFIG['stundenplan'] = array();
$GLOBAL_CONFIG['stundenplan'][5] = array('anfang' => '11:05', 'ende' => '11:50');
$GLOBAL_CONFIG['stundenplan'][6] = array('anfang' => '12:00', 'ende' => '12:45');
$GLOBAL_CONFIG['stundenplan'][7] = array('anfang' => '13:15', 'ende' => '14:00');
$GLOBAL_CONFIG['stundenplan'][8] = array('anfang' => '14:00', 'ende' => '14:45');
//Zum auslesen der XML-Dateien der Raumbelegung müssen die relevanten Stunden angegeben werden
//bei anderen Werten als 5-8 kann es zu Problemen kommen
$GLOBAL_CONFIG['unterrichtsstunden'] = array(5,6,7,8);

/*
 * Das Passwort wird beim Zurücksetzen eines Schülerfirmanutzerpassworts vegeben
 * 
 */
$GLOBAL_CONFIG['reset_passwd'] = "hgr123";
?>