<?php
session_start ();
require 'includes/functions.inc.php';
require 'includes/global_vars.inc.php';
log_out($_SESSION['userid']);
unset( $_SESSION [ 'userid']);
unset ( $_SESSION ['username'] );
unset ( $_SESSION ['userid'] );
unset ( $_SESSION ['vname'] );
unset ( $_SESSION ['nname'] );
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Verwaltung der Daten der Schülerfirma</title>
<meta name="verwaltung"
	content="Verwalte die Daten der Schülerfirma 'Schüler helfen Schülern' einfach und automatisiert.">
<link href="design.css" rel="stylesheet">
</head>
<body>
	<nav>
		<ul class="navigation">
			<li class="navigation_li"><a href="content.php">Hauptseite</a></li>
			<!-- -- <li class="navigation_li"><a href="index.php">Login</a></li> -->
			<li class="navigation_li"><a href="index.php">Du bist nicht angemeldet. Anmelden</a></li>
		</ul>
	</nav>
	<div class="content">
		<h1>Logout</h1>
		<br>Sie wurden erfolgreich abgemeldet.<br>
		<br> <a href="index.php">Hier geht es zum Login.</a>
	</div>
</body>
</html>