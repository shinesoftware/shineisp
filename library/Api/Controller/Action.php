<?php
abstract class Api_Controller_Action extends Zend_Controller_Action {
    protected $format;
    
    private $httpcodes = array(
         200 => 'Successful'
        ,201 => 'Created'
        ,202 => 'Accepted'
        ,203 => 'Non-authoritative information'
        ,204 => 'No content'
        ,205 => 'Reset content'
        ,206 => 'Partial content'
        
        ,300 => 'Multiple choices'
        ,301 => 'Moved permanently'
        ,302 => 'Moved temporarily'
        ,303 => 'See other location'
        ,304 => 'Not modified'
        ,305 => 'Use proxy'
        ,307 => 'Temporary redirect'
        
        ,400 => 'Bad request'
        ,401 => 'Not authorized'  
        ,403 => 'Forbidden'      
        ,404 => 'Not found'
        ,405 => 'Method not allowed'
        ,406 => 'Not acceptable'
        ,407 => 'Proxy authentication required'
        ,408 => 'Request timeout'
        ,409 => 'Conflict'
        ,410 => 'Gone'
        ,411 => 'Length required'
        ,412 => 'Precondition failed'
        ,413 => 'Request entity too large'
        ,414 => 'Requested URI is too long'
        ,415 => 'Unsupported media type'
        ,416 => 'Requested range not satisfiable'
        ,417 => 'Expectation failed'
        
        ,500 => 'Internal server error'
        ,501 => 'Not implemented'
        ,502 => 'Bad gateway'
        ,503 => 'Service unavailable'
        ,504 => 'Gateway timeout'
        ,505 => 'HTTP version not supported'
    );    
    
     /**
     * preDispatch
     * Starting of the module
     * (non-PHPdoc)
     * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
     */
    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $format = $this->getRequest ()->getParam ( 'format' );
        switch( $format ) {
            case 'xml':
                $this->format   = 'xml';
                break;
            default:
                $this->format   = 'json';
                break;
        }
        
        //Get username and password
        $email   = $_SERVER['PHP_AUTH_USER'];
        $password   = $_SERVER['PHP_AUTH_PW'];
        if( $email == "" && $password == "" ) {
            list($email, $password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
        }
        
        //Check if username or password aren't empty
        if( $email == "" || $password == "" ) {
            echo $this->error(403, 1000, 'Username or password empty');
            exit();
        }
        
        $result = AdminUser::fastlogin($email, $password, 0);
        switch ($result->getCode()) {
                
            case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                echo $this->error(401, 1001, 'User has been not found');
                exit();
            case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                /** do stuff for invalid credential **/
                echo $this->error(401, 1001, 'The email address or password is incorrect.');
                exit();
            case Zend_Auth_Result::SUCCESS:
                //LOGIN OK 
                break;
            case Zend_Auth_Result::FAILURE:
            default:
                /** do stuff for other failure **/
                echo $this->error(400, 1002, 'There was a problem during the login.');
                exit();
        }

        
        
        
    }
    
    protected function error( $codehttp, $code, $message ) {
        $array  = array (
                         'result'   => 'error'
                        ,'code'     => $codehttp.$code
                        ,'message'  => $message
                    );
           
        if( $this->format == 'xml' ) {
            $output = $this->_helper->xmlloader('createFromArray',array( $array ));
        } else {
            $output = json_encode($array);
        }
        
        header('HTTP/1.1 '.$codehttp.' '.$this->httpcodes[$codehttp]);
        return $output;
        
    } 
    
    protected function success( $codehttp, $response ){
        $array  = array (
                         'result'   => 'success'
                        ,'response' => $response
                    );
           
        if( $this->format == 'xml' ) {
            $output = $this->_helper->xmlloader('createFromArray',array( $array ));
        } else {
            $output = json_encode($array);
        }
        
        header('HTTP/1.1 '.$codehttp.' '.$this->httpcodes[$codehttp]);
        return $output;
    }      
    
}
