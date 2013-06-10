<?php

/**
 * PurchasesController
 * Manage the purchase purchases
 * @version 1.0
 */

class Admin_PurchasesController extends Shineisp_Controller_Admin {
	
	protected $purchases;
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
		$this->purchases = new PurchaseInvoices ();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "purchases" )->setModel ( $this->purchases );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$this->_helper->redirector ( 'list', 'purchases', 'admin' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Purchases list");
		$this->view->description = $this->translator->translate("Here you can see all the purchases.");
		$this->view->buttons = array(array("url" => "/admin/purchases/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( PurchaseInvoices::grid() )->datagrid ();
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( PurchaseInvoices::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( PurchaseInvoices::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/purchases/process" );
		$this->view->title = $this->translator->translate("New Purchase Invoice");
		$this->view->description = $this->translator->translate("Create a new purchase invoice using this form.");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
									 array("url" => "/admin/purchases/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
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
				$this->view->title = $this->translator->translate ( 'Are you sure to delete the purchase invoice selected?' );
				$this->view->description = $this->translator->translate ( 'The purchase will not be longer available ' );
				
				$record = $this->purchases->find ( $id );
				$this->view->recordselected = $record ['number'] . " - " . Shineisp_Commons_Utilities::formatDateOut ( $record ['creationdate'] );
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
		$files = new Files ( );
		$id = $this->getRequest ()->getParam ( 'id' );
		try {
			PurchaseInvoices::DeleteByID($id);
			
		} catch ( Exception $e ) {
			die ( $e->getMessage () . " " . $e->getTraceAsString () );
		}
		
		return $this->_helper->redirector ( 'index', 'purchases' );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/purchases/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/purchases/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/purchases/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		$this->view->title = $this->translator->translate("Purchases Edit");
		$this->view->description = $this->translator->translate("Here you can edit the selected purchase invoice.");
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = $this->purchases->find ( $id )->toArray ();
			if (! empty ( $rs )) {
				$rs ['creationdate'] = Shineisp_Commons_Utilities::formatDateOut ( $rs ['creationdate'] );
				$rs ['expiringdate'] = Shineisp_Commons_Utilities::formatDateOut ( $rs ['expiringdate'] );
				$rs ['paymentdate'] = Shineisp_Commons_Utilities::formatDateOut ( $rs ['paymentdate'] );
				$this->view->title = $this->translator->translate("Purchase invoice") . " #" . $rs['number'] . " - " . $rs['creationdate'];
				
				$this->view->id = $id;
				$this->view->attachment = $rs['document'];
				$form->populate ( $rs );
				
				$this->view->buttons[] = array("url" => "/admin/purchases/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right')));
				
			}
		}
		
		$this->view->mex = urldecode ( $this->getRequest ()->getParam ( 'mex' ) );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$form = $this->getForm ( "/admin/purchases/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/banks/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/banks/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'purchases', 'admin' );
		}

		if ($form->isValid ( $request->getPost () )) {
			$id = PurchaseInvoices::saveAll($this->getRequest ()->getParam ( 'purchase_id' ), $request->getPost ());
			$this->_helper->redirector ( 'edit', 'purchases', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
			
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Purchase Invoice Edit");
			$this->view->description = $this->translator->translate("Here you can edit the selected purchase.");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_PurchasesForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
	/**
	 * Delete the file attached
	 */
	public function deletefileAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if(PurchaseInvoices::DeleteAttachment($id)){
			$this->_helper->redirector ( 'edit', 'purchases', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );	
		}else{
			$this->_helper->redirector ( 'edit', 'purchases', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The file has not been found.' ), 'status' => 'error' ) );
		}
	}
}