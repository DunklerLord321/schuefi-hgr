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
	echo "<a href=\"docs/formulare/Nachweis_fuer_Nachhilfelehrer_bei_Anmeldung_per_Mail.pdf\" class=\"links2\">Nachweis für Lehrer bei Anmeldung per Mail</a><br>	";
	echo "<br><br><h3>Anleitung/Dokumentationen</h3>";
	echo "<a href=\"docs/Anleitung-kunden.pdf\"class=\"links2\">Anleitung für Kundenbetreuer</a><br>";
	echo "<br><br><h3>Wettbewerbe</h3>";
	echo "<b>LEX-Lausitzer Existenzgründer Wettbewerb - Schülerwettbewerb</b><br><br>";
	echo "<a href=\"docs/Schuelerwettbewerb Infomaterial.pdf\" class=\"links2\">Infomaterial</a><br><a href=\"docs/Teilnehmererklaerung Schueler.pdf\" class=\"links2\">Teilnehmererklärung Schüler</a> ";
	echo "<br><a href=\"docs/Checkliste-Schuelerfirmenkonzept.pdf\" class=\"links2\">Checkliste Schülerfirmenkonzept</a>";
	echo "<br><a href=\"docs/Teilnehmerfragebogen Schueler.pdf\" class=\"links2\">Teilnehmerfragebogen</a>";
	echo "<br><a href=\"docs/Teilnehmerhandbuch Schueler.pdf\" class=\"links2\">Teilnehmerhandbuch</a>";
	//	echo "<h3>Tabellen der freien Räume</h3>";
//	echo "<br>Stand: 23.10.2017<br><br><i>graue Felder werden von GTA-Angeboten belegt! <br>Blaue und Gelbe Felder sind nur in der A- oder B-Woche belegt.</i><br><br><img src=\"docs/raume1.jpg\" style=\"width:100%;\"><br><img src=\"docs/raum2.jpg\" style=\"width:100%;\">";
	// phpinfo();
	if ($user->isuserallowed('v')) {
		echo "<br><br><hr><a href=\"index.php?page=registrate\" class=\"links2\">Neuer Nutzer</a>";
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>
