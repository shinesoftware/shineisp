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
	 * Handle the Attachment links
	 * @param integer $id
	 * @param string $attachedto
	 * @return string
	 */
	public function Attachments($id, $attachedto) {
		$registry = Zend_Registry::getInstance ();
		$translator = $registry->Zend_Translate;
		$records = Files::findbyExternalId($id, $attachedto);
		$auth = Zend_Auth::getInstance ();
		if ($auth->hasIdentity ()) {
			$data = $auth->getIdentity ();
			
			$this->view->files = $records;
			
			// Path of the template
			return $this->view->render ( 'partials/attachments.phtml' );
		}
	}
}
