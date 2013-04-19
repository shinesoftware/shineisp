<?php

/**
 * ProductscategoriesController
 * Manage the product category table
 * @version 1.0
 */

class Api_ProductscategoriesController extends Api_Controller_Action {
	
	protected $productscategories;    
	
    public function init()  {
        
        parent::init();
        $this->productscategories = new ProductsCategories ();        
    }    
    
    
    public function getAll(){
        parent::success('ciao');
        
        exit();
    }
    
}