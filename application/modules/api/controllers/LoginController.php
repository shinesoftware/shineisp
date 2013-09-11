<?php

class Api_LoginController extends Shineisp_Api_Controller_Action {
	
	/**
	 * If the user is not allowed to see the API resource
	 * he/she will redirect to the noauth page
	 */
	public function noauthAction() {
		$registry = Shineisp_Registry::getInstance ();
		$translator = $registry->Zend_Translate;
		echo $translator->translate('Please check the permission and the resource in your API configuration');
	}
}
