<?php

/**
 * CmsPagesController
 * Manage the Pages Article Items
 * @version 1.0
 */

class Admin_CmspagesController extends Shineisp_Controller_Admin {
	
	protected $cmspages;
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
		$this->cmspages = new CmsPages ();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "cmspages" )->setModel ( $this->cmspages );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/cmspages/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Pages list");
		$this->view->description = $this->translator->translate("Here you can see all the published pages.");
		$this->view->buttons = array(array("url" => "/admin/cmspages/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)));
		$this->datagrid->setConfig ( CmsPages::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( CmsPages::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( CmsPages::grid() )->search ();
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
		$Session = new Zend_Session_Namespace ( 'Admin' );
		$this->view->form = $this->getForm ( "/admin/cmspages/process" );
		
		// I have to add the language id into the hidden field in order to save the record with the language selected 
		$this->view->form->populate ( array('language_id' => $Session->langid) );
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
									 array("url" => "/admin/cmspages/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')));
		
		$this->view->title = $this->translator->translate("Create a page");
		$this->view->description = $this->translator->translate("Here you can create a static page.");
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
				$this->view->title = $this->translator->translate ( 'Are you sure you want to delete this page?' );
				$this->view->description = $this->translator->translate ( 'The page will no longer be available.' );
				
				$record = $this->cmspages->find ( $id );
				$this->view->recordselected = $this->translator->translate ( $record ['title'] );
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ), 'status' => 'danger' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the cmspages
	 * @return unknown_type
	 */
	public function deleteAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			CmsPages::deleteItem( $id );
		}
		return $this->_helper->redirector ( 'index', 'cmspages' );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$Session = new Zend_Session_Namespace ( 'Admin' );
		$form = $this->getForm ( '/admin/cmspages/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		$this->view->title = $this->translator->translate("Edit Page");
		$this->view->description = $this->translator->translate("Here you can edit the page.");
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$record = $this->cmspages->getAllInfo ( $id );
			
			if (! empty ( $record )) {
				$form->populate ( $record );
				$url = $record['var'];
				$this->view->title = $record['title'];
			}
		}
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/cmspages/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => null)),
				array("url" => "/admin/cmspages/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/cmspages/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
				array("url" => "/cms/$url.html", "label" => $this->translator->translate('Visit'), "params" => array('css' => null,'target' => '_blank')),
		);
		
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$form = $this->getForm ( "/admin/cmspages/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/cmspages/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/cmspages/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'cmspages', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the id 
			$id = $this->getRequest ()->getParam ( 'page_id' );
			
			// Get the values posted
			$params = $form->getValues ();
			
			$id = CmsPages::saveAll($id, $params);
			$redirector->gotoUrl ( "/admin/cmspages/edit/id/$id" );
		
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("CMS Pages Details");
			$this->view->description = $this->translator->translate("Here you can reply to all the customers requests");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_CmspagesForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
}