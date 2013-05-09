<?php

/**
 * Login Controller
 * 
 * @author Shine Software
 * @version 1.0
 */

class Admin_LoginController extends Zend_Controller_Action {
	
	private $_adapter = null;
	
	
    public function preDispatch() {
        $this->getHelper ( 'layout' )->setLayout ( 'blank' );
    }	
	
	/**
	 * Call the login page form
	 */
	public function indexAction() {
		$this->getHelper ( 'layout' )->setLayout ( 'blank' );
		$this->view->show_dashboard = false;
			
		// Call the login box helper
		$this->view->loginform = new Admin_Form_LoginForm ( array ('action' => '/admin/login/dologin', 'method' => 'post' ) );
			
	}
	
	/**
	 * NoAuth action
	 */
	public function noauthAction() {
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
	}
	
	/**
	 * Login action
	 */
	public function dologinAction() {
		$user = new AdminUser();
		$request = $this->getRequest ();
		$registry = Zend_Registry::getInstance ();
		$translation = $registry->Zend_Translate;

		// Get our form and validate it
		$form = new Admin_Form_LoginForm ( array ('action' => '/admin/login/dologin', 'method' => 'post' ) );
		
		// Invalid entries
		if ($form->isValid ( $request->getPost () )) {
			
			if ($this->getRequest()->isPost()) {
				
				$result = AdminUser::fastlogin($this->getRequest()->getParam("email"), $this->getRequest()->getParam("password"), $this->getRequest()->getParam("rememberme"));
				
				switch ($result->getCode()) {
				
					case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
						/** do stuff for nonexistent identity **/
						Shineisp_Commons_Utilities::log("Login: User has been not found.", "login.log");
						$this->view->message = $translation->translate ( 'User has been not found.' );
						break;
				
					case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
						/** do stuff for invalid credential **/
						Shineisp_Commons_Utilities::log("Login: The email address or password is incorrect. please try again.", "login.log");
						$this->view->message = $translation->translate ( 'The email address or password is incorrect. please try again.' );
						break;
				
					case Zend_Auth_Result::SUCCESS:
						/** do stuff for successful authentication **/
						Shineisp_Commons_Utilities::log("Login: The User has been authenticated successfully.", "login.log");
						AdminUser::updateLog($this->getRequest()->getParam("email"));
						$this->_helper->redirector ( 'index', 'index', 'admin' );
						break;
				
					case Zend_Auth_Result::FAILURE:
						/** do stuff for other failure **/
						Shineisp_Commons_Utilities::log("Login: There was a problem during the login.", "login.log");
						$this->view->message = $translation->translate ( 'There was a problem during the login.' );
						break;
				}
					
			} else {
				Shineisp_Commons_Utilities::log("Login: Post request is not valid", "login.log");
				$this->view->message = $translation->translate ( 'Post request is not valid' );			
			}
		}

		//Show the login form
		$this->view->loginform = $form;
		
		return $this->render ( 'index' ); // re-render the login form
	
	}
	
	/**
	 * Reset the admin password
	 */
	public function passwordAction(){
		$request = $this->getRequest ();
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		$form = new Admin_Form_PasswordForm ( array ('action' => '/admin/login/password', 'method' => 'post' ) );
		
		if ($request->isPost ()) {
			if ($form->isValid ( $request->getPost () )) {
				$user = AdminUser::checkMD5CredencialsByIspEmail(md5($request->getParam('email')));
	
				if(!empty($user)){
					
					$isp = Isp::getActiveISP ();
				
					// Get the template from the main email template folder
					$retval = Shineisp_Commons_Utilities::getEmailTemplate ( 'isp_password' );
						
					if(!empty($retval)){
						$subject = $retval ['subject'];
						$template =  $retval ['template'] ;
						
						$subject = str_replace ( "[lastname]", $user ['lastname'], $subject );
						$template = str_replace ( "[lastname]", $user ['lastname'], $template );
						$template = str_replace ( "[email]", $user ['email'], $template );
						$template = str_replace ( "[signature]", $isp ['company'], $template );
						$template = str_replace ( "[link]", "http://" . $_SERVER ['HTTP_HOST'] . "/admin/login/dopassword/id/" . md5($user ['email']), $template );
						
						Shineisp_Commons_Utilities::SendEmail ( $user ['email'], $user ['email'], null, $subject, $template);
						
						$this->view->message = $translator->translate ( 'An email has been sent. Please click at the link included in the body of the email.' );
					}
				}
			}
		}
		
		$this->view->passwordform = $form;
		
		return $this->render ( 'password' ); // re-render the login form
	}
	
