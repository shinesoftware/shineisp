<?php
/**
 * Zend Framework
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
 * @category   Zend
 * @package    Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Feed.php 20326 2010-01-16 00:20:43Z padraic $
 */
 
/**
 * @see Zend_Feed_Writer_Extension_RendererAbstract
 */
require_once 'Zend/Feed/Writer/Extension/RendererAbstract.php';
 
/**
 * @category   Google Products
 * @package    Zend_Feed_Writer
 * @copyright  Shine Software (http://www.shinesoftware.it)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Shineisp_Feed_Writer_Extension_Google_Renderer_Feed
    extends Zend_Feed_Writer_Extension_RendererAbstract
{

    /**
     * Set to TRUE if a rendering method actually renders something. This
     * is used to prevent premature appending of a XML namespace declaration
     * until an element which requires it is actually appended.
     *
     * @var bool
     */
    protected $_called = true;
    
    /**
     * Render feed
     * 
     * @return void
     */
    public function render()
    {
    	$this->_appendNamespaces();
    }
    
    /**
     * Append Google namespaces to root element of feed
     * 
     * @return void
     */
    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');  
        $this->getRootElement()->setAttribute('xmlns:c', 'http://base.google.com/cns/1.0');  
    }

}
