<?php

/**
 * PanelsController
 * Manage the Control Panels 
 * @version 1.0
 */

class Admin_PanelsController extends Shineisp_Controller_Admin {
	
	protected $panel;
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
		$this->panel = new Panels ();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "panel" )->setModel ( $this->panel );
				
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/panels/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Panels list");
		$this->view->description = $this->translator->translate("Here you can see all the panel articles.");
		$this->view->buttons = array(array("url" => "/admin/panels/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)));
		$this->datagrid->setConfig ( Panels::grid() )->datagrid ();
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( Panels::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/panels/process" );
		$this->view->title = $this->translator->translate("Panels Details");
		$this->view->description = $this->translator->translate("Here you can handle the ISP Panels parameters");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
							   array("url" => "/admin/panels/list", "label" => $this->translator->translate('List'), "params" => array('css' => null)));
		
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
				$this->view->title = $this->translator->translate ( 'Are you sure you want to delete this panel configuration?' );
				$this->view->description = $this->translator->translate ( 'The configuration will no longer be available.' );
				
				$record = $this->panel->find ( $id );
				$this->view->recordselected = $this->translator->translate ( $record ['subject'] );
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ), 'status' => 'danger' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * Delete a record previously selected by the panel
	 */
	public function deleteAction() {
		$files = new Files ( );
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			$this->panel->find ( $id )->delete ();
		}
		return $this->_helper->redirector ( 'index', 'panels' );
	}
	
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/panels/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/panels/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/panels/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			
			$form = Panels::getParameters($id, $form);
			
			$rs = $this->panel->getAllInfo ( $id );
			
			if (! empty ( $rs )) {
				$form->populate ( $rs );
			}
			
			$this->view->buttons[] = array("url" => "/admin/panels/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => null));
		}
		
		$this->view->title = $this->translator->translate("ISP Panel Configuration");
		$this->view->description = $this->translator->translate("Here you can edit the ISP control panel configuration.");
		
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
		$form = $this->getForm ( "/admin/panels/process" );
		$request = $this->getRequest ();
		$id = $request->getParam('panel_id');
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/panels/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/panels/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		if(!empty($id)){
			// Add the isp parameters fields
			$form = Panels::getParameters($request->getParam('panel_id'), $form);
		}
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'panel', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			
			// Save all the fields
			$id = Panels::saveAll($form->getValues ());

			// Save the isp parameter 
			if($form->getSubForm('parameters')){
				Panels::saveParameterValues($form->getSubForm('parameters')->getValues(), $id);
			}
			
			$redirector->gotoUrl ( "/admin/panels/edit/id/$id" );
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Panels Details");
			$this->view->description = $this->translator->translate("Here you can correct the parameters sent");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_PanelsForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
}
