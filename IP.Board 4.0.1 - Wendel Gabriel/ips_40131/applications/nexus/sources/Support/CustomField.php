<?php
/**
 * @brief		Custom Support Field Node
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @since		16 Apr 2013
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\nexus\Support;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Custom Profile Field Node
 */
class _CustomField extends \IPS\CustomField
{
	/**
	 * @brief	[ActiveRecord] Multiton Store
	 */
	protected static $multitons;
	
	/**
	 * @brief	[ActiveRecord] Database Table
	 */
	public static $databaseTable = 'nexus_support_fields';
	
	/**
	 * @brief	[ActiveRecord] Database Prefix
	 */
	public static $databasePrefix = 'sf_';
		
	/**
	 * @brief	[Node] Order Database Column
	 */
	public static $databaseColumnOrder = 'position';
	
	/**
	 * @brief	[CustomField] Title/Description lang prefix
	 */
	protected static $langKey = 'nexus_cfield';
	
	/**
	 * @brief	[Node] ACP Restrictions
	 * @code
	 	array(
	 		'app'		=> 'core',				// The application key which holds the restrictrions
	 		'module'	=> 'foo',				// The module key which holds the restrictions
	 		'map'		=> array(				// [Optional] The key for each restriction - can alternatively use "prefix"
	 			'add'			=> 'foo_add',
	 			'edit'			=> 'foo_edit',
	 			'permissions'	=> 'foo_perms',
	 			'delete'		=> 'foo_delete'
	 		),
	 		'all'		=> 'foo_manage',		// [Optional] The key to use for any restriction not provided in the map (only needed if not providing all 4)
	 		'prefix'	=> 'foo_',				// [Optional] Rather than specifying each  key in the map, you can specify a prefix, and it will automatically look for restrictions with the key "[prefix]_add/edit/permissions/delete"
	 * @endcode
	 */
	protected static $restrictions = array(
		'app'		=> 'nexus',
		'module'	=> 'support',
		'all' 		=> 'scfields_manage'
	);
	
	/**
	 * @brief	[Node] Node Title
	 */
	public static $nodeTitle = 'custom_support_fields';

	/**
	 * @brief	[Node] Title prefix.  If specified, will look for a language key with "{$key}_title" as the key
	 */
	public static $titleLangPrefix = 'nexus_cfield_';
	
	/**
	 * @brief	[CustomField] Column Map
	 */
	public static $databaseColumnMap = array(
		'content'	=> 'extra',
		'not_null'	=> 'required',
	);

	/**
	 * @brief	[CustomField] Additional Field Classes
	 */
	public static $additionalFieldTypes = array(
		'UserPass'	=> 'sf_type_UserPass',
		'Ftp'		=> 'sf_type_Ftp'
	);
	
	/**
	 * @brief	[CustomField] Additional Field Toggles
	 */
	public static $additionalFieldToggles = array(
		'Ftp'		=> array( 'pf_not_null', 'sf_validate_ftp' )
	);
	
	/**
	 * @brief	[CustomField] Upload Field Storage Extension
	 */
	public static $uploadStorageExtension = 'nexus_Support';
	
	/**
	 * @brief	[CustomField] Editor Options
	 */
	public static $editorOptions = array( 'app' => 'nexus', 'key' => 'Support' );
	
	/**
	 * Get
	 *
	 * @param	string	$key	Key
	 * @return	mixed	$value	Value
	 */
	public function __get( $key )
	{
		/* Required fields aren't actually required because they may be department specific */
		if ( $key == 'not_null' )
		{
			return $this->required ? NULL : FALSE;
		}
		
		return parent::__get( $key );
	}
	
	/**
	 * [Node] Add/Edit Form
	 *
	 * @param	\IPS\Helpers\Form	$form	The form
	 * @return	void
	 */
	public function form( &$form )
	{
		parent::form( $form );
		

		$form->add( new \IPS\Helpers\Form\Node( 'sf_departments', $this->departments === '*' ? 0 : explode( ',', $this->departments ), TRUE, array( 'class' => 'IPS\nexus\Support\Department', 'multiple' => TRUE, 'zeroVal' => 'all' ) ), 'pf_desc' );
		$form->add( new \IPS\Helpers\Form\YesNo( 'sf_validate_ftp', extension_loaded('ftp') ? $this->validate : FALSE, FALSE, array( 'disabled' => !extension_loaded('ftp') ), NULL, NULL, NULL, 'sf_validate_ftp' ), 'pf_not_null' );
		if ( !extension_loaded('ftp') )
		{
			\IPS\Member::loggedIn()->language()->words['sf_validate_ftp_desc'] = \IPS\Member::loggedIn()->language()->addToStack( 'sf_validate_ftp_noftp' );
		}
		elseif( !extension_loaded('ssh2') )
		{
			\IPS\Member::loggedIn()->language()->words['sf_validate_ftp_warning'] = \IPS\Member::loggedIn()->language()->addToStack( 'sf_validate_ftp_nosftp' );
		}
		
		unset( $form->elements[''][1] );
		unset( $form->elements['']['pf_search_type'] );
		unset( $form->elements['']['pf_format'] );
	}
	
	/**
	 * [Node] Format form values from add/edit form for save
	 *
	 * @param	array	$values	Values from the form
	 * @return	array
	 */
	public function formatFormValues( $values )
	{
		if( isset( $values['sf_validate_ftp'] ) )
		{
			$values['departments'] = $values['sf_departments'] ? implode( ',', array_keys( $values['sf_departments'] ) ) : '*';
			unset( $values['sf_departments'] );
		}
		
		if( isset( $values['sf_validate_ftp'] ) )
		{
			$values['validate'] = $values['sf_validate_ftp'];
			unset( $values['sf_validate_ftp'] );
		}
		
		return parent::formatFormValues( $values );
	}
	
	/**
	 * Build Form Helper
	 *
	 * @param	mixed		$value					The value
	 * @param	callback	$customValidationCode	Custom validation code
	 * @return \IPS\Helpers\Form\FormAbstract
	 */
	public function buildHelper( $value=NULL, $customValidationCode=NULL )
	{
		if ( $this->type === 'UserPass' )
		{
			return new \IPS\nexus\Form\UserPass( static::$langKey . '_' . $this->id, $value, $this->not_null, array(), NULL, NULL, NULL, static::$langKey . '_' . $this->id );
		}
		elseif ( $this->type === 'Ftp' )
		{
			return new \IPS\Helpers\Form\Ftp( static::$langKey . '_' . $this->id, $value, $this->not_null, array( 'validate' => $this->validate ), NULL, NULL, NULL, static::$langKey . '_' . $this->id );
		}
		
		return parent::buildHelper( $value, $customValidationCode );
	}
	
	/**
	 * Display Value
	 *
	 * @param	mixed	$value	The value
	 * @return	string
	 */
	public function displayValue( $value=NULL )
	{
		if ( $this->type === 'UserPass' )
		{
			return \IPS\Theme::i()->getTemplate( 'forms', 'nexus', 'global' )->usernamePasswordDisplay( json_decode( \IPS\Text\Encrypt::fromTag( $value )->decrypt(), TRUE ) );
		}
		
		return parent::displayValue( $value );
	}
}