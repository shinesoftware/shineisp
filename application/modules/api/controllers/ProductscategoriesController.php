<?php

/**
 * ProductscategoriesController
 * Manage the product category table
 * @version 1.0
 */

class Api_ProductscategoriesController extends Api_Controller_Action {
	
	protected $productscategories;    
	
    public function preDispatch() {
        $registry = Zend_Registry::getInstance ();
        $this->translations = $registry->Zend_Translate;
    }  
    
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
        $infoCategory   = array();
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
    
    
}