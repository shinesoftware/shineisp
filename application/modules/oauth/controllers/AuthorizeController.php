<?php

/**
 * @version 1.0
 */

class Oauth_AuthorizeController extends Zend_Controller_Action {
	protected $session;
	
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

		// create storage
		$storage = new OAuth2_Storage_Doctrine();
		
		// create server again
		$server = new OAuth2_Server($storage,array('enforce_state' => true));
		
		$memory = new OAuth2_Storage_Memory(array(
		  'default_scope'    => $defaultScope,
		  'supported_scopes' => $supportedScopes
		));
		$scopeUtil = new OAuth2_Scope($memory);

		$server->setScopeUtil($scopeUtil);		
		
		// Add the "Authorization Code" grant type (this is required for authorization flows)
		$server->addGrantType(new OAuth2_GrantType_AuthorizationCode($storage));
		
		$request       = OAuth2_Request::createFromGlobals();
		$response      = new OAuth2_Response();
		$clientDetails = $storage->getClientDetails($request->query('client_id'));
		
		// validate the authorize request
		if (!$server->validateAuthorizeRequest($request, $response)) {
		    $response->send();
		    die;
		}

		// Check auth, if not logged, redirect to login page
		$auth = Zend_Auth::getInstance()->getIdentity();
		if ( !is_array($auth) || empty($auth) || !isset($auth['user_id']) || !intval($auth['user_id']) > 0 || !AdminRoles::isAdministrator($auth['user_id']) ) {
			$this->session = new Zend_Session_Namespace ( 'OAuth' );
			$this->session->appName         = $clientDetails['app_name'];
			$this->session->requestedScopes = $scopeUtil->getScopeFromRequest($request);
			$this->session->redirect        = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
			$this->_helper->redirector ( 'oauth', 'login', 'admin' );
			die();
		}
		
		// I'm here, so I'm logged as administrator
		$server->handleAuthorizeRequest($request, $response, true);
	    
	    // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
	    $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5);
	    //exit("SUCCESS! Authorization Code: $code");
		$response->send();		
		
    }
}