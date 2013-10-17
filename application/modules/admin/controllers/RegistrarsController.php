<?php

/**
 * Registrars
 * Manage the registrars items table
 * @version 1.0
 */

class Admin_RegistrarsController extends Shineisp_Controller_Admin {
	
	protected $registrars;
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
		$this->registrars = new Registrars();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "registrars" )->setModel ( $this->registrars );				
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/registrars/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Registrar Modules");
		$this->view->description = $this->translator->translate("Here you can see all the registrar module.");
		$this->view->buttons = array(array("url" => "/admin/registrars/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( Registrars::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( Registrars::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( Registrars::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/registrars/process" );
		$this->view->title = $this->translator->translate("New Registrar");
		$this->view->description = $this->translator->translate("Here you can create a new registrar.");
		
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
									 array("url" => "/admin/registrars/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
		
		$this->render ( 'applicantform' );
	}

	/**
	 * deleteAction
	 * Delete a record previously selected by the reviews
	 * @return unknown_type
	 */
	public function deleteAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			Registrars::deleteItem( $id );
		}
		return $this->_helper->redirector ( 'index', 'registrars' );
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
				$this->view->description = $this->translator->translate ( 'If you delete the bank information parameters the registrar will be no more available.' );
				
				$record = $this->registrars->find ( $id, null, true );
				$this->view->recordselected = $record [0] ['name'];
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}

	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/registrars/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/registrars/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/registrars/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		$this->view->description = "Here you can edit the registrar data.";
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = Registrars::find ( $id, null, true );
			if (! empty ( $rs[0] )) {
				$this->view->title = $this->translator->_("Registrar edit: %s", $rs[0]['name']);
				
				// Create the registrar custom form
				list($form, $config) = Admin_Form_RegistrarsForm::createRegistrarForm ( $form, $rs[0]['name'] );
				
				if(!empty($config->general->description)){
					$this->view->description = (string)$config->general->description;
				}
				
				if(!empty($config->general->help)){
					$this->view->help = (string)$config->general->help;
				}
				
				// Get the custom registrar settings
				$rs[0]['settings'] = json_decode($rs[0]['config'], true);
				$form->populate ( $rs[0] );
			}
			
			$this->view->buttons[] = array("url" => "/admin/registrars/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right')));
				
		}
		
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
		$form = $this->getForm ( "/admin/registrars/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/banks/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/banks/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'registrars', 'admin' );
		}

		// Create the registrar custom form
		list($form, $config) = Admin_Form_RegistrarsForm::createRegistrarForm ( $form, $request->getParam('name') );
		
		if ($form->isValid ( $request->getPost () )) {
			$params = $form->getValues ();
			
			// Save the data and get the registrar id 
			$id = Registrars::saveData($params, $params ['registrars_id']);;
			if (is_numeric ( $id )) {
				$this->_helper->redirector ( 'edit', 'registrars', 'admin', array ('id'=>$id, 'mex' => $this->translator->translate ( "The task requested has been executed successfully." ), 'status' => 'success' ) );
			} else {
				$redirector->gotoUrl ( "/admin/registrars/list/" );
			}
		} else {
			
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Registrar review");
			$this->view->description = $this->translator->translate("Here you can fix the registrar parameters.");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return form
	 */
	private function getForm($action) {
		$form = new Admin_Form_RegistrarsForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}

}
