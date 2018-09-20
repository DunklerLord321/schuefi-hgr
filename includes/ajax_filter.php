<?php
session_name("hgr-schuelerfirma");
session_start();
require 'global_vars.inc.php';
require 'class_user.php';
try {
	$pdo = new PDO('mysql:host=' . $GLOBAL_CONFIG['host'] . ';dbname=' . $GLOBAL_CONFIG['dbname'], $GLOBAL_CONFIG['dbuser'], $GLOBAL_CONFIG['dbuser_passwd'], array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
	));
}catch (PDOException $e) {
	echo "<h1>Ein DB-Fehler ist aufgetreten (01)$e<h1>";
	die();
}
if (isset($_SESSION['user']) && strlen($_SESSION['user']) > 0) {
	$user = unserialize($_SESSION['user']);
}else{
	echo "Kein korrekter Nutzer gefunden";
	die();
}
if(!$user->is_valid()) {
	echo "Sie sind nicht angemeldet";
	die();
}

$ajax_return = "";
$query = array();
$query[] = array();

if (isset($_GET['filter'])) {
	if (isset($_GET['value'])) {
		require 'functions.inc.php';
		if (isset($_GET['compare'])) {
			$compare = str_replace("nq", "!=", $_GET['compare']);
			$compare = str_replace("eq", "=", $compare);
			$compare = str_replace("lt", "<", $compare);
			$compare = str_replace("gt", ">", $compare);
		}
		if (isset($_GET['compare2'])) {
			$compare2 = str_replace("nq", "!=", $_GET['compare2']);
			$compare2 = str_replace("eq", "=", $compare2);
			$compare2 = str_replace("lt", "<", $compare2);
			$compare2 = str_replace("gt", ">", $compare2);
		}
		if ($_GET['filter'] == "tagfach" && isset($compare2) && isset($_GET['value2'])) {
//SELECT * FROM `lehrer` LEFT JOIN zeit ON zeit.lid = lehrer.id LEFT JOIN person ON person.id = lehrer.pid LEFT JOIN bietet_an ON bietet_an.lid = lehrer.id HAVING person.aktiv = 1 AND bietet_an.fid = 1 AND zeit.tag = 'mo' 			
			$query[0]['query'];
			$query[0]['params'] = array();
			$query[0]['columns'] = array();
			$query[0]['headline'] = "Lehrer:";
			$query[0]['columns_header'] = array();
			$query[0]['query'] = "SELECT * FROM `lehrer` LEFT JOIN zeit ON zeit.lid = lehrer.id LEFT JOIN person ON person.id = lehrer.pid LEFT JOIN bietet_an ON bietet_an.lid = lehrer.id HAVING person.aktiv = 1 AND bietet_an.fid $compare :value AND zeit.tag $compare2 :value2";
			array_push($query[0]['params'], $_GET['value'], $_GET['value2']);
			array_push($query[0]['columns'], 'vname', 'nname', 'email', 'fid', 'notenschnitt', 'klassenstufe', 'klasse', 'tag', 'anfang', 'ende');
			array_push($query[0]['columns_header'], 'Vorname', 'Nachname', 'E-Mail', 'Fach', 'Notenschnitt', 'Klassenstufe', 'Klasse', 'Tag', 'Anfang', 'Ende');
			$query[] = array();
			$query[1]['query'];
			$query[1]['params'] = array();
			$query[1]['columns'] = array();
			$query[1]['columns_header'] = array();
			$query[1]['headline'] = "Schüler:";
			$query[1]['query'] = "SELECT * FROM `schueler` LEFT JOIN zeit ON zeit.lid = schueler.id LEFT JOIN person ON person.id = schueler.pid LEFT JOIN fragt_nach ON fragt_nach.sid = schueler.id HAVING person.aktiv = 1 AND fragt_nach.fid $compare :value AND zeit.tag $compare2 :value2"; 			
			array_push($query[1]['params'], $_GET['value'], $_GET['value2']);
			array_push($query[1]['columns'], 'vname', 'nname', 'email', 'fid', 'notenschnitt', 'klassenstufe', 'klasse', 'tag', 'anfang', 'ende');
			array_push($query[1]['columns_header'], 'Vorname', 'Nachname', 'E-Mail', 'Fach', 'Notenschnitt', 'Klassenstufe', 'Klasse', 'Tag', 'Anfang', 'Ende');
			//			$ajax_return .= "<br><b>Achtung: die Vergleichsoperatoren < und > sind bei Fach und Wochentag nicht sinnvoll!</b><br><br>";
		}else if ($_GET['filter'] == "hinzugefuegt") {
			$value = date('Y-m-d', strtotime($_GET['value']));
			$query[0]['query'];
			$query[0]['params'] = array();
			$query[0]['columns'] = array();
			$query[0]['headline'] = "Lehrer und Schüler:";
			$query[0]['columns_header'] = array();
			$query[0]['query'] = "SELECT * FROM `person` WHERE aktiv = 1 AND hinzugefuegt $compare :value ORDER BY `hinzugefuegt` ASC ";
			array_push($query[0]['params'], $value);
			array_push($query[0]['columns'], 'vname', 'nname', 'email', 'hinzugefuegt');
			array_push($query[0]['columns_header'], 'Vorname', 'Nachname', 'E-Mail', 'hinzugefügt');
			$query[] = array();
			$query[1]['query'];
			$query[1]['params'] = array();
			$query[1]['columns'] = array();
			$query[1]['columns_header'] = array();
			$query[1]['headline'] = "Schülerfirmamitarbeiter:";
			$query[1]['query'] = "SELECT * FROM `users` WHERE `createt_time` $compare :value ORDER BY `createt_time` ASC ";
			array_push($query[1]['params'], $value);
			array_push($query[1]['columns'], 'vname', 'nname', 'email', 'createt_time');
			array_push($query[1]['columns_header'], 'Vorname', 'Nachname', 'E-Mail', 'hinzugefügt');
		}else if ($_GET['filter'] == "fach") {
			$query[0]['query'];
			$query[0]['params'] = array();
			$query[0]['columns'] = array();
			$query[0]['headline'] = "Lehrer:";
			$query[0]['columns_header'] = array();
			$query[0]['query'] = "SELECT lehrer.*, bietet_an.*, person.vname, person.nname, person.email, u.zahl_schueler FROM `lehrer` LEFT JOIN `person` ON `lehrer`.`pid` = `person`.`id` LEFT JOIN `bietet_an` ON `bietet_an`.`lid` = `lehrer`.`id`
				LEFT JOIN ( SELECT `unterricht`.`lid`, COUNT(`unterricht`.`lid`) AS zahl_schueler, unterricht.fid FROM unterricht GROUP BY unterricht.lid, unterricht.fid HAVING unterricht.fid $compare :value )
				AS u ON u.lid = lehrer.id  WHERE `bietet_an`.`fid` $compare :value ORDER BY bietet_an.fid ASC";
			array_push($query[0]['params'], $_GET['value'], $_GET['value']);
			array_push($query[0]['columns'], 'vname', 'nname', 'email', 'fid', 'notenschnitt', 'klassenstufe', 'klasse', 'status', 'zahl_schueler');
			array_push($query[0]['columns_header'], 'Name', 'Nachname', 'E-Mail', 'Fach', 'Notenschnitt', 'Klass	enstufe', 'Klasse', 'Vermittlungs-<br>status', 'Schüler-<br>anzahl');
			$query[] = array();
			$query[1]['query'];
			$query[1]['params'] = array();
			$query[1]['columns'] = array();
			$query[1]['columns_header'] = array();
			$query[1]['headline'] = "Schüler:";
			$query[1]['query'] = "SELECT * FROM `schueler` LEFT JOIN `person` ON `schueler`.`pid` = `person`.`id` LEFT JOIN `fragt_nach` ON `fragt_nach`.`sid` = `schueler`.`id` WHERE `fragt_nach`.`fid` $compare :value ORDER BY fragt_nach.fid ASC";
			array_push($query[1]['params'], $_GET['value']);
			array_push($query[1]['columns'], 'vname', 'nname', 'email', 'fid', 'notenschnitt', 'klassenstufe', 'klasse', 'status');
			array_push($query[1]['columns_header'], 'Name', 'Nachname', 'E-Mail', 'Fach', 'Notenschnitt', 'Klassenstufe', 'Klasse', 'Vermittlungsstatus');
		}else if ($_GET['filter'] == "zahlschueler") {
			$query[0]['query'];
			$query[0]['params'] = array();
			$query[0]['columns'] = array();
			$query[0]['columns_header'] = array();
			$query[0]['query'] = "SELECT * FROM `lehrer` LEFT JOIN (SELECT unterricht.lid, COUNT(unterricht.lid) AS zahl_schueler FROM unterricht GROUP BY unterricht.lid) AS u ON u.lid = lehrer.id LEFT JOIN person ON person.id = lehrer.pid HAVING person.aktiv = 1 AND u.zahl_schueler $compare :value";
			array_push($query[0]['params'], $_GET['value']);
			array_push($query[0]['columns'], 'vname', 'nname', 'email', 'zahl_schueler');
			array_push($query[0]['columns_header'], 'Name', 'Nachname', 'E-Mail', 'Anzahl der Schüler');
		}else if ($_GET['filter'] == "durchschnitt") {
			$query[0]['query'];
			$query[0]['params'] = array();
			$query[0]['columns'] = array();
			$query[0]['columns_header'] = array();
			$query[0]['query'] = "SELECT * FROM `bietet_an` INNER JOIN `lehrer` ON `lehrer`.`id` = `bietet_an`.`lid` INNER JOIN `person` ON `person`.`id` = `lehrer`.`pid` WHERE CAST(`bietet_an`.`notenschnitt` as SIGNED) $compare :value ";
			array_push($query[0]['params'], $_GET['value']);
			array_push($query[0]['columns'], 'vname', 'nname', 'email', 'fid', 'notenschnitt');
			array_push($query[0]['columns_header'], 'Vorname', 'Nachname', 'E-Mail', 'Fach', 'Notenschnitt');
		}else if ($_GET['filter'] == "klassenstufe") {
			$query[0]['query'];
			$query[0]['params'] = array();
			$query[0]['columns'] = array();
			$query[0]['headline'] = "Klassenstufe der Lehrer";
			$query[0]['columns_header'] = array();
			$query[0]['query'] = "SELECT * FROM `lehrer` LEFT JOIN `person` ON `lehrer`.`pid` = `person`.`id` WHERE `lehrer`.`klassenstufe` $compare :value";
			array_push($query[0]['params'], $_GET['value']);
			array_push($query[0]['columns'], 'vname', 'nname', 'email', 'fid', 'notenschnitt', 'klassenstufe', 'klasse');
			array_push($query[0]['columns_header'], 'Vorname', 'Nachname', 'E-Mail', 'Fach', 'Notenschnitt', 'Klassenstufe', 'Klasse');
			$query[] = array();
			$query[1]['query'];
			$query[1]['params'] = array();
			$query[1]['columns'] = array();
			$query[1]['columns_header'] = array();
			$query[1]['headline'] = "Klassenstufe der Schüler";
			$query[1]['query'] = "SELECT * FROM `schueler` LEFT JOIN `person` ON `schueler`.`pid` = `person`.`id` WHERE `schueler`.`klassenstufe` $compare :value";
			array_push($query[1]['params'], $_GET['value']);
			array_push($query[1]['columns'], 'vname', 'nname', 'email', 'fid', 'notenschnitt', 'klassenstufe', 'klasse');
			array_push($query[1]['columns_header'], 'Vorame', 'Nachname', 'E-Mail', 'Fach', 'Notenschnitt', 'Klassenstufe', 'Klasse');
		}else if ($_GET['filter'] == "geldeinausgabe") {
			/* SELECT f.geldbetrag, f.konto_bar, f.betreff, f.bemerkung, f.dokument, f.datum, f.uid, f.pid, fzwei.summe, fdrei.summeuid, p.vname, p.nname, u.vname, u.nname 
			 * FROM `finanzuebersicht` AS f 
			 * LEFT JOIN ( SELECT finanzuebersicht.pid, SUM(finanzuebersicht.geldbetrag) AS summe FROM finanzuebersicht GROUP BY finanzuebersicht.pid ) AS fzwei ON fzwei.pid = f.pid
			 * LEFT JOIN ( SELECT finanzuebersicht.uid, SUM(finanzuebersicht.geldbetrag) AS summeuid FROM finanzuebersicht GROUP BY finanzuebersicht.uid ) AS fdrei ON fdrei.uid = f.uid 
			 * LEFT JOIN person p ON f.pid = p.id LEFT JOIN users u ON u.id = f.uid WHERE fzwei.summe > 20 
			*/
			$query[0]['query'];
			$query[0]['params'] = array();
			$query[0]['columns'] = array();
			$query[0]['headline'] = "Ein- oder Auszahlungen von Lehrern und Schülern";
			$query[0]['columns_header'] = array();
			$query[0]['query'] = "SELECT f.geldbetrag, f.konto_bar, f.betreff, f.bemerkung, f.dokument, f.datum, f.uid, f.pid, fzwei.summe, p.vname, p.nname
			FROM finanzuebersicht AS f
			LEFT JOIN ( SELECT finanzuebersicht.pid, SUM(finanzuebersicht.geldbetrag) AS summe FROM finanzuebersicht GROUP BY finanzuebersicht.pid ) AS fzwei ON fzwei.pid = f.pid
			LEFT JOIN person p ON f.pid = p.id WHERE fzwei.summe $compare :value ORDER BY p.nname ASC";
			array_push($query[0]['params'], $_GET['value']);
			array_push($query[0]['columns'], 'vname', 'nname', 'geldbetrag', 'konto_bar', 'bemerkung', 'dokument', 'datum', 'summe');
			array_push($query[0]['columns_header'], 'Name', 'Nachname', 'Geldbetrag', 'Konto/Bar', 'Bemerkung', 'Dokument', 'Datum', 'Summe');
			$query[] = array();
			$query[1]['query'];
			$query[1]['params'] = array();
			$query[1]['columns'] = array();
			$query[1]['columns_header'] = array();
			$query[1]['headline'] = "Auszahlungen von Schülerfirmamitarbeitern";
			$query[1]['query'] = "SELECT f.geldbetrag, f.konto_bar, f.betreff, f.bemerkung, f.dokument, f.datum, f.uid, f.pid, fzwei.summe, u.vname, u.nname
			FROM finanzuebersicht AS f
			LEFT JOIN ( SELECT finanzuebersicht.uid, SUM(finanzuebersicht.geldbetrag) AS summe FROM finanzuebersicht GROUP BY finanzuebersicht.uid ) AS fzwei ON fzwei.uid = f.uid
			LEFT JOIN users u ON u.id = f.uid WHERE fzwei.summe $compare :value ORDER BY u.nname ASC";
			array_push($query[1]['params'], $_GET['value']);
			array_push($query[1]['columns'], 'vname', 'nname', 'geldbetrag', 'konto_bar', 'bemerkung', 'dokument', 'datum', 'summe');
			array_push($query[1]['columns_header'], 'Name', 'Nachname', 'Geldbetrag', 'Konto/Bar', 'Bemerkung', 'Dokument', 'Datum', 'Summe');
		}else if ($_GET['filter'] == "geldausgabe") {
			$query[0]['query'];
			$query[0]['params'] = array();
			$query[0]['columns'] = array();
			$query[0]['headline'] = "Auszahlungen von Lehrern (und Schülern)";
			$query[0]['columns_header'] = array();
			$query[0]['query'] = "SELECT f.geldbetrag, f.konto_bar, f.betreff, f.bemerkung, f.dokument, f.datum, f.uid, f.pid, fzwei.summe, p.vname, p.nname
			FROM finanzuebersicht AS f
			LEFT JOIN ( SELECT finanzuebersicht.pid, finanzuebersicht.geldbetrag, SUM(finanzuebersicht.geldbetrag) AS summe FROM finanzuebersicht GROUP BY finanzuebersicht.pid, finanzuebersicht.geldbetrag
			HAVING finanzuebersicht.geldbetrag < 0) AS fzwei ON fzwei.pid = f.pid
			LEFT JOIN person p ON f.pid = p.id WHERE fzwei.summe $compare :value ORDER BY p.nname ASC";
			array_push($query[0]['params'], $_GET['value']);
			array_push($query[0]['columns'], 'vname', 'nname', 'geldbetrag', 'geldbetrag2', 'konto_bar', 'bemerkung', 'dokument', 'datum', 'summe');
			array_push($query[0]['columns_header'], 'Name', 'Nachname', 'Einnahmen', 'Ausgaben', 'Konto/Bar', 'Bemerkung', 'Dokument', 'Datum', 'Summe');
			$query[] = array();
			$query[1]['query'];
			$query[1]['params'] = array();
			$query[1]['columns'] = array();
			$query[1]['columns_header'] = array();
			$query[1]['headline'] = "Auszahlungen von Schülerfirmamitarbeitern";
			$query[1]['query'] = "SELECT f.geldbetrag, f.konto_bar, f.betreff, f.bemerkung, f.dokument, f.datum, f.uid, f.pid, fzwei.summe, u.vname, u.nname
			FROM finanzuebersicht AS f
			LEFT JOIN ( SELECT finanzuebersicht.uid, finanzuebersicht.geldbetrag, SUM(finanzuebersicht.geldbetrag) AS summe FROM finanzuebersicht GROUP BY finanzuebersicht.uid, finanzuebersicht.geldbetrag
			HAVING finanzuebersicht.geldbetrag < 0) AS fzwei ON fzwei.uid = f.uid
			LEFT JOIN users u ON u.id = f.uid WHERE fzwei.summe $compare :value ORDER BY u.nname ASC";
			array_push($query[1]['params'], $_GET['value']);
			array_push($query[1]['columns'], 'vname', 'nname', 'geldbetrag', 'geldbetrag2', 'konto_bar', 'bemerkung', 'dokument', 'datum', 'summe');
			array_push($query[1]['columns_header'], 'Name', 'Nachname', 'Einnahmen', 'Ausgaben', 'Konto/Bar', 'Bemerkung', 'Dokument', 'Datum', 'Summe');
		}else if ($_GET['filter'] == "geldeingabe") {
			$ajax_return .= "Diese Abfrage funktioniert noch nicht für Schüler und Lehrer!!!!";
			$query[0]['query'];
			$query[0]['params'] = array();
			$query[0]['columns'] = array();
			$query[0]['headline'] = "Einzahlungen von Schülern (und Lehrern)";
			$query[0]['columns_header'] = array();
			$query[0]['query'] = "SELECT f.geldbetrag, f.konto_bar, f.betreff, f.bemerkung, f.dokument, f.datum, f.uid, f.pid, fzwei.summe, p.vname, p.nname
				FROM finanzuebersicht AS f
				LEFT JOIN ( SELECT finanzuebersicht.pid, finanzuebersicht.geldbetrag, SUM(finanzuebersicht.geldbetrag) AS summe FROM finanzuebersicht GROUP BY finanzuebersicht.pid, finanzuebersicht.geldbetrag
				HAVING finanzuebersicht.geldbetrag > 0) AS fzwei ON fzwei.pid = f.pid
				LEFT JOIN person p ON f.pid = p.id WHERE fzwei.summe $compare :value ORDER BY p.nname ASC";
			array_push($query[0]['params'], $_GET['value']);
			array_push($query[0]['columns'], 'vname', 'nname', 'geldbetrag', 'geldbetrag2', 'konto_bar', 'bemerkung', 'dokument', 'datum', 'summe');
			array_push($query[0]['columns_header'], 'Name', 'Nachname', 'Einnahmen', 'Ausgaben', 'Konto/Bar', 'Bemerkung', 'Dokument', 'Datum', 'Summe');
			$query[] = array();
			$query[1]['query'];
			$query[1]['params'] = array();
			$query[1]['columns'] = array();
			$query[1]['columns_header'] = array();
			$query[1]['headline'] = "Auszahlungen von Schülerfirmamitarbeitern";
			$query[1]['query'] = "SELECT f.geldbetrag, f.konto_bar, f.betreff, f.bemerkung, f.dokument, f.datum, f.uid, f.pid, fzwei.summe, u.vname, u.nname
				FROM finanzuebersicht AS f
				LEFT JOIN ( SELECT finanzuebersicht.uid, finanzuebersicht.geldbetrag, SUM(finanzuebersicht.geldbetrag) AS summe FROM finanzuebersicht GROUP BY finanzuebersicht.uid, finanzuebersicht.geldbetrag
				HAVING finanzuebersicht.geldbetrag > 0) AS fzwei ON fzwei.uid = f.uid
				LEFT JOIN users u ON u.id = f.uid WHERE fzwei.summe $compare :value ORDER BY u.nname ASC";
			array_push($query[1]['params'], $_GET['value']);
			array_push($query[1]['columns'], 'vname', 'nname', 'geldbetrag', 'geldbetrag2', 'konto_bar', 'bemerkung', 'dokument', 'datum', 'summe');
			array_push($query[1]['columns_header'], 'Name', 'Nachname', 'Einnahmen', 'Ausgaben', 'Konto/Bar', 'Bemerkung', 'Dokument', 'Datum', 'Summe');
			}else{
			echo "Es wurde kein korrekter Filter ausgewählt";
			die();
		}
		for ($j = 0; $j < count($query); $j++) {
			if(isset($query[$j]['headline'])) {
				$ajax_return .= "<b>" . $query[$j]['headline'] . "</b>";
			}
			if(count($query[$j]['params']) == 2) {
				$result = query_db($query[$j]['query'], $query[$j]['params'][0], $query[$j]['params'][1]);
			}else if(count($query[$j]['params']) == 1) {
				$result = query_db($query[$j]['query'], $query[$j]['params'][0]);
			}else{
				echo "Unbekannte Anzahl an Parametern";
			}
			if ($result !== false) {
				$return = $result->fetch();
				if (count($query[$j]['columns']) != count($query[$j]['columns_header'])) {
					echo "Ein Fehler mit den Spalten und Spaltenüberschriften ist aufgetreten";
					die();
				}
				if (count($query) > 1 && $return === false) {
					$ajax_return .= "<br>Diese Suche erzielte kein Ergebnis<br><br>";
					continue;
				}
				else if ($return === false) {
					echo "Diese Suche erzielte kein Ergebnis!";
					die();
				}
				$ajax_return .= "<table class=\"table1\"><tr>";
				for ($i = 0; $i < count($query[$j]['columns']); $i++) {
					//Eintrag für virtuelle Spalten notwendig, die nur der Formatierung der Ausgaben dienen
					if ((array_key_exists($query[$j]['columns'][$i], $return) && $query[$j]['columns'][$i] != "nname") || $query[$j]['columns'][$i] == "geldbetrag2" ) {
						if ($query[$j]['columns'][$i] == 'vname') {
							$ajax_return .= "<th style=\"text-align: left;\">" . $query[$j]['columns_header'][$i] . "</th>";
						}else{
							$ajax_return .= "<th>" . $query[$j]['columns_header'][$i] . "</th>";
						}
					}
				}
				$ajax_return .= "</tr>";
				while ($return) {
					$ajax_return .= "<tr>";
					for ($i = 0; $i < count($query[$j]['columns']); $i++) {
						if (array_key_exists($query[$j]['columns'][$i], $return)) {
							if ($query[$j]['columns'][$i] == "vname") {
								$ajax_return .= "<td style=\"text-align: left;\"><a href=\"index.php?page=output_person&filter=" . $return['pid'] . "\" class=\"links2\">" . $return[$query[$j]['columns'][$i]] . " " . $return[$query[$j]['columns'][$i+1]] . "</a></td>";
							}else if ($query[$j]['columns'][$i] == "fid") {
								$ajax_return .= "<td>" . get_name_of_subject($return[$query[$j]['columns'][$i]]) . "</td>";
							}else if ($query[$j]['columns'][$i] == "geldbetrag" && $query[$j]['columns'][($i+1)] == "geldbetrag2"){
								if($return[$query[$j]['columns'][$i]] > 0) {
									$ajax_return .= "<td>" . $return[$query[$j]['columns'][$i]] ."</td><td></td>";
								}else{
									$ajax_return .= "<td></td><td>" . abs($return[$query[$j]['columns'][$i]]) ."</td>";
								}
							}else if ($query[$j]['columns'][$i] == "hinzugefuegt" || $query[$j]['columns'][$i] == "createt_time") {
								$ajax_return .= "<td>" . date('d.m.Y, H:i', strtotime($return[$query[$j]['columns'][$i]])) . "</td>";
							}else if ($query[$j]['columns'][$i] == "anfang" || $query[$j]['columns'][$i] == "ende") {
								$ajax_return .= "<td>" . date('H:i',strtotime($return[$query[$j]['columns'][$i]])) . "</td>";
							}else if ($query[$j]['columns'][$i] != "nname") {
								$ajax_return .= "<td>" . $return[$query[$j]['columns'][$i]] . "</td>";
							}
						}
					}
					$ajax_return .= "</tr>";
					$return = $result->fetch();
				}
				$ajax_return .= "</table><br><br>";
			}
		}
		echo $ajax_return;
	}else {
		echo "Es wurde kein Wert für den Filter ausgewählt";
	}
}else {
	echo "Es wurde kein Filter ausgewählt";
}
?>
