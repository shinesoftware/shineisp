<?php
/**
 *
 * @version 0.1
 */
/**
 * Attachments helper
 * Create a simple list of attachments
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Attachments extends Zend_View_Helper_Abstract {
	/*
	 * 
	 */
	public function Attachments($id, $attachedto) {
		$registry = Zend_Registry::getInstance ();
		$translator = $registry->Zend_Translate;
		
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		if (!empty($NS->customer)) {
			$data = $NS->customer;
			
			// Get the attached files
			if(!empty($id) && is_numeric($id)){
				$this->view->files = Files::findbyExternalId($id, $attachedto);
			}
			
			// Path of the template
			return $this->view->render ( 'partials/attachments.phtml' );
		}
	}
}
