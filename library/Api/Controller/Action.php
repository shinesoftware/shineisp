<?php
abstract class Api_Controller_Action extends Zend_Controller_Action {
    private $config; 
  
  
    public function soap( $classname ){
        // initialize server and set URI
        $optionsoap =  array(   'location' => $this->config['soap']['url'].'api/request/wsdl',
                                'uri'      => 'urn:'.$classname);
        
        $server = new Zend_Soap_Server(null, $optionsoap);
        // set SOAP service class
        $server->setClass ( $classname );
        // register exceptions for generating SOAP faults
        $server->registerFaultException ( array ('Api_Exceptions' ) );
        // handle request
        $server->handle ();
        exit();
    }
    
    public function wsdl( $classname ) {
        //You can add Zend_Auth code here if you do not want
        //everybody can access the WSDL file.
  
        // initilizing zend autodiscover object.
        $wsdl = new Zend_Soap_AutoDiscover ();
        // register SOAP service class
        $wsdl->setClass ( 'Api_Productscategories' );
        // set a SOAP action URI. here, SOAP action is 'soap' as defined above.
        $wsdl->setUri ( $this->config['soap']['url'].'api/request/soap' );
        // handle request
        $wsdl->handle ();        
    }
 
}
