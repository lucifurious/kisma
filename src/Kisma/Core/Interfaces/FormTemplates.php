<?php
/**
 * FormTemplates.php
 */
namespace Kisma\Core\Interfaces;

/**
 * FormTemplates
 * Bootstrap form types
 */
interface FormTemplates
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	const PlainInput = <<<HTML
%%label%%
%%input%%
HTML;

	/**
	 * @var int
	 */
	const HorizontalInput = <<<HTML
%%label%%
%%input%%
HTML;
	/**
	 * @var int
	 */
	const Inline = '-inline';
	/**
	 * @var int
	 */
	const Search = '-search';

}