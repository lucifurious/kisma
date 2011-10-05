<?php
/**
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
 * @category	  Kisma_Aspects_Transform
 * @namespace	 \Kisma\Aspects\Transform
 * @package	   kisma.aspect.transform
 * @since		 v1.0.0
 * @filesource
 */

/**************************************************************************
 ** Namespace Declarations
 **************************************************************************/

/**
 * @namespace Kisma\Aspects\Transform
 */
namespace Kisma\Aspects\Transform;

/**
 * Xml
 * Transforms data to XML
 */
class Xml extends \Kisma\Components\Aspect implements \Kisma\ITransform
{
	//*************************************************************************
	//* Private Members 
	//*************************************************************************

	/**
	 * @var string The encoding type
	 */
	protected $_encoding = 'utf-8';
	/**
	 * @var string The version
	 */
	protected $_version = '1.0';
	/**
	 * @var bool Indicates if the output should be formatted or not
	 */
	protected $_formatOutput = true;
	/**
	 * @var string The root element for returned XML
	 */
	protected $_rootElement = 'root';
	/**
	 * @var \DOMDocument The created element
	 */
	protected $_xml = null;

	//*************************************************************************
	//* Public Methods 
	//*************************************************************************

	/**
	 * Returns the XML representation of an array
	 * @param array $value The value being transformed
	 * @param mixed $options
	 * @return string
	 */
	public function transform( $value, $options = null )
	{
		//	Load options
		$this->_loadConfiguration( $options, true );

		//	Build a DOM and return it
		$this->_xml = new \DOMDocument( $this->_version, $this->_encoding );
		$this->_xml->formatOutput = $this->_formatOutput;
		$this->_xml->appendChild( $this->_toXml( $this->_rootElement, $value ) );

		$_output = $this->_xml->saveXML();
		\Kisma\Kisma::logDebug( 'Xml Transform: ' . $_output );

		return $_output;
	}

	//*************************************************************************
	//* Private Methods
	//*************************************************************************

	/**
	 * Converts an array of data to an XML document.
	 * To set XML attributes for a node, include a sub-array of NVPs keyed with '@attributes'
	 * @param string $nodeName
	 * @param array $rawData
	 * @return \DOMElement
	 */
	protected function _toXml( $nodeName, $rawData = array() )
	{
		$_xml = $this->_xml->createElement( $nodeName );

		if ( is_array( $rawData ) )
		{
			if ( isset( $rawData['@attributes'] ) )
			{
				foreach ( $rawData['@attributes'] as $_attribute => $_attributeValue )
				{
					$_attributeValue = ( is_bool( $_attributeValue ) ? ( $_attributeValue ? 'true' : 'false' ) : $_attributeValue );
					$_xml->setAttribute( $_attribute, htmlspecialchars( $_attributeValue, ENT_QUOTES, $this->_encoding ) );
				}

				unset( $rawData['@attributes'] );
			}

			//	Now build out the rest
			foreach ( $rawData as $_nodeName => $_nodeValue )
			{
				if ( is_array( $_nodeValue ) && !empty( $_nodeValue ) )
				{
					//	Multiple sub-nodes...
					foreach ( $_nodeValue as $_subNodeName => $_subNodeValue )
					{
						$_xml->appendChild( $this->_toXml( $_subNodeName, $_subNodeValue ) );
					}
				}
				else
				{
					//	Single sub-node
					$_xml->appendChild( $this->_toXml( $_nodeName, $_nodeValue ) );
				}
				
				unset( $rawData[$_nodeName] );
			}
		}
		else
		{
			$_value = ( is_bool( $rawData ) ? ( $rawData ? 'true' : 'false' ) : $rawData );
			$_xml->appendChild( $this->_xml->createTextNode( htmlspecialchars( $_value, ENT_QUOTES, $this->_encoding ) ) );
		}

		return $_xml;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param string $encoding
	 * @return \Kisma\Aspects\Transform\Xml
	 */
	public function setEncoding( $encoding = 'utf-8' )
	{
		$this->_encoding = $encoding;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEncoding()
	{
		return $this->_encoding;
	}

	/**
	 * @param boolean $formatOutput
	 * @return \Kisma\Aspects\Transform\Xml
	 */
	public function setFormatOutput( $formatOutput = true )
	{
		$this->_formatOutput = $formatOutput;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getFormatOutput()
	{
		return $this->_formatOutput;
	}

	/**
	 * @param string $rootElement
	 * @return \Kisma\Aspects\Transform\Xml
	 */
	public function setRootElement( $rootElement )
	{
		$this->_rootElement = $rootElement;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRootElement()
	{
		return $this->_rootElement;
	}

	/**
	 * @param string $version
	 * @return \Kisma\Aspects\Transform\Xml
	 */
	public function setVersion( $version = '1.0' )
	{
		$this->_version = $version;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return $this->_version;
	}

	/**
	 * @param \DOMDocument $xml
	 * @return \Kisma\Aspects\Transform\Xml
	 */
	protected function _setXml( $xml )
	{
		$this->_xml = $xml;
		return $this;
	}

	/**
	 * @return \DOMDocument
	 */
	public function getXml()
	{
		return $this->_xml;
	}

}
