<?php
defined( 'YII_DEBUG' ) or define( 'YII_DEBUG', true );
defined( 'YII_TRACE_LEVEL' ) or define( 'YII_TRACE_LEVEL', 3 );

require_once( '/usr/local/yii/framework/yii.php' );

$_config = require_once( dirname( __FILE__ ) . '/protected/config/main.php' );

Yii::createWebApplication( $_config )->run();