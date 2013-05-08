<?php

/**
 * ProductscategoriesController
 * Manage the product category table
 * @version 1.0
 */

class Api_ProductscategoriesController extends Api_Controller_Action {
	
	protected $productscategories;  
    
     
	
    public function preDispatch() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $registry = Zend_Registry::getInstance ();
        $this->translations = $registry->Zend_Translate;
        
        $application = new Zend_Application( APPLICATION_ENV,  APPLICATION_PATH . '/configs/application.ini' );
        $this->config = $application->bootstrap()->getOptions();        
    }
    
    public function soapAction(  ) {
        self::soap( 'Api_Productscategories' );
        exit();
    }
    
    public function wsdlAction(  ) {
        self::wsdl( 'Api_Productscategories' );
        exit();
    }
    
    /*
    public function getallAction(){
        $productsCategorie  =   ProductsCategories::getMenu();
        
        echo parent::success(200, $productsCategorie);
        exit();
    }
    
    public function getproductsAction(){
        $uri = $this->getRequest ()->getParam ( 'uri' );
        if( empty($uri) ) {
            echo $this->error(400,'002',":: 'uri' field");
            exit();
        }
        
        $infoCategory   = ProductsCategories::getAllInfobyURI($uri);
        if( empty($infoCategory) ) {
            echo $this->error(400,'003',":: uri=>'{$uri}' not category assigned");
            exit();
        }
        
        //get the first elemnt
        $infoCategory   = array_shift($infoCategory);
        $categoryid     = $infoCategory['category_id'];
        $products       = ProductsCategories::getProductListbyCatID($categoryid);
        
        echo parent::success(200,$products);
        exit();
    }
    
    public function getallinfoproductsAction(){
        $uri = $this->getRequest ()->getParam ( 'uri' );
        if( empty($uri) ) {
            echo $this->error(400,'002',":: 'uri' field");
            exit();
        }
        
        $infoCategory   = ProductsCategories::getAllInfobyURI($uri);
        if( empty($infoCategory) ) {
            echo $this->error(400,'003',":: uri=>'{$uri}' not category assigned");
            exit();
        }
        
        //get the first elemnt
        $infoCategory   = array_shift($infoCategory);
        $categoryid     = $infoCategory['category_id'];
        $products       = ProductsCategories::getProductListbyCatID($categoryid);
        $getProducts    = array();
        foreach( $products as $product ) {
            $productid  = $product['product_id'];
            $getProducts[]  = Products::getAllInfo($productid);
        }
        
        echo parent::success(200,$getProducts);
        exit();
    }*/
    
}