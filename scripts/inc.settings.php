<?php
if (isset($user) && $user->runscript()) {
	if(isset($_GET['setting'])) {
		$obj = get_xml($_GET['setting']);
		if ($obj !== false) {
			if($obj['type'] == 'bool') {
				set_xml($_GET['setting'], (($obj['value']) === 'true' ? 'false':'true'));
				write_xml();
			}
		}
				
	}else if(isset($_GET['multiple_settings'])) {
		$all_xml = getall_xml_keys();
		for($i=0;$i < count($all_xml); $i++) {
			if(isset($_POST[$all_xml[$i]['key']]) && $_POST[$all_xml[$i]['key']] != $all_xml[$i]['value']) {
				set_xml($all_xml[$i]['key'], $_POST[$all_xml[$i]['key']]);
				write_xml();
			}
		}
	}
	if (isset($_GET['log'])) {
		$file = fopen("error.log", "r");
		$content = fread($file, filesize("error.log"));
		$content = str_replace("\n", "<br>", $content);
		echo "<br><br><div id=\"top\"><a href=\"#bottom\" class=\"links\">Nach Unten</a></div><br>";
		echo $content;
		echo "<div id=\"bottom\"><a href=\"#topdiv\" class=\"links\">Nach Oben</a></div><br>";
		fclose($file);
		
	}else if (isset($_GET['modify_db'])) {
		$return = query_db("SELECT * FROM person");
		if ($return ) {
			$result = $return->fetchAll();
//			var_dump($result);
			$failures = 0;
			for ($i = 0; $i < count($result); $i++) {
				if(isset($result[$i])) {
					if (isset($result[($i-1)])) {
						$return = query_db("UPDATE person SET `vname` = :vname, email = 'test123@test.de', telefon = '0176123456', geburtstag = '2000-04-01' WHERE person.id = :id", $result[($i-1)]['vname'], $result[$i]['id']);
						if(!$return) {
							$failures++;
							var_dump($return);
						}
					}else if (isset($result[ (count($result)-1) ]) ) {
						$return = query_db("UPDATE person SET `vname` = :vname, email = 'test123@test.de', telefon = '0176123456', geburtstag = '2000-04-01' WHERE person.id = :id", $result[(count($result)-1)]['vname'], $result[$i]['id']);
						if(!$return) {
							$failures++;
							var_dump($return);
						}
					}
				}
			}
			echo "$failures Fehler sind aufgetreten";
		}
	}else {
		$all_xml = getall_xml_keys();
		echo "<h2>Einstellungen</h2>";
		echo "<div class=\"formular_class\"><form action=\"index.php?page=settings&multiple_settings=1\" method=\"POST\">";
		echo "<div style=\"margin-left:3%;\">";
//		var_dump($all_xml);
		$depth = 1;
		for($i=0;$i < count($all_xml); $i++) {
			if($all_xml[$i]['type'] == "category" && count(explode("/", $all_xml[$i]['key'])) < $depth) {
				echo "</div>";
				$depth--;
			}
			if($all_xml[$i]['type'] == "category") {	
				echo $all_xml[$i]['name'].":<div style=\"margin-left:5%;\">";
				$depth++;
			}
//			var_dump(explode("/", $all_xml[$i]['key']));
//			echo "depth:".$depth."COUNT:".count(explode("/", $all_xml[$i]['key']));
			if($all_xml[$i]['type'] != "category" && count(explode("/", $all_xml[$i]['key'])) < $depth) {
				for($j=$depth; $j > count(explode("/", $all_xml[$i]['key']));$j--) {
					echo "</div>";
					$depth--;
				}
			}
			if($all_xml[$i]['type'] == 'bool') {
				echo $all_xml[$i]['name'].":".($all_xml[$i]['value']== 'true' ? "wahr":"falsch").'<a href="index.php?page=settings&setting='.$all_xml[$i]['key'].'" class="links2" >Wert ändern</a>';
			}else if($all_xml[$i]['type'] != 'category'){
				echo $all_xml[$i]['name'].":"." <input name=\"".$all_xml[$i]['key']."\" class=\"input_text\" type=\"text\" value=\"".$all_xml[$i]['value']."\">";
			}
			echo "<br><br>";
		}
		for($j=$depth; $j > 1;$j--) {
			echo "</div>";
			$depth--;
		}
		echo "<input type=\"submit\" value=\"Ändern\" class=\"mybuttons\"></form></div>";
		echo "</div>";
		echo (get_xml("livesystem","value") !== null && get_xml("livesystem","value") != 'true' ? '<br><br><a href="index.php?page=settings&modify_db=1" class="links2">Datenbank in Beispieldatenbank migrieren. Dabei werden alle E-Mail-Adressen gelöscht, die Telefonnummern geändert und sämtliche Namen geändert.</a>':'');
		
		echo '<br><br><a href="index.php?page=settings&log=1" class="links2">Log-Datei ansehen</a>';
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
