<?php
if (isset($user) && $user->runscript()) {
	echo "<h1>Backups</h1>";
//	echo "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css\">";
	$tables = array(
			'person', 
			'schueler', 
			'lehrer', 
			'zeit', 
			'bietet_an', 
			'fragt_nach', 
			'unterricht', 
			'finanzuebersicht',
			'raum'
	);
	if (isset($_GET['restore'])) {
		//Gefahrenpotential
		if (file_exists($GLOBAL_CONFIG['backup_dir'] . $_GET['restore'])) {
			$zip = new ZipArchive();
			$zip->open($GLOBAL_CONFIG['backup_dir'] . $_GET['restore']);
			$zip->extractTo($GLOBAL_CONFIG['backup_dir'] . substr($_GET['restore'], 0, -4));
			$zip->close();
			$allfiles = array();
			$filename = $GLOBAL_CONFIG['backup_dir'] . substr($_GET['restore'], 0, -4) . "/backup/" . substr($_GET['restore'], 0, -4) . ".sql";
			$filename2 = $GLOBAL_CONFIG['backup_dir'] . substr($_GET['restore'], 0, -4) . "/docs/backup/" . substr($_GET['restore'], 0, -4) . ".sql";
			if (!file_exists($filename)) {
				if (file_exists($filename2)) {
					$filename = $filename2;
				}else {
					echo "Datei existiert nicht";
					$user->log(user::LEVEL_ERROR, "SQL-Datei existiert nicht!");
					die();
				}
			}
			$allfiles[] = $filename;
			$sqlfile = fopen($filename, "r");
			if (!($content = fread($sqlfile, filesize($filename)))) {
				echo "Lesefehler";
				die();
			}
			global $pdo;
			$pdo->exec("SET foreign_key_checks = 0");
			$user->log(user::LEVEL_WARNING, "Backup wird wiederhergestellt");
			if (!strstr($content, "INSERT INTO `raum`")) {
				$user->log(user::LEVEL_WARNING, 'Tabelle raum wird nicht geändert, da in einzuspielendem Backup darüber keine Daten vorliegen');
				echo 'Tabelle raum wird nicht geändert, da in einzuspielendem Backup darüber keine Daten vorliegen';
				//Entferne Tabelle raum -> aktuelle Daten der Tabelle werden nicht gelöscht
				$tables = array_slice($tables, 0, -1, true);
				if (!$tables) {
					$user->log(user::LEVEL_ERROR, 'Beim Misachten der Tabelle Raum ist ein Fehler aufgetreten. Backup abgebrochen');
					echo "Beim Misachten der Tabelle Raum ist ein Fehler aufgetreten. Backup abgebrochen";
					die();
				}
			}
			var_dump($tables);
			for ($i = 0; $i < count($tables); $i++) {
				$result = query_db("DELETE FROM `" . $tables[$i] . "`;");
			}
			$code = array();
			$code_all = array();
			$code_all = preg_split("~(\);)~", $content, -1);
			foreach ($code_all as $string) {
				if (strpos($string, "VALUE;") === false && strpos($string, "INSERT INTO `faecher`") === false) {
					$code[] = $string;
				}
			}
			var_dump($code);
			$count = 0;
			for ($i = 0; $i < count($code); $i++) {
				if (strlen($code[$i]) > 5) {
					echo "<hr><br>".$code[$i]."<br><br>";
					$result = $pdo->exec($code[$i].");");
					if ($result === false) {
						$user->log(user::LEVEL_ERROR, "DB-FEHLER:" . implode("-", $pdo->errorInfo()));
						echo "<b>Ein Fehler ist beim Wiederherstellen aufgetreten:".$pdo->errorCode().print_r($pdo->errorInfo())."</b><br><br>";
						die();
					}else{
						echo "$result Zeilen wurden erfolgreich geändert.";
						$count++;
					}
				}
			}
			echo "<hr><br><br><br>";
			$pdo->exec("SET foreign_key_checks = 1");
			$result = query_db("SELECT `lehrer_dokument`, `schueler_dokument` FROM `unterricht`");
			if ($result) {
				$return = $result->fetch();
				while ($return) {
					if (strlen($return['lehrer_dokument']) > 2) {
						//do something
						if (!file_exists($GLOBAL_CONFIG['backup_dir'] . substr($_GET['restore'], 0, -4) . "/backup/".$return['lehrer_dokument'])) {
							echo "<br>Ein Fehler beim Wiederherstellen des Dokumentes ".$return['lehrer_dokument']." ist aufgetreten.";
							die();
						}else{
							if(rename($GLOBAL_CONFIG['backup_dir'] . substr($_GET['restore'], 0, -4) . "/backup/".$return['lehrer_dokument'], $GLOBAL_CONFIG['doc_dir'].$return['lehrer_dokument'])) {
								echo "<br>Dokument ". $return['lehrer_dokument'] ." wurde erfolgreich wiederhergestellt";
								$allfiles[] = $GLOBAL_CONFIG['backup_dir'] . substr($_GET['restore'], 0, -4) . "/backup/".$return['lehrer_dokument'];
							}else{
								echo "<br>Ein Fehler ist beim Verschieben des Dokuments aufgetreten";
								die();
							}
						}
					}
					if (strlen($return['schueler_dokument']) > 2) {
						if (!file_exists($GLOBAL_CONFIG['backup_dir'] . substr($_GET['restore'], 0, -4) . "/backup/".$return['schueler_dokument'])) {
							echo "<br>Ein Fehler beim Wiederherstellen des Dokumentes ".$return['schueler_dokument']." ist aufgetreten.";
							die();
						}else{
							if(rename($GLOBAL_CONFIG['backup_dir'] . substr($_GET['restore'], 0, -4) . "/backup/".$return['schueler_dokument'], $GLOBAL_CONFIG['doc_dir'].$return['schueler_dokument'])) {
								echo "<br>Dokument ". $return['schueler_dokument'] ." wurde erfolgreich wiederhergestellt";
								$allfiles[] = $GLOBAL_CONFIG['backup_dir'] . substr($_GET['restore'], 0, -4) . "/backup/".	$return['schueler_dokument'];
							}else{
								echo "<br>Ein Fehler ist beim Verschieben des Dokuments aufgetreten";
								die();
							}
						}
					}
					$return = $result->fetch();
				}
			}
			if (!unlink($filename)) {
				echo "Es ist ein Fehler beim Aufräumen und Löschen nicht benötigter Datein entstanden";
			}
			if (!rmdir($GLOBAL_CONFIG['backup_dir'] . substr($_GET['restore'], 0, -4)."/backup/")) {
				echo "Verzeichnis konnte nicht gelöscht werden";
			}
			if (!rmdir($GLOBAL_CONFIG['backup_dir'] . substr($_GET['restore'], 0, -4))) {
				echo "Verzeichnis konnte nicht gelöscht werden";
			}
			echo "><a href=\"index.php?page=backup_data\" class=\"links2\">Zurück zur Übersicht über die Backups</a>";
		}else {
			echo "Dieses Backup existiert leider nicht!<br><a href=\"index.php?page=backup_data\" class=\"links2\">Zurück zur Übersicht über die Backups</a>";
		}
	}else if (isset($_GET['deleteall'])) {
		$user->log(user::LEVEL_WARNING, "Alle Daten werden gelöscht");
		$pdo->exec("SET foreign_key_checks = 0");
		for ($i = 0; $i < count($tables); $i++) {
			$result = query_db("DELETE FROM `" . $tables[$i] . "`;");
		}
		$pdo->exec("SET foreign_key_checks = 1");
		echo "><a href=\"index.php?page=backup_data\" class=\"links2\">Zurück zur Übersicht über die Backups</a>";
	}else if (isset($_GET['delete'])) {
		if (file_exists($GLOBAL_CONFIG['backup_dir'] . $_GET['delete'])) {
			if (unlink($GLOBAL_CONFIG['backup_dir'] . $_GET['delete'])) {
				echo "Backup wurde erfolgreich gelöscht.<br><a href=\"index.php?page=backup_data\" class=\"links2\">Zurück zur Übersicht über die Backups</a>";
				$user->log(user::LEVEL_NOTICE, $_GET['delete'] . " wurde gelöscht");
			}else {
				echo "Ein Fehler ist beim Löschen aufgetreten<br><a href=\"index.php?page=backup_data\" class=\"links2\">Zurück zur Übersicht über die Backups</a>";
				$user->log(user::LEVEL_ERROR, "Fehler beim Löschen von" . $_GET['delete']);
			}
		}else {
			echo "Dieses Backup existiert leider nicht!<br><a href=\"index.php?page=backup_data\" class=\"links2\">Zurück zur Übersicht über die Backups</a>";
		}
	}else if (isset($_GET['newbackup']) && $_GET['newbackup'] == 1) {
		$user->log(user::LEVEL_WARNING, "Neues Backup wird erstellt");
		$content = '';
		$content = "\n-- Automatisch generiert\n-- erstellt von: " . $user->vname . " " . $user->nname . " (" . $user->getemail() . ")\n-- erstellt am " . date(DATE_RSS, time()) . "\n\n";
		$dokumente = array();
		for ($i = 0; $i < count($tables); $i++) {
			$result = query_db("SHOW COLUMNS FROM " . $tables[$i] . ";");
			if ($result) {
				$columns = $result->fetchAll();
				$columns_real = array();
				$content .= 'INSERT INTO `' . $tables[$i] . '` (';
				// Hole die Namen der Spalten
				for ($j = 0; $j < count($columns); $j++) {
					$content .= "`" . $columns[$j]['Field'] . "`,";
					$columns_real[] = $columns[$j]['Field'];
				}
				$content = substr($content, 0, -1) . ") VALUES\n";
				$result = query_db("SELECT * FROM " . $tables[$i]);
				if ($result) {
					$rows = $result->fetchAll();
					// var_dump($rows);
					for ($j = 0; $j < count($rows); $j++) {
						$content .= "('";
						for ($ij = 0; $ij < count($columns_real); $ij++) {
							if (($columns_real[$ij] == "lehrer_dokument" || $columns_real[$ij] == "schueler_dokument") && strlen($rows[$j][$columns_real[$ij]]) > 0) {
								$dokumente[] = $rows[$j][$columns_real[$ij]];
							}
							if (empty($rows[$j][$columns_real[$ij]])) {
								$content = substr($content, 0, -1);
								$content .= "NULL, '";
								// $content .= $rows[$j][$columns_real[$ij]]."', '";
							}else {
								$content .= $rows[$j][$columns_real[$ij]] . "', '";
							}
						}
						$content = substr($content, 0, -3);
						$content .= "),\n";
					}
					$content = substr($content, 0, -2);
					$content .= ";\n\n";
					// var_dump($content);
				}else {
					die();
				}
			}else {
				die();
			}
			// echo str_replace("\n", "<br>", $content);
			// var_dump($dokumente);
		}
		$filename = date("y-n-d--H-i-s", time());
		$sqlfile = fopen($GLOBAL_CONFIG['backup_dir'] . "Backup-$filename.sql", "x");
		fwrite($sqlfile, $content, strlen($content));
		fclose($sqlfile);
		$zip = new ZipArchive();
		if ($zip->open($GLOBAL_CONFIG['backup_dir'] . "Backup-$filename.zip", ZipArchive::CREATE) !== TRUE) {
			$user->log(user::LEVEL_ERROR, "Zip-Datei konnte nicht erstellt werden!");
			die();
		}
		if ($zip->addFile($GLOBAL_CONFIG['backup_dir'] . "Backup-$filename.sql", "backup/Backup-$filename.sql") !== TRUE) {
			$user->log(user::LEVEL_ERROR, "Backup-SQL konnte nicht zum ZIP hinzugefügt werden!");
			die();
		}
		for ($i = 0; $i < count($dokumente); $i++) {
			if ($zip->addFile("docs/unterricht/" . $dokumente[$i], "backup/" . $dokumente[$i]) !== TRUE) {
				$user->log(user::LEVEL_ERROR, "Backup-Datei" . $dokumente[$i] . " konnte nicht zum ZIP hinzugefügt werden!");
				die();
			}
		}
		$zip->close();
		unlink($GLOBAL_CONFIG['backup_dir'] . "Backup-$filename.sql");
		echo "Backup wurde erfolgreich erstellt";
	}else {
		echo "<a href=\"index.php?page=backup_data&newbackup=1\" class=\"links\">Neues Backup</a><br><br>";
		$backups = array();
		$dir = opendir($GLOBAL_CONFIG['backup_dir']);
		if ($dir === false) {
			$user->log(user::LEVEL_ERROR, "Backup-Directory konnte nicht geöffnet werden");
			die();
		}
		$files = scandir($GLOBAL_CONFIG['backup_dir'], SCANDIR_SORT_DESCENDING);
		for ($i = 0; $i < count($files); $i++) {
			if (strstr($files[$i], "Backup") !== false && strstr($files[$i], ".zip") !== false) {
				$file_ex = (explode("-", $files[$i]));
				$file_ex[7] = substr($file_ex[7], 0, 2);
				$file_ex[] = $files[$i];
				$backups[] = $file_ex;
			}
		}
		array_multisort($backups);
		if (count($backups > 0)) {
			for ($i = 0; $i < count($backups); $i++) {
				echo "<br>" . $backups[$i][0] . ":  " . $backups[$i][3] . "." . (strlen($backups[$i][2]) == 1 ? "0".$backups[$i][2] : $backups[$i][2]) . "." . $backups[$i][1] . " " . $backups[$i][5] . ":" . $backups[$i][6] . ":" . $backups[$i][7] . "Uhr";
				echo "<div class=\"tooltip\" style=\"margin-top: 10px; margin-left: 100px;\"><a href=\"index.php?page=backup_data&delete=" . $backups[$i][8] . "\" class=\"links2 \" onclick=\"return warn('Willst du das Backup wirklich löschen?')\" style=\"font-style: normal; text-decoration: none;\">";
				echo "<img src=\"img/png_delete_24_24.png\" alt=\"Löschen des Backups\"><span class=\"tooltext\">Lösche das Backup</span></a></div>";
				echo "<span style=\"margin-left: 100px;\"><a href=\"index.php?page=backup_data&restore=" . $backups[$i][8] . "\" onclick=\"return warn('Willst du das Backup wirklich wiederherstellen? Alle jetzigen Daten gehen dabei verloren...')\"class=\"links2\">Wiederherstellen</a></span>";
				echo "<span style=\"margin-left: 100px;\"><a href=\"" . $GLOBAL_CONFIG['backup_dir'] . $backups[$i][8] . "\" class=\"links2\"><img src=\"img/png_save_24_24.png\" alt=\"Download\"></a></span>";
				?>
<script type="text/javascript">
function warn(string) {
	if(confirm(string) == true) {
		return true;
	}
	return false;
}
				</script>

<?php
			}
			echo "<br><br><a href=\"index.php?page=backup_data&deleteall=1\" onclick=\"return warn('Willst du alle Daten wirklich löschen? Das solltest du nur machen, wenn du vorher ein Backup angelegt hast.')\" class=\"links2\">Lösche alle Daten aus der Datenbank ohne Wiederherstellen eines Backups</a>";
			echo "<br><br><br><b>Hinweis: Beim Wiederherstellen eines alten Backups kann es zu Fehlern kommen, da es möglicherweise in einer älteren Softwareversion erstellt wurde.</b>";
		}else {
			echo "Es existiert noch kein Backup";
		}
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}