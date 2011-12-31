<?php
if ( false === class_exists( 'Symfony\Component\ClassLoader\UniversalClassLoader', false ) )
{
	require_once __DIR__ . '/vendor/silex/autoload.php';
}

use Symfony\Component\ClassLoader\UniversalClassLoader;

$_loader = isset( $_loader ) ? $_loader : new UniversalClassLoader();

$_loader->registerNamespaces( array(
	'Kisma' => __DIR__ . '/src',
) );

$_loader->registerPrefixes( array(
	'PHPUnit' => '/usr/share/php',
) );

$_loader->register();
