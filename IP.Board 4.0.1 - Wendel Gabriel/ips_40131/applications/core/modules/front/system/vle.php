<?php
/**
 * @brief		Visual Language Editor
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @since		27 Jun 2013
 * @version		SVN_VERSION_NUMBER
 */
 
namespace IPS\core\modules\front\system;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Visual Language Editor
 */
class _vle extends \IPS\Dispatcher\Controller
{
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		if ( !\IPS\Member::loggedIn()->hasAcpRestriction( 'core', 'languages', 'lang_words' ) )
		{
			\IPS\Output::i()->json( 'NO_PERMISSION', 403 );
		}
		return parent::execute();
	}
	
	/**
	 * Get
	 *
	 * @return	void
	 */
	public function get()
	{
		try
		{
			$word = \IPS\Member::loggedIn()->language()->get( \IPS\Request::i()->key );
		}
		catch ( \UnderflowException $e )
		{
			$word = NULL;
		}
		
		\IPS\Output::i()->json( $word );
	}
	
	/**
	 * Set
	 *
	 * @return	void
	 */
	public function set()
	{
		try
		{
			$word	= \IPS\Db::i()->select( '*', 'core_sys_lang_words', array( 'lang_id=? AND word_key=?', \IPS\Member::loggedIn()->language()->id, \IPS\Request::i()->key ) )->first();
		}
		catch( \UnderflowException $e )
		{
			$word	= NULL;
		}

		if ( $word !== NULL )
		{
			if ( $word['word_export'] )
			{
				\IPS\Db::i()->update( 'core_sys_lang_words', array( 'word_custom' => \IPS\Request::i()->value, 'word_custom_version' => \IPS\Application::load( $word['word_app'] )->long_version ), array( 'word_id=?', $word['word_id'] ) );
			}
			else
			{
				\IPS\Lang::saveCustom( $word['word_app'], $word['word_key'], array( \IPS\Member::loggedIn()->language()->id => \IPS\Request::i()->value ) );
			}

			$string	= \IPS\Request::i()->value ?: $word['word_default'];
		}
		else
		{
			\IPS\Lang::saveCustom( 'core', \IPS\Request::i()->key, array( \IPS\Member::loggedIn()->language()->id => \IPS\Request::i()->value ) );

			$string	= \IPS\Request::i()->value;
		}

		\IPS\Db::i()->insert( 'core_admin_logs', array(
			'member_id'		=> \IPS\Member::loggedIn()->member_id,
			'ctime'			=> time(),
			'note'			=> json_encode( array( $word['word_key'] => FALSE, \IPS\Member::loggedIn()->language()->title => FALSE ) ),
			'ip_address'	=> \IPS\Request::i()->ipAddress(),
			'appcomponent'	=> 'core',
			'module'		=> 'system',
			'controller'	=> 'vle',
			'do'			=> 'set',
			'lang_key'		=> 'acplogs__lang_translate'
		) );

		\IPS\Widget::deleteCaches();

		\IPS\Output::i()->sendOutput( $string, 200, 'text/text' );
	}
}