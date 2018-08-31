<?php
class lehrer {
	private $id;
	private $klasse;
	private $klassenstufe;
	private $klassenlehrer_name;
	private $comment;
	public $person = person::class;
	public $faecher = array();
	public $zeit = array();
	const stati = array(
			'neu', 
			'ausstehend', 
			'vermittelt'
	);
	/*
	 * Wenn direkt Lehrer-ID übergeben wird, dann wird der Lehrer gleich komplett geladen
	 *
	 *
	 */
	function __construct(int $pid, int $id = -1) {
		if ($id != -1) {
			$return = query_db("SELECT * FROM `lehrer` WHERE id = :lid", $id);
			$lehrer = $return->fetch();
			if ($lehrer) {
				$this->id = $lehrer['id'];
				$this->klasse = $lehrer['klasse'];
				$this->klassenlehrer_name = $lehrer['klassenlehrer_name'];
				$this->klassenstufe = $lehrer['klassenstufe'];
				$this->comment = $lehrer['comment'];
				$pid = $lehrer['pid'];
				$return = query_db("SELECT * FROM `zeit` WHERE lid = :lid", $this->id);
				$times = $return->fetch();
				while ($times) {
					$this->zeit[] = array(
							'id' => $times['id'], 
							'tag' => $times['tag'], 
							'anfang' => date("H:i", strtotime($times['anfang'])), 
							'ende' => date("H:i", strtotime($times['ende']))
					);
					$times = $return->fetch();
				}
				$return = query_db("SELECT * FROM `bietet_an` WHERE lid = :lid", $this->id);
				$fach = $return->fetch();
				while ($fach) {
					$this->faecher[] = array(
							'fid' => $fach['fid'], 
							'nachweis_vorhanden' => $fach['nachweis_vorhanden'], 
							'fachlehrer' => $fach['fachlehrer'], 
							'notenschnitt' => $fach['notenschnitt'], 
							'status' => $fach['status']
					);
					$fach = $return->fetch();
				}
			}
		}
		if (isset($pid)) {
			if (!class_exists("person")) {
				require 'includes/class_person.php';
			}
			$this->person = new person();
			$this->person->load_person($pid);
		}
	}
	function get_id() {
		return $this->id;
	}
	function get_klasse() {
		return $this->klasse;
	}
	function get_klassenstufe() {
		return $this->klassenstufe;
	}
	function get_klassenlehrer() {
		return $this->klassenlehrer_name;
	}
	function get_comment() {
		return $this->comment;
	}
	
