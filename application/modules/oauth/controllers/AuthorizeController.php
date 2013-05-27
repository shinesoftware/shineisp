<?php

/**
 * @version 1.0
 */

class Oauth_AuthorizeController extends Zend_Controller_Action {
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
		);


		// create your storage again
		$storage = new OAuth2_Storage_Doctrine();
		
		// create your server again
		$server = new OAuth2_Server($storage,array('enforce_state' => true));
		
		$memory = new OAuth2_Storage_Memory(array(
		  'default_scope'    => $defaultScope,
		  'supported_scopes' => $supportedScopes
		));
		$scopeUtil = new OAuth2_Scope($memory);

		$server->setScopeUtil($scopeUtil);		
		
		// Add the "Authorization Code" grant type (this is required for authorization flows)
		$server->addGrantType(new OAuth2_GrantType_AuthorizationCode($storage));
		
		$request = OAuth2_Request::createFromGlobals();
		$response = new OAuth2_Response();
		
		// validate the authorize request
		if (!$server->validateAuthorizeRequest($request, $response)) {
		    $response->send();
		    die;
		}

		// display an authorization form
		if (empty($_POST)) {
		  exit('
		<form method="post">
		  <label>Do You Authorize TestClient?</label><br />
		  It needs the following privileges:<br/>
		  <xmp>'.print_r(explode(' ',$scopeUtil->getScopeFromRequest($request)),true).'</xmp>
		  <input type="submit" name="authorized" value="yes">
		  <input type="submit" name="authorized" value="no">
		</form>');
		}
		// print the authorization code if the user has authorized your client
		$is_authorized = ($_POST['authorized'] === 'yes');
		$server->handleAuthorizeRequest($request, $response, $is_authorized);
		if ($is_authorized) {
		  // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
		  $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5);
		  exit("SUCCESS! Authorization Code: $code");
		}
		$response->send();		
    }
}