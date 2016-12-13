<?php
/**
 * @brief		Background Task: Rebuild Container Counts
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @since		14 Aug 2014
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\core\extensions\core\Queue;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Background Task: Rebuild Container Counts
 */
class _RebuildContainerCounts
{
	/**
	 * @brief Number of content items to index per cycle
	 */
	public $index	= 500;
	
	/**
	 * Parse data before queuing
	 *
	 * @param	array	$data
	 * @return	array
	 */
	public function preQueueData( $data )
	{
		$classname = $data['class'];
		
		try
		{
			$data['count'] = \IPS\Db::i()->select( 'MAX(' . $classname::$databasePrefix . $classname::$databaseColumnId . ')', $classname::$databaseTable )->first();
		}
		catch( \Exception $ex )
		{
			throw new \OutOfRangeException;
		}
		
		if( $data['count'] == 0 )
		{
			return null;
		}
		
		return $data;
	}

	/**
	 * Run Background Task
	 *
	 * @param	mixed					$data	Data as it was passed to \IPS\Task::queue()
	 * @param	int						$offset	Offset
	 * @return	int|null				New offset or NULL if complete
	 * @throws	\OutOfRangeException	Indicates offset doesn't exist and thus task is complete
	 */
	public function run( $data, $offset )
	{
		$classname = $data['class'];
        $exploded = explode( '\\', $classname );
        if ( !class_exists( $classname ) or !\IPS\Application::appIsEnabled( $exploded[1] ) )
		{
			throw new \OutOfRangeException;
		}
		
		$last = NULL;
		
		\IPS\Log::i( LOG_DEBUG )->write( "Running " . $classname . ", with an offset of " . $offset, 'rebuildItemCounts' );

		$select   = \IPS\Db::i()->select( '*', $classname::$databaseTable, array( $classname::$databasePrefix . $classname::$databaseColumnId . ' > ?',  $offset ), $classname::$databasePrefix . $classname::$databaseColumnId . ' ASC', array( 0, $this->index ) );
		$idColumn = $classname::$databaseColumnId;
		$iterator = new \IPS\Patterns\ActiveRecordIterator( $select, $classname );
		$idColumn		 = $classname::$databaseColumnId;
		$itemClass       = $classname::$contentItemClass;
		$itemIdColumn    = $itemClass::$databaseColumnId;
		$commentClass    = $itemClass::$commentClass;
		$commentIdColumn = $commentClass::$databaseColumnId;
		
		foreach( $iterator as $item )
		{
			/* Update container */
			$containerWhere    = array( array( $itemClass::$databasePrefix . $itemClass::$databaseColumnMap['container'] . '=?', $item->_id ) );
			$anyContainerWhere = $containerWhere;
			
			if ( in_array( 'IPS\Content\Hideable', class_implements( $itemClass ) ) )
			{
				if ( isset( $itemClass::$databaseColumnMap['approved'] ) )
				{
					$containerWhere[] = array( $itemClass::$databasePrefix . $itemClass::$databaseColumnMap['approved'] . '=?', 1 );
				}
				elseif ( isset( $itemClass::$databaseColumnMap['hidden'] ) )
				{
					$containerWhere[] = array( $itemClass::$databasePrefix . $itemClass::$databaseColumnMap['hidden'] . '=?', 0 );
				}
			}
			if ( $item->_items !== NULL )
			{
				$item->_items = \IPS\Db::i()->select( 'COUNT(*)', $itemClass::$databaseTable, $containerWhere )->first();
			}
			if ( $item->_comments !== NULL )
			{
				$commentWhere = array(
					array( $commentClass::$databaseTable . '.' . $commentClass::$databasePrefix . $commentClass::$databaseColumnMap['item'] . ' = ' . $itemClass::$databaseTable . '.' . $itemClass::$databasePrefix . $itemIdColumn ),
					array( $itemClass::$databaseTable . '.' . $itemClass::$databasePrefix . $itemClass::$databaseColumnMap['container'] . '=?', $item->_id ) 
				);

				if ( isset( $commentClass::$databaseColumnMap['approved'] ) )
				{
					$commentWhere[] = array( $commentClass::$databaseTable . '.' . $commentClass::$databasePrefix . $commentClass::$databaseColumnMap['approved'] . '=?', 1 );
				}
				elseif ( isset( $commentClass::$databaseColumnMap['hidden'] ) )
				{
					$commentWhere[] = array( $commentClass::$databaseTable . '.' . $commentClass::$databasePrefix . $commentClass::$databaseColumnMap['hidden'] . '=?', 0 );
				}
	
				$item->_comments = \IPS\Db::i()->select( 'COUNT(*)', array(
						array( $commentClass::$databaseTable, $commentClass::$databaseTable ),
						array( $itemClass::$databaseTable, $itemClass::$databaseTable )
					), $commentWhere )->first();
			}
			
			if ( in_array( 'IPS\Content\Hideable', class_implements( $itemClass ) ) )
			{
				if ( $item->_unapprovedItems !== NULL )
				{
					$hiddenContainerWhere = array( array( $itemClass::$databasePrefix . $itemClass::$databaseColumnMap['container'] . '=?', $item->_id ) );
					
					if ( isset( $itemClass::$databaseColumnMap['approved'] ) )
					{
						$hiddenContainerWhere[] = array( $itemClass::$databasePrefix . $itemClass::$databaseColumnMap['approved'] . '=?', 0 );
					}
					elseif ( isset( $itemClass::$databaseColumnMap['hidden'] ) )
					{
						$hiddenContainerWhere[] = array( $itemClass::$databasePrefix . $itemClass::$databaseColumnMap['hidden'] . '=?', 1 );
					}
					
					$item->_unapprovedItems = \IPS\Db::i()->select( 'COUNT(*)', $itemClass::$databaseTable, $hiddenContainerWhere )->first();
				}
				
				$commentWhere = array( array( $commentClass::$databaseTable . '.' . $commentClass::$databasePrefix . $commentClass::$databaseColumnMap['item'] . ' = ' . $itemClass::$databaseTable . '.' . $itemClass::$databasePrefix . $itemIdColumn ) );
				if ( $item->_unapprovedComments !== NULL )
				{
					if ( $itemClass::$firstCommentRequired )
					{
						/* Only look in non-hidden items otherwise this count will be added to */
						$commentWhere = array_merge( $commentWhere, $containerWhere );
					}
					else
					{
						$commentWhere = array_merge( $commentWhere, $anyContainerWhere );
					}
					
					if ( isset( $commentClass::$databaseColumnMap['approved'] ) )
					{
						$commentWhere[] = array( $commentClass::$databaseTable . '.' . $commentClass::$databasePrefix . $commentClass::$databaseColumnMap['approved'] . '=?', 0 );
					}
					elseif ( isset( $commentClass::$databaseColumnMap['hidden'] ) )
					{
						$commentWhere[] = array( $commentClass::$databaseTable . '.' . $commentClass::$databasePrefix . $commentClass::$databaseColumnMap['hidden'] . '=?', 1 );
					}
					
					$item->_unapprovedComments = \IPS\Db::i()->select( 'COUNT(*)', array(
						array( $commentClass::$databaseTable, $commentClass::$databaseTable ),
						array( $itemClass::$databaseTable, $itemClass::$databaseTable )
					), $commentWhere )->first();
				}
			}
			
			$item->setLastComment();
			$item->save();
			
			$last = $item->$idColumn;
		}
		
		return $last;
	}
	
	/**
	 * Get Progress
	 *
	 * @param	mixed					$data	Data as it was passed to \IPS\Task::queue()
	 * @param	int						$offset	Offset
	 * @return	array( 'text' => 'Doing something...', 'complete' => 50 )	Text explaning task and percentage complete
	 * @throws	\OutOfRangeException	Indicates offset doesn't exist and thus task is complete
	 */
	public function getProgress( $data, $offset )
	{
        $class = $data['class'];
        $exploded = explode( '\\', $class );
        if ( !class_exists( $class ) or !\IPS\Application::appIsEnabled( $exploded[1] ) )
		{
			throw new \OutOfRangeException;
		}
		
		return array( 'text' => \IPS\Member::loggedIn()->language()->addToStack('rebuilding_item_counts', FALSE, array( 'sprintf' => array( \IPS\Member::loggedIn()->language()->addToStack( $class::$nodeTitle, FALSE, array( 'strtolower' => TRUE ) ) ) ) ), 'complete' => $data['count'] ? ( round( 100 / $data['count'] * $offset, 2 ) ) : 100 );
	}	
}