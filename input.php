<?php
require 'header.php';
require 'includes/global_vars.inc.php';
if (! function_exists ( 'get_users_logged_in' )) {
	include 'includes/functions.inc.php';
}



echo "<h2>Eingabe</h2>";
$show_formular_schueler = true;
$show_formular_lehrer = false;
if (isset ( $_GET ['lehrer'] ) && $_GET ['lehrer'] == 1) {
	$show_formular_lehrer = true;
	$show_formular_schueler = false;
} else if (isset ( $_GET ['schueler'] ) && $_GET ['schueler'] == 1) {
	$show_formular_lehrer = false;
	$show_formular_schueler = true;
}
if (isset ( $_SESSION ['userid'] ) && isset ( $_SESSION ['username'] ) && isset ( $_SESSION ['account'] ) && (strcmp ( $_SESSION ['account'], 'normal' ) == 0 || strcmp ( $_SESSION ['account'], 'root' ) == 0) && if_logged_in ( $_SESSION ['userid'] )) {
	if (isset ( $_GET ['input'] ) && ($_GET ['input'] == 1 || $_GET ['input'] == 2)) {
		$pdo_insert = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
		$person = array(
				'vname' => $_POST ['vname'],
		'nname' => $_POST ['nname'],
		'fach1' => $_POST ['fach1'],
		'fach1_lehrer' => $_POST ['fach1_lehrer'],
		'fach2' => $_POST ['fach2'],
		'fach2_lehrer' => $_POST ['fach2_lehrer'],
		'fach3' => $_POST ['fach3'],
		'fach3_lehrer' => $_POST ['fach3_lehrer'],
		'email' => $_POST ['email'],
		'klasse' => $_POST ['klasse'],
		'klassenstufe' => $_POST ['klassenstufe'],
		'klassenlehrer_name' => $_POST ['klassenlehrer'],
		'telefon' => $_POST ['telefon'],
		'mo_anfang' => $_POST ['mo_anfang'],
		'mo_ende' => $_POST ['mo_ende'],
		'di_anfang' => $_POST ['di_anfang'],
		'di_ende' => $_POST ['di_ende'],
		'mi_anfang' => $_POST ['mi_anfang'],
		'mi_ende' => $_POST ['mi_ende'],
		'do_anfang' => $_POST ['do_anfang'],
		'do_ende' => $_POST ['do_ende'],
		'fr_anfang' => $_POST ['fr_anfang'],
		'fr_ende' => $_POST ['fr_ende'],
		'comment' => $_POST['comment']
		);
		$person = validate_input($person);
		if(!is_array($person)) {
			echo "Es trat ein Fehler auf: $person<br><br>";
		} else {
			if ($_GET ['input'] == 1) {
//				echo "try";
				$return_prep = $pdo_insert->prepare ( "SELECT * FROM ".get_current_table("schueler")." WHERE vname = :vname AND nname = :nname");
				$return = $return_prep->execute ( array (
						'vname' => $person['vname'],
						'nname' => $person['nname']
				) );
				if ($return == false)
					echo "EIn PRoblem ist aufgetreten!";
				$found_user = $return_prep->fetch ();
				if ($found_user !== false) {
					echo "Dieser Schüler existiert bereits";
				} else {
//					echo "success";
					$return_prep = $pdo_insert->prepare ( "INSERT INTO ".get_current_table("schueler")." (vname, nname, email, klassenstufe, klasse, klassenlehrer_name, fach1, fach1_lehrer, fach2, fach2_lehrer, fach3, fach3_lehrer, mo_anfang, mo_ende, di_anfang, di_ende, mi_anfang, mi_ende, do_anfang, do_ende, fr_anfang, fr_ende, telefon, comment) VALUES (:vname, :nname, :email, :klassenstufe, :klasse, :klassenlehrer_name, :fach1, :fach1_lehrer, :fach2, :fach2_lehrer, :fach3, :fach3_lehrer, :mo_anfang, :mo_ende, :di_anfang, :di_ende, :mi_anfang, :mi_ende, :do_anfang, :do_ende, :fr_anfang, :fr_ende, :telefon, :comment)" );
					$return = $return_prep->execute ( array (
							'vname' => $person['vname'],
							'nname' => $person['nname'],
							'email' => $person['email'],
							'klassenstufe' => $person['klassenstufe'],
							'klasse' => $person['klasse'],
							'klassenlehrer_name' => $person['klassenlehrer_name'],
							'fach1' => $person['fach1'],
							'fach1_lehrer' => $person['fach1_lehrer'],
							'fach2' => $person['fach2'],
							'fach2_lehrer' => $person['fach2_lehrer'],
							'fach3' => $person['fach3'],
							'fach3_lehrer' => $person['fach3_lehrer'],
							'mo_anfang' => $person['mo_anfang'],
							'mo_ende' => $person['mo_ende'],
							'di_anfang' => $person['di_anfang'],
							'di_ende' => $person['di_ende'],
							'mi_anfang' => $person['mi_anfang'],
							'mi_ende' => $person['mi_ende'],
							'do_anfang' => $person['do_anfang'],
							'do_ende' => $person['do_ende'],
							'fr_anfang' => $person['fr_anfang'],
							'fr_ende' => $person['fr_ende'],
							'telefon' => $person['telefon'],
							'comment' => $person['comment']
					) );
					if ($return) {
						echo "<br>Daten erfolgreich hinzugefügt";
						$show_formular_schueler = false;
						$show_formular_lehrer = false;
					}
				}
			} elseif ($_GET ['input'] == 2) {
//				echo get_current_table("lehrer");
				$return_prep = $pdo_insert->prepare ( "SELECT * FROM ".get_current_table("lehrer")." WHERE vname = :vname AND nname = :nname" );
				$return = $return_prep->execute ( array (
						'vname' => $person['vname'],
						'nname' => $person['nname']
				) );
				if (! $return)
					echo "Es gab ein Problem";
				$found_user = $return_prep->fetch ();
				if ($found_user !== false)
					echo "Dieser Lehrer existiert bereits";
				else {
//					echo "Inserting...";
//					var_dump($person);
					$return_prep = $pdo_insert->prepare ( "INSERT INTO ".get_current_table("lehrer")." (vname, nname, email, klassenstufe, klasse, klassenlehrer_name, fach1, fach1_lehrer, fach2, fach2_lehrer, fach3, fach3_lehrer, mo_anfang, mo_ende, di_anfang, di_ende, mi_anfang, mi_ende, do_anfang, do_ende, fr_anfang, fr_ende, telefon, comment) VALUES (:vname, :nname, :email, :klassenstufe, :klasse, :klassenlehrer_name, :fach1, :fach1_lehrer, :fach2, :fach2_lehrer, :fach3, :fach3_lehrer, :mo_anfang, :mo_ende, :di_anfang, :di_ende, :mi_anfang, :mi_ende, :do_anfang, :do_ende, :fr_anfang, :fr_ende, :telefon, :comment)" );
					$return = $return_prep->execute ( array (
							'vname' => $person['vname'],
							'nname' => $person['nname'],
							'email' => $person['email'],
							'klassenstufe' => $person['klassenstufe'],
							'klasse' => $person['klasse'],
							'klassenlehrer_name' => $person['klassenlehrer_name'],
							'fach1' => $person['fach1'],
							'fach1_lehrer' => $person['fach1_lehrer'],
							'fach2' => $person['fach2'],
							'fach2_lehrer' => $person['fach2_lehrer'],
							'fach3' => $person['fach3'],
							'fach3_lehrer' => $person['fach3_lehrer'],
							'mo_anfang' => $person['mo_anfang'],
							'mo_ende' => $person['mo_ende'],
							'di_anfang' => $person['di_anfang'],
							'di_ende' => $person['di_ende'],
							'mi_anfang' => $person['mi_anfang'],
							'mi_ende' => $person['mi_ende'],
							'do_anfang' => $person['do_anfang'],
							'do_ende' => $person['do_ende'],
							'fr_anfang' => $person['fr_anfang'],
							'fr_ende' => $person['fr_ende'],
							'telefon' => $person['telefon'],
							'comment' => $person['comment']
					) );
					var_dump($return_prep->errorInfo());
					if ($return) {
						echo "Der Lehrer wurde erfolgreich hinzugefügt.";
						$show_formular_schueler = false;
						$show_formular_lehrer = false;
					}
				}
			}
		}
	}
} else {
	if (! isset ( $_SESSION ['userid'] ) || ! isset ( $_SESSION ['username'] ) || ! if_logged_in ( $_SESSION ['userid'] )) {
		$show_formular_schueler = false;
		$show_formular_lehrer = false;
		$show_formular_paar = false;
	}
	if (isset ( $_SESSION ['account'] ) && strcmp ( $_SESSION ['account'], 'view-only' ) == 0) {
		echo "Sie sind leider nicht berechtigt, neue Daten einzugeben!";
		$show_formular_schueler = false;
		$show_formular_lehrer = false;
		$show_formular_paar = false;
	}
}

