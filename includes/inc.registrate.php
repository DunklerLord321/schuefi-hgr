<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Neuer Nutzer</h2>";
	$showFormular = true;
	if (isset($_GET['register'])) {
		$return = $user->adduser($_POST['vname'], $_POST['nname'], $_POST['email'], $_POST['passwort'], $_POST['passwort2'], $_POST['account']);
		if($return !== false) {
			echo $return;
		}else {
			$user->geterror();
		}
	}
	
	if ($showFormular) {
		?>
<div id="formular">
	<form action="index.php?page=registrate&register=1" method="post">
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
		<input type="radio" name="account" value="w">
		Nur lesend
		<input type="radio" name="account" value="k" checked="checked">
		Kundenbetreuer
		<input type="radio" name="account" value="f">
		Finanzler
		<input type="radio" name="account" value="v">
		Vorstand
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
