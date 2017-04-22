<?php
if (isset($user) && $user->runscript()) {
	if (!$user->is_valid()) {
		if (!isset($_GET['loginreal']) || $_GET['loginreal'] != 1) {
			?>
<form action="?loginreal=1" method="post">
	E-Mail:
	<br>
	<input type="email" size="40" maxlength="250" name="email" autofocus>
	<br>
	<br>
	Dein Passwort:
	<br>
	<input type="password" size="40" maxlength="250" name="passwort">
	<br>
	<br>
	<input type="submit" value="Anmelden">
</form>
<?php
		} else {
			$user->setmail($_POST['email']);
			$return = $user->testpassword($_POST['passwort']);
			if (!$return) {
				echo $user->geterror();
			} else {
				$_SESSION['user'] = serialize($user);
				// var_dump($_SESSION);
				echo "Hallo " . $user->vname . " " . $user->nname . "!<br><br>Du wurdest erfolgreich angemeldet.<br><br>
		<meta http-equiv=\"refresh\" content=\"3;url=index.php?page=content\"
		<br>Du wirst in 3 Sekunden auttomatisch auf die Hauptseite weitergeleitet.<br><br>
 		Für manuelle Weiterleitung hier klicken: <a href=\"index.php?page=content\">Hautpseite</a>";
			}
		}
	}
	// var_dump($user);
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
	