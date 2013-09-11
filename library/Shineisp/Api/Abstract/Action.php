<?php

abstract class Shineisp_Api_Abstract_Action {
    
    public function authenticate() {

    	$email      = $_SERVER['PHP_AUTH_USER'];
        $password   = $_SERVER['PHP_AUTH_PW'];
        
        if( $email == "" && $password == "" ) {
            list($email, $password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
        }
        
        //Check if username or password aren't empty
        if( $email == "" || $password == "" ) {
            throw new Shineisp_Api_Exceptions( 403001 );
            exit();
        }        
        
        // login the user by ACL
        $result = AdminUser::fastlogin($email, $password, 0);
        
        switch ($result->getCode()) {
            case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                throw new Shineisp_Api_Exceptions( 401001 );
                break;
            case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                /** do stuff for invalid credential **/
                throw new Shineisp_Api_Exceptions( 401002 );
                break;
            case Zend_Auth_Result::SUCCESS:
                return true; 
            case Zend_Auth_Result::FAILURE:
            default:
                /** do stuff for other failure **/
                throw new Shineisp_Api_Exceptions( 401001 );
                break;
        }
		
		die();
    }

}
