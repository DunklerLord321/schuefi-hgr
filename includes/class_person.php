<?php
class person {
	public $id;
	public $email;
	public $vname;
	public $nname;
	public $telefon;
	public $geburtstag;
	public $aktiv;
	public $user;
	
	function __construct() {
		$this->id = null;
		$this->vname = '';
		$this->nname = '';
		$this->email = '';
		$this->telefon = '';
		$this->geburtstag = '';
		$this->aktiv = true;
	}
	function load_person_name($vname, $nname) {
		global $pdo;
		$return = query_db("SELECT * FROM `person` WHERE vname = :vname AND nname = :nname", $vname, $nname);
		$result = $return->fetch();
		if($result === false) {
			return false;
		}
		$this->id = $result['id'];
		$this->vname = $result['vname'];
		$this->nname = $result['nname'];
		$this->email = $result['email'];
		$this->telefon = $result['telefon'];
		$this->aktiv = $result['aktiv'];
		if (strlen($result['geburtstag']) > 0) {
			$time = strtotime($result['geburtstag']);
			$this->geburtstag = date('d.m.Y', $time);
		}
		return true;
	}
	function load_person($pid) {
		global $pdo;
		$ret_prep = query_db("SELECT person.*, users.id as userid FROM `person` LEFT JOIN users ON users.person_id = person.id WHERE person.id = :id", $pid);
		$result = $ret_prep->fetch();
		if ($result === false) {
			return false;
		}
		$this->id = $pid;
		$this->vname = $result['vname'];
		$this->nname = $result['nname'];
		$this->email = $result['email'];
		$this->telefon = $result['telefon'];
		$this->aktiv = $result['aktiv'];
		$this->user = new user();
		$this->user->load_user($this->email);
		if (strlen($result['geburtstag']) > 0) {
			$time = strtotime($result['geburtstag']);
			$this->geburtstag = date('d.m.Y', $time);
		}
		return true;
	}	
	
