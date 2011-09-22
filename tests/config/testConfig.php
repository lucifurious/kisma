<?php

Yii::setPathOfAlias( 'phpunit', '/usr/share/php/PHPUnit' );
Yii::import( 'phpunit.*' );

return CMap::mergeArray(
	require __DIR__ . '/mainConfig.php',
	array(
		'components' => array(
			'fixture' => array(
				'class' => 'system.test.CDbFixtureManager',
			),
			/* uncomment the following to provide test database connection
			'db'=>array(
				'connectionString'=>'DSN for test database',
			),
			*/
		),
	)
);
