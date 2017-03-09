<?php
require 'header.php';
require 'includes/global_vars.inc.php';
if(! function_exists( 'get_users_logged_in' ) ) {
	include 'includes/functions.inc.php';
}
echo "<h2>Eingabe</h2>";
$show_formular_paar = false;
if (isset ( $_GET ['paar'] ) && $_GET ['paar'] == 1) {
	$show_formular_paar = true;
}
if (isset ( $_GET ['input'] ) && ($_GET ['input'] == 1 || $_GET ['input'] == 2) && isset ( $_SESSION ['userid'] ) && isset ( $_SESSION ['username'] ) && if_logged_in($_SESSION['userid'])) {
	$pdo_insert = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
	/*
	 * $vname = $_POST ['vname'];
	 * $nname = $_POST ['nname'];
	 * $fach1 = $_POST ['fach1'];
	 * $fach1_lehrer = $_POST['fach1_lehrer'];
	 * $email = $_POST ['email'];
	 * $klasse_kurs = $_POST['klasse_kurs'];
	 * $klassenstufe = $_POST['klassenstufe'];
	 * $klassenlehrer_name = $_POST['klassenlehrer'];
	 * $telefon = $_POST['telefon'];
	 * $mo_anfang = $_POST['mo_anfang'];
	 * $mo_ende = $_POST['mo_ende'];
	 * $di_anfang = $_POST['di_anfang'];
	 * $di_ende = $_POST['di_ende'];
	 * $mi_anfang = $_POST['mi_anfang'];
	 * $mi_ende = $_POST['mi_ende'];
	 * $do_anfang = $_POST['do_anfang'];
	 * $do_ende = $_POST['do_ende'];
	 * $fr_anfang = $_POST['fr_anfang'];
	 * $fr_ende = $_POST['fr_ende'];
	 */
	$paar_schueler_string = $_POST ['paar_schueler'];
	$paar_schueler_id = strtok ( $paar_schueler_string, "_" );
	$paar_schueler_fach = strtok ( "_" );
	echo "<br>" . $vname . "  " . $nname . "  " . $fach1 . "  " . $email . $klasse_kurs . $klassenstufe . $klassenlehrer_name . "fachlehrer:" . $fach1_lehrer . "fach:" . $paar_schueler_fach . "---" . $paar_schueler_id;
	
	if (! isset ( $_POST ['paar_schueler'] )) {
		// if (strlen ( $vname ) == 0 || strlen ( $nname ) == 0 || ( strlen($email) == 0 && $telefon = 0 ) || ! isset ( $fach1 ) || strlen($fach1_lehrer) == 0 || strlen($klassenlehrer_name) == 0 || strlen($klasse_kurs) == 0 || !isset($klassenstufe)) {
		echo "Ein Problem ist aufgetreten. Bitte gib die Daten erneut ein";
	} else {
		if ($_GET ['input'] == 1) {
			$return_prep = $pdo_insert->prepare ( "SELECT * FROM schueler WHERE id = :id" );
			$return = $return_prep->execute ( array (
					'id' => $paar_schueler_id 
			) );
			if ($return == false)
				echo "EIn PRoblem ist aufgetreten!";
			$gewaehlter_schueler = $return_prep->fetch ();
			if ($gewaehlter_schueler == true) {
				$show_formular_paar = true;
				$gewaehltes_fach = $gewaehlter_schueler ['fach' . $paar_schueler_fach];
				echo $gewaehltes_fach;
				echo "Dieser Schüler existiert bereits<br>";
				echo return_schueler_40($gewaehlter_schueler);
				
				$return_prep = $pdo_insert->prepare ( "SELECT * FROM lehrer WHERE klassenstufe > :klasse AND (fach1 = :fach1 OR fach2 = :fach1 OR fach3 = :fach1)" );
				$return = $return_prep->execute ( array (
						'klasse' => $gewaehlter_schueler ['klassenstufe'],
						'fach1' => $gewaehltes_fach 
				) );
				if ($return === false) {
					echo "Ein Fehler ist aufgetreten";
				}
				$test_lehrer = $return_prep->fetch ();
				if ($test_lehrer == false) {
					echo "<br>Kein geeigneter Lehrer gefunden<br>";
				} else {
					echo "<fieldset style=\"padding: 40px; width: 40%; display: inline-block;line-height: 150%;\">";
					echo "<legend><b>Lehrer: " . $test_lehrer ['vname'] . " " . $test_lehrer ['nname'] . "</b></legend>";
					echo "Name:    " . $test_lehrer ['vname'] . " " . $test_lehrer ['nname'] . "<br>Email:   " . $test_lehrer ['email'] . "<br>Klassenlehrer/Tutor: " . $test_lehrer ['klassenlehrer_name'];
					echo "<br>1.Fach:   " . $test_lehrer ['fach1'] . " bei " . $test_lehrer ['fach1_lehrer'];
					if (strlen ( $test_lehrer ['fach2'] ) != 0) {
						echo "<br>2.Fach:   " . $test_lehrer ['fach2'] . " bei " . $test_lehrer ['fach2_lehrer'];
					}
					if (strlen ( $test_lehrer ['fach3'] ) != 0) {
						echo "<br>3.Fach:   " . $test_lehrer ['fach3'] . " bei " . $test_lehrer ['fach3_lehrer'];
					}
					echo "<br>Montag von " . $test_lehrer ['mo_anfang'] . " bis " . $test_lehrer ['mo_ende'];
					echo "<br>Dienstag von " . $test_lehrer ['di_anfang'] . " bis " . $test_lehrer ['di_ende'];
					echo "<br>Mittwoch von " . $test_lehrer ['mi_anfang'] . " bis " . $test_lehrer ['mi_ende'];
					echo "<br>Donnerstag von " . $test_lehrer ['do_anfang'] . " bis " . $test_lehrer ['do_ende'];
					echo "<br>Freitag von " . $test_lehrer ['fr_anfang'] . " bis " . $test_lehrer ['fr_ende'];
					echo "</fieldset>";
					$times = array (
							'mo_anfang',
							'mo_ende',
							'di_anfang',
							'di_ende',
							'mi_anfang',
							'mi_ende',
							'do_anfang',
							'do_ende',
							'fr_anfang',
							'fr_ende' 
					);
					$arr_size = count ( $times );
					while ( $test_lehrer != false ) {
						for($i = 0; $i < $arr_size; $i ++) {
							$time_mo_an = strtotime ( $test_lehrer [$times [$i]] );
							$sch_time_mo_an = strtotime ( $gewaehlter_schueler [$times [$i]] );
							echo $test_lehrer [$times [$i]] . "....:" . $time_mo_an . "    " . $gewaehlter_schueler [$times [$i]] . "...:" . $sch_time_mo_an . "<br><br>";
							if (strtotime ( $test_lehrer [$times [$i]] . "+30 minutes" )) {
								$test_time = strtotime ( $test_lehrer [$times [$i]] . "+30 minutes" );
								echo "test" . $test_time . "    " . date ( "H:i:s", $test_time ) . "<br><br><br>";
							}
						}
						echo "<br>" . $test_lehrer ['vname'] . " " . $test_lehrer ['nname'] . " " . $test_lehrer ['email'] . " " . $test_lehrer ['klassenstufe'] . " " . $test_lehrer ['mo_anfang'] . "<br>";
						$test_lehrer = $return_prep->fetch ();
					}
				}
			} else {
				echo "Ein Problem ist aufgetreten";
			}
		}
	}
} else {
	if (! isset ( $_SESSION ['userid'] ) || ! isset ( $_SESSION ['username'] )) {
		$show_formular_paar = false;
	}
}
if ($show_formular_paar) {
	echo "<b>Dieses Formular existiert noch nicht</b><br>";
	?>
<div class="formular_class">
	<form action="?input=1" method="POST">
		<fieldset style="padding: 40px;">
			<legend>
				<b>Nachhilfepaar</b>
			</legend>
			<select name="paar_schueler">
	<?php
	$pdo_insert = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
	$return_prep = $pdo_insert->query ( "SELECT * FROM schueler" );
	$return = $return_prep->fetch ();
	while ( $return != false ) {
		// echo $return ['id'] . " " . $return ['vname'] . " " . $return ['nname'] . " " . $return ['email'] . " " . $return ['klassenstufe'] . " " . $return ['klasse_kurs'] . " " . $return ['fach1'] . " " . $return ['fach1_lehrer'] . "<br>";
		echo "<option value=\"" . $return ['id'] . "_1\">" . $return ['vname'] . " " . $return ['nname'] . " - " . get_faecher_lesbar($return ['fach1']) . "</option>";
		if (strlen ( $return ['fach2'] ) > 0) {
			echo "<option value=\"" . $return ['id'] . "_2\">" . $return ['vname'] . " " . $return ['nname'] . " - " . get_faecher_lesbar($return ['fach2']) . "</option>";
		}
		if (strlen ( $return ['fach3'] ) > 0) {
			echo "<option value=\"" . $return ['id'] . "_3\">" . $return ['vname'] . " " . $return ['nname'] . " - " . get_faecher_lesbar($return ['fach3']) . "</option>";
		}
		$return = $return_prep->fetch ();
	}
	?>
					</select> <input type="submit" value="Suchen nach Lehrer">
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
