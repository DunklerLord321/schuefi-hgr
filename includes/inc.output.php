<?php
if (isset($user) && $user->runscript()) {
	global $pdo;
	echo "<h2>Ausgabe</h2>";
	require 'includes/class_person.php';
	require 'includes/class_lehrer.php';
	require 'includes/class_schueler.php';
	if (isset($_GET['lehrer']) && $_GET['lehrer'] == 1) {
		$return = query_db("SELECT * FROM `lehrer` WHERE `schuljahr` = :schuljahr", get_current_year());
		if ($return === false) {
			echo "Ein Problem";
			die();
		}
		$result = $return->fetch();
		if ($result !== false) {
			while ( $result ) {
				$lehrer = new lehrer(-1, $result['id']);
				?>
<fieldset style="padding: 40px; width: 80%; padding-top: 10px;">
	<legend><?php echo $lehrer->person->vname.' '.$lehrer->person->nname?></legend>
	<div style="display: flex;">
		<div style="width: 70%; display: inline-block;">
					<?php
				echo "<br>Klasse: " . $lehrer->get_klassenstufe();
				if (is_numeric($lehrer->get_klasse())) {
					echo "/";
				}
				echo $lehrer->get_klasse();
				echo "<br>Klassenlehrer/in: " . $lehrer->get_klassenlehrer();
				$faecher = $lehrer->get_angebot_faecher();
				$zeit = $lehrer->get_zeit();
				echo "<br><br><b>Fächer:</b>";
				for($i = 0; $i < count($faecher); $i++) {
					echo "<div style=\"padding-left: 5%;\">";
					echo "<br><b>" . get_faecher_name_of_id($faecher[$i]['fid']) ."</b>";
					echo "<br>Fachlehrer: ".$faecher[$i]['fachlehrer'];
					echo "<br>Notenschnitt: ".$faecher[$i]['notenschnitt'];
					echo " <br> Nachweis vorhanden: " . ($faecher[$i]['nachweis_vorhanden'] == true ? "ja" : "nein");
					echo "<br><b>Vermittlungsstatus: ".$faecher[$i]['status']."</b>";
					echo "</div>";
				}
				echo "<br><b>Zeiten:</b>";
				echo "<div style=\"padding-left: 5%;\">";
				for($i = 0; $i < count($zeit); $i++) {
					echo "<br>" . get_name_of_tag($zeit[$i]['tag']) . " von " . $zeit[$i]['anfang'] . " Uhr bis " . $zeit[$i]['ende'] . " Uhr";
				}
				echo "</div>";
				?>
		</div>
		<div style="width: 30%; display: inline-block; padding-top: 40px;">
			<a href="index.php?page=change&lehrer=<?php echo $lehrer->get_id();?>" class="links">Ändere die Daten</a>
		</div>
	</div>
</fieldset>
<?php
				$result = $return->fetch();
			}
		} else {
			echo "Es wurde noch kein Lehrer hinzugefügt";
		}
	}
	if (isset($_GET['schueler']) && $_GET['schueler'] == 1) {
		$return = query_db("SELECT * FROM `schueler` WHERE `schuljahr` = :schuljahr", get_current_year());
		if ($return === false) {
			echo "Ein Problem";
			die();
		}
		$result = $return->fetch();
		if ($result !== false) {
			while ( $result ) {
				$schueler = new schueler(-1, $result['id']);
				?>
<fieldset style="padding: 40px; width: 80%; padding-top: 10px;">
	<legend><?php echo $schueler->person->vname.' '.$schueler->person->nname?></legend>
	<div style="display: flex;">
		<div style="width: 70%; display: inline-block;">
							<?php
				echo "<br>Klasse: " . $schueler->get_klassenstufe();
				if (is_numeric($schueler->get_klasse())) {
					echo "/";
				}
				echo $schueler->get_klasse();
				echo "<br>Klassenlehrer/in: " . $schueler->get_klassenlehrer();
				$faecher = $schueler->get_nachfrage_faecher();
				$zeit = $schueler->get_zeit();
				echo "<br><br><b>Fächer:</b>";
				for($i = 0; $i < count($faecher); $i++) {
					if(isset($faecher[$i])) {
						echo "<div style=\"padding-left: 5%;\">";
						echo "<br>" . get_faecher_name_of_id($faecher[$i]['fid']) . " <br> Langfristig: " . ($faecher[$i]['langfristig'] == true ? "ja" : "nein");
						echo "<br>Fachlehrer: ".$faecher[$i]['fachlehrer'];
						echo "<br><b>Vermittlungsstatus: ".$faecher[$i]['status']."</b><br><br>";
						echo "<a href=\"index.php?page=input_paar&schueler=".$schueler->get_id()."&fid=".$faecher[$i]['fid']."\" class=\"links\">Suche nach Lehrer</a>";
						echo "</div>";
					}
				}
				echo "<br><b>Zeiten:</b>";
				echo "<div style=\"padding-left: 5%;\">";
				for($i = 0; $i < count($zeit); $i++) {
					echo "<br>" . get_name_of_tag($zeit[$i]['tag']) . " von " . $zeit[$i]['anfang'] . " Uhr bis " . $zeit[$i]['ende'] . " Uhr";
				}
				echo "</div>";
				?>
				</div>
		<div style="width: 30%; display: inline-block; padding-top: 40px;">
			<a href="index.php?page=change&schueler=<?php echo $schueler->get_id();?>" class="links">Ändere die Daten</a>
		</div>
	</div>
</fieldset>
<?php
				$result = $return->fetch();
			}
		} else {
			echo "Es wurde noch kein Schüler hinzugefügt";
		}
	}
	if(isset($_GET['paare']) && $_GET['paare'] == 1) {
		//Ausgabe der Paare
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>