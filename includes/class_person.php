<?php
class person {
	public $id;
	public $email;
	public $vname;
	public $nname;
	public $telefon;
	public $geburtstag;
	
	function load_person_name($vname, $nname) {
		
	}
	
	function load_person($pid) {
		global $pdo;
		$ret_prep = $pdo->prepare("SELECT * FROM `person` WHERE id = :id");
		$return = $ret_prep->execute(array(
				'id' => $pid
		));
		$result = $ret_prep->fetch();
		$this->id = $pid;
		$this->vname = $result['vname'];
		$this->nname = $result['nname'];
		$this->email = $result['email'];
		$this->telefon = $result['telefon'];
		$this->geburtstag = $result['geburtstag'];
	}

	function addperson($vname, $nname, $email, $telefon, $geburtstag) {
		global $pdo;
		$vname = strip_tags($vname);
		$nname = strip_tags($nname);
		$email = strip_tags($email);
		$telefon = strip_tags($telefon);
		$error = "";
		if (!isset($vname) || strlen($vname) < 3 || strlen($vname) > 49) {
			$error = $error . "<br><br>Bitte gib einen Vornamen an, der zwischen 3 und 49 Zeichen lang ist.";
		}
		if (!isset($nname) || strlen($nname) < 3 || strlen($nname) > 49) {
			$error = $error . "<br><br>Bitte gib einen Nachnamen an, der zwischen 3 und 49 Zeichen lang ist.";
		}
		if (!isset($email) || (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^\+?([0-9\/ -]+)$/", $telefon))) {
			$error = $error . "<br><br>Bitte gib eine korrekte E-Mail-Adresse oder Telefonnummer an.";
		}
		$vname = htmlspecialchars($vname, ENT_QUOTES, 'UTF-8');
		$nname = htmlspecialchars($nname, ENT_QUOTES, 'UTF-8');
		$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
		$telefon = htmlspecialchars($telefon, ENT_QUOTES, 'UTF-8');
		if (strlen($geburtstag) != 0) {
			if (!strtotime($geburtstag)) {
				$error = $error . "<br><br>Bitte gib ein gültiges Geburtsdatum an.";
			}
		} else {
			$geburtstag = NULL;
		}
		if (strlen($error) > 0) {
			echo $error;
			return false;
		}
		//Teste, ob Person bereits vorhanden ist
		$ret_prep = $pdo->prepare("SELECT * FROM `person` WHERE vname = :vname AND nname = :nname");
		$return = $ret_prep->execute(array(
				'vname' => $vname,
				'nname' => $nname
		));
		if ($return === false)
			echo "EIn Fehler ist aufgetreten";
		$result = $ret_prep->fetch();
		if ($result !== FALSE) {
			echo "Diese Person existiert bereits!";
		} else {
			//Füge Person zu DB hinzu
			$ret_prep = $pdo->prepare("INSERT INTO `person` (`vname`,`nname`,`email`,`telefon`,`geburtstag`) VALUES( :vname, :nname, :email, :telefon, :geburtstag)");
			$return = $ret_prep->execute(array(
					'vname' => $vname,
					'nname' => $nname,
					'email' => $email,
					'telefon' => $telefon,
					'geburtstag' => $geburtstag
			));
			if ($return) {
				$this->vname = $vname;
				$this->nname = $nname;
				$this->email = $email;
				$this->telefon = $telefon;
				$this->geburtstag = $geburtstag;
				echo "Person wurde erfolgreich hinzugefügt";
			} else {
				echo "Ein Fehler ist  aufgetreten";
				var_dump($pdo->errorInfo());
			}
		}
	}
	
	function search_lehrer_schueler() {
		global $pdo;
		$ret_prep = $pdo->prepare("SELECT * FROM `lehrer` WHERE pid = :id AND schuljahr = :year");
		$return = $ret_prep->execute(array(
				'id' => '2',
				'year' => '1617'
		));
		$ret_prepp = query_db("SELECT * FROM `lehrer` WHERE pid = :id AND schuljahr = :year", $this->id, '1617');
		var_dump($ret_prepp);
		var_dump($ret_prepp->fetch());
	}
	
}