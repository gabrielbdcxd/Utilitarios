<?php
/**
 * @brief		Community Enhancements: EasyPost
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @subpackage	Nexus
 * @since		18 Jun 2014
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
 * Community Enhancement: EasyPost
 */
class _EasyPost
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
	public $icon	= "easypost.png";
	
	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->enabled = (bool) \IPS\Settings::i()->easypost_api_key;
	}
	
	/**
	 * Edit
	 *
	 * @return	void
	 */
	public function edit()
	{
		if ( isset( \IPS\Request::i()->test ) )
		{
			return $this->_test();
		}
			
		$form = new \IPS\Helpers\Form;		
		$form->add( new \IPS\Helpers\Form\YesNo( 'easypost_enable', (bool) \IPS\Settings::i()->easypost_api_key, FALSE, array( 'togglesOn' => array( 'easypost_api_key', 'easypost_phone', 'easypost_show_rates' ) ) ) );
		$form->add( new \IPS\Helpers\Form\Text( 'easypost_api_key', \IPS\Settings::i()->easypost_api_key, FALSE, array(), NULL, NULL, NULL, 'easypost_api_key' ) );
		$form->add( new \IPS\Helpers\Form\Tel( 'easypost_phone', \IPS\Settings::i()->easypost_phone, FALSE, array(), NULL, NULL, NULL, 'easypost_phone' ) );
		$form->add( new \IPS\Helpers\Form\Radio( 'easypost_show_rates', \IPS\Settings::i()->easypost_show_rates, FALSE, array(
			'options'	=> array( 1 => 'easypost_show_rates_1', 0 => 'easypost_show_rates_0' ),
			'toggles'	=> array( 1 => array( 'easypost_tax', 'easypost_price_adjustment', 'easypost_delivery_adjustment' ) )
		), NULL, NULL, NULL, 'easypost_show_rates' ) );
		$form->add( new \IPS\Helpers\Form\Node( 'easypost_tax', \IPS\Settings::i()->easypost_tax, FALSE, array( 'class' => '\IPS\nexus\Tax', 'zeroVal' => 'do_not_tax' ), NULL, NULL, NULL, 'easypost_tax' ) );
		$form->add( new \IPS\nexus\Form\Money( 'easypost_price_adjustment', \IPS\Settings::i()->easypost_price_adjustment, FALSE, array(), NULL, NULL, NULL, 'easypost_price_adjustment' ) );
		$form->add( new \IPS\Helpers\Form\Number( 'easypost_delivery_adjustment', \IPS\Settings::i()->easypost_delivery_adjustment, FALSE, array(), NULL, NULL, \IPS\Member::loggedIn()->language()->addToStack('days'), 'easypost_delivery_adjustment' ) );
		if ( $values = $form->values() )
		{
			$values['easypost_tax'] = $values['easypost_tax'] ? $values['easypost_tax']->_id : 0;
			$values['easypost_price_adjustment'] = json_encode( $values['easypost_price_adjustment'] );
			try
			{
				if ( $values['easypost_enable'] )
				{
					unset( $values['easypost_enable'] );
					$this->testSettings( $values['easypost_api_key'] );
					$form->saveAsSettings( $values );
				}
				else
				{
					unset( $values['easypost_enable'] );
					$values['easypost_api_key'] = '';
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
				'link'		=> \IPS\Http\Url::ips( 'docs/easypost' ),
				'target'	=> '_blank'
			),
			'test'	=> array(
				'title'		=> 'easypost_test',
				'icon'		=> 'cog',
				'link'		=> \IPS\Http\Url::internal( 'app=core&module=applications&controller=enhancements&do=edit&id=nexus_EasyPost&test=1' ),
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
			\IPS\Db::i()->update( 'core_sys_conf_settings', array( 'conf_value' => '' ), array( 'conf_key=?', 'easypost_api_key' ) );
			unset( \IPS\Data\Store::i()->settings );
		}
	}
	
	/**
	 * Test Settings (on save to check if API key is valid)
	 *
	 * @param	string|NULL	$key	Key to check
	 * @return	void
	 * @throws	\LogicException
	 */
	protected function testSettings( $key=NULL )
	{
		$response = \IPS\Http\Url::external( 'https://api.easypost.com/v2/addresses' )->request()->login( $key, '' )->get();
		if ( $response->httpResponseCode != 200 )
		{
			throw new \LogicException( \IPS\Member::loggedIn()->language()->addToStack('easypost_bad_api_key') );
		}
	}
	
	/**
	 * Test Settings (full test)
	 *
	 * @return	void
	 */
	protected function _test()
	{
		$form = new \IPS\Helpers\Form;
		$form->addMessage( 'easypost_test_blurb' );
		$form->add( new \IPS\Helpers\Form\Radio( 'easypost_test_method', \IPS\Settings::i()->easypost_show_rates, TRUE, array(
			'options'	=> array( 1 => 'easypost_test_method_1', 0 => 'easypost_test_method_0' ),
			'toggles'	=> array(
				1			=> array( 'easypost_test_products' ),
				0			=> array( 'easypost_test_length', 'easypost_test_width', 'easypost_test_height', 'easypost_test_weight' )
			)
		) ) );
		$form->add( new \IPS\Helpers\Form\Node( 'easypost_test_products', NULL, NULL, array( 'class' => 'IPS\nexus\Package\Group', 'multiple' => TRUE, 'permissionCheck' => function( $node )
		{
			if ( !( $node instanceof \IPS\nexus\Package\Product ) or !$node->physical )
			{
				return FALSE;
			}
			return TRUE;
		} ), function( $val )
		{
			if (  \IPS\Request::i()->easypost_test_method )
			{
				if ( empty( $val ) )
				{
					throw new \DomainException('form_required');
				}
				foreach ( $val as $package )
				{
					if ( !$package->physical )
					{
						throw new \DomainException( \IPS\Member::loggedIn()->language()->addToStack( 'easypost_test_not_physical', FALSE, array( 'sprintf' => array( $package->_title ) ) ) );
					}
					foreach ( array( 'width', 'height', 'length' ) as $k )
					{
						if ( !$package->$k )
						{
							throw new \DomainException( \IPS\Member::loggedIn()->language()->addToStack( 'easypost_test_no_' . $k, FALSE, array( 'sprintf' => array( $package->_title ) ) ) );
						}
					}
				}
			}
		}, NULL, NULL, 'easypost_test_products' ) );
		$form->add( new \IPS\nexus\Form\Length( 'easypost_test_length', NULL, NULL, array(), NULL, NULL, NULL, 'easypost_test_length' ) );
		$form->add( new \IPS\nexus\Form\Length( 'easypost_test_width', NULL, NULL, array(), NULL, NULL, NULL, 'easypost_test_width' ) );
		$form->add( new \IPS\nexus\Form\Length( 'easypost_test_height', NULL, NULL, array(), NULL, NULL, NULL, 'easypost_test_height' ) );
		$form->add( new \IPS\nexus\Form\Weight( 'easypost_test_weight', NULL, NULL, array(), NULL, NULL, NULL, 'easypost_test_weight' ) );
		$form->add( new \IPS\Helpers\Form\Member( 'easypost_test_customer', \IPS\Member::loggedIn(), TRUE ) );
		$form->add( new \IPS\Helpers\Form\Address( 'easypost_test_address', NULL, TRUE ) );
		
		if ( $values = $form->values() )
		{
			if ( $values['easypost_test_method'] )
			{
				$lengthInInches = 0;
				$widthInInches = 0;
				$heightInInches = 0;
				$weightInOz = 0;
				foreach ( $values['easypost_test_products'] as $package )
				{
					$weight = new \IPS\nexus\Shipping\Weight( $package->weight );
					$weightInOz += $weight->float('oz');
					
					foreach ( array( 'length', 'width', 'height' ) as $k )
					{
						$length = new \IPS\nexus\Shipping\Length( $package->$k );
						
						$v = "{$k}InInches";
						$$v += $length->float('in');
					}
				}
			}
			else
			{
				$lengthInInches = $values['easypost_test_length']->float('in');
				$widthInInches = $values['easypost_test_width']->float('in');
				$heightInInches = $values['easypost_test_height']->float('in');
				$weightInOz = $values['easypost_test_weight']->float('oz');
			}
			
			$customer = \IPS\nexus\Customer::load( $values['easypost_test_customer']->member_id );
			
			try
			{
				$rates = \IPS\nexus\Shipping\EasyPostRate::getRates( $lengthInInches, $widthInInches, $heightInInches, $weightInOz, $customer, $values['easypost_test_address'], $customer->defaultCurrency() );
				if ( isset( $rates['error'] ) )
				{
					\IPS\Output::i()->error( $rates['error']['message'], $rates['error']['code'] );
				}
			}
			catch ( \Exception $e )
			{
				\IPS\Output::i()->error( $e->getMessage(), $e->getCode() );
			}
			
			\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'shiporders', 'nexus' )->easypostTest( $rates );
			return;
		}
		
		\IPS\Output::i()->output = $form;
	}
}