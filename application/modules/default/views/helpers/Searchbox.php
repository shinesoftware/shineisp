<?php
/**
 * Searchbox helper
 */
class Zend_View_Helper_Searchbox extends Zend_View_Helper_Abstract {
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	public function searchbox() {
		return $this->view->render ( 'partials/searchbox.phtml' );
	}
}