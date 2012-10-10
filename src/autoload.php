<?php
/**
 * autoload.php
 * Bootstrap loader for Kisma
 */
error_reporting( -1 );
$_kismaOptions = array();

//	Some basics for all
if ( !class_exists( '\\Kisma' ) )
{
	$_kismaOptions['app.auto_loader'] = require_once( dirname( __DIR__ ) . '/vendor/autoload.php' );
}

//	Initialize
\Kisma::conceive( $_kismaOptions );
