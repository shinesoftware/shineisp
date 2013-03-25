<?php

class IndexController extends Zend_Controller_Action {
	
	public function indexAction() {
		$ns = new Zend_Session_Namespace ( 'Default' );
		$locale = $ns->lang;
		
		Shineisp_Commons_Utilities::log("ShineISP starts now!");
		
		if (empty($ns->customer)) {
			$this->view->dashboard = true;
		} else {
			$this->view->dashboard = false;
		}
		
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		
		// Call the static Homepage
		$homepage = CMSPages::findbyvar ( "homepage", $locale );
		if (! empty ( $homepage ['body'] )) {

			// Set the custom layout of the page
			if (! empty ( $homepage ['pagelayout'] )) {
				$this->getHelper ( 'layout' )->setLayout ( $homepage ['pagelayout'] );
			}

			// Set the keywords of the page
			if (! empty ( $homepage ['keywords'] )) {
				$this->view->headMeta ()->setName ( 'keywords', $homepage ['keywords'] );
			}
			
			// Set the body of the page
			$this->view->content = $homepage ['body'];
			
		}
		
		$isp = Isp::getActiveISP();
		$this->view->headertitle = $isp['slogan'];
		
	}

	/**
	 * QrCode Invoice Order Management
	 */
	public function qrcodeAction() {
		$this->getHelper ( 'layout' )->setLayout ('mobile');
		$qrcode = $this->getRequest ()->getParam('q');
		if(!empty($qrcode)){
			$decoded = base64_decode($qrcode);
			if(!empty($decoded)){
				$data = json_decode($decoded, true);
				if(is_array($data)){
					if(!empty($data['customer'])){
						$this->view->customer = Customers::find($data['customer']);
						$this->view->order = Orders::getAllInfo($data['order'], "*", true);
					}
				}
			}
		}
	}

	/**
	 * Call me back custom module
	 * @see Shineisp_Custom_Callmeback
	 */
	public function callmebackAction() {
		$isp = Isp::getActiveISP ();
		$request = $this->getRequest ();
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		$form = new Default_Form_CallmebackForm( array ('action' => '/index/callmeback', 'method' => 'post' ) );
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'index' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the values posted
			$params = $form->getValues ();
			
			// Getting the email template
			$result = Shineisp_Commons_Utilities::getEmailTemplate ( 'callmeback' );
			if ($result) {
				$subject = str_replace ( "[fullname]", $params['fullname'], $result ['subject'] );
				$template = str_replace ( "[fullname]", $params['fullname'], $result ['template'] );
				$template = str_replace ( "[telephone]", $params['telephone'], $template);
				
				// Sending an email to the customer with the reset link.
				Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], $isp ['email'], null, $subject, $template );
				
