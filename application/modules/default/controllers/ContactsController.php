<?php

/*
 * ContactsController
 * handle the public contact form
 */
class ContactsController extends Shineisp_Controller_Default {
	protected $translations;
	
	public function preDispatch() {
		$registry = Shineisp_Registry::getInstance ();
        $this->translations = $registry->Zend_Translate;
	}
	
	public function indexAction() {
		$form = new Default_Form_ContactsForm ( array ('action' => '/contacts/process', 'method' => 'post' ) );
		
		$this->view->headertitle = $this->translations->translate('Contact us');
		
		$this->view->form = $form;
	}
	
	public function processAction() {
		$ns = new Zend_Session_Namespace ( "default" );
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
				$isp = Shineisp_Registry::get('ISP');
				
				Shineisp_Commons_Utilities::sendEmailTemplate($isp->email, 'contact', array(
						'fullname'      => $params['fullname'],
						'company'      => $params['company'],
						'email'      => $params['email'],
						'subject'      => $params['subject'],
						'message'      => $params['message']
				), null, null, null, null, $ns->langid);
				
				// Redirect the visitor to the contact page
				return $this->_helper->redirector ( 'index', 'contacts', 'default', array ('mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
			}
		}
		
		return $this->_helper->viewRenderer ( 'index' );
	}

}