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
    
    public function getProductsAction(){
        $auth = Zend_Auth::getInstance ();
        
        $uri = $this->getRequest ()->getParam ( 'q' );
        echo '<pre>';
        print_r($uri);
        die();
        // $productsCategorie  =   ProductsCategories::
        
        echo parent::success($productsCategorie);
        exit();
    }
    
    
}