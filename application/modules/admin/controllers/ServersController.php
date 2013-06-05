<?php

/**
 * ServersController
 * Manage the isp profile
 * @version 1.0
 */

class Admin_ServersController extends Shineisp_Controller_Admin {
	
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
				$this->view->title = $this->translator->translate ( 'Are you sure to delete this server?' );
				$this->view->description = $this->translator->translate ( 'If you delete this server all the data will be no more longer available.' );
				
				$record = Servers::find ( $id );
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
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
				Servers::bulk_delete(array($id));
			}
		} catch ( Exception $e ) {
			die ( $e->getMessage () . " " . $e->getTraceAsString () );
		}
		
		return $this->_helper->redirector ( 'index', 'servers' );
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
		$this->view->title = $this->translator->translate("Servers list");
		$this->view->description = $this->translator->translate("Here you can see all the servers.");
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
		$this->view->title = $this->translator->translate("New server");
		$this->view->description = $this->translator->translate("Insert all the details of the new server.");
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
		
		$id = $this->getRequest ()->getParam ( 'id' );
		$rs = Servers::getAll($id);
		$panel_id = ( isset($rs['panel_id']) ) ? $rs['panel_id']: null;
		
		// Add the customer custom attributes
		$form = CustomAttributes::getElements($form, "servers" , $panel_id);
		
		return $form;
	}
	
	/**
	 * accountAction
	 * Manage the Isp account settings.
	 * @return void
	 */
	public function editAction() {
		
		$form = $this->getForm ( '/admin/servers/process' );
		$id   = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/servers/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/servers/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			
			$rs = Servers::getAll($id);
			if (! empty ( $rs )) {
				$panel_id = ( isset($rs['panel_id']) ) ? $rs['panel_id']: null;
				
				$arrCustomAttributes = CustomAttributes::getElementsValues($id, 'servers', $panel_id);

				$rs += $arrCustomAttributes;
				
				$form->populate ( $rs );
				$this->view->buttons[] = array("url" => "/admin/servers/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right')));
			}
			
		}
		$this->view->form = $form;
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		$this->view->title = $this->translator->translate("Edit Server");
		$this->view->description = $this->translator->translate("Edit the server parameters.");
		$this->render ( 'applicantform' );
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$request = $this->getRequest ();
		
		$this->view->title = $this->translator->translate("Edit Server");
		$this->view->description = $this->translator->translate("Edit the server parameters.");
		
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
		$serverID = Servers::saveAll($form->getValues ());
		
		OrdersItemsServers::removeServer(89,6);

		// Save the attributes
		$attributeValues = $form->getSubForm('attributes')->getValues();
		CustomAttributes::saveElementsValues($attributeValues, $serverID, "servers");
		
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
    