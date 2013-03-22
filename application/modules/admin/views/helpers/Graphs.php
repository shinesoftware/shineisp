<?php
/**
 * Profile helper
 */
class Admin_View_Helper_Graphs extends Zend_View_Helper_Abstract {
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	public function graphs() {
		return $this;
	}
}