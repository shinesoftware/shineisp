<?php

/**
 * Check if ShineISP has been already configured
 * 
 * @author shinesoftware
 *
 */
class Shineisp_Controller_Plugin_Starter extends Zend_Controller_Plugin_Abstract {
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		$module = $request->getModuleName ();
		
		if($module == "default"){   // set the right session namespace per module
			$module_session = 'Default';
		}elseif($module == "admin"){
			$module_session = 'Admin';
		}else{
			$module_session = 'Default';
		}
		
		$ns = new Zend_Session_Namespace ( );
		$ns->module = $module;
		
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