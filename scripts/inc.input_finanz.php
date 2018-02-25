<?php
if (isset($user) && $user->runscript()) {
	echo "<h1>Neuer Finanzposten</h1>";
	if (isset($_GET['finanzeingabe'])) {
		if ((!isset($_POST['uid']) && !isset($_POST['pid'])) || (isset($_POST['uid']) && isset($_POST['pid']) && $_POST['uid'] == "-1" && $_POST['pid'] == "-1")) {
			echo "Bitte wähle eine Person oder einen Schülerfirmamitarbeiter aus";
			$user->log(user::LEVEL_WARNING, "Weder Person noch Schufimitarbeiter ausgewählt beim Finanzeingeben");
			die();
		}
		if (!isset($_POST['uid'])) {
			$_POST['uid'] = NULL;
			$_POST['pid'] = intval($_POST['pid']);
		}else {
			$_POST['uid'] = intval($_POST['uid']);
			$_POST['pid'] = NULL;
		}
		if (!strtotime($_POST['datum'])) {
			$error = $error . "<br><br>Bitte gib ein gültiges Datum an.";
		}else {
			$time = strtotime($_POST['datum']);
			$datum = date('Y-m-d H:i:s', $time);
		}
		if (strlen($_POST['eingabe']) != 0) {
			$betrag = floatval($_POST['eingabe']);
		}else {
			$betrag = floatval($_POST['ausgabe']);
			$betrag = 0 - $betrag;
		}
		$return = query_db("INSERT INTO `finanzuebersicht` (`pid`, `uid`, `geldbetrag`, `betreff`, `bemerkung`, `konto_bar`,`datum`) VALUES(:pid, :uid, :betrag, :betreff, :bemerkung, :konto_bar, :datum)", $_POST['pid'], $_POST['uid'], $betrag, $_POST['betreff'], $_POST['bemerkung'], $_POST['typ'], $datum);
		if ($return) {
			echo "Erfolgreich hinzugefügt";
		}else {
			echo "Ein Fehler ist aufgetreten";
		}
		echo "<br><br><a href=\"index.php?page=input_finanzen\" class=\"links2\">Einen weiteren Posten eingeben</a><br><br><a href=\"index.php?page=output_finanzen\" class=\"links2\">Zur Ausgabeseite der Finanzposten</a>";
	}else if (isset($_GET['finanzaenderung'])) {
			if ((!isset($_POST['uid']) && !isset($_POST['pid'])) || (isset($_POST['uid']) && isset($_POST['pid']) && $_POST['uid'] == "-1" && $_POST['pid'] == "-1")) {
				echo "Bitte wähle eine Person oder einen Schülerfirmamitarbeiter aus";
				$user->log(user::LEVEL_WARNING, "Weder Person noch Schufimitarbeiter ausgewählt beim Finanzeingeben");
				die();
			}
			if (!isset($_POST['uid'])) {
				$_POST['uid'] = NULL;
				$_POST['pid'] = intval($_POST['pid']);
			}else {
				$_POST['uid'] = intval($_POST['uid']);
				$_POST['pid'] = NULL;
			}
			if (!strtotime($_POST['datum'])) {
				$error = $error . "<br><br>Bitte gib ein gültiges Datum an.";
			}else {
				$time = strtotime($_POST['datum']);
				$datum = date('Y-m-d H:i:s', $time);
			}
			$_POST['eingabe'] = str_replace(",", ".", $_POST['eingabe']);
			if (strlen($_POST['eingabe']) != 0) {
				$betrag = floatval($_POST['eingabe']);
			}else {
				$betrag = floatval($_POST['ausgabe']);
				$betrag = 0 - $betrag;
			}
			$return = query_db("UPDATE `finanzuebersicht` SET `pid` = :pid, `uid` = :uid, `geldbetrag` = :geldbetrag, `betreff` = :betreff, `bemerkung` = :bemerkung, `konto_bar` = :konto_bar, `datum` = :datum WHERE id = :id", $_POST['pid'], $_POST['uid'], $betrag, $_POST['betreff'], $_POST['bemerkung'], $_POST['typ'], $datum, $_GET['finanzaenderung']);
			if ($return) {
				echo "Erfolgreich hinzugefügt";
			}else {
				echo "Ein Fehler ist aufgetreten";
			}
			echo "<br><br><a href=\"index.php?page=input_finanzen\" class=\"links2\">Einen weiteren Posten eingeben</a><br><br><a href=\"index.php?page=output_finanzen\" class=\"links2\">Zur Ausgabeseite der Finanzposten</a>";
		}else {
			if(isset($_GET['change'])) {
				$return = query_db("SELECT * FROM `finanzuebersicht` WHERE id = :fid", $_GET['change']);
				if ($return !== false) {
					$finanzeintrag = $return->fetch();
					if(!$finanzeintrag) {
						echo "Es trat ein Fehler beim Laden des Finanzeintrags auf";
						unset($finanzeintrag);
					}
				}else{
					echo "Dieser Eintrag konnte nicht gefunden werden";
				}
			}
		?>
<div class="formular_class">
	<form method="post" action="index.php?page=input_finanzen&<?php echo (isset($finanzeintrag) ? 'finanzaenderung='.$finanzeintrag['id']:'finanzeingabe=1');?>">
		<label>Person:</label>
		<select id="select1" name="pid" >
			<option value="-1">Bitte wählen</option>
	<?php
		$return = query_db("SELECT * FROM `person` WHERE aktiv = 1 ORDER BY person.nname ASC");
		if ($return !== false) {
			$person_id = $return->fetch();
			require 'includes/class_person.php';
			while ($person_id) {
				$person = new person();
				$person->load_person($person_id['id']);
				if (isset($finanzeintrag) && $finanzeintrag['pid'] == $person->id) {
					echo "<option value=\"" . $person->id. "\" selected>" . $person->vname . " " . $person->nname . "</option>";
				}else{
					echo "<option value=\"" . $person->id. "\">" . $person->vname . " " . $person->nname . "</option>";
				}
				$person_id = $return->fetch();
			}
		}
		?>
	</select>
		<br>
		<br>
		<label>Schülerfirmamitarbeiter:</label>
		<select id="select2" name="uid" >
			<option value="-1">Bitte wählen</option>
	<?php
		$return = query_db("SELECT * FROM `users` WHERE aktiv = 1");
		if ($return !== false) {
			$us = $return->fetch();
			while ($us) {
				if(isset($finanzeintrag) && $finanzeintrag['uid'] == $us['id']) {
					echo "<option value=\"" . $us['id'] . "\" selected >" . $us['vname'] . " " . $us['nname'] . "</option>";
				}else{
					echo "<option value=\"" . $us['id'] . "\">" . $us['vname'] . " " . $us['nname'] . "</option>";
				}
				$us = $return->fetch();
			}
		}
		?>
	</select>
		<br>
		<br>
		<div style="display: flex;">
			<div style="width: 50%; display: inline-block;">
				<label>Eingabe:</label>
				<br>
				<input id="eingabe" style="text-align: right;" type="text" name="eingabe" class="input_text" placeholder="000.00" value="<?php echo ((isset($finanzeintrag) && $finanzeintrag['geldbetrag'] > 0) ? $finanzeintrag['geldbetrag']:'');?>">
				€
			</div>
			<div style="width: 49%; display: inline-block;">
				<label>Ausgabe:</label>
				<br>
				<input id="ausgabe" style="text-align: right;" type="text" name="ausgabe" class="input_text"  placeholder="000.00" value="<?php echo (isset($finanzeintrag) && $finanzeintrag['geldbetrag'] < 0 ? abs($finanzeintrag['geldbetrag']):'');?>">
				€
			</div>
		</div>
		<label>Betreff:</label>
		<select name="betreff">
			<option value="schueler" <?php echo (isset($finanzeintrag) && $finanzeintrag['betreff'] == "schueler" ? 'selected':'');?>>Schüler</option>
			<option value="lehrer"<?php echo (isset($finanzeintrag) && $finanzeintrag['betreff'] == "lehrer" ? 'selected':'');?>>Lehrer</option>
			<option value="sonstiges"<?php echo (isset($finanzeintrag) && $finanzeintrag['betreff'] == "sonstiges" ? 'selected':'');?>>Sonstiges</option>
		</select>
		<br>
		<br>
		<label>Bar/Konto:</label>
		<select name="typ">
			<option value="bar"<?php echo (isset($finanzeintrag) && $finanzeintrag['konto_bar'] == "bar" ? 'selected':'');?>>Bar</option>
			<option value="konto"<?php echo (isset($finanzeintrag) && $finanzeintrag['konto_bar'] == "konto" ? 'selected':'');?>>Konto</option>
		</select><br><br>
		<label>Bemerkung:</label>
		<br>
		<textarea rows="4" cols="100" name="bemerkung"><?php echo (isset($finanzeintrag) && strlen($finanzeintrag['bemerkung']) > 0 ? $finanzeintrag['bemerkung']:'');?></textarea>
		<br>
		<br>
		<label>Datum:</label>
		<br>
		<input type="text" id="datepicker" name="datum" class="input_text">
		<script type="text/javascript" src="includes/jquery/jquery-ui-1.12.1/datepicker-de.js"></script>
		<script type="text/javascript" src="includes/javascript/javascript.js"></script>
		<script>
  $( function() {
    $( "#datepicker" ).datepicker({
        changeYear: true,
        yearRange: "c-20:c-10",
        dateFormat: "dd.mm.yy"
	    });
  } );
  $("#datepicker").datepicker().datepicker("setDate", <?php echo (isset($finanzeintrag) ? '\''.date('d.m.Y',strtotime($finanzeintrag['datum'])).'\'':'new Date()');?>);
</script>
		<input type="submit" value="<?php echo (isset($finanzeintrag) ? 'Ändern':'Hinzufügen');?>" class="mybuttons" style="float: right;">
		<br>
		<br>
	</form>
</div>
		<script type="text/javascript">
$(function() {
	$('#select1').change(selectboxes);
	$('#select2').change(selectboxes);
	function selectboxes() {
		if($('#select1').val() == -1) {
			$('#select2').attr("disabled", false);
			$('#select2').css('cursor', 'default');
		}
		if($('#select1').val() != -1) {
			$('#select2').attr("disabled", true);
			$('#select2').css('cursor', 'not-allowed');
		}
		if($('#select2').val() == -1) {
			$('#select1').css('cursor', 'default');
			$('#select1').attr("disabled", false);
		}
		if($('#select2').val() != -1) {
			$('#select1').css('cursor', 'not-allowed');
			$('#select1').attr("disabled", true);
		}
	}
	selectboxes();
	$('#eingabe').change(inputswitch);
	$('#ausgabe').change(inputswitch);
	function inputswitch() {
		console.log('test');
			if($('#eingabe').val().length < 0) {
				$('#ausgabe').attr("disabled", false);
				$('#ausgabe').css('cursor', 'default');
			}
			if($('#eingabe').val().length > 0 ) {
				$('#ausgabe').attr("disabled", true);
				$('#ausgabe').css('cursor', 'not-allowed');
			}
			if($('#ausgabe').val().length < 0 ) {
				$('#eingabe').css('cursor', 'default');
				$('#eingabe').attr("disabled", false);
			}
			if($('#ausgabe').val().length > 0 ) {
				$('#eingabe').css('cursor', 'not-allowed');
				$('#eingabe').attr("disabled", true);
			}
	}
	inputswitch();
});

$( "#tooltip" ).tooltip();
</script>
		

<?php
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>