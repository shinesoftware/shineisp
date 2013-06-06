<?php
class Shineisp_Controller_Common extends Zend_Controller_Action {
	/*
	 * Common for the whole admin controllers
	*/
	
	public function init() {
		// Get all settings
		Zend_Registry::set('Settings', Settings::getAll());
    }	
}