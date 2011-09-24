<?php
	if ( ! isset( $postDate ) ) $postDate = YiiXL::o( $_REQUEST, 'date', date('Y-m-d') );
	$postDate = date( 'd-m-Y', strtotime( $postDate ) );

	echo YiiXL::beginForm( array( 'postsByDate' ), 'POST', array( 'id' => 'frmPostCalendar' ) );
	
		echo YiiXL::hiddenField( 'dateValue', null, array( 'id' => 'dateValue' ) );
		echo YiiXL::tag( 'div', array( 'id' => 'postCalendarDatepicker', 'style' => 'font-size:.8em !important' ) );
		CXLHelperBase
		
	echo YiiXL::endForm();
