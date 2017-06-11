<?php 
//global $GLOBAL_CONFIG;
$GLOBAL_CONFIG = array();

// 0 = nur return false, 1 = die();
$GLOBAL_CONFIG['$exit_on_db_failure'] = 0;
$GLOBAL_CONFIG['login_user'] = 'sec_login';
$GLOBAL_CONFIG['dbuser'] = 'schuefi';
$GLOBAL_CONFIG['mail_address'] = 'schuelerfirma.hgr@gmx.de';

//passwörter werden in includes/db_data.php gesetzt
$GLOBAL_CONFIG['login_user_passwd'] = '';
$GLOBAL_CONFIG['dbuser_passwd'] = '';
$GLOBAL_CONFIG['mail_passwd'] = '';
require 'includes/db_data.php';

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