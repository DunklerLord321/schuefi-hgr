<?php
$passwort = "ferien";
$passwort_hash = password_hash ( $passwort, PASSWORD_DEFAULT );
echo $passwort."<br>".$passwort_hash;
