<?php
session_start ();
error_reporting(E_ALL);
$ret = strtok ( $_SERVER ['PHP_SELF'], "/" );
while ( $ret !== false ) {
	$ret = strtok ( "/" );
	if ($ret == false) {
		$active = $test;
	} else {
		$test = $ret;
	}
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Verwaltung der Daten der Schülerfirma</title>
<meta name="description"
	content="Verwalte die Daten der Schülerfirma 'Schüler helfen Schülern' einfach und automatisiert.">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="design.css" rel="stylesheet">
<!-- included für DatePicker -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$( function() {
  $( "#datepicker" ).datepicker();
} );
</script>
</head>
<body>
<script >
var ajax = function get_user_logged_in_ajax() {
	if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("log_in").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET","logged_in.php",true);
    xmlhttp.send();
}
// interval 60000 = jede Minute
var interval = 6000;
//setInterval(ajax,interval);
</script>
	<nav>
		<ul class="navigation">
			<li
				<?php if(strcmp($active, "content.php") == 0 ) { echo "class=\"navigation_active\""; }else { echo "class=\"navigation_li\"";}?>><a
				href="content.php">Hauptseite</a></li>
			<li
				<?php if(strcmp($active, "settings.php") == 0 ) { echo "class=\"navigation_active\""; }else { echo "class=\"navigation_li\"";}?>><a
				href="settings.php">Einstellungen</a></li>
			<li class="navigation_li"><a href="content.php" id="log_in">
			<script type="text/javascript"> function get_user_logged_in_ajax() {
				if (window.XMLHttpRequest) {
			        xmlhttp = new XMLHttpRequest();
			    } else {
			        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			    }
			    xmlhttp.onreadystatechange = function() {
			        if (this.readyState == 4 && this.status == 200) {
			            document.getElementById("log_in").innerHTML = this.responseText;
			        }
			    };
			    xmlhttp.open("GET","logged_in.php",true);
			    xmlhttp.send();
			} 
			ajax();</script></a></li>
			<li
				<?php if(strcmp($active, "logout.php") == 0 ) { echo "class=\"navigation_active\""; }else { echo "class=\"navigation_li\"";}?>>
<?php
if (isset ( $_SESSION ['userid'] ) && isset ( $_SESSION ['username'] )) {
	?>
				<a href="logout.php">Abmelden</a>
			</li>
			<li
				<?php if(strcmp($active, "user.php") == 0 ) { echo "class=\"navigation_active\""; }else { echo "class=\"navigation_li\"";}?>>
				<?php
	echo "<a href=\"user.php\">Du bist als " . $_SESSION ['username'] . " angemeldet.</a>";
} else {
	echo "<a href=\"index.php\">Du bist nicht angemeldet</a>";
}
?>
			</li>
		</ul>
	</nav>
	<nav>
		<ul class="nav_seite">
			<li
				<?php if(strcmp($active, "input.php") == 0 && strcmp($_SERVER['QUERY_STRING'], "schueler=1") == 0) { echo "class=\"active\""; }?>><a
				href="input.php?schueler=1"> Neuer Nachhilfeschüler</a></li>
			<li
				<?php if(strcmp($active, "input.php") == 0 && strcmp($_SERVER['QUERY_STRING'], "lehrer=1") == 0) { echo "class=\"active\""; }?>><a
				href="input.php?lehrer=1"> Neuer Nachhilfelehrer</a></li>
			<li
				<?php if(strcmp($active, "input_paar.php") == 0) { echo "class=\"active\""; }?>><a
				href="input_paar.php?paar=1"> Neues Paar</a></li>
			<li
				<?php if(strcmp($active, "output.php") == 0 && strcmp($_SERVER['QUERY_STRING'], "schueler=1") == 0) { echo "class=\"active\""; }?>><a
				href="output.php?schueler=1"> Ausgeben der Schüler</a></li>
				<?php if(strcmp($active, "change.php") == 0 && strpos($_SERVER['QUERY_STRING'], 'schuel') != false){?>
					<li class="active"><a href="output.php" style="text-align: right;"> Ändern eines Schülers</a></li><?php
				}?>
			<li
				<?php if(strcmp($active, "output.php") == 0 && strcmp($_SERVER['QUERY_STRING'], "lehrer=1") == 0) { echo "class=\"active\""; }?>><a
				href="output.php?lehrer=1"> Ausgeben der Lehrer</a></li>
				<?php if(strcmp($active, "change.php") == 0 && strpos($_SERVER['QUERY_STRING'], 'lehr') != false){?>
					<li class="active"><a href="output.php" style="text-align: right;"> Ändern eines Lehrers</a></li><?php
				}?>
			<li
				<?php if(strcmp($active, "output.php") == 0 && strcmp($_SERVER['QUERY_STRING'], "paare=1") == 0) { echo "class=\"active\""; }?>><a
				href="output.php?paare=1"> Ausgeben der Paare</a></li>
		</ul>
	</nav>
	<!-- - div endet in letzter Zeile   -->
	<div class="content">