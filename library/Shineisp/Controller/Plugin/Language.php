<?php

class Shineisp_Controller_Plugin_Language extends Zend_Controller_Plugin_Abstract {
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request) {

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
		
		$lang = $request->getParam ( 'lang', null );

		if (! empty ( $lang )) {
			$ns->lang = $lang;
		}else{
			if(!empty($ns->lang)){
				$lang = $ns->lang;
			}
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
			
			$registry = Zend_Registry::getInstance();
			$registry->set('Zend_Translate', $translate);
			
		}else{
			
			
			if($isReady){
				$translate = Zend_Registry::get ( 'Zend_Translate' );
			
				// Otherwise get default language
				$locale = $translate->getLocale();
				if ($locale instanceof Zend_Locale) {
					$lang = $locale->getLanguage();
				} else {
					$lang = $locale;
				}
			}else{
				$lang = $locale;
			}
		}
		
		$ns->lang = $lang;
		
		if($isReady){
			$ns->langid = Languages::get_language_id_by_code($lang);
		}else{
			$ns->langid = 1;
		}
	}
}