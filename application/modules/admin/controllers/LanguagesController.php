<?php

/**
 * LanguagesController
 * Manage the languages
 * @version 1.0
 */

class Admin_LanguagesController extends Shineisp_Controller_Admin {
	
	protected $languages;
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
		$this->languages = new Languages ();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "languages" )->setModel ( $this->languages );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/languages/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Languages list");
		$this->view->description = $this->translator->translate("Here you can see all the languages.");
		$this->view->buttons = array(array("url" => "/admin/languages/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)));
		$this->datagrid->setConfig ( Languages::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( Languages::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( Languages::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/languages/process" );
		$this->view->title = $this->translator->translate("Language Details");
		$this->view->description = $this->translator->translate("Here you can handle the bank parameters");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
									 array("url" => "/admin/languages/list", "label" => $this->translator->translate('List'), "params" => array('css' => null)));
		$this->render ( 'applicantform' );
	}
	
	/**
	 * resetAction
	 * Reset the filter previously set
	 */
	public function resetAction() {
		$NS = new Zend_Session_Namespace ( 'Admin' );
		unset ( $NS->search_languages );
		$this->_helper->redirector ( 'index', 'languages' );
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
				$this->view->title = $this->translator->translate ( 'Are you sure you want to delete the selected record?' );
				$this->view->description = $this->translator->translate ( 'If you delete the language information all the translated items will be deleted' );
				
				$record = $this->languages->find ( $id );
				$this->view->recordselected = $record [0] ['language'];
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ), 'status' => 'danger' ) );
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
			$this->languages->find ( $id )->delete ();
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'list', 'languages', 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ) . ": " . $e->getMessage (), 'status' => 'danger' ) );
		}
		return $this->_helper->redirector ( 'list', 'languages', 'admin' );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/languages/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/languages/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/languages/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = $this->languages->getAllInfo ( $id, null, true );
			
			if (! empty ( $rs [0] )) {
				$form->populate ( $rs [0] );
			}
			
			$this->view->buttons[] = array("url" => "/admin/languages/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => null));
				
		}
		
		$this->view->title = $this->translator->translate("Language Details");
        $this->view->description = $this->translator->translate("Here you can edit the languages");
		
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
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$form = $this->getForm ( "/admin/languages/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/languages/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/languages/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'languages', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the id 
			$id = $this->getRequest ()->getParam ( 'language_id' );
			
			// Set the new values
			if (is_numeric ( $id )) {
				$this->languages = Doctrine::getTable ( 'Languages' )->find ( $id );
			}
			
			// Get the values posted
			$params = $form->getValues ();
			try {
				
				$this->languages->language = $params ['language'];
				$this->languages->code = $params ['code'];
				$this->languages->locale = $params ['locale'];
				$this->languages->active = $params ['active'];
				$this->languages->base = $params ['base'];
				
				// Save the data
				$this->languages->save ();
				$id = is_numeric ( $id ) ? $id : $this->languages->getIncremented ();
				
				if(!empty($params ['translations'])){
					$file = fopen ( APPLICATION_PATH . "/languages/" . $params ['locale'] . ".csv", 'w+' );
					fputs ( $file, $params ['translations'] );
					fclose ( $file );
				}
								
				$this->_helper->redirector ( 'edit', 'languages', 'admin', array ('id' => $id, 'mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
			
			} catch ( Exception $e ) {
				$this->_helper->redirector ( 'edit', 'languages', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'Unable to process the request at this time.' ) . ": " . $e->getMessage (), 'status' => 'danger' ) );
			}
			
			$redirector->gotoUrl ( "/admin/languages/edit/id/$id" );
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Language Edit");
			$this->view->description = $this->translator->translate("Edit the bank information");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_LanguagesForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
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
			foreach ( $items as $categoryid ) {
				if (is_numeric ( $categoryid )) {
					$this->languages->set_status ( $categoryid, $status );
				}
			}
			return true;
		}
		return false;
	}
	
}
