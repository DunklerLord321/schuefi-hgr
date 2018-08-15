<?php
//Es darf keinerlei Ausgabe vor session_name() stattfinden
session_name("hgr-schuelerfirma");
session_start();
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Verwaltung der Daten der Schülerfirma</title>
<meta name="description" content="Verwalte die Daten der Schülerfirma 'Schüler helfen Schülern' einfach und automatisiert.">
<link href="css/design.css" rel="stylesheet">
<link href="includes/jquery/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet">
<script type="text/javascript" src="includes/jquery/jquery-3.2.1.min.js"></script>
<script src="includes/jquery/jquery-ui-1.12.1/jquery.js"></script>
<script src="includes/jquery/jquery-ui-1.12.1/jquery-ui.js"></script>
<!-- -<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">--->
</head>
<body>
<div id="topdiv" style="top: 0;"></div>
<?php
require 'includes/global_vars.inc.php';
require 'includes/class_user.php';
require 'includes/functions.inc.php';
setlocale(LC_TIME, 'de_DE.UTF-8');
$xml = init_settings_xml();
if (null !== get_xml("livesystem","value") && get_xml("livesystem","value") != 'true') {
	error_reporting(E_ALL);
}else{
	error_reporting(E_ERROR);
}
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
if (isset($_GET['page'])) {
	if ($_GET['page'] == 'logout' || (get_xml("bauarbeiten","value") == "true" && !$user->is_admin())) {
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
		
		if (get_xml("bauarbeiten","value") == "true") {
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
		//@TODO Navigation in Funktion
    require 'includes/navigation_functions.inc.php';
    show_main_navigation($active); 
		?>
		<nav class="nav" id="nav">
		<div class="navigation">
			<a <?php
		if (strcmp($active, "content") == 0) {
			echo "class=\"navigation_active\"";
		}else {
			echo "class=\"navigation_li\"";
		}
		if ($user->getaccount() == 'c') {
			?> href="index.php?page=customer_meetings">Hauptseite</a>
				<a <?php			
		}else{
			?> href="index.php?page=content">Hauptseite</a>
				<a <?php
		}
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
			echo "\"><a class=\"dropdown\">Einstellungen</a><div class=\"dropdown-content\"><a href=\"index.php?page=settings\">Einstellungen</a>
						<a href=\"index.php?page=user\">Ausgeben aller Nutzer</a>
						<a href=\"index.php?page=backup_data\">Backups</a></div></div>";
		}
		?>
			<span class="navigation_li" style="background-color: #0b1162;"><?php echo get_xml("servername", "value");?></span>
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
	<div class="content" id="content">
	<?php
		$ret_prep = query_db("SELECT * FROM navigation WHERE kuerzel = :kuerzel", $_GET['page']);
		$result = $ret_prep->fetch();
		//Testen, ob die angefragte Seite in der Datenbank hinterlegt ist
		if ($result !== false) {
			// Testen, ob der Nutzer Zugang zu der Seite hat
			if ($user->isuserallowed($result['allowed_users'])) {
				//Testen, ob die Seite momentan gesperrt ist
				if ($result['visible'] == 1) {
					//Testen. ob das Skript unter dem in der DB gespeicherten Pfad existiert
					if (file_exists($result['path'])) {
						//Nutzer erlaube, das Skript auszuführen (Schützt vor Ausführung der Skripte ohne Nutzung der Hauptseite
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
	<footer>
		<div style="width: 80%; margin-left: 20%;">
			<noscript>Bitte Aktivieren sie JavaScript in Ihrem Browser, um diese Website zu nutzen</noscript>
		</div>
	</footer>
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
			<a id="login" class="navigation_li" href="index.php?login=1">Du bist nicht angemeldet. Anmelden</a>
		</div>
	</nav>
	<div class="content">
		<?php
	$user->allowrunscript();
	include 'scripts/inc.login.php';
	$user->denyrunscript();
	?>
	</div>
<?php
}
?>
</body>
</html>