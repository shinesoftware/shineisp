<?php
/**
 *
 * @version 
 */
/**
 * LanguageSwitcher helper
 *
 * @uses viewHelper Zend_View_Helper
 */

class Admin_View_Helper_Languageswitcher extends Zend_View_Helper_Abstract{
    
    public function languageswitcher() {
    	$ns = new Zend_Session_Namespace ( 'Admin' );
		$t = new Zend_Controller_Request_Http();
		
		$url = $t->getRequestUri();
		$url = explode("?", $url);
		
		if(count($url) > 0){
			$uri = $url[0];
		}else{
			$uri = $url;
		}
		
		$this->view->languages = Languages::getActiveLanguageList();
		$this->view->uri = $uri;
		$this->view->langselected = $ns->lang;
		
		return $this->view->render ( 'partials/switcher.phtml' );
    }
    
}
