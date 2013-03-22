<?php

/*
 * Navigation plugin
* preDispatch
* -------------------------------------------------------------
* Type:     method
* Name:     preDispatch
* Purpose:  Check the users credencials
* -------------------------------------------------------------
*/

class Shineisp_Controller_Plugin_CheckCredencials extends Zend_Controller_Plugin_Abstract {
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$module = $request->getModuleName ();
		$controller = $request->getControllerName ();
		$action = $request->getActionName ();
		
		$auth = Zend_Auth::getInstance ();
		$auth->setStorage ( new Zend_Auth_Storage_Session ( $module ) );
		
		if ($module == "system") { // System module doesn't need the authentication
			return true;
		}
		
		if ($module == "setup"){
			return true;
		}
		
		$allowed[] = "index";
		$allowed[] = "atom";
		$allowed[] = "themes";
		$allowed[] = "login";
		$allowed[] = "domainschk";
		$allowed[] = "search";
		$allowed[] = "compare";
		$allowed[] = "rss";
		$allowed[] = "common";
		$allowed[] = "contacts";
		$allowed[] = "cms";
		$allowed[] = "customer";
		$allowed[] = "link";
		$allowed[] = "password";
		$allowed[] = "error";
		$allowed[] = "tlds";
		$allowed[] = "products";
		$allowed[] = "categories";
		$allowed[] = "cart";
		$allowed[] = "wiki";
		$allowed[] = "newsletter";
		$allowed[] = "sitemap";
		$allowed[] = "notfound";
		
		if ($module == "default" && (in_array($controller, $allowed))) { // Login controller doesn't need the authentication
			return true;
		}
		
		if (! $auth->hasIdentity ()) {
			if ("index" != $request->getControllerName ()) {
				$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
				if($module == "default"){
					$url = "/$module/customer/login";
				}else{
					$url = "/$module/";
				}
				$redirector->gotoUrl ($url . "/redir/" . $module . "_" . $controller . "_" . $action );
			}
		}
		
	}
}