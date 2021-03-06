<?php
/**
 * @brief		Template Plugin - DateTime
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @since		15 July 2013
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\Output\Plugin;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Template Plugin - DateTime
 */
class _Datetime
{
	/**
	 * @brief	Can be used when compiling CSS
	 */
	public static $canBeUsedInCss = FALSE;
	
	/**
	 * Run the plug-in
	 *
	 * @param	string 		$data	  The initial data from the tag
	 * @param	array		$options    Array of options
	 * @return	string		Code to eval
	 */
	public static function runPlugin( $data, $options )
	{
		$return = array();
		$return['pre'] = '$val = ( ' . $data . ' instanceof \IPS\DateTime ) ? ' . $data . ' : \IPS\DateTime::ts( ' . $data . ' );';

		if( isset( $options['dateonly'] ) )
		{
			$return['return'] = '(string) $val->localeDate()';
		}
		else if( isset( $options['norelative'] ) )
		{
			$return['return'] = '(string) $val';
		}
		else
		{
			$return['return'] = '$val->html()';
		}
				
		return $return;
	}
}