<?php
if (isset($user) && $user->runscript()) {
	echo "<h1>Neue Person</h1>";
	if (isset($_GET['addperson']) && $_GET['addperson'] == 1) {
//		echo $_POST['geb'];
		require 'includes/class_person.php';
		$person = new person();
		$person->addperson($_POST['vname'], $_POST['nname'], $_POST['email'], $_POST['telefon'], $_POST['geb']);
	} else {
		?>
<div class="formular_class">
	<form action="index.php?page=person&addperson=1" method="POST" novalidate="novalidate">
		<fieldset style="padding: 40px;">
			<legend>
				<b>Person</b>
			</legend>
			<br>
			Vorname:
			<span style="float: right; width: 50%;">Nachname:</span>
			<br>
			<input type="text" maxlength="49" name="vname" autofocus required style="width: 40%;">
			<input type="text" maxlength="49" name="nname" required style="width: 49%; float: right; margin-right: 5px; margin-left: 0;">
			<br>
			<br>
			Geburtstag
			<br>
			<input type="text" id="datepicker" name="geb">
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
			<input type="email" class="textinput" maxlength="49" name="email">
			<br>
			<br>
			Telefon
			<br>
			<input type="tel" name="telefon">
			<br>
			<br>
			<input type="submit" value="Füge hinzu">
		</fieldset>
	</form>
</div>
<?php
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}