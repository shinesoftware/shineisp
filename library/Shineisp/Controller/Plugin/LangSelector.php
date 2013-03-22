<?php

/*
 * LangSelector plugin
* -------------------------------------------------------------
* Type:     class
* Name:     LangSelector
* Purpose:  Select the language to be used
* -------------------------------------------------------------
*/

class Shineisp_Controller_Plugin_LangSelector extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$module = $request->getModuleName ();
		
		$default_locale = "en_US"; // Get the shineisp default language
		
		if($module == "default"){   // set the right session namespace per module
			$ns = new Zend_Session_Namespace ( 'Default' );
		}elseif($module == "admin"){
			$ns = new Zend_Session_Namespace ( 'Admin' );
		}else{
			$ns = new Zend_Session_Namespace ( 'Default' );
		}
		
		// check if the configuration file has been already set
		if(Shineisp_Main::isReady()){
			$language = Languages::getDefault (); // Load the Shineisp custom preferences
			if(!empty($language)){
				$default_locale = $language ['locale']; // Get the custom default language set in the control panel
			}
		}

		// Set the default locale 
		if(empty($ns->lang)){
			$ns->lang = $default_locale;
		}
		
		// If the client try to change the language with ?lang=en_US then
		$lang = $request->getParam ( 'lang' );   
		if (! empty ( $lang )) {
			$ns->lang = $lang;
		}

		// set the default locale
		Languages::setDefaultLanguage ( PUBLIC_PATH, $ns->lang );
		
		// check if the configuration file has been already set
		if(Shineisp_Main::isReady()){
			$ns->langid = Languages::get_language_id ( $ns->lang );
		}else{
			$ns->langid = 1;
		}
	}
}