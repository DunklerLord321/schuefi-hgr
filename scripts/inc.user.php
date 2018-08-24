<?php
if (isset($user) && $user->runscript()) {
	if (isset($_GET['reset_count'])) {
		$result = query_db("UPDATE `users` SET count_login = :count WHERE id = :id", 0, $_GET['reset_count']);
		if ($result !== false) {
			echo "Erfolgreich zurückgesetzt";
			echo "<br><a href=\"index.php?page=user\" class=\"links2\">Zurück zur Übersicht</a>";
		}else {
			echo "Ein Fehler ist beim Zurücksetzen aufgetreten";
		}
	}else if(isset($_GET['delete'])) {
		$result = query_db("UPDATE `users` SET aktiv = 0 WHERE id = :id", $_GET['delete']);
		if ($result !== false) {
			echo "Erfolgreich deaktiviert";
			echo "<br><a href=\"index.php?page=user\" class=\"links2\">Zurück zur Übersicht</a>";
		}else {
			echo "Ein Fehler ist beim Deaktivieren aufgetreten";
		}
	}else if(isset($_GET['activate'])) {
		$result = query_db("UPDATE `users` SET aktiv = 1 WHERE id = :id", $_GET['activate']);
		if ($result !== false) {
			echo "Erfolgreich aktiviert";
			echo "<br><a href=\"index.php?page=user\" class=\"links2\">Zurück zur Übersicht</a>";
		}else {
			echo "Ein Fehler ist beim Aktivieren aufgetreten";
		}
	}else if(isset($_GET['reset_passwd'])) {
		$user_to_change = new user();
		$user_to_change->load_user("", $_GET['reset_passwd']);
		var_dump($user_to_change);
		if (!$user->has_security_code()) {
			$user_to_change->create_security_token();
		}
		die();
	}else {
		$result = query_db("SELECT * FROM `users` ORDER BY `users`.`account` DESC");
		if ($result !== false) {
			$result_user = $result->fetch();
			While ($result_user) {
				echo "<fieldset style=\"padding: 40px; width: 80%; margin-bottom: 10px;line-height: 150%;\">";
				echo "<legend>";
				switch ($result_user['account']) {
					case 'v':
						echo "Administrator/Vorstand";
						break;
					case 'f':
						echo "Finanzler";
						break;
					case 'k':
						echo "Kundenbetreuer";
						break;
					case 'w':
						echo "Gast/Nur Lesend";
						break;
				}
				echo "</legend>";
				echo "Vorname: " . $result_user['vname'] . "<br>Nachname: " . $result_user['nname'] . "<br>E-Mail-Adresse: " . $result_user['email'];
				echo "<br>Erstellungsdatum: " . date('d.m.Y H:i', strtotime($result_user['createt_time']));
				echo "<br>Datum der letzten Änderung: " . date('d.m.Y H:i', strtotime($result_user['update_time']));
				echo "<br>Anzahl Loginversuche mit falschem Passwort: " . $result_user['count_login'];
				if (strlen($result_user['security_token']) > 0) {
					echo "<br><br>Der Person wurde bereits ein Registrierungslink gesendet, der bis zum ".date("d.m.y H:i",strtotime($result_user['security_token_time'])+3*24*3600)." gültig ist.";
				}
				if ($result_user['count_login'] > 0) {
					echo "   <a href=\"index.php?page=user&reset_count=" . $result_user['id'] . "\" class=\"links2\">Zurücksetzen und Login entsperren</a>";
				}
				if ($result_user['aktiv']) {
					echo "<br><a href=\"index.php?page=user&delete=" . $result_user['id'] . "\" class=\"links2\">Deaktivieren</a>";
				}else{
					echo "<br><br><i>Der Nutzer wurde bereits deaktiviert. Nutzer können nicht gelöscht werden, um die Daten für die Finanzabteilung zu erhalten.<i>";
					echo "<br><a href=\"index.php?page=user&activate=" . $result_user['id'] . "\" class=\"links2\">Aktivieren</a>";
				}
				if (strlen($result_user['security_token']) <= 0) {
					echo "<br><a href=\"index.php?page=user&reset_passwd=" . $result_user['id'] . "\" class=\"links2\">Passwort zurücksetzen</a>";
				}
				echo "</fieldset>";
				$result_user = $result->fetch();
			}
		}
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
