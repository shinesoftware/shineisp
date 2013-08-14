<?php

class Shineisp_Controller_Plugin_Navigation extends Zend_Controller_Plugin_Abstract {
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getExistingHelper ( 'ViewRenderer' );
		$viewRenderer->initView ();
		$view = $viewRenderer->view;
		$module = $request->getModuleName ();
		
		if($module == "admin"){
			$navContainerConfig = new Zend_Config_Xml(APPLICATION_PATH . '/modules/admin/navigation.xml', 'nav');
			$navContainer = new Zend_Navigation($navContainerConfig);  // Load the xml navigation menu

			// check if the database configuration has been set
			if(Shineisp_Main::isReady()){
				// Adding the configuration menu items
				$configuration = SettingsGroups::getlist ();
				$submenu = $navContainer->findOneByLabel('Configuration');
				foreach ($configuration as $id => $item){
					$pages[] = array('label' => $item, 'uri' => '/admin/settings/index/groupid/' . $id, 'resource' => 'admin:settings');
				}
				$submenu->addPages($pages);
			}
				
			// Attach the Zend ACL to the Navigation menu
			$auth = Zend_Auth::getInstance();
			if($auth){
				$acl = $auth->getStorage()->read();
				if(is_object($acl)){
					Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($acl);
					Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole("administrator");
				}
			}
		}else{
			$navContainerConfig = new Zend_Config_Xml(APPLICATION_PATH . '/modules/default/navigation.xml', 'nav');
			$navContainer = new Zend_Navigation($navContainerConfig);  // Load the xml navigation menu
				
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
		
		foreach ( $navContainer->getPages () as $page ) {
			foreach ( $page->getPages () as $subpage ) {
				foreach ( $subpage->getPages () as $subsubpage ) {
					$uri = $subsubpage->getHref ();
					if ($uri === $request->getRequestUri ()) {
						$subsubpage->setActive(true);
					}
				}
			}
		}
		$view->navigation ( $navContainer );
	}
}