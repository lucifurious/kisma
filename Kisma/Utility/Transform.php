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
			if ( !( $xmlText instanceof \DOMDocument ) )
			{
				$_xml = $xmlText;
			}
			else
			{
				if ( $xmlText instanceof \SimpleXMLElement )
				{
					$_xml = $xmlText->asXML();
				}

				if ( !is_string( $xmlText ) )
				{
					//	No clue what you've given me
					return false;
				}

				//	Make a \DOMDocument
				libxml_use_internal_errors( true );
				$_xml = new \DOMDocument( $version, $encoding );
				$_xml->loadXML( $xmlText, $options );

			}

			//	Start the chain reaction
			return array(
				$_xml->firstChild->tagName => self::_xmlNodeToArray( $_xml->firstChild ),
			);
		}

		/**
		 * @static
		 * @param \DOMNode $xmlNode
		 * @return array|bool
		 */
		protected static function _xmlNodeToArray( $xmlNode )
		{
			$_array = array();

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

				if ( false === ( $_subNode = is_array( $_array[$_prefix . $_childNode->nodeName] ) ) )
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

				if ( $_subNode )
				{
					$_array[$_prefix . $_childNode->nodeName][] = self::_xmlNodeToArray( $_childNode );
				}
				else
				{
					$_array[$_prefix . $_childNode->nodeName] = self::_xmlNodeToArray( $_childNode );
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
					$_array['@' . $_prefix . $_attribute->nodeName] = $_attribute->nodeValue;
				}
			}

			return $_array;
		}
	}
}