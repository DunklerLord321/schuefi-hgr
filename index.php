<!-- -------------------------------






	LOGIN



 -->
<?php
// wichtig, da header.php erst später eingebunden
require 'includes/global_vars.inc.php';
session_start ();
require 'includes/functions.inc.php';
$pdo_login = new PDO ( 'mysql:host=localhost;dbname=schuefi_login', $login_user, $login_user_passwd );
// echo "test";
$show_anmelden = true;
if (isset ( $_SESSION ['userid'] ) && strlen ( $_SESSION ['username'] ) != 0) {
	$output = "<br>Sie sind schon als " . $_SESSION ['vname'] . " " . $_SESSION ['nname'] . " mit der E-mail-Adresse " . $_SESSION ['username'] . " angemeldet.<br><br><a href=\"logout.php\">Hier geht es zum Logout.</a>";
	$show_anmelden = false;
}
if (isset ( $_GET ['login']) && $_GET['login'] == 1 ) {
	echo "try";
	$email = $_POST ['email'];
	$passwort = $_POST ['passwort'];
	// echo "isssssset".$email;&& $user['count_login'] < 5
	
	$statement = $pdo_login->prepare ( "SELECT * FROM users WHERE email = :email" );
	// echo "prepare";
	$result = $statement->execute ( array (
			'email' => $email 
	) );
	$user = $statement->fetch ();
	// echo "hallo";
	// Überprüfung des Passworts
	if ($user !== false && password_verify ( $passwort, $user ['passwd'] ) && $user['count_login'] < 5) {
		session_regenerate_id();
		$_SESSION ['userid'] = $user ['id'];
		echo "hhahahha";
		$_SESSION ['username'] = $user ['email'];
		$_SESSION ['vname'] = $user ['vname'];
		$_SESSION ['nname'] = $user ['nname'];
		$_SESSION ['account'] = $user ['account'];
		log_in ( $user ['id'] );
		echo "log in";
		$output = "Hallo " . $_SESSION ['vname'] . " " . $_SESSION ['nname'] . "!<br><br>Du wurdest erfolgreich angemeldet.<br><br>
		<meta http-equiv=\"refresh\" content=\"3;url=content.php\"
		<br>Du wirst in 3 Sekunden auttomatisch auf die Hauptseite weitergeleitet.<br><br>
 		Für manuelle Weiterleitung hier klicken: <a href=\"content.php\">Hautpseite</a>";
		$show_anmelden = false;
		echo "suces";
	} else {
		$errorMessage = "E-Mail oder Passwort war ungültig<br>";
		if ($user !== false) {
			$statement = $pdo_login->prepare ( "UPDATE users SET count_login = :count WHERE email = :email" );
			// echo "prepare";
			$result = $statement->execute ( array (
					'count' => $user ['count_login'] + 1,
					'email' => $email 
			) );
			if($user['count_login'] > 5) {
				$output = "Sie haben sich mindestens fünfmal versucht, mit falschem Passwort anzumelden.<br><br> Bitte kontaktieren sie den Admin.";
				unset($errorMessage);
				$show_anmelden = false;
			}
		}else{
			$show_anmelden = true;
		}
	}
}
require 'header.php';
echo "<h2>Login</h2>";
if ( isset ( $output )) {
	echo $output;
}
if (isset ( $errorMessage )) {
	echo $errorMessage;
}
if ($show_anmelden) {
	?>
<form action="?login=1" method="post">
	E-Mail:<br> <input type="email" size="40" maxlength="250" name="email"
		autofocus><br> <br> Dein Passwort:<br> <input type="password"
		size="40" maxlength="250" name="passwort"><br> <br> <input
		type="submit" value="Anmelden">
</form>
<!-- div bginnt in header.php -->
</div>
</body>
</html>
<?php
}
?>