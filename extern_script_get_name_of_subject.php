<?php
require 'includes/global_vars.inc.php';
require 'includes/class_user.php';
require 'includes/functions.inc.php';
try {
	$pdo = new PDO('mysql:host=' . $GLOBAL_CONFIG['host'] . ';dbname=' . $GLOBAL_CONFIG['dbname'], $GLOBAL_CONFIG['dbuser'], $GLOBAL_CONFIG['dbuser_passwd'], array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
	));
}catch (PDOException $e) {
	echo "<h1>Ein DB-Fehler ist aufgetreten (01)$e<h1>";
	die();
}
$user = new user();
$user->setmail('system@system.de');
$user->testpassword('hee7uThook3koth3');
if(!$user->is_valid()) {
	echo "Sie sind nicht angemeldet";
	die();
}

if (isset($_POST['fid'])) {
	echo get_faecher_name_of_id($_POST['fid']);
}else{
	echo "Ein fataler Fehler ist aufgetreten";
}

?>
