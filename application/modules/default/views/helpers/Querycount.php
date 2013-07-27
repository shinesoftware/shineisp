<?php
/**
 * Profile helper
 */
class Zend_View_Helper_Querycount extends Zend_View_Helper_Abstract {
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	public function querycount() {
		if (Settings::findbyParam('debug_queries')){
			return "Queries: ".Shineisp_Registry::get('querycount');
		}else{
			return null;
		}
	}
}