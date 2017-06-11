<?php
class user {
	public $id;
	public $vname;
	public $nname;
	protected $email;
	protected $account;
	private $hash_password;
	private $count_login_trys;
	private $error;
	private $run_script;
	
	
	/*
	 * runscript: Variable, die auf true gesetzt wird, wenn Script erfolgreich geladen wurde und ausgeführt werden darf
	 */
	function runscript() {
		return $this->run_script;
	}
	function allowrunscript() {
		$this->run_script = true;
	}
	function denyrunscript() {
		$this->run_script = false;
	}
	public function __construct() {
		$this->id = NULL;
		$this->vname = "";
		$this->nname = "";
		$this->email = "";
		$this->account = "";
		$this->password = "";
		$this->count_login_trys = 0;
	}
	public function reset() {
		$this->id = NULL;
		$this->vname = "";
		$this->nname = "";
		$this->email = "";
		$this->account = "";
		$this->password = "";
		$this->count_login_trys = 0;
	}
	function getemail() {
		return $this->email;
	}
	/*
	 * Testet, ob dieser Nutzer Script/Teilbereich eines Scripts ausführen darf;
	 * In DB ist für jedes Script gespeichert, welcher Nutzertyp es ausführen darf
	 */
	function isuserallowed($rights) {
		switch ($this->account) {
			case 'v' :
				return true;
				break;
			case 'f' :
				if ($rights == 'w' || $rights == 'f' || $rights == 'fk' || $rights == 'kf')
					return true;
				else
					return false;
				break;
			case 'k' :
				if ($rights == 'w' || $rights == 'k' || $rights == 'fk' || $rights == 'kf')
					return true;
				else
					return false;
				break;
			case 'w' :
				if ($rights == 'w')
					return true;
				else
					return false;
			default :
				return false;
		}
	}
	//Überprüfen, ob Accounttyp existiert
	function testaccount($type) {
		switch ($type) {
			case 'v' :
				return true;
				break;
			case 'f' :
				return true;
				break;
			case 'k' :
				return true;
				break;
			case 'w' :
				return true;
				break;
			default :
				return false;
		}
		return false;
	}
	function adduser($vname, $nname, $email, $passwort, $passwort2, $type = 'k') {
		$error = '';
		if (strlen($vname) == 0 || strlen($vname) > 49) {
			$error .= 'Bitte einen gültigen Vornamen angeben';
		}
		if (strlen($nname) == 0) {
			$error .= 'Bitte einen gültigen Nachnamen angeben';
			$error = true;
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$error .= 'Bitte eine gültige E-Mail-Adresse eingeben<br>';
		}
		if (strlen($passwort) == 0 || strlen($passwort) < 4) {
			$error .= 'Bitte gib ein Passwort mit mindestens 4 Zeichen an.<br>';
		}
		if ($passwort != $passwort2) {
			$error .= 'Die Passwörter müssen übereinstimmen<br>';
		}
		if (!$this->testaccount($type)) {
			$error .= "Bitte gib einen korrekten Accounttyp an.<br>";
		}
		if (strlen($error) > 0) {
			$this->error = $error;
			return false;
		}
		$return = query_db("SELECT * FROM `users` WHERE email = :email", $email);
		if (!$return) {
			$this->error = "Datenbankfehler!";
			return false;
		}
		$return = $return->fetch();
		if ($return !== false) {
			$this->error = "Es existiert bereit ein Nutzer mit dieser E-Mail-Adresse";
			return false;
		}
		$return = query_db("INSERT INTO `users` (vname, nname, email, passwort, account) VALUES (:vname, :nname, :email, :passwort, :account)", $vname, $nname, $email, password_hash($passwort, PASSWORD_DEFAULT), $type);
		if ($return) {
			return 'Der neue Nutzer wurde erfolgreich registriert. <a href="index.php">Zum Login</a>';
		} else {
			$this->error = 'Beim Abspeichern ist leider ein Fehler aufgetreten<br>';
			return false;
		}
	}

	//Holt alle Informationen über Nutzer aus DB, wenn angegebene E-Mail existiert
	function setmail($m_mail) {
		global $pdo_obj;
		global $pdo;
		// var_dump($this->pdo_obj);
		$this->email = $m_mail;
		$statement = $pdo_obj->prepare("SELECT * FROM users WHERE email = :email");
		$result = $statement->execute(array(
				'email' => $this->email
		));
		$user = $statement->fetch();
		if ($user !== false) {
			$this->vname = $user['vname'];
			$this->nname = $user['nname'];
			$this->account = $user['account'];
			$this->hash_password = $user['passwort'];
			$this->id = $user['id'];
			$this->count_login_trys = intval($user['count_login']);
			require 'includes/global_vars.inc.php';
			$pdo = new PDO("mysql:host=localhost;dbname=schuefi", $GLOBAL_CONFIG['dbuser'], $GLOBAL_CONFIG['dbuser_passwd'], array(
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
			));
			return true;
		} else {
			$this->error = "Ein Nutzer mit dieser E-Mail-Adresse existiert leider nicht";
			return false;
		}
	}
	
