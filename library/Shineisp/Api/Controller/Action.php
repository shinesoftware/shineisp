<?php

abstract class Shineisp_Api_Controller_Action extends Shineisp_Controller_Common {
    
    public function init(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
  	/**
  	 * Accept the request of the clients
  	 * 
  	 * @param string $classname
  	 */
    public function soap( $classname ){
    	
    	if(empty($classname)){
    		return false;
    	}
    	
    	list($app, $module, $class) = explode("_", $classname);
    	
        // initialize server and set URI
        $optionsoap =  array(   'location' => "http://" . $_SERVER['HTTP_HOST'] . "/".strtolower($class).".wsld",
                                'uri'      => 'urn:'.$classname);
        
        $server = new Zend_Soap_Server(null, $optionsoap);
        
        // set SOAP service class
        $server->setClass ( $classname );

        // Bind already initialized object to Soap Server
        $server->setObject(new $classname());
        $server->setReturnResponse(false);
        
        // register exceptions for generating SOAP faults
        $server->registerFaultException ( array ('Shineisp_Api_Exceptions' ) );
        
        // handle request
        $server->handle ();
    }
    
    /**
     * Show the WSDL file of a specific class 
     * 
     * @param string $classname
     */
    public function wsdl( $classname ) {
        //You can add Zend_Auth code here if you do not want
        //everybody can access the WSDL file.
    	
    	if(empty($classname)){
    		return false;
    	}
    	 
    	list($app, $module, $class) = explode("_", $classname);
    	 
    	// initilizing zend autodiscover object.
        $wsdl = new Zend_Soap_AutoDiscover ();
        
        // register SOAP service class
        $wsdl->setClass ( $classname );
        
        // set a SOAP action URI. here, SOAP action is 'soap' as defined above.
        $wsdl->setUri ( "http://" . $_SERVER['HTTP_HOST'] . "/" . strtolower($class).".soap" );
        
        // handle request
        $wsdl->handle ();        
    }
}