<?php
require 'header.php';
require 'includes/global_vars.inc.php';
if(! function_exists( 'get_users_logged_in' ) ) {
	include 'includes/functions.inc.php';
}
echo 		"<h2>Ausgabe</h2>";
if(isset($_SESSION['userid']) && isset( $_SESSION['username'])  && if_logged_in($_SESSION['userid'])) {
	$show_formular_schueler = false;
	$show_formular_lehrer = false;
	
	if (isset ( $_GET ['lehrer'] ) && $_GET ['lehrer'] == 1) {
		$show_formular_lehrer = true;
		$show_formular_schueler = false;
	} else if (isset ( $_GET ['schueler'] ) && $_GET ['schueler'] == 1) {
		$show_formular_lehrer = false;
		$show_formular_schueler = true;
	}
	if($show_formular_lehrer) {
		$pdo_insert = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
		$return_query = $pdo_insert->query("SELECT * FROM lehrer WHERE 1");
		if($return_query == false) {
			echo "EIN PROBLEM";
		}else {
			$lehrer = $return_query->fetch();
			while ($lehrer != false) {
				echo "<fieldset style=\"padding: 40px; width: 80%;\">";
				echo "<legend><b>".$lehrer['vname']." ".$lehrer['nname']."</b></legend>";
				echo "<div style=\"width: 50%; display: inline-block;\">";
				echo "Name:    ".$lehrer['vname']." ".$lehrer['nname']."<br>Email:   ".$lehrer['email']."<br>Klassenlehrer/Tutor: ".$lehrer['klassenlehrer_name'];
				echo "<br>1.Fach:   ".$lehrer['fach1']." bei ".$lehrer['fach1_lehrer'];
				if(strlen($lehrer['fach2']) != 0)
					echo "<br>2.Fach:   ".$lehrer['fach2']." bei ".$lehrer['fach2_lehrer'];
				if(strlen($lehrer['fach3']) != 0)
					echo "<br>3.Fach:   ".$lehrer['fach3']." bei ".$lehrer['fach3_lehrer'];
				echo "<br>Montag von ".$lehrer['mo_anfang']." bis ".$lehrer['mo_ende'];
				echo "<br>Dienstag von ".$lehrer['di_anfang']." bis ".$lehrer['di_ende'];
				echo "<br>Mittwoch von ".$lehrer['mi_anfang']." bis ".$lehrer['mi_ende'];
				echo "<br>Donnerstag von ".$lehrer['do_anfang']." bis ".$lehrer['do_ende'];
				echo "<br>Freitag von ".$lehrer['fr_anfang']." bis ".$lehrer['fr_ende'];
				echo "</div>";
				?>
				<div style="width: 40%; display: inline-block; margin-left: 1%;">
				<?php
				if( date('m', mktime(0,0,0,7,0)) < 9 && date('m', mktime(0,0,0,7)) > 6) {
					?>
					<button class="mybuttons" onclick="nextyear(<?php echo $lehrer['id'];?>)">ODER? Ins nächste Jahr nehmen</button>
					<script type="text/javascript">
					function nextyear(test) {
						
					</script>
					<?php
				}
				?>
				<a href="change.php?flehr=<?php echo $lehrer['id'];?>" class="links">Änder die Daten</a>
				</div>
				</fieldset>
				<?php
				$lehrer = $return_query->fetch();
			}
		}
	}
	if($show_formular_schueler)  {
		$pdo_insert = new PDO ( "mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd );
		$return_query = $pdo_insert->query("SELECT * FROM schueler WHERE 1");
		if($return_query == false) {
			echo "EIN PROBLEM";
		}else {
			$schueler = $return_query->fetch();
			while ($schueler != false) {
				echo "<fieldset style=\"padding: 40px; width: 80%;\">";
				echo "<legend><b>".$schueler['vname']." ".$schueler['nname']."</b></legend>";
				echo "<div style=\"width: 50%; display: inline-block;\">";
				echo "Name:    ".$schueler['vname']." ".$schueler['nname']."<br>Email:   ".$schueler['email']."<br>Klassenlehrer/Tutor: ".$schueler['klassenlehrer_name'];
				echo "<br>1.Fach:   ".$schueler['fach1']." bei ".$schueler['fach1_lehrer'];
				if(strlen($schueler['fach2']) != 0)
					echo "<br>2.Fach:   ".$schueler['fach2']." bei ".$schueler['fach2_lehrer'];
					if(strlen($schueler['fach3']) != 0)
						echo "<br>3.Fach:   ".$schueler['fach3']." bei ".$schueler['fach3_lehrer'];
					echo "<br>Montag von ".$schueler['mo_anfang']." bis ".$schueler['mo_ende'];
					echo "<br>Dienstag von ".$schueler['di_anfang']." bis ".$schueler['di_ende'];
					echo "<br>Mittwoch von ".$schueler['mi_anfang']." bis ".$schueler['mi_ende'];
					echo "<br>Donnerstag von ".$schueler['do_anfang']." bis ".$schueler['do_ende'];
					echo "<br>Freitag von ".$schueler['fr_anfang']." bis ".$schueler['fr_ende'];
					echo "</div>";?>
				<div style="width: 40%; display: inline-block; margin-left: 1%;">
				<a href="change.php?fschuel=<?php echo $schueler['id'];?>" class="links">Änder die Daten</a>
				</div>
				</fieldset>
				<?php
					$schueler = $return_query->fetch();
			}
		}
	}
	
}
?>
</div>
	<footer>Designed by Yannik Weber</footer>
	</body>
	</html>
	