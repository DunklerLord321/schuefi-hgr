<?php
if (isset($user) && $user->runscript()) {
/*	if (isset($_GET['toggle']) && isset($_GET['status'])) {
		query_db("UPDATE raum SET frei = :status WHERE id = :id", $_GET['status'], $_GET['toggle']);
	}*/
	if (isset($_GET['delete'])) {
		$return = query_db("DELETE FROM raum WHERE raum.id = :id", $_GET['delete']);
		if (!$return) {
			echo "Ein Paar hat zu der Zeit in dem Zimmer unterricht. Deshalb können die Daten über das Zimmer nicht gelöscht werden<br>";
		}else{
			echo "Die Daten wurden erfolgreich gelöscht";
		}
	}
	if (isset($_GET['deleteall'])) {
		$return = query_db("SELECT * FROM raum LEFT JOIN (SELECT unterricht.rid, unterricht.id as pid, COUNT(unterricht.rid) AS belegung FROM unterricht GROUP BY unterricht.rid, unterricht.id) as u ON u.rid = raum.id ORDER BY `raum`.`nummer` ASC, `raum`.`tag` ASC, `raum`.`stunde` ASC ");
		$result = $return->fetch();
		while($result) {
			if ($result['belegung'] == 1) {
				$return_change = query_db("UPDATE `unterricht` SET treff_raum = 'Cafetaria', rid = NULL WHERE id = :id;", $result['pid']);
				if(!$return_change) {
					echo "Ein Fehler ist aufgetreten!";
				}else{
					$return_delete = query_db("DELETE FROM raum WHERE raum.id = :id", $result['id']);				
				}
			}else{
				$return_delete = query_db("DELETE FROM raum WHERE raum.id = :id", $result['id']);
			}
			$result = $return->fetch();
		}
		if (!$return) {
			echo "Ein Paar hat zu der Zeit in dem Zimmer unterricht. Deshalb können die Daten über das Zimmer nicht gelöscht werden<br>";
		}else{
			echo "Die Daten wurden erfolgreich gelöscht";
		}	
	}
	echo "<h2>Ausgeben der Räume</h2>";
	echo "<br><a href=\"index.php?page=output_raum&frei=1\" class=\"links2\">Liste alle freien Zimmer auf</a>";
	echo "<br><a href=\"index.php?page=output_raum&frei=0\" class=\"links2\">Liste alle belegten Zimmer auf</a>";
	echo "<br><a href=\"index.php?page=output_raum\" class=\"links2\">Liste alle Zimmer im Datenbestand auf</a><br><br>";
	if(isset($_GET['frei']) && $_GET['frei'] == 0) {
		$return = query_db("SELECT * FROM raum LEFT JOIN (SELECT unterricht.rid, COUNT(unterricht.rid) AS belegung FROM unterricht GROUP BY unterricht.rid) as u ON u.rid = raum.id WHERE u.belegung = 1 ORDER BY `raum`.`nummer` ASC, `raum`.`tag` ASC, `raum`.`stunde` ASC");
	}else if(isset($_GET['frei']) && $_GET['frei'] == 1){
		$return = query_db("SELECT * FROM raum LEFT JOIN (SELECT unterricht.rid, COUNT(unterricht.rid) AS belegung FROM unterricht GROUP BY unterricht.rid) as u ON u.rid = raum.id WHERE u.belegung <=> NULL ORDER BY `raum`.`nummer` ASC, `raum`.`tag` ASC, `raum`.`stunde` ASC");
	}else{
		$return = query_db("SELECT * FROM raum LEFT JOIN (SELECT unterricht.rid, COUNT(unterricht.rid) AS belegung FROM unterricht GROUP BY unterricht.rid) as u ON u.rid = raum.id ORDER BY `raum`.`nummer` ASC, `raum`.`tag` ASC, `raum`.`stunde` ASC");
	}
	if($return !== false) {
		$result = $return->fetch();
		if (!$result) {
			echo ( isset($_GET['frei']) == false ? "Es wurden noch keine Daten hinzugefügt": "Mit diesem Filter wurde kein Ergebnis erzielt");
		}else{
//			echo "<table class=\"table1\"><tr><th>Zimmer</th><th>Tag</th><th>Stunde</th><th>Status</th><th></th><th></th></tr>";
			echo "<table class=\"table1\"><tr><th>Zimmer</th><th>Tag</th><th>Stunde</th><th>Status</th><th></th></tr>";
		}
		while ($result){
			echo "<tr><td>".$result['nummer']."</td><td>".get_name_of_tag($result['tag'])."</td><td>".$result['stunde']."</td><td>".($result['belegung'] != 1 ? "frei" : "<a href=\"index.php?page=output&paare=1&raumfilter=".$result['id']."\" class=\"links2\">belegt durch Nachhilfepaar</a>")."</td><td>";
			if($user->isuserallowed('fk')) {
//				echo "<a href=\"index.php?page=output_raum&toggle=" . $result['id']. "&status=".($result['belegung'] == 1 ? "0" : "1")."\" class=\"links2\" onclick=\"return warn('Willst du den Belegungssstatus wirklich per Hand ändern?')\">Ändere Status</a></td>";
				echo "<td><a href=\"index.php?page=output_raum&delete=". $result['id']."\" class=\"links2\" onclick=\"return warn('Willst du die Information über die Belegung des Zimmers für diesen Tag für diese eine Stunde wirklich löschen?')\"><img alt=\"Löschen\" src=\"img/png_delete_24_24.png\"></a></td></tr>";
			}
			$result = $return->fetch();
		}
		echo "</table><br><br><b>Hinweis:</b> <br>Der Status frei oder belegt gibt nur an, ob das Zimmer schon von einem Nachhilfepaar genutzt wird oder nicht. In allen gelisteten Zimmern findet kein Unterricht zu den gelisteten Zeiten statt.";
		echo "<br><br><a href=\"index.php?page=output_raum&deleteall=true\" class=\"links2\">Alle Zimmerbelegungen löschen</a>";
	}else{
		echo "Ein Fehler ist aufgetreten";
	}
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
		
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
	