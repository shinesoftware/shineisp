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

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	
	protected function _initDoctrine() {
		$conn = null;
		
		$this->getApplication ()->getAutoloader ()->pushAutoloader ( array ('Doctrine', 'autoload' ) );
		
		spl_autoload_register ( array ('Doctrine', 'modelsAutoload' ) );
		
		$manager = Doctrine_Manager::getInstance ();
		$manager->setAttribute ( Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true );
		$manager->setAttribute ( Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE );
		$manager->setAttribute ( Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true );
		
		if(Shineisp_Main::isReady()){
			$dsn = Shineisp_Main::getDsn();
			$conn = Doctrine_Manager::connection ( $dsn, 'doctrine' );
			
			$queryDbg = new Shineisp_Commons_QueriesLogger();
			$conn->addListener($queryDbg);
			
			$conn->setAttribute ( Doctrine::ATTR_USE_NATIVE_ENUM, true );
			$conn->setCharset ( 'UTF8' );
		}
		
		$doctrinConfig = $this->getOption ( 'doctrine' );
		Doctrine::loadModels ( $doctrinConfig ['models_path'] );
		return $conn;
	}
	
	protected function _initControllerPlugin() {
		$fc = Zend_Controller_Front::getInstance ();
		$fc->registerPlugin(new Shineisp_Controller_Plugin_Starter ());
		$fc->registerPlugin(new Shineisp_Controller_Plugin_Language());
		$fc->registerPlugin(new Shineisp_Controller_Plugin_Currency());
		
		// Init new plugin architecture
		$Shineisp_Plugins = new Shineisp_Plugins();
		$Shineisp_Plugins->initAll();
		
		if(Shineisp_Main::isReady()){
			$fc->registerPlugin ( new Shineisp_Controller_Plugin_Acl(new Shineisp_Acl()));
			$fc->registerPlugin ( new Shineisp_Controller_Plugin_Migrate() );
		}
	}
	
	
	protected function _initRouter() {
		$this->bootstrap('FrontController');
		
		$front = Zend_Controller_Front::getInstance ();
		$router = $front->getRouter ();
		 
		$router->addRoute ( 'fastproduct', new Zend_Controller_Router_Route_Regex ( '(.+)\.html', array ('module' => 'default', 'controller' => 'products', 'action' => 'get' ), array (1 => 'q' ), '%s.html' ) );
		$router->addRoute ( 'products', new Zend_Controller_Router_Route_Regex ( 'products/(.+)\.html', array ('module' => 'default', 'controller' => 'products', 'action' => 'get' ), array (1 => 'q' ), 'products/%s.html' ) );
		$router->addRoute ( 'categories', new Zend_Controller_Router_Route_Regex ( 'categories/(.+)\.html', array ('module' => 'default', 'controller' => 'categories', 'action' => 'list' ), array (1 => 'q' ), 'categories/%s.html' ) );
		$router->addRoute ( 'cms', new Zend_Controller_Router_Route_Regex ( 'cms/(.+)\.html', array ('module' => 'default', 'controller' => 'cms', 'action' => 'page' ), array (1 => 'url' ), 'cms/%s.html' ) );
		$router->addRoute ( 'wiki', new Zend_Controller_Router_Route_Regex ( 'wiki/(.+)\.html', array ('module' => 'default', 'controller' => 'wiki', 'action' => 'help' ), array (1 => 'uri' ), 'wiki/%s.html' ) );
		$router->addRoute ( 'tlds', new Zend_Controller_Router_Route_Regex ( 'tlds/(.+)\.html', array ('module' => 'default', 'controller' => 'tlds', 'action' => 'index' ), array (1 => 'uri' ), 'tlds/%s.html' ) );
		
		return $router;
	}

}