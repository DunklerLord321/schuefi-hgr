<?php
if (isset($user) && $user->runscript()) {
	
	require 'mail/class.phpmailer.php';
	require 'mail/class.smtp.php';
	?>
<nav>
	<ul class="mail_steps">
		<li>
			<a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?step=1";?>" <?php if(!isset($_GET['step']) || $_GET['step'] == 1) {echo "class=\"mail_steps_active\"";}?>>1.Schritt</a>
		</li>
		<li>
			<a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?step=2";?>" <?php if(!isset($_GET['step']) || $_GET['step'] == 2) {echo "class=\"mail_steps_active\"";}?>>2.Schritt</a>
		</li>
		<li>
			<a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?step=3";?>" <?php if(!isset($_GET['step']) || $_GET['step'] == 3) {echo "class=\"mail_steps_active\"";}?>>3.Schritt</a>
		</li>
	</ul>
</nav>
<h2>Sende Mail an Schüler</h2>


<?php
	if (isset($_GET['send'])) {
		var_dump($_POST);
		$mail = new PHPMailer();
		$mail->Host = 'mail.gmx.net';
		$mail->SMTPAuth = true;
		$mail->Username = $mail_address;
		$mail->Password = $mail_passwd;
		$mail->SMTPSecure = 'tls';
		$mail->Port = 587;
		$mail->isHTML(True);
		$mail->CharSet = 'utf-8';
		$mail->SetLanguage("de");
		
		$mail->setFrom('schuelerfirma.hgr@gmx.de', 'Schülerfirma HGR');
		$mail->addAddress('yajo10@yahoo.de', 'Kundenberatung - Schülerfirma');
		// $mail->addAddress ( 'schuelerfirma.hgr@gmx.de', 'Kundenberatung - Schülerfirma' );
		// $mail->addReplyTo($email, $vorname.' '.$name);
		
		$body = "Guten Tag,<br>der/die Schüler(in) $vorname $name ($email) aus der Klasse $klasse möchte sich gerne anmelden für Nachhilfeunterricht in:<br>$faecher<br><br>Sein(e)/Ihr(e) Fachlehrer in diesem<br> Fach/Fächern ist/sind:<br>$fachlehrer<br><br>Am liebsten hätte $vorname gerne am $unterrichtszeit Nachhilfe.<br><br>Viele Grüße!";
		$mail->Subject = 'Anmeldung für Nachhilfeunterricht auf der Webseite';
		$mail->Body = $body;
		// $mail->send();
	}
	
	if (isset($_GET['reset']) && $_GET['reset'] == 1) {
		unset($_SESSION['mail_step1']);
	}
	
	if (!isset($_GET['step']) || ($_GET['step'] != 2 && $_GET['step'] != 3)) {
		?>
<a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?reset=1";?>">Zurücksetzen</a>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?step=2"?>" method="POST">
	<div style="display: flex;">
		<div style="display: inline-block; width: 20%;">
			<br>
			<br>Nachhilfelehrer:<?php
		$pdo_insert = new PDO("mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd);
		if (isset($_GET['year']) && array_search($_GET['year'], get_all_years()) !== false) {
			$table = "`lehrer-" . $_GET['year'] . "`";
		} else {
			$table = get_current_table("lehrer");
			$_GET['year'] = get_prop("current_year")[1];
		}
		$return = $pdo_insert->query("SELECT * FROM " . $table . " WHERE 1");
		if ($return == false) {
			echo "EIn PRoblem ist aufgetreten!";
		}
		$i = 0;
		$lehrer = $return->fetch();
		if ($lehrer == false) {
			echo "EIN PROBLEM";
		} else {
			while ( $lehrer ) {
				if (isset($_SESSION['mail_step1']) && isset($_SESSION['mail_step1']['dest' . $i]) && $_SESSION['mail_step1']['dest' . $i] == $lehrer['email']) {
					echo "<br><label><input type=\"checkbox\" name=\"dest-$i\" value=\"" . $lehrer['email'] . "\" checked> " . $lehrer['vname'] . " " . $lehrer['nname'] . "<br>" . $lehrer['email'] . "</label><br>";
				} else {
					echo "<br><label><input type=\"checkbox\" name=\"dest-$i\" value=\"" . $lehrer['email'] . "\"> " . $lehrer['vname'] . " " . $lehrer['nname'] . "<br>" . $lehrer['email'] . "</label><br>";
				}
				// var_dump($lehrer);
				$lehrer = $return->fetch();
				$i++;
			}
		}
		?></div>

		<div style="display: inline-block; width: 20%;">
			<br>
			<br>Nachhilfeschüler:<?php
		$pdo_insert = new PDO("mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd);
		if (isset($_GET['year']) && array_search($_GET['year'], get_all_years()) !== false) {
			$table = "`schueler-" . $_GET['year'] . "`";
		} else {
			$table = get_current_table("schueler");
			$_GET['year'] = get_prop("current_year")[1];
		}
		$return = $pdo_insert->query("SELECT * FROM " . $table . " WHERE 1");
		if ($return == false) {
			echo "EIn PRoblem ist aufgetreten!";
		}
		$schueler = $return->fetch();
		if ($schueler == false) {
			echo "EIN PROBLEM";
		} else {
			while ( $schueler ) {
				if (isset($_SESSION['mail_step1']) && isset($_SESSION['mail_step1']['dest' . $i]) && $_SESSION['mail_step1']['dest' . $i] == $schueler['email']) {
					echo "<br><label><input type=\"checkbox\" name=\"dest-$i\" value=\"" . $schueler['email'] . "\" checked> " . $schueler['vname'] . " " . $schueler['nname'] . "<br>" . $schueler['email'] . "</label><br>";
				} else {
					echo "<br><label><input type=\"checkbox\" name=\"dest-$i\" value=\"" . $schueler['email'] . "\"> " . $schueler['vname'] . " " . $schueler['nname'] . "<br>" . $schueler['email'] . "</label><br>";
				}
				// var_dump($schueler);count($_POST) == 0 ? 0 :
				$schueler = $return->fetch();
				$i++;
			}
		}
		?></div>
	</div>
	<input type="submit" value="Weiter" style="float: right;">
	<br>
	<br>
</form>
<?php
	}
	if (isset($_GET['step']) && $_GET['step'] == 2) {
		var_dump($_POST);
		if (!isset($_SESSION['mail_step1']) || $_SESSION['mail_step1'] == 0 || count($_POST) != 0) {
			$_SESSION['mail_step1'] = $_POST;
		}
		var_dump($_SESSION);
		echo "Schritt 2";
		?>
<script type="text/javascript">
	function add_text(text) {
		var content = document.getElementById("textarea1").value;
		console.log(content);
		document.getElementById("textarea1").value = content + text;
	}
	function reset_text() {
		document.getElementById("textarea1").value = '';
	}
	</script>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?step=3"?>" method="POST">

	<?php
		if (isset($_SESSION['mail_step1']['subject'])) {
			?>
	<input type="text" name="subject" placeholder="Betreff" value="<?php echo strip_tags($_SESSION['mail_step1']['subject']);?>" style="width: 40%">
	<?php
		} else {
			?>
			<input type="text" name="subject" placeholder="Betreff" style="width: 40%">
			<?php
		}
		?>
	<br>
	<br>
	<button type="button" onclick="add_text(' .vorname')">Vorname</button>
	<button type="button" onclick="add_text(' .nachname')">Nachname</button>
	<button type="button" onclick="add_text(' .email')">Email</button>
	<button type="button" onclick="reset_text()">Reset</button>
	<?php
		if (isset($_SESSION['mail_step1']['text'])) {
			?>
	<textarea name="text" rows="5" id="textarea1" placeholder="Schreibe den Text für die E-Mail hier hin..." style="width: 80%;"><?php echo strip_tags($_SESSION['mail_step1']['text']);?></textarea>	
	<?php
		} else {
			?>
	<textarea name="text" rows="5" id="textarea1" placeholder="Schreibe den Text für die E-Mail hier hin..." style="width: 80%;"></textarea>	
			<?php
		}
		?>
	<br>
	<br>
	<input type="submit" value="Weiter" style="float: right;">
	<br>
	<br>
</form>
<?php
	}
	if (isset($_GET['step']) && $_GET['step'] == 3) {
		var_dump($_POST);
		if (!isset($_SESSION['mail_step1']) || $_SESSION['mail_step1'] == 0 || count($_POST) != 0) {
			$_SESSION['mail_step1']['subject'] = $_POST['subject'];
			$_SESSION['mail_step1']['text'] = $_POST['text'];
		}
		var_dump($_SESSION);
		echo "Schritt 3";
		$pdo_insert = new PDO("mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd);
		$return_query = $pdo_insert->query("SELECT * FROM " . get_current_table("lehrer"));
		if ($return_query == false) {
			echo "EIN PROBLEM";
		} else {
			$count_lehrer = $return_query->rowCount();
		}
		$return_query = $pdo_insert->query("SELECT * FROM " . get_current_table("schueler"));
		if ($return_query == false) {
			echo "EIN PROBLEM";
		} else {
			$count_schueler = $return_query->rowCount();
		}
		$count = $count_lehrer * $count_schueler - 1;
		$return_query = $pdo_insert->query("SELECT * FROM " . get_current_table("lehrer") . " WHERE 1");
		$return_query2 = $pdo_insert->query("SELECT * FROM " . get_current_table("schueler") . " WHERE 1");
		if ($return_query == false || $return_query2 == false) {
			echo "EIN PROBLEM";
		} else {
			$destination = array();
			$person = $return_query->fetch();
			while ( $person != false ) {
				$person = validate_input($person, true);
				if (!is_array($person)) {
					echo "Ein Fehler trat auf $person<br><br>";
				} else {
					for($i = 0; $i < $count; $i++) {
						if (isset($_SESSION['mail_step1']['dest-' . $i]) && strcmp($person['email'], $_SESSION['mail_step1']['dest-' . $i]) == 0) {
							$destination[] = $person;
						}
					}
				}
				$person = $return_query->fetch();
			}
			$person2 = $return_query2->fetch();
			while ( $person2 != false ) {
				$person2 = validate_input($person2, true);
				if (!is_array($person2)) {
					echo "Ein Fehler trat auf $person2<br><br>";
				} else {
					for($i = 0; $i < $count; $i++) {
						if (isset($_SESSION['mail_step1']['dest-' . $i]) && strcmp($person2['email'], $_SESSION['mail_step1']['dest-' . $i]) == 0) {
							$destination[] = $person2;
						}
					}
				}
				$person2 = $return_query2->fetch();
			}
			var_dump($destination);
			foreach ( $destination as $person ) {
				$_SESSION['mail_step1']['text'] = strip_tags($_SESSION['mail_step1']['text']);
				$_SESSION['mail_step1']['subject'] = strip_tags($_SESSION['mail_step1']['subject']);
				var_dump($_SESSION);
				$person['text'] = $_SESSION['mail_step1']['text'];
				$person['text'] = str_replace(".vorname", $person['vname'], $person['text']);
				$person['text'] = str_replace(".nachname", $person['nname'], $person['text']);
				var_dump($person);
			}
		}
		?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?step=3"?>" method="POST">
	<input type="submit" value="Weiter" style="float: right;">
	<br>
	<br>
</form>
<?php
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>