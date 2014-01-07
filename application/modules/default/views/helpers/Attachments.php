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
	
	/**
	 * Get the attachment files
	 * 
	 * @param integer $id
	 * @param string $attachedto [customers, orders, domains]
	 */
	public function Attachments($id, $attachedto) {
		$registry = Shineisp_Registry::getInstance ();
		$translator = $registry->Zend_Translate;
		
		// Get the attached files
		if(!empty($id) && is_numeric($id)){
			$this->view->files = Files::findbyExternalId($id, $attachedto);
		}
		
		// Path of the template
		return $this->view->render ( 'partials/attachments.phtml' );
	}
}
