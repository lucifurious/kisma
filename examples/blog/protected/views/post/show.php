<?php

$this->renderPartial( '_post', array( 'post' => $post, 'show' => true ) );

echo YiiXL::openTag( 'div', array( 'id' => 'comments' ) );

if ( $post->comment_count_nbr >= 1 )
    YiiXL::tag( 'h3', array(), $post->comment_count_nbr . ' comment' . ( $post->comment_count_nbr > 1 ? 's' : '' ) . ' to ' . YiiXL::encode( $post->title_text ) );

$this->renderPartial( '/comment/_list', array( 'comments' => $comments, 'post' => $post ) );

$this->renderPartial( '/comment/_form', array( 'comment' => $newComment, 'update' => false ) );

echo YiiXL::closeTag( 'div' ) . '<!-- comments -->';
