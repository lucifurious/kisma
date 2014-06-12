<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2014 Jerry Ablan <jerryablan@gmail.com>
 *
 * Kisma(tm) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Kisma(tm) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kisma(tm).  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Kisma\Core\Utility;

use Kisma\Core\Interfaces\HashSeed;
use Kisma\Core\Interfaces\HashType;
use Kisma\Core\Interfaces\UtilityLike;
use Kisma\Core\Seed;

/**
 * Hasher
 * Hashing utility class
 */
class Hasher extends Seed implements UtilityLike, HashSeed, HashType
{
	//*************************************************************************
	//	Constants
	//*************************************************************************

	/**
	 * @var int The maximum default length for unique hash generations
	 */
	const MAX_HASH_LENGTH = 128;

	//********************************************************************************
	//* Private Members
	//********************************************************************************

	/**
	 * @var array The various hash seeds used by this class
	 */
	protected static $_hashSeeds
		= array(
			self::All                         => array(
				'a',
				'b',
				'c',
				'd',
				'e',
				'f',
				'g',
				'h',
				'i',
				'j',
				'k',
				'l',
				'm',
				'n',
				'o',
				'p',
				'q',
				'r',
				's',
				't',
				'u',
				'v',
				'w',
				'x',
				'y',
				'z',
				'A',
				'B',
				'C',
				'D',
				'E',
				'F',
				'G',
				'H',
				'I',
				'J',
				'K',
				'L',
				'M',
				'N',
				'O',
				'P',
				'Q',
				'R',
				'S',
				'T',
				'U',
				'V',
				'W',
				'X',
				'Y',
				'Z',
				'0',
				'1',
				'2',
				'3',
				'4',
				'5',
				'6',
				'7',
				'8',
				'9'
			),
			self::AlphaLower                  => array(
				'a',
				'b',
				'c',
				'd',
				'e',
				'f',
				'g',
				'h',
				'i',
				'j',
				'k',
				'l',
				'm',
				'n',
				'o',
				'p',
				'q',
				'r',
				's',
				't',
				'u',
				'v',
				'w',
				'x',
				'y',
				'z'
			),
			self::AlphaUpper                  => array(
				'A',
				'B',
				'C',
				'D',
				'E',
				'F',
				'G',
				'H',
				'I',
				'J',
				'K',
				'L',
				'M',
				'N',
				'O',
				'P',
				'Q',
				'R',
				'S',
				'T',
				'U',
				'V',
				'W',
				'X',
				'Y',
				'Z'
			),
			self::Alpha                       => array(
				'A',
				'B',
				'C',
				'D',
				'E',
				'F',
				'G',
				'H',
				'I',
				'J',
				'K',
				'L',
				'M',
				'N',
				'O',
				'P',
				'Q',
				'R',
				'S',
				'T',
				'U',
				'V',
				'W',
				'X',
				'Y',
				'Z',
				'a',
				'b',
				'c',
				'd',
				'e',
				'f',
				'g',
				'h',
				'i',
				'j',
				'k',
				'l',
				'm',
				'n',
				'o',
				'p',
				'q',
				'r',
				's',
				't',
				'u',
				'v',
				'w',
				'x',
				'y',
				'z'
			),
			self::AlphaNumeric                => array(
				'A',
				'B',
				'C',
				'D',
				'E',
				'F',
				'G',
				'H',
				'I',
				'J',
				'K',
				'L',
				'M',
				'N',
				'O',
				'P',
				'Q',
				'R',
				'S',
				'T',
				'U',
				'V',
				'W',
				'X',
				'Y',
				'Z',
				'a',
				'b',
				'c',
				'd',
				'e',
				'f',
				'g',
				'h',
				'i',
				'j',
				'k',
				'l',
				'm',
				'n',
				'o',
				'p',
				'q',
				'r',
				's',
				't',
				'u',
				'v',
				'w',
				'x',
				'y',
				'z',
				'0',
				'1',
				'2',
				'3',
				'4',
				'5',
				'6',
				'7',
				'8',
				'9'
			),
			self::AlphaLowerNumeric           => array(
				'a',
				'b',
				'c',
				'd',
				'e',
				'f',
				'g',
				'h',
				'i',
				'j',
				'k',
				'l',
				'm',
				'n',
				'o',
				'p',
				'q',
				'r',
				's',
				't',
				'u',
				'v',
				'w',
				'x',
				'y',
				'z',
				'0',
				'1',
				'2',
				'3',
				'4',
				'5',
				'6',
				'7',
				'8',
				'9'
			),
			self::Numeric                     => array(
				'0',
				'1',
				'2',
				'3',
				'4',
				'5',
				'6',
				'7',
				'8',
				'9'
			),
			self::AlphaLowerNumericIdiotProof => array(
				'a',
				'b',
				'c',
				'd',
				'e',
				'f',
				'g',
				'h',
				'j',
				'k',
				'm',
				'n',
				'p',
				'q',
				'r',
				's',
				't',
				'u',
				'v',
				'w',
				'x',
				'y',
				'z',
				'2',
				'3',
				'4',
				'5',
				'6',
				'7',
				'8',
				'9'
			),
		);

