<?php
if (isset($user) && $user->runscript()) {
	echo "<h2>Ausgabe</h2>";
	require 'includes/class_person.php';
	require 'includes/class_lehrer.php';
	require 'includes/class_schueler.php';
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
	if (isset($_GET['lehrer']) && $_GET['lehrer'] == 1) {
		if (isset($_GET['filter'])) {
			echo "Ansicht: <a href=\"index.php?page=output&lehrer=1&filter=".$_GET['filter']."&layout=list\" class=\"links2\">Liste</a> oder <a href=\"index.php?page=output&lehrer=1&filter=".$_GET['filter']."&layout=table\" class=\"links2\">Tabelle</a><br><br>";
			$return = query_db("SELECT lehrer.*, u.zahl_schueler FROM `lehrer` LEFT JOIN (SELECT unterricht.lid, COUNT(unterricht.lid) AS zahl_schueler FROM unterricht
        						GROUP BY unterricht.lid) AS u ON u.lid = lehrer.id WHERE `schuljahr` = :schuljahr AND `pid` = :pid", get_current_year(), $_GET['filter']);
		}else {
			echo "Ansicht: <a href=\"index.php?page=output&lehrer=1&layout=list\" class=\"links2\">Liste</a> oder <a href=\"index.php?page=output&lehrer=1&layout=table\" class=\"links2\">Tabelle</a><br><br>";
			$return = query_db("SELECT `lehrer`.*, `person`.`nname`, u.zahl_schueler FROM `lehrer` LEFT JOIN `person` ON `person`.`id` = `lehrer`.`pid` LEFT JOIN (SELECT unterricht.lid, COUNT(unterricht.lid) AS zahl_schueler FROM unterricht
        						GROUP BY unterricht.lid) AS u ON u.lid = lehrer.id WHERE `schuljahr` = '1718' GROUP BY `person`.`nname`, `lehrer`.`id` ASC  ", get_current_year());
		}
		if ($return === false) {
			echo "Ein Problem";
			die();
		}
		if (isset($_GET['layout']) && $_GET['layout'] == "table") {
			set_view("table");
		}
		if (isset($_GET['layout']) && $_GET['layout'] == 'list') {
			set_view("list");
		}
		$result = $return->fetch();
		if ($result !== false) {
			if (get_view() == "table") {
				echo "<table class=\"table1\"><tr><th>Name</th><th>Klasse</th><th>Klassenlehrer</th><th>Fächer</th><th>Zeiten</th><th>Kommentar</th><th>Schüleranzahl</th><th></th></tr>";
			}
			$count = 0;
			while ($result) {
				$count ++;
				$lehrer = new lehrer(-1, $result['id']);
				if (get_view() == "table") {
					echo "<tr><td><a href=\"index.php?page=output_person&filter=" . $lehrer->person->id . "\" class=\"links2\">" . $lehrer->person->vname . ' ' . $lehrer->person->nname . "</a></td>";
					echo "<td>" . format_klassenstufe_kurs($lehrer->get_klassenstufe(), $lehrer->get_klasse())."</td>";
					echo "<td>" . $lehrer->get_klassenlehrer()."</td><td>";
					$faecher = $lehrer->get_angebot_faecher();
					$zeit = $lehrer->get_zeit();
					for ($i = 0; $i < count($faecher); $i++) {
						echo get_faecher_name_of_id($faecher[$i]['fid']) . "<br>". $faecher[$i]['fachlehrer'];
						echo "<br>Notenschnitt: " . $faecher[$i]['notenschnitt'];
						echo " <br> Nachweis vorhanden: " . ($faecher[$i]['nachweis_vorhanden'] == true ? "ja" : "nein");
						echo "<br>Vermittlungsstatus: " . $faecher[$i]['status'] . "<br>";
					}
					echo "</td><td>";
					for ($i = 0; $i < count($zeit); $i++) {
						echo get_name_of_tag($zeit[$i]['tag']) . " von " . date("H:i", strtotime($zeit[$i]['anfang'])) . " - " . date("H:i", strtotime($zeit[$i]['ende'])) . "<br>";
					}
					echo "</td>";
					if (strlen($lehrer->get_comment()) > 0) {
						echo "<td>" . $lehrer->get_comment()."</td>";
					}else{
						echo "<td>Kein Kommentar</td>";
					}
					if($result['zahl_schueler'] > 0) {
						echo "<td><a href=\"index.php?page=output&paare=1&lehrerfilter=" . $lehrer->get_id() . "\" class=\"links2\">" . $result['zahl_schueler'] . "</a></td>";
					}else{
						echo "<td>0</td>";
					}
					if($user->isuserallowed('k')) {
						echo "<td><a href=\"index.php?page=change&lehrer=".$lehrer->get_id()."\" class=\"links2\"><img src=\"img/png_change_20_24.png\" alt=\"Ändern des Lehrers\"></a>
							<a href=\"index.php?page=delete&lehrer=1&delete=" . $lehrer->get_id(). "\" class=\"links2\" onclick=\"return warn('Willst du den Lehrer wirklich löschen?
								Sämtliche Informationen wie z.B. die Zeiten oder der Nachhilfeunterricht gehen dabei verloren')\"><img src=\"img/png_delete_24_24.png\" alt=\"Löschen des Lehrers\"></a></td>";
					}
					echo "</tr>";
/*					if (count($zeit) > 1) {
						for ($i = 1; $i < count($zeit); $i++) {
							echo "<tr><td style=\"rowspan=2\">".get_name_of_tag($zeit[$i]['tag']) . " von " . date("H:i", strtotime($zeit[$i]['anfang'])) . " - " . date("H:i", strtotime($zeit[$i]['ende'])) . "<br></td><td></td></tr>";
						}
					}*/
				}else{
					echo "<fieldset style=\"padding: 40px; width: 80%; padding-top: 10px;\"><legend>";
					echo "<a href=\"index.php?page=output_person&filter=" . $lehrer->person->id . "\" class=\"links2\">" . $lehrer->person->vname . ' ' . $lehrer->person->nname . "</a></legend>";
					echo "<div style=\"display: flex;\"><div style=\"width: 70%; display: inline-block;\">";
					echo "<br>Klasse: " . format_klassenstufe_kurs($lehrer->get_klassenstufe(), $lehrer->get_klasse());
					echo "<br>Klassenlehrer/in: " . $lehrer->get_klassenlehrer();
					$faecher = $lehrer->get_angebot_faecher();
					$zeit = $lehrer->get_zeit();
					echo "<br><br><b>Fächer:</b>";
					for ($i = 0; $i < count($faecher); $i++) {
						echo "<div style=\"padding-left: 5%;\">";
						echo "<br><b>" . get_faecher_name_of_id($faecher[$i]['fid']) . "</b>";
						echo "<br>Fachlehrer: " . $faecher[$i]['fachlehrer'];
						echo "<br>Notenschnitt: " . $faecher[$i]['notenschnitt'];
						echo "<br> Nachweis vorhanden: " . ($faecher[$i]['nachweis_vorhanden'] == true ? "ja" : "nein");
						echo "<br><b>Vermittlungsstatus: " . $faecher[$i]['status'] . "</b>";
						echo "</div>";
					}
					echo "<br><b>Zeiten:</b>";
					echo "<div style=\"padding-left: 5%;\">";
					for ($i = 0; $i < count($zeit); $i++) {
						echo "<br>" . get_name_of_tag($zeit[$i]['tag']) . " von " . date("H:i", strtotime($zeit[$i]['anfang'])) . " Uhr bis " . date("H:i", strtotime($zeit[$i]['ende'])) . " Uhr";
					}
					echo "</div>";
					if (strlen($lehrer->get_comment()) > 0) {
						echo "<br>Kommentar: " . $lehrer->get_comment();
					}
					if($result['zahl_schueler'] > 0) {
						echo "<br><br><a href=\"index.php?page=output&paare=1&lehrerfilter=" . $lehrer->get_id() . "\" class=\"links2\">Der Lehrer hat schon " . $result['zahl_schueler'] . " Schüler</a>";
					}
					echo "</div>";
					if($user->isuserallowed('k')) {
						echo "<div style=\"width: 30%; display: inline-block; padding-top: 40px;\"><a href=\"index.php?page=change&lehrer=" . $lehrer->get_id() . "\" class=\"links\">Ändere die Daten</a>
								<br><br><br><br><br><a href=\"index.php?page=delete&lehrer=1&delete=" . $lehrer->get_id(). "\" class=\"links\" onclick=\"return warn('Willst du den Lehrer wirklich löschen? Sämtliche Informationen wie z.B. die Zeiten oder der Nachhilfeunterricht gehen dabei verloren')\">Löschen</a>
								</div>";
					}
					echo "</div></fieldset>";
				}
				$result = $return->fetch();
			}
			if (get_view() == "table") {
				echo "</table><br><br><span style=\"float:right;\">$count Datensätze</span><b>Hinweis:</b><br>Wenn du auf <img src=\"img/png_change_20_24.png\" alt=\"Ändern\" style=\"width:13px;\"> klickst, kannst du die Daten des Lehrers ändern.";
				echo "<br>Wenn du auf <img src=\"img/png_delete_24_24.png\" alt=\"Löschen\" style=\"width:13px;\"> klickst, kannst du die Daten des Lehrers löschen.";
			}else{
				echo "<br><br><span style=\"float:right;\">$count Datensätze</span><br>";
			}
		}else {
			if (isset($_GET['filter'])) {
				echo "Es trat ein Fehler auf. Zu der Person konnte kein Lehrer gefunden werden";
			}else {
				echo "Es wurde noch kein Lehrer hinzugefügt";
			}
		}
	}
	if (isset($_GET['schueler']) && $_GET['schueler'] == 1) {
		?>
		<fieldset  style="padding: 40px; width: 80%; padding-top: 10px;">
		<form action="index.php?page=output&schueler=1" method="get">
		<input type="hidden" name="page" value="output">
		<input type="hidden" name="schueler" value="1">
		Alle Schüler, bei denen Vermittlungsstatus =
		<select name="filterstatus">
		<option value="-1">Alle anzeigen</option>
		<option value="neu" <?php
		if (isset($_GET['filterstatus']) && $_GET['filterstatus'] == 'neu') {
			echo "selected";
		}
		?>>Neu</option>
		<option value="notwendig" <?php
		if (isset($_GET['filterstatus']) && $_GET['filterstatus'] == 'notwendig') {
			echo "selected";
		}
		?>>Notwendig</option>
		<option value="ausstehend" <?php
		if (isset($_GET['filterstatus']) && $_GET['filterstatus'] == 'ausstehend') {
			echo "selected";
		}
		?>>Ausstehend</option>
		<option value="nicht vermittelbar" <?php
		if (isset($_GET['filterstatus']) && $_GET['filterstatus'] == 'nicht vermittelbar') {
			echo "selected";
		}
		?>>Nicht vermittelbar</option>
		<option value="vermittelt" <?php
		if (isset($_GET['filterstatus']) && $_GET['filterstatus'] == 'vermittelt') {
			echo "selected";
		}
		?>>Vermittelt</option>
		</select>
		<input type="submit" value="Filtern">
		</form>
		</fieldset>
		<br><br>
		
		<?php
		if (isset($_GET['filterstatus']) && $_GET['filterstatus'] != -1) {
			echo "Ansicht: <a href=\"index.php?page=output&schueler=1&filterstatus=".$_GET['filterstatus']."&layout=list\" class=\"links2\">Liste</a> oder <a href=\"index.php?page=output&schueler=1&filterstatus=".$_GET['filterstatus']."&layout=table\" class=\"links2\">Tabelle</a><br><br>";
			$return = query_db("SELECT `schueler`.* FROM `schueler` LEFT JOIN `fragt_nach` ON `schueler`.`id` = `fragt_nach`.`sid` WHERE `fragt_nach`.`status` = :status AND `schueler`.`schuljahr` = :schuljahr  ORDER BY `schueler`.`id` ASC", $_GET['filterstatus'], get_current_year());
		}else if (isset($_GET['filter'])) {
			echo "Ansicht: <a href=\"index.php?page=output&schueler=1&filter=".$_GET['filter']."&layout=list\" class=\"links2\">Liste</a> oder <a href=\"index.php?page=output&schueler=1&filter=".$_GET['filter']."&layout=table\" class=\"links2\">Tabelle</a><br><br>";
			$return = query_db("SELECT * FROM `schueler` WHERE `schuljahr` = :schuljahr AND `pid` = :pid", get_current_year(), $_GET['filter']);
		}else {
			echo "Ansicht: <a href=\"index.php?page=output&schueler=1&layout=list\" class=\"links2\">Liste</a> oder <a href=\"index.php?page=output&schueler=1&layout=table\" class=\"links2\">Tabelle</a><br><br>";
			$return = query_db("SELECT `schueler`.*, `person`.`nname` FROM `schueler` LEFT JOIN `person` ON `person`.`id` = `schueler`.`pid` WHERE `schuljahr` = :schuljahr GROUP BY `person`.`nname`, `schueler`.`id` ASC ", get_current_year());
		}
		if ($return === false) {
			echo "Ein Problem";
			die();
		}
		if (isset($_GET['layout']) && $_GET['layout'] == "table") {
			set_view("table");
		}
		if (isset($_GET['layout']) && $_GET['layout'] == 'list') {
			set_view("list");
		}
		$result = $return->fetch();
		if ($result !== false) {
			if (get_view() == "table") {
				echo "<div style=\"overflow-x:auto\"><table class=\"table1\"><tr><th>Name</th><th>Klasse</th><th>Klassenlehrer</th><th>Fächer</th><th>Zeiten</th><th>Kommentar</th><th></th></tr>";
			}
			$count = 0;
			while ($result) {
				$count++;
				if (isset($schueler) && $schueler->get_id() == $result['id']) {
					$result = $return->fetch();
				}
				$schueler = new schueler(-1, $result['id']);
				if (get_view() == "table") {
					echo "<td><a href=\"index.php?page=output_person&filter=" . $schueler->person->id . "\" class=\"links2\">" . $schueler->person->vname . ' ' . $schueler->person->nname . "</a></td>";
					echo "<td>" . format_klassenstufe_kurs($schueler->get_klassenstufe(), $schueler->get_klasse())."</td>";
					echo "<td>" . $schueler->get_klassenlehrer()."</td><td>";
						
					$faecher = $schueler->get_nachfrage_faecher();
					$zeit = $schueler->get_zeit();
					for ($i = 0; $i < count($faecher); $i++) {
						echo get_faecher_name_of_id($faecher[$i]['fid']) .(strlen($faecher[$i]['fachlehrer']) > 0 ? "<br>":"") . $faecher[$i]['fachlehrer'];
						echo " <br> Langfristig: " . ($faecher[$i]['langfristig'] == true ? "ja" : "nein");
						echo "<br>Vermittlungsstatus: " . ($faecher[$i]['status'] == "neu" ? "<b>neu</b>" : $faecher[$i]['status']) . "<br>";
						if($faecher[$i]['status'] != "vermittelt") {
							echo "<a href=\"index.php?page=input_paar&schueler=" . $schueler->get_id() . "&fid=" . $faecher[$i]['fid'] . "\" class=\"links2\">Suche nach Lehrer</a><br>";
						}
					}
					echo "</td><td>";
					for ($i = 0; $i < count($zeit); $i++) {
						echo $zeit[$i]['tag'] . " von " . date("H:i", strtotime($zeit[$i]['anfang'])) . " - " . date("H:i", strtotime($zeit[$i]['ende'])) . "<br>";
					}
					echo "</td>";
					if (strlen($schueler->get_comment()) > 0) {
						echo "<td>" . $schueler->get_comment()."</td>";
					}else{
						echo "<td>Kein Kommentar</td>";
					}
					if($user->isuserallowed('k')) {
						echo "<td><a href=\"index.php?page=change&schueler=".$schueler->get_id()."\" class=\"links2\"><img src=\"img/png_change_20_24.png\" alt=\"Ändern des Schülers\"></a>
							<a href=\"index.php?page=delete&schueler=1&delete=" . $schueler->get_id(). "\" class=\"links2\" onclick=\"return warn('Willst du den Schüler wirklich löschen? Sämtliche Informationen wie z.B. die Zeiten oder der Nachhilfeunterricht gehen dabei verloren')\"><img src=\"img/png_delete_24_24.png\" alt=\"Löschen des Schülers\"></a></td>";
					}
					echo "</tr>";
				}else{
				?>
<fieldset style="padding: 40px; width: 80%; padding-top: 10px;">
	<legend><?php
				
				echo "<a href=\"index.php?page=output_person&filter=" . $schueler->person->id . "\" class=\"links2\">" . $schueler->person->vname . ' ' . $schueler->person->nname . "</a>"?></legend>
	<div style="display: flex;">
		<div style="width: 70%; display: inline-block;">
							<?php
				echo "<br>Klasse: " . format_klassenstufe_kurs($schueler->get_klassenstufe(), $schueler->get_klasse());
				echo "<br>Klassenlehrer/in: " . $schueler->get_klassenlehrer();
				$faecher = $schueler->get_nachfrage_faecher();
				$zeit = $schueler->get_zeit();
				echo "<br><br><b>Fächer:</b>";
				for ($i = 0; $i < count($faecher); $i++) {
					if (isset($faecher[$i]) || (isset($faecher[$i]) && isset($_GET['filterstatus']) && $_GET['filterstatus'] == $faecher[$i]['status'])) {
						echo "<div style=\"padding-left: 5%;\">";
						echo "<br>" . get_faecher_name_of_id($faecher[$i]['fid']) . " <br> Langfristig: " . ($faecher[$i]['langfristig'] == true ? "ja" : "nein");
						echo "<br>Fachlehrer: " . $faecher[$i]['fachlehrer'];
						echo "<br><b>Vermittlungsstatus: " . $faecher[$i]['status'] . "</b><br><br>";
						echo "<a href=\"index.php?page=input_paar&schueler=" . $schueler->get_id() . "&fid=" . $faecher[$i]['fid'] . "\" class=\"links\">Suche nach Lehrer</a>";
						echo "</div>";
					}
				}
				echo "<br><b>Zeiten:</b>";
				echo "<div style=\"padding-left: 5%;\">";
				for ($i = 0; $i < count($zeit); $i++) {
					echo "<br>" . get_name_of_tag($zeit[$i]['tag']) . " von " . date("H:i", strtotime($zeit[$i]['anfang'])) . " Uhr bis " . date("H:i", strtotime($zeit[$i]['ende'])) . " Uhr";
				}
				echo "</div>";
				if (strlen($schueler->get_comment()) > 0) {
					echo "<br>Kommentar: " . $schueler->get_comment();
				}
				echo "</div>";
				if($user->isuserallowed('k')) {
					echo 	'<div style="width: 30%; display: inline-block; padding-top: 40px;"><a href="index.php?page=change&schueler='.$schueler->get_id().'" class="links">Ändere die Daten</a>
					<br><br><br><br><br><a href="index.php?page=delete&schueler=1&delete='.$schueler->get_id().'" class="links" onclick="return warn(\'Willst du den Schüler wirklich löschen? 
					Sämtliche Informationen, wie die Zeit und sein Nachhilfepaar gehen dabei verloren\')">Löschen</a></div>';
				}
				echo "</div></fieldset>";
				}
				$result = $return->fetch();
			}
			if (get_view() == "table") {
				echo "</table><br><br><span style=\"float:right;\">$count Datensätze</span><b>Hinweis:</b><br>Wenn du auf <img src=\"img/png_change_20_24.png\" alt=\"Ändern\" style=\"width:13px;\"> klickst, kannst du die Daten des Schülers ändern.";
				echo "<br>Wenn du auf <img src=\"img/png_delete_24_24.png\" alt=\"Löschen\" style=\"width:13px;\"> klickst, kannst du die Daten des Schülers löschen.";
			}else{
				echo "<br><br><span style=\"float:right;\">$count Datensätze</span><br>";
			}
		}else {
			if (isset($_GET['filterstatus'])) {
				echo "Es konnte kein Schüler mit diesem Vermittlungsstatus gefunden werden";
			}else if (isset($_GET['filter'])) {
				echo "Es trat ein Fehler auf. Zu dieser Person konnte kein Schüler gefunden werden";
			}else {
				echo "Es wurde noch kein Schüler hinzugefügt";
			}
		}
	}
	if (isset($_GET['paare']) && $_GET['paare'] == 1) {
		require 'includes/class_paar.php';
		echo "Ansicht: <a href=\"index.php?page=output&paare=1&layout=list\" class=\"links2\">Liste</a> oder <a href=\"index.php?page=output&paare=1&layout=table\" class=\"links2\">Tabelle</a><br><br>";
			if(isset($_GET['filter'])) {
			$return = query_db("SELECT unterricht.* FROM `unterricht` LEFT JOIN lehrer ON unterricht.lid = lehrer.id WHERE lehrer.schuljahr = :jahr AND unterricht.id = :id", get_current_year(), $_GET['filter']);
			echo "<a href=\"index.php?page=output&paare=1\" class=\"links2\">Ausgabe aller Paare ohne Filterung</a><br><br>";
		}else if (isset($_GET['raumfilter'])) {
			$return = query_db("SELECT unterricht.* FROM `unterricht` LEFT JOIN lehrer ON unterricht.lid = lehrer.id WHERE lehrer.schuljahr = :jahr AND unterricht.rid = :rid", get_current_year(), $_GET['raumfilter']);
			echo "<a href=\"index.php?page=output&paare=1\" class=\"links2\">Ausgabe aller Paare ohne Filterung</a><br><br>";
		}else if(isset($_GET['lehrerfilter'])) {
			$return = query_db("SELECT unterricht.* FROM `unterricht` LEFT JOIN lehrer ON unterricht.lid = lehrer.id WHERE lehrer.schuljahr = :jahr AND unterricht.lid = :lid", get_current_year(), $_GET['lehrerfilter']);
			echo "<a href=\"index.php?page=output&paare=1\" class=\"links2\">Ausgabe aller Paare ohne Filterung</a><br><br>";
		}else if(isset($_GET['schuelerfilter'])) {
			$return = query_db("SELECT unterricht.* FROM `unterricht` LEFT JOIN lehrer ON unterricht.lid = lehrer.id WHERE lehrer.schuljahr = :jahr AND unterricht.sid = :sid", get_current_year(), $_GET['schuelerfilter']);
			echo "<a href=\"index.php?page=output&paare=1\" class=\"links2\">Ausgabe aller Paare ohne Filterung</a><br><br> ";
		}else{
			$return = query_db("SELECT unterricht.* FROM `unterricht` LEFT JOIN lehrer ON unterricht.lid = lehrer.id WHERE lehrer.schuljahr = '" . get_current_year() . "';");
		}
		if (isset($_GET['layout']) && $_GET['layout'] == "table") {
			set_view("table");
		}
		if (isset($_GET['layout']) && $_GET['layout'] == 'list') {
			set_view("list");
		}
		if ($return) {
			$paar = $return->fetch();
			if (!$paar && (isset($_GET['filter']) || isset($_GET['lehrerfilter']) || isset($_GET['schuelerfilter']))) {
				echo "Es trat ein Fehler auf. Zu dieser Suche konnte kein Paar gefunden werden";
			}else if(!$paar){
				echo "Es wurde noch kein Paar hinzugefügt";
			}
			if (get_view() == "table") {
				echo "<div style=\"overflow-x:auto\"><table class=\"table1\"><tr><th>Nachhilfelehrer</th><th>Nachhilfeschüler</th><th>Fach</th><th>Zeitpunkt</th><th>Zimmer</th><th>Vermittlungsdokumente</th><th></th><th></th></tr>";
			}
			$count = 0;
			while ($paar) {
				$npaar = new paar($paar['id']);
				if (get_view() == "table") {
					echo "<tr><td><a href=\"index.php?page=output&lehrer=1&filter=" . $npaar->lehrer->person->id . "\" class=\"links2\">" . $npaar->lehrer->person->vname . " " . $npaar->lehrer->person->nname . "</a></td>";
					echo "<td><a href=\"index.php?page=output&schueler=1&filter=" . $npaar->schueler->person->id . "\" class=\"links2\">" . $npaar->schueler->person->vname . " " . $npaar->schueler->person->nname . "</a></td>";
					echo "<td>". get_faecher_name_of_id($npaar->fid) . "</td><td>" . get_name_of_tag($npaar->tag) . " von " . $npaar->anfang . " Uhr bis " . $npaar->ende . " Uhr</td>";
					echo "<td>" . $npaar->raum . "</td><td>";
					if (strlen($npaar->lehrer_dokument) > 0) {
						echo "<a href=\"docs/unterricht/" . $npaar->lehrer_dokument . "\" class=\"links2\">Lehrer</a>";
						if($user->isuserallowed('k')) {
							echo "<a style=\"float: right; margin-right: 20%;\" href=\"index.php?page=create_doc&createdoc_paar=$npaar->paarid\" class=\"links2\"><img src=\"img/png_refresh_24_24.png\" alt=\"erneut erstellen\" ></a>";
						}
					}
					if($user->isuserallowed('k') && (strlen($npaar->schueler_dokument) <= 0 || strlen($npaar->lehrer_dokument) <= 0)) {
						echo "<a href=\"index.php?page=create_doc&createdoc_paar=$npaar->paarid\" class=\"links2\">Dokumente für Lehrer und Schüler erstellen</a>";
					}
					if (strlen($npaar->schueler_dokument) > 0) {
						echo "<br><a href=\"docs/unterricht/" . $npaar->schueler_dokument . "\" class=\"links2\">Schüler</a>";
					}
					echo "</td>";
					if($user->isuserallowed('k')) {
						echo "<td><a href=\"index.php?page=delete&paar=1&delete=$npaar->paarid\" class=\"links2\" onclick=\"return warn('Willst du das Paar wirklich löschen?' Dabei gehen die Daten über das Nachhilfepaar unwiederuflich verloren, allerdings bleiben die Daten über den Schüler und Lehrer erhalten)\"><img src=\"img/png_delete_24_24.png\" alt=\"Löschen\" ></a></td>";
						echo "<td><a href=\"index.php?page=change&paar=$npaar->paarid\" class=\"links2\" ><img src=\"img/png_change_20_24.png\" alt=\"Ändern\" ></a></td>";
					}
					echo "</tr>";
				}else{
					echo "<fieldset>";
					echo "<legend>Nachhilfepaar</legend>";
					echo "<p>Im Fach " . get_faecher_name_of_id($npaar->fid) . "</p>Lehrer: <a href=\"index.php?page=output&lehrer=1&filter=" . $npaar->lehrer->person->id . "\" class=\"links2\">" . $npaar->lehrer->person->vname . " " . $npaar->lehrer->person->nname . "</a><p>";
					echo "Schüler: <a href=\"index.php?page=output&schueler=1&filter=" . $npaar->schueler->person->id . "\" class=\"links2\">" . $npaar->schueler->person->vname . " " . $npaar->schueler->person->nname . "</a><br>";
					echo "<br>Zeitpunkt: " . get_name_of_tag($npaar->tag) . " von " . $npaar->anfang . " Uhr bis " . $npaar->ende . " Uhr";
					echo "<br><br>Im Zimmer: " . $npaar->raum . "<br><br>";
					if (strlen($npaar->lehrer_dokument) > 0) {
						echo "<a href=\"docs/unterricht/" . $npaar->lehrer_dokument . "\" class=\"links\">Vermittlungsdokument für Lehrer ansehen</a><br><br><br>";
					}else {
						echo "Vermittlungsdokument für Lehrer ist noch nicht vorhanden<br><br>";
					}
					if (strlen($npaar->schueler_dokument) > 0) {
						echo "<a href=\"docs/unterricht/" . $npaar->schueler_dokument . "\" class=\"links\">Vermittlungsdokument für Schüler ansehen</a><br><br><br>";
						echo ($user->isuserallowed('k') ?"<a href=\"index.php?page=create_doc&createdoc_paar=$npaar->paarid\" class=\"links\">Dokumente für Lehrer und Schüler erneut erstellen</a><br><br><br>":"");
					}else {
						echo "Vermittlungsdokument für Schüler ist noch nicht vorhanden<br><br>";
					}
					if($user->isuserallowed('k') && (strlen($npaar->schueler_dokument) <= 0 || strlen($npaar->lehrer_dokument) <= 0)) {
						echo "<a href=\"index.php?page=create_doc&createdoc_paar=$npaar->paarid\" class=\"links\">Dokumente für Lehrer und Schüler erstellen</a><br><br><br>";
					}
					if($user->isuserallowed('k')) {
						echo "<a href=\"index.php?page=delete&paar=1&delete=$npaar->paarid\" class=\"links\" onclick=\"return warn('Willst du das Paar wirklich löschen?' Dabei gehen die Daten über das Nachhilfepaar unwiederuflich verloren, allerdings bleiben die Daten über den Schüler und Lehrer erhalten)\">Löschen</a>";
						echo "<a href=\"index.php?page=change&paar=$npaar->paarid\" class=\"links\" >Ändern der Daten</a><br><br>";
					}
					echo "</fieldset>";
				}
				$paar = $return->fetch();
				$count++;
			}
			if (get_view() == "table") {
				echo "</table><br><br><span style=\"float:right;\">$count Datensätze</span><b>Hinweis:</b><br>Wenn du auf <img src=\"img/png_change_20_24.png\" alt=\"Ändern\" style=\"width:13px;\"> klickst, kannst du die Daten des Schülers ändern.";
				echo "<br>Wenn du auf <img src=\"img/png_delete_24_24.png\" alt=\"Löschen\" style=\"width:13px;\"> klickst, kannst du die Daten des Schülers löschen.";
				echo "<br>Wenn du auf <img src=\"img/png_refresh_24_24.png\" alt=\"Erneut erstellen\" style=\"width:13px;\"> klickst, kannst du das Vermittlungsdokument erneut erstellen, falls sich Daten geändert haben sollten.";
			}else{
				echo "<br><br><span style=\"float:right;\">$count Datensätze</span><br><br>";
			}
		}
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>