				$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => $translator->translate ( 'Thanks for your interest in our services. Our staff will contact you shortly.' ), "status" => 'information') );
			}
			
		}
		
		$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => $translator->translate ( 'Check the telephone number and submit your data again.' ), "status" => 'error') );
	}		
	
	/**
	 * 
	 */
	public function fastloginAction() {
		$request = $this->getRequest ();
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		$registry = Zend_Registry::getInstance ();
		$translator = $registry->Zend_Translate;
						
		$passphrase = $request->getParam ( 'id' );
		if (! empty ( $passphrase )) {
			$credentials = explode ( "-", $passphrase );
			
			// Trying to get the user in the database
			$retval = Customers::getCustomerbyLogin ( $credentials [0], $credentials [1] );
			
			if (count ( $retval ) == 0) {
				$result = new Zend_Auth_Result ( Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $passphrase );
				$NS->customer = null;
				$this->view->message = $translator->translate ( 'Email or Password incorrect' );
				return $this->_helper->viewRenderer ( 'generic' ); 
			} else {
				$NS->customer = $retval [0];

				// Set the default control panel language
				if (! empty ( $retval [0] ['language'] )) {
					$lang = $retval [0] ['language'];
				}
			}
		}
		
		// If the software detects that there is a redirect to a specific page then ...
		if(!empty($NS->goto) && is_array($NS->goto)){
			$this->_helper->redirector ($NS->goto['action'], $NS->goto['controller'], $NS->goto['module'], $NS->goto['options']);
		}
		
		if (! empty ( $lang )) {
			$this->_helper->redirector ( 'index', 'dashboard', 'default', array ('lang' => $lang ) ); // back to login page
		} else {
			$this->_helper->redirector ( 'index', 'dashboard', 'default' );
		}
	}
	
	public function passwordAction() {
		$request = $this->getRequest ();
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		$isp = Isp::getActiveISP ();
		
		if ($request->isPost ()) {
			$email = $request->getParam ( 'account' );
			$customer = Customers::findbyemail ( $email, "email, password", true );
			if (count ( $customer ) > 0) {
				
				// Getting the email template
				$result = Shineisp_Commons_Utilities::getEmailTemplate ( 'password_reset_link' );
				
				if ($result) {
					$subject = $result ['subject'];
					$template = str_replace ( "[link]", "http://" . $_SERVER ['HTTP_HOST'] . "/index/resetpwd/id/" . md5 ( $customer [0] ['email'] ), $result ['template'] );
					
					// Sending an email to the customer with the reset link.
					Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], $customer [0] ['email'], null, $subject, $template );
				}
				
				$this->view->mextype = "information";
				$this->view->mex = $translator->translate ( 'Password sent to your email box. You have to click in the link written in the email.' );
			} else {
				$this->view->mextype = "error";
				$this->view->mex = $translator->translate ( 'User not found. Check your credentials.' );
			}
		}
		return $this->_helper->viewRenderer ( 'password' );
	}
	
	public function resetpwdAction() {
		$request = $this->getRequest ();
		$emailmd5 = $request->getParam ( 'id' );
		$registry = Zend_Registry::getInstance ();
		$translator = $registry->Zend_Translate;
		$isp = Isp::getActiveISP ();
		
		$customer = Customers::getCustomerbyEmailMd5 ( $emailmd5 );
		if ($customer) {
			$newPwd = Shineisp_Commons_Utilities::GenerateRandomString ( 8 );
			
			try {
				// Update the record
				Customers::setCustomerPassword ( $emailmd5, $newPwd );
			} catch ( Exception $e ) {
				echo $e->getMessage ();
				die ();
			}
			
			// Getting the email template
			$result = Shineisp_Commons_Utilities::getEmailTemplate ( 'password_new' );
			if ($result) {
				
				$subject = $result ['subject'];
				$template = str_replace ( "[password]", $newPwd, $result ['template'] );
				
				// Sending an email to the customer with the reset link.
				Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], $customer[0]['email'], null, $subject, $template );
			}
			
			$this->view->mextype = "information";
			$this->view->mex = $translator->translate ( 'Email sent' );
		} else {
			$this->view->mextype = "error";
			$this->view->mex = $translator->translate ( 'Error occurred during setting of the password.' );
		}
		
		return $this->_helper->viewRenderer ( 'password' );
	}
	
	/*
	 * linkAction
	 * Direct access by a link
	 */
	public function linkAction() {
		$request = $this->getRequest ();
		$NS = new Zend_Session_Namespace ( 'Default' );
		try {
			$code = $request->getParam ( 'id' );
			$link = Fastlinks::findbyCode ( $code );
			
			$auth = Zend_Auth::getInstance ();
			$auth->setStorage ( new Zend_Auth_Storage_Session ( 'default' ) );
			
			if (! empty ( $link [0] ['controller'] )) {
				$customer = Customers::find ( $link [0] ['customer_id'] );
				if (isset ( $customer ) && $customer['status_id'] == Statuses::id("active", "customers")) {
					
					$NS->customer = $customer;
					Fastlinks::updateVisits ( $link [0] ['fastlink_id'] );
					$this->_helper->redirector ( $link [0] ['action'], $link [0] ['controller'], 'default', json_decode ( $link [0] ['params'], true ) );
				} else {
					header ( 'location: /customer/login' );
					die ();
				}
			} else {
				header ( 'location: /customer/login' );
				die ();
			}
		
		} catch ( Exception $e ) {
			echo $e->getMessage ();
			die ();
		}
	}
	
	/*
	 * outAction
	 * Log out of the customer
	 */
	public function outAction() {
		$ns = new Zend_Session_Namespace ( 'Default' );
		$ns->unsetAll();
        unset($ns->customer);
		$this->_helper->redirector ( 'index', 'index', 'default' ); // back to login page
	}
}	