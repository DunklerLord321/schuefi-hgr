<?php
if (isset($user) && $user->runscript()) {
	global $pdo;
	echo "<h2>Einstellungen</h2>";
		if (isset($_GET['addyear']) && $_GET['addyear'] == 1 && isset($_POST['addyear-bt']) && isset($_POST['addyear-rd']) && $_POST['addyear-rd'] == 1) {
			$return = $pdo->query("SHOW TABLES like '%-%'");
			if ($return === false) {
				echo "Ein Fehler ist passiert";
			} else {
				$tables = $return->fetch();
				while ( $tables !== false ) {
					$tablevar = explode("-", $tables[0]);
					$year = intval($tablevar[1]);
					$years[] = $year;
					$tables = $return->fetch();
				}
				$newyear = $year + 101;
				$sql = "
CREATE TABLE `lehrer-" . $newyear . "` (
  `id` int(11) NOT NULL,
  `vname` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `nname` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `telefon` int(11) DEFAULT NULL,
  `klassenstufe` int(11) NOT NULL,
  `klasse` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `klassenlehrer_name` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `geburtstag` date DEFAULT NULL,
  `fach1` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `fach1_lehrer` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `fach1_nachweis` tinyint(1) DEFAULT '0',
  `fach2` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach2_lehrer` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach2_nachweis` tinyint(1) DEFAULT '0',
  `fach3` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach3_lehrer` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach3_nachweis` tinyint(1) DEFAULT '0',
  `status` enum('neu','noetig','ausstehend','nicht_moeglich') COLLATE latin1_general_ci DEFAULT NULL,
  `mo_anfang` time DEFAULT NULL,
  `mo_ende` time DEFAULT NULL,
  `di_anfang` time DEFAULT NULL,
  `di_ende` time DEFAULT NULL,
  `mi_anfang` time DEFAULT NULL,
  `mi_ende` time DEFAULT NULL,
  `do_anfang` time DEFAULT NULL,
  `do_ende` time DEFAULT NULL,
  `fr_anfang` time DEFAULT NULL,
  `fr_ende` time DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hinzugefuegt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` varchar(500) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
		
CREATE TABLE `paare-" . $newyear . "` (
  `id` int(11) NOT NULL,
  `id_lehrer` int(11) NOT NULL,
  `id_schueler` int(11) NOT NULL,
  `erstellungs_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `treff_zeit` time DEFAULT NULL,
  `treff_raum` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
		
CREATE TABLE `schueler-" . $newyear . "` (
  `id` int(11) NOT NULL,
  `vname` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `nname` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `telefon` int(11) DEFAULT NULL,
  `klassenstufe` int(11) NOT NULL,
  `klasse` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `klassenlehrer_name` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `geburtstag` date DEFAULT NULL,
  `fach1` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `fach1_lehrer` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `fach1_themenbezogen` tinyint(1) DEFAULT '0',
  `fach2` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach2_lehrer` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach2_themenbezogen` tinyint(1) DEFAULT '0',
  `fach3` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach3_lehrer` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `fach3_themenbezogen` tinyint(1) DEFAULT '0',
  `status` enum('neu','noetig','ausstehend','nicht_moeglich','lehrer_gefunden') COLLATE latin1_general_ci DEFAULT NULL,
  `mo_anfang` time DEFAULT NULL,
  `mo_ende` time DEFAULT NULL,
  `di_anfang` time DEFAULT NULL,
  `di_ende` time DEFAULT NULL,
  `mi_anfang` time DEFAULT NULL,
  `mi_ende` time DEFAULT NULL,
  `do_anfang` time DEFAULT NULL,
  `do_ende` time DEFAULT NULL,
  `fr_anfang` time DEFAULT NULL,
  `fr_ende` time DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hinzugefuegt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` varchar(500) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
		
ALTER TABLE `lehrer-" . $newyear . "`
  ADD PRIMARY KEY (`id`);
		
ALTER TABLE `paare-" . $newyear . "`
  ADD PRIMARY KEY (`id`);
		
ALTER TABLE `schueler-" . $newyear . "`
  ADD PRIMARY KEY (`id`);
		
ALTER TABLE `lehrer-" . $newyear . "`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		
ALTER TABLE `paare-" . $newyear . "`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		
ALTER TABLE `schueler-" . $newyear . "`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		
ALTER TABLE `paare-" . $newyear . "`
  ADD FOREIGN KEY (`id_lehrer`) REFERENCES `lehrer-" . $newyear . "` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD FOREIGN KEY (`id_schueler`) REFERENCES `schueler-" . $newyear . "` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT;";
				$return_query = $pdo->query($sql);
				if ($return_query != false) {
					var_dump($pdo);
					$years = get_prop("all_years");
					var_dump($years);
					$years[1] = $years[1] . "_" . $newyear;
					var_dump($years);
					if (set_prop("all_years", $years[1]) == false)
						echo "Fehler";
					echo "<br>Neues Schuljahr erfolgreich hinzugefügt<br>";
				}
			}
		}
		if (isset($_GET['delyear']) && $_GET['delyear'] == 1 && isset($_POST['delyear']) && is_numeric($_POST['delyear'])) {
			// echo $_POST['delyear'];
			$delyear = strip_tags($_POST['delyear']);
			$return = $pdo->query("SHOW TABLES like '%" . $_POST['delyear'] . "'");
			if ($return === false) {
				echo "<br>Ein Fehler ist passiert";
			} else {
				$i = 0;
				$tables = $return->fetch();
				while ( $tables !== false ) {
					$tablevar = explode("-", $tables[0]);
					$year = intval($tablevar[1]);
					$i++;
					// echo $i;
					$tables = $return->fetch();
				}
				if ($i != 3) {
					echo "<br><b>Ein Problem trat auf</b><br>";
				} else {
					$lehrertable = "lehrer-" . $_POST['delyear'];
					$schuelertable = "schueler-" . $_POST['delyear'];
					$paartable = "paare-" . $_POST['delyear'];
					$pdo->query("SET foreign_key_checks = 0");
					$sql = "DROP TABLES `" . $lehrertable . "`, `" . $schuelertable . "`, `" . $paartable . "`;";
					// echo $sql;
					$return = $pdo->query($sql);
					if ($return === false) {
						echo "<br>Es ist ein Problem aufgetreten.";
					} else {
						$years = get_prop("all_years");
						$ex_years = explode("_", $years[1]);
						array_splice($ex_years, -1);
						$years = implode("_", $ex_years);
						if (set_prop("all_years", $years) == false)
							echo "<br><b>Fehler</b><br>";
						echo "<br>Schuljahr wurde erfolgreich gelöscht.<br>";
						$pdo->query("SET foreign_key_checks = 1");
					}
				}
			}
		}
		if (isset($_GET['set_current']) && isset($_POST['current_year'])) {
			$allyears = get_prop("all_years");
			$allyears = explode("_", $allyears[1]);
			if (array_search($_POST['current_year'], $allyears) !== FALSE) {
				set_prop("current_year", $_POST['current_year']);
			}
		}
		// damit bloß root Schuljahre löschen und hinzufügen kann, aber auch normal current year setzen kann
		if ($user->isuserallowed('v')) {
			$return = $pdo->query("SHOW TABLES like '%-%'");
			// print_r($return);
			// var_dump($return);
			$years = array();
			if ($return === false) {
				echo "Ein Fehler ist passiert";
			} else {
				$tables = $return->fetch();
				echo "<br>Tabellen für die Schuljahre:";
				$i = 0;
				?>
<form action="index.php?page=settings&delyear=1" method="post"><?php
				while ( $tables !== false ) {
					$tablevar = explode("-", $tables[0]);
					$year = intval($tablevar[1]);
					if (!in_array($year, $years)) {
						$i++;
						echo "<input type=\"radio\" value=\"$year\" name=\"delyear\">$year<br><br>";
					}
					$years[] = $year;
					$tables = $return->fetch();
				}
				if ($i != 0) {
					echo "<input type=\"submit\" value=\"Lösche Schuljahr\">";
				}
				?>
		</form>
<form action="index.php?page=settings&addyear=1" method="post">
	Neues Schuljahr hinzufügen?
	<input type="radio" name="addyear-rd" value="1">
	Ja
	<input type="radio" name="addyear-rd" checked value="0">
	Nein
	<br>
	<input type="submit" value="Füge neues Schuljahr hinzu" name="addyear-bt">
</form>
<br>
<?php
			}
		}
	} elseif ($user->isuserallowed('w')) {
		echo "Sie sind leider nicht berechtigt, Einstellungen vorzunehmen!";
	}
	if ($user->isuserallowed('fk')) {
		$year = get_prop("current_year");
		$allyears = get_prop("all_years");
		$allyears = explode("_", $allyears[1]);
		echo "Aktuelles Schuljahr:<br>$year[1]";
		var_dump($allyears);
		?>
<form action="index.php?page=settings&set_current=1" method="post">
	<select name="current_year"><?php
		for($i = 0; $i < count($allyears); $i++) {
			if (strcmp($year[1], $allyears[$i]) == 0)
				echo "<option value=\"$allyears[$i]\" selected>$allyears[$i] - momentan aktuelles Schuljahr</option>";
			else
				echo "<option value=\"$allyears[$i]\">$allyears[$i]</option>";
		}
		?></select>
	<br>
	<br>
	<input type="submit" value="Ändere" style="float: right; margin-right: 1%;">
	<br>
	<br>
</form>

<?php
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
