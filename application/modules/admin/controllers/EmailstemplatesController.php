<?php
/**
 * Hostingplans
 * Manage the hostingplans table
 * @version 1.0
 * @author  GUEST.it s.r.l. <assistenza@guest.it>
 */

class Admin_EmailstemplatesController extends Zend_Controller_Action {
	
	protected $emailstemplates;
	protected $datagrid;
	protected $session;
	protected $translator;
	
	public function preDispatch() {	
		$this->session = new Zend_Session_Namespace ( 'Admin' );
		$this->emailstemplates = new EmailsTemplates();
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "emailstemplates" )->setModel ( $this->emailstemplates );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return 
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/emailstemplates/list' );
	}

	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = "Email Templates";
		$this->view->description = "Here you can see all groups of servers.";
		$this->view->buttons = array(array("url" => "/admin/emailstemplates/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right')))
									,array("url" => "/admin/emailstemplates/confirmimport/", "label" => $this->translator->translate('Import'), "params" => array('css' => array('button', 'float_right')))
								);
		$this->datagrid->setConfig ( EmailsTemplates::grid() )->datagrid ();
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( EmailsTemplates::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( EmailsTemplates::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/emailstemplates/process" );
		$this->view->title = "Email Templates";
		$this->view->description = "Here you can edit the server group details.";
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
									 array("url" => "/admin/emailstemplates/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
		
		$this->render ( 'applicantform' );
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_EmailsTemplatesForm ( array ('action' => $action, 'method' => 'post' ) );
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
				$this->view->title = $this->translator->translate ( 'Are you  sure to delete the selected template?' );
				$this->view->description = $this->translator->translate ( 'If you delete this template, it will be no longer available.' );
				
				$record = $this->emailstemplates->getAllInfo ( $id );
				
				$this->view->recordselected = '';
                if ( isset($record) && isset($record ['name']) ) {
	                $this->view->recordselected = $record ['name'];
                }
				
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}

	/**
	 * confirmimportAction
	 * Ask to the user a confirmation before execute the import
	 * @return null
	 */
	public function confirmimportAction() {
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		
		try {
			$this->view->back = "/admin/$controller/list/";
			$this->view->goto = "/admin/$controller/import/";
			$this->view->title = $this->translator->translate ( 'Are you  sure to reimport all templates?' );
			$this->view->description = $this->translator->translate ( 'If you force import, all customization made to a template from the admin panel will no longer be available' );
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	/**
	 * importAcetion
	 * Import all templates from disks
	 * @return unknown_type
	 */
	public function importAction() {
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$importOK   = 0;
		$arrDeleted = array();

		/*
		 * Cycle all template and all languages
		 */
		$arrLanguages = Languages::getActiveLanguageList();
		if ( is_array($arrLanguages) && !empty($arrLanguages) ) {
			foreach ( $arrLanguages as $lang ) {
				if ( empty($lang['locale']) ) {
					continue;
				}
				
				$dir = PUBLIC_PATH . "/languages/emails/".$lang['locale'];
								
				foreach ( scandir($dir) as $file ) {
					if ( !preg_match('/^([a-z0-9A-Z\-\_]+)\.htm$/', $file, $out) ) {
						continue;
					}
					
					if ( empty($out[1]) ) {
						continue;
					}
					
					$code = $out[1];

					// Import
					$outArray = Shineisp_Commons_Utilities::getEmailTemplate($code, $lang['locale']);
					
					if ( !empty($outArray) ) {
						$importOK++;
					}
				}
				
			}
		}
				
		if ( $importOK > 0 ) {
			$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Template imported successfully' ), 'status' => 'success' ) );
		}

		$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'An error eccorued' ), 'status' => 'success' ) );	
		
	}

	
	/**
	 * deleteAction
	 * Delete a record previously selected by the customer
	 * @return unknown_type
	 */
	public function deleteAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$template   = EmailsTemplates::find($id, null, false);
		
		if ( is_object( $template ) ) {
			if ( isset($template->code) && !empty($template->code) ) {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'You can not delete system templates' ), 'status' => 'error' ) );
				die();	
			}
			
			$template->delete();
		}

		$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Template deleted successfully' ), 'status' => 'success' ) );	
		
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
		
		if ( !empty($id) && is_numeric($id) ) {
			$rs = $this->emailstemplates->find( $id, null, true, $this->session->langid );
			
			if (! empty ( $rs )) {
				// Join the translated data information to populate the form
				$data = !empty($rs['EmailsTemplatesData'][0]) ? $rs['EmailsTemplatesData'][0] : array();
				$rs   = array_merge($rs, $data);
				
				$rs['language_id'] = $this->session->langid; // added to the form the language id selected
				
				$form->populate ( $rs );
			}
			
			// Delete button only if no "code" is set. Templates with "code" are system templates and can not be deleted
			if ( empty($rs['code']) ) { 
				$this->view->buttons[] = array("url" => "/admin/$controller/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right')));
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
			$id = intval($this->getRequest ()->getParam ( 'template_id' ));
			
			// Get the values posted
			$params = $request->getPost ();
			
			// Save all the data 
			$id = EmailsTemplates::saveAll ( $id, $params, $this->session->langid );
			
			$redirector->gotoUrl ( "/admin/$controller/edit/id/$id" );
		} else {
			$this->view->form = $form;
			$this->view->title = "E-Mail Template";
			$this->view->description = "Here you can edit the e-mail template";
			return $this->render ( 'applicantform' );
		}
	}
		
}