<?php 

if ( $_arComments = $this->getRecentComments() )
{
	echo YiiXL::openTag( 'ul' );

	foreach( $_arComments as $_oComment )
		echo YiiXL::tag( 'li', array(), $_oComment->authorLink . ' on ' . YiiXL::link( YiiXL::encode( $_oComment->post->title_text ), array( 'post/show', 'id' => $_oComment->post->id ) ) );
		
	echo YiiXL::closeTag( 'ul' );
}
