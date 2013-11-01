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
            'class'      => 'form-control'
        ));
        
        $this->getElement('product_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList());
                  
                  
        $this->addElement('text', 'publishedat', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Published At'),
            'class'       => 'form-control date'
        ));
                  
        $this->addElement('text', 'nick', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Nick'),
            'class'       => 'form-control'
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
            'class'       => 'form-control'
        ));
                  
        $this->addElement('text', 'email', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Email'),
            'class'       => 'form-control'
        ));
        
    	$this->addElement('text', 'stars', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'data-clearable'   => $translate->_('Delete'),
            'data-min'   => "1",
            'data-max'   => "5",
            'label'      => $translate->_('Stars'),
            'decorators' => array('Bootstrap'),
            'class'      => 'rating',
        ));        
        
    	$this->addElement('checkbox', 'active', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Active'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));        
        
        $this->addElement('textarea', 'review', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
        	'required'    => true,
            'class'       => 'col-lg-12 form-control wysiwyg'
        ));
        
        $this->addElement('hidden', 'review_id');

    }
    
}