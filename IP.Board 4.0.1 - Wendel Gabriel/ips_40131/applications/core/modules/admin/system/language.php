<?php
/**
 * @brief		Language Changer
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @since		7 Oct 2014
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\core\modules\admin\system;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Language Changer
 */
class _language extends \IPS\Dispatcher\Controller
{
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		parent::execute();
	}

	/**
	 * Manage
	 *
	 * @return	void
	 */
	protected function manage()
	{
		\IPS\Session::i()->csrfCheck();
		
		\IPS\Member::loggedIn()->acp_language = (int) \IPS\Request::i()->id;
		\IPS\Member::loggedIn()->save();
		
		\IPS\Output::i()->redirect( \IPS\Http\Url::external( $_SERVER['HTTP_REFERER'] ) ?: \IPS\Http\Url::internal( '' ) );
	}
}