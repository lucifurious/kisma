<?php
/**
 * BaseDeliveryService.php
 *
 * Copyright (c) 2012 Silverpop Systems, Inc.
 * http://www.silverpop.com Silverpop Systems, Inc.
 *
 * @author Jerry Ablan <jablan@silverpop.com>
 * @filesource
 */
namespace CIS\Services;

/**
 * BaseDeliveryService
 * The base class for delivery services.
 *
 * Base properties:
 *
 * @property \CIS\Services\ServiceSettings $settings
 */
abstract class BaseDeliveryService extends \CIS\Services\BaseService
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * Makes a delivery
	 * This base class version merges in the passed in settings (if any) to
	 * the service's copy and returns the new combined settings object. In your
	 * child class, if you don't want this behavior, don't call the parent.
	 *
	 * @param \CIS\Services\Delivery\Settings|array $settings
	 *
	 * @return mixed|\CIS\Services\Delivery\Settings
	 */
	public function deliver( $settings = null )
	{
		if ( null === $this->_settings )
		{
			$this->_settings = new \CIS\Services\Delivery\Settings();
		}

		return $this->_settings->merge( $settings );
	}

	/**
	 * @param \CIS\Services\Delivery\Settings $settings
	 *
	 * @return string
	 */
	protected function _buildDestinationFileName( $settings = null )
	{
		$settings = $settings ? : $this->_settings;

		if ( !isset( $settings->destinationFileName ) && isset( $settings->destinationFilePattern ) )
		{
			return str_ireplace(
				array(
					'%%date%%',
					'%%mm%%',
					'%%dd%%',
					'%%yy%%',
					'%%yyyy%%',
				),
				array(
					date( 'Ymd' ),
					date( 'm' ),
					date( 'd' ),
					date( 'y' ),
					date( 'Y' ),
				),
				$settings->destinationFilePattern
			);
		}

		return $settings->destinationFileName;
	}
}
