<?php
function get_name_of_tag($kuerzel) {
	$tage = array(
			'mo' => 'Montag', 
			'di' => 'Dienstag', 
			'mi' => 'Mittwoch', 
			'do' => 'Donnerstag', 
			'fr' => 'Freitag'
	);
	if (isset($tage[$kuerzel])) {
		return $tage[$kuerzel];
	}else {
		return false;
	}
}
function get_stunde_for_time($time, $time2) {
	global $GLOBAL_CONFIG;
	$timestamp = strtotime($time);
	$timestamp2 = strtotime($time2);
	if($timestamp === false || $timestamp2 === false) {
		echo "Die Üergebene Zeit war nicht korrekt";
	}
	for ($i = 5; $i <= 8; $i++) {
		if ($timestamp >= strtotime($GLOBAL_CONFIG['stundenplan'][$i]['anfang']) && $timestamp < strtotime($GLOBAL_CONFIG['stundenplan'][$i]['ende'])) {
			if ($timestamp2 > strtotime($GLOBAL_CONFIG['stundenplan'][$i]['ende'])) {
				if(isset($GLOBAL_CONFIG['stundenplan'][($i+1)]['ende']) && $timestamp2 > strtotime($GLOBAL_CONFIG['stundenplan'][($i+1)]['anfang'])) {
					return array($i,($i+1));
				}else{
					return $i;
				}
			}else{
				return $i;
			}
		}
		if ($timestamp <= strtotime($GLOBAL_CONFIG['stundenplan'][$i]['anfang'])) {
			if(isset($GLOBAL_CONFIG['stundenplan'][($i+1)]['ende']) && $timestamp2 > strtotime($GLOBAL_CONFIG['stundenplan'][($i+1)]['anfang'])) {
				return array($i,($i+1));
			}
			return $i;
		}
	}
	if ($timestamp < strtotime($GLOBAL_CONFIG['stundenplan'][5]['anfang'])) {
		echo "EIn Fehler ist aufgetreten. So zeitig gibt es keinen Zimmerplan";
		return false;
	}
	if ($timestamp > strtotime($GLOBAL_CONFIG['stundenplan'][8]['anfang'])) {
		echo "Ein Fehler ist aufgetreten. So spät gibt es keinen Zimmerplan";
		return false;
	}
}
function get_faecher_all() {
	$return = query_db("SELECT * FROM `faecher`");
	$result = $return->fetchAll();
	if ($result !== false) {
		return $result;
	}else {
		return "Kein Fach gefunden";
	}
}
function get_faecher_name_of_id($fachid) {
	$return = query_db("SELECT * FROM `faecher` WHERE id = :fid", $fachid);
	$result = $return->fetch();
	if ($result) {
		return $result['name'];
	}else {
		return "Kein Fach gefunden";
	}
}
function get_faecher_name_of_kuerzel($fach_kuerzel) {
	$return = query_db("SELECT * FROM `faecher` WHERE kuerzel = :kuerzel", $fach_kuerzel);
	$result = $return->fetch();
	if ($result) {
		return $result['name'];
	}else {
		return "Kein Fach gefunden";
	}
}
function get_faecher_id_of_kuerzel($fach_kuerzel) {
	$return = query_db("SELECT * FROM `faecher` WHERE kuerzel = :kuerzel", $fach_kuerzel);
	$result = $return->fetch();
	if ($result) {
		return $result['id'];
	}else {
		return "Kein Fach gefunden";
	}
}
function get_faecher_id_of_name($fachname) {
	$return = query_db("SELECT * FROM `faecher` WHERE name = :name", $fachname);
	$result = $return->fetch();
	if ($result) {
		return $result['id'];
	}else {
		return "Kein Fach gefunden";
	}
}
function get_current_year() {
	// return "1617";
	$year = intval(date('y'));
	// echo date('y');
	$comp_date = intval(date('md'));
	// echo "<br>TEST".$comp_date;
	// vor oder nach 1.8. ?
	if ($comp_date > 801) {
		// echo "yes";
		$year = $year * 100 + $year + 1;
	}else {
		$year = (($year - 1) * 100) + $year;
	}
	// echo "<br><br>".$year;
	return $year;
}
function format_klassenstufe_kurs($klassenstufe, $klasse) {
	if (is_numeric($klasse)) {
		return $klassenstufe . "/" . $klasse;
	}else {
		return $klassenstufe . $klasse;
	}
}

// Bei Fehler wird false zurückgegeben
/*
 * Syntax: ("UPDATE tabelle SET row1 = :row1, row2 = :row2.....")
 * Wichtig: Leerzeichen zwischen :row1, und row2. Ansonsten funktioniert Funktion nicht!!!!!!!!!!
 * 
 * 
 */
