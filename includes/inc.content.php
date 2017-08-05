<?php
if (isset($user) && $user->runscript()) {	
	echo "<h2>Hauptseite</h2>";
	echo "Diese Seite hat bisher keine Funktion.";
	// phpinfo();
	if ($user->isuserallowed('v')) {
		echo "<br><br><a href=\"index.php?page=registrate\" class=\"links2\">Neuer Nutzer</a>";
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>
