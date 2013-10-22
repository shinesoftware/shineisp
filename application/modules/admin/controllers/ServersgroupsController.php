<?php
/**
 * Hostingplans
 * Manage the hostingplans table
 * @version 1.0
 */

class Admin_ServersgroupsController extends Shineisp_Controller_Admin {
	
	protected $serversgroups;
	protected $datagrid;
	protected $session;
	protected $translator;
	
	public function preDispatch() {	
		$this->session = new Zend_Session_Namespace ( 'Admin' );
		$this->serversgroups = new ServersGroups();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "serversgroups" )->setModel ( $this->serversgroups );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return 
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/serversgroups/list' );
	}

	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Servers Groups");
		$this->view->description = $this->translator->translate("Here you can see all groups of servers.");
		$this->view->buttons = array(array("url" => "/admin/serversgroups/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( ServersGroups::grid() )->datagrid ();
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( ServersGroups::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( ServersGroups::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/serversgroups/process" );
		$this->view->title = $this->translator->translate("Servers Groups");
		$this->view->description = $this->translator->translate("Here you can edit the server group details.");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
									 array("url" => "/admin/serversgroups/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
		
		$this->render ( 'applicantform' );
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_ServersGroupsForm ( array ('action' => $action, 'method' => 'post' ) );
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
				$this->view->title = $this->translator->translate ( 'Are you  sure to delete the selected group?' );
				$this->view->description = $this->translator->translate ( 'If you delete this group, it will no longer be available.' );
				
				$record = $this->serversgroups->getAllInfo ( $id );
				
				$this->view->recordselected = '';
                if ( isset($record) && isset($record ['name']) ) {
	                $this->view->recordselected = $record ['name'];
                }
				
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the customer
	 * @return unknown_type
	 */
	public function deleteAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		ServersGroups::DeleteGroup($id);
		$this->_helper->redirector ( 'list', 'serversgroups', 'admin', array ('mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$Session = new Zend_Session_Namespace ( 'Admin' );
		$form = $this->getForm ( '/admin/serversgroups/process' );
		
		$id = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/serversgroups/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/serversgroups/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
				
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = $this->serversgroups->getAllInfo ( $id, "group_id, name, fill_type, active", $Session->langid );
			
			if (! empty ( $rs )) {
				$this->view->id = $id;
				
				// Get all the servers for the group selected. 
				$rs['servers'] = ServersGroups::getServers($id);
				
				$form->populate ( $rs );
				$this->view->buttons[] = array("url" => "/admin/serversgroups/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right')));
				
			}
		}
		$this->view->title = $this->translator->translate("Server Group");
		$this->view->description = $this->translator->translate("Here you can edit the server group details.");
		
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
		$form = $this->getForm ( "/admin/serversgroups/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/serversgroups/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/serversgroups/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		try {
			
			// Check if we have a POST request
			if (! $request->isPost ()) {
				return $this->_helper->redirector ( 'list', 'serversgroups', 'admin' );
			}
			
			if ($form->isValid ( $request->getPost () )) {

				// Get the values posted
				$params = $form->getValues ();

				// Save the data
				$id = ServersGroups::saveAll($params ['name'], $params ['fill_type'], $params ['active'], $this->getRequest ()->getParam ( 'group_id' ));
				
				// Delete the old group
				ServersGroupsIndexes::deleteAllServers($id);
				
				if(!empty($params['servers'])){
					ServersGroupsIndexes::AddServers($id, $params['servers']);
				}
				
				$this->_helper->redirector ( 'edit', 'serversgroups', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
			
			} else {
				$this->view->form = $form;
				$this->view->title = $this->translator->translate("Servers Groups");
				$this->view->description = $this->translator->translate("Here you can edit the group detail");
				return $this->render ( 'applicantform' );
			}
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'edit', 'serversgroups', 'admin', array ('id' => $id, 'mex' => $e->getMessage (), 'status' => 'error' ) );
		}
	}
		
}