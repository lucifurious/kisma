<?php
/**
 * SeedAttributes.php
 */
namespace Kisma\Core\Interfaces;

/**
 * SeedAttributes
 * An interface for core object attributes
 */
interface SeedAttributes
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const Id = 'id';
	/**
	 * @var string
	 */
	const Name = 'name';
	/**
	 * @var string
	 */
	const Tag = 'tag';
	/**
	 * @var string
	 */
	const AutoAttachEvents = 'auto_attach_events';
	/**
	 * @var string
	 */
	const EventManager = 'event_manager';
	/**
	 * @var string
	 */
	const AttributeStorage = 'attribute_storage';

}
