<?php
/**
 * Mount.php
 *
 * Copyright (c) 2012 Silverpop Systems, Inc.
 * http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 * @filesource
 */
namespace CIS\Services\Delivery;

/**
 * Mount
 */
class Mount extends \CIS\Services\BaseDeliveryService
{
	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public function deliver( $settings = null )
	{
		$_settings = parent::deliver( $settings );
		throw new \CIS\Exceptions\DeliveryException( 'This delivery service is not yet operational.' );
	}

}
