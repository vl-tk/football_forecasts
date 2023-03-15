<?php

class Log
{
	public static function write($msg)
	{
		$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/logs/info.log', 'a');
		fwrite($fp, print_r( Date("d-M H:i:s")."\n",true));
        fwrite($fp, print_r( $msg ,true));
		fwrite($fp, print_r( "\n\n" ,true));
		fclose($fp);
	}
}
