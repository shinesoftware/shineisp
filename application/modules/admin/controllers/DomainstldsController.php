<?php

/**
 * Admin_DomainstldsController
 * Manage the Domains TLDS category table
 * @version 1.0
 */

class Admin_DomainstldsController extends Shineisp_Controller_Admin {
	
	protected $domainstlds;
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
		$this->domainstlds = new DomainsTlds ();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "domainstlds" )->setModel ( $this->domainstlds );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/domainstlds/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Domains Tlds list");
		$this->view->description = $this->translator->translate("Here you can see all the domain tlds.");
		$this->view->buttons = array(array("url" => "/admin/domainstlds/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( DomainsTlds::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( DomainsTlds::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( DomainsTlds::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/domainstlds/process" );
		$this->view->title = $this->translator->translate("Tld Details");
		$this->view->description = $this->translator->translate("Here you can handle the tlds details");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
									 array("url" => "/admin/domainstlds/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
		$this->render ( 'applicantform' );
	}
	
	/**
	 * resetAction
	 * Reset the filter previously set
	 */
	public function resetAction() {
		$NS = new Zend_Session_Namespace ( 'Admin' );
		unset ( $NS->search_tlds);
		$this->_helper->redirector ( 'index', 'domainstlds' );
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
				$this->view->description = $this->translator->translate ( 'If you delete the bank information parameters the customers cannot pay you anymore with this method of payment' );
				
				$record = $this->domainstlds->getAllInfo ( $id );
				$this->view->recordselected = "";
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
			$this->domainstlds->find ( $id )->delete ();
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'list', 'domainstlds', 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ) . ": " . $e->getMessage (), 'status' => 'error' ) );
		}
		return $this->_helper->redirector ( 'list', 'domainstlds', 'admin' );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$ns = new Zend_Session_Namespace ( 'Admin' );
		$form = $this->getForm ( '/admin/domainstlds/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = $this->domainstlds->getAllInfo ( $id, $ns->langid);
			
			if (! empty ( $rs  )) {
				$rs['language_id'] = $ns->langid;
				$form->populate ( $rs );
			}
			
			// Create the buttons in the edit form
			$this->view->buttons = array(
					array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
					array("url" => "/admin/domainstlds/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right'))),
					array("url" => "/admin/domainstlds/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
					array("url" => "/admin/domainstlds/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
			);
		}
		
		$this->view->title = $this->translator->translate("Tlds Details");
        $this->view->description = $this->translator->translate("Here you can edit the main Domain Tlds information parameters.");
		
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
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
		$i = 0;
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$form = $this->getForm ( "/admin/domainstlds/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/domainstlds/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/domainstlds/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'domainstlds', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			$id = DomainsTlds::saveAll($request->getPost (), $this->session->langid);
			$redirector->gotoUrl ( "/admin/domainstlds/edit/id/$id" );
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Domain Tlds");
			$this->view->description = $this->translator->translate("Check the tlds data");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_DomainstldsForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
}