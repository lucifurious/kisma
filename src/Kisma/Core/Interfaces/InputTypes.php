<?php
/**
 * InputTypes.php
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