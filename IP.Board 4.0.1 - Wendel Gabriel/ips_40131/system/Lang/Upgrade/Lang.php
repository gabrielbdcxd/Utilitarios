<?php
/**
 * @brief		Upgrader Language Class
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @since		13 Feb 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\Lang\Upgrade;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Setup Language Class
 */
class _Lang extends \IPS\_Lang
{
	/**
	 * Languages
	 *
	 * @param	null|\IPS\Db\Select	$iterator	Select iterator
	 * @return	array
	 */
	public static function languages( $iterator=NULL )
	{
		if ( !self::$gotAll )
		{
			if( $iterator === NULL )
			{
				if ( isset( \IPS\Data\Store::i()->languages ) )
				{
					$rows = \IPS\Data\Store::i()->languages;
				}
				else
				{
					$rows = iterator_to_array( \IPS\Db::i()->select( '*', 'core_sys_lang' )->setKeyField('lang_id') );
					\IPS\Data\Store::i()->languages = $rows;
				}
			}
			else
			{
				$rows	= iterator_to_array( $iterator );
			}
			
			foreach( $rows as $id => $lang )
			{
				if ( $lang['lang_default'] )
				{
					self::$defaultLanguageId = $lang['lang_id'];
				}
				self::$multitons[ $id ] = static::constructFromData( $lang );
			}
			
			self::$outputSalt = uniqid();

			self::$gotAll	= TRUE;
		}
		return self::$multitons;
	}
}