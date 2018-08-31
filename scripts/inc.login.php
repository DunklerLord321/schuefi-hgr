<?php
if (isset($user) && $user->runscript()) {
	if (!$user->is_valid()) {
		if(isset($_GET['reset_password'])) {
			echo "<h1>Registrierung</h1>";
			if (isset($_GET['change_passwd'])) {
				$user = new user();
				$user->load_user("", $_POST['userid']);
				if (!$user->validate_security_token($_POST['security_token'])) {
					echo $user->geterror();
					die();
				}
				if ($user->reset_password($_POST['passwort_neu1'], $_POST['passwort_neu2'])) {
					unset($_SESSION['user']);
					$user->delete_security_token();
					echo "Dein Passwort wurde erfolgreich gespeichert.
							<meta http-equiv=\"refresh\" content=\"5;url=index.php\"
							<br>Du wirst in 5 Sekunden auttomatisch zum Login weitergeleitet.<br><br>
								Für manuelle Weiterleitung hier klicken: <a href=\"index.php\" class=\"links2\" >Login</a>";
				}else {
					echo $user->geterror();
				}
			}else{
				if (! isset($_GET['userid']) || ! isset($_GET['security_token']) ) {
					echo "Es konnten nicht alle notwendigen Daten über den Link versendet werden. Bitte versuche es erneut";
					die();
				}
				$user = new user();
				$user->load_user("", $_GET['userid']);
				if (!$user->validate_security_token($_GET['security_token'])) {
					echo $user->geterror();
					die();
				}else{
					echo "Der Code wurde korrekt validiert. Du kannst dir nun ein neues Passwort auswählen:<br>";
				}
				echo $user->getemail();
				?>
				<form action="index.php?reset_password=1&change_passwd=1" method="post">
					Neues Passwort:
					<br>
					<input type="hidden" name="userid" value="<?php echo strip_tags($_GET['userid']);?>">
					<input type="hidden" name="security_token" value="<?php echo strip_tags($_GET['security_token']);?>">
					<input type="password" size="40" maxlength="250" name="passwort_neu1" required="required" class="input_text">
					<br>
					<br>
					Neues Passwort bestätigen:
					<br>
					<input type="password" size="40" maxlength="250" name="passwort_neu2" required="required" class="input_text">
					<br>
					<br>
					<input type="submit" value="Ändern" class="mybuttons">
				</form>
			<?php
			}
		}else{
			echo "<h1>Anmelden</h1>";
			if (!isset($_GET['loginreal']) || $_GET['loginreal'] != 1) {
				if (get_xml("bauarbeiten","value") == "true") {
					echo "<p>Achtung: Die Seite steht momenatan nur Administratoren zur Verfügung. Bitte versuche es später nochmal</p>";
				}
				if (get_xml("livesystem","value") == "false") {
					echo "<p><b>Achtung: Dies ist nicht das Livesystem. Das heißt, einige Funktionen sind nur eingeschränkt benutzbar. <br><br>Dieses System ist <i>nicht für den normalen Gebrauch</i> gedacht!</b>";
				}
				?>
				<p>Achtung: Diese Seite steht nur für Mitglieder im Organisationsteam der Schülerfirma sowie Nachhilfeschülern und Nachhilfelehrern des Humboldt-Gymnasiums Radeberg zur Verfügung. <br> 
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
				if (!$user->load_user($_POST['email'])) {
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
					if ($user->getaccount() == 'c') {
						$page = "customer_meetings";
					}else {
						$page = "content";
					}
					echo "Hallo " . $user->vname . " " . $user->nname . "!<br><br>Du wurdest erfolgreich angemeldet.<br><br>
			<meta http-equiv=\"refresh\" content=\"3;url=index.php?page=$page\"
			<br>Du wirst in 3 Sekunden auttomatisch auf die Hauptseite weitergeleitet.<br><br>
	 		Für manuelle Weiterleitung hier klicken: <a href=\"index.php?page=$page\" class=\"links2\" >Hautpseite</a>";
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
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
	