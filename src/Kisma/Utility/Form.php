<?php
/**
 * @file
 * Provides helper methods dealing with web forms
 *
 * Kisma(tm) : PHP Fun-Size Framework (http://github.com/lucifurious/kisma/)
 * Copyright 2009-2011, Jerry Ablan, All Rights Reserved
 *
 * @copyright Copyright (c) 2009-2011 Jerry Ablan
 * @license http://github.com/lucifurious/kisma/blob/master/LICENSE
 *
 * @author Jerry Ablan <kisma@pogostick.com>
 * @category Utility
 * @package kisma.utility
 * @since 1.0.0
 *
 * @ingroup utility
 */

namespace Kisma\Utility;

//*************************************************************************
//* Use-ages and Aliases
//*************************************************************************

use Kisma\K;

/**
 * Utility class to help with form construction
 *
 * @property int $uniqueIdCounter
 */
class Form implements \Kisma\IUtility
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var int A static ID counter for generating unique names
	 * @static
	 */
	protected static $_uniqueIdCounter = 1000;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Constructs a unique name based on component, hashes by default
	 *
	 * @param mixed
	 *		 The component for which to create a unique id
	 *
	 * @param boolean
	 *		If true, names returned will not be hashed
	 *
	 * @return string
	 */
	public static function createUniqueId( $component, $humanReadable = false )
	{
		$_tag = \Doctrine\Common\Util\Inflector::classify( get_class( $component ) . '.' . self::$_uniqueIdCounter++ );
		return 'kisma.' . ( $humanReadable ? $_tag : \Kisma\Utility\Hash::hash( $_tag ) );
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param int $uniqueIdCounter
	 */
	public static function setUniqueIdCounter( $uniqueIdCounter )
	{
		self::$_uniqueIdCounter = $uniqueIdCounter;
	}

	/**
	 * @return int
	 */
	public static function getUniqueIdCounter()
	{
		return self::$_uniqueIdCounter;
	}

}
