<?php
class schueler {
	private $id;
	private $klasse;
	private $klassenstufe;
	private $klassenlehrer_name;
	private $comment;
	private $person = person::class;
	public $faecher = array();
	public $zeit = array();
	function __construct(int $pid) {
		$this->person = new person();
		$this->person->load_person($pid);
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
	 * klassenlehrer_name
	 * eventuell comment
	 *
	 *
	 */
	/*
	 * Funktion fügt neuen Schüler hinzu, wenn vorhanden wird Schüler geladen
	 * 
	 * 
	 * 
	 */
	function add_schueler(array $params_arr) {
		if (isset($this->person) && is_array($params_arr)) {
			global $GLOBAL_CONFIG;
			global $pdo;
			//Überprüfe Werte, ob valide
			isset($params_arr['comment']) ?: $params_arr['comment'] = '';
			$params_arr['klassenlehrer_name'] = strip_tags($params_arr['klassenlehrer_name']);
			$params_arr['klasse'] = strip_tags($params_arr['klasse']);
			$params_arr['comment'] = strip_tags($params_arr['comment']);
			$params_arr['klassenlehrer_name'] = htmlspecialchars($params_arr['klassenlehrer_name'], ENT_QUOTES, 'UTF-8');
			$params_arr['klasse'] = htmlspecialchars($params_arr['klasse'], ENT_QUOTES, 'UTF-8');
			$params_arr['comment'] = htmlspecialchars($params_arr['comment'], ENT_QUOTES, 'UTF-8');
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
				$return = query_db("SELECT * FROM `schueler` WHERE pid = :pid AND schuljahr = :schuljahr", $this->person->id, get_current_year());
				$return = $return->fetch();
				if ($return === false) {
					$this->klasse = $params_arr['klasse'];
					$this->klassenstufe = $params_arr['klassenstufe'];
					$this->klassenlehrer_name = $params_arr['klassenlehrer_name'];
					$this->comment = $params_arr['comment'];
					$return_prep = query_db("INSERT INTO `schueler` (`id`,`pid`,`schuljahr`,`klassenstufe`,`klasse`,`klassenlehrer_name`, `comment`) VALUES (:id, :pid, :schuljahr, :klassenstufe, :klasse, :klassenlehrer_name, :comment );", NULL, $this->person->id, get_current_year(), $this->klassenstufe, $this->klasse, $this->klassenlehrer_name, $this->comment);
					$return = query_db("SELECT * FROM `schueler` WHERE pid = :pid AND schuljahr = :schuljahr", $this->person->id, get_current_year());
					$schueler = $return->fetch();
					$this->id = $schueler['id'];
					echo "Der Schüler wurde erfolgreich hinzugefügt";
					return true;
				} else {
					echo "Der Schüler existiert bereits in dem Schuljahr";
					$this->load_schueler_pid();
					return false;
				}
			}
		}
	}
	function load_schueler_pid($pid = -1) {
		if ($pid = -1) {
			$pid = $this->person->id;
		}
		$return = query_db("SELECT * FROM `schueler` WHERE pid = :pid AND schuljahr = :schuljahr", $pid, get_current_year());
		$schueler = $return->fetch();
//		var_dump($schueler);
		if ($schueler) {
			$this->id = $schueler['id'];
			$this->klasse = $schueler['klasse'];
			$this->klassenlehrer_name = $schueler['klassenlehrer_name'];
			$this->klassenstufe = $schueler['klassenstufe'];
			$this->comment = $schueler['comment'];
			$return = query_db("SELECT * FROM `zeit` WHERE sid = :sid", $this->id);
			$times = $return->fetch();
			while ( $times ) {
				$this->zeit[] = array(
						'tag' => $times['tag'],
						'from' => $times['anfang'],
						'until' => $times['ende']
				);
				$times = $return->fetch();
			}
			$return = query_db("SELECT * FROM `fragt_nach` WHERE sid = :sid", $this->id);
			$fach = $return->fetch();
			while ( $fach ) {
				$this->faecher[] = array(
						'fid' => $fach['fid'],
						'langfristig' => $fach['langfristig'],
						'status' => $fach['status']
				);
				$fach = $return->fetch();
			}
		}
	}
	function add_time(array $zeit) {
//		var_dump($zeit);
		if (isset($this->id) && is_array($zeit)) {
			$weekdays = array(
					'mo',
					'di',
					'mi',
					'do',
					'fr'
			);
//			var_dump($zeit);
			if (array_search($zeit['tag'], $weekdays) !== false) {
				if (strtotime($zeit['from']) !== false && strtotime($zeit['until']) !== false) {
					$retun = query_db("SELECT * FROM `zeit` WHERE sid = :sid AND tag = :tag", $this->id, $zeit['tag']);
					$time = $retun->fetch();
					if ($time === false) {
						query_db("INSERT INTO `zeit` (`sid`,`tag`,`anfang`,`ende`) VALUES (:id, :tag, :anfang, :ende);", $this->id, $zeit['tag'], $zeit['from'], $zeit['until']);
					} else {
						echo "Dem schueler / Der schuelerin wurde bereits eine Zeit für diesen Tag zugeordnet";
					}
				}
			}
		}
	}
	function add_nachfrage_fach($fachid, bool $langfristig, $fachlehrer) {
		$fachid = intval($fachid);
		echo $fachid;
		if (isset($this->id) && is_bool($langfristig) && is_int($fachid)) {
			$return = query_db("SELECT * FROM `fragt_nach` WHERE sid = :sid AND fid = :fid", $this->id, $fachid);
			if ($return->fetch() !== false) {
				echo "Es existiert bereits ein Angebot für diesen Schüler und für dieses Fach!";
			} else {
				query_db("INSERT INTO `fragt_nach` (`sid`,`fid`,`langfristig`,`fachlehrer`, `status`) VALUES (:sid, :fid, :langfristig, :fachlehrer, :status)", $this->id, $fachid, intval($langfristig), $fachlehrer,  'neu');
			}
		} else {
			var_dump($this);
			var_dump($langfristig);
			var_dump($fachid);
			echo "Ein Fehler ist aufgetreten";
		}
	}
	
