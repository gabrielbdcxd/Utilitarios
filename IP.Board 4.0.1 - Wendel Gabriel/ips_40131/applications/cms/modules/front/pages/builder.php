<?php
/**
 * @brief		[Front] Page Controller
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @since		25 Feb 2014
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\cms\modules\front\pages;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * page
 */
class _builder extends \IPS\core\modules\front\system\widgets
{
	/**
	 * Preview a block (from the ACP or elsewhere dynamically)
	 *
	 * @return html
	 */
	public function previewBlock()
	{
		$output = "";
		
		if ( isset( \IPS\Request::i()->block_plugin ) )
		{
			$block  = new \IPS\cms\Blocks\Block;
			$block->type       = "plugin";
			$block->plugin     = \IPS\Request::i()->block_plugin;
			$block->plugin_app = ( isset( \IPS\Request::i()->block_plugin_app ) ) ? \IPS\Request::i()->block_plugin_app : \IPS\Request::i()->block_app;
			$block->plugin_plugin = \IPS\Request::i()->block_plugin_plugin;
			$block->key		   = md5( uniqid() );
			
			$params = array();
			$block->content = NULL;
			
			if ( isset( \IPS\Request::i()->_sending ) )
			{
				foreach( explode( ",", \IPS\Request::i()->_sending ) as $field )
				{
					if ( $field and isset( \IPS\Request::i()->$field ) )
					{
						if ( $field == 'block_content' )
						{
							$block->content = \IPS\Request::i()->$field;
							
							if ( isset( \IPS\Request::i()->template_params ) )
							{
								$block->template_params = \IPS\Request::i()->template_params;
							}
							continue;
						}
						
						$params[ $field ] = \IPS\Request::i()->$field;
					}
				}
			}
			
			$block->plugin_config = json_encode( $params );
			
			/* Template stuffs */
			if ( \IPS\Request::i()->block_template_use_how == 'copy' )
			{
				$block->widget()->template( array( $block, 'getTemplate' ) );
			}

			$output = $block->widget()->render();
		}
							
		\IPS\Output::i()->sendOutput( \IPS\Theme::i()->getTemplate( 'global', 'core' )->blankTemplate( $output ), 200, 'text/html', \IPS\Output::i()->httpHeaders );
	}
	
	/**
	 * Get Output For Adding A New Block
	 *
	 * @return	void
	 */
	protected function getBlock()
	{		
		$key = $block = explode( "_", \IPS\Request::i()->blockID );
		
		if ( isset( \IPS\Request::i()->pageID ) )
		{
			try
			{
				foreach ( \IPS\Db::i()->select( '*', 'cms_page_widget_areas', array( 'area_page_id=?', \IPS\Request::i()->pageID ) ) as $item )
				{
					$blocks = json_decode( $item['area_widgets'], TRUE );
					
					foreach( $blocks as $block )
					{
						if( $block['key'] == $key[2] AND $block['unique'] == $key[3] )
						{ 
							if ( isset( $block['app'] ) and $block['app'] == $key[1] )
							{
								$widget = \IPS\Widget::load( \IPS\Application::load( $block['app'] ), $block['key'], $block['unique'], $block['configuration'], null, \IPS\Request::i()->orientation );
							}
							elseif ( isset( $block['plugin'] ) and $block['plugin'] == $key[1] )
							{
								$widget = \IPS\Widget::load( \IPS\Plugin::load( $block['plugin'] ), $block['key'], $block['unique'], $block['configuration'], null, \IPS\Request::i()->orientation );
							}
						}
					}
				}
			}
			catch ( \UnderflowException $e ) { }

			/* Make sure the current page is set so the widgets have database/page scope */
			\IPS\cms\Pages\Page::$currentPage = \IPS\cms\Pages\Page::load( \IPS\Request::i()->pageID );

			/* Have we got a database for this page? */
			$database = \IPS\cms\Pages\Page::$currentPage->getDatabase();

			if ( $database )
			{
				\IPS\cms\Databases\Dispatcher::i()->setDatabase( $database->id );
			}
		}
		
		if ( !isset( $widget ) )
		{
			try
			{
				$widget = \IPS\Widget::load( \IPS\Application::load( $key[1] ), $key[2], $key[3], array(), null, \IPS\Request::i()->orientation );

			}
			catch ( \OutOfRangeException $e )
			{
				$widget = \IPS\Widget::load( \IPS\Plugin::load( $key[1] ), $key[2], $key[3], array(), null, \IPS\Request::i()->orientation );
			}
		}

		$output = (string) $widget;

		\IPS\Output::i()->output = ( $output ) ? $output :  \IPS\Theme::i()->getTemplate( 'widgets', 'core', 'front' )->blankWidget( $widget );
	}

