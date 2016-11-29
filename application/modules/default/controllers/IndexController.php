<?php

class IndexController extends Shineisp_Controller_Default {
	
	public function indexAction() {
		$ns = new Zend_Session_Namespace ();
		$locale = $ns->lang;

		Shineisp_Commons_Utilities::log("ShineISP starts now from " . $_SERVER['SERVER_ADDR']);
		
		if (empty($ns->customer)) {
			$this->view->dashboard = true;
		} else {
			$this->view->dashboard = false;
		}
		
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		
		// Call the static Homepage
		$homepage = CmsPages::findbyvar ( "homepage", $locale );
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
				
		$this->view->headertitle = "S: ".Shineisp_Registry::get('ISP')->slogan;
		
	}

	/**
	 * Get a file attached into the documents
	 */
	public function getfilesAction() {
	    $translator = Shineisp_Registry::getInstance ()->Zend_Translate;
	    
	    $key = $this->getRequest ()->getParam('key');
	    Files::downloadbykey($key);
	    
	    if(!Files::downloadbykey($key)){
	        $this->_helper->redirector ( 'index', array ('mex' => $translator->translate ( 'File not found' ), 'status' => 'danger' ) );
	    }
	
	}

	/**
	 * QrCode Invoice Order Management
	 */
	public function qrcodeAction() {
		$qrcode = $this->getRequest ()->getParam('q');
		if(!empty($qrcode)){
			$decoded = base64_decode($qrcode);
			if(!empty($decoded)){
				$data = json_decode($decoded, true);
				if(is_array($data)){
					if(!empty($data['customer'])){
						$this->view->customer = Customers::find($data['customer']);
						$this->view->order    = Orders::getAllInfo($data['order'], null, true);
					}
				}
			}
		}
	}
	
	/**
	 * a fast way to signin in the user profile
	 */
	public function fastloginAction() {
		$request = $this->getRequest ();
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		$registry = Shineisp_Registry::getInstance ();
		$translator = $registry->Zend_Translate;
						
		$secretKey = $request->getParam ( 'id' );
		if (! empty ( $secretKey )) {
			$sha1 = Shineisp_Commons_Hasher::unhash_string($secretKey);
			
			// Trying to get the user in the database
			$retval = Customers::getCustomerbyEmailSha1 ( $sha1 );
			
			if (count ( $retval ) == 0) {
				$result = new Zend_Auth_Result ( Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $secretKey );
				$NS->customer = null;
				$this->view->message = $translator->translate ( 'Email Address or Password is incorrect.' );
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
		$request    = $this->getRequest ();
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		if ($request->isPost ()) {
			$email    = $request->getParam ( 'account' );
			$customer = Customers::findbyemail ( $email, "email, password, language_id", true );

			if ( isset($customer [0]) && is_numeric($customer[0]['customer_id']) ) {
				// generate key
				$resetKey = Customers::generateResetPasswordKey($customer[0]['customer_id']);
			}
			
			if ( count($customer) > 0 && !empty($resetKey) ) {
				Shineisp_Commons_Utilities::sendEmailTemplate($customer [0] ['email'], 'password_reset_link', array(
					 'link'       => "http://" . $_SERVER ['HTTP_HOST'] . "/index/resetpwd/id/" . $resetKey
					,':shineisp:' => $customer
				), null, null, null, null, $customer[0]['language_id']);		
				
				$this->view->mextype = "success";
				$this->view->mex = $translator->translate ( 'Your password has been sent. Please click on the link contained in the email.' );
			} else {
				$this->view->mextype = "alert";
				$this->view->mex = $translator->translate ( 'User not found. Please check your credentials.' );
			}
		}
		return $this->_helper->viewRenderer ( 'password' );
	}
	
	public function resetpwdAction() {
		$request    = $this->getRequest ();
		$resetKey   = $request->getParam ( 'id' );
		$registry   = Shineisp_Registry::getInstance ();
		$translator = $registry->Zend_Translate;
		$customer   = Customers::getCustomerByResetKey ( $resetKey );

		if ($customer) {
			$newPwd = Shineisp_Commons_Utilities::GenerateRandomPassword();
			
			try {
				// Update the record
				Customers::setCustomerPassword ( $customer[0]['customer_id'], $newPwd );
				
				// Force expire of reset link
				Customers::deleteResetPasswordKey($customer[0]['customer_id']);
				
			} catch ( Exception $e ) {
				echo $e->getMessage ();
				die ();
			}
			
            $customer[0]['password']    = $newPwd;
			// Getting the email template
			Shineisp_Commons_Utilities::sendEmailTemplate($customer[0]['email'], 'password_new', array(
				   'fullname'       => $customer [0] ['lastname']
				  ,'email'      => $customer[0]['email']
				 ,':shineisp:' => $customer
				 ,'password'   => $newPwd
			), null, null, null, null, $customer[0]['language_id']);		
			
			$this->view->mextype = "success";
			$this->view->mex = $translator->translate ( 'Email sent' );
		} else {
			$this->view->mextype = "alert";
			$this->view->mex = $translator->translate ( 'An error occurred while setting your password. Please try again.' );
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

				//TODO: GUEST - ALE - 20130516: remove access for disabled customers
				if (isset ( $customer ) && in_array($customer['status_id'], array(Statuses::id("active", "customers"),Statuses::id("disabled", "customers")) ) ) {
					
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
		$ns = new Zend_Session_Namespace ();
		$ns->unsetAll();
        unset($ns->customer);
		$this->_helper->redirector ( 'index', 'index', 'default' ); // back to login page
	}


    /**
     * license system
     */
    public function logoAction()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $parse = parse_url($_SERVER['HTTP_REFERER']);
            Shineisp_Commons_Utilities::log("LICENSE: " . $ip . " - " . $parse['host'], 'license.log');
        }

        ob_clean();
        header('Content-Type: image/gif');
        header('Expires: 0');
        echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
        die();
    }
}	
