<?php
function get_faecher_lesbar($fach) {
	if (! isset ( $login_user )) {
		include "includes/global_vars.inc.php";
	}
	for( $i = 0; $i < count($faecher); $i++) {
		if(strcmp($fach, $faecher[$i]) == 0) {
			return $faecher_lesbar[$i];
		}
	}
	return "Kein Fach gefunden.";
}

function return_schueler_40($schueler) {
	$output = "<fieldset style=\"padding: 40px; width: 40%; display: inline-block;line-height: 150%;\">
  <legend><b>Schüler: " . $schueler ['vname'] . " " . $schueler ['nname'] . "</b></legend>
  Name: " . $schueler ['vname'] . " " . $schueler ['nname'] . "<br>Email: " . $schueler ['email'] . "<br>Klassenlehrer/Tutor: " . $schueler ['klassenlehrer_name'] . "<br>1.Fach: " . $schueler ['fach1'] . " bei " . $schueler ['fach1_lehrer'];
	if (strlen ( $schueler ['fach2'] ) != 0) {
		$output = $output . "<br>2.Fach: " . $schueler ['fach2'] . " bei " . $schueler ['fach2_lehrer'];
	}
	if (strlen ( $schueler ['fach3'] ) != 0) {
		$output = $output . "<br>3.Fach: " . $schueler ['fach3'] . " bei " . $schueler ['fach3_lehrer'];
	}
	$output = $output . "<br><br>
	<table class=\"time_output\">
	<tr>
	<th></th><th>Von:</th><th>Bis:</th>
	</tr>
	<tr>
	<td>Montag:</td>
	<td>".date("H:i", strtotime($schueler['mo_anfang']))."</td>
							<td>".date("H:i", strtotime($schueler['mo_ende']))."</td>
						</tr>
						<tr>
							<td>Dienstag:</td>
							<td>".date("H:i", strtotime($schueler['di_anfang']))."</td>
							<td>".date("H:i", strtotime($schueler['di_ende']))."</td>
						</tr>
						<tr>
							<td>Mittwoch:</td>
							<td>".date("H:i", strtotime($schueler['mi_anfang']))."</td>
							<td>".date("H:i", strtotime($schueler['mi_ende']))."</td>
						</tr>
						<tr>
							<td>Donnerstag:</td>
							<td>".date("H:i", strtotime($schueler['do_anfang']))."</td>
							<td>".date("H:i", strtotime($schueler['do_ende']))."</td>
						</tr>
						<tr>
							<td>Freitag:</td>
							<td>".date("H:i", strtotime($schueler['fr_anfang']))."</td>
							<td>".date("H:i", strtotime($schueler['fr_ende']))."</td>
						</tr>
						</table>
						</fieldset>";
//	$output = $output . "<br>Montag von " . $schueler ['mo_anfang'] . " bis " . $schueler ['mo_ende'] . "<br>Dienstag von " . $schueler ['di_anfang'] . " bis " . $schueler ['di_ende'] . "<br>Mittwoch von " . $schueler ['mi_anfang'] . " bis " . $schueler ['mi_ende'] . "<br>Donnerstag von " . $schueler ['do_anfang'] . " bis " . $schueler ['do_ende'] . "<br>Freitag von " . $schueler ['fr_anfang'] . " bis " . $schueler ['fr_ende'] . "</fieldset>";
	return $output;
}
function log_in($userid) {
	if (! isset ( $login_user )) {
		include "includes/global_vars.inc.php";
	}
	$pdo_login = new PDO ( 'mysql:host=localhost;dbname=schuefi_login', $login_user, $login_user_passwd );
	$ret_prep = $pdo_login->prepare ( "UPDATE users SET logged_in = true WHERE id = :id" );
	$return = $ret_prep->execute ( array (
			'id' => $userid 
	) );
	$pdo_login = null;
}
function log_out($userid) {
	if (! isset ( $login_user )) {
		include "includes/global_vars.inc.php";
	}
	// do something
	$pdo_login = new PDO ( 'mysql:host=localhost;dbname=schuefi_login', $login_user, $login_user_passwd );
	$ret_prep = $pdo_login->prepare ( "UPDATE users SET logged_in = false WHERE id = :id" );
	$return = $ret_prep->execute ( array (
			'id' => $userid 
	) );
	$pdo_login = null;
}
function get_users_logged_in() {
	if (! isset ( $login_user )) {
		include "includes/global_vars.inc.php";
	}
	// echo "hallo";
	$pdo_login = new PDO ( 'mysql:host=localhost;dbname=schuefi_login', $login_user, $login_user_passwd );
	$ret_prep = $pdo_login->query ( "SELECT * FROM `users`" );
	$return = $ret_prep->fetch ();
	// echo "while";
	$i = 0;
	$logged_user = array ();
	while ( $return != false ) {
		// echo "test";
		if ($return ['logged_in']) {
			$i ++;
			$logged_user [] = array (
					"vname" => $return ['vname'],
					"nname" => $return ['nname'],
					"email" => $return ['email'] 
			);
		}
		$return = $ret_prep->fetch ();
	}
	return array (
			$i,
			$logged_user 
	);
}

