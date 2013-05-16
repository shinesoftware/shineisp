<?php

/**
 * Admin_NewsletterController
 * Handle the Newsletters
 * @version 1.0
 */

class Admin_NewsletterController extends Zend_Controller_Action {
	
	protected $newsletter;
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
		$this->newsletter = new Newsletters ();
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "newsletter" )->setModel ( $this->newsletter );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/newsletter/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Newsletter list");
		$this->view->description = $this->translator->translate("Here you can see all the messages of the newsletter.");
		$this->view->buttons = array(array("url" => "/admin/newsletter/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( Newsletters::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( Newsletters::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( Newsletters::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/newsletter/process" );
		$this->view->title = $this->translator->translate("Newsletter");
		$this->view->description = $this->translator->translate("Here you can create the newsletter.");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
									 array("url" => "/admin/newsletter/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
		
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
				$this->view->title = $this->translator->translate ( 'Are you sure to delete the record selected?' );
				$this->view->description = $this->translator->translate ( 'If you delete the bank information parameters the customers cannot pay you anymore with this method of payment' );
	
				$record = $this->newsletter->find ( $id )->toArray();
				$this->view->recordselected = $record ['subject'];
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the newsletter
	 * @return unknown_type
	 */
	public function deleteAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			Newsletters::deleteItem( $id );
		}
		return $this->_helper->redirector ( 'index', 'newsletter' );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/newsletter/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$fields = "news_id, subject, DATE_FORMAT(sendat, '%d/%m/%Y %H:%i:%s') as sendat, DATE_FORMAT(sent, '%d/%m/%Y %H:%i:%s') as sent, subject, message";
			$rs = $this->newsletter->getAllInfo ( $id, $fields, true );
			
			if (! empty ( $rs [0] )) {
				$form->populate ( $rs [0] );
				$this->view->record = $rs [0];
				
				// Get the queue
				$this->view->queue = array('records' => NewslettersHistory::get_queue_by_newsletter_id($id, "newsletter_id, nh.date_added as added, nh.date_sent as sent, ns.email as email, c.lastname as user"));
				
				// Hide these fields and values inside the vertical grid object
				$this->view->data = array ('records' => $rs );
				
				// Create the buttons in the edit form
				$this->view->buttons = array(
						array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
						array("url" => "/admin/newsletter/sendtest//id/$id", "label" => $this->translator->translate('Test'), "params" => array('css' => array('button', 'float_right'))),
						array("url" => "/admin/newsletter/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right'))),
						array("url" => "/admin/newsletter/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
						array("url" => "/admin/newsletter/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
				);
			}
		}
		
		$this->view->title = $this->translator->translate("Newsletter edit");
		$this->view->description = $this->translator->translate("Here you can edit the newsletter.");
		
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
		$form = $this->getForm ( "/admin/newsletter/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/newsletter/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/newsletter/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'newsletter', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			
			// Get the values posted
			$params = $form->getValues ();
			
			// Save data
			$id = Newsletters::save_data($params);
			
			$redirector->gotoUrl ( "/admin/newsletter/edit/id/$id" );
		
		} else {
			$this->view->form = $form;
			$this->view->title = "Newsletter Details";
			$this->view->description = "Here you can check the newsletter information.";
			return $this->render ( 'applicantform' );
		}
	}
	
	
	/**
	 * Send a newsletter test to the default ISP email
	 */
	public function sendtestAction() {
		$id = $this->getRequest ()->getParam('id');
		if(is_numeric($id)){
			Newsletters::send_queue(true, $id);
			$this->_helper->redirector ( 'edit', 'newsletter', 'admin', array('id'=>$id, 'mex'=>$this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
		}
		$this->_helper->redirector ( 'list', 'newsletter', 'admin' );
	}
		
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_NewsletterForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}	
	
	
}