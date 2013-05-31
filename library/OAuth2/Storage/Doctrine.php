<?php

/**
 * Simple Doctrine storage for all storage types
 *
 * NOTE: This class is meant to get users started
 * quickly. If your application requires further
 * customization, extend this class or create your own.
 *
 * NOTE: Passwords are stored in plaintext, which is never
 * a good idea.  Be sure to override this for your application
 *
 * @author GUEST.it s.r.l. <assistenza@guest.it>
 */
class OAuth2_Storage_Doctrine implements OAuth2_Storage_AuthorizationCodeInterface,
    OAuth2_Storage_AccessTokenInterface, OAuth2_Storage_ClientCredentialsInterface,
    OAuth2_Storage_UserCredentialsInterface, OAuth2_Storage_RefreshTokenInterface, OAuth2_Storage_JWTBearerInterface
{
    protected $db;
    protected $config;

    public function __construct($connection = null, $config = array())
    {
        $this->config = array_merge(array(
            'client_table'        => 'OauthClients',
            'access_token_table'  => 'OauthAccessTokens',
            'refresh_token_table' => 'OauthRefreshTokens',
            'code_table'          => 'OauthAuthorizationCodes',
            'user_table'          => 'OauthUsers',
            'jwt_table'           => 'OauthJwt',
        ), $config);
    }

    /* OAuth2_Storage_ClientCredentialsInterface */
    public function checkClientCredentials($client_id, $client_secret = null)
    {
    	$result = Doctrine_Query::create ()->select ( '*' )->from ( $this->config['client_table'] )->where ( "client_id = ?", $client_id)->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$result = array_shift($result);
        
        // make this extensible
        echo "RES: ".$result['client_secret'] == $client_secret."\n";
        return $result['client_secret'] == $client_secret;
    }

    public function getClientDetails($client_id)
    {
		$result = Doctrine_Query::create ()->select ( '*' )->from ( $this->config['client_table'] )->where ( "client_id = ?", $client_id)->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		return array_shift($result);
    }

    public function checkRestrictedGrantType($client_id, $grant_type)
    {
        $details = $this->getClientDetails($client_id);
        if (isset($details['grant_types'])) {
            return in_array($grant_type, (array) $details['grant_types']);
        }

        // if grant_types are not defined, then none are restricted
        return true;
    }

    /* OAuth2_Storage_AccessTokenInterface */
    public function getAccessToken($access_token)
    {
		$result = Doctrine_Query::create ()->select ( '*' )->from ( $this->config['access_token_table'] )->where ( "access_token = ?", $access_token)->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$result = array_shift($result);    
		
        if ($result) {
        	$token = $result;
            // convert date string back to timestamp
            $token['expires'] = isset($token['expires']) ? strtotime($token['expires']) : 0;
            
			return $token;
        }
    }

    public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);
		
		// garbage collector. Remove expired tokens
		Doctrine_Query::create()->delete($this->config['access_token_table'])->where ("expires < ?", date('Y-m-d H:i:s'))->execute();
		
        // if it exists, update it.
        if ( $this->getAccessToken($access_token) ) {
        	return Doctrine_Query::create()->update($this->config['access_token_table'])->set('client_id', $client_id)->set('expires', $expires)->set('user_id',$user_id)->set('scope',$scope)->where ("access_token = ?", $access_token)->execute(); 
        } else {
        	$DB = new $this->config['access_token_table'];
			$DB->access_token = $access_token;
			$DB->client_id    = $client_id;
			$DB->expires      = $expires;
			$DB->user_id      = $user_id;
			$DB->scope        = $scope;
			return $DB->save();
        }
    }

    /* OAuth2_Storage_AuthorizationCodeInterface */
    public function getAuthorizationCode($code)
    {
		$result = Doctrine_Query::create ()->select ( '*' )->from ( $this->config['code_table'] )->where ( "authorization_code = ?", $code)->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$result = array_shift($result);

        if ( $result ) {
        	$code = $result;
            // convert date string back to timestamp
            $code['expires'] = strtotime($code['expires']);
			
			return $code;
        }
    }

    public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);
        // if it exists, update it.
        if ($this->getAuthorizationCode($code)) {
        	return Doctrine_Query::create()->update($this->config['code_table'])->set('client_id', $client_id)->set('redirect_uri', $redirect_uri)->set('expires', $expires)->set('user_id',$user_id)->set('scope',$scope)->where ("authorization_code = ?", $code)->execute();
        } else {
        	$DB = new $this->config['code_table'];
			$DB->authorization_code = $code;
			$DB->client_id    = $client_id;
			$DB->expires      = $expires;
			$DB->user_id      = $user_id;
			$DB->scope        = $scope;
			$DB->redirect_uri = $redirect_uri;
			return $DB->save();
        }
    }

    public function expireAuthorizationCode($code)
    {
		return Doctrine_Query::create()->delete($this->config['code_table'])->where('authorization_code = ?', $code)->execute();
    }

    /* OAuth2_Storage_UserCredentialsInterface */
    public function checkUserCredentials($username, $password)
    {
        if ($user = $this->getUser($username)) {
            return $this->checkPassword($user, $password);
        }
        return false;
    }

    public function getUserDetails($username)
    {
        return $this->getUser($username);
    }

    /* OAuth2_Storage_RefreshTokenInterface */
    public function getRefreshToken($refresh_token)
    {
		$result = Doctrine_Query::create ()->select ( '*' )->from ( $this->config['refresh_token_table'] )->where ( "refresh_token = ?", $refresh_token)->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$result = array_shift($result);
		
        if ($token = $result) {
            // convert expires to epoch time
            $token['expires'] = strtotime($token['expires']);
        }

        return $token;
    }

    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

    	$DB = new $this->config['refresh_token_table'];
		$DB->client_id    = $client_id;
		$DB->expires      = $expires;
		$DB->user_id      = $user_id;
		$DB->scope        = $scope;
		$DB->refresh_token = $refresh_token;
		return $DB->save();
    }

    public function unsetRefreshToken($refresh_token)
    {
		return Doctrine_Query::create()->delete($this->config['refresh_token_table'])->where('refresh_token = ?', $refresh_token)->execute();
    }

    // plaintext passwords are bad!  Override this for your application
    protected function checkPassword($user, $password)
    {
        return $user['password'] == $password;
    }

    public function getUser($username)
    {
    	Shineisp_Commons_Utilities::log("OAuth2_Storage_Doctrine::getUser('".$username."')", 'oauth.log');
		$result = Doctrine_Query::create ()->select ( '*' )->from ( $this->config['user_table'] )->where ( "username = ?", $username)->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		return array_shift($result);
    }

    public function setUser($username, $password, $firstName = null, $lastName = null)
    {
    	//* GUEST - ALE - 20130521: set user bypassed
		return true;        
    }

    /* OAuth2_Storage_JWTBearerInterface */
    public function getClientKey($client_id, $subject)
    {
		$result = Doctrine_Query::create ()->select ( 'public_key' )->from ( $this->config['jwt_table'] )->where ( "client_id = ?", $client_id)->andWhere('subject = ?', $subject)->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$result = array_shift($result);
		return $result['public_key'];
    }
}
