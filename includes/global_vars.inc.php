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
$GLOBAL_CONFIG['backup_dir'] = 'docs/backup/';
?>