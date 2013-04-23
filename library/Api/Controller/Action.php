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
        
        $format = $this->getRequest ()->getParam ( 'format' );
        switch( $format ) {
            case 'xml':
                $this->format   = 'xml';
                break;
            default:
                $this->format   = 'json';
                break;
        }
        
        $auth = Zend_Auth::getInstance ();
        $auth->setStorage ( new Zend_Auth_Storage_Session ( 'api' ) );
        
        if ($auth->hasIdentity ()) {
            $this->getHelper ( 'layout' )->setLayout ( 'system' );
        } else {
            echo $this->error(5001,'Authentication failure');
            exit();
        }
    }
    
    protected function error( $codeerror, $message ) {
        $array  = array (
                         'result'   => 'error'
                        ,'code'     => $codeerror
                        ,'message'  => $message
                    );
           
        if( $this->format == 'xml' ) {
            $output = $this->_helper->xmlloader('createFromArray',array( $array ));
        } else {
            $output = json_encode($array);
        }
        
        return $output;
        
    } 
    
    protected function success( $response ){
        $array  = array (
                         'result'   => 'success'
                        ,'response' => $response
                    );
           
        if( $this->format == 'xml' ) {
            $output = $this->_helper->xmlloader('createFromArray',array( $array ));
        } else {
            $output = json_encode($array);
        }
        
        return $output;
    }      
    
}
