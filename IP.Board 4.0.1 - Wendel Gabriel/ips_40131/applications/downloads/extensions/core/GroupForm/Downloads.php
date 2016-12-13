<?php
/**
 * @brief		Admin CP Group Form
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @subpackage
 * @since		19 Nov 2013
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\downloads\extensions\core\GroupForm;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Admin CP Group Form
 */
class _Downloads
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
		$restrictions = $group->idm_restrictions ? json_decode( $group->idm_restrictions, TRUE ) : array();

		$form->addHeader( 'file_management' );
		$form->add( new \IPS\Helpers\Form\YesNo( 'idm_view_downloads', $group->idm_view_downloads ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'idm_view_approvers', $group->idm_view_approvers ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'idm_bypass_revision', $group->idm_bypass_revision ) );
				
		$form->addHeader( 'submission_permissions' );
		if ( \IPS\Application::appIsEnabled( 'nexus' ) )
		{
			if ( \IPS\Settings::i()->idm_nexus_on )
			{
				$form->add( new \IPS\Helpers\Form\YesNo( 'idm_add_paid', $group->idm_add_paid ) );
			}
			else
			{
				\IPS\Member::loggedIn()->language()->words['idm_add_paid_desc'] = \IPS\Member::loggedIn()->language()->addToStack( 'idm_add_paid_enable', FALSE );
				$form->add( new \IPS\Helpers\Form\YesNo( 'idm_add_paid', FALSE, FALSE, array( 'disabled' => TRUE ) ) );
			}
		}
		$form->add( new \IPS\Helpers\Form\YesNo( 'idm_bulk_submit', $group->idm_bulk_submit ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'idm_linked_files', $group->idm_linked_files ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'idm_import_files', $group->idm_import_files ) );
		
		$form->addHeader( 'download_restrictions' );
		if( $group->g_id != \IPS\Settings::i()->guest_group )
		{
			$form->add( new \IPS\Helpers\Form\Number( 'min_posts', isset( $restrictions['min_posts'] ) ? $restrictions['min_posts'] : 0, FALSE, array( 'unlimited' => 0, 'unlimitedLang' => 'idm_throttling_unlimited' ), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('approved_posts_comments') ) );
		}
		$form->add( new \IPS\Helpers\Form\Number( 'idm_throttling', $group->idm_throttling, FALSE, array( 'unlimited' => 0, 'unlimitedLang' => 'idm_throttling_unlimited' ), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('idm_throttling_suffix') ) );
		$form->add( new \IPS\Helpers\Form\Number( 'idm_wait_period', $group->idm_wait_period, FALSE, array(), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('seconds') ) );
		$form->add( new \IPS\Helpers\Form\Number( 'limit_sim', isset( $restrictions['limit_sim'] ) ? $restrictions['limit_sim'] : 0, FALSE, array( 'unlimited' => 0 ) ) );
		if ( \IPS\Application::appIsEnabled( 'nexus' ) and \IPS\Settings::i()->idm_nexus_on )
		{
			$form->add( new \IPS\Helpers\Form\YesNo( 'idm_bypass_paid', $group->idm_bypass_paid ) );
		}
		
		$form->addHeader( 'bandwidth_limits' );
		$form->addMessage( 'downloads_requires_log' );
		$form->add( new \IPS\Helpers\Form\Number( 'daily_bw', isset( $restrictions['daily_bw'] ) ? $restrictions['daily_bw'] : 0, FALSE, array( 'unlimited' => 0 ), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('kb_per_day') ) );
		$form->add( new \IPS\Helpers\Form\Number( 'weekly_bw', isset( $restrictions['weekly_bw'] ) ? $restrictions['weekly_bw'] : 0, FALSE, array( 'unlimited' => 0 ), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('kb_per_week') ) );
		$form->add( new \IPS\Helpers\Form\Number( 'monthly_bw', isset( $restrictions['monthly_bw'] ) ? $restrictions['monthly_bw'] : 0, FALSE, array( 'unlimited' => 0 ), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('kb_per_month') ) );
		$form->addHeader( 'download_limits' );
		$form->addMessage( 'downloads_requires_log' );
		$form->add( new \IPS\Helpers\Form\Number( 'daily_dl', isset( $restrictions['daily_dl'] ) ? $restrictions['daily_dl'] : 0, FALSE, array( 'unlimited' => 0 ), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('downloads_per_day') ) );
		$form->add( new \IPS\Helpers\Form\Number( 'weekly_dl', isset( $restrictions['weekly_dl'] ) ? $restrictions['weekly_dl'] : 0, FALSE, array( 'unlimited' => 0 ), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('downloads_per_week') ) );
		$form->add( new \IPS\Helpers\Form\Number( 'monthly_dl', isset( $restrictions['monthly_dl'] ) ? $restrictions['monthly_dl'] : 0, FALSE, array( 'unlimited' => 0 ), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('downloads_per_month') ) );

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
		$group->idm_view_approvers = $values['idm_view_approvers'];
		$group->idm_bypass_revision = $values['idm_bypass_revision'];
		$group->idm_view_downloads = $values['idm_view_downloads'];
		$group->idm_throttling = $values['idm_throttling'];
		$group->idm_wait_period = $values['idm_wait_period'];
		$group->idm_linked_files = $values['idm_linked_files'];
		$group->idm_import_files = $values['idm_import_files'];
		$group->idm_bulk_submit = $values['idm_bulk_submit'];
		
		if ( \IPS\Application::appIsEnabled( 'nexus' ) and \IPS\Settings::i()->idm_nexus_on )
		{
			$group->idm_add_paid = $values['idm_add_paid'];
			$group->idm_bypass_paid = $values['idm_bypass_paid'];
		}
		
		$restrictions = array();
		foreach ( array( 'limit_sim', 'daily_bw', 'weekly_bw', 'monthly_bw', 'daily_dl', 'weekly_dl', 'monthly_dl' ) as $k )
		{
			$restrictions[ $k ] = $values[ $k ];
		}
		
		if( $group->g_id != \IPS\Settings::i()->guest_group )
		{
			$restrictions['min_posts'] = $values['min_posts'];
		}
		
		$group->idm_restrictions = json_encode( $restrictions );
	}
}