	/**
	 * Get Configuration
	 *
	 * @return	void
	 */
	protected function getConfiguration()
	{
		/* Standard widget area, allow the core stuff to handle this */
		if( in_array( \IPS\Request::i()->area, array( 'sidebar', 'header', 'footer' ) ) )
		{
			return parent::getConfiguration();
		}
		
		$key	= explode( "_", \IPS\Request::i()->block );
		$blocks	= array( 'area_widgets' => NULL );
		
		/* CMS only stuff */
		try
		{
			$blocks       = \IPS\Db::i()->select( '*', 'cms_page_widget_areas', array( 'area_page_id=? AND area_area=?', \IPS\Request::i()->pageID, \IPS\Request::i()->pageArea ) )->first();

			$where = ( $key[0] ) == 'app' ? '`key`=? AND `app`=?' : '`key`=? AND `plugin`=?';
			$widgetMaster = \IPS\Db::i()->select( '*', 'core_widgets', array( $where, $key[2], $key[1] ) )->first();
		}
		catch ( \UnderflowException $e )
		{
		}
		
		$blocks	= json_decode( $blocks['area_widgets'], TRUE );
		$widget	= NULL;

		if( !empty( $blocks ) )
		{
			foreach ( $blocks as $k => $block )
			{
				if ( $block['key'] == $key[2] AND $block['unique'] == $key[3] )
				{
					if ( isset( $block['app'] ) and $block['app'] == $key[1] )
					{
						$widget = \IPS\Widget::load( \IPS\Application::load( $block['app'] ), $block['key'], $block['unique'], $block['configuration'] );
						$widget->menuStyle = $widgetMaster['menu_style'];
					}
					elseif ( isset( $block['plugin'] ) and $block['plugin'] == $key[1] )
					{
						$widget = \IPS\Widget::load( \IPS\Plugin::load( $block['plugin'] ), $block['key'], $block['unique'], $block['configuration'] );
						$widget->menuStyle = $widgetMaster['menu_style'];
					}
				}

				if( $widget !== NULL AND method_exists( $widget, 'configuration' ) )
				{
					$form = new \IPS\Helpers\Form( 'form', 'saveSettings' );
					if ( $widget->configuration( $form ) !== NULL )
					{
						if ( $values = $form->values() )
						{
							if ( method_exists( $widget, 'preConfig' ) )
							{
								$values = $widget->preConfig( $values );
							}
							
							$blocks[ $k ]['configuration'] = $values;
							\IPS\Db::i()->insert( 'cms_page_widget_areas', array( 'area_page_id' => \IPS\Request::i()->pageID, 'area_area' => \IPS\Request::i()->pageArea, 'area_widgets' => json_encode( $blocks ) ), TRUE );
							\IPS\Output::i()->json( 'OK' );
						}
						\IPS\Output::i()->sendOutput( $widget->configuration()->customTemplate( array( call_user_func_array( array( \IPS\Theme::i(), 'getTemplate' ), array( 'widgets', 'core' ) ), 'formTemplate' ), $widget ) );
					}
				}
			}
		}
	}
	
	/**
	 * Reorder Blocks
	 *
	 * @return	void
	 */
	protected function saveOrder()
	{
		$newOrder = array();
		$seen     = array();

		try
		{
			$currentConfig = \IPS\Db::i()->select( '*', 'cms_page_widget_areas', array( 'area_page_id=? AND area_area=?', \IPS\Request::i()->pageID, \IPS\Request::i()->area ) )->first();
			$widgets = json_decode( $currentConfig['area_widgets'], TRUE );
		}
		catch ( \UnderflowException $e )
		{
			$widgets = array();
		}
		
		/* Loop over the new order and merge in current blocks so we don't lose config */
		if ( isset ( \IPS\Request::i()->order ) )
		{
			foreach ( \IPS\Request::i()->order as $block )
			{
				$block = explode( "_", $block );
				
				$added = FALSE;
				foreach( $widgets as $widget )
				{
					if ( $widget['key'] == $block[2] and $widget['unique'] == $block[3] )
					{
						$seen[]     = $widget['unique'];
						$newOrder[] = $widget;
						$added = TRUE;
						break;
					}
				}
				if( !$added )
				{
					$newBlock = array();
					
					if ( $block[0] == 'app' )
					{
						$newBlock['app'] = $block[1];
					}
					else
					{
						$newBlock['plugin'] = $block[1];
					}
					
					$newBlock['key'] 		  = $block[2];
					$newBlock['unique']		  = $block[3];
					$newBlock['configuration']	= array();

					/* Make sure this widget doesn't have configuration in another area */
					$newBlock['configuration'] = \IPS\cms\Widget::getConfiguration( $newBlock['unique'] );

					$seen[]     = $block[3];
					$newOrder[] = $newBlock;
				}
			}
		}

		/* Anything to update? */
		if ( count( $widgets ) > count( $newOrder ) )
		{
			/* No items left in area, or one has been removed */
			foreach( $widgets as $widget )
			{
				/* If we haven't seen this widget, it's been removed, so add to trash */
				if ( ! in_array( $widget['unique'], $seen ) )
				{
					\IPS\Widget::trash( $widget['unique'], $widget );
				}
			}
		}

		/* Expire Caches so up to date information displays */
		\IPS\Widget::deleteCaches();

		/* Save to database */
		$orientation = ( isset( \IPS\Request::i()->orientation ) and \IPS\Request::i()->orientation === 'vertical' ) ? 'vertical' : 'horizontal';
		\IPS\Db::i()->replace( 'cms_page_widget_areas', array( 'area_orientation' => $orientation, 'area_page_id' => \IPS\Request::i()->pageID, 'area_widgets' => json_encode( $newOrder ), 'area_area' => \IPS\Request::i()->area ) );
		
		\IPS\cms\Pages\Page::load( \IPS\Request::i()->pageID )->postWidgetOrderSave();
	}
}