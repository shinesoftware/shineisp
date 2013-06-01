<?php
/**
 * Slideshow helper
 */
class Zend_View_Helper_Slideshow extends Zend_View_Helper_Abstract {
	
	public function slideshow($data = array()) {
		$ns = new Zend_Session_Namespace ();
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		$category_id = Settings::findbyParam('slidecategoryid');
		
		if(is_numeric($category_id)){
			// Get the products
			$products = ProductsCategories::getProductListbyCatID($category_id, "p.product_id, p.ishighlighted as ishighlighted, p.uri as uri, pd.name as name, pd.nickname as nickname, pd.shortdescription as description, pag.code as groupcode", $ns->langid);
			
			$this->view->products = $products;
		}
		
		return $this->view->render ( 'partials/slideshow.phtml' );
	}
}