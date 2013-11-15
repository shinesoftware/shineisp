<?php

/**
 * Banks
 * 
 * This class manage the Bank module
 * 
 * @package    ShineISP
 * 
 * @author     Shine Software <info@shineisp.com>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Banks extends BaseBanks
{
	
	/**
	 * grid
	 * create the configuration of the grid
	 */	
	public static function grid($rowNum = 10) {
		
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		$config ['datagrid'] ['columns'] [] = array ('label' => null, 'field' => 'b.bank_id', 'alias' => 'bank_id', 'type' => 'selectall', 'attributes' => array('width' => 50 ) );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Id' ), 'field' => 'b.bank_id', 'alias' => 'bank_id', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Name' ), 'field' => 'name', 'alias' => 'name', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Test Mode' ), 'field' => 'test_mode', 'alias' => 'testmode', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		
		$config ['datagrid'] ['fields'] = "bank_id, name, account, enabled, test_mode as testmode";
		$config ['datagrid'] ['rownum'] = $rowNum;
		$config ['datagrid'] ['rowlist'] = array ('10', '50', '100', '1000' );
		$config ['datagrid'] ['dqrecordset'] = Doctrine_Query::create ()->select ( $config ['datagrid'] ['fields'] )->from ( 'Banks b' );
		
		$config ['datagrid'] ['basepath'] = "/admin/banks/";
		$config ['datagrid'] ['index'] = "bank_id";
		
		return $config;
	}
		
	/**
	 * getList
	 * Get a list of items ready for the select html object
	 * @param boolean $empty
	 */
    public static function getList($empty=false) {
        $items = array ();
        
        $arrTypes = Doctrine::getTable ( 'Banks' )->findAll ();
        if($empty){
            $items[] = "";
        }
        foreach ( $arrTypes->getData () as $c ) {
            $items [$c ['bank_id']] = $c ['name'];
        }
        return $items;
    }
    	
    /**
     * getActive
     * Get all banks module gateway active
     * @return array
     */
    public static function getActive() {
        
        try {
        	$banks = array();
        	
            $items = Doctrine_Query::create ()
                    ->from ( 'Banks b' )
                    ->where ( "b.enabled = ?", true )
                    ->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
            
	        foreach ( $items as $item ) {
	            $banks [$item ['bank_id']] = $item ['name'];
	        }
	        
            return $banks;   
        } catch (Exception $e) {
            die ( $e->getMessage () );
        }
    }
    	
    /**
     * getAllInfo
     * Get all data with the bank ID
     * @param $id
     * @return Doctrine Record / Array
     */
    public static function getAllInfo($id, $fields = "*", $retarray = false) {
        
        try {
            $dq = Doctrine_Query::create ()->select ( $fields )
                    ->from ( 'Banks b' )
                    ->where ( "bank_id = $id" )
                    ->limit ( 1 );
            
            $retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
            $item = $dq->execute ( array (), $retarray );
            
            return $item;   
        } catch (Exception $e) {
            die ( $e->getMessage () );
        }
    }
    	
	/**
	 * findbyClassname
	 * Get a bank record using its class name.
	 * @param $classname
	 * @return unknown_type
	 */
    public static function findbyClassname($classname) {
        return Doctrine::getTable ( 'Banks' )->findOneBy ( 'classname', $classname )->toArray();
    }
    
    /**
     * find
     * Get a record by ID
     * @param $id
     * @return Doctrine Record
     */
    public static function find($id, $fields = "*", $retarray = false) {
        $dq = Doctrine_Query::create ()->select ( $fields )->from ( 'Banks b' )
        ->where ( "bank_id = $id" )->limit ( 1 );
        
        $retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
        $records = $dq->execute ( array (), $retarray );
        return $records;
    }
    
    /**
     * findbyMD5
     * Get a record by name converted with the MD5 code
     * @param $md5
     * @return Array
     */
    public static function findbyMD5($md5) {
        return Doctrine_Query::create ()
        							->from ( 'Banks b' )
        							->where ( "MD5(classname) = ?", $md5 )
        							->limit ( 1 )
        							->execute(array (), Doctrine_Core::HYDRATE_ARRAY);
    }
    
    /**
     * findAllActive
     * Get all the active records 
     * @return Doctrine Record
     */
    public static function findAllActive($fields = "*", $retarray = false) {
        $dq = Doctrine_Query::create ()->select ( $fields )->from ( 'Banks b' )->where('b.enabled = ?', 1);
        
        $retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
        $records = $dq->execute ( array (), $retarray );
        return $records;
    }    
    
    /**
     * getBankInfo
     * Get the bank information 
     * @return Doctrine Record
     */
    public static function getBankInfo() {
        $records = Doctrine_Query::create ()
        				->from ( 'Banks b' )
        				->where('b.enabled = ?', 1)
        				->andWhere('b.method_id = ?', 2)
        				->limit(1)
        				->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
        $records = !empty($records[0]) ? $records[0] : array();
        return $records;
    }    
}