<?php
include_once("./templates/system_header.php");

if (array_key_exists('pw', $_GET)) {
	$pw = $_GET['pw'];
	$salt = User::generateSalt();
	$hash = md5(md5($pw).$salt);

	echo $pw;
	echo "<br/>";
	echo $hash;
	echo "<br/>";
	echo $salt;
}

die();
