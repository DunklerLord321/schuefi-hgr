<?php
require 'header.php';
require 'includes/global_vars.inc.php';
if (! function_exists ( 'get_users_logged_in' )) {
	include 'includes/functions.inc.php';
}
echo "<h2>Ausgabe</h2>";
if (isset ( $_SESSION ['userid'] ) && isset ( $_SESSION ['username'] ) && if_logged_in ( $_SESSION ['userid'] )) {
	$show_formular_schueler = false;
	$show_formular_lehrer = false;
	
	if(isset ($_GET['deletelehr']) && is_numeric($_GET['deletelehr'])) {
		?>
 		<form action="<?php echo $_SERVER['PHP_SELF']."?deleteconfirmlehr=".$_GET['deletelehr'];?>" method="post">
		Achtung! Dieser Vorgang kann nicht rückgängig gemacht werden.<br> 
		Trotzdem fortsetzen?<br><br>
		<input type="submit" value="Ok">
		</form>
		<?php 
	}
	if(isset ($_GET['deleteconfirmlehr']) && is_numeric($_GET['deleteconfirmlehr'])) {
		$pdo_insert = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
		$return_query = $pdo_insert->prepare( "DELETE FROM ".get_current_table("lehrer")." WHERE id = :id" );
		$return = $return_query->execute(array (
				'id' => $_GET['deleteconfirmlehr']
		));
		if($return == false) {
			echo "Ein Problem ist aufgetreten";
		}else{
			echo "Löschen war erfolgreich";
		}
	}
	if(isset ($_GET['deleteschuel']) && is_numeric($_GET['deleteschuel'])) {
		?>
	 		<form action="<?php echo $_SERVER['PHP_SELF']."?deleteconfirmschuel=".$_GET['deleteschuel'];?>" method="post">
			Achtung! Dieser Vorgang kann nicht rückgängig gemacht werden.<br> 
			Trotzdem fortsetzen?<br><br>
			<input type="submit" value="Ok">
			</form>
			<?php 
		}
		if(isset ($_GET['deleteconfirmschuel']) && is_numeric($_GET['deleteconfirmschuel'])) {
			$pdo_insert = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
			$return_query = $pdo_insert->prepare( "DELETE FROM ".get_current_table("schueler")." WHERE id = :id" );
			$return = $return_query->execute(array (
					'id' => $_GET['deleteconfirmschuel']
			));
			if($return == false) {
				echo "Ein Problem ist aufgetreten";
			}else{
				echo "Löschen war erfolgreich";
			}
		}
		
	if (isset ( $_GET ['lehrer'] ) && $_GET ['lehrer'] == 1) {
		$show_formular_lehrer = true;
		$show_formular_schueler = false;
	} else if (isset ( $_GET ['schueler'] ) && $_GET ['schueler'] == 1) {
		$show_formular_lehrer = false;
		$show_formular_schueler = true;
	}
	if ($show_formular_lehrer) {
		$pdo_insert = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
		$return_query = $pdo_insert->query ( "SELECT * FROM ".get_current_table("lehrer")." WHERE 1" );
		if ($return_query == false) {
			echo "EIN PROBLEM";
		} else {
			$lehrer = $return_query->fetch ();
			while ( $lehrer != false ) {
				$lehrer = validate_input ( $lehrer, true );
				if (! is_array ( $lehrer )) {
					echo "Ein Fehler trat auf $lehrer<br><br>";
				} else {
					echo "<fieldset style=\"padding: 40px; width: 80%;\">";
					echo "<legend><b>" . $lehrer ['vname'] . " " . $lehrer ['nname'] . "</b></legend>";
					?>
					<div style="display: flex;">
					<div style="width: 50%; display: inline-block;">
					<?php
					echo "Name:    " . $lehrer ['vname'] . " " . $lehrer ['nname'] . "<br>Klasse: ".$lehrer['klassenstufe'];
					if(is_numeric($lehrer['klasse'])) {
						echo "/".$lehrer['klasse'];
					}else{
						echo $lehrer['klasse'];
					}
					echo "<br>Email:   " . $lehrer ['email'] . "<br>Klassenlehrer/Tutor: " . $lehrer ['klassenlehrer_name'];
					echo "<br>1.Fach:   " . get_faecher_lesbar($lehrer ['fach1']) . " bei " . $lehrer ['fach1_lehrer'];
					if (isset($lehrer['fach2']) && strlen ( $lehrer ['fach2'] ) != 0) {
						echo "<br>2.Fach:   " . get_faecher_lesbar($lehrer ['fach2']) . " bei " . $lehrer ['fach2_lehrer'];
					}
					if (isset($lehrer['fach3']) && strlen ( $lehrer ['fach3'] ) != 0) {
						echo "<br>3.Fach:   " .get_faecher_lesbar($lehrer ['fach3']) . " bei " . $lehrer ['fach3_lehrer'];
					}
					?>
					<br><br>
					<table class="time_output">
					<tr>
						<th></th><th>Von:</th><th>Bis:</th>
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
					<a href="change.php?flehr=<?php echo $lehrer['id'];?>" class="links">Änder die Daten</a>
					<?php
					if (date ( 'm', mktime ( 0, 0, 0, 7, 0 ) ) < 9 && date ( 'm', mktime ( 0, 0, 0, 7 ) ) > 6) {
					?>
					<br><br><br><br><a href="change.php?next_yearlehr=<?php echo $lehrer['id'];?>" class="links">Übernehmen ins nächste Jahr</a>
					<?php
					}
					?>
					<br><br><br><br><a href="<?php echo $_SERVER['PHP_SELF'];?>?deletelehr=<?php echo $lehrer['id'];?>" class="links">Lösche Lehrer</a>
					</div>
				</div>
				</fieldset>
				<?php
				}
				$lehrer = $return_query->fetch ();
			}
		}
	}
	if ($show_formular_schueler) {
		$pdo_insert = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
		$return_query = $pdo_insert->query ( "SELECT * FROM ".get_current_table("schueler")." WHERE 1" );
		if ($return_query == false) {
			echo "EIN PROBLEM";
		} else {
			$schueler = $return_query->fetch ();
			while ( $schueler != false ) {
				$schueler = validate_input ( $schueler, true );
				if (! is_array ( $schueler )) {
					echo "Ein Fehler trat auf $schueler<br><br>";
				} else {
					echo "<fieldset style=\"padding: 40px; width: 80%;\">";
					echo "<legend><b>" . $schueler ['vname'] . " " . $schueler ['nname'] . "</b></legend>";
					?>
					<div style="display: flex;">
					<div style="width: 50%; display: inline-block;">
					<?php 
					echo "Name:    " . $schueler ['vname'] . " " . $schueler ['nname'] ."<br>Klasse: ".$schueler['klassenstufe'];
					if(is_numeric($schueler['klasse'])) {
						echo "/".$schueler['klasse'];
					}else{
						echo $schueler['klasse'];
					}
					echo "<br>Email:   " . $schueler ['email'] . "<br>Klassenlehrer/Tutor: " . $schueler ['klassenlehrer_name'];
					echo "<br>1.Fach:   " . get_faecher_lesbar($schueler ['fach1']) . " bei " . $schueler ['fach1_lehrer'];
					if (strlen ( $schueler ['fach2'] ) != 0)
						echo "<br>2.Fach:   " . get_faecher_lesbar($schueler ['fach2']) . " bei " . $schueler ['fach2_lehrer'];
					if (strlen ( $schueler ['fach3'] ) != 0)
						echo "<br>3.Fach:   " .get_faecher_lesbar($schueler ['fach3']) . " bei " . $schueler ['fach3_lehrer'];
						?>
					<br><br>
					<table class="time_output">
					<tr>
						<th></th><th>Von:</th><th>Bis:</th>
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
					<a href="change.php?fschuel=<?php echo $schueler['id'];?>" class="links">Änder die Daten</a>
					<?php
					if (date ( 'm', mktime ( 0, 0, 0, 7, 0 ) ) < 9 && date ( 'm', mktime ( 0, 0, 0, 7 ) ) > 6) {
					?><br><br><br><br>
					<a href="change.php?next_yearschuel=<?php echo $schueler['id'];?>" class="links">Übernehmen ins nächste Jahr</a>
					<?php }?>
					<br><br><br><br><a href="<?php echo $_SERVER['PHP_SELF'];?>?deleteschuel=<?php echo $schueler['id'];?>" class="links">Lösche Schüler</a>
					</div>
					</div>
					</fieldset>
					<?php
				}
				$schueler = $return_query->fetch ();
			}
		}
	}
}
?>
</div>
<footer>Designed by Yannik Weber</footer>
</body>
</html>
