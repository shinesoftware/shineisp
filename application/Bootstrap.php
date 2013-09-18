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
		$this->bootstrap ( 'frontController' );
		
		$fc = Zend_Controller_Front::getInstance ();
		$fc->registerPlugin(new Shineisp_Controller_Plugin_Starter ());
		$fc->registerPlugin(new Shineisp_Controller_Plugin_Language());
		$fc->registerPlugin(new Shineisp_Controller_Plugin_Currency());
		$fc->registerPlugin(new Shineisp_Controller_Plugin_Navigation());
		
		if(Shineisp_Main::isReady()){
			// Init new plugin architecture
			Zend_Registry::set("em", new Zend_EventManager_EventManager());
			$Shineisp_Plugins = new Shineisp_Plugins();
			$Shineisp_Plugins->initAll();
			
			$fc->registerPlugin ( new Shineisp_Controller_Plugin_Acl(new Shineisp_Acl()));
			$fc->registerPlugin ( new Shineisp_Controller_Plugin_Migrate() );
		}
	}
	
	protected function _initRouter() {
		$this->bootstrap('FrontController');
		
		$front = Zend_Controller_Front::getInstance ();
		$router = $front->getRouter ();
		
		# http://www.shineisp.com/productname.html
		$router->addRoute ( 'fastproduct', new Zend_Controller_Router_Route_Regex ( '(.+)\.html', array ('module' => 'default', 'controller' => 'products', 'action' => 'get' ), array (1 => 'q' ), '%s.html' ) );
		
		# http://www.shineisp.com/products/productname.html
		$router->addRoute ( 'products', new Zend_Controller_Router_Route_Regex ( 'products/(.+)\.html', array ('module' => 'default', 'controller' => 'products', 'action' => 'get' ), array (1 => 'q' ), 'products/%s.html' ) );
		
		# http://www.shineisp.com/categories/hosting.html
		$router->addRoute ( 'categories', new Zend_Controller_Router_Route_Regex ( 'categories/(.+)\.html', array ('module' => 'default', 'controller' => 'categories', 'action' => 'list' ), array (1 => 'q' ), 'categories/%s.html' ) );
		
		# http://www.shineisp.com/cms/mypage.html
		$router->addRoute ( 'cms', new Zend_Controller_Router_Route_Regex ( 'cms/(.+)\.html', array ('module' => 'default', 'controller' => 'cms', 'action' => 'page' ), array (1 => 'url' ), 'cms/%s.html' ) );
		
		# http://www.shineisp.com/wiki/myhelp.html
		$router->addRoute ( 'wiki', new Zend_Controller_Router_Route_Regex ( 'wiki/(.+)\.html', array ('module' => 'default', 'controller' => 'wiki', 'action' => 'help' ), array (1 => 'uri' ), 'wiki/%s.html' ) );
		
		# http://www.shineisp.com/tlds/com.html
		$router->addRoute ( 'tlds', new Zend_Controller_Router_Route_Regex ( 'tlds/(.+)\.html', array ('module' => 'default', 'controller' => 'tlds', 'action' => 'index' ), array (1 => 'uri' ), 'tlds/%s.html' ) );
			
		# http://www.shineisp.com/seo/products.html
		$router->addRoute ( 'seo', new Zend_Controller_Router_Route_Regex ( 'seo/(.+)\.html', array ('module' => 'default', 'controller' => 'seo', 'action' => 'products' ), array (1 => 'action' ), 'seo/%s.html' ) );
			
		$routeLang = new Zend_Controller_Router_Route_Regex('([a-z]{2})', array('module' => 'default', 'lang' => 'en', 'controller' => 'index', 'action' => 'index'), array (1 => 'lang' ), '%s' );
		$router->addRoute('route_1', $routeLang);
		
		return $router;
	}

}