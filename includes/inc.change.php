<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Ändern der Daten</h2>";
	if (isset($_GET['change']) && ($_GET['change'] == 1 || $_GET['change'] == 2 || $_GET['change'] == 3)) {
		if ($_GET['change'] == 1) {
			require 'includes/class_person.php';
			$person = new person();
			$return = $person->load_person($_POST['id']);
			if ($return == false) {
				echo "Diese Person existiert nicht.";
				die();
			}
			if ($person->change_person($_POST['vname'], $_POST['nname'], $_POST['email'], $_POST['telefon'], $_POST['geb'])) {
				echo "Die Daten wurden erfolgreich geändert!";
			} else {
				echo "Es ist ein Fehler aufgetreten!";
			}
		}
		if($_GET['change'] == 2 || $_GET['change'] == 3) {
			echo "Dies funktioniert noch nicht!";
			die();
		}
	}
	if (isset($_GET['person'])) {
		require 'includes/class_person.php';
		$person = new person();
		$return = $person->load_person($_GET['person']);
		if (!$return) {
			echo "Ein Fehler ist aufgetreten";
			die();
		}
		?>
<div class="formular_class">
	<form action="index.php?page=change&change=1" method="POST" novalidate="novalidate">
		<fieldset style="padding: 40px;">
			<legend>
				<b>Person</b>
			</legend>
			<br>
			<input type="hidden" value="<?php echo $_GET['person'];?>" name="id">
			Vorname:
			<span style="float: right; width: 50%;">Nachname:</span>
			<br>
			<input type="text" maxlength="49" name="vname" autofocus required style="width: 40%;" value=<?php echo $person->vname;?>>
			<input type="text" maxlength="49" name="nname" required style="width: 49%; float: right; margin-right: 5px; margin-left: 0;" value=<?php echo $person->nname;?>>
			<br>
			<br>
			Geburtstag
			<br>
			<input type="text" id="datepicker" name="geb" value=<?php echo $person->geburtstag;?>>
			<script>
		$( function() {
			$( "#datepicker" ).datepicker({
				changeYear: true,
				yearRange: "c-20:c-10",
			});
		} );
			</script>

			<br>
			<br>
			Email:
			<br>
			<input type="email" class="textinput" maxlength="49" name="email" value=<?php echo $person->email;?>>
			<br>
			<br>
			Telefon
			<br>
			<input type="tel" name="telefon" value=<?php echo $person->telefon;?>>
			<br>
			<br>
			<input type="submit" value="Ändern">
		</fieldset>
	</form>
</div>
<?php
	}
	if (isset($_GET['schueler']) || isset($_GET['lehrer'])) {
		require 'includes/class_person.php';
		require 'includes/class_lehrer.php';
		require 'includes/class_schueler.php';
		if (isset($_GET['schueler'])) {
			$sl = new schueler(-1, $_GET['schueler']);
		}
		if (isset($_GET['lehrer'])) {
			$sl = new lehrer(-1, $_GET['lehrer']);
		}
		?>
<script src="includes/jquery/jquery-ui-timepicker/jquery.ui.timepicker.js?v=0.3.3"></script>
<link rel="stylesheet" href="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="includes/jquery/jquery-ui-timepicker/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />
<script type="text/javascript" src="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/jquery.ui.widget.min.js"></script>
<script type="text/javascript" src="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/jquery.ui.tabs.min.js"></script>
<script type="text/javascript" src="includes/jquery/jquery-ui-timepicker/include/ui-1.10.0/jquery.ui.position.min.js"></script>
<script type="text/javascript">
		var fachzahl = <?php echo count($sl->faecher);?>;
		var zeitzahl = <?php echo count($sl->zeit);?>;
		
		function addfach() {
			fachzahl++;
			var doc = document.createDocumentFragment();
			var element = document.createElement('div');
			element.innerHTML = '<div style="width: 38%; display: inline-block; margin-right: 8%;padding: 10px; border: solid 1px grey;">\
		<h3>' + fachzahl +'.Fach:</h3><select name="fach['+ fachzahl + '][id]" required>\
		<?php	$faecher = get_faecher_all(); for($i = 0; $i < count($faecher); $i++) { echo "<option value=" . $faecher[$i]['id'] . ">" . $faecher[$i]['name'] . "</option>"; } ?>\
		</select><br><br>Fachlehrer:<br><input type="text" class="textinput" maxlength="49" name="fach['+ fachzahl + '][fachlehrer]"><br>\
		<?php
		if (isset($_GET['lehrer'])) {
			echo "Notenschnitt:<br><input class=\"textinput\" type=\"text\" name=\"fach['+ fachzahl +'][notenschnitt]\">";
			echo "<br>Empfehlungsschreiben vom Fachlehrer vorhanden?";
			echo "<br><input type=\"radio\" name=\"fach['+ fachzahl + '][nachweis]\" value=\"true\">Ja";
			echo "<input type=\"radio\" name=\"fach['+ fachzahl + '][nachweis]\" value=\"false\" style=\"margin-left: 20%;\">Nein";
		}
		?>
		</div>';
			while(element.firstChild) {
				doc.appendChild(element.firstChild);
			}
			document.getElementById("insertfach").appendChild(doc);
		}
		
		function addtime() {
			var doc = document.createDocumentFragment();
			var element = document.createElement('div');
			zeitzahl++;
			element.innerHTML = '<select name="zeit['+ zeitzahl + '][tag]"><option value="mo">Montag</option><option value="di">Dienstag</option>\
	<option value="mi">Mittwoch</option><option value="do">Donnerstag</option>\
	<option value="fr">Freitag</option>\
	</select><br>\
	<br>Von: \
	<input type="text" class="timepickervon" name="zeit['+zeitzahl + '][from]" value="13:00">\
	     Bis: \
 	<input type="text" class="timepickerbis" name="zeit['+zeitzahl + '][until]" value="14:00">\
	<br><br><br><br>';
			while(element.firstChild) {
				doc.appendChild(element.firstChild);
			}
			document.getElementById("insertzeit").appendChild(doc);
		}
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
		
				</script>
<div class="formular_class">
	<form action="?page=input&input=<?php if(isset($_GET['schueler'])){echo "1";}if(isset($_GET['lehrer'])){echo "2";}?>" method="POST">
		<fieldset style="padding: 40px;">
			<legend>
				<b><?php if(isset($_GET['schueler'])){echo "Nachhilfeschüler";}if(isset($_GET['lehrer'])){echo "Nachhilfelehrer";}?></b>
			</legend>
			<p>
				Ändere Daten von:
				<b><?php echo $sl->person->vname.' '.$sl->person->nname;?></b>
			</p>
			Klassenstufe (5-12):
			<span style="float: right; width: 50%;">Klasse/Kurs (a, b, c, d, L, L1, L2):</span>
			<br>
			<input type="number" name="klassenstufe" min="5" max="12" required style="width: 40%;" value="<?php echo $sl->get_klassenstufe();?>">
			<input type="text" pattern="([ABCDabcdlL123456]|[lL][12])" name="klasse" required style="width: 49%; float: right; margin-right: 5px; margin-left: 0;" value="<?php echo $sl->get_klasse();?>">
			<br>
			<br>
			Klassenlehrer:
			<br>
			<input type="text" class="textinput" maxlength="49" name="klassenlehrer" required value="<?php echo $sl->get_klassenlehrer();?>">
			<br>
			<br>
			<input type="button" value="Füge Fach hinzu" onclick="addfach()">
			<div id="insertfach">
				<br>
			<?php
			var_dump($sl);
		for($i = 0; $i < count($sl->faecher); $i++) {
			echo "<div style=\"width: 38%; display: inline-block; margin-right: 8%;padding: 10px; border: solid 1px grey;\">
			<h3>" . ($i + 1) . ".Fach:</h3><select name=\"fach[" . ($i + 1) . "][id]\" required>";
			$faecher = get_faecher_all();
			for($ii = 0; $ii < count($faecher); $ii++) {
				if ($faecher[$ii]['id'] == intval($sl->faecher[$i]['fid'])) {
					echo "<option value=\"" . $faecher[$ii]['id'] . "\" selected> " . $faecher[$ii]['name'] . "</option>";
				} else {
					echo "<option value=\"" . $faecher[$ii]['id'] . "\"> " . $faecher[$ii]['name'] . "</option>";
				}
			}
			echo "</select><br><br>Fachlehrer:<br><input type=\"text\" class=\"textinput\" maxlength=\"49\" name=\"fach[". $i ."][fachlehrer]\" value=\"" . $sl->faecher[$i]['fachlehrer'] . "\"><br>";
			if (isset($_GET['lehrer'])) {
				echo "Notenschnitt:<br><input class=\"textinput\" type=\"text\" name=\"fach['+ $i +'][notenschnitt]\" value=\"" . $sl->faecher[$i]['notenschnitt'] . "\">";
				echo "<br>Empfehlungsschreiben vom Fachlehrer vorhanden?";
				if ($sl->faecher[$i]['nachweis_vorhanden']) {
					echo "<br><input type=\"radio\" name=\"fach['+ fachzahl + '][nachweis]\" checked value=\"true\">Ja";
					echo "<input type=\"radio\" name=\"fach['+ fachzahl + '][nachweis]\" value=\"false\" style=\"margin-left: 20%;\">Nein";
				} else {
					echo "<br><input type=\"radio\" name=\"fach['+ fachzahl + '][nachweis]\" value=\"true\">Ja";
					echo "<input type=\"radio\" name=\"fach['+ fachzahl + '][nachweis]\" value=\"false\" checked style=\"margin-left: 20%;\">Nein";
				}
			}
			echo "</div>";
		}
		?>
			</div>
			<br>
			<br>
			<h3>Zeit:</h3>
			<input type="button" value="Füge Zeit hinzu" onclick="addtime()">
			<br>
			<br>
			<div id="insertzeit">
			<?php
		for($i = 0; $i < count($sl->zeit); $i++) {
			echo "<select name=\"zeit[' + $i + '][tag]\">";
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
				if ($sl->zeit[$i]['tag'] == $tagekuerzel[$j]) {
					echo "<option value=\"" . $tagekuerzel[$j] . "\" selected >" . $tage[$j] . "</option>";
				} else {
					echo "<option value=\"" . $tagekuerzel[$j] . "\">" . $tage[$j] . "</option>";
				}
			}
			echo "</select><br>
			<br>Von: 
			<input type=\"text\" class=\"timepickervon\" name=\"zeit[". $i . "][from]\" value=\"" . date("H:i", strtotime($sl->zeit[$i]['anfang'])) . "\">
	    	 Bis: 
		 	<input type=\"text\" class=\"timepickerbis\" name=\"zeit[". $i ."][until]\" value=\"" . date("H:i", strtotime($sl->zeit[$i]['ende'])) . "\">
			<br><br><br><br>";
		}
		?>
			</div>
			<br>
			Kommentar:
			<textarea rows="4" name="comment" style="width: 100%; margin-top: 10px;"><?php echo $sl->get_comment();?></textarea>
			<br>
			<br>
			<br>
			<br>
			<input type="submit" value="Hinzufügen" style="float: right;">
		</fieldset>
	</form>
</div>
<?php
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
