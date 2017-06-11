<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Erstellen eines Nachhilfepaares</h2>";
	if (isset($_GET['schueler']) && isset($_GET['fid'])) {
		require 'includes/class_schueler.php';
		$schueler = new schueler(-1, $_GET['schueler']);
		echo "Suche Nachhilfelehrer für: ".$schueler->person->vname." ".$schueler->person->nname." für das Fach <i>".get_faecher_name_of_id($_GET['fid'])."</i>";
		$schueler->get_lehrer($_GET['fid']);
	}
	if (isset($_GET['manuell']) && $_GET['manuell'] == 1) {
		$lehrer_ex = explode('-', $_POST['lehrer']);
		$schueler_ex = explode('-', $_POST['schueler']);
		if ($lehrer_ex[1] != $schueler_ex[1]) {
			echo "Unterschiedliche Fächer wurden gewählt!";
		}
		var_dump($lehrer_ex);
		$return = query_db("INSERT INTO `unterricht` (lid, sid, fid, treff_zeit, treff_raum) VALUES (:lid, :sid, :fid, :treff_zeit, :treff_raum)", $lehrer_ex[0], $schueler_ex[0], $schueler_ex[1], $_POST['zeit']['from'], $_POST['raum']);
	}
	if (!isset($_GET['schueler']) && !isset($_GET['fid'])) {
		echo "<p>Wenn du den passenden Lehrer automatisch finden willst, dann gehe bitte über die Seite <i>Ausgeben der Schüler</i> und wähle dann den Schüler an.</p>";
		echo "<p>Hier kannst du Schüler und Lehrer per Hand zusammenfügen</p>";
		$return = query_db("SELECT * FROM `schueler` WHERE schuljahr = :schuljahr", get_current_year());
		if ($return !== false) {
			$schueler = $return->fetch();
			?>
<script src="includes/jquery/jquery-ui-timepicker/jquery.ui.timepicker.js?v=0.3.3"></script>
<link rel="stylesheet" href="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="includes/jquery/jquery-ui-timepicker/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />
<script type="text/javascript" src="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/jquery.ui.widget.min.js"></script>
<script type="text/javascript" src="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/jquery.ui.tabs.min.js"></script>
<script type="text/javascript" src="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/jquery.ui.position.min.js"></script>
<script type="text/javascript">
		$('body').on('focus','.timepickervon', function(){
    $(this).timepicker({
		showPeriodLabels: false,
		hourText: "Stunden",
		minuteText: "Minuten",
		hours: {
			starts: 11,
			ends: 17,
		},
		minutes: {
			starts: 0,
			interval: 15,
			ends: 45
		},
		rows: 2,
		defaultTime: '13:00'
    });
});

$('body').on('focus','.timepickerbis', function(){
    $(this).timepicker({
		showPeriodLabels: false,
		hourText: "Stunden",
		minuteText: "Minuten",
		hours: {
			starts: 11,
			ends: 17,
		},
		minutes: {
			starts: 0,
			interval: 15,
			ends: 45
		},
		rows: 2,
		defaultTime: '14:00'
    });
});
</script>
<form action="index.php?page=input_paar&manuell=1" method="post">
	<p>Nachhilfeschüler:</p>
	<select name="schueler">
		<?php
			require 'includes/class_schueler.php';
			while ( $schueler ) {
				$schueler = new schueler(-1, $schueler['id']);
				$faecher = $schueler->get_nachfrage_faecher();
				for($i = 0; $i < count($faecher); $i++) {
					echo "<option value=\"" . $schueler->get_id() . "-" . $faecher[$i]['fid'] . "\">" . $schueler->person->vname . " " . $schueler->person->nname . " - " . get_faecher_name_of_id($faecher[$i]['fid']) . "</option>";
				}
				$schueler = $return->fetch();
			}
			?>
		</select>
	<p>Nachhilfelehrer:</p>
	<select name="lehrer">
		<?php
			$return = query_db("SELECT * FROM `lehrer` WHERE schuljahr = :schuljahr", get_current_year());
			if ($return !== false) {
				require 'includes/class_lehrer.php';
				$lehrer = $return->fetch();
				while ( $lehrer ) {
					$lehrer = new lehrer(-1, $lehrer['id']);
					$faecher = $lehrer->get_angebot_faecher();
					var_dump($lehrer);
					for($i = 0; $i < count($faecher); $i++) {
						echo "<option value=\"" . $lehrer->get_id() . "-" . $faecher[$i]['fid'] . "\">" . $lehrer->person->vname . " " . $lehrer->person->nname . " - " . get_faecher_name_of_id($faecher[$i]['fid']) . "</option>";
					}
					$lehrer = $return->fetch();
				}
			}
			?>
		</select>
	<p>Zeitpunkt:</p>
	<select name="zeit[tag]">
		<option value="mo">Montag</option>
		<option value="di">Dienstag</option>
		<option value="mi">Mittwoch</option>
		<option value="do">Donnerstag</option>
		<option value="fr">Freitag</option>
	</select>
	<br>
	<br>
	Von:
	<input type="text" class="timepickervon" name="zeit[from]" value="13:00">
	Bis:
	<input type="text" class="timepickerbis" name="zeit[until]" value="14:00">
	<br>
	<br>
	<br>
	<br>
	<p>Raum:</p>
	<input type="text" name="raum">
	<p></p>
	<input type="submit" value="Erstellen" style="float: right;">
	<br>
	<br>
</form>
<?php
		}
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
