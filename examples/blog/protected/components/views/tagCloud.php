<?php

foreach ( Tag::model()->findTagWeights() as $_sTag => $_sWeight ) 
{
	echo YiiXL::tag( 'span', array( 'class' => 'tag', 'style' => 'font-size:' . $_sWeight . 'pt !important;' ), 
		YiiXL::link( YiiXL::encode( $_sTag ), array( 'post/list', 'tag' => $_sTag ) )
	);
}