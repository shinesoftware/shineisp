<?php
/**
 * Domain Searchbox helper
 */
class Zend_View_Helper_Domainsearchbox extends Zend_View_Helper_Abstract {
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	public function domainsearchbox() {
		$ns = new Zend_Session_Namespace ();
		
		if (!empty($ns->customer)) {
			$this->view->visible = true;
		} else {
			$this->view->visible = false;
		}
		return $this->view->render ( 'partials/searchbox.phtml' );
	}
}