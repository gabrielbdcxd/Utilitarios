<?php
/**
 * @brief		Content Router extension: Reviews
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @subpackage	Nexus
 * @since		05 May 2014
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\nexus\extensions\core\ContentRouter;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * @brief	Content Router extension: Reviews
 */
class _Reviews
{
	/**
	 * @brief	Content Item Classes
	 */
	public $classes = array( 'IPS\nexus\Package\Review' );
	
	/**
	 * @brief	Item Classes for embed only
	 */
	public $embeddableContent = array( 'IPS\nexus\Package\Item' );
}