<?php
/**
 * Profile helper
 */
class Admin_View_Helper_Querycount extends Zend_View_Helper_Abstract {
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	public function querycount() {
		return "Queries: ".Zend_Registry::get('querycount');
	}
}