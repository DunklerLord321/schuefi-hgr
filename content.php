<?php
require 'header.php';
require 'includes/global_vars.inc.php';
if(!function_exists('get_users_logged_in')) {
	require 'includes/functions.inc.php';
}
echo 		"<h2>Hauptseite</h2>";
if(isset($_SESSION['userid']) && if_logged_in($_SESSION['userid'])) {	
	$return = get_users_logged_in();
	echo "<br>".$return[0]." Nutzer sind online:<br>";
	for($i = 0; $i < $return[0]; $i++) {
		echo "- ".utf8_encode($return[1][$i]["vname"]).' '.utf8_encode($return[1][$i]["nname"]).' '.$return[1][$i]["email"]."<br>";
	}
// phpinfo();
	if ($_SESSION ['account'] == "root") {
		echo "<br><br><a href=\"registrate.php\">Neuer Nutzer</a>";
	}
	
}else{
	echo "EIn Fehler ist aufgetreten. Bitte <a href=\"index.php\">melde dich erneut an</a>";
}
?>
</div>
</body>
</html>
