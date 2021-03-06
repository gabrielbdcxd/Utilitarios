<?php
/**
 * @brief		Admin CP Group Form
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @subpackage	Gallery
 * @since		04 Mar 2014
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\gallery\extensions\core\GroupForm;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Admin CP Group Form
 */
class _Gallery
{
	/**
	 * Process Form
	 *
	 * @param	\IPS\Form\Tabbed		$form	The form
	 * @param	\IPS\Member\Group		$group	Existing Group
	 * @return	void
	 */
	public function process( &$form, $group )
	{
		$form->addHeader( 'gallery_album_permissions' );
		$form->add( new \IPS\Helpers\Form\YesNo( 'g_create_albums', $group->g_create_albums, FALSE, array( 'togglesOn' => array( 'g_create_albums_private', 'g_create_albums_fo', 'g_delete_own_albums', 'g_album_limit', 'g_img_album_limit' ) ) ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'g_create_albums_private', $group->g_create_albums_private, FALSE, array(), NULL, NULL, NULL, 'g_create_albums_private' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'g_create_albums_fo', $group->g_create_albums_fo, FALSE, array(), NULL, NULL, NULL, 'g_create_albums_fo' ) );
		$form->add( new \IPS\Helpers\Form\Number( 'g_album_limit', $group->g_album_limit, FALSE, array( 'unlimited' => 0 ), NULL, NULL, NULL, 'g_album_limit' ) );
		$form->add( new \IPS\Helpers\Form\Number( 'g_img_album_limit', $group->g_img_album_limit, FALSE, array( 'unlimited' => 0 ), NULL, NULL, NULL, 'g_img_album_limit' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'g_delete_own_albums', $group->g_delete_own_albums, FALSE, array(), NULL, NULL, NULL, 'g_delete_own_albums' ) );

		$form->addHeader( 'gallery_restrictions' );
		$form->add( new \IPS\Helpers\Form\YesNo( 'g_movies', $group->g_movies, FALSE, array( 'togglesOn' => array( 'g_movie_size' ) ) ) );
		$form->add( new \IPS\Helpers\Form\Number( 'g_movie_size', $group->g_movie_size, FALSE, array( 'unlimited' => 0 ), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('gallery_suffix_kb'), 'g_movie_size' ) );
		$form->add( new \IPS\Helpers\Form\Number( 'g_max_upload', $group->g_max_upload, FALSE, array( 'unlimited' => 0 ), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('gallery_suffix_kb') ) );

		$form->addMessage( 'gallery_requires_log' );
		$form->add( new \IPS\Helpers\Form\Number( 'g_max_transfer', $group->g_max_transfer, FALSE, array( 'unlimited' => 0 ), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('gallery_suffix_kb_day') ) );
		$form->add( new \IPS\Helpers\Form\Number( 'g_max_views', $group->g_max_views, FALSE, array( 'unlimited' => 0 ), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('gallery_suffix_day') ) );
	}
	
	/**
	 * Save
	 *
	 * @param	array				$values	Values from form
	 * @param	\IPS\Member\Group	$group	The group
	 * @return	void
	 */
	public function save( $values, &$group )
	{
		$group->g_create_albums			= $values['g_create_albums'];
		$group->g_create_albums_private	= $values['g_create_albums_private'];
		$group->g_create_albums_fo		= $values['g_create_albums_fo'];
		$group->g_album_limit			= $values['g_album_limit'];
		$group->g_img_album_limit		= $values['g_img_album_limit'];
		$group->g_delete_own_albums		= $values['g_delete_own_albums'];
		$group->g_movies				= $values['g_movies'];
		$group->g_movie_size			= $values['g_movie_size'];
		$group->g_max_upload			= $values['g_max_upload'];
		$group->g_max_transfer			= $values['g_max_transfer'];
		$group->g_max_views				= $values['g_max_views'];
	}
}