<?php
/**
 *
 * @version 
 */
/**
 * Paths helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Paths extends Zend_View_Helper_Abstract {
	
	/**
	 * 
	 * @param unknown_type $type
	 * @return string
	 */
	public function paths($type = "") {
		
		switch ($type) {
			
			case "skin":

				// Get the custom skin folder path
				$skin = Settings::findbyParam('skin');
				if(!empty($skin)){
					return "/skins/default/$skin/";
				}else {
					return "/skins/default/base/";
				}
				break;
				
			case "css":

				// Get the custom skin css folder path
				$skin = Settings::findbyParam('skin');
				if(!empty($skin)){
					return "/skins/default/$skin/css/";
				}else {
					return "/skins/default/base/css/";
				}
				break;
				
			case "images":

				// Get the custom skin images folder path
				$skin = Settings::findbyParam('skin');
				if(!empty($skin)){
					return "/skins/default/$skin/images/";
				}else {
					return "/skins/default/base/images/";
				}
				break;
				
			case "js":

				// Get the custom skin javascript folder path
				$skin = Settings::findbyParam('skin');
				if(!empty($skin)){
					return "/skins/default/$skin/js/";
				}else {
					return "/skins/default/base/js/";
				}
				break;
			
			default:
				// Get the custom skin folder path 
				$skin = Settings::findbyParam('skin');
				if(!empty($skin)){
					return "/skins/default/$skin/";
				}else {
					return "/skins/default/base/";
				}
				break;
		}
	}

}
