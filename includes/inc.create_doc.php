<?php
if (isset($user) && $user->runscript()) {
	global $pdo;
	if (isset($_GET['create'])) {
		require 'extensions/tcpdf/TCPDF-master/tcpdf.php';
		echo "<h2>Erstelle Word-Dokumente</h2>";
		$file = file_get_contents("docs/vorlage.html");
		// echo $file;
		$html = '<h1>Test des Schülers<h1>';
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($user->getemail());
		$pdf->SetTitle('Nachhilfe von Test');
		$pdf->SetSubject('Rechnung ');
		
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
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->SetFont('dejavusans', '', 10);
		
		$pdf->AddPage();
		ob_start();
		$pdf->writeHTML($file, true, false, true, false, '');
		
		// Ausgabe der PDF
		
		// Variante 1: PDF direkt an den Benutzer senden:
		$pdf->Output("Testpdf.pdf", 'I');
		
		// Variante 2: PDF im Verzeichnis abspeichern:
		// $pdf->Output(dirname(__FILE__).'/'.$pdfName, 'F');
		// echo 'PDF herunterladen: <a href="'.$pdfName.'">'.$pdfName.'</a>';
	}
} else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
