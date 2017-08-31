<?php
if (isset($_GET['user']) && isset($_GET['passwd'])) {
	echo "Test Connection";
	var_dump($_GET);
	try {
		$pdo = new PDO('mysql:dbname=DB3071995;host=rdbms.strato.de', 'U3071995', 'hg8BG3A+gKlr5H4e86S5-P');
	}catch (PDOException $e) {
		echo "FEHLER " . $e;
		die();
	}
	var_dump($pdo);
	var_dump($eret = $pdo->query("SET foreign_key_checks=0;"));
	echo "<br><br><br>";
	var_dump($eret = $pdo->query("SHOW TABLES"));
	var_dump($eret->fetchAll());
	echo "<br>Hallo";
	$file = fopen("sql/schuefi-2.0.sql", "r");
	$content = fread($file, filesize("sql/schuefi-2.0.sql"));
	var_dump($ret = $pdo->exec($content));
	fclose($file);
	echo "start user<br>";
	$file = fopen("sql/user.sql", "r");
	$content = fread($file, filesize("sql/user.sql"));
	var_dump($pdo->exec($content));
	echo "<br><br>\n\n";
	var_dump($pdo->errorInfo());
	$ret = $pdo->query("SELECT * FROM `users`");
	echo "\n\n<br><br>";
	var_dump($ret->fetchAll());
	fclose($file);
	echo "Wrote users successfull";
	$file = fopen("sql/navigation.sql", "r");
	$content = fread($file, filesize("sql/navigation.sql"));
	var_dump($pdo->exec($content));
	fclose($file);
	echo "Finish";
}else {
	echo "Gib als GET user, host, dbname und passwd an";
}