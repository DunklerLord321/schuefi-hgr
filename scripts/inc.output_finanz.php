<?php
if (isset($user) && $user->runscript()) {
	echo "<h1>Ausgabe der Finanzposten</h1>";
	echo "<p>Filter:<br>";
	$filter = array();
	$filter[] = array(
			'id' => 1,
			'row' => 'konto_bar',
			'property' => '\'konto\'',
			'typ' => 'enum',
			'compare' => "=",
			'label' => 'nur Einträge des Kontobuches'
	);
	$filter[] = array(
			'id' => 2,
			'row' => 'konto_bar',
			'property' => '\'bar\'',
			'typ' => 'enum',
			'compare' => "=",
			'label' => 'nur Einträge des Kassenbuches'
	);
	$filter[] = array(
			'id' => 3,
			'row' => 'betreff',
			'property' => '\'schueler\'',
			'typ' => 'enum',
			'compare' => "=",
			'label' => 'nur Einträge der Kategorie Schüler'
	);
	$filter[] = array(
			'id' => 4,
			'row' => 'betreff',
			'property' => '\'lehrer\'',
			'typ' => 'enum',
			'compare' => "=",
			'label' => 'nur Einträge der Kategorie Lehrer'
	);
	$filter[] = array(
			'id' => 5,
			'row' => 'betreff',
			'property' => '\'sonstiges\'',
			'typ' => 'enum',
			'compare' => "=",
			'label' => 'nur Einträge der Kategorie sonstiges'
	);
	$filter[] = array(
			'id' => 6,
			'row' => 'datum',
			'property' => ' \'2017-01-01\' AND \'2018-01-01\'',
			'typ' => 'dates',
			'compare' => 'BETWEEN',
			'label' => 'nur Einträge aus dem Jahr 2017'
	);
	$filter[] = array(
			'id' => 7,
			'row' => 'datum',
			'property' => ' \'2017-08-01\' AND \'2018-07-01\'',
			'typ' => 'dates',
			'compare' => 'BETWEEN',
			'label' => 'nur Einträge aus dem Schuljahr 2017/2018'
	);
	$filter[] = array(
			'id' => 8,
			'row' => 'datum',
			'property' => ' \'2018-01-01\' AND \'2019-01-01\'',
			'typ' => 'dates',
			'compare' => 'BETWEEN',
			'label' => 'nur Einträge aus dem Jahr 2018'
	);
	$aktiv_filter = "";
	$params = "";
	$get_string = (isset($_GET['filter']) ? $_GET['filter']:'');
	$get_string2 = (isset($_GET['filter']) ? $_GET['filter']:'');
	for($i = 0; $i < count($filter); $i++) {
		if(strlen($get_string) > 0  && strpos($get_string, strval($filter[$i]['id'])) !== false ) {
			$get_string = $get_string2;
			$get_string = str_replace($filter[$i]['id'], '', $get_string);
			$aktiv_filter .= "<a href=\"index.php?page=output_finanzen&filter=$get_string\" class=\"links2\"><img src=\"img/png_cross_24_24.png\" style=\"width:18px;heigth:18px;\">".$filter[$i]['label']."</a><br>";
			$params .= (strlen($params) > 0 ?" AND ": '').$filter[$i]['row']." ".$filter[$i]['compare'] . " ". $filter[$i]['property'];
		}else{
			echo "<br><a href=\"index.php?page=output_finanzen&filter=$get_string2".$filter[$i]['id']."\" class=\"links2\">".$filter[$i]['label']."</a>";
		}
	}
//	var_dump($params);
	echo "<br><hr><br>aktive Filter:$aktiv_filter<br><a href=\"index.php?page=output_finanzen\" class=\"links2\">kein Filter</a>";
	if(isset($_GET['filter']) && strlen($params) > 0) {
		$return = query_db("SELECT finanzuebersicht.*, users.vname, users.nname, person.vname AS pvname, person.nname AS pnname FROM `finanzuebersicht`
			LEFT JOIN `users` ON finanzuebersicht.uid = users.id LEFT JOIN `person` ON finanzuebersicht.pid = person.id  WHERE $params ORDER BY finanzuebersicht.datum ASC;");
	}else{
		$return = query_db("SELECT finanzuebersicht.*, users.vname, users.nname, person.vname AS pvname, person.nname AS pnname FROM `finanzuebersicht`
			LEFT JOIN `users` ON finanzuebersicht.uid = users.id LEFT JOIN `person` ON finanzuebersicht.pid = person.id ORDER BY finanzuebersicht.datum ASC;");
	}
	echo "<br><br><b>Hinweis:</b> Die Kontostände und Kassenstände werden auf der Basis von den angezeigten Datensätzen erstellt.
			Um den korrekten Stand der Kasse oder des Kontos zu sehen bitte alle Datensätze ohne Filter anzeigen lassen oder einen Finanzbericht erstellen.";
	if ($return != false) {
		$result = $return->fetch();
		$sum_bar = 0;
		$sum_konto = 0;
  		$table_haeder = "<table class=\"table1\"><tr><th style=\"text-align: left;\">Name</th>
		<th>Einnahmen</th><th>Ausgaben</th>
		<th>Konto/Bar</th><th>Kategorie</th><th>Dokument</th><th>Bemerkung</th><th>Datum</th><th></th></tr>";
//  		var_dump($result);
		while ($result) {
			if(isset($result_vor)) {
				if(intval(date('m', strtotime($result_vor['datum']))) < intval(date('m', strtotime($result['datum']))) || intval(date('y', strtotime($result_vor['datum']))) < intval(date('y', strtotime($result['datum'])))) {
					echo "</table><br>Kontostand: $sum_konto<br>Kassenstand: $sum_bar<h3>".strftime('%B %Y', strtotime($result['datum']))."</h3>$table_haeder";
				}
			}else{
				echo "<h3>".strftime('%B %Y', strtotime($result['datum']))."</h3>".$table_haeder;
			}
			if ($result['pid'] == NULL) {
				echo "<tr><td  style=\"text-align: left;\">" . $result['vname'] . " " . $result['nname'] . "</td>";
			}else {
				echo "<tr><td style=\"text-align: left;\"><a href=\"index.php?page=output_person&filter=" . $result['pid'] . "\" class=\"links2\">" . $result['pvname'] . " ". $result['pnname'] . "</a></td>";
			}
			if ($result['geldbetrag'] < 0) {
				echo "<td></td><td>" . (strlen($result['geldbetrag']) > 0 ? abs($result['geldbetrag']):'0') . "€</td>";
			}else {
				echo "<td>" . (strlen($result['geldbetrag']) > 0 ? $result['geldbetrag']:'0') . "€</td><td></td>";
			}
			echo "<td>" . $result['konto_bar'] . "</td><td>" . $result['betreff'] . "</td><td>" . $result['dokument'] . "</td><td>" . $result['bemerkung'] . "</td><td>" . date('d.m.Y', strtotime($result['datum'])) . "</td>";
			echo ($user->isuserallowed('f') ?"<td><a href=\"index.php?page=input_finanzen&change=".$result['id']."\" class=\"links2\"><img src=\"img/png_change_20_24.png\" alt=\"Ändern des Finanzeintrags\"></a></td>
			<td><a href=\"index.php?page=input_finanzen&finanzloeschen=".$result['id']."\" class=\"links2\"><img src=\"img/png_delete_24_24.png\" alt=\"Löschen des Finanzeintrags\"></a></td></tr>":"");
			if ($result['konto_bar'] == "konto") {
				$sum_konto += $result['geldbetrag'];
			}else if ($result['konto_bar'] == "bar") {
				$sum_bar += $result['geldbetrag'];
			}else{
				echo "Ein grober Fehler trat auf. Die Summen konnten nicht ermittelt werden";
				die();
			}
			$result_vor = $result;
			$result = $return->fetch();
		}
		echo "</table><br>Kontostand: $sum_konto<br>Kassenstand: $sum_bar";
	}
	echo "<br><br><br><a href=\"index.php?page=export_finanzen\" class=\"links2\">Erstellen der Monats/Jahresübersichten</a>";
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
