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

class Api_Bootstrap extends Zend_Application_Module_Bootstrap {
	protected function _initRouter() {
		$this->bootstrap('FrontController');
	
		$front = Zend_Controller_Front::getInstance ();
		$router = $front->getRouter ();
	
		# http://www.shineisp.com/api/products.wsdl
		$router->addRoute ( 'wsdl', new Zend_Controller_Router_Route_Regex ( 'api/(.+)\.wsdl', array ('module' => 'api', 'lang' => 'en', 'controller' => 'request', 'action' => 'wsdl' ), array (1 => 'class' ), 'api/%s.wsdl' ) );
	
		# http://www.shineisp.com/api/products.soap
		$router->addRoute ( 'soap', new Zend_Controller_Router_Route_Regex ( 'api/(.+)\.soap', array ('module' => 'api', 'lang' => 'en', 'controller' => 'request', 'action' => 'soap' ), array (1 => 'class' ), 'api/%s.soap' ) );
	
		# http://www.shineisp.com/api/resellers/products.soap
		$router->addRoute ( 'soap2', new Zend_Controller_Router_Route_Regex ( 'api/(.+)/(.+)\.soap', array ('module' => 'api', 'lang' => 'en', 'controller' => 'request', 'action' => 'soap' ), array (1 => 'isreseller', 2 => 'class'), 'api/%s/%s.soap' ) );
	
		# http://www.shineisp.com/api/resellers/products.soap
		$router->addRoute ( 'wsdl2', new Zend_Controller_Router_Route_Regex ( 'api/(.+)/(.+)\.wsdl', array ('module' => 'api', 'lang' => 'en', 'controller' => 'request', 'action' => 'wsdl' ), array (1 => 'isreseller', 2 => 'class'), 'api/%s/%s.wsdl' ) );
	
		return $router;
	}
}
