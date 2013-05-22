<?php

/**
 * @version 1.0
 */

class Oauth_TokenController extends Zend_Controller_Action {
    public function init(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		
		//OAuth2_Autoloader::register();
    }
	
    public function indexAction() {
		// $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
		$storage = new OAuth2_Storage_Doctrine();

		// Pass a storage object or array of storage objects to the OAuth2 server class
		$server = new OAuth2_Server($storage);

		$server->addGrantType(new OAuth2_GrantType_ClientCredentials($storage));
		$server->addGrantType(new OAuth2_GrantType_AuthorizationCode($storage));
		
		// Handle a request for an OAuth2.0 Access Token and send the response to the client
		$server->handleTokenRequest(OAuth2_Request::createFromGlobals(), new OAuth2_Response())->send();
    }
}