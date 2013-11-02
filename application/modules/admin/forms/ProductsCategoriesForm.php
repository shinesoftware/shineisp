<?php
class Admin_Form_ProductsCategoriesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
        $this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Name'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));

        $this->addElement('text', 'uri', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('URI'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));        

        $this->addElement('text', 'googlecategs', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Google Categories'),
            'description'      => $translate->_('See at http://support.google.com/merchants/bin/answer.py?hl=it&answer=1705911 for the list of the tassonomy'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));        

        $this->addElement('text', 'position', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Position'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));        
        
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'decorators' => array('Bootstrap'),
            'label'      => $translate->_('Description'),
            'class'      => 'col-lg-12 form-control input-lg wysiwyg'
        ));
        
        $this->addElement('textarea', 'keywords', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Keywords'),
            'rows'        => 5,
            'class'       => 'col-lg-12'
        ));     
        
        $this->addElement('select', 'parent', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Parent Category'),
            'description' => $translate->_('Select here the parent category.'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->getElement('parent')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(ProductsCategories::getList(true));   
        
        $this->addElement('multiselect', 'products', array(
            'label'       => $translate->_('Products'),
            'description' => $translate->_('Select here the products to add to this category. Use Ctrl button to select more categories.'),
            'decorators'  => array('Bootstrap'),
            'size' 		  => 20,
            'class'       => 'form-control input-lg'
        ));
        
        $this->getElement('products')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList());   

                  
        $this->addElement('textarea', 'blocks', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Blocks'),
            'decorators' => array('Bootstrap'),
            'class'      => 'col-lg-12'
        ));            
       
        
        $this->addElement('checkbox', 'enabled', array(
            'label'      => $translate->_('Enabled'),
            'decorators' => array('Bootstrap'),
            'class'      => 'checkbox'
        ));        
        
        $this->addElement('multiselect', 'wikipages', array(
        'label' => 'Wiki Pages',
        'decorators' => array('Bootstrap'),
        'class'      => 'form-control input-lg multiselect'
        ));
        
        $this->getElement('wikipages')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic list.
                  ->setMultiOptions(Wiki::getList());          
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Bootstrap'),
            'class'    => 'btn'
        ));
        
        
        $this->addElement('hidden', 'category_id');
    }
}