<?php
if (isset($user) && $user->runscript()) {
	if (isset($_GET['schueler']) && isset($_GET['fid'])) {
		require 'includes/class_schueler.php';
		$schueler = new schueler(-1, $_GET['schueler']);
		$schueler->get_lehrer($_GET['fid']);
	}

} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
