<?php
if (isset($user) && $user->runscript()) {
	echo "<h1>Neue Person</h1>";
	if (isset($_GET['addperson']) && $_GET['addperson'] == 1) {
//		echo $_POST['geb'];
		require 'includes/class_person.php';
		$person = new person();
		if($person->addperson($_POST['vname'], $_POST['nname'], $_POST['email'], $_POST['telefon'], $_POST['geb'])) {
			echo "Person wurde erfolgreich hinzugefügt!<br><br><br>";
			echo "<br><a href=\"index.php?page=input&schueler=1&pid=" . $person->id . "\" class=\"links\">Gib der Person eine Rolle als Nachhilfeschüler</a><br><br>";
			echo "<br><a href=\"index.php?page=input&lehrer=1&pid=" . $person->id . "\" class=\"links\">Gib der Person eine Rolle als Nachhilfelehrer</a><br><br>";
		}
	} else {
		?>
<div class="formular_class">
	<script type="text/javascript" src="includes/jquery/jquery-ui-1.12.1/datepicker-de.js"></script>
	<script type="text/javascript" src="includes/javascript/javascript.js"></script>
	<form action="index.php?page=person&addperson=1" method="POST" onsubmit="return person_check()">
		<fieldset style="padding: 40px;">
			<legend>
				<b>Person</b>
			</legend>
			<br>
			Vorname:
			<span style="float: right; width: 50%;">Nachname:</span>
			<br>
			<input type="text" maxlength="49" id="vname" name="vname" autofocus required style="width: 40%;" class="input_text">
			<input type="text" maxlength="49" id="nname" name="nname" required style="width: 49%; float: right; margin-right: 5px; margin-left: 0;" class="input_text">
			<br>
			<br>
			Geburtstag:
			<br>
			<input type="text" id="datepicker" name="geb" class="input_text">
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
			<input type="email" id="email" maxlength="49" name="email" class="input_text" style="width: 40%">
			<br>
			<br>
			Telefon:
			<br>
			<input type="tel" id="telefon" name="telefon" class="input_text">
			<br>
			<br>
			<input type="submit" value="Füge hinzu" class="mybuttons">
		</fieldset>
	</form>
</div>
<?php
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}