<?php
/**
 *
 * @version 
 */
/**
 * Footer helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Footer extends Zend_View_Helper_Abstract {
	
	public function footer() {
		return $this->view->render ( 'partials/footer.phtml' ); // Path of the template
	}

}
