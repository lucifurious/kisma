<?php
	$_arOpts = array();
	
	$_sTheme = Yii::app()->user->getState( CXLHelperBase
	$_iSelected = array_search( $_sTheme, CXLHelperBase
	
	echo YiiXL::beginForm( array( Yii::app()->defaultController . '/' . ThemeRoller::POST_ACTION ), 'POST', array( 'id' => 'frmThemeRoller' ) );
		echo YiiXL::hiddenField( 'uri', $this->getOwner()->getRequest()->getRequestUri() );
		echo YiiXL::dropDown( YiiXL::DD_JQUI_THEMES, 'theme', null, array( 'value' => $_iSelected, 'id' => '_themeRoller', 'style' => 'width:100%' ) );
	echo YiiXL::endForm();
?>
<script type="text/javascript">
<!--
	jQuery(function(){
		jQuery('#_themeRoller').change(function(e){
			return jQuery('#frmThemeRoller').submit();
		});
	});
//-->
</script>	
	
