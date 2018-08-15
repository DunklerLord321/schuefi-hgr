<?php
class schueler {
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
			'notwendig', 
			'ausstehend', 
			'nicht vermittelbar', 
			'vermittelt'
	);
	function __construct(int $pid, int $id = -1) {
		if ($id != -1) {
			$return = query_db("SELECT * FROM `schueler` WHERE id = :sid", $id);
			$schueler = $return->fetch();
			if ($schueler) {
				$this->id = $schueler['id'];
				$this->klasse = $schueler['klasse'];
				$this->klassenlehrer_name = $schueler['klassenlehrer_name'];
				$this->klassenstufe = $schueler['klassenstufe'];
				$this->comment = $schueler['comment'];
				$pid = $schueler['pid'];
				$return = query_db("SELECT * FROM `zeit` WHERE sid = :sid", $this->id);
				$times = $return->fetch();
				while ($times) {
					$this->zeit[] = array(
							'id' => $times['id'], 
							'tag' => $times['tag'], 
							'anfang' => $times['anfang'], 
							'ende' => $times['ende']
					);
					$times = $return->fetch();
				}
				$return = query_db("SELECT * FROM `fragt_nach` WHERE sid = :sid", $this->id);
				$fach = $return->fetch();
				while ($fach) {
					$this->faecher[] = array(
							'fid' => $fach['fid'], 
							'langfristig' => $fach['langfristig'], 
							'fachlehrer' => $fach['fachlehrer'], 
							'status' => $fach['status']
					);
					$fach = $return->fetch();
				}
			}
		}
		if (!class_exists("person")) {
			require 'includes/class_person.php';
		}
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
			// Überprüfe Werte, ob valide
			isset($params_arr['comment']) ?: $params_arr['comment'] = '';
			$params_arr['klassenlehrer_name'] = strip_tags($params_arr['klassenlehrer_name']);
			$params_arr['klasse'] = strip_tags($params_arr['klasse']);
			$params_arr['comment'] = strip_tags($params_arr['comment']);
			
			$params_arr['klassenlehrer_name'] = htmlspecialchars($params_arr['klassenlehrer_name'], ENT_QUOTES, 'UTF-8');
			$params_arr['klasse'] = htmlspecialchars($params_arr['klasse'], ENT_QUOTES, 'UTF-8');
			$params_arr['comment'] = htmlspecialchars($params_arr['comment'], ENT_QUOTES, 'UTF-8');
			$error = '';
			if (strlen($params_arr['klassenlehrer_name'] != 0) && (strlen($params_arr['klassenlehrer_name']) < 3 || strlen($params_arr['klassenlehrer_name']) > 49 || !preg_match("/^(Herr|Frau|herr|frau|Hr.|Fr.|hr.|fr.|Dr.|Doktor|DR.|Dr) [A-Za-z]*/", $params_arr['klassenlehrer_name']))) {
				$error = $error . "<br><br>Bitte gib einen korrekten Namen des Klassenlehrers an, der zwischen 3 und 49 Zeichen lang ist.";
			}
			if (!isset($params_arr['klassenstufe']) || $params_arr['klassenstufe'] < 5 || $params_arr['klassenstufe'] > 12 || strlen($params_arr['klassenstufe']) == 0) {
				$error = $error . "<br><br>Bitte gib eine korrekte Klassenstufe an.";
			}
			$params_arr['klasse'] = strtolower($params_arr['klasse']);
			if (!isset($params_arr['klasse']) || strlen($params_arr['klasse']) < 1 || strlen($params_arr['klasse']) > 2 || array_search($params_arr['klasse'], $GLOBAL_CONFIG['klassen']) === false) {
				$error = $error . "<br><br>Bitte gib eine korrekte Klasse/Kurs an.";
			}
			if (strlen($error) != 0) {
				echo $error;
				return false;
			}else {
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
					echo "Der Schüler wurde erfolgreich hinzugefügt<br><br><a href=\"index.php?page=output&schueler=1&filter=" . $this->person->id . "\" class=\"links2\">Daten des Schülers ansehen und vermitteln</a>";
					return true;
				}else {
					echo "Der Schüler existiert bereits in dem Schuljahr";
					$this->load_schueler_pid();
					return false;
				}
			}
		}
	}
	function change_schueler(array $params_arr) {
		if (isset($this->person) && is_array($params_arr)) {
			global $GLOBAL_CONFIG;
			// Überprüfe Werte, ob valide
			isset($params_arr['comment']) ?: $params_arr['comment'] = '';
			$params_arr['klassenlehrer_name'] = strip_tags($params_arr['klassenlehrer_name']);
			$params_arr['klasse'] = strip_tags($params_arr['klasse']);
			$params_arr['comment'] = strip_tags($params_arr['comment']);
			
			$params_arr['klassenlehrer_name'] = htmlspecialchars($params_arr['klassenlehrer_name'], ENT_QUOTES, 'UTF-8');
			$params_arr['klasse'] = htmlspecialchars($params_arr['klasse'], ENT_QUOTES, 'UTF-8');
			$params_arr['comment'] = htmlspecialchars($params_arr['comment'], ENT_QUOTES, 'UTF-8');
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
				$return = query_db("SELECT * FROM `schueler` WHERE id = :sid", $this->id);
				$return = $return->fetch();
				if ($return !== false) {
					$this->klasse = $params_arr['klasse'];
					$this->klassenstufe = $params_arr['klassenstufe'];
					$this->klassenlehrer_name = $params_arr['klassenlehrer_name'];
					$this->comment = $params_arr['comment'];
					$return_prep = query_db("UPDATE `schueler` SET `klassenstufe` = :klassenstufe, `klasse` = :klasse, `klassenlehrer_name` = :klassenelehrer_name, `comment` = :comment WHERE id = :id;", $this->klassenstufe, $this->klasse, $this->klassenlehrer_name, $this->comment, $this->id);
					echo "Die Daten des Schülers wurden erfolgreich geändert";
					return true;
				}else {
					echo "Der Schüler existiert noch nicht in dem Schuljahr";
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
		// var_dump($schueler);
		if ($schueler) {
			$this->id = $schueler['id'];
			$this->klasse = $schueler['klasse'];
			$this->klassenlehrer_name = $schueler['klassenlehrer_name'];
			$this->klassenstufe = $schueler['klassenstufe'];
			$this->comment = $schueler['comment'];
			$return = query_db("SELECT * FROM `zeit` WHERE sid = :sid", $this->id);
			$times = $return->fetch();
			$this->zeit = array();
			while ($times) {
				$this->zeit[] = array(
						'id' => $times['id'], 
						'tag' => $times['tag'], 
						'anfang' => $times['anfang'], 
						'ende' => $times['ende']
				);
				$times = $return->fetch();
			}
			$return = query_db("SELECT * FROM `fragt_nach` WHERE sid = :sid", $this->id);
			$fach = $return->fetch();
			$this->faecher = array();
			while ($fach) {
				$this->faecher[] = array(
						'fid' => $fach['fid'], 
						'langfristig' => $fach['langfristig'], 
						'fachlehrer' => $fach['fachlehrer'], 
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
			// var_dump($zeit);
			if (array_search($zeit['tag'], $weekdays) !== false) {
				if (strtotime($zeit['from']) !== false && strtotime($zeit['until']) !== false) {
					$retun = query_db("SELECT * FROM `zeit` WHERE sid = :sid AND tag = :tag", $this->id, $zeit['tag']);
					$time = $retun->fetch();
					if ($time === false) {
						query_db("INSERT INTO `zeit` (`sid`,`tag`,`anfang`,`ende`) VALUES (:id, :tag, :anfang, :ende);", $this->id, $zeit['tag'], $zeit['from'], $zeit['until']);
					}else {
						echo "Dem schueler / Der schuelerin wurde bereits eine Zeit für diesen Tag zugeordnet";
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
	function add_nachfrage_fach($fachid, bool $langfristig, $fachlehrer, $status) {
		$fachid = intval($fachid);
		// echo $fachid;
		if (isset($this->id) && is_bool($langfristig) && is_int($fachid) && array_search($status, self::stati) !== false) {
			$return = query_db("SELECT * FROM `fragt_nach` WHERE sid = :sid AND fid = :fid", $this->id, $fachid);
			if ($return->fetch() !== false) {
				echo "Es existiert bereits ein Angebot für diesen Schüler und für dieses Fach!";
			}else {
				query_db("INSERT INTO `fragt_nach` (`sid`,`fid`,`langfristig`,`fachlehrer`, `status`) VALUES (:sid, :fid, :langfristig, :fachlehrer, :status)", $this->id, $fachid, intval($langfristig), $fachlehrer, $status);
				$this->load_schueler_pid($this->person->id);
			}
		}else {
			// var_dump($this);
			// var_dump($langfristig);
			// var_dump($fachid);
			// var_dump(array_search($status, self::stati));
			echo "Ein Fehler ist aufgetreten";
		}
	}
	function remove_nachfrage_fach(int $fid) {
		$return = query_db("DELETE FROM `fragt_nach` WHERE fid = :fid AND sid = :sid", $fid, $this->id);
		if ($return) {
			return true;
		}else {
			echo "Ein Fach konnte nicht gelöscht werden";
			return false;
		}
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
			if (!$this->remove_nachfrage_fach($this->faecher[$i]['fid'])) {
				$fehler++;
			}
		}
		$return = query_db("DELETE FROM `unterricht` WHERE sid = :sid", $this->id);
		if (!$return) {
			$fehler++;
		}
		$return = query_db("DELETE FROM `schueler` WHERE id = :id", $this->id);
		if (!$return) {
			$fehler++;
		}
		if ($fehler > 0) {
			echo "Es traten beim Löschen $fehler Fehler auf";
		}else{
			echo "Daten erfolgreich gelöscht";
		}
		
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
		// var_dump($this->zeit);
		// echo "Fachid:".$fid."<br>";
		$return = query_db("SELECT * FROM `bietet_an` WHERE fid = :fid", $fid);
		$matching_lehrer = array();
		if ($return) {
			$angebot = $return->fetchAll();
			// durchlaufe die schleife für jeden Lehrer, der Fach anbietet
			for ($i = 0; $i < count($angebot); $i++) {
				// hole Informationen über Lehrer
				$return = query_db("SELECT * FROM `lehrer` WHERE id = :lid and schuljahr = :schuljahr", $angebot[$i]['lid'], get_current_year());
				if ($return) {
					$lehrer = $return->fetch();
					// echo "PID des Lehrers:".$lehrer['pid'];
					if ($lehrer['klassenstufe'] >= $this->klassenstufe) {
						// hole Sprechzeiten des Lehrers
						$return = query_db("SELECT * FROM `zeit` WHERE lid = :lid", $lehrer['id']);
						if ($return) {
							$gesamte_zeit = array();
							$zeit = $return->fetch();
							while ($zeit) {
								$gesamte_zeit[] = array(
										'tag' => $zeit['tag'], 
										'anfang' => $zeit['anfang'], 
										'ende' => $zeit['ende']
								);
								
								$zeit = $return->fetch();
							}
							// teste, ob schüler und Lehrer am gleichen Tag zu gleicher Zeit zeit haben
		// durchlaufe schleife für jeden Tag, an dem Schüler Zeit hat
							foreach ($this->zeit as $schuelerzeit) {
								// echo "<hr>";
		// pro Tag, an dem Lehrer Zeit hat ein Schleifendurchlauf
								foreach ($gesamte_zeit as $lehrerzeit) {
									if ($schuelerzeit['tag'] == $lehrerzeit['tag']) {
										// echo "Tag stimmt<br>";
		// zeiten Vergleichen!
										$untericht_possible = false;
										$schueler_until = new DateTime($schuelerzeit['ende']);
										$lehrer_from = new DateTime($lehrerzeit['anfang']);
										$lehrer_until = new DateTime($lehrerzeit['ende']);
										
										$unterricht_from = new DateTime($schuelerzeit['anfang']);
										// Unterichtsende ist genau eine Stunde später als Anfang
										$unterricht_until = new DateTime($schuelerzeit['anfang']);
										$unterricht_until->add(new DateInterval("PT1H"));
										// $interval = $schueler_from->diff($schueler_until);
										$max_unterricht_until = $unterricht_until->diff($schueler_until);
										$interval_from = $unterricht_from->diff($lehrer_from);
										$interval_until = $unterricht_until->diff($lehrer_until);
										while ($max_unterricht_until->format("%h") != 0 || $max_unterricht_until->format("%i") != 0) {
											// Teste, ob Anfangszeiten übereinstimmen
											/*
											 * wenn Lehrer eher, oder zeigleich zu Unterichtsanfang
											 */
											if (($interval_from->format("%h") > 0 && $interval_from->invert == 1) || ($interval_from->format("%i") > 0 && $interval_from->invert == 1) || ($interval_from->format("%i") == 0 && $interval_from->format("%h") == 0 && $interval_from->invert == 0)) {
												// Wenn Lehrer später als bis zum Unterrichtsende
												if (($interval_until->format("%h") >= 0 && $interval_until->invert == 0) || ($interval_until->format("%i") >= 0 && $interval_until->invert == 0)) {
													$untericht_possible = true;
													break;
													// Oder wenn Lehrer und Unterichtsende zeitgleichh
												}elseif ($interval_until->format("%h") == 0 && $interval_until->format("%i") == 0 && $interval_until->invert == 0) {
													$untericht_possible = true;
													break;
												}
											}
											// Füge zu Unterichtszeiten 5Minuten hinzu
											$unterricht_from->add(new DateInterval("PT5M"));
											$unterricht_until->add(new DateInterval("PT5M"));
											$max_unterricht_until = $unterricht_until->diff($schueler_until);
											$interval_from = $unterricht_from->diff($lehrer_from);
											$interval_until = $unterricht_until->diff($lehrer_until);
										}
										if (!$untericht_possible) {
											if (($interval_from->format("%h") > 0 && $interval_from->invert == 1) || ($interval_from->format("%i") > 0 && $interval_from->invert == 1) || ($interval_from->format("%i") == 0 && $interval_from->format("%h") == 0 && $interval_from->invert == 0)) {
												// echo "Passt";
												if (($interval_until->format("%h") >= 0 && $interval_until->invert == 0) || ($interval_until->format("%i") >= 0 && $interval_until->invert == 0)) {
													$untericht_possible = true;
												}elseif ($interval_until->format("%h") == 0 && $interval_until->format("%i") == 0 && $interval_until->invert == 0) {
													$untericht_possible = true;
												}
											}
										}
										/*
										 * echo "Unterricht von:";
										 * var_dump($unterricht_from);
										 * echo "Unterricht bis:";
										 * var_dump($unterricht_until);
										 * echo "Lehrer:";
										 * var_dump($lehrer_from);
										 * echo "Lehrer bis:";
										 * var_dump($lehrer_until);
										 * $inter_lehrer_schuel_from = $unterricht_from->diff($lehrer_from);
										 * echo "unterricht_from->diff lehrer_from:";
										 * var_dump($inter_lehrer_schuel_from);
										 * echo "unterricht_until->diff lehrer_until:";
										 * var_dump($unterricht_until->diff($lehrer_until));
										 */
										if ($untericht_possible) {
											$lehrer['unterricht'] = array(
													'tag' => $schuelerzeit['tag'], 
													'anfang' => $unterricht_from, 
													'ende' => $unterricht_until
											);
											$matching_lehrer[] = $lehrer;
										}
									}
								}
								/*
								 * Known Issue:
								 * es wird nur der letzte Tag als mögliche Zeit genannt
								 * Lehrer kann anderen Unterricht schon zur möglichen Zeit geben
								 *
								 */
								// var_dump($schuelerzeit);
							}
							// echo "<hr>";
									// var_dump($matching_lehrer);
						}
					}
				}
			}
			if (count($matching_lehrer) == 0) {
				echo "<br>Es wurde leider kein passender Lehrer gefunden<br>";
			}
			echo "<br>Folgende Lehrer kämen in Frage:";
			for ($i = 0; $i < count($matching_lehrer); $i++) {
				$return = query_db("SELECT * FROM `person` WHERE id = :pid", $matching_lehrer[$i]['pid']);
				if ($return) {
					$return = $return->fetch();
					echo "<br><br>" . $return['vname'] . " " . $return['nname'] . ", Klasse: " . format_klassenstufe_kurs($matching_lehrer[$i]['klassenstufe'], $matching_lehrer[$i]['klasse']);
					echo "<br>Der Unterricht würde immer " . get_name_of_tag($matching_lehrer[$i]['unterricht']['tag']);
					echo " von " . $matching_lehrer[$i]['unterricht']['anfang']->format("H:i");
					echo " Uhr bis " . $matching_lehrer[$i]['unterricht']['ende']->format("H:i") . " Uhr stattfinden";
					$ret = query_db("SELECT * FROM `unterricht` WHERE lid = :lid", $matching_lehrer[$i]['id']);
					if ($ret) {
						$unterricht = $ret->fetchAll();
						$anzahl = count($unterricht);
						if ($anzahl > 0) {
							echo "<br>Der Lehrer hat schon $anzahl Nachhilfeschüler";
						}
					}
					echo "<br><br><a href=\"index.php?page=input_paar&control_paar=1&sid=$this->id&lid=" . $matching_lehrer[$i]['id'] . "&fid=$fid&tag=" . $matching_lehrer[$i]['unterricht']['tag'] . "&anfang=" . $matching_lehrer[$i]['unterricht']['anfang']->format("H:i") . "&ende=" . $matching_lehrer[$i]['unterricht']['ende']->format("H:i") . "\" class=\"links\">Vermittlen</a>";
				}
				echo "<br><br><hr>";
			}
		}else {
			echo "Ein Fehler ist aufgetreten";
		}
	}
	function exists_in_current_year() {
		$return = query_db("SELECT * FROM `schueler` WHERE pid = :pid AND schuljahr = :year;", $this->person->id, get_current_year());
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