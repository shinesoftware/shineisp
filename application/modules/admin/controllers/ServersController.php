<?php

/**
 * ServersController
 * Manage the isp profile
 * @version 1.0
 */

class Admin_ServersController extends Zend_Controller_Action {
	
	protected $servers;
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
		$this->reviews = new Servers();
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "servers" )->setModel ( $this->servers );		
	}
	
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/servers/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = "Servers list";
		$this->view->description = "Here you can see all the servers.";
		$this->view->buttons = array(array("url" => "/admin/servers/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( Servers::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( Servers::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( Servers::grid() )->search ();
	}
	
	/**
	 * newAction
	 * Create the form module in order to create a record
	 * @return unknown_type
	 */
	public function newAction() {
		$this->view->form = $this->getForm ( "/admin/servers/process" );
		$this->view->title = "New server";
		$this->view->description = "Insert all the details of the new server.";
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
									 array("url" => "/admin/servers/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
		
		$this->render ( 'applicantform' );
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_ServersForm ( array ('action' => $action, 'method' => 'post' ) );
		
		// Add the customer custom attributes
		$form = CustomAttributes::getElements($form, "servers");
		
		return $form;
	}
	
	/**
	 * accountAction
	 * Manage the Isp account settings.
	 * @return void
	 */
	public function editAction() {
		
		$form = $this->getForm ( '/admin/servers/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/servers/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/servers/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			
			$rs = Servers::getAll($id);
			
			if (! empty ( $rs )) {
				$rs += CustomAttributes::getElementsValues($id);
				$form->populate ( $rs );
				$this->view->buttons[] = array("url" => "/admin/servers/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right')));
			}
			
		}
		$this->view->form = $form;
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		$this->view->title = "Edit Server";
		$this->view->description = "Edit the server parameters.";
		$this->render ( 'applicantform' );
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$request = $this->getRequest ();
		
		$this->view->title = "Edit Server";
		$this->view->description = "Edit the server parameters.";
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/servers/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/servers/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'index' );
		}
		
		// Get our form and validate it
		$form = $this->getForm ( '/admin/servers/process' );
		
		if (! $form->isValid ( $request->getPost () )) {
			$this->view->form = $form;  // Invalid entries
			return $this->render ( 'applicantform' ); // re-render the login form
		}
		
		// Save the data
		$serverID = Servers::saveAll($request->getPost ());

		// Save the attributes
		CustomAttributes::saveElementsValues($form->getSubForm('attributes')->getValues(), $serverID, "servers");
		
		return $this->_helper->redirector ( 'list', 'servers', 'admin' );
	
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
			foreach ( $items as $customerid ) {
				if (is_numeric ( $customerid )) {
					$this->servers->set_status ( $customerid, $status ); // set it as deleted
				}
			}
			return true;
		}
		return false;
	}
}
    