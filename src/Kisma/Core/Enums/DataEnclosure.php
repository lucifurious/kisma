<?php
namespace Kisma\Core\Enums;

use Kisma\Core\Enums\HttpResponse;
use Kisma\Core\Utility\Inflector;

/**
 * DataEnclosure
 * How data fields can be enclosed
 */
class DataEnclosure
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const NONE = '';
	/**
	 * @var string
	 */
	const DOUBLE_QUOTE = '"';
	/**
	 * @var string
	 */
	const SINGLE_QUOTE = "'";
}
