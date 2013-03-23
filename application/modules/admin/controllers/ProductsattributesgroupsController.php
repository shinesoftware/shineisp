<?php
/**
 * Hostingplans
 * Manage the hostingplans table
 * @version 1.0
 */

class Admin_ProductsattributesgroupsController extends Zend_Controller_Action {
	
	protected $productsattributesgroups;
	protected $datagrid;
	protected $session;
	protected $translator;
	
	public function preDispatch() {	
		$this->session = new Zend_Session_Namespace ( 'Admin' );
		$this->productsattributesgroups = new ProductsAttributesGroups();
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "productsattributesgroups" )->setModel ( $this->productsattributesgroups );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return 
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/productsattributesgroups/list' );
	}

	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = "Attributes Groups";
		$this->view->description = "Here you can see all the attributes groups.";
		$this->view->buttons = array(array("url" => "/admin/productsattributesgroups/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( ProductsAttributesGroups::grid() )->datagrid ();
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( ProductsAttributesGroups::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( ProductsAttributesGroups::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/productsattributesgroups/process" );
		$this->view->title = "Attribute Group";
		$this->view->description = "Here you can edit the attribute group details.";
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
									 array("url" => "/admin/productsattributesgroups/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
		
		$this->render ( 'applicantform' );
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_ProductsAttributesGroupsForm ( array ('action' => $action, 'method' => 'post' ) );
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
				$this->view->title = $this->translator->translate ( 'Are you  sure to delete the feature selected?' );
				$this->view->description = $this->translator->translate ( 'If you delete this feature, it will be no longer available.' );
				
				$record = $this->productsattributesgroups->getAllInfo ( $id );
				$this->view->recordselected = $record ['name'];
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
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
		ProductsAttributesGroups::DeleteGroup($id);
		$this->_helper->redirector ( 'list', 'productsattributesgroups', 'admin', array ('mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$Session = new Zend_Session_Namespace ( 'Admin' );
		$form = $this->getForm ( '/admin/productsattributesgroups/process' );
		$form->getElement ( 'save' )->setLabel ( 'Update' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/productsattributesgroups/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/productsattributesgroups/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
				
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = $this->productsattributesgroups->getAllInfo ( $id, "group_id, name, isrecurring, iscomparable", $Session->langid );
			
			if (! empty ( $rs )) {
				$this->view->id = $id;
				
				// Get all the attributes for the product selected. 
				$rs['attributes'] = ProductsAttributesGroups::getAttributes($id);
				
				$form->populate ( $rs );
				$this->view->buttons[] = array("url" => "/admin/productsattributesgroups/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right')));
				
			}
		}
		$this->view->title = "Attribute Group";
		$this->view->description = "Here you can edit the attribute group details.";
		
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
		$form = $this->getForm ( "/admin/productsattributesgroups/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/productsattributesgroups/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/productsattributesgroups/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		try {
			
			// Check if we have a POST request
			if (! $request->isPost ()) {
				return $this->_helper->redirector ( 'list', 'productsattributesgroups', 'admin' );
			}
			
			if ($form->isValid ( $request->getPost () )) {
				// Get the id 
				$id = $this->getRequest ()->getParam ( 'group_id' );
				
				// Set the new values
				if (is_numeric ( $id )) {
					$this->productsattributesgroups = $this->productsattributesgroups->find ( $id );
				}
				
				// Get the values posted
				$params = $form->getValues ();
				$this->productsattributesgroups->name = $params ['name'];
				$this->productsattributesgroups->code = Shineisp_Commons_UrlRewrites::format($params ['name']);
				$this->productsattributesgroups->isrecurring = $params ['isrecurring'] ? 1 : 0;
				$this->productsattributesgroups->iscomparable = $params ['iscomparable'] ? 1 : 0;
				$this->productsattributesgroups->save ();

				$id = is_numeric ( $id ) ? $id : $this->productsattributesgroups->getIncremented ();

				// Delete the old group
				ProductsAttributesGroupsIndexes::deleteAllAttributes($id);
				
				if(!empty($params['attributes'])){
					ProductsAttributesGroupsIndexes::AddAttributes($id, $params['attributes']);
				}
				
				$this->_helper->redirector ( 'edit', 'productsattributesgroups', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
			
			} else {
				$this->view->form = $form;
				$this->view->title = "Hosting Plan Feature details";
				$this->view->description = "Here you can fix the hosting plan feature details.";
				return $this->render ( 'applicantform' );
			}
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'edit', 'productsattributesgroups', 'admin', array ('id' => $id, 'mex' => $e->getMessage (), 'status' => 'error' ) );
		}
	}
}