<?php
/**
 *
 * @version 
 */
/**
 * newsletter helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Newsletter extends Zend_View_Helper_Abstract {
	
	public function newsletter() {
		return $this->view->render ( 'partials/newsletter.phtml' ); // Path of the template
	}

}
