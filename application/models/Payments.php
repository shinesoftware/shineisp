<?php

/**
 * Payments
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Payments extends BasePayments
{

	public static function grid($rowNum = 10) {
	
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
	
		$columns [] = array ('label' => null, 'field' => 'p.payment_id', 'alias' => 'payment_id', 'type' => 'selectall' );
		$columns [] = array ('label' => $translator->translate ( 'ID' ), 'field' => 'p.payment_id', 'alias' => 'payment_id', 'type' => 'integer', 'sortable' => true, 'attributes' => array ('width' => 70 ), 'searchable' => true );
		$columns [] = array ('label' => $translator->translate ( 'Invoice' ), 'field' => 'i.number', 'alias' => 'invoice', 'type' => 'integer', 'sortable' => true, 'attributes' => array ('width' => 70 ), 'searchable' => true );
		$columns [] = array ('label' => $translator->translate ( 'Transaction Date' ), 'field' => 'p.paymentdate', 'alias' => 'paymentdate', 'type' => 'date', 'sortable' => true, 'attributes' => array ('width' => 70 ), 'searchable' => true );
		$columns [] = array ('label' => $translator->translate ( 'Order Date' ), 'field' => 'o.order_date', 'alias' => 'orderdate', 'type' => 'date', 'sortable' => true, 'attributes' => array ('width' => 70 ), 'searchable' => true );
	
		$columns [] = array ('label' => $translator->translate ( 'Company' ), 'field' => "CONCAT(c.firstname, ' ', c.lastname, ' ', c.company)", 'alias' => 'customer', 'sortable' => true, 'searchable' => true, 'type' => 'string');
		$columns [] = array ('label' => $translator->translate ( 'Reseller' ), 'field' => "CONCAT(r.company, ' ', r.firstname,' ', r.lastname)", 'alias' => 'reseller', 'sortable' => true, 'searchable' => true, 'type' => 'string');
	
		$columns [] = array ('label' => $translator->translate ( 'Grand Total' ), 'field' => 'o.grandtotal', 'alias' => 'grandtotal', 'sortable' => true, 'type' => 'float' );
		$columns [] = array ('label' => $translator->translate ( 'Income' ), 'field' => 'p.income', 'alias' => 'income', 'sortable' => true, 'type' => 'float', 'searchable' => true);
		$columns [] = array ('label' => $translator->translate ( 'Outcome' ), 'field' => 'p.outcome', 'alias' => 'outcome', 'sortable' => true, 'type' => 'float', 'searchable' => true);
	
	
		$config ['datagrid'] ['columns'] = $columns;
		$config ['datagrid'] ['fields'] = "p.payment_id,
                                              DATE_FORMAT(p.paymentdate, '%d/%m/%Y') as paymentdate,
                                              CONCAT(c.firstname, ' ', c.lastname, ' ', c.company) as customer,
                                              CONCAT(r.firstname, ' ', r.lastname, ' ', r.company) as reseller,
                                              p.reference as reference,
                                              p.confirmed as confirmed,
                                              p.income as income,
                                              p.outcome as outcome,
                                              i.number as invoice,
											  o.grandtotal as grandtotal,
                                              o.order_date as orderdate,
											  o.order_id as orderid";
	
		$config ['datagrid'] ['dqrecordset'] = Doctrine_Query::create ()->select ( $config ['datagrid'] ['fields'] )
																		->from ( 'Payments p' )
																		->leftJoin ( 'p.Customers c' )
																		->leftJoin ( 'c.Customers r' )
																		->leftJoin ( 'p.Banks b' )
																		->leftJoin ( 'p.Orders o' )
																		->leftJoin ( 'o.Invoices i' )
																		->leftJoin ( 'o.Statuses s' )
																		->orderBy ( 'paymentdate desc' );
	
		$config ['datagrid'] ['rownum'] = $rowNum;
		$config ['datagrid'] ['basepath'] = "/admin/payments/";
		$config ['datagrid'] ['index'] = "order_id";
		$config ['datagrid'] ['rowlist'] = array ('10', '50', '100', '1000' );
	
		$config ['datagrid'] ['buttons'] ['edit'] ['label'] = $translator->translate ( 'Edit' );
		$config ['datagrid'] ['buttons'] ['edit'] ['cssicon'] = "edit";
		$config ['datagrid'] ['buttons'] ['edit'] ['action'] = "/admin/payments/edit/id/%d";
	
		$config ['datagrid'] ['buttons'] ['delete'] ['label'] = $translator->translate ( 'Delete' );
		$config ['datagrid'] ['buttons'] ['delete'] ['cssicon'] = "delete";
		$config ['datagrid'] ['buttons'] ['delete'] ['action'] = "/admin/payments/delete/id/%d";
		$config ['datagrid'] ['massactions']['common'] = array ('bulk_delete'=>'Mass Delete', 'bulk_export'=>'Export List');
		
		return $config;
	}
	

	/**
	 * Get a record by ID
	 * @param $id
	 */
	public static function getbyId($id) {
		return Doctrine::getTable ( 'Payments' )->find ( $id );
	}
	

	/**
	 * getAllInfo
	 * Get all data starting from the paymentId
	 * @param $id
	 * @return Doctrine Record / Array
	 */
	public static function getAllInfo($id, $fields = "*", $retarray = false) {
		try {
			$dq = Doctrine_Query::create ()->from ( 'Payments p2' )
											->leftJoin ( 'p2.Orders o' )
											->leftJoin ( 'o.Isp i' )
											->leftJoin ( 'o.Invoices in' )
											->leftJoin ( 'o.OrdersItems oi' )
											->leftJoin ( 'oi.BillingCycle bc' )
											->leftJoin ( 'o.OrdersItemsDomains oid' )
											->leftJoin ( 'oid.Domains d' )
											->leftJoin ( 'd.DomainsTlds tld' )
											->leftJoin ( 'tld.WhoisServers w' )
											->leftJoin ( 'oi.Products p' )
											->leftJoin ( 'p.Taxes t' )
											->leftJoin ( 'o.Customers c' )
											->leftJoin ( 'c.Addresses a' )
											->leftJoin ( 'a.Countries co' )
											->leftJoin ( 'o.Statuses s' )
											->where ( "payment_id = ?", $id )
											->limit ( 1 );
				
			if($fields != "*"){
				$dq->select ( $fields );
			}
				
			$retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
			$items = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
				
			return $items;
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
	}
	
    /**
     * find
     * Get a record by ID
     * @param $id
     * @return Doctrine Record
     */
    public static function find($id, $fields = "*", $retarray = false) {
        $dq = Doctrine_Query::create ()->select ( $fields )->from ( 'Payments p' )
        ->where ( "p.payment_id = $id" )->limit ( 1 );
        
        $retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
        $record = $dq->execute ( array (), $retarray );
        return $record;
    }
    
    /**
     * getAllPaymentsbyMonthYear
     * Get all the payment by year
     * @param $year
     * @return Array
     */
    public static function getAllPaymentsbyMonthYear($month, $year) {
        $record = Doctrine_Query::create ()
        				->select('payment_id, 
        						  CONCAT(c.firstname, " ", c.lastname) as fullname,
        						  b.name as bank, 
        						  i.number as invoice, 
        						  order_id as orderid, 
        						  DATE_FORMAT(paymentdate, "%d/%m/%Y") as date, 
        						  o.total as total,
        						  o.vat as vat,
        						  o.grandtotal as grandtotal')
                        ->from ( 'Payments p' )
                        ->leftJoin ( 'p.Orders o' )
                        ->leftJoin ( 'o.Customers c' )
                        ->leftJoin ( 'o.Invoices i' )
                        ->leftJoin ( 'p.Banks b' )
                        ->where('YEAR(o.order_date) = ?', $year)
                        ->andWhere('MONTH(o.order_date) = ?', $month)
                        ->andWhere('o.invoice_id is not null') // Invoiced
                        ->andWhere('o.status_id = ?', Statuses::id("complete", "orders")) // Complete
                        ->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
        
        return $record;
    }
    
    /**
     * Save the record
     * @param posted var from the form
     * @return Boolean
     */
    public static function saveData($record) {
    	 
    	// Set the new values
    	if (is_numeric ( $record['payment_id'] )) {
    		$payment = self::getbyId( $record['payment_id'] );
    	}else{
    		$payment = new Payments();
    	}
    	
    	$payment->paymentdate = Shineisp_Commons_Utilities::formatDateIn($record['paymentdate']);
    	$payment->bank_id = $record['bank_id'];
    	$payment->customer_id = $record['customer_id'];
    	$payment->order_id = $record['order_id'];
    	$payment->description = $record['description'];
    	$payment->reference = $record['reference'];
    	$payment->confirmed = $record['confirmed'];
    	$payment->income = $record['income'];
    	$payment->outcome = $record['outcome'];
    	
    	if($payment->trySave()){
    		return $payment->payment_id;
    	}
    	
    	return false;
    }
        
    /**
     * Get a record by ID
     * 
     * 
     * @param $order_id
     * @param $fields
     * @param $retarray
     * @return Doctrine Record
     */
    public static function findbyorderid($order_id, $fields = "*", $retarray = false) {
        $dq = Doctrine_Query::create ()
                        ->from ( 'Payments p' )
                        ->leftJoin ( 'p.Banks b' )
                        ->where ( "p.order_id = ?", $order_id );
                        //->limit ( 1 );
        
        if($fields != "*"){
        	$dq->select ( $fields );
        }
        
        $retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
        $record = $dq->execute ( array (), $retarray );
        return $record;
    }
    
    /**
     * addpayment
     * Add a payment information to a order
     * @param integer $orderid
     * @param string $transactionid
     * @param integer $bankid
     * @param boolean $status
     * @param float $amount
     */
    public static function addpayment ($orderid, $transactionid, $bankid, $status, $amount, $paymentdate = null, $customer_id = null, $payment_description = null) {

        
    	$paymentdata = self::findbyorderid ( $orderid, null, true );
    			
    	if (count ( $paymentdata ) == 0) {
			$payment = new Payments ();
		} else {
			$payment = Doctrine::getTable ( 'Payments' )->find ( $paymentdata [0] ['payment_id'] );
		}

		// We make a double check to properly manage "null" output coming from Shineisp_Commons_Utilities::formatDateIn
		if ( !empty($paymentdate) ) {
			$paymentdate = Shineisp_Commons_Utilities::formatDateIn ( $paymentdate );
		}
		$paymentdate = !empty($paymentdate) ? $paymentdate : date ( 'Y-m-d H:i:s' );
		
    	// Set the payment data
		$payment->order_id    = $orderid;
		$payment->bank_id     = $bankid;
		$payment->reference   = $transactionid;
		$payment->confirmed   = $status ? 1 : 0;
		$payment->income      = $amount;
		
		// Additional fields for Orders::saveAll()
		$payment->paymentdate = $paymentdate;
		$payment->customer_id = isset($customer_id)         ? intval($customer_id) : intval(Orders::getCustomer($orderid));
		$payment->description = isset($payment_description) ? $payment_description : null;

		$save = $payment->trySave ();
        
		if ( $save ) {
			Shineisp_Commons_Utilities::logs("Payments::addPayment(): save ok", "tmp_guest.log");
			// Let's check if we have the whole invoice paid.
			$isPaid = Orders::isPaid($orderid);
			Shineisp_Commons_Utilities::logs("Payments::addPayment(): verifica pagamento completato.", "tmp_guest.log");
			if ( $isPaid ) {
				Shineisp_Commons_Utilities::logs("Payments::addPayment(): isPaid ok, pagamento completato al 100%", "tmp_guest.log");
                // Set order status as "Paid"
                Orders::set_status($orderid, Statuses::id('paid', 'orders'));
                
				Shineisp_Commons_Utilities::logs("Payments::addPayment(): faccio Orders::activateItems(".$orderid.", 4)", "tmp_guest.log");
				// If we have to autosetup as soon as first payment is received, let's do here.
				Orders::activateItems($orderid, 4);
				
				// If automatic invoice creation is set to 1, we have to create the invoice
				$autoCreateInvoice = intval(Settings::findbyParam('auto_create_invoice_after_payment'));
				if ( $autoCreateInvoice === 1 && !Orders::isInvoiced($orderid) ) {
					// invoice not created yet. Let's create now
					Invoices::Create ( $orderid );
				}
				
			} else {
				Shineisp_Commons_Utilities::logs("Payments::addPayment(): isPaid KO, pagamento non completato", "tmp_guest.log");
				Shineisp_Commons_Utilities::logs("Payments::addPayment(): faccio Orders::activateItems(".$orderid.", 3)", "tmp_guest.log");
				// If we have to autosetup as soon as first payment is received, let's do here.
				Orders::activateItems($orderid, 3);
			}
		}
				
		return $save;
	}
	
	/**
	 * confirm
	 * Confirm a payment
	 * @param integer $orderid
	 */
	public static function confirm($orderid, $confirm = 1) {
		try {
			
			Doctrine_Query::create ()->update ( 'Payments p' )
									->set ( 'p.confirmed', '1' )
									->where('p.order_id = ?', intval($orderid))
									->execute ();
			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}
	

	/**
	 * Get a payment by id lists
	 * @param array $ids [1,2,3,4,...,n]
	 * @param string $fields
	 * @return Array
	 */
	public static function get_payments($ids, $fields="*", $orderby=null) {
		return Doctrine_Query::create ()->select($fields)
										->from ( 'Payments p' )
										->leftJoin ( 'p.Orders o' )
										->leftJoin ( 'o.Invoices i' )
										->leftJoin ( 'o.Customers c' )
										->leftJoin ( 'c.Customers r' )
										->leftJoin ( 'o.Statuses s' )
										->whereIn( "payment_id", $ids)
										->orderBy(!empty($orderby) ? $orderby : "")
										->execute ( array (), Doctrine::HYDRATE_ARRAY );
	}

	/**
	 * Get the order id by the payment id
	 * @param integer $id
	 * @return integer or null
	 */
	public static function getOrderId($id) {
		
		if(is_numeric($id)){
			$record = Doctrine_Query::create ()->select('order_id')
											->from ( 'Payments p' )
											->whereIn( "payment_id", $id)
											->execute ( array (), Doctrine::HYDRATE_ARRAY );
			return !empty($record[0]['order_id']) ? $record[0]['order_id'] : NULL;
		}
		
		return null;
	}
	
	/**
	 * Delete a payment transaction using its ID.
	 * @param $id
	 * @return boolean
	 */
	public static function deleteByID($id) {
		if(is_numeric($id)){
			return Doctrine_Query::create ()->delete ()->from ( 'Payments p' )->where ( 'payment_id = ?', $id )->execute ();
		}else{
			return false;
		}
	}
	
	/**
	 * delete the payment transactions selected
	 * @param array
	 * @return Boolean
	 */
	public static function massdelete($items) {
		if(is_array($items)){
			return Doctrine_Query::create ()->delete ()->from ( 'Payments' )->whereIn ( 'payment_id', $items )->execute ();
		}else{
			return false;
		}
	}
	
	######################################### BULK ACTIONS ############################################
	
	
	/**
	 * massdelete
	 * delete the items selected
	 * @param array
	 * @return Boolean
	 */
	public static function bulk_delete($items) {
		if(!empty($items)){
			return self::massdelete($items);
		}
		return false;
	}
	
	/**
	 * export the content in a pdf file
	 * @param array $items
	 */
	public function bulk_export($items) {
		$isp = Isp::getActiveISP();
		$pdf = new Shineisp_Commons_PdfList();
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
	
		// Get the records from the payment table
		$orders = self::get_payments($items, "p.payment_id,
											  o.order_id as orderid,
											  o.order_date as orderdate,
											  i.number as invoice,
                                              DATE_FORMAT(p.paymentdate, '%d/%m/%Y') as paymentdate,
                                              CONCAT(c.firstname, ' ', c.lastname, ' ', c.company) as customer,
                                              CONCAT(r.firstname, ' ', r.lastname, ' ', r.company) as reseller,
                                              p.reference as reference,
                                              p.confirmed as confirmed,
                                              p.income as income,
                                              p.outcome as outcome,
											  o.grandtotal as grandtotal
                                              ", 'o.order_date');
	
		// Create the PDF header
		$grid['headers']['title'] = $translator->translate('Payment Transactions List');
		$grid['headers']['subtitle'] = $translator->translate('List of the payment transactions');
		$grid['footer']['text'] = $isp['company'] . " - " . $isp['website'];
			
		if(!empty($orders[0])){
	
			// Create the columns of the grid
			$grid ['columns'] [] = array ("value" => $translator->translate('Payment'), 'size' => 50);
			$grid ['columns'] [] = array ("value" => $translator->translate('Order'), 'size' => 50);
			$grid ['columns'] [] = array ("value" => $translator->translate('Order Date'), 'size' => 50);
			$grid ['columns'] [] = array ("value" => $translator->translate('Invoice'), 'size' => 100);
			$grid ['columns'] [] = array ("value" => $translator->translate('Transaction Date'), 'size' => 100);
			$grid ['columns'] [] = array ("value" => $translator->translate('Company'));
			$grid ['columns'] [] = array ("value" => $translator->translate('Reseller'));
			$grid ['columns'] [] = array ("value" => $translator->translate('Reference'));
			$grid ['columns'] [] = array ("value" => $translator->translate('Confirmed'));
			$grid ['columns'] [] = array ("value" => $translator->translate('Income'), 'size' => 50);
			$grid ['columns'] [] = array ("value" => $translator->translate('Outcome'), 'size' => 50);
			$grid ['columns'] [] = array ("value" => $translator->translate('Grand Total'), 'size' => 50);
				
			// Getting the records values and delete the first column the customer_id field.
			foreach ($orders as $item){
				$values = array_values($item);
				$grid ['records'] [] = $values;
			}
	
			// Create the PDF
			die($pdf->create($grid));
		}
		
		return false;
	}
    
}