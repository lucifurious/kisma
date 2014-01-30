<?php
use Kisma\Core\Utility\Log;

$_basePath = dirname( __DIR__ );

require_once $_basePath . '/vendor/autoload.php';

Log::setDefaultLog( __DIR__ . '/log/kisma.tests.log' );