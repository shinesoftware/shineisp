<?php
class Admin_Form_ProductsCategoriesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
        $this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Name',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));

        $this->addElement('text', 'uri', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'URI',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));        

        $this->addElement('text', 'googlecategs', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Google Categories',
            'description'      => 'See at http://support.google.com/merchants/bin/answer.py?hl=it&answer=1705911 for the list of the tassonomy',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));        

        $this->addElement('text', 'position', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Position',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));        
        
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Description'
        ));
        
        $this->addElement('textarea', 'keywords', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Keywords',
            'rows'        => 5,
            'class'       => 'textarea'
        ));     
        
        $this->addElement('select', 'parent', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Parent Category',
            'description' => 'Select here the parent category.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('parent')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(ProductsCategories::getList(true));   
        
        $this->addElement('multiselect', 'products', array(
            'label'       => 'Products',
            'description' => 'Select here the products to add to this category. Use Ctrl button to select more categories.',
            'decorators'  => array('Composite'),
            'size' 		  => 20,
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('products')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList());   

                  
        $this->addElement('textarea', 'blocks', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Blocks',
            'decorators' => array('Composite'),
            'class'      => 'textarea'
        ));            
       
        
        $this->addElement('checkbox', 'enabled', array(
            'label'      => 'Enabled',
            'decorators' => array('Composite'),
            'class'      => 'checkbox'
        ));        
        
        $this->addElement('multiselect', 'wikipages', array(
        'label' => 'Wiki Pages',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input multiselect'
        ));
        
        $this->getElement('wikipages')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic list.
                  ->setMultiOptions(Wiki::getList());          
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        
        $this->addElement('hidden', 'category_id');
    }
}