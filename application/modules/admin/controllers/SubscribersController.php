<?php

/**
 * Admin_SubscribersController
 * Handle the NewslettersSubscribers of the NewslettersSubscribers
 * @version 1.0
 */

class Admin_SubscribersController extends Shineisp_Controller_Admin {
	
	protected $subscribers;
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
		$this->subscribers = new NewslettersSubscribers();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "subscribers" )->setModel ( $this->subscribers );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/subscribers/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Newsletter Subscribers");
		$this->view->description = $this->translator->translate("Here you can see the emails of the subscribers.");
		$this->view->buttons = array(array("url" => "/admin/subscribers/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)));
		$this->datagrid->setConfig ( NewslettersSubscribers::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( NewslettersSubscribers::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( NewslettersSubscribers::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/subscribers/process" );
		$this->view->title = $this->translator->translate("Newsletter Subscriber");
		$this->view->description = $this->translator->translate("Here you can create a new subscription.");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
									 array("url" => "/admin/subscribers/list", "label" => $this->translator->translate('List'), "params" => array('css' => null)));
		
		$this->render ( 'applicantform' );
	}

	/**
	 * deleteAction
	 * Delete a record previously selected by the subscribers
	 * @return unknown_type
	 */
	public function deleteAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			NewslettersSubscribers::deleteItem( $id );
		}
		return $this->_helper->redirector ( 'index', 'subscribers' );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/subscribers/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/subscribers/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/subscribers/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$fields = "subscriber_id, s.email, DATE_FORMAT(s.subscriptiondate, '%d/%m/%Y') as subscriptiondate";
			$rs = $this->subscribers->getAllInfo ( $id, $fields, true );
			
			if (! empty ( $rs [0] )) {
				$form->populate ( $rs [0] );
				$this->view->record = $rs [0];
				
				// Hide these fields and values inside the vertical grid object
				$this->view->data = array ('records' => $rs );
				$this->view->buttons[] = array("url" => "/admin/subscribers/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => null));
				
			}
		}
		
		$this->view->title = $this->translator->translate("Subscriber edit");
		$this->view->description = $this->translator->translate("Here you can edit the subscribers.");
		
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
		$form = $this->getForm ( "/admin/subscribers/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/subscribers/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/subscribers/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'subscribers', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the id 
			$id = $this->getRequest ()->getParam ( 'subscriber_id' );
			
			// Set the new values
			if (is_numeric ( $id )) {
				$this->subscribers = NewslettersSubscribers::getbyId( $id );
			}
			
			// Get the values posted
			$params = $form->getValues ();
			
			$this->subscribers->email = $params ['email'];
			$this->subscribers->subscriptiondate = !empty($params ['subscriptiondate']) ? Shineisp_Commons_Utilities::formatDateIn($params ['subscriptiondate']) : date('Y-m-d H:i:s');
			$this->subscribers->save ();
			$id = is_numeric ( $id ) ? $id : $this->subscribers->getIncremented ();
			
			$redirector->gotoUrl ( "/admin/subscribers/edit/id/$id" );
		
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Subscriber Details");
			$this->view->description = $this->translator->translate("Here you can check all the submit information.");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_SubscribersForm( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}	
}