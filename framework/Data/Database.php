<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license			http://github.com/Pogostick/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @category		Kisma_Database
 * @package			kisma.database
 * @namespace		\Kisma\Database
 * @since			v1.0.0
 * @filesource
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma\Data Kisma data handling components
 */
namespace Kisma\Data;

/**
 * Database
 * Dishes out database connectivity
 *
 * @property DataStore[] $dataStores;
 *
 */
class Database extends \Kisma\Components\Component
{
	//********************************************************************************
	//* Properties
	//********************************************************************************

	/**
	 * @var DataStore[]
	 */
	protected $_dataStores = array();

	//********************************************************************************
	//* Public Methods
	//********************************************************************************

	//********************************************************************************
	//* Private Methods
	//********************************************************************************

	//********************************************************************************
	//* Property Accessors
	//********************************************************************************

	/**
	 * @param DataStore[] $dataStores
	 * @return \Kisma\Data\Database
	 */
	public function setDataStores( $dataStores = array() )
	{
		$this->_dataStores = $dataStores;
		return $this;
	}

	/**
	 * @return DataStore[]
	 */
	public function getDataStores()
	{
		return $this->_dataStores;
	}
}
