<?php
/**
 * @brief		Community Enhancements: MaxMind
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @subpackage	Nexus
 * @since		07 Mar 2014
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\nexus\extensions\core\CommunityEnhancements;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Community Enhancement
 */
class _MaxMind
{
	/**
	 * @brief	Enhancement is enabled?
	 */
	public $enabled	= FALSE;

	/**
	 * @brief	IPS-provided enhancement?
	 */
	public $ips	= FALSE;

	/**
	 * @brief	Enhancement has configuration options?
	 */
	public $hasOptions	= TRUE;

	/**
	 * @brief	Icon data
	 */
	public $icon	= "maxmind.png";
	
	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->enabled = (bool) \IPS\Settings::i()->maxmind_key;
	}
	
	/**
	 * Edit
	 *
	 * @return	void
	 */
	public function edit()
	{
		$form = new \IPS\Helpers\Form;		
		$form->add( new \IPS\Helpers\Form\YesNo( 'maxmind_enable', (bool) \IPS\Settings::i()->maxmind_key, FALSE, array( 'togglesOn' => array( 'maxmind_key', 'maxmind_error' ) ) ) );
		$form->add( new \IPS\Helpers\Form\Text( 'maxmind_key', \IPS\Settings::i()->maxmind_key, FALSE, array(), NULL, NULL, NULL, 'maxmind_key' ) );
		$form->add( new \IPS\Helpers\Form\Radio( 'maxmind_error', \IPS\Settings::i()->maxmind_error, FALSE, array( 'options' => array( 'okay' => 'maxmind_error_okay', 'hold' => 'maxmind_error_hold' ) ), NULL, NULL, NULL, 'maxmind_error' ) );
		if ( $values = $form->values() )
		{
			try
			{
				if ( $values['maxmind_enable'] )
				{
					unset( $values['maxmind_enable'] );
					$this->testSettings( $values['maxmind_key'] );
					$form->saveAsSettings( $values );
				}
				else
				{
					unset( $values['maxmind_enable'] );
					$values['maxmind_key'] = '';
					$form->saveAsSettings( $values );
				}
				
				\IPS\Output::i()->inlineMessage	= \IPS\Member::loggedIn()->language()->addToStack('saved');
			}
			catch ( \LogicException $e )
			{
				$form->error = $e->getMessage();
			}
		}
		
		\IPS\Output::i()->sidebar['actions'] = array(
			'help'	=> array(
				'title'		=> 'learn_more',
				'icon'		=> 'question-circle',
				'link'		=> \IPS\Http\Url::ips( 'docs/maxmind' ),
				'target'	=> '_blank'
			),
		);
				
		\IPS\Output::i()->output = $form;
	}
	
	/**
	 * Enable/Disable
	 *
	 * @param	$enabled	bool	Enable/Disable
	 * @return	void
	 * @throws	\LogicException
	 */
	public function toggle( $enabled )
	{
		if ( $enabled )
		{
			throw new \LogicException;
		}
		else
		{	
			\IPS\Db::i()->update( 'core_sys_conf_settings', array( 'conf_value' => '' ), array( 'conf_key=?', 'maxmind_key' ) );
			unset( \IPS\Data\Store::i()->settings );
		}
	}
	
	/**
	 * Test Settings
	 *
	 * @param	string|NULL	$key	Key to check
	 * @return	void
	 * @throws	\LogicException
	 */
	protected function testSettings( $key=NULL )
	{
		$testAddress = new \IPS\GeoLocation;
		$testAddress->addressLines = array( 'Invision Power Services, Inc.', 'PO Box 2365' );
		$testAddress->city = 'Forest';
		$testAddress->region = 'VA';
		$testAddress->country = 'US';
		$testAddress->postalCode = '24551';
		
		$maxMind = new \IPS\nexus\Fraud\MaxMind\Request( FALSE, $key );
		$maxMind->setIpAddress( \IPS\Request::i()->ipAddress() );
		$maxMind->setBillingAddress( $testAddress );
		$maxMind = $maxMind->request();
		if ( $maxMind->err )
		{
			throw new \LogicException( $maxMind->err );
		}
	}
}