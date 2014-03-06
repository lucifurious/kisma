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
namespace Kisma\Core\Tools;

use Kisma\Core\Exceptions\NotImplementedException;
use Kisma\Core\Exceptions\StorageException;
use Kisma\Core\Seed;
use Kisma\Core\Utility\Log;
use Kisma\Core\Utility\Sql;

/**
 * DataReader
 * Thin veneer over the PDOStatement class that implements Iterator and Countable so it's traversable via foreach!
 *
 * Proxied PDO Stuff:
 *
 * @property string $queryString
 *
 * @method bool bindParam( $parameter, &$variable, $data_type = \PDO::PARAM_STR, $length = null, $driverOptions = null )
 * @method bool bindColumn( $column, &$parameter, $type = null, $maxLength = null, $driverData = null )
 * @method bool bindValue( $parameter, $value, $dataType = \PDO::PARAM_STR )
 * @method mixed fetch( $fetchStyle = null, $cursorOrientation = \PDO::FETCH_ORI_NEXT, $cursorOffset = 0 )
 * @method string fetchColumn( $columnNumber = 0 )
 * @method array fetchAll ( $fetchStyle = null, $fetchArgument = null, array $ctorArgs = 'array()' )
 * @method mixed fetchObject( $className = '\\stdClass', array $ctorArgs = null )
 * @method string errorCode()
 * @method array errorInfo()
 * @method bool setAttribute( $attribute, $value )
 * @method mixed getAttribute( $attribute )
 * @method int rowCount()
 * @method int columnCount()
 * @method array|bool getColumnMeta( $column )
 * @method bool setFetchMode( $mode )
 */
class DataReader extends Seed implements \Iterator, \Countable
{
	//*************************************************************************
	//* Members
	//*************************************************************************

	/**
	 * @var \PDOStatement
	 */
	protected $_statement;
	/**
	 * @var bool
	 */
	protected $_closed = false;
	/**
	 * @var array
	 */
	protected $_row;
	/**
	 * @var int
	 */
	protected $_index = null;
	/**
	 * @var mixed
	 */
	protected $_executeResult = null;
	/**
	 * @var array Last failed execute error info
	 */
	protected $_errorInfo = null;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param \PDOStatement $statement
	 */
	public function __construct( \PDOStatement $statement )
	{
		parent::__construct();

		if ( null !== $statement )
		{
			$this->_statement = $statement;
		}
	}

	/**
	 * DataReader factory
	 * Executes a SQL statement (with optional parameters) and returns a new DataReader ready for iteration
	 *
	 * @param string $sql
	 * @param array  $parameters
	 * @param \PDO   $connection
	 * @param int    $fetchMode
	 *
	 * @return DataReader|bool
	 */
	public static function create( $sql, $parameters = null, $connection = null, $fetchMode = \PDO::FETCH_ASSOC )
	{
		$_reader = new DataReader( Sql::createStatement( $sql, $connection, $fetchMode ) );

		if ( false === ( $_result = $_reader->execute( $parameters ) ) )
		{
			//	Don't be wasteful
			unset( $_reader );

			return false;
		}

		return $_reader;
	}

	/**
	 * Allows this class to be called directly for PDO methods
	 *
	 * @param string $name
	 * @param array  $arguments
	 *
	 * @throws \Exception
	 * @return mixed
	 */
	public function __call( $name, $arguments )
	{
		if ( !empty( $this->_statement ) && method_exists( $this->_statement, $name ) )
		{
			try
			{
				$_result = call_user_func_array( array( $this->_statement, $name ), $arguments );

				if ( is_resource( $_result ) && 'stream' == get_resource_type( $_result ) )
				{
					return stream_get_contents( $_result );
				}

				return $_result;
			}
			catch ( \Exception $_ex )
			{
				Log::error( 'PDO exception: ' . $_ex->getMessage() );
				throw $_ex;
			}
		}

		throw new NotImplementedException();
	}

	/**
	 * @param array|null $parameters
	 *
	 * @return bool
	 */
	public function execute( $parameters = null )
	{
		if ( empty( $parameters ) )
		{
			$_result = $this->_statement->execute();
		}
		else
		{
			$_result = $this->_statement->execute( $parameters );
		}

		if ( false === $_result )
		{
			$this->_errorInfo = $this->_statement->errorInfo();
			Log::error( 'SQL error: [' . $this->_errorInfo[0] . '-' . $this->_errorInfo[1] . '] ' . $this->_errorInfo[2] );
		}

		return $this->_executeResult = $_result;
	}

	/**
	 * Advances the reader to the next rowset
	 * Not supported by mssql
	 *
	 * @return boolean
	 */
	public function nextRowset()
	{
		if ( false !== ( $result = $this->_statement->nextRowset() ) )
		{
			$this->_index = null;
		}

		return $result;
	}

	/**
	 * Closes the reader.
	 */
	public function closeCursor()
	{
		$this->_statement->closeCursor();
		$this->_closed = true;
		$this->_index = null;
	}

	/**
	 * Returns the number of rows in the result set.
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->rowCount();
	}

	/**
	 * Resets the iterator to the initial state.
	 */
	public function rewind()
	{
		if ( null !== $this->_index )
		{
			throw new StorageException( 'Forward-only cursor, "rewinding" not allowed.' );
		}

		$this->next();
		$this->_index = 0;
	}

	/**
	 * Returns the index of the current row.
	 *
	 * @return int|string
	 */
	public function key()
	{
		return $this->_index;
	}

	/**
	 * Returns the current row.
	 *
	 * @return array|mixed
	 */
	public function current()
	{
		return $this->_row;
	}

	/**
	 * Moves the internal pointer to the next row.
	 */
	public function next()
	{
		$this->_row = $this->fetch();

		if ( !empty( $this->_row ) )
		{
			$this->_index++;
		}
	}

	/**
	 * Returns whether there is a row of data at current position.
	 *
	 * @return bool
	 */
	public function valid()
	{
		return false !== $this->_row;
	}

	/**
	 * @param bool $closed
	 *
	 * @return \Kisma\Core\Tools\DataReader
	 */
	public function setClosed( $closed )
	{
		$this->_closed = $closed;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getClosed()
	{
		return $this->_closed;
	}

	/**
	 * @param int $index
	 *
	 * @return \Kisma\Core\Tools\DataReader
	 */
	public function setIndex( $index )
	{
		$this->_index = $index;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getIndex()
	{
		return $this->_index;
	}

	/**
	 * @return array
	 */
	public function getRow()
	{
		return $this->_row;
	}

	/**
	 * @param \PDOStatement $statement
	 * @param int           $fetchMode
	 *
	 * @return \Kisma\Core\Tools\DataReader
	 */
	public function setStatement( $statement, $fetchMode = \PDO::FETCH_ASSOC )
	{
		if ( null !== ( $this->_statement = $statement ) )
		{
			$this->_statement->setFetchMode( $fetchMode );
		}

		return $this;
	}

	/**
	 * @return \PDOStatement
	 */
	public function getStatement()
	{
		return $this->_statement;
	}

	/**
	 * @param mixed $executeResult
	 *
	 * @return \Kisma\Core\Tools\DataReader
	 */
	public function setExecuteResult( $executeResult )
	{
		$this->_executeResult = $executeResult;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getExecuteResult()
	{
		return $this->_executeResult;
	}

	/**
	 * @param string $queryString
	 *
	 * @return \Kisma\Core\Tools\DataReader
	 */
	public function setQueryString( $queryString )
	{
		$this->_statement->queryString = $queryString;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getQueryString()
	{
		return $this->_statement->queryString;
	}
}
