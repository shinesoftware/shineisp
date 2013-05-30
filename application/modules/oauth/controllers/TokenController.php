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
		$defaultScope = ''; // empty. In this way we have a mandatory scope to request
		$supportedScopes = array(
		   'customers'
		  ,'products'
		  ,'orders'
		  ,'domains'
		  ,'invoices'
		  ,'payments'
		  ,'contacts'
		);
		    	
		// $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
		$storage = new OAuth2_Storage_Doctrine();

		// Pass a storage object or array of storage objects to the OAuth2 server class
		$server = new OAuth2_Server($storage, array('enforce_state' => true));

		// create server again
		$server = new OAuth2_Server($storage,array('enforce_state' => true));
		
		$memory = new OAuth2_Storage_Memory(array(
		  'default_scope'    => $defaultScope,
		  'supported_scopes' => $supportedScopes
		));
		$scopeUtil = new OAuth2_Scope($memory);

		$server->setScopeUtil($scopeUtil);


		//$server->addGrantType(new OAuth2_GrantType_ClientCredentials($storage));
		$server->addGrantType(new OAuth2_GrantType_AuthorizationCode($storage));
		$server->addGrantType(new OAuth2_GrantType_RefreshToken($storage));
		$server->addGrantType(new OAuth2_GrantType_JWTBearer($storage,'http://shineisp.xf.guest.it/oauth/token'));
		
		
		// Handle a request for an OAuth2.0 Access Token and send the response to the client
		$server->handleTokenRequest(OAuth2_Request::createFromGlobals(), new OAuth2_Response())->send();
    }
}