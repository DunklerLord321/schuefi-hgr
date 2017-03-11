<?php
require 'header.php';
require 'includes/global_vars.inc.php';
if (! function_exists ( 'get_users_logged_in' )) {
	include 'includes/functions.inc.php';
}
echo "<h2>Einstellungen</h2>";


if (isset ( $_SESSION ['userid'] ) && isset ( $_SESSION ['username'] ) && isset ( $_SESSION ['account'] ) && (strcmp ( $_SESSION ['account'], 'normal' ) == 0 || strcmp ( $_SESSION ['account'], 'root' ) == 0) && if_logged_in ( $_SESSION ['userid'] )) {
  echo "Do some settings";
  $pdo_query = new PDO("mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd);
  $return = $pdo_query->query("SHOW TABLES like '%er%'");
  print_r($return);
  var_dump($return);
  if($return === false) {
  	echo "Ein Fehler ist passiert";
  }else{
  	$tables = $return->fetch();
  	 while ($tables !== false) {
  		print_r($tables);
  		echo $tables[0];
  		$tables = $return->fetch();
  	}
  }
}