<?php
if (isset($user) && $user->runscript()) {
	echo "<h1>Monatsberichte erstellen</h1>";
	if (isset($_GET['export']) == 1) {
		$return = query_db("SELECT finanzuebersicht.*, users.vname, users.nname,
				person.vname AS pvname, person.nname AS pnname FROM `finanzuebersicht`
				LEFT JOIN `users` ON finanzuebersicht.uid = users.id LEFT JOIN `person` ON finanzuebersicht.pid = person.id ORDER BY finanzuebersicht.datum ASC;");
		if ($return != false) {
			$result = $return->fetch();
			$sum_bar = 0;
			$sum_konto = 0;
			$sum_jahr_bar = 0;
			$sum_jahr_konto = 0;
			$sum_monat_bar = 0;
			$sum_monat_konto = 0;
			$uebertrag_bar = 0;
			$uebertrag_konto = 0;
			$einnahmen_konto = 0;
			$einnahmen_bar = 0;
			$ausgaben_konto = 0;
			$ausgaben_bar = 0;
			$i = 0;
			$output = array();
			$months = array();
			$fill_output = array(
					'name' => '', 
					'posten' => array(),
					'uebertrag_bar' => 0,
					'uebertrag_konto' => 0,
					'sum_bar' => 0, 
					'sum_konto' => 0, 
					'sum_monat_bar' => 0, 
					'sum_monat_konto' => 0, 
					'sum_jahr_bar' => 0, 
					'sum_jahr_konto' => 0,
					'einnahmen_konto' => 0,
					'einnahmen_bar' => 0,
					'ausgaben_konto' => 0,
					'ausgaben_bar' => 0
			);
			//Für jeden Eintrag in Finanztabelle einen Schleifendurchlauf
			while ($result) {
				//prüfe, ob es schon einen Schleifendurchlauf gab
				if (isset($result_vor)) {
					//Teste, ob vorhergenden Eintrag in eherem Monat oder Jahr war: wenn ja, muss Übertrag gebildet werden
					if (intval(date('m', strtotime($result_vor['datum']))) < intval(date('m', strtotime($result['datum']))) || intval(date('y', strtotime($result_vor['datum']))) < intval(date('y', strtotime($result['datum'])))) {
						//Teste, ob vorheriger Monat zu den ausgewählten Monaten zählt
						if (array_search(date('m.Y', strtotime($result_vor['datum'])), $_POST['months']) !== false) {
							$output[$i]['name'] = strftime('%B %Y', strtotime($result_vor['datum']));
							$output[$i]['posten'] = $months;
							$output[$i]['uebertrag_bar'] = $uebertrag_bar;
							$output[$i]['uebertrag_konto'] = $uebertrag_konto;
							$output[$i]['sum_bar'] = $sum_bar;
							$output[$i]['sum_monat_bar'] = $sum_monat_bar;
							$output[$i]['sum_jahr_bar'] = $sum_jahr_bar;
							$output[$i]['sum_konto'] = $sum_konto;
							$output[$i]['sum_monat_konto'] = $sum_monat_konto;
							$output[$i]['sum_jahr_konto'] = $sum_jahr_konto;
							$output[$i]['einnahmen_konto'] = $einnahmen_konto;
							$output[$i]['einnahmen_bar'] = $einnahmen_bar;
							$output[$i]['ausgaben_konto'] = $ausgaben_konto;
							$output[$i]['ausgaben_bar'] = $ausgaben_bar;
							$i++;
							$months = array();
						}
						$uebertrag_bar += $sum_monat_bar;
						$uebertrag_konto += $sum_monat_konto;
						$sum_monat_bar = 0;
						$sum_monat_konto = 0;
						$einnahmen_bar = 0;
						$einnahmen_konto = 0;
						$ausgaben_bar = 0;
						$ausgaben_konto = 0;
						if (intval(date('y', strtotime($result_vor['datum']))) < intval(date('y', strtotime($result['datum'])))) {
							$sum_jahr_bar = 0;
							$sum_jahr_konto = 0;
						}
					}
				}else {
					//start
					if (array_search(date('m.Y', strtotime($result['datum'])), $_POST['months']) !== false) {
						$output[] = $fill_output;
					}
				}
				if (array_search(date('m.Y', strtotime($result['datum'])), $_POST['months']) !== false) {
					$months[] = $result;
				}
				if ($result['konto_bar'] == "konto") {
					$sum_jahr_konto += $result['geldbetrag'];
					$sum_konto += $result['geldbetrag'];
					$sum_monat_konto += $result['geldbetrag'];
					($result['geldbetrag']>0?$einnahmen_konto+=$result['geldbetrag']:$ausgaben_konto+=$result['geldbetrag']);
				}else if ($result['konto_bar'] == "bar") {
					$sum_jahr_bar += $result['geldbetrag'];
					$sum_bar += $result['geldbetrag'];
					$sum_monat_bar += $result['geldbetrag'];
					($result['geldbetrag']>0?$einnahmen_bar+=$result['geldbetrag']:$ausgaben_bar+=$result['geldbetrag']);
				}else {
					echo "Ein grober Fehler trat auf. Die Summen konnten nicht ermittelt werden";
					die();
				}
				$result_vor = $result;
				$result = $return->fetch();
			}
			if (array_search(date('m.Y', strtotime($result_vor['datum'])), $_POST['months']) !== false) {
				$output[$i]['name'] = date('M Y', strtotime($result_vor['datum']));
				$output[$i]['posten'] = $months;
				$output[$i]['uebertrag_bar'] = $uebertrag_bar;
				$output[$i]['uebertrag_konto'] = $uebertrag_konto;
				$output[$i]['sum_bar'] = $sum_bar;
				$output[$i]['sum_monat_bar'] = $sum_monat_bar;
				$output[$i]['sum_jahr_bar'] = $sum_jahr_bar;
				$output[$i]['sum_konto'] = $sum_konto;
				$output[$i]['sum_monat_konto'] = $sum_monat_konto;
				$output[$i]['sum_jahr_konto'] = $sum_jahr_konto;
				$output[$i]['einnahmen_konto'] = $einnahmen_konto;
				$output[$i]['einnahmen_bar'] = $einnahmen_bar;
				$output[$i]['ausgaben_konto'] = $ausgaben_konto;
				$output[$i]['ausgaben_bar'] = $ausgaben_bar;
			}
			//Erstellen Des PDF-Dokuments
			require 'extensions/tcpdf/TCPDF-master/tcpdf.php';
			class MYPDF extends TCPDF {
				public function Header() {
					global $GLOBAL_CONFIG;
					$image_file = 'img/logo.jpg';
					$this->Image($image_file, 10, 5, 25, '', 'JPG', get_xml("contact/homepage","value"), 'T', false, 300, '', false, false, 0, false, false, false);
					$this->SetFont('helvetica', 'B', 20);
					$this->Ln(10);
					$this->Cell(0, 15, 'Finanzexport', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
			$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor($user->vname . " " . $user->nname);
			$pdf->SetTitle('Finanzbericht');
			$pdf->SetSubject('Finanzbericht');	
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
			$pdf->SetFont('helvetica', '', 10);
				
			for ($i = 0; $i < count($output); $i++) {
				$pdf->AddPage();
				$html = '<body dir="ltr" style="max-width: 21.001cm; margin: 1.27cm;"><h2>' . $output[$i]['name'] . '</h2>';
				$html .= '<p>Übertrag Konto: ' . $output[$i]['uebertrag_konto'] . '€</p><p>Übertrag Bargeldkasse: ' . $output[$i]['uebertrag_bar'] . '€</p>
				<table style="width: 100%; border: none; border-collapse: collapse; line-height: 150%; text-align: center">
				<tr>
				<th>Datum</th>
				<th>Name</th>
				<th align="right">Konto<br>Einnahmen</th>
				<th align="right">Konto<br>Ausgaben</th>
				<th align="right">Bar<br>Einnahmen</th>
				<th align="right">Bar<br>Ausgaben</th>
				<th>Betreff</th>
				<th>Bemerkung</th>
				</tr>';
				for ($j = 0; $j < count($output[$i]['posten']); $j++) {
					if ($j % 2 == 0) {
						$html .= "<tr style=\"background-color: #dddddd;\">";
					}else{
						$html .= "<tr>";
					}
					$html .= "<td>".date('d.m.Y', strtotime($output[$i]['posten'][$j]['datum']))."</td>";
					if ($output[$i]['posten'][$j]['pid'] == NULL) {
						$html .= "<td>".$output[$i]['posten'][$j]['vname']." ".$output[$i]['posten'][$j]['nname']."</td>";
					}else{
						$html .= "<td>".$output[$i]['posten'][$j]['pvname']." ".$output[$i]['posten'][$j]['pnname']."</td>";
					}
					if ($output[$i]['posten'][$j]['konto_bar'] == "konto") {
						if (intval($output[$i]['posten'][$j]['geldbetrag']) > 0) {
							$html .= "<td align=\"right\">".$output[$i]['posten'][$j]['geldbetrag']."€</td><td></td><td></td><td></td>";
						}else{
							$html .= "<td></td><td align=\"right\">".abs($output[$i]['posten'][$j]['geldbetrag'])."€</td><td></td><td></td>";
						}
					}else{
						if (intval($output[$i]['posten'][$j]['geldbetrag']) > 0) {
							$html .= "<td></td><td></td><td align=\"right\">".$output[$i]['posten'][$j]['geldbetrag']."€</td><td></td>";
						}else{
							$html .= "<td></td><td></td><td></td><td align=\"right\">".abs($output[$i]['posten'][$j]['geldbetrag'])."€</td>";
						}
					}
					$html .= "<td>".$output[$i]['posten'][$j]['betreff']."</td><td>".$output[$i]['posten'][$j]['bemerkung']."</td></tr>";
				}
				$html .= ($j%2==0?"<tr style=\"background-color: #dddddd;\" >":"<tr>")."<td colspan=\"2\">Summe der Einnahmen:</td><td align=\"right\">".$output[$i]['einnahmen_konto']."€</td>
						<td></td><td align=\"right\">".$output[$i]['einnahmen_bar']."€</td><td></td><td><i>Gesamt:</i></td><td align=\"right\">".(intval($output[$i]['einnahmen_konto'])+intval($output[$i]['einnahmen_bar']))."€</td></tr>";
				$html .= (($j+1)%2==0?"<tr style=\"background-color: #dddddd;\">":"<tr>")."<td colspan=\"2\">Summe der Ausgaben:</td><td></td><td align=\"right\">".abs($output[$i]['ausgaben_konto'])."€</td>
						<td></td><td align=\"right\">".abs($output[$i]['ausgaben_bar'])."€</td><td><i>Gesamt:</i></td><td align=\"right\">".abs(intval($output[$i]['ausgaben_konto'])+intval($output[$i]['ausgaben_bar']))."€</td></tr>";
				$html .= (($j+2)%2==0?"<tr style=\"background-color: #dddddd; border-top: double;\">":"<tr style=\"border-top: double;\">")."<td colspan=\"2\"><i>Gesamt:</i></td><td></td><td align=\"right\">".$output[$i]['sum_monat_konto']."€</td>
						<td></td><td align=\"right\">".$output[$i]['sum_monat_bar']."€</td><td><i>Gesamt:</i></td><td align=\"right\">".($output[$i]['sum_monat_konto']+$output[$i]['sum_monat_bar'])."€</td></tr>";
				$html .= "</table>";
				$html .= "<br><br><br>Neuer Kontostand: ".$output[$i]['sum_konto']."€<br><br>Neuer Stand der Bargeldkasse: ".$output[$i]['sum_bar']."€";
				if (isset($output[$i]['posten'][($j-1)]) && date('m',strtotime($output[$i]['posten'][($j-1)]['datum'])) == "12") {
					$html .= "<br><br><hr><br><h3>Jahresbilanz ".date('Y',strtotime($output[$i]['posten'][($j-1)]['datum']))."</h3><br><br>Summe der Einnahmen und Ausgaben Konto: ".$output[$i]['sum_jahr_konto']."€
							<br><br>Summe der Einnahmen und Ausgaben Bargeldkasse: ".$output[$i]['sum_jahr_bar']."€";
				}
				$html .= "</body>";
				$pdf->writeHTML($html, true, false, true, false, '');
			}
			$pdf->Output(__DIR__.'/../docs/finanzen/finanzbericht.pdf', 'F');
			
			if (file_exists(__DIR__.'/../docs/finanzen/finanzbericht.pdf')) {
				echo "<a href=\"docs/finanzen/finanzbericht.pdf\" class=\"links2\">Finanzbericht</a>";
			}else{
				echo "Ein grober Fehler trat auf. Das Dokument konnte nicht gefunden werden";
			}
		}
	}else {
		$return = query_db("SELECT finanzuebersicht.*, users.vname, users.nname, 
				person.vname AS pvname, person.nname AS pnname FROM `finanzuebersicht` 
				LEFT JOIN `users` ON finanzuebersicht.uid = users.id LEFT JOIN `person` ON finanzuebersicht.pid = person.id  ORDER BY finanzuebersicht.datum ASC;");
		if ($return != false) {
			$result = $return->fetch();
			$sum_bar = 0;
			$sum_konto = 0;
			echo "<div class=\"formular_class\"><form method=\"POST\" action=\"index.php?page=export_finanzen&export=1\">";
			echo "<a href=\"#select_all\" class=\"links2\">Alle auswählen</a><br><br><a href=\"#select_none\" class=\"links2\">Alle abwählen</a><br><br><br>";
			while ($result) {
				if (isset($result_vor)) {
					if (intval(date('m', strtotime($result_vor['datum']))) < intval(date('m', strtotime($result['datum']))) || intval(date('y', strtotime($result_vor['datum']))) < intval(date('y', strtotime($result['datum'])))) {
						echo "  Kontostand: " . $sum_konto . "€ Kassenstand: " . $sum_bar . "€<br><label>
					<input type=\"checkbox\" name=\"months[]\" value=\"" . date('m.Y', strtotime($result['datum'])) . "\"><b>" . strftime('%B %Y', strtotime($result['datum'])) . "</b></label>";
					}
				}else {
					echo "<label><input type=\"checkbox\" name=\"months[]\" value=\"" . date('m.Y', strtotime($result['datum'])) . "\"><b>" . strftime('%B %Y', strtotime($result['datum'])) . "</b></label>";
				}
				if ($result['konto_bar'] == "konto") {
					$sum_konto += $result['geldbetrag'];
				}else if ($result['konto_bar'] == "bar") {
					$sum_bar += $result['geldbetrag'];
				}else {
					echo "Ein grober Fehler trat auf. Die Summen konnten nicht ermittelt werden";
					die();
				}
				$result_vor = $result;
				$result = $return->fetch();
			}
			echo "  Kontostand: " . $sum_konto . "€ Kassenstand: " . $sum_bar . "€<br><br><br><input type=\"submit\" class=\"mybuttons\" value=\"Bericht erstellen\">";
		}
	}
	?>
	<script type="text/javascript">
$(function() {
	$("A[href='#select_all']").click(function() {
		$("INPUT[type='checkbox']").prop('checked',true);
	});
	$("A[href='#select_none']").click(function() {
		$("INPUT[type='checkbox']").prop('checked',false);
	});
});
	
</script>
	
	
	<?php
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
	