<?php
class lehrer {
	private $id;
	private $klasse;
	private $klassenstufe;
	private $klassenlehrer_name;
	private $comment;
	private $person = person::class;
	private $faecher = array();
	private $zeit = array();
	function __construct(int $pid) {
		$this->person = new person();
		$this->person->load_person($pid);
		$this->faecher[0]['kuerzel'] = "test";
	}
	function get_id() {
		return $this->id;
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
			global $pdo;
			isset($params_arr['comment']) ?: $params_arr['comment'] = '';
			$params_arr['klassenlehrer_name'] = strip_tags($params_arr['klassenlehrer_name']);
			$params_arr['klasse'] = strip_tags($params_arr['klasse']);
			$params_arr['comment'] = strip_tags($params_arr['comment']);
			$error = '';
			if (!isset($params_arr['klassenlehrer_name']) || strlen($params_arr['klassenlehrer_name']) < 3 || strlen($params_arr['klassenlehrer_name']) > 49 || !preg_match("/^(Herr|Frau|herr|frau|Hr.|Fr.|hr.|fr.|Dr.|Doktor|DR.|Dr) [A-Za-z]*/", $params_arr['klassenlehrer_name'])) {
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
			} else {
				$params_arr['klassenlehrer_name'] = htmlspecialchars($params_arr['klassenlehrer_name'], ENT_QUOTES, 'UTF-8');
				$params_arr['klasse'] = htmlspecialchars($params_arr['klasse'], ENT_QUOTES, 'UTF-8');
				$params_arr['comment'] = htmlspecialchars($params_arr['comment'], ENT_QUOTES, 'UTF-8');
				$this->klasse = $params_arr['klasse'];
				$this->klassenstufe = $params_arr['klassenstufe'];
				$this->klassenlehrer_name = $params_arr['klassenlehrer_name'];
				$this->comment = $params_arr['comment'];
				$return = query_db("SELECT * FROM `lehrer` WHERE pid = :pid AND schuljahr = :schuljahr", $this->person->id, get_current_year());
				if ($return === false) {
					$return_prep = query_db("INSERT INTO `lehrer` (`id`,`pid`,`schuljahr`,`klassenstufe`,`klasse`,`klassenlehrer_name`, `comment`) VALUES (:id, :pid, :schuljahr, :klassenstufe, :klasse, :klassenlehrer_name, :comment );", NULL, $this->person->id, get_current_year(), $this->klassenstufe, $this->klasse, $this->klassenlehrer_name, $this->comment);
					$return = query_db("SELECT * FROM `lehrer` WHERE pid = :pid AND schuljahr = :schuljahr", $this->person->id, get_current_year());
					$lehrer = $return->fetch();
					$this->id = $lehrer['id'];
				} else {
					echo "Der Lehrer existiert bereits in dem Schuljahr";
					return false;
				}
			}
		}
	}
	function load_lehrer($schuljahr, $pid) {
		$return = query_db("SELECT * FROM `lehrer` WHERE pid = :pid AND schuljahr = :schuljahr", $this->person->id, $schuljahr);
		$lehrer = $return->fetch();
		if ($lehrer) {
			$this->id = $lehrer['id'];
			$this->klasse = $lehrer['klasse'];
			$this->klassenlehrer_name = $lehrer['klassenlehrer_name'];
			$this->klassenstufe = $lehrer['klassenstufe'];
			$this->comment = $lehrer['comment'];
			$return = query_db("SELECT * FROM `zeit` WHERE lid = :lid", $this->id);
			$times = $return->fetch();
			while ( $times ) {
				$this->zeit[] = array(
						'tag' => $times['tag'],
						'anfang' => $times['anfang'],
						'ende' => $times['ende']
				);
				$times = $return->fetch();
			}
		}
	}
	function add_time(array $zeit) {
		if (isset($this->id) && is_array($zeit)) {
			$weekdays = array(
					'mo',
					'di',
					'mi',
					'do',
					'fr'
			);
			if (array_search($zeit[0], $weekdays) !== false) {
				if (strtotime($zeit[1]) !== false && strtotime($zeit[2]) !== false) {
					$retun = query_db("SELECT * FROM `zeit` WHERE lid = :lid AND tag = :tag", $this->id, $zeit[0]);
					$time = $retun->fetch();
					if ($time === false) {
						query_db("INSERT INTO `zeit` (`lid`,`tag`,`anfang`,`ende`) VALUES (:id, :tag, :anfang, :ende);", $this->id, $zeit[0], $zeit[1], $zeit[2]);
					} else {
						echo "Dem Lehrer / Der Lehrerin wurde bereits eine Zeit für diesen Tag zugeordnet";
					}
				}
			}
		}
	}
	function add_angebot_fach($fachid, $nachweis_vorhanden) {
		var_dump($nachweis_vorhanden);
		if (isset($this->id) && is_bool($nachweis_vorhanden) && is_int($fachid)) {
			$return = query_db("SELECT * FROM `bietet_an` WHERE lid = :lid AND fid = :fid", $this->id, $fachid);
			if ($return->fetch() !== false) {
				echo "Es existiert bereits ein Angebot für diesen Lehrer und für dieses Fach!";
			} else {
				if (!$nachweis_vorhanden) {
					echo "Der Nachhilfelehrer hat noch keine Bestätigung für die Eignung zum Nachhilfeunterricht für dieses Fach!";
				}
				query_db("INSERT INTO `bietet_an` (`lid`,`fid`,`nachweis_vorhanden`,`status`) VALUES (:lid, :fid, :nachweis_vorhanden, :status)", $this->id, $fachid, intval($nachweis_vorhanden), 'neu');
			}
		} else {
			var_dump($fachid);
			echo "Ein Fehler ist aufgetreten";
		}
	}
	function get_angebot_faecher() {
		$return = query_db("SELECT * FROM `bietet_an` WHERE lid = :lid", $this->id);
		$result = $return->fetchAll();
		return $result;
	}
}