<?php
/**
 *
 * @version 0.1
 */
/**
 * Bbslist helper
 * Create a simple list in a table for all the posts created by the customers
 *
 * @uses viewHelper Zend_View_Helper
 */
class Admin_View_Helper_Bbslist extends Zend_View_Helper_Abstract {
	public function bbslist($data) {
		$this->view->module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$this->view->controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$this->view->action = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
		
		if (count ( $data ) > 0) {
			$data [0] ['message'] = $data [0] ['message'];

			// All the records 
			$this->view->records = $data;
			
			// Path of the template
			return $this->view->render ( 'partials/bbslist.phtml' );
		}
	}
}
