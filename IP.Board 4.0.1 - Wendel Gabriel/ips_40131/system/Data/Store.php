<?php
/**
 * @brief		Abstract Storage Class
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @since		07 May 2013
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\Data;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Abstract Storage Class
 */
abstract class _Store extends AbstractData
{
	/**
	 * @brief	Instance
	 */
	protected static $instance;
	
	/**
	 * Get instance
	 *
	 * @return	\IPS\Data\Store
	 */
	public static function i()
	{
		if( self::$instance === NULL )
		{
			$classname = 'IPS\Data\Store\\' . \IPS\STORE_METHOD;
			self::$instance = new $classname( json_decode( \IPS\STORE_CONFIG, TRUE ) );
		}
		
		return self::$instance;
	}
	
	/**
	 * @brief	Always needed Store keys
	 */
	public $initLoad = array();
	
	/**
	 * @brief	Template store keys
	 */
	public $templateLoad = array();
	
	/**
	 * @brief	Log
	 */
	public $log	= array();
		
	/**
	 * Load mutiple
	 * Used so if it is known that several are going to be needed, they can all be loaded into memory at the same time
	 *
	 * @param	array	$keys	Keys
	 * @return	void
	 */
	public function loadIntoMemory( array $keys )
	{
		
	}
	
	/**
	 * Magic Method: Get
	 *
	 * @param	string	$key	Key
	 * @return	string	Value from the _datastore
	 */
	public function __get( $key )
	{
		if( \IPS\CACHE_METHOD !== 'None' )
		{
			if( isset( $this->cacheKeys ) )
			{
				$cacheKeys = $this->cacheKeys;

				if( !is_array( $cacheKeys ) )
				{
					$cacheKeys = array();
				}
			}
			else
			{
				$cacheKeys = array();
			}
		}

		try
		{
			if ( \IPS\CACHE_METHOD !== 'None' and $key !== 'cacheKeys' and isset( $cacheKeys[ $key ] ) and isset( \IPS\Data\Cache::i()->$key ) )
			{
				$value = \IPS\Data\Cache::i()->$key;
				if ( $cacheKeys[ $key ] === md5( json_encode( $value ) ) )
				{
					return $value;
				}				
			}
			throw new \OutOfRangeException;
		}
		catch ( \OutOfRangeException $e )
		{
			$value = parent::__get( $key );
			
			if ( \IPS\CACHE_METHOD !== 'None' and $key !== 'cacheKeys' )
			{
				\IPS\Data\Cache::i()->$key = $value;

				$cacheKeys[ $key ] = md5( json_encode( $value ) );
				$this->cacheKeys = $cacheKeys;
			}
			
			return $value;
		}
	}

	/**
	 * Magic Method: Set
	 *
	 * @param	string	$key	Key
	 * @param	string	$value	Value
	 * @return	void
	 */
	public function __set( $key, $value )
	{
		parent::__set( $key, $value );
		
		if ( \IPS\CACHE_METHOD !== 'None' and $key !== 'cacheKeys' )
		{
			\IPS\Data\Cache::i()->$key = $value;
		
			if ( isset( $this->cacheKeys ) )
			{
				$cacheKeys = is_array( $this->cacheKeys ) ? $this->cacheKeys : array();
				$cacheKeys[ $key ] = md5( json_encode( $value ) );
				$this->cacheKeys = $cacheKeys;
			}
			else
			{
				$this->cacheKeys = array( $key => md5( json_encode( $value ) ) );
			}
		}
	}
	
	/**
	 * Magic Method: Isset
	 *
	 * @param	string	$key	Key
	 * @return	bool
	 */
	public function __isset( $key )
	{
		if ( \IPS\CACHE_METHOD !== 'None' and $key !== 'cacheKeys' and isset( $this->cacheKeys ) and isset( $this->cacheKeys[ $key ] ) and isset( \IPS\Data\Cache::i()->$key ) )
		{
			try
			{
				$value = \IPS\Data\Cache::i()->$key;
				if ( $this->cacheKeys[ $key ] === md5( json_encode( $value ) ) )
				{
					return TRUE;
				}	
			}
			catch ( \OutOfRangeException $e ) { }
		}
		
		return parent::__isset( $key );
	}
		
	/**
	 * Magic Method: Unset
	 *
	 * @param	string	$key	Key
	 * @return	void
	 */
	public function __unset( $key )
	{
		parent::__unset( $key );
		
		if ( \IPS\CACHE_METHOD !== 'None' and $key !== 'cacheKeys' )
		{
			unset( \IPS\Data\Cache::i()->$key );
			
			if ( isset( $this->cacheKeys ) )
			{
				$cacheKeys = is_array( $this->cacheKeys ) ? $this->cacheKeys : array();

				if( isset( $cacheKeys[ $key ] ) )
				{
					unset( $cacheKeys[ $key ] );
					$this->cacheKeys = $cacheKeys;
				}
			}
		}
	}
}