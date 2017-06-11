<?php
if (isset($user) && $user->runscript()) {
	global $pdo;
	echo "<h2>Ausgabe</h2>";
	$return = $pdo->query("SELECT * FROM `person`");
	$result = $return->fetch();
	require 'includes/class_person.php';
	require 'includes/class_lehrer.php';
	require 'includes/class_schueler.php';
	$person = new person();
	if ($result !== false) {
		while ( $result ) {
			$person->load_person($result['id']);
			?>
<fieldset style="padding: 40px; width: 80%;">
	<legend><?php echo $person->vname.' '.$person->nname?></legend>
	<div style="display: flex;">
		<div style="width: 70%; display: inline-block;">
						
			<?php
			echo "Name: " . $person->vname . " " . $person->nname . "<br>E-Mail: " . $person->email;
			if (strlen($person->telefon) > 0) {
				echo "<br>Telefon: " . $person->telefon;
			}
			if (strlen($person->geburtstag) > 0) {
				echo "<br>Geburtstag: " . $person->geburtstag;
			}
			$schueler_lehrer = $person->search_lehrer_schueler();
			if (is_array($schueler_lehrer['lehrer'])) {
				?>
				<div style="padding-left: 10%;">
				<?php
				$lehrer = new lehrer($person->id);
				$lehrer->load_lehrer_pid();
				echo "<br><br>$person->vname $person->nname ist als Nachhilfelehrer tätig:";
				echo "<br>Klasse: ".$lehrer->get_klassenstufe();
				if(is_numeric($lehrer->get_klasse())) {
					echo "/";
				}
				echo $lehrer->get_klasse();
				echo "<br>Klassenlehrer/in: ".$lehrer->get_klassenlehrer();
				echo "<br>Fächer, in denen er/sie Nachhilfe anbietet:";
				$faecher = $lehrer->get_angebot_faecher();
				$zeit = $lehrer->get_zeit();
				for($i = 0; $i < count($faecher); $i++) {
					echo "<br>".get_faecher_name_of_id($faecher[$i]['fid'])." - Nachweis vorhanden: " .($faecher[$i]['nachweis_vorhanden'] == true ? "ja": "nein");
				}
				for($i = 0; $i < count($zeit); $i++) {
					echo "<br>".get_name_of_tag($zeit[$i]['tag'])." von ".$zeit[$i]['anfang']." Uhr bis ".$zeit[$i]['ende']." Uhr";
				}
				?>
				</div>
				<?php
			}
			if(is_array($schueler_lehrer['schueler'])) {
				?>
				<div style="padding-left: 10%;">
				<?php
				$schueler = new schueler($person->id);
				$schueler->load_schueler_pid();
				echo "<br><br>$person->vname $person->nname hat sich als Nachhilfeschüler angemeldet:";
				echo "<br>Klasse: ".$schueler->get_klassenstufe();
				if(is_numeric($schueler->get_klasse())) {
					echo "/";
				}
				echo $schueler->get_klasse();
				echo $schueler->get_id();
				echo "<br>Klassenlehrer/in: ".$schueler->get_klassenlehrer();
				echo "<br>Fächer, in denen er/sie Nachhilfe benötigt:";
				$faecher = $schueler->get_nachfrage_faecher();
				$zeit = $schueler->get_zeit();
				for($i = 0; $i < count($faecher); $i++) {
					echo "<br>".get_faecher_name_of_id($faecher[$i]['fid'])." - Langfristig: " .($faecher[$i]['langfristig'] == true ? "ja": "nein");
				}
				for($i = 0; $i < count($zeit); $i++) {
					echo "<br>".get_name_of_tag($zeit[$i]['tag'])." von ".$zeit[$i]['anfang']." Uhr bis ".$zeit[$i]['ende']." Uhr";
				}
				if(strlen($schueler->get_comment()) > 0) {
					echo "<br>Kommentar: ".$schueler->get_comment();
				}
				$schueler->get_lehrer($faecher[0]['fid']);
				?>
				</div>
				<?php
			}
			?>
		</div>
		<?php 
		if($user->isuserallowed('k')) {?>
		<div style="width: 30%;">
			<a href="index.php?page=change&person=<?php echo $person->id;?>" class="links">Ändere die Daten</a>
		</div>	
		<?php }?>
		</div>
</fieldset>
<?php
			$result = $return->fetch();
		}
	} else {
		echo "Es wurde noch keine Person hinzugefügt";
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
