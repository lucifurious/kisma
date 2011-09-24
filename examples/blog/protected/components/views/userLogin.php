<?php
	$_arOpts = array();

	echo YiiXL::beginForm( '', 'POST', array( 'validate' => true, 'id' => 'frmLogin' ) );

		echo YiiXL::errorSummary( $form );
		
		echo YiiXL::field( YiiXL::TEXT, $form, 'username' );
		echo YiiXL::field( YiiXL::PASSWORD, $form, 'password' );
		echo YiiXL::field( YiiXL::CHECK, $form, 'rememberMe' );
		
		echo YiiXL::submitButtonBar( 'Login', array( 'formId' => 'frmLogin', 'noBorder' => true, 'barCenter' => true, 'style' => 'margin-top:10px;', 'jqui' => true, 'icon' => 'person' ) );
		
	echo YiiXL::endForm();
