<?php
/**
 * @brief		4.0.0 Beta 5 Upgrade Code
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @since		08 Jan 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\core\setup\upg_100008;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * 4.0.0 Beta 5 Upgrade Code
 */
class _Upgrade
{

	/**
	 * Step 1
	 * Build Search index
	 *
	 * @return	array	If returns TRUE, upgrader will proceed to next step. If it returns any other value, it will set this as the value of the 'extra' GET parameter and rerun this step (useful for loops)
	 */
	public function step1()
	{
		if( !\IPS\Settings::i()->search_engine )
		{
			\IPS\Settings::i()->search_engine	= 'mysql';
		}

		\IPS\Content\Search\Index::i()->rebuild();
		return true;
	}

	/**
	 * Custom title for this step
	 *
	 * @return string
	 */
	public function step1CustomTitle()
	{
		return "Building Search index";
	}
}