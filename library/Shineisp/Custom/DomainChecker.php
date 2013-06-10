<?php
/**
 * Shineisp_Custom_DomainChecker
 * This class help the users to check if a domain is available or not
 * @author Shine Software
 *
 */


class Shineisp_Custom_DomainChecker {
	
	/**
	 * Show the domain checker form
	 */
	public function Show($parameters){
		$view 		= new Zend_View();
		$form 		= new Default_Form_DomainsinglecheckerForm ( array ('action' => '/domainschk/check', 'method' => 'post' ) );
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		$title = !empty($parameters['title']) ? $parameters['title'] : "Choose your new domain name!";
		$form->getElement('name')->setAttrib('title', $translator->translate($title));
		
		// Set the path of the view templates
		$view->addScriptPath('../library/Shineisp/Custom/views');
		
        $view->form = $form;
        
		return $view->render('domainchecker.phtml');
	}
}