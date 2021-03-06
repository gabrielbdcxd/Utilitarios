<?php
/**
 * @brief		Designer's Mode Theme
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @since		28 Nov 2014
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\cms\Theme\Advanced;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Designer's Mode Theme
 */
class _Theme extends \IPS\cms\Theme
{
	/**
	 * Get currently logged in member's theme
	 *
	 * @return	\IPS\Theme
	 */
	public static function i()
	{
		return new self;
	}
	
	/**
	 * Write files to disk
	 *
	 * return void
	 */
	public static function export()
	{
		foreach( \IPS\Db::i()->select(
			"*, MD5( CONCAT(template_location, ',', template_group, ',', template_title) ) as bit_key",
			'cms_templates',
			NULL,
			'template_user_edited ASC'  /* Ensure we get edited versions, not the master version if one exists */
		)->setKeyField('bit_key') as $template )
		{
			try
			{
				static::writeTemplate( $template, TRUE );
			}
			catch( \RuntimeException $ex )
			{
				throw new \RuntimeException( $ex->getMessage() );
			}
		}
	}
	
	/**
	 * Imports templates from the designer's mode directories.
	 *
	 * @return	void
	 */
	public static function import()
	{
		$seen = array();
		
		foreach( array( 'block', 'page', 'database', 'js', 'css' ) as $location )
		{
			$templates = iterator_to_array( \IPS\Db::i()->select(
					"*, MD5( CONCAT(template_location, ',', template_group, ',', template_title) ) as bit_key",
					'cms_templates',
					array( 'template_location=?', $location ),
					'template_user_edited ASC'  /* Ensure we get edited versions, not the master version if one exists */
				)->setKeyField('bit_key') );
		
			$master = iterator_to_array( \IPS\Db::i()->select(
					"*, MD5( CONCAT(template_location, ',', template_group, ',', template_title) ) as bit_key",
					'cms_templates',
					array( 'template_master=1 and template_location=?', $location )
				)->setKeyField('bit_key') );
				
			$path = static::_getHtmlPath( 'cms', $location );

			if ( is_dir( $path ) )
			{
				foreach( new \DirectoryIterator( $path ) as $group )
				{
					if ( $group->isDot() || mb_substr( $group->getFilename(), 0, 1 ) === '.' )
					{
						continue;
					}

					if ( $group->isDir() )
					{
						$seen = array();

						foreach( new \DirectoryIterator( $path . '/' . $group->getFilename() ) as $file )
						{
							if ( $file->isDot() || mb_substr( $file->getFilename(), -6 ) !== '.phtml')
							{
								continue;
							}

							/* Get the content */
							$html = file_get_contents( $path . '/' . $group->getFilename() . '/' . $file->getFilename() );
	
							/* Parse the header tag */
							preg_match( '/^<ips:template parameters="(.+?)?"([^>]+?)>(\r\n?|\n)/', $html, $params );
	
							/* Strip it */
							$html = ( isset($params[0]) ) ? str_replace( $params[0], '', $html ) : $html;
							$title = str_replace( '.phtml', '', $file->getFilename() );
							$originalGroup = $group->getFilename();

							if ( isset( $params[2] ) and mb_stristr( $params[2], 'original_group' ) )
							{
								preg_match( '#original_group="(.+?)"#', $params[2], $submatches );

								if ( isset( $submatches[1] ) )
								{
									$originalGroup = $submatches[1];
								}
							}

							/* If we're syncing designer mode, check for actual changes */
							$key = md5( $location . ',' . $group->getFilename() . ',' . $title );

							$seen[] = $title;
							$added  = FALSE;
							
							if ( isset( $templates[ $key ] ) )
							{
								if ( md5( trim( $templates[ $key ]['template_content'] ) ) == md5( trim( $html ) ) )
								{
									continue;
								}
								else
								{
									/* Update */
									if ( ! $templates[ $key ]['template_master'] )
									{
										$added = TRUE;
										\IPS\Db::i()->update( 'cms_templates', array(
											'template_content' => $html,
											'template_params'  => ( isset($params[1]) ) ? $params[1] : '',
										), array( 'template_id=?', $templates[ $key ]['template_id'] ) );
									}
								}
							}
							
							if ( ! $added )
							{
								$templateType = 'template';

								if ( $location === 'css' )
								{
									$templateType = 'css';
								}
								else if ( $location === 'js' )
								{
									$templateType = 'js';
								}
									
								/* New template */
								\IPS\Db::i()->insert( 'cms_templates', array(
									'template_key'            => $location . '_' . $group->getFilename() . '_' . $title,
									'template_title'	      => $title,
									'template_desc'		      => '',
									'template_content'        => $html,
									'template_location'       => $location,
									'template_group'          => $group->getFilename(),
									'template_original_group' => $originalGroup,
									'template_container'      => 0,
									'template_params'	      => ( isset($params[1]) ) ? $params[1] : '',
									'template_master'         => 0,
									'template_user_edited'    => ( isset( $master[ $key ] ) ) ? 1 : 0,
								    'template_user_created'   => ( isset( $master[ $key ] ) ) ? 0 : 1,
								    'template_type'           => $templateType
								) );
							}

							/* remove compiled version */
							$key = \strtolower( 'template_cms_' .static::makeBuiltTemplateLookupHash( 'cms', $location, $group->getFilename() ) . '_' . static::cleanGroupName( $group->getFilename() ) );

							if ( isset( \IPS\Data\Store::i()->$key ) )
							{
								unset(\IPS\Data\Store::i()->$key);
							}
						}
					}
					
					/* Remove any templates we've not imported for this location/group */
					if ( count( $seen ) )
					{
						\IPS\Db::i()->delete( 'cms_templates', array('template_master=0 and template_location=? and template_group=? and ' . \IPS\Db::i()->in( 'template_title', $seen, TRUE ), $location, $group->getFilename() ) );
					}
				}
			}
		}
	}
	
	/**
	 * Returns the path for the IN_DEV .phtml files
	 * @param string 	 	  $app			Application Key
	 * @param string|null	  $location		Location
	 * @param string|null 	  $path			Path or Filename
	 * @return string
	 */
	protected static function _getHtmlPath( $app, $location=null, $path=null )
	{
		return rtrim( \IPS\ROOT_PATH . "/themes/cms/{$location}/{$path}", '/' ) . '/';
	}
}