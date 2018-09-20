<?php
class user {
	public $id;
	public $vname;
	public $nname;
	protected $email;
	protected $account;
	private $hash_password;
	private $count_login_trys;
	public $security_token;
	public $security_token_time;
	private $error;
	private $run_script;
	private $logfile;
	public $time_of_creation; 
  public $time_of_last_update; 
  private $activated; 
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
		$this->reset();
	}
	public function reset() {
		$this->id = NULL;
		$this->vname = "";
		$this->nname = "";
		$this->email = "";
		$this->account = "";
		$this->password = "";
		$this->count_login_trys = 0;
		$this->time_of_creation = ""; 
    $this->time_of_last_update = ""; 
    $this->activated = ""; 
	}
	function getemail() {
		return $this->email;
	}
	
	function getaccount() {
		return $this->account;
	}
	function get_login_tries() {
		return $this->count_login_trys;
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
				if ($rights == 'g' || $rights == 'f' || $rights == 'fk' || $rights == 'kf' || $rights == 'a')
					return true;
				else
					return false;
				break;
			case 'k':
				if ($rights == 'g' || $rights == 'k' || $rights == 'fk' || $rights == 'kf' || $rights == 'a')
					return true;
				else
					return false;
				break;
			case 'g':
				if ($rights == 'g' || $rights == 'a')
					return true;
				else
					return false;
			case 'c':
				if ($rights == 'c' || $rights == 'a')
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
			case 'g':
				return true;
				break;
			case 'c':
				return true;
				break;
			default:
				return false;
		}
		return false;
	}
  function is_admin() {
    $admins = get_xml("admin", "value");
    if ( strstr($admins, $this->getemail() ) == false ) {
      return false;
    } else {
      return true;
    }
  }
	function add_reference_to_person($person_id) {
		$return = query_db("SELECT * FROM `person` WHERE id = :person_id", $person_id);
		$return = $return->fetch();
		if ($return === false) {
			echo "Es existiert kein Nutzer mit dieser ID";
			return false;
		}
		$return = query_db("INSERT INTO `users` (person_id, account) VALUES (:person_id, :account)", $person_id, 'c');
		if ($return) {
			return true;
		}else {
			echo 'Beim Abspeichern ist leider ein Fehler aufgetreten<br>';
			return false;
		}
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
	/* 
 *Wichtig: Customer können nur über user-id geladen werden, wenn die Abfrage zur Anzeige aller Nutzer SELECT * FROM users; lautet, da dort für Customer keine Mail vorhanden ist 
 * 
 */ 
	function load_user($m_mail, $uid = -1) {
		if (!function_exists("query_db")) {
			require 'includes/functions.inc.php';
		}
		if ($uid != -1) {
			$result = query_db("SELECT * FROM users WHERE id = :id", $uid);			
		}else{
			$result = query_db("SELECT * FROM users WHERE email = :email", $m_mail);
		}
		$user = $result->fetch();
		if ($user === false || strlen($user['email']) == 0) {
			if ($uid != -1) {
				$result = query_db("SELECT users.*, person.vname, person.nname, person.email FROM person LEFT JOIN users on users.person_id = person.id WHERE users.id = :uid", $uid);
			}else{
				$result = query_db("SELECT users.*, person.vname, person.nname, person.email FROM person LEFT JOIN users on users.person_id = person.id WHERE person.email = :email", $m_mail);
			}
			$user = $result->fetch();
			if ($user === false || $user['id'] == NULL) {
				if ($user['id'] == NULL) {
					//TODO log ohne Login
//					$user->log(LEVEL_ERROR, "Ein Nutzer ohne korrekten Login versucht, sich anzumelden".$m_mail);
				}
				$this->error = 'Das Passwort oder die E-Mail-Adresse war leider falsch';
				return false;
			}
		}
			$this->email = $user['email'];
			$this->vname = $user['vname'];
			$this->nname = $user['nname'];
			$this->account = $user['account'];
			$this->hash_password = $user['passwort'];
			$this->id = $user['id'];
			$this->count_login_trys = intval($user['count_login']);
			$this->security_token = $user['security_token'];
			$this->security_token_time = $user['security_token_time'];
			$this->time_of_creation = $user['createt_time']; 
			$this->time_of_last_update = $user['update_time']; 
			$this->activated = intval($user['aktiv']); 
			return true;
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
				if ($debug[$i]['function'] == "testpassword" || $debug[$i]['function'] == "reset_password" || $debug[$i]['function'] == "neuespassword") {
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
		if (!$this->is_activated()) {
			$this->log(user::LEVEL_WARNING, "Anmeldung von gelöschter Person oder Nutzer");
			$this->error = 'Das Passwort oder die E-Mail-Adresse war leider falsch!';
			return false;
		}
		if (isset($this->hash_password) && isset($password)) {
			if ($this->count_login_trys < 5) {
				$return = password_verify($password, $this->hash_password);
				$return ?: $this->error = 'Das Passwort oder die E-Mail-Adresse war leider falsch!';
				$return ? $this->reset_count_login() : $this->increase_count_login();
				$return ? $this->log(user::LEVEL_NOTICE, "Login erfolgreich") : $this->log(user::LEVEL_NOTICE, "Anmeldung fehlgeschlagen");
				return $return;
			}else {
				$this->error = "Sie haben mindestens fünfmal versucht, sich mit falschem Passwort oder E-Mail-Adresse anzumelden.<br><br> Bitte kontaktieren Sie den Admin." . $this->vname . $this->nname;
				if ($this->count_login_trys == 5)
					$this->send_mail();
				$this->increase_count_login();
				$this->log(user::LEVEL_NOTICE, "Anmeldung erfolgreich");
				return false;
			}
		}
	}
	function neuespassword($passwortaktuell, $password_neu, $password_neu2) {
		if ($password_neu == $passwortaktuell) {
			$this->error = 'Das neue Passwort darf nicht mit dem alten Passwort übereinstimmen';
			return false;
		}
		if (!$this->testpassword($passwortaktuell)) {
			$this->error = 'Das alte Passwort war leider falsch';
			return false;
		}
		if (!$this->reset_password($password_neu, $password_neu2)) {
			return false;
		}
		return true;
	}
	
	function reset_password($password_neu, $password_neu2) {
		if (!$this->is_activated()) {
			$this->error = 'Der Nutzer darf nicht inaktiv sein';
			return false;
		}
		if (strlen($password_neu) < 4) {
			$this->error = 'Das neue Passwort muss mindestens 4 Zeichen lang sein';
			return false;
		}
		if ($password_neu != $password_neu2) {
			$this->error = 'Die beiden Passwörter müssen übereinstimmen';
			return false;
		}
		$passwort_hash = password_hash($password_neu, PASSWORD_DEFAULT);
		$return = query_db("UPDATE `users` SET passwort = :passwort_hash, `update_time` = CURRENT_TIME() WHERE id = :id", $passwort_hash, $this->id);
		if ($return) {
			$this->log(user::LEVEL_NOTICE, "Passwort erfolgreich geändert");
			$this->error = 'Passwort erfolgreich gespeichert';
			return true;
		}else {
			$this->error = 'Ein Datenbankfehler ist aufgetreten';
			$this->log(user::LEVEL_WARNING, "DB-Fehler bei Passwortänderung");
			return false;
		}		
	}
	function activate() {
		$result = query_db("UPDATE `users` SET aktiv = 1 WHERE id = :id", $this->id);
		if ($result !== false) {
			return true;
		}else {
			return false;
		}
	}
	function inactivate() {
		$result = query_db("UPDATE `users` SET aktiv = 0 WHERE id = :id", $this->id);
		if ($result !== false) {
			return true;
		}else {
			return false;
		}
	}
	function validate_security_token($token) {
		if (!$this->has_security_code() || !$this->is_activated()) {
			$this->error = 'Es wurde keine Registrierung mit dieser E-Mail-Addresse erlaubt';
			return false;
		}
		if ($this->security_token_time === null || strtotime($this->security_token_time) < (time()-3*24*3600)) {
			$this->error = 'Der Link zur Registrierung ist veraltet';
			return false;
		}
		if (sha1($token) != $this->security_token) {
			$this->error = "Der übergebene Code zur Registrierung war ungültig. Stelle sicher, dass du genau den Link kopiert hast";
			return false;
		}
		return true;
	}
	
	function delete_security_token() {
		$result = query_db("UPDATE users SET security_token = NULL, security_token_time = NULL WHERE id = :userid", $this->id);
		if ($result === false) {
			return false;
		}else{
			return true;
		}		
	}
	
	function create_security_token() {
		if (!$this->is_activated()) {
			echo 'Der Nutzer darf nicht inaktiv sein';
			return false;
		}
		global $GLOBAL_CONFIG;
		$passwortcode = random_string();
		$result = query_db("UPDATE users SET security_token = :security_token, security_token_time = NOW() WHERE id = :userid", sha1($passwortcode), $this->id);
		echo "Registrierungslink erfolgreich erstellt.<br>";
		require 'extensions/mail/PHPMailer-master/PHPMailerAutoload.php';
		$mail = new PHPMailer();
		$mail->Host = 'mail.gmx.net';
		$mail->SMTPAuth = true;
		// TODO an xml anpassen
		$mail->Username = $GLOBAL_CONFIG['mail_address'];
		$mail->Password = $GLOBAL_CONFIG['mail_passwd'];
		$mail->SMTPSecure = 'tls';
		$mail->Port = 587;

		$mail->isHTML(True);
		$mail->CharSet = "UTF-8";
		$mail->SetLanguage("de");
		
		// $mail->addAddress($this->email);
		if (get_xml("livesystem","value") !== null && get_xml("livesystem","value") != 'true' && get_xml("testmail","value") !== null) {
			$mail->addAddress(get_xml("testmail", "value"), get_xml("testmail", "value"));
		}else{
			$mail->addAddress($this->email);
		}
		$mail->addBCC("schuelerfirma@hgr-web.de");
		$mail->setFrom("schuelerfirma.sender.hgr@gmx.de", "Schülerfirma HGR");
		$mail->Subject = "Schülerfirma HGR - Registrierung von " . $this->vname . " " . $this->nname;
		
		$url_passwortcode = 'https://'.get_xml("servername2", "value").'/index.php?reset_password=1&userid='.$this->id.'&security_token='.$passwortcode;
		$text = 'Hallo '.$this->vname." ".$this->nname.',<br>
		vielen Dank für dein Interessen an der Schülerfirma "Schüler helfen Schülern"!<br>
		Seit diesem Jahr benötigst du einen Login bei der Schülerfirma. Um dich zu registrieren, klicke bitte auf folgenden Link.<br>	
		<a href='.$url_passwortcode.' style="font-style: italic;	text-decoration: underline;	color: black;">Registrierungslink</a>
		<br><i>Bitte beachte, dass der Link nur bis zum '.date("d.m.Y H:i").' gültig ist!</i><br>
		Weiter Informationen zu deinem Nachhilfeunterricht erhälst du in einer separaten E-Mail.<br>
		
		Viele Grüße,<br>
		deine Schülerfirma<br>';
		$alternate_text = "Hallo ".$this->vname." ".$this->nname.",\n
		vielen Dank für dein Interessen an der Schülerfirma \"Schüler helfen Schülern\"!\n
		Seit diesem Jahr benötigst du einen Login bei der Schülerfirma. Um dich zu registrieren, klicke bitte auf folgenden Link.\n\n
		Registrierungslink:\r".$url_passwortcode."\n\n
		Bitte beachte, dass der Link nur bis ".date("d.m.Y H:i")." gültig ist!\n
		Weiter Informationen zu deinem Nachhilfeunterricht erhälst du in einer separaten E-Mail.\n
		Viele Grüße,\n
		deine Schülerfirma\n";

		$mail->Body = $text;
		$mail->AltBody = $alternate_text;
		echo $url_passwortcode."<br>Registrierungslink versenden...";
		if (!$mail->send()) {
			echo $mail->ErrorInfo;
		}

	}

	
	/*
	 * Benachrichtige bei 5 falschen Anmeldeversuchen automatisch Admin(s)
	 */
	private function send_mail() {
		require 'extensions/mail/class.phpmailer.php';
		require 'extensions/mail/class.smtp.php';
		$mail = new PHPMailer();
		// $mail->isSMTP();
		$mail->Host = 'mail.gmx.net';
		$mail->SMTPAuth = true;
		// TODO an xml anpassen
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
		<br>Wenn es wirklich der Schüler war, kann er nur von einem Administrator/Vorstand wieder entsperrt werden. 
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
	function has_security_code() {
		if (strlen($this->security_token) > 0 && strlen($this->security_token_time) > 0) {
			return true;
		}else{
			return false;
		}
	}
	function has_valid_login() {
		if (strlen($this->hash_password) > 0 && !$this->has_valid_security_code()) {
			return true;
		}else{
			return false;
		}
	}
	function has_valid_security_code() { 
	 if (strlen($this->security_token) > 0 && strlen($this->security_token_time) > 0 && $this->security_token_time != null && strtotime($this->security_token_time) > (time()-3*24*3600)) { 
		 return true; 
	 }else{ 
		 return false; 
	 }
 	} 
	function is_valid() {
		if (strlen($this->vname) > 0 && strlen($this->nname) > 0 && strlen($this->email) > 0 && strlen($this->account) > 0 && $this->id !== 0) {
			return true;
		}else {
			return false;
		}
	}
	function is_activated() { 
	 if ($this->activated == 1) { 
		 return true; 
	 }else{ 
		 return false; 
	 } 
 	}
	function exists($m_mail) {
		$return = query_db("SELECT * FROM users WHERE email = :email", $m_mail);
		$result = $return->fetch();
		if ($result !== false) {
			return true;
		}else{
			return false;
		}
	}
	function has_reference_to_person($person_id) {
		$return = query_db("SELECT * FROM users WHERE person_id = :person_id", $person_id);
		$result = $return->fetch();
		if ($result !== false) {
			return true;
		}else{
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