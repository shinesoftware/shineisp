<?php
/**
 *
 * @version 0.1
 */
/**
 * Reviews helper
 * Create a simple list in a table for all the reviews created by the customers
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Reviews extends Zend_View_Helper_Abstract {
	public function reviews($data) {
		$this->view->module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$this->view->controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$this->view->action = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
		
		if (count ( $data ) > 0) {
			// All the records 
			$this->view->records = $data;
		}
		return $this->view->render ( 'partials/reviews.phtml' );
	}
}
