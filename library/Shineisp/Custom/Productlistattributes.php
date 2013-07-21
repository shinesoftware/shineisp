<?php
/**
 * Shineisp_Custom_Productlist
 * This class get all the products in the database and compare them eachother
 * @author Shine Software
 *
 */

class Shineisp_Custom_Productlistattributes {
	
	public function Show($parameters){
		$output = "";
		$ns = new Zend_Session_Namespace ();
		$languageID = Languages::get_language_id($ns->lang);
		
		$mainviewhelper = new Zend_View();
		$mainviewhelper->addBasePath(APPLICATION_PATH . '/modules/default/views/');
		
		$view = new Zend_View();
		$view->addScriptPath('../library/Shineisp/Custom/views');
		
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		if(!empty($parameters['code'])){
			$code = $parameters['code'];
		}else{
			return "";
		}
		
		// Get the products
		$data = Products::GetProductsByGroupCode($code, $languageID);
		
		// Check the existence of the mandatories attributes
		if (!empty($data['attributes'][0]))
			$view->attributes = $data['attributes'];
		
		// Check if there are values set for the group of the product selected
		if (!empty($data['attributes_values'][0]))
			$view->values = $data['attributes_values'];
			
		// Get the products
		if (!empty($data['products'][0]))
			$view->products = $data['products'];
			
		$view->mainviewhelper = $mainviewhelper;
		
		// Path of the template
		return $view->render ( 'productsattributes.phtml' );
	}
}