if ($show_formular_schueler) {
	?>
<div class="formular_class">
	<form action="?input=1" method="POST" novalidate="novalidate">
		<fieldset style="padding: 40px;">
			<legend>
				<b>Nachhilfeschüler</b>
			</legend>
			<br>
			Vorname:
			<span style="float: right; width: 50%;">Nachname:</span>
			<br>
			<input type="text" maxlength="49" name="vname" autofocus required style="width: 40%;">
			<input type="text" maxlength="49" name="nname" required style="width: 49%; float: right; margin-right: 5px; margin-left: 0;">
			<br>
			<br>
			Klassenstufe:
			<span style="float: right; width: 50%;">Klasse/Kurs:</span>
			<br>
			<input type="number" name="klassenstufe" min="5" max="12" required style="width: 40%;">
			<input type="text" pattern="([ABCDabcdl123456]|[lL][12])" name="klasse" required style="width: 49%; float: right; margin-right: 5px; margin-left: 0;">
			<br>
			<br>
			Klassenlehrer:
			<br>
			<input type="text" class="textinput" maxlength="49" name="klassenlehrer" required>
			<br>
			<br>
			Geburtstag
			<br>
			<input type="text" id="datepicker">
			<br>
			<br>
			Email:
			<br>
			<input type="email" class="textinput" maxlength="49" name="email">
			<br>
			<br>
			<br>
			Telefon
			<br>
			<input type="tel" name="telefon">
			<br>
			<div style="width: 20%; display: inline-block;">
				<h3>1.Fach:</h3>
				<select name="fach1" required>
				<?php 
				for ($i = 0; $i < count($faecher); $i++) {
					echo "<option value=".$faecher[$i].">".$faecher_lesbar[$i]."</option>";
				}
				?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach1_lehrer">
				<br>
			</div>
			<div style="width: 20%; display: inline-block; margin-left: 10%;">
				<h3>2.Fach:</h3>
				<select name="fach2">
				<?php 
				for ($i = 0; $i < count($faecher); $i++) {
					echo "<option value=".$faecher[$i].">".$faecher_lesbar[$i]."</option>";
				}
				?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach2_lehrer">
				<br>
			</div>
			<div style="width: 20%; display: inline-block; margin-left: 10%;">
				<h3>3.Fach:</h3>
				<select name="fach3">
				<?php 
				for ($i = 0; $i < count($faecher); $i++) {
					echo "<option value=".$faecher[$i].">".$faecher_lesbar[$i]."</option>";
				}
				?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach3_lehrer">
				<br>
			</div>
			<br>
			<br>
			<h3>Zeit:</h3>
			<br>
			<div style="display: flex;">
				<div style="display: inline-block; width: 25%;">
					Montag Anfang:
					<br>
					<input type="time" value="00:00" name="mo_anfang" class="input_time">
					<br>
					Montag Ende:
					<br>
					<input type="time" value="00:00" name="mo_ende" class="input_time">
					<br>
					Dienstag Anfang:
					<br>
					<input type="time" value="00:00" name="di_anfang" class="input_time">
					<br>
					Dienstag Ende:
					<br>
					<input type="time" value="00:00" name="di_ende" class="input_time">
					<br>
				</div>
				<div id="zeitdiv" style="display: inline-block; width: 25%;">
					Mitwoch Anfang:
					<br>
					<input type="time" value="00:00" name="mi_anfang" class="input_time">
					<br>
					Mitwoch Ende:
					<br>
					<input type="time" value="00:00" name="mi_ende" class="input_time">
					<br>
					Donnerstag Anfang
					<br>
					<input type="time" value="00:00" name="do_anfang" class="input_time">
					<br>
					Donnerstag Ende:
					<br>
					<input type="time" value="00:00" name="do_ende" class="input_time">
					<br>
				</div>
				<div style="display: inline-block; width: 25%;">
					Freitag Anfang:
					<br>
					<input type="time" value="00:00" name="fr_anfang" class="input_time">
					<br>
					Freitag Ende:
					<br>
					<input type="time" value="00:00" name="fr_ende" class="input_time">
					<br>
				</div>
			</div>
			<br>
			Kommentar:
			<textarea rows="4" name="comment" style="width: 100%;margin-top: 10px;"></textarea>
			<br>
			<br>
			<br>
			<br>
			<input type="submit" value="Hinzufügen" style="float: right;">
		</fieldset>
	</form>
</div>
<?php
}
if ($show_formular_lehrer) {
	?>
<div class="formular_class">
	<form action="?input=2" method="POST">
		<fieldset style="padding: 40px;">
			<legend>
				<b>Nachhilfelehrer</b>
			</legend>
			<br>
			Vorname:
			<span style="float: right; width: 50%;">Nachname:</span>
			<br>
			<input type="text" maxlength="49" name="vname" autofocus required style="width: 40%;">
			<input type="text" maxlength="49" name="nname" required style="width: 49%; float: right; margin-right: 5px; margin-left: 0;">
			<br>
			<br>
			Klassenstufe:
			<span style="float: right; width: 50%;">Klasse/Kurs:</span>
			<br>
			<input type="number" name="klassenstufe" min="5" max="12" required style="width: 40%;">
			<input type="text" pattern="[ABCDabcdl123456]" required name="klasse" style="width: 49%; float: right; margin-right: 5px; margin-left: 0;">
			<br>
			<br>
			Klassenlehrer:
			<br>
			<input type="text" class="textinput" maxlength="49" name="klassenlehrer" required>
			<br>
			<br>
			Geburtstag
			<br>
			<input type="text" id="datepicker">
			<br>
			<br>
			Email:
			<br>
			<input type="email" class="textinput" maxlength="49" name="email" required>
			<br>
			<br>
			Telefon
			<br>
			<input type="tel" name="telefon">
			<br>
			<div style="width: 20%; display: inline-block;">
				<h3>1.Fach:</h3>
				<select name="fach1" required>
				<?php 
				for ($i = 0; $i < count($faecher); $i++) {
					echo "<option value=".$faecher[$i].">".$faecher_lesbar[$i]."</option>";
				}
				?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach1_lehrer" required>
				<br>
			</div>
			<div style="width: 20%; display: inline-block; margin-left: 10%;">
				<h3>2.Fach:</h3>
				<select name="fach2">
				<?php 
				for ($i = 0; $i < count($faecher); $i++) {
					echo "<option value=".$faecher[$i].">".$faecher_lesbar[$i]."</option>";
				}
				?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach2_lehrer">
				<br>
			</div>
			<div style="width: 20%; display: inline-block; margin-left: 10%;">
				<h3>3.Fach:</h3>
				<select name="fach3">
				<?php 
				for ($i = 0; $i < count($faecher); $i++) {
					echo "<option value=".$faecher[$i].">".$faecher_lesbar[$i]."</option>";
				}
				?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach3_lehrer">
				<br>
			</div>
			<br>
			<br>
			<h3>Zeit:</h3>
			<br>
			<div style="display: flex;">
				<div style="display: inline-block; width: 25%;">
					Montag Anfang:
					<br>
					<input type="time" value="00:00" name="mo_anfang" class="input_time">
					<br>
					Montag Ende:
					<br>
					<input type="time" value="00:00" name="mo_ende" class="input_time">
					<br>
					Dienstag Anfang:
					<br>
					<input type="time" value="00:00" name="di_anfang" class="input_time">
					<br>
					Dienstag Ende:
					<br>
					<input type="time" value="00:00" name="di_ende" class="input_time">
					<br>
				</div>
				<div style="display: inline-block; width: 25%;">
					Mitwoch Anfang:
					<br>
					<input type="time" value="00:00" name="mi_anfang" class="input_time">
					<br>
					Mitwoch Ende:
					<br>
					<input type="time" value="00:00" name="mi_ende" class="input_time">
					<br>
					Donnerstag Anfang
					<br>
					<input type="time" value="00:00" name="do_anfang" class="input_time">
					<br>
					Donnerstag Ende:
					<br>
					<input type="time" value="00:00" name="do_ende" class="input_time">
					<br>
				</div>
				<div style="display: inline-block; width: 25%;">
					Freitag Anfang:
					<br>
					<input type="time" value="00:00" name="fr_anfang" class="input_time">
					<br>
					Freitag Ende:
					<br>
					<input type="time" value="00:00" name="fr_ende" class="input_time">
				</div>
			</div>
			<br>
			Kommentar:
			<textarea rows="4" name="comment" style="width: 100%;margin-top: 10px;"></textarea>
			<br>
			<br>
			<br>
			<br>
			<input type="submit" value="Hinzufügen" style="float: right;">
		</fieldset>
	</form>
</div>
<?php
}
?>
</div>
<footer>Designed by Yannik Weber</footer>
</body>
</html>
