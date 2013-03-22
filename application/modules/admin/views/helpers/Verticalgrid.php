<?php
/**
 *
 * @version 0.1
 */
/**
 * Verticalgrid helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Admin_View_Helper_Verticalgrid extends Zend_View_Helper_Abstract {
	public function verticalgrid($data) {
		if (isset($data['records'])) {
			$this->view->records = $data['records'];
			$this->view->editpage = !empty($data['editpage']) ? $data['editpage'] : null;
			return $this->view->render ( 'partials/verticalgrid.phtml' );
		}
	}
}
