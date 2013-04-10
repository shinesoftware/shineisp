<?php

/**
 * Fastlinks
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Fastlinks extends BaseFastlinks {

	/**
	 * updateVisits
	 * @param $id
	 * @return void
	 */
	public static function updateVisits($id){
		Doctrine_Query::create()->update("Fastlinks")->set('visits', 'visits + 1')->where("fastlink_id = ?", $id)->execute(); 
	}
	
    /**
     * findbyCode
     * Get a record by the code
     * @param $code
     * @return ARRAY Record
     */
    public static function findbyCode($code) {
        try {
            $dq = Doctrine_Query::create ()
                     ->from ( 'Fastlinks f' )
                     ->where ( 'f.code = ?', $code )
                     ->limit(1);
                     
            $link = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
            
            return $link;
        
        } catch ( Exception $e ) {
            die ( $e->getMessage () );
        }
    }
	
    /**
     * findbyId
     * Get a record by the id
     * @param $id
     * @return ARRAY Record
     */
    public static function findbyId($id, $table, $customerId) {
        try {
            $dq = Doctrine_Query::create ()
                     ->from ( 'Fastlinks f' )
                     ->where ( 'f.id = ?', $id )
                     ->andWhere ( 'f.sqltable = ?', $table )
                     ->andWhere ( 'f.customer_id = ?', $customerId )
                     ->limit(1);
                     
            $link = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
            
            return $link;
        
        } catch ( Exception $e ) {
            die ( $e->getMessage () );
        }
    }
	
	/**
	 * findlinks
	 * Get a record by id and table name
	 * @param $id, $table
	 * @return ARRAY Record
	 */
	public static function findlinks($id, $customer_id, $table = "*") {
		try {
			return Doctrine_Query::create ()
			         ->from ( 'Fastlinks f' )
			         ->where ( 'f.sqltable = ?', $table )
			         ->andWhere ( 'customer_id = ?', $customer_id )
			         ->andWhere ( 'id = ?', $id )
			         ->limit(1)
			         ->orderBy('fastlink_id desc')
			         ->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
			
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
	}
	
	/*
	 * checkFastlinkCode
	 * Check if the fastlink is already used in the database
	 */
	private static function checkFastlinkCode($code){
        $result = Doctrine_Query::create ()->from ( 'Fastlinks f' )->where ( 'code = ?', $code )->count();
        if($result > 0){
        	self::checkFastlinkCode(Shineisp_Commons_Utilities::GenerateRandomString ());
        }else{
        	return $code;
        }
	}
	
	 /*
     * Create a fastlink that helps all the users to get directly the content requested
     * useful within the emails messages
     */
    public static function CreateFastlink($controller, $action, $params, $sqltable, $id, $customerid) {
        $link = new Fastlinks ( );
        $fastlink = self::checkFastlinkCode(Shineisp_Commons_Utilities::GenerateRandomString ());
        $link->controller = $controller;
        $link->action = $action;
        $link->params = $params;
        $link->customer_id = $customerid;
        $link->sqltable = $sqltable;
        $link->id = $id;
        $link->code = $fastlink;
        $link->save ();
        return $fastlink;
    }       
}
