<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Eingabe</h2>";
	$show_formular_schueler = true;
	$show_formular_lehrer = false;
	if (isset($_GET['lehrer']) && $_GET['lehrer'] == 1) {
		$show_formular_lehrer = true;
		$show_formular_schueler = false;
	}else if (isset($_GET['schueler']) && $_GET['schueler'] == 1) {
		$show_formular_lehrer = false;
		$show_formular_schueler = true;
	}
	if (isset($_GET['input']) && $_GET['input'] == 1) {
		require 'includes/class_schueler.php';
		require 'includes/class_person.php';
		require 'includes/class_lehrer.php';
		$schueler_array = array(
				'klassenlehrer_name' => $_POST['klassenlehrer'], 
				'klasse' => $_POST['klasse'], 
				'klassenstufe' => $_POST['klassenstufe'], 
				'comment' => $_POST['comment']
		);
		$schueler = new schueler($_POST['person']);
		if (!$schueler->add_schueler($schueler_array)) {
			die();
		}
		if (isset($_POST['fach'])) {
			for ($i = 1; $i <= count($_POST['fach']); $i++) {
				$schueler->add_nachfrage_fach($_POST['fach'][$i]['id'], true, $_POST['fach'][$i]['fachlehrer'], 'neu');
			}
		}
		if (isset($_POST['zeit'])) {
			for ($i = 1; $i <= count($_POST['zeit']); $i++) {
				$schueler->add_time($_POST['zeit'][$i]);
			}
		}
		$show_formular_schueler = false;
	}
	if (isset($_GET['input']) && $_GET['input'] == 2) {
		require 'includes/class_person.php';
		require 'includes/class_lehrer.php';
		$lehrer_array = array(
				'klassenlehrer_name' => $_POST['klassenlehrer'], 
				'klasse' => $_POST['klasse'], 
				'klassenstufe' => $_POST['klassenstufe'], 
				'comment' => $_POST['comment']
		);
		$lehrer = new lehrer($_POST['person']);
		echo $lehrer->get_id();
		if (!$lehrer->add_lehrer($lehrer_array)) {
			die();
		}
		for ($i = 1; $i <= count($_POST['fach']); $i++) {
			$lehrer->add_angebot_fach($_POST['fach'][$i]['id'], $_POST['fach'][$i]['nachweis'], $_POST['fach'][$i]['fachlehrer'], $_POST['fach'][$i]['notenschnitt'], 'neu');
		}
		for ($i = 1; $i <= count($_POST['zeit']); $i++) {
			$lehrer->add_time($_POST['zeit'][$i]);
		}
		$show_formular_lehrer = false;
		$show_formular_schueler = false;
	}
	
	if ($show_formular_schueler || $show_formular_lehrer) {
		$return = query_db("SELECT * FROM `person` ORDER BY `person`.`nname` ASC");
		if ($return) {
			?>
<script src="includes/jquery/jquery-ui-timepicker/jquery.ui.timepicker.js?v=0.3.3"></script>
<link rel="stylesheet" href="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="includes/jquery/jquery-ui-timepicker/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />
<script type="text/javascript" src="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/jquery.ui.widget.min.js"></script>
<script type="text/javascript" src="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/jquery.ui.tabs.min.js"></script>
<script type="text/javascript" src="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/jquery.ui.position.min.js"></script>

<script type="text/javascript">
var fachzahl = 0;
var zeitzahl = 0;

$( function() {
	$('#add-subject').click(function() {
		fachzahl++;
		$('#insert-subject').append('<div style="width: 38%; display: inline-block; margin-right: 8%;padding: 10px; border: solid 1px grey; margin-top: 10px;">\
				<h3>' + fachzahl +'.Fach:</h3><select name="fach['+ fachzahl + '][id]" required>\
				<?php	$faecher = get_faecher_all(); for($i = 0; $i < count($faecher); $i++) { echo "<option value=" . $faecher[$i]['id'] . ">" . $faecher[$i]['name'] . "</option>"; } ?>\
				</select><br><br>Fachlehrer:<br><input type="text" class="input_text" maxlength="49" name="fach['+ fachzahl + '][fachlehrer]" style="width: 95%;"><br>\
				<?php
					if ($show_formular_lehrer) {
						echo "Notenschnitt:<br><input class=\"textinput input_text\" type=\"text\" name=\"fach['+ fachzahl +'][notenschnitt]\">";
						echo "<br>Empfehlungsschreiben vom Fachlehrer vorhanden?";
						echo "<br><input type=\"radio\" name=\"fach['+ fachzahl + '][nachweis]\" value=\"true\">Ja";
						echo "<input type=\"radio\" required name=\"fach['+ fachzahl + '][nachweis]\" value=\"false\" style=\"margin-left: 20%;\">Nein";
					}
					?>
				</div>');
	});
	$('#add-time').click(function() {
		zeitzahl++;
		$('#insert-time').append('<select name="zeit['+ zeitzahl + '][tag]"><option value="mo">Montag</option><option value="di">Dienstag</option>\
				<option value="mi">Mittwoch</option><option value="do">Donnerstag</option>\
				<option value="fr">Freitag</option>\
				</select><br>\
				<br>Von: \
				<input type="text" class="timepickervon input_text" name="zeit['+zeitzahl + '][from]" value="13:00">\
				     Bis: \
			 	<input type="text" class="timepickerbis input_text" name="zeit['+zeitzahl + '][until]" value="14:00">\
				<br><br><br><br>');
	});
});

/*
$("form").submit(function (event) {
	if( typeof 
}
*/
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

$( function() {
    $("#vorlage").click(function() {
        $('input[name=klassenstufe]').val("10");
		$('[name=klasse]').val("c");
		$('[name=klassenlehrer]').val("Herr Loitsch");
		$('[name=fach[1][fachlehrer]]').val("max.mustermann@yahoo.de");
		$("#telefon").val("03528778899");
    });
  } );


</script>
<div class="formular_class">
	<form action="?page=input&input=<?php if($show_formular_schueler){echo "1";}if($show_formular_lehrer){echo "2";}?>" method="POST">
		<fieldset style="padding: 40px;">
			<legend>
				<b><?php if($show_formular_schueler){echo "Nachhilfeschüler";}if($show_formular_lehrer){echo "Nachhilfelehrer";}?></b>
			</legend>
			<?php
			if (isset($GLOBAL_CONFIG['system']) && $GLOBAL_CONFIG['system'] = "test") {
					echo '<button type="button" id="vorlage" class="mybuttons">Vorlage</button><br><br>';
			}
			?>
			<br>
			<select name="person">
			<?php
			$person_db = $return->fetch();
			while ($person_db) {
				if (isset($_GET['pid']) && $_GET['pid'] == $person_db['id']) {
					echo "<option value=\"" . $person_db['id'] . "\" selected >" . $person_db['vname'] . " " . $person_db['nname'] . "</option>";
				}else {
					echo "<option value=\"" . $person_db['id'] . "\" >" . $person_db['vname'] . " " . $person_db['nname'] . "</option>";
				}
				$person_db = $return->fetch();
			}
			?>
			</select>
			<br>
			<br>
			<br>
			Klassenstufe (5-12):
			<span style="float: right; width: 50%;">Klasse/Kurs (a, b, c, d, L, L1, L2):</span>
			<br>
			<input type="number" name="klassenstufe" min="5" max="12" required style="width: 40%;" class="input_text">
			<input type="text" pattern="([ABCDabcdlL1234567]|[lL][12])" name="klasse" required style="width: 49%; float: right; margin-right: 5px; margin-left: 0;" class="input_text">
			<br>
			<br>
			Klassenlehrer:
			<br>
			<input type="text" maxlength="49" name="klassenlehrer" class="input_text" style="width: 40%;">
			<br>
			<br>
			<input type="button" value="Füge Fach hinzu" id="add-subject" class="mybuttons">
			<div id="insert-subject">
				<br>
			</div>
			<br>
			<br>
			<h3>Zeit:</h3>
			<input type="button" value="Füge Zeit hinzu" id="add-time" class="mybuttons">
			<br>
			<br>
			<div id="insert-time"></div>
			<br>
			Kommentar:
			<textarea rows="4" name="comment" style="width: 100%; margin-top: 10px;"></textarea>
			<br>
			<br>
			<br>
			<br>
			<input type="submit" value="Hinzufügen" style="float: right;" class="mybuttons">
		</fieldset>
	</form>
</div>
<?php
		}
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
