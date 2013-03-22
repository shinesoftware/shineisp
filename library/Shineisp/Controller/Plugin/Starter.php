<?php

/**
 * Check if ShineISP has been already configured
 * 
 * @author shinesoftware
 *
 */
class Shineisp_Controller_Plugin_Starter extends Zend_Controller_Plugin_Abstract {
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		if("setup" == $request->getModuleName()){
			return false;
		}

		// Check if the config file has been created
		$isReady = Shineisp_Main::isReady();
		if(!$isReady){
			header('location: /setup');
		}
	}
}