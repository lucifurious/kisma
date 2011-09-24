<?php
/*
 * This file is part of yiixl examples
 * 
 * @copyright Copyright &copy; 2009 Pogostick, LLC
 * @link http://www.pogostick.com Pogostick, LLC.
 * @license http://www.pogostick.com/licensing
 */
/**
 * @package 	yiixl.examples
 * @subpackage 	tests.config
 * 
 * @author 		Jerry Ablan <jablan@pogostick.com>
 * @version 	SVN $Id$
 * @since 		v1.1.0
 * 
 * @filesource
 */

//	Our application parameters
return array(

	//	This is displayed in the header section
	'title' => 'YiiXL Tester',
	
	//	This is used in the error pages
	'adminEmail' => 'yii@pogostick.com',
	
	//	The copyright information displayed in the footer section
	'copyrightInfo' => 'Copyright &copy; ' . date('Y') . ' by Pogostick, LLC.',

	//	Our default theme
	'theme' => 'ui-lightness',

	//	Defaults for phpDoc scaffolding. Edit accordingly
	'@copyright' => 'Copyright &copy; ' . date('Y') . ' Pogostick, LLC.',
	'@author' => 'Pogostick Yii <yii@pogostick.com>',
	'@link' => 'http://www.pogostick.com/yii',
	'@package' => 'yiixl.tests',
);