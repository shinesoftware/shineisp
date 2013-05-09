<?php

class Shineisp_Controller_Plugin_Language extends Zend_Controller_Plugin_Abstract {
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
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
		$ns->unsetAll();
		
		$lang = $request->getParam ( 'lang', null );
		
		if (! empty ( $lang )) { 
			$ns->lang = $lang;
			Shineisp_Commons_Utilities::log("Language Plugin Controller: User selection: $lang");
		}else{
			if(!empty($ns->lang)){
				$lang = $ns->lang;
				Shineisp_Commons_Utilities::log("Language Plugin Controller: Session language set: $lang");
			}else{
				$lang = "en";
			}
			
			Shineisp_Commons_Utilities::log("Language Plugin Controller: No language selection get the default 'en'");
		}
		
		if(file_exists(PUBLIC_PATH . "/languages/$lang/$lang.mo")){
			
			$translate = new Zend_Translate(
					array(
							'adapter' => "Shineisp_Translate_Adapter_Gettext",
							'content' => PUBLIC_PATH . "/languages/$lang/$lang.mo",
							'locale'  => $lang,
							'disableNotices' => true
					));
			
			$translate->setLocale ( $lang );
			
			$locale = new Zend_Locale($lang);
			$regioncode = $locale->getLocaleToTerritory($lang);
			
			Shineisp_Commons_Utilities::log('Language Plugin Controller: Load the translation language from: ' . PUBLIC_PATH . "/languages/$lang/$lang.mo");
			Shineisp_Commons_Utilities::log("Language Plugin Controller: Region Code: $regioncode");
			
		}else{
			$locale = new Zend_Locale(Zend_Locale::BROWSER);
			$translate = Zend_Registry::get ( 'Zend_Translate' );
			
			Shineisp_Commons_Utilities::log("Language Plugin Controller: Get the browser language preferences");
			
			// check if ShineISP has been configured
			if($isReady){
				if ($locale instanceof Zend_Locale) {
					$lang = $locale->getLanguage();
				} else {
					$lang = $locale;
				}
			}else{
				$lang = $locale;
			}
			
			$regioncode = $locale->getLocaleToTerritory($lang);
			Shineisp_Commons_Utilities::log("Language Plugin Controller: Browser language selected: $regioncode");
			Shineisp_Commons_Utilities::log("Language Plugin Controller: Region Code: $regioncode");
			
		}
		
		// The browser sends a generic locale: "en"
		// because the "English" browser preferences contains many locale like en_US, en_GB, ...
		// If the regioncode is empty and the locale is a generic "en" we have to set a standard en_US
		if(empty($regioncode) || $locale == "en"){
			$locale = new Zend_Locale("en_US");
			$regioncode = "en_US";
		}
			
		$currency = new Zend_Currency($regioncode);
		
		$registry->set('Zend_Translate', $translate);
		$registry->set('Zend_Locale', $translate);
		$registry->set('Zend_Currency', $currency);
		
		$ns->lang = $lang;

		if($isReady){
			$ns->langid = Languages::get_language_id_by_code($lang);
		}else{
			$ns->langid = 1;
		}
	}
}