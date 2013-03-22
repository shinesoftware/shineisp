<?php
/**
 *
 * @version 
 */
/**
 * datagrid Buttons helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Admin_View_Helper_Headers extends Zend_View_Helper_Abstract {
	
	public function headers() {
		return $this;
	}
	
	public function create() {
		try {
			return $this->view->render ( 'partials/headers.phtml' );
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
	
	}

}
