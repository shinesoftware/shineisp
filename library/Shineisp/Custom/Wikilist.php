<?php
/**
 * Shineisp_Custom_Wikilist
 * This class get a group of wiki items randomly
 * @author Shine Software
 *
 */


class Shineisp_Custom_Wikilist {

	public function Show($parameters){
		$view = new Zend_View();
		$view->addScriptPath('../library/Shineisp/Custom/views');
		$limit = 10;
		$id = null;
		
		$ns = new Zend_Session_Namespace ();
		$languageID = Languages::get_language_id($ns->lang);
		
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		if(!empty($parameters['category']) && is_numeric($parameters['category'])){
			$id = $parameters['category'];
		}
		
		if(!empty($parameters['limit']) && is_numeric($parameters['limit'])){
			$limit = $parameters['limit'];
		}
			
		// Get the products
		$view->wiki = Wiki::get_items($limit, $id);
		
		return $view->render('wikilist.phtml');
	}
}