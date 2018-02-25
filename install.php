<?php
if ((isset($_GET['user']) && isset($_GET['passwd']) && isset($_GET['host'])) || isset($_GET['art'])) {
	echo "<h1>Installieren der Datenbankstruktur</h1>";
	require 'includes/global_vars.inc.php';
	require 'includes/db_data.php';
	try {
		if (isset($_GET['art'])) {
			$pdo = new PDO('mysql:host=' . $GLOBAL_CONFIG['host'] . ';dbname=' . $GLOBAL_CONFIG['dbname'], $GLOBAL_CONFIG['dbuser'], $GLOBAL_CONFIG['dbuser_passwd'], array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
		}else if (isset($_GET['dbname'])){
			$pdo = new PDO('mysql:dbname='.$_GET['dbname'].';host='.$_GET['host'].'', $_GET['user'], $_GET['passwd'], array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
		}else{
			$pdo = new PDO('mysql:host='.$_GET['host'].'', $_GET['user'], $_GET['passwd'], array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
		}
	}catch (PDOException $e) {
		echo "FEHLER " . $e;
		die();
	}
	$eret = $pdo->query("SET foreign_key_checks=0;");
	echo "<br><br><br>";
	$file = fopen("sql/schuefi_complete.sql", "r");
	if ($file === false) {
		echo "Datei konnte nicht geöffnet werden";
		die();
	}
	$content = fread($file, filesize("sql/schuefi_complete.sql"));
	if (isset($_GET['dbname']) && $_GET['dbname'] != "schuefi") {
		$content = str_replace("schuefi", $_GET['dbname'], $content);
	}else if( isset($GLOBAL_CONFIG['dbname']) && $GLOBAL_CONFIG['dbname'] != "schuefi") {
		$content = str_replace("schuefi", $GLOBAL_CONFIG['dbname'], $content);
	}
	$code = preg_split("~(;)~", $content, -1);
	$amount = count($code);
	echo "$amount Abfragen werden ausgeführt. Die kann eine Weile dauern...";
	flush();
	for ($i = 0; $i < count($code); $i++) {
		if (strlen($code[$i]) > 5) {
			$ret = $pdo->exec($code[$i].";");
			echo "<br><br><hr><br>".$code[$i];
			if($ret === false) {
				echo "<br><br>Ein Fehler ist aufgetreten. Das Programm wird beendet.".print_r($pdo->errorInfo());
				break;
			}else{
				echo "<br>$ret Zeilen wurden geändert";
			}
			flush();
		}
	}
	fclose($file);
	$eret = $pdo->query("SET foreign_key_checks=0;");
	echo "<br><br><b>Alle Abfragen wurden erfolgreich ausgeführt</b><br><br>";
}else {
	echo "Gib als GET user, host, dbname und passwd an oder alternativ als GET art=datei zum Auslesen der DB-Daten aus includes/db_data.php";
}