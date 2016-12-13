<?php
/**
 * @brief		Content Router extension: Calendar
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @subpackage	Calendar
 * @since		7 Jan 2014
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\calendar\extensions\core\ContentRouter;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * @brief	Content Router extension: Calendar
 */
class _Calendar
{
	/**
	 * @brief	Content Item Classes
	 */
	public $classes = array( 'IPS\calendar\Event' );	
}