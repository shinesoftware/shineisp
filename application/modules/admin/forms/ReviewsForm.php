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
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('product_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList());
                  
                  
        $this->addElement('text', 'publishedat', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Published At'),
            'class'       => 'text-input little-input date'
        ));
                  
        $this->addElement('text', 'nick', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Nick'),
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('select', 'referer', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Composite'),
        		'label'       => $translate->_('Who is Talking About Us?'),
        		'class'       => 'text-input medium-input',
        		'multiOptions' => array('Google' => 'Google', 'Bing' => 'Bing', 'Yahoo' => 'Yahoo', $translate->_('Other Search Engine') => $translate->_('Other Search Engine'), 'Websites' => $translate->_('Websites/Blogs'), $translate->_('Friend suggestion') => $translate->_('Friend suggestion'))
        ));
        
        $this->addElement('text', 'city', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Composite'),
        		'label'       => $translate->_('City'),
        		'class'       => 'text-input medium-input'
        ));        
                  
        $this->addElement('text', 'ip', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Composite'),
        		'label'       => $translate->_('IP'),
        		'class'       => 'text-input medium-input'
        ));        
                  
        $this->addElement('text', 'latitude', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Composite'),
        		'label'       => $translate->_('Latitude'),
        		'class'       => 'text-input small-input'
        ));       
                  
        $this->addElement('text', 'longitude', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Composite'),
        		'label'       => $translate->_('Longitude'),
        		'class'       => 'text-input small-input'
        ));       
                  
        $this->addElement('text', 'subject', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Subject'),
            'class'       => 'text-input large-input'
        ));
                  
        $this->addElement('text', 'email', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Email'),
            'class'       => 'text-input large-input'
        ));
        
    	$this->addElement('select', 'stars', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Stars'),
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
    		'multiOptions' => array(1 => '1 ' . $translate->_('Star'), 2 => '2 ' . $translate->_('Stars'), 3 => '3 ' . $translate->_('Stars'), 4 => '4 ' . $translate->_('Stars'), 5 => '5 ' . $translate->_('Stars'))
        ));        
        
    	$this->addElement('select', 'active', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Active'),
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
    		'multiOptions' => array(0 => 'Not Published', 1 => 'Published')
        ));        
        
        $this->addElement('textarea', 'review', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
        	'required'    => true,
            'label'       => $translate->_('Review'),
            'class'       => 'textarea'
        ));
        
        $this->addElement('hidden', 'review_id');

    }
    
}