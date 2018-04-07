<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Hauptseite</h2>";
	echo "<h3>Anleitung/Dokumentationen</h3>";
	echo "<h3>Kundenbetreuer</h3>";
	echo "<a href=\"docs/Anleitung-kunden.pdf\"class=\"links2\">Anleitung für Kundenbetreuer</a><br>";
	echo "<h4>Vorlagen der Vermittlungsdokumente:</h4>";
	echo "<a href=\"docs/formulare/Anmeldeformular_Lehrer.pdf\" class=\"links2\">Anmeldeformular Lehrer</a><br>";
	echo "<a href=\"docs/formulare/Anmeldeformular_Schueler.pdf\" class=\"links2\">Anmeldeformular Schüler</a><br>";
	echo "<a href=\"docs/formulare/Wiederanmeldeformular_Lehrer.pdf\" class=\"links2\">Wiederanmeldeformular Lehrer</a><br>";
	echo "<a href=\"docs/formulare/Wiederanmeldeformular_Schueler.pdf\" class=\"links2\">Wiederanmeldeformular Schüler</a><br>";
	echo "<a href=\"docs/formulare/Nachweis_fuer_Nachhilfelehrer_bei_Anmeldung_per_Mail.pdf\" class=\"links2\">Nachweis für Lehrer bei Anmeldung per Mail</a><br>	";
	echo "<h4>Finanzabteilung</h4>";
	echo "<a href=\"docs/Vorlage_Kassenbuch.pdf\" class=\"links2\">Muster Kassenbuch</a><br>";
	echo "<a href=\"docs/Vorlage_Bankbuch.pdf\" class=\"links2\">Muster Banbuch</a><br>";
	echo "<a href=\"docs/nachweis_lehrer.pdf\" class=\"links2\">Nachweisdokument für Nachhilfelehrer</a>";
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
