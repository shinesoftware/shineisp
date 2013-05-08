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
	
    protected function _initLayoutHelper() {
        $this->bootstrap ( 'frontController' );
        if(Shineisp_Main::isReady()){
            //Zend_Controller_Action_HelperBroker::addHelper( new Api_Controller_Action_Helper_Xmlloader() );
        }
    }
}
