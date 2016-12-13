<?php
/**
 * @brief		Task to generate sitemaps
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @since		30 Aug 2013
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\core\tasks;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Task to generate sitemaps
 */
class _sitemapgenerator extends \IPS\Task
{
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		try
		{
			$generator	= new \IPS\Sitemap;
			$generator->buildNextSitemap();
		}
		catch( \Exception $e )
		{
			return $e->getMessage();
		}

		if( count( $generator->log ) )
		{
			return $generator->log;
		}
		else
		{
			return null;
		}
	}
}