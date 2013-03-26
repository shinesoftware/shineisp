<?php

/**
 * IspController
 * Manage the isp profile
 * @version 1.0
 */

class Admin_ProfileController extends Zend_Controller_Action {
	
	protected $logged_user;
	protected $translator;
	
	protected $profile;
	protected $datagrid;
	protected $session;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$this->session = new Zend_Session_Namespace ( 'Admin' );
		$this->profile = new AdminUser ();
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "profile" )->setModel ( $this->profile );
		
		$registry = Zend_Registry::getInstance ();
		$auth = Zend_Auth::getInstance ();
		if ($auth->hasIdentity ()) {
			$this->logged_user= $auth->getIdentity ();
		} else {
			return $this->_helper->redirector ( 'out', 'login', 'admin' );
		}
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/profile/account' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		if(!AdminRoles::isAdministrator($this->logged_user['user_id'])){
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
			$redirector->gotoUrl ( '/admin/profile/account' );
		}
		
		$this->view->title = "Users list";
		$this->view->description = "Here you can see all the users.";
		$this->view->buttons = array(array("url" => "/admin/profile/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( AdminUser::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( AdminUser::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	
	/**
	 * editAction
	 * Get a record and populate the application form
	 * @return unknown_type
	 */
	public function editAction() {
		
		if(!AdminRoles::isAdministrator($this->logged_user['user_id'])){
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
			$redirector->gotoUrl ( '/admin/profile/account' );
		}
		
		$Session = new Zend_Session_Namespace ( 'Admin' );
		$form = $this->getForm ( '/admin/profile/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
	
		$this->view->title = "Edit User";
		$this->view->description = "Here you can edit the user.";
	
		if (! empty ( $id ) && is_numeric ( $id )) {
			$record = AdminUser::getAllInfo( $id );
			$record['permissions'] = AdminRoles::getResourcesbyUserID($id);
			if (! empty ( $record )) {
				$form->populate ( $record );
			}
		}
	
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/profile/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right'))),
				array("url" => "/admin/profile/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/profile/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
	
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the cmspages
	 * @return unknown_type
	 */
	public function deleteAction() {
		$id = intval($this->getRequest ()->getParam ( 'id' ));

		$identity   = Zend_Auth::getInstance ()->getIdentity ();
		$adminCount = count(AdminUser::getUserbyRoleID(1));
		
		if (is_numeric ( $id )) {
			/* Security checks
			 *  - administrators cannod be deleted by unprivileged users
			 *  - you can't delete the latest administrator
			 *  - you can't delete yourself
			 */
			 
			//* you can't delete yourself 
			if ( $id == $identity['user_id'] ) {
				$this->_helper->redirector ( 'list', 'profile', 'admin', array ('mex' => $this->translator->translate ( 'You can not delete yourself.' ), 'status' => 'error' ) );
				die();	
			}			 
			
			//* administrators cannod be deleted by unprivileged users
			if ( AdminRoles::isAdministrator($id) ) {
				if ( (int)$identity['role_id'] != 1 ) {
					$this->_helper->redirector ( 'list', 'profile', 'admin', array ('mex' => $this->translator->translate ( 'The administrator profile can be deleted only by administrator.'), 'status' => 'error' ) );
					die();	
				}
			}			 
			 
			//* you can't delete the latest administrator
			if(AdminRoles::isAdministrator($id) && $adminCount <= 1){
				$this->_helper->redirector ( 'list', 'profile', 'admin', array ('mex' => $this->translator->translate ( 'You can not delete the latest administrator' ), 'status' => 'error' ) );
				die();	
			}
			
			//* all good, delete
			AdminUser::deleteUser( $id );
		}
		return $this->_helper->redirector ( 'index', 'profile' );
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
				$this->view->title = $this->translator->translate ( 'Are you sure to delete this page?' );
				$this->view->description = $this->translator->translate ( 'The page will be no more longer available.' );
	
				$record = $this->profile->find ( $id );
				$this->view->recordselected = $record ['lastname'] . " " . $record ['firstname'];
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * searchProcessAction
	 * Search the record
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( AdminUser::grid() )->search ();
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
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_ProfileForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	 
	
	/**
	 * newAction
	 * Create the form module in order to create a record
	 * @return unknown_type
	 */
	public function newAction() {
		$Session = new Zend_Session_Namespace ( 'Admin' );
		$this->view->form = $this->getForm ( "/admin/profile/process" );
	
		// I have to add the language id into the hidden field in order to save the record with the language selected
		$this->view->form->populate ( array('language_id' => $Session->langid) );
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/profile/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')));
	
		$this->view->title = "Create a new user";
		$this->view->description = "Here you can create a new user.";
		$this->render ( 'applicantform' );
	}
	
	/**
	 * accountAction
	 * Manage the User account settings.
	 * @return void
	 */
	public function accountAction() {
		$form = $this->getForm ( '/admin/profile/process' );
		$this->view->form = $form;
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		$this->view->title = "User Account";
		$this->view->description = "Here you can edit your user account information";
		$adminbuttons = array();
		
		// Create the buttons in the edit form
		if(AdminRoles::isAdministrator($this->logged_user['user_id'])){
			$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')), array("url" => "/admin/profile/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')));
		}else{
			$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')));
		}
		
		$user = AdminUser::getAllInfo( $this->logged_user['user_id'] );
		$user['permissions'] = AdminRoles::getResourcesbyUserID($this->logged_user['user_id']);
		
		// the password is a MD5 string so we need to empty it, because the software could save the MD5 value again. 
		$user ['password'] = "";
		$form->populate ( $user );
		$this->render ( 'applicantform' );
	
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$request = $this->getRequest ();
		$user_id = $request->getParam('user_id');
		$adminbuttons = array();
		
		// Get our form and validate it
		$form = $this->getForm ( '/admin/profile/process' );
		
		// Create the buttons in the edit form
		if(AdminRoles::isAdministrator($this->logged_user['user_id'])){
			$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')), array("url" => "/admin/profile/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')));
		}else{
			$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')));
		}
		
		// Check if the email already exists only when a new record is created
		if(empty($user_id)){
			$form->getElement('email')->addValidator(new Shineisp_Validate_NoRecordExists('AdminUser','email'), true);
		}
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'index' );
		}
		
		if (! $form->isValid ( $request->getPost () )) {
			// Invalid entries
			$this->view->form = $form;
			$this->view->title = "User Account";
			$this->view->description = "Some information must be checked again before saving them.";
			return $this->render ( 'applicantform' ); // re-render the login form
		}
		
		// Save the data 
		AdminUser::saveAll($request->getPost (), $request->getParam('user_id'));
		
		// Redirection
		if(AdminRoles::isAdministrator($this->logged_user['user_id'])){	
			return $this->_helper->redirector ( 'list', 'profile', 'admin' );
		}else{
			return $this->_helper->redirector ( 'account', 'profile', 'admin' );
		}			
	
	}
}
    