<?php
/**
 * Shineisp_Custom_Reviewslist
 * This class get a group of the latest products
 * @author Shine Software
 *
 */


class Shineisp_Custom_Reviewslist {

	public function Show($parameters){
		$view = new Zend_View();
		$view->addScriptPath('../library/Shineisp/Custom/views');
		$limit = 5;
		
		$ns = new Zend_Session_Namespace ();
		$languageID = Languages::get_language_id($ns->lang);
		
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		if(!empty($parameters['limit']) && is_numeric($parameters['limit'])){
			$limit = $parameters['limit'];
		}
			
		$view->data = Reviews::get_random($limit);
		return $view->render('reviewslist.phtml');
	}
}