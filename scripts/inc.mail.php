<?php
if (isset($user) && $user->runscript()) {
	
	require 'extensions/mail/PHPMailer-master/PHPMailerAutoload.php';
	echo '<nav><ul class="mail_steps"><li><a href="index.php?page=mail&step=1" ';
	if (!isset($_GET['step']) || $_GET['step'] == 1) {
		echo "class=\"mail_steps_active\"";
	}
	echo '>Serien-E-Mail/Vermittlungs-E-Mail</a></li><li><a href="index.php?page=mail&step=2" ';
	if (isset($_GET['step']) && $_GET['step'] == 2) {
		echo "class=\"mail_steps_active\"";
	}
	echo '>Adressen festlegen</a></li><li><a href="index.php?page=mail&step=3" ';
	if (isset($_GET['step']) && $_GET['step'] == 3) {
		echo "class=\"mail_steps_active\"";
	}
	echo '>E-Mail verfassen</a></li><li><a href="index.php?page=mail&step=4" ';
	if (isset($_GET['step']) && $_GET['step'] == 4) {
		echo "class=\"mail_steps_active\"";
	}
	echo '>Vorschau</a></li></ul></nav>';
	echo '<h2>Sende E-Mail an Schüler</h2><a class="mybuttons links2" href="index.php?page=mail&mail_reset=1">Alle Eingaben zurücksetzen</a>';

	if (isset($_GET['send'])) {
		$mail = new PHPMailer();
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
		$mail->addReplyTo("schuelerfirma@hgr-web.de", "Schülerfirma - Kundenbetreuung");
		// $mail->addAddress ( 'schuelerfirma.hgr@gmx.de', 'Kundenberatung - Schülerfirma' );
		// $mail->addReplyTo($email, $vorname.' '.$name);
		if (isset($_SESSION['mail_step3']) && isset($_SESSION['mail_step1']) && isset($_SESSION['mail_step2'])) {
			if ($_SESSION['mail_step1']['mailart'] == 1 && isset($_SESSION['schuelermail']) && isset($_SESSION['lehrermail'])) {
				if (get_xml("livesystem","value") !== null && get_xml("livesystem","value") != 'true' && get_xml("testmail","value") !== null) {
					$mail->addAddress(get_xml("testmail", "value"), get_xml("testmail", "value"));
				}else{
					$mail->addAddress($_SESSION['lehrermail']['empfaenger'], $_SESSION['lehrermail']['empfaenger']);
					$mail->addBCC("schuelerfirma@hgr-web.de", "Schülerfirma");
				}
				$mail->addAttachment(get_xml("attachement/agb/path","value"), get_xml("attachement/agb/displayed-name","value"));
				$mail->addAttachment(get_xml("attachement/lehrernachweis/path","value"), get_xml("attachement/lehrernachweis/displayed-name","value"));
				$mail->addAttachment(get_xml("dirs/doc","value") . $_SESSION['lehrermail']['anhang'], "Vermittlungsdokument.pdf");
				//				echo get_xml("dirs/doc","value") . $_SESSION['lehrermail']['anhang'];
				$mail->Body = $_SESSION['lehrermail']['text'];
				$mail->Subject = $_SESSION['lehrermail']['betreff'];
				if (!$mail->send()) {
					echo "Es ist ein Fehler beim Versenden der Mail aufgetreten.";
					echo $mail->ErrorInfo;
				}else {
					echo "<br><br>Mail an Lehrer erfolgreich gesendet<br>";
				}
				$mail->clearAttachments();
				$mail->clearAllRecipients();
				$mail->setFrom('schuelerfirma.sender.hgr@gmx.de', 'Schülerfirma HGR');
				if (get_xml("livesystem","value") !== null && get_xml("livesystem","value") != 'true' && get_xml("testmail","value") !== null) {
					$mail->addAddress(get_xml("testmail", "value"), get_xml("testmail", "value"));
				}else{
					$mail->addBCC("schuelerfirma@hgr-web.de", "Schülerfirma");
					$mail->addAddress($_SESSION['schuelermail']['empfaenger'], $_SESSION['schuelermail']['empfaenger']);
				}
				$mail->addAttachment("docs/AGB.pdf", "Allgemeine Geschäftsbedingungen.docx");
				$mail->addAttachment("docs/unterricht/" . $_SESSION['schuelermail']['anhang'], "Vermittlungsdokument.pdf");
				$mail->addAttachment("docs/kontoinfo.pdf", "Informationen über das Konto der Schülerfirma.pdf");
				$mail->Body = $_SESSION['schuelermail']['text'];
				$mail->Subject = $_SESSION['schuelermail']['betreff'];
				if (!$mail->send()) {
					echo "Es ist ein Fehler beim Versenden der Mail aufgetreten.";
					echo $mail->ErrorInfo;
				}else {
					echo "<br><br>Mail an Schüler erfolgreich gesendet<br>";
				}
				echo "<br><b>Hinweis: Es kann vorkommen, dass trotz der Meldung \"Mail erfolgreich gesendet\" die E-Mail-Adresse falsch war.<br>Deswegen musst du unbedingt nochmal in unseren Mail-Account schauen, ob dort eine Fehlermeldung vorliegt</b>";
			}
			if ($_SESSION['mail_step1']['mailart'] == 2 && isset($_SESSION['serienmail']) && is_array($_SESSION['serienmail'])) {
				$successsend = 0;
				for ($i = 0; $i < count($_SESSION['serienmail']); $i++) {
					$mail->clearAllRecipients();
					$mail->setFrom('schuelerfirma.sender.hgr@gmx.de', 'Schülerfirma HGR');
					$mail->addReplyTo("schuelerfirma@hgr-web.de", "Schülerfirma - Kundenbetreuung");
					if (get_xml("livesystem","value") !== null && get_xml("livesystem","value") != 'true' && get_xml("testmail","value") !== null) {
						$mail->addAddress(get_xml("testmail", "value"), get_xml("testmail", "value"));
					}else{	
						$mail->addAddress($_SESSION['serienmail'][$i]['mail'], $_SESSION['serienmail'][$i]['vname'] . " " . $_SESSION['serienmail'][$i]['nname']);
					}
					if ($i == 0) {
						// nur einmal an Schüfi in BCC
						$mail->addBCC("schuelerfirma@hgr-web.de", "Schülerfirma");
					}
					$mail->Body = $_SESSION['serienmail'][$i]['text'];
					$mail->Subject = $_SESSION['mail_step3']['subject'];
					if (!$mail->send()) {
						echo "Es ist ein Fehler beim Versenden der Mail aufgetreten.";
						echo $mail->ErrorInfo;
					}else {
						$successsend++;
					}
				}
				echo "Es wurden $successsend von $i E-Mails erfolgreich versendet";
				echo "<br><b>Hinweis: Es kann vorkommen, dass trotz der Meldung \"Mail erfolgreich gesendet\" die E-Mail-Adresse falsch war.<br>Deswegen musst du unbedingt nochmal in unseren Mail-Account schauen, ob dort eine Fehlermeldung vorliegt</b>Bei SerienMails wird die erste Mail als BCC an schuelerfirma@hgr-web.de gesendet.";
			}
		}
		// $mail->send();
	}
	
	if (isset($_GET['mail_reset']) && $_GET['mail_reset'] == 1) {
		unset($_SESSION['mail_step1']);
		unset($_SESSION['mail_step2']);
		unset($_SESSION['mail_step3']);
		unset($_SESSION['lehrermail']);
		unset($_SESSION['schuelermail']);
	}
	
	if ((!isset($_GET['step']) && !isset($_GET['send'])) || (isset($_GET['step']) && $_GET['step'] == 1)) {
		echo "<form action=\"" . htmlspecialchars($_SERVER['PHP_SELF']) . "?page=mail&step=2\" method=\"POST\"><p></p>";
		if (isset($_SESSION['mail_step1']) && isset($_SESSION['mail_step1']['mailart']) && $_SESSION['mail_step1']['mailart'] == 1) {
			echo "<label><input type=\"radio\" name=\"mailart\" value=\"1\" checked>E-Mail an ein Nachhilfepaar</label><br>";
			echo "<label><input type=\"radio\" name=\"mailart\" value=\"2\" >Serien-E-Mail/E-Mail an eine Person</label>";
		}else {
			echo "<label><input type=\"radio\" name=\"mailart\" value=\"1\" checked>E-Mail an ein Nachhilfepaar</label><br>";
			echo "<label><input type=\"radio\" name=\"mailart\" value=\"2\" >Serien-E-Mail/E-Mail an eine Person</label>";
		}
		echo "<label><input type=\"checkbox\" name=\"last_year\" value=\"true\" >Nutze Schüler- / Lehrerdaten aus vorigem Jahr</label>";
		echo "<br><br><input type=\"submit\" value=\"Weiter\" class=\"mybuttons\" style=\"float: right;\"><br><br><br></form>";
	}
	if (isset($_GET['step']) && $_GET['step'] == 2) {
		if (!isset($_SESSION['mail_step1']) || $_SESSION['mail_step1'] == 0 || count($_POST) != 0) {
			$_SESSION['mail_step1'] = $_POST;
		}
		?>
<form action="<?php
		
		echo htmlspecialchars($_SERVER['PHP_SELF']) . "?page=mail&step=3"?>" method="POST"><?php
		require 'includes/class_person.php';
		require 'includes/class_lehrer.php';
		require 'includes/class_schueler.php';
		require 'includes/class_paar.php';
		if (isset($_SESSION['mail_step1']['mailart']) && $_SESSION['mail_step1']['mailart'] != 1) {
			$return = query_db("SELECT * FROM `person` ORDER BY `person`.`nname` ASC");
			// $return = query_db("SELECT * FROM `lehrer` WHERE schuljahr = :schuljahr", get_current_year());
			$i = 0;
			$result = $return->fetch();
			$person = new person();
			$schueler_output = '';
			if ($result == false) {
				echo "EIN PROBLEM";
			}else {
				if (isset($_POST['last_year'])) {
					echo "<b>Achtung: Es handelt sich hierbei um Schüler und Lehrer aus dem letzten Jahr! Die angegebene Klasse bezieht sich auf das Schuljahr ".get_last_year()."</b>";
				}
				?>
				<div style="display: flex;">
				<script type="text/javascript">
			$(function() {
				$("A[href='#select_all_teacher']").click(function() {
					$(".teacher").prop('checked',true);
				});
				$("A[href='#select_all_student']").click(function() {
					$(".student").prop('checked',true);
				});
				$("A[href='#select_none_teacher']").click(function() {
					$(".teacher").prop('checked',false);
				});
				$("A[href='#select_none_student']").click(function() {
					$(".student").prop('checked',false);
				});
			});
			</script>
				<div style="display: inline-block; width: 30%;">
			<br>
			<br>Nachhilfelehrer:<?php
			echo "<br><br><a href=\"#select_all_teacher\" class=\"mybuttons\">Alle auswählen</a><a href=\"#select_none_teacher\" class=\"mybuttons\">Alle abwählen</a><br><br>";
				while ($result) {
					$person->load_person($result['id']);
					if (isset($_POST['last_year'])) {
						$lehrerschueler = $person->search_lehrer_schueler(get_last_year());						
					}else{
						$lehrerschueler = $person->search_lehrer_schueler();
					}
					if (is_array($lehrerschueler['lehrer'])) {
						
						$lehrer = new lehrer($person->id);
						if (isset($_POST['last_year'])) {
							$lehrer->load_lehrer_pid($person->id, get_last_year());
						}else{
							$lehrer->load_lehrer_pid();
						}
						if (isset($_SESSION['mail_step2']) && isset($_SESSION['mail_step2']['dest-' . $i]) && $_SESSION['mail_step2']['dest-' . $i] == $person->id . "-" . $person->email) {
							echo "<br><label><input type=\"checkbox\" class=\"teacher\" name=\"dest-$i\" value=\"" . $person->id . "-" . $person->email . "\" checked> " . $person->vname . " " . $person->nname . ", Klasse ". format_klassenstufe_kurs($lehrer->get_klassenstufe(),$lehrer->get_klasse()) . "<br>" . $person->email . "</label><br>";
						}else {
							echo "<br><label><input type=\"checkbox\" class=\"teacher\" name=\"dest-$i\" value=\"" . $person->id . "-" . $person->email . "\"> " . $person->vname . " " . $person->nname . ", Klasse ". format_klassenstufe_kurs($lehrer->get_klassenstufe(),$lehrer->get_klasse()) . "<br>" . $person->email . "</label><br>";
						}
					}
					if (is_array($lehrerschueler['schueler'])) {
						
						$schueler = new schueler($person->id);
						if (isset($_POST['last_year'])) {
							$schueler->load_schueler_pid($person->id, get_last_year());
						}else{
							$schueler->load_schueler_pid();
						}
						
						if (isset($_SESSION['mail_step2']) && isset($_SESSION['mail_step2']['dest-' . $i]) && $_SESSION['mail_step2']['dest-' . $i] == $person->id . "-" . $person->email) {
							$schueler_output .= "<br><label><input type=\"checkbox\" class=\"student\" name=\"dest-$i\" value=\"" . $person->id . "-" . $person->email . "\" checked> " . $person->vname . " " . $person->nname . ", Klasse ". format_klassenstufe_kurs($schueler->get_klassenstufe(),$schueler->get_klasse()) . "<br>" . $person->email . "</label><br>";
						}else {
							$schueler_output .= "<br><label><input type=\"checkbox\" class=\"student\" name=\"dest-$i\" value=\"" . $person->id . "-" . $person->email . "\"> " . $person->vname . " " . $person->nname . ", Klasse ". format_klassenstufe_kurs($schueler->get_klassenstufe(),$schueler->get_klasse()) . "<br>" . $person->email . "</label><br>";
						}
					}
					// var_dump($lehrer);
					$result = $return->fetch();
					$i++;
				}
			}
			?></div>

		<div style="display: inline-block; width: 30%;">
			<br>
			<br>Nachhilfeschüler:<?php
			echo "<br><br><a href=\"#select_all_student\" class=\"mybuttons\">Alle auswählen</a><a href=\"#select_none_student\" class=\"mybuttons\">Alle abwählen</a><br><br>";
			echo $schueler_output;
			echo "</div>";
		}else {
			// Nachhilfepaar
			$return = query_db("SELECT unterricht.* FROM `unterricht` LEFT JOIN lehrer ON unterricht.lid = lehrer.id WHERE lehrer.schuljahr = '" . get_current_year() . "';");
			$result = $return->fetch();
			$i = 0;
			$paar_output = '';
			if ($result == false) {
				echo "Ein Fehler trat auf: Es existiert noch kein Nachhilfepaar!";
			}else {
				while ($result) {
					$paar = new paar($result['id']);
					if (isset($_SESSION['mail_step2']) && isset($_SESSION['mail_step2']['dest_paar-' . $i]) && $_SESSION['mail_step2']['dest_paar-' . $i] == $paar->paarid) {
						$paar_output .= "<br><label><input type=\"radio\" name=\"dest_paar\" value=\"" . $paar->paarid . "\" checked> " . $paar->lehrer->person->vname . " " . $paar->lehrer->person->nname . " - " . $paar->schueler->person->vname . " " . $paar->schueler->person->nname . "<br>" . get_name_of_subject($paar->fid) . "</label><br>";
					}else {
						$paar_output .= "<br><label><input type=\"radio\" name=\"dest_paar\" value=\"" . $paar->paarid . "\"> " . $paar->lehrer->person->vname . " " . $paar->lehrer->person->nname . " - " . $paar->schueler->person->vname . " " . $paar->schueler->person->nname . "<br>" . get_name_of_subject($paar->fid) . "</label><br>";
					}
					$i++;
					$result = $return->fetch();
				}
				?>
			<div style="display: inline-block; width: 30%;">
				<br>
				<br>Nachhilfepaare:<?php
				echo $paar_output;
				?></div><?php
			}
		}
		?>
		
	</div>
		<input type="submit" value="Weiter" style="float: right;" class="mybuttons"><br>
		<br>
		<br>

</form>
<?php
	}
	if (isset($_GET['step']) && $_GET['step'] == 3) {
		if (!isset($_SESSION['mail_step2']) || $_SESSION['mail_step2'] == 0 || count($_POST) != 0) {
			$_SESSION['mail_step2'] = $_POST;
		}
		?>
<script type="text/javascript">
	function vermittlungsmail() {
		document.getElementById('subject2').value = 'Dein Nachhilfeunterricht in :fach';
		document.getElementById('subject').value = 'Deine Nachhilfe in :fach';
		document.getElementById('anhang').checked = 'checked';
		document.getElementById('anhang2').checked = 'checked';
		document.getElementById('textarea2').value = 'Hallo :vorname :nachname,\ndeine erste Nachhilfestunde wird am :tag um :treff_zeit im Zimmer :treff_raum stattfinden.\nDein Schüler ist :vorname_schueler :nachname_schueler.\nBitte setze dich mit Ihm in Kontakt. Du kannst Ihn unter :email_schueler erreichen.\nBitte lies unbedingt die der E-Mail anghängten Datein.\nSolltest du noch Fragen haben, kannst du uns gerne anschreiben.\n\nLiebe Grüße\nDie Schülerfirma\n\n';
		document.getElementById('textarea1').value = 'Hallo :vorname :nachname,\ndeine erste Nachhilfestunde wird am :tag um :treff_zeit im Zimmer :treff_raum stattfinden.\nDein Lehrer ist :vorname_lehrer :nachname_lehrer.\nBitte lies unbedingt die der E-Mail angehängten Datein.\nSolltest du noch Fragen haben, kannst du uns gerne schreiben.\n\nLiebe Grüße\nDie Schülerfirma\n';
	}
	function wartemail() {
		document.getElementById('subject').value = 'Deine Nachhilfe';
		document.getElementById('textarea1').value = 'Hallo :vorname :nachname,\nleider können wir dich momentan nicht vermitteln.\nWenn du willst, suchen wir aber weiter nach einem passenden Lehrer für dich.\n\nViele Grüße\nDie Schülerfirma';
	}
	function anmeldung_schueler() {
		document.getElementById('subject').value = 'Anmeldung bei der Schülerfirma';
		document.getElementById('textarea1').value = 'Hallo :vorname :nachname,\nvielen Dank für deine Anmeldung bei unserer Schülefirma "Schüler helfen Schülern".\nWir werden nun einen passenden Nachhilfelehrer für dich suchen. Wenn wir einen gefunden haben, melden wir uns wieder bei dir.\n\nViele Grüße\nDie Schülerfirma';
	}
	function anmeldung_lehrer() {
		document.getElementById('subject').value = 'Anmeldung bei der Schülerfirma';
		document.getElementById('textarea1').value = 'Hallo :vorname :nachname,\nvielen Dank für deine Anmeldung bei unserer Schülefirma "Schüler helfen Schülern".\nSobald wir einen passenden Schüler für dich gefunden haben, melden wir uns wieder bei dir.\n\nViele Grüße\nDie Schülerfirma';
	}
	function neuesfinanzsystem_mail() {
		document.getElementById('subject').value = 'Bezahlsystem der Schülerfirma';
		document.getElementById('textarea1').value = 'Hallo :vorname :nachname,\nEs gibt dieses Jahr für dich noch eine Neuerung. Und zwar stellen wir gerade auf ein neues Bezahlsystem um. Wie bisher gehabt musst du Geld für deine Nachhilfe bezahlen.\nDas geht entweder in Bar oder über unser Konto. Dann holst du dir bei uns eine Tutoring-Karte ab. Auf der Unterschreibt dein Nachhilfelehrer weiterhin. Dann musst du dich allerdings zusätzlich online bei der Schülerfirma anmelden.\n\
Die Zugangsdaten erhälst du in einer separaten E-Mail. \nDort kannst du dann deine Nachhilfestunden eingeben. Diese werden dann von deinem Lehrer bestätigt. Wenn ihr beide eine Unterrichtsstunde eingetragen habt, gilt diese als ganz normal gehalten und Konrad bekommt dann von uns seinen Lohn. \n\nFindet mal eine Nachhilfestunde nicht statt, weil z.B. einer von euch krank wird oder aus anderen Gründen fehlt oder nicht erscheint und dies vorher abgesprochen ist, \
tragt ihr einfach nichts für das  betreffende Datum ein. Sollte jedoch jemand unentschuldigt fehlen, dann tragt bitte einen entsprechenden Kommentar dazu ein. \n\
Da das ganze erstmal getestet wird, behalten wir das alte System noch eine Weile weiter.\n\n\
Wenn es noch Fragen oder Anregungen gibt oder etwas unklar ist, würde ich mich über eine Antwort sehr freuen.\n\nViele Grüße\nDie Schülerfirma';		
	}
	function add_text(text, element) {
		var content = document.getElementById(element).value;
		document.getElementById(element).value = content + text;
	}
	function reset_text(element) {
		document.getElementById(element).value = '';
	}
	</script>
<form action="index.php?page=mail&step=4" method="POST"><br>
	<?php
		// Email an Nachhilfepaar
		if (isset($_SESSION['mail_step1']['mailart']) && $_SESSION['mail_step1']['mailart'] == 1) {
			echo "<h3>E-Mail an Nachhilfelehrer:</h3>";
			echo "<p>Hinweis: <i>Die AGB's werden automatisch mit an die Vermittlungsemails angehängt sowie die Lohnkarte für den Lehrer und die Kontoinformationen für den Schüler<br>
					Sämtliche Textbausteine (:vorname,:fach...) werden erst im nächsten Schritt ersetzt.</i></p>";
			echo "<button type=\"button\" class=\"mybuttons\" onclick=\"vermittlungsmail()\">Vorlage für Vermittlungsmail</button><br>";
			if (isset($_SESSION['mail_step3']['subject2'])) {
				echo "<input id=\"subject2\" type=\"text\" name=\"subject2\" placeholder=\"Betreff\" value=\"" . strip_tags($_SESSION['mail_step3']['subject2']) . "\" style=\"width: 40%\">";
			}else {
				echo "<input id=\"subject2\" type=\"text\" name=\"subject2\" placeholder=\"Betreff\" style=\"width: 40%\">";
			}
			echo "<br>";
			if (isset($_SESSION['mail_step3']['anhang2']) && $_SESSION['mail_step3']['anhang2'] == 1) {
				echo "<label><input id=\"anhang2\" type=\"checkbox\" value=\"1\" name=\"anhang2\" checked>Vermittlungsdokument als Anhang</label>";
			}else {
				echo "<label><input id=\"anhang2\" type=\"checkbox\" value=\"1\" name=\"anhang2\">Vermittlungsdokument als Anhang</label>";
			}
			?>
			<br><br>
	<button type="button" onclick="add_text(' :vorname', 'textarea2')" class="mybuttons">Vorname</button>
	<button type="button" onclick="add_text(' :nachname', 'textarea2')" class="mybuttons">Nachname</button>
	<button type="button" onclick="add_text(' :email', 'textarea2')" class="mybuttons">Email</button><?php
			if (isset($_SESSION['mail_step1']['mailart']) && $_SESSION['mail_step1']['mailart'] == 1) {
				echo "<br><button type=\"button\" onclick=\"add_text(' :fach', 'textarea2')\" class=\"mybuttons\">Fach</button>";
				echo "<button type=\"button\" onclick=\"add_text(' :treff_raum', 'textarea2')\" class=\"mybuttons\">Raum</button>";
				echo "<button type=\"button\" onclick=\"add_text(' :tag', 'textarea2')\" class=\"mybuttons\">Tag</button>";
				echo "<button type=\"button\" onclick=\"add_text(' :treff_zeit', 'textarea2')\" class=\"mybuttons\">Anfangszeit</button>";
				echo "<br><button type=\"button\" onclick=\"add_text(' :vorname_schueler', 'textarea2')\" class=\"mybuttons\">Vorname des Schülers</button>";
				echo "<button type=\"button\" onclick=\"add_text(' :nachname_schueler', 'textarea2')\" class=\"mybuttons\">Nachname des Schülers</button>";
				echo "<button type=\"button\" onclick=\"add_text(' :email_schueler', 'textarea2')\" class=\"mybuttons\">E-Mail-Adresse des Schülers</button>";
			}
			?>
	<button type="button" onclick="reset_text('textarea2')" class="mybuttons">Reset</button><br>
	<textarea name="text2" rows="10" id="textarea2" placeholder="Schreibe den Text für die E-Mail hier hin..." style="width: 80%; padding: 5px;"><?php
			
			if (isset($_SESSION['mail_step3']['text2'])) {
				echo strip_tags($_SESSION['mail_step3']['text2']);
			}
			?>
	</textarea><br><br>
			<?php
			echo "<h3>E-Mail an Nachhilfeschüler:</h3>";
		}
		if (isset($_SESSION['mail_step3']['subject'])) {
			echo "<input id=\"subject\" type=\"text\" name=\"subject\" placeholder=\"Betreff\" value=\"" . strip_tags($_SESSION['mail_step3']['subject']) . "\" style=\"width: 40%\">";
		}else {
			echo "<input id=\"subject\" type=\"text\" name=\"subject\" placeholder=\"Betreff\" style=\"width: 40%\">";
		}
		echo "<br>";
		if (isset($_SESSION['mail_step1']['mailart']) && $_SESSION['mail_step1']['mailart'] == 1) {
			if (isset($_SESSION['mail_step3']['anhang']) && $_SESSION['mail_step3']['anhang'] == 1) {
				echo "<label><input id=\"anhang\" type=\"checkbox\" value=\"1\" name=\"anhang\" checked>Vermittlungsdokument als Anhang</label>";
			}else {
				echo "<label><input id=\"anhang\" type=\"checkbox\" value=\"1\" name=\"anhang\">Vermittlungsdokument als Anhang</label>";
			}
		}
		?>
	<br>
	<br>
	<button type="button" onclick="add_text(' :vorname', 'textarea1')" class="mybuttons">Vorname</button>
	<button type="button" onclick="add_text(' :nachname', 'textarea1')" class="mybuttons">Nachname</button>
	<button type="button" onclick="add_text(' :email', 'textarea1')" class="mybuttons">Email</button><?php
		if (isset($_SESSION['mail_step1']['mailart']) && $_SESSION['mail_step1']['mailart'] == 1) {
			echo "<br><button type=\"button\" onclick=\"add_text(' :fach', 'textarea1')\" class=\"mybuttons\">Fach</button>";
			echo "<button type=\"button\" onclick=\"add_text(' :treff_raum', 'textarea1')\" class=\"mybuttons\">Raum</button>";
			echo "<button type=\"button\" onclick=\"add_text(' :tag', 'textarea1')\" class=\"mybuttons\">Tag</button>";
			echo "<button type=\"button\" onclick=\"add_text(' :treff_zeit', 'textarea1')\" class=\"mybuttons\">Anfangszeit</button>";
			echo "<br><button type=\"button\" onclick=\"add_text(' :vorname_lehrer', 'textarea1')\" class=\"mybuttons\">Vorname des Lehrers</button>";
			echo "<button type=\"button\" onclick=\"add_text(' :nachname_lehrer', 'textarea1')\" class=\"mybuttons\">Nachname des Lehrers</button>";
			echo "<button type=\"button\" onclick=\"add_text(' :email_lehrer', 'textarea1')\" class=\"mybuttons\">E-Mail-Adresse des Lehrers</button>";
			echo "<button type=\"button\" onclick=\"reset_text('textarea1')\" class=\"mybuttons\">Reset</button>";
		}else {
			echo "<button type=\"button\" onclick=\"reset_text('textarea1')\" class=\"mybuttons\">Reset</button><br>";
			echo "<button type=\"button\" onclick=\"wartemail()\" class=\"mybuttons\">Vorlage für Warteschreiben</button>";
			echo "<button type=\"button\" onclick=\"anmeldung_schueler()\" class=\"mybuttons\">Anmmeldungs-E-Mail für Schüler</button>";
			echo "<button type=\"button\" onclick=\"anmeldung_lehrer()\" class=\"mybuttons\">Anmeldungs-E-Mail für Lehrer</button>";
			echo "<button type=\"button\" onclick=\"neuesfinanzsystem_mail()\" class=\"mybuttons\">Erklärung des neuen Finanzsystems für Schüler</button>";
		}
		?>
	<textarea name="text" rows="10" id="textarea1" placeholder="Schreibe den Text für die E-Mail hier hin..." style="width: 80%; padding: 5px;"><?php
		
		if (isset($_SESSION['mail_step3']['text'])) {
			echo strip_tags($_SESSION['mail_step3']['text']);
		}
		?></textarea>
	<br>
	<br>
	<input type="submit" value="Weiter" style="float: right;" class="mybuttons"><br>
	<br>
	<br>
</form>
<?php
	}
	if (isset($_GET['step']) && $_GET['step'] == 4) {
		if (!isset($_SESSION['mail_step3']) || $_SESSION['mail_step3'] == 0 || count($_POST) != 0) {
			if (isset($_SESSION['mail_step1']['mailart']) && $_SESSION['mail_step1']['mailart'] == 1) {
				$_SESSION['mail_step3']['subject2'] = $_POST['subject2'];
				$_SESSION['mail_step3']['text2'] = $_POST['text2'];
				if (isset($_POST['anhang2'])) {
					$_SESSION['mail_step3']['anhang2'] = '1';
				}else {
					$_SESSION['mail_step3']['anhang2'] = '0';
				}
			}
			$_SESSION['mail_step3']['subject'] = $_POST['subject'];
			$_SESSION['mail_step3']['text'] = $_POST['text'];
			if (isset($_POST['anhang'])) {
				$_SESSION['mail_step3']['anhang'] = '1';
			}else {
				$_SESSION['mail_step3']['anhang'] = '0';
			}
		}
		if (isset($_SESSION['mail_step3']) && isset($_SESSION['mail_step1']) && isset($_SESSION['mail_step2'])) {
			if ($_SESSION['mail_step1']['mailart'] == 1) {
				// Email an Nachhilfepaar
				require 'includes/class_paar.php';
				$paar = new paar(intval($_SESSION['mail_step2']['dest_paar']));
				$lehrertext = $_SESSION['mail_step3']['text2'];
				$schuelertext = $_SESSION['mail_step3']['text'];
				$schuelertext = str_replace("\n", "<br>", $schuelertext);
				$lehrertext = str_replace("\n", "<br>", $lehrertext);
				$lehrertext = str_replace(":vorname_schueler", $paar->schueler->person->vname, $lehrertext);
				$lehrertext = str_replace(":nachname_schueler", $paar->schueler->person->nname, $lehrertext);
				$lehrertext = str_replace(":email_schueler", $paar->schueler->person->email, $lehrertext);
				$lehrertext = str_replace(":vorname", $paar->lehrer->person->vname, $lehrertext);
				$lehrertext = str_replace(":nachname", $paar->lehrer->person->nname, $lehrertext);
				$lehrertext = str_replace(":email", $paar->lehrer->person->email, $lehrertext);
				$lehrertext = str_replace(":fach", get_name_of_subject($paar->fid), $lehrertext);
				$lehrertext = str_replace(":treff_raum", $paar->raum, $lehrertext);
				$lehrertext = str_replace(":tag", get_name_of_day($paar->tag), $lehrertext);
				$lehrertext = str_replace(":treff_zeit", $paar->anfang, $lehrertext);
				$lehrertext = str_replace(":tag", get_name_of_day($paar->tag), $lehrertext);
				$schuelertext = str_replace(":vorname_lehrer", $paar->lehrer->person->vname, $schuelertext);
				$schuelertext = str_replace(":nachname_lehrer", $paar->lehrer->person->nname, $schuelertext);
				$schuelertext = str_replace(":email_lehrer", $paar->lehrer->person->email, $schuelertext);
				$schuelertext = str_replace(":vorname", $paar->schueler->person->vname, $schuelertext);
				$schuelertext = str_replace(":nachname", $paar->schueler->person->nname, $schuelertext);
				$schuelertext = str_replace(":email", $paar->schueler->person->email, $schuelertext);
				$schuelertext = str_replace(":fach", get_name_of_subject($paar->fid), $schuelertext);
				$schuelertext = str_replace(":treff_raum", $paar->raum, $schuelertext);
				$schuelertext = str_replace(":tag", get_name_of_day($paar->tag), $schuelertext);
				$schuelertext = str_replace(":treff_zeit", $paar->anfang, $schuelertext);
				$schuelertext = str_replace(":tag", get_name_of_day($paar->tag), $schuelertext);
				$lehrermail = array(
						'empfaenger' => $paar->lehrer->person->email, 
						'text' => $lehrertext, 
						'betreff' => str_replace(":fach", get_name_of_subject($paar->fid), $_SESSION['mail_step3']['subject2']), 
						'anhang' => ''
				);
				$schuelermail = array(
						'empfaenger' => $paar->schueler->person->email, 
						'text' => $schuelertext, 
						'betreff' => str_replace(":fach", get_name_of_subject($paar->fid), $_SESSION['mail_step3']['subject2']), 
						'anhang' => ''
				);
				if ($_SESSION['mail_step3']['anhang2'] == 1) {
					$lehrermail['anhang'] = $paar->lehrer_dokument;
				}
				if ($_SESSION['mail_step3']['anhang'] == 1) {
					$schuelermail['anhang'] = $paar->schueler_dokument;
				}
				echo "<h3>E-Mail an den Lehrer:</h3>";
				echo "Betreff: <i>" . $lehrermail['betreff'] . "</i><br>";
				echo "An: <i>" . $lehrermail['empfaenger'] . "</i><br>";
				echo "Anhänge: <i>" . $lehrermail['anhang'] . "</i><br>";
				echo "Text: <br><i>" . $lehrermail['text'] . "</i><br><br>";
				
				echo "<h3>E-Mail an den Schüler:</h3>";
				echo "Betreff: <i>" . $schuelermail['betreff'] . "</i><br>";
				echo "An: <i>" . $schuelermail['empfaenger'] . "</i><br>";
				echo "Anhänge: <i>" . $schuelermail['anhang'] . "</i><br>";
				echo "Text: <br><i>" . $schuelermail['text'] . "</i><br><br>";
				$_SESSION['lehrermail'] = $lehrermail;
				$_SESSION['schuelermail'] = $schuelermail;
			}else {
				// serienmail
				echo "<br><br>";
				require 'includes/class_person.php';
				if (!is_array($_SESSION['mail_step2'])) {
					echo "Ein Fehler ist aufgetreten";
					$user->log(user::LEVEL_ERROR, "Array mail_step2 nicht definiert");
				}
				// var_dump($_SESSION);
				// echo $_SESSION['mail_step2']["dest-1"];
				$personen = array();
				$mailpersonen = array();
				$personid = array();
				$person = new person();
				$personen = array_values($_SESSION['mail_step2']);
				for ($i = 0; $i < count($personen); $i++) {
					$personid = explode("-", $personen[$i]);
					// echo "<br>".$personen[$i]." ".$personid[0];
					$person->load_person($personid[0]);
					$string = str_replace("\n", "<br>", $_SESSION['mail_step3']['text']);
					$string = str_replace(":vorname", $person->vname, $string);
					$string = str_replace(":nachname", $person->nname, $string);
					$string = str_replace(":email", $person->email, $string);
					$mailpersonen[] = array(
							'mail' => $person->email, 
							'vname' => $person->vname, 
							'nname' => $person->nname, 
							'text' => $string
					);
					echo "<br>Betreff: <i>" . $_SESSION['mail_step3']['subject'] . "</i><br>";
					echo "An: <i>" . $mailpersonen[$i]['mail'] . "</i><br>";
					echo "Text: <br><i>" . $mailpersonen[$i]['text'] . "</i><br><br><hr>";
				}
				// var_dump($string);
				// var_dump($personen);
				// var_dump($mailpersonen);
				$_SESSION['serienmail'] = $mailpersonen;
				// var_dump($_SESSION);
			}
			if (get_xml("livesystem","value") !== null && get_xml("livesystem","value") != 'true' && get_xml("testmail","value") !== null) {
				echo "<b>Achtung: Diese Mails werden momentan alle nicht an die richtige Adresse gesendet. Das momentane Ziel ist ".get_xml("testmail","value")."</b>";
			}
		}
		?>
<form action="<?php
		
		echo htmlspecialchars($_SERVER['PHP_SELF']) . "?page=mail&send=1"?>" method="POST">
	<input type="submit" value="Absenden" style="float: right;" class="mybuttons"><br>
	<br>
	<br>
</form>
<?php
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
?>