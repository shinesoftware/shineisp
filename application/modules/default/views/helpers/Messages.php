<?php
class Zend_View_Helper_Messages extends Zend_View_Helper_Abstract {
	/*
	 * 
	 */
	public function Messages() {
		
		$mex = Zend_Controller_Front::getInstance ()->getRequest ()->getParam('mex');
		
		if(!empty($mex)){
			$this->view->message = $mex;
		}
			
		// Path of the template
		return $this->view->render ( 'partials/messages.phtml' );
	}
}