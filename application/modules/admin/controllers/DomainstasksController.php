<?php

/**
 * DomainsTasks
 * Manage the domainstasks items table
 * @version 1.0
 */

class Admin_DomainstasksController extends Shineisp_Controller_Admin {
	
	protected $domainstasks;
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
		$this->domainstasks = new DomainsTasks();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "domainstasks" )->setModel ( $this->domainstasks );				
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/domainstasks/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Tasks for hosted domains");
		$this->view->description = $this->translator->translate("Here you can see all the tasks for hosted domains.");
		$this->view->buttons = array(array("url" => "/admin/domainstasks/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)));
		
		$this->datagrid->setConfig ( DomainsTasks::grid() )->datagrid ();
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( DomainsTasks::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( DomainsTasks::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/domainstasks/process" );
		$this->view->title = $this->translator->translate("New Domain task");
		$this->view->description = $this->translator->translate("Here you can create a new domain task.");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
								array("url" => "/admin/domainstasks/list", "label" => $this->translator->translate('List'), "params" => array('css' => null)));
		
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
			DomainsTasks::deleteItem( $id );
		}
		return $this->_helper->redirector ( 'index', 'domainstasks' );
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
				$this->view->description = $this->translator->translate ( 'If you delete the domain task information the data will no longer be restored' );
				
				$record = $this->domainstasks->getById ( $id, null, true );
				$this->view->recordselected = $record [0] ['domain'];
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ), 'status' => 'danger' ) );
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
		$form = $this->getForm ( '/admin/domainstasks/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		$this->view->title = $this->translator->translate("Domain task");
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/domainstasks/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/domainstasks/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = DomainsTasks::getById( $id, null, true );
			if (! empty ( $rs[0] )) {
				$rs[0]['startdate'] = Shineisp_Commons_Utilities::formatDateOut($rs[0]['startdate']);
				$rs[0]['enddate'] = Shineisp_Commons_Utilities::formatDateOut($rs[0]['enddate']);
				
				$domain = $rs[0]['Domains']['domain'] . "." . $rs[0]['Domains']['DomainsTlds']['WhoisServers']['tld'];
				$this->view->title = $this->translator->_("Domain task: %s", $domain);
				$this->view->titlelink = "/admin/domains/edit/id/" . $rs[0]['domain_id'];
				$form->populate ( $rs[0] );
				$this->view->buttons[] = array("url" => "/admin/domainstasks/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => null));
			}
		}
		
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		$this->view->description = $this->translator->translate("Here you can edit the domain task information.");
		
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
		$form = $this->getForm ( "/admin/domainstasks/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/domainstasks/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/domainstasks/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'domainstasks', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			
			$params = $form->getValues ();
			
			// Get the id 
			$id = DomainsTasks::saveData($params, $params ['task_id']);
			if (is_numeric ( $id )) {
				$this->_helper->redirector ( 'edit', 'domainstasks', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( "The task requested has been executed successfully." ), 'status' => 'success' ) );
			} else {
				$redirector->gotoUrl ( "/admin/domainstasks/list/" );
			}
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Domain task");
			$this->view->description = $this->translator->translate("Here you can edit the domain task information.");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return form
	 */
	private function getForm($action) {
		$form = new Admin_Form_DomainsTasksForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}

}
