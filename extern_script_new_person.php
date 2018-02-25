<?php
require 'includes/global_vars.inc.php';
require 'includes/class_user.php';
require 'includes/class_person.php';
require 'includes/class_schueler.php';
require 'includes/class_lehrer.php';
require 'includes/functions.inc.php';

function post_values($key) {
	return $_POST['hgr-schuelerfirma-contact-'.$key];
}

function is_post_values($key) {
	return (isset($_POST['hgr-schuelerfirma-contact-'.$key]) ? true: false);
}

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
die();
if (is_post_values('firstname') && is_post_values('name') && is_post_values('email') && is_post_values('phonenumber') && is_post_values('birthday')) {
	$person = new person();
	if(!$person->load_person_name(post_values('firstname'), post_values('name'))) {
		$person->addperson(post_values('firstname'), post_values('name'), post_values('email'), post_values('phonenumber'), post_values('birthday'));
	}
}else{
	echo "Ein fataler Fehler ist aufgetreten";
}

?>
