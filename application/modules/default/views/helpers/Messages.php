<?php
class Zend_View_Helper_Messages extends Zend_View_Helper_Abstract {
	/*
	 * 
	 */
	public function Messages() {
		
		$mex = Zend_Controller_Front::getInstance ()->getRequest ()->getParam('mex');
		$status = Zend_Controller_Front::getInstance ()->getRequest ()->getParam('status', 'info');
		
		if(!empty($mex)){
			$this->view->message = $mex;
			$this->view->status = $status;
		}
			
		// Path of the template
		return $this->view->render ( 'partials/messages.phtml' );
	}
}