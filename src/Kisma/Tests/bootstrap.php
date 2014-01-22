<?php
use Kisma\Core\Utility\Log;

$_basePath = dirname( dirname( dirname( __DIR__ ) ) );

require_once $_basePath . '/vendor/autoload.php';

Log::setDefaultLog( $_basePath . '/log/kisma.test.log' );