<?php

/**
 * SettingsController
 * Manage the settings of the project
 * @version 1.0
 */

class Admin_SettingsController extends Shineisp_Controller_Admin {
	
	protected $settings;
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$this->settings = new Settings ();
		$registry = Zend_Registry::getInstance ();
		$this->translator = $registry->Zend_Translate;;
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		// get the group id 
		$groupid = $this->getRequest ()->getParam ( 'groupid' );
		
		// Check if the groupid is a number
		if (empty ( $groupid ) || !is_numeric ( $groupid )) {
			$grp = SettingsGroups::findbyName('General');
			$groupid = $grp['group_id'];
		}
		
		$group = SettingsGroups::find($groupid);
		if(!empty($group)){
			$this->view->title = $group['name'];
			$this->view->description = $group['description'];
			$this->view->help = $group['help'];
		}else{
			$this->view->title = $this->translator->translate("Settings");
			$this->view->description = $this->translator->translate("Set here all the ShineISP parameters.");
		}
		
		$form = SettingsParameters::createForm ( $groupid );
		
		if ($this->getRequest ()->isPost () && $form->isValid ( $this->getRequest ()->getPost () )) {
			Settings::saveRecord ( $groupid, $form->getValues () );
		}
		
		
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')));
		
		// Create the html form 
		$this->view->form = $form;
	}
}