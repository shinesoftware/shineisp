<?php

/**
 * Admin_BulkmailController
 * Handle the bulk email manager software 
 * @version 1.0
 */

class Admin_BulkmailController extends Zend_Controller_Action {
	
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
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
	}
	
	/*
	 * Mailbulk form
	 * Send a mail to all the customers
	 */
	public function indexAction() {
		$auth = Zend_Auth::getInstance ();
		$registry = Zend_Registry::getInstance ();
		$translation = Zend_Registry::get ( 'Zend_Translate' );
		$retval = array ();
		
		if ($auth->hasIdentity ()) {
			$request = Zend_Controller_Front::getInstance ()->getRequest ();
			try {
				$form = new Admin_Form_BulkmailForm ( array ('action' => '/admin/bulkmail/#bulkmail', 'method' => 'post' ) );
				if ($request->isPost ()) {
					$isp = Isp::getActiveISP ();
					if ($form->isValid ( $request->getPost () )) {
						$data = $request->getPost ();
						$mail = new Bulkmails ();
						$mail->subject = $data ['subject'];
						$mail->body = $data ['body'];
						$mail->senddate = date ( 'Y-m-d H:i:s' );
						$mail->save ();
						$customers = Customers::getEmails ();
						foreach ( $customers as $customer ) {
							$body = str_replace ( '{fullname}', $customer ['fullname'], $data ['body'] );
							$result = Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], $customer ['email'], null, $data ['subject'], $body, true );
							if ($result !== true) {
								$retval [] = $result;
							}
						}
					}
				}
				$retval = count ( $retval ) > 0 ? $retval : null;
				$this->view->form = $form;
				$this->view->errors = $retval;
			} catch ( Exception $e ) {
				die ( $e->getMessage () );
			}
		}
	}

}