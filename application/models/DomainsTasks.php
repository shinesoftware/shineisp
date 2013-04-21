<?php

/**
 * DomainsTasks
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class DomainsTasks extends BaseDomainsTasks {
	
	/**
	 * create the configuration of the grid
	 */
	public static function grid($rowNum = 10) {
	
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
	
		$config ['datagrid'] ['columns'] [] = array ('label' => null, 'field' => 'dt.task_id', 'alias' => 'task_id', 'type' => 'selectall' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'ID' ), 'field' => 'dt.task_id', 'alias' => 'task_id', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Action' ), 'field' => 'dt.action', 'alias' => 'action', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Domain' ), 'field' => 'd.domain', 'alias' => 'domain', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Tld' ), 'field' => 'w.tld', 'alias' => 'tld', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Registrar' ), 'field' => 'r.name', 'alias' => 'registrar', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Start' ), 'field' => 'DATE_FORMAT(startdate, "%d/%m/%Y %H:%i:%s")', 'alias' => 'startdate', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'End' ), 'field' => 'DATE_FORMAT(enddate, "%d/%m/%Y %H:%i:%s")', 'alias' => 'enddate', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Status' ), 'field' => 's.status', 'alias' => 'status', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
	
		$config ['datagrid'] ['fields'] = "task_id, s.status as status, DATE_FORMAT(startdate, '%d/%m/%Y %H:%i:%s') as startdate, DATE_FORMAT(enddate, '%d/%m/%Y %H:%i:%s') as enddate, dt.action as action, d.domain as domain, r.name as registrar, w.tld as tld";
		$config ['datagrid'] ['dqrecordset'] = Doctrine_Query::create ()->select ( $config ['datagrid'] ['fields'] )->from ( 'DomainsTasks dt' )->leftJoin('dt.Statuses s')->leftJoin('dt.Registrars r')->leftJoin('dt.Domains d')->leftJoin('d.DomainsTlds t')->leftJoin('t.WhoisServers w');
	
		$config ['datagrid'] ['rownum'] = $rowNum;
	
		$config ['datagrid'] ['basepath'] = "/admin/domainstaks/";
		$config ['datagrid'] ['index'] = "task_id";
		$config ['datagrid'] ['rowlist'] = array ('10', '50', '100', '1000' );
	
		$config ['datagrid'] ['buttons'] ['edit'] ['label'] = $translator->translate ( 'Edit' );
		$config ['datagrid'] ['buttons'] ['edit'] ['cssicon'] = "edit";
		$config ['datagrid'] ['buttons'] ['edit'] ['action'] = "/admin/domainstaks/edit/id/%d";
	
		$config ['datagrid'] ['buttons'] ['delete'] ['label'] = $translator->translate ( 'Delete' );
		$config ['datagrid'] ['buttons'] ['delete'] ['cssicon'] = "delete";
		$config ['datagrid'] ['buttons'] ['delete'] ['action'] = "/admin/domainstaks/delete/id/%d";
		return $config;
	}
	
	/**
	 * Delete a record by ID
	 * @param $id
	 */
	public static function deleteItem($id) {
		Doctrine::getTable ( 'DomainsTasks' )->findOneBy ( 'task_id', $id )->delete();
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <Doctrine_Record, mixed, boolean, Doctrine_Collection, PDOStatement, Doctrine_Adapter_Statement, Doctrine_Connection_Statement, unknown, number>
	 */
	public static function find($id) {
		return Doctrine::getTable ( 'DomainsTasks' )->findOneBy ( 'task_id', $id );
	}
	
	/**
	 * Get a record by ID
	 *
	 * @param $id
	 * @return Doctrine Record
	 */
	public static function getById($id, $fields = "*", $retarray = false) {
		$dq = Doctrine_Query::create ()->select ( $fields )
		->from ( 'DomainsTasks t' )
		->where ( "t.task_id = ?", $id )
		->limit ( 1 );
	
		$retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
		$record = $dq->execute ( array (), $retarray );
		return $record;
	}
	
	/**
	 * Delete
	 * @return boolean
	 */
	public static function DeleteTask($id) {
		if (is_numeric($id))
			return Doctrine_Core::getTable('DomainsTasks')->find($id)->delete();
		
		return false;
	}

	/**
	 * Save the record
	 *
	 * @param posted var from the form
	 * @return Boolean
	 */
	public static function saveData($record, $id=null) {
	
		// Set the new values
		if (is_numeric ( $id )) {
			$domainstasks = self::find( $id );
		}else{
			$domainstasks = new DomainsTasks();
		}
	
		$domainstasks->action = $record ['action'];
		$domainstasks->startdate = Shineisp_Commons_Utilities::formatDateIn($record ['startdate']);
		$domainstasks->enddate = Shineisp_Commons_Utilities::formatDateIn($record ['enddate']);
		$domainstasks->registrars_id = $record ['registrars_id'];
		$domainstasks->domain_id = $record ['domain_id'];
		$domainstasks->status_id = $record ['status_id'];
	
		if($domainstasks->trySave()){
			return $domainstasks->task_id;
		}
	
		return false;
	}
	
	/**
	 * GetTask
	 * @return void
	 */
	public static function GetTask($limit=100) {
		return Doctrine_Query::create ()
				->select('startdate, enddate, action, domain, r.name as registrar, log, s.status as status')
				->from ( 'DomainsTasks dt' )
				->leftJoin('dt.Registrars r')
				->leftJoin('dt.Domains d')
				->leftJoin('dt.Statuses s')
				->orderBy('startdate desc')
				->limit($limit)
				->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
	}
	
	
	/**
	 * GetTask
	 * @return void
	 */
	public static function GetIncompleteTask($customerid) {
		if(!is_numeric($customerid)){
			return null;
		}
		return Doctrine_Query::create ()->from ( 'DomainsTasks dt' )->leftJoin('dt.Domains d')->where('d.customer_id = ?', $customerid)->andWhere('status_id = ?', Statuses::id('processing', 'domains_tasks'))->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
	}
	
	/**
	 * UpdateTaskCounter
	 * @param $id
	 * @return void
	 */
	public static function UpdateTaskCounter($id) {
		Doctrine_Query::create ()->update ( "DomainsTasks" )->set ( 'times', 'times + 1' )->where ( "task_id = ?", $id )->execute ();
	}
	
	/**
	 * setStatusTask
	 * @param $domain, $statusID
	 * @return void
	 */
	public static function setStatusTask($id, $statusID) {
		$dq = Doctrine_Query::create ()->update ( "DomainsTasks dt" )
										->set ( 'dt.status_id', $statusID )
										->set ( 'dt.enddate', '?', date ( 'Y-m-d H:i:s' ))
										->where ( "dt.domain_id = $id" );
		

		$dq->execute ();
	}
	
	/**
	 * UpdateTaskLog
	 * @param $id
	 * @param $log
	 * @return void
	 */
	public static function UpdateTaskLog($id, $log) {
		Doctrine_Query::create ()->update ( "DomainsTasks dt" )
										->set ( 'log', "'" . addslashes($log) . "'" )
										->set ( 'dt.enddate', '?', date ( 'Y-m-d H:i:s' ))
										->where ( "task_id = ?", $id )
										->execute ();
	}
	
	/**
	 * UpdateTaskStatus
	 * @param $id
	 * @param $status_id
	 * @return void
	 */
	public static function UpdateTaskStatus($id, $status_id) {
		Doctrine_Query::create ()->update ( "DomainsTasks dt" )
										->set ( 'dt.status_id', $status_id )
										->set ( 'dt.enddate', '?', date ( 'Y-m-d H:i:s' ) )
										->where ( "dt.task_id = ?", $id )
										->execute ();
	}
	
	/*
	 * add domains tasks to be done by the cron job 
	 */
	static public function AddTasks($domains) {
		try {

			for($i = 0; $i < count ( $domains ); $i ++) {
				// Check if exist a domain name previously added in the task but not yet completed.
				// If the domain exists, it is excluded 
				$taskset = self::getTasksbyDomain ( $domains [$i] ['domain'], $domains [$i] ['action'], Statuses::id("processing", "domains_tasks") ); 
				
				if (count ( $taskset ) == 0) {
					self::AddTask($domains [$i] ['domain'], $domains [$i] ['action']);
				}
			}
			
		} catch ( Doctrine_Exception $e ) {
			echo $e->getMessage ();
			die ();
		}
		return true;
	}
	
	/*
	 * AddTask
	 * add a domain task to be done by the cron job 
	 */
	static public function AddTask($domain, $action) {
		$task = new DomainsTasks ( );
		$task->startdate = date ( 'Y-m-d H:i:s' );
		$task->action = $action;
		$task->domain = $domain;
		$task->domain_id = Domains::getDomainIDbyName($domain);
		$task->registrars_id = Registrars::findRegistrarIDbyDomain($domain);
		$task->status_id = Statuses::id('active', 'domains_tasks'); //Domains Task Status;
		return $task->trySave ();
	}
	
	/**
	 * getTasks
	 * Get a record by ID
	 * @return Doctrine Record
	 */
	public static function getTasks($statusId = "", $limit = null) {
		$dq = Doctrine_Query::create ()->from ( 'DomainsTasks dt' );
		
		if (is_numeric ( $statusId )) {
			$dq->where ( 'dt.status_id = ?', $statusId );
		}
		
		if (is_numeric ( $limit )) {
			$dq->limit ( $limit );
		}
		return $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
	}
	
	/**
	 * getTasksbyDomain
	 * Get a domain task by domain name, action and status
	 * @return Array
	 */
	public static function getTasksbyDomain($domain, $action = "registerDomain", $status = "", $limit = null) {
		$dq = Doctrine_Query::create ()->from ( 'DomainsTasks dt' );
		
		$dq->where ( 'dt.action = ?', $action );
		$dq->addWhere ( 'dt.domain = ?', $domain );
		if (is_numeric ( $status )) {
			$dq->addWhere ( 'dt.status_id = ?', $status );
		}
		
		if (is_numeric ( $limit )) {
			$dq->limit ( $limit );
		}
		
		return $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
	}
	
	/**
	 * getTasksbyDomainID
	 * Get a domain task by domain id
	 * @return Array
	 */
	public static function getTasksbyDomainID($domainID) {
		$records = Doctrine_Query::create ()
								->select("DATE_FORMAT(startdate, '%d/%m/%Y %H:%i:%s') as startdate, 
										  DATE_FORMAT(enddate, '%d/%m/%Y %H:%i:%s') as enddate,
										  action,
										  log,
										  s.status")
								->from ( 'DomainsTasks dt' )
								->leftJoin( 'dt.Statuses s' )
								->where ( 'dt.domain_id = ?', $domainID )
								->orderBy('dt.startdate desc')
								->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		
		return $records;
	}
	
	/**
	 * Create a widget for the dashboard
	 * @return Array
	 */
	public static function Last($limit=10) {
		$records = Doctrine_Query::create ()
								->select("DATE_FORMAT(startdate, '%d/%m/%Y %H:%i:%s') as startdate, 
										  DATE_FORMAT(enddate, '%d/%m/%Y %H:%i:%s') as enddate,
										  domain,
										  action,
										  log,
										  s.status as status")
								->from ( 'DomainsTasks dt' )
								->leftJoin( 'dt.Statuses s' )
								->orderBy('dt.startdate desc')
								->limit($limit)
								->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		return $records;
	}
}