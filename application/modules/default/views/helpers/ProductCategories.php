<?php
/**
 * ProductCategories helper
 */
class Zend_View_Helper_ProductCategories extends Zend_View_Helper_Abstract {
	
	public function productcategories($categories) {
		$cats = array();
		$categories = explode("/", $categories);
		
		foreach($categories as $categoryid){
		  $category = ProductsCategories::find($categoryid, "name, uri", true);
		  if(!empty($category[0])){
		  	$cats[] = array('uri' => "/categories/".$category[0]['uri'].".html",  'name' => $category[0]['name']);
		  }
		}
		
		$this->view->categories = $cats;
        return $this->view->render ( 'partials/productcategories.phtml' );
	}
}