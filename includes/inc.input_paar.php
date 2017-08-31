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
		$return = query_db("SELECT * FROM `unterricht` WHERE `lid` = :lid AND `sid` = :sid AND `fid` = :fid", $lehrer_ex[0], $schueler_ex[0], $schueler_ex[1]);
		$result = $return->fetch();
		if($result !== false) {
			echo "Das Paar existiert schon!";
		}else{
			$return = query_db("UPDATE `fragt_nach` SET `status` = 'vermittelt' WHERE sid = :sid AND fid = :fid", $schueler_ex[0], $schueler_ex[1]);
			$return = query_db("INSERT INTO `unterricht` (lid, sid, fid, tag, treff_zeit, treff_zeit_ende, treff_raum) VALUES (:lid, :sid, :fid, :tag, :treff_zeit, :treff_zeit_ende, :treff_raum)", $lehrer_ex[0], $schueler_ex[0], $schueler_ex[1],$_POST['zeit']['tag'], $_POST['zeit']['from'], $_POST['zeit']['until'], intval($_POST['raum']));
			if($return !== false) {
				echo "Das Paar wurde erfolgreich hinzugefügt";
			}
		}
	}
	if (!isset($_GET['schueler']) && !isset($_GET['manuell'])) {
		if(!isset($_GET['control_paar'])) {
			echo "<p>Wenn du den passenden Lehrer automatisch finden willst, dann gehe bitte über die Seite <a href=\"index.php?page=output&schueler=1\" class=\"links2\">Ausgeben der Schüler</a> und wähle dann den Schüler an.</p>";
			echo "<p>Hier kannst du Schüler und Lehrer per Hand zusammenfügen</p>";
		}else{
			echo "<p>Bitte überprüfe nochmal die Angaben. Wenn es mehrer mögliche Lehrer für den Schüler gab, dann kann es sein, dass das Programm nicht die beste Möglichkeit findet. Da es immer nur für einen Schüler den Lehrer sucht und nicht die beste Möglichkeit für alle Schüler findet.</p>";
		}
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
	<label>Nachhilfeschüler:</label>
	<select name="schueler">
		<?php
			require 'includes/class_schueler.php';
			while ( $schueler ) {
				$schueler = new schueler(-1, $schueler['id']);
				$faecher = $schueler->get_nachfrage_faecher();
				for($i = 0; $i < count($faecher); $i++) {
					if(isset($_GET['control_paar']) && isset($_GET['sid']) && $_GET['sid'] == $schueler->get_id()) {
						echo "<option value=\"" . $schueler->get_id() . "-" . $faecher[$i]['fid'] . "\" selected>" . $schueler->person->vname . " " . $schueler->person->nname . " - " . get_faecher_name_of_id($faecher[$i]['fid']) . "</option>";
					}else{
						echo "<option value=\"" . $schueler->get_id() . "-" . $faecher[$i]['fid'] . "\">" . $schueler->person->vname . " " . $schueler->person->nname . " - " . get_faecher_name_of_id($faecher[$i]['fid']) . "</option>";
					}
				}
				$schueler = $return->fetch();
			}
			?>
		</select><br><br>
	<label>Nachhilfelehrer:</label>
	<select name="lehrer">
		<?php
			$return = query_db("SELECT * FROM `lehrer` WHERE schuljahr = :schuljahr", get_current_year());
			if ($return !== false) {
				require 'includes/class_lehrer.php';
				$lehrer = $return->fetch();
				while ( $lehrer ) {
					$lehrer = new lehrer(-1, $lehrer['id']);
					$faecher = $lehrer->get_angebot_faecher();
//					var_dump($lehrer);
					for($i = 0; $i < count($faecher); $i++) {
						if(isset($_GET['control_paar']) && isset($_GET['lid']) && $_GET['lid'] == $lehrer->get_id()) {
							echo "<option value=\"" . $lehrer->get_id() . "-" . $faecher[$i]['fid'] . "\" selected >" . $lehrer->person->vname . " " . $lehrer->person->nname . " - " . get_faecher_name_of_id($faecher[$i]['fid']) . "</option>";
						}else{
							echo "<option value=\"" . $lehrer->get_id() . "-" . $faecher[$i]['fid'] . "\">" . $lehrer->person->vname . " " . $lehrer->person->nname . " - " . get_faecher_name_of_id($faecher[$i]['fid']) . "</option>";
						}
					}
					$lehrer = $return->fetch();
				}
			}
			?>
		</select><br><br>
	<label>Zeitpunkt:</label>
	<select name="zeit[tag]">
	<?php 
	$tagekuerzel = array(
			"mo",
			"di",
			"mi",
			"do",
			"fr"
	);
	$tage = array(
			"Montag",
			"Dienstag",
			"Mittwoch",
			"Donnerstag",
			"Freitag"
	);
	for($j = 0; $j < count($tagekuerzel); $j++) {
		if (isset($_GET['tag']) && $_GET['tag'] == $tagekuerzel[$j]) {
			echo "<option value=\"" . $tagekuerzel[$j] . "\" selected >" . $tage[$j] . "</option>";
		} else {
			echo "<option value=\"" . $tagekuerzel[$j] . "\">" . $tage[$j] . "</option>";
		}
	}
	?>
	</select>
	<br>
	<br>
	Von:
	<input type="text" class="timepickervon input_text" name="zeit[from]" value="<?php if(isset($_GET['anfang'])){echo $_GET['anfang'];}else{echo "13:00";}?>">
	Bis:
	<input type="text" class="timepickerbis input_text" name="zeit[until]" value="<?php if(isset($_GET['ende'])){echo $_GET['ende'];}else{echo "14:00";}?>">
	<br>
	<br>
	<label>Raum:</label><br>
	<input type="text" name="raum" class="input_text">
	<p></p>
	<input type="submit" value="Erstellen" style="float: right;" class="mybuttons">
	<br>
	<br>
</form>
<?php
		}
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
