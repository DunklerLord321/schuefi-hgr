<?php
require 'header.php';
require 'includes/global_vars.inc.php';
if (! function_exists ( 'get_users_logged_in' )) {
	include 'includes/functions.inc.php';
}
echo "<h2>Einstellungen</h2>";
if (isset ( $_SESSION ['userid'] ) && isset ( $_SESSION ['username'] ) && isset ( $_SESSION ['account'] ) && (strcmp ( $_SESSION ['account'], 'root' ) == 0) && if_logged_in ( $_SESSION ['userid'] )) {
	$pdo_query = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
	if (isset ( $_GET ['addyear'] ) && $_GET ['addyear'] == 1 && isset($_POST['addyear-bt']) && isset($_POST['addyear-rd']) && $_POST['addyear-rd'] == 1) {
		$return = $pdo_query->query ( "SHOW TABLES like '%-%'" );
		if ($return === false) {
			echo "Ein Fehler ist passiert";
		} else {
			$tables = $return->fetch ();
			while ( $tables !== false ) {
				$tablevar = explode ( "-", $tables [0] );
				$year = intval ( $tablevar [1] );
				$years [] = $year;
				$tables = $return->fetch ();
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
  `hinzugefügt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `1617` tinyint(1) DEFAULT '0',
  `1718` tinyint(1) DEFAULT '0',
  `1819` tinyint(1) DEFAULT '0',
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
  `hinzugefügt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `1617` tinyint(1) DEFAULT '0',
  `1718` tinyint(1) DEFAULT '0',
  `1819` tinyint(1) DEFAULT '0',
  `comment` varchar(500) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
		
ALTER TABLE `lehrer-" . $newyear . "`
  ADD PRIMARY KEY (`id`);
		
ALTER TABLE `paare-" . $newyear . "`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paare_ibfk_2` (`id_schueler`),
  ADD KEY `paare_ibfk_1` (`id_lehrer`);
		
ALTER TABLE `schueler-" . $newyear . "`
  ADD PRIMARY KEY (`id`);
		
ALTER TABLE `lehrer-" . $newyear . "`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		
ALTER TABLE `paare-" . $newyear . "`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		
ALTER TABLE `schueler-" . $newyear . "`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		
ALTER TABLE `paare-" . $newyear . "`
  ADD CONSTRAINT `paare_ibfk_1` FOREIGN KEY (`id_lehrer`) REFERENCES `lehrer-" . $newyear . "` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `paare_ibfk_2` FOREIGN KEY (`id_schueler`) REFERENCES `schueler-" . $newyear . "` (`id`) ON UPDATE CASCADE;";
			$return_query = $pdo_query->query ( $sql );
			if ($return_query != false) {
				echo "<br>Neues Schuljahr erfolgreich hinzugefügt<br>";
			}
		}
	}
	if(isset($_GET['delyear']) && $_GET['delyear'] == 1 && isset($_POST['delyear']) && is_numeric($_POST['delyear'])) {
//		echo $_POST['delyear'];
		$return = $pdo_query->query ( "SHOW TABLES like '%".$_POST['delyear']."'" );
		if ($return === false) {
			echo "<br>Ein Fehler ist passiert";
		} else {
			$i = 0;
			$tables = $return->fetch ();
			while ( $tables !== false ) {
				$tablevar = explode ( "-", $tables [0] );
				$year = intval ( $tablevar [1] );
				$i++;
//				echo $i;
				$tables = $return->fetch ();
			}
			if($i != 3) {
				echo "<br>Ein Problem trat auf";
			}else{
				$lehrertable = "lehrer-".$_POST['delyear'];
				$schuelertable = "schueler-".$_POST['delyear'];
				$paartable = "paare-".$_POST['delyear'];
				$sql = "DROP TABLES `".$lehrertable."`, `".$schuelertable."`, `".$paartable."`;";
//				echo $sql;
				$return = $pdo_query->query($sql);
				if($return === false) {
					echo "<br>Es ist ein Problem aufgetreten.";
				}else{
					echo "<br>Schuljahr wurde erfolgreich gelöscht.";
				}
			}
		}
	}
	$pdo_query = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
	$return = $pdo_query->query ( "SHOW TABLES like '%-%'" );
	// print_r($return);
	// var_dump($return);
	$years = array ();
	if ($return === false) {
		echo "Ein Fehler ist passiert";
	} else {
		$tables = $return->fetch ();
		echo "<br>Tabellen für die Schuljahre:";
		$i = 0;
		?>
		<form action="<?php echo $_SERVER['PHP_SELF'];?>?delyear=1" method="post"><?php
		while ( $tables !== false ) {
			$tablevar = explode ( "-", $tables [0] );
			$year = intval ( $tablevar [1] );
			if (! in_array ( $year, $years )) {
				$i++;
				echo "<input type=\"radio\" value=\"$year\" name=\"delyear\">$year<br><br>";
			}
			$years [] = $year;
			$tables = $return->fetch ();
		}
		if($i != 0) {
			echo "<input type=\"submit\" value=\"Lösche Schuljahr\">";
		}
		?>
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF'];?>?addyear=1" method="post">
		Neues Schuljahr hinzufügen?
		<input type="radio" name="addyear-rd" value="1">Ja
		<input type="radio" name="addyear-rd" checked value="0">Nein<br>
		<input type="submit" value="Füge neues Schuljahr hinzu" name="addyear-bt">
		</form>
		<br>
		<?php
	}
}elseif (strcmp($_SESSION['acount'],'normal') == 0 || strcmp($_SESSION['acount'],'view-only') == 0) {
	echo "Sie sind leider nicht berechtigt, Einstellungen vorzunehmen!";
}