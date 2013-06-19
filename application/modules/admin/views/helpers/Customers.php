<?php

/**
 * create the customers list
 * @author shine software
 * @version 1.0
 */

class Admin_View_Helper_Customers extends Zend_View_Helper_Abstract{
	
	public function customers() {
		$registry = Shineisp_Registry::getInstance ();
		$translator = $registry->Zend_Translate;
		$this->view->customers = Customers::getList($translator->translate('Select the customer ...'), array(array('where' => 'u.status_id = ?', 'params' => Statuses::id('active', 'customers'))));
		return $this->view->render ( 'partials/customers.phtml' );
	}
	
}
