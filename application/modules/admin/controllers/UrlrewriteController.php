<?php

/**
 * UrlrewriteController
 * Manage the Urlrewrite table
 * @version 1.0
 */

class Admin_UrlrewriteController extends Shineisp_Controller_Admin {
	
	protected $urlrewrite;
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
		$this->urlrewrite = new UrlRewrite();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "urlrewrite" )->setModel ( $this->urlrewrite );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/urlrewrite/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Url rewrite list");
		$this->view->description = $this->translator->translate("Here you can see all the url rewrite.");
		$this->datagrid->setConfig ( Urlrewrite::grid() )->datagrid ();
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( Urlrewrite::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( Urlrewrite::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/urlrewrite/process" );
		$this->view->title = $this->translator->translate("Url Rewrite Details");
		$this->view->description = $this->translator->translate("Here you can handle the url rewrite parameters");
		$this->render ( 'applicantform' );
	}
	
	/**
	 * resetAction
	 * Reset the filter previously set
	 */
	public function resetAction() {
		$NS = new Zend_Session_Namespace ( 'Admin' );
		unset ( $NS->search_urlrewrite );
		$this->_helper->redirector ( 'index', 'urlrewrite' );
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
				
				$record = $this->urlrewrite->find ( $id );
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
			$this->urlrewrite->find ( $id )->delete ();
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'list', 'urlrewrite', 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ) . ": " . $e->getMessage (), 'status' => 'error' ) );
		}
		return $this->_helper->redirector ( 'list', 'urlrewrite', 'admin' );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/urlrewrite/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = $this->urlrewrite->getAllInfo ( $id, null, true );
			
			if (! empty ( $rs [0] )) {
				$form->populate ( $rs [0] );
			}
		}
		
		$this->view->title = $this->translator->translate("Url Rewrite Details");
        $this->view->description = $this->translator->translate("Here you can edit the main Url Rewrite information paramenters.");
		
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
		$form = $this->getForm ( "/admin/urlrewrite/process" );
		$request = $this->getRequest ();
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'urlrewrite', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the id 
			$id = $this->getRequest ()->getParam ( 'url_rewrite_id' );
			
			// Set the new values
			if (is_numeric ( $id )) {
				$this->urlrewrite = Doctrine::getTable ( 'Urlrewrite' )->find ( $id );
			}
			
			// Get the values posted
			$params = $form->getValues ();
			try {
				
				$this->urlrewrite->target_path = $params ['target_path'];
				$this->urlrewrite->request_path = $params ['request_path'];
				$this->urlrewrite->description = $params ['description'];
				$this->urlrewrite->product_id = !is_numeric($params ['product_id']) ? $params ['product_id'] : null;
				$this->urlrewrite->category_id = !is_numeric($params ['category_id']) ? $params ['category_id'] : null;
				$this->urlrewrite->date_added = date('Y-m-d H:i:s');
				$this->urlrewrite->temporary = $params ['temporary'] ? 1 : 0;
				
				// Save the data
				$this->urlrewrite->save ();
				$id = is_numeric ( $id ) ? $id : $this->urlrewrite->getIncremented ();
				
				$this->_helper->redirector ( 'edit', 'urlrewrite', 'admin', array ('id' => $id, 'mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
			
			} catch ( Exception $e ) {
				$this->_helper->redirector ( 'edit', 'urlrewrite', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'Unable to process request at this time.' ) . ": " . $e->getMessage (), 'status' => 'error' ) );
			}
			
			$redirector->gotoUrl ( "/admin/urlrewrite/edit/id/$id" );
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Url Rewrite Processing form");
			$this->view->description = $this->translator->translate("There was an error during the insert of data");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_UrlrewriteForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
}