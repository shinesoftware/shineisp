<?php

/**
 * OrdersController
 * Manage the customers table
 * @version 1.0
 */

class Admin_OrdersController extends Shineisp_Controller_Admin {
	
	protected $orders;
	protected $translator;
	protected $datagrid;
	protected $session;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$this->session = new Zend_Session_Namespace ( 'Admin' );
		$this->orders = new Orders ();
		$registry = Shineisp_Registry::getInstance ();
		$this->translator = $registry->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "orders" )->setModel ( $this->orders );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$this->_helper->redirector ( 'list', 'orders', 'admin' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Orders list");
		$this->view->description = $this->translator->translate("Here you can see all the orders.");
		$this->view->buttons = array(array("url" => "/admin/orders/new/", "label" => $this->translator->translate('New order'), "params" => array('css' => null)));
		$this->datagrid->setConfig ( Orders::grid () )->datagrid ();
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( Orders::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( Orders::grid () )->search ();
	}
	
	/*
	 *  bulkAction
	 *  Execute a custom function for each item selected in the list
	 *  this method will be call from a jQuery script 
	 *  @return string
	 */
	public function bulkAction() {
		$this->_helper->ajaxgrid->massActions ();
	}
	
	/**
	 * recordsperpage
	 * Set the number of the records per page
	 * @return unknown_type
	 */
	public function recordsperpageAction() {
		$this->_helper->ajaxgrid->setRowNum ();
	}
	
	/**
	 * newAction
	 * Create the form module in order to create a record
	 * @return unknown_type
	 */
	public function newAction() {
		
		$this->view->form = $this->getForm ( "/admin/orders/process" );
		$this->view->title = $this->translator->translate("New Order");
		$this->view->description = $this->translator->translate("Create a new order using this form.");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save order'), "params" => array('css' => null,'id' => 'submit')));
		$this->render ( 'applicantform' );
	}
	
	/**
	 * confirmAction
	 * Ask to the user a confirmation before to execute the task
	 * @return null
	 */
	public function confirmAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$controller = $this->getRequest ()->getControllerName ();
		try {
			if (is_numeric ( $id )) {
				$this->view->back = "/admin/$controller/edit/id/$id";
				$this->view->goto = "/admin/$controller/delete/id/$id";
				$this->view->title = $this->translator->translate ( 'Are you sure you want to delete this order?' );
				$this->view->description = $this->translator->translate ( 'If you delete this order all the data will no longer be available.' );
				
				$record = $this->orders->find ( $id );
				$this->view->recordselected = $record ['order_id'] . " - " . Shineisp_Commons_Utilities::formatDateOut ( $record ['order_date'] );
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ), 'status' => 'danger' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	
	}
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the order
	 * @return unknown_type
	 */
	public function deleteAction() {
		$files = new Files ();
		$id = $this->getRequest ()->getParam ( 'id' );
		try {
			if (is_numeric ( $id )) {
				
				// Delete all the files attached
				Shineisp_Commons_Utilities::delTree ( PUBLIC_PATH . "/documents/orders/$id/" );
				Orders::DeleteByID($id);
				
			}
		} catch ( Exception $e ) {
			die ( $e->getMessage () . " " . $e->getTraceAsString () );
		}
		
		return $this->_helper->redirector ( 'index', 'orders' );
	}
	
	/**
	 * Delete a status history
	 */
	public function deletestatushistoryAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		try {
			if (is_numeric ( $id )) {
				$record = StatusHistory::getAllInfo($id);

				// get the order information before to delete the status history
				if($record){
					$statusHistory = $record->toArray();
					$record->delete();
					$this->_helper->redirector ( 'edit', 'orders', 'admin', array ('id' => $statusHistory[0]['section_id'], 'mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
				}
			}
		} catch ( Exception $e ) {
			die ( $e->getMessage () . " " . $e->getTraceAsString () );
		}
		
		$this->_helper->redirector ( 'index', 'orders' );
	}
	
	/**
	 * Get products using the categories
	 * 
	 * @return Json
	 */
	public function getproductsAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$products = array();
		if(is_numeric($id)){
			$products = ProductsCategories::getProductListbyCatID ( $id, "p.product_id, pd.name as name" );	
		}
		
		die ( json_encode ( $products ) );
	}
	
	/**
	 * Get the billing cycle items
	 * 
	 * @return Json
	 */
	public function getbillingcyclesAction() {
	    $billingcycles = array();
	    $currency = Shineisp_Registry::get ( 'Zend_Currency' );
	    $id = $this->getRequest ()->getParam ( 'id' );
	    if(is_numeric($id)){
	       $billingcycles = ProductsTranches::getTranches($id, "billing_cycle_id, bc.name as name, pt.price as price, pt.setupfee as setupfee");
	    }
	    die ( json_encode ( $billingcycles) );
	}
	
	
	/**
	 * Get product info
	 * @return Json
	 */
	public function getproductinfoAction() {
		$ns = new Zend_Session_Namespace ( 'Admin' );
		$id = $this->getRequest ()->getParam ( 'id' );
		$product = Products::getAllInfo( $id, $ns->langid);
		die ( json_encode ( $product ) );
	}
	
	/**
	 * Get billing cycles information
	 * @return Json
	 */
	public function getbillingsAction() {
		$ns = new Zend_Session_Namespace ( 'Admin' );
		$id = $this->getRequest ()->getParam ( 'id' );
		$billid = $this->getRequest ()->getParam ( 'billid' );
		$billings = ProductsTranches::getTranchesBy_ProductId_BillingId( $id, $billid );
		die ( json_encode ( $billings ) );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		
		$form = $this->getForm ( '/admin/orders/process' );
		$currency = Shineisp_Registry::getInstance ()->Zend_Currency;
		$customer = null;
		$createInvoiceConfirmText = $this->translator->translate('Are you sure you want to create or overwrite the invoice for this order?');
		$id = intval($this->getRequest ()->getParam ( 'id' ));
		
		$this->view->description = $this->translator->translate("Here you can edit the selected order.");
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = $this->orders->find ( $id );
			if (! empty ( $rs )) {
				$rs = $rs->toArray ();
				
				$rs ['setupfee']        = Orders::getSetupfee ( $id );
				$rs ['order_date']      = Shineisp_Commons_Utilities::formatDateOut ( $rs ['order_date'] );
				$rs ['expiring_date']   = Shineisp_Commons_Utilities::formatDateOut ( $rs ['expiring_date'] );
				$rs ['received_income'] = 0;
				$rs ['missing_income']  = $rs['grandtotal'];
				$rs ['order_number']    = !empty($rs['order_number']) ? $rs['order_number'] : Orders::formatOrderId($rs['order_id']);
				
				$payments = Payments::findbyorderid ( $id, 'income', true );
				if (isset ( $payments )) {
					foreach ( $payments as $payment ) {
						$rs ['received_income'] += (isset($payment['income'])) ? $payment['income'] : 0;
						$rs ['missing_income']  -= (isset($payment['income'])) ? $payment['income'] : 0;
					}
				}

				// set the default income to prepare the payment task
				$rs ['income'] = $rs ['missing_income'];
				$rs ['missing_income'] = sprintf('%.2f',$rs ['missing_income']);
				unset($payments);
				
				$parent = Customers::find ( $rs ['customer_id'] );
				
				//if customer comes from reseller
				if ($parent ['parent_id']) {
					$rs ['customer_parent_id'] = $parent ['parent_id'];
				} else {
					$rs ['customer_parent_id'] = $rs ['customer_id'];
				}
				
				$link = Fastlinks::findlinks ( $id, $rs ['customer_id'], 'Orders' );
				if (isset ( $link [0] )) {
					$rs ['fastlink'] = $link [0] ['code'];
					$rs ['visits'] = $link [0] ['visits'];
				}
				
				$form->populate ( $rs );
				$this->view->id = $id;
				$this->view->customerid = $rs ['customer_id'];
				
				if(!empty($rs['fastlink'])){
					$this->view->titlelink = "/index/link/id/" . $rs['fastlink'];
				}
				
				if(!empty($rs['order_number'])){
					$this->view->title = $this->translator->_( "Order nr. %s", $rs['order_number']);
				}
				
				$this->view->messages = Messages::getbyOrderId ($id);

				$createInvoiceConfirmText = ( $rs ['missing_income'] > 0 ) ? $this->translator->translate('Are you sure you want to create or overwrite the invoice for this order? The order status is: not paid.') : $createInvoiceConfirmText;
				$customer = Customers::get_by_customerid ( $rs ['customer_id'], 'company, firstname, lastname, email' );
				
			} else {
				$this->_helper->redirector ( 'list', 'orders', 'admin' );
			}
		}
		
		
		$this->view->mex = urldecode ( $this->getRequest ()->getParam ( 'mex' ) );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
									array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('id'=>'submit', 'css' => array('btn btn-success'))),
									array("url" => "/admin/orders/print/id/$id", "label" => $this->translator->translate('Print'), "params" => array('css' => null)),
									array("url" => "/admin/orders/dropboxit/id/$id", "label" => $this->translator->translate('Dropbox It'), "params" => array('css' => null)),
									array("url" => "/admin/orders/clone/id/$id", "label" => $this->translator->translate('Clone'), "params" => array('css' => null)),
									array("url" => "/admin/orders/sendorder/id/$id", "label" => $this->translator->translate('Email'), "params" => array('css' => array('btn btn-danger'))),
									array("url" => "/admin/orders/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('btn btn-danger'))),
									array("url" => "/admin/orders/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
								);
		
		// Check if the order has been invoiced
		$invoice_id = Orders::isInvoiced($id);
		if($invoice_id){
			$this->view->buttons[] = array("url" => "/admin/orders/sendinvoice/id/$invoice_id", "label" => $this->translator->translate('Email invoice'), "params" => array('css' => array('btn btn-danger')));
			$this->view->buttons[] = array("url" => "/admin/invoices/print/id/$invoice_id", "label" => $this->translator->translate('Print invoice'), "params" => array('css' => null));
		} else {
			// Order not invoiced, show button to create a new invoice
			$this->view->buttons[] = array("url" => "/admin/orders/createinvoice/id/$id", "label" => $this->translator->translate('Invoice'), "params" => array('css' => array('btn btn-danger')), 'onclick' => "return confirm('".$createInvoiceConfirmText."')");
		}
		
		$this->view->customer          = array ('records' => $customer, 'editpage' => 'customers' );
		$this->view->ordersdatagrid    = $this->orderdetailGrid ();
		$this->view->paymentsdatagrid  = $this->paymentsGrid ();
		$this->view->statushistory            = StatusHistory::getStatusList($id);  // Get Order status history
		$this->view->filesgrid         = $this->filesGrid ();
		$this->view->statushistorygrid = $this->statusHistoryGrid ();
		$this->view->form              = $form;
		$this->render ( 'applicantform' );
	}
	
	private function orderdetailGrid() {
		$request = $this->getRequest ();
		
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Orders::getDetails ( $request->id, "detail_id,
			                                          DATE_FORMAT(d.date_start, '".Settings::getMySQLDateFormat('dateformat')."') as date-start,
			                                          DATE_FORMAT(d.date_end, '".Settings::getMySQLDateFormat('dateformat')."') as date-end,
			                                          d.quantity, 
								                      d.description, 
			                                          d.setupfee, 
								                      d.price, 
		                                              d.vat,
								                      d.subtotal,
			                                          CONCAT(dm.domain, '.',ws.tld) as domain" );
			if (isset ( $rs )) {
				$columns = array();
				$columns[] = $this->translator->translate('Quantity');
				$columns[] = $this->translator->translate('Description');
				$columns[] = $this->translator->translate('Setup fees');
				$columns[] = $this->translator->translate('Price');
				$columns[] = $this->translator->translate('VAT');
				$columns[] = $this->translator->translate('Subtotal');
				$columns[] = $this->translator->translate('Start data');
				$columns[] = $this->translator->translate('End data');
				$columns[] = $this->translator->translate('Domain');
				
				return array (	'columns' => $columns, 
				                'records' => $rs, 
								'delete' => array ('controller' => 'ordersitems', 'action' => 'confirm' ), 
								'edit' => array ('controller' => 'ordersitems', 'action' => 'edit' ), 
								'actions' => array('/admin/services/edit/id/' => $this->translator->translate('Service')), 
								'pager' => true );
			}
		}
	}
	
	/**
	 * Creation of the payment transaction grid 
	 * @return multitype:boolean multitype:string
	 */
	private function paymentsGrid() {
		$currency = Shineisp_Registry::getInstance ()->Zend_Currency;
		$myrec = array ();
		$requestId = $this->getParam('id');
				
		if (!empty($requestId) && is_numeric ( $requestId )) {
			$rs = Payments::findbyorderid ( $requestId, 'payment_id, paymentdate, b.name as bank, description, reference, confirmed, income, outcome', true );
			
			if (isset ( $rs )) {
				$i = 0;
				
				// Format some data
				foreach ( $rs as $record ) {
					$myrec[$i]['id'] = $record['payment_id'];
					
					// Set the date format
					$myrec[$i]['payment_date'] = Shineisp_Commons_Utilities::formatDateOut ($record['paymentdate']);
					
					$myrec[$i]['description'] = $record['description'];
					$myrec[$i]['reference'] = $record['reference'];
					$myrec[$i]['confirmed'] = $record['confirmed'] ? $this->translator->translate ( 'Yes' ) : $this->translator->translate ( 'No' );
					$myrec[$i]['type'] = $record['bank'];
					
					// Checking the currency set in the configuration
					$myrec[$i]['income'] = $currency->toCurrency($record['income'], array('currency' => Settings::findbyParam('currency')));
					$myrec[$i]['outcome'] = $currency->toCurrency($record['outcome'], array('currency' => Settings::findbyParam('currency')));
						
					$i++;
				}
				
				return array (
					'records' => $myrec, 
					'pager'  => true,
					'edit' => array ('controller' => 'payments', 'action' => 'edit' ),
					'delete' => array ('controller' => 'orders', 'action' => 'deletepayment' ),
				);
			}
		}
	}
	
	/*
	 * Clone the order 
	 */
	public function cloneAction() {
		$request = $this->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$newOrderId = Orders::cloneOrder ( $request->id );
			if (is_numeric ( $newOrderId )) {
				$this->_helper->redirector ( 'edit', 'orders', 'admin', array ('id' => $newOrderId, 'mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
			} else {
				$this->_helper->redirector ( 'edit', 'orders', 'admin', array ('id' => $request->id, 'mex' => $this->translator->translate ( 'Order has been not cloned' ), 'status' => 'danger' ) );
			}
		}
		$this->_helper->redirector ( 'list', 'orders', 'admin' );
	}
	
	/*
	 * Delete payment transaction
	 */
	public function deletepaymentAction() {
		$request = $this->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {

			// Get the order id attached to the payment transaction
			$orderId = Payments::getOrderId($request->id);
			if(is_numeric($orderId)){
				
				// Delete the payment transaction
				if (Payments::deleteByID($request->id)) {
					$this->_helper->redirector ( 'edit', 'orders', 'admin', array ('id' => $orderId, 'mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
				} else {
					$this->_helper->redirector ( 'edit', 'orders', 'admin', array ('id' => $orderId, 'mex' => $this->translator->translate ( 'There was a problem' ), 'status' => 'danger' ) );
				}
			}
		}
		$this->_helper->redirector ( 'list', 'orders', 'admin' );
	}
	
	/**
	 * Delete the attached file from the order
	 */
	public function deletefileAction(){
	    $id = $this->getRequest ()->getParam('id');
	    if(is_numeric($id)){
	        $record = Files::get_by_id($id);
	        if($record){
	            $orderId = $record['id'];
	            Files::del($id);
	            $this->_helper->redirector ( 'edit', 'orders', 'admin', array ('id' => $orderId, 'mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
	        }
	    }
	    
	    $this->_helper->redirector ( 'list', 'orders', 'admin', array ('mex' => $this->translator->translate ( 'There was a problem' ), 'status' => 'danger' ) );
	}
	
	/*
	 * Get the list of the attached files
	 */
	private function filesGrid() {
		$request = $this->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Files::findbyExternalId ( $request->id, "orders", "file, Date_Format(date, '%d/%m/%Y') as date, fc.name as name, DATE_FORMAT(lastdownload, '".Settings::getMySQLDateFormat('dateformat')."') as downloaded" );
			if (isset ( $rs [0] )) {
				return array ('records' => $rs, 'actions' => array ('/admin/orders/getfile/id/' => $this->translator->translate('Download') ), 'delete' => array ('controller' => 'orders', 'action' => 'deletefile' ) );
			}
		}
	}
	
	/*
	 * 
	 */
	private function statusHistoryGrid() {
		$request = $this->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = StatusHistory::getAll($request->id, "orders");
			$columns[] = $this->translator->translate('Date');
			$columns[] = $this->translator->translate('Status');
			if (isset ( $rs [0] )) {
				return array ('name' => 'orders_statusHistoryGrid', 'columns'=>$columns, 'records' => $rs, 'delete' => array ('controller' => 'orders', 'action' => 'deletestatushistory' ) ); 
			}
		}
	}
	
	/**
	 * get the file attached into the order
	 */
	public function getfileAction(){
	    $id = $this->getRequest ()->getParam('id');
	    $file = Files::get_by_id($id);
	    if(!empty($file)){
	        if(!Files::downloadbykey($file['publickey'])){
	            $this->_helper->redirector ( 'list', 'orders', 'admin', array ('mex' => $this->translator->translate ( 'There was a problem' ), 'status' => 'danger' ) );
	        }
	    }
	    $this->_helper->redirector ( 'list', 'orders', 'admin', array ('mex' => $this->translator->translate ( 'There was a problem' ), 'status' => 'danger' ) );
	}
	
	/**
	 * createinvoiceAction
	 * Create an invoice reference
	 * @return void
	 */
	public function createinvoiceAction() {
		$request = $this->getRequest ();
		if (is_numeric ($request->id) && !Orders::isInvoiced($request->id)) {
			//TODO: create invoice should only create the invoice and not set order as complete
			//Orders::Complete($request->id);
			Invoices::Create ( $request->id );
			
			$this->_helper->redirector ( 'edit', 'orders', 'admin', array ('id' => $request->id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
		}
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$form = $this->getForm ( "/admin/orders/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/orders/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/orders/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		// Get the id 
		$id = $this->getRequest ()->getParam ( 'order_id' );
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'orders', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			
			$params = $form->getValues ();
			
			// Save the data
			$id = Orders::saveAll ( $params, $id );
			
			// Save the upload file
			Orders::UploadDocument($id, $params);
			
			$this->_helper->redirector ( 'edit', 'orders', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Order Edit");
			$this->view->description = $this->translator->translate("Here you can edit the selected order.");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_OrdersForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
	/**
	 * SortingData
	 * Manage the request of sorting of the order 
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
	 * set_status
	 * Set the status of all items passed
	 * @param $items
	 * @return void
	 */
	private function set_status($items) {
		$request = $this->getRequest ();
		$status = $request->getParams ( 'params' );
		$params = parse_str ( $status ['params'], $output );
		$status = $output ['status'];
		if (is_array ( $items ) && is_numeric ( $status )) {
			foreach ( $items as $orderid ) {
				if (is_numeric ( $orderid )) {
					$this->orders->set_status ( $orderid, $status ); // set it as deleted
				}
			}
			return true;
		}
		return false;
	}
	
	/**
	 * sendorder
	 * Send the order email
	 * @return url link
	 */
	public function sendorderAction() {
		$request = $this->getRequest ();
		$orderid = $request->getParam ( 'id' );
		
		if (is_numeric ( $orderid )) {
			if (Orders::sendOrder ( $orderid )) {
				$this->_helper->redirector ( 'edit', 'orders', 'admin', array ('id' => $orderid, 'mex' => $this->translator->translate ( 'The order has been sent successfully.' ), 'status' => 'success' ) );
			}
		}
		$this->_helper->redirector ( 'edit', 'orders', 'admin', array ('id' => $orderid, 'mex' => $this->translator->translate ( 'The order has not been found.' ), 'status' => 'danger' ) );
	}
	
	/**
	 * sendinvoice
	 * Send the invoice email
	 * @return url link
	 */
	public function sendinvoiceAction() {
		$request = $this->getRequest ();
		$invoiceid = $request->getParam ( 'id' );
		$order = Invoices::getOrderbyInvoiceId ( $invoiceid );
		if ($order) {
			if (is_numeric ( $invoiceid )) {
				if (Invoices::sendInvoice ( $invoiceid )) {
					$this->_helper->redirector ( 'edit', 'invoices', 'admin', array ('id' => $invoiceid, 'mex' => $this->translator->translate ( 'Invoice sent successfully.' ), 'status' => 'success' ) );
				}
			}
			$this->_helper->redirector ( 'edit', 'invoices', 'admin', array ('id' => $invoiceid, 'mex' => $this->translator->translate ( 'The invoice has not been found.' ), 'status' => 'danger' ) );
		}
		$this->_helper->redirector ( 'list', 'invoices', 'admin', array ('mex' => $this->translator->translate ( 'The invoice has not been found.' ), 'status' => 'danger' ) );
	}
	
	
	/**
	 * printAction
	 * Create a pdf invoice document
	 * @return void
	 */
	public function printAction() {
		$request = $this->getRequest ();
		try {
			if (is_numeric ( $request->id )) {
				$file = Orders::pdf ( $request->id, false, true );
				header('location: ' . $file);
				die;
			}
			
			$this->_helper->redirector ( 'list', 'orders', 'admin', array ('mex' => $this->translator->translate ( 'The order has not been found.' ), 'status' => 'danger' ) );
			
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
	}
	
	/**
	 * Upload an order to the dropbox account
	 *
	 * @return void
	 */
	public function dropboxitAction() {
		$request = $this->getRequest ();
		if (is_numeric ( $request->id )) {
			$sent = Orders::pdf( $request->id, false, true );
			if ($sent) {
				$this->_helper->redirector ( 'edit', 'orders', 'admin', array ('id' => $request->id, 'mex' => $this->translator->translate ( 'The order has been uploaded to dropbox.' ), 'status' => 'success' ) );
			} else {
				$this->_helper->redirector ( 'edit', 'orders', 'admin', array ('id' => $request->id, 'mex' => $this->translator->translate ( 'There was a problem during the process.' ), 'status' => 'danger' ) );
			}
		}
	}
}