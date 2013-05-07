<?php
abstract class Api_Controller_Action extends Zend_Controller_Action {
    protected $format;
    
     /**
     * preDispatch
     * Starting of the module
     * (non-PHPdoc)
     * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
     */
    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        /*
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
            echo $this->error(403, '001');
            exit();
        }
        
        $result = AdminUser::fastlogin($email, $password, 0);
        switch ($result->getCode()) {
                
            case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                echo $this->error(401, '001');
                exit();
            case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                / ** do stuff for invalid credential ** /
                echo $this->error(401, '002');
                exit();
            case Zend_Auth_Result::SUCCESS:
                //LOGIN OK 
                break;
            case Zend_Auth_Result::FAILURE:
            default:
                / ** do stuff for other failure ** /
                echo $this->error(400, '001');
                exit();
        }
        */
        
        
        
    }
    
    protected function error( $codehttp, $code, $message = "" ) {
        $array  = array (
                         'result'   => 'error'
                        ,'code'     => $codehttp.$code
                        ,'message'  => $this->errorguest[$codehttp.$code]." ".$message
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
            $output = $this->_helper->xmlloader->xmlToArray ( $array );            
        } else {
            $output = json_encode($array);
        }
        
        header('HTTP/1.1 '.$codehttp.' '.$this->httpcodes[$codehttp]);
        return $output;
    }      
    
}