function query_db($statement, ...$params) {
	global $pdo;
	global $user;
	if(!isset($pdo) || $pdo == null) {
		echo "Ein DB_Fehler ist aufgetreten";
		$user->log(user::LEVEL_ERROR, "\$pdo nicht definiert oder null");
	}
	$stat_ex = explode(' ', $statement);
	$i = 0;
	// var_dump(debug_backtrace());
//var_dump($params);
	// var_dump($stat_ex);
	// baue assoziativen Array für prepared-Statement
	$parameter = array();
	$replace = array(
			',' => '', 
			')' => '', 
			';' => ''
	);
	foreach ($stat_ex as $string) {
		if (strpos($string, ':') !== false) {
			// var_dump($string);
			$stringpart = explode(':', $string);
			// var_dump($stringpart);
			// $parameter[$stringpart[1]] = $params[$i];
			$parameter[strtr($stringpart[1], $replace)] = $params[$i];
			// var_dump($parameter);
			$i++;
		}
	}
	// var_dump($parameter);
	$ret_prep = $pdo->prepare($statement);
	// var_dump($ret_prep);
	if ($ret_prep === false) {
		echo "Ein DB-Fehler ist aufgetreten - 1";
		$user->log(user::LEVEL_ERROR, "DB-Fehler ist aufgetreten!" . $ret_prep->errorInfo());
		var_dump($ret_prep->errorInfo());
		var_dump(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
		get_xml("exit_on_db_failure","value") == 'true' ?: die();
		return false;
	}else {
		$return = $ret_prep->execute($parameter);
		if ($return === false) {
			echo "Ein DB-Fehler ist aufgtreten";
			$user->log(user::LEVEL_ERROR, "DB-Fehler ist aufgetreten!" . implode("-", $ret_prep->errorInfo()));
			var_dump($ret_prep->errorInfo());
			get_xml("exit_on_db_failure","value") == 'true' ?: die();
			return false;
		}else {
			return $ret_prep;
		}
	}
}

function set_view($view) {
	if($view == "table") {
		$_SESSION['view'] = "table";
	}else if($view == "list") {
		$_SESSION['view'] = "list";
	}else{
		$_SESSION['view'] = "";
	}
}

function get_view() {
	if(isset($_SESSION['view'])) {
		return $_SESSION['view'];
	}else {
		return "list";
	}
}

function get_xml($key, $param = "none") {
	global $xml;
	$child =  $xml->xpath($key);
	if($child == false) {
		echo "Ein grober Fehler trat auf beim Lesen von XML-Werten: $key ist nicht existent!";
		return false;
	}
//	var_dump($child);
	switch ($param) {
		case "value":
			return ( string ) $child[0];
		case "key":
			return ( string ) $child[0]->getName();
		case "name":
			return ( string ) $child[0]->attributes()['name'];
		case "type":
			return ( string ) $child[0]->attributes()['type'];
		default:
			return array(
					'key' => $child[0]->getName(), 
					'value' => ( string ) $child[0], 
					'name' => ( string ) $child[0]->attributes()['name'], 
					'type' => ( string ) $child[0]->attributes()['type']
			);	
	}
}

function get_children($child) {
	global $ret;
	var_dump($child);
//	$ret = array();
	if ($child->count() == 0) {
		echo "test";
		return array('key' => $child->getName(), 'value' => (string)$child, 'name' => (string)$child->attributes()['name'], 'type' => (string)$child->attributes()['type']);
	}else{
		echo "tetssd";
		var_dump($child->children());
		foreach ($child->children() as $cofc) {
			$ret[] = get_children($cofc);
		}
	}
	var_dump($ret);
//	return $ret;
}


function getall_xml_keys() {
	global $xml;
//	var_dump($xml);
	$all_keys = array();
//	$ret = array();
//	var_dump(get_children($xml));
	foreach ($xml->children() as $child) {
//		var_dump($child->count());
//		var_dump($child->getName());
		if($child->count() == 0) {
			$all_keys[] = array('key' => $child->getName(), 'value' => (string)$child, 'name' => (string)$child->attributes()['name'], 'type' => (string)$child->attributes()['type']);
//			var_dump($all_keys);
		}else if($child->count() > 0) {
			$all_keys[] = array('key' => $child->getName(), 'value' => (string)$child, 'name' => (string)$child->attributes()['name'], 'type' => (string)$child->attributes()['type']);
			foreach ($child->children() as $child_of_child) {
//				var_dump($child_of_child);
				if($child_of_child->count() == 0) {
					$all_keys[] = array('key' => $child->getName()."/".$child_of_child->getName(), 'value' => (string)$child_of_child, 'name' => (string)$child_of_child->attributes()['name'],'type' => (string)$child_of_child->attributes()['type']);
				}else if($child_of_child->count() > 0 ) {
					$all_keys[] = array('key' => $child->getName()."/".$child_of_child->getName(), 'value' => (string)$child_of_child, 'name' => (string)$child_of_child->attributes()['name'],'type' => (string)$child_of_child->attributes()['type']);
					foreach ($child_of_child->children() as $cofcofc) {
						if($cofcofc->count() == 0) {
							$all_keys[] = array('key' => $child->getName()."/".$child_of_child->getName()."/".$cofcofc->getName(), 'value' => (string)$cofcofc, 'name' => (string)$cofcofc->attributes()['name'], 'type' => (string)$cofcofc->attributes()['type']);
						}
					}
				}
			}
		}
	}
	return $all_keys;
}

function set_xml($key, $value) {
	global $xml;
	$object =  $xml->xpath($key);
	if($object === false) {
		echo "Ein grober Fehler trat auf beim ändern von XML-Werten!";
		return false;
	}else{
		$object[0][0] = $value;
	}
}

function write_xml() {
	global $xml,$GLOBAL_CONFIG;
	$ret = $xml->asXML($GLOBAL_CONFIG['settings_file']);
	if ($ret != true) {
		echo "Ein schwerwiegender Fehler ist aufgetreten!!";
		die();
	}else {
		return true;
	}
}


function init_settings_xml() {
	global $GLOBAL_CONFIG,$xml;
	$xml = simplexml_load_file($GLOBAL_CONFIG['settings_file']);
	if($xml == false) {
		echo "<h2>Ein sehr schwerwiegender Fehler ist aufgetreten. Bitte unbedingt den Administrator kontaktieren.";
	}
	return $xml;	
}


?>