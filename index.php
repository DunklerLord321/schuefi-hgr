<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Verwaltung der Daten der Schülerfirma</title>
<meta name="description" content="Verwalte die Daten der Schülerfirma 'Schüler helfen Schülern' einfach und automatisiert.">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="design.css" rel="stylesheet">
<link href="includes/jquery/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet">
<script type="text/javascript" src="includes/jquery/jquery-3.2.1.min.js"></script>
<script src="includes/jquery/jquery-ui-1.12.1/jquery.js"></script>
<script src="includes/jquery/jquery-ui-1.12.1/jquery-ui.js"></script>


</head>
<body>
<?php
session_start();
error_reporting(E_ALL);
require 'includes/global_vars.inc.php';
require 'includes/class_user.php';
$pdo_obj = new PDO('mysql:host=localhost;dbname=schuefi', $GLOBAL_CONFIG['dbuser'], $GLOBAL_CONFIG['dbuser_passwd'], array(
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
));
if (isset($_SESSION['user']) && strlen($_SESSION['user']) > 0) {
	$user = unserialize($_SESSION['user']);
}
if (!isset($user)) {
	echo "Neuer Nutzer";
	$user = new user();
}
if (isset($_GET['reset'])) {
	$user->reset();
}

if (isset($_GET['page'])) {
	if ($_GET['page'] == 'logout') {
		?>
	<nav>
		<ul class="navigation">
			<!-- -- <li class="navigation_li"><a href="index">Login</a></li> -->
			<li class="navigation_li">
				<a href="index.php?login=1">Du bist nicht angemeldet. Anmelden</a>
			</li>
		</ul>
	</nav>
	<div class="content">
		<h1>Logout</h1>
		<br>
		Sie wurden erfolgreich abgemeldet.
		<br>
		<br>
		<a href="index.php?login=1">Hier geht es zum Login.</a>
	</div>
		<?php
	} else if ($user->is_valid()) {
		$active = $_GET['page'];
		?>
	<nav>
		<ul class="navigation">
			<li <?php if(strcmp($active, "content") == 0 ) { echo "class=\"navigation_active\""; }else { echo "class=\"navigation_li\"";}?>>
				<a href="index.php?page=content">Hauptseite</a>
			</li>
			<li <?php if(strcmp($active, "settings") == 0 ) { echo "class=\"navigation_active\""; }else { echo "class=\"navigation_li\"";}?>>
				<a href="index.php?page=settings">Einstellungen</a>
			</li>
			<li <?php if(strcmp($active, "user") == 0 ) { echo "class=\"navigation_active\""; }else { echo "class=\"navigation_li\"";}?>>
				<a href="index.php?page=user" id="log_in">Passwort ändern</a>
			</li>
			<li <?php if(strcmp($active, "logout") == 0 ) { echo "class=\"navigation_active\""; }else { echo "class=\"navigation_li\"";}?>>
				<a href="index.php?page=logout">Abmelden</a>
			</li>
			<li class="navigation_li">
				<?php
		echo "<a href=\"index.php?page=user\">Du bist als " . $user->getemail() . " angemeldet.</a>";
		?>
			</li>
		</ul>
	</nav>
	<nav>
		<ul class="nav_seite">
			<li <?php if(strcmp($active, "person") == 0 ) { echo "class=\"active\""; }?>>
				<a href="index.php?page=person"> Neue Person</a>
			</li>
			<li <?php if(strcmp($active, "input") == 0 && strpos($_SERVER['QUERY_STRING'], "schueler=1") !== false) { echo "class=\"active\""; }?>>
				<a href="index.php?page=input&schueler=1"> Neuer Nachhilfeschüler</a>
			</li>
			<li <?php if(strcmp($active, "input") == 0 && strpos($_SERVER['QUERY_STRING'], "lehrer=1") !== false) { echo "class=\"active\""; }?>>
				<a href="index.php?page=input&lehrer=1"> Neuer Nachhilfelehrer</a>
			</li>
			<li <?php if(strcmp($active, "input_paar") == 0) { echo "class=\"active\""; }?>>
				<a href="index.php?page=input_paar&paar=1"> Neues Paar</a>
			</li>
			<li <?php if(strcmp($active, "output_person") == 0) { echo "class=\"active\""; }?>>
				<a href="index.php?page=output_person"> Ausgeben der Personen</a>
			</li>
			<li <?php if(strcmp($active, "output") == 0 && strpos($_SERVER['QUERY_STRING'], "schueler=1") !== false) { echo "class=\"active\""; }?>>
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
			<li <?php if(strcmp($active, "output") == 0 && strpos($_SERVER['QUERY_STRING'], "lehrer=1") !== false) { echo "class=\"active\""; }?>>
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
			<li <?php if(strcmp($active, "output") == 0 && strcmp($_SERVER['QUERY_STRING'], "paare=1") == 0) { echo "class=\"active\""; }?>>
				<a href="index.php?page=output&paare=1"> Ausgeben der Paare</a>
			</li>
			<li <?php if(strcmp($active, "mail") == 0 ) { echo "class=\"active\""; }?>>
				<a href="index.php?page=mail&step=1"> Sende E-Mail</a>
			</li>
		</ul>
	</nav>
	<!-- - div endet in letzter Zeile   -->
	<div class="content">
	<?php
		$pdo = new PDO("mysql:host=localhost;dbname=schuefi", $dbuser, $dbuser_passwd, array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
		));
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
					} else {
						print("Die angefragte Seite konnte nicht gefunden werden!");
					}
				} else {
					echo "Die Seite ist im Moment gesperrt.";
				}
			} else {
				echo "Die gewünschte Seite darfst du nicht besuchen!";
			}
		} else {
			echo "Die angefragte Seite wurde nicht gefunden!";
		}
		
		?>
	</div>
	<?php
	} else {
		?>
	<nav>
		<ul class="navigation">
			<!-- -- <li class="navigation_li"><a href="index">Login</a></li> -->
			<li class="navigation_li">
				<a href="index.php?login=1">Du bist nicht angemeldet. Anmelden</a>
			</li>
		</ul>
	</nav>
	<div class="content">
		<h1>Logout</h1>
		<br>
		Sie wurden leider vom System abgemeldet. Bitte melden Sie sich erneut an.
		<br>
		<br>
		<a href="index.php?login=1">Hier geht es zum Login.</a>
	</div>
<?php
		die();
	}
} else {
	$user->logout();
	?>
	<nav>
		<ul class="navigation">
			<!-- -- <li class="navigation_li"><a href="index.php">Login</a></li> -->
			<li class="navigation_li">
				<a href="index.php?login=1">Du bist nicht angemeldet. Anmelden</a>
			</li>
		</ul>
	</nav>
	<div class="content">
		<?php
	// soll später entfernt werden
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