	//********************************************************************************
	//* Methods
	//********************************************************************************

	/**
	 * Our default function
	 *
	 * @static
	 * @return mixed
	 */
	public function __invoke()
	{
		return call_user_func_array( array( get_called_class() . '::hash' ), func_get_args() );
	}

	/**
	 * Generates a unique hash code
	 *
	 * @param int $hashLength
	 * @param int $hashSeed
	 *
	 * @return string
	 */
	public static function generate( $hashLength = self::MAX_HASH_LENGTH, $hashSeed = self::All )
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
	 * Generates a pseudo-random/mostly-unique hash code. If you need guaranteed uniqueness, write yourself a GUID method
	 *
	 * @param string $seed
	 * @param int    $length
	 * @param string $algorithm
	 *
	 * @return string
	 */
	public static function generateUnique( $seed = null, $length = self::MAX_HASH_LENGTH, $algorithm = 'sha512' )
	{
		$_entropy = @( microtime( true ) * mt_rand() ) ? : microtime( true ) . mt_rand();

		//	If a seed is passed, use that as our base....
		if ( !empty( $seed ) )
		{
			$_entropy = $seed;

			if ( file_exists( '/dev/urandom' ) )
			{
				$_fp = @fopen( '/dev/urandom', 'rb' );
				$_entropy = fread( $_fp, $length * 10 );
				@fclose( $_fp );
			}
		}

		$_seed = hash( $algorithm, $_entropy . microtime( true ) );
		$_hash = substr( hash( $algorithm, $_seed ), 0, $length );

		return $_hash;

	}

	/**
	 * Generic hashing method. Will hash any string or generate a random hash and hash that!
	 *
	 * @param string  $hashTarget    The value to hash..
	 * @param int     $hashType      [optional] The type of hash to create. Can be {@see Hasher::MD5}, {@see Hash#SHA1},
	 *                               or {@link Hash#CRC32}. Defaults to {@see Hasher::SHA1}.
	 * @param integer $hashLength    [optional] The length of the hash to return. Only applies if <b>$hashType</b>
	 *                               is not MD5, SH1,
	 *                               or CRC32. . Defaults to static::MAX_HASH_LENGTH.
	 * @param boolean $rawOutput     [optional] If <b>$rawOutput</b> is true, then the hash digest is returned in
	 *                               raw binary format instead of
	 *                               ASCII.
	 *
	 * @return string
	 */
	public static function hash( $hashTarget = null, $hashType = self::SHA1, $hashLength = self::MAX_HASH_LENGTH, $rawOutput = false )
	{
		$_value = ( null === $hashTarget ) ? self::generate( $hashLength ) : $hashTarget;

		switch ( $hashType )
		{
			case self::MD5:
				$_hash = md5( $_value, $rawOutput );
				break;

			case self::SHA1:
				$_hash = sha1( $_value, $rawOutput );
				break;

			case self::CRC32:
				$_hash = crc32( $_value );
				break;

			default:
				$_hash = hash( $hashType, $_value, $rawOutput );
				break;
		}

		return $_hash;
	}

	/**
	 * Simple string encryption using $salt
	 *
	 * @param string  $text
	 * @param string  $salt
	 * @param boolean $urlEncode If true, the string will be url-encoded after encryption
	 *
	 * @return string
	 */
	public static function encryptString( $text, $salt, $urlEncode = false )
	{
		$_value = trim( \base64_encode( \mcrypt_encrypt( MCRYPT_RIJNDAEL_256,
			sha1( $salt, true ),
			$text,
			MCRYPT_MODE_ECB,
			\mcrypt_create_iv( \mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ),
				MCRYPT_RAND ) ) ) );

		return $urlEncode ? urlencode( $_value ) : $_value;
	}

	/**     * Simple string decryption using the $salt as a key
	 *
	 * @param string  $text
	 * @param string  $salt
	 * @param boolean $urlDecode If true, the string will be url-decoded before decryption
	 *
	 * @return string
	 */
	public static function decryptString( $text, $salt, $urlDecode = false )
	{
		return trim( \mcrypt_decrypt( MCRYPT_RIJNDAEL_256,
			sha1( $salt, true ),
			\base64_decode( $urlDecode ? urldecode( $text ) : $text ),
			MCRYPT_MODE_ECB,
			\mcrypt_create_iv( \mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND ) ) );
	}
}
