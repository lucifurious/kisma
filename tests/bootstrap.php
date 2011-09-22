<?php
$_yii = '/opt/yii/framework/yiit.php';
$_config = __DIR__ . '/config/testConfig.php';

require_once( $_yii );
require_once( 'WebTestCase.php' );

Yii::createWebApplication( $_config );
