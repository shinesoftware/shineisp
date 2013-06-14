<?php
class Shineisp_Controller_Common extends Zend_Controller_Action {
	/*
	 * Common for the whole admin controllers
	*/
	
	public function init() {
		// Get all settings
		Shineisp_Registry::set('Settings', Settings::getAll());
		
		// Statuses are used everywhere in system, so we need to make just one query
		Shineisp_Registry::set('Status', Statuses::getAll());
    }	
}