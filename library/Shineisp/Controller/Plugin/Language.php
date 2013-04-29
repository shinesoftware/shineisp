<?php

class Shineisp_Controller_Plugin_Language extends Zend_Controller_Plugin_Abstract {
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
	
		$module = $request->getModuleName ();
		
		if($module == "default"){   // set the right session namespace per module
			$ns = new Zend_Session_Namespace ( 'Default' );
		}elseif($module == "admin"){
			$ns = new Zend_Session_Namespace ( 'Admin' );
		}else{
			$ns = new Zend_Session_Namespace ( 'Default' );
		}
		
		$lang = $request->getParam ( 'lang', null );

		if (! empty ( $lang )) {
			$ns->lang = $lang;
		}else{
			if(!empty($ns->lang)){
				$lang = $ns->lang;
			}
		}
		$translate = Zend_Registry::get ( 'Zend_Translate' );
		
		if ($translate->isAvailable ( $lang )) {
			$translate->setLocale ( $lang );
		} else {
		 	// Otherwise get default language
            $locale = $translate->getLocale();
            if ($locale instanceof Zend_Locale) {
                $lang = $locale->getLanguage();
            } else {
                $lang = $locale;
            }
		}
		
		// Set language to global param so that our language route can fetch it nicely.
		$front = Zend_Controller_Front::getInstance ();
		$router = $front->getRouter ();
		$router->setGlobalParam ( 'lang', $lang );
		
	}
}