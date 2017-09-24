<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Ausgabe</h2>";
	if (isset($_GET['filter'])) {
		echo "Ansicht: <a href=\"index.php?page=output_person&filter=".$_GET['filter']."&layout=list\" class=\"links2\">Liste</a> oder <a href=\"index.php?page=output_person&filter=".$_GET['filter']."&layout=table\" class=\"links2\">Tabelle</a><br><br>";
		$return = query_db("Select * FROM `person` WHERE id = :id ORDER BY `person`.`nname` ASC", $_GET['filter']);
	}else {
		echo "Ansicht: <a href=\"index.php?page=output_person&layout=list\" class=\"links2\">Liste</a> oder <a href=\"index.php?page=output_person&layout=table\" class=\"links2\">Tabelle</a><br><br>";
		$return = query_db("SELECT * FROM `person` ORDER BY `person`.`nname` ASC");
	}
	$result = $return->fetch();
	require 'includes/class_person.php';
	require 'includes/class_lehrer.php';
	require 'includes/class_schueler.php';
	$person = new person();
	if ($result !== false) {
		if(isset($_GET['layout']) && $_GET['layout'] == 'table') {
			echo "<table class=\"table1\"><tr><th>Vorname</th><th>Nachname</th><th>E-Mail-Adresse</th><th>Telefon</th><th>Geburtstag</th><th>Nachhilfeschüler</th><th>Nachhilfelehrer</th><th></th></tr>";
		}
		while ($result) {
			$person->load_person($result['id']);
			if(isset($_GET['layout']) && $_GET['layout'] == 'table') {
				echo "<tr><td>$person->vname</td><td>$person->nname</td><td>$person->email</td><td>$person->telefon</td><td>$person->geburtstag</td>";
				$schueler_lehrer = $person->search_lehrer_schueler();
				if (is_array($schueler_lehrer['schueler'])) {
					echo "<td><a href=\"index.php?page=output&schueler=1&filter=" . $person->id . "\" class=\"links2 fa fa-check-square-o\"> ja</a></td>";
				}else{
					echo "<td><span class=\"fa fa-minus-square-o\"> nein</span></td>";
				}
				if (is_array($schueler_lehrer['lehrer'])) {
					echo "<td><a href=\"index.php?page=output&lehrer=1&filter=" . $person->id . "\" class=\"links2 fa fa-check-square-o\"> ja</a></td>";
				}else{
					echo "<td><span class=\"fa fa-minus-square-o\"> nein</span></td>";
				}
				if ($user->isuserallowed('k')) {
					echo "<td><a href=\"index.php?page=change&person=$person->id\" class=\"links2\">Ändern</a></td>";
				}
				echo "</tr>";
			}else{
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
			}
			$result = $return->fetch();
		}
		if(isset($_GET['layout']) && $_GET['layout'] == 'list') {
			echo "</table>";
		}
	}else {
		echo "Es wurde noch keine Person hinzugefügt";
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
