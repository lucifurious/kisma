<?php
/**
 * Detector.php
 */
namespace Kisma\Core\Utility;
/**
 * Detector
 * Provides detection services for various things
 */
class Detector implements \Kisma\Core\Interfaces\PhpFrameworks
{
	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * @param string $path
	 */
	public static function framework( $path = null )
	{
		foreach ( \Kisma\Core\Enums\PhpFrameworks::getDefinedConstants() as $_constant => $_value )
		{
			if ( method_exists( 'Detector', 'sniff_' . $_constant ) )
			{
				if ( call_user_func( array( __CLASS__, 'sniff_' . $_constant ) ) )
				{
					\Kisma::set( 'app.framework', $_constant );

					switch ( $_constant )
					{
						case \Kisma\Core\Enums\PhpFrameworks::Yii:
							/**
							 * Pull in all the parameters from the Yii application into the Kisma bag...
							 */
							foreach ( \Yii::app()->getParams()->toArray() as $_parameterName => $_parameterValue )
							{
								\Kisma::set( $_parameterName, $_parameterValue );
							}
							break;
					}

					return $_value;
				}
			}
		}
	}

	/**
	 * Sniffs for the Yii Framework. Pretty stupidly...
	 *
	 * @return bool
	 */
	public static function sniff_yii()
	{
		return class_exists( '\\Yii', false );
	}
}
