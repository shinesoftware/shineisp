<?php

/**
 * Invoice Purchase Category Controller
 * Manage the invoice purchase category table
 * @version 1.0
 */

class Admin_PurchasescategoriesController extends Shineisp_Controller_Admin {
	
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
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->categories = new PurchaseCategories();
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "admin" )->setModel ( $this->categories );		
	}
	
	/**
	 * indexAction
	 * Show files categories list (Call listAction method)
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/purchasescategories/list' );
	}
	
	/**
	 * indexAction
	 * Show files categories list
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Invoice purchasese categories list");
		$this->view->description = $this->translator->translate("Here you can see all the invoice purchases categories.");
		$this->view->buttons = array(array("url" => "/admin/purchasescategories/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( PurchaseCategories::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( PurchaseCategories::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( PurchaseCategories::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/purchasescategories/process" );
		$this->view->title = $this->translator->translate("Invoice purchase category Details");
		$this->view->description = $this->translator->translate("Here you can handle the invoice purchase catgeories parameters");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
									 array("url" => "/admin/purchasescategories/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
		$this->render ( 'applicantform' );
	}
	
	/**
	 * resetAction
	 * Reset the filter previously set
	 */
	public function resetAction() {
		$NS = new Zend_Session_Namespace ( 'Admin' );
		unset ( $NS->search_category );
		$this->_helper->redirector ( 'index', 'purchasescategories' );
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
				$this->view->title = $this->translator->translate ( 'Are you sure to delete the record selected?' );
				
				$record = $this->categories->find ( $id );
				$this->view->recordselected = $record [0] ['name'];
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the category
	 * @return unknown_type
	 */
	public function deleteAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		try {
			$this->categories->find ( $id )->delete ();
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'list', 'purchasescategories', 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ) . ": " . $e->getMessage (), 'status' => 'error' ) );
		}
		return $this->_helper->redirector ( 'list', 'purchasescategories', 'admin' );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/purchasescategories/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/purchasescategories/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/purchasescategories/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = $this->categories->getAllInfo ( $id, null, true );
			
			if (! empty ( $rs [0] )) {
				$form->populate ( $rs [0] );
			}
			
			$this->view->buttons[] = array("url" => "/admin/purchasescategories/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right')));
				
		}
		
		$this->view->title = $this->translator->translate("Invoice purchase category Details");
        $this->view->description = $this->translator->translate("Here you can edit the main file category information paramenters. Be careful, if you change something the module could be damaged.");
		
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	/**
	 * processAction
	 * Update the record previously selected or save new record
	 * @return unknown_type
	 */
	public function processAction() {
		$i = 0;
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$form = $this->getForm ( "/admin/purchasescategories/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/purchasescategories/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/purchasescategories/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'purchasescategories', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the id 
			$id = $this->getRequest ()->getParam ( 'category_id' );
			
			// Set the new values
			if (is_numeric ( $id )) {
				$this->categories = Doctrine::getTable ( 'PurchaseCategories' )->find ( $id );
			}
			
			// Get the values posted
			$params = $form->getValues ();
			try {
				
				$this->categories->category = $params ['category'];
				
				// Save the data
				$this->categories->save ();
				$id = is_numeric ( $id ) ? $id : $this->categories->getIncremented ();
				
				$this->_helper->redirector ( 'edit', 'purchasescategories', 'admin', array ('id' => $id, 'mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
			
			} catch ( Exception $e ) {
				$this->_helper->redirector ( 'edit', 'purchasescategories', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'Unable to process request at this time.' ) . ": " . $e->getMessage (), 'status' => 'error' ) );
			}
			
			$redirector->gotoUrl ( "/admin/purchasescategories/edit/id/$id" );
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Invoice purchase category Edit");
			$this->view->description = $this->translator->translate("Edit the invoice purchase category information");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return Admin_Form_FilecategoriesForm form
	 */
	private function getForm($action) {
		$form = new Admin_Form_PurchasescategoriesForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
}