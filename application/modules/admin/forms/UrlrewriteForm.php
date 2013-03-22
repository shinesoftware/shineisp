<?php
class Admin_Form_UrlrewriteForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
        $this->addElement('text', 'target_path', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Target Path',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'request_path', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Request Path',
        	'required'   => true,
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Description',
            'decorators' => array('Composite'),
            'class'      => 'textarea'
        ));   
        
        $this->addElement('select', 'product_id', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Products',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('product_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList(true));                  
        
        $this->addElement('select', 'category_id', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Categories',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(ProductsCategories::getList(true));                  
        
        $this->addElement('checkbox', 'temporary', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Temporary',
            'decorators' => array('Composite')
        ));  
                          
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'url_rewrite_id');
    }
}