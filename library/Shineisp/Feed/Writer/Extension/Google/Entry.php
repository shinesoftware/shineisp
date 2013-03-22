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
 * @version    $Id: Entry.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @category   Google Products
 * @package    Zend_Feed_Writer
 * @copyright  Shine Software (http://www.shinesoftware.it)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Writer_Extension_Google_Entry
{
    /**
     * Array of Feed data for rendering by Extension's renderers
     *
     * @var array
     */
    protected $_data = array();
    
    /**
     * Encoding of all text values
     *
     * @var string
     */
    protected $_encoding = 'UTF-8';
    
    /**
     * Set feed encoding
     * 
     * @param  string $enc 
     * @return Zend_Feed_Writer_Extension_Google_Entry
     */
    public function setEncoding($enc)
    {
        $this->_encoding = $enc;
        return $this;
    }
    
    /**
     * Get feed encoding
     * 
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }
    
    /**
     * Set the product type
     *
     * @return string|null
     */
    public function setProductType($type)
    {
    	if (!is_string($type)) {
    		require_once 'Zend/Feed/Exception.php';
    		throw new Zend_Feed_Exception('Invalid parameter: parameter must be a string');
    	}
    	$this->_data['g:product_type'] = $type;
    }
    
    /**
     * Get the type
     *
     * @return string|null
     */
    public function getProductType()
    {
    	if (!array_key_exists('g:product_type', $this->_data)) {
    		return null;
    	}
    	return $this->_data['g:product_type'];
    }    
    
    /**
     * Set the product category
     *
     * @return string|null
     */
    public function setBrand($brand)
    {
    	if (!is_string($brand)) {
    		require_once 'Zend/Feed/Exception.php';
    		throw new Zend_Feed_Exception('Invalid parameter: parameter must be a string');
    	}
    	$this->_data['g:brand'] = $brand;
    }
    
    /**
     * Get the category
     *
     * @return string|null
     */
    public function getBrand()
    {
    	if (!array_key_exists('g:brand', $this->_data)) {
    		return null;
    	}
    	return $this->_data['g:brand'];
    }    
    
    /**
     * Set the product category
     *
     * @return string|null
     */
    public function setCategory($category)
    {
    	if (!is_string($category)) {
    		require_once 'Zend/Feed/Exception.php';
    		throw new Zend_Feed_Exception('Invalid parameter: parameter must be a string');
    	}
    	$this->_data['g:google_product_category'] = $category;
    }
    
    /**
     * Get the category
     *
     * @return string|null
     */
    public function getCategory()
    {
    	if (!array_key_exists('g:google_product_category', $this->_data)) {
    		return null;
    	}
    	return $this->_data['g:google_product_category'];
    }    
    
    /**
     * Set the product availability
     *
     * @return string|null
     */
    public function setAvailability($availability)
    {
    	if (!is_bool($availability)) {
    		require_once 'Zend/Feed/Exception.php';
    		throw new Zend_Feed_Exception('Invalid parameter: parameter must be a boolean value');
    	}
    	$this->_data['g:availability'] = ($availability) ? "in stock" : "out of stock";
    }
    
    /**
     * Get the product availability
     *
     * @return string|null
     */
    public function getAvailability()
    {
    	if (!array_key_exists('g:availability', $this->_data)) {
    		return null;
    	}
    	return $this->_data['g:availability'];
    }    
    
    /**
     * Set the product title
     *
     * @return string|null
     */
    public function setProductId($id)
    {
    	if (empty($id) || !is_string($id)) {
    		require_once 'Zend/Feed/Exception.php';
    		throw new Zend_Feed_Exception('Invalid parameter: parameter must be a non-empty string');
    	}
    	$this->_data['g:id'] = $id;
    }
    
    /**
     * Get the price
     *
     * @return string|null
     */
    public function getProductId()
    {
    	if (!array_key_exists('g:id', $this->_data)) {
    		return null;
    	}
    	return $this->_data['g:id'];
    }    
    
    /**
     * Set Product Image Link
     * 
     * @param  string $value 
     * @return Zend_Feed_Writer_Extension_Google_Entry
     */
    public function setImageLink($value)
    {
    	if (!Zend_Uri::check($value)) {
            require_once 'Zend/Feed/Exception.php';
            throw new Zend_Feed_Exception('invalid parameter: "image" may only'
            . ' be a valid URI/IRI: ' . $value);
        }
        if (!in_array(substr($value, -3), array('jpg','png','gif'))) {
            require_once 'Zend/Feed/Exception.php';
            throw new Zend_Feed_Exception('invalid parameter: "image" may only'
            . ' use file extension "jpg" or "png" or "gif" which must be the last three'
            . ' characters of the URI (i.e. no query string or fragment). '. $value . ' file has been sent');
        }
        $this->_data['g:image_link'] = $value;
        return $this;
    }
    
    /**
     * Get the image link to the HTML source
     *
     * @return string|null
     */
    public function getImageLink()
    {
    	if (!array_key_exists('g:image_link', $this->_data)) {
    		return null;
    	}
    	return $this->_data['g:image_link'];
    }    
    
    /**
     * Set Product Price
     * 
     * @param  string $value 
     * @return Zend_Feed_Writer_Extension_Google_Entry
     */
    public function setPrice($value)
    {
    	if (!is_numeric($value)) {
            require_once 'Zend/Feed/Exception.php';
            throw new Zend_Feed_Exception('invalid parameter: "price" may only use a number');
    	}
        $this->_data['g:price'] = $value;
        return $this;
    }
    
    /**
     * Get the price
     *
     * @return string|null
     */
    public function getPrice()
    {
    	if (!array_key_exists('g:price', $this->_data)) {
    		return null;
    	}
    	return $this->_data['g:price'];
    }    
    
    /**
     * Set Product Condition
     * 
     * @param  string $value 
     * @return Zend_Feed_Writer_Extension_Google_Entry
     */
    public function setCondition($value)
    {
    	if (empty($value)) {
            require_once 'Zend/Feed/Exception.php';
            throw new Zend_Feed_Exception('invalid parameter: "price" may only use a number');
    	}
        $this->_data['g:condition'] = $value;
        return $this;
    }
    
    /**
     * Get the price
     *
     * @return string|null
     */
    public function getCondition()
    {
    	if (!array_key_exists('g:condition', $this->_data)) {
    		return null;
    	}
    	return $this->_data['g:condition'];
    }    
    
    /**
     * Overloading to itunes specific setters
     * 
     * @param  string $method 
     * @param  array $params 
     * @return mixed
     */
    public function __call($method, array $params)
    {
        $point = Zend_Feed_Writer::lcfirst(substr($method, 9));
        if (!method_exists($this, 'setGoogle' . ucfirst($point))
            && !method_exists($this, 'addGoogle' . ucfirst($point))
        ) {
            require_once 'Zend/Feed/Writer/Exception/InvalidMethodException.php';
            throw new Zend_Feed_Writer_Exception_InvalidMethodException(
                'invalid method: ' . $method
            );
        }
        if (!array_key_exists($point, $this->_data) 
            || empty($this->_data[$point])
        ) {
            return null;
        }
        return $this->_data[$point];
    }
}
