<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Neuer Raum</h2>";
	if (isset($_GET['raum'])) {
		$raume = array();
		$raume = array_values($_POST['raum']);
	}else if(isset($_GET['autoraum']))	{
		if (!isset($_FILES['file']['tmp_name']) || strlen($_FILES['file']['tmp_name']) == 0) {
			echo "Keine Datei ausgewählt!";
			die();
		}
		$raume = array();
		$xml = simplexml_load_file($_FILES['file']['tmp_name']);
		for ($j = 0; $j < count($xml->element->haupt->zeile); $j++) {
			if (isset($xml->element->haupt->zeile[$j])) {
				if (array_search($xml->element->haupt->zeile[$j]->stunde, $GLOBAL_CONFIG['unterrichtsstunden']) !== false) {
					if (strlen($xml->element->haupt->zeile[$j]->tag1->gruppe) == 0 && strlen($xml->element->haupt->zeile[$j]->tag1->fach) == 0) {
						$raume[] = array('tag' => "mo", 'stunde' => (string)$xml->element->haupt->zeile[$j]->stunde, 'nummer' => (string)$xml->element->kopf->aktuell);
					}
					if (strlen($xml->element->haupt->zeile[$j]->tag2->gruppe) == 0 && strlen($xml->element->haupt->zeile[$j]->tag2->fach) == 0) {
						$raume[] = array('tag' => "di", 'stunde' => (string)$xml->element->haupt->zeile[$j]->stunde, 'nummer' => (string)$xml->element->kopf->aktuell);
					}
					if (strlen($xml->element->haupt->zeile[$j]->tag3->gruppe) == 0 && strlen($xml->element->haupt->zeile[$j]->tag3->fach) == 0) {
						$raume[] = array('tag' => "mi", 'stunde' => (string)$xml->element->haupt->zeile[$j]->stunde, 'nummer' => (string)$xml->element->kopf->aktuell);
					}
					if (strlen($xml->element->haupt->zeile[$j]->tag4->gruppe) == 0 && strlen($xml->element->haupt->zeile[$j]->tag4->fach) == 0) {
						$raume[] = array('tag' => "do", 'stunde' => (string)$xml->element->haupt->zeile[$j]->stunde, 'nummer' => (string)$xml->element->kopf->aktuell);
					}
					if (strlen($xml->element->haupt->zeile[$j]->tag5->gruppe) == 0 && strlen($xml->element->haupt->zeile[$j]->tag5->fach) == 0) {
						$raume[] = array('tag' => "fr", 'stunde' => (string)$xml->element->haupt->zeile[$j]->stunde, 'nummer' => (string)$xml->element->kopf->aktuell);
					}
				}
			}
		}
		if (count($xml->element->haupt->zeile) == 7) {
			$raume[] = array('tag' => "mo", 'stunde' => '8', 'nummer' => (string)$xml->element->kopf->aktuell);
			$raume[] = array('tag' => "di", 'stunde' => '8', 'nummer' => (string)$xml->element->kopf->aktuell);
			$raume[] = array('tag' => "mi", 'stunde' => '8', 'nummer' => (string)$xml->element->kopf->aktuell);
			$raume[] = array('tag' => "do", 'stunde' => '8', 'nummer' => (string)$xml->element->kopf->aktuell);
			$raume[] = array('tag' => "fr", 'stunde' => '8', 'nummer' => (string)$xml->element->kopf->aktuell);
		}
		echo "Daten wurden aus Datei erfolgreich gelesen. Dabei wurden ".count($raume)." freie Räume gefunden";
	}
	if(isset($raume)) {
		//raum in DB inserieren
		$tage = array('mo', 'di', 'mi', 'do', 'fr');
		$counter = 0;
		for ($i = 0; $i < count($raume); $i++) {
			if (isset($raume[$i])){
				if (array_search($raume[$i]['tag'], $tage) === false) {
					echo "<br>Ungültiger Wert für Tag";
					break;
				}
				if (array_search($raume[$i]['stunde'], $GLOBAL_CONFIG['unterrichtsstunden']) === false) {
					echo "<br>Ungültiger Wert für Stunde";
					break;
				}
				if (strlen($raume[$i]['nummer']) > 5) {
					echo "<br>Ungültiger Wert für Zimmer";
					break;
				}
				$result = query_db("SELECT * FROM raum WHERE nummer = :nummer AND tag = :tag AND stunde = :stunde", $raume[$i]['nummer'], $raume[$i]['tag'], $raume[$i]['stunde']);
				if($result->fetch()) {
					echo "<br>Für dieses Zimmer existiert für diesen Tag zu der angegeben Zeit schon ein Datensatz";
				}else{
					$result = query_db("INSERT INTO raum (nummer, tag, stunde, frei) VALUES(:nummer, :tag, :stunde, 1)", $raume[$i]['nummer'], $raume[$i]['tag'], $raume[$i]['stunde']);
					if($result !== false) {
						$counter++;
					}
				}
			}
		}
		echo "<br><br>$counter von ".count($raume)." Datensätze wurden erfolgreich eingefügt";
		echo "<br><a href=\"index.php?page=input_raum\" class=\"links2\">Weitere Räume hinzufügen</a>";
		echo "<br><a href=\"index.php?page=output_raum\" class=\"links2\">Ausgeben der Räume</a>";
	}else{
		?>
		<script type="text/javascript">
var zaehler = 1;
$(function() {
	$('#addrow').click(function() {
		zaehler ++; 
		$('#rows').append('		<div style="width: 30%; display: inline-block;">\
				<label>Zimmer:</label>\
				<input type="text" name="raum['+zaehler+'][nummer]">\
			</div>\
			<div style="width: 30%; display: inline-block;">\
				<select name="raum['+zaehler+'][tag]" style="width: 90%">\
					<option value="mo">Montag</option>\
					<option value="di">Dienstag</option>\
					<option value="mi">Mittwoch</option>\
					<option value="do">Donnerstag</option>\
					<option value="fr">Freitag</option>\
				</select>\
			</div>\
			<div style="width: 30%; display: inline-block;">\
				<label>frei in:</label>\
				<select name="raum['+zaehler+'][stunde]" style="width: 80%">\
					<option value="5">5.Stunde</option>\
					<option value="6">6.Stunde</option>\
					<option value="7">7.Stunde</option>\
					<option value="8">8.Stunde</option>\
				</select>\
			</div>\
			');
		$('[name="raum['+zaehler+'][nummer]"]').val($('[name="raum['+(zaehler-1)+'][nummer]"]').val());
		$('[name="raum['+zaehler+'][tag]"]').val($('[name="raum['+(zaehler-1)+'][tag]"]').val());
		$('[name="raum['+zaehler+'][stunde]"]').val($('[name="raum['+(zaehler-1)+'][stunde]"]').val());
		if(zaehler > 9) {
			$('#rows').append('<br><br><b>Hinweis:</b> Bitte gib nicht mehr als 10 Zimmerbelgeungen auf einmal ein<br>');
			$('#addrow').remove();
		} 
	});
});
</script>
manuelle Eingabe:
<div class="formular_class">
	<form method="post" action="index.php?page=input_raum&raum=1">
		<div style="width: 30%; display: inline-block;">
			<label>Zimmer:</label>
			<input type="text" name="raum[1][nummer]">
		</div>
		<div style="width: 30%; display: inline-block;">
			<select name="raum[1][tag]" style="width: 90%">
				<option value="mo">Montag</option>
				<option value="di">Dienstag</option>
				<option value="mi">Mittwoch</option>
				<option value="do">Donnerstag</option>
				<option value="fr">Freitag</option>
			</select>
		</div>
		<div style="width: 30%; display: inline-block;">
			<label>frei in:</label>
			<select name="raum[1][stunde]" style="width: 80%">
				<option value="5">5.Stunde</option>
				<option value="6">6.Stunde</option>
				<option value="7">7.Stunde</option>
				<option value="8">8.Stunde</option>
			</select>
		</div>
		<div id="rows"></div>
		<br>
		<button id="addrow" type="button" class="mybuttons"><img src="img/png_add_13_20.png" ></button>
		<input type="submit" value="Hinzufügen" class="mybuttons" style="float: right;">
		<br>
		<br>
		<br>
		<br>
	</form>
</div>
<hr><br>Eingabe der Raumbelegung mit XML-Datei (von der Planung als XML/HTML-Datei exportieren lassen):<br><br>
<div class="formular_class">
	<form method="post" action="index.php?page=input_raum&autoraum=1" enctype="multipart/form-data">
	<input type="file" name="file" class="mybuttons">
	<input type="submit" class="mybuttons" style="float: right;" value="Hinzufügen"><br><br><br>
	</form>
</div>
<?php
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
	