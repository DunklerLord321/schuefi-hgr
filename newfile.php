<?php
//$empfaenger = "gajo01@gmx.de";
$empfaenger = "yajo10@yahoo.de";
$betreff = "Die Mail-Funktion";
$from = "From: Schuelerfirma HGR <schuelerfirma.hgr@gmx.de>";
$text = "Hallo, ich freu mich, von dir zu hören :)";

$ret = mail($empfaenger, $betreff, $text, $from);
if($ret == false) {
	echo "FAILURE";
}
?>