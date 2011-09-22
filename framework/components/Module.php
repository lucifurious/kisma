<?php
/**
 * Kisma(tm) : PHP Microframework (http://github.com/lucifurious/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/lucifurious/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/lucifurious/kisma/ Kisma(tm)
 * @license			http://github.com/lucifurious/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @package			kisma
 * @namespace		\Kisma\Components
 * @since			v1.0.0
 * @filesource
 */
namespace Kisma\Components;
/**
 * Module
 * Compartmentalizes a chunk of stuff
 */
abstract class Module extends Component implements \Kisma\IModule
{
	/**
	 * @var string
	 */
	protected $_basePath = null;
	/**
	 * @var array[]
	 */
	protected $_components = array();
	/**
	 * @var Component[]
	 */
	protected $_componentCache = array();
	/**
	 * @var string
	 */
	protected $_id = null;
	/**
	 * @var array
	 */
	protected $_parameters = array();
	/**
	 * @var string
	 */
	protected $_parent = null;
	/**
	 * @var string
	 */
	protected $_path = null;
	/**
	 * @var array
	 */
	protected $_submodules = array();

	//*************************************************************************
	//* Default/Magic Methods
	//*************************************************************************

	/**
	 * Constructor.
	 * @param array $options
	 * @return \Kisma\Components\Module
	 */
	public function __construct( $options = array() )
	{
		parent::__construct( $options );

		//	Load any components configured
		$this->loadComponents( $this->_components );
	}

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param array $components
	 * @return void
	 */
	public function loadComponents( $components = array() )
	{
		foreach ( $components as $_component )
		{
			if ( false === $this->loadComponent( $_component ) )
			{
				//	What to do?
			}
		}
	}

	/**
	 * Loads a component for use
	 * @param string $id
	 * @param boolean $create If the component does not exist, and this is set to true, the component will be created
	 * @return \Kisma\Components\Component|false
	 */
	public function loadComponent( $id, $create = true )
	{
		if ( isset( $this->_componentCache[$id] ) )
		{
			return $this->_componentCache[$id];
		}

		if ( $create && isset( $this->_components[$id] ) )
		{
			return $this->_components[$id] = $this->createComponent( $this->_components[$id] );
		}

		return false;
	}

	/**
	 * Creates a new component within this module
	 * @throws ComponentException
	 * @param array $config
	 * @return Component
	 */
	public function createComponent( $config = array() )
	{
		if ( null === ( $_class = K::o( $config, 'class', null, true ) ) )
		{
			throw new ComponentException( 'Unable to create component. No "class" specified.' );
		}

		return new $_class( $config );
	}

	/**
	 * Returns the directory that contains the application modules.
	 * @return string the directory that contains the application modules. Defaults to the 'modules' subdirectory of {@link basePath}.
	 */
	public function getBasePath()
	{
		if ( $this->_path !== null )
		{
			return $this->_path;
		}
		else
		{
			return $this->_path = $this->getBasePath() . DIRECTORY_SEPARATOR . 'modules';
		}
	}

	/**
	 * Sets the directory that contains the application modules.
	 * @param string $value the directory that contains the application modules.
	 * @throws CException if the directory is invalid
	 */
	public function setModulePath( $value )
	{
		if ( ( $this->_path = realpath( $value ) ) === false || !is_dir( $this->_path ) )
		{
			throw new CException( Yii::t( 'yii', 'The module path "{path}" is not a valid directory.',
					array
					(
						'{path}' => $value
					) ) );
		}
	}

	/**
	 * Sets the aliases that are used in the module.
	 * @param array $aliases list of aliases to be imported
	 */
	public function setImport( $aliases )
	{
		foreach ( $aliases as $alias )
		{
			Yii::import( $alias );
		}
	}

	/**
	 * Defines the root aliases.
	 * @param array $mappings list of aliases to be defined. The array keys are root aliases,
	 * while the array values are paths or aliases corresponding to the root aliases.
	 * For example,
	 * <pre>
	 * array(
	 *	'models'=>'application.models',			  // an existing alias
	 *	'extensions'=>'application.extensions',	  // an existing alias
	 *	'backend'=>dirname(__FILE__).'/../backend',  // a directory
	 * )
	 * </pre>
	 * @since 1.0.5
	 */
	public function setAliases( $mappings )
	{
		foreach ( $mappings as $name => $alias )
		{
			if ( ( $path = Yii::getPathOfAlias( $alias ) ) !== false )
			{
				Yii::setPathOfAlias( $name, $path );
			}
			else
			{
				Yii::setPathOfAlias( $name, $alias );
			}
		}
	}

	/**
	 * Returns the parent module.
	 * @return CModule the parent module. Null if this module does not have a parent.
	 */
	public function getParentModule()
	{
		return $this->_parentModule;
	}

	/**
	 * Retrieves the named application module.
	 * The module has to be declared in {@link modules}. A new instance will be created
	 * when calling this method with the given ID for the first time.
	 * @param string $id application module ID (case-sensitive)
	 * @return CModule the module instance, null if the module is disabled or does not exist.
	 */
	public function getModule( $id )
	{
		if ( isset( $this->_modules[$id] ) || array_key_exists( $id, $this->_modules ) )
		{
			return $this->_modules[$id];
		}
		else
		{
			if ( isset( $this->_moduleConfig[$id] ) )
			{
				$config = $this->_moduleConfig[$id];
				if ( !isset( $config['enabled'] ) || $config['enabled'] )
				{
					Yii::trace( "Loading \"$id\" module", 'system.base.CModule' );
					$class = $config['class'];
					unset( $config['class'], $config['enabled'] );
					if ( $this === Yii::app() )
					{
						$module = Yii::createComponent( $class, $id, null, $config );
					}
					else
					{
						$module = Yii::createComponent( $class, $this->getId() . '/' . $id, $this, $config );
					}
					return $this->_modules[$id] = $module;
				}
			}
		}
	}

