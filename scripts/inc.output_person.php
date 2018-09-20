<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Ausgabe</h2>";
	if (isset($_GET['send_registrate_link']) && isset($_GET['person_id'])) {
		require 'includes/class_person.php';
		$person = new person();
		$person->load_person($_GET['person_id']);
		if (!$person->user->has_reference_to_person($person->id)) {
			die("Der Login wurde der Person noch nicht ermöglicht!");
		}
		$person->user->create_security_token();
		die();
	}
	?>
	<script type="text/javascript">
function warn(string) {
	if(confirm(string) == true) {
		return true;
	}
	return false;
}
</script>
	<?php
	if (isset($_GET['filter'])) {
		echo "Ansicht: <a href=\"index.php?page=output_person&filter=".$_GET['filter']."&layout=list\" class=\"links2\">Liste</a> oder <a href=\"index.php?page=output_person&filter=".$_GET['filter']."&layout=table\" class=\"links2\">Tabelle</a><br><br>";
		$return = query_db("Select * FROM `person` WHERE id = :id ORDER BY `person`.`nname` ASC", $_GET['filter']);
	}else if(isset($_GET['filteractive']) && $_GET['filteractive'] == 'false') {
		echo "Ansicht: <a href=\"index.php?page=output&lehrer=1&filteractive=".$_GET['filteractive']."&layout=list\" class=\"links2\">Liste</a> oder <a href=\"index.php?page=output&lehrer=1&filteractive=".$_GET['filteractive']."&layout=table\" class=\"links2\">Tabelle</a><br><br>";
		$return = query_db("Select * FROM `person` WHERE aktiv is not TRUE ORDER BY `person`.`nname` ASC");
	}else {
		echo "Ansicht: <a href=\"index.php?page=output_person&layout=list\" class=\"links2\">Liste</a> oder <a href=\"index.php?page=output_person&layout=table\" class=\"links2\">Tabelle</a><br><br>";
		$return = query_db("SELECT * FROM `person` WHERE aktiv = 1 ORDER BY `person`.`nname` ASC");
	}
	echo "<br><a href=\"index.php?page=output_person&filteractive=false\" class=\"links2\">Nur gelöschte Personen anzeigen</a><br><br><a href=\"index.php?page=output_person\"
				class=\"links2\">Alle aktiven Personen anzeigen</a><br><br>";
	if (isset($_GET['layout']) && $_GET['layout'] == 'table') {
		set_view("table");
	}
	if (isset($_GET['layout']) && $_GET['layout'] == 'list') {
		set_view("list");
	}
	$result = $return->fetch();
	require 'includes/class_person.php';
	require 'includes/class_lehrer.php';
	require 'includes/class_schueler.php';
	$person = new person();
	if ($result !== false) {
		if (get_view() == "table") {
			echo "<table class=\"table1\"><tr><th>Vorname</th><th>Nachname</th><th>E-Mail-Adresse</th><th>Telefon</th><th>Geburtstag</th><th>Nachhilfeschüler</th><th>Nachhilfelehrer</th><th>Login</th><th></th><th></th><th></th></tr>";
		}
		$count = 0;
		while ($result) {
			$count++;
			$person->load_person($result['id']);
			if (get_view() == "table") {
				echo "<tr><td>$person->vname</td><td>$person->nname</td><td>$person->email</td><td>$person->telefon</td><td>$person->geburtstag</td>";
				$schueler_lehrer = $person->search_lehrer_schueler();
				if (is_array($schueler_lehrer['schueler'])) {
					echo "<td><a href=\"index.php?page=output&schueler=1&filter=" . $person->id . "\" class=\"links2\"><img src=\"img/png_yes_12_16.png\" alt=\"ja\" style=\"width:13px;\"></a></td>";
				}else{
					echo "<td><img src=\"img/png_no_13_20.png\" alt=\"nein\"></td>";
				}
				if (is_array($schueler_lehrer['lehrer'])) {
					echo "<td><a href=\"index.php?page=output&lehrer=1&filter=" . $person->id . "\" class=\"links2\"><img src=\"img/png_yes_12_16.png\" alt=\"ja\" style=\"width:13px;\"></a></td>";
				}else{
					echo "<td><img src=\"img/png_no_13_20.png\" alt=\"nein\"></td>";
				}
				if($person->user->has_valid_login()) {
					echo "<td>Ja</td>";
				}else if($person->user->has_valid_security_code()) {
					echo "<td>Ausstehend</td>";
				}else if($person->user->has_reference_to_person($person->id)) {
					echo "<td><a href=\"index.php?page=output_person&send_registrate_link=1&person_id=$person->id\" class=\"links\">Sende Registrierungslink</a></td>";
				}else{
					echo "<td>Nein</td>";
				}
				echo '<td><a href="index.php?page=customer_meetings&customer_id='.$person->user->id.'" class="links2">Nachhilfetreffen</a></td>';
				if($person->aktiv) {
					if ($user->isuserallowed('k')) {
						echo "<td><a href=\"index.php?page=change&person=$person->id\" class=\"links2\"><img src=\"img/png_change_20_24.png\" alt=\"Ändern der Person\"></a></td>";
						echo "<td><a href=\"index.php?page=delete&person=1&delete=$person->id\" class=\"links2\" onclick=\"return warn('Willst du die Person $person->vname $person->nname wirklich löschen?')\"><img src=\"img/png_delete_24_24.png\" alt=\"Löschen der Person\"></a></td>";
					}
				}else{
					echo "<td>Die Person wurde bereits gelöscht und existiert nur noch, um die Daten für die Finanzabteilung zu erhalten</td><td></td>";
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
				echo "<div style=\"padding-left: 10%;\">";
				echo "<br><br><a href=\"index.php?page=output&lehrer=1&filter=" . $person->id . "\" class=\"links2\">$person->vname $person->nname ist als Nachhilfelehrer tätig</a></div>";
			}
			if (is_array($schueler_lehrer['schueler'])) {
				echo "<div style=\"padding-left: 10%;\">";
				echo "<br><br><a href=\"index.php?page=output&schueler=1&filter=" . $person->id . "\" class=\"links2\">$person->vname $person->nname hat sich als Nachhilfeschüler angemeldet</a></div>";
			}
			echo '<br><a href="index.php?page=customer_meetings&customer_id='.$person->user->id.'" class="links2">Nachhilfetreffen ansehen</a>';
			if ($person->user->has_security_code()) {
				echo "<br><br>Der Person wurde bereits ein Registrierungslink gesendet, der bis zum ".date("d.m.y H:i",strtotime($person->user->security_token_time)+3*24*3600)." gültig ist.";
			}
			?>
		</div>
		<?php
			if($person->aktiv) {
			if ($user->isuserallowed('k')) {
				?>
		<div style="width: 30%;">
			<a href="index.php?page=change&person=<?php echo $person->id;?>" class="links">Ändere die Daten</a><br><br><br>
			<a href="index.php?page=delete&person=1&delete=<?php echo $person->id;?>" class="links" onclick="return warn('Willst du die Person wirklich löschen?')">Löschen</a>
			<?php 
			if (!$person->user->has_valid_security_code()) {
				echo '<a href="index.php?page=output_person&send_registrate_link=1&person_id='.$person->id.'" class="links">Sende Registrierungslink</a><br><br>';
			}
			?>
		</div>	
		<?php }
			}else{
				echo "Die Person wurde bereits gelöscht und existiert nur noch, um die Daten für die Finanzabteilung zu erhalten";	
			}
		
		?>
		</div>
</fieldset>
<?php
			}
			$result = $return->fetch();
		}
		if(get_view() == 'table') {
			echo "</table><br><br><span style=\"float:right;\">$count Datensätze</span><b>Hinweis:</b> Wenn du auf <img src=\"img/png_yes_12_16.png\" alt=\"ja\" style=\"width:13px;\"> klickst, kannst du dir die Schüler- oder Lehrerdaten der Person ansehen.";
			echo "<br>Wenn du auf <img src=\"img/png_change_20_24.png\" alt=\"Ändern\" style=\"width:13px;\"> klickst, kannst du die Daten der Person ändern.";
			echo "<br>Wenn du auf <img src=\"img/png_delete_24_24.png\" alt=\"Löschen\" style=\"width:13px;\"> klickst, kannst du die Daten der Person löschen.";
			echo "<br>Login-Status: Ausstehend bedeutet, dass die Person sich noch nicht registriert hat, aber einen gültigen Registrierungslink zugesendet bekommen hat";
		}else{
			echo "<br><br><span style=\"float:right;\">$count Datensätze</span><br>";
		}
	}else {
		if(isset($_GET['filter'])) {
			echo "Diese Person konnte nicht mehr gefunden werden. Entweder wurde sie schon gelöscht oder hat noch nie existiert.";
		}else{
			echo "<br>Es wurde noch keine Person hinzugefügt";
		}
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
