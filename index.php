<?php
//Es darf keinerlei Ausgabe vor session_name() stattfinden
session_name("hgr-schuelerfirma");
session_start();
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Verwaltung der Daten der Schülerfirma</title>
<meta name="description" content="Verwalte die Daten der Schülerfirma 'Schüler helfen Schülern' einfach und automatisiert.">
<link href="css/design.css" rel="stylesheet">
<link href="includes/jquery/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet">
<script type="text/javascript" src="includes/jquery/jquery-3.2.1.min.js"></script>
<script src="includes/jquery/jquery-ui-1.12.1/jquery.js"></script>
<script src="includes/jquery/jquery-ui-1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php
require 'includes/global_vars.inc.php';
require 'includes/class_user.php';
try {
	$pdo = new PDO('mysql:host=' . $GLOBAL_CONFIG['host'] . ';dbname=' . $GLOBAL_CONFIG['dbname'], $GLOBAL_CONFIG['dbuser'], $GLOBAL_CONFIG['dbuser_passwd'], array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
	));
}catch (PDOException $e) {
	echo "<h1>Ein DB-Fehler ist aufgetreten (01)$e<h1>";
	die();
}
if (isset($_SESSION['user']) && strlen($_SESSION['user']) > 0) {
	$user = unserialize($_SESSION['user']);
}
if (!isset($user)) {
	$user = new user();
}
if (isset($_GET['reset'])) {
	$user->reset();
}
if (isset($_GET['page'])) {
	if ($_GET['page'] == 'logout' || ($GLOBAL_CONFIG['bauarbeiten'] == true && $user->getemail() != "yajo10@yahoo.de")) {
		$user->logout();
		?>
	<nav>
		<div class="navigation">
			<a class="navigation_li" href="index.php?login=1">Du bist nicht angemeldet. Anmelden</a>
		</div>
	</nav>
	<div class="content">
		<h1>Logout</h1>
		<br>
		<?php
		
		if ($GLOBAL_CONFIG['bauarbeiten']) {
			echo "Es finden gerade Bauarbeiten am System statt. Bitte versuche es später nochmal.<br><br>";
		}
		?>
		Sie wurden erfolgreich abgemeldet.
		<br>
		<br>
		<a href="index.php?login=1" class="links2">Hier geht es zum Login.</a>
	</div>
		<?php
	}else if ($user->is_valid()) {
		$active = $_GET['page'];
		?>
		<nav>
		<div id="nav_div">
		<button id="nav_button"><i class="fa fa-bars" style="color: white;">Menu</i></button>
		<ul class="nav_seite" id="nav_seite">
		<?php
		
		if ($user->isuserallowed('k')) {
			?>
			<li <?php
			
			if (strcmp($active, "person") == 0) {
				echo "class=\"active\"";
			}
			?>>
				<a href="index.php?page=person"> Neue Person</a>
			</li>
			<li <?php
			
			if (strcmp($active, "input") == 0 && strpos($_SERVER['QUERY_STRING'], "schueler=1") !== false) {
				echo "class=\"active\"";
			}
			?>>
				<a href="index.php?page=input&schueler=1"> Neuer Nachhilfeschüler</a>
			</li>
			<li <?php
			
			if (strcmp($active, "input") == 0 && strpos($_SERVER['QUERY_STRING'], "lehrer=1") !== false) {
				echo "class=\"active\"";
			}
			?>>
				<a href="index.php?page=input&lehrer=1"> Neuer Nachhilfelehrer</a>
			</li>
			<li <?php
			
			if (strcmp($active, "input_paar") == 0) {
				echo "class=\"active\"";
			}
			?>>
				<a href="index.php?page=input_paar&paar=1"> Neues Paar</a>
			</li>
			<?php
		}
		?>
			<li <?php
		
		if (strcmp($active, "output_person") == 0) {
			echo "class=\"active\"";
		}
		?>>
				<a href="index.php?page=output_person"> Ausgeben der Personen</a>
			</li>
			<?php
		if (strcmp($active, "change") == 0 && strpos($_SERVER['QUERY_STRING'], 'person') !== false) {
			?>
			<li class="active">
				<a href="index.php?page=output_person" style="text-align: right;"> Ändern einer Person</a>
			</li><?php
		}
		?>
			<li <?php
		
		if (strcmp($active, "output") == 0 && strpos($_SERVER['QUERY_STRING'], "schueler=1") !== false) {
			echo "class=\"active\"";
		}
		?>>
				<a href="index.php?page=output&schueler=1"> Ausgeben der Schüler</a>
			</li>
				<?php
		// Unterpunkt nur beim Löschen sichtbar
		if (strcmp($active, "output") == 0 && (strpos($_SERVER['QUERY_STRING'], 'deleteschuel') !== false || strpos($_SERVER['QUERY_STRING'], 'deleteconfirmschuel') !== false)) {
			?>
					<li class="active">
				<a href="index.php?page=output" style="text-align: right;"> Löschen eines Schülers</a>
			</li><?php
		}
		// Unterpunkt nur beim Ändern sichtbar
		if (strcmp($active, "change") == 0 && strpos($_SERVER['QUERY_STRING'], 'schuel') !== false) {
			?>
					<li class="active">
				<a href="index.php?page=output" style="text-align: right;"> Ändern eines Schülers</a>
			</li><?php
		}
		?>
			<li <?php
		
		if (strcmp($active, "output") == 0 && strpos($_SERVER['QUERY_STRING'], "lehrer=1") !== false) {
			echo "class=\"active\"";
		}
		?>>
				<a href="index.php?page=output&lehrer=1"> Ausgeben der Lehrer</a>
			</li>
				<?php
		// Unterpunkt nur beim Löschen sichtbar
		if (strcmp($active, "output") == 0 && (strpos($_SERVER['QUERY_STRING'], 'deletelehr') !== false || strpos($_SERVER['QUERY_STRING'], 'deleteconfirmlehr') !== false)) {
			?>
					<li class="active">
				<a href="index.php?page=output" style="text-align: right;"> Löschen eines Lehrers</a>
			</li><?php
		}
		// Unterpunkt nur bei Änderungen sichtbar
		if (strcmp($active, "change") == 0 && strpos($_SERVER['QUERY_STRING'], 'lehr') !== false) {
			?>
					<li class="active">
				<a href="index.php?page=output" style="text-align: right;"> Ändern eines Lehrers</a>
			</li><?php
		}
		?>
			<li <?php
		
		if (strcmp($active, "output") == 0 && strpos($_SERVER['QUERY_STRING'], "paare=1") !== false) {
			echo "class=\"active\"";
		}
		?>>
				<a href="index.php?page=output&paare=1"> Ausgeben der Paare</a>
			</li>
			<li <?php
		
		if (strcmp($active, "mail") == 0) {
			echo "class=\"active\"";
		}
		?>>
				<a href="index.php?page=mail&step=1"> Sende E-Mail</a>
			</li>
			<?php
		
		if ($user->isuserallowed('f')) {
			?>
			<li <?php
			
			if (strcmp($active, "input_finanzen") == 0) {
				echo "class=\"active\"";
			}
			?>>
				<a href="index.php?page=input_finanzen">Eingabe Finanzen</a>
			</li>
			<?php
		}
		?>
			<li <?php
		
		if (strcmp($active, "output_finanzen") == 0) {
			echo "class=\"active\"";
		}
		?>>
				<a href="index.php?page=output_finanzen">Ausgabe Finanzen</a>
			</li>
		</ul>
		</div>
	</nav>
		
		
		<nav class="nav" id="nav">
		<div class="navigation">
			<a <?php
		if (strcmp($active, "content") == 0) {
			echo "class=\"navigation_active\"";
		}else {
			echo "class=\"navigation_li\"";
		}
		?> href="index.php?page=content">Hauptseite</a>
			<a <?php
		if (strcmp($active, "change_passwd") == 0) {
			echo "class=\"navigation_active\"";
		}else {
			echo "class=\"navigation_li\"";
		}
		?> href="index.php?page=change_passwd" id="log_in">Passwort ändern</a>
		<?php
		if ($user->isuserallowed('v')) {
			if (strcmp($active, "settings") == 0 || strcmp($active, "backup_data") == 0 || strcmp($active, "user") == 0) {
				echo " <div class=\"dropdiv dropdiv_active";
			}else {
				echo "<div class=\"dropdiv";
			}
			echo "\"><button class=\"dropdown\">Einstellungen</button><div class=\"dropdown-content\"><a href=\"index.php?page=settings\">Log-Datei</a>
						<a href=\"index.php?page=user\">Ausgeben aller Nutzer</a>
						<a href=\"index.php?page=backup_data\">Backups</a></div></div>";
		}
		?>
			<a <?php
		
		if (strcmp($active, "logout") == 0) {
			echo "class=\"navigation_active\"";
		}else {
			echo "class=\"navigation_li\"";
		}
		?> href="index.php?page=logout">Abmelden</a>
				<?php
		
		echo "<a id=\"angemeldet\" class=\"navigation_li\" href=\"index.php?page=change_passwd\">Du bist als " . $user->getemail() . " angemeldet.</a>";
		?>
		</div>
		</nav>
	<!-- - div endet in letzter Zeile   -->
	<div class="content">
	<?php
		require 'includes/functions.inc.php';
		$ret_prep = query_db("SELECT * FROM navigation WHERE kuerzel = :kuerzel", $_GET['page']);
		$result = $ret_prep->fetch();
		if ($result !== false) {
			if ($user->isuserallowed($result['allowed_users'])) {
				if ($result['visible'] == 1) {
					if (file_exists($result['path'])) {
						$user->allowrunscript();
						require $result['path'];
						$user->denyrunscript();
					}else {
						print("Die angefragte Seite konnte nicht gefunden werden!");
					}
				}else {
					echo "Die Seite ist im Moment gesperrt.";
				}
			}else {
				echo "Die gewünschte Seite darfst du nicht besuchen!";
			}
		}else {
			echo "Die angefragte Seite wurde nicht gefunden!";
		}
		
		?>
	</div>
	<?php
	}else {
		?>
	<nav>
		<div class="navigation">
			<!-- -- <li class="navigation_li"><a href="index">Login</a></li> -->
			<a class="navigation_li" href="index.php?login=1">Du bist nicht angemeldet. Anmelden</a>
		</div>
	</nav>
	<div class="content">
		<h1>Logout</h1>
		<br>
		Sie wurden leider vom System abgemeldet. Bitte melden Sie sich erneut an.
		<br>
		<br>
		<a href="index.php?login=1" class="links2">Hier geht es zum Login.</a>
	</div>
<?php
		die();
	}
}else {
	$user->logout();
	?>
	<nav>
		<div class="navigation">
			<!-- -- <li class="navigation_li"><a href="index">Login</a></li> -->
			<a class="navigation_li" href="index.php?login=1">Du bist nicht angemeldet. Anmelden</a>
		</div>
	</nav>
	<div class="content">
		<?php
	$user->allowrunscript();
	include 'includes/inc.login.php';
	$user->denyrunscript();
	?>
	</div>
<?php
}
?>
</body>
</html>