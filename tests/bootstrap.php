<?php
use Kisma\Core\Utility\Log;
use Kisma\Kisma;

$_rootPath = dirname( __DIR__ );

Kisma::setAutoLoader( $_autoloader = require( $_rootPath . '/vendor/autoload.php' ) );
Log::setDefaultLog( $_rootPath . '/log/unit-tests.log' );