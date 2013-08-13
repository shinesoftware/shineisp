<?php
/**
 * ShineISP
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   ISP Management
 * @copyright  Copyright (c) 2005-2010 Shine Software. (http://www.shinesoftware.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap {
	
	protected function _initLayoutHelper() {
		$this->bootstrap ( 'frontController' );
        
		if(Shineisp_Main::isReady()){
			Zend_Controller_Action_HelperBroker::addHelper ( new Shineisp_Controller_Action_Helper_LayoutLoader ( ) );
			Zend_Controller_Action_HelperBroker::addHelper ( new Shineisp_Controller_Action_Helper_Ajaxgrid ( ) );
			Zend_Controller_Action_HelperBroker::addHelper ( new Shineisp_Controller_Action_Helper_Datagrid ( ) );
		}
	}
	
	/**
	 * Initializate the Administration Menu
	 */
	protected function _initViewHelpers() {
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$pages = array();
		
		// Load the xml navigation menu 
		$navContainerConfig = new Zend_Config_Xml(APPLICATION_PATH . '/modules/admin/navigation.xml', 'nav');
		$navContainer = new Zend_Navigation($navContainerConfig);
		
		// Adding the configuration menu items
		$configuration = SettingsGroups::getlist ();
		$submenu = $navContainer->findOneByLabel('Configuration');
		foreach ($configuration as $id => $item){
			$pages[] = array('label' => $item, 'uri' => '/admin/settings/index/groupid/' . $id, 'resource' => 'admin:settings');
		}
		$submenu->addPages($pages);
		Zend_Registry::set('navigation', $navContainer);
		
		// Create the menu
		$view->navigation($navContainer);
		
		// Attach the Zend ACL to the Navigation menu
		$auth = Zend_Auth::getInstance();
		if($auth){
			$acl = $auth->getStorage()->read();
			if(is_object($acl)){
				Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($acl);
				Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole("guest");
			}
		}
	}
}