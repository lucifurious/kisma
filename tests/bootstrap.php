<?php
use Kisma\Core\Utility\Log;
use Kisma\Kisma;

$_rootPath = dirname( __DIR__ );

$_autoloader = require( $_rootPath . '/vendor/autoload.php' );

Kisma::setAutoLoader( $_autoloader );
Log::setDefaultLog( $_rootPath . '/log/unit-tests.log' );