<?php

/**
 * UsersController
 * Handling the customers 
 * @version 1.0
 */

class Admin_CustomersController extends Shineisp_Controller_Admin {
	
	protected $customers;
	protected $datagrid;
	protected $session;
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$this->session = new Zend_Session_Namespace ( 'Admin' );
		$this->customers = new Customers ();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "customers" )->setModel ( $this->customers );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/customers/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Customer list");
		$this->view->description = $this->translator->translate("Here you can see all the customers.");
		$this->view->buttons = array(array("url" => "/admin/customers/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)));
		$this->datagrid->setConfig ( Customers::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( Customers::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( Customers::grid() )->search ();
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
	 * Search the record for the Select2 JQuery Object by ajax
	 * @return json
	 */
	public function searchAction() {
	    
	    if($this->getRequest()->isXmlHttpRequest()){
	    
    	    $term = $this->getParam('term');
    	    $id = $this->getParam('id');
    	    
    	    if(!empty($term)){
    	        $term = "%$term%";
    	        $records = Customers::findbyCustomfield("(firstname LIKE ?) OR (lastname LIKE ?) OR company LIKE ?", array($term,$term,$term));
    	        die(json_encode($records));
    	    }
    	    
    	    if(!empty($id)){
    	        $records = Customers::get_by_customerid($id);
    	        die(json_encode($records));
    	    }
    	    
    	    $records = Customers::getAll();
    		die(json_encode($records));
	    }else{
	        die();
	    }
	}
	
	/**
	 * Select the type of companies starting from the legal form
	 * @return json
	 */
	public function companytypeAction() {
	    
	    if($this->getRequest()->isXmlHttpRequest()){
	    
    	    $id = $this->getParam('id');
    	    
    	    if(!empty($id)){
    	        $records = CompanyTypes::getListbyLegalformID($id);
    	        die(json_encode($records));
    	    }
    	    
    	    $records = Customers::getAll();
    		die(json_encode($records));
	    }else{
	        die();
	    }
	}
	
	/**
	 * newAction
	 * Create the form module in order to create a record
	 * @return unknown_type
	 */
	public function newAction() {
		$this->view->form = $this->getForm ( "/admin/customers/process" );
		$this->view->title = $this->translator->translate("Customer details");
		$this->view->description = $this->translator->translate("Here you can edit the customer details.");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
							   		 array("url" => "/admin/customers/list", "label" => $this->translator->translate('List'), "params" => array('css' => null)));
		$this->render ( 'applicantform' );
	}
	
	/**
	 * confirmAction
	 * Ask to the user a confirmation before to execute the task
	 * @return null
	 */
	public function confirmAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		try {
			if (is_numeric ( $id )) {
				$this->view->back = "/admin/$controller/edit/id/$id";
				$this->view->goto = "/admin/$controller/delete/id/$id";
				$this->view->title = $this->translator->translate ( 'WARNING: Are you sure you want to delete this customer, their domains, invoices, orders and tickets?' );
				$this->view->description = $this->translator->translate ( 'If you delete this customer whole information will no longer be available anymore.' );
				
				$record = $this->customers->find ( $id );
				$this->view->recordselected = $record ['firstname'] . " " . $record ['lastname'] . " " . $record ['company'];
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ), 'status' => 'danger' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * Delete the address selected
	 * @return unknown_type
	 */
	public function addressdeleteAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {

			// Get the customer information before to delete the address 
			$customer = Addresses::getCustomer($id);
			if(is_array($customer)){
				try {
					if(Addresses::delete_address($id)){
						$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $customer['customer_id'], 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );	
					}
					
				} catch ( Exception $e ) {
					$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $customer['customer_id'], 'mex' => $this->translator->translate ( 'Unable to process the request at this time.' ) . ": " . $e->getMessage (), 'status' => 'danger' ) );
				}
			}
		}
	}
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the customer
	 * @return unknown_type
	 */
	public function deletecontactAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			// Get the customer information before to delete the address
			$customer = Contacts::getCustomer($id);
			if(is_array($customer)){
				try {
					if(Contacts::delete_contact ( $id )){
						$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $customer['customer_id'], 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
					}
				} catch ( Exception $e ) {
					$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $customer['customer_id'], 'mex' => $this->translator->translate ( 'Unable to process the request at this time.' ) . ": " . $e->getMessage (), 'status' => 'danger' ) );
				}
			}
		}
	}
	
	/**
	 * Delete the files attached
	 */
	public function deletefileAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			try {
				if(Files::del( $id )){
					$this->_helper->redirector ( 'list', 'customers', 'admin', array ('mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
				}
			} catch ( Exception $e ) {
				$this->_helper->redirector ( 'list', 'customers', 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ) . ": " . $e->getMessage (), 'status' => 'danger' ) );
			}
		}
	}
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the customer
	 * @return unknown_type
	 */
	public function deleteAction() {
		$files = new Files ();
		$id = $this->getRequest ()->getParam ( 'id' );
		try {
			if (is_numeric ( $id )) {
				Customers::del($id);
			}
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'Unable to process the request at this time.' ) . ": " . $e->getMessage (), 'status' => 'danger' ) );
		}
		
		$this->_helper->redirector ( 'list', 'customers', 'admin', array ('mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		
		$form = $this->getForm ( '/admin/customers/process' );
		
		$id = $this->getRequest ()->getParam ( 'id' );
		
		$this->view->title = $this->translator->translate("Customer edit");
		$this->view->description = $this->translator->translate("Here you can edit the customer details.");
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/customers/list", "label" => $this->translator->translate('List'), "params" => array('css' => null)),
				array("url" => "/admin/customers/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			
			$rs = $this->customers->getAllInfo ( $id );
			
			if (! empty ( $rs )) {
				
				$rs += CustomAttributes::getElementsValues($id, 'customers');
				$rs['birthdate'] = Shineisp_Commons_Utilities::formatDateOut($rs['birthdate']);
				
				$this->view->id = $id;
				$form->populate ( $rs );
				
				if(!empty($rs['company'])){
					$this->view->title = $rs['company'] . " - " . $rs['firstname'] . " " . $rs['lastname'];
				}else{
					$this->view->title = $rs['firstname'] . " " . $rs['lastname'];
				}
				
				$this->view->buttons[] = array("url" => "/admin/orders/new", "label" => $this->translator->translate('New Order'), "params" => array('css' => null));
				$this->view->buttons[] = array("url" => "/admin/customers/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => null));
				$this->view->buttons[] = array("url" => "/default/index/fastlogin/id/" . Shineisp_Commons_Hasher::hash_string($rs['email']), "label" => $this->translator->translate('Public profile'), "params" => array('css' => null));
				
			}
		}
		
		
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		
		$this->view->editmode = true;
		
		$this->view->addressesdatagrid = $this->addressesGrid ();
		$this->view->contactsdatagrid = $this->contactsGrid ();
		$this->view->filesdatagrid = $this->filesGrid ();
		$this->view->domainsdatagrid = $this->domainsGrid ();
		$this->view->servicesdatagrid = $this->servicesGrid ();
		$this->view->ordersdatagrid = $this->ordersGrid ();
		$this->view->tickets = $this->ticketsGrid ();
		$this->view->invoicesdatagrid = $this->invoicesGrid ();
		$this->view->sentmailsdatagrid = $this->sentmailsGrid ();
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	private function domainsGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Domains::findbyUserId ( $request->id, "d.domain_id, CONCAT(d.domain,'.', ws.tld) as domain, DATE_FORMAT(d.creation_date, '%d/%m/%Y') as creation_date,  DATE_FORMAT(d.expiring_date, '%d/%m/%Y') as expiring_date" );
			if (isset ( $rs [0] )) {
				
				$columns[] = $this->translator->translate('Domain');
				$columns[] = $this->translator->translate('Creation Date');
				$columns[] = $this->translator->translate('Expiry Date');
				
				return array ('name' => 'domains', 'columns'=>$columns, 'records' => $rs, 'edit' => array ('controller' => 'domains', 'action' => 'edit' ), 'pager' => true );
			}
		}
	}
	
	private function servicesGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		try {
			if (isset ( $request->id ) && is_numeric ( $request->id )) {
				// In order to select only the fields interested we have to add an alias to all the fields. If the aliases are not created Doctrine will require an index field for each join created.
				//$rs = Products::getAllServicesByCustomerID ( $request->id, 'oi.detail_id as detail_id, pd.name as productname' );
				$rs = Products::getAllServicesByCustomerID ( $request->id, 'o.order_id, oi.detail_id as detail_id, pd.name as productname, DATE_FORMAT(oi.date_start, "%d/%m/%Y") AS date_start, DATE_FORMAT(oi.date_end, "%d/%m/%Y") AS date_end, DATEDIFF(oi.date_end, CURRENT_DATE) AS daysleft, oi.price as price, oi.autorenew as autorenew, oi.status_id as status' );
				if ($rs) {
					$arrStatuses = Statuses::getList('orders');
					
					foreach ( $rs as $k => $v) {
						if ( isset($v['price']) ) {
							//* TODO: Format price based on locale
							$rs[$k]['price'] = $v['price'];
						}

						if ( isset($v['status']) && isset($arrStatuses[$v['status']]) ) {
							$rs[$k]['status'] = $arrStatuses[$v['status']];
						}
						
						if ( isset($v['autorenew']) ) {
							$rs[$k]['autorenew'] = ($v['autorenew'] == 1) ? $this->translator->translate('Yes') : $this->translator->translate('Yes');
						}
						
						if ( isset($v['date_start']) ) {
							$rs[$k]['date_start'] = Shineisp_Commons_Utilities::formatDateIn ( $v['date_start'] );
						}
						if ( isset($v['date_end']) ) {
							$rs[$k]['date_end'] = Shineisp_Commons_Utilities::formatDateIn ( $v['date_end'] );
						}
					}
						
					$columns[] = $this->translator->translate('Product');
					$columns[] = $this->translator->translate('Creation Date');
					$columns[] = $this->translator->translate('Expiry Date');
					$columns[] = $this->translator->translate('Days left');
					$columns[] = $this->translator->translate('Price');
					$columns[] = $this->translator->translate('Automatic renewal');
					$columns[] = $this->translator->translate('Status');

                    return array('name' => 'services', 'columns' => $columns, 'records' => $rs, 'edit' => array('controller' => 'orders', 'action' => 'edit'), 'pager' => true);
				}
			}
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $request->id, 'mex' => $this->translator->translate ( 'Unable to process the request at this time.' ) . ": " . $e->getMessage (), 'status' => 'danger' ) );
		}
	}	
	
	private function addressesGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Addresses::find_by_customerid($request->id);
			
			if (isset ( $rs )) {
				return array ('name' => 'contacts', 'records' => $rs, 'delete' => array ('controller' => 'customers', 'action' => 'addressdelete' ) );
			}
		}
	}
	
	private function contactsGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Contacts::getContacts ( $request->id );
			if (isset ( $rs  )) {
				
				$columns[] = $this->translator->translate('Contacts');
				$columns[] = $this->translator->translate('Type');
				
				return array ('name' => 'contacts', 'columns' => $columns, 'records' => $rs, 'delete' => array ('controller' => 'customers', 'action' => 'deletecontact' ) );
			}
		}
	}
	
	private function filesGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Files::findbyExternalId ( $request->id, "customers", "file_id, fc.name as category, CONCAT(path, file) as file, date" );
			
			if (isset ( $rs  )) {
				$columns[] = $this->translator->translate('Imported at');
				$columns[] = $this->translator->translate('Category');
				$columns[] = $this->translator->translate('File');
				
				return array ('name' => 'files','columns'=>$columns, 'records' => $rs, 'delete' => array ('controller' => 'customers', 'action' => 'deletefile' ) );
			}
		}
	}
	
	private function ordersGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Orders::getOrdersByCustomerID ( $request->id, "o.order_id, o.order_id as order, o.order_number as order_number, in.formatted_number as invoice, DATE_FORMAT(o.order_date, '%d/%m/%Y') as date, o.grandtotal as total");
			if (isset ( $rs )) {
				
				$columns[] = $this->translator->translate('ID');
				$columns[] = $this->translator->translate('Number');
				$columns[] = $this->translator->translate('Invoice No.');
				$columns[] = $this->translator->translate('Date');
				$columns[] = $this->translator->translate('Total');
				
				return array ('name' => 'orders', 'columns' => $columns, 'records' => $rs, 'edit' => array ('controller' => 'orders', 'action' => 'edit' ) );
			}
		}
	}
	
	private function ticketsGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Tickets::getByCustomerID ( $request->id, "t.subject, s.status, DATE_FORMAT(t.date_open, '%d/%m/%Y') as date_open, c.company");
			
			$columns[] = $this->translator->translate('Subject');
			$columns[] = $this->translator->translate('Creation Date');
			$columns[] = $this->translator->translate('Company');
			$columns[] = $this->translator->translate('Status');
			
			return array ('name' => 'tickets', 'columns' => $columns, 'records' => $rs, 'edit' => array ('controller' => 'tickets', 'action' => 'edit' ) );
		}
	}
	
	private function invoicesGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$fields = "invoice_id, 
				DATE_FORMAT(i.invoice_date, '%d/%m/%Y') as invoice_date, 
				i.formatted_number as invoice, 
				o.order_number as order, 
				o.total as total, 
				o.vat as vat,
				o.grandtotal as grandtotal";
			$rs = Invoices::getByCustomerID ($request->id, $fields);
			
			$columns[] = $this->translator->translate('Date');
			$columns[] = $this->translator->translate('Invoice No.');
			$columns[] = $this->translator->translate('Order No.');
			$columns[] = $this->translator->translate('Total');
			$columns[] = $this->translator->translate('VAT');
			$columns[] = $this->translator->translate('Grand Total');
				 
			return array ('name' => 'invoices', 'columns' => $columns, 'records' => $rs, 'edit' => array ('controller' => 'invoices', 'action' => 'edit' ), 'actions' => array ('/admin/invoices/print/id/' => $this->translator->translate('Print')) );
		}
	}
	
	/**
	 * get the email list sent to the customer
	 */
	private function sentmailsGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$fields = "date, subject, recipient";
			$rs = EmailsTemplatesSends::getByCustomerID ($request->id, $fields);
			
			$columns[] = $this->translator->translate('ID');
			$columns[] = $this->translator->translate('Sent at');
			$columns[] = $this->translator->translate('Subject');
			$columns[] = $this->translator->translate('Recipient');
			
			return array ('name' => 'emailstemplatessends', 'columns' => $columns, 'records' => $rs, 'targetlink'=>'_blank', 'view' => array ('controller' => 'customers', 'action' => 'emailview' ) );
		}
	}
	
		
	/**
	 * Show the content of the email
	 */
	public function emailviewAction() {
		$this->getHelper ( 'layout' )->setLayout ( 'blank' );
		
		$id = $this->getRequest ()->getParam('id');
		if(is_numeric($id)){
			$email = EmailsTemplatesSends::getById($id);
			$this->view->email = $email;
			
		}
		
		return $this->render ( 'emailpreview' );
	}
		
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$form = $this->getForm ( "/admin/customers/process" );
		
		// Add the customer custom attributes
		$form = CustomAttributes::getElements($form);
		
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/customers/list", "label" => $this->translator->translate('List'), "params" => array('css' => null)),
				array("url" => "/admin/customers/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		try {
			
			// Check if we have a POST request
			if (! $request->isPost ()) {
				return $this->_helper->redirector ( 'list', 'customers', 'admin' );
			}
			
			if ($form->isValid ( $request->getPost () )) {
			    $params = $request->getPost();
                $area   = intval($params['area']);
                if( $area != 0 ) {
                    $province   = Provinces::find($area);
                    $area       = $province->code;
                    $params['area'] = $area;
                }
 
				$id = Customers::saveAll($params, $request->getParam ( 'customer_id' ));
				CustomAttributes::saveElementsValues($form->getSubForm('attributes')->getValues(), $request->getParam ( 'customer_id' ), "customers");
				
				$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
			} else {
				$this->view->form = $form;
				$this->view->title = $this->translator->translate("Customer details");
				$this->view->description = $this->translator->translate("Here you can edit the customer details.");
				return $this->render ( 'applicantform' );
			}
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $id, 'mex' => $e->getMessage (), 'status' => 'danger' ) );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_CustomersForm ( array ('action' => $action, 'method' => 'post' ) );
		
		// Add the customer custom attributes
		$form = CustomAttributes::getElements($form, "customers");
		
		return $form;
	}
	
	/**
	 * Get customer info
	 * @return Json
	 */
	public function getcustomerinfoAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$customer = Customers::find($id, null, true);
		if(!empty($customer)){
			die ( json_encode ( $customer ) );
		}
		die(json_encode(array()));
	}
		
}