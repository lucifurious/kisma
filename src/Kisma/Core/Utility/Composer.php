<?php
/**
 * Composer.php
 */
namespace Kisma\Core\Utility;
/**
 * Composer
 * Provides Composer event handling
 */
class Composer
{
	/**
	 * @param \Composer\Script\Event $event
	 *
	 * @internal param $
	 */
	public static function postUpdate( \Composer\Script\Event $event )
	{
		$_composer = $event->getComposer();
		$_package = $_composer->getPackage();

		// do stuff
	}

	/**
	 * @param \Composer\Script\Event $event
	 */
	public static function postPackageInstall( \Composer\Script\Event $event )
	{
		$_composer = $event->getComposer();
		$_package = $_composer->getPackage();

		// do stuff
	}
}
