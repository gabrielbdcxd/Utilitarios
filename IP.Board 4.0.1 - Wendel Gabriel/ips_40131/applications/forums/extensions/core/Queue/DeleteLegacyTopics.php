<?php
/**
 * @brief		Background Task
 *
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 *
 * @package		IPS Social Suite
 * @subpackage	Forums
 * @since		18 Mar 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\forums\extensions\core\Queue;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Background Task
 */
class _DeleteLegacyTopics
{
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
		$select = \IPS\Db::i()->select( '*', 'forums_topics', 'approved > 1', 'tid ASC', array( 0, 5 ) );
		if ( !count( $select ) )
		{
			throw new \OutOfRangeException;
		}
		
		$done = 0;
		foreach( new \IPS\Patterns\ActiveRecordIterator( $select, 'IPS\forums\Topic' ) as $topic )
		{
			$topic->delete();
			$done++;
		}
		
		return $offset + $done;
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
		$count = \IPS\Db::i()->select( 'COUNT(*)', 'forums_topics', 'approved > 1' )->first();
		return array( 'text' => \IPS\Member::loggedIn()->language()->addToStack('queue_deleting_legacy_topics'), 'complete' => 100 / ( $count ?: 1 + $offset ) * $offset );
	}	
}