<?php
if (isset($user) && $user->runscript()) {
	if (isset($_GET['createdoc_paar']) || isset($_GET['renew_docs'])) {
		require 'includes/class_paar.php';
		if(isset($_GET['createdoc_paar'])) {
			$paar = new paar($_GET['createdoc_paar']);
		}else{
			//@TODO aktuelles Schuljahr
			$return = query_db("SELECT * FROM `unterricht`");
			if($return) {
				$result = $return->fetch();
				if($result) {
					$paar = new paar($result['id']);								
				}else{
					$paar = false;
				}
			}
		}
			require 'extensions/tcpdf/TCPDF-master/tcpdf.php';
			echo "<h2>Erstelle Vermittlungsdokumente</h2>";
			class MYPDF extends TCPDF {
				public function Header() {
					global $GLOBAL_CONFIG;
					$image_file = 'img/logo.jpg';
					$this->Image($image_file, 10, 5, 25, '', 'JPG', get_xml("contact/homepage","value"), 'T', false, 300, '', false, false, 0, false, false, false);
					$this->SetFont('helvetica', 'B', 20);
					$this->Ln(10);
					$this->Cell(0, 15, 'Vermittlungsdokument', 0, false, 'C', 0, '', 0, false, 'M', 'M');
					$this->Ln(10);
					$this->SetFont('helvetica', '', 15);
					$this->Cell(0, 15, 'Schülerfirma \'Schüler helfen Schülern\'', 0, false, 'C', 0, '', 0, false, 'M', 'M');
				}
				public function Footer() {
					global $GLOBAL_CONFIG;
					$this->SetY(-15);
					$this->SetFont('helvetica', '', 8);
					$this->addHtmlLink(get_xml("contact/homepage","value"), "Website: ".get_xml("contact/homepage","value"), false, true, array(
							0, 
							0, 
							0
					), '', false);
					$this->Ln(5);
					$this->addHtmlLink("mailto:".get_xml("contact/email","value"), "E-Mail-Adresse: ".get_xml("contact/email","value"), false, true, array(
							0, 
							0, 
							0
					), '', false);
					$this->Cell(0, 0, 'Seite ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
				}
			}
			while($paar) {
		
			for ($i = 0; $i < 2; $i++) {
				if ($i == 0) {
					$file = file_get_contents("docs/vorlage.html");
				}else {
					$file = file_get_contents("docs/vorlage_schueler.html");
				}
				$file = str_replace("::namelehrer", $paar->lehrer->person->vname . " " . $paar->lehrer->person->nname, $file);
				$file = str_replace("::nameschueler", $paar->schueler->person->vname . " " . $paar->schueler->person->nname, $file);
				$file = str_replace("::schuefimail", get_xml("contact/email","value"), $file);
				$file = str_replace("::schuefiweb", get_xml("contact/homepage","value"), $file);
				$file = str_replace("::tag", get_name_of_day($paar->tag), $file);
				$file = str_replace("::raum", $paar->raum, $file);
				$file = str_replace("::zeit", $paar->anfang, $file);
				$file = str_replace("::emailschueler", $paar->schueler->person->email, $file);
				$file = str_replace("::lehrerklasse", format_klassenstufe_kurs($paar->lehrer->get_klassenstufe(), $paar->lehrer->get_klasse()), $file);
				$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				
				$pdf->SetCreator(PDF_CREATOR);
				$pdf->SetAuthor($user->vname . " " . $user->nname);
				$pdf->SetTitle('Nachhilfeunterricht von ' . $paar->lehrer->person->vname . " " . $paar->lehrer->person->nname);
				$pdf->SetSubject('Vermittlung');
				
				$pdf->setHeaderFont(Array(
						PDF_FONT_NAME_MAIN, 
						'', 
						PDF_FONT_SIZE_MAIN
				));
				$pdf->setFooterFont(Array(
						PDF_FONT_NAME_DATA, 
						'', 
						PDF_FONT_SIZE_DATA
				));
				$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
				$pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
				$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
				$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
				$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
				$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
				$pdf->SetFont('dejavusans', '', 10);
				
				$pdf->AddPage();
				$pdf->writeHTML($file, true, false, true, false, '');
				if ($i == 0) {
					$pdf->Output(__DIR__ . '/../docs/unterricht/lehrer-' . $paar->lehrer->get_id() . 'paar-' . $paar->paarid . '.pdf', 'F');
					$paar->adddokument('lehrer-' . $paar->lehrer->get_id() . 'paar-' . $paar->paarid . '.pdf');
					echo "Dokument für ".$paar->lehrer->person->vname." ". $paar->lehrer->person->nname ." erfolgreich erstellt<br><br>";
				}else {
					$pdf->Output(__DIR__ . '/../docs/unterricht/schueler-' . $paar->schueler->get_id() . 'paar-' . $paar->paarid . '.pdf', 'F');
					$paar->adddokument("", 'schueler-' . $paar->schueler->get_id() . 'paar-' . $paar->paarid . '.pdf');
					echo "Dokument für ".$paar->schueler->person->vname." ". $paar->schueler->person->nname ." erfolgreich erstellt<br><br>";
				}
			}
			//Notwendig für korrekten Link zurück zum Paar
			$paarid = $paar->paarid;
			if (isset($_GET['renew_docs'])) {
				$result = $return->fetch();
				if($result) {
					$paar = new paar($result['id']);								
				}else{
					$paar = false;
				}
			}else{
				$paar = false;
			}
		}
		if (isset($_GET['createdoc_paar'])) {
			echo "<a href=\"index.php?page=output&paare=1&filter=" . $paarid . "\" class=\"links2\">Zurück zum Paar</a>";
		}
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