	function delete() {
		/*
		 * In Planung
		 * 
		 */
	}
	
	function get_nachfrage_faecher() {
		$return = query_db("SELECT * FROM `fragt_nach` WHERE sid = :sid", $this->id);
		if ($return) {
			$result = $return->fetchAll();
			return $result;
		}
		return false;
	}
	
	function get_zeit() {
		$return = query_db("SELECT * FROM `zeit` WHERE sid = :sid", $this->id);
		if ($return) {
			$result = $return->fetchAll();
			return $result;
		}
		return false;
	}
	
	function get_lehrer($fid) {
		var_dump($this->zeit);
		$return = query_db("SELECT * FROM `bietet_an` WHERE fid = :fid", $fid);
		$matching_lehrer = array();
		if ($return) {
			$angebot = $return->fetchAll();
			// durchlaufe die schleife für jeden Lehrer, der Fach anbietet
			for($i = 0; $i < count($angebot); $i++) {
				//hole Informationen über Lehrer
				$return = query_db("SELECT * FROM `lehrer` WHERE id = :lid and schuljahr = :schuljahr", $angebot[$i]['lid'], get_current_year());
				if ($return) {
					$lehrer = $return->fetch();
					echo $lehrer['pid'];
					if ($lehrer['klassenstufe'] > $this->klassenstufe) {
						//hole Sprechzeiten des Lehrers
						$return = query_db("SELECT * FROM `zeit` WHERE lid = :lid", $lehrer['id']);
						if ($return) {
							$gesamte_zeit = array();
							$zeit = $return->fetch();
							while($zeit) {
								$gesamte_zeit[] = array('tag' => $zeit['tag'],'anfang' => $zeit['anfang'],'ende' => $zeit['ende']);
								
								$zeit = $return->fetch();
							}
							//teste, ob schüler und Lehrer am gleichen Tag zu gleicher Zeit zeit haben
							foreach ($this->zeit as $schuelerzeit) {
								echo "<hr>";
								foreach ($gesamte_zeit as $lehrerzeit) {
									if($schuelerzeit['tag'] == $lehrerzeit['tag']) {
										echo "Tag stimmt<br>";
										// zeiten Vergleichen!
										$schueler_from = new DateTime($schuelerzeit['from']);
										$schueler_until = new DateTime($schuelerzeit['until']);
										$lehrer_from = new DateTime($lehrerzeit['anfang']);
										$lehrer_until = new DateTime($lehrerzeit['ende']);
										
										$interval = $schueler_from->diff($schueler_until);
										var_dump($lehrer_from);
										var_dump($schueler_from);
										$inter_lehrer_schuel_from = $lehrer_from->diff($schueler_from);
										var_dump($inter_lehrer_schuel_from);
										var_dump($schueler_from->diff($lehrer_from));
										if($interval->format("%h") >= 1) {
											echo "Schüler hat min 60min Zeit";
										}
										if($interval->format("%i") >= 90) {
											echo "Schüler hat min 90min Zeit";
										}
										var_dump($interval);
										var_dump($schueler_from);
										var_dump(strtotime($schuelerzeit['from']));
										var_dump(strtotime($schuelerzeit['until']));
										var_dump(strtotime("60min"));
										if ((strtotime($schuelerzeit['until']) - strtotime($schuelerzeit['from'])) > 3600) {
											echo "test";
										}
									}
								}
								var_dump($schuelerzeit);
							}
							$lehrer[] = $gesamte_zeit;
							var_dump($lehrer);
							$matching_lehrer[] = $lehrer;
							var_dump($matching_lehrer);
						}
					}
				}
			}
		}
	}
}