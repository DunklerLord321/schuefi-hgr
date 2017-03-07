<?php
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
	$output = $output . "<br>Montag von " . $schueler ['mo_anfang'] . " bis " . $schueler ['mo_ende'] . "<br>Dienstag von " . $schueler ['di_anfang'] . " bis " . $schueler ['di_ende'] . "<br>Mittwoch von " . $schueler ['mi_anfang'] . " bis " . $schueler ['mi_ende'] . "<br>Donnerstag von " . $schueler ['do_anfang'] . " bis " . $schueler ['do_ende'] . "<br>Freitag von " . $schueler ['fr_anfang'] . " bis " . $schueler ['fr_ende'] . "</fieldset>";
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
function validate_input($sl) {
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
	if(!strtotime($sl['mo_anfang']) || !strtotime($sl['mo_anfang']) || !strtotime($sl['di_anfang']) || !strtotime($sl['di_ende']) || !strtotime($sl['mi_anfang']) || !strtotime($sl['mi_ende']) || !strtotime($sl['do_anfang']) || !strtotime($sl['do_ende']) || !strtotime($sl['fr_anfang']) || !strtotime($sl['fr_ende']) ) {
		echo "FEHLER bei der Zeit";
	}
	if (strcmp ( $fach2, '' ) == 0) {
		$fach2 = NULL;
		$fach2_lehrer = NULL;
	}
	if (strcmp ( $fach3, '' ) == 0) {
		$fach3 = NULL;
		$fach3_lehrer = NULL;
	}	
	
	if(!isset($sl['vname']) || strlen($sl['vname']) < 3 || strlen($sl['vname']) > 49) {
		echo "<br><br>Bitte gib einen Vornamen an, der zwischen 3 und 49 Zeichen lang ist.";
//		return "<br><br>Bitte gib einen Vornamen an, der zwischen 3 und 49 Zeichen lang ist.";
	}
	if(!isset($sl['nname']) || strlen($sl['nname']) < 3 || strlen($sl['nname']) > 49) {
		echo "<br><br>Bitte gib einen Nachnamen an, der zwischen 3 und 49 Zeichen lang ist.";
//		return "<br><br>Bitte gib einen Nachnamen an, der zwischen 3 und 49 Zeichen lang ist.";
	}
	if(!isset($sl['klassenlehrer_name']) || strlen($sl['klassenlehrer_name']) < 3 || strlen($sl['klassenlehrer_name']) > 49 || !preg_match("/^(Herr|Frau|herr|frau|Hr.|Fr.|hr.|fr.) [A-Za-z]*/", $sl['klassenlehrer_name'])) {
		echo "<br><br>Bitte gib einen korrenkten Namen des Klassenlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
//		return "<br><br>Bitte gib einen Namen des Klassenlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
	}
	//preg_match funktioniert nicht
	if(!isset($sl['klasse_kurs']) || strlen($sl['klasse_kurs']) < 1 || strlen($sl['klasse_kurs']) > 2 ||  array_search($sl['klasse_kurs'], $klassen) === false) {
		echo "<br><br>Bitte gib eine korrekte Klasse/Kurs an.";
//		return "<br><br>Bitte gib eine korrekte Klasse/Kurs an.";
	}
	if(!isset($sl['klassenstufe']) || $sl['klassenstufe'] < 5  || $sl['klassenstufe'] > 12 || strlen($sl['klassenstufe']) == 0) {
		echo "<br><br>Bitte gib eine korrekte Klassenstufe an.";
//		return "<br><br>Bitte gib eine korrekte Klassenstufe an.";
	}
	if(!isset($sl['email']) || (!filter_var($sl['email'], FILTER_VALIDATE_EMAIL) && !preg_match("/^\+?([0-9\/ -]+)$/", $sl['telefon']) ) || !isset($sl['telefon'])) {
		echo "<br><br>Bitte gib eine korrekte E-Mail-Adresse oder Telefonnummer an.";
		//		return "<br><br>Bitte gib eine korrekte E-Mail-Adresse oder Telefonnummer an.";
	}
	
	if(!isset($sl['fach1']) || array_search($sl['fach1'], $faecher) === false || strlen($sl['fach1']) == 0) {
		echo "<br><br>Bitte wähle ein korrektes Fach aus.";
//		return "<br><br>Bitte wähle ein korrektes Fach aus.";
	}
	if(!isset($sl['fach2']) || array_search($sl['fach2'], $faecher) === false) {
		echo "<br><br>Bitte wähle ein korrektes 2.Fach aus.";
		//		return "<br><br>Bitte wähle ein korrektes 2.Fach aus.";
	}
	if(!isset($sl['fach3']) || array_search($sl['fach3'], $faecher) === false) {
		echo "<br><br>Bitte wähle ein korrektes 3.Fach aus.";
		//		return "<br><br>Bitte wähle ein korrektes 3.Fach aus.";
	}

	if(!isset($sl['fach1_lehrer']) || strlen($sl['fach1_lehrer']) < 3 || strlen($sl['fach1_lehrer']) > 49 || !preg_match("/^(Herr|Frau|herr|frau|Hr.|Fr.|hr.|fr.) [A-Za-z]*/", $sl['fach1_lehrer'])) {
		echo "<br><br>Bitte gib einen korrenkten Namen des Fachlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
		//		return "<br><br>Bitte gib einen Namen des Klassenlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
	}
	if($sl['fach2_lehrer'] != NULL && (strlen($sl['fach2_lehrer']) < 3 || strlen($sl['fach2_lehrer']) > 49 || !preg_match("/^(Herr|Frau|herr|frau|Hr.|Fr.|hr.|fr.) [A-Za-z]*/", $sl['fach2_lehrer']) )  ) {
		echo "<br><br>Bitte gib einen korrenkten Namen des 2.Fachlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
		//		return "<br><br>Bitte gib einen korekten Namen des 2.Fachlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
	}
	if($sl['fach3_lehrer'] != NULL && (strlen($sl['fach3_lehrer']) < 3 || strlen($sl['fach3_lehrer']) > 49 || !preg_match("/^(Herr|Frau|herr|frau|Hr.|Fr.|hr.|fr.) [A-Za-z]*/", $sl['fach3_lehrer']) )  ) {
		echo "<br><br>Bitte gib einen korrenkten Namen des 3.Fachlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
		//		return "<br><br>Bitte gib einen korrekten Namen des 3.Fachlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
	}
	return "<br><br>Error";
//	return $sl;
}
?>