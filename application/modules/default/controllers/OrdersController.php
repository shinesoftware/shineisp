<?php

class OrdersController extends Shineisp_Controller_Default {
	protected $customer;
	protected $orders;
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Shineisp_Controller_Default#preDispatch()
	 */
	
	public function preDispatch() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		if (empty($NS->customer)) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		}
		$this->customer = $NS->customer;
		$registry = Shineisp_Registry::getInstance ();
		$this->orders = new Orders ();
		$this->translator = $registry->Zend_Translate;
		
		// Set the navigation menu for the client control panel page on the left sidebar
		#$this->view->placeholder ( "left" )->append ( $string);	
		
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
	
	}
	
	/**
	 * indexAction
	 * Redirect the user to the list action
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/default/orders/list' );
	}
	
	public function listAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$arrSort = array ();
		$params = array ();
		
		$page = $this->getRequest ()->getParam ( 'page' );
		$sort = $this->getRequest ()->getParam ( 'sort' );
		
		if (! empty ( $sort )) {
			$arrSort [] = $this->SortingData ( $sort );
			$arrSort [] = $sort;
		}
		
		$params ['search'] = array ('method' => 'andWhere', 'criteria' => "c.customer_id = ? OR c.parent_id = ?", 'value' => array($NS->customer ['customer_id'], $NS->customer ['customer_id']) );
		
		try {
			$page = ! empty ( $page ) && is_numeric ( $page ) ? $page : 1;
			$data = $this->orders->findAll ( "o.order_id, 
												i.formatted_number as Invoice, 
												CONCAT(c.company, ' ', c.firstname,' ', c.lastname) as company, 
												o.order_number as order_number,
												o.order_date as start_date,
												o.grandtotal as Total", 
												$page, $NS->recordsperpage, $arrSort, $params );
			$data ['currentpage'] = $page;
			
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
		
		// Get the status of the items
		if (! empty ( $data ['records'] )) {
			for($i = 0; $i < count ( $data ['records'] ); $i ++) {
				$data ['records'] [$i] ['Status']     = Orders::getStatus ( $data ['records'] [$i] ['order_id'], true );
				$data ['records'] [$i] ['start_date'] = Shineisp_Commons_Utilities::formatDateOut ( $data ['records'] [$i] ['start_date'] );
			}
		}
		
		$data ['columns'][] = $this->translator->translate('Invoice No.');
		$data ['columns'][] = $this->translator->translate('Company');
		$data ['columns'][] = $this->translator->translate('Order No.');
		$data ['columns'][] = $this->translator->translate('Creation Date');
		$data ['columns'][] = $this->translator->translate('Total');
		$data ['columns'][] = $this->translator->translate('Status');
		
		$this->view->headTitle()->prepend ($this->translator->translate('Orders List'));
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		$this->view->title = $this->translator->translate("Orders List");
		$this->view->description = $this->translator->translate("This is the list of all your orders.");
		$this->view->orders = $data;
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form     = $this->getForm ( '/orders/process' );
		$id       = $this->getRequest ()->getParam ( 'id' );
		$NS       = new Zend_Session_Namespace ( 'Default' );
		$currency = Shineisp_Registry::getInstance ()->Zend_Currency;
		
		try {
			if (! empty ( $id ) && is_numeric ( $id )) {
				$fields = "o.order_id, 
							o.order_number as order_number,
							DATE_FORMAT(o.order_date, '%d/%m/%Y') as Starting, 
							DATE_FORMAT(o.expiring_date, '%d/%m/%Y') as Valid_Up, 
							in.invoice_id as invoice_id, 
							in.formatted_number as invoice_number, 
							CONCAT(d.domain, '.', w.tld) as Domain, 
							c.company as company, 
							o.status_id, 
							s.status as Status, 
							o.vat as VAT, 
							o.total as Total, 
							o.grandtotal as Grandtotal";
				
				$rs = Orders::getAllInfo ( $id, $fields, true, $NS->customer ['customer_id'] );
				if (! empty ( $rs )) {
					
					// Check the status of the order. 
					// If the order has to be paid we have update it to the last prices and taxes
					if($rs [0] ['status_id'] == Statuses::id('tobepaid', 'orders')){
						
						// Update the total order
						Orders::updateTotalsOrder($id);

						// Reload the data
						$rs = Orders::getAllInfo ( $id, $fields, true, $NS->customer ['customer_id'] );
						
						$rs[0]['Total'] = $currency->toCurrency($rs[0]['Total'], array('currency' => Settings::findbyParam('currency')));
						$rs[0]['VAT'] = $currency->toCurrency($rs[0]['VAT'], array('currency' => Settings::findbyParam('currency')));
						$rs[0]['Grandtotal'] = $currency->toCurrency($rs[0]['Grandtotal'], array('currency' => Settings::findbyParam('currency')));
						
						$this->view->tobepaid = true; // To be Paid status 
					}
					
					$records = OrdersItems::getAllDetails ( $id, "oi.detail_id, oi.description as description, DATE_FORMAT(oi.date_end, '%d/%m/%Y') as expiration_date, oi.quantity as quantity, oi.price as price, bc.name as billingcycle, oi.setupfee as setupfee", true );
					for ($i=0; $i<count($records); $i++){
						$records[$i]['price'] = $currency->toCurrency($records[$i]['price'], array('currency' => Settings::findbyParam('currency')));;
						$records[$i]['setupfee'] = $currency->toCurrency($records[$i]['setupfee'], array('currency' => Settings::findbyParam('currency')));;
					}

					$this->view->customer_id = $NS->customer ['customer_id'];
					$this->view->invoiced = ($rs [0] ['status_id'] == Statuses::id("complete", "orders") && $rs [0] ['invoice_number'] > 0) ? true : false;
					$this->view->invoice_id = $rs [0] ['invoice_id'];
					$this->view->order = array ('records' => $rs );
					$this->view->details = array ('records' => $records );
					
					// Get Order status history
					$this->view->statushistory = StatusHistory::getStatusList($id);
					
					// Show the list of the messages attached to this domain
					$this->view->messages = Messages::find ( 'order_id', $id, true );
					
					$this->view->headTitle()->prepend ($this->translator->_('Order %s', $rs [0]['order_number']));
					
					$rsfiles = Files::findbyExternalId ( $id, "orders", "file, Date_Format(date, '%d/%m/%Y') as date" );
					if (isset ( $rsfiles [0] )) {
						$this->view->files = $rsfiles;
					}
					
					// Send the data to the form
					$form->populate ( $rs [0] );
					
					$this->view->title = $this->translator->_('Order %s', $rs [0] ['order_number']);
					$this->view->orderid = $id;
					
				}else{
					$this->_helper->redirector ( 'index', 'orders', 'default', array ('mex' => 'Order not found', 'status' => 'information' ) );
					die;
				}
				
			}
			
			#$this->view->title = $this->translator->_('Order %s', $formattedID);
			$this->view->description = "Here you can see all the order information.";
			$this->view->dnsdatagrid = $this->dnsGrid ();
			$this->view->form = $form;
			$this->_helper->viewRenderer ( 'customform' );
		} catch ( Exception $e ) {
			echo $e->getMessage ();
			die ();
		}
	}
	
	/**
	 * renewAction
	 * Renew the order
	 * @return unknown_type
	 */
	public function renewAction() {
		$id = $this->getRequest()->getParam('id');
		
		if(!is_numeric($id))
			$this->_helper->redirector ( 'list', 'orders', 'default' );
		
		if(Orders::isOwner($id, $this->customer ['customer_id']))	
			$id = Orders::cloneOrder($id, null, true);
			$this->_helper->redirector ( 'edit', 'orders', 'default', array ('id' => $id, 'mex' => 'The requested task has been completed successfully', 'status' => 'success' ) );
			
		die($id);
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$isp = Shineisp_Registry::get('ISP');
		$request = $this->getRequest ();
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'index' );
		}
		
		// Get our form and validate it
		$form = $this->getForm ( '/admin/orders/process' );
		
		if (! $form->isValid ( $request->getPost () )) {
			// Invalid entries
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Order process");
			$this->view->description = $this->translator->translate("Check the information posted and then click on the save button.");
			return $this->_helper->viewRenderer ( 'applicantform' ); // re-render the login form
		}
		
		// Get the values posted
		$params = $form->getValues ();
		
		// Get the id 
		$id = $this->getRequest ()->getParam ( 'order_id' );
		
		// Save the message note
		if (! empty ( $params ['note'] )) {
			
			// If the order is commentable then go on
			if(Orders::IsCommentable($id)){
				$order = Orders::getAllInfo ( $id, null, true );
				$link = Fastlinks::findlinks ( $id, $this->customer ['customer_id'], 'orders' );
				
				if(!empty($link [0] ['code'])){
					$code = $link [0] ['code'];
				}else{
					$code = Fastlinks::CreateFastlink('orders', 'edit', json_encode (array('id' => $id)), 'orders', $id, $this->customer ['customer_id']);
				}
				
				// Save the message in the database
				Messages::addMessage($params ['note'], $this->customer ['customer_id'], null, $id);
				
				$in_reply_to = md5($id);
				
				$placeholder['messagetype'] = $this->translator->translate('Order');
				$placeholders['subject'] = sprintf ( "%03s", $id ) . " - " . Shineisp_Commons_Utilities::formatDateOut ( $order [0] ['order_date'] );
				$placeholders['fullname'] = $this->customer ['firstname'] . " " . $this->customer ['lastname'];
				$placeholders['orderid'] = $placeholders['subject'];
				$placeholders['conditions'] = Settings::findbyParam('conditions');
				$placeholders['url'] = "http://" . $_SERVER ['HTTP_HOST'] . "/index/link/id/" . $code;
				
				// Send a message to the customer
				Shineisp_Commons_Utilities::sendEmailTemplate($order [0] ['Customers'] ['email'], 'order_message', $placeholders, $in_reply_to, null, null, $isp, $order [0] ['Customers'] ['language_id']);
					
				$placeholders['url'] = "http://" . $_SERVER ['HTTP_HOST'] . "/admin/login/link/id/$code/keypass/" . Shineisp_Commons_Hasher::hash_string ( $isp->email );
				$placeholders['message'] = $params ['note'];
				
				// Send a message to the administrator 
				Shineisp_Commons_Utilities::sendEmailTemplate($isp->email, 'order_message_admin', $placeholders, $in_reply_to);
			}
		}
		
		$this->_helper->redirector ( 'index', 'orders', 'default', array ('mex' => 'The requested task has been completed successfully', 'status' => 'success' ) );
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Default_Form_OrdersForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
	/**
	 * deleteAction
	 * Delete the record previously selected
	 */
	public function deleteAction() {
		$ns = new Zend_Session_Namespace ();
		$id = $this->getRequest ()->getParam ( 'id' );
		Orders::DeleteByID ( $id, $this->customer ['customer_id'] );
		unset ( $ns->idorder );
		$this->_helper->redirector ( 'index', 'orders', 'default', array ('mex' => 'The requested task has been completed successfully', 'status' => 'success' ) );
	}
	
	/**
	 * setdeleteAction
	 * Set the order as deleted
	 */
	public function setdeleteAction() {
		$ns = new Zend_Session_Namespace ();
		$id = $this->getRequest ()->getParam ( 'id' );
		if(is_numeric($id)){
			// set the order as deleted
			Orders::setDeleted( $id, $this->customer ['customer_id'] );
			
			// Update the pdf document
			Orders::pdf($id, false, true);
			$this->_helper->redirector ( 'index', 'orders', 'default', array ('mex' => 'The requested task has been completed successfully', 'status' => 'success' ) );
		}else{
			$this->_helper->redirector ( 'index', 'orders', 'default', array ('mex' => 'An error occurred during the task execution.', 'status' => 'error' ) );
		}
	}
	
	/**
	 * SortingData
	 * Manage the request of sorting of the user 
	 * @return string
	 */
	private function sortingData($sort) {
		$strSort = "";
		if (! empty ( $sort )) {
			$sort = addslashes ( htmlspecialchars ( $sort ) );
			$sorts = explode ( "-", $sort );
			
			foreach ( $sorts as $sort ) {
				$sort = explode ( ",", $sort );
				$strSort .= $sort [0] . " " . $sort [1] . ",";
			}
			
			if (! empty ( $strSort )) {
				$strSort = substr ( $strSort, 0, - 1 );
			}
		}
		
		return $strSort;
	}
	
	/**
	 * recordsperpage
	 * Set the number of the records per page
	 * @return unknown_type
	 */
	public function recordsperpageAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$records = $this->getRequest ()->getParam ( 'id' );
		if (! empty ( $records ) && is_numeric ( $records )) {
			$NS->recordsperpage = $records;
		} elseif (! empty ( $records ) && $records == "all") {
			$NS->recordsperpage = 999999;
		}
		$redirector->gotoUrl ( '/orders/' );
	}
	
	/**
	 * bulkexport
	 * Custom function called by the Bulk action method
	 * @param $items
	 * @return url link
	 */
	public function bulkExport($items) {
		if (is_array ( $items )) {
			$fields = "d.domain, d.tld";
			$service = $this->service->findbyIds ( $fields, $items );
			$cvs = Shineisp_Commons_Utilities::cvsExport ( $service );
			die ( json_encode ( array ('mex' => '<a href="/public/documents/export.csv">' . $registry->Zend_Translate->translate ( "download" ) . '</a>' ) ) );
		}
		die ( json_encode ( array ('mex' => $this->translator->translate ( "There was a problem during the export process" ) ) ) );
	}
	
	/*
     *  bulkAction
     *  Execute a custom function for each item selected in the list
     *  this method will be call from a jQuery script 
     *  @return string
     */
	public function bulkAction() {
		$request = $this->getRequest ();
		$items = $request->getParams ();
		if (! empty ( $items ['params'] )) {
			parse_str ( $items ['params'], $arrparams );
			$action = isset ( $arrparams ['do'] ) ? $arrparams ['do'] : "";
			if (method_exists ( __CLASS__, $action )) {
				$retval = $this->$action ( $arrparams ['item'] );
				if ($retval) {
					die ( json_encode ( array ('mex' => $this->translator->translate ( "The task requested has been executed successfully." ) ) ) );
				}
			} else {
				die ( json_encode ( array ('mex' => $this->translator->translate ( "methodnotset" ) ) ) );
			}
		}
		die ( json_encode ( array ('mex' => $this->translator->translate ( "An error occurred during the task execution." ) ) ) );
	}
	
	/**
	 * dnsGrid
	 * Get the dns zone information.
	 * @return array
	 */
	private function dnsGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$zones = new Dns_Zones ();
			$records = $zones->findAllbyDomain ( $request->id, 'subdomain, zt.zone, target', true );
			if (isset ( $records [0] )) {
				return array ('records' => $records );
			}
		}
	}
	
	/**
	 * printAction
	 * Create a pdf invoice document
	 * @return void
	 */
	public function createinvoiceAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		if(empty($NS->customer ['customer_id'])){
			$this->_helper->redirector ( 'index', 'index', 'default', array ('mex' => 'There was an error. The invoice selected has not been found.', 'status' => 'error' ) );
		}
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		try {
			if (is_numeric ( $request->id )) {
				if (Invoices::isOwner ( $request->id, $NS->customer ['customer_id'] )) {
					Invoices::PrintPDF ( $request->id );
				}
			}
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		die ();
	}
	
	/**
	 * printAction
	 * Create a pdf order document
	 * @return void
	 */
	public function printAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		
		try {
			if (is_numeric ( $request->id )) {
				if (Orders::isOwner ( $request->id, $NS->customer ['customer_id'] )) {
					Orders::pdf ( $request->id, true );
				}
			}
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		die ();
	}
	
	/**
	 * callbackAction
	 * This method isn't called from the project 
	 * but it is called from a bank gateway service 
	 * in order to set as payed the order processed
	 */
	public function callbackAction() {
		// Go to the default/common/callback controller
	}
	
	/**
	 * Process the response of the banks gateways
	 * 
	 * @return void
	 */
	public function responseAction() {
		$request = $this->getRequest ();
		$response = $request->getParams ();
		
		if (! empty ( $response ['custom'] ) && is_numeric ( trim ( $response ['custom'] ) )) {
			
			$isp = Shineisp_Registry::get('ISP');
			
			// Orderid back from the bank
			$order_id = trim ( $response ['custom'] );
			
			// Getting the md5 value in order to match with the class name.
			$classrequest = $request->gateway;
			
			// Get the bank selected using the MD5 code 
			$bank = Banks::findbyMD5 ( $classrequest );
			if (! empty ( $bank [0] ['classname'] )) {
				if (! empty ( $bank [0] ['classname'] ) && class_exists ( $bank [0] ['classname'] )) {
					
					$class = $bank [0] ['classname'];
					$payment = new $class ( $order_id );
					
					// Check if the method "Response" exists in the Payment class and send all the bank information to the payment module
					if (method_exists ( $class, "Response" )) {
						$OrderID = $payment->Response ( $response );
					} else {
						$OrderID = false;
					}
				}
			}
			
			// Check if the OrderID is a number because it 
			// means that the order has been executed correctly
			if (is_numeric ( $OrderID )) {
				
				// Sending an email to the customer and the administrator with the order details.
				$order = Orders::getAllInfo ( $OrderID, null, true );
				
				Shineisp_Commons_Utilities::sendEmailTemplate($order [0] ['Customers'] ['email'], 'order_confirm', array(
											'fullname'      => $order [0] ['Customers']['fullname'],
											'orderid'      => $OrderID,
											'order'      => $order,
				), null, null, null, null, $order [0] ['Customers'] ['language_id']);
				
				// Redirect the user in the The task requested has been executed successfully. page
				$this->_helper->redirector ( 'list', 'orders', 'default', array ('mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
			
			}
		}
		$this->_helper->redirector ( 'list', 'orders', 'default', array ('mex' => 'There was a problem during the payment process.', 'status' => 'error' ) );
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function downloadAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$filename = Invoices::DownloadAllbyCustomerID($NS->customer ['customer_id']);
		if(!empty($filename) && file_exists(PUBLIC_PATH . $filename)){
			ob_clean();
			header('location: ' . $filename);
			die;
		}else{
			$this->_helper->redirector ( 'list', 'orders', 'default', array ('mex' => 'No invoices have been found.', 'status' => 'error' ) );
		}
	}
}