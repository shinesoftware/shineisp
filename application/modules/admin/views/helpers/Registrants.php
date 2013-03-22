<?php
/**
 *
 * @author mturillo
 * @version 
 */
/**
 * Registrars helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Admin_View_Helper_Registrars extends Zend_View_Helper_Abstract {
	
	public function registrars() {
		$auth = Zend_Auth::getInstance ();
		if ($auth->hasIdentity ()) {
			$registrars = Registrars::findActiveRegistrars();
			$this->view->data = $registrars;
			// Path of the template
			return $this->view->render ( 'partials/registrars.phtml' );
		}
	}

}
