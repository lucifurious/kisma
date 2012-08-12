<?php
/**
 * autoload.php
 * Bootstrap loader for the Kisma
 *
 */

//	Some basics for all
if ( !class_exists( '\\Kisma', false ) )
{
	require_once __DIR__ . '/Kisma.php';
}

//	Initialize
\Kisma::conceive();
