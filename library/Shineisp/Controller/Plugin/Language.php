<?php

class Shineisp_Controller_Plugin_Language extends Zend_Controller_Plugin_Abstract {
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		$force = false;
		
		$registry = Shineisp_Registry::getInstance();
		
		// Check if the config file has been created
		$isReady = Shineisp_Main::isReady();
		
		$module = $request->getModuleName ();
		
		if($module == "default"){   // set the right session namespace per module
			$module_session = 'Default';
		}elseif($module == "admin"){
			$module_session = 'Admin';
		}else{
			$module_session = 'Default';
		}
		
		$ns = new Zend_Session_Namespace ( $module_session );
		
		try{
			$locale = new Zend_Locale(Zend_Locale::BROWSER);
			if(!empty($ns->lang)){
				$locale = new Zend_Locale($ns->lang);
			}
				
			Shineisp_Commons_Utilities::log("System: Get the browser locale: " . $locale, "debug.log");
		}catch (Exception $e){
			Shineisp_Commons_Utilities::log("System: " . $e->getMessage(), "debug.log");
			if(!empty($ns->lang)){
				Shineisp_Commons_Utilities::log("System: Get the session var locale", "debug.log");
				$locale = new Zend_Locale($ns->lang);
			}else{
				$locale = new Zend_Locale("en");
				Shineisp_Commons_Utilities::log("System: There is not any available locale, set the default one: en_GB", "debug.log");
			}
		}
		
		// check the user request if it is not set, please get the old prefereces
		$lang = $request->getParam ( 'lang' );
		
		if(empty($lang)){  							// Get the user preference
			if(strlen($locale) == 2){ 				// Check if the Browser locale is formed with 2 chars
				$lang = $locale;
			}elseif (strlen($locale) > 4){			// Check if the Browser locale is formed with > 4 chars
				$lang = substr($locale, 0, 2);		// Get the language code from the browser preference
			}
		}else{
			$force = true;
		}
		
		// Get the translate language or the default language: en
		if(file_exists(PUBLIC_PATH . "/languages/$lang/$lang.mo")){
			$translate = new Zend_Translate(array('adapter' => "Shineisp_Translate_Adapter_Gettext", 'content' => PUBLIC_PATH . "/languages/$lang/$lang.mo", 'locale'  => $lang, 'disableNotices' => true));
		}else{
			$translate = new Zend_Translate(array('adapter' => "Shineisp_Translate_Adapter_Gettext", 'locale'  => $lang, 'disableNotices' => true));
		}
		
		$registry->set('Zend_Translate', $translate);
		$registry->set('Zend_Locale', $locale);
		
		if($isReady){
			if(empty($ns->langid) || $force){
				$ns->langid = Languages::get_language_id_by_code($lang);
			}
		}else{
			$ns->langid = 1;
		}
		
		$ns->lang = $lang;


    }
}