<ul>
	<li><?php echo YiiXL::link( 'Approve Comments', array( 'comment/list' ) ) . ' (' . Comment::model()->pendingCommentCount . ')'; ?></li>
	<li><?php echo YiiXL::link( 'Create New Post', array( 'post/create' ) ); ?></li>
	<li><?php echo YiiXL::link( 'Manage Posts', array( 'post/admin' ) ); ?></li>
	<li><?php echo YiiXL::linkButton( 'Logout', array( 'submit' => '', 'params' => array( 'command' => 'logout' ) ) ); ?></li>
</ul>