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
	 * Extensible framework sniffer.
	 * Subclass and add a YourClass::sniff_[framework]() method.
	 *
	 * @param string $path
	 */
	public static function framework( $path = null )
	{
		foreach ( \Kisma\Core\Enums\PhpFrameworks::getDefinedConstants() as $_constant => $_value )
		{
			if ( method_exists( get_called_class(), 'sniff_' . $_value ) )
			{
				if ( call_user_func( array( get_called_class(), 'sniff_' . $_value ) ) )
				{
					\Kisma::set( 'app.framework', $_value );
					Log::debug( 'PHP framework detected: ' . $_constant . ' (' . $_value . ')' );

					switch ( $_constant )
					{
						case \Kisma\Core\Enums\PhpFrameworks::Yii:
							/**
							 * Pull in all the parameters from the Yii app into the bag...
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
