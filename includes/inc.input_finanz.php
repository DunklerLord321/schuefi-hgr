<?php
if(isset($user) && $user->runscript()){
	echo "<h1>Neuer Finanzeintrag</h1>";
	if(isset($_GET['finanzeingabe'])) {
		if((!isset($_POST['uid']) && !isset($_POST['pid'])) || ($_POST['uid'] == "-1" && $_POST['pid'] == "-1")) {
			echo "Bitte wähle eine Person oder einen Schülerfirmamitarbeiter aus";
			$user->log(user::LEVEL_WARNING, "Weder Person noch Schufimitarbeiter ausgewählt beim Finanzeingeben");
			die();
		}
		if(!isset($_POST['uid'])) {
			$_POST['uid'] = NULL;
			$_POST['pid'] = intval($_POST['pid']);
		}else{
			$_POST['uid'] = intval($_POST['uid']);
			$_POST['pid'] = NULL;
		}
		if (!strtotime($_POST['datum'])) {
			$error = $error . "<br><br>Bitte gib ein gültiges Datum an.";
		} else {
			$time = strtotime($_POST['datum']);
			$datum = date('Y-m-d H:i:s', $time);
		}
		if(strlen($_POST['eingabe']) != 0) {
			$betrag = intval($_POST['eingabe']);
		}else{
			$betrag = intval($_POST['ausgabe']);
			$betrag = 0-$betrag;
		}
		$return = query_db("INSERT INTO `finanzuebersicht` (`pid`, `uid`, `geldbetrag`, `betreff`, `bemerkung`, `konto_bar`,`datum`) VALUES(:pid, :uid, :betrag, :betreff, :bemerkung, :konto_bar, :datum)", $_POST['pid'], $_POST['uid'], intval($betrag), $_POST['betreff'], $_POST['bemerkung'], $_POST['typ'], $datum);
		if($return) {
			echo "Erfolgreich hinzugefügt";
		}else{
			echo "Ein Fehler ist aufgetreten";
		}
	}else{
		?>
<script type="text/javascript">

function selectboxes(item1, item2) {
	if(document.getElementById(item1).value == -1) {
		document.getElementById(item2).disabled = false;
		document.getElementById(item2).style.cursor = 'default';
	}else{
		document.getElementById(item2).style.cursor = 'not-allowed';
		document.getElementById(item2).disabled = true;
	}
}
function inputswitch(item1, item2) {
	if(document.getElementById(item1).value.length == 0) {
		document.getElementById(item2).disabled = false;
		document.getElementById(item2).style.cursor = 'default';
	}else{
		document.getElementById(item2).style.cursor = 'not-allowed';
		document.getElementById(item2).disabled = true;
	}
}

$( "#tooltip" ).tooltip();

</script>
<div class="formular_class">
	<form method="post" action="index.php?page=input_finanzen&finanzeingabe=1">
	<label>Person</label>
	<select id="select1" name="pid" onchange="selectboxes('select1','select2')">
	<option value="-1">Bitte wählen</option>
	<?php 
	$return = query_db("SELECT * FROM `schueler` WHERE schuljahr = :schuljahr", get_current_year());
	if ($return !== false) {
		$schueler = $return->fetch();
				require 'includes/class_schueler.php';
				while ( $schueler ) {
					$schueler = new schueler(-1, $schueler['id']);
					echo "<option value=\"" . $schueler->get_id() . "\">" . $schueler->person->vname . " " . $schueler->person->nname . "</option>";
					$schueler = $return->fetch();
				}
		$return = query_db("SELECT * FROM `lehrer` WHERE schuljahr = :schuljahr", get_current_year());
	}
	if ($return !== false) {
		require 'includes/class_lehrer.php';
		$lehrer = $return->fetch();
		while ( $lehrer ) {
			$lehrer = new lehrer(-1, $lehrer['id']);
			echo "<option value=\"" . $lehrer->get_id() . "\">" . $lehrer->person->vname . " " . $lehrer->person->nname . "</option>";
			$lehrer = $return->fetch();
		}
	}
	?>
	</select><br><br>
	<label>Schülerfirmamitarbeiter</label>
	<select id="select2" name="uid" onchange="selectboxes('select2','select1')">
	<option value="-1">Bitte wählen</option>
	<?php 
	$return = query_db("SELECT * FROM `users`");
	if($return !== false) {
		$us = $return->fetch();
		while ($us) {
			echo "<option value=\"".$us['id']."\">".$us['vname']." ".$us['nname']."</option>";
			$us = $return->fetch();
		}
	}
	?>
	</select><br><br>
	<div style="display: flex;">
	<div style="width: 50%; display: inline-block;">
	<label>Eingabe:</label><br>
	<input id="eingabe" type="text" dir="rtl" name="eingabe" class="input_text" oninput="inputswitch('eingabe','ausgabe')">€
	</div>
	<div style="width: 49%; display: inline-block;">
	<label>Ausgabe:</label><br>
	<input id="ausgabe" dir="rtl" type="text" name="ausgabe" class="input_text" oninput="inputswitch('ausgabe','eingabe')">€
	</div>
	</div>
	<label>Betreff:</label>
	<select name="betreff">
	<option value="schueler">Schüler</option>
	<option value="lehrer">Lehrer</option>
	<option value="sonstiges">Sonstiges</option>
	</select><br><br>
	<label>Bar/Konto</label>
	<select name="typ">
	<option value="bar">Bar</option>
	<option value="konto">Konto</option>
	</select>
	<label>Bemerkung:</label><br>
	<textarea rows="4" cols="100" name="bemerkung"></textarea>
	<br><br><label>Datum:</label><br>
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
  $("#datepicker").datepicker().datepicker("setDate", new Date());
</script>
	<input type="submit" value="Hinzufügen" class="mybuttons" style="float: right;"><br><br>
	</form>
</div>

<?php
	}
}else{
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>