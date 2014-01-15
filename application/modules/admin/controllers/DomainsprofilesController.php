<?php

/**
 * DomainsprofilesController
 * Handling the domain profiles data 
 * @version 1.0
 */

class Admin_DomainsprofilesController extends Shineisp_Controller_Admin {
	
	protected $domainsprofiles;
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
		$this->domainsprofiles = new DomainsProfiles ();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "domainsprofiles" )->setModel ( $this->domainsprofiles );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/domainsprofiles/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Domain Profiles");
		$this->view->description = $this->translator->translate("Here you can see all the domain profiles.");
		$this->view->buttons = array(array("url" => "/admin/domainsprofiles/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)));
		$this->datagrid->setConfig ( DomainsProfiles::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( DomainsProfiles::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( DomainsProfiles::grid() )->search ();
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
	 * Search the record for the Select2 JQuery Object by ajax
	 * @return json
	 */
	public function searchAction() {
	    
	    if($this->getRequest()->isXmlHttpRequest()){
	    
    	    $term = $this->getParam('term');
    	    $id = $this->getParam('id');
    	    
    	    if(!empty($term)){
    	        $term = "%$term%";
    	        $records = DomainsProfiles::findbyCustomfield("(firstname LIKE ?) OR (lastname LIKE ?) OR company LIKE ?", array($term,$term,$term));
    	        die(json_encode($records));
    	    }
    	    
    	    if(!empty($id)){
    	        $records = DomainsProfiles::get_by_customerid($id);
    	        die(json_encode($records));
    	    }
    	    
    	    $records = DomainsProfiles::getAll();
    		die(json_encode($records));
	    }else{
	        die();
	    }
	}
	
	/**
	 * newAction
	 * Create the form module in order to create a record
	 * @return unknown_type
	 */
	public function newAction() {
		$this->view->form = $this->getForm ( "/admin/domainsprofiles/process" );
		$this->view->title = $this->translator->translate("Domain Profiles");
		$this->view->description = $this->translator->translate("Here you can edit the domain profile details.");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
							   		 array("url" => "/admin/domainsprofiles/list", "label" => $this->translator->translate('List'), "params" => array('css' => null)));
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
				$this->view->title = $this->translator->translate ( 'WARNING: Are you sure you want to delete this profile?' );
				$this->view->description = $this->translator->translate ( 'If you delete this customer whole information will no longer be available anymore.' );
				
				$record = $this->domainsprofiles->getAllInfo ( $id );
				
				$this->view->recordselected = $record ['firstname'] . " " . $record ['lastname'] . " " . $record ['company'];
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
	
	    $form = $this->getForm ( '/admin/domainsprofiles/process' );
	
	    $id = $this->getRequest ()->getParam ( 'id' );
	
	    $this->view->title = $this->translator->translate("Customer edit");
	    $this->view->description = $this->translator->translate("Here you can edit the customer details.");
	
	    // Create the buttons in the edit form
	    $this->view->buttons = array(
	            array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
	            array("url" => "/admin/domainsprofiles/list", "label" => $this->translator->translate('List'), "params" => array('css' => null)),
	            array("url" => "/admin/domainsprofiles/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
	    );
	
	    if (! empty ( $id ) && is_numeric ( $id )) {
	        	
	        $rs = $this->domainsprofiles->getAllInfo ( $id );
	        	
	        if (! empty ( $rs )) {
	
	            $rs['birthdate'] = Shineisp_Commons_Utilities::formatDateOut($rs['birthdate']);
	
	            $this->view->id = $id;
	            $form->populate ( $rs );
	
	            if(!empty($rs['company'])){
	                $this->view->title = $rs['company'] . " - " . $rs['firstname'] . " " . $rs['lastname'];
	            }else{
	                $this->view->title = $rs['firstname'] . " " . $rs['lastname'];
	            }
	            
	            $this->view->buttons[] = array("url" => "/admin/domainsprofiles/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => null));
	        }
	    }
	
	    $this->view->mex = $this->getRequest ()->getParam ( 'mex' );
	    $this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
	
	    $this->view->editmode = true;
	
	    $this->view->form = $form;
	    $this->render ( 'applicantform' );
	}

	/**
	 * deleteAction
	 * Delete a record previously selected by the customer
	 * @return unknown_type
	 */
	public function deleteAction() {
	    $id = $this->getRequest ()->getParam ( 'id' );
	    try {
	        if (is_numeric ( $id )) {
	            DomainsProfiles::del($id);
	        }
	    } catch ( Exception $e ) {
	        $this->_helper->redirector ( 'edit', 'domainsprofiles', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'Unable to process the request at this time.' ) . ": " . $e->getMessage (), 'status' => 'danger' ) );
	    }
	
	    $this->_helper->redirector ( 'list', 'domainsprofiles', 'admin', array ('mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
	}
		
	/**
	 * Update the record previously selected
	 */
	public function processAction() {
	    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
	    $form = $this->getForm ( "/admin/domainsprofiles/process" );
	
	    $request = $this->getRequest ();
	
	    // Create the buttons in the edit form
	    $this->view->buttons = array(
	            array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
	            array("url" => "/admin/domainsprofiles/list", "label" => $this->translator->translate('List'), "params" => array('css' => null)),
	            array("url" => "/admin/domainsprofiles/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
	    );
	
	    try {
	        	
	        // Check if we have a POST request
	        if (! $request->isPost ()) {
	            return $this->_helper->redirector ( 'list', 'domainsprofiles', 'admin' );
	        }
	        	
	        if ($form->isValid ( $request->getPost () )) {
	            $params = $request->getPost();
	            $area   = intval($params['area']);
	            if( $area != 0 ) {
	                $province   = Provinces::find($area);
	                $area       = $province->code;
	                $params['area'] = $area;
	            }
	
	            $id = DomainsProfiles::saveAll($params, $request->getParam ( 'profile_id' ));
	
	            $this->_helper->redirector ( 'edit', 'domainsprofiles', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
	        } else {
	            $this->view->form = $form;
	            $this->view->title = $this->translator->translate("Domain Profile details");
	            $this->view->description = $this->translator->translate("Here you can edit the domains profile details.");
	            return $this->render ( 'applicantform' );
	        }
	    } catch ( Exception $e ) {
	        $this->_helper->redirector ( 'edit', 'domainsprofiles', 'admin', array ('id' => $id, 'mex' => $e->getMessage (), 'status' => 'danger' ) );
	    }
	}
	
	
	/**
	 * getForm
	 * Get the customized application form
	 * @return unknown_type
	 */
	private function getForm($action) {
	    $form = new Admin_Form_DomainsProfilesForm ( array ('action' => $action, 'method' => 'post' ) );
	
	    return $form;
	}
}