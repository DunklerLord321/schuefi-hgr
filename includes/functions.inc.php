<?php
function get_name_of_tag($kuerzel) {
	$tage = array(
			'mo' => 'Montag',
			'di' => 'Dienstag',
			'mi' => 'Mittwoch',
			'do' => 'Donnerstag',
			'fr' => 'Freitag'
			);
	if(isset($tage[$kuerzel])) {
		return $tage[$kuerzel];
	}else{
		return false;
	}
}

function get_faecher_all() {
	$return = query_db("SELECT * FROM `faecher`");
	$result = $return->fetchAll();
	if ($result !== false) {
		return $result;
	} else {
		return "Kein Fach gefunden";
	}
}
function get_faecher_name_of_id($fachid) {
	$return = query_db("SELECT * FROM `faecher` WHERE id = :fid", $fachid);
	$result = $return->fetch();
	if ($result) {
		return $result['name'];
	} else {
		return "Kein Fach gefunden";
	}
}
function get_faecher_name_of_kuerzel($fach_kuerzel) {
	$return = query_db("SELECT * FROM `faecher` WHERE kuerzel = :kuerzel", $fach_kuerzel);
	$result = $return->fetch();
	if ($result) {
		return $result['name'];
	} else {
		return "Kein Fach gefunden";
	}
}

function get_faecher_id_of_kuerzel($fach_kuerzel) {
	$return = query_db("SELECT * FROM `faecher` WHERE kuerzel = :kuerzel", $fach_kuerzel);
	$result = $return->fetch();
	if ($result) {
		return $result['id'];
	} else {
		return "Kein Fach gefunden";
	}
}

function get_faecher_id_of_name($fachname) {
	$return = query_db("SELECT * FROM `faecher` WHERE name = :name", $fachname);
	$result = $return->fetch();
	if ($result) {
		return $result['id'];
	} else {
		return "Kein Fach gefunden";
	}
}
function get_current_year() {
	return "1617";
	$year = intval(date('y'));
	// echo date('y');
	$comp_date = intval(date('md'));
	// echo "<br>TEST".$comp_date;
	// vor oder nach 1.8. ?
	if ($comp_date > 801) {
		// echo "yes";
		$year = $year * 100 + $year + 1;
	} else {
		$year = (($year - 1) * 100) + $year;
	}
	// echo "<br><br>".$year;
	return $year;
}

function format_klassenstufe_kurs($klassenstufe, $klasse) {
	if (is_numeric($klasse)) {
		return $klassenstufe."/".$klasse;
	}else{
		return $klassenstufe.$klasse;
	}
}

//Bei Fehler wird false zurückgegeben
function query_db($statement, ...$params) {
	global $exit_on_db_failure;
	global $pdo;
	global $user;
	$stat_ex = explode(' ', $statement);
	$i = 0;
//	var_dump(debug_backtrace());
// var_dump($params);
//var_dump($stat_ex);
	// baue assoziativen Array für prepared-Statement
	$parameter = array();
	$replace = array(
			',' => '',
			')' => '',
			';' => ''
	);
	foreach ( $stat_ex as $string ) {
		if (strpos($string, ':') !== false) {
//			var_dump($string);
			$stringpart = explode(':', $string);
//			var_dump($stringpart);
			// $parameter[$stringpart[1]] = $params[$i];
			$parameter[strtr($stringpart[1], $replace)] = $params[$i];
//			var_dump($parameter);
			$i++;
		}
	}
//var_dump($parameter);
	$ret_prep = $pdo->prepare($statement);
//var_dump($ret_prep);
	if ($ret_prep === false) {
		echo "Ein DB-Fehler ist aufgetreten - 1";
		$user->log(user::LEVEL_ERROR, "DB-Fehler ist aufgetreten!".$ret_prep->errorInfo());
		var_dump($ret_prep->errorInfo());
		var_dump(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
		$exit_on_db_failure == 0 ?: die();
		return false;
	} else {
		$return = $ret_prep->execute($parameter);
		if ($return === false) {
			echo "Ein DB-Fehler ist aufgtreten";
			$user->log(user::LEVEL_ERROR, "DB-Fehler ist aufgetreten!".implode("-", $ret_prep->errorInfo()));
			var_dump($ret_prep->errorInfo());
			$exit_on_db_failure == 0 ?: die();
			return false;
		} else {
			return $ret_prep;
		}
	}
}


?>