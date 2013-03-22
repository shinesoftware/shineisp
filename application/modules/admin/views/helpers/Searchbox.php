<?php
/**
 * Profile helper
 */
class Admin_View_Helper_Searchbox extends Zend_View_Helper_Abstract {
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	public function searchbox() {
		$auth = Zend_Auth::getInstance ();
		if ($auth->hasIdentity ()) {
			$this->view->visible = true;
		} else {
			$this->view->visible = false;
		}
		return $this->view->render ( 'partials/searchbox.phtml' );
	}
}