	/**
	 * Returns a value indicating whether the specified module is installed.
	 * @param string $id the module ID
	 * @return boolean whether the specified module is installed.
	 * @since 1.1.2
	 */
	public function hasModule( $id )
	{
		return isset( $this->_moduleConfig[$id] ) || isset( $this->_modules[$id] );
	}

	/**
	 * Returns the configuration of the currently installed modules.
	 * @return array the configuration of the currently installed modules (module ID => configuration)
	 */
	public function getModules()
	{
		return $this->_moduleConfig;
	}

	/**
	 * Configures the sub-modules of this module.
	 *
	 * Call this method to declare sub-modules and configure them with their initial property values.
	 * The parameter should be an array of module configurations. Each array element represents a single module,
	 * which can be either a string representing the module ID or an ID-configuration pair representing
	 * a module with the specified ID and the initial property values.
	 *
	 * For example, the following array declares two modules:
	 * <pre>
	 * array(
	 *	 'admin',				// a single module ID
	 *	 'payment'=>array(	   // ID-configuration pair
	 *		 'server'=>'paymentserver.com',
	 *	 ),
	 * )
	 * </pre>
	 *
	 * By default, the module class is determined using the expression <code>ucfirst($moduleID).'Module'</code>.
	 * And the class file is located under <code>modules/$moduleID</code>.
	 * You may override this default by explicitly specifying the 'class' option in the configuration.
	 *
	 * You may also enable or disable a module by specifying the 'enabled' option in the configuration.
	 *
	 * @param array $modules module configurations.
	 */
	public function linkSubmodules( $submodules )
	{
		foreach ( $submodules as $_id => $_submodule )
		{
			//	Allow for id-less modules
			if ( is_numeric( $_id ) )
			{
				$_id = $_submodule;
				$_submodule = array();
			}

			//	Auto-choose class based on id
			if ( !isset( $_submodule['class'] ) )
			{
				K::alias( $_id, $this->getBasePath() . DIRECTORY_SEPARATOR . $_id );
				$_submodule['class'] = $_id . '.' . self::standardizeName( $_id ) . 'Module';
			}

			if ( isset( $this->_submodules[$_id] ) )
			{
				$this->_submodules[$_id]->setOptions( $_submodule );
			}
			else
			{
				$this->_submodules[$_id] = $_submodule;
			}
		}
	}

	/**
	 * Returns true if we are aware of this component, linked or not.
	 * @param string $id
	 * @return boolean
	 */
	public function hasComponent( $id )
	{
		return isset( $this->_componentCache[$id] ) || isset( $this->_components[$id] );
	}

	/**
	 * Add a component to the module
	 * @param string $id
	 * @param Component	$component
	 * @param bool $overwrite
	 */
	public function linkComponent( $id, $component, $overwrite = true )
	{
		if ( !$overwrite && isset( $this->_componentCache[$id] ) )
		{
			throw new ComponentException( 'Component already exists but cannot overwrite.' );
		}

		$this->_componentCache[$id] = $component;
	}

	/**
	 * Removes a component from the module
	 * @param string $id
	 */
	public function unlinkComponent( $id )
	{
		if ( isset( $this->_componentCache[$id] ) )
		{
			unset( $this->_componentCache[$id] );
		}
	}

	/**
	 * Links one or more components at once
	 * @param array $components
	 * @param bool $overwrite
	 */
	public function linkComponents( $components, $overwrite = true )
	{
		foreach ( $components as $_id => $_component )
		{
			$this->linkComponent( $_id, $_component, $overwrite );
		}
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * Returns the base path of this module
	 * @return string|null
	 */
	public function getBasePath()
	{
		if ( null === $this->_basePath )
		{
			$_object = new \ReflectionClass( $this );
			$this->_basePath = dirname( $_object->getFileName() );
		}

		return $this->_basePath;
	}

	/**
	 * Sets the base path of the module
	 * @param string $path
	 * @return \Kisma\Components\Module
	 * @throws InvalidArgumentException
	 */
	public function setBasePath( $path )
	{
		if ( !is_dir( realpath( $path ) ) )
		{
			throw new InvalidArgumentException( 'The path "' . $path . '" is not valid or non-existant.' );
		}

		$this->_basePath = $path;
		return $this;
	}

	/**
	 * @param $componentCache
	 * @return \Kisma\Components\Module
	 */
	public function setComponentCache( $componentCache )
	{
		$this->_componentCache = $componentCache;
		return $this;
	}

	/**
	 * @return array|\Kisma\Components\Component[]
	 */
	public function getComponentCache()
	{
		return $this->_componentCache;
	}

	/**
	 * @param string $id
	 * @return \Kisma\Components\Module $this
	 */
	public function setId( $id )
	{
		$this->_id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param array $parameters
	 * @return \Kisma\Components\Module $this
	 */
	public function setParameters( $parameters )
	{
		$this->_parameters = $parameters;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->_parameters;
	}

	/**
	 * @param string $parent
	 * @return \Kisma\Components\Module $this
	 */
	public function setParent( $parent )
	{
		$this->_parent = $parent;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getParent()
	{
		return $this->_parent;
	}

	/**
	 * @param string $path
	 * @return \Kisma\Components\Module $this
	 */
	public function setPath( $path )
	{
		$this->_path = $path;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->_path;
	}

	/**
	 * @param array $submodules
	 * @return \Kisma\Components\Module $this
	 */
	public function setSubmodules( $submodules )
	{
		$this->_submodules = $submodules;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getSubmodules()
	{
		return $this->_submodules;
	}
}
