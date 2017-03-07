<?php
if (! isset ( $login_user )) {
	include "includes/global_vars.inc.php";
}
// echo "hallo";
$pdo_login = new PDO ( 'mysql:host=localhost;dbname=schuefi_login', $login_user, $login_user_passwd );
$ret_prep = $pdo_login->query ( "SELECT * FROM `users`" );
$return = $ret_prep->fetch ();
// echo "while";
$i = 0;
while ( $return != false ) {
	// echo "test";
	if ($return ['logged_in']) {
		$i ++;
	}
	$return = $ret_prep->fetch ();
}
if($i == 1) {
	echo "Ein Nutzer ist online";
}elseif ($i == 0) {
	echo "Kein Nutzer ist online";
}else{
	echo "$i Nutzer sind online";
}
?>