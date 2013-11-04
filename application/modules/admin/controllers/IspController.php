<?php

/**
 * IspController
 * Manage the isp profile
 * @version 1.0
 */

class Admin_IspController extends Shineisp_Controller_Admin {
	
	protected $isp;
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$registry = Shineisp_Registry::getInstance ();
		$auth = Zend_Auth::getInstance ();
		if ($auth->hasIdentity ()) {
			$this->isp = $auth->getIdentity ();
		} else {
			return $this->_helper->redirector ( 'out', 'login', 'admin' );
		}
		$this->translator = $registry->Zend_Translate;
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/isp/account' );
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_IspForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
	/**
	 * accountAction
	 * Manage the Isp account settings.
	 * @return void
	 */
	public function accountAction() {
		$form = $this->getForm ( '/admin/isp/process' );
		$this->view->form = $form;
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		$this->view->title = $this->translator->translate("ISP Account");
		$this->view->description = $this->translator->translate("Here you can edit your ISP account information");
		$this->view->serversgrid = $this->serversGrid ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
		);
		
		$isp = ISP::find ( $this->isp ['isp_id'] )->toArray ();
		// the password is a MD5 string so we need to empty it, because the software could save the MD5 value again. 
		$isp ['password'] = "";
		$form->populate ( $isp );
		$this->view->srclogo = "/documents/isp/" . $isp['logo'];
		$this->view->srclogoemail = "/documents/isp/" . $isp['logo_email'];
		$this->render ( 'applicantform' );
	
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/isp/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/isp/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'index' );
		}
		
		// Get our form and validate it
		$form = $this->getForm ( '/admin/isp/process' );
		
		if (! $form->isValid ( $request->getPost () )) {
			// Invalid entries
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("ISP Account");
			$this->view->description = $this->translator->translate("Some information must be checked again before saving them.");
			return $this->render ( 'applicantform' ); // re-render the login form
		}
		
		// Save the data 
		Isp::saveAll($request->getPost (), $this->isp ['isp_id']);
			
		return $this->_helper->redirector ( 'account', 'isp', 'admin' );
	
	}
	
	/**
	 * serversGrid
	 * Get the servers information.
	 * @return array
	 */
	private function serversGrid() {
		$auth = Zend_Auth::getInstance ();
		if ($auth->hasIdentity ()) {
			$data = $auth->getIdentity ();
			$isp = Doctrine::getTable ( 'Isp' )->findBy ( 'email', $data ['email'] )->toArray ();
			if (isset ( $isp [0] )) {
				$servers = new Servers ( );
				$records = $servers->findAllbyIsp ( $isp [0] ['isp_id'], 'server_id, name, ip, netmask', true );
				if (isset ( $records [0] )) {
					return array ('records' => $records, 'editpage' => 'servers' );
				}
			}
		}
	}
}
    