<?php
if (isset($user) && $user->runscript()) {	
	echo "<h2>Hauptseite</h2>";
	var_dump($user);
	get_current_year();
	$return = get_users_logged_in();
	echo "<br>" . $return[0] . " Nutzer sind online:<br>";
	for($i = 0; $i < $return[0]; $i++) {
		echo "- " . $return[1][$i]["vname"] . ' ' . $return[1][$i]["nname"] . ' ' . $return[1][$i]["email"] . "<br>";
	}
	// phpinfo();
	if ($user->isuserallowed('v')) {
		echo "<br><br><a href=\"index.php?page=registrate\">Neuer Nutzer</a>";
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>
