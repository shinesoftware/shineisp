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
		  	$cats[] = '<a href="/categories/'.$category[0]['uri'].'.html">' . $category[0]['name'] . "</a>";
		  }
		}
        echo implode(" / ", $cats);		
	}
}