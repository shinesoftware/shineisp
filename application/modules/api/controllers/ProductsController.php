<?php

/**
 * ProductscategoriesController
 * Manage the product category table
 * @version 1.0
 */

class Api_ProductsController extends Shineisp_Api_Shineisp_Controller_Action {
	
	protected $productscategories;  
    
    public function preDispatch() {
        $registry = Shineisp_Registry::getInstance ();
        $this->translations = $registry->Zend_Translate;
        
        $application = new Zend_Application( APPLICATION_ENV,  APPLICATION_PATH . '/configs/application.ini' );
        $this->config = $application->bootstrap()->getOptions();        
    }
    
    public function soapAction(  ) {
        self::soap( 'Shineisp_Api_Shineisp_Products' );
        exit();
    }
    
    public function wsdlAction(  ) {
        self::wsdl( 'Shineisp_Api_Shineisp_Products' );
        exit();
    }
    
}