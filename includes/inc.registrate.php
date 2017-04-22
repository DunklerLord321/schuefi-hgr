<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Neuer Nutzer</h2>";
	$showFormular = true;
	global $pdo_obj;
	if (isset($_GET['register'])) {
		$error = false;
		$email = $_POST['email'];
		$vname = $_POST['vname'];
		$nname = $_POST['nname'];
		$type = $_POST['account'];
		$passwort = $_POST['passwort'];
		$passwort2 = $_POST['passwort2'];
		// echo $email . "<br>" . $passwort . "<br>" . $passwort2 . "<br>Error" . $error . "<br>";
		
		if (strlen($vname) == 0 || strlen($vname) > 49) {
			echo 'Bitte einen gültigen Vornamen angeben';
			$error = true;
		}
		if (strlen($nname) == 0) {
			echo 'Bitte einen gültigen Nachnamen angeben';
			$error = true;
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo 'Bitte eine gültige E-Mail-Adresse eingeben<br>';
			$error = true;
		}
		if (strlen($passwort) == 0 || strlen($passwort) < 4) {
			echo 'Bitte ein Passwort angeben mit mindestens 4 Zeichen.<br>';
			$error = true;
		}
		if ($passwort != $passwort2) {
			echo 'Die Passwörter müssen übereinstimmen<br>';
			$error = true;
		}
		
		// Überprüfe, dass die E-Mail-Adresse noch nicht registriert wurde
		// echo "testerror" . $error . "<br>";
		if (!$error) {
			$statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
			$result = $statement->execute(array(
					'email' => $email
			));
			$user = $statement->fetch();
			
			if ($user !== false) {
				echo 'Diese E-Mail-Adresse ist bereits vergeben<br><br>';
				$error = true;
			}
		}
		
		// Keine Fehler, wir können den Nutzer registrieren
		if (!$error) {
			$passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);
			echo $passwort_hash;
			$statement = $pdo->prepare("INSERT INTO users (vname, nname, email, passwd, account) VALUES (:vname, :nname, :email, :passwd, :account)");
			$result = $statement->execute(array(
					'vname' => $vname,
					'nname' => $nname,
					'email' => $email,
					'passwd' => $passwort_hash,
					'account' => $type
			));
			echo "<br>RESULT:" . $result;
			
			if ($result) {
				echo 'Du wurdest erfolgreich registriert. <a href="index.php">Zum Login</a>';
				$showFormular = false;
			} else {
				echo 'Beim Abspeichern ist leider ein Fehler aufgetreten<br>';
			}
		}
	}
	
	if ($showFormular) {
		?>
<div id="formular">
	<form action="?register=1" method="post">
		Vorname:
		<br>
		<input type="text" size="40" maxlength="49" name="vname" autofocus>
		<br>
		<br>
		Nachname:
		<br>
		<input type="text" size="40" maxlength="49" name="nname">
		<br>
		<br>
		E-Mail:
		<br>
		<input type="email" size="40" maxlength="250" name="email">
		<br>
		<br>
		Accounttype:
		<br>
		<input type="radio" name="account" value="view-only">
		Nur lesend
		<input type="radio" name="account" value="normal" checked="checked">
		Schreibend und Lesend
		<input type="radio" name="account" value="root">
		Admin
		<br>
		<br>
		Passwort:
		<br>
		<input type="password" size="40" maxlength="250" name="passwort">
		<br>
		<br>
		Passwort wiederholen:
		<br>
		<input type="password" size="40" maxlength="250" name="passwort2">
		<br>
		<br>
		<input type="submit" value="Abschicken">
	</form>
</div>
<?php
	} // Ende von if($showFormular)
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>
