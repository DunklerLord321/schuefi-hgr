<?php
if (isset($user) && $user->runscript()) {
	// $return = query_db("SELECT * FROM finanzuebersicht WHERE MONTH(datum) = MONTH(CURRENT_DATE);");
	$return = query_db("SELECT finanzuebersicht.*, users.vname, users.nname, person.vname AS pvname, person.nname AS pnname FROM `finanzuebersicht` LEFT JOIN `users` ON finanzuebersicht.uid = users.id LEFT JOIN `person` ON finanzuebersicht.pid = person.id;");
	if ($return != false) {
		$result = $return->fetch();
		?>
<h1>Ausgaben der Finanzen</h1>
<table class="table1">
	<tr>
		<th style="text-align: left;">Vorname</th>
		<th style="text-align: left;">Nachname</th>
		<th>Eingabe</th>
		<th>Ausgabe</th>
		<th>Konto/Bar</th>
		<th>Betreff</th>
		<th>Dokument</th>
		<th>Bemerkung</th>
		<th>Datum</th>
	</tr>
  <?php
		while ($result) {
			if ($result['pid'] == NULL) {
				echo "<tr><td  style=\"text-align: left;\">" . $result['vname'] . "</td><td style=\"text-align: left;\">" . $result['nname'] . "</td>";
			}else {
				echo "<tr><td style=\"text-align: left;\">" . $result['pvname'] . "</td><td style=\"text-align: left;\">" . $result['pnname'] . "</td>";
			}
			if ($result['geldbetrag'] < 0) {
				echo "<td></td><td>" . abs($result['geldbetrag']) . "€</td>";
			}else {
				echo "<td>" . $result['geldbetrag'] . "€</td><td></td>";
			}
			echo "<td>" . $result['konto_bar'] . "</td><td>" . $result['betreff'] . "</td><td>" . $result['dokument'] . "</td><td>" . $result['bemerkung'] . "</td><td>" . date('d.m.Y', strtotime($result['datum'])) . "</td></tr>";
			$result = $return->fetch();
		}
		?>
</table>
<?php
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
	