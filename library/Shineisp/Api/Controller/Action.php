<?php
abstract class Shineisp_Api_Controller_Action extends Shineisp_Controller_Common {
    private $config; 
  
    public function init(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		
		/*
		 * TODO: enable the following when OAuth2 will be properly used for authenticating API requests
		// Get authenticated user
		$auth = Zend_Auth::getInstance()->getIdentity();

		// Store logged ISP. I'm inside admin, se we use only the logged user
		if ( isset($auth['isp_id']) ) {
			$isp_id = intval($auth['isp_id']);
			
			$ISP = new Isp();
			Shineisp_Registry::set('ISP', $ISP->find($isp_id));
		}
		 */		
		
    }
  
    public function soap( $classname ){
        // initialize server and set URI
        $optionsoap =  array(   'location' => "http://" . $_SERVER['HTTP_HOST'] . '/api/request/wsdl',
                                'uri'      => 'urn:'.$classname);
        
        $server = new Zend_Soap_Server(null, $optionsoap);
        // set SOAP service class
        $server->setClass ( $classname );
        // register exceptions for generating SOAP faults
        $server->registerFaultException ( array ('Shineisp_Api_Exceptions' ) );
        // handle request
        $server->handle ();
    }
    
    public function wsdl( $classname ) {
        //You can add Zend_Auth code here if you do not want
        //everybody can access the WSDL file.
  
        // initilizing zend autodiscover object.
        $wsdl = new Zend_Soap_AutoDiscover ();
        // register SOAP service class
        $wsdl->setClass ( $classname );
        // set a SOAP action URI. here, SOAP action is 'soap' as defined above.
        $wsdl->setUri ( "http://" . $_SERVER['HTTP_HOST'] . '/api/request/soap' );
        
        // handle request
        $wsdl->handle ();        
    }
 
}