	function addperson($vname, $nname, $email, $telefon, $geburtstag) {
		global $pdo;
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
		if (strlen($geburtstag) != 0) {
			if (!strtotime($geburtstag)) {
				$error = $error . "<br><br>Bitte gib ein gültiges Geburtsdatum an.";
			}else {
				$time = strtotime($geburtstag);
				$geburtstag = date('Y-m-d', $time);
			}
		}else {
			$geburtstag = NULL;
		}
		if (strlen($error) > 0) {
			echo $error;
			return false;
		}
		// Teste, ob Person bereits vorhanden ist
		$return = query_db("SELECT * FROM `person` WHERE vname = :vname AND nname = :nname", $vname, $nname);
		if ($return === false)
			echo "EIn Fehler ist aufgetreten";
		$result = $return->fetch();
		if ($result !== FALSE) {
			echo "Diese Person existiert bereits!";
		}else {
			// Füge Person zu DB hinzu
			$return = query_db("INSERT INTO `person` (`vname`,`nname`,`email`,`telefon`,`geburtstag`) VALUES( :vname, :nname, :email, :telefon, :geburtstag)", $vname, $nname, $email, $telefon, $geburtstag); 
			if ($return) {
				$this->vname = $vname;
				$this->nname = $nname;
				$this->email = $email;
				$this->telefon = $telefon;
				$this->geburtstag = $geburtstag;
				$return = query_db("SELECT * FROM `person` WHERE vname = :vname AND nname = :nname", $this->vname, $this->nname);
				$result = $return->fetch();
				$this->id = $result['id'];
				return true;
			}else {
				echo "Ein Fehler ist  aufgetreten";
				// var_dump($pdo->errorInfo());
				return false;
			}
		}
	}
	function search_lehrer_schueler($year = -1) {
		if ($year == -1) {
			$year = get_current_year();
		}
		global $pdo;
		$ret_prepp = query_db("SELECT * FROM `lehrer` WHERE pid = :id AND schuljahr = :year", $this->id, $year);
		if ($ret_prepp) {
			$lehrer = $ret_prepp->fetch();
		}
		$ret_prepp = query_db("SELECT * FROM `schueler` WHERE pid = :id AND schuljahr = :year", $this->id, $year);
		if ($ret_prepp) {
			$schueler = $ret_prepp->fetch();
		}
		//Nur ein Eintrag je schueler- und lehrer-Tabelle auf eine Person und ein Jahr gesehen möglich
		if (isset($lehrer) && isset($schueler)) {
			return array(
					'lehrer' => $lehrer, 
					'schueler' => $schueler
			);
		}else {
			return array(
					'lehrer' => NULL, 
					'schueler' => NULL
			);
		}
	}
	function change_person($vname, $nname, $email, $telefon, $geburtstag, $is_allowed_to_login) {
		global $pdo;
		if (!isset($this->id) || $this->id == NULL || $this->aktiv == false) {
			return false;
		}
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
		if (strlen($geburtstag) != 0) {
			if (!strtotime($geburtstag)) {
				$error = $error . "<br><br>Bitte gib ein gültiges Geburtsdatum an.";
			}else {
				$time = strtotime($geburtstag);
				$geburtstag = date('Y-m-d', $time);
			}
		}else {
			$geburtstag = NULL;
		}
		if (strlen($error) > 0) {
			echo $error;
			return false;
		}
		// Teste, ob Person bereits vorhanden ist
		$ret_prep = query_db("SELECT * FROM `person` WHERE id = :pid", $this->id);
		if ($ret_prep === false) {
			echo "EIn Fehler ist aufgetreten";
		}
		$result = $ret_prep->fetch();
		if ($result === false) {
			echo "Diese Person existiert nicht!";
			return false;
		}else {
			// Ändere Person
			$ret_prep = query_db("UPDATE `person` SET `vname` = :vname, `nname` = :nname, `email` = :email, `telefon` = :telefon, `geburtstag` = :geburtstag WHERE `id` = :id", $vname, $nname, $email, $telefon, $geburtstag, $this->id);
			if ($is_allowed_to_login == true) {
				if (!$this->user->has_reference_to_person($this->id)) {
					$this->user->add_reference_to_person($this->id);
				}
			}else{
				$this->user->inactivate();
			}
			if ($ret_prep) {
				$this->vname = $vname;
				$this->nname = $nname;
				$this->email = $email;
				$this->telefon = $telefon;
				$this->geburtstag = $geburtstag;
				return true;
			}else {
				echo "Ein Fehler ist  aufgetreten";
				// var_dump($pdo->errorInfo());
				return false;
			}
		}
	}
	
	function activate() {
		if ($this->aktiv == true) {
			return true;
		}
		$return = query_db("UPDATE `person` SET `aktiv` = 1 WHERE id = :id", $this->id);
		if (!$return) {
			return "Es trat beim Aktivieren ein Fehler auf";
		}else{
			return true;
			$this->aktiv = true;
		}
	}
	function deactivate() {
		if ($this->aktiv == false) {
			return true;
		}
		$return = query_db("UPDATE `person` SET `aktiv` = 0 WHERE id = :id", $this->id);
		if (!$return) {
			return "Es trat beim Deaktivieren ein Fehler auf";
		}else{
			return true;
			$this->aktiv = true;
		}
	}
	
	function delete() {
		if($this->aktiv == false) {
			echo "Die Person wurde bereits gelöscht. Die Daten bleiben weiterhin erhalten, um Probleme mit der Finanztabelle zu vermeiden";
		}
		$schueler_lehrer = $this->search_lehrer_schueler();
		if (is_array($schueler_lehrer['lehrer']) || is_array($schueler_lehrer['schueler'])) {
			echo "Die Person konnte nicht gelöscht werden.<br><br>Zu der Person gibt es noch einen Schüler oder Lehrer. Lösche bitte zuerst diese.";
			return false;
		}
		$return = query_db("UPDATE `person` SET `aktiv` = 0 WHERE id = :id", $this->id);
		if (!$return) {
			echo "Es trat beim Löschen ein Fehler auf";
		}else{
			echo "Die Person wurde erfolgreich gelöscht";
			$this->aktiv = false;
		}
		
	}
}