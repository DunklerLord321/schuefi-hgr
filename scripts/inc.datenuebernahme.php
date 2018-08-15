<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Datenübernahme</h2>";
	echo "<p>Daten können erst nach dem 01.08. eines Jahres in des nächste Schuljahr übernommen werden. Beachte, dass die Daten zur Raumbelegung aktualisiert werden müssen. Wenn erst die Raumbelegung aktualisiert wird und anschließend die Nachhilfepaare übernommen werden, können den Paaren bei der Übernahme gleich neue Zimmer automatisch zugewiesen werden.</p><br>";
	echo "<a href=\"index.php?page=next_year&role=person\" class=\"links2\">Übernahme von Personen in nächstes Schuljahr</a><br><br>";
	echo "<a href=\"index.php?page=next_year&role=teacher\" class=\"links2\">Übernahme von Lehrern in nächstes Schuljahr</a><br><br>";
	echo "<a href=\"index.php?page=next_year&role=student\" class=\"links2\">Übernahme von Schülern in nächstes Schuljahr</a><br><br>";
	?>
	<script type="text/javascript">
$(function() {
	$("A[href='#select_all']").click(function() {
		$("INPUT[type='checkbox']").prop('checked',true);
	});
	$("A[href='#select_none']").click(function() {
		$("INPUT[type='checkbox']").prop('checked',false);
	});
});
</script>
<?php
	if (isset($_GET['role']) && $_GET['role'] == "person") {
		/*
		* Übernahme der Daten einer Person mittels aktivieren/Deaktivieren
		*/
		if(isset($_GET['action']) && $_GET['action'] == "change") {
			$return = query_db("SELECT * FROM `person` ORDER BY `person`.`nname` ASC");
			$result = $return->fetch();
			require 'includes/class_person.php';
			$person = new person();
			$count = 0;
			if ($result !== false) {
				while ($result) {
					$person->load_person($result['id']);
					if (isset($_POST[$person->id]) && $_POST[$person->id] == "true") {
						$ret = $person->activate();
						if ($ret !== true) {
							echo $ret;
							$count++;
						}
					}else{
						$ret = $person->deactivate();
						if ($ret !== true) {
							echo $ret;
							$count++;
						}
					}
					$result = $return->fetch();
				}
				echo "<br>$count Fehler traten auf";
			}
		}else{
			$return = query_db("SELECT * FROM `person` ORDER BY `person`.`nname` ASC");
			$result = $return->fetch();
			require 'includes/class_person.php';
			require 'includes/class_lehrer.php';
			require 'includes/class_schueler.php';
			$person = new person();
			if ($result !== false) {
				echo "<form action=\"index.php?page=next_year&role=person&action=change\" method=\"POST\"><table class=\"table1\"><tr><th>Vorname</th><th>Nachname</th><th>E-Mail-Adresse</th><th>Telefon</th><th>Geburtstag</th><th>Nachhilfeschüler</th><th>Nachhilfelehrer</th><th>Aktiv im <br>nächsten Jahr</th></tr>";
				while ($result) {
					$person->load_person($result['id']);
					echo "<tr><td>$person->vname</td><td>$person->nname</td><td>$person->email</td><td>$person->telefon</td><td>$person->geburtstag</td>";
					$schueler_lehrer = $person->search_lehrer_schueler();
					if (is_array($schueler_lehrer['schueler'])) {
						echo "<td><a href=\"index.php?page=output&schueler=1&filter=" . $person->id . "\" class=\"links2\"><img src=\"img/png_yes_12_16.png\" alt=\"ja\" style=\"width:13px;\"></a></td>";
					}else{
						echo "<td><img src=\"img/png_no_13_20.png\" alt=\"nein\"></td>";
					}
					if (is_array($schueler_lehrer['lehrer'])) {
						echo "<td><a href=\"index.php?page=output&lehrer=1&filter=" . $person->id . "\" class=\"links2\"><img src=\"img/png_yes_12_16.png\" alt=\"ja\" style=\"width:13px;\"></a></td>";
					}else{
						echo "<td><img src=\"img/png_no_13_20.png\" alt=\"nein\"></td>";
					}
					echo "<td><input type=\"checkbox\" name=\"".$person->id."\" value=\"true\" ".($person->aktiv == 1 ? 'checked':'')."></td></tr>";
					$result = $return->fetch();
				}
				echo "</table><br><a href=\"#select_all\" class=\"mybuttons\" style=\"float:right;\">Alle auswählen</a><a href=\"#select_none\" class=\"mybuttons\" style=\"float:right;\">Alle abwählen</a>";
				echo "<br><br><br><input class=\"mybuttons\" style=\"float:right;\" type=\"submit\" value=\"Ändern\"><br><br><br></form>";
			}
		}
	}elseif (isset($_GET['role']) && $_GET['role'] == "teacher") {
		$return = query_db("SELECT `lehrer`.*, `person`.`nname` FROM `lehrer` LEFT JOIN `person` ON `person`.`id` = `lehrer`.`pid` WHERE `schuljahr` = :schuljahr GROUP BY `person`.`nname`, `lehrer`.`id` ASC  ", get_last_year());
		if ($return === false) {
			echo "Ein Problem";
			die();
		}
		$result = $return->fetch();
		if ($result !== false) {
			echo "<table class=\"table1\"><tr><th>Name</th><th>Klasse</th><th>Klassenlehrer</th><th>Fächer</th><th>Zeiten</th><th>Kommentar</th><th></th></tr>";
			$count = 0;
			require 'includes/class_lehrer.php';
			while ($result) {
				$count ++;
				$lehrer = new lehrer(-1, $result['id']);
				echo "<tr><td><a href=\"index.php?page=output_person&filter=" . $lehrer->person->id . "\" class=\"links2\">" . $lehrer->person->vname . ' ' . $lehrer->person->nname . "</a></td>";
				echo "<td>" . format_klassenstufe_kurs($lehrer->get_klassenstufe(), $lehrer->get_klasse())."</td>";
				echo "<td>" . $lehrer->get_klassenlehrer()."</td><td>";
				$faecher = $lehrer->get_angebot_faecher();
				$zeit = $lehrer->get_zeit();
				for ($i = 0; $i < count($faecher); $i++) {
					echo get_faecher_name_of_id($faecher[$i]['fid']) . "<br>". $faecher[$i]['fachlehrer'];
					echo "<br>Notenschnitt: " . $faecher[$i]['notenschnitt'];
					echo "<br> Nachweis vorhanden: " . ($faecher[$i]['nachweis_vorhanden'] == true ? "ja" : "nein");
					echo "<br>Vermittlungsstatus: " . $faecher[$i]['status'] . "<br>";
				}
				echo "</td><td>";
				for ($i = 0; $i < count($zeit); $i++) {
					echo get_name_of_tag($zeit[$i]['tag']) . " von " . date("H:i", strtotime($zeit[$i]['anfang'])) . " - " . date("H:i", strtotime($zeit[$i]['ende'])) . "<br>";
				}
				echo "</td>";
				if (strlen($lehrer->get_comment()) > 0) {
					echo "<td>" . $lehrer->get_comment()."</td>";
				}else{
					echo "<td>Kein Kommentar</td>";
				}
				if($user->isuserallowed('k')) {
					if($lehrer->exists_in_current_year() == false) {
						echo "<td><a href=\"index.php?page=change&prev=next_year&lehrer=".$lehrer->get_id()."\" class=\"links2\">Übernahme in nächstes Schuljahr</a></td>";
					}else{
						echo "<td><a href=\"index.php?page=output&lehrer=1&filter=".$lehrer->person->id."\" class=\"links2\">Der Lehrer wurde schon in das nächste Jahr übernommen</a><td>";
					}
				}
				echo "</tr>";
				$result = $return->fetch();
			}
		}else{
			echo "<br><br>Kein Lehrer gefunden";
		}
	}elseif (isset($_GET['role']) && $_GET['role'] == "student") {
		$return = query_db("SELECT `schueler`.*, `person`.`nname` FROM `schueler` LEFT JOIN `person` ON `person`.`id` = `schueler`.`pid` WHERE `schuljahr` = :schuljahr GROUP BY `person`.`nname`, `schueler`.`id` ASC  ", get_last_year());
		if ($return === false) {
			echo "Ein Problem";
			die();
		}
		$result = $return->fetch();
		if ($result !== false) {
			echo "<table class=\"table1\"><tr><th>Name</th><th>Klasse</th><th>Klassenlehrer</th><th>Fächer</th><th>Zeiten</th><th>Kommentar</th><th></th></tr>";
			$count = 0;
			require 'includes/class_schueler.php';
			while ($result) {
				$count ++;
				$schueler = new schueler(-1, $result['id']);
				echo "<tr><td><a href=\"index.php?page=output_person&filter=" . $schueler->person->id . "\" class=\"links2\">" . $schueler->person->vname . ' ' . $schueler->person->nname . "</a></td>";
				echo "<td>" . format_klassenstufe_kurs($schueler->get_klassenstufe(), $schueler->get_klasse())."</td>";
				echo "<td>" . $schueler->get_klassenlehrer()."</td><td>";
				$faecher = $schueler->get_nachfrage_faecher();
				$zeit = $schueler->get_zeit();
				for ($i = 0; $i < count($faecher); $i++) {
					echo get_faecher_name_of_id($faecher[$i]['fid']) . "<br>". $faecher[$i]['fachlehrer'];
					echo "<br>Vermittlungsstatus: " . $faecher[$i]['status'] . "<br>";
				}
				echo "</td><td>";
				for ($i = 0; $i < count($zeit); $i++) {
					echo get_name_of_tag($zeit[$i]['tag']) . " von " . date("H:i", strtotime($zeit[$i]['anfang'])) . " - " . date("H:i", strtotime($zeit[$i]['ende'])) . "<br>";
				}
				echo "</td>";
				if (strlen($schueler->get_comment()) > 0) {
					echo "<td>" . $schueler->get_comment()."</td>";
				}else{
					echo "<td>Kein Kommentar</td>";
				}
				if($user->isuserallowed('k')) {
					if($schueler->exists_in_current_year() == false) {
						echo "<td><a href=\"index.php?page=change&prev=next_year&schueler=".$schueler->get_id()."\" class=\"links2\">Übernahme in nächstes Schuljahr</a></td>";
					}else{
						echo "<td><a href=\"index.php?page=output&schueler=1&filter=".$schueler->person->id."\" class=\"links2\">Der Schüler wurde schon in das nächste Jahr übernommen</a><td>";
					}
				}
				echo "</tr>";
				$result = $return->fetch();
			}
		}else{
			echo "<br><br>Kein Schüler gefunden";
		}
	}
}else {	
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
	