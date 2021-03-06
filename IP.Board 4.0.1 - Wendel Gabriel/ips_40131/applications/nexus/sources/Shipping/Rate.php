<?php
/**
 * @brief		Shipping Rate Interface
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @subpackage	Nexus
 * @since		19 Mar 2014
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\nexus\Shipping;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Shipping Rate Interface
 */
interface Rate
{	
	/**
	 * Is available to destination?
	 *
	 * @param	\IPS\GeoLocation	$destination	Desired destination
	 * @return	bool
	 */
	public function isAvailable( \IPS\GeoLocation $destination );
	
	/**
	 * Name
	 *
	 * @return	string
	 */
	public function getName();
	
	/**
	 * Price
	 *
	 * @param	array					$items		Items
	 * @param	string					$currency	Desired currency
	 * @param	\IPS\nexus\Invoice|NULL	$invoice	The invoice
	 * @return	\IPS\nexus\Money
	 */
	public function getPrice( array $items, $currency, \IPS\nexus\Invoice $invoice = NULL );
	
	/**
	 * Tax
	 *
	 * @return	\IPS\nexus\Tax|NULL
	 */
	public function getTax();
	
	/**
	 * Estimated delivery date
	 *
	 * @param	array					$items		Items
	 * @param	\IPS\nexus\Invoice|NULL	$invoice	The invoice
	 * @return	string
	 */
	public function getEstimatedDelivery( array $items, \IPS\nexus\Invoice $invoice = NULL );
}