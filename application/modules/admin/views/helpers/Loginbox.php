<?php
/**
 * Profile helper
 */
class Admin_View_Helper_Loginbox extends Zend_View_Helper_Abstract {
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	public function loginbox($form="") {
		$auth = Zend_Auth::getInstance ();
		if ($auth->hasIdentity ()) {
		} else {
			if ($form) {
				$this->view->loginform = $form;
			} else {
				$this->view->loginform = new Default_Form_LoginForm ( array ('action' => '/admin/index/login', 'method' => 'post' ) );
			}
		}
		return $this->view->render ( 'partials/loginbox.phtml' );
	}
}