<?php
/**
 * Transform.php
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
 * @category	  Kisma_Utilities
 * @package	   kisma.utilities
 * @since		 v1.0.0
 * @filesource
 */
namespace Kisma\Utility
{
	//*************************************************************************
	//* Imports
	//*************************************************************************

	use Kisma\Components as Components;
	use Kisma\Services as Services;
	use Kisma\Utility as Utility;

	//*************************************************************************
	//* Classes
	//*************************************************************************

	/**
	 * Transform
	 */
	class Transform extends \Kisma\Components\SubComponent implements \Kisma\IUtility
	{
		//*************************************************************************
		//* Class Constants
		//*************************************************************************

		//*************************************************************************
		//* Private Members
		//*************************************************************************

		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		//*************************************************************************
		//* Private Methods
		//*************************************************************************

		//*************************************************************************
		//* Properties
		//*************************************************************************

		/**
		 * @param string|\DOMDocument|\SimpleXMLElement $xmlText
		 * @param string $version
		 * @param string $encoding
		 * @param int|null $options Options to pass on to DOMDocument::loadXML()
		 * @return array|bool
		 */
		public static function xmlToArray( $xmlText, $version = '1.0', $encoding = 'utf-8', $options = null )
		{
			$_xml = $xmlText;

			if ( $_xml instanceof \DOMDocument )
			{
				Log::debug( 'Received DOMDocument' );
				$_xml = $xmlText->asXML();
			}
			else if ( $_xml instanceof \SimpleXMLElement )
			{
				Log::debug( 'Received SimpleXMLElement' );
				$_xml = $xmlText->asXML();
			}

			if ( !is_string( $xmlText ) )
			{
				//	No clue what you've given me
				return false;
			}

			//	Start the chain reaction
			$_xmlObject = self::createArray( $_xml );

			Log::debug( 'Transformed XML to Array: ' . print_r( $_xmlObject, true ) );

			return $_xmlObject;
		}

		/**
		 * @static
		 * @param \DOMNode $xmlNode
		 * @return array|bool
		 */
		protected static function _xmlNodeToArray( $xmlNode )
		{
			if ( XML_ELEMENT_NODE != $xmlNode->nodeType )
			{
				return false;
			}

			/** @var $_childNode \DOMNode */
			foreach ( $xmlNode->childNodes as $_childNode )
			{
				if ( XML_ELEMENT_NODE != $xmlNode->nodeType )
				{
					continue;
				}

				$_prefix = ( $_childNode->prefix ? $_childNode->prefix . ':' : null );
				$_nodeName = $_prefix . $_childNode->nodeName;
				$_subNode = false;

				if ( false === ( isset( $_array[$_nodeName] ) && is_array( $_array[$_nodeName] ) ) )
				{
					/** @var $_childNodeCheck \DOMNode */
					foreach ( $xmlNode->childNodes as $_childNodeCheck )
					{
						if ( $_childNode->nodeName == $_childNodeCheck->nodeName && !$_childNode->isSameNode( $_childNodeCheck ) )
						{
							$_subNode = true;
							break;
						}
					}
				}
				else
				{
					$_subNode = true;
				}

				if ( $_subNode )
				{
					$_array[$_nodeName][] = self::_xmlNodeToArray( $_childNode );
				}
				else
				{
					$_array[$_nodeName] = self::_xmlNodeToArray( $_childNode );
				}
			}

			if ( !is_array( $_array ) )
			{
				$_array['#text'] = html_entity_decode(
					htmlentities(
						$xmlNode->nodeValue,
						ENT_COMPAT,
						'UTF-8'
					),
					ENT_COMPAT,
					'ISO-8859-15'
				);
			}

			if ( $xmlNode->hasAttributes() )
			{
				foreach ( $xmlNode->attributes as $_attribute )
				{
					$_prefix = ( $_attribute->prefix ? $_attribute->prefix . ':' : null );
					$_array['@attributes'][$_prefix . $_attribute->nodeName] = $_attribute->nodeValue;
				}
			}

			return $_array;
		}

		public static function createArray( $xmlText )
		{
			$awsXml = $xmlText;
			$awsVals = array();
			$awsIndex = array();
			$awsRetArray = array();
			$awsParser = xml_parser_create();
			xml_parser_set_option( $awsParser, XML_OPTION_SKIP_WHITE, 1 );
			xml_parser_set_option( $awsParser, XML_OPTION_CASE_FOLDING, 0 );
			xml_parse_into_struct( $awsParser, $awsXml, $awsVals, $awsIndex );
			xml_parser_free( $awsParser );
			$i = 0;
			$awsName = $awsVals[$i]['tag'];
			$awsRetArray[$awsName] = isset( $awsVals[$i]['attributes'] ) ? $awsVals[$i]['attributes'] : '';
			$awsRetArray[$awsName] = self::_struct_to_array( $awsVals, $i );
			return $awsRetArray;
		}

		protected static function _struct_to_array( $awsVals, &$i )
		{
			$awsChild = array();
			if ( isset( $awsVals[$i]['value'] ) )
				array_push( $awsChild, $awsVals[$i]['value'] );

			while ( $i++ < count( $awsVals ) )
			{
				switch ( $awsVals[$i]['type'] )
				{
					default:
						array_push( $awsChild, $awsVals[$i]['value'] );
						break;

					case 'cdata':
						array_push( $awsChild, $awsVals[$i]['value'] );
						break;

					case 'complete':
						$awsName = $awsVals[$i]['tag'];
						if ( !empty( $awsName ) )
						{
							$awsChild[$awsName] = ( $awsVals[$i]['value'] ) ? ( $awsVals[$i]['value'] ) : '';
							if ( isset( $awsVals[$i]['attributes'] ) )
							{
								$awsChild[$awsName] = $awsVals[$i]['attributes'];
							}
						}
						break;

					case 'open':
						$awsName = $awsVals[$i]['tag'];
						$size = isset( $awsChild[$awsName] ) ? sizeof( $awsChild[$awsName] ) : 0;
						if ( intval( $size ) > 0 )
						{
							$awsChild[$awsName][$size] = self::_struct_to_array( $awsVals, $i );
						}
						else
						{
							$awsChild[$awsName] = self::_struct_to_array( $awsVals, $i );
						}
						break;

					case 'close':
						return $awsChild;
						break;
				}
			}
			return $awsChild;
		}
	}
}