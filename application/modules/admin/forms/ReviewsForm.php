<?php
class Admin_Form_ReviewsForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
    	$this->addElement('select', 'product_id', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Product',
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
            'label'       => 'Published At',
            'class'       => 'text-input little-input date'
        ));
                  
        $this->addElement('text', 'nick', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'Nick',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('select', 'referer', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Composite'),
        		'label'       => 'Who is Talking About Us?',
        		'class'       => 'text-input medium-input',
        		'multiOptions' => array('Google' => 'Google', 'Bing' => 'Bing', 'Yahoo' => 'Yahoo', 'Other Search Engine' => 'Other Search Engine', 'Websites' => 'Websites/Blogs', 'Magento Commerce' => 'Magento Commerce', 'Friend suggestion' => 'Friend suggestion')
        ));
        
        $this->addElement('text', 'city', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Composite'),
        		'label'       => 'City',
        		'class'       => 'text-input medium-input'
        ));        
                  
        $this->addElement('text', 'ip', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Composite'),
        		'label'       => 'IP',
        		'class'       => 'text-input medium-input'
        ));        
                  
        $this->addElement('text', 'latitude', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Composite'),
        		'label'       => 'Latitude',
        		'class'       => 'text-input small-input'
        ));       
                  
        $this->addElement('text', 'longitude', array(
        		'filters'     => array('StringTrim'),
        		'decorators'  => array('Composite'),
        		'label'       => 'Longitude',
        		'class'       => 'text-input small-input'
        ));       
                  
        $this->addElement('text', 'subject', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'Subject',
            'class'       => 'text-input large-input'
        ));
                  
        $this->addElement('text', 'email', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Email',
            'class'       => 'text-input large-input'
        ));
        
    	$this->addElement('select', 'stars', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Stars',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
    		'multiOptions' => array(1 => '1 Star', 2 => '2 Stars', 3 => '3 Stars', 4 => '4 Stars', 5 => '5 Stars')
        ));        
        
    	$this->addElement('select', 'active', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Active',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
    		'multiOptions' => array(0 => 'Not Published', 1 => 'Published')
        ));        
        
        $this->addElement('textarea', 'review', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
        	'required'    => true,
            'label'       => 'Review',
            'class'       => 'textarea'
        ));
   
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'review_id');

    }
    
}