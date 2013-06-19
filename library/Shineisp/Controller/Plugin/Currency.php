<?php

class Shineisp_Controller_Plugin_Currency extends Zend_Controller_Plugin_Abstract {
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		$currency = new Zend_Currency('IT');

		$registry = Shineisp_Registry::getInstance();
		
		// Check if the config file has been created
		$isReady = Shineisp_Main::isReady();

		if($isReady){
			// TODO: To be completed
		}

		$registry->set('Zend_Currency', $currency);
	}
}