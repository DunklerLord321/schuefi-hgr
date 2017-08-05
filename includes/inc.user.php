<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Profil ändern</h2>";
	$show_formular = true;
	if (isset($_GET['change']) && $_GET['change'] == 1) {
		if($user->neuespassword($_POST['passwort_alt'], $_POST['passwort_neu1'], $_POST['passwort_neu2'])) {
			unset($_SESSION['user']);
			echo "Dein Passwort wurde erfolgreich geändert. Bitte melde dich erneut an.
					<meta http-equiv=\"refresh\" content=\"5;url=index.php\"
					<br>Du wirst in 5 Sekunden auttomatisch zum Logout weitergeleitet.<br><br>
			  		Für manuelle Weiterleitung hier klicken: <a href=\"index.php\">Login</a>";
			$show_formular = false;
		}else{
			echo $user->geterror();
		}
	}
	
	if ($show_formular) {
		?>
<form action="index.php?page=user&change=1" method="post">
	Dein Passwort:
	<br>
	<input type="password" size="40" maxlength="250" name="passwort_alt" required="required" autofocus="autofocus" placeholder="Passwort" value="" class="input_text">
	<br>
	<br>
	Neues Passwort:
	<br>
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
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>
