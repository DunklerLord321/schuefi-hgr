<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Hauptseite</h2>";
	echo "<h3>Dokumentation</h3>";
	echo "Folgendes ist zu beachten:<br>Wenn du einen neuen Schüler oder Lehrer hinzufügen willst, musst du zuerst eine neue Person hinzufügen. Danach kannst du der Person eine Rolle als Lehrer oder/und Schüler geben. Die Schüler/Lehrer sind für nur ein Schuljahr aktiviert, die Personen allerdings so lange, bis sie deaktiviert werden";
	echo "<br><br><b>Alle E-Mails landen jetzt bei den angegeben Adressen und als CC bei schuelerfirma@hgr-web.de</b>";
	echo "<h3>Vorlagen der Vermittlungsdokumente</h3>";
	echo "<a href=\"docs/formulare/Anmeldeformular_Lehrer.pdf\" class=\"links2\">Anmeldeformular Lehrer</a><br>";
	echo "<a href=\"docs/formulare/Anmeldeformular_Schueler.pdf\" class=\"links2\">Anmeldeformular Schüler</a><br>";
	echo "<a href=\"docs/formulare/Wiederanmeldeformular_Lehrer.pdf\" class=\"links2\">Wiederanmeldeformular Lehrer</a><br>";
	echo "<a href=\"docs/formulare/Wiederanmeldeformular_Schueler.pdf\" class=\"links2\">Wiederanmeldeformular Schüler</a><br>";
	echo "<a href=\"docs/formulare/Nachweis_fuer_Nachhilfelehrer_bei_Anmeldung_per_Mail.pdf\" class=\"links2\">Nachweis für Lehrer bei Anmeldung per Mail</a><br><br>	";
	echo "<h3>Tabellen der freien Räume</h3>";
	echo "<br>Stand: 23.10.2017<br><br><i>graue Felder werden von GTA-Angeboten belegt! <br>Blaue und Gelbe Felder sind nur in der A- oder B-Woche belegt.</i><br><br><img src=\"docs/raume1.jpg\" style=\"width:100%;\"><br><img src=\"docs/raum2.jpg\" style=\"width:100%;\">";
	// phpinfo();
	if ($user->isuserallowed('v')) {
		echo "<br><br><a href=\"index.php?page=registrate\" class=\"links2\">Neuer Nutzer</a>";
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>
