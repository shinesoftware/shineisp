<?php

/**
 * Handle the SOAP API requests 
 * @version 1.0
 */

class Api_RequestController extends Shineisp_Api_Controller_Action {

    /**
     * Dummy action
     * @throws Exception
     */
    public function indexAction() {
    	throw new Exception("Please call http://" . $_SERVER['HTTP_HOST'] . "/api/request/soap/class/classname or http://" . $_SERVER['HTTP_HOST'] . "/api/request/wsdl/class/classname link with your soap client");	
    }
    
    /**
     * Get the SOAP endpoint
     * @throws Shineisp_Api_Exceptions
     */
    public function soapAction() {
    	$class = $this->getRequest()->getParam('class');
    	if(!empty($class)){
    		$class = "Shineisp_Api_" . ucfirst($class);
    		if(class_exists($class)){
    			self::soap( $class );
    		}else{
    			throw new Shineisp_Api_Exceptions(400008);
    		}
    	}else{
    		throw new Shineisp_Api_Exceptions(400009);
    	}
        exit();
    }
    
    /**
     * Get the WSDL description
     * @throws Shineisp_Api_Exceptions
     */
    public function wsdlAction() {
    	$class = $this->getRequest()->getParam('class');
    	if(!empty($class)){
    		$class = "Shineisp_Api_" . ucfirst($class);
    		if(class_exists($class)){
    			self::wsdl( $class );
    		}else{
    			throw new Shineisp_Api_Exceptions(400008);
    		}
    	}else{
    		throw new Shineisp_Api_Exceptions(400009);
    	}
        exit();
    }
    
}