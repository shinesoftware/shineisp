<?php

/*
 * ContactsController
 * handle the public contact form
 */
class ContactsController extends Zend_Controller_Action {
	protected $translations;
	
	public function preDispatch() {
		$registry = Zend_Registry::getInstance ();
        $this->translations = $registry->Zend_Translate;
	}
	
	public function indexAction() {
		$form = new Default_Form_ContactsForm ( array ('action' => '/contacts/process', 'method' => 'post' ) );
		
		$this->view->headertitle = $this->translations->translate('Contact us');
		
		$this->view->form = $form;
	}
	
	public function processAction() {
		$request = $this->getRequest ();
		$form = new Default_Form_ContactsForm ( array ('action' => '/contacts/process', 'method' => 'post' ) );
		$this->view->form = $form;
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'index', 'contacts', 'default' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the values posted
			$params = $form->getValues ();
		
			$captcha = $params ['captcha'];
			// Actually it's an array, so both the ID and the submitted word
			// is in it with the corresponding keys
			// So here's the ID...
			$captchaId = $captcha ['id'];
			// And here's the user submitted word...
			$captchaInput = $captcha ['input'];
			// We are accessing the session with the corresponding namespace
			// Try overwriting this, hah!
			$captchaSession = new Zend_Session_Namespace ( 'Zend_Form_Captcha_' . $captchaId );
			// To access what's inside the session, we need the Iterator
			// So we get one...
			$captchaIterator = $captchaSession->getIterator ();
			// And here's the correct word which is on the image...
			$captchaWord = $captchaIterator ['word'];

			// Now just compare them...
			if ($captchaInput == $captchaWord) {
				$retval = Shineisp_Commons_Utilities::getEmailTemplate ( 'contact' );
				if ($retval) {
					$isp = Isp::getActiveISP();
					$subject = $retval ['subject'];
					$subject = str_replace ( "[subject]", $this->translations->translate("Message from the website"), $subject );
					$body = $retval ['template'];
					$body = str_replace ( "[fullname]", $params['fullname'], $body);
					$body = str_replace ( "[company]", $params['company'], $body);
					$body = str_replace ( "[email]", $params['email'], $body);
					$body = str_replace ( "[subject]", $params['subject'], $body);
					$body = str_replace ( "[message]", $params['message'], $body);

					// Send first message to the visitor
					Shineisp_Commons_Utilities::SendEmail ( $isp['email'], $params['email'], null, $subject, $body);
					
					// Send the message to the administrator
					Shineisp_Commons_Utilities::SendEmail ( $isp['email'], $isp['email'], null, $subject, $body, false, null, null, $params['email']);
					
					// Redirect the visitor to the contact page
					return $this->_helper->redirector ( 'index', 'contacts', 'default', array ('mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
				}
			}
		}
		
		return $this->_helper->viewRenderer ( 'index' );
	}

}