	/*
	 * erwartet array mit:
	 * klassenstufe
	 * klasse
	 * klassenlehrer
	 * fachkuerzel ---\
	 * fachlehrer ---|-> für 3 Fächer
	 * fachnachweis_vorhanden ---/
	 *
	 * tag
	 * anfangszeit
	 * endzeit
	 *
	 *
	 */
	function add_lehrer(array $params_arr) {
		if (isset($this->person) && is_array($params_arr)) {
			global $GLOBAL_CONFIG;
			isset($params_arr['comment']) ?: $params_arr['comment'] = '';
			$error = '';
			if (strlen($params_arr['klassenlehrer_name'] != 0) && (strlen($params_arr['klassenlehrer_name']) < 3 || strlen($params_arr['klassenlehrer_name']) > 49 || !preg_match("/^(Herr|Frau|herr|frau|Hr.|Fr.|hr.|fr.|Dr.|Doktor|DR.|Dr) [A-Za-z]*/", $params_arr['klassenlehrer_name']))) {
				$error = $error . "<br><br>Bitte gib einen korrekten Namen des Klassenlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
			}
			if (!isset($params_arr['klassenstufe']) || $params_arr['klassenstufe'] < 5 || $params_arr['klassenstufe'] > 12 || strlen($params_arr['klassenstufe']) == 0) {
				$error = $error . "<br><br>Bitte gib eine korrekte Klassenstufe an.";
			}
			if (!isset($params_arr['klasse']) || strlen($params_arr['klasse']) < 1 || strlen($params_arr['klasse']) > 2 || array_search($params_arr['klasse'], $GLOBAL_CONFIG['klassen']) === false) {
				$error = $error . "<br><br>Bitte gib eine korrekte Klasse/Kurs an.";
			}
			if (strlen($error) != 0) {
				echo $error;
				return false;
			}else {
				$params_arr['klassenlehrer_name'] = htmlspecialchars($params_arr['klassenlehrer_name'], ENT_QUOTES, 'UTF-8');
				$params_arr['klasse'] = htmlspecialchars($params_arr['klasse'], ENT_QUOTES, 'UTF-8');
				$params_arr['comment'] = htmlspecialchars($params_arr['comment'], ENT_QUOTES, 'UTF-8');
				$this->klasse = $params_arr['klasse'];
				$this->klassenstufe = $params_arr['klassenstufe'];
				$this->klassenlehrer_name = $params_arr['klassenlehrer_name'];
				$this->comment = $params_arr['comment'];
				$return = query_db("SELECT * FROM `lehrer` WHERE pid = :pid AND schuljahr = :schuljahr", $this->person->id, get_current_year());
				$return = $return->fetch();
				// var_dump($return);
				if ($return === false) {
					$return_prep = query_db("INSERT INTO `lehrer` (`id`,`pid`,`schuljahr`,`klassenstufe`,`klasse`,`klassenlehrer_name`, `comment`) VALUES (:id, :pid, :schuljahr, :klassenstufe, :klasse, :klassenlehrer_name, :comment );", NULL, $this->person->id, get_current_year(), $this->klassenstufe, $this->klasse, $this->klassenlehrer_name, $this->comment);
					$return = query_db("SELECT * FROM `lehrer` WHERE pid = :pid AND schuljahr = :schuljahr", $this->person->id, get_current_year());
					$lehrer = $return->fetch();
					$this->id = $lehrer['id'];
					echo "Der Lehrer wurde erfolgreich hinzugefügt";
					return true;
				}else {
					echo "Der Lehrer existiert bereits in dem Schuljahr";
					$this->load_lehrer_pid();
					return false;
				}
			}
		}
	}
	function change_lehrer(array $params_arr) {
		if (isset($this->person) && is_array($params_arr)) {
			global $GLOBAL_CONFIG;
			isset($params_arr['comment']) ?: $params_arr['comment'] = '';
			$error = '';
			if (strlen($params_arr['klassenlehrer_name'] != 0) && (strlen($params_arr['klassenlehrer_name']) < 3 || strlen($params_arr['klassenlehrer_name']) > 49 || !preg_match("/^(Herr|Frau|herr|frau|Hr.|Fr.|hr.|fr.|Dr.|Doktor|DR.|Dr) [A-Za-z]*/", $params_arr['klassenlehrer_name']))) {
				$error = $error . "<br><br>Bitte gib einen korrekten Namen des Klassenlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
			}
			if (!isset($params_arr['klassenstufe']) || $params_arr['klassenstufe'] < 5 || $params_arr['klassenstufe'] > 12 || strlen($params_arr['klassenstufe']) == 0) {
				$error = $error . "<br><br>Bitte gib eine korrekte Klassenstufe an.";
			}
			if (!isset($params_arr['klasse']) || strlen($params_arr['klasse']) < 1 || strlen($params_arr['klasse']) > 2 || array_search($params_arr['klasse'], $GLOBAL_CONFIG['klassen']) === false) {
				$error = $error . "<br><br>Bitte gib eine korrekte Klasse/Kurs an.";
			}
			if (strlen($error) != 0) {
				echo $error;
				return false;
			}else {
				$params_arr['klassenlehrer_name'] = htmlspecialchars($params_arr['klassenlehrer_name'], ENT_QUOTES, 'UTF-8');
				$params_arr['klasse'] = htmlspecialchars($params_arr['klasse'], ENT_QUOTES, 'UTF-8');
				$params_arr['comment'] = htmlspecialchars($params_arr['comment'], ENT_QUOTES, 'UTF-8');
				$this->klasse = $params_arr['klasse'];
				$this->klassenstufe = $params_arr['klassenstufe'];
				$this->klassenlehrer_name = $params_arr['klassenlehrer_name'];
				$this->comment = $params_arr['comment'];
				$return = query_db("SELECT * FROM `lehrer` WHERE pid = :pid AND schuljahr = :schuljahr", $this->person->id, get_current_year());
				$return = $return->fetch();
				// var_dump($return);
				if ($return !== false) {
					$return_prep = query_db("UPDATE `lehrer` SET `klassenstufe` = :klassenstufe, `klasse` = :klasse, `klassenlehrer_name` = :klassenelehrer_name, `comment` = :comment WHERE id = :id;", $this->klassenstufe, $this->klasse, $this->klassenlehrer_name, $this->comment, $this->id);
				}else {
					echo "Der Lehrer existiert noch nicht in dem Schuljahr";
					$this->load_lehrer_pid();
					return false;
				}
			}
		}
	}
	function load_lehrer_pid($pid = -1, $year = -1) {
		if ($pid == -1) {
			$pid = $this->person->id;
		}
		if ($year == -1) {
			$year = get_current_year();
		}
		$return = query_db("SELECT * FROM `lehrer` WHERE pid = :pid AND schuljahr = :schuljahr", $pid, $year);
		$lehrer = $return->fetch();
		if ($lehrer) {
			$this->id = $lehrer['id'];
			$this->klasse = $lehrer['klasse'];
			$this->klassenlehrer_name = $lehrer['klassenlehrer_name'];
			$this->klassenstufe = $lehrer['klassenstufe'];
			$this->comment = $lehrer['comment'];
			$return = query_db("SELECT * FROM `zeit` WHERE lid = :lid", $this->id);
			$times = $return->fetch();
			$this->zeit = array();
			while ($times) {
				$this->zeit[] = array(
						'id' => $times['id'], 
						'tag' => $times['tag'], 
						'anfang' => date("H:i", strtotime($times['anfang'])), 
						'ende' => date("H:i", strtotime($times['ende']))
				);
				$times = $return->fetch();
			}
			$return = query_db("SELECT * FROM `bietet_an` WHERE lid = :lid", $this->id);
			$fach = $return->fetch();
			$this->faecher = array();
			while ($fach) {
				$this->faecher[] = array(
						'fid' => $fach['fid'], 
						'nachweis_vorhanden' => $fach['nachweis_vorhanden'], 
						'fachlehrer' => $fach['fachlehrer'], 
						'notenschnitt' => $fach['notenschnitt'], 
						'status' => $fach['status']
				);
				$fach = $return->fetch();
			}
		}
	}
	function add_time(array $zeit) {
		// var_dump($zeit);
		if (isset($this->id) && is_array($zeit)) {
			$weekdays = array(
					'mo', 
					'di', 
					'mi', 
					'do', 
					'fr'
			);
			if (array_search($zeit['tag'], $weekdays) !== false) {
				if (strtotime($zeit['from']) !== false && strtotime($zeit['until']) !== false) {
					$retun = query_db("SELECT * FROM `zeit` WHERE lid = :lid AND tag = :tag", $this->id, $zeit['tag']);
					$time = $retun->fetch();
					if ($time === false) {
						query_db("INSERT INTO `zeit` (`lid`,`tag`,`anfang`,`ende`) VALUES (:id, :tag, :anfang, :ende);", $this->id, $zeit['tag'], $zeit['from'], $zeit['until']);
					}else {
						echo "Dem Lehrer / Der Lehrerin wurde bereits eine Zeit für diesen Tag zugeordnet";
					}
				}
			}
		}
	}
	function remove_time(int $tid) {
		$return = query_db("DELETE FROM `zeit` WHERE id = :id", $tid);
		if ($return) {
			return true;
		}else {
			echo "Eine Zeit konnte nicht gelöscht werden";
			return false;
		}
	}
	function get_zeit() {
		$return = query_db("SELECT * FROM `zeit` WHERE lid = :lid", $this->id);
		if ($return) {
			$result = $return->fetchAll();
			return $result;
		}
		return false;
	}
	function add_angebot_fach($fachid, $nachweis_vorhanden, $fachlehrer, $notenschnitt, $status) {
		// var_dump($nachweis_vorhanden);
		// var_dump($this->id);
		$nachweis_vorhanden = boolval($nachweis_vorhanden);
		if (isset($this->id) && is_bool($nachweis_vorhanden) && is_int(intval($fachid)) && array_search($status, self::stati) !== false) {
			$return = query_db("SELECT * FROM `bietet_an` WHERE lid = :lid AND fid = :fid", $this->id, $fachid);
			if ($return->fetch() !== false) {
				echo "Es existiert bereits ein Angebot für diesen Lehrer und für dieses Fach!";
			}else {
				if (!$nachweis_vorhanden) {
					echo "Der Nachhilfelehrer hat noch keine Bestätigung für die Eignung zum Nachhilfeunterricht für dieses Fach!";
				}
				query_db("INSERT INTO `bietet_an` (`lid`, `fid`, `nachweis_vorhanden`, `fachlehrer`, `notenschnitt`, `status`) VALUES (:lid, :fid, :nachweis_vorhanden, :fachlehrer, :notenschnitt, :status)", $this->id, $fachid, intval($nachweis_vorhanden), $fachlehrer, $notenschnitt, $status);
			}
		}else {
			// var_dump($fachid);
			echo "Ein Fehler ist aufgetreten";
		}
	}
	function remove_angebot_fach(int $fid) {
		$return = query_db("DELETE FROM `bietet_an` WHERE fid = :fid AND lid = :lid", $fid, $this->id);
		if ($return) {
			return true;
		}else {
			echo "Ein Fach konnte nicht gelöscht werden";
			return false;
		}
	}
	function get_angebot_faecher() {
		$return = query_db("SELECT * FROM `bietet_an` WHERE lid = :lid", $this->id);
		if ($return) {
			$result = $return->fetchAll();
			return $result;
		}
		return false;
	}
	
	function delete() {
		$fehler = 0;
		//Löschen sämtlicher Zeiten
		for ($i = 0; $i < count($this->zeit); $i++) {
			if (!$this->remove_time($this->zeit[$i]['id'])) {
				$fehler++;
			}
		}
		for ($i = 0; $i < count($this->faecher); $i++) {
			if (!$this->remove_angebot_fach($this->faecher[$i]['fid'])) {
				$fehler++;
			}
		}
		$return = query_db("DELETE FROM `unterricht` WHERE lid = :lid", $this->id);
		if (!$return) {
			$fehler++;
		}
		$return = query_db("DELETE FROM `lehrer` WHERE id = :id", $this->id);
		if (!$return) {
			$fehler++;
		}
		if ($fehler > 0) {
			echo "Es traten beim Löschen $fehler Fehler auf";
		}else{
			echo "Daten erfolgreich gelöscht";
		}
		
		
	}
	
	function exists_in_current_year() {
		$return = query_db("SELECT * FROM `lehrer` WHERE pid = :pid AND schuljahr = :year;", $this->person->id, get_current_year());
		if(!$return) {
			return false;
		}
		$result = $return->fetch();
		if($result === false) {
			return false;
		}else{
			return true;
		}
	}
}