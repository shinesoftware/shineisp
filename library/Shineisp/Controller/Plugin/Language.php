<?php

class Shineisp_Controller_Plugin_Language extends Zend_Controller_Plugin_Abstract {
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		try{
			$locale = new Zend_Locale(Zend_Locale::BROWSER);
		}catch (Exception $e){
			$locale = new Zend_Locale("en_GB");
		}
		
		$registry = Zend_Registry::getInstance();
		
		// Check if the config file has been created
		$isReady = Shineisp_Main::isReady();
		
		$module = $request->getModuleName ();
		
		if($module == "default"){   // set the right session namespace per module
			$ns = new Zend_Session_Namespace ( 'Default' );
		}elseif($module == "admin"){
			$ns = new Zend_Session_Namespace ( 'Admin' );
		}else{
			$ns = new Zend_Session_Namespace ( 'Default' );
		}
		
		// check the user request if it is not set, please get the old prefereces
		$lang = $request->getParam ( 'lang', $ns->lang );
		
		if(empty($lang)){  							// Get the user preference
			if(strlen($locale) == 2){ 				// Check if the Browser locale is formed with 2 chars
				$lang = $locale;
			}elseif (strlen($locale) > 4){			// Check if the Browser locale is formed with > 4 chars
				$lang = substr($locale, 0, 2);		// Get the language code from the browser preference
			}
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
			$ns->langid = Languages::get_language_id_by_code($lang);
		}else{
			$ns->langid = 1;
		}
		
		Shineisp_Commons_Utilities::log("System: Locale set: " . $locale);
		Shineisp_Commons_Utilities::log("System: Language selected: " . $lang);
		
		$ns->lang = $lang;
	}
}