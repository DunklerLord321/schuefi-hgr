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
	private $logfile;
	const LEVEL_ERROR = 1;
	const LEVEL_WARNING = 2;
	const LEVEL_NOTICE = 3;
	
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
			case 'v':
				return true;
				break;
			case 'f':
				if ($rights == 'w' || $rights == 'f' || $rights == 'fk' || $rights == 'kf')
					return true;
				else
					return false;
				break;
			case 'k':
				if ($rights == 'w' || $rights == 'k' || $rights == 'fk' || $rights == 'kf')
					return true;
				else
					return false;
				break;
			case 'w':
				if ($rights == 'w')
					return true;
				else
					return false;
			default:
				return false;
		}
	}
	// Überprüfen, ob Accounttyp existiert
	function testaccount($type) {
		switch ($type) {
			case 'v':
				return true;
				break;
			case 'f':
				return true;
				break;
			case 'k':
				return true;
				break;
			case 'w':
				return true;
				break;
			default:
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
			$this->log(user::LEVEL_WARNING, "DB-Fehler beim anlegen von neuem User");
			return false;
		}
		$return = $return->fetch();
		if ($return !== false) {
			$this->error = "Es existiert bereit ein Nutzer mit dieser E-Mail-Adresse";
			return false;
		}
		$return = query_db("INSERT INTO `users` (vname, nname, email, passwort, account) VALUES (:vname, :nname, :email, :passwort, :account)", $vname, $nname, $email, password_hash($passwort, PASSWORD_DEFAULT), $type);
		if ($return) {
			return 'Der neue Nutzer wurde erfolgreich registriert. <a href="index.php" class="links2">Zum Login</a>';
		}else {
			$this->error = 'Beim Abspeichern ist leider ein Fehler aufgetreten<br>';
			return false;
		}
	}
	
	// Holt alle Informationen über Nutzer aus DB, wenn angegebene E-Mail existiert
	function setmail($m_mail) {
		require 'includes/functions.inc.php';
		$this->email = $m_mail;
		$result = query_db("SELECT * FROM users WHERE email = :email", $this->email);
		$user = $result->fetch();
		if ($user !== false) {
			$this->vname = $user['vname'];
			$this->nname = $user['nname'];
			$this->account = $user['account'];
			$this->hash_password = $user['passwort'];
			$this->id = $user['id'];
			$this->count_login_trys = intval($user['count_login']);
			return true;
		}else {
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
	function log(int $error_level, string $logstring) {
		$string = date("D,d.m.Y-H:i:s", time());
		$string .= ": " . $this->email;
		if ($error_level == $this::LEVEL_ERROR) {
			$string .= " ERROR ";
		}elseif ($error_level == $this::LEVEL_WARNING) {
			$string .= " WARNING ";
		}elseif ($error_level == $this::LEVEL_NOTICE) {
			$string .= " NOTICE ";
		}
		$debug = debug_backtrace();
		$string .= $logstring . "   <<<<<<";
		for ($i = (count($debug) - 1); $i >= 0; $i--) {
			$string .= "{" . $debug[$i]['file'] . ":" . $debug[$i]['line'] . "-" . $debug[$i]['function'];
			if (count($debug[$i]['args']) > 0) {
				if ($debug[$i]['function'] == "testpassword") {
					$string .= "[*****]";
				}else {
					$string .= "[" . implode("_", $debug[$i]['args']) . "]";
				}
			}
			$string .= "}";
		}
		$string .= ">>>>>>>\n\n";
		$this->logfile = fopen("error.log", "a");
		fwrite($this->logfile, $string);
		fclose($this->logfile);
	}
	function testpassword($password) {
		if (isset($this->hash_password) && isset($password)) {
			if ($this->count_login_trys < 5) {
				$return = password_verify($password, $this->hash_password);
				$return ?: $this->error = 'Das Passwort war leider falsch!';
				$return ? $this->reset_count_login() : $this->increase_count_login();
				$return ? $this->log(user::LEVEL_NOTICE, "Login erfolgreich") : $this->log(user::LEVEL_NOTICE, "Anmeldung fehlgeschlagen");
				return $return;
			}else {
				$this->error = "Sie haben mindestens fünfmal versucht, sich mit falschem Passwort anzumelden.<br><br> Bitte kontaktieren Sie den Admin." . $this->vname . $this->nname;
				if ($this->count_login_trys == 5)
					$this->send_mail();
				$this->increase_count_login();
				$this->log(user::LEVEL_NOTICE, "Anmeldung erfolgreich");
				return false;
			}
		}
	}
	function neuespassword($passwortaktuell, $password_neu, $password_neu2) {
		if (strlen($password_neu) < 4) {
			$this->error = 'Das neue Passwort muss mindestens 4 Zeichen lang sein';
			return false;
		}
		if ($password_neu != $password_neu2) {
			$this->error = 'Die beiden Passwörter müssen übereinstimmen';
			return false;
		}
		if ($password_neu == $passwortaktuell) {
			$this->error = 'Das neue Passwort darf nicht mit dem alten Passwort übereinstimmen';
			return false;
		}
		if (!$this->testpassword($passwortaktuell)) {
			$this->error = 'Das alte Passwort war leider falsch';
			return false;
		}
		$passwort_hash = password_hash($password_neu, PASSWORD_DEFAULT);
		$return = query_db("UPDATE `users` SET passwort = :passwort_hash, `update_time` = CURRENT_TIME() WHERE id = :id", $passwort_hash, $this->id);
		if ($return) {
			$this->log(user::LEVEL_NOTICE, "Passwort erfolgreich geändert");
			return true;
		}else {
			$this->error = 'Ein Datenbankfehler ist aufgetreten';
			$this->log(user::LEVEL_WARNING, "DB-Fehler bei Passwortänderung");
			return false;
		}
	}
	
	/*
	 * Benachrichtige bei 5 falschen Anmeldeversuchen automatisch Admin(s)
	 */
	private function send_mail() {
		require 'mail/class.phpmailer.php';
		require 'mail/class.smtp.php';
		$mail = new PHPMailer();
		// $mail->isSMTP();
		$mail->Host = 'mail.gmx.net';
		$mail->SMTPAuth = true;
		$mail->Username = $GLOBAL_CONFIG['mail_address'];
		$mail->Password = $GLOBAL_CONFIG['mail_passwd'];
		$mail->SMTPSecure = 'tls';
		$mail->Port = 587;
		$mail->isHTML(True);
		$mail->CharSet = 'utf-8';
		$mail->SetLanguage("de");
		
		$mail->setFrom('schuelerfirma.sender.hgr@gmx.de', 'Schülerfirma HGR');
		$stat = query_db("SELECT `email` FROM `users` WHERE `account` = 'v'");
		$result = $stat->fetch();
		while ($result) {
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
		}else {
			return false;
		}
	}
	function logout() {
		global $user;
		unset($_SESSION['user']);
		$this->id = NULL;
		$this->vname = "";
		$this->nname = "";
		$this->account = "";
		$this->hash_password = "";
		$this->password = "";
		$this->count_login_trys = 0;
		// email ist notwendig, um zu sehen, wer sich abmeldet
		if (isset($this->email) && strlen($this->email) > 0) {
			$this->log(user::LEVEL_NOTICE, "Logout erfolgreich");
		}
		$this->email = "";
		$_SESSION['user'] = serialize($user);
	}
}