<?php

/**
 * FilesCategories
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ShineISP
 * 
 * @author     Shine Software <info@shineisp.com>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class FilesCategories extends BaseFilesCategories {
	
	/**
	 * getList
	 * Get a list of items ready for the select html object
	 */
	public static function getList() {
		$registry = Shineisp_Registry::getInstance();
		$items = array ();
		$arrTypes = Doctrine::getTable ( 'FilesCategories' )->findAll ();
		foreach ( $arrTypes->getData () as $c ) {
			$items [$c ['category_id']] = $registry->Zend_Translate->translate($c ['name']);
		}
		return $items;
	}
	
	/**
	 * grid
	 * create the configuration of the grid
	 */
	public static function grid($rowNum = 10) {
	
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
	
		$config ['datagrid'] ['columns'] [] = array ('label' => null, 'field' => 'c.category_id', 'alias' => 'category_id', 'type' => 'selectall' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Id' ), 'field' => 'c.category_id', 'alias' => 'category_id', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Name' ), 'field' => 'name', 'alias' => 'name', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		
		$config ['datagrid'] ['fields'] = "category_id, name";
		$config ['datagrid'] ['rownum'] = $rowNum;
	
		$config ['datagrid'] ['dqrecordset'] = Doctrine_Query::create ()->select ( $config ['datagrid'] ['fields'] )->from ( 'FilesCategories c' );
	
		$config ['datagrid'] ['basepath'] = "/admin/filecategories/";
		$config ['datagrid'] ['index'] = "category_id";
	
		return $config;
	}
	
	 
	/**
	 * getAllInfo
	 * Get all data with the File category ID
	 * @param $id
	 * @return Doctrine Record / Array
	 */
	public static function getAllInfo($id, $fields = "*", $retarray = false) {
	
		try {
			$dq = Doctrine_Query::create ()->select ( $fields )
			->from ( 'FilesCategories c' )
			->where ( "category_id = $id" )
			->limit ( 1 );
	
			$retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
			$item = $dq->execute ( array (), $retarray );
	
			return $item;
		} catch (Exception $e) {
			die ( $e->getMessage () );
		}
	}
	 
	/**
	 * findbyName
	 * Get a filecategory record using its name.
	 * @param $name
	 * @return unknown_type
	 */
	public static function findbyName($name) {
		return Doctrine::getTable ( 'FilesCategories' )->findOneBy ( 'name', $name )->toArray();
	}
	
	/**
	 * find
	 * Get a record by ID
	 * @param $id
	 * @return Doctrine Record
	 */
	public static function find($id, $fields = "*", $retarray = false) {
		$dq = Doctrine_Query::create ()->select ( $fields )->from ( 'FilesCategories c' )
		->where ( "category_id = $id" )->limit ( 1 );
	
		$retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
		$records = $dq->execute ( array (), $retarray );
		return $records;
	}
}