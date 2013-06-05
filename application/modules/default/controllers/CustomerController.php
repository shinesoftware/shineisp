<?php

class CustomerController extends Shineisp_Controller_Default {

	public function preDispatch() {
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
	}
	
	public function indexAction() {
		$redirector = Shineisp_Controller_Default_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/default/customer/login' );
	}
	
	public function loginAction() {
		$redir = "";
		$params = $this->getRequest()->getParams();
		
		// If exist a redirect request the client will be redirect after the login action
		if(!empty($params['redir'])){
			$this->view->form = new Default_Form_LoginForm ( array ('action' => '/customer/signin/redir/' . $params['redir'], 'method' => 'post' ) );
		}else{
			$this->view->form = new Default_Form_LoginForm ( array ('action' => '/customer/signin/', 'method' => 'post' ) );
		}
		
	}
	
	public function signinAction() {
		$request = $this->getRequest ();
		$NS = new Zend_Session_Namespace ( 'Default' );
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		$customerparams = array ();
		
		// Check the request of redirection of the user
		$redir = $request->getParam('redir');
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'login', 'customer' );
		}
		
		// Get our form and validate it
		$form = new Default_Form_LoginForm ( array ('action' => '/customer/signin/redir/'.$redir, 'method' => 'post' ) );
		
		if (! $form->isValid ( $request->getPost () )) {
			// Invalid entries
			$this->view->form = $form;
			return $this->_helper->viewRenderer ( 'login' ); // re-render the login form
		}
		
		// Get the values posted
		$params = $form->getValues ();
		
		$auth = Zend_Auth::getInstance ();
		$auth->setStorage ( new Zend_Auth_Storage_Session ( 'default' ) );

		// Get the customer 
		$retval = Customers::login ( $params ['email'], $params ['password'] );
		
		if (empty($retval)) {
			$result = new Zend_Auth_Result ( Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $params ['email'] );
			$NS->customer = null;
			$this->view->form = $form;
			$this->view->message = $translator->translate ( 'User not found. Check your credentials.' );
			return $this->_helper->viewRenderer ( 'login' ); // re-render the login form
		} else {
			$result = new Zend_Auth_Result ( Zend_Auth_Result::SUCCESS, null );
			$customer = Customers::getAllInfo($retval['customer_id'], "c.customer_id, a.address_id, cts.type_id, l.legalform_id, ct.country_id, cn.contact_id, s.status_id, c.*, a.*, l.*, cn.*, cts.*, s.*");
			$NS->customer = $customer;
			
			// Set the default control panel language
			if (! empty ( $retval ['language'] )) {
				$lang = $retval ['language'];
			}
		}
		
		// We're authenticated! Redirect to the home page
		$auth->getStorage ()->write ( $retval  );
		
		// If exist a redirect request the client will be redirect after the login action
		if(!empty($NS->goto) && is_array($NS->goto)){
			$this->_helper->redirector ($NS->goto['action'], $NS->goto['controller'], $NS->goto['module'], $NS->goto['options']);
		}
		
		if (! empty ( $lang )) {
			$this->_helper->redirector ( 'index', 'dashboard', 'default', array ('lang' => $lang ) ); // back to login page
		} else {
			$this->_helper->redirector ( 'index', 'dashboard', 'default' );
		}
	}
	
	public function signupAction() {
		$request = $this->getRequest ();
		$form = new Default_Form_SignupForm ( array ('action' => '/customer/dosignup', 'method' => 'post' ) );
		$this->view->form = $form;
	}
	
	/**
	 * Signup Action Controller
	 */
	public function dosignupAction() {
		$request = $this->getRequest ();
		$redirector = Shineisp_Controller_Default_HelperBroker::getStaticHelper ( 'redirector' );
		$form = new Default_Form_SignupForm ( array ('action' => '/customer/dosignup', 'method' => 'post' ) );
		$this->view->form = $form;
		$post = $request->getPost ();
		
		if (is_array ( $post )) {
			
			if (! $form->isValid ( $request->getPost () )) {
				// Invalid entries
				$this->view->form = $form;
				return $this->_helper->viewRenderer ( 'signup' ); // re-render the signup form
			}
			
			// Get the values posted
			$params = $form->getValues ();
			
			// Create the user
			Customers::Create($params);
			
			// Send the user to the auto login page
			//$redirector->gotoUrl ( '/default/index/fastlogin/id/' . md5 ( $params ['email'] ) . "-" . md5 ( $params ['password'] ) );
			$url = '/default/index/fastlogin/id/' . Shineisp_Commons_Hasher::hash_string($params ['email']);
			$redirector->gotoUrl ( $url );
		
		}
	}
}