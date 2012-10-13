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
	 */
	public static function postUpdate( Event $event )
	{
		$_composer = $event->getComposer();
		// do stuff
	}

	/**
	 * @param \Composer\Script\Event $event
	 */
	public static function postPackageInstall( Event $event )
	{
		$_installedPackage = $event->getOperation()->getPackage();
		// do stuff
	}
}
