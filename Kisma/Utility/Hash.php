<?php
/**
 * FilterInput.php
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright	 Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link		  http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license	   http://github.com/Pogostick/kisma/licensing/
 * @author		Jerry Ablan <kisma@pogostick.com>
 * @category	  Kisma_Utility
 * @package	   kisma.utility
 * @namespace	 \Kisma\Utility
 * @since		 v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma\Utility
 */
namespace Kisma\Utility
{
	/**
	 * Hash code/password helpers
	 */
	class Hash extends \Kisma\Components\SubComponent implements \Kisma\IUtility
	{
		//********************************************************************************
		//* Private Members
		//********************************************************************************

		/**
		 * @var array Our hash seeds
		 */
		protected static $_hashSeeds = array(
			\Kisma\HashSeed::All => array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
			\Kisma\HashSeed::AlphaLower => array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z' ),
			\Kisma\HashSeed::AlphaUpper => array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' ),
			\Kisma\HashSeed::Alpha => array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z' ),
			\Kisma\HashSeed::AlphaNumeric => array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
			\Kisma\HashSeed::AlphaLowerNumeric => array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
			\Kisma\HashSeed::Numeric => array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
			\Kisma\HashSeed::AlphaLowerNumericIdiotProof => array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '2', '3', '4', '5', '6', '7', '8', '9' ),
		);

		//********************************************************************************
		//* Public Methods
		//********************************************************************************

		/**
		 * Our default function
		 * @static
		 * @return mixed
		 */
		public static function __invoke()
		{
			return call_user_func_array( array( '\Kisma\Utility\Hash::hash' ), func_get_args() );
		}

		/**
		 * Generates a unique hash code
		 *
		 * @param int $hashLength
		 * @param int $hashSeed
		 * @return string
		 */
		public static function generate( $hashLength = 20, $hashSeed = \Kisma\HashSeed::All )
		{
			//	If we ain't got what you're looking for, return simple md5 hash...
			if ( !isset( self::$_hashSeeds, self::$_hashSeeds[$hashSeed] ) || !is_array( self::$_hashSeeds[$hashSeed] ) )
			{
				return md5( time() . mt_rand() . time() );
			}

			//	Randomly pick elements from the array of seeds
			for ( $_i = 0, $_hash = null, $_size = count( self::$_hashSeeds[$hashSeed] ) - 1; $_i < $hashLength; $_i++ )
			{
				$_hash .= self::$_hashSeeds[$hashSeed][mt_rand( 0, $_size )];
			}

			return $_hash;
		}

		/**
		 * Generic hashing method. Will hash any string or generate a random hash and hash that!
		 *
		 * @param string $hashTarget The value to hash..
		 * @param int $hashType [optional] The type of hash to create. Can be {@see Hash::MD5}, {@see Hash#SHA1},
		 * or {@link Hash#CRC32}. Defaults to {@see Hash::SHA1}.
		 * @param integer $hashLength [optional] The length of the hash to return. Only applies if <b>$hashType</b> is not MD5, SH1,
		 * or CRC32. . Defaults to 32.
		 * @param boolean $rawOutput [optional] If <b>$rawOutput</b> is true, then the hash digest is returned in raw binary format instead of
		 * ASCII.
		 * @return string
		 */
		public static function hash( $hashTarget = null, $hashType = \Kisma\HashType::SHA1, $hashLength = 32, $rawOutput = false )
		{
			$_value = ( null === $hashTarget ) ? self::generate( $hashLength ) : $hashTarget;

			switch ( $hashType )
			{
				case \Kisma\HashType::MD5:
					$_hash = md5( $_value, $rawOutput );
					break;

				case \Kisma\HashType::SHA1:
					$_hash = sha1( $_value, $rawOutput );
					break;

				case \Kisma\HashType::CRC32:
					$_hash = crc32( $_value );
					break;

				default:
					$_hash = hash( $hashType, $_value, $rawOutput );
					break;
			}

			return $_hash;
		}

	}
}