<div class="post-item">
	<div class="post-title">
		<h1><?php 
				$_sTitle = $post->title_text;
				echo ( isset( $show ) && $show ) ? $_sTitle : YiiXL::link( YiiXL::encode( $_sTitle ), array( 'post/show', 'id' => $post->id ) );
			?>
		</h1>
	</div>

	<div class="post-author">
		<?php
			if ( ! Yii::app()->user->isGuest )
				echo YiiXL::tag( 'span', array( 'class' => strtolower( $post->statusText ) ), '[' . $post->statusText . ']' );
				
			echo ' posted by ' . $post->author->user_name_text . ' on ' . $post->create_date;
		?>
	</div>
	
	<div class="post-content">
		<?php echo $post->content_display_text; ?>
	</div>

	<div class="post-nav">
		<?php
			if ( $_sLinks = $this->getTagLinks( $post ) )
			{
				echo YiiXL::beginFieldSet( 'Tags' );
					echo '<div class="post-nav-tag-links">' . $_sLinks . '</div>';
				echo YiiXL::endFieldset();
			}
			
			echo '<div class="post-nav-links">';
				echo YiiXL::link( 'Read more', array( 'post/show', 'id' => $post->id ) ) . YiiXL::pipe();
				echo YiiXL::link( "Comments ({$post->comment_count_nbr})", array( 'post/show', 'id' => $post->id, '#' => 'comments' ) ) . YiiXL::pipe();
			
				if ( ! Yii::app()->user->isGuest )
				{
					echo YiiXL::link( 'Update', array( 'post/update', 'id' => $post->id ) ) . YiiXL::pipe();
					echo YiiXL::linkButton( 'Delete', 
						array(
							'submit' => array( 'post/delete', 'id' => $post->id ),
							'confirm' => 'Are you sure to delete this post?',
						)
					) . YiiXL::pipe();
				}
				
				echo 'Last updated on ' . $post->lmod_date;
			echo '</div>';
		?>
	</div>
	
</div><!-- post -->
