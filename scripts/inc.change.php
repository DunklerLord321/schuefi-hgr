<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Ändern der Daten</h2>";
	if (isset($_GET['change']) && ($_GET['change'] == 1 || $_GET['change'] == 2 || $_GET['change'] == 3 || $_GET['change'] == 4)) {
		if ($_GET['change'] == 1) {
			require 'includes/class_person.php';
			$person = new person();
			$return = $person->load_person($_POST['id']);
			if ($return == false) {
				echo "Diese Person existiert nicht.";
				die();
			}
			if (isset($_POST['login'])) {
				$is_person_allowed_to_login = true;
			}else{
				$is_person_allowed_to_login = false;				
			}
			if ($person->change_person($_POST['vname'], $_POST['nname'], $_POST['email'], $_POST['telefon'], $_POST['geb'], $is_person_allowed_to_login)) {
				echo "Die Daten wurden erfolgreich geändert!";
			}else {
				echo "Es ist ein Fehler aufgetreten!";
			}
		}
		if ($_GET['change'] == 2) {
			require 'includes/class_schueler.php';
			$schueler = new schueler(-1, $_POST['id']);
			$params = array(
					'klasse' => $_POST['klasse'],
					'klassenstufe' => $_POST['klassenstufe'],
					'klassenlehrer_name' => $_POST['klassenlehrer'],
					'comment' => $_POST['comment']
			);
			$schueler->change_schueler($params);
			// var_dump($schueler);
			// Ändere Zeit
			// array neu indizieren, falls Eintrag in der Mitte gelöscht wurde (element 1 und 3 existieren, aber count nur 2)
			if (isset($_POST['zeit'])) {
				$_POST['zeit'] = array_values($_POST['zeit']);
			}
			if (!isset($_POST['zeit']) && count($schueler->zeit) != 0) {
				for ($i = 0; $i < count($schueler->zeit); $i++) {
					$schueler->remove_time($schueler->zeit[$i]['id']);
				}
			}
			if (isset($_POST['zeit']) && count($schueler->zeit) != 0) {
				for ($i = 0; $i < count($schueler->zeit); $i++) {
					$schueler->remove_time($schueler->zeit[$i]['id']);
				}
				$schueler->load_schueler_pid(-1);
			}
			if (isset($_POST['zeit']) && count($schueler->zeit) == 0) {
				for ($i = 0; $i <= count($_POST['zeit']); $i++) {
					if (isset($_POST['zeit'][$i])) {
						$schueler->add_time(array(
								'tag' => $_POST['zeit'][$i]['tag'],
								'from' => $_POST['zeit'][$i]['from'],
								'until' => $_POST['zeit'][$i]['until']
						));
					}
				}
			}

			// Ändere Fächer
			$_POST['fach'] = array_values($_POST['fach']);
			if (!isset($_POST['fach']) && count($schueler->faecher) != 0) {
				for ($i = 0; $i < count($schueler->faecher); $i++) {
					$schueler->remove_nachfrage_fach($schueler->faecher[$i]['fid']);
				}
			}
			if (isset($_POST['fach']) && count($schueler->faecher) != 0) {
				for ($i = 0; $i < count($schueler->faecher); $i++) {
					$schueler->remove_nachfrage_fach($schueler->faecher[$i]['fid']);
				}
				$schueler->load_schueler_pid(-1);
			}
			if (isset($_POST['fach']) && count($schueler->faecher) == 0) {
				for ($i = 0; $i <= count($_POST['fach']); $i++) {
					if (isset($_POST['fach'][$i])) {
						$schueler->add_nachfrage_fach($_POST['fach'][$i]['id'], true, $_POST['fach'][$i]['fachlehrer'], $_POST['fach'][$i]['status']);
					}
				}
			}
		}
		if ($_GET['change'] == 3) {
			require 'includes/class_lehrer.php';
			$lehrer = new lehrer(-1, $_POST['id']);
			$params = array(
					'klasse' => $_POST['klasse'],
					'klassenstufe' => $_POST['klassenstufe'],
					'klassenlehrer_name' => $_POST['klassenlehrer'],
					'comment' => $_POST['comment']
			);
			$lehrer->change_lehrer($params);
			// array neu indizieren, falls Eintrag in der Mitte gelöscht wurde (element 1 und 3 existieren, aber count nur 2)
			if (isset($_POST['zeit'])) {
				$_POST['zeit'] = array_values($_POST['zeit']);
			}
			if (!isset($_POST['zeit']) && count($lehrer->zeit) != 0) {
				for ($i = 0; $i < count($lehrer->zeit); $i++) {
					$lehrer->remove_time($lehrer->zeit[$i]['id']);
				}
			}
			if (isset($_POST['zeit']) && count($lehrer->zeit) != 0) {
				for ($i = 0; $i < count($lehrer->zeit); $i++) {
					$lehrer->remove_time($lehrer->zeit[$i]['id']);
				}
				$lehrer->load_lehrer_pid(-1);
			}
			if (isset($_POST['zeit']) && count($lehrer->zeit) == 0) {
				for ($i = 0; $i <= count($_POST['zeit']); $i++) {
					if (isset($_POST['zeit'][$i])) {
						$lehrer->add_time(array(
								'tag' => $_POST['zeit'][$i]['tag'],
								'from' => $_POST['zeit'][$i]['from'],
								'until' => $_POST['zeit'][$i]['until']
						));
					}
				}
			}

			// Ändere Fächer
			$_POST['fach'] = array_values($_POST['fach']);
			if (!isset($_POST['fach']) && count($lehrer->faecher) != 0) {
				for ($i = 0; $i < count($lehrer->faecher); $i++) {
					$lehrer->remove_angebot_fach($lehrer->faecher[$i]['fid']);
				}
			}
			if (isset($_POST['fach']) && count($lehrer->faecher) != 0) {
				for ($i = 0; $i < count($lehrer->faecher); $i++) {
					$lehrer->remove_angebot_fach($lehrer->faecher[$i]['fid']);
				}
				$lehrer->load_lehrer_pid(-1);
			}
			if (isset($_POST['fach']) && count($lehrer->faecher) == 0) {
				for ($i = 0; $i <= count($_POST['fach']); $i++) {
					if (isset($_POST['fach'][$i])) {
						$lehrer->add_angebot_fach($_POST['fach'][$i]['id'], true, $_POST['fach'][$i]['fachlehrer'], $_POST['fach'][$i]['notenschnitt'], $_POST['fach'][$i]['status']);
					}
				}
			}
			echo "Die Daten des Lehrers wurden erfolgreich geändert.";
		}
		if ($_GET['change'] == 4) {
			var_dump($_POST);
			if (isset($_POST['ridraum']) && $_POST['ridraum'] != -1) {
				$return = query_db("UPDATE `unterricht` SET tag = :tag, treff_zeit = :treff_zeit, treff_zeit_ende = :treff_zeit_ende, treff_raum = :treff_raum, `rid` = :rid WHERE id = :pid",
						$_POST['zeit']['tag'], $_POST['zeit']['from'], $_POST['zeit']['until'], $_POST['raum'], $_POST['ridraum'], $_POST['paar_id']);
			}else{
				$return = query_db("UPDATE `unterricht` SET tag = :tag, treff_zeit = :treff_zeit, treff_zeit_ende = :treff_zeit_ende,
						treff_raum = :treff_raum, `rid` = NULL WHERE id = :pid",
						$_POST['zeit']['tag'], $_POST['zeit']['from'], $_POST['zeit']['until'], $_POST['raum'],
						$_POST['paar_id']);
			}
			if ($return !== false) {
				echo "<br><a href=\"index.php?page=output&paare=1&filter=".$_POST['paar_id']."\" class=\"links2\">Daten des Paares wurden erfolgreich geändert</a>";
			}else{
				echo "Es ist ein Fehler beim Aktualisieren aufgetreten";
			}
		}
		echo "<br> Nach erfolgreichen Änderungen müssen die Vermittlungsdokumente eventuell aktualisiert und dem Paar erneut zugeschickt werden";
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
	<form action="index.php?page=change&change=1" method="POST">
		<fieldset style="padding: 40px;">
			<legend>
				<b>Person</b>
			</legend>
			<br>
			<input type="hidden" value="<?php echo $person->id;?>" name="id">
			Vorname:
			<span style="float: right; width: 50%;">Nachname:</span>
			<br>
			<input type="text" maxlength="49" name="vname" autofocus required style="width: 40%;" value="<?php echo $person->vname;?>" class="input_text">
			<input type="text" maxlength="49" name="nname" required style="width: 49%; float: right; margin-right: 5px; margin-left: 0;" value="<?php echo $person->nname;?>" class="input_text">
			<br>
			<br>
			Geburtstag
			<br>
			<input type="text" id="datepicker" name="geb" value="<?php echo $person->geburtstag;?>" class="input_text">
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
			<input type="email" maxlength="49" name="email" value="<?php echo $person->email;?>" class="input_text" style="width: 40%;">
			<br>
			<br>
			Telefon
			<br>
			<input type="tel" name="telefon" value="<?php echo $person->telefon;?>" class="input_text">
			<br>
			<br>
			Möglichkeit zum Login:
			<input type="checkbox" name="login" <?php echo ($person->user->has_reference_to_person($person->id)?"checked":""); ?> class="input_text">
			<?php if ($person->user->exists($person->email)) {echo "Diese Person hat schon einen Login!";} ?>
			<br>
			<br>
			<input type="submit" value="Ändern" class="mybuttons">
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
			element.innerHTML = '<div id="faecherdiv-'+ fachzahl+'" style="width: 38%; display: inline-block; margin-right: 8%;padding: 10px; border: solid 1px grey;">\
		<h3>' + fachzahl +'.Fach:</h3><select name="fach['+ fachzahl + '][id]" required>\
		<?php	$faecher = all_subjects(); for($i = 0; $i < count($faecher); $i++) { echo "<option value=" . $faecher[$i]['id'] . ">" . $faecher[$i]['name'] . "</option>"; } ?>\
		</select><br><br>Fachlehrer:<br><input type="text" class="input_text" maxlength="49" name="fach['+ fachzahl + '][fachlehrer]" style="width: 98%;"><br>\
		<?php
		if (isset($_GET['lehrer'])) {
			echo "Notenschnitt:<br><input class=\"input_text\" type=\"text\" name=\"fach['+ fachzahl +'][notenschnitt]\">";
			echo "<br>Empfehlungsschreiben vom Fachlehrer vorhanden?";
			echo "<br><input type=\"radio\" name=\"fach['+ fachzahl + '][nachweis]\" value=\"true\">Ja";
			echo "<input type=\"radio\" required name=\"fach['+ fachzahl + '][nachweis]\" value=\"false\" style=\"margin-left: 20%;\">Nein";
			echo "<select name=\"fach['+ fachzahl +'][status]\">";
			for ($j = 0; $j < count(lehrer::stati); $j++) {
				echo "<option value=\"" . lehrer::stati[$j] . "\">" . lehrer::stati[$j] . "</option>";
			}
			echo "</select>";
		}else {
			echo "<select name=\"fach['+ fachzahl +'][status]\">";
			for ($j = 0; $j < count(schueler::stati); $j++) {
				echo "<option value=\"" . schueler::stati[$j] . "\">" . schueler::stati[$j] . "</option>";
			}
			echo "</select>";
		}
		echo "<br><br><a class=\"mybuttons\" onClick=\"deletefach(\'faecherdiv-'+ fachzahl +'\')\">Fach löschen</a><br><br>";
		?>
		</div>';
			while(element.firstChild) {
				doc.appendChild(element.firstChild);
			}
			document.getElementById("insertfach").appendChild(doc);
		}

		function deletefach(id) {
			document.getElementById(id).parentNode.removeChild(document.getElementById(id));
			fachzahl--;
		}

		function deletetime(id) {
			document.getElementById(id).parentNode.removeChild(document.getElementById(id));
			zeitzahl--;
		}

		function addtime() {
			var doc = document.createDocumentFragment();
			var element = document.createElement('div');
			zeitzahl++;
			element.innerHTML = '<div id="timediv-'+ zeitzahl+'"><select name="zeit['+ zeitzahl + '][tag]"><option value="mo">Montag</option><option value="di">Dienstag</option>\
	<option value="mi">Mittwoch</option><option value="do">Donnerstag</option>\
	<option value="fr">Freitag</option>\
	</select><br>\
	<br>Von: \
	<input type="text" class="timepickervon input_text" name="zeit['+zeitzahl + '][from]" value="13:00">\
	     Bis: \
 	<input type="text" class="timepickerbis input_text" name="zeit['+zeitzahl + '][until]" value="14:00">\
 	<a class="mybuttons" onclick="deletetime(\'timediv-'+zeitzahl+'\')">Zeit löschen</a>\
	<br><br><br><br></div>';
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
	<?php if(isset($_GET['prev']) && $_GET['prev'] == "next_year") {
		echo "<form action=\"?page=input&prev=next_year&input=".(isset($_GET['schueler']) ?"1":"2")."\" method=\"POST\">";
	}else{
		echo "<form action=\"?page=change&change=".(isset($_GET['schueler']) ?"2":"3")."\" method=\"POST\">";
	}
	?>
		<fieldset style="padding: 40px;">
			<legend>
				<b><?php if(isset($_GET['schueler'])){echo "Nachhilfeschüler";}if(isset($_GET['lehrer'])){echo "Nachhilfelehrer";}?></b>
			</legend>
			<p>
				Ändere Daten von:
				<b><?php echo $sl->person->vname.' '.$sl->person->nname;?></b>
			</p>
			<input type="hidden" value="<?php if(isset($_GET['schueler'])){echo $_GET['schueler'];}if(isset($_GET['lehrer'])){echo $_GET['lehrer'];}?>" name="id">
			<input type="hidden" value="<?php echo $sl->person->id;?>" name="person">
			<?php if(isset($_GET['prev']) && $_GET['prev'] == "next_year") {
				echo "Daten im letzten Schuljahr:".format_klassenstufe_kurs($sl->get_klassenstufe(), $sl->get_klasse());
				echo "<br>Klassenlehrer:".$sl->get_klassenlehrer();
			}?>
			<br><br>Klassenstufe (5-12):
			<span style="float: right; width: 50%;">Klasse/Kurs (a, b, c, d, L, L1, L2):</span>
			<br>
			<input type="number" name="klassenstufe" min="5" max="12" required style="width: 40%;" value="<?php echo $sl->get_klassenstufe();?>" class="input_text">
			<input type="text" name="klasse" required style="width: 49%; float: right; margin-right: 5px; margin-left: 0;" value="<?php echo $sl->get_klasse();?>"
				class="input_text">
			<br>
			<br>
			Klassenlehrer:
			<br>
			<input type="text" class="input_text" maxlength="49" name="klassenlehrer" value="<?php echo $sl->get_klassenlehrer();?>" style="width: 40%;">
			<br>
			<br>
			<input type="button" value="Füge Fach hinzu" onclick="addfach()" class="mybuttons">
			<div id="insertfach">
				<br>
			<?php
		// var_dump($sl);
		for ($i = 0; $i < count($sl->faecher); $i++) {
			echo "<div id=\"faecherdiv-" . ($i + 1) . "\" style=\"width: 38%; display: inline-block; margin-right: 8%;padding: 10px; border: solid 1px grey;\">
			<h3>" . ($i + 1) . ".Fach:</h3><select name=\"fach[" . ($i + 1) . "][id]\" required>";
			$faecher = all_subjects();
			for ($ii = 0; $ii < count($faecher); $ii++) {
				if ($faecher[$ii]['id'] == intval($sl->faecher[$i]['fid'])) {
					echo "<option value=\"" . $faecher[$ii]['id'] . "\" selected> " . $faecher[$ii]['name'] . "</option>";
				}else {
					echo "<option value=\"" . $faecher[$ii]['id'] . "\"> " . $faecher[$ii]['name'] . "</option>";
				}
			}
			echo "</select><br><br>Fachlehrer:<br><input type=\"text\" class=\"input_text\" maxlength=\"49\" name=\"fach[" . ($i + 1) . "][fachlehrer]\" value=\"" . $sl->faecher[$i]['fachlehrer'] . "\"><br>";
			if (isset($_GET['lehrer'])) {
				echo "Notenschnitt:<br><input class=\"input_text\" type=\"text\" name=\"fach[" . ($i + 1) . "][notenschnitt]\" value=\"" . $sl->faecher[$i]['notenschnitt'] . "\">";
				echo "<br>Empfehlungsschreiben vom Fachlehrer vorhanden?";
				if ($sl->faecher[$i]['nachweis_vorhanden']) {
					echo "<br><input type=\"radio\" name=\"fach[" . ($i + 1) . "][nachweis]\" checked value=\"true\">Ja";
					echo "<input type=\"radio\" name=\"fach[" . ($i + 1) . "][nachweis]\" value=\"false\" style=\"margin-left: 20%;\">Nein";
				}else {
					echo "<br><input type=\"radio\" name=\"fach[" . ($i + 1) . "][nachweis]\" value=\"true\">Ja";
					echo "<input type=\"radio\" name=\"fach[" . ($i + 1) . "][nachweis]\" value=\"false\" checked style=\"margin-left: 20%;\">Nein";
				}
				echo "<select name=\"fach[" . ($i + 1) . "][status]\">";
				for ($j = 0; $j < count(lehrer::stati); $j++) {
					if ($sl->faecher[$i]['status'] == lehrer::stati[$j]) {
						echo "<option value=\"" . lehrer::stati[$j] . "\" selected>" . lehrer::stati[$j] . "</option>";
					}else {
						echo "<option value=\"" . lehrer::stati[$j] . "\">" . lehrer::stati[$j] . "</option>";
					}
				}
				echo "</select>";
			}else {
				echo "<select name=\"fach[" . ($i + 1) . "][status]\">";
				for ($j = 0; $j < count(schueler::stati); $j++) {
					if ($sl->faecher[$i]['status'] == schueler::stati[$j]) {
						echo "<option value=\"" . schueler::stati[$j] . "\" selected>" . schueler::stati[$j] . "</option>";
					}else {
						echo "<option value=\"" . schueler::stati[$j] . "\">" . schueler::stati[$j] . "</option>";
					}
				}
				echo "</select>";
			}
			echo "<br><br><a class=\"mybuttons\" onClick=\"document.getElementById('faecherdiv-" . ($i + 1) . "').parentNode.removeChild(document.getElementById('faecherdiv-" . ($i + 1) . "'))\">Fach löschen</a><br><br>";
			echo "</div>";
		}
		?>
			</div>
			<br>
			<br>
			<h3>Zeit:</h3>
			<input type="button" value="Füge Zeit hinzu" onclick="addtime()" class="mybuttons">
			<br>
			<br>
			<div id="insertzeit">
			<?php
		for ($i = 0; $i < count($sl->zeit); $i++) {
			echo "<div id=\"timediv-" . ($i + 1) . "\"><select name=\"zeit[" . ($i + 1) . "][tag]\">";
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
			for ($j = 0; $j < count($tagekuerzel); $j++) {
				if ($sl->zeit[$i]['tag'] == $tagekuerzel[$j]) {
					echo "<option value=\"" . $tagekuerzel[$j] . "\" selected >" . $tage[$j] . "</option>";
				}else {
					echo "<option value=\"" . $tagekuerzel[$j] . "\">" . $tage[$j] . "</option>";
				}
			}
			echo "</select><br>
			<br>Von:
			<input type=\"text\" class=\"timepickervon input_text\" name=\"zeit[" . ($i + 1) . "][from]\" value=\"" . date("H:i", strtotime($sl->zeit[$i]['anfang'])) . "\">
	    	 Bis:
		 	<input type=\"text\" class=\"timepickerbis input_text\" name=\"zeit[" . ($i + 1) . "][until]\" value=\"" . date("H:i", strtotime($sl->zeit[$i]['ende'])) . "\">
	 		<a class=\"mybuttons\" onclick=\"deletetime('timediv-" . ($i + 1) . "')\">Zeit löschen</a>
			<br><br><br><br></div>";
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
			<input type="submit" value="<?php echo (isset($_GET['prev'])?"Übernahme in nächstes Jahr":"Ändern" );?>" style="float: right;" class="mybuttons">
		</fieldset>
	</form>
</div>
<?php
	}
	if (isset($_GET['paar'])) {
		require 'includes/class_paar.php';
		$paar = new paar($_GET['paar']);
		if (!is_object($paar)) {
			echo "Ein Fehler beim Laden des Paares ist aufgetreten";
			die();
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
				<form action="index.php?page=change&change=4" method="post">
				<input type="hidden" name="paar_id" value="<?php echo $paar->paarid?>">
				<label>Nachhilfeschüler:</label> <?php echo $paar->schueler->person->vname." ".$paar->schueler->person->nname?>
			<br>
			<br>
			<label>Nachhilfelehrer:</label> <?php echo $paar->lehrer->person->vname." ".$paar->lehrer->person->nname?>
			<br>
			<br>
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
					for ($j = 0; $j < count($tagekuerzel); $j++) {
						if (isset($paar->tag) && $paar->tag == $tagekuerzel[$j]) {
							echo "<option value=\"" . $tagekuerzel[$j] . "\" selected >" . $tage[$j] . "</option>";
						}else {
							echo "<option value=\"" . $tagekuerzel[$j] . "\">" . $tage[$j] . "</option>";
						}
					}
					?>
			</select>
			<br>
			<br>
			Von:
			<input type="text" class="timepickervon input_text" name="zeit[from]" value="<?php if(isset($paar->anfang)){echo $paar->anfang;}else{echo "13:00";}?>">
			Bis:
			<input type="text" class="timepickerbis input_text" name="zeit[until]" value="<?php if(isset($paar->ende)){echo $paar->ende;}else{echo "14:00";}?>">
			<br>
			<br>
			<?php
			if(!isset($paar->anfang) || !isset($paar->ende) || !isset($paar->tag)) {
				echo "<i>Keine Automatische Zimmersuche möglich!</i><br><br>";
			}else{
				echo "Mit automatischer Zimmersuche vorgeschlagenes Zimmer:";
				echo "<br><br><i>Achtung:</i> Das Vorgeschlagene Zimmer bezieht sich auf die nicht geänderte Uhrzeit. Um sowohl Zeit als auch Zimmer zu ändern bitte in zwei Schritten vorgehen.<br><br>";
				$stunden = get_lesson_for_time($paar->anfang, $paar->ende);
				if(is_array($stunden)) {
					$return = query_db("SELECT raum.*, r.stunde as stunde1, zahlnummer FROM `raum` INNER JOIN (SELECT raum.* FROM raum WHERE stunde = :stunde AND frei = 1 AND tag = :tag) as r on r.nummer = raum.nummer
							LEFT JOIN ( SELECT raum.id, raum.nummer, raum.tag, COUNT(raum.nummer) AS zahlnummer FROM raum INNER JOIN unterricht ON unterricht.rid = raum.id
							GROUP BY raum.id, raum.nummer, raum.tag HAVING raum.tag = :tag) AS rz ON rz.nummer = raum.nummer
							HAVING raum.stunde = :stunde2 AND raum.tag = :tag ", $stunden[0], $paar->tag, $paar->tag, $stunden[1], $paar->tag);
				}else {
					$return = query_db("SELECT raum.*, zahlnummer FROM `raum`
							LEFT JOIN ( SELECT raum.id, raum.nummer, raum.tag, COUNT(raum.nummer) AS zahlnummer FROM raum INNER JOIN unterricht ON unterricht.rid = raum.id
							GROUP BY raum.id, raum.nummer, raum.tag HAVING raum.tag = :tag) AS rz
							ON rz.nummer = raum.nummer
							WHERE raum.tag = :tag AND raum.stunde = :stunde AND raum.frei = 1", $paar->tag, $paar->tag, $stunden);
				}
				if ($return !== false) {
					echo "<select name=\"ridraum\" id=\"selectraum\"><option value=\"-1\">Entweder hier neuen Raum wählen oder in Eingabefeld eintragen</option>";
					$result = $return->fetch();
					while ($result) {
						if (isset($paar->rid) && $paar->rid == $result['id']) {
							echo "<option selected value=\"" . $result['id'] . "\">momentanes Zimmer: ".$result['nummer']." -- " . (isset($result['stunde1'])?$result['stunde1']."./":"") . $result['stunde'] .".Stunde, " . get_name_of_day($paar->tag). "</option>";
						}else{
							echo "<option value=\"" . $result['id'] . "\">Zimmer: ".$result['nummer']." -- " . (isset($result['stunde1'])?$result['stunde1']."./":"") . $result['stunde'] .".Stunde, " . get_name_of_day($paar->tag) .($result['zahlnummer'] != NULL ? $result['zahlnummer']."x belegt an dem Tag zu anderer Zeit. Eventuell sind Überschneidung möglich.":"")."</option>";
						}
						$result = $return->fetch();
					}
					echo "</select><br><br>";
				}
			}
			?>
			<label id="labelraum">momentaner Raum:</label>
			<br>
			<input type="text" name="raum" class="input_text" value="<?php echo $paar->raum?>">
			<p></p>
			<input type="submit" value="Ändern" style="float: right;" class="mybuttons">
			<br>
			<br>
			<script type="text/javascript">
		$(function() {
			$('#selectraum').change(function() {
				if($('#selectraum').val() == -1) {
					$('[name=raum]').show();
					$('#labelraum').show();
				}else{
				$('[name=raum]').hide();
				$('#labelraum').hide();
				}
			});
		});
			</script>
		</form>
		<?php
		// TODO: Daten per JS validieren
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
