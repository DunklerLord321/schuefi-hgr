<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Ausgabe</h2>";
	require 'includes/class_person.php';
	require 'includes/class_lehrer.php';
	require 'includes/class_schueler.php';
	require 'includes/class_paar.php';
	if (isset($_GET['schueler']) && $_GET['schueler'] == 1 && $_GET['delete']) {
		$schueler = new schueler(-1, $_GET['delete']);
		$schueler->delete();
	}
	if (isset($_GET['lehrer']) && $_GET['lehrer'] == 1 && $_GET['delete']) {
		$lehrer = new lehrer(-1, $_GET['delete']);
		$lehrer->delete();
	}
	if (isset($_GET['paar']) && $_GET['paar'] == 1 && $_GET['delete']) {
		$paar = new paar($_GET['delete']);
		$paar->delete();
	}
	if (isset($_GET['person']) && $_GET['person'] == 1 && $_GET['delete']) {
		$person = new person();
		$person->load_person($_GET['delete']);
		$person->delete();
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
	