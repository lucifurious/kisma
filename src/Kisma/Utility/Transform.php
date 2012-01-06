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
	class Transform extends \Kisma\Components\Seed implements \Kisma\IUtility
	{
		/**
		 * Dynamically generates the object from the properties of the given object
		 *
		 * @param $object
		 *
		 * @return \stdClass
		 */
		public static function toObject( $object )
		{
			if ( null === $object )
			{
				return null;
			}

			$_obj = new \stdClass();

			if ( is_array( $object ) )
			{
				$_properties = array();

				foreach ( $object as $_key => $_value )
				{
					$_property = new \stdClass();
					$_property->name = $_key;
				}
			}
			else
			{
				$_me = new \ReflectionObject( $object );
				$_properties = $_me->getProperties();
			}

			if ( !empty( $_properties ) )
			{
				if ( !is_array( $object ) )
				{
					$_myClass = get_class( $object );
				}
				else
				{
					$_myClass = '_array_';
				}

				foreach ( $_properties as $_property )
				{
					//	Only want properties of $object hierarchy...
					if ( isset( $_property->class ) )
					{
						$_class = new \ReflectionClass( $_property->class );

						if ( !empty( $_class ) && !$_class->isInstance( $object ) && !$_class->isSubclassOf( $_myClass ) )
						{
							continue;
						}

						unset( $_class );
					}

					try
					{
						$_propertyName = ltrim( $_property->name, '_' );

						if ( 'parentId' != $_propertyName )
						{
							$_getter = 'get' . $_propertyName;

							if ( method_exists( $object, $_getter ) )
							{
								$_propertyValue = $object->{$_getter}();

								if ( !is_scalar( $_propertyValue ) )
								{
									$_propertyValue = self::toObject( $_propertyValue );
								}

								$_obj->{$_propertyName} = $_propertyValue;
							}
						}
					}
					catch ( \Exception $_ex )
					{
						//	Just ignore, not a valid property if we can't read it with a getter
					}
				}
			}

			return $_obj;
		}

		/**
		 * @param string|\DOMDocument|\SimpleXMLElement $xmlText
		 * @param string                                $version
		 * @param string                                $encoding
		 * @param int|null                              $options Options to pass on to DOMDocument::loadXML()
		 *
		 * @return array|bool
		 */
		public static function xmlToArray2( $xmlText, $version = '1.0', $encoding = 'utf-8', $options = null )
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
			$_result = array();
			$_xml = new \SimpleXMLElement( $_xml );

			$_entries = $_xml->children( 'http://www.w3.org/2005/Atom' );

			/** @var $_entry \SimpleXMLElement */
			foreach ( $_entries->entry->children as $_entry )
			{
				$_values = $_entry->children( 'http://schemas.google.com/g/2005' );

				$_resultName = ( $_entry->prefix ? $_entry->prefix . ':' : null ) . $_entry->nodeName;

				$_result[$_resultName] = self::_xmlNodeToArray( $_entry );
			}

			Log::debug( 'Transformed XML to Array: ' . print_r( $_result, true ) );

			return $_xml;
		}

		/**
		 * @static
		 *
		 * @param \SimpleXMLElement $xmlNode
		 *
		 * @return array|bool
		 */
		protected static function _xmlNodeToArray( $xmlNode )
		{
			$_result = false;

			if ( $xmlNode->attributes() )
			{
				$_result['@attributes'] = array();

				foreach ( $xmlNode->attributes() as $_attribute )
				{
					$_result['@attributes'][$_attribute->nodeName] = $_attribute->nodeValue;
				}
			}

			if ( null !== ( $_children = $xmlNode->children( 'http://www.w3.org/2005/Atom' ) ) )
			{
				if ( 1 == $_children->length )
				{
					$_result[$xmlNode->firstChild->nodeName] = $xmlNode->firstChild->nodeValue;
				}
				else
				{
					foreach ( $_children as $_child )
					{
						if ( $_child->nodeType != XML_TEXT_NODE )
						{
							$_result[$_child->nodeName][] = self::_xmlNodeToArray( $_child );
						}
					}
				}
			}

			return $_result;
		}

		/**
		 * @static
		 *
		 * @param string $xmlString
		 * @param string $encoding
		 * @param bool   $includeAttributes
		 *
		 * @return array
		 */
		public static function xmlToArray3( $xmlString, $encoding = 'utf-8', $includeAttributes = true )
		{
			$_parser = xml_parser_create( $encoding );
			xml_parser_set_option( $_parser, XML_OPTION_CASE_FOLDING, 0 );
			xml_parser_set_option( $_parser, XML_OPTION_SKIP_WHITE, 1 );
			xml_parse_into_struct( $_parser, $xmlString, $_xml, $_index );
			xml_parser_free( $_parser );

			$_levels = array( null );

			foreach ( $_xml as $_value )
			{
				if ( Scalar::in( $_value['type'], 'open', 'complete' ) )
				{
					if ( !array_key_exists( $_value['level'], $_levels ) )
					{
						$_levels[$_value['level']] = array();
					}
				}

				$_priorLevel = &$_levels[$_value['level'] - 1];
				$_parent = $_priorLevel[sizeof( $_priorLevel ) - 1];

				if ( 'open' == $_value['type'] )
				{
					$_value['children'] = array();
					array_push( &$_levels[$_value['level']], $_value );
					continue;
				}
				else if ( 'complete' == $_value['type'] )
				{
					$_parent['children'][$_value['tag']] = $_value['value'];
				}
				else if ( 'close' == $_value['type'] )
				{
					$_popped = array_pop( $_levels[$_value['level']] );
					$_tag = $_popped['tag'];

					if ( $_parent )
					{
						if ( !array_key_exists( $_tag, $_parent['children'] ) )
						{
							$_parent['children'][$_tag] = $_popped['children'];
						}
						else if ( is_array( $_parent['children'][$_tag] ) )
						{
							if ( !isset( $_parent['children'][$_tag][0] ) )
							{
								$_oldSingle = $_parent['children'][$_tag];
								$_parent['children'][$_tag] = null;
								$_parent['children'][$_tag][] = $_oldSingle;
							}

							$parent['children'][$_tag][] = $_popped['children'];
						}
					}
					else
					{
						return ( array( $_popped['tag'] => $_popped['children'] ) );
					}
				}

				$_priorLevel[sizeof( $_priorLevel ) - 1] = $_parent;
			}
		}

		/**
		 * @static
		 *
		 * @param string $xml
		 * @param string $encoding
		 * @param bool   $includeAttributes
		 *
		 * @return array
		 * @todo Make this faster
		 */
		public static function xmlToArray( $xml, $encoding = 'utf-8', $includeAttributes = true )
		{
			if ( !function_exists( 'xml_parser_create' ) )
			{
				return array();
			}

			$contents = $xml;

			$parser = xml_parser_create( $encoding );

			xml_parser_set_option( $parser, XML_OPTION_TARGET_ENCODING, $encoding );
			xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
			xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
			xml_parse_into_struct( $parser, trim( $contents ), $xml_values );
			xml_parser_free( $parser );

			if ( !$xml_values )
			{
				return;
			}

			$repeated_tag_index = $_outputArray = $parent = array();
			$current =& $_outputArray;

			foreach ( $xml_values as $data )
			{
				/**
				 * @var string $tag
				 * @var string $type
				 * @var int    $level
				 * @var array  $attributes
				 */
				unset ( $attributes, $value );

				extract( $data );

				$result = array();
				$attributes_data = array();

				if ( isset ( $value ) )
				{
					$result = $value;
				}

				if ( isset ( $attributes ) and $includeAttributes )
				{
					foreach ( $attributes as $attr => $val )
					{
						$attributes_data[$attr] = $val;
					}
				}

				if ( $type == "open" )
				{
					$parent[$level - 1] = & $current;
					if ( !is_array( $current ) or ( !in_array( $tag, array_keys( $current ) ) ) )
					{
						$current[$tag] = $result;
						if ( $attributes_data )
						{
							$current[$tag . '_attr'] = $attributes_data;
						}
						$repeated_tag_index[$tag . '_' . $level] = 1;
						$current = & $current[$tag];
					}
					else
					{
						if ( isset ( $current[$tag][0] ) )
						{
							$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
							$repeated_tag_index[$tag . '_' . $level]++;
						}
						else
						{
							$current[$tag] = array(
								$current[$tag], $result
							);
							$repeated_tag_index[$tag . '_' . $level] = 2;
							if ( isset ( $current[$tag . '_attr'] ) )
							{
								$current[$tag]['0_attr'] = $current[$tag . '_attr'];
								unset ( $current[$tag . '_attr'] );
							}
						}
						$last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
						$current = & $current[$tag][$last_item_index];
					}
				}
				elseif ( $type == "complete" )
				{
					if ( !isset ( $current[$tag] ) )
					{
						$current[$tag] = $result;
						$repeated_tag_index[$tag . '_' . $level] = 1;

						if ( $attributes_data )
						{
							$current[$tag . '_attr'] = $attributes_data;
						}
					}
					else
					{
						if ( isset ( $current[$tag][0] ) and is_array( $current[$tag] ) )
						{
							$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

							if ( $includeAttributes and $attributes_data )
							{
								$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
							}

							$repeated_tag_index[$tag . '_' . $level]++;
						}
						else
						{
							$current[$tag] = array(
								$current[$tag], $result
							);
							$repeated_tag_index[$tag . '_' . $level] = 1;

							if ( $includeAttributes )
							{
								if ( isset ( $current[$tag . '_attr'] ) )
								{
									$current[$tag]['0_attr'] = $current[$tag . '_attr'];
									unset ( $current[$tag . '_attr'] );
								}

								if ( $attributes_data )
								{
									$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] =
										$attributes_data;
								}
							}

							$repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
						}
					}
				}
				elseif ( $type == 'close' )
				{
					$current = & $parent[$level - 1];
				}
			}

			//	Some kind of error, should have 'feed' root
			if ( !isset( $_outputArray['feed'] ) )
			{
				return false;
			}

			if ( $includeAttributes )
			{
				//				self::_normalizeAttributes( $_outputArray );
			}

			return $_outputArray['feed'];
		}

		/**
		 * Down and dirty object to array function
		 *
		 * @static
		 *
		 * @param object $object
		 *
		 * @return array
		 */
		public static function objectToArray( $object )
		{
			if ( is_object( $object ) )
			{
				$object = get_object_vars( $object );
			}

			if ( is_array( $object ) )
			{
				return array_map( array(
					'\\Kisma\\Utility\\Transform', 'objectToArray'
				), $object );
			}

			// Return array
			return $object;
		}

		/**
		 * Down and dirty array to object function
		 *
		 * @static
		 *
		 * @param array $array
		 *
		 * @return object
		 */
		public static function arrayToObject( $array )
		{
			if ( is_array( $array ) )
			{
				return (object)array_map( array(
					'\\Kisma\\Utility\\Transform', 'arrayToObject'
				), $array );
			}

			return $array;
		}

		//**************************************************************************
		//* Private Methods
		//**************************************************************************

		/**
		 * Fixes up the attributes created by the slow-ass xmlToArray function.
		 *
		 * @param array $source
		 */
		protected function _normalizeAttributes( &$source = null )
		{
			if ( !is_array( $source ) )
			{
				return;
			}

			foreach ( $source as $_key => $_value )
			{
				$_attributeCounter = 0;

				//	Get the value fixed up...
				if ( is_array( $_value ) )
				{
					self::_normalizeAttributes( $_value );
				}

				$source[$_key] = $_value;

				//	Check attributes
				$_bogusKey = $_key . '_attr';

				if ( array_key_exists( $_bogusKey, $source ) && is_array( $source[$_bogusKey] ) )
				{
					$_attributes = array();

					foreach ( $source[$_bogusKey] as $_attributeKey => $_attributeValue )
					{
						$_attributes[$_attributeKey] =
							is_array( $_attributeValue ) ? self::_normalizeAttributes( $_attributeValue ) :
								$_attributeValue;
					}

					//	Remove bogus entry
					unset( $source[$_bogusKey] );

					//	Add attributes to source key
					if ( is_array( $source[$_key] ) )
					{
						array_push( $source[$_key], array( '@attributes' => $_attributes ) );
					}
					else
					{
						//	Get old value and move into an array
						$_value = $source[$_key];
						$_result[$_key] = array(
							'@attributes' => $_attributes, $_value,
						);
					}
				}
			}
		}

	}
}