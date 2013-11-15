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
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'request_path', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Request Path'),
        	'required'   => true,
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Description'),
            'decorators' => array('Bootstrap'),
            'class'      => 'col-lg-12'
        ));   
        
        $this->addElement('select', 'product_id', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Products'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->getElement('product_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList(true));                  
        
        $this->addElement('select', 'category_id', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Categories'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(ProductsCategories::getList(true));                  
        
        $this->addElement('checkbox', 'temporary', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Temporary'),
            'decorators' => array('Bootstrap')
        ));  
        
        $this->addElement('hidden', 'url_rewrite_id');
    }
}