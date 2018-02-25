<?php
if (isset($user) && $user->runscript()) {
	if (!$user->is_valid()) {
		echo "<h1>Anmelden</h1>";
		if (!isset($_GET['loginreal']) || $_GET['loginreal'] != 1) {
			?>
			<p>Achtung: Diese Seite steht nur für Mitglieder im Organisationsteam der Schülerfirma des Humboldt-Gymnasiums Radeberg zur Verfügung. <br>
			Die offizielle Homepage der Schülerfirma findest du hier: <a href="https://www.hgr-web.de/schuelerfirma" class="links2">Website der Schülerfirma "Schüler helfen Schülern"</a>
			<br>Wenn du mit uns in Kontakt treten willst, kannst du uns per E-Mail unter <a href="mailto:schuelerfirma@hgr-web.de" class="links2">schuelerfirma@hgr-web.de</a> erreichen. 
<form action="?loginreal=1" method="post">
	E-Mail:
	<br>
	<input type="email" size="40" maxlength="250" name="email" autofocus class="input_text">
	<br>
	<br>
	Dein Passwort:
	<br>
	<input type="password" size="40" maxlength="250" name="passwort" class="input_text">
	<br>
	<br>
	<input type="submit" value="Anmelden" class="mybuttons">
</form>
<?php
		}else {
			if (!$user->setmail($_POST['email'])) {
				echo "Ein Fehler trat auf:<br><br>";
				echo $user->geterror();
				echo "<br><br><a href=\"index.php?login=1\" class=\"links2\">Erneut versuchen</a>";
				die();
			}
			$return = $user->testpassword($_POST['passwort']);
			if (!$return) {
				echo $user->geterror();
				echo "<br><br><a href=\"index.php?login=1\" class=\"links2\">Erneut versuchen</a>";
			}else {
				$_SESSION['user'] = serialize($user);
				echo "Hallo " . $user->vname . " " . $user->nname . "!<br><br>Du wurdest erfolgreich angemeldet.<br><br>
		<meta http-equiv=\"refresh\" content=\"3;url=index.php?page=content\"
		<br>Du wirst in 3 Sekunden auttomatisch auf die Hauptseite weitergeleitet.<br><br>
 		Für manuelle Weiterleitung hier klicken: <a href=\"index.php?page=content\" class=\"links2\" >Hautpseite</a>";
				?>
				<script type="text/javascript">
$(function() {
	$('#login').html('Angemeldet');
	
});
				</script>
				
				<?php
			}
		}
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
	