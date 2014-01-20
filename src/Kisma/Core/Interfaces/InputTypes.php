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
namespace Kisma\Core\Interfaces;

/**
 * InputTypes
 * HTML input tag types. Yup, this is really the entire list
 */
interface InputTypes
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const Button = 'button';
	/**
	 * @var string
	 */
	const Checkbox = 'checkbox';
	/**
	 * @var string
	 */
	const Color = 'color';
	/**
	 * @var string
	 */
	const Date = 'date';
	/**
	 * @var string
	 */
	const DateTime = 'datetime';
	/**
	 * @var string
	 */
	const DateTimeLocal = 'datetime-local';
	/**
	 * @var string
	 */
	const Email = 'email';
	/**
	 * @var string
	 */
	const File = 'file';
	/**
	 * @var string
	 */
	const Hidden = 'hidden';
	/**
	 * @var string
	 */
	const Image = 'image';
	/**
	 * @var string
	 */
	const Month = 'month';
	/**
	 * @var string
	 */
	const Number = 'number';
	/**
	 * @var string
	 */
	const Password = 'password';
	/**
	 * @var string
	 */
	const Radio = 'radio';
	/**
	 * @var string
	 */
	const Range = 'range';
	/**
	 * @var string
	 */
	const Reset = 'reset';
	/**
	 * @var string
	 */
	const Search = 'search';
	/**
	 * @var string
	 */
	const Submit = 'submit';
	/**
	 * @var string
	 */
	const Tel = 'tel';
	/**
	 * @var string
	 */
	const Text = 'text';
	/**
	 * @var string
	 */
	const Time = 'time';
	/**
	 * @var string
	 */
	const Url = 'url';
	/**
	 * @var string
	 */
	const Week = 'week';
}