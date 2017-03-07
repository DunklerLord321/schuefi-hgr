<?php
session_start ();
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
<meta name="verwaltung"
	content="Verwalte die Daten der Schülerfirma 'Schüler helfen Schülern' einfach und automatisiert.">
<link href="design.css" rel="stylesheet">
</head>
<body>
	<nav>
		<ul class="navigation">
			<li
				<?php if(strcmp($active, "content.php") == 0 ) { echo "class=\"navigation_active\""; }else { echo "class=\"navigation_li\"";}?>><a
				href="content.php">Hauptseite</a></li>
			<li
				<?php if(strcmp($active, "index.php") == 0 ) { echo "class=\"navigation_active\""; }else { echo "class=\"navigation_li\"";}?>><a
				href="index.php">Login</a></li>
			<li class="navigation_li"><a href="content.php"><?php
			if (isset ( $_SESSION ['userid'] ) && isset ( $_SESSION ['username'] )) {
				echo "hihi";
				include "includes/global_vars.inc.php";
				echo "hallo".$login_user;
				$pdo_login = new PDO ( 'mysql:host=localhost;dbname=schuefi_login', $login_user, $login_user_passwd );
				echo "tetetete";
				$ret_prep = $pdo_login->query ( "SELECT * FROM `users` " );
				if($ret_prep == false) {
					echo "Failure";
				}
				echo "tatda";
				$return = $ret_prep->fetch ();
				echo "while";
				$i = 0;
				$logged_user = array ();
				while ( $return != false ) {
					echo "test";
					if ($return ['logged_in']) {
						$last_active = strtotime ( $return ['last_active'] );
						if ($last_active < strtotime ( "-30 Minutes" )) {
							echo "try log out" . $return ['id'];
							//				log_out ( $return ['	id'] );
						} else {
							$i ++;
							$logged_user [] = array (
									"vname" => $return ['vname'],
									"nname" => $return ['nname'],
									"email" => $return ['email']
							);
						}
					}
					$return = $ret_prep->fetch ();
				}
				$ret_array = array (
						$i,
						$logged_user
				);
				echo "included";
//				$return = get_users_logged_in ();
				echo "return";
				if ($ret_array [0] == 1) {
					echo "Ein Nutzer ist angemeldet.";
				} else {
					echo $ret_array [0] . " Nutzer sind angemeldet.";
				}
			} else {
				echo "Kein Nutzen angemeldet.";
			}
			?></a></li>
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
			<li
				<?php if(strcmp($active, "output.php") == 0 && strcmp($_SERVER['QUERY_STRING'], "lehrer=1") == 0) { echo "class=\"active\""; }?>><a
				href="output.php?lehrer=1"> Ausgeben der Lehrer</a></li>
			<li
				<?php if(strcmp($active, "output.php") == 0 && strcmp($_SERVER['QUERY_STRING'], "paare=1") == 0) { echo "class=\"active\""; }?>><a
				href="output.php?paare=1"> Ausgeben der Paare</a></li>
		</ul>
	</nav>
	<!-- - div endet in letzter Zeile   -->
	<div class="content">