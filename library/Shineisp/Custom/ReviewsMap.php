<?php
/**
 * Shineisp_Custom_ReviewsMap
 * This class get all the customer review coordinates and create a google map object 
 * @author Shine Software
 *
 */


class Shineisp_Custom_ReviewsMap {

	public function Show($parameters){
		$view = new Zend_View();
		$view->addScriptPath('../library/Shineisp/Custom/views');
		
		$ns = new Zend_Session_Namespace ();
		$languageID = Languages::get_language_id($ns->lang);
		
		// Generate the xml file in the public path /documents
		Reviews::getXMLDataMap($languageID);
		return $view->render('reviewsmap.phtml');
	}
}