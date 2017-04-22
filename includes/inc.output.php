<?php
if (isset($user) && $user->runscript()) {
	global $pdo;
	echo "<h2>Ausgabe</h2>";
	get_all_years();
	if (isset($_GET['deletelehr']) && is_numeric($_GET['deletelehr']) && isset($_GET['year']) && array_search($_GET['year'], get_all_years()) !== false) {
		?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?deleteconfirmlehr=".$_GET['deletelehr']."&year=".$_GET['year'];?>" method="post">
	Achtung! Dieser Vorgang kann nicht rückgängig gemacht werden.
	<br>
	Trotzdem fortsetzen?
	<br>
	<br>
	<input type="submit" value="Ok">
</form>
<?php
	}
	if (isset($_GET['deleteconfirmlehr']) && is_numeric($_GET['deleteconfirmlehr']) && isset($_GET['year']) && array_search($_GET['year'], get_all_years()) !== false) {
		$return_query = $pdo->prepare("DELETE FROM `lehrer` WHERE id = :id");
		$return = $return_query->execute(array(
				'id' => $_GET['deleteconfirmlehr']
		));
		if ($return == false) {
			echo "Ein Problem ist aufgetreten";
			if ($return_query->errorInfo()[1] == 1451) {
				echo "<br><br><b>Bitte löse vorher sämtliche Nachhilfepaare mit diesem Lehrer auf!</b>";
			}
		} else {
			echo "Löschen war erfolgreich";
		}
	}
	if (isset($_GET['deleteschuel']) && is_numeric($_GET['deleteschuel']) && isset($_GET['year']) && array_search($_GET['year'], get_all_years()) !== false) {
		?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?deleteconfirmschuel=".$_GET['deleteschuel']."&year=".$_GET['year'];?>" method="post">
	Achtung! Dieser Vorgang kann nicht rückgängig gemacht werden.
	<br>
	Trotzdem fortsetzen?
	<br>
	<br>
	<input type="submit" value="Ok">
</form>
<?php
	}
	if (isset($_GET['deleteconfirmschuel']) && is_numeric($_GET['deleteconfirmschuel']) && isset($_GET['year']) && array_search($_GET['year'], get_all_years()) !== false) {
		$return_query = $pdo->prepare("DELETE FROM `schueler-" . $_GET['year'] . "` WHERE id = :id");
		$return = $return_query->execute(array(
				'id' => $_GET['deleteconfirmschuel']
		));
		if ($return == false) {
			echo "Ein Problem ist aufgetreten";
			if ($return_query->errorInfo()[1] == 1451) {
				echo "<br><br><b>Bitte löse vorher sämtliche Nachhilfepaare mit diesem Schüler auf!</b>";
			}
		} else {
			echo "Löschen war erfolgreich";
		}
	}
	
	if (isset($_GET['lehrer']) && $_GET['lehrer'] == 1) {
		$show_formular_lehrer = true;
		$show_formular_schueler = false;
	} else if (isset($_GET['schueler']) && $_GET['schueler'] == 1) {
		$show_formular_lehrer = false;
		$show_formular_schueler = true;
	}
	if ($show_formular_lehrer || $show_formular_schueler) {
		$year = get_prop("current_year");
		$allyears = get_prop("all_years");
		$allyears = explode("_", $allyears[1]);
		echo "Wähle Schuljahr:<br>";
		?>
<form action="index.php" method="get">
	<input type="hidden" value="output" name="page">
			<?php
		if ($show_formular_lehrer) {
			echo "<input type=\"hidden\" value=\"" . $_GET['lehrer'] . "\" name=\"lehrer\">";
		} else {
			echo "<input type=\"hidden\" value=\"" . $_GET['schueler'] . "\" name=\"schueler\">";
		}
		echo "<select name=\"year\">";
		for($i = 0; $i < count($allyears); $i++) {
			echo "<option value=\"$allyears[$i]\"";
			if (isset($_GET['year']) && strcmp($_GET['year'], $allyears[$i]) == 0) {
				echo " selected";
			}
			if (strcmp($year[1], $allyears[$i]) == 0) {
				if (!isset($_GET['year'])) {
					echo " selected>$allyears[$i] - aktuelles Schuljahr</option>";
				}
				echo ">$allyears[$i] - aktuelles Schuljahr</option>";
			} else
				echo ">$allyears[$i]</option>";
		}
		?></select>
	<br>
	<br>
	<input type="submit" value="Zeige">
	<br>
	<br>
	<br>
</form>
<?php
	}
	if (isset($_GET['year']) && array_search($_GET['year'], get_all_years()) !== false) {
		$table_schueler = "`schueler-" . $_GET['year'] . "`";
		$table_lehrer = "`lehrer-" . $_GET['year'] . "`";
	} else {
		$table_schueler = get_current_table("schueler");
		$table_lehrer = get_current_table("lehrer");
		$_GET['year'] = get_prop("current_year")[1];
	}
	if ($show_formular_lehrer) {
		$return_query = $pdo->query("SELECT * FROM " . $table_lehrer . " WHERE 1");
		if ($return_query == false) {
			echo "EIN PROBLEM";
		} else {
			$lehrer = $return_query->fetch();
			while ( $lehrer != false ) {
				$lehrer = validate_input($lehrer, true);
				if (!is_array($lehrer)) {
					echo "Ein Fehler trat auf $lehrer<br><br>";
				} else {
					echo "<fieldset style=\"padding: 40px; width: 80%;\">";
					echo "<legend><b>" . $lehrer['vname'] . " " . $lehrer['nname'] . "</b></legend>";
					?>
<div style="display: flex;">
	<div style="width: 50%; display: inline-block;">
					<?php
					echo "Name:    " . $lehrer['vname'] . " " . $lehrer['nname'] . "<br>Klasse: " . $lehrer['klassenstufe'];
					if (is_numeric($lehrer['klasse'])) {
						echo "/" . $lehrer['klasse'];
					} else {
						echo $lehrer['klasse'];
					}
					echo "<br>Email:   " . $lehrer['email'] . "<br>Klassenlehrer/Tutor: " . $lehrer['klassenlehrer_name'];
					echo "<br>1.Fach:   " . get_faecher_lesbar($lehrer['fach1']) . " bei " . $lehrer['fach1_lehrer'];
					if (isset($lehrer['fach2']) && strlen($lehrer['fach2']) != 0) {
						echo "<br>2.Fach:   " . get_faecher_lesbar($lehrer['fach2']) . " bei " . $lehrer['fach2_lehrer'];
					}
					if (isset($lehrer['fach3']) && strlen($lehrer['fach3']) != 0) {
						echo "<br>3.Fach:   " . get_faecher_lesbar($lehrer['fach3']) . " bei " . $lehrer['fach3_lehrer'];
					}
					?>
					<br>
		<br>
		<table class="time_output">
			<tr>
				<th></th>
				<th>Von:</th>
				<th>Bis:</th>
			</tr>
			<tr>
				<td>Montag:</td>
				<td><?php echo date("H:i", strtotime($lehrer['mo_anfang']));?></td>
				<td><?php echo date("H:i", strtotime($lehrer['mo_ende']));?></td>
			</tr>
			<tr>
				<td>Dienstag:</td>
				<td><?php echo date("H:i", strtotime($lehrer['di_anfang']));?></td>
				<td><?php echo date("H:i", strtotime($lehrer['di_ende']));?></td>
			</tr>
			<tr>
				<td>Mittwoch:</td>
				<td><?php echo date("H:i", strtotime($lehrer['mi_anfang']));?></td>
				<td><?php echo date("H:i", strtotime($lehrer['mi_ende']));?></td>
			</tr>
			<tr>
				<td>Donnerstag:</td>
				<td><?php echo date("H:i", strtotime($lehrer['do_anfang']));?></td>
				<td><?php echo date("H:i", strtotime($lehrer['do_ende']));?></td>
			</tr>
			<tr>
				<td>Freitag:</td>
				<td><?php echo date("H:i", strtotime($lehrer['fr_anfang']));?></td>
				<td><?php echo date("H:i", strtotime($lehrer['fr_ende']));?></td>
			</tr>
		</table>
	</div>
	<div style="width: 48%; display: inline-block; margin-left: 1%;">
		<a href="index.php?page=change&flehr=<?php echo $lehrer['id']."&year=".$_GET['year'];?>" class="links">Änder die Daten</a>
					<?php
					if (date('m', mktime(0, 0, 0, 7, 0)) < 9 && date('m', mktime(0, 0, 0, 7)) > 6 && $_GET['year'] == get_prop("current_year")[1]) {
						?>
					<br>
		<br>
		<br>
		<br>
		<a href="index.php?page=change&next_yearlehr=<?php echo $lehrer['id'];?>" class="links">Übernehmen ins nächste Jahr</a>
					<?php
					}
					?>
					<br>
		<br>
		<br>
		<br>
		<a href="?page=output&deletelehr=<?php echo $lehrer['id']."&year=".$_GET['year'];?>" class="links">Lösche Lehrer</a>
	</div>
</div>
</fieldset>
<?php
				}
				$lehrer = $return_query->fetch();
			}
		}
	}
	if ($show_formular_schueler) {
		$return_query = $pdo->query("SELECT * FROM " . $table_schueler . " WHERE 1");
		if ($return_query == false) {
			echo "EIN PROBLEM";
		} else {
			$schueler = $return_query->fetch();
			while ( $schueler != false ) {
				$schueler = validate_input($schueler, true);
				if (!is_array($schueler)) {
					echo "Ein Fehler trat auf $schueler<br><br>";
				} else {
					echo "<fieldset style=\"padding: 40px; width: 80%;\">";
					echo "<legend><b>" . $schueler['vname'] . " " . $schueler['nname'] . "</b></legend>";
					?>
<div style="display: flex;">
	<div style="width: 50%; display: inline-block;">
					<?php
					echo "Name:    " . $schueler['vname'] . " " . $schueler['nname'] . "<br>Klasse: " . $schueler['klassenstufe'];
					if (is_numeric($schueler['klasse'])) {
						echo "/" . $schueler['klasse'];
					} else {
						echo $schueler['klasse'];
					}
					echo "<br>Email:   " . $schueler['email'] . "<br>Klassenlehrer/Tutor: " . $schueler['klassenlehrer_name'];
					echo "<br>1.Fach:   " . get_faecher_lesbar($schueler['fach1']) . " bei " . $schueler['fach1_lehrer'];
					if (strlen($schueler['fach2']) != 0)
						echo "<br>2.Fach:   " . get_faecher_lesbar($schueler['fach2']) . " bei " . $schueler['fach2_lehrer'];
					if (strlen($schueler['fach3']) != 0)
						echo "<br>3.Fach:   " . get_faecher_lesbar($schueler['fach3']) . " bei " . $schueler['fach3_lehrer'];
					?>
					<br>
		<br>
		<table class="time_output">
			<tr>
				<th></th>
				<th>Von:</th>
				<th>Bis:</th>
			</tr>
			<tr>
				<td>Montag:</td>
				<td><?php echo date("H:i", strtotime($schueler['mo_anfang']));?></td>
				<td><?php echo date("H:i", strtotime($schueler['mo_ende']));?></td>
			</tr>
			<tr>
				<td>Dienstag:</td>
				<td><?php echo date("H:i", strtotime($schueler['di_anfang']));?></td>
				<td><?php echo date("H:i", strtotime($schueler['di_ende']));?></td>
			</tr>
			<tr>
				<td>Mittwoch:</td>
				<td><?php echo date("H:i", strtotime($schueler['mi_anfang']));?></td>
				<td><?php echo date("H:i", strtotime($schueler['mi_ende']));?></td>
			</tr>
			<tr>
				<td>Donnerstag:</td>
				<td><?php echo date("H:i", strtotime($schueler['do_anfang']));?></td>
				<td><?php echo date("H:i", strtotime($schueler['do_ende']));?></td>
			</tr>
			<tr>
				<td>Freitag:</td>
				<td><?php echo date("H:i", strtotime($schueler['fr_anfang']));?></td>
				<td><?php echo date("H:i", strtotime($schueler['fr_ende']));?></td>
			</tr>
		</table>
	</div>
	<div style="width: 40%; display: inline-block; margin-left: 1%;">
		<a href="index.php?page=change&fschuel=<?php echo $schueler['id']."&year=".$_GET['year'];?>" class="links">Änder die Daten</a>
					<?php
					if (date('m', mktime(0, 0, 0, 7, 0)) < 9 && date('m', mktime(0, 0, 0, 7)) > 6 && $_GET['year'] == get_prop("current_year")[1]) {
						?><br>
		<br>
		<br>
		<br>
		<a href="index.php?page=change&next_yearschuel=<?php echo $schueler['id'];?>" class="links">Übernehmen ins nächste Jahr</a>
					<?php }?>
					<br>
		<br>
		<br>
		<br>
		<a href="?page=<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>&deleteschuel=<?php echo $schueler['id']."&year=".$_GET['year'];?>" class="links">Lösche Schüler</a>
	</div>
</div>
</fieldset>
<?php
				}
				$schueler = $return_query->fetch();
			}
		}
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}

?>