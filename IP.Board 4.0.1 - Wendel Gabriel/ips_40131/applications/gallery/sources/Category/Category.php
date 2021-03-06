<?php
/**
 * @brief		Category Node
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @subpackage	Gallery
 * @since		04 Mar 2014
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\gallery;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Category Node
 */
class _Category extends \IPS\Node\Model implements \IPS\Node\Permissions
{
	/**
	 * @brief	[ActiveRecord] Multiton Store
	 */
	protected static $multitons;
	
	/**
	 * @brief	[ActiveRecord] Database Table
	 */
	public static $databaseTable = 'gallery_categories';
	
	/**
	 * @brief	[ActiveRecord] Database Prefix
	 */
	public static $databasePrefix = 'category_';
		
	/**
	 * @brief	[Node] Order Database Column
	 */
	public static $databaseColumnOrder = 'position';
	
	/**
	 * @brief	[Node] Parent ID Database Column
	 */
	public static $databaseColumnParent = 'parent_id';
	
	/**
	 * @brief	[Node] Node Title
	 */
	public static $nodeTitle = 'categories';

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
		'app'		=> 'gallery',
		'module'	=> 'gallery',
		'prefix'	=> 'categories_'
	);
	
	/**
	 * @brief	[Node] App for permission index
	 */
	public static $permApp = 'gallery';
	
	/**
	 * @brief	[Node] Type for permission index
	 */
	public static $permType = 'category';
	
	/**
	 * @brief	The map of permission columns
	 */
	public static $permissionMap = array(
		'view' 				=> 'view',
		'read'				=> 2,
		'add'				=> 3,
		'reply'				=> 4,
		'rate'				=> 5
	);

	/**
	 * @brief	[Node] Title prefix.  If specified, will look for a language key with "{$key}_title" as the key
	 */
	public static $titleLangPrefix = 'gallery_category_';
	
	/**
	 * @brief	[Node] Description suffix.  If specified, will look for a language key with "{$titleLangPrefix}_{$id}_{$descriptionLangSuffix}" as the key
	 */
	public static $descriptionLangSuffix = '_desc';
	
	/**
	 * @brief	[Node] Moderator Permission
	 */
	public static $modPerm = 'gallery_categories';

	/**
	 * @brief	Content Item Class
	 */
	public static $contentItemClass = 'IPS\gallery\Image';
	
	/**
	 * Get SEO name
	 *
	 * @return	string
	 */
	public function get_name_seo()
	{
		if( !$this->_data['name_seo'] )
		{
			$this->name_seo	= \IPS\Http\Url::seoTitle( \IPS\Lang::load( \IPS\Lang::defaultLanguage() )->get( 'gallery_category_' . $this->id ) );
			$this->save();
		}

		return $this->_data['name_seo'] ?: \IPS\Http\Url::seoTitle( \IPS\Lang::load( \IPS\Lang::defaultLanguage() )->get( 'gallery_category_' . $this->id ) );
	}

	/**
	 * [Node] Get whether or not this node is enabled
	 *
	 * @note	Return value NULL indicates the node cannot be enabled/disabled
	 * @return	bool|null
	 */
	protected function get__enabled()
	{
		return NULL;
	}

	/**
	 * Get sort order
	 *
	 * @return	string
	 */
	public function get__sortBy()
	{
		return $this->sort_options;
	}

	/**
	 * [Node] Get number of content items
	 *
	 * @return	int
	 * @note	We return null if there are non-public albums so that we can count what you can see properly
	 */
	protected function get__items()
	{
		return $this->nonpublic_albums ? NULL : $this->count_imgs;
	}

	/**
	 * [Node] Get number of content comments
	 *
	 * @return	int
	 */
	protected function get__comments()
	{
		return $this->count_comments;
	}

	/**
	 * [Node] Get number of unapproved content items
	 *
	 * @return	int
	 */
	protected function get__unnapprovedItems()
	{
		return $this->count_imgs_hidden;
	}
	
	/**
	 * [Node] Get number of unapproved content comments
	 *
	 * @return	int
	 */
	protected function get__unapprovedComments()
	{
		return $this->count_comments_hidden;
	}

	/**
	 * Set number of items
	 *
	 * @param	int	$val	Items
	 * @return	void
	 */
	protected function set__items( $val )
	{
		$this->count_imgs = (int) $val;
	}

	/**
	 * Set number of items
	 *
	 * @param	int	$val	Comments
	 * @return	void
	 */
	protected function set__comments( $val )
	{
		$this->count_comments = (int) $val;
	}

	/**
	 * [Node] Get number of unapproved content items
	 *
	 * @param	int	$val	Unapproved Items
	 * @return	void
	 */
	protected function set__unapprovedItems( $val )
	{
		$this->count_imgs_hidden = $val;
	}
	
	/**
	 * [Node] Get number of unapproved content comments
	 *
	 * @param	int	$val	Unapproved Comments
	 * @return	void
	 */
	protected function set__unapprovedComments( $val )
	{
		$this->count_comments_hidden = $val;
	}

	/**
	 * [Node] Add/Edit Form
	 *
	 * @param	\IPS\Helpers\Form	$form	The form
	 * @return	void
	 */
	public function form( &$form )
	{
		$form->addTab( 'category_settings' );
		$form->addHeader( 'category_settings' );
		$form->add( new \IPS\Helpers\Form\Translatable( 'category_name', NULL, TRUE, array( 'app' => 'gallery', 'key' => ( $this->id ? "gallery_category_{$this->id}" : NULL ) ) ) );
		$form->add( new \IPS\Helpers\Form\Translatable( 'category_description', NULL, FALSE, array(
			'app'		=> 'gallery',
			'key'		=> ( $this->id ? "gallery_category_{$this->id}_desc" : NULL ),
			'editor'	=> array(
				'app'			=> 'gallery',
				'key'			=> 'Categories',
				'autoSaveKey'	=> ( $this->id ? "gallery-cat-{$this->id}" : "gallery-new-cat" ),
				'attachIds'		=> $this->id ? array( $this->id, NULL, 'description' ) : NULL, 
				'minimize'		=> 'cdesc_placeholder'
			)
		) ) );

		$class = get_called_class();

		$form->add( new \IPS\Helpers\Form\Node( 'gcategory_parent_id', $this->id ? $this->parent_id : ( \IPS\Request::i()->parent ?: 0 ), FALSE, array(
			'class'		      => '\IPS\gallery\Category',
			'disabled'	      => false,
			'zeroVal'         => 'node_no_parentg',
			'permissionCheck' => function( $node ) use ( $class )
			{
				if( isset( $class::$subnodeClass ) AND $class::$subnodeClass AND $node instanceof $class::$subnodeClass )
				{
					return FALSE;
				}

				return !isset( \IPS\Request::i()->id ) or ( $node->id != \IPS\Request::i()->id and !$node->isChildOf( $node::load( \IPS\Request::i()->id ) ) );
			}
		) ) );
		$form->add( new \IPS\Helpers\Form\Select( 'category_sort_options', $this->sort_options ?: 'updated', FALSE, array( 'options' => array( 'updated' => 'sort_updated', 'rating' => 'sort_rating', 'num_comments' => 'sort_num_comments' ) ), NULL, NULL, NULL, 'category_sort_options' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'category_allow_albums', $this->id ? $this->allow_albums : TRUE, FALSE ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'category_approve_img', $this->approve_img, FALSE ) );

		if( \IPS\Settings::i()->gallery_watermark_path )
		{
			$form->add( new \IPS\Helpers\Form\YesNo( 'category_watermark', $this->id ? $this->watermark : TRUE, FALSE ) );
		}

		$form->addHeader( 'category_comments_and_ratings' );
		$form->add( new \IPS\Helpers\Form\YesNo( 'category_allow_comments', $this->id ? $this->allow_comments : TRUE, FALSE, array( 'togglesOn' => array( 'category_approve_com' ) ), NULL, NULL, NULL, 'category_allow_comments' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'category_approve_com', $this->approve_com, FALSE, array(), NULL, NULL, NULL, 'category_approve_com' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'gcategory_allow_rating', $this->id ? $this->allow_rating : TRUE, FALSE ) );

		if ( \IPS\Settings::i()->tags_enabled )
		{
			$form->addHeader( 'category_tags' );
			$form->add( new \IPS\Helpers\Form\YesNo( 'category_can_tag', $this->id ? $this->can_tag : TRUE, FALSE, array( 'togglesOn' => array( 'category_tag_prefixes', 'category_preset_tags' ) ), NULL, NULL, NULL, 'category_can_tag' ) );
			$form->add( new \IPS\Helpers\Form\YesNo( 'category_tag_prefixes', $this->id ? $this->tag_prefixes : TRUE, FALSE, array(), NULL, NULL, NULL, 'category_tag_prefixes' ) );
			
			if ( !\IPS\Settings::i()->tags_open_system )
			{
				$form->add( new \IPS\Helpers\Form\Text( 'category_preset_tags', $this->preset_tags, FALSE, array( 'autocomplete' => array( 'unique' => 'true' ), 'nullLang' => 'ctags_predefined_unlimited' ), NULL, NULL, NULL, 'category_preset_tags' ) );
			}
		}

		$form->addTab( 'category_rules' );
		$form->add( new \IPS\Helpers\Form\Radio( 'category_show_rules', $this->id ? $this->show_rules : 0, FALSE, array(
			'options' => array(
				0	=> 'category_show_rules_none',
				1	=> 'category_show_rules_link',
				2	=> 'category_show_rules_full'
			),
			'toggles'	=> array(
				1	=> array(
					'category_rules_title',
					'category_rules_text'
				),
				2	=> array(
					'category_rules_title',
					'category_rules_text'
				),
			)
		) ) );
		$form->add( new \IPS\Helpers\Form\Translatable( 'category_rules_title', NULL, FALSE, array( 'app' => 'gallery', 'key' => ( $this->id ? "gallery_category_{$this->id}_rulestitle" : NULL ) ), NULL, NULL, NULL, 'category_rules_title' ) );
		$form->add( new \IPS\Helpers\Form\Translatable( 'category_rules_text', NULL, FALSE, array( 'app' => 'gallery', 'key' => ( $this->id ? "gallery_category_{$this->id}_rules" : NULL ), 'editor' => array( 'app' => 'gallery', 'key' => 'Categories', 'autoSaveKey' => ( $this->id ? "gallery-rules-{$this->id}" : "gallery-new-rules" ), 'attachIds' => $this->id ? array( $this->id, NULL, 'rules' ) : NULL ) ), NULL, NULL, NULL, 'category_rules_text' ) );
		//$form->add( new \IPS\Helpers\Form\Url( 'category_rules_link', $this->rules_link, FALSE, array(), NULL, NULL, NULL, 'category_rules_link' ) );
		
		$form->addTab( 'error_messages' );
		$form->add( new \IPS\Helpers\Form\Translatable( 'category_permission_custom_error', NULL, FALSE, array( 'app' => 'gallery', 'key' => ( $this->id ? "gallery_category_{$this->id}_permerror" : NULL ), 'editor' => array( 'app' => 'gallery', 'key' => 'Categories', 'autoSaveKey' => ( $this->id ? "gallery-permerror-{$this->id}" : "gallery-new-permerror" ), 'attachIds' => $this->id ? array( $this->id, NULL, 'permerror' ) : NULL, 'minimize' => 'gallery_permerror_placeholder' ) ), NULL, NULL, NULL, 'gallery_permission_custom_error' ) );
	}
	
	/**
	 * [Node] Format form values from add/edit form for save
	 *
	 * @param	array	$values	Values from the form
	 * @return	array
	 */
	public function formatFormValues( $values )
	{
		/* Fix field (lang conflict) */
		if( isset( $values['gcategory_allow_rating'] ) )
		{
			$values['category_allow_rating'] = $values['gcategory_allow_rating'];
			unset( $values['gcategory_allow_rating'] );
		}
		if( isset( $values['gcategory_parent_id'] ) )
		{
			$values['category_parent_id'] = $values['gcategory_parent_id'];
			unset( $values['gcategory_parent_id'] );
		}

		/* Claim attachments */
		if ( !$this->id )
		{
			$this->save();
			\IPS\File::claimAttachments( 'gallery-new-cat', $this->id, NULL, 'description', TRUE );
			\IPS\File::claimAttachments( 'gallery-new-rules', $this->id, NULL, 'rules', TRUE );
			\IPS\File::claimAttachments( 'gallery-new-permerror', $this->id, NULL, 'permerror', TRUE );
		}

		/* Custom language fields */
		if( isset( $values['category_name'] ) )
		{
			\IPS\Lang::saveCustom( 'gallery', "gallery_category_{$this->id}", $values['category_name'] );
			$values['name_seo']	= \IPS\Http\Url::seoTitle( $values['category_name'][ \IPS\Lang::defaultLanguage() ] );
			unset( $values['category_name'] );
		}

		if( isset( $values['category_description'] ) )
		{
			\IPS\Lang::saveCustom( 'gallery', "gallery_category_{$this->id}_desc", $values['category_description'] );
			unset( $values['category_description'] );
		}

		if( isset( $values['category_rules_title'] ) )
		{
			\IPS\Lang::saveCustom( 'gallery', "gallery_category_{$this->id}_rulestitle", $values['category_rules_title'] );
			unset( $values['category_rules_title'] );
		}

		if( isset( $values['category_rules_text'] ) )
		{
			\IPS\Lang::saveCustom( 'gallery', "gallery_category_{$this->id}_rules", $values['category_rules_text'] );
			unset( $values['category_rules_text'] );
		}

		if( isset( $values['category_permission_custom_error'] ) )
		{
			\IPS\Lang::saveCustom( 'gallery', "gallery_category_{$this->id}_permerror", $values['category_permission_custom_error'] );
			unset( $values['category_permission_custom_error'] );
		}

		/* Parent ID */
		if ( isset( $values['category_parent_id'] ) )
		{
			$values['category_parent_id'] = $values['category_parent_id'] ? intval( $values['category_parent_id']->id ) : 0;
		}

		/* Send to parent */
		return $values;
	}

	/**
	 * @brief	Cached URL
	 */
	protected $_url	= NULL;

	/**
	 * Get URL
	 *
	 * @return	\IPS\Http\Url
	 */
	public function url()
	{
		if( $this->_url === NULL )
		{
			$this->_url = \IPS\Http\Url::internal( "app=gallery&module=gallery&controller=browse&category={$this->_id}", 'front', 'gallery_category', $this->name_seo );
		}

		return $this->_url;
	}

	/**
	 * Get "No Permission" error message
	 *
	 * @return	string
	 */
	public function errorMessage()
	{
		if ( \IPS\Member::loggedIn()->language()->checkKeyExists( "gallery_category_{$this->id}_permerror" ) )
		{
			$message = \IPS\Member::loggedIn()->language()->addToStack( "gallery_category_{$this->id}_permerror" );
			if ( $message and $message != '<p></p>' )
			{
				return $message;
			}
		}
		
		return 'node_error_no_perm';
	}

	/**
	 * Get latest image information
	 *
	 * @return	\IPS\gallery\Image|NULL
	 */
	public function lastImage()
	{
		$latestImage = NULL;
		
		if( $this->last_img_id )
		{
			try
			{
				$latestImage = \IPS\gallery\Image::load( $this->last_img_id );
			}
			catch ( \OutOfRangeException $e )
			{
				$latestImage = NULL;
			}
		}

		foreach( $this->children() as $child )
		{
			$childLatest = $child->lastImage();

			if( $childLatest !== NULL AND ( $latestImage === NULL OR $childLatest->date > $latestImage->date ) )
			{
				$latestImage = $childLatest;
			}
		}

		return $latestImage;
	}
	
	/**
	 * Delete Record
	 *
	 * @return	void
	 */
	public function delete()
	{
		\IPS\File::unclaimAttachments( 'gallery_Categories', $this->id );
		parent::delete();
		
		\IPS\Lang::deleteCustom( 'gallery', "gallery_category_{$this->id}_rulestitle" );
		\IPS\Lang::deleteCustom( 'gallery', "gallery_category_{$this->id}_rules" );
		\IPS\Lang::deleteCustom( 'gallery', "gallery_category_{$this->id}_permerror" );
	}
	
	/**
	 * Set last comment
	 *
	 * @param	\IPS\Content\Comment|NULL	$comment	The latest comment or NULL to work it out
	 * @return	int
	 * @note	We actually want to set the last image info, not the last comment, so we ignore $comment
	 */
	public function setLastComment( \IPS\Content\Comment $comment=NULL )
	{
		$this->setLastImage();
	}

	/**
	 * Set last image data
	 *
	 * @param	\IPS\gallery\Image|NULL	$image	The latest image or NULL to work it out
	 * @return	void
	 */
	public function setLastImage( \IPS\gallery\Image $image=NULL )
	{
		if( $image === NULL )
		{
			try
			{
				$image	= \IPS\gallery\Image::constructFromData( \IPS\Db::i()->select( '*', 'gallery_images', array( 'image_category_id=? AND image_approved=1 AND ( image_album_id = 0 OR album_type NOT IN ( 2, 3 ) )', $this->id ), 'image_date DESC', 1 )->join(
					'gallery_albums',
					"image_album_id=album_id"
				)->first() );
			}
			catch ( \UnderflowException $e )
			{
				$this->last_img_id		= 0;
				$this->last_img_date	= 0;
				return;
			}
		}
				
		$this->last_img_id		= $image->id;
		$this->last_img_date	= $image->date;

		if( $image->album_id )
		{
			$album = \IPS\gallery\Album::load( $image->album_id );
			$album->setLastImage( $image );
			$album->save();
		}
	}
	
	/**
	 * Get last comment time
	 *
	 * @note	This should return the last comment time for this node only, not for children nodes
	 * @return	\IPS\DateTime|NULL
	 */
	public function getLastCommentTime()
	{
		return $this->last_img_date ? \IPS\DateTime::ts( $this->last_img_date ) : NULL;
	}

	/**
	 * @brief	Cached cover photo
	 */
	protected $coverPhoto	= NULL;

	/**
	 * Retrieve a cover photo
	 *
	 * @param	string	$size	One of full, medium, small or thumb
	 * @return	string|null
	 */
	public function coverPhoto( $size='thumb' )
	{
		$property = $size . "_file_name";

		if( !$this->cover_img_id )
		{
			if( $lastImage = $this->lastImage() )
			{
				return (string) \IPS\File::get( 'gallery_Images', $lastImage->$property )->url;
			}

			return NULL;
		}

		if( !in_array( $size, array( 'full', 'medium', 'small', 'thumb' ) ) )
		{
			throw new \InvalidArgumentException;
		}

		if( $size == 'full' )
		{
			$size	= 'masked';
		}

		if( $this->coverPhoto === NULL )
		{
			$this->coverPhoto	= \IPS\gallery\Image::load( $this->cover_img_id );
		}

		return (string) \IPS\File::get( 'gallery_Images', $this->coverPhoto->$property )->url;
	}

	/**
	 * Load record based on a URL
	 *
	 * @param	\IPS\Http\Url	$url	URL to load from
	 * @return	static
	 * @throws	\InvalidArgumentException
	 * @throws	\OutOfRangeException
	 */
	public static function loadFromUrl( \IPS\Http\Url $url )
	{
		$qs = array_merge( $url->queryString, $url->getFriendlyUrlData() );
		
		if ( isset( $qs['category'] ) )
		{
			if ( method_exists( get_called_class(), 'loadAndCheckPerms' ) )
			{
				return static::loadAndCheckPerms( $qs['category'] );
			}
			else
			{
				return static::load( $qs['category'] );
			}
		}
		
		throw new \InvalidArgumentException;
	}

	/**
	 * Check if any albums are located in this category
	 *
	 * @return	bool
	 */
	public function hasAlbums()
	{
		return (bool) \IPS\Db::i()->select( 'COUNT(*) as total', 'gallery_albums', array( 'album_category_id=?', $this->id ) )->first();
	}
	
	/**
	 * Form to delete or move content
	 *
	 * @param	bool	$showMoveToChildren	If TRUE, will show "move to children" even if there are no children
	 * @return	\IPS\Helpers\Form
	 */
	public function deleteOrMoveForm( $showMoveToChildren=FALSE )
	{
		if ( $this->hasChildren( NULL ) OR $this->hasAlbums() )
		{
			$showMoveToChildren = TRUE;
			if( $this->hasChildren( NULL ) AND $this->hasAlbums() )
			{
				\IPS\Member::loggedIn()->language()->words['node_move_children']	= \IPS\Member::loggedIn()->language()->addToStack( 'node_move_catsalbums', FALSE );
				\IPS\Member::loggedIn()->language()->words['node_delete_children']	= \IPS\Member::loggedIn()->language()->addToStack( 'node_delete_children_catsalbums', FALSE );
			}
			else if( $this->hasChildren( NULL ) )
			{
				\IPS\Member::loggedIn()->language()->words['node_move_children']	= \IPS\Member::loggedIn()->language()->addToStack( 'node_move_children', FALSE, array( 'sprintf' => array( \IPS\Member::loggedIn()->language()->addToStack( static::$nodeTitle ) ) ) );
				\IPS\Member::loggedIn()->language()->words['node_delete_children']	= \IPS\Member::loggedIn()->language()->addToStack( 'node_delete_children_cats', FALSE );
			}
			else
			{
				\IPS\Member::loggedIn()->language()->words['node_move_children']	= \IPS\Member::loggedIn()->language()->addToStack( 'node_move_subalbums', FALSE );
				\IPS\Member::loggedIn()->language()->words['node_delete_children']	= \IPS\Member::loggedIn()->language()->addToStack( 'node_delete_children_albums', FALSE );
			}
		}
		return parent::deleteOrMoveForm( $showMoveToChildren );
	}
	
	/**
	 * Handle submissions of form to delete or move content
	 *
	 * @param	array	$values			Values from form
	 * @param	bool	$deleteWhenDone	Delete the node when done?
	 * @return	void
	 */
	public function deleteOrMoveFormSubmit( $values, $deleteWhenDone=TRUE )
	{
		if ( $this->hasAlbums() )
		{
			foreach ( new \IPS\Patterns\ActiveRecordIterator( \IPS\Db::i()->select( '*', 'gallery_albums', array( 'album_category_id=?', $this->id ) ), 'IPS\gallery\Album' ) as $album )
			{
				if ( $values['node_move_children'] )
				{
					$album->moveTo( $values['node_move_children'] );
				}
				else
				{
					\IPS\Task::queue( 'core', 'DeleteOrMoveContent', array( 'class' => 'IPS\gallery\Album', 'id' => $album->_id, 'deleteWhenDone' => $deleteWhenDone ) );
				}
			}
		}
		
		return parent::deleteOrMoveFormSubmit( $values );
	}

	/**
	 * Get template for node tables
	 *
	 * @return	callable
	 */
	public static function nodeTableTemplate()
	{
		return array( \IPS\Theme::i()->getTemplate( 'browse', 'gallery' ), 'categoryRow' );
	}

	/**
	 * Check permissions on any node
	 *
	 * For example - can be used to check if the user has
	 * permission to create content in any node to determine
	 * if there should be a "Submit" button
	 *
	 * @param	mixed								$permission		A key which has a value in static::$permissionMap['view'] matching a column ID in core_permission_index
	 * @param	\IPS\Member|\IPS\Member\Group|NULL	$member			The member or group to check (NULL for currently logged in member)
	 * @return	bool
	 * @throws	\OutOfBoundsException	If $permission does not exist in static::$permissionMap
	 */
	public static function canOnAny( $permission, $member = NULL )
	{
		/* Load member */
		if ( $member === NULL )
		{
			$member = \IPS\Member::loggedIn();
		}

		if ( $member->members_bitoptions['remove_gallery_access'] )
		{
			return FALSE;
		}

		return parent::canOnAny( $permission, $member );
	}

    /**
     * [ActiveRecord] Duplicate
     *
     * @return	void
     */
    public function __clone()
    {
        if ( $this->skipCloneDuplication === TRUE )
        {
            return;
        }

        $this->public_albums = 0;
        $this->nonpublic_albums = 0;

        $oldId = $this->id;

        parent::__clone();

        foreach ( array( 'rules_title' => "gallery_category_{$this->id}_rulestitle", 'rules_text' => "gallery_category_{$this->id}_rules" ) as $fieldKey => $langKey )
        {
            $oldLangKey = str_replace( $this->id, $oldId, $langKey );
            \IPS\Lang::saveCustom( 'gallery', $langKey, iterator_to_array( \IPS\Db::i()->select( 'word_custom, lang_id', 'core_sys_lang_words', array( 'word_key=?', $oldLangKey ) )->setKeyField( 'lang_id' )->setValueField('word_custom') ) );
        }
    }
    	
	/**
	 * Get content for embed
	 *
	 * @param	array	$params	Additional parameters to add to URL
	 * @return	string
	 */
	public function embedContent( $params )
	{
		return 'x';
	}
}