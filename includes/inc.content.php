<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Hauptseite</h2>";
	echo "<h3>Dokumentation</h3>";
	echo "Folgendes ist zu beachten:<br>Wenn du einen neuen Schüler oder Lehrer hinzufügen willst, musst du zuerst eine neue Person hinzufügen. Danach kannst du der Person eine Rolle als Lehrer oder/und Schüler geben. Die Schüler/Lehrer sind für nur ein Schuljahr aktiviert, die Personen allerdings so lange, bis sie deaktiviert werden";
	echo "<br><br><b>Alle E-Mails landen jetzt bei den angegeben Adressen und als CC bei schuelerfirma@hgr-web.de</b>";
	// phpinfo();
	if ($user->isuserallowed('v')) {
		echo "<br><br><a href=\"index.php?page=registrate\" class=\"links2\">Neuer Nutzer</a>";
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>
