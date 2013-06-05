<?php
/**
 * Applications Controllers
 * Manage the applications needed by OAuth2
 * @version 1.0
 * @author  GUEST.it s.r.l. <assistenza@guest.it>
 */

class Admin_ApplicationsController extends Shineisp_Controller_Admin {
	
	protected $applications;
	protected $datagrid;
	protected $session;
	protected $translator;
	
	public function preDispatch() {	
		$this->session = new Zend_Session_Namespace ( 'Admin' );
		$this->applications = new OauthClients();
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "applications" )->setModel ( $this->applications );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return 
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/applications/list' );
	}

	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Applications");
		$this->view->description = $this->translator->translate("Here you can see all applications allowed access via API");
		$this->view->buttons = array(array("url" => "/admin/applications/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right')))
								);
		$this->datagrid->setConfig ( OauthClients::grid() )->datagrid ();
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( OauthClients::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( OauthClients::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/applications/process" );
		$this->view->title = $this->translator->translate("Applications");
		$this->view->description = $this->translator->translate("Here you can edit the application details.");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
									 array("url" => "/admin/applications/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
		
		$this->render ( 'applicantform' );
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_ApplicationsForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
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
				$this->view->title = $this->translator->translate ( 'Are you  sure to delete the selected application?' );
				$this->view->description = $this->translator->translate ( 'If you delete this application, it will be no longer available.' );
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}

	/**
	 * confirmoverwritekeysAction
	 * Ask to the user a confirmation before overwriting keys
	 * @return null
	 */
	public function confirmoverwritekeysAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		
		try {
			if (is_numeric ( $id )) {
				$this->view->back = "/admin/$controller/edit/id/$id";
				$this->view->goto = "/admin/$controller/overwritekey/id/$id";
				$this->view->title = $this->translator->translate ( 'Are you sure to generate a new keypair? All existings application using these will be unable to access' );
				$this->view->description = $this->translator->translate ( 'If you generate a new keypair, all existings application using these will be unable to access.' );
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
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$form       = $this->getForm ( '/admin/'.$controller.'/process' );
		$id         = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/$controller/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/$controller/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			
			$rs = OauthClients::getAll($id);
			if (! empty ( $rs )) {
				$form->populate ( $rs );
				$this->view->buttons[] = array("url" => "/admin/$controller/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right')));
				$this->view->buttons[] = array("url" => "/admin/$controller/confirmoverwritekeys/id/$id", "label" => $this->translator->translate('Generate Keypairs'), "params" => array('css' => array('button', 'float_right')));
			}
			
		}
		
		$this->view->description = "Here you can edit the template details";
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
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		
		$form = $this->getForm ( "/admin/$controller/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/$controller/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/$controller/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);

		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', $controller, 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the id 
			$id = intval($this->getRequest ()->getParam ( 'id' ));
			
			// Get the values posted
			$params = $request->getPost ();
			
			// Save all the data 
			$id = OauthClients::saveAll ( $params );
			
			$redirector->gotoUrl ( "/admin/$controller/list/" );
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Application");
			$this->view->description = $this->translator->translate("Here you can edit the applications detail");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * overwritekeysAction
	 * Overwrite keypairs
	 * @return unknown_type
	 */
	public function overwritekeyAction() {
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$form       = $this->getForm ( '/admin/'.$controller.'/process' );
		$id         = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "/admin/$controller/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$client = OauthClients::getAll($id);
			if (! empty ( $client ) && isset($client['client_id']) ) {
				$keypair = OauthJwt::generate($client['client_id']);
			}
		}
		
		$this->view->description = "Here you can see your private key and it's passphrase. You need both to access API via OAuth2";
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		$this->view->passphrase  = $keypair['passphrase'];
		$this->view->private_key = $keypair['private_key'];
		$this->render ( 'keypairs' );
	}
	
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the order
	 * @return unknown_type
	 */
	public function deleteAction() {
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$files      = new Files ();
		$id         = $this->getRequest ()->getParam ( 'id' );
		try {
			if (is_numeric ( $id )) {
				OauthClients::del($id);
			}
		} catch ( Exception $e ) {
			die ( $e->getMessage () . " " . $e->getTraceAsString () );
		}
		
		return $this->_helper->redirector ( 'index', $controller );
	}	
	
		
}