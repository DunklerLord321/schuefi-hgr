<?php
if (isset($user) && $user->runscript()) {
	global $pdo;
	echo "<h2>Ausgabe</h2>";
	$return = $pdo->query("SELECT * FROM `person`");
	$result = $return->fetch();
	require 'includes/class_person.php';
	require 'includes/class_lehrer.php';
	$person = new person();
	if ($result !== false) {
		while ( $result ) {
			echo $result['vname'];
			$person->load_person($result['id']);
			var_dump($person);
			$person->search_lehrer_schueler();
			$result = $return->fetch();
			$lehrer = new lehrer($person->id);
			if($person->id == 1) {
				$array = array(
						'klassenstufe' => 8,
						'klasse' => 'a',
						'klassenlehrer_name' => 'Frau Bau',
				);
				$lehrer->add_lehrer($array) != false ?:$lehrer->load_lehrer(get_current_year(), $person->id); 
				$lehrer->get_id();
				$zeit = array('mi','15:00','16:00');
				$lehrer->add_time($zeit);
				var_dump(get_faecher_all());
				$lehrer->add_angebot_fach(1, false);
				var_dump($lehrer);
			}
		}
	} else {
		echo "Es wurde noch keine Person hinzugefügt";
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
