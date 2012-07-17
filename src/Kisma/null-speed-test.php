<?php
error_reporting( E_ALL || ~E_NOTICE );
require_once '../Kisma.php';
//checking with ===
$a = array();
$time = microtime();
for ( $i = 0; $i < 10000; $i++ )
{
	$_k = new Kisma;
	unset($_k);
}
echo 'Testing with === ', microtime() - $time, "\n";
//
////checking with is_null()
//$time = microtime();
//for ( $i = 0; $i < 10000; $i++ )
//{
//	if ( is_null( $a[$i] ) )
//	{
//		//do nothing
//	}
//}
//echo 'Testing with is_null() ', microtime() - $time . PHP_EOL;