	/*
	 * Anzahl der fehlerhaften Loginversuche mit bestimmter E-Mail werden in DB gespeichert. 
	 * Max. 5 Versuche sind möglich, dann muss Login mit best. E-Mail von Admin freigegeben werden.
	 *  
	 */
	private function increase_count_login() {
		if (!function_exists("query_db")) {
			require 'includes/functions.inc.php';
		}
		query_db("UPDATE `users` SET count_login = :count WHERE email = :email", $this->count_login_trys + 1, $this->email);
		$this->count_login_trys++;
	}
	private function reset_count_login() {
		if (!function_exists("query_db")) {
			require 'includes/functions.inc.php';
		}
		query_db("UPDATE `users` SET count_login = :count WHERE email = :email", 0, $this->email);
		$this->count_login_trys = 0;
	}
	function testpassword($password) {
		if (isset($this->hash_password) && isset($password)) {
			if ($this->count_login_trys < 5) {
				$return = password_verify($password, $this->hash_password);
				$return ?: $this->error = 'Das Passwort war leider falsch!';
				$return ? $this->reset_count_login() : $this->increase_count_login();
				return $return;
			} else {
				$this->error = "Sie haben mindestens fünfmal versucht, sich mit falschem Passwort anzumelden.<br><br> Bitte kontaktieren Sie den Admin." . $this->vname . $this->nname;
				if ($this->count_login_trys == 5)
					$this->send_mail();
				$this->increase_count_login();
				return false;
			}
		}
	}
	//noch nicht funktionstüchtig!!!
	function neuespassword($password_neu, $password_neu2) {
		if (strlen($password_neu) < 4) {
			$this->error = 'Das neue Passwort muss mindestens 4 Zeichen lang sein';
			return false;
		}
		if ($password_neu != $password_neu2) {
			$this->error = 'Die beiden Passwörter müssen übereinstimmen';
			return false;
		}
	}
	
	/*
	 * Benachrichtige bei 5 falschen Anmeldeversuchen automatisch Admin(s)
	 */
	private function send_mail() {
		require 'mail/class.phpmailer.php';
		require 'mail/class.smtp.php';
		global $pdo_obj;
		$mail = new PHPMailer();
		// $mail->isSMTP();
		$mail->Host = 'mail.gmx.net';
		$mail->SMTPAuth = true;
		$mail->Username = 'schuelerfirma.hgr@gmx.de';
		$mail->Password = 'schick2014';
		$mail->SMTPSecure = 'tls';
		$mail->Port = 587;
		$mail->isHTML(True);
		$mail->CharSet = 'utf-8';
		$mail->SetLanguage("de");
		
		$mail->setFrom('schuelerfirma.hgr@gmx.de', 'Schülerfirma HGR');
		$stat = $pdo_obj->query("SELECT `email` FROM `users` WHERE `account` = 'v'");
		$result = $stat->fetch();
		while ( $result ) {
			$mail->addAddress($result['email'], 'Anmeldefehler');
			$result = $stat->fetch();
		}
		// $mail->addAddress('schuelerfirma.hgr@gmx.de', 'Kundenberatung - Schülerfirma');
		// $mail->addReplyTo($email, $vorname.' '.$name);
		
		$Body = "Lieber Admin / Liebe Admins,<br> der Schüler/in " . $this->vname . " " . $this->nname . " mit der E-Mail-Addresse $this->email hat zu oft versucht, sich mit einem flaschen Passwort anzumelden.
		<br>Bitte überprüfe, ob es wirklich der Schüler war und nehme mit ihm Kontakt auf.
		<br><br>Vielen Dank
		<br>Hnweis: Dies ist automatisch gesendet!";
		$mail->Subject = 'Schuelerfirma HGR - Anmeldefehler von ' . $this->vname . ' ' . $this->nname;
		$mail->Body = $Body;
		if (!$mail->send()) {
			echo $mail->ErrorInfo;
		}
	}
	function geterror($reset = FALSE) {
		return $this->error;
		$reset ?: $this->error;
	}
	function is_valid() {
		if (strlen($this->vname) > 0 && strlen($this->nname) > 0 && strlen($this->email) > 0 && strlen($this->account) > 0 && $this->id !== 0) {
			return true;
		} else {
			return false;
		}
	}
	function logout() {
		global $pdo;
		$this->id = NULL;
		$this->vname = "";
		$this->nname = "";
		$this->email = "";
		$this->account = "";
		$this->password = "";
		$this->count_login_trys = 0;
		$pdo = NULL;
	}
}