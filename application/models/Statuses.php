<?php

/**
 * Statuses
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class Statuses extends BaseStatuses {
	
	/**
	 * Get a list ready for the html select object
	 * 
	 * 
	 * @return array
	 */
	public static function getList($section, $onlypublic = false) {
		$items = array ();
		$dq = Doctrine_Query::create ()->from ( 'Statuses s' )->where ( 's.section = ?', $section );
		
		if ($onlypublic) {
			$dq->addWhere ( 's.public = ?', 1 );
		}
		
		$records = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		
		foreach ( $records as $record ) {
			$items [$record ['status_id']] = $record ['status'];
		}
		
		return $items;
	}
	
	/**
	 * Get all statuses
	 * 
	 * @return object $statuses
	 */
	public static function getAll() {
		$out     = array();
		$records = Doctrine_Query::create ()->from ( 'Statuses s' )
										   ->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
								
		// normalize data
		foreach ( $records as $record ) {
			$section = strtolower($record['section']);
			$code    = strtolower($record['code']);
						
			// code as key
			$out[$section][$code] = $record;
			
			// status_id as key
			$out['id:'.$record['status_id']] = $record; // PHP BUG: See Statuses::getById
		}		

		return Shineisp_Commons_Utilities::array2object($out);
	}	
	
	
	/**
	 * Get the Status ID
	 * 
	 * @param string $status 
	 * @param string $section
	 */
	public static function id($status, $section="generic") {
		if(!empty($status))
			$status = strtolower($status);
			$section = strtolower($section);
			return intval(Shineisp_Registry::get('Status')->$section->$status->status_id) ? intval(Shineisp_Registry::get('Status')->$section->$status->status_id) : null;
		
		return null;
	}	

	/**
	 * Get the status by Id
	 * Fix a PHP bug that doesn't allow to access casted object through ID
	 * 
	 * @param string $id 
	 */
	public static function getById($id) {
		$id = intval($id);
		return Shineisp_Registry::get('Status')->{'id:'.$id};
	}	

	
	/**
	 * get the label name of the status
	 * 
	 * 
	 * @param integer $status_id
	 */
	public static function getLabel($status_id) {
		$status = self::getById($status_id)->status;
		
		if ($status) {
			$registry = Shineisp_Registry::getInstance ();
			$translation = $registry->Zend_Translate;
			return $translation->translate ( $status );
		}
		
		return false;
	}
}