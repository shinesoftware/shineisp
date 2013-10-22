<?php
class Admin_Form_UrlrewriteForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
        $this->addElement('text', 'target_path', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Target Path'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'request_path', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Request Path'),
        	'required'   => true,
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Description'),
            'decorators' => array('Composite'),
            'class'      => 'textarea'
        ));   
        
        $this->addElement('select', 'product_id', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Products'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->getElement('product_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList(true));                  
        
        $this->addElement('select', 'category_id', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Categories'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(ProductsCategories::getList(true));                  
        
        $this->addElement('checkbox', 'temporary', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Temporary'),
            'decorators' => array('Composite')
        ));  
        
        $this->addElement('hidden', 'url_rewrite_id');
    }
}