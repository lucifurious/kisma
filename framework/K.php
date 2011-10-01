<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright		  Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			   http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license			http://github.com/Pogostick/kisma/licensing/
 * @author			 Jerry Ablan <kisma@pogostick.com>
 * @category		   Kisma
 * @package			kisma
 * @namespace		  \Kisma
 * @since			  v1.0.0
 * @filesource
 */

/**
 * I know this is hokey but I couldn't figure out how to achieve what
 * I wanted.
 *
 * What I wanted was basically a \K class. But I can't seem to subclass a
 * namespaced class in the global namespace if you know what I mean. If
 * anyone has a suggestion to make this mo-betta, let me know.
 *
 * And yes, I tried using class alias...
 *
 * So, as you've probably realized if you create a new namespace within
 * the Kisma project, you must add your own K alias, if you want. You can
 * still always use the \Kisma\Kisma::method() format.
 */

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma
 */
namespace Kisma
{
	/**
	 * A shortcut to \Kisma\Kisma
	 */
	class K extends \Kisma\Kisma
	{
		//	Nothing here, move along
	}
}

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma\Components
 */
namespace Kisma\Components
{
	/**
	 * K
	 * A shortcut to \Kisma\Kisma
	 */
	class K extends \Kisma\Kisma
	{
		//	Nothing here, move along
	}
}

//*************************************************************************
//* Namespace Declarations
//*************************************************************************

/**
 * @namespace Kisma\Aspects
 */
namespace Kisma\Aspects
{
	/**
	 * A shortcut to \Kisma\Kisma
	 */
	class K extends \Kisma\Kisma
	{
		//	Nothing here, move along
	}
}