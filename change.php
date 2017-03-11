<?php
require 'header.php';
require 'includes/global_vars.inc.php';
if (! function_exists ( 'get_users_logged_in' )) {
	include 'includes/functions.inc.php';
}
echo "<h2>Ändern der Schülerdaten</h2>";
$show_formular_schueler = false;
$show_formular_lehrer = false;
if (isset ( $_GET ['flehr'] )) {
	$show_formular_lehrer = true;
	$show_formular_schueler = false;
} else if (isset ( $_GET ['fschuel'] )) {
	$show_formular_lehrer = false;
	$show_formular_schueler = true;
}
if (isset ( $_SESSION ['userid'] ) && isset ( $_SESSION ['username'] ) && isset ( $_SESSION ['account'] ) && (strcmp ( $_SESSION ['account'], 'normal' ) == 0 || strcmp ( $_SESSION ['account'], 'root' ) == 0) && if_logged_in ( $_SESSION ['userid'] )) {
	if (isset ( $_GET ['change'] ) && ($_GET ['change'] == 1 || $_GET ['change'] == 2)) {
		$pdo_insert = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
		$vname = $_POST ['vname'];
		$nname = $_POST ['nname'];
		$fach1 = $_POST ['fach1'];
		$fach1_lehrer = $_POST ['fach1_lehrer'];
		$fach2 = $_POST ['fach2'];
		$fach2_lehrer = $_POST ['fach2_lehrer'];
		$fach3 = $_POST ['fach3'];
		$fach3_lehrer = $_POST ['fach3_lehrer'];
		$email = $_POST ['email'];
		$klasse_kurs = $_POST ['klasse_kurs'];
		$klassenstufe = $_POST ['klassenstufe'];
		$klassenlehrer_name = $_POST ['klassenlehrer'];
		$telefon = $_POST ['telefon'];
		$mo_anfang = $_POST ['mo_anfang'];
		$mo_ende = $_POST ['mo_ende'];
		$di_anfang = $_POST ['di_anfang'];
		$di_ende = $_POST ['di_ende'];
		$mi_anfang = $_POST ['mi_anfang'];
		$mi_ende = $_POST ['mi_ende'];
		$do_anfang = $_POST ['do_anfang'];
		$do_ende = $_POST ['do_ende'];
		$fr_anfang = $_POST ['fr_anfang'];
		$fr_ende = $_POST ['fr_ende'];
		echo "<br>" . $vname . "  " . $nname . "  " . $fach1 . "  " . $email . $klasse_kurs . $klassenstufe . $klassenlehrer_name . "fachlehrer:" . $fach1_lehrer;
		
		if (strlen ( $vname ) == 0 || strlen ( $nname ) == 0 || (strlen ( $email ) == 0 && $telefon = 0) || ! isset ( $fach1 ) || strlen ( $fach1_lehrer ) == 0 || strlen ( $klassenlehrer_name ) == 0 || strlen ( $klasse_kurs ) == 0 || ! isset ( $klassenstufe )) {
			echo "Ein Problem ist aufgetreten. Bitte gib die Daten erneut ein";
		} else {
			if ($_GET ['change'] == 1) {
				if (strcmp ( $fach2, '' ) == 0) {
					$fach2 = NULL;
					$fach2_lehrer = NULL;
				}
				if (strcmp ( $fach3, '' ) == 0) {
					$fach3 = NULL;
					$fach3_lehrer = NULL;
				}
				// $return_prep = $pdo_insert->prepare ( "UPDATE schueler (vname, nname, email, klassenstufe, klasse, klassenlehrer_name, fach1, fach1_lehrer, mo_anfang, mo_ende, di_anfang, di_ende, mi_anfang, mi_ende, do_anfang, do_ende, fr_anfang, fr_ende) VALUES (:vname, :nname, :email, :klassenstufe, :klasse, :klassenlehrer_name, :fach1, :fach1_lehrer, :mo_anfang, :mo_ende, :di_anfang, :di_ende, :mi_anfang, :mi_ende, :do_anfang, :do_ende, :fr_anfang, :fr_ende)" );
				$return_prep = $pdo_insert->prepare ( "UPDATE ".$schueler_table." SET vname = :vname, nname = :nname, email = :email, klassenstufe = :klassenstufe, klasse = :klasse, klassenlehrer_name = :klassenlehrer_name, fach1 = :fach1, fach1_lehrer = :fach1_lehrer, fach2 = :fach2, fach2_lehrer = :fach2_lehrer, fach3 = :fach3, fach3_lehrer = :fach3_lehrer, mo_anfang = :mo_anfang, mo_ende = :mo_ende, di_anfang = :di_anfang, di_ende = :di_ende, mi_anfang = :mi_anfang, mi_ende = :mi_ende, do_anfang = :do_anfang, do_ende = :do_ende, fr_anfang = :fr_anfang, fr_ende = :fr_ende WHERE id = :id" );
				$return = $return_prep->execute ( array (
						'vname' => $vname,
						'nname' => $nname,
						'email' => $email,
						'klassenstufe' => $klassenstufe,
						'klasse' => $klasse_kurs,
						'klassenlehrer_name' => $klassenlehrer_name,
						'fach1' => $fach1,
						'fach1_lehrer' => $fach1_lehrer,
						'fach2' => $fach2,
						'fach2_lehrer' => $fach2_lehrer,
						'fach3' => $fach3,
						'fach3_lehrer' => $fach3_lehrer,
						'mo_anfang' => $mo_anfang,
						'mo_ende' => $mo_ende,
						'di_anfang' => $di_anfang,
						'di_ende' => $di_ende,
						'mi_anfang' => $mi_anfang,
						'mi_ende' => $mi_ende,
						'do_anfang' => $do_anfang,
						'do_ende' => $do_ende,
						'fr_anfang' => $fr_anfang,
						'fr_ende' => $fr_ende,
						'id' => $_SESSION ['schuelerid'] 
				) );
				if ($return) {
					echo "<br>Die Daten des Schüler wurden erfolgreich geändert.";
					$show_formular_schueler = false;
					$show_formular_lehrer = false;
					unset ( $_SESSION ['schuelerid'] );
				}
			} elseif ($_GET ['change'] == 2) {
				if (strcmp ( $fach2, '' ) == 0) {
					$fach2 = NULL;
					$fach2_lehrer = NULL;
				}
				if (strcmp ( $fach3, '' ) == 0) {
					$fach3 = NULL;
					$fach3_lehrer = NULL;
				}
				$return_prep = $pdo_insert->prepare ( "UPDATE ".$lehrer_table." SET vname = :vname, nname = :nname, email = :email, klassenstufe = :klassenstufe, klasse = :klasse, klassenlehrer_name = :klassenlehrer_name, fach1 = :fach1, fach1_lehrer = :fach1_lehrer, fach2 = :fach2, fach2_lehrer = :fach2_lehrer, fach3 = :fach3, fach3_lehrer = :fach3_lehrer, mo_anfang = :mo_anfang, mo_ende = :mo_ende, di_anfang = :di_anfang, di_ende = :di_ende, mi_anfang = :mi_anfang, mi_ende = :mi_ende, do_anfang = :do_anfang, do_ende = :do_ende, fr_anfang = :fr_anfang, fr_ende = :fr_ende WHERE id = :id" );
				$return = $return_prep->execute ( array (
						'vname' => $vname,
						'nname' => $nname,
						'email' => $email,
						'klassenstufe' => $klassenstufe,
						'klasse' => $klasse_kurs,
						'klassenlehrer_name' => $klassenlehrer_name,
						'fach1' => $fach1,
						'fach1_lehrer' => $fach1_lehrer,
						'fach2' => $fach2,
						'fach2_lehrer' => $fach2_lehrer,
						'fach3' => $fach3,
						'fach3_lehrer' => $fach3_lehrer,
						'mo_anfang' => $mo_anfang,
						'mo_ende' => $mo_ende,
						'di_anfang' => $di_anfang,
						'di_ende' => $di_ende,
						'mi_anfang' => $mi_anfang,
						'mi_ende' => $mi_ende,
						'do_anfang' => $do_anfang,
						'do_ende' => $do_ende,
						'fr_anfang' => $fr_anfang,
						'fr_ende' => $fr_ende,
						'id' => $_SESSION ['lehrerid'] 
				) );
				if ($return) {
					echo "Die Daten des Lehrers wurden erfolgreich geändert.";
					$show_formular_schueler = false;
					$show_formular_lehrer = false;
					unset ( $_SESSION ['lehrerid'] );
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
	$pdo_insert = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
	$return_prep = $pdo_insert->prepare ( "SELECT * FROM ".$schueler_table." WHERE id = :id" );
	$return = $return_prep->execute ( array (
			'id' => $_GET ['fschuel'] 
	) );
	if ($return == false) {
		echo "EIn PRoblem ist aufgetreten!";
	}
	$schueler = $return_prep->fetch ();
	if ($schueler == false) {
		echo "EIN PROBLEM";
	} else {
		$_SESSION ['schuelerid'] = $_GET ['fschuel'];
		?>
<div class="formular_class">
	<form action="?change=1" method="POST">
		<fieldset style="padding: 40px;">
			<legend>
				<b>Nachhilfeschüler</b>
			</legend>
			<br>
			Vorname:
			<span style="float: right; width: 50%;">Nachname:</span>
			<br>
			<input type="text" maxlength="49" name="vname" autofocus required="required" value="<?php echo $schueler['vname'];?>" style="width: 40%;">
			<input type="text" maxlength="49" name="nname" required="required" value="<?php echo $schueler['nname'];?>" style="width: 49%; float: right; margin-right: 5px; margin-left: 0;">
			<br>
			<br>
			Klassenstufe:
			<span style="float: right; width: 50%;">Klasse/Kurs:</span>
			<br>
			<input type="number" name="klassenstufe" min="5" max="12" required="required" value="<?php echo $schueler['klassenstufe'];?>" style="width: 40%;">
			<input type="text" pattern="([ABCDabcdl123456]|[lL][12])" name="klasse_kurs" required="required" value="<?php echo $schueler['klasse'];?>"
				style="width: 49%; float: right; margin-right: 5px; margin-left: 0;">
			<br>
			<br>
			Klassenlehrer:
			<br>
			<input type="text" class="textinput" maxlength="49" name="klassenlehrer" required="required" value="<?php echo $schueler['klassenlehrer_name'];?>">
			<br>
			<br>
			Geburtstag
			<br>
			<input type="date" value="<?php echo $schueler['geburtstag'];?>">
			<br>
			<br>
			Email:
			<br>
			<input type="email" class="textinput" maxlength="49" name="email" value="<?php echo $schueler['email'];?>">
			<br>
			<br>
			<br>
			Telefon
			<br>
			<input type="tel" name="telefon" value="<?php echo $schueler['telefon'];?>">
			<br>
			<div style="width: 20%; display: inline-block;">
				<h3>1.Fach:</h3>
				<br>
				<select name="fach1">
				<?php 
				for ($i = 0; $i < count($faecher); $i++) {
					if(strcmp($schueler['fach1'], $faecher[$i]) == 0) {
						echo "<option value=\"".$faecher[$i]."\" selected >".$faecher_lesbar[$i]."</option>";
					}else{
						echo "<option value=\"".$faecher[$i]."\">".$faecher_lesbar[$i]."</option>";
					}
				}
				?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach1_lehrer" value="<?php echo $schueler['fach1_lehrer'];?>">
				<br>
			</div>
			<div style="width: 20%; display: inline-block; margin-left: 10%;">
				<h3>2.Fach:</h3>
				<br>
				<select name="fach2">
				<?php 
				for ($i = 0; $i < count($faecher); $i++) {
					if(strcmp($schueler['fach2'], $faecher[$i]) == 0) {
						echo "<option value=\"".$faecher[$i]."\" selected >".$faecher_lesbar[$i]."</option>";
					}else{
						echo "<option value=\"".$faecher[$i]."\">".$faecher_lesbar[$i]."</option>";
					}
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
				<br>
				<select name="fach3">
				<?php 
				for ($i = 0; $i < count($faecher); $i++) {
					if(strcmp($schueler['fach3'], $faecher[$i]) == 0) {
						echo "<option value=\"".$faecher[$i]."\" selected >".$faecher_lesbar[$i]."</option>";
					}else{
						echo "<option value=\"".$faecher[$i]."\">".$faecher_lesbar[$i]."</option>";
					}
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
					<input type="time" name="mo_anfang" class="input_time" value="<?php echo date("H:i", strtotime($schueler['mo_anfang']));?>">
					<br>
					Montag Ende:
					<br>
					<input type="time" name="mo_ende" class="input_time" value="<?php echo date("H:i", strtotime($schueler['mo_ende']));?>">
					<br>
					Dienstag Anfang:
					<br>
					<input type="time" name="di_anfang" class="input_time" value="<?php echo date("H:i", strtotime($schueler['di_anfang']));?>">
					<br>
					Dienstag Ende:
					<br>
					<input type="time" name="di_ende" class="input_time" value="<?php echo date("H:i", strtotime($schueler['di_ende']));?>">
					<br>
				</div>
				<div id="zeitdiv" style="display: inline-block; width: 25%;">
					Mttwoch Anfang:
					<br>
					<input type="time" name="mi_anfang" class="input_time" value="<?php echo date("H:i", strtotime($schueler['mi_anfang']));?>">
					<br>
					Mitwoch Ende:
					<br>
					<input type="time" name="mi_ende" class="input_time" value="<?php echo date("H:i", strtotime($schueler['mi_ende']));?>">
					<br>
					Donnerstag Anfang
					<br>
					<input type="time" name="do_anfang" class="input_time" value="<?php echo date("H:i", strtotime($schueler['do_anfang']));?>">
					<br>
					Donnerstag Ende:
					<br>
					<input type="time" name="do_ende" class="input_time" value="<?php echo date("H:i", strtotime($schueler['do_ende']));?>">
					<br>
				</div>
				<div style="display: inline-block; width: 25%; height: zeitdiv.height;">
					Freitag Anfang:
					<br>
					<input type="time" name="fr_anfang" class="input_time" value="<?php echo date("H:i", strtotime($schueler['fr_anfang']));?>">
					<br>
					Freitag Ende:
					<br>
					<input type="time" name="fr_ende" class="input_time" value="<?php echo date("H:i", strtotime($schueler['fr_ende']));?>">
				</div>
			</div>
			<br>
			<br>
			<br>
			<br>
			<input type="reset" value="Reset">
			<input type="submit" value="Ändern" style="float: right;">
		</fieldset>
	</form>
</div>
<?php
	}
}
if ($show_formular_lehrer) {
	$pdo_insert = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
	$return_prep = $pdo_insert->prepare ( "SELECT * FROM ".$lehrer_table." WHERE id = :id" );
	$return = $return_prep->execute ( array (
			'id' => $_GET ['flehr'] 
	) );
	if ($return == false) {
		echo "EIn PRoblem ist aufgetreten!";
	}
	$lehrer = $return_prep->fetch ();
	if ($lehrer == false) {
		echo "EIN PROBLEM";
	} else {
		$_SESSION ['lehrerid'] = $_GET ['flehr'];
		?>
<div class="formular_class">
	<form action="?change=2" method="POST">
		<fieldset style="padding: 40px;">
			<legend>
				<b>Nachhilfeschüler</b>
			</legend>
			<br>
			Vorname:
			<span style="float: right; width: 50%;">Nachname:</span>
			<br>
			<input type="text" maxlength="49" name="vname" autofocus required="required" value="<?php echo $lehrer['vname'];?>" style="width: 40%;">
			<input type="text" maxlength="49" name="nname" required="required" value="<?php echo $lehrer['nname'];?>" style="width: 49%; float: right; margin-right: 5px; margin-left: 0;">
			<br>
			<br>
			Klassenstufe:
			<span style="float: right; width: 50%;">Klasse/Kurs:</span>
			<br>
			<input type="number" name="klassenstufe" min="5" max="12" required="required" value="<?php echo $lehrer['klassenstufe'];?>" style="width: 40%;">
			<input type="text" pattern="([ABCDabcdl123456]|[lL][12])" name="klasse_kurs" required="required" value="<?php echo $lehrer['klasse'];?>"
				style="width: 49%; float: right; margin-right: 5px; margin-left: 0;">
			<br>
			<br>
			Klassenlehrer:
			<br>
			<input type="text" class="textinput" maxlength="49" name="klassenlehrer" required="required" value="<?php echo $lehrer['klassenlehrer_name'];?>">
			<br>
			<br>
			Geburtstag
			<br>
			<input type="date" value="<?php echo $lehrer['geburtstag'];?>">
			<br>
			<br>
			Email:
			<br>
			<input type="email" class="textinput" maxlength="49" name="email" value="<?php echo $lehrer['email'];?>">
			<br>
			<br>
			<br>
			Telefon
			<br>
			<input type="tel" name="telefon" value="<?php echo $lehrer['telefon'];?>">
			<br>
			<div style="width: 20%; display: inline-block;">
				<h3>1.Fach:</h3>
				<br>
				<select name="fach1">
				<?php 
				for ($i = 0; $i < count($faecher); $i++) {
					if(strcmp($lehrer['fach1'], $faecher[$i]) == 0) {
						echo "<option value=\"".$faecher[$i]."\" selected >".$faecher_lesbar[$i]."</option>";
					}else{
						echo "<option value=\"".$faecher[$i]."\">".$faecher_lesbar[$i]."</option>";
					}
				}
				?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach1_lehrer" value="<?php echo $lehrer['fach1_lehrer'];?>">
				<br>
			</div>
			<div style="width: 20%; display: inline-block; margin-left: 10%;">
				<h3>2.Fach:</h3>
				<br>
				<select name="fach2">
				<?php 
				for ($i = 0; $i < count($faecher); $i++) {
					if(strcmp($lehrer['fach2'], $faecher[$i]) == 0) {
						echo "<option value=\"".$faecher[$i]."\" selected >".$faecher_lesbar[$i]."</option>";
					}else{
						echo "<option value=\"".$faecher[$i]."\">".$faecher_lesbar[$i]."</option>";
					}
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
				<br>
				<select name="fach3">
				<?php 
				for ($i = 0; $i < count($faecher); $i++) {
					if(strcmp($lehrer['fach3'], $faecher[$i]) == 0) {
						echo "<option value=\"".$faecher[$i]."\" selected >".$faecher_lesbar[$i]."</option>";
					}else{
						echo "<option value=\"".$faecher[$i]."\">".$faecher_lesbar[$i]."</option>";
					}
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
					<input type="time" name="mo_anfang" class="input_time" value="<?php echo date("H:i", strtotime($lehrer['mo_anfang']));?>">
					<br>
					Montag Ende:
					<br>
					<input type="time" name="mo_ende" class="input_time" value="<?php echo date("H:i", strtotime($lehrer['mo_ende']));?>">
					<br>
					Dienstag Anfang:
					<br>
					<input type="time" name="di_anfang" class="input_time" value="<?php echo date("H:i", strtotime($lehrer['di_anfang']));?>">
					<br>
					Dienstag Ende:
					<br>
					<input type="time" name="di_ende" class="input_time" value="<?php echo date("H:i", strtotime($lehrer['di_ende']));?>">
					<br>
				</div>
				<div id="zeitdiv" style="display: inline-block; width: 25%;">
					Mttwoch Anfang:
					<br>
					<input type="time" name="mi_anfang" class="input_time" value="<?php echo date("H:i", strtotime($lehrer['mi_anfang']));?>">
					<br>
					Mitwoch Ende:
					<br>
					<input type="time" name="mi_ende" class="input_time" value="<?php echo date("H:i", strtotime($lehrer['mi_ende']));?>">
					<br>
					Donnerstag Anfang
					<br>
					<input type="time" name="do_anfang" class="input_time" value="<?php echo date("H:i", strtotime($lehrer['do_anfang']));?>">
					<br>
					Donnerstag Ende:
					<br>
					<input type="time" name="do_ende" class="input_time" value="<?php echo date("H:i", strtotime($lehrer['do_ende']));?>">
					<br>
				</div>
				<div style="display: inline-block; width: 25%; height: zeitdiv.height;">
					Freitag Anfang:
					<br>
					<input type="time" name="fr_anfang" class="input_time" value="<?php echo date("H:i", strtotime($lehrer['fr_anfang']));?>">
					<br>
					Freitag Ende:
					<br>
					<input type="time" name="fr_ende" class="input_time" value="<?php echo date("H:i", strtotime($lehrer['fr_ende']));?>">
					<br>
				</div>
			</div>
			<br>
			<br>
			<br>
			<br>
			<input type="reset" value="Reset">
			<input type="submit" value="Ändern" style="float: right;">
		</fieldset>
	</form>
</div>
<?php
	}
}
?>
</div>
<footer>Designed by Yannik Weber</footer>
</body>
</html>
