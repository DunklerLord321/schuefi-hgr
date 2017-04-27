<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Profil ändern</h2>";
	global $pdo;
	global $user;
	$show_formular = true;
	if (isset($_GET['change']) && $_GET['change'] == 1) {
		if (strlen($_POST['passwort_neu1']) == 0 || strlen($_POST['passwort_neu1']) < 4) {
			echo 'Das Passwort muss mindestens aus 4 Zeichen bestehen';
		}
		if ($_POST['passwort_neu1'] != $_POST['passwort_neu2']) {
			echo 'Die Passwörter müssen übereinstimmen<br>';
		} elseif ($_POST['passwort_neu1'] == $_POST['passwort_alt']) {
			echo "Das Passwort muss neu sein!";
		} else {
			$statement = $pdo->prepare("SELECT * FROM users WHERE id = :id");
			$result = $statement->execute(array(
					'id' => $user->id
			));
			$fuser = $statement->fetch();
			// echo "USER:" . $user['id'];
			
			if (!password_verify($_POST['passwort_alt'], $fuser['passwd'])) {
				echo "Das alte Passwort stimmt nicht!";
			} elseif ($fuser != false) {
				$passwort_hash = password_hash($_POST['passwort_neu1'], PASSWORD_DEFAULT);
				// echo $passwort_hash.$_POST['passwort_neu1'];
				$statement = $pdo->prepare("UPDATE `users` SET passwd = :passwort_hash WHERE id = :id");
				$return = $statement->execute(array(
						'passwort_hash' => $passwort_hash,
						'id' => $fuser['id']
				));
				if ($return) {
					unset($_SESSION['user']);
					echo "Dein Passwort wurde erfolgreich geändert. Bitte melde dich erneut an.
					<meta http-equiv=\"refresh\" content=\"5;url=index.php\"
					<br>Du wirst in 5 Sekunden auttomatisch zum Logout weitergeleitet.<br><br>
			  		Für manuelle Weiterleitung hier klicken: <a href=\"index.php\">Login</a>";
					$show_formular = false;
				}
			}
		}
	}
	
	if ($show_formular) {
		?>
<form action="index.php?page=user&change=1" method="post">
	Dein Passwort:
	<br>
	<input type="password" size="40" maxlength="250" name="passwort_alt" required="required" autofocus="autofocus" placeholder="Passwort" value="">
	<br>
	<br>
	Neues Passwort:
	<br>
	<input type="password" size="40" maxlength="250" name="passwort_neu1" required="required">
	<br>
	<br>
	Neues Passwort bestätigen:
	<br>
	<input type="password" size="40" maxlength="250" name="passwort_neu2" required="required">
	<br>
	<br>
	<input type="submit" value="Ändern">
</form>

<?php
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>
