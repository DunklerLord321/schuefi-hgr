<?php
echo password_hash("schuefionline123", PASSWORD_DEFAULT);
/******************
 //$empfaenger = "gajo01@gmx.de";
 $empfaenger = "yajo10@yahoo.de";
 $betreff = "Die Mail-Funktion";
 $from = "From: Schuelerfirma HGR <schuelerfirma.hgr@gmx.de>";
 $text = "Hallo, ich freu mich, von dir zu hören :)";
 
 $ret = mail($empfaenger, $betreff, $text, $from);
 if($ret == false) {
 echo "FAILURE";
 }*******************/

//
// A very simple PHP example that sends a HTTP POST to a remote site
//
$data = "fid=$id";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"localhost/schuefi/ajax_get_name_of_subject.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
		$data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec ($ch);
curl_close ($ch);
var_dump($server_output);
die();
$data = array(
		'vname' => "test",
		'zeit' => array(),
		'fid' => 3
);
$data['vname'] = "test";
$data['nname'] = "tetsgsdjkjksd";
$data['zeit'] = array('tag'=>'mo','from'=>"13:00");
$url = http_build_query($data);

$ch = curl_init();
var_dump($url);
var_dump($data);
curl_setopt($ch, CURLOPT_URL,"localhost/schuefi/ajax_new_person.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
		$url);

// in real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS,
//          http_build_query(array('postvar1' => 'value1')));

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);

curl_close ($ch);
var_dump($server_output);
exit();
require_once 'includes/functions.inc.php';
$input = file ( "Nachhilfepaare-2.csv" );
for($i = 0; $i < count ( $input ); $i ++) {
	$string = explode ( ",", $input [$i] );
	echo "<br><b>new</b><br>";
	$string = array_map('trim', $string);
	$person = array (
			'vname' => $string [0],
			'nname' => $string [1],
			'email' => $string [4],
			'klassenstufe' => 0,
			'klasse' => '',
			'klassenlehrer_name' => $string [5],
			'fach1' => get_faecher_kuerzel ( $string [14] ),
			'fach1_lehrer' => $string [6],
			'fach2' => get_faecher_kuerzel ( $string [15] ),
			'fach2_lehrer' => $string [7],
			'fach3' => get_faecher_kuerzel ( $string [16] ),
			'fach3_lehrer' => $string [8],
			'mo_anfang' => '00:00:00',
			'mo_ende' => '00:00:00',
			'di_anfang' => '00:00:00',
			'di_ende' => '00:00:00',
			'mi_anfang' => '00:00:00',
			'mi_ende' => '00:00:00',
			'do_anfang' => '00:00:00',
			'do_ende' => '00:00:00',
			'fr_anfang' => '00:00:00',
			'fr_ende' => '00:00:00',
			'telefon' => $string [3],
			'comment' => $string [17] 
	);
	if (! preg_match ( "/^(Herr|Frau|herr|frau|Hr.|Fr.|hr.|fr.|Dr.|Doktor|DR.|Dr) [A-Za-z]*/", $person ['klassenlehrer_name'] )) {
		// echo "<br><br>Bitte gib einen korrenkten Namen des Klassenlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
		echo "<br><b>Bitte gib einen korrekten Namen des Klassenlehrers an, der zwischen 3 und 49 Zeichen lang ist.</b>";
	}
	if (strstr ( $string [2], '/' )) {
		$person ['klassenstufe'] = substr ( $string [2], 0, 2 );
		$person ['klasse'] = substr ( $string [2], 3 );
	} else {
		if (is_numeric ( substr ( $string [2], 1, 1 ) )) {
			$person ['klassenstufe'] = substr ( $string [2], 0, 2 );
			$person ['klasse'] = substr ( $string [2], 2 );
		} else {
			$person ['klassenstufe'] = substr ( $string [2], 0, 1 );
			$person ['klasse'] = substr ( $string [2], 1 );
		}
	}
	if (strlen ( $string [9] > 0 )) {
		$person ['mo_anfang'] = strtok ( $string [9], "-" );
		if (strlen ( $string [10] ) > 10) {
			$person ['mo_ende'] = substr ( $string [9], - 5 );
		}
	}
	if (strlen ( $string [10] ) > 0) {
		$person ['di_anfang'] = strtok ( $string [10], "-" );
		if (strlen ( $string [10] ) > 10) {
			$person ['di_ende'] = substr ( $string [10], - 5 );
		}
	}
	if (strlen ( $string [11] ) > 0) {
		$person ['mi_anfang'] = strtok ( $string [11], "-" );
		if (strlen ( $string [11] ) > 10) {
			$person ['mi_ende'] = substr ( $string [11], - 5 );
		}
	}
	if (strlen ( $string [12] ) > 0) {
		$person ['do_anfang'] = strtok ( $string [12], "-" );
		if (strlen ( $string [12] ) > 10) {
			$person ['do_ende'] = substr ( $string [12], - 5 );
		}
	}
	if (strlen ( $string [13] ) > 0) {
		$person ['fr_anfang'] = strtok ( $string [13], "-" );
		if (strlen ( $string [13] ) > 10) {
			$person ['fr_ende'] = substr ( $string [13], - 5 );
		}
	}
	$save_person = $person;
	// echo "Bearbeitete Person:";
	$person = validate_input ( $person );
	if (is_array ( $person ) == false) {
		echo "Bei dieser Person trat ein Fehler auf!";
		var_dump ( $string );
		var_dump ( $save_person );
		echo $person;
	}
}

?>