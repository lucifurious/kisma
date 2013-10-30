<?php
use Kisma\Core\Utility\Log;

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

Log::setDefaultLog( __DIR__ . '/log/test.log' );