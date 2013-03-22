<?php

class Setup_IndexController extends Zend_Controller_Action {
	
	/**
	 * Load all the resources
	 * @see Zend_Controller_Action::preDispatch()
	 */
	public function preDispatch() {
		$this->_helper->redirector ( 'index', 'localization', 'setup');
	}
	
}