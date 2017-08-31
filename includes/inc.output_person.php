<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Ausgabe</h2>";
	if (isset($_GET['filter'])) {
		$return = query_db("Select * FROM `person` WHERE id = :id", $_GET['filter']);
	}else {
		$return = query_db("SELECT * FROM `person`");
	}
	$result = $return->fetch();
	require 'includes/class_person.php';
	require 'includes/class_lehrer.php';
	require 'includes/class_schueler.php';
	$person = new person();
	if ($result !== false) {
		while ($result) {
			$person->load_person($result['id']);
			?>
<fieldset style="padding: 40px; width: 80%;">
	<legend><?php echo $person->vname.' '.$person->nname?></legend>
	<div style="display: flex;">
		<div style="width: 70%; display: inline-block;">
						
			<?php
			echo "Name: " . $person->vname . " " . $person->nname . "<br>E-Mail: " . $person->email;
			if (strlen($person->telefon) > 0) {
				echo "<br>Telefon: " . $person->telefon;
			}
			if (strlen($person->geburtstag) > 0) {
				echo "<br>Geburtstag: " . $person->geburtstag;
			}
			$schueler_lehrer = $person->search_lehrer_schueler();
			if (is_array($schueler_lehrer['lehrer'])) {
				?>
				<div style="padding-left: 10%;">
				<?php
				echo "<br><br><a href=\"index.php?page=output&lehrer=1&filter=" . $person->id . "\" class=\"links2\">$person->vname $person->nname ist als Nachhilfelehrer tätig</a>";
				?>
				</div>
				<?php
			}
			if (is_array($schueler_lehrer['schueler'])) {
				?>
				<div style="padding-left: 10%;">
				<?php
				echo "<br><br><a href=\"index.php?page=output&schueler=1&filter=" . $person->id . "\" class=\"links2\">$person->vname $person->nname hat sich als Nachhilfeschüler angemeldet</a>";
				?>
				</div>
				<?php
			}
			?>
		</div>
		<?php
			if ($user->isuserallowed('k')) {
				?>
		<div style="width: 30%;">
			<a href="index.php?page=change&person=<?php echo $person->id;?>" class="links">Ändere die Daten</a>
		</div>	
		<?php }?>
		</div>
</fieldset>
<?php
			$result = $return->fetch();
		}
	}else {
		echo "Es wurde noch keine Person hinzugefügt";
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
