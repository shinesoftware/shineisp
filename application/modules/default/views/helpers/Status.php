<?php
/**
 * Status bar 
 * @version 0.2
 */
/**
 * Status helper
 * Create a simple progress bar
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Status extends Zend_View_Helper_Abstract {

	public function Status($items) {
		$translation = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		$this->view->data = $items;
		
		// Path of the template
		return $this->view->render ( 'partials/status.phtml' );
	}
}
