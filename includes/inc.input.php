<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Eingabe</h2>";
	$show_formular_schueler = true;
	$show_formular_lehrer = false;
	if (isset($_GET['lehrer']) && $_GET['lehrer'] == 1) {
		$show_formular_lehrer = true;
		$show_formular_schueler = false;
	} else if (isset($_GET['schueler']) && $_GET['schueler'] == 1) {
		$show_formular_lehrer = false;
		$show_formular_schueler = true;
	}
	if (isset($_GET['input']) && ($_GET['input'] == 1 || $_GET['input'] == 2)) {
		require 'includes/class_schueler.php';
		require 'includes/class_person.php';
		$schueler_array= array(
				'klassenlehrer_name' => $_POST['klassenlehrer'],
				'klasse' => $_POST['klasse'],
				'klassenstufe' => $_POST['klassenstufe']
		);
		$schueler = new schueler($_POST['person']);
		$schueler->add_schueler($schueler_array);
		$schueler->add_nachfrage_fach($_POST['fach1'], true);
		$show_formular_schueler = false;
	}
	
	if ($show_formular_schueler) {
		$return = query_db("SELECT * FROM `person`");
		if($return) {
		?>
<div class="formular_class">
	<form action="?page=input&input=1" method="POST" novalidate="novalidate">
		<fieldset style="padding: 40px;">
			<legend>
				<b>Nachhilfeschüler</b>
			</legend>
			<select name="person">
			<?php 
			$person_db = $return->fetch();
			while ($person_db) {
				if(isset($_GET['pid']) && $_GET['pid'] == $person_db['id']) {
					echo "<option value=\"".$person_db['id']."\" selected >".$person_db['vname']." ".$person_db['nname']."</option>";
				}else{
					echo "<option value=\"".$person_db['id']."\" >".$person_db['vname']." ".$person_db['nname']."</option>";
				}
				$person_db = $return->fetch();
			}
			?>
			</select>
			<br>
			<br>
			Klassenstufe:
			<span style="float: right; width: 50%;">Klasse/Kurs:</span>
			<br>
			<input type="number" name="klassenstufe" min="5" max="12" required style="width: 40%;">
			<input type="text" pattern="([ABCDabcdl123456]|[lL][12])" name="klasse" required style="width: 49%; float: right; margin-right: 5px; margin-left: 0;">
			<br>
			<br>
			Klassenlehrer:
			<br>
			<input type="text" class="textinput" maxlength="49" name="klassenlehrer" required>
			<br>
			<br>
			<div style="width: 20%; display: inline-block;">
				<h3>1.Fach:</h3>
				<select name="fach1" required>
				<?php
				$faecher = get_faecher_all();
				var_dump($faecher);
		for($i = 0; $i < count($faecher); $i++) {
			echo "<option value=" . $faecher[$i]['id'] . ">" . $faecher[$i]['name'] . "</option>";
		}
		?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach1_lehrer">
				<br>
			</div>
			<div style="width: 20%; display: inline-block; margin-left: 10%;">
				<h3>2.Fach:</h3>
				<select name="fach2">
				<?php
				$faecher = get_faecher_all();
				var_dump($faecher);
				for($i = 0; $i < count($faecher); $i++) {
					echo "<option value=" . $faecher[$i]['id'] . ">" . $faecher[$i]['name'] . "</option>";
				}
				?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach2_lehrer">
				<br>
			</div>
			<div style="width: 20%; display: inline-block; margin-left: 10%;">
				<h3>3.Fach:</h3>
				<select name="fach3">
				<?php
				$faecher = get_faecher_all();
				var_dump($faecher);
				for($i = 0; $i < count($faecher); $i++) {
					echo "<option value=" . $faecher[$i]['id'] . ">" . $faecher[$i]['name'] . "</option>";
				}
				?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach3_lehrer">
				<br>
			</div>
			<br>
			<br>
			<h3>Zeit:</h3>
			<br>
			<div style="display: flex;">
				<div style="display: inline-block; width: 25%;">
					Montag Anfang:
					<br>
					<input type="time" value="00:00" name="mo_anfang" class="input_time">
					<br>
					Montag Ende:
					<br>
					<input type="time" value="00:00" name="mo_ende" class="input_time">
					<br>
					Dienstag Anfang:
					<br>
					<input type="time" value="00:00" name="di_anfang" class="input_time">
					<br>
					Dienstag Ende:
					<br>
					<input type="time" value="00:00" name="di_ende" class="input_time">
					<br>
				</div>
				<div id="zeitdiv" style="display: inline-block; width: 25%;">
					Mitwoch Anfang:
					<br>
					<input type="time" value="00:00" name="mi_anfang" class="input_time">
					<br>
					Mitwoch Ende:
					<br>
					<input type="time" value="00:00" name="mi_ende" class="input_time">
					<br>
					Donnerstag Anfang
					<br>
					<input type="time" value="00:00" name="do_anfang" class="input_time">
					<br>
					Donnerstag Ende:
					<br>
					<input type="time" value="00:00" name="do_ende" class="input_time">
					<br>
				</div>
				<div style="display: inline-block; width: 25%;">
					Freitag Anfang:
					<br>
					<input type="time" value="00:00" name="fr_anfang" class="input_time">
					<br>
					Freitag Ende:
					<br>
					<input type="time" value="00:00" name="fr_ende" class="input_time">
					<br>
				</div>
			</div>
			<br>
			Kommentar:
			<textarea rows="4" name="comment" style="width: 100%; margin-top: 10px;"></textarea>
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
	}
	if ($show_formular_lehrer) {
		?>
<div class="formular_class">
	<form action="?input=2" method="POST">
		<fieldset style="padding: 40px;">
			<legend>
				<b>Nachhilfelehrer</b>
			</legend>
			<br>
			Vorname:
			<span style="float: right; width: 50%;">Nachname:</span>
			<br>
			<input type="text" maxlength="49" name="vname" autofocus required style="width: 40%;">
			<input type="text" maxlength="49" name="nname" required style="width: 49%; float: right; margin-right: 5px; margin-left: 0;">
			<br>
			<br>
			Klassenstufe:
			<span style="float: right; width: 50%;">Klasse/Kurs:</span>
			<br>
			<input type="number" name="klassenstufe" min="5" max="12" required style="width: 40%;">
			<input type="text" pattern="[ABCDabcdl123456]" required name="klasse" style="width: 49%; float: right; margin-right: 5px; margin-left: 0;">
			<br>
			<br>
			Klassenlehrer:
			<br>
			<input type="text" class="textinput" maxlength="49" name="klassenlehrer" required>
			<br>
			<br>
			Geburtstag
			<br>
			<input type="text" id="datepicker">
			<br>
			<br>
			Email:
			<br>
			<input type="email" class="textinput" maxlength="49" name="email" required>
			<br>
			<br>
			Telefon
			<br>
			<input type="tel" name="telefon">
			<br>
			<div style="width: 20%; display: inline-block;">
				<h3>1.Fach:</h3>
				<select name="fach1" required>
				<?php
		for($i = 0; $i < count($faecher); $i++) {
			echo "<option value=" . $faecher[$i] . ">" . $faecher_lesbar[$i] . "</option>";
		}
		?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach1_lehrer" required>
				<br>
			</div>
			<div style="width: 20%; display: inline-block; margin-left: 10%;">
				<h3>2.Fach:</h3>
				<select name="fach2">
				<?php
		for($i = 0; $i < count($faecher); $i++) {
			echo "<option value=" . $faecher[$i] . ">" . $faecher_lesbar[$i] . "</option>";
		}
		?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach2_lehrer">
				<br>
			</div>
			<div style="width: 20%; display: inline-block; margin-left: 10%;">
				<h3>3.Fach:</h3>
				<select name="fach3">
				<?php
		for($i = 0; $i < count($faecher); $i++) {
			echo "<option value=" . $faecher[$i] . ">" . $faecher_lesbar[$i] . "</option>";
		}
		?>
				</select>
				<br>
				<br>
				Fachlehrer
				<br>
				<input type="text" class="textinput" maxlength="49" name="fach3_lehrer">
				<br>
			</div>
			<br>
			<br>
			<h3>Zeit:</h3>
			<br>
			<div style="display: flex;">
				<div style="display: inline-block; width: 25%;">
					Montag Anfang:
					<br>
					<input type="time" value="00:00" name="mo_anfang" class="input_time">
					<br>
					Montag Ende:
					<br>
					<input type="time" value="00:00" name="mo_ende" class="input_time">
					<br>
					Dienstag Anfang:
					<br>
					<input type="time" value="00:00" name="di_anfang" class="input_time">
					<br>
					Dienstag Ende:
					<br>
					<input type="time" value="00:00" name="di_ende" class="input_time">
					<br>
				</div>
				<div style="display: inline-block; width: 25%;">
					Mitwoch Anfang:
					<br>
					<input type="time" value="00:00" name="mi_anfang" class="input_time">
					<br>
					Mitwoch Ende:
					<br>
					<input type="time" value="00:00" name="mi_ende" class="input_time">
					<br>
					Donnerstag Anfang
					<br>
					<input type="time" value="00:00" name="do_anfang" class="input_time">
					<br>
					Donnerstag Ende:
					<br>
					<input type="time" value="00:00" name="do_ende" class="input_time">
					<br>
				</div>
				<div style="display: inline-block; width: 25%;">
					Freitag Anfang:
					<br>
					<input type="time" value="00:00" name="fr_anfang" class="input_time">
					<br>
					Freitag Ende:
					<br>
					<input type="time" value="00:00" name="fr_ende" class="input_time">
				</div>
			</div>
			<br>
			Kommentar:
			<textarea rows="4" name="comment" style="width: 100%; margin-top: 10px;"></textarea>
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
