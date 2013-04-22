<?php

/**
 * OrdersItems
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class OrdersItems extends BaseOrdersItems {
	

	/**
	 * grid
	 * create the configuration of the grid
	 */	
	public static function grid($rowNum = 10) {
		$ns = new Zend_Session_Namespace ( 'Default' );
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		$config ['datagrid'] ['columns'] [] = array ('label' => null, 'field' => 'd.detail_id', 'alias' => 'id', 'type' => 'selectall' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'ID' ), 'field' => 'd.detail_id', 'alias' => 'id', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Order ID' ), 'field' => 'o.order_id', 'alias' => 'order_id', 'sortable' => true, 'searchable' => true, 'type' => 'string', 'attributes' => array('width' => 50) );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Service' ), 'field' => 'pd.name', 'alias' => 'productname', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Domain' ), 'field' => 'dm.domain', 'alias' => 'domain', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Company' ), 'field' => 'c.company', 'alias' => 'company', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Lastname' ), 'field' => 'c.lastname', 'alias' => 'lastname', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Days left' ), 'field' => 'd.date_end', 'alias' => 'daysleft', 'sortable' => true, 'searchable' => false, 'type' => 'integer' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Start date' ), 'field' => 'd.date_start', 'alias' => 'date_start', 'sortable' => true, 'searchable' => true, 'type' => 'date' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'End date' ), 'field' => 'd.date_end', 'alias' => 'date_end', 'sortable' => true, 'searchable' => true, 'type' => 'date' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Statuses' ), 'field' => 's.status', 'alias' => 'status', 'sortable' => true, 'searchable' => true);
		
		$config ['datagrid'] ['fields'] =  "d.detail_id as id,
											o.order_id as order_id, 
											c.customer_id as customer_id, 
											c.company as company, 
											c.lastname as lastname,
											d.order_id, 
											DATE_FORMAT(d.date_start, '%d/%m/%Y') as date_start, 
											DATE_FORMAT(d.date_end, '%d/%m/%Y') as date_end, 
											pd.name as productname, 
											s.status as status, 
											oid.relationship_id as oid,
											dm.domain_id as domain_id,
											DATEDIFF(d.date_end, CURRENT_DATE) as daysleft, 
											CONCAT(dm.domain, '.', ws.tld ) as domain,
											bc.name as cycle";
		
		$config ['datagrid'] ['rownum'] = $rowNum;
		
		$config ['datagrid'] ['dqrecordset'] = Doctrine_Query::create ()->select ( $config ['datagrid'] ['fields'] )->from ( 'OrdersItems d' )
					->leftJoin ( 'd.Orders o' )
					->leftJoin ( 'd.OrdersItemsDomains oid ON d.detail_id = oid.orderitem_id' )
					->leftJoin ( 'd.BillingCycle bc' )
					->leftJoin ( 'oid.Domains dm' )
					->leftJoin ( 'dm.DomainsTlds dt' )
					->leftJoin ( 'dt.WhoisServers ws' )
					->leftJoin ( 'd.Products p' )
					->leftJoin ( "p.ProductsData pd WITH pd.language_id = $ns->idlang" )
					->leftJoin ( 'p.Taxes t' )->leftJoin ( 'o.Customers c' )
					->leftJoin ( 'd.Statuses s' )
					->where ( 'p.type <> ?', 'domain'); // Show all the records but not the Expired services // Show only the services and not the domains
			
		
		$config ['datagrid'] ['basepath'] = "/admin/services/";
		$config ['datagrid'] ['index'] = "detail_id";
		$config ['datagrid'] ['rowlist'] = array ('10', '50', '100', '1000' );
		
		$config ['datagrid'] ['buttons'] ['edit'] ['label'] = $translator->translate ( 'Edit' );
		$config ['datagrid'] ['buttons'] ['edit'] ['cssicon'] = "edit";
		$config ['datagrid'] ['buttons'] ['edit'] ['action'] = "/admin/services/edit/id/%d";
		
		$config ['datagrid'] ['buttons'] ['delete'] ['label'] = $translator->translate ( 'Delete' );
		$config ['datagrid'] ['buttons'] ['delete'] ['cssicon'] = "delete";
		$config ['datagrid'] ['buttons'] ['delete'] ['action'] = "/admin/services/delete/id/%d";
		$config ['datagrid'] ['massactions']['common'] = array ('massdelete'=>'Mass Delete', 'bulkexport'=>'Export' );
		return $config;
	}
	

	
	/**
	 * findAll
	 * Get records the orders from the DB
	 * @param $currentPage
	 * @param $rowNum
	 * @param $sort
	 * @param $where
	 * @return array
	 */
	public static function findAll($fields = "*", $currentPage = 1, $rowNum = 2, array $sort = array(), array $where = array(), $locale=1) {
		
		$module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		
		try {
			// Defining the url sort
			$uri = isset ( $sort [1] ) ? "/sort/$sort[1]" : "";
			$dq = Doctrine_Query::create ()->select ( $fields )->from ( 'OrdersItems d' )
					->leftJoin ( 'd.Orders o' )
					->leftJoin ( 'd.OrdersItemsDomains oid ON d.detail_id = oid.orderitem_id' )
					->leftJoin ( 'd.BillingCycle bc' )
					->leftJoin ( 'oid.Domains dm' )
					->leftJoin ( 'd.Products p' )
					->leftJoin ( "p.ProductsData pd WITH pd.language_id = $locale" )
					->leftJoin ( 'p.Taxes t' )
					->leftJoin ( 'o.Customers c' )
					->leftJoin ( 'd.Statuses s' )
					->where ( 'p.type <> ?', 'domain'); // Show all the records but not the Expired services // Show only the services and not the domains
			

			$pagerLayout = new Doctrine_Pager_Layout ( new Doctrine_Pager ( $dq, $currentPage, $rowNum ), new Doctrine_Pager_Range_Sliding ( array ('chunk' => 10 ) ), "/$module/$controller/list/page/{%page_number}" . $uri );
			
			// Get the pager object
			$pager = $pagerLayout->getPager ();
			
			// Set the Order criteria
			if (isset ( $sort [0] )) {
				$pager->getQuery ()->orderBy ( $sort [0] );
			}
			
			if (isset ( $where ) && is_array ( $where )) {
				foreach ( $where as $filters ) {
					if (isset ( $filters [0] ) && is_array($filters [0])) {
						foreach ( $filters as $filter ) {
							$method = $filter ['method'];
							$value = $filter ['value'];
							$criteria = $filter ['criteria'];
							$pager->getQuery ()->$method ( $criteria, $value );
						}
					} else {
						$method = $filters ['method'];
						$value = $filters ['value'];
						$criteria = $filters ['criteria'];
						$pager->getQuery ()->$method ( $criteria, $value );
					}
				}
			}
			
			$pagerLayout->setTemplate ( '<a href="{%url}">{%page}</a> ' );
			$pagerLayout->setSelectedTemplate ( '<a class="active" href="{%url}">{%page}</a> ' );
			$records = $pagerLayout->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
			$pagination = $pagerLayout->display ( null, true );
			
			return array ('records' => $records, 'pagination' => $pagination, 'pager' => $pager, 'recordcount' => $dq->count () );
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
	}	
		

	/**
	 * getExpiringSerivcesByDays
	 * Get all the services from the order details bought by the customers
	 * @param integer $days
	 * @param integer $status
	 * @param integer $autorenew [0, 1]
	 */
    public static function getExpiringSerivcesByDays($days=0, $status=null, $autorenew=null) {
        $dq = Doctrine_Query::create ()->select ( "oi.detail_id, 
        										   pd.name as product,
        										   c.customer_id as id, 
        										   oi.date_end as expiringdate, 
        										   DATEDIFF(oi.date_end, CURRENT_DATE) as daystoend,
        										   o.customer_id as customer_id,
        										   Concat(c.firstname, ' ', c.lastname, ' ', c.company) as fullname, 
        										   c.email as email, 
        										   c.password as password,
        										   c.parent_id as reseller, 
        										   oi.autorenew as renew,
        										   DATEDIFF(oi.date_end, CURRENT_DATE) as days" )
                                           ->from ( 'OrdersItems oi' )
                                           ->leftJoin ( 'oi.Orders o' )
                                           ->leftJoin ( 'oi.Products p' )
                                           ->leftJoin ( "p.ProductsData pd WITH pd.language_id = 1" )
                                           ->leftJoin ( 'o.Customers c' )
                                           ->where ( 'p.type <> ?', 'domain');

        if(is_numeric($days)){
        	$dq->andWhere ( 'DATEDIFF(oi.date_end, CURRENT_DATE) = ?', $days );
        }
        
        if(is_numeric($status)){
        	$dq->andWhere ( 'oi.status_id = ?', $status );  
        }else{
        	$dq->andWhere ( 'oi.status_id = ?', Statuses::id("complete", "orders") ); 
        }
        
        if(is_numeric($autorenew)){
        	$dq->andWhere ( 'oi.autorenew = ?', $autorenew );  
        }

        $records = $dq->execute ( null, Doctrine::HYDRATE_ARRAY );
        
        return $records;
            
    }
		

    /**
     * getExpiringSerivcesByRange
     * Get all the services from the order details bought by the customers and by days range
     * @param integer $from
     * @param integer $to
     * @param integer $status
     * @param integer $autorenew [0, 1]
     */
    public static function getExpiringSerivcesByRange($from, $to, $status="", $autorenew=null) {
        $dq = Doctrine_Query::create ()->select ( "oi.detail_id, 
        										   pd.name as product,
        										   c.customer_id as id, 
        										   DATE_FORMAT(oi.date_end, '%d/%m/%Y') as expiringdate,
        										   o.customer_id as customer_id,
        										   Concat(c.firstname, ' ', c.lastname, ' ', c.company) as fullname, 
        										   c.email as email, 
        										   c.password as password,
        										   c.parent_id as reseller, 
        										   oi.autorenew as renew,
        										   DATEDIFF(oi.date_end, CURRENT_DATE) as days" )
                                           ->from ( 'OrdersItems oi' )
                                           ->leftJoin ( 'oi.Orders o' )
                                           ->leftJoin ( 'oi.Products p' )
                                           ->leftJoin ( "p.ProductsData pd WITH pd.language_id = 1" )
                                           ->leftJoin ( 'o.Customers c' )
                                           ->where ( 'p.type <> ?', 'domain')
                                           ->andWhere ( 'DATEDIFF(oi.date_end, CURRENT_DATE) >= ? and DATEDIFF(oi.date_end, CURRENT_DATE) <= ?', array($from, $to) );
        
        if(is_numeric($status)){
        	$dq->andWhere ( 'oi.status_id = ?', $status );  
        }else{
        	$dq->andWhere ( 'oi.status_id = ?', Statuses::id("complete", "orders") );
        }
        
        if(is_numeric($autorenew)){
        	$dq->andWhere ( 'oi.autorenew = ?', $autorenew );  
        }
        
        return $dq->execute ( null, Doctrine::HYDRATE_ARRAY );
            
    }
	
	/**
	 * getItemsList
	 * Get a list of all items in all the orders by description
	 * @param $description
	 * @return array
	 */
	public static function getItemsListbyDescription($description) {
		$items = array ();
		if (! empty ( $description )) {
			$registry = Zend_Registry::getInstance ();
			$translations = $registry->Zend_Translate;
			
			$dq = Doctrine_Query::create ()->select ( "oi.detail_id, o.order_id as order_id, s.status as status, DATE_FORMAT(order_date, '%d/%m/%Y') as orderdate, oi.description as description" )->from ( 'OrdersItems oi' )->leftJoin ( 'oi.Orders o' )->leftJoin ( 'o.Statuses s' )->where ( 'oi.description like ?', "%" . $description . "%" );
			$retval = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
			foreach ( $retval as $c ) {
				$description = $c ['description'];
				$description = str_replace ( "\n", "", $description );
				$items [$c ['order_id']] = $c ['order_id'] . " - " . $c ['orderdate'] . " - " . $description . " - [" . $c['status']. "] ";
			}
		}
		
		return $items;
	}
	
	/**
	 * setStatus
	 * Set a record with a status
	 * @param $id, $status
	 * @return Void
	 */
	public static function set_status($id, $status) {
		$dq = Doctrine_Query::create ()->update ( 'OrdersItems oi' )->set ( 'status_id', $status )->where ( "detail_id = ?", $id );
		return $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
	}
	
	/**
	 * setAutorenew
	 * Set the autorenew for the record selected 
	 * @param $id
	 * @return Boolean
	 */
	public static function setAutorenew($id, $value=1) {
		return Doctrine_Query::create ()
						->update ( 'OrdersItems oi' )
						->set ( 'autorenew', $value )
						->where ( "detail_id = ?", $id )
						->execute ();
	}
	
	/**
	 * Update the product system parameters of the service by service id (detail_id)
	 * 
	 * Parameters sample array 
	 * =======================
	 * 
	 *  array(5) {
	 *	  ["webspace"] => string(3) "500"
	 *	  ["trafficdata"] => string(4) "1000"
	 *	  ["emails"] => string(1) "5"
	 *	  ["databases"] => string(1) "1"
	 *	  ["mailalias"] => string(4) "1000"
	 *	}
	 *  
	 * @param $detail_id
	 * @param $parameters 
	 * @return Boolean
	 */
	public static function updateSysParameters($detail_id, array $parameters) {
		if(is_numeric($detail_id) && !empty($parameters)){
			$oi = Doctrine::getTable ( 'OrdersItems' )->find ( $detail_id );
			$oi['parameters'] = json_encode($parameters);
			$oi->save();
			return true;
		}
		return false;
	}
	
	/**
	 * saveAll
	 * Save all the data 
	 * @param $params
	 * @return Boolean
	 */
	public static function saveAll($id, $params) {
		$date_end = null;
		
		if(!is_array($params))
			return false;
		
		if(!empty($params['billing_cycle_id']))
			$months = BillingCycle::getMonthsNumber ( $params ['billing_cycle_id'] );
			if ($months > 0) 
				$params ['date_end'] = Shineisp_Commons_Utilities::add_date ( $params ['date_start'], null, $months );
			
		
		$details = Doctrine::getTable ( 'OrdersItems' )->find ( $id );
		$details->quantity         = $params ['quantity'];
		$details->date_start       = Shineisp_Commons_Utilities::formatDateIn ( $params ['date_start'] );
		$details->date_end         = Shineisp_Commons_Utilities::formatDateIn($params ['date_end']);
		$details->billing_cycle_id = !empty($params['billing_cycle_id']) ? $params ['billing_cycle_id'] : null;
		$details->price            = $params ['price'];
		$details->cost             = $params ['cost'];
		$details->product_id       = is_numeric($params ['product_id']) && $params ['product_id'] > 0 ? $params ['product_id'] : NULL;
		$details->setupfee         = $params ['setupfee'];
		$details->status_id        = $params ['status_id'];
		$details->description      = $params ['description'];
		$details->parameters       = $params ['parameters'];
		
		if($details->trySave ())
			OrdersItems::setAutorenew($id, $params ['autorenew']);

			// Remove all domains
			OrdersItemsDomains::removeAllDomains($id);

			if ($params ['domains_selected']) {
				foreach ( $params ['domains_selected'] as $domain ) {
					OrdersItemsDomains::addDomain($details ['order_id'], $domain);
				}
			}
			
			return true;
		
		
		return false;
	}
	
	/**
	 * setNewStatus
	 * Set the status in a group of records
	 * @param $orderid, $status
	 * @return Void
	 */
	public static function setNewStatus($orderid, $status) {
		
		$dq = Doctrine_Query::create ()->update ( 'OrdersItems oi' )->set ( 'status_id', $status )->where ( "order_id = ?", $orderid );
		return $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
	}
	
	/**
	 * Get the product bought by the customer in the orderitems
	 * @param $detail_id
	 * @param $locale
	 * @return ArrayObject
	 */
	public static function getDetail($detail_id, $locale=1) {
		
		$record = Doctrine_Query::create ()->from ( 'OrdersItems oi' )
										   ->leftJoin ( 'oi.Products p' )
										   ->leftJoin ( "p.ProductsData pd WITH pd.language_id = $locale" )
										   ->where ( "detail_id = ?", $detail_id )
										   ->limit ( 1 )
										   ->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
										   
		return !empty($record[0]) ? $record[0] : array();
	}
	
	/**
	 * getAllInfo
	 * Get all data starting from the detail ID 
	 * @param $id
	 * @return Doctrine Record / Array
	 */
	public static function getAllInfo($id, $fields = "*", $where = "", $locale=1) {
		
		try {
			$dq = Doctrine_Query::create ()->from ( 'OrdersItems oi' )
										   ->leftJoin ( 'oi.Orders o' )
										   ->leftJoin ( 'o.OrdersItemsDomains oid' )
										   ->leftJoin ( 'oid.Domains d' )
										   ->leftJoin ( 'd.DomainsTlds dt' )
										   ->leftJoin ( 'dt.WhoisServers ws' )
										   ->leftJoin ( 'dt.DomainsTldsData dtd' )
										   ->leftJoin ( 'dt.Taxes tax' )
										   ->leftJoin ( 'o.Customers c' )
										   ->leftJoin ( 'oi.Statuses s' )
										   ->leftJoin ( 'oi.Products p' )
										   ->leftJoin ( 'p.ProductsAttributesGroups pag' )
										   ->leftJoin ( 'pag.ProductsAttributesGroupsIndexes pagi' )
										   ->leftJoin ( 'p.ProductsAttributesIndexes pai' )
										   ->leftJoin ( 'pai.ProductsAttributes pa' )
										   ->leftJoin ( "p.ProductsData pd WITH pd.language_id = $locale" )
										   ->leftJoin ( 'p.Taxes t' )
										   ->leftJoin ( 'oi.BillingCycle bc' )
										   ->where ( "detail_id = ?", $id )
										   ->limit ( 1 );
			
			if(!empty($fields) && $fields != "*"){
				$dq->select ( $fields );
			}							   
										   
			if ($where) {
				$dq->andWhere ( $where );
			}
			
			$items = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
			
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		
		return !empty($items[0]) ? $items[0] : array();
	}
	
	/**
	 * find
	 * Get a record by ID
	 * @param $id
	 * @return Doctrine Record
	 */
	public static function find($id, $fields = "*", $retarray = false) {
		$id = intval($id);
		$dq = Doctrine_Query::create ()->select ( $fields )->from ( 'OrdersItems oi' )->where ( "detail_id = $id" )->limit ( 1 );
		$retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
		$item = $dq->execute ( array (), $retarray );
		return $item;
	}
	
	/**
	 * getAllDetails
	 * Get all Details starting from the orderID 
	 * @param $id
	 * @return Doctrine Record / Array
	 */
	public static function getAllDetails($id, $fields = "*", $retarray = false) {
		$dq = Doctrine_Query::create ()->select ( $fields )->from ( 'OrdersItems oi' )
		->leftJoin ( 'oi.Orders o' )
		->leftJoin ( 'oi.DomainsTlds dt' )
	    ->leftJoin ( 'dt.DomainsTldsData dtd' )
	    ->leftJoin ( 'dt.Taxes tax' )
		->leftJoin ( 'o.Customers c' )
		->leftJoin ( 'oi.Statuses s' )
		->leftJoin ( 'oi.Products p' )
		->leftJoin ( 'oi.BillingCycle bc' )
		->where ( "order_id = ?", $id );
		
		$retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
		$items = $dq->execute ( array (), $retarray );
		
		return $items;
	}
	
	/**
	 * Get the setup information of a particular service 
	 * 
	 * @param integer $itemid
	 */
	public static function getSetupConfig($itemid){
		if(is_numeric($itemid)){
			$dq = Doctrine_Query::create ()->select ( "setup" )->from ( 'OrdersItems oi' )->where ( "detail_id = ?", $itemid )->limit(1);
			
			$records = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
			return !empty($records[0]['setup']) ? json_decode($records[0]['setup'], true) : array();
		}
		return false;
	}
	
	
	/**
	 * getAllRecurringServices
	 * Get all Services subscribed by the customers 
	 * @param $fields, $retarray
	 * @return Doctrine Record / Array
	 */
	public static function getAllRecurringServices($fields="*", $productgroups = array(), $locale=1) {
		$items = Doctrine_Query::create ()
						->from ( 'OrdersItems oi' )
						->leftJoin ( 'oi.BillingCycle bc' )
						->leftJoin ( 'oi.Orders o' )
						->leftJoin ( 'o.Customers c' )
						->leftJoin ( 'oi.Products p' )
						->leftJoin ( 'p.ProductsAttributesGroups pag' )
						->leftJoin ( 'oi.OrdersItemsDomains oid' )
						->leftJoin ( 'oid.Domains d' )
						->leftJoin ( 'd.DomainsTlds dt' )
						->leftJoin ( 'dt.WhoisServers ws' )
						->leftJoin ( "p.ProductsData pd WITH pd.language_id = $locale" )
						->where ( "pag.isrecurring = ?", 1)
						->andWhere ( "oi.status_id = ?", Statuses::id("complete", "orders") )	// with status "Complete"
						->orderBy('YEAR(oi.date_end), MONTH(oi.date_end)')
						->limit(10);
						
		if($fields != "*"){
			$items->select ($fields);
		}				
			// select a group of product				
		if(count($productgroups)>0){
			foreach ($productgroups as $group_id){
				$groups[] = "p.group_id = ?";
			}
			$items->andWhere ( "(" . implode(" OR ", $groups) . ")", $productgroups);
		}						
	
		$records = $items->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		return $records;
	}
		
	/**
	 * RecurringService_datagraph
	 * Create the recurring services group by month
	 * @param unknown_type $services
	 */
	private function RecurringService_datagraph($data, $year){
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		$datagraph = array();
		$total = 0;
		
		for ($i=1; $i<=12; $i++){
			$datagraph[$i]['cost'] = 0;
			$datagraph[$i]['price'] = 0;
			$datagraph[$i]['monthnumber'] = $i;
			$datagraph[$i]['monthname'] = $translator->translate(date("F", mktime(0, 0, 0, $i, 1, date('Y'))));
	
			foreach ($data as $item) {
				if($i == $item['monthnumber'] ){
					$datagraph[$i]['yeardate'] = $item['yeardate'];
					$datagraph[$i]['cost'] += $item['cost'];
					$datagraph[$i]['price'] += $item['price'];
					
					#$datagraph[$i][] = $item;
				}
			}
		}
		
		return $datagraph;
	}	
	
	
	/**
	 * RecurringServiceslist
	 * Create the recurring services list
	 * @param unknown_type $services
	 */
	private function RecurringServiceslist($services){
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		$recurringservices = array();
		
		// Loop for each services found in the database
		foreach ($services as $item){
			
			$item['months'] = !empty($item['months']) ? $item['months'] : 12;
			
			// Now we have to check the length of service. The duration of service is expressed in days.
			$times = round(365 / $item['months']);
			
			if($times > 1){
				// We have to create all the future items
				for ($i = 1; $i <= $times; $i++) {
					if($i >= $item['monthnumber']){
						$item['monthnumber'] = $i;
						$item['monthname'] = $translator->translate(date("F", mktime(0, 0, 0, $i, 1, $item['yeardate'])));
						$recurringservices[] = $item;
					}
				}
			}else{
				$recurringservices[] = $item;
			}
		}
		
		// Delete some array indexes
		for ($i=0;$i<count($recurringservices); $i++){
			unset($recurringservices[$i]['months']);
			unset($recurringservices[$i]['billingcycle']);
		}
		
		return $recurringservices;
	}
	
	/**
	 * Check if the product has been bought from a customer 
	 * 
	 * 
	 * @param $product_id
	 * @return Array
	 */
	public static function CheckIfProductExist($product_id) {
		$total = 0;
		
		if(is_numeric($product_id)){
			$dq = Doctrine_Query::create ()->select ( 'count(*) as total' )->from ( 'OrdersItems oi' )->where ( "product_id = ?", $product_id );
			$items = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
			$total = $items [0] ['total'];
		}
		
		return $total;
	}
	
	/**
	 * ProductsInOrdersItems
	 * Get all the orders where the product has been selected.
	 * @return array
	 */
	public static function ProductsInOrdersItems($product_id) {
		if (is_numeric ( $product_id )) {
			$records = Doctrine_Query::create ()->select ( 'DATE_FORMAT(oi.date_start, "%d/%m/%Y") as date, CONCAT(c.firstname, " ", c.lastname, " - ", c.company) as customer, oi.quantity, oi.order_id as orderid, s.status as status' )
										   ->from ( 'OrdersItems oi' )
										   ->leftJoin( 'oi.Orders o' )
										   ->leftJoin( 'o.Statuses s' )
										   ->leftJoin( 'o.Customers c' )
										   ->where ( "product_id = ?", $product_id )
										   ->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
			return $records;
		}
	}
	
	/**
	 * Save the setup configuration
	 * 
	 * 
	 * @param integer $id OrdersItems::detail_id
	 * @param array $data
	 * @param string $section
	 */
	public static function setNewSetup($id, array $data, $section) {
		
		// Check the main variables
		if(empty($id) || !is_numeric($id) || !is_array($data) || empty($section)){
			return false;
		}
		
		// Get the service setup information
		$service = Doctrine_Query::create ()->select('setup')
										->from ( 'OrdersItems oi' )
										->where ( 'oi.detail_id = ?', $id )
										->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		if(!empty($service[0])){
			// Create the old array
			$oldSetup = json_decode($service[0]['setup'], true);
			
			// Override the old configuration
			$oldSetup[$section] = $data;
			
			// Create the new configuration
			$newSetup = json_encode($oldSetup);

			// Save the new configuration
			Doctrine_Query::create ()->update ( "OrdersItems oi" )
											->set ( 'oi.setup', "?", $newSetup )
											->where ( "oi.detail_id = ?", $id )
											->execute ();
			return true;
		}
		
		return false;							
		
	}
	
	
	/**
	 * Get the setup configuration
	 * 
	 * 
	 * @return ArrayObject
	 */
	public static function getSetup($id) {
		$setup = "";
		$services = array();
		$panel = Isp::getPanel();
		$records = Doctrine_Query::create ()
										->from ( 'OrdersItems oi' )
										->leftJoin( 'oi.Products p' )
										->where ( 'oi.detail_id = ?', $id )
										->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		
		foreach ($records as $record) {
			if(!empty($record['Products']['setup'])){
				$setup = $record['Products']['setup'];
				
				// Get the parameters set in the order details (service)
				$params = json_decode($record['parameters'], true);
				if(is_array($params)){

					// Get the list of all the domains
					$domains = OrdersItemsDomains::get_domains($record['detail_id']);

					// We have to get the first domain
					if(!empty($domains[0]['domain'])){
						$params['domain'] = $domains[0]['domain'];
						
						// Get all the var {string} in the xml setup 
						preg_match_all( '/{([^}]+)}/Ui', $setup, $matches );
						foreach ($matches[1] as $parameter) {
							$setup = str_replace("{".$parameter."}", $params[$parameter], $setup);
						}
					}
				}
			}
		}
		
		$xml = simplexml_load_string($setup);		
		$arrSetup = Shineisp_Commons_Utilities::simpleXMLToArray($xml);
		
		return $arrSetup;
	}
	

	/**
	 * Save the setup configuration
	 * 
	 * 
	 * @param integer $id OrdersItems::detail_id
	 * @param array $data
	 * @param string $section
	 */
	public static function set_setup($id, array $data, $section) {
		
		// Check the main variables
		if(empty($id) || !is_numeric($id) || !is_array($data) || empty($section)){
			return false;
		}
		
		// Get the service setup information
		$service = Doctrine_Query::create ()->select('setup')
										->from ( 'OrdersItems oi' )
										->where ( 'oi.detail_id = ?', $id )
										->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		if(!empty($service[0])){
			// Create the old array
			$oldSetup = json_decode($service[0]['setup'], true);
			
			// Override the old configuration
			$oldSetup[$section] = $data;
			
			// Create the new configuration
			$newSetup = json_encode($oldSetup);

			// Save the new configuration
			Doctrine_Query::create ()->update ( "OrdersItems oi" )
											->set ( 'oi.setup', "?", $newSetup )
											->where ( "oi.detail_id = ?", $id )
											->execute ();
			return true;
		}
		
		return false;							
		
	}
	
	/***
	 * Get the refund info
	 * @param orderid
	 * @return productid and refundPrice
	 *****/
	public static function getRefundInfo( $orderid ) {
		$service 	= self::getDetail($orderid);
		$product	= $service['Products'];
		$productid	= $product['product_id'];
		
        $productInfo    = Products::getAllInfo($productid);
        $isrefundable   = intval($productInfo['isrefundable']);
        $priceRefund    = 0;
        if( $isrefundable > 0 ) {
    		$pricePayed	= $service['price'];
    		
    		$date		= explode(' ',$service['date_start']);
    		$date		= array_shift($date);
    		list($yyyy,$mm,$dd)	= explode('-',$date);
    		$tsStartService		= mktime(0,0,0,$mm,$dd,$yyyy);
    		
    		$date		= explode(' ',$service['date_end']);
    		$date		= array_shift($date);
    		list($yyyy,$mm,$dd)	= explode('-',$date);
    		$tsEndService	= mktime(0,0,0,$mm,$dd,$yyyy);
    		$tsToday		= mktime(0,0,0,date('m'),date('d'),date('Y'));
    		
    		$dayService		= round( ($tsEndService - $tsStartService) / ( 60*60*24 ) );
    		$priceServiceForDay	= $pricePayed / $dayService;
    		
    		$tsRemain		= 0;
    		$priceRefund	= false;
    		if( $tsEndService > $tsToday ) {
    			$dayRemain		= round( ( $tsEndService - $tsToday ) / (60*60*24) );
    			$priceRefund	= round($priceServiceForDay * $dayRemain,2);
    		}
        }
		
		$result	= array(
			 'productid'	=> $productid
			,'refund'		=> $priceRefund
		);
        
		return $result;					
	}	
	
	######################################### CRON METHODS ############################################

	/**
	 * checkservicesAction
	 * CREATE THE ORDER FOR ALL THE AUTORENEWABLE DOMAINS/SERVICES
	 * Check all the services [domains, products] and create the orders for each customer only if the service has been set as renewable
	 * @return void
	 */
	public static function checkServices() {
		try {
			$isp = Isp::getActiveISP ();
			$i = 0;
			$customers = array ();
				
			/* We have to start to get all the domains that them expiring date is today
			 then we have to create a custom array sorted by customerID in order to
			group services and domains of a particular customer.
			*/
				
			// Get all the active domains that expire in 1 day
			$domains = Domains::getExpiringDomainsByDays ( 1, Statuses::id("active", "domains") );
				
			if ($domains) {
				// Create the customer group list for the email summary
				foreach ( $domains as $domain ) {
					if (is_numeric($domain ['reseller'])) {
						$invoice_dest = Customers::getAllInfo ( $domain ['reseller'] );
						$customers [$domain ['customer_id']] ['id'] = $invoice_dest ['customer_id'];
						$customers [$domain ['customer_id']] ['fullname'] = $invoice_dest ['firstname'] . " " . $invoice_dest ['lastname'] . " " . $invoice_dest ['company'];
						$customers [$domain ['customer_id']] ['email'] = $invoice_dest ['email'];
					} else {
						$customers [$domain ['customer_id']] ['id'] = $domain ['customer_id'];
						$customers [$domain ['customer_id']] ['fullname'] = $domain ['fullname'];
						$customers [$domain ['customer_id']] ['email'] = $domain ['email'];
					}
					$customers [$domain ['customer_id']] ['products'] [$i] ['name'] = $domain ['domain'];
					$customers [$domain ['customer_id']] ['products'] [$i] ['type'] = "domain";
					$customers [$domain ['customer_id']] ['products'] [$i] ['renew'] = $domain ['renew'];
					$customers [$domain ['customer_id']] ['products'] [$i] ['expiring_date'] = $domain ['expiringdate'];
					$customers [$domain ['customer_id']] ['products'] [$i] ['days'] = $domain ['days'];
					$customers [$domain ['customer_id']] ['products'] [$i] ['oldorderitemid'] = $domain ['detail_id'];
					$i ++;
				}
			}
				
			/*
			 * Now we have to get the services expired and we have to sum the previous $customers array with these
			* new information.
			*/
				
			// Get all the services active that expire the day after
			$services = OrdersItems::getExpiringSerivcesByDays ( 1, Statuses::id("complete", "orders") );
			
			if ($services) {
				// Create the customer group list for the email summary
				foreach ( $services as $service ) {
					if (is_numeric($service ['reseller'])) {
						$invoice_dest = Customers::getAllInfo ( $service ['reseller'] );
						$customers [$service ['customer_id']] ['id'] = $invoice_dest ['customer_id'];
						$customers [$service ['customer_id']] ['fullname'] = $invoice_dest ['firstname'] . " " . $invoice_dest ['lastname'] . " " . $invoice_dest ['company'];
						$customers [$service ['customer_id']] ['email'] = $invoice_dest ['email'];
						$customers [$service ['customer_id']] ['password'] = $invoice_dest ['password'];
					} else {
						$customers [$service ['customer_id']] ['id'] = $service ['id'];
						$customers [$service ['customer_id']] ['fullname'] = $service ['fullname'];
						$customers [$service ['customer_id']] ['email'] = $service ['email'];
						$customers [$service ['customer_id']] ['password'] = $service ['password'];
					}
					$customers [$service ['customer_id']] ['products'] [$i] ['name'] = $service ['product'];
					$customers [$service ['customer_id']] ['products'] [$i] ['type'] = "service";
					$customers [$service ['customer_id']] ['products'] [$i] ['renew'] = $service ['renew'];
					$customers [$service ['customer_id']] ['products'] [$i] ['expiring_date'] = $service ['expiringdate'];
					$customers [$service ['customer_id']] ['products'] [$i] ['days'] = $service ['days'];
					$customers [$service ['customer_id']] ['products'] [$i] ['oldorderitemid'] = $service ['detail_id'];
					$i ++;
				}
			}
				
			// Create the emailS for the customers
			if (count ( $customers ) > 0) {
	
				$signature = $isp ['company'] . "\n" . $isp ['email'];
				foreach ( $customers as $customer ) {
					$items = "";
						
					// **** CREATE THE ORDER FOR ALL THE AUTORENEWABLE DOMAINS/SERVICES ***
					// ============================================================
					// Renew all the services and domain where the customer has choosen the autorenew of the service.
					$orderID = Orders::renewOrder ( $customer ['id'], $customer ['products'] );
					if (is_numeric ( $orderID )) {
						$link = Fastlinks::findlinks ( $orderID, $customer ['id'], 'orders' );
	
						// Create the fast link to include in the email
						if (! empty ( $link [0] ['code'] )) {
							$url = "http://" . $_SERVER ['HTTP_HOST'] . "/index/link/id/" . $link [0] ['code'];
						} else {
							$url = "http://" . $_SERVER ['HTTP_HOST'];
						}
	
						// Get the template from the main email template folder
						$retval = Shineisp_Commons_Utilities::getEmailTemplate ( 'order_renew' );
						if ($retval) {
							$subject = $retval ['subject'];
							$Template = $retval ['template'];
							$Template = str_replace ( "[fullname]", $customer ['fullname'], $Template );
							$Template = str_replace ( "[email]", $isp ['email'], $Template );
							$Template = str_replace ( "[url]", $url, $Template );
							$Template = str_replace ( "[signature]", $signature, $Template );
							Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], $customer ['email'], $isp ['email'], $subject, $Template );
						}
					}
				}
			}
				
			/*
			 * Now we have to set as expired all the domains records that the date is the date of the expiring of the domain
			* // Expired
			*/
			$dq = Doctrine_Query::create ()->update ( 'Domains d' )->set ( 'd.status_id', 5 )->where ( 'DATEDIFF(d.expiring_date, CURRENT_DATE) <= ?', 0 )->addWhere ( 'DATEDIFF(d.expiring_date, CURRENT_DATE) >= ?', 0 );
			$dq->execute ( null, Doctrine::HYDRATE_ARRAY );
				
			/*
			 * Now we have to set as closed all the domains records that the date is older of -2 days
			* // Closed
			*/
			$dq = Doctrine_Query::create ()->update ( 'Domains d' )->set ( 'd.status_id', 28 )->where ( 'DATEDIFF(d.expiring_date, CURRENT_DATE) <= ?', - 2 );
			$dq->execute ( null, Doctrine::HYDRATE_ARRAY );
				
			/*
			 * Now we have to set as expired all the services records
			* // Expired
			*/
			$dq = Doctrine_Query::create ()->update ( 'OrdersItems oi' )->set ( 'oi.status_id', 10 )->where ( 'DATEDIFF(oi.date_end, CURRENT_DATE) <= ?', 0 );
			$dq->execute ( null, Doctrine::HYDRATE_ARRAY );
				
			/*
			 * Now we have to set as deleted all the services records
			* // Deleted
			*/
			$dq = Doctrine_Query::create ()->update ( 'OrdersItems oi' )->set ( 'oi.status_id', 20 )->where ( 'DATEDIFF(oi.date_end, CURRENT_DATE) <= ?', - 2 );
			$dq->execute ( null, Doctrine::HYDRATE_ARRAY );
				
			Shineisp_Commons_Utilities::sendEmailTemplate($isp ['email'], 'cron', array(
				 'storename'  => $isp ['company']
				,'email'      => $isp ['email']
				,'signature'  => $isp ['company'] . "\n" . $isp ['email']
				,'cronjob'    => 'Check Services'
			));				
				
				
		} catch ( Exception $e ) {
			return $e->getMessage () ;
		}
	
		return true;
	}

}