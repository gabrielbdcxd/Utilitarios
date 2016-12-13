<?php
/**
 * @brief		Settings
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @subpackage	Gallery
 * @since		04 Mar 2014
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\gallery\modules\admin\gallery;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * settings
 */
class _settings extends \IPS\Dispatcher\Controller
{
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'settings_manage' );
		parent::execute();
	}

	/**
	 * Manage settings
	 *
	 * @return	void
	 */
	protected function manage()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'settings_manage' );
		
		$form = new \IPS\Helpers\Form;

		$form->addHeader( 'gallery_images' );
		$form->addMessage( \IPS\Member::loggedIn()->language()->addToStack('gallery_dims_explanation'), '', FALSE );
		$large	= ( isset( \IPS\Settings::i()->gallery_large_dims ) ) ? explode( 'x', \IPS\Settings::i()->gallery_large_dims ) : array( 1600, 1200 );
		$medium	= ( isset( \IPS\Settings::i()->gallery_medium_dims ) ) ? explode( 'x', \IPS\Settings::i()->gallery_medium_dims ) : array( 640, 480 );
		$small	= ( isset( \IPS\Settings::i()->gallery_small_dims ) ) ? explode( 'x', \IPS\Settings::i()->gallery_small_dims ) : array( 240, 240 );
		$thumb	= ( isset( \IPS\Settings::i()->gallery_thumb_dims ) ) ? explode( 'x', \IPS\Settings::i()->gallery_thumb_dims ) : array( 100, 100 );
		$form->add( new \IPS\Helpers\Form\WidthHeight( 'gallery_large_dims', $large, TRUE, array( 'resizableDiv' => FALSE ) ) );
		$form->add( new \IPS\Helpers\Form\WidthHeight( 'gallery_medium_dims', $medium, TRUE, array( 'resizableDiv' => FALSE ) ) );
		$form->add( new \IPS\Helpers\Form\WidthHeight( 'gallery_small_dims', $small, TRUE, array( 'resizableDiv' => FALSE ) ) );
		$form->add( new \IPS\Helpers\Form\WidthHeight( 'gallery_thumb_dims', $thumb, TRUE, array( 'resizableDiv' => FALSE ) ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'gallery_use_square_thumbnails', \IPS\Settings::i()->gallery_use_square_thumbnails ) );
		$form->add( new \IPS\Helpers\Form\Upload( 'gallery_watermark_path', \IPS\Settings::i()->gallery_watermark_path ? \IPS\File::get( 'core_Theme', \IPS\Settings::i()->gallery_watermark_path ) : NULL, FALSE, array( 'image' => TRUE, 'storageExtension' => 'core_Theme' ) ) );

		$form->addHeader( 'gallery_bandwidth' );
		$form->add( new \IPS\Helpers\Form\YesNo( 'gallery_detailed_bandwidth', \IPS\Settings::i()->gallery_detailed_bandwidth ) );
		$form->add( new \IPS\Helpers\Form\Number( 'gallery_bandwidth_period', \IPS\Settings::i()->gallery_bandwidth_period, FALSE, array( 'unlimited' => -1 ) ) );

		$form->addHeader( 'gallery_options' );
		$form->add( new \IPS\Helpers\Form\YesNo( 'gallery_rss_enabled', \IPS\Settings::i()->gallery_rss_enabled ) );

		if ( $values = $form->values() )
		{
			$form->saveAsSettings( array( 
				'gallery_large_dims'			=> implode( 'x', $values['gallery_large_dims'] ), 
				'gallery_medium_dims'			=> implode( 'x', $values['gallery_medium_dims'] ),
				'gallery_small_dims'			=> implode( 'x', $values['gallery_small_dims'] ),
				'gallery_thumb_dims'			=> implode( 'x', $values['gallery_thumb_dims'] ),
				'gallery_use_square_thumbnails'	=> $values['gallery_use_square_thumbnails'],
				'gallery_watermark_path'		=> $values['gallery_watermark_path'],
				'gallery_detailed_bandwidth'	=> $values['gallery_detailed_bandwidth'],
				'gallery_bandwidth_period'		=> $values['gallery_bandwidth_period'],
				'gallery_rss_enabled'			=> $values['gallery_rss_enabled'],
			) );
			\IPS\Session::i()->log( 'acplogs__gallery_settings' );
		}
		
		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('settings');
		\IPS\Output::i()->output = $form;
	}

	/**
	 * Rebuild gallery images
	 *
	 * @return	void
	 */
	protected function rebuildImages()
	{
		$perGo = 5;

		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('gallery_rebuildimages');
		\IPS\Output::i()->output = new \IPS\Helpers\MultipleRedirect( \IPS\Http\Url::internal( 'app=gallery&module=gallery&controller=settings&do=rebuildImages' ), function( $doneSoFar ) use( $perGo )
		{
			$select = \IPS\Db::i()->select( '*', 'gallery_images', array( 'image_media=?', 0 ), 'image_id', array( $doneSoFar, $perGo ), NULL, NULL, \IPS\Db::SELECT_SQL_CALC_FOUND_ROWS );
			$total	= $select->count( TRUE );

			if ( !$select->count() )
			{
				return NULL;
			}
						
			foreach ( $select as $row )
			{
				try
				{
					$image	= \IPS\gallery\Image::constructFromData( $row );
					$image->buildThumbnails();
					$image->save();
				}
				catch ( \Exception $e ) {}
			}

			$doneSoFar += $perGo;
			return array( $doneSoFar, \IPS\Member::loggedIn()->language()->addToStack('rebuilding'), ( 100 * $doneSoFar ) / $total );			
			
		}, function()
		{
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=gallery&module=gallery&controller=settings' ), 'completed' );
		} );
	}
}