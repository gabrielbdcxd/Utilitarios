<?php
/**
 * @brief		Content Router extension: Downloads
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @subpackage	Downloads
 * @since		02 Dec 2013
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\downloads\extensions\core\ContentRouter;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * @brief	Content Router extension: Downloads
 */
class _Downloads
{
	/**
	 * @brief	Content Item Classes
	 */
	public $classes = array( 'IPS\downloads\File' );	
}