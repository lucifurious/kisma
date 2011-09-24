<?php 
Yii::import( 'yiixl.core.components.config.CXLConfig' );

$this->pageTitle = XL::_gan();

/**
 * @var array
 */
$_testOptions = array(
	'option.0' => 'option.0.value',
	'option.1' => 'option.1.value',
	'option.2' => 'option.2.value',
	'option.3' => 'option.3.value',
	'option.4' => 'option.4.value',
	'option.5' => 'option.5.value',
	'option.6' => 'option.6.value',
	'option.7' => 'option.7.value',
	'option.8' => 'option.8.value',
	'option.9' => 'option.9.value',
	'option.array' => array(
		'suboption.0' => 'suboption.0.value',
		'suboption.1' => 'suboption.1.value',
		'suboption.2' => 'suboption.2.value',
		'suboption.3' => 'suboption.3.value',
		'suboption.4' => 'suboption.4.value',
		'suboption.5' => 'suboption.5.value',
		'suboption.6' => 'suboption.6.value',
		'suboption.7' => 'suboption.7.value',
		'suboption.8' => 'suboption.8.value',
		'suboption.9' => 'suboption.9.value',
	),
);


$_config = new CXLConfig( $_testOptions );

?>

<h1>Test Generator</h1>

<h3>Core (<em>yiixl.core</em>)</h3>
<p>
<ul>
	<li>
		CXLComponent
	</li>
	<li>
		CXLConfig
	</li>
</ul>
</p>
