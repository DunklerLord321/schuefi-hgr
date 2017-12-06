<?php
if (isset($user) && $user->runscript()) {
	if (isset($_GET['log'])) {
		$file = fopen("error.log", "r");
		$content = fread($file, filesize("error.log"));
		$content = str_replace("\n", "<br>", $content);
		echo "<br><br><div id=\"top\"><a href=\"#bottom\" class=\"links\">Nach Unten</a></div><br>";
		echo $content;
		echo "<div id=\"bottom\"><a href=\"#top\" class=\"links\">Nach Oben</a></div><br>";
		fclose($file);
	}else {
		?>
<a href="index.php?page=settings&log=1" class="links2">Log-Datei ansehen</a>

<?php
	}
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
