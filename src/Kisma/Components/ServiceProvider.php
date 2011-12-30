<?php
/**
 * ServiceProvider.php
 *
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license http://github.com/Pogostick/kisma/licensing/
 * @author Jerry Ablan <kisma@pogostick.com>
 * @package kisma.components
 * @since v1.0.0
 *
 * @filesource
 */
namespace Kisma\Components
{
	/**
	 * ServiceProvider.php
	 * A base class for all data service providers
	 */
	abstract class ServiceProvider implements \Kisma\IProvider, \Silex\ServiceProviderInterface
	{
		//*************************************************************************
		//* Public Methods
		//*************************************************************************

		/**
		 * Registers services on the given app.
		 *
		 * @param \Kisma\Kisma $app An app instance
		 *
		 * @return mixed
		 */
		public function register( \Kisma\Kisma $app )
		{
			//	You shouldn't really get here...
			return null;
		}
	}
}