<?php

/**
 * ServicesController
 * Manage the services table
 * @version 1.0
 */

class Admin_ServicesController extends Shineisp_Controller_Admin {
	
	protected $services;
	protected $categories;
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
		$this->services = new OrdersItems ();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "services" )->setModel ( $this->services );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/services/list' );
	}

	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Services list");
		$this->view->description = $this->translator->translate("Here you can see all the subscribed services list from the customers.");
		$this->view->buttons = array(array("url" => "/admin/services/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( OrdersItems::grid() )->datagrid ();
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( OrdersItems::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( OrdersItems::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/services/process" );
		$this->view->title = $this->translator->translate("New Service");
		$this->view->description = $this->translator->translate("Create a new service");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
							   array("url" => "/admin/services/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
		
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
				$this->view->title = $this->translator->translate ( 'Are you sure to delete the service selected?' );
				$this->view->description = $this->translator->translate ( 'The service will no longer be recoverable' );
				
				$record = $this->services->find ( $id, null, true );
				$this->view->recordselected = $record [0] ['description'];
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the service
	 * @return unknown_type
	 */
	public function deleteAction() {
		$files = new Files ( );
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			if (file_exists ( PUBLIC_PATH . "/documents/$id" )) {
				Shineisp_Commons_Utilities::delTree ( PUBLIC_PATH . "/documents/$id" );
			}
			$this->services->find ( $id )->delete ();
		}
		return $this->_helper->redirector ( 'index', 'services' );
	}
	
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/services/process' );
		$service_domains = new OrdersItemsDomains ( );
		$form->getElement ( 'save' )->setLabel ( 'Update' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/services/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/services/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		try {
			if (! empty ( $id ) && is_numeric ( $id )) {
				$form->getElement ( 'domains' )->setMultiOptions ( Domains::getFreeOrderDomainsList ( $id ) );
				
				$rs = $this->services->getAllInfo ( $id, null, true );
				
				if (! empty ( $rs )) {
					$form->getElement ( 'domains_selected' )->setMultiOptions ( $service_domains->getList ( $id ) );
					$rs ['date_start'] = Shineisp_Commons_Utilities::formatDateOut ( $rs ['date_start'] );
					$rs ['date_end'] = Shineisp_Commons_Utilities::formatDateOut ( $rs ['date_end'] );
					$rs ['customer_id'] = $rs ['Orders']['customer_id'];
					$form->populate ( $rs );
					$this->view->buttons[] = array("url" => "/admin/services/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right')));
						
				}
				
				// Get all the messages attached to the ordersitems
				$this->view->messages = Messages::find ( 'detail_id', $id, true );
				$this->view->owner_datagrid = $this->ownerGrid ( $rs ['Orders'] ['customer_id'] );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
		
		$this->view->title = $this->translator->translate("Service Details");
		$this->view->description = $this->translator->translate("Here you can see the datails of the service subscribed by the customer.");
		
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	/**
	 * ownerGrid
	 * Get the customer/owner information.
	 * @return array
	 */
	private function ownerGrid($customerid) {
		if (is_numeric ( $customerid )) {
			$customer = Customers::find ( $customerid, 'company, firstname, lastname, email' );
			if (isset ( $customer )) {
				return array ('records' => array($customer), 'editpage' => 'customers' );
			}
		}
	}
	
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$i = 0;
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$form = $this->getForm ( "/admin/services/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/services/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/services/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'services', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the id 
			$id = $this->getRequest ()->getParam ( 'detail_id' );
			
			// Set the new values
			if (is_numeric ( $id )) {
				$this->services = Doctrine::getTable ( 'OrdersItems' )->find ( $id );
			}
			
			// Get the values posted
			$params = $form->getValues ();
			$datestart = explode(" ", $params ['date_start']);
			try {
				$months = BillingCycle::getMonthsNumber ( $params ['billing_cycle_id'] );
				if($months > 0){
					$date_end = Shineisp_Commons_Utilities::add_date ($datestart[0], null, $months );
				}else{
					$date_end = null;
				}
				
				$this->services->date_start = Shineisp_Commons_Utilities::formatDateIn ( $params ['date_start'] );
				$this->services->date_end =  Shineisp_Commons_Utilities::formatDateIn ( $date_end );
				$this->services->order_id = $params ['order_id'];
				$this->services->product_id = $params ['product_id'];
				$this->services->billing_cycle_id = $params ['billing_cycle_id'];
				$this->services->quantity = $params ['quantity'];
				$this->services->status_id = $params ['status_id'];
				$this->services->setup = $params ['setup'];
				$this->services->note = $params ['note'];
				
				// Save the data
				$this->services->save ();
				$id = is_numeric ( $id ) ? $id : $this->services->getIncremented ();
				
				// Set the autorenew 
				OrdersItems::setAutorenew($id, $params ['autorenew']);
				
				// Save the message note
				if (! empty ( $params ['message'] )) {
					$message = new Messages ( );
					$message->dateposted = date ( 'Y-m-d H:i:s' );
					$message->message = $params ['message'];
					$message->isp_id = 1;
					$message->detail_id = $id;
					$message->save ();
				}
				
				// Clear the list from the DB
				Doctrine::getTable ( 'OrdersItemsDomains' )->findBy ( 'orderitem_id', $id )->delete ();
				
				if ($params ['domains_selected']) {
					$service_domains = new Doctrine_Collection ( 'OrdersItemsDomains' );
					foreach ( $params ['domains_selected'] as $domain ) {
						$service_domains [$i]->domain_id = $domain;
						$service_domains [$i]->order_id = $params ['order_id'];
						$service_domains [$i]->orderitem_id = $id;
						$i ++;
					}
					$service_domains->save ();
				}
				
				$this->_helper->redirector ( 'edit', 'services', 'admin', array ('id' => $id, 'mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
			
			} catch ( Exception $e ) {
				$this->_helper->redirector ( 'edit', 'services', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'Unable to process request at this time.' ) . ": " . $e->getMessage (), 'status' => 'error' ) );
			}
			
			$redirector->gotoUrl ( "/admin/services/edit/id/$id" );
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Service Details");
            $this->view->description = $this->translator->translate("Here you can see the datails of the service subscribed by the customer.");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_ServicesForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
}