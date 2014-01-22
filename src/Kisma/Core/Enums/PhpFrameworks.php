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
namespace Kisma\Core\Enums;

/**
 * PhpFrameworks
 * Various PHP frameworks used by the Detector!
 */
class PhpFrameworks extends SeedEnum implements \Kisma\Core\Interfaces\PhpFrameworks
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const CakePhp = 'cake_php';
	/**
	 * @var string
	 */
	const CodeIgniter = 'code_igniter';
	/**
	 * @var string
	 */
	const Drupal = 'drupal';
	/**
	 * @var string
	 */
	const Joomla = 'joomla';
	/**
	 * @var string
	 */
	const WordPress = 'word_press';
	/**
	 * @var string
	 */
	const FuelPhp = 'fuel_php';
	/**
	 * @var string
	 */
	const Laravel = 'laravel';
	/**
	 * @var string
	 */
	const Silex = 'silex';
	/**
	 * @var string
	 */
	const Symfony = 'symfony';
	/**
	 * @var string
	 */
	const Yii = 'yii';
	/**
	 * @var string
	 */
	const ZendFramework = 'zend_framework';
}