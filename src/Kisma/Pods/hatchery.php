<?php
namespace Kisma\Pods;

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

ini_set( 'display_errors', 0 );

/**
 * Hatchery
 *
 * @package Kisma\Pods
 */
class Hatchery extends Application
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const HATCHERY_VERSION = '1.0.0-beta';

	/**
	 * @var string
	 */
	const HATCHERY_TITLE = 'Kisma Pod Hatchery';

	/**
	 * @var string
	 */
	const HATCHERY_PATH_ENV = 'HATCHERY_COMMAND_PATH';

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param string $version
	 */
	public function __construct( $version = self::HATCHERY_VERSION )
	{
		parent::__construct( static::HATCHERY_TITLE, $version );
	}

	/**
	 * @param string $version
	 */
	public static function hatch( $version = self::HATCHERY_VERSION )
	{
		$_app = new self( $version );

		//	Automatically add any commands that exist in the commands directory.
		$_commands = array();
		$_files = glob( __DIR__ . '/Commands/*.php' );

		if ( !empty( $_files ) )
		{
			foreach ( $_files as $_file )
			{
				if ( !is_dir( $_file ) )
				{
					/** @noinspection PhpIncludeInspection */
					@require_once $_file;

					//	Construct a class name from the directory entry. PSR-0 required.
					$_class = 'Kisma\\Pods' . str_ireplace(
						array(
							 __DIR__,
							 DIRECTORY_SEPARATOR,
							 '.php'
						),
						array(
							 null,
							 '\\',
							 null,
						),
						$_file
					);

					$_commands[] = new $_class;
				}
			}
		}

		$_app->addCommands( $_commands );
		$_app->run();
	}

}

Hatchery::hatch();