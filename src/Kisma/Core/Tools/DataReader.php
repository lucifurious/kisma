<?php
/**
 * DataReader.php
 */
namespace Kisma\Core\Tools;
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
class DataReader extends \Kisma\Core\Seed implements \Iterator, \Countable
{
	//*************************************************************************
	//* Private Members
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
	//* Public Methods
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
		$_reader = new DataReader( \Kisma\Core\Utility\Sql::createStatement( $sql, $connection, $fetchMode ) );

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
	 * @return mixed
	 */
	public function __call( $name, $arguments )
	{
		if ( !empty( $this->_statement ) && method_exists( $this->_statement, $name ) )
		{
			try
			{
				$_result = call_user_func_array( array( $this->_statement, $name ), $arguments );

				return $_result;
			}
			catch ( \Exception $_ex )
			{
				\Kisma\Core\Utility\Log::error( 'PDO exception: ' . $_ex->getMessage() );
			}
		}
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
			\Kisma\Core\Utility\Log::error( 'SQL error: [' . $this->_errorInfo[0] . '-' . $this->_errorInfo[1] . '] ' . $this->_errorInfo[2] );
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

	//*************************************************************************
	//* Interface Methods
	//*************************************************************************

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
			throw new \Kisma\Core\Exceptions\StorageException( 'Forward-only cursor, "rewinding" not allowed.' );
		}

		$this->next();
		$this->_index = 0;
	}

	/**
	 * Returns the index of the current row.
	 *
	 * @return \scalar
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

	//*************************************************************************
	//* Properties
	//*************************************************************************

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
