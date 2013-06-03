<?php
/**
 * Shineisp_Custom_Productlist
 * This class get all the products in the database
 * @author Shine Software
 *
 */


class Shineisp_Custom_Productlist {
	/*
	 * Show
	 * List of all the products
	 */
	public function Show($parameters){
		$view = new Zend_View();
		$view->addScriptPath('../library/Shineisp/Custom/views');
		
		$ns = new Zend_Session_Namespace ();
		$languageID = Languages::get_language_id($ns->lang);
		
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		if(!empty($parameters['category']) && is_numeric($parameters['category'])){
			$id = $parameters['category'];
		}else{
			return "";
		}
			
		// Get the products
		$view->products = ProductsCategories::getProductListbyCatID($id, "p.product_id, p.uri as uri, pd.name as name, pd.shortdescription as description", $languageID);
		
		return $view->render('productlist.phtml');
	}
}