// nicht sinnvoll
function if_logged_in($userid) {
	if (! isset ( $login_user )) {
		include "includes/global_vars.inc.php";
	}
	$pdo_login = new PDO ( 'mysql:host=localhost;dbname=schuefi_login', $login_user, $login_user_passwd );
	$ret_prep = $pdo_login->prepare ( "SELECT * FROM users WHERE id = :id" );
	$return = $ret_prep->execute ( array (
			'id' => $userid 
	) );
	$return = $ret_prep->fetch ();
	if ($return ['logged_in'] == true) {
		// echo "logged_in";
		return true;
	} else {
		// echo "not logged in";
		return false;
	}
	$pdo_login = null;
}


// gibt bei korrektem Wert einen Array zurück mit bearbeiteten und sicherem Input, bei Fehler einen String mit errorbeschreibung
function validate_input($sl, $is_output = FALSE) {
	if (! isset ( $login_user )) {
		include "includes/global_vars.inc.php";
	}
	$sl['vname'] = strip_tags($sl['vname']);
	$sl['nname'] = strip_tags($sl['nname']);
	$sl['email'] = strip_tags($sl['email']);
	$sl['fach1'] = strip_tags($sl['fach1']);
	$sl['fach2'] = strip_tags($sl['fach2']);
	$sl['fach3'] = strip_tags($sl['fach3']);
	$sl['fach1_lehrer'] = strip_tags($sl['fach1_lehrer']);
	$sl['fach2_lehrer'] = strip_tags($sl['fach2_lehrer']);
	$sl['fach3_lehrer'] = strip_tags($sl['fach3_lehrer']);
	$sl['klassenlehrer_name'] = strip_tags($sl['klassenlehrer_name']);
	$sl['klasse_kurs'] = strip_tags($sl['klasse_kurs']);
	$sl['comment'] = strip_tags($sl['comment']);
	if(!is_int($sl['telefon']) && $sl['telefon'] != NULL) {
		echo "FEHLER";
	}
	if (strcmp ( $fach2, '' ) == 0) {
		$fach2 = NULL;
		$fach2_lehrer = NULL;
	}
	if (strcmp ( $fach3, '' ) == 0) {
		$fach3 = NULL;
		$fach3_lehrer = NULL;
	}	
	$error;
	if(!isset($sl['vname']) || strlen($sl['vname']) < 3 || strlen($sl['vname']) > 49) {
//		echo "<br><br>Bitte gib einen Vornamen an, der zwischen 3 und 49 Zeichen lang ist.";
		$error = $error."<br><br>Bitte gib einen Vornamen an, der zwischen 3 und 49 Zeichen lang ist.";
	}
	if(!isset($sl['nname']) || strlen($sl['nname']) < 3 || strlen($sl['nname']) > 49) {
//		echo "<br><br>Bitte gib einen Nachnamen an, der zwischen 3 und 49 Zeichen lang ist.";
		$error = $error."<br><br>Bitte gib einen Nachnamen an, der zwischen 3 und 49 Zeichen lang ist.";
	}
	if(!isset($sl['klassenlehrer_name']) || strlen($sl['klassenlehrer_name']) < 3 || strlen($sl['klassenlehrer_name']) > 49 || !preg_match("/^(Herr|Frau|herr|frau|Hr.|Fr.|hr.|fr.) [A-Za-z]*/", $sl['klassenlehrer_name'])) {
//		echo "<br><br>Bitte gib einen korrenkten Namen des Klassenlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
		$error = $error."<br><br>Bitte gib einen korrekten Namen des Klassenlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
	}
	//preg_match funktioniert nicht
	if(!isset($sl['klasse']) || strlen($sl['klasse']) < 1 || strlen($sl['klasse']) > 2 ||  array_search($sl['klasse'], $klassen) === false) {
//		echo "<br><br>Bitte gib eine korrekte Klasse/Kurs an.";
		$error = $error."<br><br>Bitte gib eine korrekte Klasse/Kurs an.";
	}
	if(!isset($sl['klassenstufe']) || $sl['klassenstufe'] < 5  || $sl['klassenstufe'] > 12 || strlen($sl['klassenstufe']) == 0) {
//		echo "<br><br>Bitte gib eine korrekte Klassenstufe an.";
		$error = $error."<br><br>Bitte gib eine korrekte Klassenstufe an.";
	}
//	if(!isset($sl['telefon']))
//	echo $sl['email'].filter_var($sl['email'], FILTER_VALIDATE_EMAIL)."telefon".preg_match("/^\+?([0-9\/ -]+)$/", $sl['telefon']);
	if(!isset($sl['email']) || (!filter_var($sl['email'], FILTER_VALIDATE_EMAIL) && !preg_match("/^\+?([0-9\/ -]+)$/", $sl['telefon']) )) {
//		echo "<br><br>Bitte gib eine korrekte E-Mail-Adresse oder Telefonnummer an.";
		$error = $error."<br><br>Bitte gib eine korrekte E-Mail-Adresse oder Telefonnummer an.";
	}
	
	if(!isset($sl['fach1']) || array_search($sl['fach1'], $faecher) === false || strlen($sl['fach1']) == 0) {
//		echo "<br><br>Bitte wähle ein korrektes Fach aus.";
		$error = $error."<br><br>Bitte wähle ein korrektes Fach aus.";
	}
	if(!isset($sl['fach2']) || array_search($sl['fach2'], $faecher) === false) {
//		echo "<br><br>Bitte wähle ein korrektes 2.Fach aus.";
		$error = $error."<br><br>Bitte wähle ein korrektes 2.Fach aus.";
	}
	if(!isset($sl['fach3']) || array_search($sl['fach3'], $faecher) === false) {
//		echo "<br><br>Bitte wähle ein korrektes 3.Fach aus.";
		$error = $error."<br><br>Bitte wähle ein korrektes 3.Fach aus.";
	}

	if(!isset($sl['fach1_lehrer']) || strlen($sl['fach1_lehrer']) < 3 || strlen($sl['fach1_lehrer']) > 49 || !preg_match("/^(Herr|Frau|herr|frau|Hr.|Fr.|hr.|fr.) [A-Za-z]*/", $sl['fach1_lehrer'])) {
//		echo "<br><br>Bitte gib einen korrenkten Namen des Fachlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
		$error = $error."<br><br>Bitte gib einen Namen des Fachlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
	}
	if($sl['fach2_lehrer'] != NULL && (strlen($sl['fach2_lehrer']) < 3 || strlen($sl['fach2_lehrer']) > 49 || !preg_match("/^(Herr|Frau|herr|frau|Hr.|Fr.|hr.|fr.) [A-Za-z]*/", $sl['fach2_lehrer']) )  ) {
//		echo "<br><br>Bitte gib einen korrenkten Namen des 2.Fachlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
		$error = $error."<br><br>Bitte gib einen korekten Namen des 2.Fachlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
	}
	if($sl['fach3_lehrer'] != NULL && (strlen($sl['fach3_lehrer']) < 3 || strlen($sl['fach3_lehrer']) > 49 || !preg_match("/^(Herr|Frau|herr|frau|Hr.|Fr.|hr.|fr.) [A-Za-z]*/", $sl['fach3_lehrer']) )  ) {
//		echo "<br><br>Bitte gib einen korrenkten Namen des 3.Fachlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
		$error = $error."<br><br>Bitte gib einen korrekten Namen des 3.Fachlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
	}
//	echo "<br>moa:".strtotime($sl['mo_anfang'])."<br>".strtotime($sl['mo_ende'])."<br>dia:".strtotime($sl['di_anfang'])."<br>".strtotime($sl['di_ende'])."<br>mia:".strtotime($sl['mi_anfang'])."<br>mi_ende:".strtotime($sl['mi_ende'])."<br>".strtotime($sl['do_anfang'])."<br>".strtotime($sl['do_ende'])."<br>".strtotime($sl['fr_anfang'])."<br>10:".strtotime($sl['fr_ende']);
	if(strtotime($sl['mo_anfang']) == false || strtotime($sl['mo_ende']) == false || strtotime($sl['di_anfang']) == false || strtotime($sl['di_ende']) == false || strtotime($sl['mi_anfang']) == false || strtotime($sl['mi_ende']) == false || strtotime($sl['do_anfang']) == false || strtotime($sl['do_ende']) == false || strtotime($sl['fr_anfang']) == false || strtotime($sl['fr_ende']) == false) {
//		echo "<br><br>Bitte gib korrekte Zeitpunkte an.";
		$error = $error."<br><br>Bitte gib korrekte Zeitpunkte an.";
	}
	if(strlen($sl['comment']) > 250) {
		$error = $error."<br><br>Bitte gib einen kürzeren Kommentar an.";
	}
	if($is_output) {
		$sl['vname'] = htmlspecialchars($sl['vname'],  ENT_QUOTES, 'UTF-8');
		$sl['nname'] = htmlspecialchars($sl['nname'],  ENT_QUOTES, 'UTF-8');
		$sl['email'] = htmlspecialchars($sl['email'],  ENT_QUOTES, 'UTF-8');
		$sl['fach1'] = htmlspecialchars($sl['fach1'],  ENT_QUOTES, 'UTF-8');
		$sl['fach2'] = htmlspecialchars($sl['fach2'],  ENT_QUOTES, 'UTF-8');
		$sl['fach3'] = htmlspecialchars($sl['fach3'],  ENT_QUOTES, 'UTF-8');
		$sl['fach1_lehrer'] = htmlspecialchars($sl['fach1_lehrer'],  ENT_QUOTES, 'UTF-8');
		$sl['fach2_lehrer'] = htmlspecialchars($sl['fach2_lehrer'],  ENT_QUOTES, 'UTF-8');
		$sl['fach3_lehrer'] = htmlspecialchars($sl['fach3_lehrer'],  ENT_QUOTES, 'UTF-8');
		$sl['klassenlehrer_name'] = htmlspecialchars($sl['klassenlehrer_name'],  ENT_QUOTES, 'UTF-8');
		$sl['klasse_kurs'] = htmlspecialchars($sl['klasse_kurs'],  ENT_QUOTES, 'UTF-8');
		$sl['comment'] = htmlspecialchars($sl['comment'],  ENT_QUOTES, 'UTF-8');
	}
//	return "<br><br>Error";
	if(strlen($error) > 0) {
		return $error;
	}else{
		return $sl;
	}
}
?>