<?php
/**
 * @brief		Editor Extension: Reports
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @since		15 Jul 2013
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\core\extensions\core\EditorLocations;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Editor Extension: Reports
 */
class _Reports
{
	/**
	 * Can we use HTML in this editor?
	 *
	 * @param	\IPS\Member	$member	The member
	 * @return	bool|null	NULL will cause the default value (based on the member's permissions) to be used, and is recommended in most cases. A boolean value will override that.
	 */
	public function canUseHtml( $member )
	{
		return NULL;
	}
	
	/**
	 * Can we use attachments in this editor?
	 *
	 * @param	\IPS\Member					$member	The member
	 * @param	\IPS\Helpers\Form\Editor	$field	The editor field
	 * @return	bool|null	NULL will cause the default value (based on the member's permissions) to be used, and is recommended in most cases. A boolean value will override that.
	 */
	public function canAttach( $member, $field )
	{
		return NULL;
	}
	
	/**
	 * Permission check for attachments
	 *
	 * @param	\IPS\Member	$member	The member
	 * @param	int|null	$id1	Primary ID
	 * @param	int|null	$id2	Secondary ID
	 * @param	string|null	$id3	Arbitrary data
	 * @return	bool
	 */
	public function attachmentPermissionCheck( $member, $id1, $id2, $id3 )
	{
		return $member->modPermission( 'can_view_reports' );
	}
	
	/**
	 * Attachment lookup
	 *
	 * @param	int|null	$id1	Primary ID
	 * @param	int|null	$id2	Secondary ID
	 * @param	string|null	$id3	Arbitrary data
	 * @return	\IPS\Http\Url|\IPS\Content|\IPS\Node\Model
	 * @throws	\LogicException
	 */
	public function attachmentLookup( $id1, $id2, $id3 )
	{
		return \IPS\core\Reports\Report::load( $id1 );
	}

	/**
	 * Rebuild content post-upgrade
	 *
	 * @param	int|null	$offset	Offset to start from
	 * @param	int|null	$max	Maximum to parse
	 * @return	int			Number completed
	 * @note	This method is optional and will only be called if it exists
	 */
	public function rebuildContent( $offset, $max )
	{
		return $this->performRebuild( $offset, $max, array( 'IPS\Text\LegacyParser', 'parseStatic' ) );
	}

	/**
	 * Rebuild attachment images in non-content item areas
	 *
	 * @param	int|null	$offset	Offset to start from
	 * @param	int|null	$max	Maximum to parse
	 * @return	int			Number completed
	 */
	public function rebuildAttachmentImages( $offset, $max )
	{
		return $this->performRebuild( $offset, $max, array( 'IPS\Text\Parser', 'rebuildAttachmentUrls' ) );
	}

	/**
	 * Perform rebuild - abstracted as the call for rebuildContent() and rebuildAttachmentImages() is nearly identical
	 *
	 * @param	int|null	$offset		Offset to start from
	 * @param	int|null	$max		Maximum to parse
	 * @param	callable	$callback	Method to call to rebuild content
	 * @return	int			Number completed
	 */
    protected function performRebuild( $offset, $max, $callback )
    {
        $did	= 0;

        foreach( \IPS\Db::i()->select( '*', 'core_rc_reports', NULL, 'id ASC', array( $offset, $max ) ) as $report )
        {
            $did++;
            
            $update = array();

		    try
		    {
				if( $callback[1] == 'rebuildAttachmentUrls' )
				{
					$update['report'] = \IPS\Text\Parser::rebuildAttachmentUrls( $report['report'], \IPS\Member::load( $report['report_by'] ) );
				}
				elseif( $callback[1] == 'parseStatic' )
				{
					$update['report'] = call_user_func( $callback, $report['report'], \IPS\Member::load( $report['report_by'] ), FALSE, 'core_Reports', $report['id'], NULL, NULL );
				}
		    }
		    catch( \InvalidArgumentException $e )
		    {
		        if( $callback[1] == 'parseStatic' AND $e->getcode() == 103014 )
		        {
		            $update['report'] = preg_replace( "#\[/?([^\]]+?)\]#", '', $report['report'] );
		        }
		        else
		        {
		            throw $e;
		        }
			}

            if( count( $update ) )
            {
                \IPS\Db::i()->update( 'core_rc_reports', $update, array( 'id=?', $report['id'] ) );
            }
        }

        return $did;
    }
}