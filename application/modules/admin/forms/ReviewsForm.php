<?php
class Admin_Form_ReviewsForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
    	$this->addElement('select', 'product_id', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Product'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large'
        ));
        
        $this->getElement('product_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList());
                  
                  
        $this->addElement('text', 'publishedat', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Published At'),
            'class'       => 'little-input date'
        ));
                  
        $this->addElement('text', 'nick', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Nick'),
            'class'       => 'input-large'
        ));
        
        $this->addElement('select', 'referer', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Bootstrap'),
        		'label'       => $translate->_('Who is Talking About Us?'),
        		'class'       => 'medium-input',
        		'multiOptions' => array('Google' => 'Google', 'Bing' => 'Bing', 'Yahoo' => 'Yahoo', $translate->_('Other Search Engine') => $translate->_('Other Search Engine'), 'Websites' => $translate->_('Websites/Blogs'), $translate->_('Friend suggestion') => $translate->_('Friend suggestion'))
        ));
        
        $this->addElement('text', 'city', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Bootstrap'),
        		'label'       => $translate->_('City'),
        		'class'       => 'medium-input'
        ));        
                  
        $this->addElement('text', 'ip', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Bootstrap'),
        		'label'       => $translate->_('IP'),
        		'class'       => 'medium-input'
        ));        
                  
        $this->addElement('text', 'latitude', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Bootstrap'),
        		'label'       => $translate->_('Latitude'),
        		'class'       => 'small-input'
        ));       
                  
        $this->addElement('text', 'longitude', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Bootstrap'),
        		'label'       => $translate->_('Longitude'),
        		'class'       => 'small-input'
        ));       
                  
        $this->addElement('text', 'subject', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Subject'),
            'class'       => 'input-large'
        ));
                  
        $this->addElement('text', 'email', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Email'),
            'class'       => 'input-large'
        ));
        
    	$this->addElement('select', 'stars', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Stars'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large',
    		'multiOptions' => array(1 => '1 ' . $translate->_('Star'), 2 => '2 ' . $translate->_('Stars'), 3 => '3 ' . $translate->_('Stars'), 4 => '4 ' . $translate->_('Stars'), 5 => '5 ' . $translate->_('Stars'))
        ));        
        
    	$this->addElement('select', 'active', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Active'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large',
    		'multiOptions' => array(0 => 'Not Published', 1 => 'Published')
        ));        
        
        $this->addElement('textarea', 'review', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
        	'required'    => true,
            'label'       => $translate->_('Review'),
            'class'       => 'span12'
        ));
        
        $this->addElement('hidden', 'review_id');

    }
    
}