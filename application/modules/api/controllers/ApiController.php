<?php

/**
 * ApiController
 * Manage the product category table
 * @version 1.0
 */

class Api_ApiController extends Zend_Controller_Action {
    
    private $config;
    
    public function preDispatch() {
        $registry = Zend_Registry::getInstance ();
        $this->translations = $registry->Zend_Translate;
    }
    
    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);        
        
        $application = new Zend_Application( APPLICATION_ENV,  APPLICATION_PATH . '/configs/application.ini' );
        $this->config = $application->bootstrap()->getOptions();
    }    
    
    /**
     * SOAP action named as soap.
     ******/
    public function soapAction() {
        // initialize server and set URI
        $server = new Zend_Soap_Server($this->config['soap']['url'].'api/api/wsdl');
        // set SOAP service class
        $server->setClass ( 'Api_Productscategories' );
        // register exceptions for generating SOAP faults
        $server->registerFaultException ( array ('Api_Exceptions' ) );
        // handle request
        $server->handle ();
    }
    
    /**
     * function to generate WSDL.
     */
    public function wsdlAction() {
        //You can add Zend_Auth code here if you do not want
        //everybody can access the WSDL file.
  
        // initilizing zend autodiscover object.
        $wsdl = new Zend_Soap_AutoDiscover ();
        // register SOAP service class
        $wsdl->setClass ( 'Api_Productscategories' );
        // set a SOAP action URI. here, SOAP action is 'soap' as defined above.
        $wsdl->setUri ( $this->config['soap']['url'].'api/api/soap' );
        // handle request
        $wsdl->handle ();
    }    
}