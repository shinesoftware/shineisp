<?php
/**
 * Shineisp_Custom_Callmeback
 * This class help the users to show a little form to ask a telephone number
 * 
 * 
 * @author Shine Software
 */


class Shineisp_Custom_Callmeback {
	
	/**
	 * Show the domain checker form
	 */
	public function Show($parameters){
		$view 		= new Zend_View();
		$form 		= new Default_Form_CallmebackForm( array ('action' => '/index/callmeback', 'method' => 'post' ) );
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		$title = !empty($parameters['button']) ? $parameters['button'] : "Call me!";
		$form->getElement('callme')->setAttrib('title', $translator->translate($title));
		
		// Set the path of the view templates
		$view->addScriptPath('../library/Shineisp/Custom/views');
		
        $view->form = $form;
        
		return $view->render('callmeback.phtml');
	}
}