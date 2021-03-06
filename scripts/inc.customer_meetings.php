<?php
if (isset($user) && $user->runscript()) {
	echo "<h1>Nachhilfetreffen</h1>";
	if ($user->getaccount() == 'c') {
		echo "<h2>Eigene Daten</h2>";		
	}
	if (isset($_GET['new_meeting'])) {
		if (array_search($_POST['paar_id'], $user->all_pairs) === false) {
			echo "Ein Fehler ist aufgetreten! Bitte versuche es erneut";
			die();
		}
		if (!class_exists("paar")) {
			require 'includes/class_paar.php';
		}
		$paar = new paar($_POST['paar_id']);
		if (!isset($user->schueler_id) && !isset($user->lehrer_id)) {
			echo "Ein Fehler ist aufgetreten! Bitte versuche es erneut";
			die();
		}
		if (isset($user->schueler_id)) {
			$paar->add_meeting($user->schueler_id, NULL, $_POST['datum'], $_POST['comment']);	
		}
		if (isset($user->lehrer_id)) {
			$paar->add_meeting(NULL, $user->lehrer_id, $_POST['datum'], $_POST['comment']);	
		}
	}
	if ($user->getaccount() == 'c') {
  	$return = query_db("SELECT person.* FROM users LEFT JOIN person ON person.id = users.person_id WHERE users.id = :id", $user->id);
	} else{
		$return = query_db("SELECT person.* FROM users LEFT JOIN person ON person.id = users.person_id WHERE users.id = :id", $_GET['customer_id']);		
	}
  $result = $return->fetch();
  $person_id = $result['id'];
	if (!class_exists("person")) {
		require 'includes/class_person.php';
	}
	$person = new person();
	$person->load_person($person_id);
	if ($user->getaccount() != 'c') {
		echo "<h2>Daten von $person->vname $person->nname</h2>";		
	}
	$schueler_lehrer = $person->search_lehrer_schueler();
	unset($result);
	if (is_array($schueler_lehrer['lehrer'])) {
		if (!class_exists("lehrer")) {
			require 'includes/class_lehrer.php';
		}
		$lehrer = new lehrer(0, $schueler_lehrer['lehrer']['id']);
		$result = query_db("SELECT unterricht.* FROM unterricht WHERE lid = :id", $lehrer->get_id());
		$is_person_teacher = true;
	}
	if (is_array($schueler_lehrer['schueler'])) {
		if (!class_exists("schueler")) {
			require 'includes/class_schueler.php';
		}
		$schueler = new schueler(0,$schueler_lehrer['schueler']['id']);
		$result = query_db("SELECT unterricht.* FROM unterricht WHERE sid = :id", $schueler->get_id());
		$is_person_teacher = false;
	}
	if (!isset($result) || $result === false) {
		echo "Es ist ein Fehler aufgetreten. Für dich gibt es noch keine Nachhilfedaten!";
		$user->log(USER::LEVEL_WARNING, "Kein Lehrer oder Schüler zu Person gefunden:".$person_id);
		return false;
	}
	$return = $result->fetch();
	if (!class_exists("paar")) {
		require 'includes/class_paar.php';
	}
	if ($return === false) {
		echo "Für dich gibt es noch keinen Nachhilfeunterricht!";
		return false;
	}
	while($return) {
		$paar = new paar($return['id']);
		if (!isset($user->all_pairs)) {
			$user->all_pairs = array();			
		}
		$user->all_pairs[] = $paar->paarid;
		if ($is_person_teacher) {
			$user->lehrer_id = $lehrer->get_id();
			$user->schueler_id = NULL;
		}else{
			$user->lehrer_id = NULL;
			$user->schueler_id = $schueler->get_id();
		}
		$_SESSION['user'] = serialize($user);
		echo "<hr>";
		if ($is_person_teacher) {
			echo "<br><h3>Nachhilfetreffen mit ".$paar->schueler->person->vname." ".$paar->schueler->person->nname."</h3>";
		}else{
			echo "<br><h3>Nachhilfetreffen mit ".$paar->lehrer->person->vname." ".$paar->lehrer->person->nname."</h3>";
		}
		$return_meetings = $paar->all_meetings($is_person_teacher);
		if ($return_meetings !== false) {
			$result_meetings = $return_meetings->fetch();
			if ($result_meetings) {
				echo "<table class=\"table1\"><tr><th>Datum</th><th>Eingetragen vom Lehrer</th><th>Eingetragen vom Schüler</th><th>Kommentar</th></tr>";
			}
			while($result_meetings) {
				echo "<tr><td>".date('d.m.Y', strtotime($result_meetings['datum']))."</td><td>";
				if ($result_meetings['lid'] != NULL) {
					echo "Ja";
				}else{
					echo "Nein";
				}
				echo "</td><td>";
				if ($result_meetings['sid'] != NULL) {
					echo "Ja";
				}else{
					echo "Nein";
				}
				echo "</td><td style=\"width:30%;\">";
				if (strlen($result_meetings['bemerkung_lehrer']) > 0) {
					echo "Lehrer:".$result_meetings['bemerkung_lehrer'];
				}
				if (strlen($result_meetings['bemerkung_schueler']) > 0) {
					echo $result_meetings['bemerkung_schueler'];
				}
				echo "</td></tr>";
				$result_meetings = $return_meetings->fetch();				
			}
			echo "</table><br><br>";
		}
		?>
	<div class="formular_class">
		<script type="text/javascript" src="includes/jquery/jquery-ui-1.12.1/datepicker-de.js"></script>
		<script type="text/javascript" src="includes/javascript/javascript.js"></script>
		<form method="post" action="index.php?page=customer_meetings&new_meeting=1<?php echo (isset($_GET['customer_id'])?"&customer_id=".$_GET['customer_id']:"");?>">
			<script>
  $( function() {
    $( "#datepicker" ).datepicker({
        changeYear: true,
        yearRange: "2017:2020",
	    });
  } );
  </script>
			<fieldset style="padding: 40px;">
				<legend>
					Neues Nachhilfetreffen eintragen
				</legend>
			<input type="hidden" name="paar_id" value="<?php echo $paar->paarid; ?>">
			Datum:<br>
			<input type="text" id="datepicker" name="datum" class="input_text" value="<?php echo date("d.m.Y", time()); ?>"><br><br>
			Kommentar:<br>
			<textarea rows="4" name="comment" style="width: 100%; margin-top: 10px;" class="input_text"></textarea><br><br>
			<input type="submit" value="Hinzufügen" class="mybuttons">
			</fieldset>
		</form>
	</div>
	<br>
		<?php
		$return = $result->fetch();
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>
