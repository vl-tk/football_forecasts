<?php
require_once('./templates/system_header.php');

if ($user->isAdmin()):
	//header('Content-Type: text/html; charset=utf-8');
    header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.basename("db_cup_".date("Y-m-d_H-i-s").".sql"));
	$dump = Database::dump();
	echo $dump;
	exit();
endif;

require_once('./templates/footer.php');
