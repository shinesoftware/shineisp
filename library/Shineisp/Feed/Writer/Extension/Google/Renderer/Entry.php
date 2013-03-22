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
 * @version    $Id: Entry.php 20326 2010-01-16 00:20:43Z padraic $
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
class Shineisp_Feed_Writer_Extension_Google_Renderer_Entry
    extends Zend_Feed_Writer_Extension_RendererAbstract
{

    /**
     * Set to TRUE if a rendering method actually renders something. This
     * is used to prevent premature appending of a XML namespace declaration
     * until an element which requires it is actually appended.
     *
     * @var bool
     */
    protected $_called = false;
    
    /**
     * Render entry
     * 
     * @return void
     */
    public function render()
    {
		$this->_appendNamespaces();
		
		$this->_setProductId($this->_dom, $this->_base);
		$this->_setProductType($this->_dom, $this->_base);
		$this->_setAvailability($this->_dom, $this->_base);
		$this->_setBrand($this->_dom, $this->_base);
		$this->_setCategory($this->_dom, $this->_base);
		$this->_setImageLink($this->_dom, $this->_base);
		$this->_setPrice($this->_dom, $this->_base);
		$this->_setCondition($this->_dom, $this->_base);
    }
    
    /**
     * Append entry namespaces
     * 
     * @return void
     */
    protected function _appendNamespaces(){}
    
    /**
     * Set Image Product link 
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    protected function _setImageLink(DOMDocument $dom, DOMElement $root)
    {
    	$link = $dom->createElement('g:image_link');
    	$root->appendChild($link);
    	$text = $dom->createTextNode($this->getDataContainer()->getImageLink());
        $link->appendChild($text);
    }
    
    /**
     * Set Product Availability
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    protected function _setAvailability(DOMDocument $dom, DOMElement $root)
    {
    	$link = $dom->createElement('g:availability');
    	$root->appendChild($link);
    	$text = $dom->createTextNode($this->getDataContainer()->getAvailability());
        $link->appendChild($text);
    }
    
    /**
     * Set Product Brand
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    protected function _setBrand(DOMDocument $dom, DOMElement $root)
    {
    	$link = $dom->createElement('g:brand');
    	$root->appendChild($link);
    	$text = $dom->createTextNode($this->getDataContainer()->getBrand());
        $link->appendChild($text);
    }
    
    /**
     * Set Product Type Code
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    protected function _setProductType(DOMDocument $dom, DOMElement $root)
    {
    	$link = $dom->createElement('g:product_type');
    	$root->appendChild($link);
    	$text = $dom->createTextNode($this->getDataContainer()->getProductType());
        $link->appendChild($text);
    }
    
    /**
     * Set Product Category
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    protected function _setCategory(DOMDocument $dom, DOMElement $root)
    {
    	$link = $dom->createElement('g:google_product_category');
    	$root->appendChild($link);
    	$text = $dom->createTextNode($this->getDataContainer()->getCategory());
        $link->appendChild($text);
    }
    
    /**
     * Set Product Identifier
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    protected function _setProductId(DOMDocument $dom, DOMElement $root)
    {
    	$link = $dom->createElement('g:id');
    	$root->appendChild($link);
    	$text = $dom->createTextNode($this->getDataContainer()->getProductId());
        $link->appendChild($text);
    }
    
    /**
     * Set Price of the Product
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    protected function _setPrice(DOMDocument $dom, DOMElement $root)
    {
    	$link = $dom->createElement('g:price');
    	$root->appendChild($link);
    	$text = $dom->createTextNode($this->getDataContainer()->getPrice());
        $link->appendChild($text);
    }
    
    /**
     * Set Condition of the Product 
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    protected function _setCondition(DOMDocument $dom, DOMElement $root)
    {
    	$link = $dom->createElement('g:condition');
    	$root->appendChild($link);
    	$text = $dom->createTextNode($this->getDataContainer()->getCondition());
        $link->appendChild($text);
    }
}
