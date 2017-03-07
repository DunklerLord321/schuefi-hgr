<?php 
require 'includes/global_vars.inc.php';
require 'header.php';
echo 		"<h2>Profil ändern</h2>";
$show_formular  = true;
if( !isset($_SESSION['userid'])) {
	echo "Sie sind nicht angemeldet";
	$show_formular = false;
}else {
	if(isset($_GET['change']) && $_GET['change'] == 1) {
		$pdo = new PDO ( 'mysql:host=localhost;dbname=schuefi_login', $login_user, $login_user_passwd );
		if(strlen($_POST['passwort_neu1']) == 0 || strlen($_POST['passwort_neu1']) < 4) {
			echo 'Das Passwort muss mindestens aus 4 Zeichen bestehen';
		}
		if ($_POST['passwort_neu1'] != $_POST['passwort_neu2']) {
			echo 'Die Passwörter müssen übereinstimmen<br>';
		}elseif ($_POST['passwort_neu1'] == $_POST['passwort_alt']) {
			echo "Das Passwort muss neu sein!";
		}else{
			$statement = $pdo->prepare ( "SELECT * FROM users WHERE id = :id" );
			$result = $statement->execute ( array (
					'id' => $_SESSION['userid']	) );
			$user = $statement->fetch ();
//			echo "USER:" . $user['id'];
			
			if(!password_verify($_POST['passwort_alt'], $user['passwd'])) {
				echo "Das alte Passwort stimmt nicht!";
			}elseif ($user != false) {
				$passwort_hash = password_hash ( $_POST['passwort_neu1'], PASSWORD_DEFAULT );
//				echo $passwort_hash.$_POST['passwort_neu1'];
				$statement = $pdo->prepare("UPDATE users SET passwd = :passwort_hash WHERE id = :id");
				$return = $statement->execute( array(
						'passwort_hash' => $passwort_hash,
						'id' => $user['id']
				));
				if($return) {
					echo "Passwort wurde erfolgreich geändert.
					<meta http-equiv=\"refresh\" content=\"3;url=logout.php\"
					<br>Du wirst in 3 Sekunden auttomatisch zum Logout weitergeleitet.<br><br>
			  		Für manuelle Weiterleitung hier klicken: <a href=\"content.php\">Hautpseite</a>";
					$show_formular = false;
				}
				
			}
		}
		
	}
}
if($show_formular) {
	?>
		<form action="?change=1" method="post"> 
		Dein Passwort:<br>
		<input type="password" size="40"  maxlength="250" name="passwort_alt" required="required" autofocus="autofocus"><br><br>
		Neues Passwort:<br>
		<input type="password" size="40"  maxlength="250" name="passwort_neu1" required="required"><br><br>
		Neues Passwort bestätigen:<br>
		<input type="password" size="40"  maxlength="250" name="passwort_neu2" required="required"><br><br>	
		<input type="submit" value="Ändern">
		</form> 
			
		<?php 
}
?>
</div>
</body>
</html>