	/**
	 * Execute the request of the admin user to change the password
	 */
	public function dopasswordAction() {
		$code = $this->getRequest ()->getParam('id');
		$user = AdminUser::checkMD5CredencialsByIspEmail($code);
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		$form = new Admin_Form_PasswordForm ( array ('action' => '/admin/login/password', 'method' => 'post' ) );
		
		if(!empty($user)){
			
			$isp = Isp::getActiveISP ();
			
			// Get the template from the main email template folder
			$retval = Shineisp_Commons_Utilities::getEmailTemplate ( 'isp_password_changed' );
			
			$newpassword = AdminUser::resetPassword($user['user_id']);
			
			if(!empty($retval)){
				$subject = $retval ['subject'];
				$template =  $retval ['template'] ;
					
				$subject = str_replace ( "[lastname]", $user ['lastname'], $subject );
				$template = str_replace ( "[lastname]", $user ['lastname'], $template );
				$template = str_replace ( "[admin_url]", "http://" . $_SERVER ['HTTP_HOST'] . "/admin/", $template );
				$template = str_replace ( "[email]", $user ['email'], $template );
				$template = str_replace ( "[password]", $newpassword, $template );
				$template = str_replace ( "[signature]", $isp ['company'], $template );
					
				Shineisp_Commons_Utilities::SendEmail ( $user ['email'], $user ['email'], null, $subject, $template);
				
				$this->view->message = $translator->translate ( 'An email has been sent with the new login credencials' );
			}	
		}
		
		$this->view->passwordform = $form;
		return $this->render ( 'password' ); // re-render the login form
	}
	
	/*
	 * Fast Link action
	* this function redirect the administrator directly to the admin panel
	* for instance: http://www.shineisp.it/admin/login/link/id/desaxo/keypass/md5email
	*/
	public function linkAction() {
		$request = $this->getRequest ();
		
		try {
			$code = $request->getParam ( 'id' );
			$keypass = $request->getParam ( 'keypass' );
			$link = Fastlinks::findbyCode ( $code );
				
			$auth = Zend_Auth::getInstance ();
				
			if (! empty ( $link [0] ['controller'] ) && ! empty ( $link [0] ['action'] )) {
				$adapter = new Shineisp_Auth_Adapter_Secretkey(Doctrine_Manager::connection()->getTable("AdminUser"), "email");
				$adapter->setIdentity($keypass);
				$adapter->setType('operator');
				
				$auth->setStorage(new Zend_Auth_Storage_Session('admin'));
				$auth->authenticate($adapter);
				
				// Check if the credencials are set in the Operator profile or the credencials are set in the ISP profile
				if ($auth->hasIdentity()) {
					Fastlinks::updateVisits ( $link [0] ['fastlink_id'] );
					Shineisp_Commons_Utilities::log("Login: The user has been logged in correctly from " . $_SERVER['REMOTE_ADDR'], "login.log");
					$this->_helper->redirector ( $link [0] ['action'], $link [0] ['controller'], 'admin', json_decode ( $link [0] ['params'], true ) );
				} else {
					
					// Check if the credencials are set in the Isp profile
					$adapter->setType('isp');
					
					$auth->setStorage(new Zend_Auth_Storage_Session('admin'));
					$auth->authenticate($adapter);
					if ($auth->hasIdentity()) {
						Fastlinks::updateVisits ( $link [0] ['fastlink_id'] );
						Shineisp_Commons_Utilities::log("Login: The user has been logged in correctly from " . $_SERVER['REMOTE_ADDR'], "login.log");
						$this->_helper->redirector ( $link [0] ['action'], $link [0] ['controller'], 'admin', json_decode ( $link [0] ['params'], true ) );
					}else{
						$auth->clearIdentity();
						header ( 'location: /admin' );
						die ();
					}
				}				
			} else {
				$auth->clearIdentity();
				header ( 'location: /admin' );
				die ();
			}
	
		} catch ( Exception $e ) {
			echo $e->getMessage ();
			die ();
		}
	}
	
	/**
	 * Login out action
	 */
	public function outAction() {
		$auth = Zend_Auth::getInstance ();
		$auth->setStorage ( new Zend_Auth_Storage_Session ( 'admin' ) );
		$auth->clearIdentity ();
		Shineisp_Commons_Utilities::log("Login: The user has been logged out correctly", "login.log");
		$this->_helper->redirector ( 'index', 'index', 'admin' ); // back to login page
	}
	
}